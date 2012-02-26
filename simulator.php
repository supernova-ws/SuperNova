<?php

/**
 * simulator.php
 *
 * @package combat
 * @version 1.8
 *
 * 1.8 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] PCG-compliant
 *   [~] PTE-compliant. Not using simulator_row.tpl
 *   [~] Fully rewrote REPLAY structure
 *
 * 1.7 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Enchanced REPLAY - now you can link to page when you can change simulator input data
 *
 * 1.6 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Security checks & tests
 *
 * 1.5 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [!] Now fully unified with combat engine and removed duplicate code
 *   [+] Added REPLAY ability - link to simulator results
 *   [+] Added ACS support
 *   [~] Many optimizations
 *
 * 1.0 copyright 2008 by Anthony for Darkness fo Evolution
 *   [!] Template for Sonyedorlys converter.
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if(($_GET['BE_DEBUG'] || $_POST['BE_DEBUG']) && !defined('BE_DEBUG'))
{
  define('BE_DEBUG', true);
}

require_once('includes/includes/coe_calculate_attack.php');
require_once('includes/includes/coe_simulator_helpers.php');

$replay = $_GET['replay'] ? $_GET['replay'] : $_POST['replay'];
$execute = intval($_GET['execute']);
$sym_defender = $_POST['defender'] ? $_POST['defender'] : array();
$sym_attacker = $_POST['attacker'] ? $_POST['attacker'] : array();

if($replay)
{
  $unpacked = coe_sym_decode_replay($replay);

  $sym_defender = $unpacked['D'];
  $sym_attacker = $unpacked['A'];
}
else
{
  $sym_defender = array(0 => $sym_defender);
  $sym_attacker = array(1 => $sym_attacker);
}

if($_POST['submit'] || $execute)
{
  $replay = coe_sym_encode_replay($sym_defender, 'D');
  $replay .= coe_sym_encode_replay($sym_attacker, 'A');

  $arr_combat_defender = coe_sym_to_combat($sym_defender, 'def');
  $arr_combat_attacker = coe_sym_to_combat($sym_attacker, 'detail');

  // Lets calcualte attack...

//pdump($sym_defender);

//pdump($arr_combat_attacker, '$arr_combat_attacker');
//pdump($arr_combat_defender, '$arr_combat_defender');
  $start = microtime(true);
  $result = coe_attack_calculate($arr_combat_attacker, $arr_combat_defender, true);
  $totaltime = microtime(true) - $start;
//pdump($result);

  // calculating loot per attacking fleet
  $loot = BE_calculatePostAttacker($arr_combat_defender[0]['resources'], $arr_combat_attacker, $result, true);

  // Calculating Moon Chance
  $MoonChance = uni_calculate_moon_chance($result['debree']['att'][0] + $result['debree']['def'][0] + $result['debree']['att'][1] + $result['debree']['def'][1]);

  $formatted_cr = coe_report_format($result, $loot['looted'], $MoonChance, '', $totaltime);

  // Well lets just copy rw.php code. If I am showing a cr why re-inent the wheel???
  $Page  = '<html>';
  $Page .= '<head>';
  $Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$dpath}/formate.css\">";
  $Page .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
  $Page .= '</head>';
  $Page .= '<body>';
  $Page .= '<center>';

  //OK, one change, we won't be getting cr from datbase, instead we will be generating it directly, lets skip the database stage, this is what get generated and put in the database.
  $report = stripslashes($formatted_cr['html']);
  foreach ($lang['tech'] as $id => $s_name) {
    $str_replace1  = array("[ship[{$id}]]");
    $str_replace2  = array($s_name);
    $report = str_replace($str_replace1, $str_replace2, $report);
  }
  $no_fleet = '<table border=1 align=\"center\"><tr><th>Тип</th></tr><tr><th>Всего</th></tr><tr><th>Оружие</th></tr><tr><th>Щиты</th></tr><tr><th>Броня</th></tr></table>';
  $destroyed = '<table border=1 align=\"center\"><tr><th><font color=\"red\"><strong>Уничтожены!</strong></font></th></tr></table>';
  $str_replace1  = array($no_fleet);
  $str_replace2  = array($destroyed);
  $report = str_replace($str_replace1, $str_replace2, $report);
  $Page .= $report;

  $Page .= '<br /><br />';

  $Page .= "<a href=simulator.php?execute=1&replay={$replay}><font color=red>Link to simulation result</font></a><br>";
  $Page .= "<a href=simulator.php?replay={$replay}><font color=red>Link to edit simulatioin data</font></a><br>";

  $Page .= '<br /><br /></center></body></html>';

  echo $Page;
}
else
{
  $template = gettemplate('simulator', true);
  $techs_and_officers = array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR, MRC_ADMIRAL);

  foreach($techs_and_officers as $tech_id)
  {
    if(!$sym_attacker[1][$tech_id])
    {
      $sym_attacker[1][$tech_id] = mrc_get_level($user, false, $tech_id);
    }
  }

  foreach(array_merge($techs_and_officers, $sn_data['groups']['combat'], $sn_data['groups']['resources_loot']) as $unit_id)
  {
    $tab++;

    $new_group = $unit_id - $unit_id % 100;
    if($unit_group != $new_group)
    {
      $unit_group = $new_group;
      $template->assign_block_vars('simulator', array(
        'GROUP' => $unit_group,
        'NAME'  => $lang['tech'][$unit_group],
      ));
    }

    if(in_array($unit_id, $sn_data['groups']['tech']) || $unit_id == MRC_ADMIRAL)
    {
      $value = mrc_get_level($user, $planetrow, $unit_id);
    }
    else
    {
      $value = $planetrow[$sn_data[$unit_id]['name']];
    }

    $template->assign_block_vars('simulator', array(
      'NUM'      => $tab < 9 ? "0{$tab}" : $tab,
      'ID'       => $unit_id,
      'GROUP'    => $unit_group,
      'NAME'     => $lang['tech'][$unit_id],
      'ATTACKER' => intval($sym_attacker[1][$unit_id]),
      'DEFENDER' => intval($sym_defender[0][$unit_id]),
      'VALUE'    => $value,
    ));
  }

  $template->assign_vars(array(
    'BE_DEBUG' => BE_DEBUG,
  ));

  display(parsetemplate($template, $parse), $lang['coe_combatSimulator'], false);
}

?>
