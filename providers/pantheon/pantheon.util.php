<?php

/**
 * Get Pantheon specific options for Requests
 * @return array
 */
function switchboard_pantheon_requests_options() {
  $cookies = drush_cache_get('session', 'switchboard-auth-pantheon');
  $options = array(
    'cookies' => array($cookies->data),
  );
  return $options;
}

/**
 * Validate Atlas UUID.
 * @param $uuid
 * @return boolean
 */
function switchboard_pantheon_validate_uuid($uuid) {
  return preg_match('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $uuid) ? TRUE : FALSE;
}

/**
 * Parse form build ID, based on terminus_pauth_login_get_form_build_id().
 * https://github.com/pantheon-systems/terminus
 *
 * @param $html
 * @return string
 */
function switchboard_pantheon_auth_login_get_form_build_id($html) {
  if (!$html) {
    return FALSE;
  }
  // Parse form build ID.
  $DOM = new DOMDocument;
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

/**
 * Parse session out of a header, based on terminus_pauth_get_session_from_headers().
 * https://github.com/pantheon-systems/terminus
 *
 * @param array $headers
 * @return string
 */
function switchboard_pantheon_auth_login_get_session_from_headers($headers) {
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
