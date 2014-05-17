<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Environment {
  protected $id;
  protected $site_id;
  protected $name;
  protected $host;
  protected $branch;
  protected $updated;

  /**
   * Constructor.
   *
   * @param string $site_id
   *  Optional site_id.
   * @param string $name
   *  Optional environment name.
   */
  public function __construct($site_id = NULL, $name = NULL) {
    $this->site_id = $site_id;
    $this->name = $name;

    if ($site_id && $name) {
      $this->read();
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
   * Create an environment.
   */
  public function create() {
    $pdo = Sqlite::get();

    try {
      $sql_query = 'INSERT INTO environments (site_id, name, updated) ';
      $sql_query .= 'VALUES (:site_id, :name, :updated) ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':site_id', $this->site_id);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':updated', time());
      $stmt->execute();
      $this->id = $pdo->lastInsertId();
    } catch (\PDOException $e) {
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
      $sql_query .= 'FROM environments ';
      // ID known.
      if ($this->id) {
        $sql_query .= 'WHERE id = :id ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':id', $this->id);
      }
      // Name and site_id known.
      elseif ($this->name && $this->site_id) {
        $sql_query .= 'WHERE site_id = :site_id ';
        $sql_query .= 'AND name = :name ';
        $stmt = $pdo->prepare($sql_query);
        $stmt->bindParam(':site_id', $this->site_id);
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
   * Update an environment.
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

    $all_fields = get_object_vars($this);
    $fields_to_update = array();

    // Scan changed values.
    foreach ($all_fields as $key => $value) {
      // Empty string (allows for NULL).
      if ($value === '') {
        continue;
      }
      // Protected.
      if (in_array($key, array('name', 'site_id', 'id', 'updated'))) {
        continue;
      }
      // Safe to update.
      $fields_to_update[$key] = $value;
    }

    if (empty($fields_to_update)) {
      return;
    }

    try {
      $sql_query = 'UPDATE environments SET ';
      $sql_query_set = array();
      foreach (array_keys($fields_to_update) as $key) {
        $sql_query_set[] = $key . ' = ? ';
      }
      $sql_query .= implode(', ', $sql_query_set);
      $sql_query .= ', updated = ? ';
      $sql_query .= 'WHERE id = ? ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->execute(array_merge(array_values($fields_to_update), array(
        time(),
        $this->id,
      )));
      drush_log(dt('Updated environment @site_id:@name - @fields_to_update', array(
        '@site_id' => $this->site_id,
        '@name' => $this->name,
        '@fields_to_update' => implode(', ', array_keys($fields_to_update)),
      )));
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Delete an environment.
   */
  public function destroy() {
    $pdo = Sqlite::get();
    try {
      $stmt = $pdo->prepare('DELETE FROM environments WHERE id = :id');
      $stmt->execute(array(
        $this->id,
      ));
    } catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
    $this->id = NULL;
  }

  /**
   * Render an environment as a Drush table.
   */
  public function renderDrushTable() {
    $fields = get_object_vars($this);
    $rows = array();
    $rows[] = array_keys($fields);
    $rows[] = array_values($fields);
    drush_print_table($rows, TRUE);
  }

  public function to_array() {
    return get_object_vars($this);
  }
}
