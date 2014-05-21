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
}
