<?php

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
