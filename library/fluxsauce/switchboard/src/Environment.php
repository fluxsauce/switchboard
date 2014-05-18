<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class Environment extends Persistent {
  protected $site_id;
  protected $host;
  protected $branch;

  protected $external_key_name = 'site_id';

  protected $provider_name;
  protected $envdbs;

  /**
   * Constructor.
   *
   * @param string $provider_name
   *   Required provider name.
   * @param int $external_id
   *   Optional external identifier.
   * @param string $name
   *   Optional name.
   */
  public function __construct($provider_name, $external_id = NULL, $name = NULL) {
    if (!$provider_name) {
      throw new \Exception(get_called_class() . ' does not have a provider.');
    }
    parent::__construct($external_id, $name);
    $this->provider_name = $provider_name;
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
    $value = parent::__get($name);
    if (is_null($value) || drush_get_option('refresh')) {
      $callers = debug_backtrace();
      drush_log(dt('Environment is missing value for @name from @calling_function.', array(
        '@name' => $name,
        '@calling_function' => $callers[1]['function'],
      )));
      $provider = Provider::getInstance($this->provider_name);
      if ($name == 'envdbs') {
        $site = new Site();
        $site->id = $this->site_id;
        $site->read();
        $provider->api_get_site_env_dbs($site->name, $this->name);
      }
      else {
        $this->$name = $value = $provider->site_get_field($this->name, $name);
      }
    }
    return $value;
  }

  public function dbAdd(EnvDb $db) {
    if (!is_array($this->envdbs)) {
      $this->envdbs = array();
    }
    $this->envdbs[$db->name] = $db;
  }

  public function dbRemove(EnvDb $db) {
    unset($this->envdbs[$db->name]);
  }

  /**
   * Read an environment.
   */
  public function read() {
    parent::read();
    $pdo = Sqlite::get();
    // Databases.
    try {
      $sql_query = 'SELECT name ';
      $sql_query .= 'FROM envdbs ';
      $sql_query .= 'WHERE environment_id = :id ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':id', $this->id);
      $result = $stmt->execute();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $this->dbAdd(new EnvDb($this->id, $row['name']));
      }
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  public function renderDbsDrushTable() {
    $rows = array();
    $envdb = new EnvDb();
    $fields = $envdb->toArray();
    $rows = array();
    $rows[] = array_keys($fields);
    $this->__get('envdbs');
    foreach ($this->__get('envdbs') as $envdb) {
      $fields = $envdb->toArray();
      $rows[] = array_values($fields);
    }
    drush_print_table($rows, TRUE);
  }

  /**
   * Dump to an array.
   *
   * @return array
   *   Property names and values.
   */
  public function toArray() {
    $fields = parent::toArray();
    unset($fields['provider_name'], $fields['envdbs']);
    return $fields;
  }
}
