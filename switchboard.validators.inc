<?php
/**
 * @file
 * Custom validators for Switchboard.
 */

/**
 * Validate a site name; assuming a-z, 0-9 and dash, 255 chars max.
 *
 * @param string $site_name
 * @return bool
 */
function switchboard_validate_site_name($site_name) {
  return preg_match('/^[-a-z0-9]{1,255}$/', $site_name) ? TRUE : FALSE;
}

/**
 * Check to see if a site exists within a particular provider.
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
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  return $provider->siteExists($site_name);
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
  return $provider->authIsLoggedIn();
}

/**
 * Determine if a given provider name is in the list of known provider names.
 * @param $provider_name
 * @return boolean
 */
function switchboard_validate_provider_name($provider_name) {
  if (!$provider_name) {
    return FALSE;
  }
  return in_array($provider_name, switchboard_get_provider_names());
}

/**
 * Determine if an environment exists with a given site.
 *
 * @param string $provider_name
 * @param string $site_name
 * @param string $env_name
 */
function switchboard_validate_site_env($provider_name, $site_name, $env_name) {
  if (!$env_name) {
    return FALSE;
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  return array_key_exists($env_name, $provider->sites[$site_name]->environments);
}

function switchboard_validate_destination($destination_path) {
  if (!$destination_path) {
    return FALSE;
  }
  if (!file_exists($destination_path)) {
    return TRUE;
  }
  return is_writable($destination_path);
}

/**
 * Validate a standard 8-4-4-4-12 UUID.
 * @param $uuid
 * @return boolean
 */
function switchboard_validate_uuid($uuid) {
  return preg_match('#^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$#', $uuid) ? TRUE : FALSE;
}

/**
 * Ensure that every option has values.
 *
 * @param string $command_name
 * @return bool
 */
function switchboard_validate_drush_option_required_values($command_name) {
  foreach (switchboard_get_drush_options($command_name) as $option_name => $option) {
    if (is_array($option)) {
      if (isset($option['value']) && $option['value'] == 'required') {
        if (is_bool(drush_get_option($option_name, 'dummy'))) {
          return FALSE;
        }
      }
    }
  }
  return TRUE;
}
