<?php

/**
 * eco_can_build_unit.php
 *
 * 2.0 copyright 2009-2011 Gorlum for http://supernova.ws
 *  [!] Full rewrote from scratch
 *  [+] function eco_unit_busy
 *  [+] function eco_lab_is_building
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function eco_can_build_unit($user, $planet, $unit_id)
{
  global $sn_data;

  $require = $sn_data[$unit_id]['require'];

  $accessible = true;
  if (isset($require))
  {
    foreach($require as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = isset($planet[$db_name]) ? $planet[$db_name] : $user[$db_name];

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

  return (($unit_id == 31 || $unit_id == 35) && $lab_busy) || ($unit_id == 21 && $hangar_busy);
}

function eco_lab_is_building($config, $que)
{
  return $que['in_que_abs'][31] && !$config->BuildLabWhileRun ? true : false;
}

function eco_hangar_is_building($que)
{
  return $que['in_que_abs'][21] ? true : false;
}

?>
