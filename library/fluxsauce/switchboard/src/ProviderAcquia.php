<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class ProviderAcquia extends Provider {
  protected $name = 'acquia';
  protected $label = 'Acquia';
  protected $homepage = 'http://www.acquia.com/';
  protected $endpoint = 'https://cloudapi.acquia.com/v1';

  public function api_get_sites() {
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'resource' => '/sites',
    ));
    $site_names = json_decode($result->body);
    $sites = array();
    foreach ($site_names as $site_data) {
      list($realm, $site_name) = explode(':', $site_data);
      $site = new Site($this->name, $site_name);
      $site->realm = $realm;
      $site->update();
      $this->sites[$site->name] = $site;
    }
  }

  public function requests_options_custom() {
    $email = drush_cache_get('email', 'switchboard-auth-acquia');
    $password = drush_cache_get('password', 'switchboard-auth-acquia');
    $options = array(
      'auth' => new \Requests_Auth_Basic(array(
          $email->data,
          $password->data,
        )),
    );
    return $options;
  }

  public function auth_login($email, $password) {
    drush_cache_clear_all('*', 'switchboard-auth-acquia', TRUE);
    drush_cache_set('email', $email, 'switchboard-auth-acquia');
    drush_cache_set('password', $password, 'switchboard-auth-acquia');
    return TRUE;
  }

  public function auth_is_logged_in() {
    $email = drush_cache_get('email', 'switchboard-auth-acquia');
    $password = drush_cache_get('password', 'switchboard-auth-acquia');
    return ($email->data && $password->data) ? TRUE : FALSE;
  }
}
