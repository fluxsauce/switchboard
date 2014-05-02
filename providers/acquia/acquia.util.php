<?php

function switchboard_acquia_requests_options() {
  $email = drush_cache_get('email', 'switchboard-auth-acquia');
  $password = drush_cache_get('password', 'switchboard-auth-acquia');
  $options = array(
    'auth' => new Requests_Auth_Basic(array(
        $email->data,
        $password->data,
      )),
  );
  return $options;
}
