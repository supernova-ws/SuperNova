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

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('market');

$mode = intval($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);
$exchangeTo = intval($_POST['exchangeTo']);

$parse = $lang;

$parse['rpg_cost_exchanger'] = $config->rpg_cost_exchanger;
$parse['rpg_cost_scraper']   = $config->rpg_cost_scraper;
$parse['rpg_cost_banker']    = $config->rpg_cost_banker;
$parse['rpg_cost_stockman']  = $config->rpg_cost_stockman;

$parse['rpg_exchange_metal'] = $config->rpg_exchange_metal;
$parse['rpg_exchange_crystal'] = $config->rpg_exchange_crystal;
$parse['rpg_exchange_deuterium'] = $config->rpg_exchange_deuterium;

$parse['avail_metal']       = floor($planetrow['metal']);
$parse['avail_crystal']     = floor($planetrow['crystal']);
$parse['avail_deuterium']   = floor($planetrow['deuterium']);
$parse['avail_dark_matter'] = $user['rpg_points'];

$parse['mode']  = $mode;

switch($mode){
  case 1:
    $page_title = "{$lang['eco_mrk_title']} - {$lang['eco_mrk_exchanger']}";

    $error_list = array(
      0 => $lang['eco_mrk_error_none'],
      1 => $lang['eco_mrk_error_noDM'],
      2 => $lang['eco_mrk_error_noResources']
    );

    $intError = 0;
    if($_POST['exchange'] && isset($exchangeTo)){
      if($user['rpg_points']<$config->rpg_cost_exchanger){
        $intError = 1;
      }else{
        $rates = array($config->rpg_exchange_metal, $config->rpg_exchange_crystal, $config->rpg_exchange_deuterium);

        $qry = "UPDATE {{planets}} SET ";
        foreach($_POST['spend'] as $resource => $amount){
          $amount = abs(intval($amount));
          $value += $amount * $rates[$resource] / $rates[$exchangeTo];

          $qry .= "`{$reslist['resources'][$resource]}` = `{$reslist['resources'][$resource]}` - {$amount}, ";
          if($planetrow[$reslist['resources'][$resource]] < $amount)
            $intError = 2;
        }
        if(!$intError){
          $qry .= "`{$reslist['resources'][$exchangeTo]}` = `{$reslist['resources'][$exchangeTo]}` + {$value} WHERE `id` = {$planetrow['id']};";
          doquery($qry);
          doquery("UPDATE {{users}} SET `rpg_points` = `rpg_points` - {$config->rpg_cost_exchanger} WHERE `id` = {$user['id']};");
          $user['rpg_points'] -= $config->rpg_cost_exchanger;
        }
      }
    }

    $page = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => $error_list[$intError]));
    if($intError){
      $parse['spend0'] = intval($_POST['spend'][0]);
      $parse['spend1'] = intval($_POST['spend'][1]);
      $parse['spend2'] = intval($_POST['spend'][2]);
      $parse['exchangeTo'] = $exchangeTo;
    }else{
      $parse['spend0'] = 0;
      $parse['spend1'] = 0;
      $parse['spend2'] = 0;
    }

    display($page . parsetemplate(gettemplate('market_exchanger'), $parse), $page_title);
    break;

  case 2:
    break;

  case 3:
    break;

  case 4:
    break;

  default:
    display(parsetemplate(gettemplate('market'), $parse), $lang['eco_mrk_title']);
    break;
}
?>