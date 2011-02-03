<?php

/**
 * market.php
 *
 * Black market
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = (defined('SN_ROOT_PATH')) ? SN_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include("{$ugamela_root_path}common.{$phpEx}");

includeLang('market');
includeLang('fleet');

ini_set('display_errors', 1);

$mode = sys_get_param_int('mode');
$action = sys_get_param_int('action');
$tradeList = $_POST['spend'];
$shipList  = $_POST['ships'];
$exchangeTo = intval($_POST['exchangeTo']);
$exchangeTo = in_array($exchangeTo, $sn_data['groups']['resources_trader']) ? $exchangeTo : 0 ;

$page_title = "{$lang['eco_mrk_title']}";

$stock = sys_unit_str2arr($config->eco_stockman_fleet);

$newstock = $stock;

switch($mode)
{
  case MARKET_RESOURCES: // Resource trader
    require('includes/market/market_trader.inc');
  break;

  case MARKET_SCRAPPER: // Fleet scraper
    $newrow = $planetrow;
    $page_title .= " - {$lang['eco_mrk_scraper']}";

    if(is_array($shipList))
    {
      if($user['rpg_points'] >= $config->rpg_cost_scraper)
      {
        $message .= "{$lang['eco_mrk_scraper_ships']}<ul>";
        $qry = "UPDATE {{planets}} SET ";
        foreach($shipList as $shipID => $shipCount)
        {
          $shipCount = abs($shipCount);
          if($shipCount <= 0)
          {
            continue;
          }
          if($newrow[$sn_data[$shipID]['name']] < $shipCount)
          {
            $intError = MARKET_NO_SHIPS;
            break;
          }
          if(!in_array($shipID, $sn_data['groups']['fleet']))
          {
            $debug->warning('Hack Attempt', 'User supplied non-ship unit ID on Black Market page', 306);
            $intError = MARKET_NOT_A_SHIP;
            break;
          }
          $ship_db_name = $sn_data[$shipID]['name'];
          $qry .= "`{$ship_db_name}` = `{$ship_db_name}` - {$shipCount}, ";
          $newrow[$ship_db_name] -= $shipCount;
          $newstock[$shipID] += $shipCount;

          $resTemp['metal'] = floor($pricelist[$shipID]['metal']*$shipCount*$config->rpg_scrape_metal);
          $resTemp['crystal'] = floor($pricelist[$shipID]['crystal']*$shipCount*$config->rpg_scrape_crystal);
          $resTemp['deuterium'] = floor($pricelist[$shipID]['deuterium']*$shipCount*$config->rpg_scrape_deuterium);

          foreach($resTemp as $resID => $resCount)
          {
            $total[$resID] += $resCount;
          }

          $message .= "<li>{$lang['tech'][$shipID]}: {$shipCount}";
        }

        if($intError == MARKET_DEAL)
        {
          $message .= "</ul>";
          if(array_sum($total) > 0)
          {
            $message .= "{$lang['eco_mrk_scraper_res']}<ul>";
            foreach($total as $resID => $resCount)
            {
              $newrow[$resID] += $resCount;
              $qry .= "`{$resID}` = `{$resID}` + {$resCount}, ";
              $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
            }
            $message .= "</ul>";
            $qry .= "`id`=`id` WHERE `id` = {$planetrow['id']};";
            doquery($qry);

            $rpg_deduct = $config->rpg_cost_scraper;

            $planetrow = $newrow;
            $stock = $newstock;

            $config->eco_stockman_fleet = sys_unit_arr2str($stock);
            $config->db_saveItem('eco_stockman_fleet');
          }
          else
          {
            $intError = MARKET_ZERO_RES;
          }
        }
      }else{
        $intError = MARKET_NO_DM;
      }

      if($intError != MARKET_DEAL)
      {
        $message = parsetemplate(gettemplate('message_body'), array('title' => $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
        foreach($shipList as $shipID => $shipCount)
        {
          $data['ships'][$shipID] = max(0, intval($shipCount));
        }
      }
      else
      {
        $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => "<div align=left>{$message}</div>"));
      }
    }
    $template = gettemplate('market_fleet', true);
    $template->assign_var('rpg_cost', $config->rpg_cost_scraper);

    foreach($reslist['fleet'] as $shipID)
    {
      if($planetrow[$sn_data[$shipID]['name']] > 0)
      {
        $template->assign_block_vars('ships', array(
          'ID' => $shipID,
          'COUNT' => $planetrow[$sn_data[$shipID]['name']],
          'NAME' => $lang['tech'][$shipID],
          'METAL' => floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal),
          'CRYSTAL' => floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal),
          'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium),
          'AMOUNT' => intval($data['ships'][$shipID]),
        ));
      }
    }
  break;

  case MARKET_STOCKMAN: // S/H ship seller
    $newrow = $planetrow;
    $page_title .= " - {$lang['eco_mrk_stockman']}";

    if(is_array($shipList))
    {
      if($user['rpg_points'] >= $config->rpg_cost_stockman)
      {
        $message .= "{$lang['eco_mrk_stockman_ships']}<ul>";
        $qry = "UPDATE {{planets}} SET ";
        foreach($shipList as $shipID => $shipCount)
        {
          $shipCount = abs($shipCount);
          if($shipCount <= 0) continue;
          if($stock[$shipID] < $shipCount)
          {
            $intError = MARKET_NO_STOCK;
            break;
          }
          if(!in_array($shipID, $sn_data['groups']['fleet']))
          {
            $debug->warning('Hack Attempt', 'User supplied non-ship unit ID on Black Market page', 306);
            $intError = MARKET_NOT_A_SHIP;
            break;
          }
          $qry .= "`{$sn_data[$shipID]['name']}` = `{$sn_data[$shipID]['name']}` + {$shipCount}, ";
          $newrow[$sn_data[$shipID]['name']] += $shipCount;
          $newstock[$shipID] -= $shipCount;

          $resTemp['metal'] = floor($pricelist[$shipID]['metal']*$shipCount/$config->rpg_scrape_metal);
          $resTemp['crystal'] = floor($pricelist[$shipID]['crystal']*$shipCount/$config->rpg_scrape_crystal);
          $resTemp['deuterium'] = floor($pricelist[$shipID]['deuterium']*$shipCount/$config->rpg_scrape_deuterium);

          foreach($resTemp as $resID => $resCount)
          {
            $total[$resID] += $resCount;
          }

          $message .= "<li>{$lang['tech'][$shipID]}: {$shipCount}";
        }

        foreach($total as $resID => $resCount)
        {
          if($newrow[$resID] < $resCount)
          {
            $intError = MARKET_NO_RESOURCES;
            $debug->warning('Trying to use bug in s/h market', 'S/H Ship Market', 301);
            break;
          }
        }

        if($intError == MARKET_DEAL)
        {
          $message .= "</ul>";
          if(array_sum($total) > 0)
          {
            $message .= "{$lang['eco_mrk_stockman_res']}<ul>";

            foreach($total as $resID => $resCount)
            {
              $newrow[$resID] -= $resCount;
              $qry .= "`{$resID}` = `{$resID}` - {$resCount}, ";
              $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
            }
            $message .= "</ul>";
            $qry .= "`id`=`id` WHERE `id` = {$planetrow['id']};";
            doquery($qry);

            $rpg_deduct = $config->rpg_cost_stockman;

            $planetrow = $newrow;
            $stock = $newstock;

            $config->eco_stockman_fleet = sys_unit_arr2str($stock);
            $config->db_saveItem('eco_stockman_fleet');
          }
          else
          {
            $intError = MARKET_ZERO_RES_STOCK;
          }
        }
      }
      else
      {
        $intError = MARKET_NO_DM;
      }

      if($intError != MARKET_DEAL)
      {
        $message = parsetemplate(gettemplate('message_body'), array('title' => $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
        foreach($shipList as $shipID => $shipCount)
        {
          $data['ships'][$shipID] = max(0, intval($shipCount));
        }
      }
      else
      {
        $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => "<div align=left>{$message}</div>"));
      }
    }

    $template = gettemplate('market_fleet', true);
    $template->assign_var('rpg_cost', $config->rpg_cost_stockman);

    if($stock){
      foreach($stock as $shipID => $shipCount){
        if($shipCount > 0){
          $template->assign_block_vars('ships', array(
            'ID'        => $shipID,
            'COUNT'     => $shipCount,
            'NAME'      => $lang['tech'][$shipID],
            'METAL'     => floor($pricelist[$shipID]['metal']/$config->rpg_scrape_metal),
            'CRYSTAL'   => floor($pricelist[$shipID]['crystal']/$config->rpg_scrape_crystal),
            'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']/$config->rpg_scrape_deuterium),
            'AMOUNT'    => intval($data['ships'][$shipID]),
          ));
        }
      }
    };
  break;

  case MARKET_EXCHANGE: // Cross-player resource exchange
  break;

  case MARKET_BANKER: // Banker
  break;

  case MARKET_PAWNSHOP: // Pawnshop
  break;

  default:
    $template = gettemplate('market', true);
  break;
}

if($intError == MARKET_DEAL && $rpg_deduct){
  rpg_points_change($user['id'], -($rpg_deduct), "Using Black Market page {$mode}");
  $user['rpg_points'] -= $rpg_deduct;
}

$message_id = sys_get_param_int('message_id');
if($message_id != MARKET_NOTHING)
{
  $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => $lang['eco_mrk_errors'][$message_id]));
}

pdump($_POST);

$template->assign_vars(array(
  'rpg_cost_trader'   => $config->rpg_cost_trader,
  'rpg_cost_scraper'  => $config->rpg_cost_scraper,
  'rpg_cost_banker'   => $config->rpg_cost_banker,
  'rpg_cost_stockman' => $config->rpg_cost_stockman,
  'rpg_cost_exchange' => $config->rpg_cost_exchange,
  'rpg_cost_pawnshop' => $config->rpg_cost_pawnshop,

  'message' => $message,
  'MODE' => $mode
));

display($template, $page_title);

?>
