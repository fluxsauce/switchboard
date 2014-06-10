<?php
/**
 * @file
 * Site environment.
 */

namespace Fluxsauce\Switchboard;

/**
 * Site environment.
 */
class Environment extends Persistent {
  /**
   * @var int External key to the Site.
   */
  protected $siteId;

  /**
   * @var string The hostname for the Environment.
   */
  protected $host;

  /**
   * @var string The UNIX username for the Environment.
   */
  protected $username;

  /**
   * @var string The default VCS branch for the Environment.
   */
  protected $branch;

  /**
   * @var string Metadata for ORM defining database structure.
   */
  protected $externalKeyName = 'siteId';
}
