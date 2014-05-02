<?php
/**
 * @file
 * Switchboard, the host agnostic development facilitator.
 */
date_default_timezone_set('UTC');

// Requests - Library used for HTTP requests.
if (!file_exists(__DIR__ . '/vendor/rmccue/requests/library/Requests.php')) {
  return drush_set_error('SWITCHBOARD_MISSING_REQUESTS', dt('Requests is not installed; please see README.md for instructions.'));
}
require 'vendor/autoload.php';

/**
 * Get the list of providers supported by Switchboard.
 *
 * @return array
 *   Machine names of providers.
 */
function switchboard_get_providers() {
  $providers = array();
  $files = scandir('providers');
  foreach ($files as $filename) {
    if (is_dir('providers/' . $filename) && !in_array($filename, array(
        '.',
        '..'
      ))
    ) {
      $providers[] = $filename;
    }
  }
  return $providers;
}

function switchboard_get_provider_settings($provider) {
  return parse_ini_file('providers/' . $provider . '/provider.ini');
}

/**
 * Implements hook_drush_command().
 */
function switchboard_drush_command() {
  $items = array();

  $items['sw-auth-login'] = array(
    'description' => dt('Create session with a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider.'),
      'email' => dt('Email address of the account with the provider'),
      'password' => dt('Password for the account'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider.'),
    'arguments' => array(
      'provider' => dt('Name of the provider.'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-sites-list'] = array(
    'description' => dt('List available sites from a provider.'),
    'arguments' => array(
      'provider' => dt('Name of the provider.'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  return $items;
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_login_validate($provider = FALSE, $email = FALSE, $password = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_EMAIL', dt('Invalid email; cannot authenticate.'));
  }
  if (!$password) {
    return drush_set_error('SWITCHBOARD_AUTH_LOGIN_INVALID_PASSWORD', dt('Password missing; cannot authenticate.'));
  }
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  return call_user_func('switchboard_' . $provider . '_auth_login_validate', $email, $password);
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_login($provider, $email, $password) {
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  call_user_func('switchboard_' . $provider . '_auth_login', $email, $password);
  drush_log(dt('Logged into @provider as @email.', array(
    '@provider' => $provider,
    '@email' => $email,
  )), 'ok');
}

/**
 * Implements hook_drush_command_validate().
 */
function drush_switchboard_sw_auth_logout_validate($provider = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  return call_user_func('switchboard_' . $provider . '_auth_logout_validate');
}

/**
 * Implements hook_drush_command().
 */
function drush_switchboard_sw_auth_logout($provider) {
  require_once('providers/' . $provider . '/' . $provider . '.auth.php');
  call_user_func('switchboard_' . $provider . '_auth_logout');
  drush_log(dt('Logged out of @provider.', array(
    '@provider' => $provider,
  )), 'ok');
}

function switchboard_requests_options($provider, $options = array()) {
  $common_options = array(
    'timeout' => 30,
  );

  // Get provider specific options.
  require_once('providers/' . $provider . '/' . $provider . '.util.php');
  $defaults = array_merge($common_options, call_user_func('switchboard_' . $provider . '_requests_options'));

  // Combine defaults with custom options.
  return array_merge($defaults, $options);
}

/**
 * Send a request.
 * @param $options
 * @return bool|Requests_Response
 */
function switchboard_request($provider, $options) {
  $provider_settings = switchboard_get_provider_settings($provider);
  $url = $provider_settings['endpoint'];

  if ($provider == 'pantheon') {
    $url .= '/terminus.php?';
  }

  $url .= $options['resource'];

  if ($provider == 'pantheon') {
    $url .= '=' . $options['uuid'];
    $url .= '&path=' . $options['realm'];
  }
  if ($provider == 'acquia') {
    $url .= '.json';
  }

  $requests_options = switchboard_requests_options($provider);

  if (!in_array($options['method'], array('GET', 'DELETE', 'POST', 'PUT'))) {
    return drush_set_error('SWITCHBOARD_REQUEST_INVALID_METHOD');
  }
  $headers = array();

  // Prepare data for transport.
  $data = NULL;
  if ($options['data']) {
    $data = json_encode(array('data' => $options['data']));
    if (in_array($options['method'], array('POST', 'PUT', 'DELETE'))) {
      $headers = array(
        'Content-Type' => 'application/json',
        'Content-Length' => strlen($data),
      );
    }
    else {
      if (in_array($options['method'], array('GET'))) {
        $url .= '?' . http_build_query($data);
      }
    }
  }

  try {
    switch ($options['method']) {
      case 'GET':
        $response = Requests::get($url, $headers, $requests_options);
        break;

      case 'DELETE':
        $response = Requests::delete($url, $headers, $requests_options);
        break;

      case 'POST':
        $response = Requests::post($url, $headers, $data, $requests_options);
        break;

      case 'PUT':
        $response = Requests::put($url, $headers, $data, $requests_options);
        break;

    }
  } catch (Requests_Exception $e) {
    return drush_set_error('SWITCHBOARD_REQUEST_FAIL', dt('API failure: @error', array(
      '@error' => $e->getMessage(),
    )));
  }
  return $response;
}

function drush_switchboard_sw_sites_list($provider) {
  require_once('providers/' . $provider . '/' . $provider . '.sites.php');
  $sites = call_user_func('switchboard_' . $provider . '_sites_get');

  $pdo = switchboard_get_sqlite_db();

  try {
    // Cleanup.
    $sql_query = 'DELETE FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
    $stmt->execute();

    $sql_query = 'INSERT INTO sites (provider, name, uuid, updated) ';
    $sql_query .= 'VALUES (:provider, :name, :uuid, :updated) ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':updated', time());

    foreach ($sites as $site_data) {
      $stmt->bindParam(':name', $site_data['name']);
      $stmt->bindParam(':uuid', $site_data['uuid']);
      $stmt->execute();
    }

    $sql_query = 'SELECT name, uuid, updated ';
    $sql_query .= 'FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
      var_dump($row);
    }
  } catch (PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
  }
}

function switchboard_get_sqlite_db() {
  try {
    $pdo = new PDO('sqlite:switchboard.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_query = 'CREATE TABLE IF NOT EXISTS sites ( ';
    $sql_query .= 'id INTEGER PRIMARY KEY ';
    $sql_query .= ', provider TEXT ';
    $sql_query .= ', name TEXT ';
    $sql_query .= ', uuid TEXT ';
    $sql_query .= ', updated INTEGER ';
    $sql_query .= ') ';

    $pdo->exec($sql_query);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $pdo;
}
