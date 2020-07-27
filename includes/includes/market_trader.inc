<?php

if((!defined('SN_IN_MARKET') || SN_IN_MARKET !== true) && (!defined('SN_IN_ALLY') || SN_IN_ALLY !== true)) {
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

function eco_mrk_trader($user, $planetrow = null) {
  global $debug, $mode;
  // global $lang;

  // $page_title = " - {$lang['eco_mrk_trader']}";
  $template = SnTemplate::gettemplate('market_trader', true);

  $intError = MARKET_DEAL;
  $planetrow = isset($planetrow) ? $planetrow : $user;

  global $page_title, $config, $lang;

  lng_include('market');

  $rates = array(
    RES_METAL => $config->rpg_exchange_metal,
    RES_CRYSTAL => $config->rpg_exchange_crystal,
    RES_DEUTERIUM => $config->rpg_exchange_deuterium,
    RES_DARK_MATTER => $config->rpg_exchange_darkMatter
  );

  // $dm_db_name = pname_resource_name(RES_DARK_MATTER);
  $exchangeTo = in_array($exchangeTo = sys_get_param_int('exchangeTo'), sn_get_groups('resources_trader')) ? $exchangeTo : 0;
  if($exchangeTo && is_array($tradeList = $_POST['spend'])) {
    $value = 0;
    $qry = array();

    sn_db_transaction_start();
    if($planetrow['id_owner']) {
      $global_data = sys_o_get_updated($user, $planetrow, SN_TIME_NOW);
      $planetrow = $global_data['planet'];
    }
    else {
      // Locking user record
      $user = db_user_by_id($user['id'], true);
    }

    foreach(sn_get_groups('resources_trader') as $resource_id) {
      $amount = floatval($tradeList[$resource_id]);
      if($amount < 0) {
        $debug->error('Trying to supply negative resource amount on Black Market Page', 'Hack Attempt', 305);
      }

      if($resource_id == RES_DARK_MATTER && $exchangeTo == RES_DARK_MATTER) {
        continue;
      }

      $resource_db_name = pname_resource_name($resource_id);
      if($exchangeTo == RES_DARK_MATTER) {
        $sign = '+';
        $amount = floor($tradeList[RES_DARK_MATTER] / 3 * $rates[RES_DARK_MATTER] / $rates[$resource_id]);
        $value += $amount;
      } else {
        $value += floor($amount * $rates[$resource_id] / $rates[$exchangeTo]);
        if($resource_id == RES_DARK_MATTER) {
          $amount = 0;
        } else {
          if(mrc_get_level($user, $planetrow, $resource_id, true) < $amount) {
            $intError = MARKET_NO_RESOURCES;
            break;
          }

          $sign = '-';
        }
      }

      if($amount) {
        $qry[] = "`{$resource_db_name}` = `{$resource_db_name}` {$sign} {$amount}";
      }
    }

    if($exchangeTo != RES_DARK_MATTER) {
      $amount = floor($value);
      $exchange_to_db_name = pname_resource_name($exchangeTo);
      $qry[] = "`{$exchange_to_db_name}` = `{$exchange_to_db_name}` + {$amount}";
    }

    $operation_cost = $config->rpg_cost_trader * ($exchangeTo == RES_DARK_MATTER ? 3 : 1) + $tradeList[RES_DARK_MATTER];

    $intError = $value <= 0 ? MARKET_ZERO_DEAL : $intError;
    $intError = mrc_get_level($user, null, RES_DARK_MATTER) < $operation_cost ? MARKET_NO_DM : $intError;

    if($intError == MARKET_DEAL) {
      $qry = implode(', ', $qry);
      $table = $planetrow['id_owner'] ? 'planets' : 'users';

      doquery("UPDATE {{{$table}}} SET {$qry} WHERE `id` = {$planetrow['id']} LIMIT 1;");
      rpg_points_change($user['id'], RPG_MARKET_EXCHANGE, -$operation_cost, "Using Black Market page {$lang['eco_mrk_trader']}");
      sn_db_transaction_commit();

      $intError = MARKET_DEAL_TRADE;
      $_SERVER['REQUEST_URI'] = ($has_message = strpos($_SERVER['REQUEST_URI'], '&message=')) ? substr($_SERVER['REQUEST_URI'], 0, $has_message) : $_SERVER['REQUEST_URI'];
      sys_redirect("{$_SERVER['REQUEST_URI']}&message={$intError}");
    }
    sn_db_transaction_rollback();
    $template->assign_block_vars('result', array(
      'STATUS' => $intError == MARKET_DEAL ? ERR_NONE : ERR_ERROR,
      'MESSAGE' => $lang['eco_mrk_errors'][$intError],
    ));
  }

  $template->assign_vars(array(
    'EXCHANGE_TO_RESOURCE_ID' => $exchangeTo,
  ));

  foreach(sn_get_groups('resources_trader') as $resource_id) {
    if($resource_id == RES_DARK_MATTER) {
      $amount = floor(mrc_get_level($user, null, RES_DARK_MATTER) - $config->rpg_cost_trader);
    } else {
      $amount = floor(mrc_get_level($user, $planetrow, $resource_id));
    }

    $template->assign_block_vars('resources', array(
      'ID'         => $resource_id,
      'NAME'       => $lang['tech'][$resource_id],
      'AVAIL'      => $amount,
      'AVAIL_TEXT' => HelperString::numberFloorAndFormat($amount),
      'SPENT'      => ($intError == MARKET_DEAL) ? 0 : max(0, $tradeList[$resource_id]),
      'RATE'       => $rates[$resource_id],
    ));
  }

  $template->assign_vars(array(
    'rpg_cost_trader'   => $config->rpg_cost_trader,
    // 'message' => $message,
    'MODE' => $mode,
  ));

  SnTemplate::display($template, $page_title);
}
