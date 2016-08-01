<?php

use Vector\Vector;

class DBStaticPlanet {

  /**
   * @param array $rowUser
   * @param array $rowPlanet
   *
   * @return array
   */
  public static function getResources($rowUser, $rowPlanet) {
    $planetResources = array();

    $sn_group_resources = sn_get_groups('resources_loot');
    foreach ($sn_group_resources as $resource_id) {
      $planetResources[$resource_id] = floor(mrc_get_level($rowUser, $rowPlanet, $resource_id));
    }

    return $planetResources;
  }


  public static function db_planets_purge() {
    classSupernova::$db->doDelete("DELETE FROM `{{planets}}` WHERE `id_owner` NOT IN (SELECT `id` FROM `{{users}}`);");
  }


  /**
   * @param int    $planet_id
   * @param bool   $for_update
   * @param string $fields
   *
   * @return array|null
   */
  public static function db_planet_by_id($planet_id, $for_update = false, $fields = '*') {
    $result = classSupernova::$gc->cacheOperator->db_get_record_by_id(LOC_PLANET, $planet_id, $for_update, $fields);

    return empty($result) ? null : $result;
  }

  public static function db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update = false, $fields = '*') {
    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`{{planets}}`.`galaxy` = {$galaxy} AND `{{planets}}`.`system` = {$system} AND `{{planets}}`.`planet` = {$planet} AND `{{planets}}`.`planet_type` = {$planet_type}", true);
  }

  public static function db_planet_by_gspt($galaxy, $system, $planet, $planet_type, $for_update = false, $fields = '*') {
    $galaxy = intval($galaxy);
    $system = intval($system);
    $planet = intval($planet);
    $planet_type = intval($planet_type);

    return DBStaticPlanet::db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update, $fields);
  }

  public static function db_planet_by_vector($vector, $prefix = '', $for_update = false, $fields = '*') {
    $galaxy = isset($vector[$prefix . 'galaxy']) ? intval($vector[$prefix . 'galaxy']) : 0;
    $system = isset($vector[$prefix . 'system']) ? intval($vector[$prefix . 'system']) : 0;
    $planet = isset($vector[$prefix . 'planet']) ? intval($vector[$prefix . 'planet']) : 0;
    $planet_type = isset($vector[$prefix . 'planet_type']) ? intval($vector[$prefix . 'planet_type']) :
      (isset($vector[$prefix . 'type']) ? intval($vector[$prefix . 'type']) : 0);
    $planet_type = $planet_type == PT_DEBRIS ? PT_PLANET : $planet_type;

    return DBStaticPlanet::db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update, $fields);
  }

  /**
   * @param Vector $vector
   * @param bool   $for_update
   * @param string $fields
   *
   * @return array
   */
  public static function db_planet_by_vector_object($vector, $for_update = false, $fields = '*') {
    $planet_type = $vector->type == PT_DEBRIS ? PT_PLANET : $vector->type;
    $result = DBStaticPlanet::db_planet_by_gspt_safe($vector->galaxy, $vector->system, $vector->planet, $planet_type, $for_update, $fields);

    return !empty($result) ? $result : array();
  }

  public static function db_planet_by_parent($parent_id, $for_update = false, $fields = '*') {
    if (!($parent_id = idval($parent_id))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`parent_planet` = {$parent_id} AND `planet_type` = " . PT_MOON, true);
  }

  public static function db_planet_by_id_and_owner($planet_id, $owner_id, $for_update = false, $fields = '*') {
    //if(!($planet_id = intval($planet_id)) || !($owner_id = intval($owner_id))) return false;
    if (!($planet_id = idval($planet_id)) || !($owner_id = idval($owner_id))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`id` = {$planet_id} AND `id_owner` = {$owner_id}", true);
  }


  public static function db_planet_list_moon_other($user_id, $this_moon_id) {
    // if(!($user_id = intval($user_id)) || !($this_moon_id = intval($this_moon_id))) return false;
    if (!($user_id = idval($user_id)) || !($this_moon_id = idval($this_moon_id))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`planet_type` = " . PT_MOON . " AND `id_owner` = {$user_id} AND `id` != {$this_moon_id}");
  }

  public static function db_planet_list_in_system($galaxy, $system) {
    $galaxy = intval($galaxy);
    $system = intval($system);

    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`galaxy` = {$galaxy} AND `system` = {$system}");
  }

  public static function db_planet_list_sorted($user_row, $skip_planet_id = false, $field_list = '', $conditions = '') {
    if (!is_array($user_row)) {
      return false;
    }
    // $field_list = $field_list != '*' ? "{{planets}}.`id`, `name`, `image`, {{planets}}.`galaxy`, {{planets}}.`system`, {{planets}}.`planet`, `planet_type`{$field_list}" : $field_list;
    $conditions .= $skip_planet_id ? " AND `id` <> {$skip_planet_id} " : '';

    $sort_orders = array(
      SORT_ID       => '{{planets}}.`id`',
      SORT_LOCATION => '{{planets}}.`galaxy`, {{planets}}.`system`, {{planets}}.`planet`, {{planets}}.`planet_type`',
      SORT_NAME     => '`name`',
      SORT_SIZE     => '({{planets}}.`field_max`)',
    );
    $order_by = classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT];
    empty($sort_orders[$order_by]) ? $order_by = SORT_ID : false;
    $order_by = $sort_orders[$order_by] . ' ' . (classSupernova::$user_options[PLAYER_OPTION_PLANET_SORT_INVERSE] == SORT_ASCENDING ? 'ASC' : 'DESC');

    // Compilating query
    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      "`id_owner` = '{$user_row['id']}' {$conditions} ORDER BY {$order_by}");
  }

  public static function db_planet_list_by_user_or_planet($user_id, $planet_id) {
    if (!($user_id = idval($user_id)) && !($planet_id = idval($planet_id))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET,
      $planet_id = idval($planet_id) ? "{{planets}}.`id` = {$planet_id}" : "`id_owner` = {$user_id}", $planet_id);
  }

  /**
   * @param array $planetRowFieldChanges - array of $resourceId => $amount
   * @param int   $planetId
   */
  public static function db_planet_update_resources($planetRowFieldChanges, $planetId) {
    foreach ($planetRowFieldChanges as $resourceId => &$value) {
      $fieldName = pname_resource_name($resourceId);
      $value = "{$fieldName} = {$fieldName} + ('{$value}')";
    }
    if($query = implode(',', $planetRowFieldChanges)) {
      classSupernova::$gc->db->doUpdate("UPDATE `{{planets}}` SET {$query} WHERE id = {$planetId}");
    }
  }

  public static function db_planet_update_set_by_id($planet_id, $set) {
    if (!($planet_id = idval($planet_id))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_upd_record_by_id(LOC_PLANET, $planet_id, $set);
  }

  public static function db_planet_update_set_by_gspt($ui_galaxy, $ui_system, $ui_planet, $ui_planet_type = PT_ALL, $set) {
    if (!($set = trim($set))) {
      return false;
    }

    $si_galaxy = intval($ui_galaxy);
    $si_system = intval($ui_system);
    $si_planet = intval($ui_planet);
    $si_planet_type = ($si_planet_type = intval($ui_planet_type)) ? "AND `planet_type` = {$si_planet_type}" : '';

    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_PLANET, $set, "`galaxy` = {$si_galaxy} AND `system` = {$si_system} AND `planet` = {$si_planet} {$si_planet_type}");
  }

  public static function db_planet_set_by_parent($ui_parent_id, $ss_set) {
    //if(!($si_parent_id = intval($ui_parent_id)) || !($ss_set = trim($ss_set))) return false;
    if (!($si_parent_id = idval($ui_parent_id)) || !($ss_set = trim($ss_set))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_PLANET, $ss_set, "`parent_planet` = {$si_parent_id}");
  }

  public static function db_planet_set_by_owner($ui_owner_id, $ss_set) {
    //if(!($si_owner_id = intval($ui_owner_id)) || !($ss_set = trim($ss_set))) return false;
    if (!($si_owner_id = idval($ui_owner_id)) || !($ss_set = trim($ss_set))) {
      return false;
    }

    return classSupernova::$gc->cacheOperator->db_upd_record_list(LOC_PLANET, $ss_set, "`id_owner` = {$si_owner_id}");
  }


  public static function db_planet_delete_by_id($planet_id) {
    // if(!($planet_id = intval($planet_id))) return false;
    if (!($planet_id = idval($planet_id))) {
      return false;
    }
    classSupernova::$gc->cacheOperator->db_del_record_by_id(LOC_PLANET, $planet_id);
    classSupernova::$gc->cacheOperator->db_del_record_list(LOC_UNIT, "`unit_location_type` = " . LOC_PLANET . " AND `unit_location_id` = " . $planet_id);

    // Очереди очистятся автоматически по FOREIGN KEY
    return true;
  }

  public static function db_planet_list_delete_by_owner($ui_owner_id) {
    // if(!($si_owner_id = intval($ui_owner_id))) return false;
    if (!($si_owner_id = idval($ui_owner_id))) {
      return false;
    }
    classSupernova::$gc->cacheOperator->db_del_record_list(LOC_PLANET, "`id_owner` = {$si_owner_id}");
    classSupernova::$gc->cacheOperator->db_del_record_list(LOC_UNIT, "`unit_location_type` = " . LOC_PLANET . " AND `unit_player_id` = " . $si_owner_id);

    // Очереди очистятся автоматически по FOREIGN KEY
    return true;
  }


  public static function db_planet_count_by_type($ui_user_id, $ui_planet_type = PT_PLANET) {
    // $si_user_id = intval($ui_user_id);
    $si_user_id = idval($ui_user_id);
    $si_planet_type = intval($ui_planet_type);

    // Лочим запись-родителя - если она есть и еще не залочена
    $record_list = classSupernova::$gc->cacheOperator->db_get_record_list(LOC_PLANET, "`id_owner` = {$si_user_id} AND `planet_type` = {$si_planet_type}");

    return is_array($record_list) ? count($record_list) : 0;
    // $planets = doquery("SELECT COUNT(*) AS planet_count FROM {{planets}} WHERE `id_owner` = {$si_user_id} AND `planet_type` = {$si_planet_type}", true);
    // return isset($planets['planet_count']) ? $planets['planet_count'] : 0;
  }

  public static function db_planet_list_resources_by_owner() {
    return classSupernova::$db->doSelect("SELECT `id_owner`, sum(metal) AS metal, sum(crystal) AS crystal, sum(deuterium) AS deuterium FROM `{{planets}}` WHERE id_owner <> 0 /*AND id_owner is not null*/ GROUP BY id_owner;");
  }

}
