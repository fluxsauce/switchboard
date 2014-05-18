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
function switchboard_validate_provider_site($provider_name = FALSE, $site_name = FALSE) {
  if (!$site_name) {
    return drush_set_error('SWITCHBOARD_PROVIDER_SITE_MISSING', dt('No site was specified.'));
  }
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->site_exists($site_name)) {
    return drush_set_error('SWITCHBOARD_PROVIDER_SITE_INVALID', dt('Site @site_name does not exist in provider @provider.', array(
      '@site_name' => $site_name,
      '@provider' => $provider_name,
    )));
  }
  return TRUE;
}

function switchboard_validate_auth_logged_in($provider_name = FALSE) {
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->auth_is_logged_in()) {
    return drush_set_error('SWITCHBOARD_NOT_LOGGED_IN', dt('Not logged-in to @provider_name, cannot continue.', array(
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
    return drush_set_error('SWITCHBOARD_PROVIDER_MISSING', dt('Provider name not specified.'));
  }
  if (!in_array($provider_name, switchboard_get_provider_names())) {
    return drush_set_error('SWITCHBOARD_PROVIDER_INVALID', dt('Invalid provider name.'));
  }
  return TRUE;
}
