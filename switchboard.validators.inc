<?php
/**
 * @file
 * Custom validators for Switchboard.
 */

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
function switchboard_validate_site($provider_name, $site_name) {
  if (!$site_name) {
    return drush_set_error('SWITCHBOARD_PROVIDER_SITE_MISSING', dt('No site was specified.'));
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->site_exists($site_name)) {
    return switchboard_message_fail('SWITCHBOARD_PROVIDER_SITE_INVALID', dt('Site @site_name does not exist in provider @provider.', array(
      '@site_name' => $site_name,
      '@provider' => $provider_name,
    )));
  }
  return TRUE;
}

/**
 * Check to see if user is logged-in to provider.
 *
 * @param string $provider_name
 * @return boolean
 *   TRUE if logged-in to provider.
 */
function switchboard_validate_auth_logged_in($provider_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->auth_is_logged_in()) {
    return switchboard_message_fail('SWITCHBOARD_NOT_LOGGED_IN', dt('Not logged-in to @provider_name, cannot continue.', array(
      '@provider_name' => $provider_name,
    )));
  }
  return TRUE;
}

/**
 * Determine if a given provider name is in the list of known provider names.
 * @param $provider_name
 * @return boolean
 */
function switchboard_validate_provider_name($provider_name = FALSE) {
  if (!$provider_name) {
    return switchboard_message_fail('SWITCHBOARD_PROVIDER_MISSING', dt('Provider name not specified.'));
  }
  if (!in_array($provider_name, switchboard_get_provider_names())) {
    return switchboard_message_fail('SWITCHBOARD_PROVIDER_INVALID', dt('Invalid provider name @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
  return TRUE;
}

/**
 * Determine if an environment exists with a given site.
 * @param string $provider_name
 * @param string $site_name
 * @param string $env_name
 */
function switchboard_validate_site_env($provider_name, $site_name, $env_name) {
  if (!$env_name) {
    return switchboard_message_fail('SWITCHBOARD_SITE_ENV_MISSING', dt('Environment name not specified.'));
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!array_key_exists($env_name, $provider->sites[$site_name]->environments)) {
    return switchboard_message_fail('SWITCHBOARD_SITE_ENV_INVALID', dt('@env_name is not a valid environment for @site_name.', array(
      '@env_name' => $env_name,
      '@site_name' => $site_name,
    )));
  }
  return TRUE;
}

function switchboard_validate_destination($destination_path) {
  if (!$destination_path) {
    return switchboard_message_fail('SWITCHBOARD_DESTINATION_MISSING', dt('Destination path not specified.'));
  }
  if (is_writable($destination_path)) {
    return TRUE;
  }
  return switchboard_message_fail('SWITCHBOARD_DESTINATION_UNWRITEABLE', dt('@destination_path is not writeable.', array(
    '@destination_path' => $destination_path,
  )));
}

/**
 * Validate a standard 8-4-4-4-12 UUID.
 * @param $uuid
 * @return boolean
 */
function switchboard_validate_uuid($uuid) {
  return preg_match('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $uuid) ? TRUE : FALSE;
}

function switchboard_get_drush_options($command_name) {
  $command = switchboard_drush_command();
  if (isset($command[$command_name]['options'])) {
    $options = $command[$command_name]['options'];
    unset($options['json']);
    return array_keys($options);
  }
  return array();
}

function switchboard_validate_drush_option_required_values($command_name) {
  foreach (switchboard_get_drush_options($command_name) as $option_name) {
    if (is_bool(drush_get_option($option_name, 'dummy'))) {
      return FALSE;
    }
  }
  return TRUE;
}
