<?php
/**
 * @file
 * The Switchboard brain.
 */

namespace Fluxsauce\Switchboard;

/**
 * The Switchboard brain.
 */
class Sqlite {
  /**
   * Get the location of the Switchboard brain.
   *
   * @return string
   *   The full path to the location of the brain.
   */
  public static function getLocation() {
    return SWITCHBOARD_BASE_PATH . '/brain/switchboard.sqlite';
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
    }
    return $pdo;
  }
}
