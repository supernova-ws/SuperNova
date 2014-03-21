<?php

lng_include('mrc_mercenary');

function mrc_officer_accessible(&$user, $mercenary_id)
{
  global $sn_data, $config;

  if($config->empire_mercenary_temporary || $sn_data[$mercenary_id]['type'] == UNIT_PLANS)
  {
    return true;
  }

  if(isset($sn_data[$mercenary_id]['require']))
  {
    foreach($sn_data[$mercenary_id]['require'] as $unit_id => $unit_level)
    {
      if(mrc_get_level($user, null, $unit_id) < $unit_level)
      {
        return false;
      }
    }
  }

  return true;
}

function mrc_mercenary_hire($mode, $user, $mercenary_id)
{
  global $time_now, $sn_data, $config, $lang, $sn_pwr_buy_discount;

  try
  {
    $is_permanent = $mode == UNIT_PLANS || !$config->empire_mercenary_temporary;
    $cost_alliance_multiplyer = (SN_IN_ALLY === true && $mode == UNIT_PLANS ? $config->ali_bonus_members : 1);
    $cost_alliance_multiplyer = $cost_alliance_multiplyer >= 1 ? $cost_alliance_multiplyer : 1;
    if(!in_array($mercenary_id, sn_get_groups($mode == UNIT_PLANS ? 'plans' : 'mercenaries')))
    {
      throw new Exception($lang['mrc_msg_error_wrong_mercenary'], ERR_ERROR);
    }

    if(!mrc_officer_accessible($user, $mercenary_id))
    {
      throw new Exception($lang['mrc_msg_error_requirements'], ERR_ERROR);
    }

    $mercenary_level = sys_get_param_int('mercenary_level');
    if($mercenary_level < 0 || $mercenary_level > $sn_data[$mercenary_id]['max'])
    {
      throw new Exception($lang['mrc_msg_error_wrong_level'], ERR_ERROR);
    }

    if($mercenary_level && !array_key_exists($mercenary_period = sys_get_param_int('mercenary_period'), $sn_pwr_buy_discount))
    {
      throw new Exception($lang['mrc_msg_error_wrong_period'], ERR_ERROR);
    }

    doquery('START TRANSACTION;');

    $mercenary_level_old = mrc_get_level($user, $planetrow, $mercenary_id, true, true);
    if($config->empire_mercenary_temporary && $mercenary_level_old && $mercenary_level)
    {
      throw new Exception($lang['mrc_msg_error_already_hired'], ERR_ERROR); // Can't hire already hired temp mercenary - dismiss first
    }
    elseif($config->empire_mercenary_temporary && !$mercenary_level_old && !$mercenary_level)
    {
      throw new Exception('', ERR_NONE); // Can't dismiss (!$mercenary_level) not hired (!$mercenary_level_old) temp mercenary. But no error
    }

    if($mercenary_level)
    {
      $darkmater_cost = eco_get_total_cost($mercenary_id, $mercenary_level);
      if(!$config->empire_mercenary_temporary && $mercenary_level_old)
      {
       $darkmater_cost_old = eco_get_total_cost($mercenary_id, $mercenary_level_old);
       $darkmater_cost[BUILD_CREATE][RES_DARK_MATTER] -= $darkmater_cost_old[BUILD_CREATE][RES_DARK_MATTER];
      }
      $darkmater_cost = ceil($darkmater_cost[BUILD_CREATE][RES_DARK_MATTER] * $mercenary_period * $sn_pwr_buy_discount[$mercenary_period] / $config->empire_mercenary_base_period);
    }
    else
    {
      $darkmater_cost = 0;
    }
    $darkmater_cost *= $cost_alliance_multiplyer;

    if($user[$sn_data[RES_DARK_MATTER]['name']] < $darkmater_cost)
    {
      throw new Exception($lang['mrc_msg_error_no_resource'], ERR_ERROR);
    }

    if(($darkmater_cost && $mercenary_level) || !$is_permanent)
    {
      //doquery("DELETE FROM {{powerup}} WHERE powerup_user_id = {$user['id']} AND powerup_unit_id = {$mercenary_id} LIMIT 1;");
      doquery("DELETE FROM {{unit}} WHERE unit_player_id = {$user['id']} AND unit_snid = {$mercenary_id} LIMIT 1;");
    }
    if($darkmater_cost && $mercenary_level)
    {
      $time_start = $is_permanent ? 0 : $time_now;
      $time_end = $is_permanent ? 0 : $time_now + $mercenary_period;

      doquery(
        "INSERT INTO
          {{unit}}
        SET
          unit_player_id = {$user['id']},
          unit_location_type = " . LOC_USER . ",
          unit_location_id = {$user['id']},
          unit_type = {$mode},
          unit_snid = {$mercenary_id},
          unit_level = {$mercenary_level},
          unit_time_start = FROM_UNIXTIME({$time_start}),
          unit_time_finish = FROM_UNIXTIME({$time_end});"
      );

      rpg_points_change($user['id'], $mode == UNIT_PLANS ? RPG_PLANS : RPG_MERCENARY, -($darkmater_cost), "Spent for officer {$lang['tech'][$mercenary_id]} ID {$mercenary_id}");
    }
    doquery('COMMIT;');
    sys_redirect($_SERVER['REQUEST_URI']);
  }
  catch (Exception $e)
  {
    doquery('ROLLBACK;');
    $operation_result = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
  }

  return $operation_result;
}

function mrc_mercenary_render($user)
{
  global $time_now, $sn_data, $config, $lang, $sn_pwr_buy_discount;

  $mode = sys_get_param_int('mode', UNIT_MERCENARIES);
  $mode = in_array($mode, array(UNIT_MERCENARIES, UNIT_PLANS)) ? $mode : UNIT_MERCENARIES;
  $is_permanent = $mode == UNIT_PLANS || !$config->empire_mercenary_temporary;

  if($mercenary_id = sys_get_param_int('mercenary_id'))
  {
    $operation_result = mrc_mercenary_hire($mode, $user, $mercenary_id);
  }

  lng_include('infos');

  $template = gettemplate('mrc_mercenary_hire', true);

  if(!empty($operation_result))
  {
    $template->assign_block_vars('result', $operation_result);
  }

  foreach($sn_pwr_buy_discount as $hire_period => $hire_discount)
  {
    $template->assign_block_vars('period', array(
      'LENGTH'   => $hire_period,
      'TEXT'     => $lang['mrc_period_list'][$hire_period],
      'DISCOUNT' => $hire_period / $config->empire_mercenary_base_period * $hire_discount,
      'SELECTED' => $hire_period == $config->empire_mercenary_base_period,
    ));
  }

  $user_dark_matter = mrc_get_level($user, '', RES_DARK_MATTER);
  $cost_alliance_multiplyer = (SN_IN_ALLY === true && $mode == UNIT_PLANS ? $config->ali_bonus_members : 1);
  $cost_alliance_multiplyer = $cost_alliance_multiplyer >= 1 ? $cost_alliance_multiplyer : 1;
  foreach(sn_get_groups($mode == UNIT_PLANS ? 'plans' : 'mercenaries') as $mercenary_id)
  {
    {
      $mercenary = $sn_data[$mercenary_id];
      $mercenary_bonus = $mercenary['bonus'];
      $mercenary_bonus = $mercenary_bonus >= 0 ? "+{$mercenary_bonus}" : "{$mercenary_bonus}";
      switch($mercenary['bonus_type'])
      {
        case BONUS_PERCENT:
          $mercenary_bonus = "{$mercenary_bonus}% ";
        break;

        case BONUS_ABILITY:
          $mercenary_bonus = '';
        break;

        case BONUS_ADD:
        default:
        break;
      }

      $mercenary_level = mrc_get_level($user, null, $mercenary_id, false, true);
      $mercenary_level_bonus = max(0, mrc_get_level($user, null, $mercenary_id) - $mercenary_level);
      $total_cost_old = 0;
      if($is_permanent)
      {
        $total_cost_old = eco_get_total_cost($mercenary_id, $mercenary_level);
        $total_cost_old = $total_cost_old[BUILD_CREATE][RES_DARK_MATTER] * $cost_alliance_multiplyer;
      }
      $total_cost = eco_get_total_cost($mercenary_id, $mercenary_level + 1);
      $total_cost[BUILD_CREATE][RES_DARK_MATTER] *= $cost_alliance_multiplyer;
      $mercenary_time_finish = $user[$mercenary_id]['unit_time_finish'];
      $template->assign_block_vars('officer', array(
        'ID'          => $mercenary_id,
        'NAME'        => $lang['tech'][$mercenary_id],
        'DESCRIPTION' => $lang['info'][$mercenary_id]['description'],
        'EFFECT'      => $lang['info'][$mercenary_id]['effect'],
        'COST'        => $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old,
        'COST_TEXT'   => pretty_number($total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old, 0, $user_dark_matter),
        'LEVEL'       => $mercenary_level,
        'LEVEL_BONUS' => $mercenary_level_bonus,
        'LEVEL_MAX'   => $mercenary['max'],
        'BONUS'       => $mercenary_bonus,
        'BONUS_TYPE'  => $mercenary['bonus_type'],
        'HIRE_END'    => $mercenary_time_finish && $mercenary_time_finish >= $time_now ? date(FMT_DATE_TIME, $mercenary_time_finish) : '',
        'CAN_BUY'     => mrc_officer_accessible($user, $mercenary_id),
      ));

      $upgrade_cost = 1;
      for($i = $config->empire_mercenary_temporary ? 1 : $mercenary_level + 1; $mercenary['max'] ? ($i <= $mercenary['max']) : $upgrade_cost <= $user_dark_matter; $i++)
      {
        $total_cost = eco_get_total_cost($mercenary_id, $i);
        $total_cost[BUILD_CREATE][RES_DARK_MATTER] *= $cost_alliance_multiplyer;
        /*
        if(!$config->empire_mercenary_temporary && $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old > $user[$sn_data[RES_DARK_MATTER]['name']])
        {
          break;
        }
        */
        $upgrade_cost = $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old;
        $template->assign_block_vars('officer.level', array(
          'VALUE' => $i,
          'PRICE' => $upgrade_cost,
        ));
      }
    }
  }

  $template->assign_vars(array(
    'PAGE_HEADER' => $lang['tech'][$mode],
    'MODE' => $mode,
    'IS_PERMANENT' => intval($is_permanent),
    'EMPIRE_MERCENARY_TEMPORARY' => $config->empire_mercenary_temporary,
    'DARK_MATTER' => $user_dark_matter,
  ));

  display(parsetemplate($template), $lang['tech'][$mode]);
}

?>
