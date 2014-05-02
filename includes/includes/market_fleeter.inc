<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= ' - ' . $lang["eco_mrk_{$submode}"];

$template = gettemplate('market_fleet', true);
$template->assign_vars(array(
  'PAGE_HEADER' => $page_title,
  'rpg_cost' => $rpg_cost,
));

if(is_array($shipList))
{
  if(mrc_get_level($user, null, RES_DARK_MATTER) < $rpg_cost)
  {
    $intError = MARKET_NO_DM;
  }

  sn_db_transaction_start();
  $global_data = sys_o_get_updated($user, $planetrow, $time_now);
  $planetrow = $global_data['planet'];

  $message .= $lang["eco_mrk_{$submode}_ships"] . '<ul>';
  $qry = array();
  $total = array();
  $db_changeset = array();
  foreach($shipList as $shipID => &$shipCount)
  {
    $shipCount = floatval($shipCount);
    if(!$shipCount)
    {
      continue;
    }

    if($shipCount < 0)
    {
      $debug->warning('User supplied negative ship count on Black Market page', 'Hack Attempt', 307);
      $intError = MARKET_NEGATIVE_SHIPS;
      break;
    }

    if($mode == MARKET_SCRAPPER)
    {
      $amount = mrc_get_level($user, $planetrow, $shipID, true, true); // $planetrow[get_unit_param($shipID, P_NAME)];
    }
    elseif($mode == MARKET_STOCKMAN)
    {
      $amount = $stock[$shipID];
    }

    if($amount < $shipCount)
    {
      $intError = $error_no_stock;
      break;
    }

    if(!in_array($shipID, sn_get_groups('fleet')))
    {
      $debug->warning('Hack Attempt', 'User supplied non-ship unit ID on Black Market page', 306);
      $intError = MARKET_NOT_A_SHIP;
      break;
    }

    $multiplier = $mode == MARKET_SCRAPPER ? -1 : 1;

    $db_changeset['unit'][] = sn_db_unit_changeset_prepare($shipID, $shipCount * $multiplier, $user, $planetrow['id']);
    $newstock[$shipID] += $shipCount * (-$multiplier);

    /*
    if($mode == MARKET_SCRAPPER)
    {
      $qry .= "`{$ship_db_name}` = `{$ship_db_name}` - {$shipCount},";
      $newrow[$ship_db_name] = $planetrow[$ship_db_name] - $shipCount;
      $newstock[$shipID] += $shipCount;
    }
    elseif($mode == MARKET_STOCKMAN)
    {
      $qry .= "`{$ship_db_name}` = `{$ship_db_name}` + {$shipCount},";
      $newrow[$ship_db_name] = $planetrow[$ship_db_name] + $shipCount;
      $newstock[$shipID] -= $shipCount;
    }
    */

    $build_data = eco_get_build_data($user, $planetrow, $shipID);
    $resTemp['metal']     = floor($build_data[BUILD_CREATE][RES_METAL] * $shipCount * $config_rpg_scrape_metal * (-$multiplier));
    $resTemp['crystal']   = floor($build_data[BUILD_CREATE][RES_CRYSTAL] * $shipCount * $config_rpg_scrape_crystal * (-$multiplier));
    $resTemp['deuterium'] = floor($build_data[BUILD_CREATE][RES_DEUTERIUM] * $shipCount * $config_rpg_scrape_deuterium * (-$multiplier));

    foreach($resTemp as $resID => $resCount)
    {
      $total[$resID] += $resCount;
    }

    $message .= "<li>{$lang['tech'][$shipID]}: {$shipCount}";
  }

  if($mode == MARKET_STOCKMAN && $intError == MARKET_DEAL)
  {
    foreach($total as $resID => $resCount)
    {
      if($planetrow[$resID] < $resCount)
      {
        $intError = MARKET_NO_RESOURCES;
        $debug->warning('Trying to use bug in s/h market', 'S/H Ship Market', 301);
        break;
      }
    }
  }

  $intError = ($intError == MARKET_DEAL) && (array_sum($total) == 0) ? $error_zero_res : $intError;

  if($intError == MARKET_DEAL)
  {
    $message .= '</ul>' . $lang["eco_mrk_{$submode}_res"] . '<ul>';
    foreach($total as $resID => $resCount)
    {
      if(!$resCount)
      {
        continue;
      }

      $newrow[$resID] = $planetrow[$resID] + $resCount * (-$multiplier);
      $qry[] = "`{$resID}` = `{$resID}` + ({$resCount})";
      /*
      if($mode == MARKET_SCRAPPER)
      {
        $newrow[$resID] = $planetrow[$resID] + $resCount;
        $qry .= "`{$resID}` = `{$resID}` + {$resCount},";
      }
      elseif($mode == MARKET_STOCKMAN)
      {
        $newrow[$resID] = $planetrow[$resID] - $resCount;
        $qry .= "`{$resID}` = `{$resID}` - {$resCount},";
      }
      */

      $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
    }
    $message .= "</ul>";

    $qry = implode(',', $qry);
    /*
    pdump("UPDATE {{planets}} SET {$qry} WHERE `id` = {$planetrow['id']} LIMIT 1;");
    pdump($config->eco_stockman_fleet, '$oldstock');
    pdump($newstock, '$newstock');
    */
    // pdump($db_changeset);
    //  die();

    doquery("UPDATE {{planets}} SET {$qry} WHERE `id` = {$planetrow['id']} LIMIT 1;");
    sn_db_changeset_apply($db_changeset);

    rpg_points_change($user['id'], RPG_MARKET, -($rpg_cost), "Using Black Market page {$mode}");
    $config->db_saveItem('eco_stockman_fleet', sys_unit_arr2str($newstock));
    sn_db_transaction_commit();

    $stock = $newstock;
    $planetrow = array_merge($planetrow, $newrow);

    $template->assign_block_vars('result', array(
      'STATUS' => ERR_NONE,
      'MESSAGE' => $message,
    ));
  }
  else
  {
    sn_db_transaction_rollback();
    $template->assign_block_vars('result', array(
      'STATUS' => ERR_ERROR,
      'MESSAGE' => $lang['eco_mrk_errors'][$intError],
    ));

    foreach($shipList as $shipID => $shipCount)
    {
      $data['ships'][$shipID] = max(0, intval($shipCount));
    }
  }
}
$message = '';

tpl_set_resource_info($template, $planetrow, array());

if(!$array)
{
  $array = array();
}

$group_fleet = sn_get_groups('fleet');
foreach($array as $key => $value)
{
  if($mode == MARKET_SCRAPPER)
  {
    $shipID = $value;
    $amount = mrc_get_level($user, $planetrow, $shipID, false, true); // $planetrow[get_unit_param($shipID, P_NAME)];
  }
  elseif($mode == MARKET_STOCKMAN)
  {
    $shipID = $key;
    $amount = $value;
  }

  if(!in_array($shipID, $group_fleet))
  {
    continue;
  }

  if($amount > 0)
  {
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
