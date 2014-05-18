<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Sqlite {
  /**
   * Delete the SQLite database.
   */
  public static function delete() {
    if (file_exists(drush_directory_cache('switchboard') . '/switchboard.sqlite')) {
      unlink(drush_directory_cache('switchboard') . '/switchboard.sqlite');
    }
  }

  /**
   * Get the SQLite PDO.
   * @return \PDO
   */
  public static function get() {
    static $pdo;
    if (!isset($pdo)) {
      try {
        $pdo = new \PDO('sqlite:' . drush_directory_cache('switchboard') . '/switchboard.sqlite');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);
      } catch (\PDOException $e) {
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
      } catch (\PDOException $e) {
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
      } catch (\PDOException $e) {
        switchboard_pdo_exception_debug($e);
      }
    }
    return $pdo;
  }
}
