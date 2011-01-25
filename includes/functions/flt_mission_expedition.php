<?php

/**
 * MissionCaseExpedition.php
 *
 * version 2.0 returns results for new fleet handler
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function flt_mission_expedition($fleet_row)
{
  global $lang, $pricelist, $sn_data;

  $FleetOwner = $fleet_row['fleet_owner'];
  $MessSender = $lang['sys_mess_qg'];
  $MessTitle  = $lang['sys_expe_report'];

  if($fleet_row['fleet_mess'] != 0)
  {
    if($fleet_row['fleet_end_time'] <= time())
    {
      SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_time'], 15, $MessSender, $MessTitle, $lang['sys_expe_back_home'] );
      return RestoreFleetToPlanet($fleet_row, true);
    }
    return CACHE_NOTHING;
  }

  if ($fleet_row['fleet_end_stay'] > time())
  {
    return CACHE_NOTHING;
  };

  // La Flotte vient de finir son exploration
  // Table de ratio de points par type de vaisseau
  $PointsFlotte = array(
    202 => 1.0,  // 'Petit transporteur'
    203 => 1.5,  // 'Grand transporteur'
    204 => 0.5,  // 'Chasseur léger'
    205 => 1.5,  // 'Chasseur lourd'
    206 => 2.0,  // 'Croiseur'
    207 => 2.5,  // 'Vaisseau de bataille'
    208 => 0.5,  // 'Vaisseau de colonisation'
    209 => 1.0,  // 'Recycleur'
    210 => 0.01, // 'Sonde espionnage'
    211 => 3.0,  // 'Bombardier'
    212 => 0.0,  // 'Satellite solaire'
    213 => 3.5,  // 'Destructeur'
    214 => 5.0,  // 'Etoile de la mort'
    215 => 3.2,  // 'Traqueur'
  );

  // Table de ratio de gains en nombre par type de vaisseau
  $RatioGain = array (
    202 => 0.1,     // 'Petit transporteur'
    203 => 0.1,     // 'Grand transporteur'
    204 => 0.1,     // 'Chasseur léger'
    205 => 0.5,     // 'Chasseur lourd'
    206 => 0.25,    // 'Croiseur'
    207 => 0.125,   // 'Vaisseau de bataille'
    208 => 0.5,     // 'Vaisseau de colonisation'
    209 => 0.1,     // 'Recycleur'
    210 => 0.1,     // 'Sonde espionnage'
    211 => 0.0625,  // 'Bombardier'
    212 => 0.0,     // 'Satellite solaire'
    213 => 0.0625,  // 'Destructeur'
    214 => 0.03125, // 'Etoile de la mort'
    215 => 0.0625,  // 'Traqueur'
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
  $Hasard = rand(0, 10);

  $MessSender = "{$lang['sys_mess_qg']} ({$Hasard})";

  if ($Hasard < 3) {
    // Pas de bol, on les mange tout crus
    $Hasard     += 1;
    $LostAmount  = (($Hasard * 33) + 1) / 100;

    // Message pour annoncer la bonne mauvaise nouvelle
    if ($LostAmount == 100) {
      // Supprimer effectivement la flotte
      SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $lang['sys_expe_blackholl_2'] );
      doquery ("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    } else {
      foreach ($LaFlotte as $Ship => $Count) {
        $LostShips[$Ship] = intval($Count * $LostAmount);
        $NewFleetArray   .= $Ship.','. ($Count - $LostShips[$Ship]) .';';
      }

      doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}';");
      SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $lang['sys_expe_blackholl_1'] );
    }

  } elseif ($Hasard == 3) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    rpg_points_change($fleet_row['fleet_owner'], 1, 'Expedition Bonus');
    SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $lang['sys_expe_nothing_1'] );
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
      SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $Message );
    }
  } elseif ($Hasard == 7) {
    // Ah un tour pour rien
    doquery("UPDATE {{fleets}} SET `fleet_mess` = '1' WHERE `fleet_id` = {$fleet_row['fleet_id']}");
    SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $lang['sys_expe_nothing_2'] );
  } elseif ($Hasard >= 8 && $Hasard < 11) {
    // Gain de vaisseaux
    $FoundChance = $FleetPoints / $FleetCount;
    for ($Ship = 202; $Ship < 216; $Ship++) {
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
    foreach ($FoundShip as $Ship => $Count) {
      if ($Count != 0) {
        $FoundShipMess   .= "{$Count} {$lang['tech'][$Ship]},";
      }
    }

    doquery("UPDATE {{fleets}} SET `fleet_array` = '{$NewFleetArray}', `fleet_mess` = '1' WHERE `fleet_id` = '{$fleet_row['fleet_id']}' LIMIT 1;");
    $Message = "{$lang['sys_expe_found_ships']}{$FoundShipMess}";
    SendSimpleMessage ( $FleetOwner, '', $fleet_row['fleet_end_stay'], 15, $MessSender, $MessTitle, $Message );
  }

  return CACHE_FLEET | CACHE_USER_SRC;
}

?>
