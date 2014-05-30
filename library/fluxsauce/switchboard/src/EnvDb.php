<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

class EnvDb extends Persistent {
  protected $environmentId;
  protected $externalKeyName = 'environmentId';
}
