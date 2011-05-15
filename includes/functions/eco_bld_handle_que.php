<?php

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

function eco_bld_handle_que($user, &$planet, $production_time)
{
  global $resource;

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
        $build_time = GetBuildingTime($user, $planet, $unit_id);

        if(!$skip_rest)
        {
          $unit_db_name = $resource[$unit_id];

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
