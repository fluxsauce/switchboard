<?php
/**
 * @file
 * The Switchboard brain.
 */

namespace Fluxsauce\Switchboard;

class Sqlite {
  /**
   * Get the location of the Switchboard brain.
   *
   * @return string
   *   The full path to the location of the brain.
   */
  public static function getLocation() {
    $brain_path = drush_cache_get('brain_path', 'switchboard');
    if (isset($brain_path->data)) {
      return $brain_path->data;
    }
    $default_location = drush_directory_cache('switchboard') . '/switchboard.sqlite';
    Sqlite::setLocation($default_location);
    return $default_location;
  }

  /**
   * Set the location of the switchboard brain.
   *
   * @param string $brain_path
   *   The full path to the new location of the brain.
   *
   * @return bool
   *   TRUE if successful.
   */
  public static function setLocation($brain_path) {
    drush_cache_set('brain_path', $brain_path, 'switchboard');
  }

  /**
   * Destroy the SQLite database.
   */
  public static function destroy() {
    $location = Sqlite::getLocation();
    drush_log(dt('Destroying SQLite database at @location', array(
      '@location' => $location,
    )));
    if (file_exists($location)) {
      unlink($location);
      drush_cache_clear_all('brain_path', 'switchboard', TRUE);
      drush_log(dt('SQLite database removed.'));
    }
    else {
      drush_log(dt('SQLite database does not exist, nothing to remove.'));
    }
  }

  /**
   * Drop a specific table from the SQLite database.
   *
   * @param string $table
   *   The victim table.
   */
  public static function destroyTable($table) {
    $pdo = Sqlite::get();

    try {
      $sql_query = 'DROP TABLE IF EXISTS ' . $table;
      $pdo->exec($sql_query);
    }
    catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Get the SQLite PDO.
   *
   * @return \PDO
   *   Fully set up SQLite PDO, including tables.
   */
  public static function get() {
    static $pdo;
    if (!isset($pdo)) {
      try {
        $pdo = new \PDO('sqlite:' . Sqlite::getLocation());
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
      }
      catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }

      try {
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
        $sql_query .= ', ssh_port TEXT ';
        $sql_query .= ', updated INTEGER ';
        $sql_query .= ') ';

        $pdo->exec($sql_query);
      }
      catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }

      try {
        $sql_query = 'CREATE TABLE IF NOT EXISTS environments ( ';
        $sql_query .= 'id INTEGER PRIMARY KEY ';
        $sql_query .= ', site_id INTEGER ';
        $sql_query .= ', name TEXT ';
        $sql_query .= ', host TEXT ';
        $sql_query .= ', username TEXT ';
        $sql_query .= ', branch TEXT ';
        $sql_query .= ', updated INTEGER ';
        $sql_query .= ') ';

        $pdo->exec($sql_query);
      }
      catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }

      try {
        $sql_query = 'CREATE TABLE IF NOT EXISTS envdbs ( ';
        $sql_query .= 'id INTEGER PRIMARY KEY ';
        $sql_query .= ', environment_id INTEGER ';
        $sql_query .= ', name TEXT ';
        $sql_query .= ', updated INTEGER ';
        $sql_query .= ') ';

        $pdo->exec($sql_query);
      }
      catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }

      try {
        $sql_query = 'CREATE TABLE IF NOT EXISTS projects ( ';
        $sql_query .= 'id INTEGER PRIMARY KEY ';
        $sql_query .= ', name TEXT ';
        $sql_query .= ', uuid TEXT ';
        $sql_query .= ', site_id INTEGER ';
        $sql_query .= ', hostname TEXT ';
        $sql_query .= ', ssh_port INTEGER ';
        $sql_query .= ', code_path TEXT ';
        $sql_query .= ', database_host TEXT ';
        $sql_query .= ', database_username TEXT ';
        $sql_query .= ', database_password TEXT ';
        $sql_query .= ', database_name TEXT ';
        $sql_query .= ', database_port INTEGER ';
        $sql_query .= ', updated INTEGER ';
        $sql_query .= ') ';

        $pdo->exec($sql_query);
      }
      catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }
    }
    return $pdo;
  }
}
