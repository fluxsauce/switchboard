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
      'example-value' => 'caffeine.kala',
    ),
    'code_path' => array(
      'description' => dt('Absolute path to codebase'),
      'value' => 'required',
      'example-value' => '/srv/site/code',
    ),
    'database_host' => array(
      'description' => dt('Database Hostname'),
      'value' => 'required',
      'example-value' => 'localhost',
    ),
    'database_username' => array(
      'description' => dt('Database Username'),
      'value' => 'required',
      'example-value' => 'dbuser',
    ),
    'database_password' => array(
      'description' => dt('Database Password'),
      'value' => 'required',
      'example-value' => 'dbpass',
    ),
    'database_name' => array(
      'description' => dt('Database Name'),
      'value' => 'required',
      'example-value' => 'dbname',
    ),
    'database_port' => array(
      'description' => dt('Database Port'),
      'value' => 'required',
      'example-value' => '3306',
    ),
    'files_path' => array(
      'description' => dt('Absolute path to files'),
      'value' => 'required',
      'example-value' => '/srv/site/files',
    ),
  );

  $items['sw-project-create'] = array(
    'description' => dt('Create a local project record. Returns the UUID of the project.'),
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

  $items['sw-project-files-rsync'] = array(
    'description' => dt('rsync project files with remote.'),
    'arguments' => array(
      'project_name' => dt('Name of the project.'),
      'env_name' => dt('Name of the environment.'),
      'direction' => dt('Direction of sync; down (remote to local) and up (local to remote).'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-files-wipe'] = array(
    'description' => dt('Remove all Project files.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-db-get'] = array(
    'description' => dt('Get the database credentials of a Project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => array(
      'format' => dt('Specify the format for the credentials, such as "conf" or "command".'),
    ) + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-db-import'] = array(
    'description' => dt('Import the contents of a database dump into a Project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
      'path' => dt('Path to database backup to import.'),
    ),
    'options' => array(
      'pv' => dt('Display progress using Pipe Viewer.'),
    ) + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-db-wipe'] = array(
    'description' => dt('Wipe the database of a Project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-get-host'] = array(
    'description' => dt('Get the hostname for a particular site.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => array(
      'format' => dt('Format of hostname, such as "url"'),
    ) + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-get-path'] = array(
    'description' => dt('Get the path to the files for a particular site.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
      'component' => dt('Name of the component, such as "code" or "files".'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-list'] = array(
    'description' => dt('List available local project records.'),
    'options' => $site_options + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-info'] = array(
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

  $items['sw-project-vcs-clone'] = array(
    'description' => dt('Clone a remote Site into a project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-vcs-commit'] = array(
    'description' => dt('Commit changes to code in a local project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
      'message' => dt('Commit message.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-vcs-pull'] = array(
    'description' => dt('Pull code from the remote site.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-vcs-push'] = array(
    'description' => dt('Push code to the remote site.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-project-vcs-reset'] = array(
    'description' => dt('Reset all changes in a local project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => $json_option,
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

  $items['sw-site-info'] = array(
    'description' => dt('Get site information.'),
    'arguments' => $default_site_arguments,
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  return $items;
}

/**
 * Validation callback for drush sw-project-create.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_create_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  if (!switchboard_validate_drush_option_required_values('sw-project-create')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_OPTION_VALUE', dt('You must provide a value for every option specified.'));
  }
  $uuid = drush_get_option('uuid');
  if ($uuid) {
    if (!switchboard_validate_uuid($uuid)) {
      return switchboard_message_fail_generate('SWITCHBOARD_INVALID_UUID', array(
        '@uuid' => $uuid,
      ));
    }
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if ($project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_ALREADY_EXISTS', dt('A local project named @project_name already exists.', array(
      '@project_name' => $project_name,
    )));
  }
}

/**
 * Command callback for drush sw-project-create.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_create($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  foreach (array_keys(switchboard_get_drush_options('sw-project-create')) as $option) {
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
  return switchboard_message_success($project->uuid);
}

/**
 * Validation callback for drush sw-project-destroy.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_destroy_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
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
 * Command callback for drush sw-project-destroy.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_destroy($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  $project->destroy();
  switchboard_message_success(dt('Local project @project_name deleted.', array(
    '@project_name' => $project_name,
  )));
}

/**
 * Validation callback for drush sw-project-db-get.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_db_get_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
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
  foreach ($project->getDatabaseSpecs() as $db_field => $db_value) {
    if (!$db_value) {
      return switchboard_message_fail('SWITCHBOARD_PROJECT_DB_FIELD_MISSING', dt('@project_name does not have a value for @field_name.', array(
        '@project_name' => $project_name,
        '@field_name' => $db_field,
      )));
    }
  }
  if (drush_get_option('format') && !in_array(drush_get_option('format'), array('conf', 'command'))) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_DB_BAD_FORMAT', dt('Format not recognized; "conf" or "command" are accepted.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-db-get.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_db_get($project_name) {
  $project = drush_get_option('project');
  if (drush_get_option('format')) {
    if (drush_get_option('format') == 'command') {
      $command = $project->getDatabaseConnection();
    }
    if (drush_get_option('format') == 'conf') {
      $command = $project->getDatabaseSettings();
    }
    if (drush_get_option('json')) {
      drush_print(json_encode($command));
    }
    else {
      drush_print($command);
    }
  }
  else {
    if (drush_get_option('json')) {
      drush_print(json_encode($project->getDatabaseSpecs()));
    }
    else {
      $rows = array(
        array_keys($project->getDatabaseSpecs()),
        array_values($project->getDatabaseSpecs()),
      );
      drush_print_table($rows, TRUE);
    }
  }
}

/**
 * Validation callback for drush sw-project-db-import.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $path
 *   Path to backup to import.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_db_import_validate($project_name = '', $path = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  foreach ($project->getDatabaseSpecs() as $db_field => $db_value) {
    if (!$db_value) {
      return switchboard_message_fail('SWITCHBOARD_PROJECT_DB_FIELD_MISSING', dt('@project_name does not have a value for @field_name.', array(
        '@project_name' => $project_name,
        '@field_name' => $db_field,
      )));
    }
  }
  drush_set_option('project', $project);

  if (!file_exists($path)) {
    return switchboard_message_fail('SWITCHBOARD_FILE_MISSING', dt('Unable to locate file at "@path"', array(
      '@path' => $path,
    )));
  }

  if (drush_get_option('pv')) {
    $result = shell_exec('which pv');
    if (empty($result)) {
      return switchboard_message_fail('SWITCHBOARD_PV_MISSING', dt('Pipe Viewer option specified, but Pipe Viewer cannot be found.'));
    }
  }
}

/**
 * Command callback for drush sw-project-db-import.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_db_import($project_name, $path) {
  $project = drush_get_option('project');
  $mysql_command = $project->getDatabaseConnection();
  if (drush_get_option('pv')) {
    $command = 'pv ' . $path . ' | gunzip | ' . $mysql_command;
  }
  else {
    $command = 'gunzip ' . $path . ' -c | ' . $mysql_command;
  }
  if (drush_shell_exec($command)) {
    return switchboard_message_success(dt('Database imported.'));
  }
  else {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_DB_IMPORT_FAIL', dt('Database import failed.'));
  }
}

/**
 * Validation callback for drush sw-project-files-rsync.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $direction
 *   Direction of the rsync.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_files_rsync_validate($project_name = '', $env_name = '', $direction = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!switchboard_validate_destination($project->filesPath)) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_PATH_UNWRITEABLE', dt('Files path @files_path is not writeable.', array(
      '@files_path' => $project->filesPath,
    )));
  }
  drush_set_option('project', $project);
  if (!in_array($direction, array('up', 'down'))) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_RSYNC_DIR', dt('Invalid rsync direction (up or down).'));
  }

  $site = new \Fluxsauce\Switchboard\Site();
  $site->id = $project->siteId;
  $site->read();
  if (!$site->id) {
    return switchboard_message_fail('SWITCHBOARD_SITE_NOT_FOUND', dt('Cannot find site_id #@site_id.', array(
      '@site_id' => $project->siteId,
    )));
  }
  drush_set_option('site', $site);

  if (!switchboard_validate_site_env($site->provider, $site->name, $env_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_SITE_ENV_INVALID', array(
      '@env_name' => $env_name,
      '@site_name' => $site->name,
    ));
  }
}

/**
 * Command callback for drush sw-project-files-rsync.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $direction
 *   Direction of the rsync.
 */
function drush_switchboard_sw_project_files_rsync($project_name, $env_name, $direction) {
  $project = drush_get_option('project');
  $site = drush_get_option('site');
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($site->provider);
  $site = drush_get_option('site');
  $command = "rsync -rlvz --size-only --ipv4 --progress -e 'ssh -p {$site->sshPort}' ";
  $environment = $site->environments[$env_name];
  $remote = "{$environment->username}@{$environment->host}:{$provider->getFilesPath($site->name, $env_name)}";
  $local = $project->filesPath;
  if ($direction == 'up') {
    $command .= "$local/ $remote";
  }
  else {
    $command .= "$remote/ $local";
  }
  if (drush_shell_exec($command)) {
    switchboard_message_success(dt('Files synced @direction.', array(
      '@direction' => $direction,
    )));
  }
  else {
    switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_RSYNC_FAIL', dt('Rsync failed.'));
  }
}

/**
 * Validation callback for drush sw-project-files-wipe.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_files_wipe_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!switchboard_validate_destination($project->filesPath)) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_PATH_UNWRITEABLE', dt('Files path @files_path is not writeable.', array(
      '@files_path' => $project->filesPath,
    )));
  }
  drush_set_option('project', $project);

  $confirm = drush_confirm(dt('Are you sure you want to wipe @files_path for @project_name?', array(
    '@files_path' => $project->filesPath,
    '@project_name' => $project_name,
  )));
  if (!$confirm) {
    return drush_user_abort();
  }
}

/**
 * Command callback for drush sw-project-files-wipe.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_files_wipe($project_name) {
  $project = drush_get_option('project');
  if (drush_delete_dir_contents($project->filesPath)) {
    switchboard_message_success(dt('@files_path wiped.', array(
      '@files_path' => $project->filesPath,
    )));
  }
  else {
    switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_WIPE_FAIL', dt('Unable to wipe @files_path.', array(
      '@files_path' => $project->filesPath,
    )));
  }
}

/**
 * Validation callback for drush sw-project-get-host.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_get_host_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
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
  if (!$project->hostname) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_HOSTNAME_MISSING', dt('@project_name is missing a hostname.', array(
      '@project_name' => $project_name,
    )));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-get-host.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_get_host($project_name) {
  $project = drush_get_option('project');
  $ret_val = $project->hostname;
  if (drush_get_option('format') == 'url') {
    $ret_val = "http://$ret_val";
  }
  switchboard_message_success($ret_val);
}

/**
 * Validation callback for drush sw-project-get-path.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $component_name
 *   Name of the Project component in question, such as code or files.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_get_path_validate($project_name = '', $component_name = '') {
  $property_name = $component_name . 'Path';
  if (!in_array($property_name, array(
    'filesPath',
    'codePath',
  ))) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_INVALID_COMPONENT', dt('Invalid component name (expecting "code" or "files").'));
  }
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
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
  if (!$project->$property_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_PATH_MISSING', dt('@project_name is missing a value for @property_name.', array(
      '@project_name' => $project_name,
      '@property_name' => $property_name,
    )));
  }
  drush_set_option('project', $project);
  drush_set_option('property_name', $property_name);
}

/**
 * Command callback for drush sw-project-get-path.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $component_name
 *   Name of the Project component in question, such as files.
 */
function drush_switchboard_sw_project_get_path($project_name, $component_name) {
  $property_name = drush_get_option('property_name');
  $project = drush_get_option('project');
  switchboard_message_success($project->$property_name);
}

/**
 * Command callback for drush sw-project-list.
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
  }
  catch (\PDOException $e) {
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
 * Validation callback for drush sw-project-info.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_info_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
}

/**
 * Command callback for drush sw-project-info.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_info($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  $project->render();
}

/**
 * Validation callback for drush sw-project-update.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_update_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
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
 * Command callback for drush sw-project-update.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_update($project_name) {
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  foreach (array_keys(switchboard_get_drush_options('sw-project-update')) as $option) {
    $option_value = drush_get_option($option);
    if ($option_value && !is_bool($option_value)) {
      $project->$option = $option_value;
    }
  }
  $project->update();
  switchboard_message_success(dt('Local project @project_name updated.', array(
    '@project_name' => $project_name,
  )));
}

/**
 * Validation callback for drush sw-project-vcs-clone.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_vcs_clone_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot update.'));
  }

  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }

  if (!$project->siteId) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_SITE', dt('Project does not have a site associated with it.'));
  }

  $site = new \Fluxsauce\Switchboard\Site();
  $site->id = $project->siteId;
  $site->read();
  if (!$site->id) {
    return switchboard_message_fail('SWITCHBOARD_SITE_NOT_FOUND', dt('Cannot find site_id #@site_id.', array(
      '@site_id' => $project->siteId,
    )));
  }

  if (!$project->codePath) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_PATH_MISSING', dt('Project does not have a code path.'));
  }

  if (!switchboard_validate_destination($project->codePath)) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_PATH_UNWRITEABLE', dt('Code path @code_path is not writeable.', array(
      '@code_path' => $project->codePath,
    )));
  }

  drush_set_option('code_path', $project->codePath);

  $url = $site->getVcsUrl();

  if (!$url) {
    return switchboard_message_fail('SWITCHBOARD_SITE_NO_VCS', dt('Site does not contain VCS info.'));
  }

  drush_set_option('url', $url);
}

/**
 * Command callback for drush sw-project-vcs-clone.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_vcs_clone($project_name) {
  make_download_git($project_name, '', array(
    'url' => drush_get_option('url'),
  ), drush_get_option('code_path'));
}

/**
 * Validation callback for drush sw-project-vcs-commit.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $message
 *   The contents of the commit message.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_vcs_commit_validate($project_name = '', $message = '') {
  if (!$message) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_COMMIT_NO_MESSAGE', dt('Commit message missing, cannot commit.'));
  }
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot commit.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->codePath . '/.git')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_GIT_MISSING', dt('Project code path not initialized with git.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-vcs-commit.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_vcs_commit($project_name, $message) {
  $project = drush_get_option('project');
  if (!drush_shell_exec($project->vcsCommitCommand($message))) {
    switchboard_message_fail('SWITCHBOARD_PROJECT_VCS_COMMIT_FAIL', dt('Unable to commit code.'));
  }
  else {
    switchboard_message_success(dt('Code committed.'));
  }
}

/**
 * Validation callback for drush sw-project-vcs-pull.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_vcs_pull_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot pull.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->codePath . '/.git')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_GIT_MISSING', dt('Project code path not initialized with git.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-vcs-pull.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_vcs_pull($project_name) {
  $project = drush_get_option('project');
  if (!drush_shell_exec($project->vcsPullCommand())) {
    switchboard_message_fail('SWITCHBOARD_PROJECT_VCS_PULL_FAIL', dt('Unable to pull code.'));
  }
  else {
    switchboard_message_success(dt('Code pulled.'));
  }
}

/**
 * Validation callback for drush sw-project-vcs-push.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_vcs_push_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot push.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->codePath . '/.git')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_GIT_MISSING', dt('Project code path not initialized with git.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-vcs-push.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_vcs_push($project_name) {
  $project = drush_get_option('project');
  if (!drush_shell_exec($project->vcsPushCommand())) {
    switchboard_message_fail('SWITCHBOARD_PROJECT_VCS_PUSH_FAIL', dt('Unable to push code.'));
  }
  else {
    switchboard_message_success(dt('Code pushed.'));
  }
}

/**
 * Validation callback for drush sw-project-vcs-reset.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_vcs_reset_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot update.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->codePath . '/.git')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_GIT_MISSING', dt('Project code path not initialized with git.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-vcs-reset.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_vcs_reset($project_name) {
  $project = drush_get_option('project');
  if (!drush_shell_exec($project->vcsresetCommand())) {
    switchboard_message_fail('SWITCHBOARD_PROJECT_VCS_RESET_FAIL', dt('Unable to reset code.'));
  }
  else {
    switchboard_message_success(dt('Code reset to latest commit.'));
  }
}

/**
 * Validation callback for drush sw-project-db-wipe.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_db_wipe_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_NAME', dt('Project name missing, cannot update.'));
  }
  $project = new \Fluxsauce\Switchboard\Project($project_name, $project_name);
  if (!$project->id) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  drush_set_option('project', $project);

  foreach ($project->getDatabaseSpecs() as $db_field => $db_value) {
    if (!$db_value) {
      return switchboard_message_fail('SWITCHBOARD_PROJECT_DB_FIELD_MISSING', dt('@project_name does not have a value for @field_name.', array(
        '@project_name' => $project_name,
        '@field_name' => $db_field,
      )));
    }
  }

  $confirm = drush_confirm(dt('Are you sure you want to wipe the database for @project_name?', array(
    '@project_name' => $project_name,
  )));
  if (!$confirm) {
    return drush_user_abort();
  }
}

/**
 * Command callback for drush sw-project-db-wipe.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_db_wipe($project_name) {
  $project = drush_get_option('project');
  $command = 'drush --db-url=' . $project->getDatabaseUrl() . ' sql-drop -y';
  if (drush_shell_exec($command)) {
    switchboard_message_success(dt('Database wiped.'));
  }
  else {
    switchboard_message_fail('SWITCHBOARD_PROJECT_DB_WIPE_FAIL', dt('Database wipe failed.'));
  }
}

/**
 * Command callback for drush sw-project-list.
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
 * Command callback for drush sw-brain-read.
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
 * Validation callback for drush sw-brain-update.
 *
 * @param string $path
 *   The absolute path to the brain location.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_brain_update_validate($path = '') {
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
 * Command callback for drush sw-brain-update.
 *
 * @param string $path
 *   The absolute path to the brain location.
 */
function drush_switchboard_sw_brain_update($path) {
  \Fluxsauce\Switchboard\Sqlite::setLocation($path);
  switchboard_message_success(dt('Switchboard brain location set to @path.', array(
    '@path' => $path,
  )));
}

/**
 * Validation callback for drush sw-brain-destroy.
 *
 * @return bool
 *   FALSE upon failure.
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
 * Command callback for drush sw-brain-destroy.
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
    Fluxsauce\Switchboard\Sqlite::destroy();
    switchboard_message_success(dt('Switchboard brain ablation completed (file removed).'));
  }
}

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

  if (count($provider->sites) == 0) {
    $provider->apiGetSites();
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
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $provider->sites[$site_name]->render();
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
 * Validation callback for drush sw-site-env-db-backup.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $destination
 *   The directory path of the destination file.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_site_env_db_backup_validate($provider_name = '', $site_name = '', $env_name = '', $destination = '') {
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
  if (!switchboard_validate_destination($destination)) {
    return switchboard_message_fail_generate('SWITCHBOARD_DESTINATION_UNAVAILABLE', array(
      '@destination_path' => $destination,
    ));
  }
}

/**
 * Command callback for drush sw-site-env-db-backup.
 *
 * @param string $provider_name
 *   The name of the Provider.
 * @param string $site_name
 *   The name of the remote Site.
 * @param string $env_name
 *   The name of the site environment.
 * @param string $destination
 *   The directory path of the destination file.
 */
function drush_switchboard_sw_site_env_db_backup($provider_name, $site_name, $env_name, $destination) {
  $provider =& \Fluxsauce\Switchboard\Provider::getInstance($provider_name);
  $backup = $provider->getSiteEnvDbBackupLatest($site_name, $env_name);

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
