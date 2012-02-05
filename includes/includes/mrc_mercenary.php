<?php

lng_include('mrc_mercenary');

$sn_mrc_hire_discount = array(
  PERIOD_MINUTE    => 1,
  PERIOD_MINUTE_3  => 1,
  PERIOD_MINUTE_5  => 1,
  PERIOD_MINUTE_10 => 1,
  PERIOD_DAY       => 3,
  PERIOD_DAY_3     => 2,
  PERIOD_WEEK      => 1.5,
  PERIOD_WEEK_2    => 1.2,
  PERIOD_MONTH     => 1,
  PERIOD_MONTH_2   => 0.9,
  PERIOD_MONTH_3   => 0.8,
);

function mrc_officer_accessible(&$user, $mercenary_id)
{
  global $sn_data;

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

function mrc_mercenary_hire($user, $mercenary_id)
{
  global $time_now, $sn_data, $config, $lang, $sn_mrc_hire_discount;

  try
  {
    if(!in_array($mercenary_id, $sn_data['groups']['mercenaries']))
    {
      throw new Exception($lang['mrc_msg_error_wrong_mercenary'], ERR_ERROR);
    }

    if(!$config->empire_mercenary_temporary && mrc_officer_accessible($user, $mercenary_id) != 1)
    {
      throw new Exception($lang['mrc_msg_error_requirements'], ERR_ERROR);
    }

    $mercenary_level = sys_get_param_int('mercenary_level');
    if($mercenary_level < 0 || $mercenary_level > $sn_data[$mercenary_id]['max'])
    {
      throw new Exception($lang['mrc_msg_error_wrong_level'], ERR_ERROR);
    }

    if($mercenary_level && !array_key_exists($mercenary_period = sys_get_param_int('mercenary_period'), $sn_mrc_hire_discount))
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
      $darkmater_cost = ceil($darkmater_cost[BUILD_CREATE][RES_DARK_MATTER] * $mercenary_period * $sn_mrc_hire_discount[$mercenary_period] / $config->empire_mercenary_base_period);
    }
    else
    {
      $darkmater_cost = 0;
    }

    if($user[$sn_data[RES_DARK_MATTER]['name']] < $darkmater_cost)
    {
      throw new Exception($lang['mrc_msg_error_no_resource'], ERR_ERROR);
    }

    doquery("DELETE FROM {{powerup}} WHERE powerup_user_id = {$user['id']} AND powerup_unit_id = {$mercenary_id} LIMIT 1;");
    if($darkmater_cost && $mercenary_level)
    {
      $time_start = $config->empire_mercenary_temporary ? $time_now : 0;
      $time_end = $config->empire_mercenary_temporary ? $time_now + $mercenary_period : 0;
      doquery("INSERT INTO {{powerup}} SET powerup_user_id = {$user['id']}, powerup_unit_id = {$mercenary_id}, powerup_unit_level = {$mercenary_level}, powerup_time_start = {$time_start}, powerup_time_finish = {$time_end};");

      rpg_points_change($user['id'], RPG_MERCENARY, -($darkmater_cost), "Spent for officer {$lang['tech'][$mercenary_id]} ID {$mercenary_id}");
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
  global $time_now, $sn_data, $config, $lang, $sn_mrc_hire_discount;

  if($mercenary_id = sys_get_param_int('mercenary_id'))
  {
    $operation_result = mrc_mercenary_hire($user, $mercenary_id);
  }

  lng_include('infos');

  $template = gettemplate('mrc_mercenary_hire', true);

  if(!empty($operation_result))
  {
    $template->assign_block_vars('result', $operation_result);
  }

  foreach($sn_mrc_hire_discount as $hire_period => $hire_discount)
  {
    $template->assign_block_vars('period', array(
      'LENGTH'   => $hire_period,
      'TEXT'     => $lang['mrc_period_list'][$hire_period],
      'DISCOUNT' => $hire_period / $config->empire_mercenary_base_period * $hire_discount,
      'SELECTED' => $hire_period == $config->empire_mercenary_base_period,
    ));
  }

  $total_cost_old = 0;
  foreach($sn_data['groups']['mercenaries'] as $mercenary_id)
  {
  //  if($Result = ($config->empire_mercenary_temporary || mrc_officer_accessible ( $user, $mercenary_id )))
    {
      $mercenary = $sn_data[$mercenary_id];
      $mercenary_bonus = $mercenary['bonus'];
      $mercenary_bonus = $mercenary_bonus >= 0 ? "+{$mercenary_bonus}" : "{$mercenary_bonus}";
      switch($mercenary['bonus_type'])
      {
        case BONUS_PERCENT:
          $mercenary_bonus = "{$mercenary_bonus}% ";
        break;

        case BONUS_ADD:
        break;

        case BONUS_ABILITY:
          $mercenary_bonus = '';
        break;

        default:
        break;
      }

      $mercenary_level = mrc_get_level($user, null, $mercenary_id, false, true);
      if(!$config->empire_mercenary_temporary)
      {
        $total_cost_old = eco_get_total_cost($mercenary_id, $mercenary_level);
        $total_cost_old = $total_cost_old[BUILD_CREATE][RES_DARK_MATTER];
      }
      $total_cost = eco_get_total_cost($mercenary_id, $mercenary_level + 1);
      $mercenary_time_finish = $user[$mercenary_id]['powerup_time_finish'];
      $template->assign_block_vars('officer', array(
        'ID'          => $mercenary_id,
        'NAME'        => $lang['tech'][$mercenary_id],
        'DESCRIPTION' => $lang['info'][$mercenary_id]['description'],
        'EFFECT'      => $lang['info'][$mercenary_id]['effect'],
        'COST'        => $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old,
        'LEVEL'       => $mercenary_level,
        'LEVEL_MAX'   => $mercenary['max'],
        'BONUS'       => $mercenary_bonus,
        'BONUS_TYPE'  => $mercenary['bonus_type'],
        'HIRE_END'    => $mercenary_time_finish && $mercenary_time_finish >= $time_now ? date(FMT_DATE_TIME, $mercenary_time_finish) : '',
        'CAN_BUY'     => mrc_officer_accessible($user, $mercenary_id),
      ));

      for($i = $config->empire_mercenary_temporary ? 1 : $mercenary_level + 1; $i <= $mercenary['max']; $i++)
      {
        $total_cost = eco_get_total_cost($mercenary_id, $i);
        if(!$config->empire_mercenary_temporary && $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old > $user[$sn_data[RES_DARK_MATTER]['name']])
        {
          break;
        }
        $template->assign_block_vars('officer.level', array(
          'VALUE' => $i,
          'PRICE' => $total_cost[BUILD_CREATE][RES_DARK_MATTER] - $total_cost_old,
        ));
      }
    }
  }

  $template->assign_vars(array(
    'EMPIRE_MERCENARY_TEMPORARY' => $config->empire_mercenary_temporary,
  ));

  display(parsetemplate($template), $lang['tech'][MRC_MERCENARIES]);
}

?>
