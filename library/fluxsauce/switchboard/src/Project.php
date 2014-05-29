<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class Project extends Persistent {
  protected $uuid;
  protected $site_id;
  protected $hostname;
  protected $ssh_port;
  protected $code_path;
  protected $database_host;
  protected $database_username;
  protected $database_password;
  protected $database_name;
  protected $database_port;
  protected $files_path;

  protected $external_key_name = 'name';

  /**
   * Get the minimal database specs.
   *
   * @return array
   *   Keys for database, host, user, password, port.
   */
  public function getDatabaseSpecs() {
    return array(
      'database' => $this->database_name,
      'host' => $this->database_host,
      'user' => $this->database_username,
      'password' => $this->database_password,
      'port' => $this->database_port,
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
    return "mysql://{$this->database_username}:{$this->database_password}@{$this->database_host}:{$this->database_port}/{$this->database_name}";
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
  'username' => '{$this->database_username}',
  'password' => '{$this->database_password}',
  'host' => '{$this->database_host}',
  'port' => '{$this->database_port}',
  'database' => '{$this->database_name}',
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
    return 'cd ' . $this->code_path . ' && git add --all . && git commit -m ' . drush_escapeshellarg($message);
  }

  /**
   * Generate command to pull code changes from remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPullCommand() {
    return 'git --git-dir=' . $this->code_path . '/.git pull origin master';
  }

  /**
   * Generate command to push code changes to remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPushCommand() {
    return 'git --git-dir=' . $this->code_path . '/.git push origin master';
  }

  /**
   * Generate command to reset all local code changes.
   *
   * @return string
   *   The git command.
   */
  public function vcsResetCommand() {
    return 'cd ' . $this->code_path . ' && git checkout .';
  }
}
