<?php

/*
 * PlanetResourceUpdate.php
 *
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Bit more optimization
 * 2.0 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *     [+] Full rewrote and optimization
 *
 */
function sys_o_get_updated($user, $planet, $UpdateTime, $simulation = false)
{
  global $lang;

  $no_data = array('user' => false, 'planet' => false, 'que' => false);

  if(!$planet)
  {
    return $no_data;
  }

  $suffix = $simulation ? '' : 'FOR UPDATE';
  if(is_array($planet))
  {
    if(!(isset($planet['id']) && $planet['id']) || !$simulation)
    {
      $planet = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$planet['galaxy']}' AND `system` = '{$planet['system']}' AND `planet` = '{$planet['planet']}' and `planet_type` = '{$planet['planet_type']}' LIMIT 1 {$suffix};", '', true);
    }
  }
  else
  {
    $planet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$planet}' LIMIT 1 {$suffix};", '', true);
  }

  if(!($planet && isset($planet['id']) && $planet['id']))
  {
    return $no_data;
  }

  if(!$user || !is_array($user) || !isset($user['id']))
  {
    $user = doquery("SELECT * FROM `{{users}}` WHERE `id` = {$planet['id_owner']} LIMIT 1 {$suffix};", '', true);
    if(!$user)
    {
      return $no_data;
    }
  }

  $ProductionTime = max(0, $UpdateTime - $planet['last_update']);
  $planet['prev_update'] = $planet['last_update'];
  $planet['last_update'] += $ProductionTime;

  $que = eco_que_process($user, $planet, $ProductionTime);
  $hangar_built = $ProductionTime && !$simulation ? eco_bld_que_hangar($user, $planet, $ProductionTime) : array();

  $caps_real = eco_get_planet_caps($user, $planet, $ProductionTime);
  $resources_increase = array(
    RES_METAL => 0,
    RES_CRYSTAL => 0,
    RES_DEUTERIUM => 0,
  );

  switch($planet['planet_type'])
  {
    case PT_PLANET:
      foreach($resources_increase as $resource_id => &$increment)
      {
        $resource_name = get_unit_param($resource_id, P_NAME);
        $increment = $caps_real['total'][$resource_id] * $ProductionTime / 3600;
        $store_free = $caps_real['total_storage'][$resource_id] - $planet[$resource_name];
        $increment = min($increment, max(0, $store_free));

        if($planet[$resource_name] + $increment < 0 && !$simulation)
        {
          global $debug;
          $debug->warning("Player ID {$user['id']} have negative resources on ID {$planet['id']}.{$planet['planet_type']} [{$planet['galaxy']}:{$planet['system']}:{$planet['planet']}]. Difference {$planet[$resource_name]} of {$resource_name}", 'Negative Resources', 501);
        }
        $planet[$resource_name] += $increment;
        $planet[$resource_name . '_perhour'] = $caps_real['total'][$resource_id];
      }
    break;

    case PT_MOON:
    default:
      $planet['metal_perhour'] = 0;
      $planet['crystal_perhour'] = 0;
      $planet['deuterium_perhour'] = 0;
      $planet['energy_used'] = 0;
      $planet['energy_max'] = 0;
    break;
  }

  $planet['field_current'] = 0;
  $sn_group_build_allow = sn_get_groups('build_allow');
  foreach($sn_group_build_allow[$planet['planet_type']] as $building_id)
  {
    $planet['field_current'] += $planet[get_unit_param($building_id, P_NAME)];
  }

  if($simulation)
  {
    return array('user' => $user, 'planet' => $planet, 'que' => $que);
  }

  $QryUpdatePlanet = "UPDATE {{planets}} SET `last_update` = '{$planet['last_update']}',
    `field_current` = {$planet['field_current']},
    `metal`     = `metal`     + '{$resources_increase[RES_METAL]}',
    `crystal`   = `crystal`   + '{$resources_increase[RES_CRYSTAL]}',
    `deuterium` = `deuterium` + '{$resources_increase[RES_DEUTERIUM]}',
    `metal_perhour` = '{$planet['metal_perhour']}',
    `crystal_perhour` = '{$planet['crystal_perhour']}',
    `deuterium_perhour` = '{$planet['deuterium_perhour']}',
    `energy_used` = '{$planet['energy_used']}',
    `energy_max` = '{$planet['energy_max']}', ";

  if(!empty($hangar_built['built']))
  {
    foreach($hangar_built['built'] as $Element => $Count)
    {
      $Element = intval($Element);
      $Count = intval($Count);
      if($Element)
      {
        $db_name = get_unit_param($Element, P_NAME);
        $QryUpdatePlanet .= "`{$db_name}` = `{$db_name}` + '{$Count}', ";
      }
    }
    if(!$planet['b_hangar_id'])
    {
      msg_send_simple_message($user['id'], 0, SN_TIME_NOW, MSG_TYPE_QUE, $lang['msg_que_planet_from'], $lang['msg_que_hangar_subject'], sprintf($lang['msg_que_hangar_message'], uni_render_planet($planet)));
    }
  }

  $QryUpdatePlanet .= "`b_hangar_id` = '{$planet['b_hangar_id']}', `b_hangar` = '{$planet['b_hangar']}' ";
  $QryUpdatePlanet .= $que['query'] != $planet['que'] ? ",{$que['query']} " : '';

  if(!empty($que['built']))
  {
    $message = array();
    foreach($que['built'] as $unit_id => $built_count)
    {
      if($built_count > 0)
      {
        $message[] = sprintf($lang['msg_que_built_message'], uni_render_planet($planet), $lang['tech'][$unit_id], $built_count);
      }
      else
      {
        $message[] = sprintf($lang['msg_que_destroy_message'], uni_render_planet($planet), $lang['tech'][$unit_id], -$built_count);
      }
    }
    msg_send_simple_message($user['id'], 0, SN_TIME_NOW, MSG_TYPE_QUE, $lang['msg_que_planet_from'], $lang['msg_que_built_subject'], implode('<br />', $message));
  }

  $QryUpdatePlanet .= "WHERE `id` = '{$planet['id']}' LIMIT 1;";
  doquery($QryUpdatePlanet);

  if(!empty($que['xp']))
  {
    foreach($que['xp'] as $xp_type => $xp_amount)
    {
      rpg_level_up($user, $xp_type, $xp_amount);
    }
  }

  // Can't use array_merge here - it will broke numeric array indexes those broke quest_id
  // TODO: Make own function for this
  if(!empty($hangar_built['rewards']))
  {

    foreach($hangar_built['rewards'] as $quest_id => $quest_reward)
    {
      $que['rewards'][$quest_id] = $quest_reward;
    }
  }
  qst_reward($user, $planet, $que['rewards'], $que['quests']);

  //$planet['planet_caps'] = $Caps;

  return array('user' => $user, 'planet' => $planet, 'que' => $que);
}
