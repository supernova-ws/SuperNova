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

/**
 * IsOfficierAccessible.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Verification si l'on a le droit ou non a un officier
// Retour:
//  0 => pas les Officiers necessaires
//  1 => Tout va tres bien on peut le faire celui l�
// -1 => On pouvait le faire, mais on est d�ja au level max
function IsOfficierAccessible ($CurrentUser, $Officier) {
  global $requeriments, $resource, $pricelist;

  if (isset($requeriments[$Officier])) {
    $enabled = true;
    foreach($requeriments[$Officier] as $ReqOfficier => $OfficierLevel) {
      if ($CurrentUser[$resource[$ReqOfficier]] &&
        $CurrentUser[$resource[$ReqOfficier]] >= $OfficierLevel) {
        $enabled = 1;
      } else {
        return 0;
      }
    }
  }
  if ($CurrentUser[$resource[$Officier]] < $pricelist[$Officier]['max']  ) {
    return 1;
  } else {
    return -1;
  }
}

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
        rpg_points_change($user['id'], -($darkmater_cost), "Spent for officer {$lang['tech'][$Selected]} ID {$Selected}");
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
      $mercenary = $sn_data[$mercenary_id];
      $mercenary_bonus = $mercenary['bonus'];
      $mercenary_bonus = $mercenary_bonus>=0 ? "+{$mercenary_bonus}" : "{$mercenary_bonus}";
      switch($mercenary['bonus_type'])
      {
        case BONUS_PERCENT:
          $mercenary_bonus = "{$mercenary_bonus}% ";
        break;

        case BONUS_ADD:
        break;

        case BONUS_ABILITY:
          $mercenary_bonus = '';
        break;

        default:
        break;
      }

      $template->assign_block_vars('officer', array(
        'ID'          => $mercenary_id,
        'NAME'        => $lang['tech'][$mercenary_id],
        'DESCRIPTION' => $lang['info'][$mercenary_id]['description'],
        'DESCRIPTION_SHORT' => $lang['info'][$mercenary_id]['description_short'],
        'LEVEL'       => $user[$resource[$mercenary_id]],
        'LEVEL_MAX'   => $mercenary['max'],
        'BONUS'       => $mercenary_bonus,
        'BONUS_TYPE'  => $mercenary['bonus_type'],
        'CAN_BUY'     => $Result,
      ));
    }
  }

  $template->assign_var('DM_COST', $darkmater_cost);

  display($template, $lang['tech'][600]);
}

?>
