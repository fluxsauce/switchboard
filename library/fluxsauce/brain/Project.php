<?php
/**
 * @file
 * Project.
 */

namespace Fluxsauce\Brain;

use Fluxsauce\Brain\om\BaseProject;

/**
 * Skeleton subclass for representing a row from the 'project' table.
 *
 * @package    propel.generator.brain
 */
class Project extends BaseProject {
  /**
   * Get the minimal database specs.
   *
   * @return array
   *   Keys for database, host, user, password, port.
   */
  public function getDatabaseSpecs() {
    return array(
      'database' => $this->getDatabasename(),
      'host' => $this->getDatabasehost(),
      'user' => $this->getDatabaseusername(),
      'password' => $this->getDatabasepassword(),
      'port' => $this->getDatabaseport(),
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
    return "mysql://{$this->getDatabaseusername()}:{$this->getDatabasepassword()}@{$this->getDatabasehost()}:{$this->getDatabaseport()}/{$this->getDatabasename()}";
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
  'username' => '{$this->getDatabaseusername()}',
  'password' => '{$this->getDatabasepassword()}',
  'host' => '{$this->getDatabasehost()}',
  'port' => '{$this->getDatabaseport()}',
  'database' => '{$this->getDatabasename()}',
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
    return 'cd ' . $this->getCodepath() . ' && git add --all . && git commit -m ' . drush_escapeshellarg($message);
  }

  /**
   * Generate command to pull code changes from remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPullCommand() {
    return 'git --git-dir=' . $this->getCodepath() . '/.git pull origin master';
  }

  /**
   * Generate command to push code changes to remote.
   *
   * @return string
   *   The git command.
   */
  public function vcsPushCommand() {
    return 'git --git-dir=' . $this->getCodepath() . '/.git push origin master';
  }

  /**
   * Generate command to reset all local code changes.
   *
   * @return string
   *   The git command.
   */
  public function vcsResetCommand() {
    return 'cd ' . $this->getCodepath() . ' && git checkout .';
  }

  /**
   * Renders a project as a Drush alias.
   *
   * @return string
   *   Drush alias.
   */
  public function toDrushAlias() {
    return <<<ALIAS
\$aliases['{$this->getName()}'] = array(
  'root' => '{$this->getCodepath()}',
  'uri' => '{$this->getHostname()}',
  'db-url' => '{$this->getDatabaseUrl()}',
  'remote-host' => '{$this->getHostname()}',
  'remote-user' => '{$this->getUsername()}',
  'ssh-options' => '-p {$this->getSshport()}',
  'path-aliases' => array(
    '%files' => '{$this->getFilespath()}',
  ),
);
ALIAS;
  }
}
