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
    $pdo = \Fluxsauce\Switchboard\Sqlite::getSite();

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
    $pdo = \Fluxsauce\Switchboard\Sqlite::getSite();
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
  public function update($update = array()) {
    $pdo = \Fluxsauce\Switchboard\Sqlite::getSite();
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
      if (in_array($key, array('name', 'provider', 'id', 'updated'))) {
        continue;
      }
      // Safe to update.
      $fields_to_update[$key] = $value;
    }

    if (empty($fields_to_update)) {
      return;
    }

    try {
      $sql_query = 'UPDATE sites SET ';
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
      drush_log(dt('Updated site @provider:@name', array(
        '@provider' => $this->provider,
        '@name' => $this->name,
      )));
    } catch (PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Delete a Site.
   */
  public function destroy() {
    $pdo = \Fluxsauce\Switchboard\Sqlite::getSite();
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

  /**
   * Render a site as a Drush table.
   */
  public function renderDrushTable() {
    $fields = get_object_vars($this);
    $rows = array();
    $rows[] = array_keys($fields);
    $rows[] = array_values($fields);
    drush_print_table($rows, TRUE);
  }
}
