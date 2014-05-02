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
    );
  }
  return $sites;
}
