<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= ' - ' . $lang["eco_mrk_{$submode}"];

if(is_array($shipList))
{
  if($user[$sn_data[RES_DARK_MATTER]['name']] < $rpg_cost)
  {
    $intError = MARKET_NO_DM;
  }

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planetrow, $time_now);
  $planetrow = $global_data['planet'];

  $message .= $lang["eco_mrk_{$submode}_ships"] . '<ul>';
  $qry = '';
  $total = array();
  foreach($shipList as $shipID => $shipCount)
  {
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
      $amount = $planetrow[$sn_data[$shipID]['name']];
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

    if(!in_array($shipID, $sn_data['groups']['fleet']))
    {
      $debug->warning('Hack Attempt', 'User supplied non-ship unit ID on Black Market page', 306);
      $intError = MARKET_NOT_A_SHIP;
      break;
    }

    $ship_db_name = $sn_data[$shipID]['name'];
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

    $resTemp['metal'] = floor($sn_data[$shipID]['metal'] * $shipCount * $config_rpg_scrape_metal);
    $resTemp['crystal'] = floor($sn_data[$shipID]['crystal'] * $shipCount * $config_rpg_scrape_crystal);
    $resTemp['deuterium'] = floor($sn_data[$shipID]['deuterium'] * $shipCount * $config_rpg_scrape_deuterium);

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

  $intError = ($intError == MARKET_DEAL) && (array_sum($total) <= 0) ? $error_zero_res : $intError;

  if($intError == MARKET_DEAL)
  {
    $message .= '</ul>' . $lang["eco_mrk_{$submode}_res"] . '<ul>';
    foreach($total as $resID => $resCount)
    {
      if(!$resCount)
      {
        continue;
      }

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

      $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
    }
    $message .= "</ul>";
    doquery("UPDATE {{planets}} SET {$qry}`id`=`id` WHERE `id` = {$planetrow['id']} LIMIT 1;");

    rpg_points_change($user['id'], RPG_MARKET, -($rpg_cost), "Using Black Market page {$mode}");
    $config->db_saveItem('eco_stockman_fleet', sys_unit_arr2str($newstock));
    doquery('COMMIT;');

    $stock = $newstock;
    // $user[$sn_data[RES_DARK_MATTER]['name']] -= $rpg_cost;
    $planetrow = array_merge($planetrow, $newrow);

    $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => "<div align=left>{$message}</div>"));
  }
  else
  {
    doquery('ROLLBACK;');
    $message = parsetemplate(gettemplate('message_body'), array('title' => $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
    foreach($shipList as $shipID => $shipCount)
    {
      $data['ships'][$shipID] = max(0, intval($shipCount));
    }
  }
}

$template = gettemplate('market_fleet', true);
$template->assign_vars(array(
  'rpg_cost' => $rpg_cost,
));

tpl_set_resource_info($template, $planetrow, array());

if(!$array)
{
  $array = array();
}

foreach($array as $key => $value)
{
  if($mode == MARKET_SCRAPPER)
  {
    $shipID = $value;
    $amount = $planetrow[$sn_data[$shipID]['name']];
  }
  elseif($mode == MARKET_STOCKMAN)
  {
    $shipID = $key;
    $amount = $value;
  }

  if($amount > 0)
  {
    $template->assign_block_vars('ships', array(
      'ID'        => $shipID,
      'COUNT'     => $amount,
      'NAME'      => $lang['tech'][$shipID],
      'METAL'     => floor($sn_data[$shipID]['metal'] * $config_rpg_scrape_metal),
      'CRYSTAL'   => floor($sn_data[$shipID]['crystal'] * $config_rpg_scrape_crystal),
      'DEUTERIUM' => floor($sn_data[$shipID]['deuterium'] * $config_rpg_scrape_deuterium),
      'AMOUNT'    => intval($data['ships'][$shipID]),
    ));
  }
}

?>
