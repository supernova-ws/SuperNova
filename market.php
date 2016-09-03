<?php

/**
 * market.php
 *
 * Black market
 *
 * 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *
 */

require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

define('SN_IN_MARKET', true);

lng_include('market');
lng_include('fleet');

$mode = sys_get_param_int('mode');
$action = sys_get_param_int('action');
$shipList = $_POST['ships'];

$page_title = classLocale::$lang['eco_mrk_title'];

$stock = sys_unit_str2arr(classSupernova::$config->eco_stockman_fleet);
$newstock = $stock;
$intError = MARKET_DEAL;

switch($mode)
{
  case MARKET_RESOURCES: // Resource trader
    require('includes/includes/market_trader.inc');
    $template = eco_mrk_trader($user, $planetrow);
  break;

  case MARKET_SCRAPPER: // Fleet scraper
    $rpg_cost = classSupernova::$config->rpg_cost_scraper;
    $submode = 'scraper';
    $error_no_stock = MARKET_NO_SHIPS;
    $error_zero_res = MARKET_ZERO_RES;

    $config_rpg_scrape_metal     = classSupernova::$config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = classSupernova::$config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = classSupernova::$config->rpg_scrape_deuterium;

    $array = classSupernova::$gc->groupFleet;

    require('includes/includes/market_fleeter.inc');
  break;

  case MARKET_STOCKMAN: // S/H ship seller
    $rpg_cost = classSupernova::$config->rpg_cost_stockman;
    $submode = 'stockman';
    $error_no_stock = MARKET_NO_STOCK;
    $error_zero_res = MARKET_ZERO_RES_STOCK;

    $config_rpg_scrape_metal     = 1 / classSupernova::$config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = 1 / classSupernova::$config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = 1 / classSupernova::$config->rpg_scrape_deuterium;

    $array = &$stock;

    require('includes/includes/market_fleeter.inc');
  break;

  case MARKET_INFO: // Infotrader
    require('includes/includes/market_info.inc');
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
  $template->assign_block_vars('result', array('MESSAGE' => classLocale::$lang['eco_mrk_errors'][$message_id]));
}

if($message)
{
  $template->assign_block_vars('result', array('MESSAGE' => $message));
}

$template->assign_vars(array(
  'rpg_cost_trader'   => classSupernova::$config->rpg_cost_trader,
  'rpg_cost_scraper'  => classSupernova::$config->rpg_cost_scraper,
  'rpg_cost_stockman' => classSupernova::$config->rpg_cost_stockman,
  'rpg_cost_info'     => classSupernova::$config->rpg_cost_info,

  'rpg_cost_banker'   => classSupernova::$config->rpg_cost_banker,
  'rpg_cost_exchange' => classSupernova::$config->rpg_cost_exchange,
  'rpg_cost_pawnshop' => classSupernova::$config->rpg_cost_pawnshop,

//  'message' => $message,
  'MODE' => $mode
));

display($template, $page_title);
