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
}
