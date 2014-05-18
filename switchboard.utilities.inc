<?php
/**
 * @file
 * General purpose utilities.
 */

/**
 * Send a request.
 * @param \Fluxsauce\Switchboard\Provider $provider
 * @param $options
 * @return bool|Requests_Response
 */
function switchboard_request($provider, $options) {
  $url = $provider->endpoint;

  if ($provider->name == 'pantheon') {
    $url .= '/terminus.php?';
  }

  $url .= $options['resource'];

  if ($provider->name == 'pantheon') {
    $url .= '=' . $options['uuid'];
    $url .= '&path=' . $options['realm'];
  }
  if ($provider->name == 'acquia') {
    $url .= '.json';
  }

  $requests_options = $provider->requests_options();

  if (!in_array($options['method'], array('GET', 'DELETE', 'POST', 'PUT'))) {
    return drush_set_error('SWITCHBOARD_REQUEST_INVALID_METHOD');
  }
  $headers = array();

  // Prepare data for transport.
  $data = NULL;
  if (isset($options['data'])) {
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

  drush_log($url);

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

/**
 * Get the list of providers supported by Switchboard.
 *
 * @return array
 *   Machine names of providers.
 */
function switchboard_get_provider_names() {
  // Hard coding for now.
  // Later, be clever and scan the directory containing Provider definitions.
  $provider_names = array(
    'acquia',
    'pantheon',
  );
  return $provider_names;
}

/**
 * Helper for debugging PDO exceptions.
 * @param PDOException $e
 */
function switchboard_pdo_exception_debug(PDOException $e) {
  drush_set_error(dt('PDO Exception in @file on line @line: "@message"', array(
    '@file' => $e->getFile(),
    '@line' => $e->getLine(),
    '@message' => $e->getMessage(),
  )), 'error');
}
