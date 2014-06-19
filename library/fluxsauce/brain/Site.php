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
    $provider->siteGetField($this->getName(), $name);
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
   * Render site environments to standard output.
   */
  public function renderEnvironments() {
    if (drush_get_option('json')) {
      $this->renderEnvironmentsJson();
    }
    else {
      $this->renderEnvironmentsDrushTable();
    }
  }

  /**
   * Render a Site's environments as a Drush table.
   */
  public function renderEnvironmentsDrushTable() {
    $environments = $this->getEnvironments();
    if (count($environments) == 0) {
      return;
    }
    $rows = array();
    foreach ($environments as $environment) {
      $fields = $environment->toArray();
      $rows[] = array_values($fields);
    }
    array_unshift($rows, array_keys($fields));
    drush_print_table($rows, TRUE);
  }

  /**
   * Render a Site's environments as JSON.
   */
  public function renderEnvironmentsJson() {
    $environments = $this->getEnvironments();
    if (count($environments) == 0) {
      return;
    }
    $rows = array();
    foreach ($environments as $environment) {
      $rows[] = $environment->toArray();
    }
    drush_print(json_encode($rows));
  }

  /**
   * Gets an array of Environment objects which reference a Site.
   *
   * @param Criteria $criteria
   *   Optional Criteria object to narrow the query.
   * @param PropelPDO $con
   *   Optional connection object.
   *
   * @return PropelObjectCollection|Environment[]
   *   List of Environment objects.
   * @throws PropelException
   */
  public function getEnvironments($criteria = NULL, PropelPDO $con = NULL) {
    $environments = parent::getEnvironments($criteria, $con);
    if (count($environments) == 0) {
      $this->apiGetField('environments');
      $environments = parent::getEnvironments($criteria, $con);
    }
    return $environments;
  }
}
