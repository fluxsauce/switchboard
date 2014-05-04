<?php

function switchboard_pantheon_sites_get() {
  $user_uuid = drush_cache_get('user_uuid', 'switchboard-auth-pantheon');
  $result = switchboard_request('pantheon', array(
    'method' => 'GET',
    'realm' => 'sites',
    'resource' => 'user',
    'uuid' => $user_uuid->data,
  ));
  $site_metadata = json_decode($result->body);

  $sites = array();

  foreach ($site_metadata as $uuid => $data) {
    $sites[] = array(
      'uuid' => $uuid,
      'name' => $data->information->name,
      'realm' => $data->information->preferred_zone,
    );
  }
  return $sites;
}

function switchboard_pantheon_site_info($site_name) {
  $site = switchboard_site_read('pantheon', $site_name);

  // Hard coding.
  $repository = 'codeserver.dev.' . $site['uuid'];
  $repository .= '@codeserver.dev.' . $site['uuid'];
  $repository .= '.drush.in:2222/~/repository.git';
  return array(
    'vcs_url' => $repository,
    'vcs_type' => 'git',
    'vcs_protocol' => 'ssh',
  );
}
