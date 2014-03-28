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

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('fleet');

if($TargetPlanet = sys_get_param_id('jmpto'))
{
  doquery('START TRANSACTION');
  $planetrow = doquery("SELECT * FROM {{planets}} WHERE id = {$planetrow['id']} LIMIT 1 FOR UPDATE;", true);
  if(!($NextJumpTime = uni_get_time_to_jump($planetrow)))
  {
    $TargetGate = doquery ( "SELECT `id`, `sprungtor`, `last_jump_time` FROM {{planets}} WHERE `id` = '{$TargetPlanet}'  LIMIT 1 FOR UPDATE;", true);
    if($TargetGate['sprungtor'] > 0)
    {
      $NextDestTime = uni_get_time_to_jump ( $TargetGate );
      if(!$NextDestTime)
      {
        $SubQueryOri = "";
        $SubQueryDes = "";
        $ship_list = sys_get_param('ships');
        foreach($ship_list as $ship_id => $ship_count)
        {
          if(!in_array($ship_id, sn_get_groups('fleet')))
          {
            continue;
          }

          $ship_count = max(0, min(floor($ship_count), mrc_get_level($user, $planetrow, $ship_id)));
          if($ship_count)
          {
            $ship_db_name = get_unit_param($ship_id, P_NAME);
            $SubQueryOri .= "`{$ship_db_name}` = `{$ship_db_name}` - '{$ship_count}', ";
            $SubQueryDes .= "`{$ship_db_name}` = `{$ship_db_name}` + '{$ship_count}', ";
          }
        }
        // Dit monsieur, y avait quelque chose a envoyer ???
        if($SubQueryOri)
        {
          doquery("UPDATE {{planets}} SET {$SubQueryOri} `last_jump_time` = '{$time_now}' WHERE `id` = '{$planetrow['id']}' LIMIT 1;");
          doquery("UPDATE {{planets}} SET {$SubQueryDes} `last_jump_time` = '{$time_now}' WHERE `id` = '{$TargetGate['id']}' LIMIT 1;");
          doquery("UPDATE {{users}} SET `current_planet` = '{$TargetGate['id']}' WHERE `id` = '{$user['id']}' LIMIT 1;");

          $planetrow['last_jump_time'] = $time_now;
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
  doquery('COMMIT;');
  message($RetMessage, $lang['tech'][STRUC_MOON_GATE], "jumpgate.php", 10);
} else {
  $template = gettemplate('jumpgate', true);
  if(mrc_get_level($user, $planetrow, STRUC_MOON_GATE) > 0)
  {
    $Combo = "";
    $MoonList = doquery("SELECT * FROM {{planets}} WHERE `planet_type` = '3' AND `id_owner` = '" . $user['id'] . "' AND `id` != '{$planetrow['id']}';");
    while($CurMoon = mysql_fetch_assoc($MoonList))
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
        'SHIP_COUNT_TEXT' => pretty_number($ship_count),
      ));
    }

    $template->assign_vars(array(
      'GATE_JUMP_REST_TIME' => uni_get_time_to_jump($planetrow),
      'gate_start_name' => $planetrow['name'],
      'gate_start_link' => uni_render_coordinates_href($planetrow, '', 3),
      'TIME_NOW' => $time_now,
    ));

    display($template, $lang['tech'][STRUC_MOON_GATE]);
  }
  else
  {
    message($lang['gate_no_src_ga'], $lang['tech'][STRUC_MOON_GATE], "overview.php", 10);
  }
}


// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version from scrap .. y avait pas ... bin maintenant y a !!

?>
