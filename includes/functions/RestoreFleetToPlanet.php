<?php

/**
 * RestoreFleetToPlanet.php
 *
 * @version 1.0
 * @copyright 2008 Chlorel for XNova
 */

// RestoreFleetToPlanet
//
// $FleetRow -> enregistrement de flotte
// $Start    -> true  = planete de depart
//           -> false = planete d'arrivée
function RestoreFleetToPlanet ( &$fleet_row, $start = true )
{
  if(!is_array($fleet_row))
  {
    return false;
  }

  global $sn_data;

  $prefix = $start ? 'start' : 'end';

  $query = 'UPDATE {{planets}} SET ';

  $fleet_strings = explode(';', $fleet_row['fleet_array']);
  foreach ($fleet_strings as $ship_string)
  {
    if ($ship_string != '')
    {
      $ship_record = explode (',', $ship_string);
      $ship_db_name = $sn_data[$ship_record[0]]['name'];
      $query .= "`{$ship_db_name}` = `{$ship_db_name}` + '{$ship_record[1]}', ";
    }
  }

  $query .= "`metal` = `metal` + '{$fleet_row['fleet_resource_metal']}', ";
  $query .= "`crystal` = `crystal` + '{$fleet_row['fleet_resource_crystal']}', ";
  $query .= "`deuterium` = `deuterium` + '{$fleet_row['fleet_resource_deuterium']}' ";
  $query .= "WHERE ";
  $query .= "`galaxy` = '". $fleet_row["fleet_{$prefix}_galaxy"] ."' AND ";
  $query .= "`system` = '". $fleet_row["fleet_{$prefix}_system"] ."' AND ";
  $query .= "`planet` = '". $fleet_row["fleet_{$prefix}_planet"] ."' AND ";
  $query .= "`planet_type` = '". $fleet_row["fleet_{$prefix}_type"] ."' ";
  $query .= "LIMIT 1;";

  doquery($query);

  doquery("DELETE FROM {{fleets}} WHERE `fleet_id`='{$fleet_row['fleet_id']}' LIMIT 1;");
}

?>
