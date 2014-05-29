<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class Environment extends Persistent {
  protected $site_id;
  protected $host;
  protected $username;
  protected $branch;

  protected $external_key_name = 'site_id';
}
