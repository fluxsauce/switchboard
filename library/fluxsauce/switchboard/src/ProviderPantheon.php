<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class ProviderPantheon extends Provider {
  protected $name = 'pantheon';
  protected $label = 'Pantheon';
  protected $homepage = 'https://www.getpantheon.com/';
  protected $endpoint = 'https://terminus.getpantheon.com';

  public function site_get_field($site_name, $field) {
    switch ($field) {
      // No API required.
      case 'name':
      case 'provider':
        break;
      case 'vcs_url':
      case 'vcs_type':
      case 'vcs_protocol':
        $this->api_get_site($site_name);
        break;
      case 'unix_username':
      case 'realm':
      case 'uuid':
      case 'title':
        $this->api_get_sites();
        break;
      case 'environments':
        $this->api_get_site_environments($site_name);
        break;
      default:
        throw new \Exception('Unknown field ' . $field . ' in ' . __CLASS__);
    }
    return $this->sites[$site_name]->$field;
  }

  public function api_get_site($site_name) {
    $site = new Site('pantheon', $site_name);

    $repository = 'codeserver.dev.' . $site->uuid;
    $repository .= '@codeserver.dev.' . $site->uuid;
    $repository .= '.drush.in:2222/~/repository.git';

    $site->update(array(
      'vcs_url' => $repository,
      'vcs_type' => 'git',
      'vcs_protocol' => 'ssh',
    ));
    $this->sites[$site_name] = $site;
  }

  public function api_get_sites() {
    $user_uuid = drush_cache_get('user_uuid', 'switchboard-auth-pantheon');
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'realm' => 'sites',
      'resource' => 'user',
      'uuid' => $user_uuid->data,
    ));
    $site_metadata = json_decode($result->body);

    $sites = array();

    foreach ($site_metadata as $uuid => $data) {
      $site = new Site($this->name, $data->information->name);
      $site->uuid = $uuid;
      $site->realm = $data->information->preferred_zone;
      $site->title = $site->name;
      $site->unix_username = '';
      $site->update();
      $this->sites[$site->name] = $site;
    }
  }

  public function requests_options_custom() {
    $options = array();
    $cookies = drush_cache_get('session', 'switchboard-auth-pantheon');
    if (isset($cookies->data)) {
      $options = array(
        'cookies' => array($cookies->data),
      );
    }
    return $options;
  }

  public function auth_login($email, $password) {
    $url = $this->endpoint . '/login';

    // Get the form build ID.
    try {
      $response = \Requests::post($url);
    }
    catch (\Requests_Exception $e) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_ENDPOINT_UNAVAILABLE', dt('Pantheon endpoint unavailable: @error', array(
        '@error' => $e->getMessage(),
      )));
    }
    $form_build_id = $this->auth_login_get_form_build_id($response->body);
    if (!$form_build_id) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_LOGIN_UNAVAILABLE', dt('Pantheon login unavailable.'));
    }

    // Attempt to log in.
    try {
      $response = \Requests::post($url, array(), array(
        'email' => $email,
        'password' => $password,
        'form_build_id' => $form_build_id,
        'form_id' => 'atlas_login_form',
        'op' => 'Login',
      ), $this->requests_options(array('follow_redirects' => FALSE)));
    }
    catch (\Requests_Exception $e) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_LOGIN_FAILURE', dt('Pantheon login failure: @error', array(
        '@error' => $e->getMessage(),
      )));
    }

    $session = $this->auth_login_get_session_from_headers($response->headers->getValues('set-cookie'));

    if (!$session) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_SESSION', dt('Pantheon Session not found; please check your credentials and try again.'));
    }

    // Get the UUID.
    $user_uuid = array_pop(explode('/', $response->headers->offsetGet('Location')));
    if (!switchboard_validate_uuid($user_uuid)) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_UUID', dt('Pantheon User UUID not found; please check your credentials and try again.'));
    }

    drush_cache_clear_all('*', 'switchboard-auth-pantheon', TRUE);
    drush_cache_set('user_uuid', $user_uuid, 'switchboard-auth-pantheon');
    drush_cache_set('session', $session, 'switchboard-auth-pantheon');
    drush_cache_set('email', $email, 'switchboard-auth-pantheon');
    return TRUE;
  }

  public function auth_is_logged_in() {
    $session = drush_cache_get('session', 'switchboard-auth-pantheon');
    return isset($session->data) ? TRUE : FALSE;
  }

  /**
   * Parse session out of a header, based on terminus_pauth_get_session_from_headers().
   * https://github.com/pantheon-systems/terminus
   *
   * @param array $headers
   * @return string
   */
  function auth_login_get_session_from_headers($headers) {
    $session = FALSE;
    foreach ($headers as $header) {
      foreach (explode('; ', $header) as $cookie) {
        if (strpos($cookie, 'SSESS') === 0) {
          $session = $cookie;
        }
      }
    }
    return $session;
  }

  /**
   * Parse form build ID, based on terminus_pauth_login_get_form_build_id().
   * https://github.com/pantheon-systems/terminus
   *
   * @param $html
   * @return string
   */
  public function auth_login_get_form_build_id($html) {
    if (!$html) {
      return FALSE;
    }
    // Parse form build ID.
    $DOM = new \DOMDocument;
    @$DOM->loadHTML($html);
    $login_form = $DOM->getElementById('atlas-login-form');
    if (!$login_form) {
      return FALSE;
    }

    foreach ($login_form->getElementsByTagName('input') as $input) {
      if ($input->getAttribute('name') == 'form_build_id') {
        return $input->getAttribute('value');
      }
    }
    return FALSE;
  }

  public function api_get_site_environments($site_name) {
    $site =& $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'realm' => 'environments',
      'resource' => 'site',
      'uuid' => $site->uuid,
    ));
    $environment_data = json_decode($result->body);
    foreach ($environment_data as $environment_name => $environment) {
      $new_environment = new Environment($site->id, $environment_name);
      $new_environment->branch = 'master';
      $new_environment->host = "appserver.$environment_name.{$site->uuid}.drush.in";
      $new_environment->username = "$environment_name.$site_name";
      $new_environment->files_path = "files";
      $new_environment->update();
      $site->environmentAdd($new_environment);
    }
  }

  public function api_get_site_env_dbs($site_name, $env_name) {
    $site =& $this->sites[$site_name];
    $env =& $site->environments[$env_name];
    $new_db = new EnvDb($env->id, 'pantheon');
    $new_db->update();
    $env->dbAdd($new_db);
  }

  public function api_get_site_env_db_backups($site_name, $env_name) {
    $site = $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => 'site',
      'realm' => 'environments/' . $env_name . '/backups/catalog',
      'uuid' => $site->uuid,
    ));
    // $result = terminus_api_backup_download_url(drush_get_option('site_uuid'), drush_get_option('environment'), drush_get_option('bucket'), drush_get_option('element'));
    $backups = array();
    $backup_data = json_decode($result->body);
    foreach ($backup_data as $id => $backup) {
      $parts = explode('_', $id);
      if (!isset($backup->filename) || $parts[2] != 'database') {
        continue;
      }
      $backups[$backup->timestamp] = array(
        'filename' => $backup->filename,
        'url' => '',
        'bucket' => $parts[0] . '_' . $parts[1],
        'timestamp' => $backup->timestamp,
      );
    }
    arsort($backups);
    return $backups;
  }

  public function get_site_env_db_backup_latest($site_name, $env_name) {
    $backup = parent::get_site_env_db_backup_latest($site_name, $env_name);
    $backup['url'] = $this->api_get_backup_download_url($site_name, $env_name, $backup['bucket'], 'database');
    unset($backup['bucket']);
    return $backup;
  }

  public function api_get_backup_download_url($site_name, $env_name, $bucket, $element) {
    $site = $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'POST',
      'resource' => 'site',
      'realm' => 'environments/' . $env_name . '/backups/catalog/' . $bucket . '/' . $element . '/s3token',
      'uuid' => $site->uuid,
      'data' => array('method' => 'GET'),
    ));
    $token = json_decode($result->body);
    return $token->url;
  }

  public function api_download_backup($backup, $destination) {
    // See Drush's package_handler_download_project().
    $destination_path = $destination . DIRECTORY_SEPARATOR . $backup['filename'];
    $path = _drush_download_file($backup['url'], $destination_path, 31556926);
    if ($path || drush_get_context('DRUSH_SIMULATE')) {
      return $destination_path;
    }
    else {
      return drush_set_error('SWITCHBOARD_PANTHEON_BACKUP_DL_FAIL', dt('Unable to download!'));
    }
  }
}
