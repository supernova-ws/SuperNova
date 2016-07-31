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

global $user, $planetrow;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('infos');
lng_include('artifacts');

include('includes/includes/art_artifact.php');

$sn_group_artifacts = sn_get_groups('artifacts');

$Message = '';
if (($action = sys_get_param_int('action')) && in_array($unit_id = sys_get_param_int('unit_id'), $sn_group_artifacts)) {
  switch ($action) {
    case ACTION_BUY:
      sn_db_transaction_start();

      $user = DBStaticUser::db_user_by_id($user['id'], true);
      $artifact_level = mrc_get_level($user, null, $unit_id, true);

      $build_data = eco_get_build_data($user, $planetrow, $unit_id, $artifact_level, true);
      $darkmater_cost = $build_data[BUILD_CREATE][RES_DARK_MATTER];

      // TODO: more correct check - with "FOR UPDATE"
      if (mrc_get_level($user, null, RES_DARK_MATTER) >= $darkmater_cost) {
        $unit_max_stack = get_unit_param($unit_id, P_MAX_STACK);
        if (!isset($unit_max_stack) || $unit_max_stack > mrc_get_level($user, $planetrow, $unit_id)) {

          $db_changeset['unit'][] = DBStaticUnit::dbUpdateOrInsertUnit($unit_id, 1, $user);

          rpg_points_change($user['id'], RPG_ARTIFACT, -($darkmater_cost),
            sprintf('Spent for artifact %1$s ID %2$d', classLocale::$lang['tech'][$unit_id], $unit_id)
          );
          sn_db_transaction_commit();
          header("Location: artifacts.php#{$unit_id}");
          ob_end_flush();
          die();
        } else {
          $Message = classLocale::$lang['off_maxed_out'];
        }
      } else {
        $Message = classLocale::$lang['sys_no_points'];
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
  message($Message, classLocale::$lang['tech'][UNIT_ARTIFACTS], 'artifacts.' . PHP_EX, 5);
}

$template = gettemplate('artifacts', true);

foreach ($sn_group_artifacts as $artifact_id) {
  $artifact_level = mrc_get_level($user, null, $artifact_id, true);
  $build_data = eco_get_build_data($user, $planetrow, $artifact_id, $artifact_level);
  {
    $artifact_data = get_unit_param($artifact_id);
    $artifact_data_bonus = $artifact_data['bonus'];
    $artifact_data_bonus = $artifact_data_bonus >= 0 ? "+{$artifact_data_bonus}" : "{$artifact_data_bonus}";
    switch ($artifact_data['bonus_type']) {
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
      'NAME'        => classLocale::$lang['tech'][$artifact_id],
      'DESCRIPTION' => classLocale::$lang['info'][$artifact_id]['description'],
      'EFFECT'      => classLocale::$lang['info'][$artifact_id]['effect'],
      'COST'        => $build_data[BUILD_CREATE][RES_DARK_MATTER],
      'COST_TEXT'   => pretty_number($build_data[BUILD_CREATE][RES_DARK_MATTER]),
      'LEVEL'       => intval($artifact_level),
      'LEVEL_MAX'   => intval($artifact_data['max']),
      'BONUS'       => $artifact_data_bonus,
      'BONUS_TYPE'  => $artifact_data['bonus_type'],
      'CAN_BUY'     => $build_data['CAN'][BUILD_CREATE],
    ));
  }
}

$template->assign_vars(array(
  'PAGE_HEADER' => classLocale::$lang['tech'][UNIT_ARTIFACTS],
  'PAGE_HINT'   => classLocale::$lang['art_page_hint'],
));

display(parsetemplate($template), classLocale::$lang['tech'][UNIT_ARTIFACTS]);
