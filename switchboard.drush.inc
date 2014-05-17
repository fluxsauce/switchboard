<?php
/**
 * @file
 * Switchboard, the host agnostic development facilitator.
 *
 * Copyright (C) 2014 Jonathan Peck, jpeck@fluxsauce.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
date_default_timezone_set('UTC');
define('SWITCHBOARD_BASE_PATH', __DIR__);

// PSR-4 autoloading.
if (!file_exists(SWITCHBOARD_BASE_PATH . '/vendor/autoload.php')) {
  return drush_set_error('SWITCHBOARD_MISSING_COMPOSER', dt('Composer autoloader is missing; please see README.md for installation instructions.'));
}
require SWITCHBOARD_BASE_PATH . '/vendor/autoload.php';

/**
 * Get the list of providers supported by Switchboard.
 *
 * @return array
 *   Machine names of providers.
 */
function switchboard_get_provider_names() {
  // Hard coding for now.
  // Later, be clever and scan the directory containing Provider definitions.
  $provider_names = array(
    'acquia',
    'pantheon',
  );
  return $provider_names;
}

/**
 * Determine if a given provider name is in the list of known provider names.
 * @param $provider_name
 * @return boolean
 */
function switchboard_is_valid_provider_name($provider_name) {
  return in_array($provider_name, switchboard_get_provider_names()) ? TRUE : FALSE;
}

/**
 * Pseudo factory method for getting a Provider.
 * @param $provider_name
 * @return mixed
 */
function switchboard_get_provider($provider_name) {
  $class_name = '\Fluxsauce\Switchboard\Provider' . ucfirst($provider_name);
  if (drush_get_option('refresh')) {
    return new $class_name(TRUE);
  }
  $provider = new $class_name();
  drush_set_option('provider', $provider);
  return $provider;
}

/**
 * Implements hook_drush_command().
 */
function switchboard_drush_command() {
  $items = array();

  $default_arguments = array(
    'provider_name' => dt('Name of the provider.'),
  );
  $default_site_arguments = array(
    'site_name' => dt('Name of the site.'),
  ) + $default_arguments;
  $default_options = array(
    'refresh' => dt('Refresh all remote data.'),
  );

  $items['sw-provider-list'] = array(
    'description' => dt('List all available providers.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-login'] = array(
    'description' => dt('Create session with a provider.'),
    'arguments' => array(
      'email' => dt('Email address of the account with the provider.'),
      'password' => dt('Password for the account.'),
    ) + $default_arguments,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider.'),
    'arguments' => $default_arguments,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-list'] = array(
    'description' => dt('List available sites from a provider.'),
    'arguments' => $default_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-delete'] = array(
    'description' => dt('Locally delete site.'),
    'arguments' => $default_site_arguments,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-env-list'] = array(
    'description' => dt('List available site environments from a provider.'),
    'arguments' => $default_site_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-info'] = array(
    'description' => dt('Get site information.'),
    'arguments' => $default_site_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-vcs'] = array(
    'description' => dt('Get the command for retrieving site code.'),
    'arguments' => $default_site_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-reset'] = array(
    'description' => dt('Reset the Switchboard brain.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  return $items;
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_provider_list() {
  $provider_names = switchboard_get_provider_names();
  $rows = array();
  foreach ($provider_names as $provider_name) {
    $provider = switchboard_get_provider($provider_name);
    $rows[$provider->name] = array(
      $provider->name,
      $provider->label,
      $provider->homepage,
    );
  }
  ksort($rows);
  array_unshift($rows, array(dt('Name'), dt('Label'), dt('Homepage')));
  drush_print_table($rows, TRUE);
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_reset_validate() {
  $confirm = drush_confirm(dt('Are you sure you want to reset the Switchboard brain?'));
  if (!$confirm) {
    return drush_user_abort();
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_reset() {
  Fluxsauce\Switchboard\Sqlite::delete();
  drush_log('Switchboard brain ablation completed.', 'ok');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_login_validate($provider_name = FALSE, $email = FALSE, $password = FALSE) {
  if (!switchboard_is_valid_provider_name($provider_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  $provider = switchboard_get_provider($provider_name);
  if ($provider->auth_is_logged_in()) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_ALREADY_LOGGEDIN', dt('Already logged-in to @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }

  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_EMAIL', dt('Invalid email; cannot authenticate.'));
  }

  if (!$password) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_PASSWORD', dt('Password missing; cannot authenticate.'));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_login($provider_name, $email, $password) {
  $provider = drush_get_option('provider');

  $result = $provider->auth_login($email, $password);
  if ($result) {
    drush_log(dt('Logged into @provider_name as @email.', array(
      '@provider_name' => $provider_name,
      '@email' => $email,
    )), 'ok');
    // Update list of sites.
    drush_set_option('refresh', TRUE);
    $provider = switchboard_get_provider($provider_name);
  }
  else {
    drush_set_error('SWITCHBOARD_AUTH_LOGIN_FAILURE', dt('Unable to login to @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_logout_validate($provider_name = FALSE) {
  if (!switchboard_is_valid_provider_name($provider_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  $provider = switchboard_get_provider($provider_name);
  if (!$provider->auth_is_logged_in()) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGOUT_ALREADY_LOGGEDOUT', dt('Already logged-out of @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_logout($provider_name) {
  $provider = drush_get_option('provider');
  $provider->auth_logout();
  drush_log(dt('Logged out of @provider.', array(
    '@provider' => $provider_name,
  )), 'ok');
}

/**
 * Send a request.
 * @param \Fluxsauce\Switchboard\Provider $provider
 * @param $options
 * @return bool|Requests_Response
 */
function switchboard_request($provider, $options) {
  $url = $provider->endpoint;

  if ($provider->name == 'pantheon') {
    $url .= '/terminus.php?';
  }

  $url .= $options['resource'];

  if ($provider->name == 'pantheon') {
    $url .= '=' . $options['uuid'];
    $url .= '&path=' . $options['realm'];
  }
  if ($provider->name == 'acquia') {
    $url .= '.json';
  }

  $requests_options = $provider->requests_options();

  if (!in_array($options['method'], array('GET', 'DELETE', 'POST', 'PUT'))) {
    return drush_set_error('SWITCHBOARD_REQUEST_INVALID_METHOD');
  }
  $headers = array();

  // Prepare data for transport.
  $data = NULL;
  if (isset($options['data'])) {
    $data = json_encode(array('data' => $options['data']));
    if (in_array($options['method'], array('POST', 'PUT', 'DELETE'))) {
      $headers = array(
        'Content-Type' => 'application/json',
        'Content-Length' => strlen($data),
      );
    }
    else {
      if (in_array($options['method'], array('GET'))) {
        $url .= '?' . http_build_query($data);
      }
    }
  }

  drush_log($url);

  try {
    switch ($options['method']) {
      case 'GET':
        $response = Requests::get($url, $headers, $requests_options);
        break;

      case 'DELETE':
        $response = Requests::delete($url, $headers, $requests_options);
        break;

      case 'POST':
        $response = Requests::post($url, $headers, $data, $requests_options);
        break;

      case 'PUT':
        $response = Requests::put($url, $headers, $data, $requests_options);
        break;

    }
  } catch (Requests_Exception $e) {
    return drush_set_error('SWITCHBOARD_REQUEST_FAIL', dt('API failure: @error', array(
      '@error' => $e->getMessage(),
    )));
  }
  return $response;
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_list_validate($provider_name = FALSE) {
  if (!switchboard_is_valid_provider_name($provider_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider name.'));
  }
  $provider = switchboard_get_provider($provider_name);
  if (!$provider->auth_is_logged_in()) {
    return drush_set_error('SWITCHBOARD_NOT_LOGGED_IN', dt('Not logged-in to @provider_name, cannot continue.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_list($provider_name) {
  $provider = drush_get_option('provider');

  if (count($provider->sites) == 0) {
    return drush_print('No sites.');
  }

  $rows = array();
  foreach ($provider->sites as $site) {
    $rows[] = array(
      $site->name,
      $site->uuid,
    );
  }
  ksort($rows);
  array_unshift($rows, array(dt('Name'), dt('UUID')));
  drush_print_table($rows, TRUE);
}

/**
 * Reusable validator for provider, site and login check.
 *
 * @param string $provider_name
 *   Name of the provider.
 * @param string $site_name
 *   Name of the site.
 *
 * @return boolean
 *   TRUE if all checks pass.
 */
function switchboard_validate_provider_site($provider_name = FALSE, $site_name = FALSE) {
  if (!switchboard_is_valid_provider_name($provider_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider name.'));
  }
  $provider = switchboard_get_provider($provider_name);
  if (!$provider->site_exists($site_name)) {
    return drush_set_error('SWITCHBOARD_PROVIDER_INVALID_SITE', dt('Site @site_name does not exist in provider @provider.', array(
      '@site_name' => $site_name,
      '@provider' => $provider_name,
    )));
  }
  if (!$provider->auth_is_logged_in()) {
    return drush_set_error('SWITCHBOARD_NOT_LOGGED_IN', dt('Not logged-in to @provider_name, cannot continue.', array(
      '@provider_name' => $provider_name,
    )));
  }
  return TRUE;
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_env_list_validate($provider_name = FALSE, $site_name = FALSE) {
  return switchboard_validate_provider_site($provider_name, $site_name);
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_env_list($provider_name = FALSE, $site_name = FALSE) {
  $provider = drush_get_option('provider');
  drush_print_r($provider->sites[$site_name]->environments);
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_info_validate($provider_name = FALSE, $site_name = FALSE) {
  return switchboard_validate_provider_site($provider_name, $site_name);
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_info($provider_name, $site_name) {
  $provider = drush_get_option('provider');
  $provider->sites[$site_name]->renderDrushTable();
}

/**
 * Helper for debugging PDO exceptions.
 * @param PDOException $e
 */
function switchboard_pdo_exception_debug(PDOException $e) {
  drush_set_error(dt('PDO Exception in @file on line @line: "@message"', array(
    '@file' => $e->getFile(),
    '@line' => $e->getLine(),
    '@message' => $e->getMessage(),
  )), 'error');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_vcs_validate($provider_name = FALSE, $site_name = FALSE) {
  return switchboard_validate_provider_site($provider_name, $site_name);
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_vcs($provider_name, $site_name) {
  $provider = drush_get_option('provider');
  $site = $provider->sites[$site_name];
  $command = '';
  switch ($site->vcs_type) {
    case 'git':
      $command .= 'git clone';
      break;
    case 'svn':
      $command .= 'svn checkout';
      break;
    default:
      return drush_set_error('SWITCHBOARD_SITE_VCS_INVALID_PROTOCOL', dt('Invalid VCS type for site.'));
  }
  $command .= ' ';
  if ($site->vcs_protocol == 'ssh') {
    $command .= 'ssh://';
  }
  $command .= $site->vcs_url . ' ' . $site_name;
  drush_print($command);
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_delete_validate($provider_name = FALSE, $site_name = FALSE) {
  if (!switchboard_is_valid_provider_name($provider_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  $provider = switchboard_get_provider($provider_name);
  if (!$provider->site_exists($site_name)) {
    return drush_set_error('SWITCHBOARD_PROVIDER_INVALID_SITE', dt('Site @site_name does not exist in provider @provider.', array(
      '@site_name' => $site_name,
      '@provider' => $provider_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_delete($provider_name, $site_name) {
  $provider = drush_get_option('provider');
  $provider->site_delete($site_name);
  drush_log(dt('Locally deleted @site_name from @provider.', array(
    '@site_name' => $site_name,
    '@provider' => $provider_name,
  )), 'ok');
}
