<?php

function switchboard_pantheon_site_info($site_name) {
  $site = new \Fluxsauce\Switchboard\Site('pantheon', $site_name);

  // Hard coding.
  $repository = 'codeserver.dev.' . $site->uuid;
  $repository .= '@codeserver.dev.' . $site->uuid;
  $repository .= '.drush.in:2222/~/repository.git';

  return array(
    'vcs_url' => $repository,
    'vcs_type' => 'git',
    'vcs_protocol' => 'ssh',
  );
}
