<?php

/**
 * eco_can_build_unit.php
 *
 * 2.0 copyright 2009-2011 Gorlum for http://supernova.ws
 *  [!] Full rewrote from scratch
 *  [+] function eco_unit_busy
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function eco_can_build_unit($user, $planet, $unit_id)
{
  global $sn_data;

  $accessible = true;
  if (isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = isset($planet[$db_name]) ? $planet[$db_name] : (isset($user[$db_name]) ? $user[$db_name] : 0);

      if($data < $require_level)
      {
        $accessible = false;
        break;
      }
    }
  }

  return $accessible;
}

function eco_unit_busy($user, $planet, $que, $unit_id)
{
  global $config;

  $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
  $lab_busy    = $planet['b_tech'] && $planet['b_tech_id'] && !$config->BuildLabWhileRun;

  switch($unit_id)
  {
    case 21:
      $return = $hangar_busy;
    break;

    case 31:
    case 35:
      $return = $lab_busy;
    break;

    default:
      $return = false;
    break;
  }

//  return (($unit_id == 31 || $unit_id == 35) && $lab_busy) || ($unit_id == 21 && $hangar_busy);
  return $return;
}

functioin eco_unit_buildable($user, $planet, $que, $que_id, $unit_id, $unit_amount = 1, $build_mode = BUILD_CREATE)
{
  if($unit_amount < 1)
  {
    return BUILD_AMOUNT_WRONG;
  }
  $unit_amount = intval($unit_amount);

  @$que_data = $GLOBALS['sn_data']['groups']['ques'][$que_id];
  if(!isarray($que_data))
  {
    return BUILD_QUE_WRONG;
  }

  if($que_id == QUE_STRUCTURES)
  {
    $que_data['unit_list'] = $GLOBALS['sn_data']['groups']['build_allow'][$planet['planet_type']];
  }

  if(!in_array($unit_id, $que_data['unit_list']))
  {
    return BUILD_QUE_UNIT_WRONG;
  }

  $config_build_busy_lab = $GLOBALS['config']->BuildLabWhileRun;

/*
  $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
  $lab_busy    = $planet['b_tech'] && $planet['b_tech_id'] && !$config->BuildLabWhileRun;

  switch($unit_id)
  {
    case 21:
      $return = $hangar_busy;
    break;

    case 31:
    case 35:
      $return = $lab_busy;
    break;

    default:
      $return = false;
    break;
  }

//  return (($unit_id == 31 || $unit_id == 35) && $lab_busy) || ($unit_id == 21 && $hangar_busy);
  return $return;
*/

//  $unit_level = ($planet[$unit_db_name] ? $planet[$unit_db_name] : 0) + $que['in_que'][$unit_id];
//  $build_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);
}

?>
