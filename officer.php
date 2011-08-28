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
//  1 => Tout va tres bien on peut le faire celui la
// -1 => On pouvait le faire, mais on est d�ja au level max
function IsOfficierAccessible ($CurrentUser, $Officier) {
  global $sn_data;

  if (isset($sn_data[$Officier]['require'])) {
    $enabled = true;
    foreach($sn_data[$Officier]['require'] as $ReqOfficier => $OfficierLevel) 
    {
      $unit_db_name = $sn_data[$ReqOfficier]['name'];
      if ($CurrentUser[$unit_db_name] &&
        $CurrentUser[$unit_db_name] >= $OfficierLevel) {
        $enabled = 1;
      } else {
        return 0;
      }
    }
  }
  if ($CurrentUser[$sn_data[$Officier]['name']] < $sn_data[$Officier]['max']  ) {
    return 1;
  } else {
    return -1;
  }
}

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$mode = sys_get_param_int('mode');
$offi = sys_get_param_int('offi');

lng_include('infos');

//darkmater constant
$sn_data_dark_matter_db_name = $sn_data[RES_DARK_MATTER]['name'];
// Si recrutement d'un officier
if($mode == 2)
{
  $build_data = eco_get_build_data($user, $planetrow, $offi, $user[$sn_data[$offi]['name']]);
  $darkmater_cost = $build_data[BUILD_CREATE][RES_DARK_MATTER];

  if($user[$sn_data_dark_matter_db_name] >= $darkmater_cost)
  {
    $Selected    = $offi;
    if(in_array($Selected, $sn_data['groups']['mercenaries']))
    {
      $Result = IsOfficierAccessible ( $user, $Selected );
      if ( $Result == 1 )
      {
        $selected_db_name = $sn_data[$Selected]['name'];
//debug($selected_db_name);
//debug($darkmater_cost);
//die();
        doquery( "UPDATE {{users}} SET `{$selected_db_name}` = `{$selected_db_name}` + 1 WHERE `id` = '{$user['id']}';");
        rpg_points_change($user['id'], RPG_MERCENARY, -($darkmater_cost), "Spent for officer {$lang['tech'][$Selected]} ID {$Selected}");
//        $Message = $lang['off_recruited'];
//        $user[$selected_db_name] += 1;
//        $user[$sn_data_dark_matter_db_name]         -= $darkmater_cost;
        header("Location: officer.php?goto={$offi}");
        ob_end_flush();
        die();
      } 
      elseif ( $Result == -1 )
      {
        $Message = $lang['off_maxed_out'];
      }
      elseif ( $Result == 0 )
      {
        $Message = $lang['off_not_available'];
      }
    }
  }
  else
  {
    $Message = $lang['off_no_points'];
  }
  message($Message, $lang['tech'][600], 'officer.' . PHP_EX, 5);
}
else
{
  $template = gettemplate('officer', true);
  foreach($sn_data['groups']['mercenaries'] as $mercenary_id) {
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

      $build_data = eco_get_build_data($user, $planetrow, $mercenary_id, $user[$sn_data[$mercenary_id]['name']]);

      $template->assign_block_vars('officer', array(
        'ID'          => $mercenary_id,
        'NAME'        => $lang['tech'][$mercenary_id],
        'DESCRIPTION' => $lang['info'][$mercenary_id]['description'],
        'EFFECT'      => $lang['info'][$mercenary_id]['effect'],
        'COST'        => $build_data[BUILD_CREATE][RES_DARK_MATTER],
        'LEVEL'       => $user[$sn_data[$mercenary_id]['name']],
        'LEVEL_MAX'   => $mercenary['max'],
        'BONUS'       => $mercenary_bonus,
        'BONUS_TYPE'  => $mercenary['bonus_type'],
        'CAN_BUY'     => $Result,
      ));
    }
  }

  display(parsetemplate($template), $lang['tech'][600]);
}

?>
