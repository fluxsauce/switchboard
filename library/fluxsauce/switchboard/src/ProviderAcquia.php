<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class ProviderAcquia extends Provider {
  protected $name = 'acquia';
  protected $label = 'Acquia';
  protected $homepage = 'http://www.acquia.com/';
  protected $endpoint = 'https://cloudapi.acquia.com/v1';

  public function site_get_field($site_name, $field) {
    switch ($field) {
      // No API required.
      case 'name':
      case 'provider':
        break;
      case 'unix_username':
      case 'vcs_url':
      case 'vcs_type':
      case 'vcs_protocol':
      case 'uuid':
      case 'title':
      case 'ssh_port':
        $this->api_get_site($site_name);
        break;
      case 'realm':
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

  public function requests_options_custom() {
    $email = drush_cache_get('email', 'switchboard-auth-acquia');
    $password = drush_cache_get('password', 'switchboard-auth-acquia');
    $options = array(
      'auth' => new \Requests_Auth_Basic(array(
        $email->data,
        $password->data,
      )),
    );
    return $options;
  }

  public function auth_login($email, $password) {
    drush_cache_clear_all('*', 'switchboard-auth-acquia', TRUE);
    drush_cache_set('email', $email, 'switchboard-auth-acquia');
    drush_cache_set('password', $password, 'switchboard-auth-acquia');
    return TRUE;
  }

  protected function auth_email_get() {
    $email = drush_cache_get('email', 'switchboard-auth-acquia');
    if (isset($email->data)) {
      return $email->data;
    }
  }

  protected function auth_password_get() {
    $password = drush_cache_get('password', 'switchboard-auth-acquia');
    if (isset($password->data)) {
      return $password->data;
    }
  }

  public function auth_is_logged_in() {
    $email = drush_cache_get('email', 'switchboard-auth-acquia');
    $password = drush_cache_get('password', 'switchboard-auth-acquia');
    return (isset($email->data) && isset($password->data)) ? TRUE : FALSE;
  }

  public function api_get_sites() {
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites',
    ));
    $site_names = json_decode($result->body);
    $sites = array();
    foreach ($site_names as $site_data) {
      list($realm, $site_name) = explode(':', $site_data);
      $site = new Site($this->name, $site_name);
      $site->realm = $realm;
      $site->update();
      $this->sites[$site->name] = $site;
    }
  }

  public function api_get_site($site_name) {
    $site = new Site('acquia', $site_name);
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites/' . $site->realm . ':' . $site_name,
    ));
    $site_info = json_decode($result->body);
    $site->update(array(
      'unix_username' => $site_info->unix_username,
      'vcs_url' => $site_info->vcs_url,
      'vcs_type' => $site_info->vcs_type,
      'vcs_protocol' => 'git',
      'uuid' => $site_info->uuid,
      'title' => $site_info->title,
      'ssh_port' => 22,
    ));
    $this->sites[$site_name] = $site;
  }

  public function api_get_site_environments($site_name) {
    $site =& $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites/' . $site->realm . ':' . $site_name . '/envs',
    ));
    $environment_data = json_decode($result->body);
    foreach ($environment_data as $environment) {
      $new_environment = new Environment($site->id, $environment->name);
      $new_environment->branch = $environment->vcs_path;
      $new_environment->host = $environment->ssh_host;
      $new_environment->username = "$site_name.$environment->name";
      $new_environment->update();
      $site->environmentAdd($new_environment);
    }
  }

  public function api_get_site_env_dbs($site_name, $env_name) {
    $site =& $this->sites[$site_name];
    $env =& $site->environments[$env_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites/' . $site->realm . ':' . $site_name . '/envs/' . $env_name . '/dbs',
    ));
    $db_data = json_decode($result->body);
    foreach ($db_data as $db) {
      $new_db = new EnvDb($env->id, $db->instance_name);
      $new_db->update();
      $env->dbAdd($new_db);
    }
  }

  public function api_get_site_env_db_backups($site_name, $env_name) {
    $site = $this->sites[$site_name];
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites/' . $site->realm . ':' . $site_name . '/envs/' . $env_name . '/dbs/' . $site_name . '/backups',
    ));
    $backup_data = json_decode($result->body);

    $backups = array();
    foreach ($backup_data as $backup) {
      $backups[$backup->completed] = array(
        'filename' => end(explode('/', $backup->path)),
        'url' => '',
        'timestamp' => $backup->completed,
        'id' => $backup->id,
      );
    }
    arsort($backups);
    return $backups;
  }

  public function get_site_env_db_backup_latest($site_name, $env_name) {
    $site = $this->sites[$site_name];
    $backup = parent::get_site_env_db_backup_latest($site_name, $env_name);
    $backup['url'] = 'https://cloudapi.acquia.com/v1/sites/' . $site->realm . ':' . $site_name . '/envs/' . $env_name . '/dbs/' . $site_name . '/backups/' . $backup['id'] . '/download.json';
    unset($backup['id']);
    return $backup;
  }

  public function api_download_backup($backup, $destination) {
    drush_log(var_export($backup, TRUE));
    $destination_tmp = drush_tempnam('download_file');
    drush_shell_exec("curl --fail -s -L -u " . $this->auth_email_get() . ":" . $this->auth_password_get() . " --connect-timeout 30 -o %s %s", $destination_tmp, $backup['url']);
    if (!drush_file_not_empty($destination_tmp) && $file = @file_get_contents($backup['url'])) {
      @file_put_contents($destination_tmp, $file);
    }
    if (!drush_file_not_empty($destination_tmp)) {
      return drush_set_error('SWITCHBOARD_ACQUIA_BACKUP_DL_FAIL', dt('Unable to download!'));
    }
    $destination_path = $destination . DIRECTORY_SEPARATOR . $backup['filename'];
    drush_move_dir($destination_tmp, $destination_path, TRUE);
    return $destination_path;
  }

  public function get_files_path($site_name, $env_name) {
    return "/mnt/files/$site_name.$env_name/sites/default/files";
  }
}
