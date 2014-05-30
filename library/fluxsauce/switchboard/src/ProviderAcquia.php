<?php
/**
 * @file
 * Acquia specific API interactions.
 */

namespace Fluxsauce\Switchboard;

class ProviderAcquia extends Provider {
  protected $name = 'acquia';
  protected $label = 'Acquia';
  protected $homepage = 'http://www.acquia.com/';
  protected $endpoint = 'https://cloudapi.acquia.com/v1';

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

      case 'unixUsername':
      case 'vcsUrl':
      case 'vcsType':
      case 'vcsProtocol':
      case 'uuid':
      case 'title':
      case 'sshPort':
        $this->apiGetSite($site_name);
        break;

      case 'realm':
        $this->apiGetSites();
        break;

      case 'environments':
        $this->apiGetSiteEnvironments($site_name);
        break;

      default:
        throw new \Exception('Unknown field ' . $field . ' in ' . __CLASS__);
    }
    return $this->sites[$site_name]->$field;
  }

  /**
   * Provider specific options for Requests.
   *
   * @return array
   *   Options for the request; see Requests::request for details.
   */
  public function requestsOptionsCustom() {
    $email = drush_cache_get('email', $this->drushCacheBinAuthName());
    $password = drush_cache_get('password', $this->drushCacheBinAuthName());
    $options = array(
      'auth' => new \Requests_Auth_Basic(array(
        $email->data,
        $password->data,
      )),
    );
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
    drush_cache_clear_all('*', $this->drushCacheBinAuthName(), TRUE);
    drush_cache_set('email', $email, $this->drushCacheBinAuthName());
    drush_cache_set('password', $password, $this->drushCacheBinAuthName());
    return TRUE;
  }

  /**
   * Helper function to get the cached email.
   *
   * @return mixed
   *   The password in question or NULL.
   */
  protected function authEmailGet() {
    $email = drush_cache_get('email', $this->drushCacheBinAuthName());
    if (isset($email->data)) {
      return $email->data;
    }
  }

  /**
   * Helper function to get the cached password.
   *
   * @return mixed
   *   The password in question or NULL.
   */
  protected function authPasswordGet() {
    $password = drush_cache_get('password', $this->drushCacheBinAuthName());
    if (isset($password->data)) {
      return $password->data;
    }
  }

  /**
   * Determine whether a user is logged-in to a Provider.
   *
   * @return bool
   *   TRUE if they are.
   */
  public function authIsLoggedIn() {
    $email = drush_cache_get('email', $this->drushCacheBinAuthName());
    $password = drush_cache_get('password', $this->drushCacheBinAuthName());
    return (isset($email->data) && isset($password->data)) ? TRUE : FALSE;
  }

  /**
   * Populate available Sites from a Provider.
   */
  public function apiGetSites() {
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

  /**
   * Perform an API call to get site information from a Provider.
   *
   * @param string $site_name
   *   The name of the site in question.
   */
  public function apiGetSite($site_name) {
    $site = new Site('acquia', $site_name);
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites/' . $site->realm . ':' . $site_name,
    ));
    $site_info = json_decode($result->body);
    $site->update(array(
      'unixUsername' => $site_info->unix_username,
      'vcsUrl' => $site_info->vcs_url,
      'vcsType' => $site_info->vcs_type,
      'vcsProtocol' => 'git',
      'uuid' => $site_info->uuid,
      'title' => $site_info->title,
      'sshPort' => 22,
    ));
    $this->sites[$site_name] = $site;
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

  /**
   * Get a list of database backups for a particular Site Environment.
   *
   * @param string $site_name
   *   The machine name of the Site.
   * @param string $env_name
   *   The machine name of the Site Environment.
   *
   * @return array
   *   An array of Backup arrays keyed by the timestamp. Each Backup
   *   array has the following keys:
   *   - 'filename'
   *   - 'url'
   *   - 'timestamp'
   */
  public function apiGetSiteEnvDbBackups($site_name, $env_name) {
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

  /**
   * Helper function to get the latest database backup.
   *
   * @param string $site_name
   *   The machine name of the Site in question.
   * @param string $env_name
   *   The machine name of the Site Environment in question.
   *
   * @return array
   *   A backup array as defined in apiGetSiteEnvDbBackups().
   */
  public function getSiteEnvDbBackupLatest($site_name, $env_name) {
    $site = $this->sites[$site_name];
    $backup = parent::getSiteEnvDbBackupLatest($site_name, $env_name);
    $backup['url'] = 'https://cloudapi.acquia.com/v1/sites/' . $site->realm . ':' . $site_name . '/envs/' . $env_name . '/dbs/' . $site_name . '/backups/' . $backup['id'] . '/download.json';
    unset($backup['id']);
    return $backup;
  }

  /**
   * Download a backup.
   *
   * @param array $backup
   *   An array from apiGetSiteEnvDbBackups().
   * @param string $destination
   *   The path to the destination.
   *
   * @return string
   *   The full path to the downloaded backup.
   */
  public function apiDownloadBackup($backup, $destination) {
    drush_log(var_export($backup, TRUE));
    $destination_tmp = drush_tempnam('download_file');
    drush_shell_exec("curl --fail -s -L -u " . $this->authEmailGet() . ":" . $this->authPasswordGet() . " --connect-timeout 30 -o %s %s", $destination_tmp, $backup['url']);
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
    return "/mnt/files/$site_name.$env_name/sites/default/files";
  }
}
