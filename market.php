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
includeLang('fleet');

ini_set('display_errors', 1);

$mode = intval($_GET['mode'] ? $_GET['mode'] : $_POST['mode']);
$tradeList = $_POST['spend'];
$shipList  = $_POST['ships'];
$exchangeTo = intval($_POST['exchangeTo']);
$exchangeTo = ($exchangeTo<3) ? $exchangeTo : 0 ;

$page_title = "{$lang['eco_mrk_title']}";

$stock = sys_fleetUnPack($config->eco_stockman_fleet);

$newrow = $planetrow;
$newstock = $stock;

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
      if($user['rpg_points'] >= $config->rpg_cost_trader + $tradeList[3]){
        $rates = array($config->rpg_exchange_metal, $config->rpg_exchange_crystal, $config->rpg_exchange_deuterium, $config->rpg_exchange_darkMatter);

        $qry = "UPDATE {{planets}} SET ";
        foreach($tradeList as $res_name => $amount){
          $amount = abs(intval($amount));
          $value += $amount * $rates[$res_name] / $rates[$exchangeTo];

          if($res_name == 3){
            $amountDM = $amount;
          }else{
            $qry .= "`{$reslist['resources'][$res_name]}` = `{$reslist['resources'][$res_name]}` - {$amount}, ";
            if($planetrow[$reslist['resources'][$res_name]] < $amount) $intError = 2;
            $newrow[$reslist['resources'][$res_name]] -= $amount;
          }
        }
        if(!$intError){
          $rpg_deduct = $config->rpg_cost_trader + $tradeList[3];
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
    $template->assign_vars(array(
      'rpg_exchange_metal'      => $config->rpg_exchange_metal,
      'rpg_exchange_crystal'    => $config->rpg_exchange_crystal,
      'rpg_exchange_deuterium'  => $config->rpg_exchange_deuterium,
      'rpg_exchange_darkMatter' => $config->rpg_exchange_darkMatter,
    ));

    $data = array(
      'avail' => array( floor($planetrow['metal']), floor($planetrow['crystal']), floor($planetrow['deuterium']), $user['rpg_points'] - $config->rpg_cost_trader, ),
      'name'=> array( $lang['Metal'], $lang['Crystal'], $lang['Deuterium'], $lang['dark_matter'], ),
    );
    if($intError){
      for($i=0; $i<=3; $i++)
        $data['spend'][$i] = abs(intval($tradeList[$i]));

      $template->assign_var('exchangeTo', $exchangeTo);
    }

    for($i=0; $i<=3; $i++){
      $template->assign_block_vars('resources', array(
        'ID'    => $i,
        'NAME'  => $data['name'][$i],
        'AVAIL' => $data['avail'][$i],
        'SPEND' => $data['spend'][$i],
      ));
      $avail_res .= $data['avail'][$i] . ', ';
    }
    $avail_res .= '0';
    $template->assign_var('resources', $avail_res);
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
          if($newrow[$sn_data[$shipID]['name']] < $shipCount){
            $intError = 2;
            break;
          }
          $ship_db_name = $sn_data[$shipID]['name'];
          $qry .= "`{$ship_db_name}` = `{$ship_db_name}` - {$shipCount}, ";
          $newrow[$ship_db_name] -= $shipCount;
          $newstock[$shipID] += $shipCount;

          $resTemp['metal'] = floor($pricelist[$shipID]['metal']*$shipCount*$config->rpg_scrape_metal);
          $resTemp['crystal'] = floor($pricelist[$shipID]['crystal']*$shipCount*$config->rpg_scrape_crystal);
          $resTemp['deuterium'] = floor($pricelist[$shipID]['deuterium']*$shipCount*$config->rpg_scrape_deuterium);

          foreach($resTemp as $resID => $resCount)
            $total[$resID] += $resCount;

          $message .= "<li>{$lang['tech'][$shipID]}: {$shipCount}";
        }

        if(!$intError){
          $message .= "</ul>";
          if(array_sum($total) > 0){
            $message .= "{$lang['eco_mrk_scraper_res']}<ul>";
            foreach($total as $resID => $resCount){
              $newrow[$resID] += $resCount;
              $qry .= "`{$resID}` = `{$resID}` + {$resCount}, ";
              $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
            }
            $message .= "</ul>";
            $qry .= "`id`=`id` WHERE `id` = {$planetrow['id']};";
            doquery($qry);

            $rpg_deduct = $config->rpg_cost_scraper;

            $planetrow = $newrow;
            $stock = $newstock;

            $config->eco_stockman_fleet = sys_fleetPack($stock);
            $config->db_saveItem('eco_stockman_fleet');
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
    $template = gettemplate('market_fleet', true);
    $template->assign_var('rpg_cost', $config->rpg_cost_scraper);

    foreach($reslist['fleet'] as $shipID){
      if($planetrow[$sn_data[$shipID]['name']] > 0){
        $template->assign_block_vars('ships', array(
          'ID' => $shipID,
          'COUNT' => $planetrow[$sn_data[$shipID]['name']],
          'NAME' => $lang['tech'][$shipID],
          'METAL' => floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal),
          'CRYSTAL' => floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal),
          'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium),
          'AMOUNT' => intval($data['ships'][$shipID]),
        ));
        /*
        $ships .= "Array($shipID, ";
        $ships .= floor($pricelist[$shipID]['metal']*$config->rpg_scrape_metal) . ", ";
        $ships .= floor($pricelist[$shipID]['crystal']*$config->rpg_scrape_crystal) . ", ";
        $ships .= floor($pricelist[$shipID]['deuterium']*$config->rpg_scrape_deuterium) . ", ";
        $ships .= $planetrow[$sn_data[$shipID]['name']];
        $ships .= '), ';
        */
      }
    }
    /*
    if($ships){
      $ships .= "1";
      $template->assign_var('ships', $ships);
    };
    */
  break;

  case 3: // S/H ship seller
    $page_title .= " - {$lang['eco_mrk_stockman']}";

    $error_list = array(
      1 => $lang['eco_mrk_error_noDM'],
      2 => $lang['eco_mrk_error_noStock'],
      3 => $lang['eco_mrk_error_zeroResStock'],
      4 => $lang['eco_mrk_error_noResources'],
    );

    if(is_array($shipList)){
      if($user['rpg_points'] >= $config->rpg_cost_stockman){
        $message .= "{$lang['eco_mrk_stockman_ships']}<ul>";
        $qry = "UPDATE {{planets}} SET ";
        foreach($shipList as $shipID => $shipCount){
          $shipCount = abs($shipCount);
          if($shipCount <= 0) continue;
          if($stock[$shipID] < $shipCount){
            $intError = 2;
            break;
          }
          $qry .= "`{$sn_data[$shipID]['name']}` = `{$sn_data[$shipID]['name']}` + {$shipCount}, ";
          $newrow[$sn_data[$shipID]['name']] += $shipCount;
          $newstock[$shipID] -= $shipCount;

          $resTemp['metal'] = floor($pricelist[$shipID]['metal']*$shipCount/$config->rpg_scrape_metal);
          $resTemp['crystal'] = floor($pricelist[$shipID]['crystal']*$shipCount/$config->rpg_scrape_crystal);
          $resTemp['deuterium'] = floor($pricelist[$shipID]['deuterium']*$shipCount/$config->rpg_scrape_deuterium);

          foreach($resTemp as $resID => $resCount)
            $total[$resID] += $resCount;

          $message .= "<li>{$lang['tech'][$shipID]}: {$shipCount}";
        }

        foreach($total as $resID => $resCount)
          if($newrow[$resID] < $resCount){
            $intError = 4;
            $debug->warning('Trying to use bug in s/h market', 'S/H Ship Market', 300);
            break;
          }

        if(!$intError){
          $message .= "</ul>";
          if(array_sum($total) > 0){
            $message .= "{$lang['eco_mrk_stockman_res']}<ul>";

            foreach($total as $resID => $resCount){
              $newrow[$resID] -= $resCount;
              $qry .= "`{$resID}` = `{$resID}` - {$resCount}, ";
              $message .= "<li>" . $lang['sys_' . $resID] . ": {$resCount}";
            }
            $message .= "</ul>";
            $qry .= "`id`=`id` WHERE `id` = {$planetrow['id']};";
            doquery($qry);

            $rpg_deduct = $config->rpg_cost_stockman;

            $planetrow = $newrow;
            $stock = $newstock;

            $config->eco_stockman_fleet = sys_fleetPack($stock);
            $config->db_saveItem('eco_stockman_fleet');
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

    $template = gettemplate('market_fleet', true);
    $template->assign_var('rpg_cost', $config->rpg_cost_stockman);

    if($stock){
      foreach($stock as $shipID => $shipCount){
        if($shipCount > 0){
          $template->assign_block_vars('ships', array(
            'ID'        => $shipID,
            'COUNT'     => $shipCount,
            'NAME'      => $lang['tech'][$shipID],
            'METAL'     => floor($pricelist[$shipID]['metal']/$config->rpg_scrape_metal),
            'CRYSTAL'   => floor($pricelist[$shipID]['crystal']/$config->rpg_scrape_crystal),
            'DEUTERIUM' => floor($pricelist[$shipID]['deuterium']/$config->rpg_scrape_deuterium),
            'AMOUNT'    => intval($data['ships'][$shipID]),
          ));
          /*
          $ships .= "Array($shipID, ";
          $ships .= floor($pricelist[$shipID]['metal']/$config->rpg_scrape_metal) . ", ";
          $ships .= floor($pricelist[$shipID]['crystal']/$config->rpg_scrape_crystal) . ", ";
          $ships .= floor($pricelist[$shipID]['deuterium']/$config->rpg_scrape_deuterium) . ", ";
          $ships .= $shipCount;
          $ships .= '), ';
          */
        }
      }
      /*
      $ships .= "1";
      $template->assign_var('ships', $ships);
      */
    };
  break;

  case 4: // Banker
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
  rpg_pointsAdd($user['id'], -($rpg_deduct), "Using Black Market page {$mode}");
  $user['rpg_points'] -= $rpg_deduct;
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