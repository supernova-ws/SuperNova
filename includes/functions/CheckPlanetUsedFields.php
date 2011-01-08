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
}

?>
