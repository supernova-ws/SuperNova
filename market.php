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
$tradeList = $_POST['spend'];
$shipList  = $_POST['ships'];
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

$newrow = $planetrow;

switch($mode){
  case 1: // Resource trader
    $page_title .= " - {$lang['eco_mrk_trader']}";

    $error_list = array(
      0 => $lang['eco_mrk_trader_ok'],
      1 => $lang['eco_mrk_error_noDM'],
      2 => $lang['eco_mrk_error_noResources'],
    );

    $intError = 0;
    if(is_array($tradeList) && isset($exchangeTo)){
      $rpg_deduct = $config->rpg_cost_trader + $tradeList[3];
      if($user['rpg_points']>=$rpg_deduct){
        $rates = array($config->rpg_exchange_metal, $config->rpg_exchange_crystal, $config->rpg_exchange_deuterium, $config->rpg_exchange_darkMatter);

        $qry = "UPDATE {{planets}} SET ";
        foreach($tradeList as $resource => $amount){
          $amount = abs(intval($amount));
          $value += $amount * $rates[$resource] / $rates[$exchangeTo];

          if($resource == 3){
            $amountDM = $amount;
          }else{
            $qry .= "`{$reslist['resources'][$resource]}` = `{$reslist['resources'][$resource]}` - {$amount}, ";
            if($planetrow[$reslist['resources'][$resource]] < $amount) $intError = 2;
            $newrow[$reslist['resources'][$resource]] -= $amount;
          }
        }
        if(!$intError){
          $amountDM = intval($amountDM);
          $newrow[$reslist['resources'][$exchangeTo]] += $value;

          $qry .= "`{$reslist['resources'][$exchangeTo]}` = `{$reslist['resources'][$exchangeTo]}` + {$value} WHERE `id` = {$planetrow['id']};";
          doquery($qry);

          $planetrow = $newrow;
        }
      }else{
        $intError = 1;
      }
      $message = parsetemplate(gettemplate('message_body'), array('title' => $lang['eco_mrk_error_title'], 'mes' => $error_list[$intError]));
    }

    $template = gettemplate('market_trader', true);
    $data = array(
      'avail' => array( floor($planetrow['metal']), floor($planetrow['crystal']), floor($planetrow['deuterium']), $user['rpg_points'], ),
      'name'=> array( $lang['Metal'], $lang['Crystal'], $lang['Deuterium'], $lang['dark_matter'], ),
    );
    if($intError){
      for($i=0; $i<=3; $i++)
        $data['spend'][$i] = abs(intval($tradeList[$i]));

      $parse['exchangeTo'] = $exchangeTo;
    }

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
    $page_title .= " - {$lang['eco_mrk_scraper']}";

    $error_list = array(
      1 => $lang['eco_mrk_error_noDM'],
      2 => $lang['eco_mrk_error_noShips'],
      3 => $lang['eco_mrk_error_zeroRes'],
    );

    if(is_array($shipList)){
      if($user['rpg_points'] >= $config->rpg_cost_scraper){
        $message .= "{$lang['eco_mrk_scraper_ships']}<ul>";
        $qry = "UPDATE {{planets}} SET ";
        foreach($shipList as $shipID => $shipCount){
          $shipCount = abs($shipCount);
          if($shipCount <= 0) continue;
          if($newrow[$resource[$shipID]] < $shipCount){
            $intError = 2;
            break;
          }
          $qry .= "`{$resource[$shipID]}` = `{$resource[$shipID]}` - {$shipCount}, ";
          $newrow[$resource[$shipID]] -= $shipCount;

          $total['metal'] += floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal)*$shipCount;
          $total['crystal'] += floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal)*$shipCount;
          $total['deuterium'] += floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium)*$shipCount;

          $newrow['metal'] += floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal)*$shipCount;
          $newrow['crystal'] += floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal)*$shipCount;
          $newrow['deuterium'] += floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium)*$shipCount;

          $message .= "<li>{$lang['tech'][$shipID]}:";
          $message .= " {$shipCount}";
        }

        if(!$intError){
          $message .= "</ul>";
          if(array_sum($total) > 0){
            $message .= "{$lang['eco_mrk_scraper_got']}<ul>";
            $message .= "<li>{$lang['sys_metal']}: {$total['metal']}";
            $message .= "<li>{$lang['sys_crystal']}: {$total['crystal']}";
            $message .= "<li>{$lang['sys_deuterium']}: {$total['deuterium']}";
            $message .= "</ul>";

            $qry .= "`metal` = `metal` + {$total['metal']}, ";
            $qry .= "`crystal` = `crystal` + {$total['crystal']}, ";
            $qry .= "`deuterium` = `deuterium` + {$total['deuterium']}";
            doquery($qry);

            $rpg_deduct = $config->rpg_cost_scraper;

            $planetrow = $newrow;
          }else{
            $intError = 3;
          }
        }
      }else{
        $intError = 1;
      }

      if($intError){
        $message = parsetemplate(gettemplate('message_body'), array('title' => $lang['eco_mrk_error_title'], 'mes' => $error_list[$intError]));
        foreach($shipList as $shipID => $shipCount){
          $data['ships'][$shipID] = abs(intval($shipCount));
        }
      }else{
        $message = parsetemplate(gettemplate('message_body'), array('title' => $page_title, 'mes' => "<div align=left>{$message}</div>"));
      }
    }

    $template = gettemplate('market_scraper', true);

    foreach($reslist['fleet'] as $shipID){
      if($planetrow[$resource[$shipID]]){
        $template->assign_block_vars('ships', array(
          'ID' => $shipID,
          'COUNT' => $planetrow[$resource[$shipID]],
          'NAME' => $lang['tech'][$shipID],
          'METAL' => floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal),
          'CRYSTAL' => floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal),
          'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium),
          'SELL' => intval($data['ships'][$shipID]),
        ));
        $ships .= "Array($shipID, ";
        $ships .= floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal) . ", ";
        $ships .= floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal) . ", ";
        $ships .= floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium) . ", ";
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
    $template = gettemplate('market', true);
    break;
}

if(!$intError && $rpg_deduct){
  doquery("UPDATE {{users}} SET `rpg_points` = `rpg_points` - {$rpg_deduct} WHERE `id` = {$user['id']};");
  $user['rpg_points'] -= $rpg_deduct;
}

$template->assign_var('message', $message);
display(parsetemplate($template, $parse), $page_title);
?>