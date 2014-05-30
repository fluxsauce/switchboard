<?php
/**
 * @file
 * Local Project structure.
 */

namespace Fluxsauce\Switchboard;

class Project extends Persistent {
  protected $uuid;
  protected $siteId;
  protected $hostname;
  protected $username;
  protected $sshPort;
  protected $codePath;
  protected $databaseHost;
  protected $databaseUsername;
  protected $databasePassword;
  protected $databaseName;
  protected $databasePort;
  protected $filesPath;

  protected $externalKeyName = 'name';

  /**
   * Get the minimal database specs.
   *
   * @return array
   *   Keys for database, host, user, password, port.
   */
  public function getDatabaseSpecs() {
    return array(
      'database' => $this->databaseName,
      'host' => $this->databaseHost,
      'user' => $this->databaseUsername,
      'password' => $this->databasePassword,
      'port' => $this->databasePort,
    );
  }

  /**
   * Get a database connection string.
   *
   * @return string
   *   The command for connecting to a database.
   */
  public function getDatabaseConnection() {
    $parameter_strings = array();
    foreach ($this->getDatabaseSpecs() as $key => $value) {
      $value = drush_escapeshellarg($value);
      $parameter_strings[] = "--$key=$value";
    }
    return 'mysql ' . implode(' ', $parameter_strings);
  }

  /**
   * Get a Drupal v6 database connection string.
   *
   * @return string
   *   The db-url string.
   */
  public function getDatabaseUrl() {
    return "mysql://{$this->databaseUsername}:{$this->databasePassword}@{$this->databaseHost}:{$this->databasePort}/{$this->databaseName}";
  }

  /**
   * Get a Drupal v7 database connection settings.
   *
   * @return string
   *   The databases array.
   */
  public function getDatabaseSettings() {
    return <<<CONF
\$databases['default']['default'] = array(
  'driver' => 'mysql',
  'username' => '{$this->databaseUsername}',
  'password' => '{$this->databasePassword}',
  'host' => '{$this->databaseHost}',
  'port' => '{$this->databasePort}',
  'database' => '{$this->databaseName}',
);
CONF;
  }

  /**
   * Generate command to commit all code changes.
   *
   * @param string $message
   *   The git commit message.
   *
   * @return string
   *   The git command.
   */
  public function vcsCommitCommand($message) {
    return 'cd ' . $this->codePath . ' && git add --all . && git commit -m ' . drush_escapeshellarg($message);
  }

  /**
   * Generate command to pull code changes from remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPullCommand() {
    return 'git --git-dir=' . $this->codePath . '/.git pull origin master';
  }

  /**
   * Generate command to push code changes to remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPushCommand() {
    return 'git --git-dir=' . $this->codePath . '/.git push origin master';
  }

  /**
   * Generate command to reset all local code changes.
   *
   * @return string
   *   The git command.
   */
  public function vcsResetCommand() {
    return 'cd ' . $this->codePath . ' && git checkout .';
  }

  /**
   * Renders a project as a Drush alias.
   *
   * @return string
   *   Drush alias.
   */
  public function toDrushAlias() {
    return <<<ALIAS
\$aliases['{$this->name}'] = array(
  'root' => '{$this->codePath}',
  'uri' => '{$this->hostname}',
  'db-url' => '{$this->getDatabaseUrl()}',
  'remote-host' => '{$this->hostname}',
  'remote-user' => '{$this->username}',
  'ssh-options' => '-p {$this->sshPort}',
  'path-aliases' => array(
    '%files' => '{$this->filesPath}',
  ),
);
ALIAS;
  }
}
