<?php

  /**
   * formatCR.php by Anthony (MadnessRed) [http://madnessred.co.cc/]
   *
   * Copyright (c) MadnessRed 2008.
   *
   * made from Scratch by MadnessRed to work with the ACS Combat engine.
   *
   * The files (below line 15) is under the GPL liscence, and the file license.txt must be included with this file.
   *
   * You may not edit this comment block. You may not copy any part of this file into any other file with out copying this comment block with it and placing it above any code there might be.
  */

  /*
    Partial Copyright (c) Gorlum 2010
    Rewrite and optimization by Gorlum for http://ogame.triolan.com.ua
  */

function formatCR_Fleet(&$dataInc, &$data_prev, $isAttacker, $isLastRound)
{
  global $lang;

  if ($isAttacker){
    $dataA = $dataInc['attackers'];
    $dataB = $dataInc['infoA'];
    $data_prevA = $data_prev['attackers'];
    $strField = 'detail';
    $strType = $lang['sys_attacker'];
  }else{
    $dataA = $dataInc['defenders'];
    $dataB = $dataInc['infoD'];
    $data_prevA = $data_prev['defenders'];
    $strField = 'def';
    $strType = $lang['sys_defender'];
    $Coord = $dataInc['defCoord'];
  };

  foreach($dataA as $fleet_id => $data2)
  {
    //Player Information
    $weap = ($data2['user']['military_tech'] * 10);
    $shie = ($data2['user']['shield_tech'] * 10);
    $armr = ($data2['user']['defence_tech'] * 10);

    //And html output player information
    $fl_info1  = "<table><tr><th>";

    if($isAttacker)
    {
      $Coord = "[".
        intval($data2['fleet']['fleet_start_galaxy']).":".
        intval($data2['fleet']['fleet_start_system']).":".
        intval($data2['fleet']['fleet_start_planet'])."]";
    }

    $fl_info1 .= "{$strType} {$data2['user']['username']} ({$Coord})<br />";
    $fl_info1 .= "{$lang['sys_ship_weapon']}: {$weap}% {$lang['sys_ship_shield']}: {$shie} {$lang['sys_ship_armour']}: {$armr}%";

    //Start the table rows.
    $ships1  = "<tr><th>{$lang['sys_ship_type']}</th>";
    $count1  = "<tr><th>{$lang['sys_ship_count']}</th>";
    $weap1  = "<tr><th>{$lang['sys_ship_weapon']}</th>";
    $shields1  = "<tr><th>{$lang['sys_ship_shield']}</th>";
    $armour1  = "<tr><th>{$lang['sys_ship_armour']}</th>";

    //And now the data columns "foreach" ship
    if(!is_array($data2[$strField]))
    {
      $data2[$strField] = array();
    }
    foreach($data2[$strField] as $ship_id => $ship_count1)
    {
      if ($ship_count1 > 0)
      {
//        $ships1 .= "<th>[ship[".$ship_id."]]</th>";
        $ships_destroyed = !empty($data_prevA) ? $ship_count1 - $data_prevA[$fleet_id][$strField][$ship_id] : 0;
        $ships1 .= "<th>{$lang['tech'][$ship_id]}</th>";
        $count1 .= "<th>".$ship_count1." ".($ships_destroyed ? "<span style=\"color:red\">{$ships_destroyed}</span>" : '')."</th>";

        if (!$isLastRound)
        {
          $ship_points = $dataB[$fleet_id][$ship_id];
          if ($ship_points['def'] > 0)
          {
            $weap1 .= "<th>{$ship_points['att']}</th>";
            $shields1 .= "<th>{$ship_points['shield']}</th>";
            $armour1 .= "<th>{$ship_points['def']}</th>";
          }
        }
      }
    }

    //End the table Rows
    $ships1 .= "</tr>";
    $count1 .= "</tr>";
    $weap1 .= "</tr>";
    $shields1 .= "</tr>";
    $armour1 .= "</tr>";

    //now compile what we have, ok its the first half but the rest comes later.
    $html .= $fl_info1;
    $html .= "<table border=1 align=\"center\">";
    $html .= $ships1.$count1;
    if (!$isLastRound)
      $html .= $weap1.$shields1.$armour1;
    $html .= "</table></th></tr></table><br />";
  }

  return $html;
};

function formatCR (&$result_array,&$steal_array,&$moon_int,$moon_string,&$time_float) {

  global $lang;

  $html = "";
  $bbc = "";

  if (defined('BE_DEBUG'))
  {
    global $be_debug_array;

    if($be_debug_array)
    {
      foreach($be_debug_array as $be_debug_line)
      {
        $html .= $be_debug_line;
      }
    }
  }

  //And lets start the CR. And admin message like asking them to give the cr. Nope, well moving on give the time and date ect.
  $html .= "{$lang['sys_coe_combat_start']} ".date(FMT_DATE_TIME)."<br /><br />";

  $data = $result_array['rw'][0]['attackers'];
  $dataKey = array_keys($data);
  $data = $data[$dataKey[0]]['fleet'];
  $defenderCoord = "[".intval($data['fleet_end_galaxy']).":".intval($data['fleet_end_system']).":".intval($data['fleet_end_planet'])."]";

  $rw_count = count($result_array['rw']);
  for ($round_no = 1; $round_no <= $rw_count; $round_no++) {
    $isLastRound = ($round_no == $rw_count);
    if ($isLastRound){
      $html .= "{$lang['sys_coe_combat_end']}:<br /><br />";
    }else{
      $html .= "{$lang['sys_coe_round']} ".$round_no.":<br /><br />";
    };

    //Now whats that attackers and defenders data
    $data = $result_array['rw'][$round_no-1];
    $data_prev = $round_no == 1 ? false : $result_array['rw'][$round_no-2];
    $data['defCoord'] = $defenderCoord;

    $html .= formatCR_Fleet($data, $data_prev, true, $isLastRound);
    $html .= formatCR_Fleet($data, $data_prev, false, $isLastRound);

    //HTML What happens?
    if (!$isLastRound){
      $html .= sprintf($lang['sys_coe_attacker_turn'], $data['attack']['total'], $data['defShield']);
      $html .= sprintf($lang['sys_coe_defender_turn'], $data['defense']['total'], $data['attackShield']);
    }
  }

  //ok, end of rounds, battle results now.

  //Who won?
  if ($result_array['won'] == 2){
    //Defender wins
    $result1  = $lang['sys_coe_outcome_win'];
  }elseif ($result_array['won'] == 1){
    //Attacker wins
    $result1  = $lang['sys_coe_outcome_loss'];
    $result1 .= sprintf($lang['sys_coe_outcome_loot'], $steal_array['metal'], $steal_array['crystal'], $steal_array['deuterium']);
  }else{
    //Battle was a draw
    $result1  = $lang['sys_coe_outcome_draw'];
  }



  //$html .= "<br /><br />";
  $html .= $result1;
  $html .= "<br />";

  $debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
  $debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);
  $html .= sprintf($lang['sys_coe_attacker_lost'], $result_array['lost']['att']);
  $html .= sprintf($lang['sys_coe_defender_lost'], $result_array['lost']['def']);
  $html .= sprintf($lang['sys_coe_debris_left'], $debirs_meta, $debirs_crys);
  $html .= sprintf($lang['sys_coe_moon_chance'], $moon_int);
  $html .= "{$moon_string}<br /><br />";

  $html .= sprintf($lang['sys_coe_rw_time'], $time_float);

  return array('html' => $html, 'bbc' => $bbc);
}

?>
