<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

/**
 * Site environment.
 */
class Environment extends Persistent {
  /**
   * @var int External key to the Site.
   */
  protected $siteId;

  /**
   * @var string The hostname for the Environment.
   */
  protected $host;

  /**
   * @var string The UNIX username for the Environment.
   */
  protected $username;

  /**
   * @var string The default VCS branch for the Environment.
   */
  protected $branch;

  /**
   * @var string Metadata for ORM defining database structure.
   */
  protected $externalKeyName = 'siteId';

  /**
   * @var array Contains instances of Fluxsauce\Switchboard\EnvBackup
   */
  protected $backups;

  /**
   * Read an Environment.
   */
  public function read() {
    parent::read();
    $pdo = Sqlite::get();
    // Backups.
    try {
      $sql_query = 'SELECT name ';
      $sql_query .= 'FROM envbackups ';
      $sql_query .= 'WHERE environmentId = :id ';
      $stmt = $pdo->prepare($sql_query);
      $stmt->bindParam(':id', $this->id);
      $result = $stmt->execute();
      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $this->backupAdd(new EnvBackup($this->id, $row['name']));
      }
    }
    catch (\PDOException $e) {
      switchboard_pdo_exception_debug($e);
    }
  }

  /**
   * Helper to add a backup to an Environment.
   *
   * @param EnvBackup $envBackup
   *   EnvBackup to add.
   */
  public function environmentAdd(EnvBackup $envBackup) {
    if (!is_array($this->backups)) {
      $this->backups = array();
    }
    $this->backups[$envBackup->name] = $envBackup;
  }

  /**
   * Helper to remove a backup from an Environment.
   *
   * @param EnvBackup $envBackup
   *   EnvBackup to remove.
   */
  public function backupRemove(EnvBackup $envBackup) {
    unset($this->backups[$envBackup->name]);
  }
}
