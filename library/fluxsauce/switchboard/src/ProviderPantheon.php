<?php
/**
 * @file
 */

namespace Fluxsauce\Switchboard;

class ProviderPantheon extends Provider {
  protected $name = 'pantheon';
  protected $label = 'Pantheon';
  protected $homepage = 'https://www.getpantheon.com/';
  protected $endpoint = 'https://terminus.getpantheon.com';

  public function api_get_sites() {
    $user_uuid = drush_cache_get('user_uuid', 'switchboard-auth-pantheon');
    $result = switchboard_request($this, array(
      'method' => 'GET',
      'realm' => 'sites',
      'resource' => 'user',
      'uuid' => $user_uuid->data,
    ));
    $site_metadata = json_decode($result->body);

    $sites = array();

    foreach ($site_metadata as $uuid => $data) {
      $site = new Site($this->name, $data->information->name);
      $site->uuid = $uuid;
      $site->realm = $data->information->preferred_zone;
      $site->update();
      $this->sites[$site->name] = $site;
    }
  }

  public function requests_options_custom() {
    $cookies = drush_cache_get('session', 'switchboard-auth-pantheon');
    $options = array(
      'cookies' => array($cookies->data),
    );
    return $options;
  }

  /**
   * Parse session out of a header, based on terminus_pauth_get_session_from_headers().
   * https://github.com/pantheon-systems/terminus
   *
   * @param array $headers
   * @return string
   */
  function auth_login_get_session_from_headers($headers) {
    $session = FALSE;
    foreach ($headers as $header) {
      foreach (explode('; ', $header) as $cookie) {
        if (strpos($cookie, 'SSESS') === 0) {
          $session = $cookie;
        }
      }
    }
    return $session;
  }

  /**
   * Parse form build ID, based on terminus_pauth_login_get_form_build_id().
   * https://github.com/pantheon-systems/terminus
   *
   * @param $html
   * @return string
   */
  public function auth_login_get_form_build_id($html) {
    if (!$html) {
      return FALSE;
    }
    // Parse form build ID.
    $DOM = new \DOMDocument;
    @$DOM->loadHTML($html);
    $login_form = $DOM->getElementById('atlas-login-form');
    if (!$login_form) {
      return FALSE;
    }

    foreach ($login_form->getElementsByTagName('input') as $input) {
      if ($input->getAttribute('name') == 'form_build_id') {
        return $input->getAttribute('value');
      }
    }
    return FALSE;
  }
}
