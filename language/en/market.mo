<?php

/*
#############################################################################
#  Filename: market.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2011 madmax1991 for Project "SuperNova.WS"
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 31a22.2
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$lang = array_merge($lang, array(
  'eco_mrk_title' => 'Market',
  'eco_mrk_description' => 'Very strange but you can not recognize any mention of this menu item in documentation. Where it came from?'
  'eco_mrk_service' => 'Service',
  'eco_mrk_service_cost' => 'Serivce cost',

  'eco_mrk_trader' => 'Trade resources',
  'eco_mrk_trader_cost' => 'Cost sharing resources',
  'eco_mrk_trader_exchange' => 'Exchange',
  'eco_mrk_trader_to' => 'Exchanged for',
  'eco_mrk_trader_course' => 'Course',
  'eco_mrk_trader_left' => 'Balance',
  'eco_mrk_trader_resources_all' => 'All resources',

  'eco_mrk_scraper' => 'Reworked ships for scrap',
  'eco_mrk_scraper_price' => 'Scrap output',
  'eco_mrk_scraper_perShip' => 'from ship',
  'eco_mrk_scraper_total' => 'Total',
  'eco_mrk_scraper_cost' => 'Sell ships for scrap costs',
  'eco_mrk_scraper_onOrbit' => 'In orbit',
  'eco_mrk_scraper_to' => 'Allow for scrapping',
  'eco_mrk_scraper_res' => 'The following scrap:',
  'eco_mrk_scraper_ships' => 'Put the following ships for scrap:',
  'eco_mrk_scraper_noShip' => 'There spacecrafts in orbit',

  'eco_mrk_stockman' => 'Buy s/h ships',
  'eco_mrk_stockman_price' => 'Price',
  'eco_mrk_stockman_perShip' => 'Ship',
  'eco_mrk_stockman_onStock' => 'From the seller',
  'eco_mrk_stockman_buy' => 'Buy ships',
  'eco_mrk_stockman_res' => 'Cost of purchased ships:',
  'eco_mrk_stockman_ships' => 'Purchased the following ships:',
  'eco_mrk_stockman_noShip' => 'The seller now has no ships for sale',

  'eco_mrk_exchange' => 'Resource Exchange',
  'eco_mrk_banker' => 'Banker',
  'eco_mrk_pawnshop' => 'Pawnshop',

  'eco_mrk_error_title' => 'Market - Error',
  'eco_mrk_errors' => array(
    MARKET_RESOURCES => 'The operation was a success',
    MARKET_SCRAPPER => 'Exchange of resources occurred successfully',
    MARKET_NOT_A_SHIP => 'Do not try to sell anything other than a ship!',
    MARKET_STOCKMAN => 'Dark matter is missing to complete the operation',
    MARKET_NO_RESOURCES => 'Not enough resources to complete operation',
    MARKET_PAWNSHOP => 'You are trying to send more ships for scrap than there are in orbit',
    MARKET_NO_STOCK => 'You are trying to buy more ships than the seller. This might not have selected ships, someone else has already bought them',
    MARKET_ZERO_DEAL => 'Do not specify the number of resources for sharing',
    MARKET_NOTHING => 'Select ships for sale',
    MARKET_ZERO_RES_STOCK => 'Select ships for purchase',
    MARKET_NEGATIVE_SHIPS => 'Do not try to sell a negative number of ships!',
  ),

));

?>
