<?php

function switchboard_pantheon_auth_login_validate($email, $password) {
  $session = drush_cache_get('session', 'switchboard-auth-pantheon');
  if ($session->data) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_ALREADY_LOGGEDIN', dt('Already logged-in to Pantheon.'));
  }
}

function switchboard_pantheon_auth_login($email, $password) {
  require_once 'pantheon.util.php';
  $provider_settings = switchboard_get_provider_settings('pantheon');

  $url = $provider_settings['endpoint'] . '/login';

  // Get the form build ID.
  try {
    $response = Requests::post($url);
  }
  catch (Requests_Exception $e) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_ENDPOINT_UNAVAILABLE', dt('Pantheon endpoint unavailable: @error', array(
      '@error' => $e->getMessage(),
    )));
  }
  $form_build_id = switchboard_pantheon_auth_login_get_form_build_id($response->body);
  if (!$form_build_id) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_LOGIN_UNAVAILABLE', dt('Pantheon login unavailable.'));
  }

  // Attempt to log in.
  try {
    $response = Requests::post($url, array(), array(
      'email' => $email,
      'password' => $password,
      'form_build_id' => $form_build_id,
      'form_id' => 'atlas_login_form',
      'op' => 'Login',
    ), switchboard_requests_options('pantheon', array('follow_redirects' => FALSE)));
  }
  catch (Requests_Exception $e) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_LOGIN_FAILURE', dt('Pantheon login failure: @error', array(
      '@error' => $e->getMessage(),
    )));
  }

  $session = switchboard_pantheon_auth_login_get_session_from_headers($response->headers->getValues('set-cookie'));

  if (!$session) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_SESSION', dt('Pantheon Session not found; please check your credentials and try again.'));
  }

  // Get the UUID.
  $user_uuid = array_pop(explode('/', $response->headers->offsetGet('Location')));
  if (!switchboard_pantheon_validate_uuid($user_uuid)) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_PANTHEON_NO_UUID', dt('Pantheon User UUID not found; please check your credentials and try again.'));
  }

  drush_cache_clear_all('*', 'switchboard-auth-pantheon', TRUE);
  drush_cache_set('user_uuid', $user_uuid, 'switchboard-auth-pantheon');
  drush_cache_set('session', $session, 'switchboard-auth-pantheon');
  drush_cache_set('email', $email, 'switchboard-auth-pantheon');
}

function switchboard_pantheon_auth_logout_validate() {
  $session = drush_cache_get('session', 'switchboard-auth-pantheon');
  if (!$session->data) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGOUT_PANTHEON_ALREADY_LOGGEDOUT', dt('Already logged-out of Pantheon.'));
  }
}

function switchboard_pantheon_auth_logout() {
  drush_cache_clear_all('*', 'switchboard-auth-pantheon', TRUE);
}
