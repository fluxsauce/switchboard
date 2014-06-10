<?php
/**
 * @file
 * Site environment database.
 */

namespace Fluxsauce\Switchboard;

/**
 * Site environment database.
 */
class EnvDb extends Persistent {
  /**
   * @var int External key to the Environment.
   */
  protected $environmentId;

  /**
   * @var string Metadata for ORM defining database structure.
   */
  protected $externalKeyName = 'environmentId';
}
