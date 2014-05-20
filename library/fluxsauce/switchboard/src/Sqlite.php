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
    if (!isset($brain_path->data)) {
      return drush_directory_cache('switchboard') . '/switchboard.sqlite';
    }
    return $brain_path->data;
  }

  /**
   * Set the location of the switchboard brain. Moves old brain if it exists.
   *
   * @param string $brain_path
   *   The full path to the new location of the brain.
   *
   * @return bool
   *   TRUE if successful.
   */
  public static function setLocation($brain_path) {
    $existing_location = Sqlite::getLocation();
    drush_cache_set('brain_path', $brain_path, 'switchboard');
    if (file_exists($existing_location)) {
      // Move it.
      if (!drush_move_dir($existing_location, $brain_path, TRUE)) {
        drush_cache_set('brain_path', $existing_location, 'switchboard');
        return switchboard_message_fail('SWITCHBOARD_BRAIN_MOVE_FAIL', dt('Unable to move Switchboard brain to @brain_path; aborting.', array(
          '@brain_path' => $brain_path,
        )));
      }
    }
    return TRUE;
  }

  /**
   * Delete the SQLite database.
   */
  public static function delete() {
    if (file_exists(Sqlite::getLocation())) {
      unlink(Sqlite::getLocation());
    }
  }

  /**
   * Delete a specific table from the SQLite database.
   *
   * @param string $table
   *   The victim table.
   */
  public static function deleteTable($table) {
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
    }
    return $pdo;
  }
}
