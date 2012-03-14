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

define('SN_RENDER_NAVBAR_PLANET', false);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');

$mode            = sys_get_param_str('mode');
switch($mode)
{
  case 'manage':
    $template  = gettemplate('planet_manage', true);
    $planet_id = sys_get_param_id('planet_id');
    $hire = sys_get_param_int('hire');

    if(sys_get_param_str('rename') && $new_name = sys_get_param_str('new_name'))
    {
      $planetrow['name'] = $new_name;
      $new_name = mysql_real_escape_string($new_name);
      doquery("UPDATE {{planets}} SET `name` = '{$new_name}' WHERE `id` = '{$planetrow['id']}' LIMIT 1;");
    }
    elseif(sys_get_param_str('abandon'))
    {
      $abandon_confirm = $_POST['abandon_confirm'];
      if(md5($abandon_confirm) == $user['password'])
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
    elseif(
      $hire && in_array($hire, $sn_data['groups']['governors'])
      && (
        !isset($sn_data[$hire]['max']) ||
        ($planetrow['PLANET_GOVERNOR_ID'] != $hire) ||
        (
          $planetrow['PLANET_GOVERNOR_ID'] == $hire &&
          $planetrow['PLANET_GOVERNOR_LEVEL'] < $sn_data[$hire]['max']
        )
      )
    )
    {
      doquery('START TRANSACTION;');
      $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", '', true);
      $build_data = eco_get_build_data($user, $planetrow, $hire, $planetrow['PLANET_GOVERNOR_ID'] == $hire ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0);
      if($build_data['CAN'][BUILD_CREATE])
      {
        if($planetrow['PLANET_GOVERNOR_ID'] == $hire)
        {
          $planetrow['PLANET_GOVERNOR_LEVEL']++;
          $query = '`PLANET_GOVERNOR_LEVEL` + 1';
        }
        else
        {
          $planetrow['PLANET_GOVERNOR_LEVEL'] = 1;
          $planetrow['PLANET_GOVERNOR_ID'] = $hire;
          $query = '1';
        }
        doquery("UPDATE {{planets}} SET `PLANET_GOVERNOR_ID` = {$hire}, `PLANET_GOVERNOR_LEVEL` = {$query} WHERE `id` = {$planetrow['id']} LIMIT 1;");
        rpg_points_change($user['id'], RPG_MERCENARY, -$build_data[BUILD_CREATE][RES_DARK_MATTER]);
      }
      doquery('COMMIT;');
      header("Location: overview.php?mode=manage");
      ob_end_flush();
      die();
    }

    lng_include('mrc_mercenary');
    int_planet_pretemplate($planetrow, $template);
    foreach($sn_data['groups']['governors'] as $governor_id)
    {
      if($planetrow['planet_type'] == PT_MOON && $governor_id == MRC_TECHNOLOGIST)
      {
        continue;
      }

      $governor_level = $planetrow['PLANET_GOVERNOR_ID'] == $governor_id ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0;
      $build_data = eco_get_build_data($user, $planetrow, $governor_id, $governor_level);
      $template->assign_block_vars('governors', array(
        'ID'   => $governor_id,
        'NAME' => $lang['tech'][$governor_id],
        'COST' => $build_data[BUILD_CREATE][RES_DARK_MATTER],
        'MAX'  => $sn_data[$governor_id]['max'],
        'LEVEL' => $governor_level,
      ));
    }

    $template->assign_vars(array(
      'DARK_MATTER' => $user['dark_matter'],

      'PAGE_HINT'   => $lang['ov_manage_page_hint'],
    ));

    display($template, $lang['rename_and_abandon_planet']);
  break;

  default:
    $template = gettemplate('planet_overview', true);

    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);
    rpg_level_up($user, RPG_TECH);

    $fleet_id = 1;

    int_get_fleet_to_planet("SELECT DISTINCT * FROM {{fleets}} WHERE `fleet_owner` = '{$user['id']}' OR `fleet_target_owner` = '{$user['id']}';");
    int_get_missile_to_planet("SELECT * FROM `{{iraks}}` WHERE `fleet_owner` = '{$user['id']}'");

    $planet_count = 0;
    $planets_query = SortUserPlanets($user, false, '*');

    while ($UserPlanet = mysql_fetch_assoc($planets_query))
    {
      $UserPlanet      = sys_o_get_updated($user, $UserPlanet, $time_now, true);
      $list_planet_que = $UserPlanet['que'];
      $UserPlanet      = $UserPlanet['planet'];

      $template_planet = tpl_parse_planet($UserPlanet, $list_planet_que);

      $planet_fleet_id = 0;
      $fleet_list = $template_planet['fleet_list'];
      if($fleet_list['own']['count'])
      {
        $planet_fleet_id = "p{$UserPlanet['id']}";
        $fleets_to_planet[$UserPlanet['id']] = tpl_parse_fleet_sn($fleet_list['own']['total'], $planet_fleet_id);
//        $fleet_id++;
      }
      if($UserPlanet['planet_type'] == PT_MOON)
      {
        continue;
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

      $planet_count++;
    }

    tpl_assign_fleet($template, $fleets_to_planet);
    tpl_assign_fleet($template, $fleets);

    $parse = $lang;

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
    // Last chat message
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

    int_planet_pretemplate($planetrow, $template);

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

    $que_hangar_length = tpl_assign_hangar(QUE_HANGAR, $planetrow, $template);

    $template->assign_block_vars('ques', array(
      ID     => QUE_HANGAR,
      NAME   => $lang['sys_ques'][QUE_HANGAR],
      LENGTH => $que_hangar_length,
    ));

    $tech_que_item = explode(',', $user['que']);
    $unit_id = intval($tech_que_item[QI_UNIT_ID]);
    $time_rest = $tech_que_item[QI_TIME];
    $time_rest = $time_rest >= 0 ? $time_rest : 0;
    $template->assign_block_vars('ques', array(
      ID     => QUE_RESEARCH,
      NAME   => $lang['sys_ques'][QUE_RESEARCH],
      LENGTH => $unit_id && $time_rest ? 1 : 0,
    ));
    if($unit_id && $time_rest)
    {
      $template->assign_block_vars('que', array(
        'ID' => $unit_id,
        'QUE' => QUE_RESEARCH,
        'NAME' => $lang['tech'][$unit_id],
        'TIME' => $time_rest,
        'TIME_FULL' => $time_rest,
        'AMOUNT' => 1,
        'LEVEL' => $user[$sn_data[$unit_id]['name']] + 1,
      ));
    }


    $overview_planet_rows = $user['opt_int_overview_planet_rows'];
    $overview_planet_columns = $user['opt_int_overview_planet_columns'];

    if($overview_planet_rows <= 0 && $overview_planet_columns <= 0)
    {
      $overview_planet_rows = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_rows'];
      $overview_planet_columns = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_columns'];
    }

    if($overview_planet_rows > 0 && $overview_planet_columns <= 0)
    {
      $overview_planet_columns = ceil($planet_count / $overview_planet_rows);
    }

    $template->assign_vars(array(
      'TIME_NOW'              => $time_now,

      'USER_ID'               => $user['id'],
      'user_username'         => $user['username'],
      'USER_AUTHLEVEL'        => $user['authlevel'],

      'NEW_MESSAGES'          => $user['new_message'],
      'NEW_LEVEL_MINER'       => $level_miner,
      'NEW_LEVEL_RAID'        => $level_raid,

      'planet_diameter'       => pretty_number($planetrow['diameter']),
      'planet_field_current'  => $planetrow['field_current'],
      'planet_field_max'      => eco_planet_fields_max($planetrow),
      'PLANET_FILL'           => floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100),
      'PLANET_FILL_BAR'       => $planet_fill,
      'metal_debris'          => pretty_number($planetrow['debris_metal']),
      'crystal_debris'        => pretty_number($planetrow['debris_crystal']),
      'RECYCLERS_SEND'        => $recyclers_send,
      'planet_temp_min'       => $planetrow['temp_min'],
      'planet_temp_max'       => $planetrow['temp_max'],

      'GATE_LEVEL'            => $planetrow[$sn_data[STRUC_MOON_GATE]['name']],
      'GATE_JUMP_REST_TIME'   => uni_get_time_to_jump($planetrow),

      'ADMIN_EMAIL'           => $config->game_adminEmail,

      'PLANET_GOVERNOR_ID'    => $planetrow['PLANET_GOVERNOR_ID'],
      'PLANET_GOVERNOR_LEVEL' => $planetrow['PLANET_GOVERNOR_LEVEL'],
      'PLANET_GOVERNOR_NAME'  => $lang['tech'][$planetrow['PLANET_GOVERNOR_ID']],

      'LIST_ROW_COUNT'        => $overview_planet_rows,
      'LIST_COLUMN_COUNT'     => $overview_planet_columns,

      //'LastChat'       => CHT_messageParse($msg),
    ));
    tpl_set_resource_info($template, $planetrow, $fleets_to_planet, 2);
    nws_render($template, "WHERE UNIX_TIMESTAMP(`tsTimeStamp`) >= {$user['news_lastread']}", $config->game_news_overview); //  AND UNIX_TIMESTAMP(`tsTimeStamp`) + {$config->game_news_actual} >= {$time_now}

    display(parsetemplate($template, $parse), "{$lang['ov_overview']} - {$lang['sys_planet_type'][$planetrow['planet_type']]} {$planetrow['name']} [{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}]");
  break;
}

?>
