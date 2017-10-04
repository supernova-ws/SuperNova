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

//define('SN_RENDER_NAVBAR_PLANET', false);

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('overview');

$result = array();

switch ($mode = sys_get_param_str('mode')) {
  case 'manage':
    sn_sys_sector_buy('overview.php?mode=manage');

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $result[] = sn_sys_planet_core_transmute($user, $planetrow);

    $template = gettemplate('planet_manage', true);
    $planet_id = sys_get_param_id('planet_id');

    if (sys_get_param_str('rename') && $new_name = sys_get_param_str('new_name')) {
      $planetrow['name'] = $new_name;
//      $new_name = db_escape($new_name);
      DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`name` = '{$new_name}'");
    } elseif (sys_get_param_str('action') == 'make_capital') {
      try {
        sn_db_transaction_start();
        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
//        $global_data = sys_o_get_updated($user, $planetrow['id'], SN_TIME_NOW);
//        $user = $global_data['user'];
//        $planetrow = $global_data['planet'];

        if ($planetrow['planet_type'] != PT_PLANET) {
          throw new exception(classSupernova::$lang['ov_capital_err_not_a_planet'], ERR_ERROR);
        }

        if ($planetrow['id'] == $user['id_planet']) {
          throw new exception(classSupernova::$lang['ov_capital_err_capital_already'], ERR_ERROR);
        }

        if ($user_dark_matter < classSupernova::$config->planet_capital_cost) {
          throw new exception(classSupernova::$lang['ov_capital_err_no_dark_matter'], ERR_ERROR);
        }

        rpg_points_change($user['id'], RPG_CAPITAL, -classSupernova::$config->planet_capital_cost,
          array('Planet %s ID %d at coordinates %s now become Empire Capital', $planetrow['name'], $planetrow['id'], uni_render_coordinates($planetrow))
        );

        db_user_set_by_id($user['id'], "id_planet = {$planetrow['id']}, galaxy = {$planetrow['galaxy']}, system = {$planetrow['system']}, planet = {$planetrow['planet']}");

        $user['id_planet'] = $planetrow['id'];
        $result[] = array(
          'STATUS'  => ERR_NONE,
          'MESSAGE' => classSupernova::$lang['ov_capital_err_none'],
        );
        sn_db_transaction_commit();
        sys_redirect('overview.php?mode=manage');
      } catch (exception $e) {
        sn_db_transaction_rollback();
        $result[] = array(
          'STATUS'  => $e->getCode(),
          'MESSAGE' => $e->getMessage(),
        );
      }
    } elseif (sys_get_param_str('action') == 'planet_teleport') {
      try {
        if (!uni_coordinates_valid($new_coordinates = array(
          'galaxy' => sys_get_param_int('new_galaxy'),
          'system' => sys_get_param_int('new_system'),
          'planet' => sys_get_param_int('new_planet')))
        ) {
          throw new exception(classSupernova::$lang['ov_teleport_err_wrong_coordinates'], ERR_ERROR);
        }

        sn_db_transaction_start();
        // При телепорте обновлять данные не надо - просто получить текущие данные и залочить их
        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
//        $global_data = sys_o_get_updated($user, $planetrow['id'], SN_TIME_NOW);
//        $user = $global_data['user'];
//        $planetrow = $global_data['planet'];

        $can_teleport = uni_planet_teleport_check($user, $planetrow, $new_coordinates);
        if ($can_teleport['result'] != ERR_NONE) {
          throw new exception($can_teleport['message'], $can_teleport['result']);
        }

        rpg_points_change($user['id'], RPG_TELEPORT, -classSupernova::$config->planet_teleport_cost,
          array(&classSupernova::$lang['ov_teleport_log_record'], $planetrow['name'], $planetrow['id'], uni_render_coordinates($planetrow), uni_render_coordinates($new_coordinates))
        );
        $planet_teleport_next = SN_TIME_NOW + classSupernova::$config->planet_teleport_timeout;
        DBStaticPlanet::db_planet_set_by_gspt($planetrow['galaxy'], $planetrow['system'], $planetrow['planet'], PT_ALL,
          "galaxy = {$new_coordinates['galaxy']}, system = {$new_coordinates['system']}, planet = {$new_coordinates['planet']}, planet_teleport_next = {$planet_teleport_next}");

        if ($planetrow['id'] == $user['id_planet']) {
          db_user_set_by_id($user['id'], "galaxy = {$new_coordinates['galaxy']}, system = {$new_coordinates['system']}, planet = {$new_coordinates['planet']}");
        }

        // $global_data = sys_o_get_updated($user, $planetrow['id'], SN_TIME_NOW);
        sn_db_transaction_commit();
        $user = db_user_by_id($user['id'], true, '*');
        $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
        $result[] = array(
          'STATUS'  => ERR_NONE,
          'MESSAGE' => classSupernova::$lang['ov_teleport_err_none'],
        );
        sys_redirect('overview.php?mode=manage');
      } catch (exception $e) {
        sn_db_transaction_rollback();
        $result[] = array(
          'STATUS'  => $e->getCode(),
          'MESSAGE' => $e->getMessage(),
        );
      }
    } elseif (sys_get_param_str('action') == 'planet_abandon') {
      // if(sec_password_check($user['id'], sys_get_param('abandon_confirm'))) {
      if (classSupernova::$auth->password_check(sys_get_param('abandon_confirm'))) {
        if ($user['id_planet'] != $user['current_planet'] && $user['current_planet'] == $planet_id) {
          $destroyed = SN_TIME_NOW + 60 * 60 * 24;
          DBStaticPlanet::db_planet_set_by_id($user['current_planet'], "`destruyed`='{$destroyed}', `id_owner`=0");
          DBStaticPlanet::db_planet_set_by_parent($user['current_planet'], "`destruyed`='{$destroyed}', `id_owner`=0");
          db_user_set_by_id($user['id'], '`current_planet` = `id_planet`');
          messageBox(classSupernova::$lang['ov_delete_ok'], classSupernova::$lang['colony_abandon'], 'overview.php?mode=manage');
        } else {
          messageBox(classSupernova::$lang['ov_delete_wrong_planet'], classSupernova::$lang['colony_abandon'], 'overview.php?mode=manage');
        }
      } else {
        messageBox(classSupernova::$lang['ov_delete_wrong_pass'], classSupernova::$lang['colony_abandon'], 'overview.php?mode=manage');
      }
    } elseif (
      ($hire = sys_get_param_int('hire')) && in_array($hire, sn_get_groups('governors'))
      && (
        !get_unit_param($hire, P_MAX_STACK) ||
        ($planetrow['PLANET_GOVERNOR_ID'] != $hire) ||
        (
          $planetrow['PLANET_GOVERNOR_ID'] == $hire &&
          $planetrow['PLANET_GOVERNOR_LEVEL'] < get_unit_param($hire, P_MAX_STACK)
        )
      )
    ) {
      sn_db_transaction_start();
      $user = db_user_by_id($user['id'], true);
      $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true);
      $build_data = eco_get_build_data($user, $planetrow, $hire, $planetrow['PLANET_GOVERNOR_ID'] == $hire ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0);
      if ($build_data['CAN'][BUILD_CREATE]) {
        if ($planetrow['PLANET_GOVERNOR_ID'] == $hire) {
          $planetrow['PLANET_GOVERNOR_LEVEL']++;
          $query = '`PLANET_GOVERNOR_LEVEL` + 1';
        } else {
          $planetrow['PLANET_GOVERNOR_LEVEL'] = 1;
          $planetrow['PLANET_GOVERNOR_ID'] = $hire;
          $query = '1';
        }
        DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`PLANET_GOVERNOR_ID` = {$hire}, `PLANET_GOVERNOR_LEVEL` = {$query}");
        rpg_points_change(
          $user['id'],
          RPG_GOVERNOR,
          -$build_data[BUILD_CREATE][RES_DARK_MATTER],
          sprintf(classSupernova::$lang['ov_governor_purchase'], classSupernova::$lang['tech'][$hire], $hire, $planetrow['PLANET_GOVERNOR_LEVEL'], uni_render_planet_full($planetrow, '', false, true))
        );

        //  => 'Игрок купил Губернатора %1$s ID %2$d уровня %3$d на планету %4$s',
        // die();
      }
      sn_db_transaction_commit();
      sys_redirect('overview.php?mode=manage');
      die();
    }

    lng_include('mrc_mercenary');
    int_planet_pretemplate($planetrow, $template);
    foreach (sn_get_groups('governors') as $governor_id) {
      if ($planetrow['planet_type'] == PT_MOON && $governor_id == MRC_TECHNOLOGIST) {
        continue;
      }

      $governor_level = $planetrow['PLANET_GOVERNOR_ID'] == $governor_id ? $planetrow['PLANET_GOVERNOR_LEVEL'] : 0;
      $build_data = eco_get_build_data($user, $planetrow, $governor_id, $governor_level);
      $template->assign_block_vars('governors', array(
        'ID'         => $governor_id,
        'NAME'       => classSupernova::$lang['tech'][$governor_id],
        'COST'       => $build_data[BUILD_CREATE][RES_DARK_MATTER],
        'MAX'        => get_unit_param($governor_id, P_MAX_STACK),
        'LEVEL'      => $governor_level,
        'LEVEL_PLUS' => mrc_get_level($user, $planetrow, $governor_id) - $governor_level,
      ));
    }

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $planet_density_index = $planetrow['density_index'];
    $density_price_chart = planet_density_price_chart($planetrow);
    tpl_planet_density_info($template, $density_price_chart, $user_dark_matter);

    $sector_cost = eco_get_build_data($user, $planetrow, UNIT_SECTOR, mrc_get_level($user, $planetrow, UNIT_SECTOR), true);
    $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
    $planet_fill = floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100);
    $planet_fill = $planet_fill > 100 ? 100 : $planet_fill;
    $can_teleport = uni_planet_teleport_check($user, $planetrow);
    $template->assign_vars(array(
      'DARK_MATTER' => $user_dark_matter,

      'PLANET_FILL'          => floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100),
      'PLANET_FILL_BAR'      => $planet_fill,
      'SECTOR_CAN_BUY'       => $sector_cost <= $user_dark_matter,
      'SECTOR_COST'          => $sector_cost,
      'SECTOR_COST_TEXT'     => HelperString::numberFloorAndFormat($sector_cost),
      'planet_field_current' => $planetrow['field_current'],
      'planet_field_max'     => eco_planet_fields_max($planetrow),

      'CAN_TELEPORT'         => $can_teleport['result'] == ERR_NONE,
      'CAN_NOT_TELEPORT_MSG' => $can_teleport['message'],
      'TELEPORT_COST_TEXT'   => prettyNumberStyledCompare(classSupernova::$config->planet_teleport_cost, $user_dark_matter),

      'CAN_CAPITAL'       => $user_dark_matter >= classSupernova::$config->planet_capital_cost,
      'CAPITAL_COST_TEXT' => prettyNumberStyledCompare(classSupernova::$config->planet_capital_cost, $user_dark_matter),

      'PLANET_DENSITY_INDEX' => $planet_density_index,
      'PLANET_CORE_TEXT'     => classSupernova::$lang['uni_planet_density_types'][$planet_density_index],

      'IS_CAPITAL' => $planetrow['id'] == $user['id_planet'],

      'PAGE_HINT' => classSupernova::$lang['ov_manage_page_hint'],
    ));

    foreach ($result as &$a_result) {
      $template->assign_block_vars('result', $a_result);
    }

    display($template, classSupernova::$lang['rename_and_abandon_planet']);
  break;

  default:
    sn_sys_sector_buy();

    if (sys_get_param_str('rename') && $new_name = sys_get_param_str('new_name')) {
      $planetrow['name'] = $new_name;
      $new_name_safe = db_escape($new_name);
      DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`name` = '{$new_name_safe}'");
    }

    $result[] = sn_sys_planet_core_transmute($user, $planetrow);

    $template = gettemplate('planet_overview', true);

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $planet_density_index = $planetrow['density_index'];
    $density_price_chart = planet_density_price_chart($planetrow);
    tpl_planet_density_info($template, $density_price_chart, $user_dark_matter);

    rpg_level_up($user, RPG_STRUCTURE);
    rpg_level_up($user, RPG_RAID);
    rpg_level_up($user, RPG_TECH);
    rpg_level_up($user, RPG_EXPLORE);

    $fleet_id = 1;

    $fleets = flt_parse_fleets_to_events(fleet_and_missiles_list_incoming($user['id']));

    $planet_count = 0;
    $planets_query = DBStaticPlanet::db_planet_list_sorted($user, false, '*');
    foreach ($planets_query as $an_id => $planetRecord) {
      sn_db_transaction_start();
      $updatedData = sys_o_get_updated($user, $planetRecord['id'], SN_TIME_NOW, false, true);
      sn_db_transaction_commit();

      $templatizedPlanet = tpl_parse_planet($updatedData['planet'], $fleets_to_planet);

      if ($planetRecord['planet_type'] == PT_MOON) {
        continue;
      }
      $moon = DBStaticPlanet::db_planet_by_parent($planetRecord['id']);
      if ($moon) {
        $moon_fill = min(100, floor($moon['field_current'] / eco_planet_fields_max($moon) * 100));
      } else {
        $moon_fill = 0;
      }

      $moon_fleets = flt_get_fleets_to_planet($moon);
      $template->assign_block_vars('planet', array_merge($templatizedPlanet, array(
        'MOON_ID'    => $moon['id'],
        'MOON_NAME'  => $moon['name'],
        'MOON_IMG'   => $moon['image'],
        'MOON_FILL'  => min(100, $moon_fill),
        'MOON_ENEMY' => $moon_fleets['enemy']['count'],

        'MOON_PLANET' => $moon['parent_planet'],
      )));

      $planet_count++;
    }

    tpl_assign_fleet($template, $fleets_to_planet);
    tpl_assign_fleet($template, $fleets);

    $lune = $planetrow['planet_type'] == PT_PLANET ? DBStaticPlanet::db_planet_by_parent($planetrow['id']) : DBStaticPlanet::db_planet_by_id($planetrow['parent_planet']);
    if ($lune) {
      $template->assign_vars(array(
        'MOON_ID'   => $lune['id'],
        'MOON_IMG'  => $lune['image'],
        'MOON_NAME' => $lune['name'],
      ));
    }

    $planet_fill = floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100);
    $planet_fill = $planet_fill > 100 ? 100 : $planet_fill;

    $planet_recyclers_orbiting = 0;
    foreach (sn_get_groups('flt_recyclers') as $recycler_id) {
      $planet_recyclers_orbiting += mrc_get_level($user, $planetrow, $recycler_id);
    }

    int_planet_pretemplate($planetrow, $template);

    $sn_group_ques = sn_get_groups('ques');
    if (!defined('GAME_STRUCTURES_DISABLED') || !GAME_STRUCTURES_DISABLED) {
      foreach (array(QUE_STRUCTURES => $sn_group_ques[QUE_STRUCTURES]) as $que_id => $que_type_data) {
        $this_que = $que['ques'][$que_id][$user['id']][$planetrow['id']];
        $template->assign_block_vars('ques', array(
          'ID'     => $que_id,
          'NAME'   => classSupernova::$lang['sys_ques'][$que_id],
          'LENGTH' => empty($this_que) ? 0 : count($this_que),
        ));

        if (!empty($this_que)) {
          foreach ($this_que as $que_item) {
            $template->assign_block_vars('que', que_tpl_parse_element($que_item));
          }
        }
      }
    }

    $que_hangar_length = tpl_assign_hangar($template, $planetrow, SUBQUE_FLEET);
    $template->assign_block_vars('ques', array(
      'ID'     => QUE_HANGAR,
      'NAME'   => classSupernova::$lang['sys_ques'][QUE_HANGAR],
      'LENGTH' => $que_hangar_length,
    ));

    if (!defined('GAME_DEFENSE_DISABLED') || !GAME_DEFENSE_DISABLED) {
      $que_hangar_length = tpl_assign_hangar($template, $planetrow, SUBQUE_DEFENSE);
      $template->assign_block_vars('ques', array(
        'ID'     => SUBQUE_DEFENSE,
        'NAME'   => classSupernova::$lang['sys_ques'][SUBQUE_DEFENSE],
        'LENGTH' => $que_hangar_length,
      ));
    }

    $overview_planet_rows = $user['opt_int_overview_planet_rows'];
    $overview_planet_columns = $user['opt_int_overview_planet_columns'];

    if ($overview_planet_rows <= 0 && $overview_planet_columns <= 0) {
      $overview_planet_rows = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_rows'];
      $overview_planet_columns = $user_option_list[OPT_INTERFACE]['opt_int_overview_planet_columns'];
    }

    if ($overview_planet_rows > 0 && $overview_planet_columns <= 0) {
      $overview_planet_columns = ceil($planet_count / $overview_planet_rows);
    }

    $sector_cost = eco_get_build_data($user, $planetrow, UNIT_SECTOR, mrc_get_level($user, $planetrow, UNIT_SECTOR), true);
    $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
    $governor_level = $planetrow['PLANET_GOVERNOR_ID'] ? mrc_get_level($user, $planetrow, $planetrow['PLANET_GOVERNOR_ID'], false, true) : 0;
    $template->assign_vars(array(
      'USER_ID'        => $user['id'],
      'user_username'  => $user['username'],
      'USER_AUTHLEVEL' => $user['authlevel'],

      'NEW_MESSAGES'    => $user['new_message'],
      'NEW_LEVEL_MINER' => $level_miner,
      'NEW_LEVEL_RAID'  => $level_raid,

      'planet_diameter'      => HelperString::numberFloorAndFormat($planetrow['diameter']),
      'planet_field_current' => $planetrow['field_current'],
      'planet_field_max'     => eco_planet_fields_max($planetrow),
      'PLANET_FILL'          => floor($planetrow['field_current'] / eco_planet_fields_max($planetrow) * 100),
      'PLANET_FILL_BAR'      => $planet_fill,
      'metal_debris'         => HelperString::numberFloorAndFormat($planetrow['debris_metal']),
      'crystal_debris'       => HelperString::numberFloorAndFormat($planetrow['debris_crystal']),
      'PLANET_RECYCLERS'     => $planet_recyclers_orbiting,
      'planet_image'         => $planetrow['image'],
      'planet_temp_min'      => $planetrow['temp_min'],
      'planet_temp_avg'      => round(($planetrow['temp_min'] + $planetrow['temp_max']) / 2),
      'planet_temp_max'      => $planetrow['temp_max'],
      'planet_density'       => $planetrow['density'],
      'planet_density_index' => $planetrow['density_index'],
      'planet_density_text'  => classSupernova::$lang['uni_planet_density_types'][$planetrow['density_index']],

      'GATE_LEVEL'          => mrc_get_level($user, $planetrow, STRUC_MOON_GATE),
      'GATE_JUMP_REST_TIME' => uni_get_time_to_jump($planetrow),

      'ADMIN_EMAIL' => classSupernova::$config->game_adminEmail,

      'PLANET_GOVERNOR_ID'         => $planetrow['PLANET_GOVERNOR_ID'],
      'PLANET_GOVERNOR_LEVEL'      => $governor_level,
      'PLANET_GOVERNOR_LEVEL_PLUS' => mrc_get_level($user, $planetrow, $planetrow['PLANET_GOVERNOR_ID']) - $governor_level,
      'PLANET_GOVERNOR_NAME'       => classSupernova::$lang['tech'][$planetrow['PLANET_GOVERNOR_ID']],

      'LIST_ROW_COUNT'    => $overview_planet_rows,
      'LIST_COLUMN_COUNT' => $overview_planet_columns,

      'DARK_MATTER' => $user_dark_matter,

      'PLANET_DENSITY_INDEX' => $planet_density_index,
      'PLANET_CORE_TEXT'     => classSupernova::$lang['uni_planet_density_types'][$planet_density_index],

      'SECTOR_CAN_BUY'   => $sector_cost <= mrc_get_level($user, null, RES_DARK_MATTER),
      'SECTOR_COST'      => $sector_cost,
      'SECTOR_COST_TEXT' => HelperString::numberFloorAndFormat($sector_cost),

      'PAGE_HEADER' => classSupernova::$lang['ov_overview']. " - " . classSupernova::$lang['sys_planet_type'][$planetrow['planet_type']] . " {$planetrow['name']} [{$planetrow['galaxy']}:{$planetrow['system']}:{$planetrow['planet']}]",
    ));
    tpl_set_resource_info($template, $planetrow, $fleets_to_planet);

    foreach ($result as &$a_result) {
      $template->assign_block_vars('result', $a_result);
    }

    display($template);
  break;
}
