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

// Autoloader.
if (!file_exists(SWITCHBOARD_BASE_PATH . '/vendor/autoload.php')) {
  return drush_set_error('SWITCHBOARD_MISSING_COMPOSER', dt('Composer autoloader is missing; please see README.md for installation instructions.'));
}
require_once SWITCHBOARD_BASE_PATH . '/vendor/autoload.php';

// Common functions.
require_once SWITCHBOARD_BASE_PATH . '/switchboard.validators.inc';
require_once SWITCHBOARD_BASE_PATH . '/switchboard.utilities.inc';

// Drush commands.
require_once SWITCHBOARD_BASE_PATH . '/switchboard.drush.sw_auth.inc';
require_once SWITCHBOARD_BASE_PATH . '/switchboard.drush.sw_project.inc';
require_once SWITCHBOARD_BASE_PATH . '/switchboard.drush.sw_site.inc';

// Initialize Propel.
Propel::init(SWITCHBOARD_BASE_PATH . '/propel/conf/brain-conf.php');
$configuration = Propel::getConfiguration();
$configuration['datasources']['brain']['connection']['dsn'] = 'sqlite:' . SWITCHBOARD_BASE_PATH . '/brain/switchboard.sqlite';
Propel::setConfiguration($configuration);

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

  $items['sw-manual'] = array(
    'description' => dt('Generate a GitHub Markdown manual for Switchboard.'),
    'hidden' => TRUE,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-provider-list'] = array(
    'description' => dt('List all available providers.'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
    'options' => $json_option,
  );

  $items['sw-project-alias'] = array(
    'description' => dt('Generate a Drush alias for a project.'),
    'arguments' => array(
      'name' => dt('Name of the project.'),
    ),
    'options' => array(
      'write' => dt('If set, writes alias file to local .drush directory.'),
    ) + $json_option,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
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
      'example-value' => 'hops.kala',
    ),
    'username' => array(
      'description' => dt('Username'),
      'value' => 'required',
      'example-value' => 'user',
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

  $items['sw-site-env-backup-dl'] = array(
    'description' => dt('Download the latest backup.'),
    'arguments' => $default_site_arguments + array(
      'env_name' => dt('Environment name'),
      'backup_type' => dt('Backup type.'),
      'destination' => dt('Destination path'),
    ),
    'options' => $default_options,
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-env-backup-list'] = array(
    'description' => dt('List the available backups for a site environment.'),
    'arguments' => $default_site_arguments + array(
      'env_name' => dt('Environment name'),
      'backup_type' => dt('Backup type.'),
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
 * Command callback for drush sw-manual.
 */
function drush_switchboard_sw_manual() {
  $commands = drush_get_commands();
  asort($commands);
  drush_print(dt('# Switchboard Commands'));
  foreach ($commands as $command_name => $command) {
    if (array_key_exists($command_name, switchboard_drush_command())) {
      drush_print(dt('## @command_name', array('@command_name' => $command_name)));

      _drush_help_merge_subcommand_information($command);

      if (!$help = drush_command_invoke_all('drush_help', 'drush:' . $command['command'])) {
        $help = array($command['description']);
      }

      if ($command['strict-option-handling']) {
        $command['topics'][] = 'docs-strict-options';
      }

      drush_print(implode("\n", $help));
      drush_print();

      // Sort command options.
      uksort($command['options'], '_drush_help_sort_command_options');

      // Print command sections help.
      foreach ($command['sections'] as $key => $value) {
        if (!empty($command[$key])) {
          drush_print(dt($value) . ':');
          $rows = drush_format_help_section($command, $key);
          drush_print('````');
          drush_print_table($rows, FALSE, array('label' => 40));
          unset($rows);
          drush_print('````');
        }
      }

      // Append aliases if any.
      if ($command['aliases']) {
        drush_print(dt("Aliases: ") . implode(', ', $command['aliases']));
      }
    }
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
