<?php

/**
 * officer.php
 * Handles officer hire
 *
 * @package roleplay
 * @version 2.0
 *
 * Revision History
 * ================
 * 2.0 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Utilizes PTE
 *
 * 1.2 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Security checks & tests
 *
 * 1.1 copyright 2008 By Chlorel for XNova
 *   [~] Réécriture Chlorel pour integration complete dans XNova
 *
 * 1.0 copyright 2008 By Tom1991 for XNova
 *   [!] Version originelle (Tom1991)
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.{$phpEx}");
}

check_urlaubmodus ($user);

$mode = $_GET['mode'];
$offi = $_GET['offi'];

includeLang('infos');

// Vérification que le joueur n'a pas un nombre de points négatif
//  if ($user['rpg_points'] < 0) {
//    doquery("UPDATE {{table}} SET `rpg_points` = '0' WHERE `id` = '". $user['id'] ."';", 'users');
//  }
//darkmater constant
$darkmater_cost = $config->rpg_officer;
// Si recrutement d'un officier
if ($mode == 2) {
  if ($user['rpg_points'] >= $darkmater_cost) {
    $Selected    = $offi;
    if ( in_array($Selected, $reslist['mercenaries']) ) {
      $Result = IsOfficierAccessible ( $user, $Selected );
      if ( $Result == 1 ) {
        $user[$resource[$Selected]] += 1;
        $user['rpg_points']         -= $darkmater_cost;
        doquery( "UPDATE {{users}} SET `{$resource[$Selected]}` = `{$resource[$Selected]}` + 1 WHERE `id` = '{$user['id']}';");
        rpg_pointsAdd($user['id'], -($darkmater_cost), "Spent for officer {$lang['info'][$Selected]['name']} ID {$Selected}");
        $Message = $lang['off_recruited'];
        Header("Location: officer.php");
      } elseif ( $Result == -1 ) {
        $Message = $lang['off_maxed_out'];
      } elseif ( $Result == 0 ) {
        $Message = $lang['off_not_available'];
      }
    }
  }
  else
  {
    $Message = $lang['off_no_points'];
  }
  message($lang['off_no_points'], $lang['tech'][600], "officer.{$phpEx}", 5);
}
else
{
  $template = gettemplate('officer', true);
  foreach ($sn_groups['mercenaries'] as $mercenary_id) {
    $Result = IsOfficierAccessible ( $user, $mercenary_id );
    if($Result)
    {
      $template->assign_block_vars('officer', array(
        'ID'          => $mercenary_id,
        'NAME'        => $lang['tech'][$mercenary_id],
        'DESCRIPTION' => $lang['info'][$mercenary_id]['description'],
        'LEVEL'       => $user[$resource[$mercenary_id]],
        'LEVEL_MAX'   => $sn_data[$mercenary_id]['max'],
        'BONUS'       => $sn_data[$mercenary_id]['bonus'],
        'BONUS_TYPE'  => $sn_data[$mercenary_id]['bonus_type'],
        'CAN_BUY'     => $Result,
      ));
    }
  }

  $template->assign_var('DM_COST', $darkmater_cost);

  display($template, $lang['tech'][600]);
}

?>
