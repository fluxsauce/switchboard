<?php

function switchboard_acquia_sites_get() {
  $result = switchboard_request('acquia', array(
    'method' => 'GET',
    'resource' => '/sites',
  ));
  $site_names = json_decode($result->body);
  $sites = array();
  foreach ($site_names as $site_data) {
    $sites[] = array(
      'name' => $site_data,
      // Acquia doesn't include the UUID.
      'uuid' => '',
    );
  }
  return $sites;
}
