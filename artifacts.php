<?php

/**
* artifacts.php
* Artifact actions
*
* @package roleplay
* @version 1.0
*
* Revision History
* ================
* 1.0 copyright (c) 2011 by Gorlum for http://supernova.ws
*
*/

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('infos');
lng_include('artifacts');

include('includes/includes/art_artifact.php');

$sn_data_dark_matter_db_name = $sn_data[RES_DARK_MATTER]['name'];

if(($action = sys_get_param_int('action')) && in_array($unit_id = sys_get_param_int('unit_id'), $sn_data['groups']['artifacts']))
{
  switch($action)
  {
    case ACTION_BUY:
      sn_db_transaction_start();

      $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", true);
      $artifact_level = mrc_get_level($user, array(), $unit_id, true);

      $build_data = eco_get_build_data($user, $planetrow, $unit_id, $artifact_level, true);
      $darkmater_cost = $build_data[BUILD_CREATE][RES_DARK_MATTER];

      // TODO: more correct check - with "FOR UPDATE"
      if($user[$sn_data_dark_matter_db_name] >= $darkmater_cost)
      {
        if(!isset($sn_data[$unit_id]['max']) || ($sn_data[$unit_id]['max'] > $user[$sn_data[$unit_id]['name']]))
        {
          $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, 1, $user);
          sn_db_changeset_apply($db_changeset);
          rpg_points_change($user['id'], RPG_ARTIFACT, -($darkmater_cost), "Spent for artifact {$lang['tech'][$unit_id]} ID {$unit_id}");
          sn_db_transaction_commit();
          header("Location: artifacts.php#{$unit_id}");
          ob_end_flush();
          die();
        }
        else
        {
          $Message = $lang['off_maxed_out'];
        }
      }
      else
      {
        $Message = $lang['sys_no_points'];
      }
      sn_db_transaction_rollback();
    break;

    case ACTION_USE:
      art_use($user, $planetrow, $unit_id);
      header("Location: artifacts.php#{$unit_id}");
      ob_end_flush();
      die();
    break;
  }
  message($Message, $lang['tech'][UNIT_ARTIFACTS], 'artifacts.' . PHP_EX, 5);
}

$template = gettemplate('artifacts', true);

foreach($sn_data['groups']['artifacts'] as $artifact_id)
{
  $artifact_level = mrc_get_level($user, array(), $artifact_id, true);
  $build_data = eco_get_build_data($user, $planetrow, $artifact_id, $artifact_level);
  {
    $artifact_data = &$sn_data[$artifact_id];
    $artifact_data_bonus = $artifact_data['bonus'];
    $artifact_data_bonus = $artifact_data_bonus >= 0 ? "+{$artifact_data_bonus}" : "{$artifact_data_bonus}";
    switch($artifact_data['bonus_type'])
    {
      case BONUS_PERCENT:
        $artifact_data_bonus = "{$artifact_data_bonus}% ";
      break;

      case BONUS_ADD:
      break;

      case BONUS_ABILITY:
        $artifact_data_bonus = '';
      break;

      default:
      break;
    }

    $template->assign_block_vars('artifact', array(
      'ID'          => $artifact_id,
      'NAME'        => $lang['tech'][$artifact_id],
      'DESCRIPTION' => $lang['info'][$artifact_id]['description'],
      'EFFECT'      => $lang['info'][$artifact_id]['effect'],
      'COST'        => $build_data[BUILD_CREATE][RES_DARK_MATTER],
      'LEVEL'       => intval($artifact_level),
      'LEVEL_MAX'   => intval($artifact_data['max']),
      'BONUS'       => $artifact_data_bonus,
      'BONUS_TYPE'  => $artifact_data['bonus_type'],
      'CAN_BUY'     => $build_data['CAN'][BUILD_CREATE],
    ));
  }
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['tech'][UNIT_ARTIFACTS],
  'PAGE_HINT' => $lang['art_page_hint'],
));

display(parsetemplate($template), $lang['tech'][UNIT_ARTIFACTS]);
