<?php

use DBAL\OldDbChangeSet;
use Planet\DBStaticPlanet;

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true) {
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= ' - ' . $lang["eco_mrk_{$submode}"];

$template = SnTemplate::gettemplate('market_fleet', true);
$template->assign_vars(array(
  'PAGE_HEADER' => $page_title,
  'rpg_cost' => $rpg_cost,
));

if(is_array($shipList)) {
  if(mrc_get_level($user, null, RES_DARK_MATTER) < $rpg_cost) {
    $intError = MARKET_NO_DM;
  }

  sn_db_transaction_start();
  // Здесь не надо апдейтить планету - очередь мы посчитали глобально
  $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id'], true, '*');
//  $global_data = sys_o_get_updated($user, $planetrow, SN_TIME_NOW);
//  $planetrow = $global_data['planet'];

  // Блокируем запись о запасах кораблей у трейдера
  doquery("SELECT * FROM {{config}} WHERE `config_name` = 'eco_stockman_fleet' LIMIT 1 FOR UPDATE;");

  $message .= $lang["eco_mrk_{$submode}_ships"] . '<ul>';
  $qry = array();
  $total = array();
  $db_changeset = array();
  foreach($shipList as $shipID => &$shipCount) {
    $shipCount = ceil(floatval($shipCount));
    if(!$shipCount) {
      continue;
    }

    if($shipCount < 0) {
      $debug->warning('User supplied negative ship count on Black Market page', 'Hack Attempt', 307);
      $intError = MARKET_NEGATIVE_SHIPS;
      break;
    }

    if($mode == MARKET_SCRAPPER) {
      $amount = mrc_get_level($user, $planetrow, $shipID, true, true); // $planetrow[get_unit_param($shipID, P_NAME)];
    } elseif($mode == MARKET_STOCKMAN) {
      $amount = $stock[$shipID];
    }

    if($amount < $shipCount) {
      $intError = $error_no_stock;
      break;
    }

    if(!in_array($shipID, sn_get_groups('fleet'))) {
      $debug->warning('Hack Attempt', 'User supplied non-ship unit ID on Black Market page', 306);
      $intError = MARKET_NOT_A_SHIP;
      break;
    }

    $multiplier = $mode == MARKET_SCRAPPER ? -1 : 1;

    $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($shipID, $shipCount * $multiplier, $user, $planetrow['id']);
    $newstock[$shipID] += $shipCount * (-$multiplier);

    $build_data = eco_get_build_data($user, $planetrow, $shipID);
    $resTemp[RES_METAL] = floor($build_data[BUILD_CREATE][RES_METAL] * $shipCount * $config_rpg_scrape_metal * (-$multiplier));
    $resTemp[RES_CRYSTAL] = floor($build_data[BUILD_CREATE][RES_CRYSTAL] * $shipCount * $config_rpg_scrape_crystal * (-$multiplier));
    $resTemp[RES_DEUTERIUM] = floor($build_data[BUILD_CREATE][RES_DEUTERIUM] * $shipCount * $config_rpg_scrape_deuterium * (-$multiplier));

    foreach($resTemp as $resID => $resCount) {
      $total[$resID] += $resCount;
    }

    $message .= "<li>{$lang['tech'][$shipID]}: " . HelperString::numberFloorAndFormat($shipCount);
  }

  if($mode == MARKET_STOCKMAN && $intError == MARKET_DEAL) {
    foreach($total as $resID => $resCount) {
      if(mrc_get_level($user, $planetrow, $resID, true, true) < -$resCount) {
        $intError = MARKET_NO_RESOURCES;
        $debug->warning('Trying to use bug in s/h market', 'S/H Ship Market', 301);
        break;
      }
    }
  }

  $intError = ($intError == MARKET_DEAL) && (array_sum($total) == 0) ? $error_zero_res : $intError;

  if($intError == MARKET_DEAL) {
    $message .= '</ul>' . $lang["eco_mrk_{$submode}_res"] . '<ul>';
    foreach($total as $resID => $resCount) {
      if(!$resCount) {
        continue;
      }

      $resource_name = pname_resource_name($resID);
      // $newrow[$resID] = $planetrow[$resID] + $resCount * (-$multiplier);
      $qry[] = "`{$resource_name}` = `{$resource_name}` + ({$resCount})";

      $message .= "<li>" . $lang['sys_' . $resource_name] . ": " . HelperString::numberFloorAndFormat(abs($resCount));
    }
    $message .= "</ul>";

    DBStaticPlanet::db_planet_set_by_id($planetrow['id'], implode(',', $qry));
    OldDbChangeSet::db_changeset_apply($db_changeset);

    rpg_points_change($user['id'], RPG_MARKET_FLEET, -($rpg_cost), "Using Black Market page {$mode}");
    $config->db_saveItem('eco_stockman_fleet', sys_unit_arr2str($newstock));

    $stock = $newstock;
    $planetrow = DBStaticPlanet::db_planet_by_id($planetrow['id']);

    sn_db_transaction_commit();

    $template->assign_block_vars('result', array(
      'STATUS' => ERR_NONE,
      'MESSAGE' => $message,
    ));
  } else {
    sn_db_transaction_rollback();
    $template->assign_block_vars('result', array(
      'STATUS' => ERR_ERROR,
      'MESSAGE' => $lang['eco_mrk_errors'][$intError],
    ));

    foreach($shipList as $shipID => $shipCount) {
      $data['ships'][$shipID] = max(0, intval($shipCount));
    }
  }
}
$message = '';

if(!$config->eco_stockman_fleet && $config->eco_stockman_fleet_populate) {
  $config->db_saveItem('eco_stockman_fleet', sys_unit_arr2str(array_map(function($item){return mt_rand(1, 1000);}, sn_get_groups('fleet'))));
}

tpl_set_resource_info($template, $planetrow, array());

if(!$array) {
  $array = array();
}

$group_fleet = sn_get_groups('fleet');
foreach($array as $key => $value) {
  if($mode == MARKET_SCRAPPER) {
    $shipID = $value;
    $amount = mrc_get_level($user, $planetrow, $shipID, false, true); // $planetrow[get_unit_param($shipID, P_NAME)];
  } elseif($mode == MARKET_STOCKMAN) {
    $shipID = $key;
    $amount = $value;
  }

  if(!in_array($shipID, $group_fleet)) {
    continue;
  }

  if($amount > 0) {
    $build_data = eco_get_build_data($user, $planetrow, $shipID);
    $template->assign_block_vars('ships', array(
      'ID'        => $shipID,
      'COUNT'     => $amount,
      'NAME'      => $lang['tech'][$shipID],
      'METAL'     => floor($build_data[BUILD_CREATE][RES_METAL] * $config_rpg_scrape_metal),
      'CRYSTAL'   => floor($build_data[BUILD_CREATE][RES_CRYSTAL] * $config_rpg_scrape_crystal),
      'DEUTERIUM' => floor($build_data[BUILD_CREATE][RES_DEUTERIUM] * $config_rpg_scrape_deuterium),
      'AMOUNT'    => intval($data['ships'][$shipID]),
    ));
  }
}
