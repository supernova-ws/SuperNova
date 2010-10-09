<?php

/**
 * simulator.php
 *
 * 1.5st - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.5 Heavily modified by Gorlum for http://supernova.ws
 *
 *   [*] Added REPLAY ability - link to simulator results
 *   [*] Many optimizations
 *   [*] Added ACS support
 *   [*] Now fully unified with combat engine and removed duplicate code
 *
 *  @version 1.0
 * @copyright 2008 by Anthony for Darkness fo Evolution
 *
 * Script by Anthony
 *
 * Template for Sonyedorlys converter.
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

$replay = $_GET['replay'] ? $_GET['replay'] : $_POST['replay'];
$execute = intval($_GET['execute']);
$sym_attacker = $_POST['attacker'];
$sym_defender = $_POST['defender'];

if($replay)
{
  $unpacked     = sys_combatDataUnPack($replay);

  $sym_attacker = $unpacked['detail'];
  $sym_defender = $unpacked['def'];
}

if(($_GET['BE_DEBUG'] || $_POST['BE_DEBUG']) && !defined('BE_DEBUG'))
{
  define('BE_DEBUG', true);
}

if($_POST['submit'] || $execute)
{
  $replay = sys_combatDataPack($sym_attacker, 'detail');
  $replay .= sys_combatDataPack($sym_defender, 'def');pdump($sym_defender);

  $a = array(&$sym_attacker, &$sym_defender);
  foreach($a as &$sym_fleet_list)
  {
    foreach($sym_fleet_list as &$fleet)
    {
      foreach($fleet['user'] as $key => $value)
      {
        $fleet['user'][$resource[$key]] = $value;
        unset($fleet['user'][$key]);
      }

      if(is_array($fleet['detail']))
      {
        $tmp = 'detail';
      }
      else
      {
        $tmp = 'def';
      }

      foreach($fleet[$tmp] as $key => $value)
      {
        if(!$value)
        {
          unset($fleet[$tmp][$key]);
        }
      }
    }
  }

  // Lets calcualte attack...
  $start = microtime(true);
  $result = calculateAttack($sym_attacker, $sym_defender, true);
  $totaltime = microtime(true) - $start;

  // calculating loot per attacking fleet
  $loot = BE_calculatePostAttacker($sym_defender[0]['resources'], $sym_attacker, $result, true);

  // Calculating Moon Chance
  $MoonChance = BE_calculateMoonChance($result);

  $formatted_cr = formatCR($result, $loot['looted'], $MoonChance, '', $totaltime);

  // Well lets just copy rw.php code. If I am showing a cr why re-inent the wheel???
  $Page  = '<html>';
  $Page .= '<head>';
  $Page .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$dpath}/formate.css\">";
  $Page .= '<meta http-equiv="content-type" content="text/html; charset=windows-1251" />';
  $Page .= '</head>';
  $Page .= '<body>';
  $Page .= '<center>';

  //OK, one change, we won't be getting cr from datbase, instead we will be generating it directly, lets skip the database stage, this is what get generated and put in the database.
  $report = stripslashes($formatted_cr['html']);
  foreach ($lang['tech_rc'] as $id => $s_name) {
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
  $parse = $lang;

  $tmp = coe_simulatorHTMLMake(array_merge($reslist['combat'], array(109, 110, 111)));

  $parse['inputTech'] = $tmp['100'];
  $parse['inputFleet'] = $tmp['200'];
  $parse['inputDefense'] = $tmp['400'];
  $parse['res_metal'] = intval($unpacked['def'][0]['resources']['metal']);
  $parse['res_crystal'] = intval($unpacked['def'][0]['resources']['crystal']);
  $parse['res_deuterium'] = intval($unpacked['def'][0]['resources']['deuterium']);
  $parse['BE_DEBUG'] = $_GET['BE_DEBUG'];
  $page = parsetemplate(gettemplate('simulator', true), $parse);
  display($page, $lang['coe_combatSimulator'], false);
}

function rp($input)
{
  return str_replace('.', '', $input);
}
?>