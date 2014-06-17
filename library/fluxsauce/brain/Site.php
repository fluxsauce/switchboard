<?php
/**
 * @file
 * Remote Site.
 */

namespace Fluxsauce\Brain;

use Fluxsauce\Brain\om\BaseSite;
use Fluxsauce\Switchboard;

/**
 * Skeleton subclass for representing a row from the 'site' table.
 *
 * @package    propel.generator.brain
 */
class Site extends BaseSite {
  /**
   * Magic __get, overriding Persistent.
   *
   * @param string $name
   *   Name of the property.
   *
   * @return mixed
   *   Value of set property.
   * @throws \Exception
   */
  public function apiGetField($name) {
    $callers = debug_backtrace();
    drush_log(dt('Site @site_name is missing value for @name from @calling_function.', array(
      '@site_name' => $this->name,
      '@name' => $name,
      '@calling_function' => $callers[1]['function'],
    )));
    $provider = Switchboard\Provider::getInstance($this->getProvider());
    return $provider->siteGetField($this->getName(), $name);
  }

  /**
   * Get the UUID of a site; performs API call if necessary.
   *
   * @return string
   *   The UUID of the site.
   */
  public function getUuid() {
    $value = parent::getUuid();
    if (!$value) {
      $this->apiGetField('uuid');
      $value = parent::getUuid();
    }
    return $value;
  }

  /**
   * The realm of the site; performs API call if necessary.
   *
   * @return string
   *   The realm of the site.
   */
  public function getRealm() {
    $value = parent::getRealm();
    if (!$value) {
      $this->apiGetField('realm');
      $value = parent::getRealm();
    }
    return $value;
  }

  /**
   * The title of the site; performs API call if necessary.
   *
   * @return string
   *   The title of the site.
   */
  public function getTitle() {
    $value = parent::getTitle();
    if (!$value) {
      $this->apiGetField('title');
      $value = parent::getTitle();
    }
    return $value;
  }

  /**
   * Get the VCS URL for the site; performs API call if necessary.
   *
   * @return string
   *   The Version Control System URL for the site.
   */
  public function getVcsurl() {
    $value = parent::getVcsurl();
    if (!$value) {
      $this->apiGetField('vcsUrl');
      $value = parent::getVcsurl();
    }
    return $value;
  }

  /**
   * Get the VCS type for the site; performs API call if necessary.
   *
   * @return string
   *   The Version Control System type for the site.
   */
  public function getVcstype() {
    $value = parent::getVcstype();
    if (!$value) {
      $this->apiGetField('vcsType');
      $value = parent::getVcstype();
    }
    return $value;
  }

  /**
   * Get the VCS protocol for the site; performs API call if necessary.
   *
   * @return string
   *   The Version Control System protocol for the site.
   */
  public function getVcsprotocol() {
    $value = parent::getVcsprotocol();
    if (!$value) {
      $this->apiGetField('vcsProtocol');
      $value = parent::getVcsprotocol();
    }
    return $value;
  }

  /**
   * Get the SSH port for the site; performs API call if necessary.
   *
   * @return string
   *   The SSH port for the site.
   */
  public function getSshport() {
    $value = parent::getSshport();
    if (!$value) {
      $this->apiGetField('sshPort');
      $value = parent::getSshport();
    }
    return $value;
  }

  /**
   * Build VCS URL.
   *
   * @return string
   *   A full VCS connection URL.
   */
  public function getVcsConnection() {
    $url = '';
    if ($this->getVcsprotocol() == 'ssh') {
      $url .= 'ssh://';
    }
    $url .= $this->getVcsUrl();
    return $url;
  }

  /**
   * Render to standard output.
   */
  public function render() {
    if (drush_get_option('json')) {
      $this->renderJson();
    }
    else {
      $this->renderDrushTable();
    }
  }

  /**
   * Render as a Drush table.
   */
  public function renderDrushTable() {
    $fields = $this->toArray();
    $rows = array();
    $rows[] = array_keys($fields);
    $rows[] = array_values($fields);
    drush_print_table($rows, TRUE);
  }

  /**
   * Render as a JSON array.
   */
  public function renderJson() {
    drush_print($this->toJSON(FALSE));
  }
}
