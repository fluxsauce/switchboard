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

  public function __construct($refresh = FALSE) {
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

    $pdo = Sqlite::siteGet();
    $this->sites = array();

    try {
      $sql_query = 'SELECT * ';
      $sql_query .= 'FROM sites ';
      $sql_query .= 'WHERE provider = :provider ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':provider', $this->name);
      $stmt->execute();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $site = new Site($this->name, $row['name']);
        $site->update($row);
        $ret_val[$row['name']] = $site;
      }
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }

    if ($refresh) {
      $this->api_get_sites();
    }
  }

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
      throw new \Exception(__CLASS__ . ' property ' . $name . ' does not exist, cannot get.');
    }
    return $this->$name;
  }

  public function sites_delete() {
    $this->sites = array();
    $pdo = Sqlite::siteGet();
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
    return array_key_exists($site_name, $this->sites);
  }

  /**
   * Update list of sites from provider.
   */
  abstract public function api_get_sites();

  /**
   * @return array
   */
  abstract public function requests_options_custom();

  public function requests_options($options = array()) {
    $common_options = array(
      'timeout' => 30,
    );

    // Get provider specific options.
    $defaults = array_merge($common_options, $this->requests_options_custom());

    // Combine defaults with custom options.
    return array_merge($defaults, $options);
  }

  /**
   * Validate a standard 8-4-4-4-12 UUID.
   * @param $uuid
   * @return boolean
   */
  public function validate_uuid($uuid) {
    return preg_match('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $uuid) ? TRUE : FALSE;
  }
}
