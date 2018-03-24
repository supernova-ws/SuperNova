<?php

/*
#############################################################################
#  Filename: market.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 43a16.13
* @condition clear
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = (array(
  'eco_mrk_title' => 'Market',
  'eco_mrk_description' => 'Very strange but you can not recognize any mention of this menu item in documentation. Where it came from?',
  'eco_mrk_service' => 'Service',
  'eco_mrk_service_cost' => 'Serivce cost',

  'eco_mrk_trader_do' => 'Exchange resources',
  'eco_mrk_trader' => 'Exchange resources',
  'eco_mrk_trader_cost' => 'Cost sharing resources',
  'eco_mrk_trader_exchange' => 'Exchange',
  'eco_mrk_trader_to' => 'Exchanged for',
  'eco_mrk_trader_course' => 'Course',
  'eco_mrk_trader_left' => 'Exchange result',
  'eco_mrk_trader_resources_all' => 'All resources',
  'eco_mrk_trader_exchange_dm_confirm' => 'Are you sure that you want to trade {0} Dark Matter for resources?',

  'eco_mrk_scraper_do' => 'Scrap ships for resources',
  'eco_mrk_scraper' => 'Scrap ships for resources',
  'eco_mrk_scraper_price' => 'Scrap output',
  'eco_mrk_scraper_perShip' => 'from ship',
  'eco_mrk_scraper_total' => 'Total',
  'eco_mrk_scraper_cost' => 'Sell ships for scrap costs',
  'eco_mrk_scraper_onOrbit' => 'In orbit',
  'eco_mrk_scraper_to' => 'Allow for scrapping',
  'eco_mrk_scraper_res' => 'The following scrap:',
  'eco_mrk_scraper_ships' => 'Put the following ships for scrap:',
  'eco_mrk_scraper_noShip' => 'There spacecrafts in orbit',

  'eco_mrk_stockman_do' => 'Buy s/h ships',
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

  'eco_mrk_info_do' => 'Buy information',
  'eco_mrk_info' => 'Infotrader',
  'eco_mrk_info_description' => 'You discover in your inbox letter from unknown source. It says exactly:',
  'eco_mrk_info_description_2' => 'I have access to plenty of interest information. I can share it with your... for a reward, of course. On request will cost you only',
  'eco_mrk_info_buy' => 'Buy infopacket',

  'eco_mrk_info_player' => 'Info about player',
  'eco_mrk_info_player_description' => 'I can tell you which Mercenaries currently working under player\'s rule',
  'eco_mrk_info_player_message' => 'As far as I know list of player ID %1$d [%2$s] Mercenaries for now looks like this:',

  'eco_mrk_info_not_hired' => 'not hired',

  'eco_mrk_info_ally' => 'Info about Alliance',
  'eco_mrk_info_online' => 'Current activity on Universe',

  'eco_mrk_info_msg_from' => 'Untracible source',

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

    MARKET_NO_DM => 'There is not enough DM to complete operation',
    MARKET_INFO_WRONG => 'I did not sell this kind of information',
    MARKET_INFO_PLAYER => 'Information bought succesfully. Check your Personal Mail',
    MARKET_INFO_PLAYER_WRONG => 'You should specify player name or ID',
    MARKET_INFO_PLAYER_NOT_FOUND => 'Can not identify player. If player name consists from unreadable symbols or plain numbers - try to use player ID',
    MARKET_INFO_PLAYER_SAME => 'Why would you like to pay for info about yourself?',
  ),

));
