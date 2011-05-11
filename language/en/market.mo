<?php

if (!defined('INSIDE')) 
{
	die('Hack attempt!');
}

$lang['mrkt_title']		= "Market";
$lang['mrkt_merchant']	= "Resource Trader";
$lang['mrkt_scraper']	= "Cornerman fleet";
$lang['mrkt_call_cost']	= "The cost of the call: ";
$lang['mrkt_call']		= "Call";

$lang['mod_ma_title'] = "Trader";
$lang['mod_ma_typer'] = "You want to sell";
$lang['mod_ma_rates'] = "Exchange materials 2/1/0.5";
$lang['mod_ma_buton'] = "Call Merchant";
$lang['mod_ma_noten'] = "Not enough";
$lang['mod_ma_done']  = "The Exchange was held successfully!";
$lang['mod_ma_error'] = "Error";
$lang['mod_ma_donet'] = "The Exchange was held successfully!";
$lang['mod_ma_excha'] = "Exchange";
$lang['mod_ma_cours'] = "Exchange rate";
$lang['mod_ma_nbre']  = "You can enter only numbers!!!";

$lang['eco_mrk_title']             = 'Market';
$lang['eco_mrk_services']          = 'Services';
$lang['eco_mrk_dark_matter_short'] = 'Dark matter';
$lang['eco_mrk_service_cost']      = 'Cost of service';

$lang['eco_mrk_error_title']        = $lang['eco_mrk_title'] . ' - Error';

$lang['eco_mrk_errors'] = array(
  MARKET_DEAL           => 'The operation was a success',
  MARKET_DEAL_TRADE     => 'Exchange of resources occurred successfully',
  MARKET_NOT_A_SHIP     => 'Do not try to sell anything other than a ship!',
  MARKET_NO_DM          => 'Dark matter is missing to complete the operation',
  MARKET_NO_RESOURCES   => 'Not enough resources to complete operation',
  MARKET_NO_SHIPS       => 'You are trying to send more ships for scrap than there are in orbit',
  MARKET_NO_STOCK       => 'You are trying to buy more ships than the seller. This might not have selected ships, someone else has already bought them',
  MARKET_ZERO_DEAL      => 'Do not specify the number of resources for sharing',
  MARKET_ZERO_RES       => 'Select ships for sale',
  MARKET_ZERO_RES_STOCK => 'Select ships for purchase',
  MARKET_NEGATIVE_SHIPS => 'Do not try to sell a negative number of ships!',
);

$lang['eco_mrk_trader']          = 'Resource Trader';
$lang['eco_mrk_trader_cost']     = 'Cost sharing resources';
$lang['eco_mrk_trader_exchange'] = 'Exchange';
$lang['eco_mrk_trader_to']       = 'Exchanged for';
$lang['eco_mrk_trader_course']   = 'Course';
$lang['eco_mrk_trader_left']     = 'Balance';

$lang['eco_mrk_scraper']         = 'Reworked ships for scrap';
$lang['eco_mrk_scraper_price']   = 'Scrap output';
$lang['eco_mrk_scraper_perShip'] = 'from ship';
$lang['eco_mrk_scraper_total']   = 'Total';
$lang['eco_mrk_scraper_cost']    = 'Sell ships for scrap costs';
$lang['eco_mrk_scraper_onOrbit'] = 'In orbit';
$lang['eco_mrk_scraper_to']      = 'Allow for scrapping';
$lang['eco_mrk_scraper_res']     = 'The following scrap:';
$lang['eco_mrk_scraper_ships']   = 'Put the following ships for scrap:';
$lang['eco_mrk_scraper_noShip']  = 'There spacecrafts in orbit';

$lang['eco_mrk_stockman']         = 'Seller of used vehicles';
$lang['eco_mrk_stockman_price']   = 'Price';
$lang['eco_mrk_stockman_perShip'] = 'Ship';
$lang['eco_mrk_stockman_onStock'] = 'From the seller';
$lang['eco_mrk_stockman_buy']     = 'Buy ships';
$lang['eco_mrk_stockman_res']     = 'Cost of purchased ships:';
$lang['eco_mrk_stockman_ships']   = 'Purchased the following ships:';
$lang['eco_mrk_stockman_noShip']  = 'The seller now has no ships for sale';

$lang['eco_mrk_exchange'] = 'Resource Exchange';

$lang['eco_mrk_banker']   = 'Banker';
$lang['eco_mrk_pawnshop'] = 'Pawnshop';
?>