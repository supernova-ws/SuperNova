<?php

/**
 * jumpgate.php
 *
 * Jump Gate interface, I presume
 *
 * @version 1.0st Security checks & tests by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$parse = $lang;

$TargetPlanet = sys_get_param_id('jmpto');

if($TargetPlanet)
{
  doquery('START TRANSACTION');
  $planetrow = doquery("SELECT * FROM {{planets}} WHERE id = {$planetrow['id']} LIMIT 1 FOR UPDATE;", true);
  $NextJumpTime = uni_get_time_to_jump($planetrow);
  // Dit monsieur, j'ai le droit de sauter ???
  if(!$NextJumpTime)
  {
    // Dit monsieur, ou je veux aller ca existe ???
    $TargetGate   = doquery ( "SELECT `id`, `sprungtor`, `last_jump_time` FROM {{planets}} WHERE `id` = '{$TargetPlanet}'  LIMIT 1 FOR UPDATE;", true);
    // Dit monsieur, ou je veux aller y a une porte de saut ???
    if ($TargetGate['sprungtor'] > 0) {
      $NextDestTime = uni_get_time_to_jump ( $TargetGate );
      // Dit monsieur, chez toi aussi peut y avoir un saut ???
      if(!$NextDestTime)
      {
        // Bon j'ai eu toutes les autorisations, donc je compte les radis !!!
        $ShipArray   = array();
        $SubQueryOri = "";
        $SubQueryDes = "";
//        for ( $Ship = 200; $Ship < 300; $Ship++ )
        foreach($sn_data['groups']['fleet'] as $Ship)
        {
          $ShipLabel = "c". $Ship;
          $ShipNum = floor(floatval($_POST[$ShipLabel]));
          if ( $ShipNum > $planetrow[ $sn_data[$Ship]['name'] ] )
          {
            $ShipArray[ $Ship ] = $planetrow[ $sn_data[$Ship]['name'] ];
          } else {
            $ShipArray[ $Ship ] = $ShipNum;
          }
          if ($ShipArray[ $Ship ] > 0) {
            $SubQueryOri .= "`". $sn_data[$Ship]['name'] ."` = `". $sn_data[$Ship]['name'] ."` - '". $ShipArray[ $Ship ] ."', ";
            $SubQueryDes .= "`". $sn_data[$Ship]['name'] ."` = `". $sn_data[$Ship]['name'] ."` + '". $ShipArray[ $Ship ] ."', ";
          }
        }
        // Dit monsieur, y avait quelque chose a envoyer ???
        if ($SubQueryOri != "") {
          // Soustraction de la lune de depart !
          $QryUpdateOri  = "UPDATE {{planets}} SET ";
          $QryUpdateOri .= $SubQueryOri;
          $QryUpdateOri .= "`last_jump_time` = '". $time_now ."' ";
          $QryUpdateOri .= "WHERE ";
          $QryUpdateOri .= "`id` = '". $planetrow['id'] ."';";
          doquery ( $QryUpdateOri);

          // Addition à la lune d'arrivée !
          $QryUpdateDes  = "UPDATE {{planets}} SET ";
          $QryUpdateDes .= $SubQueryDes;
          $QryUpdateDes .= "`last_jump_time` = '". $time_now ."' ";
          $QryUpdateDes .= "WHERE ";
          $QryUpdateDes .= "`id` = '". $TargetGate['id'] ."';";
          doquery ( $QryUpdateDes);

          // Deplacement vers la lune d'arrivée
          $QryUpdateUsr  = "UPDATE {{users}} SET ";
          $QryUpdateUsr .= "`current_planet` = '". $TargetGate['id'] ."' ";
          $QryUpdateUsr .= "WHERE ";
          $QryUpdateUsr .= "`id` = '". $user['id'] ."';";
          doquery ( $QryUpdateUsr);

          $planetrow['last_jump_time'] = $time_now;
          $RetMessage    = $lang['gate_jump_done'] ." - ". pretty_time(uni_get_time_to_jump($planetrow));
        } else {
          $RetMessage = $lang['gate_wait_data'];
        }
      } else {
        $RetMessage = $lang['gate_wait_dest'] ." - ". pretty_time($NextDestTime);
      }
    } else {
      $RetMessage = $lang['gate_no_dest_g'];
    }
  } else {
    $RetMessage = $lang['gate_wait_star'] ." - ". pretty_time($NextJumpTime);
  }
  doquery('COMMIT;');
  message ($RetMessage, $lang['tech'][STRUC_MOON_GATE], "jumpgate.php", 10);
} else {
  $GateTPL = gettemplate('gate_fleet_table', true);
  if($planetrow[$sn_data[STRUC_MOON_GATE]['name']] > 0)
  {
    $NextJumpTime = uni_get_time_to_jump($planetrow);
    $parse['GATE_JUMP_REST_TIME'] = $NextJumpTime;
    $parse['gate_start_name'] = $planetrow['name'];
    $parse['gate_start_link'] = uni_render_coordinates_href($planetrow, '', 3);
    $parse['TIME_NOW'] = $time_now;

    $QrySelectMoons = "SELECT * FROM {{planets}} WHERE `planet_type` = '3' AND `id_owner` = '" . $user['id'] . "';";
    $MoonList = doquery($QrySelectMoons);
    $Combo = "";
    while ($CurMoon = mysql_fetch_assoc($MoonList))
    {
      if ($CurMoon['id'] != $planetrow['id'])
      {
        $NextJumpTime = uni_get_time_to_jump($CurMoon);
        if ($CurMoon[$sn_data[STRUC_MOON_GATE]['name']] >= 1)
        {
          $Combo .= "<option value=\"" . $CurMoon['id'] . "\">[" . $CurMoon['galaxy'] . ":" . $CurMoon['system'] . ":" . $CurMoon['planet'] . "] " . $CurMoon['name'] . ' ' . ($NextJumpTime ? pretty_time($NextJumpTime) : '') . "</option>\n";
        }
      }
    }
    $parse['gate_dest_moons'] = $Combo; // BuildJumpableMoonCombo($user, $planetrow);

    $RowsTPL = gettemplate('gate_fleet_rows');
    $CurrIdx = 1;
    $ship_list = "";
    for ($Ship = 300; $Ship > 200; $Ship--)
    {
      if ($sn_data[$Ship]['name'] != "")
      {
        if ($planetrow[$sn_data[$Ship]['name']] > 0)
        {
          $bloc['idx'] = $CurrIdx;
          $bloc['fleet_id'] = $Ship;
          $bloc['fleet_name'] = $lang['tech'][$Ship];
          $bloc['fleet_max'] = pretty_number($planetrow[$sn_data[$Ship]['name']]);
          $bloc['gate_ship_dispo'] = $lang['gate_ship_dispo'];
          $ship_list .= parsetemplate($RowsTPL, $bloc);
          $CurrIdx++;
        }
      }
    }

    $parse['gate_fleet_rows'] = $ship_list;
    $page = parsetemplate($GateTPL, $parse);

    display($page, $lang['tech'][STRUC_MOON_GATE]);
  }
  else
  {
    message($lang['gate_no_src_ga'], $lang['tech'][STRUC_MOON_GATE], "overview.php", 10);
  }
}


// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Version from scrap .. y avait pas ... bin maintenant y a !!

?>
