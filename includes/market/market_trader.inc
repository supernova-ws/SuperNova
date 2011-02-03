<?php

$page_title .= " - {$lang['eco_mrk_trader']}";

$rates = array(
  RES_METAL => $config->rpg_exchange_metal,
  RES_CRYSTAL => $config->rpg_exchange_crystal,
  RES_DEUTERIUM => $config->rpg_exchange_deuterium,
  RES_DARK_MATTER => $config->rpg_exchange_darkMatter
);

if(is_array($tradeList) && isset($exchangeTo))
{
  $intError = MARKET_DEAL_TRADE;

  $total_amount = 0;
  foreach($tradeList as $amount)
  {
    if($amount < 0)
    {
      $debug->warning('Trying to supply negative resource amount on Black Market Page: ' . dump($tradeList), 'Hack Attempt', 305);
      die();
    }
    else
    {
      $total_amount += $amount;
    }
  }

  if($intError == MARKET_DEAL)
  {
    doquery('START TRANSACTION;');
    $global_data = sys_o_get_updated($user, $planetrow, $time_now);
    $planetrow = $global_data['planet'];
    foreach($tradeList as $resource_id => $amount)
    {
      $amount = max(0, intval($amount));
      $value += $amount * $rates[$resource_id] / $rates[$exchangeTo];

      if($resource_id == RES_DARK_MATTER)
      {
        $amountDM = $amount;
      }
      else
      {
        $qry .= "`{$sn_data[$resource_id]['name']}` = `{$sn_data[$resource_id]['name']}` - '{$amount}', ";
        if ($planetrow[$sn_data[$resource_id]['name']] < $amount)
        {
          $intError = MARKET_NO_RESOURCES;
          break;
        }
        $newrow[$sn_data[$resource_id]['name']] = $planetrow[$sn_data[$resource_id]['name']] - $amount;
      }
    }
  }

  if ($total_amount <= 0)
  {
    $intError = MARKET_ZERO_DEAL;
  }

  if($user['rpg_points'] < $config->rpg_cost_trader + $tradeList[RES_DARK_MATTER])
  {
    $intError = MARKET_NO_DM;
  }

  if($intError == MARKET_DEAL)
  {
    $rpg_deduct = $config->rpg_cost_trader + $tradeList[RES_DARK_MATTER];
    $amountDM = intval($amountDM);
    $newrow[$sn_data[$exchangeTo]['name']] = $planetrow[$sn_data[$exchangeTo]['name']] + $value;

    $qry = "UPDATE {{planets}} SET {$qry} `{$sn_data[$exchangeTo]['name']}` = `{$sn_data[$exchangeTo]['name']}` + '{$value}' WHERE `id` = {$planetrow['id']} LIMIT 1;";
    doquery($qry);

    $planetrow = array_merge($planetrow, $newrow);
  }
pdump($intError);
  if($intError == MARKET_DEAL_TRADE)
  {
    rpg_points_change($user['id'], -($rpg_deduct), "Using Black Market page {$mode}");
    doquery('COMMIT;');

    header("Location: {$_SERVER['PHP_SELF']}?mode={$mode}&message={$intError}");
    ob_end_flush();
    die();
  }
  doquery('ROLLBACK;');
  $message = parsetemplate(gettemplate('message_body'), array('title' => $intError == MARKET_DEAL ? $page_title : $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
}

$template = gettemplate('market_trader', true);

$resource_list = array();
foreach($sn_data['groups']['resources_loot'] as $resource_id)
{
  $config_name = "rpg_exchange_{$sn_data[$resource_id]['name']}";
  $res_amount = floor($planetrow[$sn_data[$resource_id]['name']]);
  $template->assign_block_vars('resources', array(
    'ID'    => $resource_id,
    'NAME'  => $lang['tech'][$resource_id],
    'AVAIL' => $res_amount,
    'SPENT' => $intError == MARKET_DEAL ? 0 : max(0, intval($tradeList[$resource_id])),
    'RATE'  => $rates[$resource_id],
  ));
}

$res_amount = $user['rpg_points'] - $config->rpg_cost_trader;
$template->assign_block_vars('resources', array(
  'ID'    => RES_DARK_MATTER,
  'NAME'  => $lang['tech'][RES_DARK_MATTER],
  'AVAIL' => $res_amount,
  'SPENT' => $intError != MARKET_DEAL ? max(0, intval($tradeList[RES_DARK_MATTER])) : 0,
  'RATE'  => $config->rpg_exchange_darkMatter
));

$template->assign_vars(array(
  'exchangeTo' => $exchangeTo,
));

?>
