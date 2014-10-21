<?php
/**
 * @file
 * User authorization callbacks.
 */

/**
 * Validation callback for drush sw-auth-login.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $email
 *   The email address used to login.
 * @param string $password
 *   The password used to login.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_auth_login_validate($provider_name = '', $email = '', $password = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if ($provider->authIsLoggedIn()) {
    return switchboard_message_fail('SWITCHBOARD_AUTH_LOGIN_ALREADY_LOGGEDIN', dt('Already logged-in to @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }

  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return switchboard_message_fail('SWITCHBOARD_AUTH_LOGIN_INVALID_EMAIL', dt('Invalid email; cannot authenticate.'));
  }

  if (!$password) {
    return switchboard_message_fail('SWITCHBOARD_AUTH_LOGIN_INVALID_PASSWORD', dt('Password missing; cannot authenticate.'));
  }
}

/**
 * Command callback for drush sw-auth-login.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $email
 *   The email address used to login.
 * @param string $password
 *   The password used to login.
 */
function drush_switchboard_sw_auth_login($provider_name, $email, $password) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  $result = $provider->authLogin($email, $password);
  if ($result) {
    switchboard_message_success(dt('Logged into @provider_name as @email.', array(
      '@provider_name' => $provider_name,
      '@email' => $email,
    )));
  }
  else {
    switchboard_message_fail('SWITCHBOARD_AUTH_LOGIN_FAILURE', dt('Unable to login to @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Validation callback for drush sw-auth-logout.
 *
 * @param string $provider_name
 *   The name of the Provider.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_auth_logout_validate($provider_name = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->authIsLoggedIn()) {
    return switchboard_message_fail('SWITCHBOARD_AUTH_LOGOUT_ALREADY_LOGGEDOUT', dt('Already logged-out of @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Command callback for drush sw-auth-logout.
 *
 * @param string $provider_name
 *   The name of the Provider.
 */
function drush_switchboard_sw_auth_logout($provider_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->authLogout();
  switchboard_message_success(dt('Logged out of @provider.', array(
    '@provider' => $provider_name,
  )));
}
