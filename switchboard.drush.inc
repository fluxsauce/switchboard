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

// Requests - Library used for HTTP requests.
if (!file_exists(__DIR__ . '/vendor/rmccue/requests/library/Requests.php')) {
  return drush_set_error('SWITCHBOARD_MISSING_REQUESTS', dt('Requests is not installed; please see README.md for instructions.'));
}
require 'vendor/autoload.php';

/**
 * Get the list of providers supported by Switchboard.
 *
 * @return array
 *   Machine names of providers.
 */
function switchboard_get_providers() {
  // Hard coding for now.
  // Later, be clever and scan the directory containing Provider definitions.
  $providers = array(
    'acquia',
    'pantheon',
  );
  return $providers;
}

function switchboard_get_provider($provider) {
  $class_name = '\Fluxsauce\Switchboard\Provider' . ucfirst($provider);
  if (drush_get_option('refresh')) {
    return new $class_name(TRUE);
  }
  return new $class_name();
}

function switchboard_get_provider_settings($provider) {
  return parse_ini_file('providers/' . $provider . '/provider.ini');
}

/**
 * Implements hook_drush_command().
 */
function switchboard_drush_command() {
  $items = array();

  $items['sw-auth-login'] = array(
    'description' => dt('Create session with a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
      'email' => dt('Email address of the account with the provider'),
      'password' => dt('Password for the account'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-list'] = array(
    'description' => dt('List available sites from a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
    ),
    'options' => array(
      'refresh' => dt('Refresh site list.'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-info'] = array(
    'description' => dt('Get site information'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
      'site_name' => dt('Name of the site'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-vcs'] = array(
    'description' => dt('Get the command for retrieving site code.'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
      'site_name' => dt('Name of the site'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-reset'] = array(
    'description' => dt('Reset the Switchboard brain.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  return $items;
}

function drush_switchboard_sw_reset() {
  Fluxsauce\Switchboard\Sqlite::siteDelete();
  drush_log('Switchboard brain deleted.', 'ok');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_login_validate($provider = FALSE, $email = FALSE, $password = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_EMAIL', dt('Invalid email; cannot authenticate.'));
  }
  if (!$password) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_PASSWORD', dt('Password missing; cannot authenticate.'));
  }
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  return call_user_func('switchboard_' . $provider . '_auth_login_validate', $email, $password);
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_login($provider, $email, $password) {
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  call_user_func('switchboard_' . $provider . '_auth_login', $email, $password);
  drush_log(dt('Logged into @provider as @email.', array(
    '@provider' => $provider,
    '@email' => $email,
  )), 'ok');
  // Update list of sites.
  $provider_object = switchboard_get_provider($provider);
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_logout_validate($provider = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  return call_user_func('switchboard_' . $provider . '_auth_logout_validate');
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_logout($provider) {
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  call_user_func('switchboard_' . $provider . '_auth_logout');
  drush_log(dt('Logged out of @provider.', array(
    '@provider' => $provider,
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
  if ($options['data']) {
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

  drush_print($url);

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

function drush_switchboard_sw_site_info_validate($provider = FALSE, $site_name = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider.'));
  }
  $provider_object = switchboard_get_provider($provider);
  if (!$provider_object->site_exists($site_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_SITE', dt('Invalid site; cannot continue.'));
  }
}

function drush_switchboard_sw_site_info($provider, $site_name) {
  $provider_object = switchboard_get_provider($provider);
  require_once('providers/' . $provider . '/' . $provider . '.sites.php');
  $site_info = call_user_func('switchboard_' . $provider . '_site_info', $site_name);
  $site = new Fluxsauce\Switchboard\Site($provider, $site_name);
  $site->update($site_info);
  $site->renderDrushTable();
}

function drush_switchboard_sw_site_list_validate($provider = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider.'));
  }
}

function drush_switchboard_sw_site_list($provider) {
  $provider_object = switchboard_get_provider($provider);

  $rows = array();
  foreach ($provider_object->sites as $site) {
    $rows[] = array(
      $site->name,
      $site->uuid,
    );
  }
  ksort($rows);
  array_unshift($rows, array(dt('Name'), dt('UUID')));
  drush_print_table($rows, TRUE);
}

function switchboard_pdo_exception_debug(PDOException $e) {
  return drush_set_error(dt('PDO Exception in @file on line @line: "@message"', array(
    '@file' => $e->getFile(),
    '@line' => $e->getLine(),
    '@message' => $e->getMessage(),
  )), 'error');
  exit;
}

function drush_switchboard_sw_site_vcs($provider, $site_name) {
  $site = switchboard_site_read($provider, $site_name);
  $command = '';
  switch ($site['vcs_type']) {
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
  if ($site['vcs_protocol'] == 'ssh') {
    $command .= 'ssh://';
  }
  $command .= $site['vcs_url'] . ' ' . $site_name;
  drush_print($command);
}
