<?php

/**
 *
 * CheckPlanetUsedFields.php
 *
 * v2.0 Rewrote to utilize foreach()
 *      Complying with PCG0
 * v1.1 some optimizations
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// Verification du nombre de cases utilisées sur la planete courrante
function CheckPlanetUsedFields(&$planet)
{
  if(!$planet['id'])
  {
    return 0;
  }

  global $sn_data;

  $planet_fields = 0;
  foreach($sn_data['groups']['build_allow'][$planet['planet_type']] as $building_id)
  {
    $planet_fields += $planet[$sn_data[$building_id]['name']];
  }

  if($planet['field_current'] != $planet_fields)
  {
    $planet['field_current'] = $planet_fields;
    doquery("UPDATE {{planets}} SET field_current={$planet_fields} WHERE id={$planet['id']} LIMIT 1;");
  }

/*
  // Tous les batiments
  $cfc  = $planet[$sn_data[1]['name']]  + $planet[$sn_data[2]['name']]  + $planet[$sn_data[3]['name']];
  $cfc += $planet[$sn_data[4]['name']]  + $planet[$sn_data[12]['name']] + $planet[$sn_data[14]['name']];
  $cfc += $planet[$sn_data[15]['name']] + $planet[$sn_data[21]['name']] + $planet[$sn_data[22]['name']];
  $cfc += $planet[$sn_data[23]['name']] + $planet[$sn_data[24]['name']] + $planet[$sn_data[31]['name']];
  $cfc += $planet[$sn_data[33]['name']] + $planet[$sn_data[34]['name']] + $planet[$sn_data[44]['name']];

  // If planet is a moon - checking for specific structures
  if ($planet['planet_type'] == '3') {
    $cfc += $planet[$sn_data[41]['name']] + $planet[$sn_data[42]['name']] + $planet[$sn_data[43]['name']];
  }

  // Storing correct numbers to DB
  if ($planet['field_current'] != $cfc) {
    $planet['field_current'] = $cfc;
    doquery("UPDATE {{planets}} SET field_current={$cfc} WHERE id={$planet['id']}");
  }
*/
}
?>