<?php
/**
 * @file
 * Generic class for hosting / PaaS Providers.
 */

namespace Fluxsauce\Switchboard;
use Fluxsauce\Brain\SiteQuery;

/**
 * Generic class for hosting / PaaS Providers.
 */
abstract class Provider {
  /**
   * @var string Machine name of the Provider.
   */
  protected $name;

  /**
   * @var string Human readable label for the Provider.
   */
  protected $label;

  /**
   * @var string Homepage URL for the provider.
   */
  protected $homepage;

  /**
   * @var string Endpoint URL for the provider.
   */
  protected $endpoint;

  /**
   * @var array Contains instances of Fluxsauce\Switchboard\Site
   */
  protected $sites;

  /**
   * Returns a singleton Provider.
   *
   * @param string $provider_name
   *   The machine name of a Provider.
   *
   * @return mixed
   *   A Provider subclass.
   */
  static public function getInstance($provider_name) {
    static $instance = array();
    if (!isset($instance[$provider_name])) {
      $class_name = '\Fluxsauce\Switchboard\Provider' . ucfirst($provider_name);
      $instance[$provider_name] = new $class_name();
    }
    return $instance[$provider_name];
  }

  /**
   * Protected constructor; use getInstance.
   */
  protected function __construct() {
    // Ensure implementing classes have necessary properties.
    if (!$this->name) {
      throw new \Exception('Missing name from ' . __CLASS__);
    }
    if (!$this->label) {
      throw new \Exception('Missing label from ' . __CLASS__);
    }
    if (!filter_var($this->homepage, FILTER_VALIDATE_URL)) {
      throw new \Exception('Missing valid homepage from ' . __CLASS__);
    }
    if (!filter_var($this->endpoint, FILTER_VALIDATE_URL)) {
      throw new \Exception('Missing valid endpoint from ' . __CLASS__);
    }

    if (drush_get_option('refresh')) {
      $this->apiGetSites();
    }

    $sites = SiteQuery::create()
      ->filterByProvider($this->name)
      ->find();
    if (!empty($sites)) {
      foreach ($sites as $site) {
        $this->sites[$site->getName()] = $site;
      }
    }
  }

  /**
   * Prevent cloning of the Provider Singleton.
   */
  private function __clone() {}

  /**
   * Prevent unserializing of the Provider Singleton.
   */
  private function __wakeup() {}

  /**
   * Magic __get.
   *
   * @param string $name
   *   The name of the property to get.
   *
   * @return mixed
   *   Value of set property.
   * @throws \Exception
   */
  public function __get($name) {
    if (!property_exists($this, $name)) {
      drush_print_r(debug_backtrace());
      throw new \Exception(__CLASS__ . ' property ' . $name . ' does not exist, cannot get.');
    }
    return $this->$name;
  }

  /**
   * Perform an API call to get site information from a Provider.
   *
   * @param string $site_name
   *   The name of the site in question.
   */
  abstract public function apiGetSite($site_name);

  /**
   * Destroy a site associated with a Provider.
   *
   * @param string $site_name
   *   The machine name of the Site to destroy.
   *
   * @throws \Exception
   */
  public function siteDestroy($site_name) {
    if (!isset($this->sites[$site_name])) {
      throw new \Exception(__CLASS__ . ' site ' . $site_name . ' does not exist, cannot destroy.');
    }
    $this->sites[$site_name]->destroy();
    unset($this->sites[$site_name]);
  }

  /**
   * Delete all Sites associated with a Provider.
   */
  public function sitesDestroy() {
    if (!empty($this->sites)) {
      foreach ($this->sites as $site) {
        $site->delete();
      }
    }
  }

  /**
   * Determine if a Site exists within a Provider.
   *
   * @param string $site_name
   *   The machine name of the Site to check.
   *
   * @return bool
   *   TRUE if Site exists within a provider, FALSE if it does not.
   */
  public function siteExists($site_name) {
    if (!$site_name) {
      return FALSE;
    }
    if (empty($this->sites)) {
      $this->apiGetSites();
    }
    return array_key_exists($site_name, $this->sites);
  }

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
  abstract public function siteGetField($site_name, $field);

  /**
   * Populate available Sites from a Provider.
   */
  abstract public function apiGetSites();

  /**
   * Populate available Site Environments from a Provider.
   *
   * @param string $site_name
   *   The machine name of the site in question.
   */
  abstract public function apiGetSiteEnvironments($site_name);

  /**
   * Get and populate list of Databases for a particular Environment.
   *
   * @param string $site_name
   *   The machine name of the Site.
   * @param string $env_name
   *   The machine name of the Site Environment.
   */
  abstract public function apiGetSiteEnvDbs($site_name, $env_name);

  /**
   * Provider specific options for Requests.
   *
   * @return array
   *   Options for the request; see Requests::request for details.
   */
  abstract public function requestsOptionsCustom();

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
  abstract public function authLogin($email, $password);

  /**
   * Download a backup.
   *
   * @param array $backup
   *   An array from apiGetSiteEnvBackup().
   * @param string $destination
   *   The path to the destination.
   *
   * @return string
   *   The full path to the downloaded backup.
   */
  abstract public function apiDownloadBackup($backup, $destination);

  /**
   * Helper function to get the latest database backup.
   *
   * @param string $site_name
   *   The name of the remote Site.
   * @param string $env_name
   *   The name of the site environment.
   * @param string $backup_type
   *   The type of backup.
   *
   * @return array
   *   An array keyed by timestamps containing arrays with the following keys:
   *     'filename'
   *     'url'
   *     'timestamp'
   */
  abstract public function apiGetSiteEnvBackups($site_name, $env_name, $backup_type);

  /**
   * Helper function to get the latest database backup.
   *
   * @param string $site_name
   *   The name of the remote Site.
   * @param string $env_name
   *   The name of the site environment.
   * @param string $backup_type
   *   The type of backup.
   *
   * @return array
   *   A backup array as defined in apiGetSiteEnvBackups().
   */
  public function getSiteEnvBackupLatest($site_name, $env_name, $backup_type) {
    $backups = $this->apiGetSiteEnvBackups($site_name, $env_name, $backup_type);
    return array_pop($backups);
  }

  /**
   * Get the name of the Drush cache bin for a particular Provider.
   *
   * @return string
   *   The name of the drush cache bin.
   */
  public function drushCacheBinAuthName() {
    return 'switchboard-auth-' . $this->name;
  }

  /**
   * Log out of target provider.
   */
  public function authLogout() {
    drush_cache_clear_all('*', $this->drushCacheBinAuthName(), TRUE);
  }

  /**
   * Determine whether a user is logged-in to a Provider.
   *
   * @return bool
   *   TRUE if they are.
   */
  abstract public function authIsLoggedIn();

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
  abstract public function getFilesPath($site_name, $env_name);

  /**
   * Get all Provider specific and custom options for the Requests library.
   *
   * @param array $options
   *   Optional overriding options.
   *
   * @return array
   *   Options for the request; see Requests::request for details.
   */
  public function requestsOptions($options = array()) {
    $defaults = array(
      'timeout' => 30,
    );

    // Provider specific options.
    $provider_options = $this->requestsOptionsCustom();
    if (!empty($provider_options)) {
      $defaults += $provider_options;
    }

    // Custom options.
    if (!empty($options)) {
      $defaults += $options;
    }

    return $defaults;
  }
}
