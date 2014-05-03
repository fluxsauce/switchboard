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
  $files = scandir(__DIR__ . '/providers');
  foreach ($files as $filename) {
    if (is_dir(__DIR__ . '/providers/' . $filename) && !in_array($filename, array(
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
      'provider' => dt('Name of the provider'),
      'email' => dt('Email address of the account with the provider'),
      'password' => dt('Password for the account'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-sites-list'] = array(
    'description' => dt('List available sites from a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-site-info'] = array(
    'description' => dt('Get site information'),
    'arguments' => array(
      'provider' => dt('Name of the provider'),
      'site_name' => dt('Name of the site'),
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

  drush_print($url);

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

function drush_switchboard_sw_site_info_validate($provider = FALSE, $site_name = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider.'));
  }
  if (!switchboard_site_exists($provider, $site_name)) {
    return drush_set_error('SWITCHBOARD_INVALID_SITE', dt('Invalid site; cannot continue.'));
  }
}

function drush_switchboard_sw_site_info($provider, $site_name) {
  require_once('providers/' . $provider . '/' . $provider . '.sites.php');
  $site_info = call_user_func('switchboard_' . $provider . '_site_info', $site_name);
  $pdo = switchboard_get_sqlite_db();
  try {
    // Determine what values have changed.
    $sql_query = 'UPDATE sites SET ';
    $sql_query_set = array();
    foreach (array_keys($site_info) as $key) {
      $sql_query_set[] = '"' . $key . '" = ? ';
    }
    $sql_query .= implode(', ', $sql_query_set);
    $sql_query .= ', updated = ? ';
    $sql_query .= 'WHERE name = ? ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->execute(array_merge(array_values($site_info), array(
      time(),
      $site_name,
    )));
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }

  var_dump($site_info);
}

function drush_switchboard_sw_sites_list_validate($provider = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider.'));
  }
}

function drush_switchboard_sw_sites_list($provider) {
  require_once('providers/' . $provider . '/' . $provider . '.sites.php');
  $sites = call_user_func('switchboard_' . $provider . '_sites_get');

  $pdo = switchboard_get_sqlite_db();

  try {
    // Cleanup.
    $stmt = $pdo->prepare('DELETE FROM sites WHERE provider = :provider');
    $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
    $stmt->execute();

    $sql_query = 'INSERT INTO sites (provider, name, uuid, realm, updated) ';
    $sql_query .= 'VALUES (:provider, :name, :uuid, :realm, :updated) ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':updated', time());

    foreach ($sites as $site_data) {
      $stmt->bindParam(':name', $site_data['name']);
      $stmt->bindParam(':uuid', $site_data['uuid']);
      $stmt->bindParam(':realm', $site_data['realm']);
      $stmt->execute();
    }

  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }

  $sites = switchboard_get_sites($provider);

  $rows = array();
  foreach ($sites as $site_data) {
    $rows[$site_data->name] = array(
      $site_data->name,
      $site_data->uuid,
    );
  }
  ksort($rows);
  array_unshift($rows, array(dt('Name'), dt('UUID')));
  drush_print_table($rows, TRUE);
}

function switchboard_get_sqlite_db() {
  try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/switchboard.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_query = 'CREATE TABLE IF NOT EXISTS sites ( ';
    $sql_query .= 'id INTEGER PRIMARY KEY ';
    $sql_query .= ', provider TEXT ';
    $sql_query .= ', uuid TEXT ';
    $sql_query .= ', realm TEXT ';
    $sql_query .= ', name TEXT ';
    $sql_query .= ', title TEXT ';
    $sql_query .= ', unix_username TEXT ';
    $sql_query .= ', vcs_url TEXT ';
    $sql_query .= ', vcs_type TEXT ';
    $sql_query .= ', vcs_protocol TEXT ';
    $sql_query .= ', updated INTEGER ';
    $sql_query .= ') ';

    $pdo->exec($sql_query);
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $pdo;
}

function switchboard_get_sites($provider) {
  $pdo = switchboard_get_sqlite_db();
  $ret_val = array();

  try {
    $sql_query = 'SELECT * ';
    $sql_query .= 'FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
      $ret_val[$row->name] = $row;
    }
  } catch (PDOException $e) {
    echo $e->getMessage();
  }
  return $ret_val;
}

function switchboard_get_site($provider, $site_name) {
  $pdo = switchboard_get_sqlite_db();

  try {
    $sql_query = 'SELECT * ';
    $sql_query .= 'FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $sql_query .= 'AND name = :name ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':name', $site_name);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  } catch (PDOException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit;
  }
}

function switchboard_site_exists($provider, $site_name) {
  if (!$site_name) {
    return FALSE;
  }
  $sites = switchboard_get_sites($provider);
  return array_key_exists($site_name, $sites);
}
