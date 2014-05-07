<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Sqlite {
  public static function siteDelete() {
    unlink(drush_directory_cache('switchboard') . '/switchboard.sqlite');
  }
  public static function siteGet() {
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
}
