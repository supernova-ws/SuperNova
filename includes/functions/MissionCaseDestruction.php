<?php
/*
 * Partial copyright (c) 2009-2010 by Gorlum for http://supernova.ws

Based on original code:
#############################################################################
#  Filename: MissionCaseDestruction.php
#  Create date: Saturday, April 05, 2008    15:51:35
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
function MissionCaseDestruction($FleetRow) {
  $result = MissionCaseAttack($FleetRow);

  if(empty($result)) return;
  if ($result['won'] != 1) return;

  global $user, $phpEx, $ugamela_root_path, $pricelist, $lang, $resource, $CombatCaps, $time_now;

  $TargetPlanet = doquery('SELECT * FROM {{table}} WHERE ' .
         '`galaxy` = '. $FleetRow['fleet_end_galaxy'] .
    ' AND `system` = '. $FleetRow['fleet_end_system'] .
    ' AND `planet` = '. $FleetRow['fleet_end_planet'] .
    ' AND `planet_type` = '. $FleetRow['fleet_end_type'] .';',
  'planets', true);

  $MoonSize      = $TargetPlanet['diameter'];
  $MoonName      = $TargetPlanet['name'];

  $RipsKilled = 0;
  $MoonDestroyed = 0;

  foreach($result['rw'][count($result['rw'])-1]['attackers'] as $fleet){
    foreach($fleet['detail'] as $shipID => $shipNum){
      if($shipID == 214)
        $Rips += $shipNum;
    }
  }

  if($Rips>0){
     $MoonDestChance = min(99, round((100 - sqrt($MoonSize)) * (sqrt($Rips))));
     $RipDestChance = round((sqrt($MoonSize)) / 2);
     $UserChance = mt_rand(1, 100);
     if (($UserChance > 0) AND ($UserChance <= $MoonDestChance)){
        $RipsKilled = 0;
        $MoonDestroyed = 1;
     }elseif (($UserChance > 0) AND ($UserChance <= $RipDestChance)){
        $RipsKilled = 1;
        $MoonDestroyed = 0;
     }
  }

  if ($MoonDestroyed == 1){
     // Getting ID of Luna in Galaxy table - really just for deletion
     $QryGalaxy = doquery("SELECT * FROM {{table}} WHERE " .
       "`galaxy` = '". $FleetRow['fleet_end_galaxy'] . "' AND " .
       "`system` = '". $FleetRow['fleet_end_system'] . "' AND " .
       "`planet` = '". $FleetRow['fleet_end_planet'] . "';"
     , 'galaxy', true);
     doquery("DELETE FROM {{table}} WHERE `id` = '".$QryGalaxy['id_luna']."';", 'lunas');
     doquery("DELETE FROM {{table}} WHERE `id` ='".$TargetPlanet['id']."';", 'planets');

     $QryUpdateGalaxy  = "UPDATE {{table}} SET ";
     $QryUpdateGalaxy .= "`id_luna` = '0' ";
     $QryUpdateGalaxy .= "WHERE ";
     $QryUpdateGalaxy .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
     $QryUpdateGalaxy .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
     $QryUpdateGalaxy .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' ";
     $QryUpdateGalaxy .= "LIMIT 1;";
     doquery( $QryUpdateGalaxy , 'galaxy');
     $message  = $lang['sys_moon_destroyed'];
  }elseif($RipsKilled == 1){
     doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
     $message  = $lang['sys_rips_destroyed'];
  }else{
     $message  = $lang['sys_rips_come_back'];
  }
  $message .= "<br><br>";
  $message .= $lang['sys_chance_moon_destroy'].intval($MoonDestChance)."%. <br>".$lang['sys_chance_rips_destroy'].intval($RipDestChance)."%";

  SendSimpleMessage ( $FleetRow['fleet_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );
  SendSimpleMessage ( $TargetPlanet['id_owner'], '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );
}
?>