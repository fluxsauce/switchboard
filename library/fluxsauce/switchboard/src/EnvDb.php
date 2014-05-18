<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class EnvDb extends Persistent {
  protected $environment_id;
  protected $external_key_name = 'environment_id';
}
