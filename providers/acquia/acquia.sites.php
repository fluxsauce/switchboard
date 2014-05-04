<?php

function switchboard_acquia_sites_get() {
  $result = switchboard_request('acquia', array(
    'method' => 'GET',
    'resource' => '/sites',
  ));
  $site_names = json_decode($result->body);
  $sites = array();
  foreach ($site_names as $site_data) {
    list($realm, $site_name) = explode(':', $site_data);
    $sites[] = array(
      'name' => $site_name,
      'realm' => $realm,
      // Acquia doesn't include the UUID.
      'uuid' => '',
    );
  }
  return $sites;
}

function switchboard_acquia_site_info($site_name) {
  $site = switchboard_site_read('acquia', $site_name);
  $result = switchboard_request('acquia', array(
    'method' => 'GET',
    'resource' => '/sites/' . $site['realm'] . ':' . $site_name,
  ));
  $site_info = json_decode($result->body);
  $ret_val = array(
    'unix_username' => $site_info->unix_username,
    'vcs_url' => $site_info->vcs_url,
    'vcs_type' => $site_info->vcs_type,
    'vcs_protocol' => 'git',
    'uuid' => $site_info->uuid,
    'title' => $site_info->title,
  );
  return $ret_val;
}
