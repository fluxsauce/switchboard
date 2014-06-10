<?php
/**
 * @file
 * Generic class providing database persistence.
 *
 * @todo Use a real ORM.
 */

namespace Fluxsauce\Switchboard;

/**
 * Generic class providing database persistence.
 */
abstract class Persistent {
  /**
   * @var int The Primary Key of the record.
   */
  protected $id;

  /**
   * @var string The machine name of the record.
   */
  protected $name;

  /**
   * @var int The UNIX timestamp of when the record was last updated.
   */
  protected $updated;

  /**
   * @var string Metadata for ORM defining database structure.
   */
  protected $externalKeyName;

  /**
   * Constructor.
   *
   * @param int $external_id
   *   Optional external identifier.
   * @param string $name
   *   Optional name.
   *
   * @throws \Exception
   */
  public function __construct($external_id = NULL, $name = NULL) {
    // Ensure that implementing classes include an external key name of some
    // sort.
    if (!$this->externalKeyName) {
      throw new \Exception(get_called_class() . ' is missing the external key name.');
    }
    $this->{$this->externalKeyName} = $external_id;
    $this->name = $name;

    if ($external_id && $name) {
      $this->read();
    }
  }

  /**
   * Magic __get.
   *
   * @param string $name
   *   Name of the property to get.
   *
   * @return mixed
   *   Value of set property.
   * @throws \Exception
   */
  public function __get($name) {
    $name = switchboard_to_camel_case($name);
    if (!property_exists($this, $name)) {
      drush_print_r(debug_backtrace());
      throw new \Exception(get_called_class() . ' property ' . $name . ' does not exist, cannot get.');
    }
    return $this->$name;
  }

  /**
   * Magic __set.
   *
   * @param string $name
   *   Name of the property to set.
   * @param mixed $value
   *   Value of said property.
   *
   * @throws \Exception
   */
  public function __set($name, $value) {
    $name = switchboard_to_camel_case($name);
    if (!property_exists($this, $name)) {
      drush_print_r(debug_backtrace());
      throw new \Exception(get_called_class() . ' property ' . $name . ' does not exist, cannot set.');
    }
    $this->$name = $value;
  }

  /**
   * Get the SQLite3 table name of an extending class.
   *
   * @return string
   *   Name of the table, which is the plural of the class name.
   */
  public function getTableName() {
    $reflect = new \ReflectionClass($this);
    return strtolower($reflect->getShortName()) . 's';
  }

  /**
   * Create a persistent record.
   */
  public function create() {
    $pdo = Sqlite::get();

    try {
      $sql_query = 'INSERT INTO ' . $this->getTableName() . ' ';
      $sql_query .= '(' . $this->externalKeyName . ', name, updated) ';
      $sql_query .= 'VALUES (:' . $this->externalKeyName . ', :name, :updated) ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':' . $this->externalKeyName, $this->{$this->externalKeyName});
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':updated', time());
      $stmt->execute();
      $this->id = $pdo->lastInsertId();
    }
    catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Read an environment.
   */
  public function read() {
    $pdo = Sqlite::get();
    try {
      $sql_query = 'SELECT * ';
      $sql_query .= 'FROM ' . $this->getTableName() . ' ';
      // ID known.
      if ($this->id) {
        $sql_query .= 'WHERE id = :id ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':id', $this->id);
      }
      // Name and id known.
      elseif ($this->name && $this->{$this->externalKeyName}) {
        $sql_query .= 'WHERE ' . $this->externalKeyName . ' = :' . $this->externalKeyName . ' ';
        $sql_query .= 'AND name = :name ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':' . $this->externalKeyName, $this->{$this->externalKeyName});
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
      else {
        if ($this->id) {
          $this->id = NULL;
        }
      }
    }
    catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Update a record.
   */
  public function update($update = array()) {
    $pdo = Sqlite::get();
    if (!$this->id) {
      $this->create();
    }

    if (!empty($update)) {
      foreach ($update as $key => $value) {
        $this->$key = $value;
      }
    }

    $fields_to_update = array();

    // Scan changed values.
    foreach ($this->toArray() as $key => $value) {
      // Do not save null values, but do allow empty string.
      if (is_null($value)) {
        continue;
      }
      // Protected.
      if (in_array($key, array('name', 'id', 'updated'))) {
        continue;
      }
      // Safe to update.
      $fields_to_update[$key] = $value;
    }

    if (empty($fields_to_update)) {
      return;
    }

    try {
      $sql_query = 'UPDATE ' . $this->getTableName() . ' SET ';
      $sql_query_set = array();
      foreach (array_keys($fields_to_update) as $key) {
        $sql_query_set[] = $key . ' = ? ';
      }
      $sql_query .= implode(', ', $sql_query_set);
      $sql_query .= ', updated = ? ';
      $sql_query .= 'WHERE id = ? ';
      $values = array_merge($fields_to_update, array(
        'updated' => time(),
        'id' => $this->id,
      ));
      $stmt = $pdo->prepare($sql_query);
      $result = $stmt->execute(array_values($values));
      drush_log(dt('Updated @class @external_key_id:@name - @fields_to_update', array(
        '@class' => get_class($this),
        '@external_key_id' => $this->{$this->externalKeyName},
        '@name' => $this->name,
        '@fields_to_update' => implode(', ', array_keys($fields_to_update)),
      )));
    }
    catch (\PDOException $e) {
      drush_log($sql_query);
      drush_log(var_export($values));
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Delete a record.
   */
  public function destroy() {
    $pdo = Sqlite::get();
    try {
      $stmt = $pdo->prepare('DELETE FROM ' . $this->getTableName() . ' WHERE id = :id');
      $stmt->execute(array(
        $this->id,
      ));
    }
    catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
    $this->id = NULL;
  }

  /**
   * Dump to an array.
   *
   * @return array
   *   Property names and values.
   */
  public function toArray() {
    $fields = get_object_vars($this);
    unset($fields['externalKeyName']);
    return $fields;
  }

  /**
   * Render to standard output.
   */
  public function render() {
    if (drush_get_option('json')) {
      $this->renderJson();
    }
    else {
      $this->renderDrushTable();
    }
  }

  /**
   * Render as a Drush table.
   */
  public function renderDrushTable() {
    $fields = $this->toArray();
    foreach ($fields as $field => &$value) {
      $value = $this->__get($field);
    }

    $rows = array();
    $rows[] = array_keys($fields);
    $rows[] = array_values($fields);
    drush_print_table($rows, TRUE);
  }

  /**
   * Render as a JSON array.
   */
  public function renderJson() {
    $fields = $this->toArray();
    foreach ($fields as $field => &$value) {
      $value = $this->__get($field);
    }
    drush_print(json_encode($fields));
  }
}
