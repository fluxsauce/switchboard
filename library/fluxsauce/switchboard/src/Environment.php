<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class Environment extends Persistent {
  protected $siteId;
  protected $host;
  protected $username;
  protected $branch;

  protected $externalKeyName = 'siteId';
}
