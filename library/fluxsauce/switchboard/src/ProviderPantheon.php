<?php
/**
 * @file
 * Pantheon specific API interactions.
 */

namespace Fluxsauce\Switchboard;
use Fluxsauce\Brain\Environment;

/**
 * Pantheon specific API interactions.
 */
class ProviderPantheon extends Provider {
  /**
   * @var string Machine name of the Provider.
   */
  protected $name = 'pantheon';

  /**
   * @var string Human readable label for the Provider.
   */
  protected $label = 'Pantheon';

  /**
   * @var string Homepage URL for the provider.
   */
  protected $homepage = 'https://www.getpantheon.com/';

  /**
   * @var string Endpoint URL for the provider.
   */
  protected $endpoint = 'https://terminus.getpantheon.com';

  /**
   * A mapping function that calls the appropriate API to populate a field.
   *
   * @param string $site_name
   *   Machine name of the site in question.
   * @param string $field
   *   Name of the field to populate.
   *
   * @return string
   *   The value of the field.
   * @throws \Exception
   *   Unknown field name.
   */
  public function siteGetField($site_name, $field) {
    switch ($field) {
      // No API required.
      case 'name':
      case 'provider':
        break;

      case 'vcsUrl':
      case 'vcsType':
      case 'vcsProtocol':
      case 'sshPort':
        $this->apiGetSite($site_name);
        break;

      case 'unixUsername':
      case 'realm':
      case 'uuid':
        $this->apiGetSites();
        break;

      case 'title':
        $this->apiGetSiteName($site_name);
      case 'environments':
        $this->apiGetSiteEnvironments($site_name);
        break;

      default:
        throw new \Exception('Unknown field ' . $field . ' in ' . __CLASS__);
    }
    return $this->sites[$site_name]->$field;
  }

  /**
   * Perform an API call to get site information from a Provider.
   *
   * @param string $site_name
   *   The name of the site in question.
   */
  public function apiGetSite($site_name) {
    $site = new Site('pantheon', $site_name);

    $repository = 'codeserver.dev.' . $site->uuid;
    $repository .= '@codeserver.dev.' . $site->uuid;
    $repository .= '.drush.in:2222/~/repository.git';

    $site->update(array(
      'vcsUrl' => $repository,
      'vcsType' => 'git',
      'vcsProtocol' => 'ssh',
      'sshPort' => 2222,
    ));
    $this->sites[$site_name] = $site;
  }

  /**
   * Populate available Sites from a Provider.
   */
  public function apiGetSites() {
    $user_uuid = drush_cache_get('user_uuid', $this->drushCacheBinAuthName());
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
      $site->unixUsername = '';
      $site->update();
      $this->sites[$site->name] = $site;
    }
  }

  /**
   * API call to get the human readable name of a Site.
   *
   * @param string $site_name
   *   The machine name of a site.
   */
  public function apiGetSiteName($site_name) {
    $site =& $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'realm' => 'attributes',
      'resource' => 'site',
      'uuid' => $site->uuid,
    ));
    $site_attributes = json_decode($result->body);
    $site->title = $site_attributes->label;
    $site->update();
    $this->sites[$site->name] = $site;
  }

  /**
   * Provider specific options for Requests.
   *
   * @return array
   *   Options for the request; see Requests::request for details.
   */
  public function requestsOptionsCustom() {
    $options = array();
    $cookies = drush_cache_get('session', $this->drushCacheBinAuthName());
    if (isset($cookies->data)) {
      $options = array(
        'cookies' => array($cookies->data),
      );
    }
    return $options;
  }

  /**
   * Log in to target Provider.
   *
   * @param string $email
   *   The email address of the user.
   * @param string $password
   *   The password of the user.
   *
   * @return bool
   *   Indicates success.
   */
  public function authLogin($email, $password) {
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
    $form_build_id = $this->authLoginGetFormBuildId($response->body);
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
      ), $this->requestsOptions(array('follow_redirects' => FALSE)));
    }
    catch (\Requests_Exception $e) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_LOGIN_FAILURE', dt('Pantheon login failure: @error', array(
        '@error' => $e->getMessage(),
      )));
    }

    $session = $this->authLoginGetSessionFromHeaders($response->headers->getValues('set-cookie'));

    if (!$session) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_SESSION', dt('Pantheon Session not found; please check your credentials and try again.'));
    }

    // Get the UUID.
    $user_uuid = array_pop(explode('/', $response->headers->offsetGet('Location')));
    if (!switchboard_validate_uuid($user_uuid)) {
      return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_UUID', dt('Pantheon User UUID not found; please check your credentials and try again.'));
    }

    drush_cache_clear_all('*', $this->drushCacheBinAuthName(), TRUE);
    drush_cache_set('user_uuid', $user_uuid, $this->drushCacheBinAuthName());
    drush_cache_set('session', $session, $this->drushCacheBinAuthName());
    drush_cache_set('email', $email, $this->drushCacheBinAuthName());
    return TRUE;
  }

  /**
   * Determine whether a user is logged-in to a Provider.
   *
   * @return bool
   *   TRUE if they are.
   */
  public function authIsLoggedIn() {
    $session = drush_cache_get('session', $this->drushCacheBinAuthName());
    return isset($session->data) ? TRUE : FALSE;
  }

  /**
   * Parse session out of a header.
   *
   * Based on terminus_pauth_get_session_from_headers().
   *
   * @param array $headers
   *   Headers to parse.
   *
   * @return string
   *   The session cookie, if found.
   */
  public function authLoginGetSessionFromHeaders($headers) {
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
   *
   * @param string $html
   *   The raw HTML to parse.
   *
   * @return string
   *   The login form_build_id, if found.
   */
  public function authLoginGetFormBuildId($html) {
    if (!$html) {
      return FALSE;
    }
    // Parse form build ID.
    $dom = new \DOMDocument();
    @$dom->loadHTML($html);
    $login_form = $dom->getElementById('atlas-login-form');
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

  /**
   * Populate available Site Environments from a Provider.
   *
   * @param string $site_name
   *   The machine name of the site in question.
   */
  public function apiGetSiteEnvironments($site_name) {
    $site =& $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'realm' => 'environments',
      'resource' => 'site',
      'uuid' => $site->uuid,
    ));
    $environment_data = json_decode($result->body);
    foreach ($environment_data as $environment_name => $environment) {
      $new_environment = new Environment();
      $new_environment->setSiteid($site->id);
      $new_environment->setName($environment->name);
      $new_environment->setBranch('master');
      $new_environment->setHost("appserver.$environment_name.{$site->uuid}.drush.in");
      $new_environment->setUsername("$environment_name.$site_name");
      $new_environment->save();
      $site->environmentAdd($new_environment);
    }
  }

  /**
   * Get and populate list of Databases for a particular Environment.
   *
   * @param string $site_name
   *   The machine name of the Site.
   * @param string $env_name
   *   The machine name of the Site Environment.
   */
  public function apiGetSiteEnvDbs($site_name, $env_name) {
    $site =& $this->sites[$site_name];
    $env =& $site->environments[$env_name];
    $new_db = new EnvDb($env->id, 'pantheon');
    $new_db->update();
    $env->dbAdd($new_db);
  }

  /**
   * Get a list of database backups for a particular Site Environment.
   *
   * @param string $site_name
   *   The machine name of the Site.
   * @param string $env_name
   *   The machine name of the Site Environment.
   * @param string $backup_type
   *   The type of backup.
   *
   * @return array
   *   An array of Backup arrays keyed by the timestamp. Each Backup
   *   array has the following keys:
   *   - 'filename'
   *   - 'url'
   *   - 'timestamp'
   */
  public function apiGetSiteEnvBackups($site_name, $env_name, $backup_type) {
    $site = $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => 'site',
      'realm' => 'environments/' . $env_name . '/backups/catalog',
      'uuid' => $site->uuid,
    ));
    $backups = array();
    $backup_data = json_decode($result->body);
    foreach ($backup_data as $id => $backup) {
      $parts = explode('_', $id);
      if ($backup_type == 'db') {
        if (!isset($backup->filename) || $parts[2] != 'database') {
          continue;
        }
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

  /**
   * Helper function to get the latest database backup.
   *
   * @param string $site_name
   *   The machine name of the Site in question.
   * @param string $env_name
   *   The machine name of the Site Environment in question.
   * @param string $backup_type
   *   The type of backup.
   *
   * @return array
   *   A backup array as defined in apiGetSiteEnvBackups().
   */
  public function getSiteEnvBackupLatest($site_name, $env_name, $backup_type) {
    $backup = parent::getSiteEnvBackupLatest($site_name, $env_name, $backup_type);
    if ($backup_type == 'db') {
      $backup['url'] = $this->apiGetBackupDownloadUrl($site_name, $env_name, $backup['bucket'], 'database');
    }
    unset($backup['bucket']);
    return $backup;
  }

  /**
   * Helper function to get the S3 backup URL.
   *
   * @param string $site_name
   *   The machine name of the Site in question.
   * @param string $env_name
   *   The machine name of the Site Environment in question.
   * @param string $bucket
   *   The S3 bucket.
   * @param string $element
   *   Elements are db, code, files.
   *
   * @return string
   *   The S3 URL of the backup.
   */
  public function apiGetBackupDownloadUrl($site_name, $env_name, $bucket, $element) {
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

  /**
   * Download a backup.
   *
   * @param array $backup
   *   An array from apiGetSiteEnvBackups().
   * @param string $destination
   *   The path to the destination.
   *
   * @return string
   *   The full path to the downloaded backup.
   */
  public function apiDownloadBackup($backup, $destination) {
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

  /**
   * Get the remote path to files for a particular Site Environment.
   *
   * @param string $site_name
   *   The machine name of the Site in question.
   * @param string $env_name
   *   The machine name of the Site Environment in question.
   *
   * @return string
   *   The full path of the files directory.
   */
  public function getFilesPath($site_name, $env_name) {
    return 'files';
  }
}
