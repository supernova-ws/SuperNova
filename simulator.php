<?php

/**
* simulator.php
*
* @version 1.0
* @copyright 2008 by Anthony for Darkness fo Evolution
*
* Script by Anthony
*
* Template for Sonyedorlys converter.
*
* Hevily modified by Gorlum for http://ogame.triolan.com.ua
*
* [*] Many optimizations
* [*] Added ACS support
* [*] Now fully unified with combat engine and removed duplicate code
*/

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if(isset($_POST['submit'])) {

  // !-------------------------------------------------------------------------------------------------------------------------------------! //

  // Lets get fleet.
  // ACS function: put all fleet into an array
  $attackFleets = array();

//Generic
  for ($fleet_id_mr = 1; $fleet_id_mr < 2; $fleet_id_mr++) {
    $fleet_code[$fleet_id_mr] = '';
    $fleet_count[$fleet_id_mr] = '';
    for ($i = 200; $i < 300; $i++) {
      $fleet_us_mr = $_POST['fleet_us'];
      if($fleet_us_mr[$fleet_id_mr][$i] > 0){
        $fleet_code[$fleet_id_mr]  .= $i.",".$fleet_us_mr[$fleet_id_mr][$i].";";
        $fleet_count[$fleet_id_mr] += $fleet_us_mr[$fleet_id_mr][$i];
      }
    }

    $attackFleets[$fleet_id_mr]['fleet'] = $attackFleets_fleet_mr;
    $attackFleets[$fleet_id_mr]['fleet']['fleet_id'] = $fleet_id_mr;
    $attackFleets[$fleet_id_mr]['fleet']['fleet_owner'] = $fleet_id_mr;
    $attackFleets[$fleet_id_mr]['fleet']['fleet_amount'] = $fleet_count[$fleet_id_mr];
    $attackFleets[$fleet_id_mr]['fleet']['fleet_array'] = $fleet_code[$fleet_id_mr];

    $rpg_amiral_us_mr    = $_POST['rpg_amiral_us'];
    $defence_tech_us_mr  = $_POST['defence_tech_us'];
    $shield_tech_us_mr   = $_POST['shield_tech_us'];
    $military_tech_us_mr = $_POST['military_tech_us'];

    $attackFleets[$fleet_id_mr]['user']['rpg_amiral']    = $rpg_amiral_us_mr[$fleet_id_mr];
    $attackFleets[$fleet_id_mr]['user']['defence_tech']  = $defence_tech_us_mr[$fleet_id_mr];
    $attackFleets[$fleet_id_mr]['user']['shield_tech']   = $shield_tech_us_mr[$fleet_id_mr];
    $attackFleets[$fleet_id_mr]['user']['military_tech'] = $military_tech_us_mr[$fleet_id_mr];

    $attackFleets[$fleet_id_mr]['detail'] = array();
    $temp = explode(';', $attackFleets[$fleet_id_mr]['fleet']['fleet_array']);
    foreach ($temp as $temp2) {
      //!! check line below!!
      $temp2 = explode(',', $temp2);
      if ($temp2[0] < 100) continue;
      if (!isset($attackFleets[$fleet_id_mr]['detail'][$temp2[0]])) $attackFleets[$fleet_id_mr]['detail'][$temp2[0]] = 0;
      $attackFleets[$fleet_id_mr]['detail'][$temp2[0]] += $temp2[1];
    }
  }

  // !---------------------------------------------------------------------------------------------------------------------------!//

  //Lets get Defense
  $defense = array();

  $rpg_amiral_them_mr    = $_POST['rpg_amiral_them'];
  $defence_tech_them_mr  = $_POST['defence_tech_them'];
  $shield_tech_them_mr   = $_POST['shield_tech_them'];
  $military_tech_them_mr = $_POST['military_tech_them'];

  $defense[0]['user']['rpg_amiral']    = $rpg_amiral_them_mr[0];
  $defense[0]['user']['defence_tech']  = $defence_tech_them_mr[0];
  $defense[0]['user']['shield_tech']   = $shield_tech_them_mr[0];
  $defense[0]['user']['military_tech'] = $military_tech_them_mr[0];


  $defense[0]['def'] = array();
  for ($i = 200; $i < 500; $i++) {
    $fleet_them_mr = $_POST['fleet_them'];
    if($fleet_them_mr[0][$i] > 0){
      $defense[0]['def'][$i] = $fleet_them_mr[0][$i];
    }
  }

  $TargetPlanet = array(
    'metal'     => intval($_POST['metal']),
    'crystal'   => intval($_POST['crystal']),
    'deuterium' => intval($_POST['deuterium']));

  $replay = serialize(array($attackFleets, $defense));
}

if(isset($_GET['replay'])) {
  $replay       = $_GET['replay'];
  $unpacked     = unserialize($replay);
  $attackFleets = &$unpacked[0];
  $defense      = &$unpacked[1];
}

if(is_array($attackFleets)){
  // Lets calcualte attack...
  $start = microtime(true);
  $result = calculateAttack($attackFleets, $defense, true);
  $totaltime = microtime(true) - $start;

  // !G+ calculating loot per attacking fleet
  $loot = BE_calculatePostAttacker($TargetPlanet, $attackFleets, $result, true);

  // Calculating Moon Chance
  $MoonChance = BE_calculateMoonChance($result);

  $formatted_cr = formatCR($result,$loot['looted'],$MoonChance,"",$totaltime);

  // Well lets just copy rw.php code. If I am showing a cr why re-inent the wheel???
  $Page  = "<html>";
  $Page .= "<head>";
  $Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$dpath."/formate.css\">";
  $Page .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=windows-1251\" />";
  $Page .= "</head>";
  $Page .= "<body>";
  $Page .= "<center>";

  //OK, one change, we won't be getting cr from datbase, instead we will be generating it directly, lets skip the database stage, this is what get generated and put in the database.
  $report = stripslashes($formatted_cr['html']);
  foreach ($lang['tech_rc'] as $id => $s_name) {
    $str_replace1  = array("[ship[".$id."]]");
    $str_replace2  = array($s_name);
    $report = str_replace($str_replace1, $str_replace2, $report);
  }
  $no_fleet = "<table border=1 align=\"center\"><tr><th>Тип</th></tr><tr><th>Всего</th></tr><tr><th>Оружие</th></tr><tr><th>Щиты</th></tr><tr><th>Броня</th></tr></table>";
  $destroyed = "<table border=1 align=\"center\"><tr><th><font color=\"red\"><strong>Уничтожены!</strong></font></th></tr></table>";
  $str_replace1  = array($no_fleet);
  $str_replace2  = array($destroyed);
  $report = str_replace($str_replace1, $str_replace2, $report);
  $Page .= $report;

  $Page .= "<br /><br />";
  //We we aren't gonna chare this reoprt because we cheated so it acutally doesn't exist.

  $Page .= '<a href=simulator.php?replay=' . $replay .'><font color=red>';
  $Page .= "Sorry, this report CAN be shared!";
  $Page .= "</font></a>";

  $Page .= "<br /><br />";
  $Page .= "</center>";
  $Page .= "</body>";
  $Page .= "</html>";

  echo $Page;
}else{
// Now its Sonyedorlys input form. Many thanks for allowing me to use it. (Slightly edited)
  $parse['military'] = 0;
  $parse['defence'] = 0;
  $parse['shield'] = 0;
  if($user['military_tech'] != '') $parse['military'] = $user['military_tech'];
  if($user['defence_tech'] != '') $parse['defence'] = $user['defence_tech'];
  if($user['shield_tech'] != '') $parse['shield'] = $user['shield_tech'];
  $parse['metal'] = 0;
  $parse['crystal'] = 0;
  $parse['deuterium'] = 0;
  for ($SetItem = 109; $SetItem <= 111; $SetItem++) $parse[$SetItem] = 0;
  for ($SetItem = 200; $SetItem <= 500; $SetItem++) $parse[$SetItem] = 0;
  if($_GET['raport'] != '') {
    $esprep = mysql_fetch_assoc(doquery("SELECT message_text FROM {{table}} WHERE `message_id` = '".$_GET['raport']."'", 'messages'));
    $esprep = $esprep['message_text'];
    $esprep = preg_replace("/<(.*?)>/","\n", $esprep);
    //echo $esprep;
    preg_match("/\[(.*?):(.*?):(.*?)\]/", $esprep, $matches);
    $parse['target_galaxy'] = $matches[1];
    $parse['target_system'] = $matches[2];
    $parse['target_planet'] = $matches[3];
    preg_match("/Metal\n\n(.*?)\n\n&nbsp;\n\nCrystal\n\n\n(.*?)\n\n\n\nDeuterium\n\n(.*?)\n\n&nbsp;/", $esprep, $matches);
    $parse['metal'] = $matches[1];
    $parse['crystal'] = $matches[2];
    $parse['deuterium'] = $matches[3];
    for ($SetItem = 109; $SetItem <= 111; $SetItem++) {
      if($lang["tech"][$SetItem] != "" && strpos($lang["tech"][$SetItem], $esprep) != -1) {
        preg_match("/".$lang["tech"][$SetItem]."\n\n(.*?)\n/", $esprep, $matches);
        if($matches[1] != '') $parse[$SetItem] = $matches[1];
        else $parse[$SetItem] = 0;
      } else $parse[$SetItem] = 0;
    }
    for ($SetItem = 200; $SetItem < 500; $SetItem++) {
      if($lang["tech"][$SetItem] != "" && strpos($lang["tech"][$SetItem], $esprep) != -1) {
        preg_match("/".$lang["tech"][$SetItem]."\n\n(.*?)\n/", $esprep, $matches);
        if($matches[1] != '') $parse[$SetItem] = $matches[1];
        else $parse[$SetItem] = 0;
      } else $parse[$SetItem] = 0;
    }
  }
  $page = "<form action='simulator.php' method='post'><center><table><tr><td>Combat Simulator<br />";
  $page .= "<table border=1 width=100%><tr><td class=\"c\">&nbsp;</td><td class=\"c\">Атакующий</td><td class=\"c\">Обороняющийся</td></tr>";
  $page .= "<tr><td class=\"c\" colspan=\"3\">Исследование</td></tr>";
  $page .= "<tr><th>Оружие</th><th><input type='text' name='military_tech_us[1]' value='".$parse['military']."'></th><th><input type='text' name='military_tech_them[0]' value='".$parse[109]."'></th></tr>";
  $page .= "<tr><th>Броня</th><th><input type='text' name='defence_tech_us[1]' value='".$parse['defence']."'></th><th><input type='text' name='defence_tech_them[0]' value='".$parse[110]."'></th></tr>";
  $page .= "<tr><th>Щиты</th><th><input type='text' name='shield_tech_us[1]' value='".$parse['shield']."'></th><th><input type='text' name='shield_tech_them[0]' value='".$parse[111]."'></th></tr>";
  for ($SetItem = 200; $SetItem < 500; $SetItem++) {
    if($lang["tech"][$SetItem] != "") {
      if(floor($SetItem/100)*100 == $SetItem) $page .= "<tr><td class=\"c\" colspan=\"3\">".$lang["tech"][$SetItem]."</td></tr>";
      else {
        $page .= "<tr><th>".$lang["tech"][$SetItem]."</th>";
        if($SetItem < 400)
          $page .= "<th><input type='text' name='fleet_us[1][".$SetItem."]' value='0'></th><th><input type='text' name='fleet_them[0][".$SetItem."]' value='".$parse["$SetItem"]."'></th></tr>";
        else
          $page .= "<th>&nbsp;</th><th><input type='text' name='fleet_them[0][".$SetItem."]' value='".$parse["$SetItem"]."'></th></tr>";
      }
    }
  }
  $page .= "<tr><td class=\"c\" colspan=\"3\">Ресурсы</td></tr>";
  $page .= "<tr><th>Металл</th><th>&nbsp;</th><th><input type='text' name='metal' value='".$parse['metal']."'></th></tr>";
  $page .= "<tr><th>Кристалл</th><th>&nbsp;</th><th><input type='text' name='crystal' value='".$parse['crystal']."'></th></tr>";
  $page .= "<tr><th>Дейтерий</th><th>&nbsp;</th><th><input type='text' name='deuterium' value='".$parse['deuterium']."'></th></tr>";
  $page .= "<tr><th colspan='3'><input type='submit' name='submit' value='Simulate'></th></tr>";
  $page .= "</table></center></form>";
  display($page, "Combat Simulator", false);
}

function rp($input) {
  return str_replace(".", "", $input);
}
?>
