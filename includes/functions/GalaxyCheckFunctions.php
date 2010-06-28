<?php

/**
 * GalaxyCheckFunctions
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------
//
// Verification sur la base des planetes
//

// Suppression complete d'une planete
function CheckAbandonPlanetState (&$planet) {
  global $time_now;

/*
  if($planet['planet_type'] == 1){
    if ($planet['destruyed'] <= time()) {
      doquery("DELETE FROM `{{planets}}` WHERE `id` = '{$planet['id']}';");
      doquery("DELETE FROM `{{galaxy}}` WHERE `id_planet` = '{$planet['id']}';");
    }
  }elseif($planet['planet_type'] == 3){
    if (($planet['destruyed'] + 172800) <= time() && $planet['destruyed']) {
      $query = doquery("DELETE FROM {{planets}} WHERE `id` = '{$planet['id']}';");
    }
  }
*/

  if(!$planet['destruyed']) return;

  if($planet['planet_type'] == 1 && $planet['destruyed'] <= $time_now){
    doquery("DELETE FROM `{{planets}}` WHERE `id` = '{$planet['id']}';");
    doquery("DELETE FROM `{{galaxy}}` WHERE `id_planet` = '{$planet['id']}';");
  }elseif($planet['planet_type'] == 3 && ($planet['destruyed'] + 172800) <= $time_now){
    doquery("DELETE FROM `{{planets}}` WHERE `id` = '{$planet['id']}';");
  }
}
?>