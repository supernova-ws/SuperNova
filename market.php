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
$exchangeTo = ($exchangeTo<3) ? $exchangeTo : 0 ;

$parse = $lang;

$parse['rpg_cost_trader']   = $config->rpg_cost_trader;
$parse['rpg_cost_scraper']  = $config->rpg_cost_scraper;
$parse['rpg_cost_banker']   = $config->rpg_cost_banker;
$parse['rpg_cost_stockman'] = $config->rpg_cost_stockman;
$parse['rpg_cost_exchange'] = $config->rpg_cost_exchange;
$parse['rpg_cost_pawnshop'] = $config->rpg_cost_pawnshop;

$parse['rpg_exchange_metal'] = $config->rpg_exchange_metal;
$parse['rpg_exchange_crystal'] = $config->rpg_exchange_crystal;
$parse['rpg_exchange_deuterium'] = $config->rpg_exchange_deuterium;
$parse['rpg_exchange_darkMatter'] = $config->rpg_exchange_darkMatter;

$parse['mode']  = $mode;
$page_title = "{$lang['eco_mrk_title']}";

switch($mode){
  case 1: // Resource trader
    $page_title .= " - {$lang['eco_mrk_trader']}";

    $error_list = array(
      0 => $lang['eco_mrk_error_none'],
      1 => $lang['eco_mrk_error_noDM'],
      2 => $lang['eco_mrk_error_noResources']
    );

    $intError = 0;
    if($_POST['exchange'] && isset($exchangeTo)){
      if($user['rpg_points']<$config->rpg_cost_trader + $_POST['spend'][3])
        $intError = 1;
      else{
        $rates = array($config->rpg_exchange_metal, $config->rpg_exchange_crystal, $config->rpg_exchange_deuterium, $config->rpg_exchange_darkMatter);

        $qry = "UPDATE {{planets}} SET ";
        foreach($_POST['spend'] as $resource => $amount){
          $amount = abs(intval($amount));
          $value += $amount * $rates[$resource] / $rates[$exchangeTo];

          if($resource == 3){
            $amountDM = $amount;
          }else{
            $qry .= "`{$reslist['resources'][$resource]}` = `{$reslist['resources'][$resource]}` - {$amount}, ";
            if($planetrow[$reslist['resources'][$resource]] < $amount) $intError = 2;
            $planetrow[$reslist['resources'][$resource]] -= $amount;
          }
        }
        if(!$intError){
          $amountDM = intval($amountDM);
          $qry .= "`{$reslist['resources'][$exchangeTo]}` = `{$reslist['resources'][$exchangeTo]}` + {$value} WHERE `id` = {$planetrow['id']};";
          doquery($qry);
          doquery("UPDATE {{users}} SET `rpg_points` = `rpg_points` - {$config->rpg_cost_trader} - {$amountDM} WHERE `id` = {$user['id']};");
          $user['rpg_points'] -= $config->rpg_cost_trader + $amountDM;
          $planetrow[$reslist['resources'][$exchangeTo]] += $value;
        }
      }
      $page = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => $error_list[$intError]));
    }
    $template = gettemplate('market_trader', true);
    $data = array(
      'avail' => array( floor($planetrow['metal']), floor($planetrow['crystal']), floor($planetrow['deuterium']), $user['rpg_points'], ),
      'name'=> array( $lang['Metal'], $lang['Crystal'], $lang['Deuterium'], $lang['dark_matter'], ),
    );
    if($intError){
      for($i=0; $i<=3; $i++)
        $data['spend'][$i] = intval($_POST['spend'][$i]);

      $parse['exchangeTo'] = $exchangeTo;
    }
    $template->assign_var('message', $page);

    for($i=0; $i<=3; $i++){
      $template->assign_block_vars('resources', array(
        'ID'    => $i,
        'NAME'  => $data['name'][$i],
        'AVAIL' => $data['avail'][$i],
        'SPEND' => $data['spend'][$i],
      ));
      $resources .= $data['avail'][$i] . ', ';
    }
    $resources .= '0';
    $template->assign_var('resources', $resources);
    break;

  case 2: // Fleet scraper
    pdump($_POST);
    $page_title .= " - {$lang['eco_mrk_scraper']}";

    if($_POST['scrape']){
      foreach($_POST['ships'] as $shipID => $shipCount){
      }
    }


    $template = gettemplate('market_scraper', true);

    foreach($reslist['fleet'] as $shipID){
      if($planetrow[$resource[$shipID]]){
        $template->assign_block_vars('ships', array(
          'ID' => $shipID,
          'COUNT' => $planetrow[$resource[$shipID]],
          'NAME' => $lang['tech'][$shipID],
          'METAL' => floor($pricelist[$shipID]['metal']*3/4),
          'CRYSTAL' => floor($pricelist[$shipID]['crystal']/2),
          'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']/4),
          'SELL' => 0,
        ));
        $ships .= "Array($shipID, ";
        $ships .= floor($pricelist[$shipID]['metal']*3/4) . ", ";
        $ships .= floor($pricelist[$shipID]['crystal']/2) . ", ";
        $ships .= floor($pricelist[$shipID]['deuterium']/4) . ", ";
        $ships .= $planetrow[$resource[$shipID]];
        $ships .= '), ';
      }
    }
    $ships .= "1";
    $template->assign_var('ships', $ships);
    break;

  case 3: // Banker
    break;

  case 4: // S/H ship seller
    break;

  case 5: // Cross-player resource exchange
    break;

  case 6: // Pawnshop
    break;

  default:
    $template = gettemplate('market');
    break;
}
display(parsetemplate($template, $parse), $page_title);
?>