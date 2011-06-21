<?php

/**
 * MissionCaseExpedition.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_explore($mission_data)
{
  global $lang, $pricelist, $sn_data;

  $fleet_row = $mission_data['fleet'];

  $FleetOwner = $fleet_row['fleet_owner'];
  $MessSender = $lang['sys_mess_qg'];
  $MessTitle  = $lang['sys_expe_report'];

  // La Flotte vient de finir son exploration
  // Table de ratio de points par type de vaisseau
  $PointsFlotte = array(
    SHIP_CARGO_SMALL     => 1.0,
    SHIP_CARGO_BIG       => 1.5,
    SHIP_CARGO_SUPER     => 1.0,
    SHIP_FIGHTER_LIGHT   => 0.5,
    SHIP_FIGHTER_HEAVY   => 1.5,
    SHIP_FIGHTER_ASSAULT => 3.0,
    SHIP_DESTROYER       => 2.0,
    SHIP_CRUISER         => 2.5,
    SHIP_COLONIZER       => 0.5,
    SHIP_RECYCLER        => 1.0,
    SHIP_SPY             => 0.0,
    SHIP_BOMBER          => 3.0,
    SHIP_SATTELITE_SOLAR => 0.0,
    SHIP_DESTRUCTOR      => 3.5,
    SHIP_DEATH_STAR      => 5.0,
    SHIP_BATTLESHIP      => 3.2,
    SHIP_SUPERNOVA       => 9.9,
  );

  // Table de ratio de gains en nombre par type de vaisseau
  $RatioGain = array(
    SHIP_CARGO_SMALL     => 0.1,
    SHIP_CARGO_BIG       => 0.1,
    SHIP_FIGHTER_LIGHT   => 0.1,
    SHIP_FIGHTER_HEAVY   => 0.05,
    SHIP_FIGHTER_ASSAULT => 0.0125,
    SHIP_DESTROYER       => 0.25,
    SHIP_CRUISER         => 0.125,
    SHIP_COLONIZER       => 0.05,
    SHIP_CARGO_SUPER     => 0.05,
    SHIP_RECYCLER        => 0.1,
    SHIP_SPY             => 0.1,
    SHIP_BOMBER          => 0.0625,
    SHIP_SATTELITE_SOLAR => 0.0,
    SHIP_DESTRUCTOR      => 0.0625,
    SHIP_DEATH_STAR      => 0.03125,
    SHIP_BATTLESHIP      => 0.0625,
    SHIP_SUPERNOVA       => 0.00125,
  );

  $FleetStayDuration = ($fleet_row['fleet_end_stay'] - $fleet_row['fleet_start_time']) / 3600;

  // Initialisation du contenu de la Flotte
  $farray = explode(';', $fleet_row['fleet_array']);
  foreach ($farray as $Item => $Group) {
    if ($Group != '') {
      $Class = explode (',', $Group);
      $TypeVaisseau = $Class[0];
      $NbreVaisseau = $Class[1];

      $LaFlotte[$TypeVaisseau] = $NbreVaisseau;

      //On calcul les ressources maximum qui peuvent être récupéré
      $FleetCapacity += $pricelist[$TypeVaisseau]['capacity'];
      // Maintenant on calcul en points toute la flotte
      $FleetPoints   += ($NbreVaisseau * $PointsFlotte[$TypeVaisseau]);
    }
  }

  // Espace deja occupé dans les soutes si ce devait etre le cas
  $FleetUsedCapacity  = $fleet_row['fleet_resource_metal'] + $fleet_row['fleet_resource_crystal'] + $fleet_row['fleet_resource_deuterium'];
  $FleetCapacity     -= $FleetUsedCapacity;

  //On récupère le nombre total de vaisseaux
  $FleetCount = $fleet_row['fleet_amount'];

  // Bon on les mange comment ces explorateurs ???
  $Hasard = mt_rand(0, 10);

  $MessSender = "{$lang['sys_mess_qg']} ({$Hasard})";

  if ($Hasard < 3) {
    // Pas de bol, on les mange tout crus
    $Hasard     += 1;
    $LostAmount  = (($Hasard * 33) + 1) / 100;

    // Message pour annoncer la bonne mauvaise nouvelle
    if ($LostAmount == 100) {
      // Supprimer effectivement la flotte
      msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $lang['sys_expe_blackholl_2'] );
      doquery ("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    } else {
      foreach ($LaFlotte as $Ship => $Count) {
        $LostShips[$Ship] = intval($Count * $LostAmount);
        $NewFleetArray   .= $Ship.','. ($Count - $LostShips[$Ship]) .';';
      }

      doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}';");
      msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $lang['sys_expe_blackholl_1'] );
    }

  } elseif ($Hasard == 3) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    rpg_points_change($fleet_row['fleet_owner'], 1, 'Expedition Bonus');
    msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $lang['sys_expe_nothing_1'] );
  } elseif ($Hasard >= 4 && $Hasard < 7) {
    // Gains de ressources
    if ($FleetCapacity > 5000) {
      $MinCapacity = $FleetCapacity - 5000;
      $MaxCapacity = $FleetCapacity;
      $FoundGoods  = rand($MinCapacity, $MaxCapacity);
      $FoundMetal  = intval($FoundGoods / 2);
      $FoundCrist  = intval($FoundGoods / 4);
      $FoundDeute  = intval($FoundGoods / 6);

      $QryUpdateFleet  = "UPDATE {{fleets}} SET ";
      $QryUpdateFleet .= "`fleet_resource_metal` = `fleet_resource_metal` + '{$FoundMetal}', ";
      $QryUpdateFleet .= "`fleet_resource_crystal` = `fleet_resource_crystal` + '{$FoundCrist}', ";
      $QryUpdateFleet .= "`fleet_resource_deuterium` = `fleet_resource_deuterium` + '{$FoundDeute}', ";
      $QryUpdateFleet .= "`fleet_mess` = '1'  ";
      $QryUpdateFleet .= "WHERE `fleet_id` = '{$fleet_row['fleet_id']}';";
      doquery( $QryUpdateFleet);
      $Message = sprintf($lang['sys_expe_found_goods'],
        pretty_number($FoundMetal), $lang['Metal'],
        pretty_number($FoundCrist), $lang['Crystal'],
        pretty_number($FoundDeute), $lang['Deuterium']);
      msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $Message );
    }
  } elseif ($Hasard == 7) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $lang['sys_expe_nothing_2'] );
  } elseif ($Hasard >= 8 && $Hasard < 11) {
    // Gain de vaisseaux
    $FoundChance = $FleetPoints / $FleetCount;
    foreach($sn_data['groups']['fleet'] as $Ship)
    {
      if ($LaFlotte[$Ship] != 0) {
        $FoundShip[$Ship] = round($LaFlotte[$Ship] * $RatioGain[$Ship]);
        if ($FoundShip[$Ship] > 0) {
          $LaFlotte[$Ship] += $FoundShip[$Ship];
        }
      }
    }
    $NewFleetArray = '';
    $FoundShipMess = '';
    foreach ($LaFlotte as $Ship => $Count) {
      if ($Count > 0) {
        $NewFleetArray   .= "{$Ship},{$Count};";
      }
    }

    if($FoundShip)
    {
      foreach ($FoundShip as $Ship => $Count)
      {
        if ($Count != 0)
        {
          $FoundShipMess   .= "{$Count} {$lang['tech'][$Ship]},";
        }
      }
    }

    doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    $Message = "{$lang['sys_expe_found_ships']}{$FoundShipMess}";
    msg_send_simple_message ( $FleetOwner, '', $fleet_row['fleet_end_stay'], MSG_TYPE_EXPLORE, $MessSender, $MessTitle, $Message );
  }

  return CACHE_FLEET | CACHE_USER_SRC;
}

?>
