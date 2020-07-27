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


use Unit\DBStaticUnit;

global $lang, $user, $planetrow;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('infos');
lng_include('artifacts');

include('includes/includes/art_artifact.php');

$sn_group_artifacts = sn_get_groups('artifacts');

/**
 * @param $user
 * @param $unit_id
 * @param $planetrow
 * @param $lang
 *
 * @return string
 */
function art_buy($user, $unit_id, $planetrow, $lang) {
  $Message = '';
  sn_db_transaction_start();

  $user = db_user_by_id($user['id'], true);
  $artifact_level = mrc_get_level($user, array(), $unit_id, true);

  $build_data = eco_get_build_data($user, $planetrow, $unit_id, $artifact_level, true);
  $darkmater_cost = $build_data[BUILD_CREATE][RES_DARK_MATTER];

  // TODO: more correct check - with "FOR UPDATE"
  if (mrc_get_level($user, null, RES_DARK_MATTER) >= $darkmater_cost) {
    $unit_max_stack = get_unit_param($unit_id, P_MAX_STACK);
    if (!isset($unit_max_stack) || $unit_max_stack > mrc_get_level($user, $planetrow, $unit_id)) {
      if (!DBStaticUnit::dbUserAdd($user['id'], $unit_id, 1)) {
        $Message = '{Ошибка записи в БД}';
      } else {
        rpg_points_change($user['id'], RPG_ARTIFACT, -($darkmater_cost), "Spent for artifact {$lang['tech'][$unit_id]} ID {$unit_id}");
        sn_db_transaction_commit();
        sys_redirect("artifacts.php#{$unit_id}");
      }
    } else {
      $Message = $lang['off_maxed_out'];
    }
  } else {
    $Message = $lang['sys_no_points'];
  }
  sn_db_transaction_rollback();

  return $Message;
}

if (($action = sys_get_param_int('action')) && in_array($unit_id = sys_get_param_int('unit_id'), $sn_group_artifacts)) {
  $Message = '';
  switch ($action) {
    case ACTION_BUY:
      $Message = art_buy($user, $unit_id, $planetrow, $lang);
    break;

    case ACTION_USE:
      art_use($user, $planetrow, $unit_id);
      sys_redirect("artifacts.php#{$unit_id}");
    break;
  }
  SnTemplate::messageBox($Message, $lang['tech'][UNIT_ARTIFACTS], 'artifacts.' . PHP_EX, 5);
}

$user = db_user_by_id($user['id'], true);

$template = SnTemplate::gettemplate('artifacts', true);

foreach ($sn_group_artifacts as $artifact_id) {
  $artifact_level = mrc_get_level($user, [], $artifact_id, true);
  $build_data = eco_get_build_data($user, $planetrow, $artifact_id, $artifact_level);
  $artifact_data = get_unit_param($artifact_id);
  $artifact_data_bonus = SnTemplate::tpl_render_unit_bonus_data($artifact_data);

  $template->assign_block_vars('artifact', array(
    'ID'          => $artifact_id,
    'NAME'        => $lang['tech'][$artifact_id],
    'DESCRIPTION' => $lang['info'][$artifact_id]['description'],
    'EFFECT'      => $lang['info'][$artifact_id]['effect'],
    'COST'        => $build_data[BUILD_CREATE][RES_DARK_MATTER],
    'LEVEL'       => intval($artifact_level),
    'LEVEL_MAX'   => intval($artifact_data['max']),
    'BONUS'       => $artifact_data_bonus,
    'BONUS_TYPE'  => $artifact_data[P_BONUS_TYPE],
    'CAN_BUY'     => $build_data['CAN'][BUILD_CREATE],
  ));
}

$template->assign_vars(array(
  'PAGE_HEADER' => $lang['tech'][UNIT_ARTIFACTS],
  'PAGE_HINT'   => $lang['art_page_hint'],
));

SnTemplate::display($template, $lang['tech'][UNIT_ARTIFACTS]);
