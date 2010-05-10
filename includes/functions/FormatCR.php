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

function formatCR_Fleet(&$dataInc, $isAttacker, $isLastRound){
  if ($isAttacker){
    $dataA = $dataInc['attackers'];
    $dataB = $dataInc['infoA'];
    $strField = 'detail';
    $strType = 'Атакующий';
  }else{
    $dataA = $dataInc['defenders'];
    $dataB = $dataInc['infoD'];
    $strField = 'def';
    $strType = 'Обороняющийся';
    $Coord = $dataInc['defCoord'];
  };

  foreach( $dataA as $fleet_id => $data2){ //25
    //Player Info
    $weap = ($data2['user']['military_tech'] * 10);
    $shie = ($data2['user']['shield_tech'] * 10);
    $armr = ($data2['user']['defence_tech'] * 10);

    //And html output player info
    $fl_info1  = "<table><tr><th>";

    if($isAttacker){
      $Coord = "[".
        intval($data2['fleet']['fleet_start_galaxy']).":".
        intval($data2['fleet']['fleet_start_system']).":".
        intval($data2['fleet']['fleet_start_planet'])."]";
    }

    $fl_info1 .= $strType . " ".$data2['user']['username']." (".$Coord.")<br />";
/*
    $fl_info1 .= $strType . " ".$data2['user']['username']." ([".
      intval($data2['fleet']['fleet_'.$strPoint.'_galaxy']).":".
      intval($data2['fleet']['fleet_'.$strPoint.'_system']).":".
      intval($data2['fleet']['fleet_'.$strPoint.'_planet'])."])<br />";
*/
    $fl_info1 .= "Оружие: ".$weap."% Щиты: ".$shie."% Броня: ".$armr."%";

    //Start the table rows.
    $ships1  = "<tr><th>Тип корабля</th>";
    $count1  = "<tr><th>Кол-во</th>";
    $weap1  = "<tr><th>Оружие</th>";
    $shields1  = "<tr><th>Щиты</th>";
    $armour1  = "<tr><th>Броня</th>";

    //And now the data columns "foreach" ship
    foreach( $data2[$strField] as $ship_id => $ship_count1){
      if ($ship_count1 > 0){
        $ships1 .= "<th>[ship[".$ship_id."]]</th>";
        $count1 .= "<th>".$ship_count1."</th>";

        if (!$isLastRound) {
          $ship_points = $dataB[$fleet_id][$ship_id];
          if ($ship_points['def'] > 0){
            $weap1 .= "<th>".$ship_points['att']."</th>";
            $shields1 .= "<th>".$ship_points['shield']."</th>";
            $armour1 .= "<th>".$ship_points['def']."</th>";
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

  global $phpEx, $ugamela_root_path, $pricelist, $lang, $resource, $CombatCaps, $game_config;

  $html = "";
  $bbc = "";

  //And lets start the CR. And admin message like asking them to give the cr. Nope, well moving on give the time and date ect.
  $html .= "Флоты соперников встретились ".date("d-m-Y H:i:s")."<br /><br />";

  $data = $result_array['rw'][0]['attackers'];
  $dataKey = array_keys($data);
  $data = $data[$dataKey[0]]['fleet'];
  $defenderCoord = "[".intval($data['fleet_end_galaxy']).":".intval($data['fleet_end_system']).":".intval($data['fleet_end_planet']."]");

  $rw_count = count($result_array['rw']);
  for ($round_no = 1; $round_no <= $rw_count; $round_no++) {
    $isLastRound = ($round_no == $rw_count);
    if ($isLastRound){
      $html .= "Результат боя:<br /><br />";
    }else{
      $html .= "Раунд ".$round_no.":<br /><br />";
    };

    //Now whats that attackers and defenders data
    $data = $result_array['rw'][$round_no-1];
    $data['defCoord'] = $defenderCoord;

    $html .= formatCR_Fleet($data, true, $isLastRound);
    $html .= formatCR_Fleet($data, false, $isLastRound);

    //HTML What happens?
    if (!$isLastRound){
      $html .= "Атакующий делает выстрелы общей мощностью ".$data['attack']['total'].". Щиты обороняющегося поглощают ".$data['defShield']." выстрелов.<br />";
      $html .= "Обороняющийся делает выстрелы общей мощностью ".$data['defense']['total'].". Щиты атакующего поглощают ".$data['attackShield']." выстрелов.<br /><br /><br />";
    }
  }

  //ok, end of rounds, battle results now.

  //Who won?
  if ($result_array['won'] == 2){
    //Defender wins
    $result1  = "Обороняющийся выиграл битву!<br />";
  }elseif ($result_array['won'] == 1){
    //Attacker wins
    $result1  = "Атакующий выиграл битву!<br />";
    $result1 .= "Он получает ".$steal_array['metal']." металла, ".$steal_array['crystal']." кристаллов, and ".$steal_array['deuterium']." дейтерия<br />";
  }else{
    //Battle was a draw
    $result1  = "Бой закончился ничьёй.<br />";
  }



  //$html .= "<br /><br />";
  $html .= $result1;
  $html .= "<br />";

  $debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
  $debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);
  $html .= "Атакующий потерял ".$result_array['lost']['att']." единиц.<br />";
  $html .= "Обороняющийся потерял ".$result_array['lost']['def']." единиц.<br />";
  $html .= "Теперь на этих пространственных координатах находятся ".$debirs_meta." металла и ".$debirs_crys." кристаллов.<br /><br />";

  $html .= "Шанс появления луны составляет ".$moon_int."%<br />";
  $html .= $moon_string."<br /><br />";

  $html .= "Время генерации страницы ".$time_float." секунд<br />";

  //return array('html' => $html, 'bbc' => $bbc, 'extra' => $extra);
  return array('html' => $html, 'bbc' => $bbc);
}
?>