<?php
/**
 * @file
 * Site environment backup.
 */

namespace Fluxsauce\Switchboard;

/**
 * Site environment backup.
 */
class EnvBackup extends Persistent {
  /**
   * @var int External key to the Environment.
   */
  protected $environmentId;

  /**
   * @var string The type of backup.
   */
  protected $type;

  /**
   * @var int UNIX timestamp for creation time.
   */
  protected $created;

  /**
   * @var string Metadata for ORM defining database structure.
   */
  protected $externalKeyName = 'environmentId';
}
