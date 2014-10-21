<?php
/**
 * @file
 * Site callbacks.
 */

/**
 * Validation callback for drush sw-site-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_list_validate($provider_name = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
}

/**
 * Command callback for drush sw-site-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 */
function drush_switchboard_sw_site_list($provider_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  $sites = \Fluxsauce\Brain\SiteQuery::create()
    ->filterByProvider($provider_name)
    ->find();

  if (count($sites) == 0) {
    $provider->apiGetSites();
    $sites = \Fluxsauce\Brain\SiteQuery::create()
      ->filterByProvider($provider_name)
      ->find();
  }
  if (count($sites) == 0) {
    if (drush_get_option('json')) {
      return drush_print(json_encode(array()));
    }
    return drush_print(dt('No sites.'));
  }

  $rows = array();
  foreach ($sites as $site) {
    $rows[$site->getName()] = array(
      'name' => $site->getName(),
      'uuid' => $site->getUuid(),
    );
  }
  ksort($rows);
  if (drush_get_option('json')) {
    drush_print(json_encode($rows));
  }
  else {
    array_unshift($rows, array(dt('Name'), dt('UUID')));
    drush_print_table($rows, TRUE);
  }
}

/**
 * Validation callback for drush sw-site-env-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_env_list_validate($provider_name = '', $site_name = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_site_name($site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_NAME_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
}

/**
 * Command callback for drush sw-site-env-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_env_list($provider_name, $site_name) {
  $site = \Fluxsauce\Brain\SiteQuery::create()
    ->filterByProvider($provider_name)
    ->filterByName($site_name)
    ->findOne();
  $environments = $site->getEnvironments();
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (drush_get_option('json')) {
    $provider->sites[$site_name]->renderEnvironmentsJson();
  }
  else {
    $provider->sites[$site_name]->renderEnvironmentsDrushTable();
  }
}

/**
 * Validation callback for drush sw-site-info.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_info_validate($provider_name = '', $site_name = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_site_name($site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_NAME_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
}

/**
 * Command callback for drush sw-site-info.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 */
function drush_switchboard_sw_site_info($provider_name, $site_name) {
  $site = \Fluxsauce\Brain\SiteQuery::create()
    ->filterByProvider($provider_name)
    ->filterByName($site_name)
    ->findOne();
  switchboard_render_object($site);
}

/**
 * Validation callback for drush sw-site-destroy.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_destroy_validate($provider_name = '', $site_name = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_site_name($site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_NAME_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
}

/**
 * Command callback for drush sw-site-destroy.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 */
function drush_switchboard_sw_site_destroy($provider_name, $site_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->siteDestroy($site_name);
  switchboard_message_success(dt('Locally deleted @site_name from @provider.', array(
    '@site_name' => $site_name,
    '@provider' => $provider_name,
  )));
}

/**
 * Validation callback for drush sw-site-env-backup-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $backup_type
 *   The type of backup.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_env_backup_list_validate($provider_name = '', $site_name = '', $env_name = '', $backup_type = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_site_name($site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_NAME_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site_env($provider_name, $site_name, $env_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_SITE_ENV_INVALID', array(
      '@env_name' => $env_name,
      '@site_name' => $site_name,
    ));
  }
  if (!in_array($backup_type, array('db'))) {
    return switchboard_message_fail('SWITCHBOARD_SITE_ENV_BACKUP_TYPE_INVALID', dt('Invalid backup type specified.'));
  }
}

/**
 * Command callback for drush sw-site-env-backup-list.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $backup_type
 *   The type of backup.
 */
function drush_switchboard_sw_site_env_backup_list($provider_name, $site_name, $env_name, $backup_type) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $backups = $provider->apiGetSiteEnvBackups($site_name, $env_name, $backup_type);
  if (!empty($backups)) {
    $rows = array();
    foreach ($backups as $backup_data) {
      $rows[] = array(
        'filename' => $backup_data['filename'],
        'timestamp' => $backup_data['timestamp'],
      );
    }
    ksort($rows);
    if (drush_get_option('json')) {
      drush_print(json_encode($rows));
    }
    else {
      array_unshift($rows, array(dt('Filename'), dt('Timestamp')));
      drush_print_table($rows, TRUE);
    }
  }
}

/**
 * Validation callback for drush sw-site-env-backup-dl.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $backup_type
 *   The type of backup.
 * @param string $destination
 *   The directory path of the destination file.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_env_backup_dl_validate($provider_name = '', $site_name = '', $env_name = '', $backup_type = '', $destination = '') {
  if (!switchboard_validate_provider_name($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_INVALID', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_auth_logged_in($provider_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_NOT_LOGGED_IN', array(
      '@provider_name' => $provider_name,
    ));
  }
  if (!switchboard_validate_site_name($site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_NAME_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site($provider_name, $site_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROVIDER_SITE_INVALID', array(
      '@site_name' => $site_name,
    ));
  }
  if (!switchboard_validate_site_env($provider_name, $site_name, $env_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_SITE_ENV_INVALID', array(
      '@env_name' => $env_name,
      '@site_name' => $site_name,
    ));
  }
  if (!in_array($backup_type, array('db'))) {
    return switchboard_message_fail('SWITCHBOARD_SITE_ENV_BACKUP_TYPE_INVALID', dt('Invalid backup type specified.'));
  }
  if (!switchboard_validate_destination($destination)) {
    return switchboard_message_fail_generate('SWITCHBOARD_DESTINATION_UNAVAILABLE', array(
      '@destination_path' => $destination,
    ));
  }
}

/**
 * Command callback for drush sw-site-env-backup-dl.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $backup_type
 *   The type of backup.
 * @param string $destination
 *   The directory path of the destination file.
 */
function drush_switchboard_sw_site_env_backup_dl($provider_name, $site_name, $env_name, $backup_type, $destination) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $backup = $provider->getSiteEnvBackupLatest($site_name, $env_name, $backup_type);

  drush_log(dt('Downloading @filename from @date...', array(
    '@filename' => $backup['filename'],
    '@date' => date('r', $backup['timestamp']),
  )));

  $result = $provider->apiDownloadBackup($backup, $destination);
  if ($result) {
    switchboard_message_success($result);
  }
  else {
    switchboard_message_fail('SWITCHBOARD_SITE_ENV_BACKUP_DOWNLOAD_FAIL', dt('Unable to download latest @site_name:@env_name backup from @provider_name.', array(
      '@provider_name' => $provider_name,
      '@site_name' => $site_name,
      '@env_name' => $env_name,
    )));
  }
}
