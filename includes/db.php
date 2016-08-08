<?php

/**
 * @version 2015-04-11 11:47:49 39b14.2
 * @copyright 2008-2015 Gorlum for Project "SuperNova.WS"
 */

defined('INSIDE') || die();

require_once('db/db_queries.php');

// TODO: THIS FUNCTION IS OBSOLETE AND SHOULD BE REPLACED!
// TODO - ТОЛЬКО ДЛЯ РЕСУРСОВ
// $unit_list should have unique entrances! Recompress non-uniq entrances before pass param!
/**
 * @param       $user
 * @param       $planet
 * @param array $unit_list
 * @param null  $query
 */
function db_change_resources(&$user, &$planet, $unit_list) {
  $group = sn_get_groups('resources_loot');
  reset($unit_list);
  $locationType = sys_get_unit_location($user, $planet, key($unit_list));

  $resourcesChange = array();
  foreach($unit_list as $unit_id => $unit_amount) {
    if(!in_array($unit_id, $group)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: db_change_resources() вызван для не-ресурсов!</h1>');
      pdump(debug_backtrace());
      die('db_change_resources() вызван для не-ресурсов!');
    }

    if(empty($unit_amount)) {
      continue;
    }

    $resourcesChange[pname_resource_name($unit_id)] += $unit_amount;
  }

  if($locationType == LOC_USER) {
    $locationId = $user['id'];
  } else {
    $locationId = $planet['id'];
  }

  if (!empty($locationId) && !empty($resourcesChange)) {
    classSupernova::$gc->cacheOperator->db_upd_record_by_id(
      $locationType,
      $locationId,
      array(),
      $resourcesChange
    );
  }

}

function sn_db_transaction_check($transaction_should_be_started = null) {
  return classSupernova::$gc->db->getTransaction()->check($transaction_should_be_started);
}
function sn_db_transaction_start($level = '') {
  return classSupernova::$gc->db->getTransaction()->start($level);
}
function sn_db_transaction_commit() {
  return classSupernova::$gc->db->getTransaction()->commit();
}
function sn_db_transaction_rollback() {
  return classSupernova::$gc->db->getTransaction()->rollback();
}



function db_fetch(&$query) {
  return classSupernova::$gc->db->db_fetch($query);
}
function db_escape($unescaped_string) {
  return classSupernova::$gc->db->db_escape($unescaped_string);
}
