<?php
/**
 * @file
 * Project callbacks.
 */

/**
 * Validation callback for drush sw-project-alias.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_alias_validate($project_name = '') {
  if (!$project_name) {
    return switchboard_message_fail_generate('SWITCHBOARD_PROJECT_NO_NAME');
  }
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  drush_set_option('project', $project);

  if (drush_get_option('write')) {
    $destination = drush_normalize_path(drush_server_home() . '/.drush/' . $project_name . '.aliases.drushrc.php');
    // Ensure destination can be written to.
    $fh = fopen($destination, 'w');
    if ($fh === FALSE) {
      return switchboard_message_fail('SWITCHBOARD_ALIASES_NO_WRITE', dt('Cannot write aliases to @destination - verify this location is writable.', array(
        '@destination' => $destination,
      )));
    }
    drush_set_option('fh', $fh);
  }
}

/**
 * Command callback for drush sw-project-alias.
 *
 * @param string $project_name
 *   Name of the local project in question.
 *
 * @return bool
 *   FALSE upon failure.
 */
function drush_switchboard_sw_project_alias($project_name) {
  $project = drush_get_option('project');
  $ret_val = <<<ALIAS
<?php
{$project->toDrushAlias()}
ALIAS;
  if (drush_get_option('write')) {
    $fh = drush_get_option('fh');
    fwrite($fh, $ret_val);
    fclose($fh);
    return switchboard_message_success(dt('Drush alias created; you should clear your drush cache.'));
  }

  if (drush_get_option('json')) {
    drush_print(json_encode($ret_val));
  }
  else {
    drush_print($ret_val);
  }
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if ($project) {
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
  $project = new \Fluxsauce\Brain\Project();
  $project->setName($project_name);
  foreach (array_keys(switchboard_get_drush_options('sw-project-create')) as $option) {
    $option_value = drush_get_option($option);
    if ($option_value && !is_bool($option_value)) {
      $method = 'set' . switchboard_underscore_to_property_name($option);
      $project->$method($option_value);
    }
  }
  // Generate a UUID if none was provided.
  if (!switchboard_validate_uuid($project->getUuid())) {
    $project->setUuid(switchboard_generate_uuid());
  }
  $project->save();
  return switchboard_message_success($project->getUuid());
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
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
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-destroy.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_destroy($project_name) {
  $project = drush_get_option('project');
  $project->delete();
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
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
 * @param string $path
 *   Path to backup to import.
 *
 * @return bool
 *   FALSE upon failure.
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!switchboard_validate_destination($project->getFilespath())) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_PATH_UNWRITEABLE', dt('Files path @files_path is not writeable.', array(
      '@files_path' => $project->getFilespath(),
    )));
  }
  drush_set_option('project', $project);
  if (!in_array($direction, array('up', 'down'))) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_RSYNC_DIR', dt('Invalid rsync direction (up or down).'));
  }

  $site = \Fluxsauce\Brain\SiteQuery::create()->findPk($project->getSiteid());

  if (!$site) {
    return switchboard_message_fail('SWITCHBOARD_SITE_NOT_FOUND', dt('Cannot find site_id #@site_id.', array(
      '@site_id' => $project->getSiteid(),
    )));
  }
  drush_set_option('site', $site);

  if (!switchboard_validate_site_env($site->getProvider(), $site->getName(), $env_name)) {
    return switchboard_message_fail_generate('SWITCHBOARD_SITE_ENV_INVALID', array(
      '@env_name' => $env_name,
      '@site_name' => $site->getName(),
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
  $environment = \Fluxsauce\Brain\EnvironmentQuery::create()
    ->filterBySite($site)
    ->filterByName($env_name)
    ->findOne();
  $provider = \Fluxsauce\Switchboard\Provider::getInstance($site->getProvider());
  $command = "rsync -rlvz --size-only --ipv4 --progress -e 'ssh -p {$site->getSshport()}' ";
  $remote = "{$environment->getUsername()}@{$environment->getHost()}:{$provider->getFilesPath($site->getName(), $env_name)}";
  $local = $project->getFilespath();
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!switchboard_validate_destination($project->getFilespath())) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_PATH_UNWRITEABLE', dt('Files path @files_path is not writeable.', array(
      '@files_path' => $project->getFilespath(),
    )));
  }
  drush_set_option('project', $project);

  $confirm = drush_confirm(dt('Are you sure you want to wipe @files_path for @project_name?', array(
    '@files_path' => $project->getFilespath(),
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
  if (drush_delete_dir_contents($project->getFilespath())) {
    switchboard_message_success(dt('@files_path wiped.', array(
      '@files_path' => $project->getFilespath(),
    )));
  }
  else {
    switchboard_message_fail('SWITCHBOARD_PROJECT_FILES_WIPE_FAIL', dt('Unable to wipe @files_path.', array(
      '@files_path' => $project->getFilespath(),
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!$project->getHostname()) {
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
  $ret_val = $project->getHostname();
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  $method_name = 'get' . switchboard_underscore_to_property_name($property_name);
  if (!$project->$method_name()) {
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
  $method_name = 'get' . switchboard_underscore_to_property_name($property_name);
  switchboard_message_success($project->$method_name());
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

  $projects = \Fluxsauce\Brain\ProjectQuery::create()->find();

  if (!empty($projects)) {
    foreach ($projects as $project) {
      $project_row = array();
      foreach ($keys_to_show as $key) {
        $method = 'get' . switchboard_underscore_to_property_name($key);
        $project_row[$key] = $project->$method();
      }
      $rows[$project->getName()] = $project_row;
    }
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-info.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_info($project_name) {
  $project = drush_get_option('project');
  switchboard_render_object($project);
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-update.
 *
 * @param string $project_name
 *   Name of the local project in question.
 */
function drush_switchboard_sw_project_update($project_name) {
  $project = drush_get_option('project');
  foreach (array_keys(switchboard_get_drush_options('sw-project-update')) as $option) {
    $option_value = drush_get_option($option);
    if ($option_value && !is_bool($option_value)) {
      $method = 'set' . switchboard_underscore_to_property_name($option);
      $project->$method($option_value);
    }
  }
  $project->save();
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

  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }

  if (!$project->getSiteid()) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_NO_SITE', dt('Project does not have a site associated with it.'));
  }

  $site = \Fluxsauce\Brain\SiteQuery::create()
    ->filterById($project->getSiteid())
    ->findOne();

  if (!$site) {
    return switchboard_message_fail('SWITCHBOARD_SITE_NOT_FOUND', dt('Cannot find site_id #@site_id.', array(
      '@site_id' => $project->getSiteid(),
    )));
  }

  if (!$project->getCodepath()) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_PATH_MISSING', dt('Project does not have a code path.'));
  }

  if (!switchboard_validate_destination($project->getCodepath())) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_PATH_UNWRITEABLE', dt('Code path @code_path is not writeable.', array(
      '@code_path' => $project->getCodepath(),
    )));
  }

  drush_set_option('code_path', $project->getCodepath());

  $url = $site->getVcsConnection();

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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->getCodepath() . '/.git')) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_CODE_GIT_MISSING', dt('Project code path not initialized with git.'));
  }
  drush_set_option('project', $project);
}

/**
 * Command callback for drush sw-project-vcs-commit.
 *
 * @param string $project_name
 *   Name of the local project in question.
 * @param string $message
 *   The contents of the commit message.
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->getCodepath() . '/.git')) {
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->getCodepath() . '/.git')) {
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
    return switchboard_message_fail('SWITCHBOARD_PROJECT_MISSING', dt('Cannot find local project with name @project_name.', array(
      '@project_name' => $project_name,
    )));
  }
  if (!file_exists($project->getCodepath() . '/.git')) {
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
  $project = \Fluxsauce\Brain\ProjectQuery::create()
    ->filterByName($project_name)
    ->findOne();
  if (!$project) {
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
