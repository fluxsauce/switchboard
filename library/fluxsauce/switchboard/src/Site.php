<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Site extends Persistent {
  protected $provider;
  protected $uuid;
  protected $realm;
  protected $title;
  protected $unix_username;
  protected $vcs_url;
  protected $vcs_type;
  protected $vcs_protocol;
  protected $environments;

  protected $external_key_name = 'provider';

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
      drush_log(dt('Site is missing value for @name from @calling_function.', array(
        '@name' => $name,
        '@calling_function' => $callers[1]['function'],
      )));
      $provider = Provider::getInstance($this->provider);
      $this->$name = $value = $provider->site_get_field($this->name, $name);
    }
    return $value;
  }

  public function environmentAdd(Environment $environment) {
    if (!is_array($this->environments)) {
      $this->environments = array();
    }
    $this->environments[$environment->name] = $environment;
  }

  public function environmentRemove(Environment $environment) {
    unset($this->environments[$environment->name]);
  }

  /**
   * Read a site.
   */
  public function read() {
    parent::read();
    $pdo = Sqlite::get();
    // Environments.
    try {
      $sql_query = 'SELECT name ';
      $sql_query .= 'FROM environments ';
      $sql_query .= 'WHERE site_id = :id ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':id', $this->id);
      $result = $stmt->execute();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $this->environmentAdd(new Environment($this->provider, $this->id, $row['name']));
      }
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  public function renderEnvironmentsDrushTable() {
    $rows = array();
    $environment = new Environment($this->provider);
    $fields = $environment->toArray();
    $rows = array();
    $rows[] = array_keys($fields);
    foreach ($this->__get('environments') as $environment) {
      $fields = $environment->toArray();
      $rows[] = array_values($fields);
    }
    drush_print_table($rows, TRUE);
  }
}
