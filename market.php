<?php

/**
 * market.php
 *
 * Black market
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

define('SN_IN_MARKET', true);

includeLang('market');
includeLang('fleet');

$mode = sys_get_param_int('mode');
$action = sys_get_param_int('action');
$shipList  = $_POST['ships'];

$page_title = "{$lang['eco_mrk_title']}";

$stock = sys_unit_str2arr($config->eco_stockman_fleet);

$newstock = $stock;

$intError = MARKET_DEAL;
switch($mode)
{
  case MARKET_RESOURCES: // Resource trader
    require('includes/market/market_trader.inc');
  break;

  case MARKET_SCRAPPER: // Fleet scraper
    $rpg_cost = $config->rpg_cost_scraper;
    $submode = 'scraper';
    $error_no_stock = MARKET_NO_SHIPS;
    $error_zero_res = MARKET_ZERO_RES;

    $config_rpg_scrape_metal     = $config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = $config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = $config->rpg_scrape_deuterium;

    $array = &$reslist['fleet'];

    require('includes/market/market_fleeter.inc');
  break;

  case MARKET_STOCKMAN: // S/H ship seller
    $rpg_cost = $config->rpg_cost_stockman;
    $submode = 'stockman';
    $error_no_stock = MARKET_NO_STOCK;
    $error_zero_res = MARKET_ZERO_RES_STOCK;

    $config_rpg_scrape_metal     = 1 / $config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = 1 / $config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = 1 / $config->rpg_scrape_deuterium;

    $array = &$stock;

    require('includes/market/market_fleeter.inc');
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

$message_id = sys_get_param_int('message');
if($message_id != MARKET_NOTHING)
{
  $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => $lang['eco_mrk_errors'][$message_id]));
}

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
