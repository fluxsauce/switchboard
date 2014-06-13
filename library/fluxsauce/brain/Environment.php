<?php
/**
 * @file
 * A remote site environment.
 */

namespace Fluxsauce\Brain;

use Fluxsauce\Brain\om\BaseEnvironment;


/**
 * Skeleton subclass for representing a row from the 'environment' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.brain
 */
class Environment extends BaseEnvironment {
  /**
   * Set the time updated when saving.
   *
   * @return bool
   *   Always TRUE.
   */
  public function preSave() {
    $this->setUpdated(time());
    return TRUE;
  }
}
