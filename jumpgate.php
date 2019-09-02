<?php

/**
 * jumpgate.php
 *
 * Jump Gate interface, I presume
 *
 * @version 1.0st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

use DBAL\OldDbChangeSet;
use Planet\DBStaticPlanet;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('fleet');

if($TargetPlanet = sys_get_param_id('jmpto'))
{
  sn_db_transaction_start();
  db_user_by_id($user['id'], true, 'id');
  $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true);
  if(!($NextJumpTime = uni_get_time_to_jump($planetrow)))
  {
    $TargetGate = DBStaticPlanet::db_planet_by_id($TargetPlanet, true, '`id`, `last_jump_time`');
    if(mrc_get_level($user, $TargetGate, STRUC_MOON_GATE) > 0)
    {
      $NextDestTime = uni_get_time_to_jump ( $TargetGate );
      if(!$NextDestTime)
      {
        // $SubQueryOri = "";
        // $SubQueryDes = "";
        $ship_list = sys_get_param('ships');
        $db_changeset = array();
        foreach($ship_list as $ship_id => $ship_count)
        {
          if(!in_array($ship_id, sn_get_groups('fleet')))
          {
            continue;
          }

          $ship_count = max(0, min(floor($ship_count), mrc_get_level($user, $planetrow, $ship_id)));
          if($ship_count)
          {
            $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($ship_id, -$ship_count, $user, $planetrow['id']);
            $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($ship_id, $ship_count, $user, $TargetGate['id']);
          }
        }
        // Dit monsieur, y avait quelque chose a envoyer ???
        if(!empty($db_changeset))
        {
          DBStaticPlanet::db_planet_set_by_id($TargetGate['id'], "`last_jump_time` = " . SN_TIME_NOW . "");
          DBStaticPlanet::db_planet_set_by_id($planetrow['id'], "`last_jump_time` = " . SN_TIME_NOW . "");
          OldDbChangeSet::db_changeset_apply($db_changeset);

          db_user_set_by_id($user['id'], "`current_planet` = '{$TargetGate['id']}'");

          $planetrow['last_jump_time'] = SN_TIME_NOW;
          $RetMessage = $lang['gate_jump_done'] ." - ". pretty_time(uni_get_time_to_jump($planetrow));
        } else {
          $RetMessage = $lang['gate_wait_data'];
        }
      } else {
        $RetMessage = $lang['gate_wait_dest'] ." - ". pretty_time($NextDestTime);
      }
    } else {
      $RetMessage = $lang['gate_no_dest_g'];
    }
  } else {
    $RetMessage = $lang['gate_wait_star'] ." - ". pretty_time($NextJumpTime);
  }
  sn_db_transaction_commit();
  SnTemplate::messageBox($RetMessage, $lang['tech'][STRUC_MOON_GATE], "jumpgate.php", 10);
} else {
  $template = SnTemplate::gettemplate('jumpgate', true);
  if(mrc_get_level($user, $planetrow, STRUC_MOON_GATE) > 0)
  {
    $Combo = '';
    $MoonList = DBStaticPlanet::db_planet_list_moon_other($user['id'], $planetrow['id']);
    // while($CurMoon = db_fetch($MoonList))
    foreach($MoonList as $CurMoon)
    {
      if(mrc_get_level($user, $CurMoon, STRUC_MOON_GATE) >= 1)
      {
        $NextJumpTime = uni_get_time_to_jump($CurMoon);
        $template->assign_block_vars('moon', array(
          'ID'             => $CurMoon['id'],
          'GALAXY'         => $CurMoon['galaxy'],
          'SYSTEM'         => $CurMoon['system'],
          'PLANET'         => $CurMoon['planet'],
          'NAME'           => $CurMoon['name'],
          'NEXT_JUMP_TIME' => $NextJumpTime ? pretty_time($NextJumpTime) : '',
        ));
      }
    }

    foreach(sn_get_groups('fleet') as $Ship)
    {
      if(($ship_count = mrc_get_level($user, $planetrow, $Ship)) <= 0)
      {
        continue;
      }

      $template->assign_block_vars('fleet', array(
        'SHIP_ID'         => $Ship,
        'SHIP_NAME'       => $lang['tech'][$Ship],
        'SHIP_COUNT'      => $ship_count,
        'SHIP_COUNT_TEXT' => HelperString::numberFloorAndFormat($ship_count),
      ));
    }

    $template->assign_vars(array(
      'GATE_JUMP_REST_TIME' => uni_get_time_to_jump($planetrow),
      'gate_start_name' => $planetrow['name'],
      'gate_start_link' => uni_render_coordinates_href($planetrow, '', 3),
    ));

    SnTemplate::display($template, $lang['tech'][STRUC_MOON_GATE]);
  }
  else
  {
    SnTemplate::messageBox($lang['gate_no_src_ga'], $lang['tech'][STRUC_MOON_GATE], "overview.php", 10);
  }
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version from scrap .. y avait pas ... bin maintenant y a !!
