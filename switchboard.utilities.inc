<?php
/**
 * @file
 * General purpose utilities.
 */

/**
 * Send a request.
 *
 * @param \Fluxsauce\Switchboard\Provider $provider
 *   The Provider in question.
 * @param array $options
 *   An array of options for Requests::Request().
 *
 * @return bool|Requests_Response
 *   FALSE upon failure, Requests_Response upon success.
 */
function switchboard_request($provider, $options) {
  $url = $provider->endpoint;

  if ($provider->name == 'pantheon') {
    $url .= '/terminus.php?';
  }

  $url .= $options['resource'];

  if ($provider->name == 'pantheon') {
    $url .= '=' . $options['uuid'];
    $url .= '&path=' . urlencode($options['realm']);
  }
  if ($provider->name == 'acquia') {
    $url .= '.json';
  }

  $requests_options = $provider->requestsOptions();

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

  // Perform request.
  drush_log(dt('Making API @method request to @url', array(
    '@method' => $options['method'],
    '@url' => $url,
  )));

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
  }
  catch (Requests_Exception $e) {
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
 *
 * @param PDOException $e
 *   The thrown PDOException.
 */
function switchboard_pdo_exception_debug(PDOException $e) {
  $trace = $e->getTrace();
  drush_set_error(dt('PDO Exception in @file on line @line: "@message"', array(
    '@file' => $e->getFile(),
    '@line' => $e->getLine(),
    '@message' => $e->getMessage(),
  )));
  drush_print(var_export($trace[0], TRUE));
  exit;
}

/**
 * Render a success message; respects JSON option.
 *
 * @param string $message
 *   The success message.
 */
function switchboard_message_success($message) {
  if (drush_get_option('json')) {
    drush_set_context('DRUSH_USER_ABORT', TRUE);
    drush_print(json_encode(array('success' => $message)));
    exit;
  }
  drush_log($message, 'success');
}

/**
 * Helper function to generate error messages.
 *
 * @param string $code
 *   Drush machine code for error.
 * @param array $replacements
 *   An associative array of replacement items.
 *
 * @return bool
 *   Always returns FALSE.
 * @throws Exception
 *   Invalid code.
 */
function switchboard_message_fail_generate($code, $replacements = array()) {
  switch ($code) {
    case 'SWITCHBOARD_PROVIDER_SITE_NAME_INVALID':
      if (!$replacements['@site_name']) {
        $message = dt('No site name was specified.');
      }
      else {
        $message = dt('"@site_name" is an invalid site name.', $replacements);
      }
      break;

    case 'SWITCHBOARD_PROVIDER_SITE_INVALID':
      $message = dt('Site "@site_name" does not exist locally.', $replacements);
      break;

    case 'SWITCHBOARD_PROVIDER_INVALID':
      if (!$replacements['@provider_name']) {
        $message = dt('No provider name was specified.');
      }
      else {
        $message = dt('Invalid provider name "@provider_name".', $replacements);
      }
      break;

    case 'SWITCHBOARD_SITE_ENV_INVALID':
      if (!$replacements['@env_name']) {
        $message = dt('No valid environment name for @site_name was provided.', $replacements);
      }
      else {
        $message = dt('"@env_name" is not a valid environment for @site_name.', $replacements);
      }
      break;

    case 'SWITCHBOARD_NOT_LOGGED_IN':
      $message = dt('Not logged-in to @provider_name, cannot continue.', $replacements);
      break;

    case 'SWITCHBOARD_DESTINATION_UNAVAILABLE':
      if (!$replacements['@destination_path']) {
        $message = dt('No destination path specified.');
      }
      else {
        $message = dt('"@destination_path" does not exist or cannot be written to.', $replacements);
      }
      break;

    case 'SWITCHBOARD_INVALID_UUID':
      $message = dt('"@uuid" is an invalid UUID.', $replacements);
      break;

    case 'SWITCHBOARD_PROJECT_NO_NAME':
      $message = dt('Project name missing, aborting.');
      break;

    default:
      throw new \Exception(dt('Invalid code @code, cannot generate failure message.', array(
        '@code' => $code,
      )));
      break;
  }
  return switchboard_message_fail($code, $message);
}

/**
 * Render a failure message; respects JSON option.
 *
 * @param string $code
 *   Drush machine code for error.
 * @param string $message
 *   The message to display.
 *
 * @return bool
 *   Always FALSE.
 */
function switchboard_message_fail($code, $message) {
  if (drush_get_option('json')) {
    drush_set_context('DRUSH_USER_ABORT', TRUE);
    drush_print(json_encode(array('error' => $message)));
    exit;
  }
  return drush_set_error($code, $message);
}

/**
 * Generate a UUID.
 *
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 *
 * @return string
 *   The generated UUID.
 */
function switchboard_generate_uuid() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    // 32 bits for "time_low".
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    // 16 bits for "time_mid".
    mt_rand(0, 0xffff),
    // 16 bits for "time_hi_and_version", four most significant bits holds
    // version number 4.
    mt_rand(0, 0x0fff) | 0x4000,
    // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most
    // significant bits holds zero and one for variant DCE1.1.
    mt_rand(0, 0x3fff) | 0x8000,
    // 48 bits for "node".
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
  );
}

/**
 * Get all the options for a particular command.
 *
 * @param string $command_name
 *   The name of the command.
 *
 * @return array
 *   All options, except JSON.
 */
function switchboard_get_drush_options($command_name) {
  $command = switchboard_drush_command();
  if (isset($command[$command_name]['options'])) {
    $options = $command[$command_name]['options'];
    unset($options['json']);
    return $options;
  }
  return array();
}

/**
 * Convert an underscore_delimited string to CamelCase.
 *
 * @param string $string
 *   The string to be converted.
 *
 * @return string
 *   The converted string.
 */
function switchboard_to_camel_case($string) {
  $ret_val = preg_replace_callback('/_(.?)/', function ($matches) {
    return str_replace('_', '', strtoupper($matches[0]));
  }, $string);
  return $ret_val;
}
