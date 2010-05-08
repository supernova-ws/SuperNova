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
  global $user, $phpEx, $ugamela_root_path, $pricelist, $lang, $resource, $CombatCaps, $time_now;

  // --- This is universal part which should be moved to fleet manager
  // Checking fleet message: if not 0 then we already managed this fleet
  if($FleetRow['fleet_mess'] != 0) {
    // Checking fleet end_time: if less then time_now then restoring fleet to planet
    if($FleetRow['fleet_end_time'] <= $time_now) {
      RestoreFleetToPlanet($FleetRow);
      doquery ('DELETE FROM {{table}} WHERE `fleet_id`='.$FleetRow['fleet_id'],'fleets');
    }
    return;
  }

  // Checking if we should now to proceed this fleet - does it arrive? No - exiting.
  if ($FleetRow['fleet_start_time'] > $time_now) return;

  // Misc checking for missing fleet data. Why we need it anyway?!
  if (!isset($CombatCaps[202]['sd']))
     message("<font color=\"red\">". $lang['sys_no_vars'] ."</font>", $lang['sys_error'], "fleet." . $phpEx, 2);

  // Using to get ownerID, lunaID from PLANETS table and list of resources
  $TargetPlanet = doquery('SELECT * FROM {{table}} WHERE ' .
         '`galaxy` = '. $FleetRow['fleet_end_galaxy'] .
    ' AND `system` = '. $FleetRow['fleet_end_system'] .
    ' AND `planet` = '. $FleetRow['fleet_end_planet'] .
    ' AND `planet_type` = '. $FleetRow['fleet_end_type'] .';',
  'planets', true);

  if (!isset($TargetPlanet['id'])) {
    if ($FleetRow['fleet_group'] > 0) {
      doquery('DELETE FROM {{table}} WHERE `fleet_group` ='.$FleetRow['fleet_group'],'aks');
      doquery('UPDATE {{table}} SET `fleet_mess` = 1 WHERE `fleet_group` ='.$FleetRow['fleet_group'],'fleets');
    } else {
      doquery('UPDATE {{table}} SET `fleet_mess` = 1 WHERE `fleet_id` ='.$FleetRow['fleet_id'],'fleets');
    }
    return;
  }
  // --- End of Universal part

  $CurrentUserID = $FleetRow['fleet_owner'];
  $TargetUserID  = $TargetPlanet['id_owner'];

  $TargetUser    = doquery('SELECT * FROM {{table}} WHERE `id` ='.$TargetPlanet['id_owner'],'users', true);

  UpdatePlanetBatimentQueueList($TargetPlanet, $TargetUser);
  PlanetResourceUpdate( $TargetUser, $TargetPlanet, $time_now );

  $MoonSize      = $TargetPlanet['diameter'];
  $MoonName      = $TargetPlanet['name'];

  // Getting ID of Luna in Galaxy table - really just for deletion
  $QryGalaxyMoon  = "SELECT * FROM {{table}} ";
  $QryGalaxyMoon .= "WHERE ";
  $QryGalaxyMoon .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
  $QryGalaxyMoon .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
  $QryGalaxyMoon .= "`planet` = '". $FleetRow['fleet_end_planet'] ."';";
  $QryGalaxy         = doquery($QryGalaxyMoon, 'galaxy', true);

  $QryCurrentTech   = "SELECT ";
  $QryCurrentTech  .= "`military_tech`, `defence_tech`, `shield_tech` ";
  $QryCurrentTech  .= "FROM {{table}} ";
  $QryCurrentTech  .= "WHERE ";
  $QryCurrentTech  .= "`id` = '". $CurrentUserID ."';";

  $CurrentTechno    = doquery($QryCurrentTech, 'users', true);

  for ($SetItem = 200; $SetItem < 500; $SetItem++) {
     if ($TargetPlanet[$resource[$SetItem]] > 0) {
        $TargetSet[$SetItem]['count'] = $TargetPlanet[$resource[$SetItem]];
     }
  }

  $TheFleet = explode(";", $FleetRow['fleet_array']);
  foreach($TheFleet as $a => $b) {
     if ($b != '') {
        $a = explode(",", $b);
        $CurrentSet[$a[0]]['count'] = $a[1];
     }
  }



  include_once($ugamela_root_path . 'includes/ataki.' . $phpEx);

  // Calcul de la duree de traitement (initialisation)
  $mtime        = microtime();
  $mtime        = explode(" ", $mtime);
  $mtime        = $mtime[1] + $mtime[0];
  $starttime    = $mtime;

  $walka        = walka($CurrentSet, $TargetSet, $CurrentTechno, $TargetUser);

  // Calcul de la duree de traitement (calcul)
  $mtime        = microtime();
  $mtime        = explode(" ", $mtime);
  $mtime        = $mtime[1] + $mtime[0];
  $endtime      = $mtime;
  $totaltime    = ($endtime - $starttime);

  // Ce qu'il reste de l'attaquant
  $CurrentSet   = $walka["atakujacy"];
  // Ce qu'il reste de l'attaqué
  $TargetSet    = $walka["wrog"];
  // Le resultat de la bataille
  $FleetResult  = $walka["wygrana"];
  // Rapport long (rapport de bataille detaillé)
  $dane_do_rw   = $walka["dane_do_rw"];
  // Rapport court (cdr + unitées perdues)
  $zlom         = $walka["zlom"];

  $FleetArray   = "";
  $FleetAmount  = 0;
  $FleetStorage = 0;
  $Rips = 0;
  foreach ($CurrentSet as $Ship => $Count) {
     if ($Ship == '214'){
        $Rips += $Count['count'];
     }
     if ($Ship == '210'){
        $FleetStorage += 0;
     }else{
        $FleetStorage += $pricelist[$Ship]["capacity"] * $Count['count'];
     }
     $FleetArray   .= $Ship.",".$Count['count'].";";
     $FleetAmount  += $Count['count'];
  }
  // Au cas ou le p'tit rigolo qu'a envoyé la flotte y avait mis des ressources ...
  $FleetStorage -= $FleetRow["fleet_resource_metal"];
  $FleetStorage -= $FleetRow["fleet_resource_crystal"];
  $FleetStorage -= $FleetRow["fleet_resource_deuterium"];

  $TargetMoonUpd = "";
  if (!is_null($TargetSet)) {
     foreach($TargetSet as $Ship => $Count) {
        $TargetMoonUpd .= "`". $resource[$Ship] ."` = '". $Count['count'] ."', ";
     }
  }

  // Determination des ressources pillées
  $Mining['metal']   = 0;
  $Mining['crystal'] = 0;
  $Mining['deuter']  = 0;
  if ($FleetResult == "a") {
     if ($FleetStorage > 0) {
        $metal   = $TargetPlanet['metal'] / 2;
        $crystal = $TargetPlanet['crystal'] / 2;
        $deuter  = $TargetPlanet['deuterium'] / 2;
        if (($metal + $crystal + $deuter) > $FleetStorage){
           if ($metal > ($FleetStorage / 3)) {
              $Mining['metal']   = $FleetStorage / 3;
              $FleetStorage      = $FleetStorage - $Mining['metal'];
           } else {
              $Mining['metal']   = $metal;
              $FleetStorage      = $FleetStorage - $Mining['metal'];
           }

           if (($crystal) > $FleetStorage / 2) {
              $Mining['crystal'] = $FleetStorage / 2;
              $FleetStorage      = $FleetStorage - $Mining['crystal'];
           } else {
              $Mining['crystal'] = $crystal;
              $FleetStorage      = $FleetStorage - $Mining['crystal'];
           }

           if (($deuter) > $FleetStorage) {
              $Mining['deuter']  = $FleetStorage;
              $FleetStorage      = $FleetStorage - $Mining['deuter'];
           } else {
              $Mining['deuter']  = $deuter;
              $FleetStorage      = $FleetStorage - $Mining['deuter'];
           }
        } else {
           $Mining['metal']   = $metal;
           $Mining['crystal'] = $crystal;
           $Mining['deuter']  = $deuter;
           $FleetStorage      = $FleetStorage - $Mining['metal'] - $Mining['crystal'] - $Mining['deuter'];
        }
     }
  }
  $Mining['metal']   = round($Mining['metal']);
  $Mining['crystal'] = round($Mining['crystal']);
  $Mining['deuter']  = round($Mining['deuter']);

  // Mise a jour de l'enregistrement de la planete attaquée
  $QryUpdateTarget  = "UPDATE {{table}} SET ";
  $QryUpdateTarget .= $TargetMoonUpd;
  $QryUpdateTarget .= "`metal` = `metal` - '". $Mining['metal'] ."', ";
  $QryUpdateTarget .= "`crystal` = `crystal` - '". $Mining['crystal'] ."', ";
  $QryUpdateTarget .= "`deuterium` = `deuterium` - '". $Mining['deuter'] ."' ";
  $QryUpdateTarget .= "WHERE ";
  $QryUpdateTarget .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
  $QryUpdateTarget .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
  $QryUpdateTarget .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
  $QryUpdateTarget .= "`planet_type` = '3' ";
  $QryUpdateTarget .= "LIMIT 1;";
  doquery( $QryUpdateTarget , 'planets');

  // Mise a jour du champ de ruine devant la planete attaquée
  $QryUpdateGalaxy  = "UPDATE {{table}} SET ";
  $QryUpdateGalaxy .= "`metal` = `metal` + '". $zlom['metal'] ."', ";
  $QryUpdateGalaxy .= "`crystal` = `crystal` + '". $zlom['crystal'] ."' ";
  $QryUpdateGalaxy .= "WHERE ";
  $QryUpdateGalaxy .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
  $QryUpdateGalaxy .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
  $QryUpdateGalaxy .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' ";
  $QryUpdateGalaxy .= "LIMIT 1;";
  doquery( $QryUpdateGalaxy , 'galaxy');

  // Là on va discuter le bout de gras pour voir s'il y a moyen d'avoir une Lune !
  $FleetDebris      = $zlom['metal'] + $zlom['crystal'];
  $StrAttackerUnits = sprintf ($lang['sys_attacker_lostunits'], $zlom["atakujacy"]);
  $StrDefenderUnits = sprintf ($lang['sys_defender_lostunits'], $zlom["wrog"]);
  $StrRuins         = sprintf ($lang['sys_gcdrunits'], $zlom["metal"], $lang['metal'], $zlom['crystal'], $lang['crystal']);
  $DebrisField      = $StrAttackerUnits ."<br />". $StrDefenderUnits ."<br />". $StrRuins;
  $MoonChance       = $FleetDebris / 100000;
  if ($FleetDebris > 2000000) {
     $MoonChance = 20;
  }
  if ($FleetDebris < 100000) {
     $UserChance = 0;
     $ChanceMoon = "";
  } elseif ($FleetDebris >= 100000) {
     $UserChance = mt_rand(1, 100);
     $ChanceMoon       = sprintf ($lang['sys_moonproba'], $MoonChance);
  }

  $GottenMoon = "";

  $AttackDate        = date("m-d H:i:s", $FleetRow["fleet_start_time"]);
  $title             = sprintf ($lang['sys_attack_title'], $AttackDate);
  $raport            = "<center><table><tr><td>". $title ."<br />";
  $zniszczony        = false;
  $a_zestrzelona     = 0;
  $AttackTechon['A'] = $CurrentTechno["military_tech"] * 10;
  $AttackTechon['B'] = $CurrentTechno["defence_tech"] * 10;
  $AttackTechon['C'] = $CurrentTechno["shield_tech"] * 10;
  $AttackerData      = sprintf ($lang['sys_attack_attacker_pos'], $CurrentUser["username"], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet'] );
  $AttackerTech      = sprintf ($lang['sys_attack_techologies'], $AttackTechon['A'], $AttackTechon['B'], $AttackTechon['C']);

  $DefendTechon['A'] = $TargetUser["military_tech"] * 10;
  $DefendTechon['B'] = $TargetUser["defence_tech"] * 10;
  $DefendTechon['C'] = $TargetUser["shield_tech"] * 10;
  $DefenderData      = sprintf ($lang['sys_attack_defender_pos'], $TargetUser["username"], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'] );
  $DefenderTech      = sprintf ($lang['sys_attack_techologies'], $DefendTechon['A'], $DefendTechon['B'], $DefendTechon['C']);

  foreach ($dane_do_rw as $a => $b) {
     $raport .= "<table border=1 width=100%><tr><th><br /><center>".$AttackerData."<br />".$AttackerTech."<table border=1>";
     if ($b["atakujacy"]['count'] > 0) {
        $raport1 = "<tr><th>".$lang['sys_ship_type']."</th>";
        $raport2 = "<tr><th>".$lang['sys_ship_count']."</th>";
        $raport3 = "<tr><th>".$lang['sys_ship_weapon']."</th>";
        $raport4 = "<tr><th>".$lang['sys_ship_shield']."</th>";
        $raport5 = "<tr><th>".$lang['sys_ship_armour']."</th>";
        foreach ($b["atakujacy"] as $Ship => $Data) {
           if (is_numeric($Ship)) {
              if ($Data['count'] > 0) {
                 $raport1 .= "<th>". $lang["tech_rc"][$Ship] ."</th>";
                 $raport2 .= "<th>". $Data['count'] ."</th>";
                 $raport3 .= "<th>". round($Data["atak"]   / $Data['count']) ."</th>";
                 $raport4 .= "<th>". round($Data["tarcza"] / $Data['count']) ."</th>";
                 $raport5 .= "<th>". round($Data["obrona"] / $Data['count']) ."</th>";
              }
           }
        }
        $raport1 .= "</tr>";
        $raport2 .= "</tr>";
        $raport3 .= "</tr>";
        $raport4 .= "</tr>";
        $raport5 .= "</tr>";
        $raport .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;
     } else {
        if ($a == 2) {
           $a_zestrzelona = 1;
        }
        $zniszczony = true;
        $raport .= "<br />". $lang['sys_destroyed'];
     }

     $raport .= "</table></center></th></tr></table>";
     $raport .= "<table border=1 width=100%><tr><th><br /><center>".$DefenderData."<br />".$DefenderTech."<table border=1>";
     if ($b["wrog"]['count'] > 0) {
        $raport1 = "<tr><th>".$lang['sys_ship_type']."</th>";
        $raport2 = "<tr><th>".$lang['sys_ship_count']."</th>";
        $raport3 = "<tr><th>".$lang['sys_ship_weapon']."</th>";
        $raport4 = "<tr><th>".$lang['sys_ship_shield']."</th>";
        $raport5 = "<tr><th>".$lang['sys_ship_armour']."</th>";
        foreach ($b["wrog"] as $Ship => $Data) {
           if (is_numeric($Ship)) {
              if ($Data['count'] > 0) {
                 $raport1 .= "<th>". $lang["tech_rc"][$Ship] ."</th>";
                 $raport2 .= "<th>". $Data['count'] ."</th>";
                 $raport3 .= "<th>". round($Data["atak"]   / $Data['count']) ."</th>";
                 $raport4 .= "<th>". round($Data["tarcza"] / $Data['count']) ."</th>";
                 $raport5 .= "<th>". round($Data["obrona"] / $Data['count']) ."</th>";
              }
           }
        }
        $raport1 .= "</tr>";
        $raport2 .= "</tr>";
        $raport3 .= "</tr>";
        $raport4 .= "</tr>";
        $raport5 .= "</tr>";
        $raport .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;
     } else {
        $zniszczony = true;
        $raport .= "<br />". $lang['sys_destroyed'];
     }
     $raport .= "</table></center></th></tr></table>";

     if (($zniszczony == false) and ($a < 6)) {
        $AttackWaveStat    = sprintf ($lang['sys_attack_attack_wave'], floor($b["atakujacy"]["count"]), floor($b["atakujacy"]["atak"]), floor($b["wrog"]["tarcza"]));
        $DefendWavaStat    = sprintf ($lang['sys_attack_defend_wave'], floor($b["wrog"]["count"]), floor($b["wrog"]["atak"]), floor($b["atakujacy"]["tarcza"]));
        $raport           .= "<br /><center>".$AttackWaveStat."<br />".$DefendWavaStat."</center>";
     }
  }
  switch ($FleetResult) {
     case "a":
        $Pillage           = sprintf ($lang['sys_stealed_ressources'], $Mining['metal'], $lang['metal'], $Mining['crystal'], $lang['Crystal'], $Mining['deuter'], $lang['deuterium']);
        $raport           .= $lang['sys_attacker_won'] ."<br />". $Pillage ."<br />";
        $raport           .= $DebrisField ."<br />";
        $raport           .= $ChanceMoon ."<br />";
        $raport           .= $GottenMoon ."<br />";
        break;
     case "r":
        $raport           .= $lang['sys_both_won'] ."<br />";
        $raport           .= $DebrisField ."<br />";
        $raport           .= $ChanceMoon ."<br />";
        $raport           .= $GottenMoon ."<br />";
        break;
     case "w":
        $raport           .= $lang['sys_defender_won'] ."<br />";
        $raport           .= $DebrisField ."<br />";
        $raport           .= $ChanceMoon ."<br />";
        $raport           .= $GottenMoon ."<br />";
        doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
        break;
     default:
        break;
  }
  $SimMessage        = sprintf ($lang['sys_rapport_build_time'], $totaltime);
  $raport           .= $SimMessage ."</table>";

  $dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];
  $rid   = md5($raport);
  $RidMessage            = "<br><br><center> reportID= ".$rid."</center>";
  $raport   .= $RidMessage;
  $QryInsertRapport  = "INSERT INTO {{table}} SET ";
  $QryInsertRapport .= "`time` = UNIX_TIMESTAMP(), ";
  $QryInsertRapport .= "`id_owner1` = '". $FleetRow['fleet_owner'] ."', ";
  $QryInsertRapport .= "`id_owner2` = '". $TargetUserID ."', ";
  $QryInsertRapport .= "`rid` = '". $rid ."', ";
  $QryInsertRapport .= "`a_zestrzelona` = '". $a_zestrzelona ."', ";
  $QryInsertRapport .= "`raport` = '". addslashes ( $raport ) ."';";
  doquery( $QryInsertRapport , 'rw');

  // Colorisation du résumé de rapport pour l'attaquant
  $raport  = "<a href=\"#\" OnClick=\"f( 'rw.php?raport=". $rid ."', '');\" >";
  $raport .= "<center>";
  if       ($FleetResult == "a") {
     $raport .= "<font color=\"green\">";
  } elseif ($FleetResult == "r") {
     $raport .= "<font color=\"orange\">";
  } elseif ($FleetResult == "w") {
     $raport .= "<font color=\"red\">";
  }
  $raport .= $lang['sys_mess_attack_report'] ." [". $FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."] </font></a><br /><br />";
  $raport .= "<font color=\"red\">". $lang['sys_perte_attaquant'] .": ". $zlom["atakujacy"] ."</font>";
  $raport .= "<font color=\"green\">   ". $lang['sys_perte_defenseur'] .":". $zlom["wrog"] ."</font><br />" ;
  $raport .= $lang['sys_gain'] ." ". $lang['metal'] .":<font color=\"#adaead\">". $Mining['metal'] ."</font>   ". $lang['crystal'] .":<font color=\"#ef51ef\">". $Mining['crystal'] ."</font>   ". $lang['deuterium'] .":<font color=\"#f77542\">". $Mining['deuter'] ."</font><br />";
  $raport .= $lang['sys_debris'] ." ". $lang['metal'] .":<font color=\"#adaead\">". $zlom['metal'] ."</font>   ". $lang['crystal'] .":<font color=\"#ef51ef\">". $zlom['crystal'] ."</font><br /></center>";

  $Mining['metal']   = $Mining['metal']   + $FleetRow["fleet_resource_metal"];
  $Mining['crystal'] = $Mining['crystal'] + $FleetRow["fleet_resource_crystal"];
  $Mining['deuter']  = $Mining['deuter']  + $FleetRow["fleet_resource_deuterium"];

  $QryUpdateFleet  = "UPDATE {{table}} SET ";
  $QryUpdateFleet .= "`fleet_amount` = '". $FleetAmount ."', ";
  $QryUpdateFleet .= "`fleet_array` = '". $FleetArray ."', ";
  $QryUpdateFleet .= "`fleet_mess` = '1', ";
  $QryUpdateFleet .= "`fleet_resource_metal` = '". $Mining['metal'] ."', ";
  $QryUpdateFleet .= "`fleet_resource_crystal` = '". $Mining['crystal'] ."', ";
  $QryUpdateFleet .= "`fleet_resource_deuterium` = '". $Mining['deuter'] ."' ";
  $QryUpdateFleet .= "WHERE fleet_id = '". $FleetRow['fleet_id'] ."' ";
  $QryUpdateFleet .= "LIMIT 1 ;";
  doquery( $QryUpdateFleet , 'fleets');

  SendSimpleMessage ( $CurrentUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport );

  // Colorisation du résumé de rapport pour l'attaquant
  $raport2  = "<a href=\"#\" OnClick=\"f( 'rw.php?raport=". $rid ."', '');\" >";
  $raport2 .= "<center>";
  if       ($FleetResult == "a") {
     $raport2 .= "<font color=\"red\">";
  } elseif ($FleetResult == "r") {
     $raport2 .= "<font color=\"orange\">";
  } elseif ($FleetResult == "w") {
     $raport2 .= "<font color=\"green\">";
  }
  $raport2 .= $lang['sys_mess_attack_report'] ." [". $FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."] </font></a><br /><br />";

  SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $raport2 );

  $RipsKilled = 0;
  $MoonDestroyed = 0;

  if ($FleetResult == "a" AND $Rips > '0'){
     $MoonDestChance = round((100 - sqrt($MoonSize)) * (sqrt($Rips)));
     $MoonDestChance1 = $MoonDestChance;
     if ($MoonDestChance > 99){
        $MoonDestChance = 99;
     }
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
     $DeleteMoonQry1  = "DELETE FROM {{table}} WHERE `id` = '".$QryGalaxy['id_luna']."';";
     doquery($DeleteMoonQry1, 'lunas');

     $DeleteMoonQry2  = "DELETE FROM {{table}} WHERE `id` ='".$TargetPlanet['id']."';";
     doquery($DeleteMoonQry2, 'planets');

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
  $message .= $lang['sys_chance_moon_destroy'].$MoonDestChance."%. <br>".$lang['sys_chance_rips_destroy'].$RipDestChance."%";

$message .= "<br>MoonDestChance = " . $MoonDestChance1;
$message .= "<br>MoonSize = " . $MoonSize;
$message .= "<br>MoonName = " . $MoonName;

  SendSimpleMessage ( $CurrentUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );
  SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_moon_destruction_report'], $message );

}
?>