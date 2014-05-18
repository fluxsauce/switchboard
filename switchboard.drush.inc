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
require_once SWITCHBOARD_BASE_PATH . '/vendor/autoload.php';

// Common functions.
require_once SWITCHBOARD_BASE_PATH . '/switchboard.validators.inc';
require_once SWITCHBOARD_BASE_PATH . '/switchboard.utilities.inc';

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

  $items['sw-site-env-db-backup'] = array(
    'description' => dt('Get URL of latest database backup.'),
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
    $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
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
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
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
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  $result = $provider->auth_login($email, $password);
  if ($result) {
    drush_log(dt('Logged into @provider_name as @email.', array(
      '@provider_name' => $provider_name,
      '@email' => $email,
    )), 'ok');
    // Update list of sites.
    drush_set_option('refresh', TRUE);
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
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
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
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->auth_logout();
  drush_log(dt('Logged out of @provider.', array(
    '@provider' => $provider_name,
  )), 'ok');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_list_validate($provider_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_list($provider_name) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  if (count($provider->sites) == 0) {
    $provider->api_get_sites();
  }
  if (count($provider->sites) == 0) {
    return drush_print(dt('No sites.'));
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
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_env_list_validate($provider_name = FALSE, $site_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_env_list($provider_name, $site_name) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->sites[$site_name]->renderEnvironmentsDrushTable();
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_info_validate($provider_name = FALSE, $site_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_info($provider_name, $site_name) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->sites[$site_name]->renderDrushTable();
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_vcs_validate($provider_name = FALSE, $site_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_vcs($provider_name, $site_name) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
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
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_delete($provider_name, $site_name) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->site_delete($site_name);
  drush_log(dt('Locally deleted @site_name from @provider.', array(
    '@site_name' => $site_name,
    '@provider' => $provider_name,
  )), 'ok');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_env_db_backup_validate($provider_name = FALSE, $site_name = FALSE, $env_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return FALSE;
  }
  if (!switchboard_validate_site_env($provider_name, $site_name, $env_name)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_env_db_backup($provider_name, $site_name, $env_name) {

}
