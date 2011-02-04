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
    require('includes/market/market_scraper.inc');
  break;

  case MARKET_STOCKMAN: // S/H ship seller
    require('includes/market/market_stockman.inc');
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
