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

function PlanetResourceUpdate ( $user, &$planet, $UpdateTime, $Simul = false )
{
  if(!$planet || !isset($planet['id']))
  {
    return false;
  }

  global $debug, $sn_data;

  doquery('START TRANSACTION;');

  $ProductionTime        = ($UpdateTime - $planet['last_update']);
  $planet['last_update'] = $UpdateTime;

  $Caps = ECO_getPlanetCaps($user, $planet);
  $incRes = array ('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

  switch($planet['planet_type'])
  {
    case PT_PLANET:
      foreach($incRes as $resName => &$incCount)
      {
        $Caps['planet'][$resName] = max(0, $Caps['planet'][$resName]);
        $incCount = ($Caps[$resName.'_perhour'][0] + $Caps['planet'][$resName.'_perhour'] * $Caps['production']) * $ProductionTime / 3600 ;

        $store_free = $Caps['planet'][$resName.'_max'] - $Caps['planet'][$resName];
        $incCount = min($incCount, max(0, $store_free));

        if($planet[$resName]<0){
          $GLOBALS['debug']->warning("Player ID {$user['id']} have negative resources on ID {$planet['id']}.{$planet['planet_type']} [{$planet['galaxy']}:{$planet['system']}:{$planet['planet']}]. Difference {$planet[$resName]} of {$resName}", 'Negative Resources', 500);
        }
        $Caps['planet'][$resName] += $incCount;
        $Caps['planet'][$resName.'_perhour'] = $Caps['real'][$resName.'_perhour'];
      }
    break;

    case PT_MOON:
    default:
      $planet['metal_perhour']        = 0;
      $planet['crystal_perhour']      = 0;
      $planet['deuterium_perhour']    = 0;
      $planet['energy_used']          = 0;
      $planet['energy_max']           = 0;
    break;
  }

  $planet = array_merge($planet, $Caps['planet']);

  $que = eco_que_process($user, $planet, $ProductionTime);

  if (!$Simul) {
    $QryUpdatePlanet  = "UPDATE {{planets}} SET ";
    $QryUpdatePlanet .= "`last_update` = '{$planet['last_update']}', ";

    $QryUpdatePlanet .= "`metal`     = `metal`     + '{$incRes['metal']}', ";
    $QryUpdatePlanet .= "`crystal`   = `crystal`   + '{$incRes['crystal']}', ";
    $QryUpdatePlanet .= "`deuterium` = `deuterium` + '{$incRes['deuterium']}', ";

    $QryUpdatePlanet .= "`metal_perhour` = '{$planet['metal_perhour']}', ";
    $QryUpdatePlanet .= "`crystal_perhour` = '{$planet['crystal_perhour']}', ";
    $QryUpdatePlanet .= "`deuterium_perhour` = '{$planet['deuterium_perhour']}', ";

    $QryUpdatePlanet .= "`energy_used` = '{$planet['energy_used']}', ";
    $QryUpdatePlanet .= "`energy_max` = '{$planet['energy_max']}', ";

    $Builded = eco_bld_handle_que($user, $planet, $ProductionTime);
    $QryUpdatePlanet .= "`b_hangar_id` = '{$planet['b_hangar_id']}', ";
    if ( $Builded != '' ) {
      foreach ( $Builded as $Element => $Count ) {
        if ($Element <> '') {
          $QryUpdatePlanet .= "`{$sn_data[$Element]['name']}` = `{$sn_data[$Element]['name']}` + '{$Count}', ";
        }
      }
    }
    $QryUpdatePlanet .= "`b_hangar` = '{$planet['b_hangar']}'";

    $QryUpdatePlanet .= $que['query'] != $planet['que'] ? ",{$que['query']} " : '';

    $QryUpdatePlanet .= "WHERE `id` = '{$planet['id']}' LIMIT 1;";

    doquery($QryUpdatePlanet);

    if(!empty($que['xp']))
    {
      foreach($que['xp'] as $xp_type => $xp_amount)
      {
        rpg_level_up($user, $xp_type, $xp_amount);
      }
    }
  }

  doquery('COMMIT;');

  return $que;
}

function sys_get_updated($user, $planet_id, $UpdateTime, $Simul = false)
{
  if(!$planet_id)
  {
    return false;
  }

  global $debug, $sn_data;

  if (!$Simul) {
    doquery('START TRANSACTION;');
    if(is_array($planet_id))
    {
      $planet = doquery("SELECT * FROM `{{planets}}` WHERE `galaxy` = '{$planet_id['galaxy']}' AND `system` = '{$planet_id['system']}' AND `planet` = '{$planet_id['planet']}' and `planet_type` = '{$planet_id['planet_type']}' LIMIT 1 FOR UPDATE;", '', true);
    }
    else
    {
      $planet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = {$planet_id} LIMIT 1 FOR UPDATE;", '', true);
    }
  }

  if(!$planet || !isset($planet['id']))
  {
    return false;
  }

  $ProductionTime        = ($UpdateTime - $planet['last_update']);
  $planet['last_update'] = $UpdateTime;

  $Caps = ECO_getPlanetCaps($user, $planet);
  $incRes = array ('metal' => 0, 'crystal' => 0, 'deuterium' => 0);

  switch($planet['planet_type'])
  {
    case PT_PLANET:
      foreach($incRes as $resName => &$incCount)
      {
        $Caps['planet'][$resName] = max(0, $Caps['planet'][$resName]);
        $incCount = ($Caps[$resName.'_perhour'][0] + $Caps['planet'][$resName.'_perhour'] * $Caps['production']) * $ProductionTime / 3600 ;

        $store_free = $Caps['planet'][$resName.'_max'] - $Caps['planet'][$resName];
        $incCount = min($incCount, max(0, $store_free));

        if($planet[$resName]<0){
          $GLOBALS['debug']->warning("Player ID {$user['id']} have negative resources on ID {$planet['id']}.{$planet['planet_type']} [{$planet['galaxy']}:{$planet['system']}:{$planet['planet']}]. Difference {$planet[$resName]} of {$resName}", 'Negative Resources', 500);
        }
        $Caps['planet'][$resName] += $incCount;
        $Caps['planet'][$resName.'_perhour'] = $Caps['real'][$resName.'_perhour'];
      }
    break;

    case PT_MOON:
    default:
      $planet['metal_perhour']        = 0;
      $planet['crystal_perhour']      = 0;
      $planet['deuterium_perhour']    = 0;
      $planet['energy_used']          = 0;
      $planet['energy_max']           = 0;
    break;
  }

  $planet = array_merge($planet, $Caps['planet']);

  $que = eco_que_process($user, $planet, $ProductionTime);

  if (!$Simul) {
    $QryUpdatePlanet  = "UPDATE {{planets}} SET ";
    $QryUpdatePlanet .= "`last_update` = '{$planet['last_update']}', ";

    $QryUpdatePlanet .= "`metal`     = `metal`     + '{$incRes['metal']}', ";
    $QryUpdatePlanet .= "`crystal`   = `crystal`   + '{$incRes['crystal']}', ";
    $QryUpdatePlanet .= "`deuterium` = `deuterium` + '{$incRes['deuterium']}', ";

    $QryUpdatePlanet .= "`metal_perhour` = '{$planet['metal_perhour']}', ";
    $QryUpdatePlanet .= "`crystal_perhour` = '{$planet['crystal_perhour']}', ";
    $QryUpdatePlanet .= "`deuterium_perhour` = '{$planet['deuterium_perhour']}', ";

    $QryUpdatePlanet .= "`energy_used` = '{$planet['energy_used']}', ";
    $QryUpdatePlanet .= "`energy_max` = '{$planet['energy_max']}', ";

    $Builded = eco_bld_handle_que($user, $planet, $ProductionTime);
    $QryUpdatePlanet .= "`b_hangar_id` = '{$planet['b_hangar_id']}', ";
    if ( $Builded != '' ) {
      foreach ( $Builded as $Element => $Count ) {
        if ($Element <> '') {
          $QryUpdatePlanet .= "`{$sn_data[$Element]['name']}` = `{$sn_data[$Element]['name']}` + '{$Count}', ";
        }
      }
    }
    $QryUpdatePlanet .= "`b_hangar` = '{$planet['b_hangar']}'";

    $QryUpdatePlanet .= $que['query'] != $planet['que'] ? ",{$que['query']} " : '';

    $QryUpdatePlanet .= "WHERE `id` = '{$planet['id']}' LIMIT 1;";

    doquery($QryUpdatePlanet);

    if(!empty($que['xp']))
    {
      foreach($que['xp'] as $xp_type => $xp_amount)
      {
        rpg_level_up($user, $xp_type, $xp_amount);
      }
    }
    doquery('COMMIT;');
  }

  return array('user' => $user, 'planet' => $planet, 'que' => $que);
}

?>
