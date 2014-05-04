<?php

function switchboard_site_create($provider, $site_name) {
  $pdo = switchboard_get_sqlite_db();

  try {
    $sql_query = 'INSERT INTO sites (provider, name, updated) ';
    $sql_query .= 'VALUES (:provider, :name, :updated) ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':name', $site_name);
    $stmt->bindParam(':updated', time());
    $stmt->execute();
  } catch (PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }
}

function switchboard_site_read($provider, $site_name) {
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
    return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }
  return array();
}

function switchboard_site_read_all($provider) {
  $pdo = switchboard_get_sqlite_db();
  $ret_val = array();

  try {
    $sql_query = 'SELECT * ';
    $sql_query .= 'FROM sites ';
    $sql_query .= 'WHERE provider = :provider ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->bindParam(':provider', $provider);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $ret_val[$row['name']] = $row;
    }
  } catch (PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }
  return $ret_val;
}

function switchboard_site_update($provider, $site_name, $fields) {
  $pdo = switchboard_get_sqlite_db();
  // Ensure that the site exists.
  $existing_site = switchboard_site_read($provider, $site_name);
  if (empty($existing_site)) {
    switchboard_site_create($provider, $site_name);
  }

  try {
    $sql_query = 'UPDATE sites SET ';
    $sql_query_set = array();
    foreach (array_keys($fields) as $key) {
      // Don't update the name.
      if ($key == 'name') {
        unset($fields[$key]);
      }
      else {
        $sql_query_set[] = $key . ' = ? ';
      }
    }
    // Nothing to update.
    if (empty($sql_query_set)) {
      return;
    }
    $sql_query .= implode(', ', $sql_query_set);
    $sql_query .= ', updated = ? ';
    $sql_query .= 'WHERE name = ? ';
    $sql_query .= 'AND provider = ? ';
    $stmt = $pdo->prepare($sql_query);
    $stmt->execute(array_merge(array_values($fields), array(
      time(),
      $site_name,
      $provider,
    )));
  } catch (PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }
}

function switchboard_site_delete_all($provider) {
  $pdo = switchboard_get_sqlite_db();
  try {
    $stmt = $pdo->prepare('DELETE FROM sites WHERE provider = :provider');
    $stmt->bindParam(':provider', $provider, PDO::PARAM_STR);
    $stmt->execute();
  } catch (PDOException $e) {
    switchboard_pdo_exception_debug($e);
  }
}

function switchboard_site_exists($provider, $site_name) {
  if (!$site_name) {
    return FALSE;
  }
  $sites = switchboard_site_read_all($provider);
  return array_key_exists($site_name, $sites);
}
