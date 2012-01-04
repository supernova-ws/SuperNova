<?php

/**
 * PlanetResourceUpdate.php
 *
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Bit more optimization
 * 2.0 - copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *     [+] Full rewrote and optimization
 * 1.1 - @copyright 2008 By Chlorel for XNova
 *     [*] Mise a jour automatique mines / silos / energie ...
 * 1.0 - @copyright 2008 By Chlorel for XNova
 *     [*] Mise en module initiale
 */
function sys_o_get_updated($user, $planet, $UpdateTime, $simulation = false)
{
  global $time_now, $sn_data, $lang;

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
  $planet['last_update'] += $ProductionTime;

  $Caps = eco_get_planet_caps($user, $planet);
  $incRes = array('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

  switch($planet['planet_type'])
  {
    case PT_PLANET:
      foreach($incRes as $resName => &$incCount)
      {
//        $Caps['planet'][$resName] = max(0, $Caps['planet'][$resName]);
        $incCount = ($Caps[$resName . '_perhour'][0] + $Caps['planet'][$resName . '_perhour'] * $Caps['production']) * $ProductionTime / 3600;

        $store_free = $Caps['planet'][$resName . '_max'] - $Caps['planet'][$resName];
//        $incCount = max(0, min($incCount, max(0, $store_free)));
        $incCount = min($incCount, max(0, $store_free));

        if($planet[$resName] + $incCount < 0)
        {
          $GLOBALS['debug']->warning("Player ID {$user['id']} have negative resources on ID {$planet['id']}.{$planet['planet_type']} [{$planet['galaxy']}:{$planet['system']}:{$planet['planet']}]. Difference {$planet[$resName]} of {$resName}", 'Negative Resources', 501);
        }
        $Caps['planet'][$resName] += $incCount;
        $Caps['planet'][$resName . '_perhour'] = $Caps['real'][$resName . '_perhour'];
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

  $planet = array_merge($planet, $Caps['planet']);

  $que = eco_que_process($user, $planet, $ProductionTime);

  if($simulation)
  {
    return array('user' => $user, 'planet' => $planet, 'que' => $que);
  }

  $QryUpdatePlanet = "UPDATE {{planets}} SET `last_update` = '{$planet['last_update']}', ";
  $QryUpdatePlanet .= "`metal`     = `metal`     + '{$incRes['metal']}', `crystal`   = `crystal`   + '{$incRes['crystal']}', `deuterium` = `deuterium` + '{$incRes['deuterium']}', ";
  $QryUpdatePlanet .= "`metal_perhour` = '{$planet['metal_perhour']}', `crystal_perhour` = '{$planet['crystal_perhour']}', `deuterium_perhour` = '{$planet['deuterium_perhour']}', ";
  $QryUpdatePlanet .= "`energy_used` = '{$planet['energy_used']}', `energy_max` = '{$planet['energy_max']}', ";

  $built = eco_bld_que_hangar($user, $planet, $ProductionTime);
  if($built['built'])
  {
    foreach($built['built'] as $Element => $Count)
    {
      $Element = intval($Element);
      $Count = intval($Count);
      if($Element)
      {
        $QryUpdatePlanet .= "`{$sn_data[$Element]['name']}` = `{$sn_data[$Element]['name']}` + '{$Count}', ";
      }
    }
    if(!$planet['b_hangar'])
    {
      msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_planet_from'], $lang['msg_que_hangar_subject'], sprintf($lang['msg_que_hangar_message'], uni_render_planet($planet)));
    }
  }

  $QryUpdatePlanet .= "`b_hangar_id` = '{$planet['b_hangar_id']}', ";
  $QryUpdatePlanet .= "`b_hangar` = '{$planet['b_hangar']}' ";

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
    msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_planet_from'], $lang['msg_que_built_subject'], implode('<br />', $message));
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
  foreach($built['rewards'] as $quest_id => $quest_reward)
  {
    $que['rewards'][$quest_id] = $quest_reward;
  }
  qst_reward($user, $planet, $que['rewards'], $que['quests']);

  $planet['planet_caps'] = $Caps;

  return array('user' => $user, 'planet' => $planet, 'que' => $que);
}

?>
