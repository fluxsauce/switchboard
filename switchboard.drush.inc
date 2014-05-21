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
  $json_option = array(
    'json' => dt('Render response in JSON'),
  );
  $default_options = array(
    'refresh' => dt('Refresh all remote data.'),
  ) + $json_option;

  $items['sw-brain-update'] = array(
    'description' => dt('Set Switchboard brain (SQLite database) file location.'),
    'arguments' => array(
      'path' => dt('Full path, including filename, of brain.'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'options' => $json_option,
  );

  $items['sw-brain-read'] = array(
    'description' => dt('Get Switchboard brain (SQLite database) location.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'options' => $json_option,
  );

  $items['sw-brain-destroy'] = array(
    'description' => dt('Destroy the Switchboard brain.'),
    'options' => array(
        'tables' => dt('List of tables, comma separated, to remove from the database.'),
      ) + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-provider-list'] = array(
    'description' => dt('List all available providers.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'options' => $json_option,
  );

  $site_options = array(
    'uuid' => array(
      'description' => dt('UUID of the project'),
      'value' => 'required',
    ),
    'site_id' => array(
      'description' => dt('Remote Site ID'),
      'value' => 'required',
      'example-value' => 37,
    ),
    'ssh_port' => array(
      'description' => dt('SSH Port'),
      'value' => 'required',
      'example-value' => 22,
    ),
    'hostname' => array(
      'description' => dt('Hostname'),
      'value' => 'required',
      'example-value' => 'caffeine.kala'
    ),
    'code_path' => array(
      'description' => dt('Absolute path to codebase'),
      'value' => 'required',
      'example-value' => '/srv/site/code',
    ),
    'database_host' => array(
      'description' => dt('Database Hostname'),
      'value' => 'required',
    ),
    'database_username' => array(
      'description' => dt('Database Username'),
      'value' => 'required',
    ),
    'database_password' => array(
      'description' => dt('Database Password'),
      'value' => 'required',
    ),
    'database_name' => array(
      'description' => dt('Database Name'),
      'value' => 'required',
    ),
    'database_port' => array(
      'description' => dt('Database Port'),
      'value' => 'required',
    ),
    'files_path' => array(
      'description' => dt('Absolute path to files'),
      'value' => 'required',
    ),
  );

  $items['sw-project-create'] = array(
    'description' => dt('Create a local project record.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $site_options + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-destroy'] = array(
    'description' => dt('Destroy a local project record.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-list'] = array(
    'description' => dt('List available local project records.'),
    'options' => $site_options + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-read'] = array(
    'description' => dt('Get information on a local project record.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $site_options + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-update'] = array(
    'description' => dt('Update a local project record.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $site_options + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-login'] = array(
    'description' => dt('Authenticate with a provider.'),
    'arguments' => array(
      'email' => dt('Email address of the account with the provider.'),
      'password' => dt('Password or API key for the account.'),
    ) + $default_arguments,
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider.'),
    'arguments' => $default_arguments,
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-list'] = array(
    'description' => dt('List available sites from a provider.'),
    'arguments' => $default_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-destroy'] = array(
    'description' => dt('Locally destroy site record.'),
    'arguments' => $default_site_arguments,
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-env-list'] = array(
    'description' => dt('List available site environments from a provider.'),
    'arguments' => $default_site_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-env-db-backup'] = array(
    'description' => dt('Download the latest database backup.'),
    'arguments' => $default_site_arguments + array(
      'env_name' => dt('Environment name'),
      'db_name' => dt('Database name'),
      'destination' => dt('Destination path'),
    ),
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-read'] = array(
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

  return $items;
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_project_create_validate($project_name = FALSE) {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot create.'));
  }
  if (!switchboard_validate_drush_option_required_values('sw-project-create')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_OPTION_VALUE', dt('You must provide a value for every option specified.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if ($project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_ALREADY_EXISTS', dt('A local project named @project_name already exists.', array(
      '@project_name' => $project_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_project_create($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  foreach (switchboard_get_drush_options('sw-project-create') as $option) {
    $option_value = drush_get_option($option);
    if ($option_value && !is_bool($option_value)) {
      $project->$option = $option_value;
    }
  }
  // Generate a UUID if none was provided.
  if (!$project->uuid) {
    $project->uuid = switchboard_generate_uuid();
  }
  $project->update();
  return switchboard_message_success(dt('Local project @project_name created.', array(
    '@project_name' => $project_name,
  )));
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_project_destroy_validate($project_name = FALSE) {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot destroy.'));
  }
  if (!switchboard_validate_drush_option_required_values('sw-project-create')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_OPTION_VALUE', dt('You must provide a value for every option specified.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  $confirm = drush_confirm(dt('Are you sure that you want to delete local project @project_name?', array(
    '@project_name' => $project_name,
  )));
  if (!$confirm) {
    return drush_user_abort();
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_project_destroy($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  $project->destroy();
  return switchboard_message_success(dt('Local project @project_name deleted.', array(
    '@project_name' => $project_name,
  )));
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_project_list() {
  $pdo = \Fluxsauce\Switchboard\Sqlite::get();
  $rows = array();

  $keys_to_show = array(
    'id',
    'name',
    'hostname',
    'uuid',
  );

  try {
    $sql_query = 'SELECT name ';
    $sql_query .= 'FROM projects ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->execute();
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      $project = new \Fluxsauce\Switchboard\Project($row['name'], $row['name']);
      $project_row = array();
      foreach ($keys_to_show as $key) {
        $project_row[$key] = $project->$key;
      }
      $rows[$row['name']] = $project_row;
    }
  } catch (\PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }

  if (empty($rows)) {
    if (drush_get_option('json')) {
      return json_encode($rows);
    }
    else {
      return drush_print(dt('No local projects exist.'));
    }
  }

  ksort($rows);
  if (drush_get_option('json')) {
    drush_print(json_encode($rows));
  }
  else {
    array_unshift($rows, $keys_to_show);
    drush_print_table($rows, TRUE);
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_project_read_validate($project_name = FALSE) {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot create.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_project_read($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  $project->render();
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_project_update_validate($project_name = FALSE) {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot update.'));
  }
  if (!switchboard_validate_drush_option_required_values('sw-project-create')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_OPTION_VALUE', dt('You must provide a value for every option specified.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_project_update($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  foreach (switchboard_get_drush_options('sw-project-update') as $option) {
    $option_value = drush_get_option($option);
    if ($option_value && !is_bool($option_value)) {
      $project->$option = $option_value;
    }
  }
  $project->update();
  return switchboard_message_success(dt('Local project @project_name updated.', array(
    '@project_name' => $project_name,
  )));
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_provider_list() {
  $provider_names = switchboard_get_provider_names();
  $rows = array();
  foreach ($provider_names as $provider_name) {
    $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
    $rows[$provider->name] = array(
      'name' => $provider->name,
      'label' => $provider->label,
      'homepage' => $provider->homepage,
    );
  }
  ksort($rows);
  if (drush_get_option('json')) {
    drush_print(json_encode($rows));
  }
  else {
    array_unshift($rows, array(dt('Name'), dt('Label'), dt('Homepage')));
    drush_print_table($rows, TRUE);
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_brain_read() {
  $brain_path = \Fluxsauce\Switchboard\Sqlite::getLocation();
  if (drush_get_option('json')) {
    drush_print(json_encode($brain_path));
  }
  else {
    drush_print($brain_path);
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_brain_update_validate($path = FALSE) {
  if (!$path) {
    return switchboard_message_fail('SWITCHBOARD_BRAIN_PATH_MISSING', dt('Path missing, cannot set to nothing.'));
  }
  if (!drush_is_absolute_path($path)) {
    return switchboard_message_fail('SWITCHBOARD_BRAIN_PATH_ABSOLUTE', dt('You must specify the absolute path to the file.'));
  }
  if (is_dir($path)) {
    return switchboard_message_fail('SWITCHBOARD_BRAIN_PATH_DIR', dt('You need to specify the name of the file, not just the directory.'));
  }
  if (!(substr($path, -strlen('.sqlite')) === '.sqlite')) {
    return switchboard_message_fail('SWITCHBOARD_BRAIN_PATH_EXT', dt('You need to specify the file extension .sqlite for the brain.'));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_brain_update($path) {
  \Fluxsauce\Switchboard\Sqlite::setLocation($path);
  switchboard_message_success(dt('Switchboard brain location set to @path.', array(
    '@path' => $path,
  )));
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_brain_destroy_validate() {
  $tables = drush_get_option('tables');
  if ($tables) {
    $confirm = drush_confirm(dt('Are you sure you want to reset @tables?', array(
      '@tables' => $tables,
    )));
  }
  else {
    $confirm = drush_confirm(dt('Are you sure you want to destroy the Switchboard brain?'));
  }
  if (!$confirm) {
    return drush_user_abort();
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_brain_destroy() {
  $tables = drush_get_option('tables');
  if ($tables) {
    foreach (explode(',', $tables) as $table) {
      Fluxsauce\Switchboard\Sqlite::destroyTable($table);
      switchboard_message_success(dt('Switchboard brain ablation of @table completed.', array(
        '@table' => $table,
      )));
    }
  }
  else {
    switchboard_message_success(dt('Switchboard brain ablation completed (file removed).'));
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_login_validate($provider_name = FALSE, $email = FALSE, $password = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if ($provider->auth_is_logged_in()) {
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
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_login($provider_name, $email, $password) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  $result = $provider->auth_login($email, $password);
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
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_logout_validate($provider_name = FALSE) {
  if (!switchboard_validate_provider_name($provider_name)) {
    return FALSE;
  }
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (!$provider->auth_is_logged_in()) {
    return switchboard_message_fail('SWITCHBOARD_AUTH_LOGOUT_ALREADY_LOGGEDOUT', dt('Already logged-out of @provider_name.', array(
      '@provider_name' => $provider_name,
    )));
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_logout($provider_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->auth_logout();
  switchboard_message_success(dt('Logged out of @provider.', array(
    '@provider' => $provider_name,
  )));
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
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);

  if (count($provider->sites) == 0) {
    $provider->api_get_sites();
  }
  if (count($provider->sites) == 0) {
    if (drush_get_option('json')) {
      return drush_print(json_encode(array()));
    }
    return drush_print(dt('No sites.'));
  }

  $rows = array();
  foreach ($provider->sites as $site) {
    $rows[$site->name] = array(
      'name' => $site->name,
      'uuid' => $site->uuid,
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
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  if (drush_get_option('json')) {
    $provider->sites[$site_name]->renderEnvironmentsJson();
  }
  else {
    $provider->sites[$site_name]->renderEnvironmentsDrushTable();
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_read_validate($provider_name = FALSE, $site_name = FALSE) {
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
function drush_switchboard_sw_site_read($provider_name, $site_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->sites[$site_name]->render();
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
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
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
      return switchboard_message_fail('SWITCHBOARD_SITE_VCS_INVALID_PROTOCOL', dt('Invalid VCS type for site.'));
  }
  $command .= ' ';
  if ($site->vcs_protocol == 'ssh') {
    $command .= 'ssh://';
  }
  $command .= $site->vcs_url . ' ' . $site_name;
  if (drush_get_option('json')) {
    drush_print(json_encode($command));
  }
  else {
    drush_print($command);
  }
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_destroy_validate($provider_name = FALSE, $site_name = FALSE) {
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
function drush_switchboard_sw_site_destroy($provider_name, $site_name) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->site_destroy($site_name);
  switchboard_message_success(dt('Locally deleted @site_name from @provider.', array(
    '@site_name' => $site_name,
    '@provider' => $provider_name,
  )));
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_site_env_db_backup_validate($provider_name = FALSE, $site_name = FALSE, $env_name = FALSE, $destination = FALSE) {
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
  if (!switchboard_validate_destination($destination)) {
    return FALSE;
  }
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_site_env_db_backup($provider_name, $site_name, $env_name, $destination) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $backup = $provider->get_site_env_db_backup_latest($site_name, $env_name);

  drush_log(dt('Downloading @filename from @date...', array(
    '@filename' => $backup['filename'],
    '@date' => date('r', $backup['timestamp']),
  )));

  $result = $provider->api_download_backup($backup, $destination);
  if ($result) {
    drush_print($result);
  }
}
