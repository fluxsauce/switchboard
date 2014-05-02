<?php

function switchboard_acquia_auth_login_validate($email, $password) {
  $email = drush_cache_get('email', 'switchboard-auth-acquia');
  $password = drush_cache_get('password', 'switchboard-auth-acquia');
  if ($email->data && $password->data) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_ACQUIA_ALREADY_LOGGEDIN', dt('Already logged-in to Acquia.'));
  }
}

function switchboard_acquia_auth_login($email, $password) {
  drush_cache_clear_all('*', 'switchboard-auth-acquia', TRUE);
  drush_cache_set('email', $email, 'switchboard-auth-acquia');
  drush_cache_set('password', $password, 'switchboard-auth-acquia');
}

function switchboard_acquia_auth_logout_validate() {
  $email = drush_cache_get('email', 'switchboard-auth-acquia');
  $password = drush_cache_get('password', 'switchboard-auth-acquia');
  if (!$email->data || !$password->data) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGOUT_ACQUIA_ALREADY_LOGGEDOUT', dt('Already logged-out of Acquia.'));
  }
}

function switchboard_acquia_auth_logout() {
  drush_cache_clear_all('*', 'switchboard-auth-acquia', TRUE);
}
