<?php

function eco_que_process($user, &$planet, $time_left)
{
  global $lang;
  $sn_data = &$GLOBALS['sn_data'];

  $quest_list = qst_get_quests($user['id']);
  $quest_triggers = qst_active_triggers($quest_list);
  $quest_rewards = array();

  $que = array();
  $built = array();
  $xp = array();
  $que_amounts = array();
  $in_que = array();
  $in_que_abs = array();
  $query_string = '';
  $query = '';

  if($planet['que'])
  {
    $que_types = $sn_data['groups']['ques'];
    foreach($que_types as $que_type_id => &$que_type_data)
    {
      $que_type_data['time_left'] = $time_left;
      $que_type_data['unit_place'] = 0;
      $que_type_data['que_changed'] = false;
    }
    $que_types[QUE_STRUCTURES]['unit_list'] = $sn_data['groups']['build_allow'][$planet['planet_type']];

    $que_strings = explode(';', $planet['que']);
    foreach($que_strings as $que_item_string)
    { //start processing $que_strings

      // skipping empty que lines
      if(!$que_item_string)
      {
        continue;
      }
//pdump($que_item_string);
      $que_item = explode(',', $que_item_string);

      // Skipping invalid negative values for unit_amount
      if((int)$que_item[1] < 1)
      {
        continue;
      }

      $unit_id = $que_item[0];
      $que_item = array(
        'ID'     => $que_item[0], // unit ID
        'AMOUNT' => $que_item[1] > 0 ? $que_item[1] : 0, // unit amount
        'TIME'   => $que_item[2] > 0 ? $que_item[2] : 0, // build time left (in seconds)
        'TIME_FULL'   => $que_item[2] > 0 ? $que_item[2] : 0, // build time left (in seconds)
        'MODE'   => $que_item[3], // build/destroy
        'QUE'    => $que_item[4], // que ID
        'NAME'   => $lang['tech'][$unit_id],
        'STRING' => "{$que_item_string};",
      );

      $que_id         = $que_item['QUE'];
      $que_data       = &$que_types[$que_id];
      if(!in_array($unit_id, $que_data['unit_list']))
      {
        // Unit is in wrong que. It can't happens in normal circuimctances - hacked?
        // We will not proceed such units
        continue;
      }
      $que_unit_place = &$que_data['unit_place'];
      $time_left = &$que_data['time_left'];

      $unit_db_name = $sn_data[$unit_id]['name'];

      $build_mode = $que_item['MODE'] == BUILD_CREATE ? 1 : -1;
      $amount_change = $build_mode * $que_item['AMOUNT'];

      $unit_level = ($planet[$unit_db_name] ? $planet[$unit_db_name] : 0) + $in_que[$unit_id];
      $build_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);
      $build_data_time = $build_data[RES_TIME][$que_item['MODE']];
      if($que_unit_place)
      {
        $que_item['TIME'] = $build_data_time;
      }
      $que_item['TIME_FULL'] = $build_data_time;

      $build_data = $build_data[$que_item['MODE']];
      $que_unit_place++;

      if($time_left > 0)
      {  // begin processing que with time left on it
        $build_time = max(0, $que_item['TIME']);
        $amount_to_build = min($que_item['AMOUNT'], floor($time_left / $build_time));

        if($amount_to_build > 0)
        {

          // This Is Building!
          // Do not 'optimize' by deleting this IF! It will be need later
          if($que_id == QUE_STRUCTURES)
          {
            $que_item['AMOUNT'] -= $amount_to_build;

            $time_left -= max(0, min($time_left, $amount_to_build * $build_time)); // prevents negative times and cycling
            $amount_to_build *= $build_mode;
            $built[$unit_id] += $amount_to_build;

            $xp_incoming = 0;
            foreach($sn_data['groups']['resources_loot'] as $resource_id)
            {
              $xp_incoming += $build_data[$resource_id] * $amount_to_build;
            }

            $xp[RPG_STRUCTURE] += round(($xp_incoming > 0 ? $xp_incoming : 0)/1000);
            $planet[$unit_db_name] += $amount_to_build;
            $query .= "`{$unit_db_name}` = `{$unit_db_name}` + '{$amount_to_build}',";
            $que_type_data['que_changed'] = true;

            // TODO: Check mutiply condition quests
            $quest_trigger_list = array_keys($quest_triggers, $unit_id);
            foreach($quest_trigger_list as $quest_id)
            {
              if($quest_list[$quest_id]['quest_unit_amount'] <= $planet[$unit_db_name] && $quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE)
              {
                $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
                $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
              }
            }
          }

        }

        if($que_item['AMOUNT'] > 0)
        {
          $que_item['TIME'] = $time_left > $que_item['TIME'] ? 0 : $que_item['TIME'] - $time_left; // Prevents negative time left
          $que_item['STRING'] = "{$unit_id},{$que_item['AMOUNT']},{$que_item['TIME']},{$que_item['MODE']},{$que_item['QUE']};";

          $time_left = 0;
        }
      }  // end processing que with time left on it
      else
      {
        $time_left = 0;
      }

      if($que_item['AMOUNT'] > 0)
      {
        $query_string .= $que_item['STRING'];
        $que_amounts[$que_id] += $amount_change;
        $in_que[$unit_id] += $amount_change;
        $in_que_abs[$unit_id] += $que_item['AMOUNT'];

        $que_item['LEVEL']  = ($planet[$unit_db_name] ? $planet[$unit_db_name] : 0) + $in_que[$unit_id] + $built[$unit_id];
        $que_item['CHANGE'] = $in_que[$unit_id] + $built[$unit_id];

        $que[$que_id][] = $que_item;
      }

    } // end processing $que_strings
  } // end if($planet['que'])

  $planet['que'] = $query_string;
  $query .= "`que` = '{$query_string}'";

  return array(
    'que'     => $que,
    'built'   => $built,
    'xp'      => $xp,
    'amounts' => $que_amounts,
    'in_que'  => $in_que,
    'in_que_abs' => $in_que_abs,
    'string'  => $query_string,
    'query'   => $query,
    'rewards' => $quest_rewards,
    'quests'  => $quest_list,
    'processed' => true
  );
}

function eco_que_add($user, &$planet, $que, $que_id, $unit_id, $unit_amount = 1, $build_mode = BUILD_CREATE)
{
  global $lang, $time_now, $sn_data;

  $que_types = $sn_data['groups']['ques'];
  $que_types[QUE_STRUCTURES]['unit_list'] = $sn_data['groups']['build_allow'][$planet['planet_type']];

  $que_data  = &$que_types[$que_id];

  // We do not work with negaitve unit_amounts - hack or cheat
  if(
    $unit_amount < 1
    || !in_array($unit_id, $que_data['unit_list'])
    || count($que['que'][$que_id]) >= $que_data['length']
  )
  {
    return $que;
  }

  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = {$planet['id']} LIMIT 1 FOR UPDATE;", '', true);
  if(
    eco_can_build_unit($user, $planet, $unit_id) != BUILD_ALLOWED
    || eco_unit_busy($user, $planet, $que, $unit_id)
    || (
        $que_id == QUE_STRUCTURES
        && (
             ($build_mode == BUILD_CREATE && max(0, eco_planet_fields_max($planet) - $planet['field_current'] - $que['amounts'][$que_id]) <= 0)
             ||
             ($build_mode == BUILD_DESTROY && $planet['field_current'] <= $que['amounts'][$que_id])
           )
       )
  )
  {
    doquery('ROLLBACK;');
    return $que;
  }

  if($que === false)
  {
    $que = array();
  }

  $build_mode = $build_mode == BUILD_CREATE ? 1 : -1;

  $unit_db_name = $sn_data[$unit_id]['name'];

  $unit_level = ($planet[$unit_db_name] ? $planet[$unit_db_name] : 0) + $que['in_que'][$unit_id];
  $build_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);

  $unit_level += $build_mode * $unit_amount;
  if($build_data['CAN'][$build_mode] >= $unit_amount && $unit_level >= 0)
  {
    $unit_time       = $build_data[RES_TIME][$build_mode];
    $que_item_string = "{$unit_id},{$unit_amount},{$unit_time},{$build_mode},{$que_id};";

    $que['que'][$que_id][] = array(
        'ID'        => $unit_id, // unit ID
        'AMOUNT'    => $unit_amount, // unit amount
        'TIME'      => $unit_time, // build time left (in seconds)
        'TIME_FULL' => $unit_time, // build time full (in seconds)
        'MODE'      => $build_mode, // build/destroy
        'NAME'      => $lang['tech'][$unit_id],
        'QUE'       => $que_id, // que ID
        'STRING'    => $que_item_string,
        'LEVEL'     => $unit_level
    );
    $que['amounts'][$que_id] += $unit_amount * $build_mode;
    $que['in_que'][$unit_id] += $unit_amount * $build_mode;
    $que['in_que_abs'][$unit_id] += $unit_amount;
    $que['string'] .= $que_item_string;
    $que['query'] = "`que` = '{$que['string']}'";

    $planet['que'] = $que['string'];
    foreach($sn_data['groups']['resources_loot'] as $resource_id)
    {
      $resource_db_name = $sn_data[$resource_id]['name'];
      $resource_change = $build_data[$build_mode][$resource_id] * $unit_amount;
      $planet[$resource_db_name] -= $resource_change;
      $que['query'] = "`$resource_db_name` = `$resource_db_name` - '{$resource_change}',{$que['query']}";
    }
    doquery("UPDATE {{planets}} SET {$que['query']} WHERE `id` = '{$planet['id']}' LIMIT 1;");
  }

  doquery('COMMIT');

  return $que;
}

function eco_que_clear($user, &$planet, $que, $que_id, $only_one = false)
{
  global $sn_data;

  $que_string = '';
  $que_query = '';

  doquery('START TRANSACTION;');
  $planet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = {$planet['id']} LIMIT 1 FOR UPDATE;", '', true);
  foreach($que['que'] as $que_data_id => &$que_data)
  {
    // TODO: MAY BE NOT ALL QUES CAN BE CLEARED - ADD CHECK FOR CLEAREBILITY!
    if($que_data_id == $que_id && count($que_data))
    {
      $resource_change = array();
      for($i = ($only_one ? 0 : count($que_data) - 1); $i >= 0; $i--)
      {
        $que_item = $que_data[count($que_data) - 1];

        $unit_id = $que_item['ID'];
        $build_mode = $que_item['MODE'];
        $unit_amount = $que_item['AMOUNT'];

        $build_data = eco_get_build_data($user, $planet, $unit_id, $que_item['LEVEL'] - $build_mode);
        foreach($sn_data['groups']['resources_loot'] as $resource_id)
        {
          $resource_change[$resource_id] += $build_data[$build_mode][$resource_id] * $unit_amount;
        }

        $que['amounts'][$que_id] -= $build_mode * $unit_amount;
        $que['in_que'][$unit_id] -= $build_mode * $unit_amount;
        $que['in_que_abs'][$unit_id] -= $unit_amount;

        unset($que_data[count($que_data) - 1]);
      }

      foreach($resource_change as $resource_id => $resource_amount)
      {
        $resource_db_name = $sn_data[$resource_id]['name'];
        $planet[$resource_db_name] += $resource_amount;
        $que_query .= "`$resource_db_name` = `$resource_db_name` + '{$resource_amount}', ";
      }
    }

    foreach($que_data as $que_item)
    {
      $que_string .= $que_item['STRING'];
    }
  }

  $que['query'] = "{$que_query}`que` = '{$que_string}'";
  $que['string'] = $planet['que'] = $que_string;

  doquery("UPDATE {{planets}} SET {$que['query']} WHERE `id` = '{$planet['id']}' LIMIT 1;");
  doquery('COMMIT');

  return $que;
}

function eco_bld_que_tech(&$user)
{
  if(!$user['que'])
  {
    return;
  }

  global $sn_data, $time_now, $lang;

  $time_left = max(0, $time_now - $user['onlinetime']);

  doquery('START TRANSACTION;');
  $user_row = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", true);
  $planet = array('id' => $user['id_planet']);

  $update_add = '';
  $que_item = $user['que'] ? explode(',', $user['que']) : array();
  if($user['que'] && $que_item[QI_TIME] <= $time_left)
  {
    $unit_id = $que_item[QI_UNIT_ID];
    $unit_db_name = $sn_data[$unit_id]['name'];

    $user[$unit_db_name]++;
    msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_research_from'], $lang['msg_que_research_subject'], sprintf($lang['msg_que_research_message'], $lang['tech'][$unit_id], $user[$unit_db_name]));

    // TODO: Re-enable quests for Alliances
    if(!$user['user_as_ally'] && $planet['id'])
    {
      $quest_list = qst_get_quests($user['id']);
      $quest_triggers = qst_active_triggers($quest_list);
      $quest_rewards = array();
      // TODO: Check mutiply condition quests
      $quest_trigger_list = array_keys($quest_triggers, $unit_id);
      foreach($quest_trigger_list as $quest_id)
      {
        if($quest_list[$quest_id]['quest_unit_amount'] <= $user[$unit_db_name] && $quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE)
        {
          $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
          $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
        }
      }
      qst_reward($user, $planet, $quest_rewards, $quest_list);
    }

    $update_add = "`{$unit_db_name}` = `{$unit_db_name}` + 1, ";

//    doquery("UPDATE `{{users}}` SET `{$unit_db_name}` = `{$unit_db_name}` + 1, `que` = '' WHERE `id` = '{$user['id']}' LIMIT 1;");
//    $user = doquery("SELECT * FROM {{users}} WHERE `id` = '{$user['id']}' LIMIT 1;", '', true);

    $build_data = eco_get_build_data($user, $planet, $unit_id, $user[$unit_db_name] - 1);
    $build_data = $build_data[BUILD_CREATE];
    $xp_incoming = 0;
    foreach($sn_data['groups']['resources_loot'] as $resource_id)
    {
      $xp_incoming += $build_data[$resource_id];
    }
    rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);

    $que_item = array();
  }
  else
  {
    $que_item[QI_TIME] -= $time_left;
  }

  $user['que'] = implode(',', $que_item);
  doquery("UPDATE `{{users}}` SET {$update_add}`que` = '{$user['que']}' WHERE `id` = '{$user['id']}' LIMIT 1;");
  doquery('COMMIT');
}

// History revision
// 1.0 - mise en forme modularisation version initiale
// 1.1 - Correction retour de fonction (retourne un tableau a la place d'un flag)

/**
 * eco_bld_handle_que.php
 * Handles building in hangar
 *
 * @oldname HandleElementBuildingQueue.php
 * @package economic
 * @version 2
 *
 * Revision History
 * ================
 *    2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *      [!] Full rewrite
 *      [%] Fixed stupid bug that allows to build several fast-build
 *          units utilizing build-time of slow-build units upper in que
 *      [~] Some optimizations and speedups
 *      [~] Complies with PCG1
 *
 *    1 - copyright 2008 By Chlorel for XNova
 */

function eco_bld_que_hangar($user, &$planet, $production_time)
{
  global $sn_data;

  $quest_rewards = array();
  if ($planet['b_hangar_id'] != 0)
  {
    $hangar_time = $planet['b_hangar'] + $production_time;
    $que = explode(';', $planet['b_hangar_id']);

    $quest_list = qst_get_quests($user['id']);
    $quest_triggers = qst_active_triggers($quest_list);

    $built = array();
    $new_hangar = '';
    $skip_rest = false;
    foreach ($que as $que_string)
    {
      if ($que_string)
      {
        $que_data = explode(',', $que_string);

        $unit_id  = $que_data[0];
        $count = $que_data[1];
        $build_data = eco_get_build_data($user, $planet, $unit_id);
        $build_time = $build_data[RES_TIME][BUILD_CREATE];
//        $build_time = GetBuildingTime($user, $planet, $unit_id);

        if(!$skip_rest)
        {
          $unit_db_name = $sn_data[$unit_id]['name'];

          $planet_unit = $planet[$unit_db_name];
          while ($hangar_time >= $build_time && $count > 0)
          {
            $hangar_time -= $build_time;
            $count--;
            $built[$unit_id]++;
            $planet_unit++;
          }
          $planet[$unit_db_name] = $planet_unit;

          // TODO: Check mutiply condition quests
          $quest_trigger_list = array_keys($quest_triggers, $unit_id);
          foreach($quest_trigger_list as $quest_id)
          {
            if($quest_list[$quest_id]['quest_unit_amount'] <= $planet[$unit_db_name] && $quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE)
            {
              $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
              $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
            }
          }

          if($count)
          {
            $skip_rest = true;
          }
        }
        if($count > 0)
        {
          $new_hangar .= "{$unit_id},{$count};";
        }
      }
    }
    if(!$new_hangar)
    {
      $hangar_time = 0;
    }
    $planet['b_hangar']    = $hangar_time;
    $planet['b_hangar_id'] = $new_hangar;
  } else {
    $built = '';
    $planet['b_hangar'] = 0;
  }

  return array(
    'built' => $built,
    'rewards' => $quest_rewards,
  );
}

?>
