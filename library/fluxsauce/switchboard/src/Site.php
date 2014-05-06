<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Site {
  protected $id;
  protected $provider;
  protected $uuid;
  protected $realm;
  protected $name;
  protected $title;
  protected $unix_username;
  protected $vcs_url;
  protected $vcs_type;
  protected $vcs_protocol;
  protected $updated;

  /**
   * Constructor; if both provider and string are provided, attempts to read
   * from database.
   *
   * @param string $provider
   *  Optional provider name.
   * @param string $name
   *  Optional site name.
   */
  public function __construct($provider = NULL, $name = NULL) {
    $this->provider = $provider;
    $this->name = $name;

    if ($provider && $name) {
      $this->read();
    }
  }

  /**
   * This should be something more abstract.
   * @return \PDO
   */
  protected function _get_pdo() {
    static $pdo;
    if (!isset($pdo)) {
      try {
        $pdo = new \PDO('sqlite:' . drush_directory_cache('switchboard') . '/switchboard.sqlite');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql_query = 'CREATE TABLE IF NOT EXISTS sites ( ';
        $sql_query .= 'id INTEGER PRIMARY KEY ';
        $sql_query .= ', provider TEXT ';
        $sql_query .= ', uuid TEXT ';
        $sql_query .= ', realm TEXT ';
        $sql_query .= ', name TEXT ';
        $sql_query .= ', title TEXT ';
        $sql_query .= ', unix_username TEXT ';
        $sql_query .= ', vcs_url TEXT ';
        $sql_query .= ', vcs_type TEXT ';
        $sql_query .= ', vcs_protocol TEXT ';
        $sql_query .= ', updated INTEGER ';
        $sql_query .= ') ';

        $pdo->exec($sql_query);
      } catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }
    }
    return $pdo;
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
      throw new \Exception('Property ' . $name . ' does not exist, cannot get.');
    }
    return $this->$name;
  }

  /**
   * Magic __set.
   *
   * @param $name string
   *  Name of the property to set.
   * @param $value mixed
   *  Value of said property.
   * @throws \Exception
   */
  public function __set($name, $value) {
    if (!property_exists($this, $name)) {
      throw new \Exception('Property ' . $name . ' does not exist, cannot get.');
    }
    $this->$name = $value;
  }

  /**
   * Create a site.
   */
  public function create() {
    $pdo = $this->_get_pdo();

    try {
      $sql_query = 'INSERT INTO sites (provider, name, updated) ';
      $sql_query .= 'VALUES (:provider, :name, :updated) ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':provider', $this->provider);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':updated', time());
      $stmt->execute();
      $this->id = $pdo->lastInsertId();
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Read a site.
   */
  public function read() {
    $pdo = $this->_get_pdo();
    try {
      $sql_query = 'SELECT * ';
      $sql_query .= 'FROM sites ';
      // ID known.
      if ($this->id) {
        $sql_query .= 'WHERE id = :id ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':id', $this->id);
      }
      // Name and provider known.
      elseif ($this->name && $this->provider) {
        $sql_query .= 'WHERE provider = :provider ';
        $sql_query .= 'AND name = :name ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':provider', $this->provider);
        $stmt->bindParam(':name', $this->name);
      }
      // Not enough information.
      else {
        return;
      }
      $result = $stmt->execute();
      if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        foreach ($row as $key => $value) {
          $this->$key = $value;
        }
      }
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Update a site.
   */
  public function update() {
    $pdo = $this->_get_pdo();
    if (!$this->id) {
      $this->create();
    }

    $fields = get_object_vars($this);

    try {
      $sql_query = 'UPDATE sites SET ';
      $sql_query_set = array();
      foreach (array_keys($fields) as $key) {
        // Safety.
        if (in_array($key, array('name', 'provider', 'id'))) {
          unset($fields[$key]);
        }
        else {
          $sql_query_set[] = $key . ' = ? ';
        }
      }
      // Nothing to update.
      if (empty($sql_query_set)) {
        return;
      }
      $sql_query .= implode(', ', $sql_query_set);
      $sql_query .= ', updated = ? ';
      $sql_query .= 'WHERE id = ? ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->execute(array_merge(array_values($fields), array(
        time(),
        $this->id,
      )));
    } catch (PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Delete a Site.
   */
  public function destroy() {
    $pdo = $this->_get_pdo();
    try {
      $stmt = $pdo->prepare('DELETE FROM sites WHERE id = :id');
      $stmt->execute(array(
        $this->id,
      ));
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
    $this->id = NULL;
  }
}
