<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= " - {$lang['eco_mrk_trader']}";

$tradeList = $_POST['spend'];
$exchangeTo = sys_get_param_int('exchangeTo');
$exchangeTo = in_array($exchangeTo, $sn_data['groups']['resources_trader']) ? $exchangeTo : 0;

$rates = array(
  RES_METAL => $config->rpg_exchange_metal,
  RES_CRYSTAL => $config->rpg_exchange_crystal,
  RES_DEUTERIUM => $config->rpg_exchange_deuterium,
  RES_DARK_MATTER => $config->rpg_exchange_darkMatter
);

if(is_array($tradeList) && isset($exchangeTo))
{
  $value = 0;
  $qry = array();

  doquery('START TRANSACTION;');
  $global_data = sys_o_get_updated($user, $planetrow, $time_now);
  $planetrow = $global_data['planet'];

  foreach($sn_data['groups']['resources_trader'] as $resource_id)
  {
    $amount = floatval($tradeList[$resource_id]);
    if($amount < 0)
    {
      $debug->error('Trying to supply negative resource amount on Black Market Page', 'Hack Attempt', 305);
    }

    if($resource_id == RES_DARK_MATTER && $exchangeTo == RES_DARK_MATTER)
    {
      continue;
    }

    if($exchangeTo == RES_DARK_MATTER)
    {
      $sign = '+';
      $amount = floor($tradeList[RES_DARK_MATTER] / 3 * $rates[RES_DARK_MATTER] / $rates[$resource_id]);
      $value += $amount;
    }
    else
    {
      $value += floor($amount * $rates[$resource_id] / $rates[$exchangeTo]);
      if($resource_id == RES_DARK_MATTER)
      {
        $amount = 0;
      }
      else
      {
        if($planetrow[$sn_data[$resource_id]['name']] < $amount)
        {
          $intError = MARKET_NO_RESOURCES;
          break;
        }

        $sign = '-';
      }
    }

    if($amount)
    {
      $qry[] = "`{$sn_data[$resource_id]['name']}` = `{$sn_data[$resource_id]['name']}` {$sign} {$amount}";
    }
  }

  if($exchangeTo != RES_DARK_MATTER)
  {
    $amount = floor($value);
    $qry[] = "`{$sn_data[$exchangeTo]['name']}` = `{$sn_data[$exchangeTo]['name']}` + {$amount}";
  }

  $operation_cost = $config->rpg_cost_trader * ($exchangeTo == RES_DARK_MATTER ? 3 : 1) + $tradeList[RES_DARK_MATTER];

  $intError = $value <= 0 ? MARKET_ZERO_DEAL : $intError;
  $intError = $user[$sn_data[RES_DARK_MATTER]['name']] < $operation_cost ? MARKET_NO_DM : $intError;

  if($intError == MARKET_DEAL)
  {
    $qry = implode(', ', $qry);

    doquery("UPDATE {{planets}} SET {$qry} WHERE `id` = {$planetrow['id']} LIMIT 1;");
    rpg_points_change($user['id'], RPG_MARKET, -$operation_cost, "Using Black Market page {$lang['eco_mrk_trader']}");
    doquery('COMMIT;');

    $intError = MARKET_DEAL_TRADE;
    header("Location: {$_SERVER['PHP_SELF']}?mode={$mode}&message={$intError}");
    ob_end_flush();
    die();
  }
  doquery('ROLLBACK;');
  $message = parsetemplate(gettemplate('message_body'), array('title' => $intError == MARKET_DEAL ? $page_title : $lang['eco_mrk_error_title'], 'mes' => $lang['eco_mrk_errors'][$intError]));
}

$template = gettemplate('market_trader', true);
$template->assign_vars(array(
  'exchangeTo' => $exchangeTo,
));

foreach($sn_data['groups']['resources_trader'] as $resource_id)
{
  if($resource_id == RES_DARK_MATTER)
  {
    $amount = floor($user[$sn_data[RES_DARK_MATTER]['name']] - $config->rpg_cost_trader);
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
