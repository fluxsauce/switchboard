<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

abstract class Provider {
  protected $name;
  protected $label;
  protected $homepage;
  protected $endpoint;

  protected $sites;

  /**
   * Returns a singleton Provider.
   *
   * @param string $provider_name
   * @return mixed
   */
  static function getInstance($provider_name) {
    static $instance = array();
    if (!isset($instance[$provider_name])) {
      $class_name = '\Fluxsauce\Switchboard\Provider' . ucfirst($provider_name);
      $instance[$provider_name] = new $class_name;
    }
    return $instance[$provider_name];
  }

  /**
   * Protected constructor.
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

    $pdo = Sqlite::get();
    $this->sites = array();

    try {
      $sql_query = 'SELECT name ';
      $sql_query .= 'FROM sites ';
      $sql_query .= 'WHERE provider = :provider ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':provider', $this->name);
      $stmt->execute();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $site = new Site($this->name, $row['name']);
        $this->sites[$row['name']] = $site;
      }
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }

    if (drush_get_option('refresh')) {
      $this->api_get_sites();
      foreach ($this->sites as $site) {
        $site->update();
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
  private function __wakeup(){}

  /**
   * Magic __get.
   *
   * @param $name string
   * @return mixed
   *  Value of set property.
   * @throws \Exception
   */
  public function __get($name) {
    if (!property_exists($this, $name)) {
      drush_print_r(debug_backtrace());
      throw new \Exception(__CLASS__ . ' property ' . $name . ' does not exist, cannot get.');
    }
    return $this->$name;
  }

  public function site_destroy($site_name) {
    if (!isset($this->sites[$site_name])) {
      throw new \Exception(__CLASS__ . ' site ' . $site_name . ' does not exist, cannot destroy.');
    }
    $this->sites[$site_name]->destroy();
    unset($this->sites[$site_name]);
  }

  public function sites_destroy() {
    $this->sites = array();
    $pdo = Sqlite::get();
    try {
      $stmt = $pdo->prepare('DELETE FROM sites WHERE provider = :provider');
      $stmt->bindParam(':provider', $this->name, PDO::PARAM_STR);
      $stmt->execute();
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  public function site_exists($site_name) {
    if (!$site_name) {
      return FALSE;
    }
    if (empty($this->sites)) {
      $this->api_get_sites();
    }
    return array_key_exists($site_name, $this->sites);
  }

  /**
   * A mapping function that calls the appropriate API to populate a field.
   *
   * @param $site_name
   * @param $field
   * @return string
   */
  abstract public function site_get_field($site_name, $field);

  /**
   * Update list of sites from provider.
   */
  abstract public function api_get_sites();

  abstract public function api_get_site_environments($site_name);

  /**
   * @return array
   */
  abstract public function requests_options_custom();

  /**
   * Log in to target provider.
   * @param $email
   * @param $password
   * @return boolean
   */
  abstract public function auth_login($email, $password);

  abstract public function api_download_backup($backup, $destination);

  /**
   * Log out of target provider.
   */
  public function auth_logout() {
    drush_cache_clear_all('*', 'switchboard-auth-' . $this->name, TRUE);
  }

  /**
   * @return boolean
   */
  abstract public function auth_is_logged_in();

  abstract public function api_get_site_env_db_backups($site_name, $env_name);

  public function get_site_env_db_backup_latest($site_name, $env_name) {
    $backups = $this->api_get_site_env_db_backups($site_name, $env_name);
    return array_pop($backups);
  }

  public function requests_options($options = array()) {
    $defaults = array(
      'timeout' => 30,
    );

    // Get provider specific options.
    $provider_options = $this->requests_options_custom();
    if (!empty($provider_options)) {
      $defaults = array_merge($defaults, $provider_options);
    }

    // Get custom options.
    if (!empty($options)) {
      $defaults = array_merge($defaults, $options);
    }

    return $defaults;
  }
}
