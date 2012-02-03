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

$action = sys_get_param_int('action');
$unit_id = sys_get_param_int('unit_id');

$sn_data_dark_matter_db_name = $sn_data[RES_DARK_MATTER]['name'];

$artifact_list = sys_unit_str2arr($user['player_artifact_list']);
foreach($sn_data['groups']['artifacts'] as $artifact_unit_id)
{
  $user[$sn_data[$artifact_unit_id]['name']] = isset($artifact_list[$artifact_unit_id]) ? $artifact_list[$artifact_unit_id] : 0;
}

if($action && in_array($unit_id, $sn_data['groups']['artifacts']))
{
  switch($action)
  {
    case ACTION_BUY:
      $build_data = eco_get_build_data($user, $planetrow, $unit_id, $user[$sn_data[$unit_id]['name']]);
      $darkmater_cost = $build_data[BUILD_CREATE][RES_DARK_MATTER];

      // TODO: more correct check - with "FOR UPDATE"
      if($user[$sn_data_dark_matter_db_name] >= $darkmater_cost)
      {
        if(!isset($sn_data[$unit_id]['max']) || ($sn_data[$unit_id]['max'] >= $user[$sn_data[$unit_id]['name']]))
        {
          {
            $selected_db_name = $sn_data[$unit_id]['name'];
            doquery("START TRANSACTION;");
            $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", '', true);
            $artifact_list = sys_unit_str2arr($user['player_artifact_list']);
            @$artifact_list[$unit_id]++;
            $artifact_list = sys_unit_arr2str($artifact_list);
            doquery( "UPDATE {{users}} SET `player_artifact_list` = '{$artifact_list}' WHERE `id` = '{$user['id']}' LIMIT 1;");
            rpg_points_change($user['id'], RPG_ARTIFACT, -($darkmater_cost), "Spent for artifact {$lang['tech'][$unit_id]} ID {$unit_id}");
            doquery("COMMIT;");
            header("Location: artifacts.php?goto={$unit_id}");
            ob_end_flush();
            die();
          }
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
    break;

    case ACTION_USE:
      art_use($user, $planetrow, $unit_id);
      header("Location: artifacts.php?goto={$unit_id}");
      ob_end_flush();
      die();
    break;
  }
  message($Message, $lang['tech'][ART_ARTIFACTS], 'artifacts.' . PHP_EX, 5);
}

$template = gettemplate('artifacts', true);

foreach($sn_data['groups']['artifacts'] as $artifact_id)
{
  $build_data = eco_get_build_data($user, $planetrow, $artifact_id, $user[$sn_data[$artifact_id]['name']]);
  {
    $artifact_data = $sn_data[$artifact_id];
    $artifact_data_bonus = $artifact_data['bonus'];
    $artifact_data_bonus = $artifact_data_bonus>=0 ? "+{$artifact_data_bonus}" : "{$artifact_data_bonus}";
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
      'LEVEL'       => intval($user[$sn_data[$artifact_id]['name']]),
      'LEVEL_MAX'   => intval($artifact_data['max']),
      'BONUS'       => $artifact_data_bonus,
      'BONUS_TYPE'  => $artifact_data['bonus_type'],
      'CAN_BUY'     => $build_data['CAN'][BUILD_CREATE],
    ));
  }
}

$template->assign_vars(array(
  'PAGE_HINT' => $lang['art_page_hint'],
));

display(parsetemplate($template), $lang['tech'][ART_ARTIFACTS]);

?>
