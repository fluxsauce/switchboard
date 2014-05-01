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

  $items['sw-auth-logout'] = array(
    'description' => dt('End session with a provider.'),
    'arguments' => array(
      'provider' => dt('Name of the provider. Currently provided: @providers', array(
        '@providers' => implode(', ', switchboard_get_providers()),
      )),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-auth-login'] = array(
    'description' => dt('Create session with a provider'),
    'arguments' => array(
      'provider' => dt('Name of the provider. Currently provided: @providers', array(
        '@providers' => implode(', ', switchboard_get_providers()),
      )),
      'email' => dt('Email address of the account with the provider'),
      'password' => dt('Password for the account'),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  $items['sw-sites-list'] = array(
    'description' => dt('List available sites from a provider.'),
    'arguments' => array(
      'provider' => dt('Name of the provider. Currently provided: @providers', array(
        '@providers' => implode(', ', switchboard_get_providers()),
      )),
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUSH,
  );

  return $items;
}

/**
 * Implements drush_COMMAND_validate().
 */
function drush_switchboard_sw_auth_login_validate($provider = FALSE, $email = FALSE, $password = FALSE) {
  if (!in_array($provider, switchboard_get_providers())) {
    return drush_set_error('SWITCHBOARD_INVALID_PROVIDER', dt('Invalid provider; cannot authenticate.'));
  }
  if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return drush_set_error('SWITCHBOARD_INVALID_EMAIL', dt('Invalid email; cannot authenticate.'));
  }
  if (!$password) {
    return drush_set_error('SWITCHBOARD_INVALID_PASSWORD', dt('Password missing; cannot authenticate.'));
  }
}

function drush_switchboard_sw_auth_login($provider, $email, $password) {
  $provider_settings = switchboard_get_provider_settings($provider);
  if ($provider_settings['auth'] == 'basic') {
    drush_cache_set('email', $email, 'switchboard-auth-' . $provider);
    drush_cache_set('password', $password, 'switchboard-auth-' . $provider);
  }
}

function drush_switchboard_sw_auth_logout($provider) {
  drush_cache_clear_all('email', 'switchboard-auth-' . $provider, TRUE);
  drush_cache_clear_all('password', 'switchboard-auth-' . $provider, TRUE);
}

function switchboard_request_options($provider) {
  $email = drush_cache_get('email', 'switchboard-auth-' . $provider);
  $password = drush_cache_get('password', 'switchboard-auth-' . $provider);
  $options = array(
    'auth' => new Requests_Auth_Basic(array(
        $email->data,
        $password->data,
      )),
  );
  return $options;
}

function switchboard_request($options) {
  $provider = drush_get_option('provider');
  $provider_settings = switchboard_get_provider_settings($provider);
  $url = $provider_settings['endpoint'] . $options['resource'];
  if ($provider == 'acquia') {
    $url .= '.json';
  }

  $request_options = switchboard_request_options($provider);

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
        $response = Requests::get($url, $headers, $request_options);
        break;

      case 'DELETE':
        $response = Requests::delete($url, $headers, $request_options);
        break;

      case 'POST':
        $response = Requests::post($url, $headers, $data, $request_options);
        break;

      case 'PUT':
        $response = Requests::put($url, $headers, $data, $request_options);
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
  drush_set_option('provider', 'acquia');
  require_once 'providers/acquia/acquia.sites.api.inc';
  $result = switchboard_request(switchboard_acquia_sites());
  $json = json_decode($result->body);

  try {
    $pdo = new PDO('sqlite:switchboard.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql_query = 'CREATE TABLE IF NOT EXISTS sites ( ';
    $sql_query .= 'id INTEGER PRIMARY KEY ';
    $sql_query .= ', provider TEXT ';
    $sql_query .= ', site TEXT ';
    $sql_query .= ', updated INTEGER ';
    $sql_query .= ') ';

    $pdo->exec($sql_query);

    // Cleanup.
    $sql_query = 'DELETE FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
    $stmt->execute();

    $sql_query = 'INSERT INTO sites (provider, site, updated) ';
    $sql_query .= 'VALUES (:provider, :site, :updated) ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':updated', time());

    foreach ($json as $site) {
      $stmt->bindParam(':site', $site);
      $stmt->execute();
    }

    $sql_query = 'SELECT site, updated ';
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
