<?php
/**
 * index.php - overview.php
 *
 * 2.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [-] Removed News frame
 *     [-] Time & Usersonline moved to Top-Frame
 * 2.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Complying with PCG
 * 2.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Redo flying fleet list
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Planets on planet list now have indication of planet fill
 *     [+] Planets on planet list now have indication when there is enemy fleet flying to planet
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Now there is full planet list on right side of screen a-la oGame
 *     [+] Planet list now include icons for buildings/tech/fleet on progress
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Subplanet timers now use sn_timer.js library
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] All mainplanet timers now use new sn_timer.js library
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of player infos
 * 1.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of planet infos
 * 1.1 - Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');

$mode            = $_GET['mode'];
switch ($mode)
{
  case 'manage':
    $template        = gettemplate('planet_manage', true);
    $planet_id       = sys_get_param_int('planet_id');

    $rename          = sys_get_param_str('rename');
    $new_name        = sys_get_param_str('new_name', 'Colony');

    $abandon         = sys_get_param_str('abandon');
    $abandon_confirm = $_POST['abandon_confirm'];

    if ($rename && $new_name)
    {
      $planetrow['name'] = $new_name;
      $new_name = mysql_real_escape_string($new_name);
      doquery("UPDATE {{planets}} SET `name` = '{$new_name}' WHERE `id` = '{$planetrow['id']}' LIMIT 1;");
    }
    elseif ($abandon)
    {
      if (md5($abandon_confirm) == $user['password'])
      {
        if($user['id_planet'] != $user['current_planet'] && $user['current_planet'] == $planet_id)
        {
          $destruyed        = $time_now + 60 * 60 * 24;
          doquery("UPDATE {{planets}} SET `destruyed`='{$destruyed}', `id_owner`='0' WHERE `id`='{$user['current_planet']}' LIMIT 1;");
          doquery("UPDATE {{planets}} SET `destruyed`='{$destruyed}', `id_owner`='0' WHERE `parent_planet`='{$user['current_planet']}' LIMIT 1;");
          doquery("UPDATE {{users}} SET `current_planet` = `id_planet` WHERE `id` = '{$user['id']}' LIMIT 1");
          message($lang['ov_delete_ok'], $lang['colony_abandon'], 'overview.php?mode=manage');
        }
        else
        {
          message($lang['ov_delete_wrong_planet'], $lang['colony_abandon'], 'overview.php?mode=manage');
        }
      }
      else
      {
        message($lang['ov_delete_wrong_pass'] , $lang['colony_abandon'], 'overview.php?mode=manage');
      }
    }

    int_planet_pretemplate($template);
    foreach($sn_data['groups']['governors'] as $governor_id)
    {
      $template->assign_block_vars('governors', array(
        'ID' => $governor_id,
        'NAME' => $lang['tech'][$governor_id],
      ));
    }

    display(parsetemplate($template), $lang['rename_and_abandon_planet']);
  break;

  default:
    // --- Gestion des messages ----------------------------------------------------------------------
    $template = gettemplate('planet_overview', true);

    // --- Gestion Officiers -------------------------------------------------------------------------
    // Passage au niveau suivant, ajout du point de compétence et affichage du passage au nouveau level
    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);

    // -----------------------------------------------------------------------------------------------
    // Filling table with fleet events relating to current users
    $fleets = array();
    $fleet_number = 0;
    $flying_fleets_mysql = doquery(
      "SELECT DISTINCT * FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}' OR `fleet_target_owner` = '{$user['id']}';"
    );

    while ($fleet = mysql_fetch_assoc($flying_fleets_mysql))
    {
      $planet_start_type = $fleet['fleet_start_type'] == 3 ? 3 : 1;
      $planet_start = doquery(
        "SELECT `name` FROM {{planets}}
          WHERE
            galaxy = {$fleet['fleet_start_galaxy']} AND
            system = {$fleet['fleet_start_system']} AND
            planet = {$fleet['fleet_start_planet']} AND
            planet_type = {$planet_start_type}
        ", '', true);
      $fleet['fleet_start_name'] = $planet_start['name'];

      if($fleet['fleet_end_planet'] > $config->game_maxPlanet)
      {
        $fleet['fleet_end_name'] = $lang['ov_fleet_exploration'];
      }
      elseif($fleet['fleet_mission'] == MT_COLONIZE)
      {
        $fleet['fleet_end_name'] = $lang['ov_fleet_colonization'];
      }
      else
      {
        $planet_end_type = $fleet['fleet_end_type'] == 3 ? 3 : 1;
        $planet_end = doquery(
          "SELECT `name` FROM {{planets}}
            WHERE
              galaxy = {$fleet['fleet_end_galaxy']} AND
              system = {$fleet['fleet_end_system']} AND
              planet = {$fleet['fleet_end_planet']} AND
              planet_type = {$planet_end_type}
          ", '', true);
        $fleet['fleet_end_name'] = $planet_end['name'];
      }

      if($fleet['fleet_start_time'] > $time_now && $fleet['fleet_mess'] == 0)
      {
        int_assign_event($fleet, 0);
      }
      if($fleet['fleet_end_stay'] > $time_now && $fleet['fleet_mess'] == 0)
      {
        int_assign_event($fleet, 1);
      }
      if($fleet['fleet_end_time'] > $time_now && $fleet['fleet_owner'] == $user['id'] &&
        !($fleet['fleet_mess'] == 0 &&
          ($fleet['fleet_mission'] == MT_RELOCATE || $fleet['fleet_mission'] == MT_COLONIZE)))
      {
        int_assign_event($fleet, 2);
      }
    }

    // -----------------------------------------------------------------------------------------------
    // Adding missile attacks to fleet event table
    $iraks_query = doquery("SELECT * FROM `{{iraks}}` WHERE `owner` = '{$user['id']}'");
    while ($irak = mysql_fetch_assoc ($iraks_query))
    {
      if ($irak['zeit'] >= $time_now) {
        $irak['fleet_id']             = -$irak['anzahl'];
        $irak['fleet_owner']          = $irak['owner'];
        $irak['fleet_mission']        = MT_MISSILE;
        $irak['fleet_array']          = "503,{$irak['anzahl']};";
        $irak['fleet_amount']         = $irak['anzahl'];

        $planet_end = doquery("SELECT `name` FROM `{{planets}}` WHERE
          `galaxy` = '{$irak['galaxy']}' AND
          `system` = '{$irak['system']}' AND
          `planet` = '{$irak['planet']}' AND
          `planet_type` = '1'", '', true);
        $irak['fleet_end_galaxy']     = $irak['galaxy'];
        $irak['fleet_end_system']     = $irak['system'];
        $irak['fleet_end_planet']     = $irak['planet'];
        $irak['fleet_end_type']       = 1;
        $irak['fleet_end_time']       = $irak['zeit'];
        $irak['fleet_end_name']       = $planet_end['name'];

        $planet_start = doquery("SELECT `name` FROM `{{planets}}` WHERE
          `galaxy` = '{$irak['galaxy_angreifer']}' AND
          `system` = '{$irak['system_angreifer']}' AND
          `planet` = '{$irak['planet_angreifer']}' AND
          `planet_type` = '1'", '', true);
        $irak['fleet_start_galaxy']   = $irak['galaxy_angreifer'];
        $irak['fleet_start_system']   = $irak['system_angreifer'];
        $irak['fleet_start_planet']   = $irak['planet_angreifer'];
        $irak['fleet_start_type']     = 1;
        $irak['fleet_start_name']     = $planet_start['name'];
        //$irak['fleet_start_time']   = $irak['zeit'];

        int_assign_event($irak, 3);
      }
    }

    // -----------------------------------------------------------------------------------------------
    // --- Gestion de la liste des planetes ----------------------------------------------------------
    // Planetes ...
    $planets_query = SortUserPlanets($user, false, '*');

    $fleet_id = 1;
    while ($UserPlanet = mysql_fetch_assoc($planets_query))
    {
      if($UserPlanet['planet_type'] == PT_MOON)
      {
        continue;
      }

      $UserPlanet      = sys_o_get_updated($user, $UserPlanet, $time_now, true);
      $list_planet_que = $UserPlanet['que'];
      $UserPlanet      = $UserPlanet['planet'];

      $template_planet = tpl_parse_planet($UserPlanet, $list_planet_que);

      $planet_fleet_id = 0;
      $fleet_list = $template_planet['fleet_list'];
      if($fleet_list['own']['count'])
      {
        $planet_fleet_id = "p{$fleet_id}";
        $fleets[] = tpl_parse_fleet_sn($fleet_list['own']['total'], $planet_fleet_id);
        $fleet_id++;
      }
      $moon = doquery("SELECT * FROM {{planets}} WHERE `parent_planet` = '{$UserPlanet['id']}' AND `planet_type` = 3 LIMIT 1;", '', true);
      if($moon)
      {
        $moon_fill = min(100, floor($moon['field_current'] / eco_planet_fields_max($moon) * 100));
      }
      else
      {
        $moon_fill = 0;
      }

      $moon_fleets = flt_get_fleets_to_planet($moon);
      $template->assign_block_vars('planet', array_merge($template_planet, array(
          'PLANET_FLEET_ID'  => $planet_fleet_id,

          'MOON_ID'      => $moon['id'],
          'MOON_NAME'    => $moon['name'],
          'MOON_IMG'     => $moon['image'],
          'MOON_FILL'    => min(100, $moon_fill),
          'MOON_ENEMY'   => $moon_fleets['enemy']['count'],

          'MOON_PLANET'  => $moon['parent_planet'],
      )));
    }

    tpl_assign_fleet($template, $fleets);

    // -----------------------------------------------------------------------------------------------
    $parse                         = $lang;

    // --- Gestion de l'affichage d'une lune ---------------------------------------------------------
    if($planetrow['planet_type'] == PT_PLANET)
    {
      $lune = doquery("SELECT * FROM {{planets}} WHERE `parent_planet` = '{$planetrow['id']}' AND `planet_type` = " . PT_MOON . " LIMIT 1;", '', true);
    }
    else
    {
      $lune = doquery("SELECT * FROM {{planets}} WHERE `id` = '{$planetrow['parent_planet']}' AND `planet_type` = " . PT_PLANET . " LIMIT 1;", '', true);
    }

    if ($lune)
    {
      $template->assign_vars(array(
        'MOON_ID' => $lune['id'],
        'MOON_IMG' => $lune['image'],
        'MOON_NAME' => $lune['name'],
      ));
    }
    // Moon END

    $planet_fill = floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100);
    $planet_fill = $planet_fill > 100 ? 100 : $planet_fill;

/*
    $ally = $user['ally_id'];
    $OnlineUsersNames = doquery("SELECT `username` FROM {{users}} WHERE `onlinetime`>'".$time."' AND `ally_id`='".$ally."' AND `ally_id` != '0'");

    $names = '';
    while ($OUNames = mysql_fetch_assoc($OnlineUsersNames)) {
      $names .= $OUNames['username'];
      $names .= ", ";
    }
    $parse['MembersOnline2'] = $names;
*/
/*
    //��������� ��������� ����.
    $mess = doquery("SELECT `user`,`message` FROM {{chat}} WHERE `ally_id` = '0' ORDER BY `messageid` DESC LIMIT 5");
    $msg = '<table>';
    while ($result = mysql_fetch_assoc($mess)) {
      //$str = substr($result['message'], 0, 85);
      $str = $result['message'];
      $usr = $result['user'];
      $msg .= "<tr><td align=\"left\">".$usr.":</td><td>".$str."</td></tr>";
    }
    $msg .= '</table>';
*/
    $recyclers_send = min(ceil(($planetrow['debris_metal'] + $planetrow['debris_crystal']) / $sn_data[SHIP_RECYCLER]['capacity']), $planetrow[$sn_data[SHIP_RECYCLER]['name']]);

    int_planet_pretemplate($template);

    foreach(array(QUE_STRUCTURES => $sn_data['groups']['ques'][QUE_STRUCTURES]) as $que_id => $que_type_data)
    {
      $template->assign_block_vars('ques', array(
        ID     => $que_id,
        NAME   => $lang['sys_ques'][$que_id],
        LENGTH => count($que['que'][$que_id]),
      ));

      if($que['que'][$que_id])
      {
        foreach($que['que'][$que_id] as $que_item)
        {
          $template->assign_block_vars('que', $que_item);
        }
      }
    }

    $template->assign_vars(array(
      'TIME_NOW'             => $time_now,

      'USER_ID'              => $user['id'],
      'user_username'        => $user['username'],
      'USER_AUTHLEVEL'       => $user['authlevel'],

      'NEW_MESSAGES'         => $user['new_message'],
      'NEW_LEVEL_MINER'      => $level_miner,
      'NEW_LEVEL_RAID'       => $level_raid,

      'BUILDING'             => int_buildCounter($planetrow, 'building', '', $que),
      'HANGAR'               => int_buildCounter($planetrow, 'hangar'),
      'TECH'                 => int_buildCounter($planetrow, 'tech'),
      'planet_diameter'      => pretty_number($planetrow['diameter']),
      'planet_field_current' => $planetrow['field_current'],
      'planet_field_max'     => eco_planet_fields_max($planetrow),
      'PLANET_FILL'          => floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100),
      'PLANET_FILL_BAR'      => $planet_fill,
      'metal_debris'         => pretty_number($planetrow['debris_metal']),
      'crystal_debris'       => pretty_number($planetrow['debris_crystal']),
      'RECYCLERS_SEND'       => $recyclers_send,
      'planet_temp_min'      => $planetrow['temp_min'],
      'planet_temp_max'      => $planetrow['temp_max'],

      'ADMIN_EMAIL'          => $config->game_adminEmail,

      //'LastChat'       => CHT_messageParse($msg),
    ));

    display(parsetemplate($template, $parse), "{$lang['ov_overview']} - {$lang['sys_planet_type'][$planetrow['planet_type']]} {$planetrow['name']} [{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}]");
  break;
}

function int_assign_event($fleet, $ov_label)
{
  global $fleets, $fleet_number, $planetrow, $planet_end_type, $user;

  $fleet['ov_label'] = $ov_label;
  switch($ov_label)
  {
    case 0:
      $fleet['ov_time'] = $fleet['fleet_start_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 1:
      $fleet['ov_time'] = $fleet['fleet_end_stay'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_end_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_end_system']) AND
        ($planetrow['planet'] == $fleet['fleet_end_planet']) AND
        ($planetrow['planet_type'] == $planet_end_type));
    break;

    case 2:
    case 3:
      $fleet['ov_time'] = $fleet['fleet_end_time'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['fleet_start_galaxy']) AND
        ($planetrow['system'] == $fleet['fleet_start_system']) AND
        ($planetrow['planet'] == $fleet['fleet_start_planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;

    case 3:
      $fleet['ov_time'] = $fleet['zeit'];
      $is_this_planet = (
        ($planetrow['galaxy'] == $fleet['galaxy']) AND
        ($planetrow['system'] == $fleet['system']) AND
        ($planetrow['planet'] == $fleet['planet']) AND
        ($planetrow['planet_type'] == $fleet['fleet_start_type']));
    break;
  }

  $fleet['ov_this_planet'] = $is_this_planet;

  if($fleet['fleet_owner'] == $user['id'])
  {
    $user_data = $user;
  }
  else
  {
    $user_data = doquery("SELECT * FROM `{{users}}` WHERE `id` = {$fleet['fleet_owner']};", '', true);
  };

  $fleets[] = tpl_parse_fleet_db($fleet, ++$fleet_number, $user_data);
}

function int_planet_pretemplate(&$template)
{
  global $planetrow, $lang, $sn_data;

  $governor_id = $planetrow['governor'];

  $template->assign_vars(array(
    'PLANET_ID'          => $planetrow['id'],
    'PLANET_NAME'        => $planetrow['name'],
    'PLANET_GALAXY'      => $planetrow['galaxy'],
    'PLANET_SYSTEM'      => $planetrow['system'],
    'PLANET_PLANET'      => $planetrow['planet'],
    'PLANET_TYPE'        => $planetrow['planet_type'],
    'PLANET_TYPE_TEXT'   => $lang['sys_planet_type'][$planetrow['planet_type']],

    'GOVERNOR_ID'        => $governor_id,
    'GOVERNOR_NAME'      => $lang['tech'][$governor_id],
    'GOVERNOR_LEVEL'     => $planetrow['governor_level'],
    'GOVERNOR_LEVEL_MAX' => $sn_data[$governor_id]['max'],
  ));
}

?>
