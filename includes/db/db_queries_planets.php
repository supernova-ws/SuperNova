<?php

function db_planet_by_id($planet_id, $for_update = false, $fields = '*')
{
  return ($planet_id = intval($planet_id)) ? doquery("SELECT {$fields} FROM {{planets}} WHERE `id` = {$planet_id} LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true) : false;
}

function db_planet_by_parent($parent_id, $for_update = false, $fields = '*')
{
  return ($parent_id = intval($parent_id)) ? doquery("SELECT {$fields} FROM {{planets}} WHERE `parent_planet` = {$parent_id} AND `planet_type` = " . PT_MOON . " LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true) : false;
}

function db_planet_by_id_and_owner($planet_id, $owner_id, $for_update = false, $fields = '*')
{
  return ($planet_id = intval($planet_id)) && ($owner_id = intval($owner_id))
    ? doquery("SELECT {$fields} FROM {{planets}} WHERE `id` = {$planet_id} AND `id_owner` = {$owner_id} LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true)
    : false;
}

function db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update = false, $fields = '*')
{
  return doquery("SELECT {$fields} FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$planet} AND `planet_type` = {$planet_type} LIMIT 1" . ($for_update ? ' FOR UPDATE' : ''), true);
}

function db_planet_by_gspt($galaxy, $system, $planet, $planet_type, $for_update = false, $fields = '*')
{
  $galaxy = intval($galaxy);
  $system = intval($system);
  $planet = intval($planet);
  $planet_type = intval($planet_type);

  return db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update, $fields);
}

function db_planet_by_vector($vector, $prefix = '', $for_update = false, $fields = '*')
{
  $galaxy = isset($vector[$prefix . 'galaxy']) ? intval($vector[$prefix . 'galaxy']) : 0;
  $system = isset($vector[$prefix . 'system']) ? intval($vector[$prefix . 'system']) : 0;
  $planet = isset($vector[$prefix . 'planet']) ? intval($vector[$prefix . 'planet']) : 0;
  $planet_type = isset($vector[$prefix . 'planet_type']) ? intval($vector[$prefix . 'planet_type']) :
    (isset($vector[$prefix . 'type']) ? intval($vector[$prefix . 'type']) : 0);

  return db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update, $fields);
}



function db_planet_list_moon_other($user_id, $this_moon_id)
{
  $user_id = intval($user_id);
  $this_moon_id = intval($this_moon_id);

  return doquery("SELECT * FROM {{planets}} WHERE `planet_type` = " . PT_MOON . " AND `id_owner` = {$user_id} AND `id` != {$this_moon_id}");
}

function db_planet_list_in_system($galaxy, $system)
{
  $galaxy = intval($galaxy);
  $system = intval($system);

  return doquery("SELECT * FROM {{planets}} WHERE `galaxy` = {$galaxy} AND `system` = {$system}");
}

function db_planet_list_by_owner($owner_id, $for_update = false, $fields = '*')
{
  $owner_id = intval($owner_id);
  return doquery("SELECT {$fields} FROM {{planets}} WHERE `id_owner` = {$owner_id}" . ($for_update ? ' FOR UPDATE' : ''));
}

function db_planet_list_sorted($user_row, $skip_planet_id = false, $field_list = '', $conditions = '')
{
  $field_list = $field_list != '*' ? "`id`, `name`, `image`, `galaxy`, `system`, `planet`, `planet_type`{$field_list}" : $field_list;
  $conditions .= $skip_planet_id ? " AND `id` <> {$skip_planet_id} " : '';

  $sort_orders = array(
    SORT_ID       => '`id`',
    SORT_LOCATION => '`galaxy`, `system`, `planet`, `planet_type`',
    SORT_NAME     => '`name`',
    SORT_SIZE     => '(`field_max` + `terraformer` * 5 + `mondbasis` * 3)',
  );
  $order_by = (isset($sort_orders[$user_row['planet_sort']]) ? $sort_orders[$user_row['planet_sort']] : $sort_orders[SORT_ID])
    . ($user_row['planet_sort_order'] == SORT_DESCENDING ? " DESC" : " ASC");

  // Compilating query
  $QryPlanets = "SELECT {$field_list} FROM {{planets}} WHERE `id_owner` = '{$user_row['id']}' {$conditions} ORDER BY {$order_by}";

  return doquery($QryPlanets);
}


function db_planet_list_by_user_or_planet($user_id, $planet_id)
{
  return doquery("SELECT * FROM {{planets}} WHERE " . ($planet_id = intval($planet_id) ? "`id` = {$planet_id} LIMIT 1" : "`id_owner` = {$user_id}") . " FOR UPDATE");
}





function db_planet_list_resources_by_owner()
{
  return doquery("SELECT `id_owner`, sum(metal) AS metal, sum(crystal) AS crystal, sum(deuterium) AS deuterium FROM {{planets}} WHERE id_owner <> 0 /*AND id_owner is not null*/ GROUP BY id_owner;");
}










function db_planet_set_by_id($planet_id, $set)
{
  return ($planet_id = intval($planet_id)) && ($set = trim($set)) ? doquery("UPDATE {{planets}} SET {$set} WHERE `id` = {$planet_id} LIMIT 1") : false;
}

function db_planet_set_by_gspt($galaxy, $system, $planet, $planet_type = PT_ALL, $set)
{
  $galaxy = intval($galaxy);
  $system = intval($system);
  $planet = intval($planet);
  $planet_type = ($planet_type = intval($planet_type)) ? "AND `planet_type` = {$planet_type}" : '';

  return ($set = trim($set)) ? doquery("UPDATE {{planets}} SET {$set} WHERE `galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$planet} {$planet_type} LIMIT 1") : false;
}

function db_planet_set_by_parent($parent_id, $set)
{
  return ($parent_id = intval($parent_id)) && ($set = trim($set)) ? doquery("UPDATE {{planets}} SET {$set} WHERE `parent_planet` = {$parent_id} LIMIT 1") : false;
}

function db_planet_list_set_by_owner($owner_id, $set)
{
  return ($owner_id = intval($owner_id)) && ($set = trim($set)) ? doquery("UPDATE {{planets}} SET {$set} WHERE `id_owner` = {$owner_id}") : false;
}






function db_planet_delete_by_id($planet_id)
{
  return ($planet_id = intval($planet_id)) ? doquery("DELETE FROM {{planets}} WHERE `id` = {$planet_id} LIMIT 1") : false;
}



function db_planet_list_delete_by_owner($owner_id)
{
  return ($owner_id = intval($owner_id)) ? doquery("DELETE FROM {{planets}} WHERE `id_owner` = {$owner_id}") : false;
}





function db_planet_insert_set($set)
{
  return ($set = trim($set)) ? doquery("INSERT INTO `{{planets}}` SET {$set}") : false;
}



function db_planet_count_by_type($user_id, $planet_type = PT_PLANET)
{
  $user_id = intval($user_id);
  $planet_type = intval($planet_type);

  $planets = doquery("SELECT COUNT(*) AS planet_count FROM {{planets}} WHERE id_owner = {$user_id} AND `planet_type` = {$planet_type} FOR UPDATE", true);
  return isset($planets['planet_count']) ? $planets['planet_count'] : 0;
}

function db_planet_list_by_search($planet_id, $queryPart)
{
  return doquery("SELECT id, name, id_owner, galaxy, system, planet FROM {{planets}} WHERE `name` like '{$planet_id}'" . $queryPart);
}

