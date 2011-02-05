<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= " - {$lang['eco_mrk_trader']}";

$tradeList = $_POST['spend'];
$exchangeTo = sys_get_param_int('exchangeTo');
$exchangeTo = in_array($exchangeTo, $sn_data['groups']['resources_loot']) ? $exchangeTo : 0 ;

$rates = array(
  RES_METAL => $config->rpg_exchange_metal,
  RES_CRYSTAL => $config->rpg_exchange_crystal,
  RES_DEUTERIUM => $config->rpg_exchange_deuterium,
  RES_DARK_MATTER => $config->rpg_exchange_darkMatter
);

if(is_array($tradeList) && isset($exchangeTo))
{
  $value = 0;
  $qry = '';

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planetrow, $time_now);
  $planetrow = $global_data['planet'];

  foreach($tradeList as $resource_id => $amount)
  {
    if($amount < 0)
    {
      $debug->error('Trying to supply negative resource amount on Black Market Page', 'Hack Attempt', 305);
    }

    $value += $amount * $rates[$resource_id] / $rates[$exchangeTo];
    if($resource_id != RES_DARK_MATTER)
    {
      if ($planetrow[$sn_data[$resource_id]['name']] < $amount)
      {
        $intError = MARKET_NO_RESOURCES;
        break;
      }

      $qry .= "`{$sn_data[$resource_id]['name']}` = `{$sn_data[$resource_id]['name']}` - '{$amount}', ";
//      $newrow[$sn_data[$resource_id]['name']] = $planetrow[$sn_data[$resource_id]['name']] - $amount;
    }
  }

  $intError = ($intError == MARKET_DEAL) && ($value <= 0) ? MARKET_ZERO_DEAL : $intError;
  $intError = ($intError == MARKET_DEAL) && ($user['rpg_points'] < $config->rpg_cost_trader + $tradeList[RES_DARK_MATTER]) ? MARKET_NO_DM : $intError;

  if($intError == MARKET_DEAL)
  {
    doquery("UPDATE {{planets}} SET {$qry} `{$sn_data[$exchangeTo]['name']}` = `{$sn_data[$exchangeTo]['name']}` + '{$value}' WHERE `id` = {$planetrow['id']} LIMIT 1;");
    rpg_points_change($user['id'], -($config->rpg_cost_trader + $tradeList[RES_DARK_MATTER]), "Using Black Market page {$lang['eco_mrk_trader']}");
    doquery('COMMIT;');

//    $newrow[$sn_data[$exchangeTo]['name']] = $planetrow[$sn_data[$exchangeTo]['name']] + $value;
//    $planetrow = array_merge($planetrow, $newrow);

    $intError = MARKET_DEAL_TRADE;
    header("Location: {$_SERVER['PHP_SELF']}?mode={$mode}&message={$intError}");
    ob_end_flush();
    die();
  }
  doquery('ROLLBACK;');
  $message = parsetemplate(gettemplate('message_body'), array('title' => $intError == MARKET_DEAL ? $page_title : $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
}

$template = gettemplate('market_trader', true);
$template->assign_vars(array('exchangeTo' => $exchangeTo,));

foreach($sn_data['groups']['resources_trader'] as $resource_id)
{
  if($resource_id == RES_DARK_MATTER)
  {
    $amount = floor($user['rpg_points'] - $config->rpg_cost_trader);
  }
  else
  {
    $amount = floor($planetrow[$sn_data[$resource_id]['name']]);
  }
  $template->assign_block_vars('resources', array(
    'ID'         => $resource_id,
    'NAME'       => $lang['tech'][$resource_id],
    'AVAIL'      => $amount,
    'AVAIL_TEXT' => pretty_number($amount),
    'SPENT'      => ($intError == MARKET_DEAL) ? 0 : max(0, $tradeList[$resource_id]),
    'RATE'       => $rates[$resource_id],
  ));
}

?>
