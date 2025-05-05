<?php
/**
 * Created by Gorlum 15.06.2017 4:32
 */

// ------------------------------------------------------------------
use DBAL\db_mysql;
use DBAL\OldDbChangeSet;
use Fleet\DbFleetStatic;
use Planet\DBStaticPlanet;

/**
 * @param array $fleet_row
 * @param bool  $start
 * @param bool  $only_resources
 * @param bool  $safe_fleet
 *
 * @return mixed
 * @deprecated
 */
function RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false) {
  /** @see sn_RestoreFleetToPlanet() Default function */
  return sn_function_call('RestoreFleetToPlanet', array(&$fleet_row, $start, $only_resources, $safe_fleet, &$result));
}

/**
 * @param array $fleet_row
 * @param bool  $start
 * @param bool  $only_resources
 * @param bool  $safe_fleet
 * @param mixed $result
 *
 * @return int
 * @deprecated
 */
function sn_RestoreFleetToPlanet(&$fleet_row, $start = true, $only_resources = false, $safe_fleet = false, &$result) {
  db_mysql::db_transaction_check(true);

  $result = CACHE_NOTHING;
  if (!is_array($fleet_row)) {
    return $result;
  }

  $prefix = $start ? 'start' : 'end';

  // Поскольку эта функция может быть вызвана не из обработчика флотов - нам надо всё заблокировать вроде бы НЕ МОЖЕТ!!!
  // TODO Проеверить от многократного срабатывания !!!
  // Тут не блокируем пока - сначала надо заблокировать пользователя, что бы не было дедлока
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1", true);
  // Узнаем ИД владельца планеты - без блокировки
  // TODO поменять на владельца планеты - когда его будут возвращать всегда !!!
  $user_id = DBStaticPlanet::db_planet_by_vector($fleet_row, "fleet_{$prefix}_");
  $user_id = $user_id['id_owner'];
  // Блокируем пользователя
  $user = db_user_by_id($user_id, true);
  // Блокируем планету
  $planet_arrival = DBStaticPlanet::db_planet_by_vector($fleet_row, "fleet_{$prefix}_");
  // Блокируем флот
//  $fleet_row = doquery("SELECT * FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1 FOR UPDATE;", true);

  // Если флот уже обработан - не существует или возращается - тогда ничего не делаем
  if (!$fleet_row || !is_array($fleet_row) || ($fleet_row['fleet_mess'] == 1 && $only_resources)) {
    return $result;
  }

  // Флот, который возвращается на захваченную планету, пропадает
  if ($start && $fleet_row['fleet_mess'] == 1 && $planet_arrival['id_owner'] != $fleet_row['fleet_owner']) {
    DbFleetStatic::db_fleet_delete($fleet_row['fleet_id']);

    return $result;
  }

  $db_changeset = array();
  if (!$only_resources) {
    DbFleetStatic::db_fleet_delete($fleet_row['fleet_id']);

    if ($fleet_row['fleet_owner'] == $planet_arrival['id_owner']) {
      $fleet_array = sys_unit_str2arr($fleet_row['fleet_array']);
      foreach ($fleet_array as $ship_id => $ship_count) {
        if ($ship_count) {
          $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($ship_id, $ship_count, $user, $planet_arrival['id']);
        }
      }
    } else {
      return CACHE_NOTHING;
    }
  } else {
    $fleet_set = array(
      'fleet_resource_metal'     => 0,
      'fleet_resource_crystal'   => 0,
      'fleet_resource_deuterium' => 0,
      'fleet_mess'               => 1,
    );
    DbFleetStatic::fleet_update_set($fleet_row['fleet_id'], $fleet_set);
  }

  if (!empty($db_changeset)) {
    OldDbChangeSet::db_changeset_apply($db_changeset);
  }

  DBStaticPlanet::db_planet_set_by_id($planet_arrival['id'],
    "`metal` = `metal` + '{$fleet_row['fleet_resource_metal']}', `crystal` = `crystal` + '{$fleet_row['fleet_resource_crystal']}', `deuterium` = `deuterium` + '{$fleet_row['fleet_resource_deuterium']}'");
  $result = CACHE_FLEET | ($start ? CACHE_PLANET_SRC : CACHE_PLANET_DST);

  return $result;
}
