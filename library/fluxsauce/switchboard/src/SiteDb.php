<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class SiteDb extends Persistent {
  protected $environment_id;
  protected $external_key_name = 'environment_id';
}
