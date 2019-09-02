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

$page_title = "{$lang['eco_mrk_title']}";

$stock = sys_unit_str2arr(SN::$config->eco_stockman_fleet);
$newstock = $stock;
$intError = MARKET_DEAL;

switch($mode)
{
  case MARKET_RESOURCES: // Resource trader
    require('includes/includes/market_trader.inc');
    $template = eco_mrk_trader($user, $planetrow);
  break;

  case MARKET_SCRAPPER: // Fleet scraper
    $rpg_cost = SN::$config->rpg_cost_scraper;
    $submode = 'scraper';
    $error_no_stock = MARKET_NO_SHIPS;
    $error_zero_res = MARKET_ZERO_RES;

    $config_rpg_scrape_metal     = SN::$config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = SN::$config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = SN::$config->rpg_scrape_deuterium;

    $array = sn_get_groups('fleet');

    require('includes/includes/market_fleeter.inc');
  break;

  case MARKET_STOCKMAN: // S/H ship seller
    $rpg_cost = SN::$config->rpg_cost_stockman;
    $submode = 'stockman';
    $error_no_stock = MARKET_NO_STOCK;
    $error_zero_res = MARKET_ZERO_RES_STOCK;

    $config_rpg_scrape_metal     = 1 / SN::$config->rpg_scrape_metal;
    $config_rpg_scrape_crystal   = 1 / SN::$config->rpg_scrape_crystal;
    $config_rpg_scrape_deuterium = 1 / SN::$config->rpg_scrape_deuterium;

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
    $template = SnTemplate::gettemplate('market', true);
  break;
}

$message_id = sys_get_param_int('message');
if($message_id != MARKET_NOTHING)
{
  $template->assign_block_vars('result', array('MESSAGE' => $lang['eco_mrk_errors'][$message_id]));
}

if($message)
{
  $template->assign_block_vars('result', array('MESSAGE' => $message));
}

$template->assign_vars(array(
  'rpg_cost_trader'   => SN::$config->rpg_cost_trader,
  'rpg_cost_scraper'  => SN::$config->rpg_cost_scraper,
  'rpg_cost_stockman' => SN::$config->rpg_cost_stockman,
  'rpg_cost_info'     => SN::$config->rpg_cost_info,

  'rpg_cost_banker'   => SN::$config->rpg_cost_banker,
  'rpg_cost_exchange' => SN::$config->rpg_cost_exchange,
  'rpg_cost_pawnshop' => SN::$config->rpg_cost_pawnshop,

//  'message' => $message,
  'MODE' => $mode
));

SnTemplate::display($template, $page_title);
