<?php

function db_planet_by_id($planet_id, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_record_by_id(LOC_PLANET, $planet_id, $for_update, $fields);
}
function db_planet_by_gspt_safe($galaxy, $system, $planet, $planet_type, $for_update = false, $fields = '*')
{
  return classSupernova::db_get_record_list(LOC_PLANET,
    "{{planets}}.`galaxy` = {$galaxy} AND {{planets}}.`system` = {$system} AND {{planets}}.`planet` = {$planet} AND {{planets}}.`planet_type` = {$planet_type}", true);
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
function db_planet_by_parent($parent_id, $for_update = false, $fields = '*')
{
  if(!($parent_id = intval($parent_id))) return false;
  return classSupernova::db_get_record_list(LOC_PLANET,
    "`parent_planet` = {$parent_id} AND `planet_type` = " . PT_MOON, true);
}
function db_planet_by_id_and_owner($planet_id, $owner_id, $for_update = false, $fields = '*')
{
  if(!($planet_id = intval($planet_id)) || !($owner_id = intval($owner_id))) return false;
  return classSupernova::db_get_record_list(LOC_PLANET,
    "`id` = {$planet_id} AND `id_owner` = {$owner_id}", true);
}


function db_planet_list_moon_other($user_id, $this_moon_id)
{
  if(!($user_id = intval($user_id)) || !($this_moon_id = intval($this_moon_id))) return false;
  return classSupernova::db_get_record_list(LOC_PLANET,
    "`planet_type` = " . PT_MOON . " AND `id_owner` = {$user_id} AND `id` != {$this_moon_id}");
}
function db_planet_list_in_system($galaxy, $system)
{
  $galaxy = intval($galaxy);
  $system = intval($system);
  return classSupernova::db_get_record_list(LOC_PLANET,
    "`galaxy` = {$galaxy} AND `system` = {$system}");
}

function db_planet_list_sorted($user_row, $skip_planet_id = false, $field_list = '', $conditions = '')
{
  $field_list = $field_list != '*' ? "{{planets}}.`id`, `name`, `image`, {{planets}}.`galaxy`, {{planets}}.`system`, {{planets}}.`planet`, `planet_type`{$field_list}" : $field_list;
  $conditions .= $skip_planet_id ? " AND `id` <> {$skip_planet_id} " : '';

  $sort_orders = array(
    SORT_ID       => '{{planets}}.`id`',
    SORT_LOCATION => '{{planets}}.`galaxy`, {{planets}}.`system`, {{planets}}.`planet`, {{planets}}.`planet_type`',
    SORT_NAME     => '`name`',
    SORT_SIZE     => '({{planets}}.`field_max`)',
  );
  $order_by =
    (isset($sort_orders[$user_row['planet_sort']])
      ? $sort_orders[$user_row['planet_sort']]
      : $sort_orders[SORT_ID])
    . ($user_row['planet_sort_order'] == SORT_DESCENDING ? " DESC" : " ASC");

  // Compilating query
  return classSupernova::db_get_record_list(LOC_PLANET, // FIXED
    "`id_owner` = '{$user_row['id']}' {$conditions} ORDER BY {$order_by}");
}
function db_planet_list_by_user_or_planet($user_id, $planet_id)
{
  if(!($user_id = intval($user_id)) && !($planet_id = intval($planet_id))) return false;

  return classSupernova::db_get_record_list(LOC_PLANET, // FIXED
    $planet_id = intval($planet_id) ? "{{planets}}.`id` = {$planet_id}" : "`id_owner` = {$user_id}", $planet_id);
}

function db_planet_set_by_id($planet_id, $set)
{
  return classSupernova::db_upd_record_by_id(LOC_PLANET, $planet_id, $set);
}
function db_planet_set_by_gspt($galaxy, $system, $planet, $planet_type = PT_ALL, $set)
{
  if(!($set = trim($set))) return false;

  $galaxy = intval($galaxy);
  $system = intval($system);
  $planet = intval($planet);
  $planet_type = ($planet_type = intval($planet_type)) ? "AND `planet_type` = {$planet_type}" : '';

  return classSupernova::db_upd_record_list(LOC_PLANET, "`galaxy` = {$galaxy} AND `system` = {$system} AND `planet` = {$planet} {$planet_type}", $set);
}
function db_planet_set_by_parent($parent_id, $set)
{
  if(!($parent_id = intval($parent_id)) || !($set = trim($set))) return false;
  return classSupernova::db_upd_record_list(LOC_PLANET, "`parent_planet` = {$parent_id}", $set);
}
function db_planet_set_by_owner($owner_id, $set)
{
  if(!($owner_id = intval($owner_id)) || !($set = trim($set))) return false;
  return classSupernova::db_upd_record_list(LOC_PLANET, "`id_owner` = {$owner_id}", $set);
}


function db_planet_insert_set($set)
{
  return classSupernova::db_ins_record(LOC_PLANET, $set);
}


function db_planet_delete_by_id($planet_id)
{
  return classSupernova::db_del_record_by_id(LOC_PLANET, $planet_id);
}
function db_planet_list_delete_by_owner($owner_id)
{
  if(!($owner_id = intval($owner_id))) return false;
  return classSupernova::db_del_record_list(LOC_PLANET, "`id_owner` = {$owner_id}");
}



function db_planet_count_by_type($user_id, $planet_type = PT_PLANET)
{
  $user_id = intval($user_id);
  $planet_type = intval($planet_type);

  $planets = doquery("SELECT COUNT(*) AS planet_count FROM {{planets}} WHERE id_owner = {$user_id} AND `planet_type` = {$planet_type} FOR UPDATE", true);
  return isset($planets['planet_count']) ? $planets['planet_count'] : 0;
}
function db_planet_list_resources_by_owner()
{
  return doquery("SELECT `id_owner`, sum(metal) AS metal, sum(crystal) AS crystal, sum(deuterium) AS deuterium FROM {{planets}} WHERE id_owner <> 0 /*AND id_owner is not null*/ GROUP BY id_owner;");
}
