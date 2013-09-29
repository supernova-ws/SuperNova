<?php

function eco_que_str2arr($que_str)
{
  $que_arr = explode(';', $que_str);
  foreach($que_arr as $que_index => &$que_item)
  {
    if($que_item)
    {
      $que_item = explode(',', $que_item);
    }
    else
    {
      unset($que_arr[$que_index]);
    }
  }
  return $que_arr;
}

function eco_que_arr2str($que_arr)
{
  foreach($que_arr as &$que_item)
  {
    $que_item = implode(',', $que_item);
  }
  return implode(';', $que_arr);
}

function eco_que_process($user, &$planet, $time_left)
{
  global $lang, $sn_data;

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
        // TODO: Next 2 lines will not work with unit que! Change it!
        $build_time = max(1, $que_item['TIME']);
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
    || count($que['que'][$que_id]) >= ($que_data['length'] + (mrc_get_level($user, $planet, MRC_ENGINEER)))
  )
  {
    return $que;
  }

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planet['id'], $time_now);
  $planet = $global_data['planet'];
  $que = $global_data['que'];
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
  //TODO: Rewrite as eco_bld_hangar_clear($planet, $action)
  global $sn_data;

  $que_string = '';
  $que_query = '';

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planet['id'], $time_now);
  $planet = $global_data['planet'];
  $que = $global_data['que'];
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
  global $sn_data, $time_now, $lang;

  doquery('START TRANSACTION');
  $user_row = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE", true);
  $user['que'] = $user_row['que'];
  if(!$user['que'])
  {
    doquery('ROLLBACK');
    return;
  }

  $time_left = max(0, $time_now - $user['onlinetime']);

  $planet = array('id' => $user['id_planet']);

  $update_add = '';
  $que_item = $user['que'] ? explode(',', $user['que']) : array();
//  if($user['que'] && $que_item[QI_TIME] <= $time_left)
  if($que_item[QI_TIME] <= $time_left)
  {
    $unit_id = $que_item[QI_UNIT_ID];
    $unit_db_name = $sn_data[$unit_id]['name'];

    $user[$unit_db_name] = $user_row[$unit_db_name];
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
  doquery("UPDATE `{{users}}` SET {$update_add}`que` = '{$user['que']}' WHERE `id` = '{$user['id']}' LIMIT 1");
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








/*
 * С $for_update === true эта функция должна вызываться только из транзакции! Все соответствующие записи в users и planets должны быть уже блокированы!
 *
 * $ques - Куда надо возвратить очереди
 * $que_type
 *   false - все очереди
 *   QUE_XXX - конкретная очередь по планете
 * $user - user запись
 * $planet
 *   $que_type === false - игнорируется
 *   $que_type === QUE_RESEARCH - игнорируется, $planet_id == 0, $planet_id_sql == NULL
 *   else - $que_type для указанной планеты
 * $for_update - true == нужно блокировать записи
 *
 */
function que_get_que(&$ques, $que_type = false, $user_id = null, $planet_id = 0, $for_update = false)
{
  sn_db_transaction_check($for_update);

  $planet_id = $planet_id && $que_type !== QUE_RESEARCH ? $planet_id : 0;
  // TODO: Some checks
  if($for_update || !$que_type || !$ques[$que_type][$planet_id])
  {
    if($que_type)
    {
      $ques[$que_type][$planet_id] = $ques['in_que'][$que_type][$planet_id] = array();
      $planet_id_sql = $planet_id ? '= ' . $planet_id : 'IS NULL';
      $sql_que = " AND `que_planet_id` {$planet_id_sql} AND `que_type` = {$que_type}";
    }
    else
    {
      $ques = array();
      $sql_que = '';
    }
    $que_query = doquery("SELECT * FROM {{que}} WHERE `que_player_id` = {$user_id} {$sql_que} ORDER BY que_id" . ($for_update ? ' FOR UPDATE' : ''));
//print($q . '<br/>');
    while($row = mysql_fetch_assoc($que_query))
    {
      $ques[$row['que_type']][intval($row['que_planet_id'])][] = $row;
      $ques['in_que'][$row['que_type']][intval($row['que_planet_id'])][$row['que_unit_id']] += $row['que_unit_amount'] * ($row['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
    }
  }
}

function que_add_unit($que_type, $unit_id, $user = array(), $planet = array(), $build_data, $unit_level = 0, $unit_amount = 1)
{
  sn_db_transaction_check(true);

  // TODO: Some checks
  db_change_units($user, $planet, array(
    RES_METAL     => -$build_data[BUILD_CREATE][RES_METAL],
    RES_CRYSTAL   => -$build_data[BUILD_CREATE][RES_CRYSTAL],
    RES_DEUTERIUM => -$build_data[BUILD_CREATE][RES_DEUTERIUM],
  ));

  $planet_id_origin = $planet['id'] ? $planet['id'] : 'NULL';
  $planet_id = $que_type == QUE_RESEARCH ? 'NULL' : $planet_id_origin;

  $resource_list = sys_unit_arr2str($build_data[BUILD_CREATE]);

  doquery(
    "INSERT INTO
      `{{que}}`
    SET
      `que_player_id` = {$user['id']},
      `que_planet_id` = {$planet_id},
      `que_planet_id_origin` = {$planet_id_origin},
      `que_type` = {$que_type},
      `que_time_left` = {$build_data[RES_TIME][BUILD_CREATE]},
      `que_unit_id` = {$unit_id},
      `que_unit_amount` = {$unit_amount},
      `que_unit_mode` = " . BUILD_CREATE . ",
      `que_unit_level` = {$unit_level},
      `que_unit_time` = {$build_data[RES_TIME][BUILD_CREATE]},
      `que_unit_price` = '{$resource_list}'"
  );
}

function que_delete($que_type, $user = array(), $planet = array(), $clear = false)
{
  // TODO: Some checks
  sn_db_transaction_start();
  $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE", true);
  $planet['id'] = $planet['id'] && $que_type !== QUE_RESEARCH ? $planet['id'] : 0;
  que_get_que($global_que, $que_type, $user['id'], $planet['id'], true);
//pdump($global_que);
//pdump($planet['id']);
//pdump($global_que[$que_type][$planet['id']]);

  if(!empty($global_que[$que_type][$planet['id']]))
  {
    $global_que[$que_type][$planet['id']] = array_reverse($global_que[$que_type][$planet['id']]);

    foreach($global_que[$que_type][$planet['id']] as $que_item)
    {
      doquery("DELETE FROM {{que}} WHERE que_id = {$que_item['que_id']} LIMIT 1");

      if($que_item['que_planet_id_origin'])
      {
        $planet['id'] = $que_item['que_planet_id_origin'];
      }

      $planet = $planet['id'] ? doquery("SELECT * FROM {{planets}} WHERE `id` = {$planet['id']} LIMIT 1 FOR UPDATE", true) : $planet;

      $build_data = sys_unit_str2arr($que_item['que_unit_price']);

      db_change_units($user, $planet, array(
        RES_METAL     => $build_data[RES_METAL] * $que_item['que_unit_amount'],
        RES_CRYSTAL   => $build_data[RES_CRYSTAL] * $que_item['que_unit_amount'],
        RES_DEUTERIUM => $build_data[RES_DEUTERIUM] * $que_item['que_unit_amount'],
      ));

      if(!$clear)
      {
        break;
      }
    }

    sn_db_transaction_commit();
  }
  else
  {
    sn_db_transaction_rollback();
  }
//die();
  sys_redirect($_SERVER['REQUEST_URI']);
}


/*
 *
 * Процедура парсит очереди текущего игрока в темплейт
 *
 * TODO: Переместить в хелперы темплейтов
 * TODO: Сделать поддержку нескольких очередей
 *
 */
function que_tpl_parse(&$template, $que_type, $user, $planet = array())
{
  // TODO: Переделать для 4que_type === false
  global $global_que, $lang, $config;

  $planet['id'] = $planet['id'] ? $planet['id'] : 0;
  que_get_que($global_que, $que_type, $user['id'], $planet['id']);

  if($global_que[$que_type])
  {
    foreach($global_que[$que_type][$planet['id']] as $que_element)
    {
      $unit_id = &$que_element['que_unit_id'];

      $template->assign_block_vars('que', array(
        'ID' => $unit_id,
        'QUE' => $que_type,
        'NAME' => $lang['tech'][$unit_id],
        'TIME' => $que_element['que_time_left'],
        'TIME_FULL' => $que_element['que_unit_time'],
        'AMOUNT' => $que_element['que_unit_amount'],
        'LEVEL' => $que_element['que_unit_level'],
      ));
    }
  }

  if($que_type == QUE_RESEARCH)
  {
    $template->assign_var('RESEARCH_ONGOING', count($global_que[QUE_RESEARCH][0]) >= $config->server_que_length_research);
  }
}


/*
 *
 * Эта процедура должна вызываться исключительно в транзакции!!!
 *
 * $user_id
 *   (integer) - обрабатываются глообальные очереди пользователя
 * $planet_id
 *   null - обработка очередей планет не производится
 *   false/0 - обрабатываются очереди всех планет по $user_id
 *   (integer) - обрабатываются локальные очереди для планеты. Нужно, например, в обработчике флотов
 *
 */
function que_process(&$user, $planet_id = null)
{
  global $sn_data, $time_now, $lang;

  $user_time_left = max(0, $time_now - $user['onlinetime']);
//$user_time_left = 25; // 25
  if($planet_id === null && !$user_time_left)
  {
    return;
  }

  sn_db_transaction_start();
  // Блокируем пользователя. Собственно, запись о нём нам не нужна - будем использовать старую
  doquery("SELECT `id` FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE");

  // Если нужно изменять данные на планетах - блокируем планеты и получаем данные о них
  // TODO: Это перенести на попозже, для избежания блокировок
  $planet_list = array();
  if($planet_id !== null)
  {
    // TODO: ХУЕТА!!!! ПЕРЕПИСАТЬ!!!!
    $planet_query = doquery("SELECT `id`, `last_update` FROM {{planets}} WHERE " . ($planet_id ? "`id` = {$planet_id}" : "`id_owner` = {$user['id']}") . " FOR UPDATE");
    while($planet_row = mysql_fetch_assoc($planet_query))
    {
      $planet_list[$planet_row['id']] = $planet_row;
    }
  }

  // Определяем, какие очереди нам нужны и получаем их
  $que_type_id = $planet_id === null ? QUE_RESEARCH : false;
  que_get_que($local_que, $que_type_id, $user['id'], $planet_id, true);
  $local_que['time_left'][QUE_RESEARCH][0] = $user_time_left;

//pdump($user_time_left, '$user_time_left');
  $db_changeset = array();
  $unit_changes = array();
  foreach($local_que as $que_id => &$que_data)
  {
    if(!intval($que_id))continue;
    foreach($que_data as $owner_id => &$que_items)
    {
      foreach($que_items as &$que_item)
      {
/*
$qi1 = $que_item;
unset($qi1['que_id']);
unset($qi1['que_player_id']);
unset($qi1['que_planet_id']);
unset($qi1['que_planet_id_origin']);
unset($qi1['que_type']);
unset($qi1['que_unit_mode']);
unset($qi1['que_unit_price']);
unset($qi1['que_unit_level']);
unset($qi1['que_unit_id']);
pdump($qi1['que_time_left'] + ($qi1['que_unit_amount'] - 1 ) * $qi1['que_unit_time'], 'total_time');
pdump($qi1, '$que_item');
*/


        // Вычисляем, сколько целых юнитов будет построено - от 0 до количества юнитов в очереди
        $unit_processed = min($que_item['que_unit_amount'] - 1, floor($local_que['time_left'][$que_id][$owner_id] / $que_item['que_unit_time']));
        // Вычитаем это время из остатков
        $local_que['time_left'][$que_id][$owner_id] -= $unit_processed * $que_item['que_unit_time'];

        // Теперь работаем с остатком времени на юните. Оно не может быть равно или меньше нуля

        // Вычитаем остаток времени работы очереди с времени постройки юнита
        if($que_item['que_time_left'] <= $local_que['time_left'][$que_id][$owner_id])
        {
          // Если время постройки - неположительное, значит мы достроили юнит
          // Увеличиваем количество отстроенных юнитов
          $unit_processed++;
          // Вычитаем из времени очереди потраченное на постройку время
          $local_que['time_left'][$que_id][$owner_id] -= $que_item['que_time_left'];
          $que_item['que_time_left'] = $que_item['que_unit_time'];
          // Тут у нас может остатся время очереди - если постройка была не последняя
        }

        // Изменяем количество оставшихся юнитов
        $que_item['que_unit_amount'] -= $unit_processed;

        if($que_item['que_unit_amount'])
        {
          $que_item['que_time_left'] = $que_item['que_time_left'] - $local_que['time_left'][$que_id][$owner_id];
          $local_que['time_left'][$que_id][$owner_id] = 0;
        }

        if(!$que_item['que_unit_amount'])
        {
          $db_changeset['que'][$que_item['que_id']] = array(
            'action' => SQL_OP_DELETE,
            'where' => array(
              "`que_id` = {$que_item['que_id']}",
            ),
          );
        }
        else
        {
          $db_changeset['que'][$que_item['que_id']] = array(
            'action' => SQL_OP_UPDATE,
            'where' => array(
              "`que_id` = {$que_item['que_id']}",
            ),
            'fields' => array(
              'que_unit_amount' => array(
                'delta' => -$unit_processed
              ),
              'que_time_left' => array(
                'set' => $que_item['que_time_left']
              ),
            ),
          );
        }

        if($unit_processed)
        {
          $unit_processed_delta = $unit_processed * ($que_item['que_unit_mode'] == BUILD_CREATE ? 1 : -1);
          $unit_changes[$owner_id][$que_item['que_unit_id']] += $unit_processed_delta;
        }
/*
pdump($unit_processed, '$unit_processed');
$qi2 = $que_item;
unset($qi2['que_id']);
unset($qi2['que_player_id']);
unset($qi2['que_planet_id']);
unset($qi2['que_planet_id_origin']);
unset($qi2['que_type']);
unset($qi2['que_unit_mode']);
unset($qi2['que_unit_price']);
unset($qi2['que_unit_level']);
unset($qi2['que_unit_id']);
pdump($qi2, '$que_item');
pdump($local_que['time_left'][$que_id][$owner_id], '$local_que[time_left][$que_id][$owner_id]');
print('<hr />');
*/
        // Если на очереди времени не осталось - выходим
        if(!$local_que['time_left'][$que_id][$owner_id])
        {
          break;
        }
      }
    }
  }


  // TODO: Re-enable quests for Alliances
  if(!empty($unit_changes) && !$user['user_as_ally'] && $user['id_planet'])
  {
    $planet = doquery("SELECT * FROM {{planets}} WHERE `id` = {$user['id_planet']} FOR UPDATE", true);
    $quest_list = qst_get_quests($user['id']);
    $quest_triggers = qst_active_triggers($quest_list);
  }
  else
  {
    $planet = array();
  }

  $quest_rewards = array();
  $xp_incoming = 0;
  foreach($unit_changes as $owner_id => $changes)
  {
    // $user_id_sql = $owner_id ? $owner_id : $user['id'];
    $planet_id_sql = $owner_id ? $owner_id : null;
    foreach($changes as $unit_id => $unit_value)
    {

      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, $unit_value, $user, $planet_id_sql);

      // TODO: Изменить согласно типу очереди
      $unit_level_new = mrc_get_level($user, array(), $unit_id, false, true) + $unit_value;
      $build_data = eco_get_build_data($user, array(), $unit_id, $unit_level_new - 1);
      $build_data = $build_data[BUILD_CREATE];
      foreach($sn_data['groups']['resources_loot'] as $resource_id)
      {
        $xp_incoming += $build_data[$resource_id];
      }

      if($planet['id'])
      {
        // TODO: Check mutiply condition quests
        $quest_trigger_list = array_keys($quest_triggers, $unit_id);
        foreach($quest_trigger_list as $quest_id)
        {
          if($quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE && $quest_list[$quest_id]['quest_unit_amount'] <= $unit_level_new)
          {
            $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
            $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
          }
        }
      }

    }
  }

  // TODO: Изменить согласно типу очереди
  rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);
  // TODO: Изменить начисление награды за квесты на ту планету, на которой происходил ресеч
  qst_reward($user, $planet, $quest_rewards, $quest_list);


  sn_db_changeset_apply($db_changeset);

  // Сообщения о постройке


//  $user['que'] = $user_row['que'];
//  if(!$user['que'])
//  {
//    sn_db_transaction_rollback();
//    return;
//  }
//
//  $time_left = max(0, $time_now - $user['onlinetime']);
//
//  $planet = array('id' => $user['id_planet']);
//
//  $update_add = '';
//  $que_item = $user['que'] ? explode(',', $user['que']) : array();
//  if($que_item[QI_TIME] <= $time_left)
//  {
//    $unit_id = $que_item[QI_UNIT_ID];
//    $unit_db_name = $sn_data[$unit_id]['name'];
//
//    $user[$unit_db_name] = $user_row[$unit_db_name];
//    $user[$unit_db_name]++;
//    msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_research_from'], $lang['msg_que_research_subject'], sprintf($lang['msg_que_research_message'], $lang['tech'][$unit_id], $user[$unit_db_name]));
//
//    /*
//    // TODO: Re-enable quests for Alliances
//    if(!$user['user_as_ally'] && $planet['id'])
//    {
//      $quest_list = qst_get_quests($user['id']);
//      $quest_triggers = qst_active_triggers($quest_list);
//      $quest_rewards = array();
//      // TODO: Check mutiply condition quests
//      $quest_trigger_list = array_keys($quest_triggers, $unit_id);
//      foreach($quest_trigger_list as $quest_id)
//      {
//        if($quest_list[$quest_id]['quest_unit_amount'] <= $user[$unit_db_name] && $quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE)
//        {
//          $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
//          $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
//        }
//      }
//      qst_reward($user, $planet, $quest_rewards, $quest_list);
//    }
//    */
//
//    $update_add = "`{$unit_db_name}` = `{$unit_db_name}` + 1, ";
//
//    $build_data = eco_get_build_data($user, $planet, $unit_id, $user[$unit_db_name] - 1);
//    $build_data = $build_data[BUILD_CREATE];
//    $xp_incoming = 0;
//    foreach($sn_data['groups']['resources_loot'] as $resource_id)
//    {
//      $xp_incoming += $build_data[$resource_id];
//    }
//    rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);
//
//    $que_item = array();
//  }
//  else
//  {
//    $que_item[QI_TIME] -= $time_left;
//  }
//
//  $user['que'] = implode(',', $que_item);
//  doquery("UPDATE `{{users}}` SET {$update_add}`que` = '{$user['que']}' WHERE `id` = '{$user['id']}' LIMIT 1");
  $user =   doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE", true);
  // TODO Так же пересчитывать планеты

  sn_db_transaction_commit();
}
