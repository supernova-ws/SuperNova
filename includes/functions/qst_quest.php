<?php

function qst_render_page()
{
  global $lang, $user, $template, $config;

  $user_id = sys_get_param_id('user_id', false);
  $mode    = sys_get_param_str('mode');

  $quest_units_allowed  = sn_get_groups(array('structures', 'tech', 'fleet', 'defense'));
  $quest_reward_allowed = sn_get_groups('quest_rewards');

  $in_admin = defined('IN_ADMIN') && IN_ADMIN === true;

  if($in_admin)
  {
    $quest_id = sys_get_param_id('id');
    $quest_name = sys_get_param_str_raw('QUEST_NAME');
    if(!empty($quest_name))
    {
      $quest_description = sys_get_param_str_raw('QUEST_DESCRIPTION');
      try
      {
        $quest_rewards_list = sys_get_param('QUEST_REWARDS_LIST');
        $quest_rewards = array();
        foreach($quest_rewards_list as $quest_rewards_id => $quest_rewards_amount)
        {
          if(!in_array($quest_rewards_id, $quest_reward_allowed))
          {
            throw new Exception($lang['qst_adm_err_reward_type']);
          }

          if($quest_rewards_amount < 0)
          {
            throw new Exception($lang['qst_adm_err_reward_amount']);
          }
          elseif($quest_rewards_amount > 0)
          {
            $quest_rewards[] = "{$quest_rewards_id},{$quest_rewards_amount}";
          }
        }
        if(empty($quest_rewards))
        {
          throw new Exception($lang['qst_adm_err_reward_empty']);
        }

        $quest_rewards = implode(';', $quest_rewards);

        $quest_unit_id = sys_get_param_int('QUEST_UNIT_ID');
        if(!in_array($quest_unit_id, $quest_units_allowed))
        {
          throw new Exception($lang['qst_adm_err_unit_id']);
        }

        $quest_unit_amount = sys_get_param_float('QUEST_UNIT_AMOUNT');
        if($quest_unit_amount <= 0)
        {
          throw new Exception($lang['qst_adm_err_unit_amount']);
        }
        $quest_conditions = "{$quest_unit_id},{$quest_unit_amount}";

        // TODO: Change quest type
        $quest_type = 0;

        if($mode == 'edit')
        {
          $quest_name        = mysql_real_escape_string($quest_name);
          $quest_description = mysql_real_escape_string($quest_description);
          doquery(
            "UPDATE {{quest}} SET
              `quest_name` = '{$quest_name}',
              `quest_type` = '{$quest_type}',
              `quest_description` = '{$quest_description}',
              `quest_conditions` = '$quest_conditions',
              `quest_rewards` = '{$quest_rewards}'
            WHERE `quest_id` = {$quest_id} LIMIT 1;"
          );
        }
        else
        {
          sn_db_perform('{{quest}}', array(
            'quest_name' => $quest_name,
            'quest_type' => $quest_type,
            'quest_description' => $quest_description,
            'quest_conditions' => $quest_conditions,
            'quest_rewards' => $quest_rewards,
          ));
        }
        // doquery("UPDATE {{users}} SET `news_lastread` = `news_lastread` + 1;");

        // TODO: Add mass mail for new quests
        /*
        if(sys_get_param_int('news_mass_mail'))
        {
          msg_send_simple_message('*', 0, 0, MSG_TYPE_PLAYER, $lang['sys_administration'], $lang['news_title'], $text);
        }
        */
      }
      catch (Exception $e)
      {
        message($e->getMessage(), $lang['sys_error']);
      }

      $mode = '';
    };

    switch($mode)
    {
      case 'del':
        doquery("DELETE FROM {{quest}} WHERE `quest_id` = {$quest_id} LIMIT 1;");
        $mode = '';
      break;

      case 'edit':
        $template->assign_var('QUEST_ID', $quest_id);

      case 'copy':
        $quest = doquery("SELECT * FROM {{quest}} WHERE `quest_id` = {$quest_id} LIMIT 1;", '', true);
      break;
    }
    $query = doquery("SELECT count(*) AS count FROM {{quest}};", '', true);
    $config->db_saveItem('quest_total', $query['count']);
  }
  elseif(!$user_id)
  {
    $user_id = $user['id'];
  }

  $quest_list = qst_get_quests($user_id);
  $template->assign_vars(array(
    'AUTHLEVEL' => $user['authlevel'],
    'TOTAL'     => count($quest_list),
    'mode'      => $mode,
    'USER_ID'   => $user_id,
    'IN_ADMIN'  => $in_admin,
  ));

  if($quest)
  {
    $quest_templatized = qst_templatize(qst_quest_parse($quest, false));
  }
  else
  {
    $quest_templatized['quest_rewards_list'] = array();
  }

  foreach($quest_reward_allowed as $unit_id)
  {
    $found = false;
    foreach($quest_templatized['quest_rewards_list'] as $quest_templatized_reward)
    {
      if($quest_templatized_reward['ID'] == $unit_id)
      {
        $found = true;
        break;
      }
    }

    if(!$found)
    {
      $quest_templatized['quest_rewards_list'][$unit_id] = array(
        'ID'     => $unit_id,
        'NAME'   => $lang['tech'][$unit_id],
        'AMOUNT' => 0,
      );
    }
  }

  qst_assign_to_template($template, $quest_templatized);

  foreach($quest_list as $quest_data)
  {
    qst_assign_to_template($template, qst_templatize($quest_data, true), 'quest');
  }

  foreach($quest_units_allowed as $unit_id)
  {
    $template->assign_block_vars('allowed_unit', array(
      'ID'   => $unit_id,
      'NAME' => $lang['tech'][$unit_id],
    ));
  }
}

function qst_get_quests($user_id = false, $status = false)
{
  $quest_list = array();

  if($user_id)
  {
    if($status !== false)
    {
      $query_add_where = "AND qs.quest_status_status ";
      if($status == null)
      {
        $query_add_where .= "IS NULL";
      }
      else
      {
        $query_add_where .= "= {$status}";
      }
    }
    $query_add_select = ", qs.quest_status_progress, qs.quest_status_status";
    $query_add_from = "LEFT JOIN {{quest_status}} AS qs ON qs.quest_status_quest_id = q.quest_id AND qs.quest_status_user_id = {$user_id}";
  }

  $query = doquery(
    "SELECT q.* {$query_add_select}
      FROM {{quest}} AS q {$query_add_from}
      WHERE 1 {$query_add_where}
    ;"
  );

  while($quest = mysql_fetch_assoc($query))
  {
    $quest_list[$quest['quest_id']] = qst_quest_parse($quest);
  }

  return $quest_list;
}

function qst_assign_to_template(&$template, $quest_templatized, $block_name = false)
{
  if($block_name)
  {
    $template->assign_block_vars($block_name, $quest_templatized);
  }
  else
  {
    $template->assign_vars($quest_templatized);
    if(!empty($quest_templatized['quest_rewards_list']))
    {
      foreach($quest_templatized['quest_rewards_list'] as $quest_reward)
      {
        $template->assign_block_vars(($block_name ? $block_name . '.' : '') . 'quest_rewards_list', $quest_reward);
      }
    }
  }
}

function qst_quest_parse($quest)
{
  list($quest['quest_unit_id'], $quest['quest_unit_amount']) = explode(',', $quest['quest_conditions']);

  $tmp = explode(';', $quest['quest_rewards']);
  $quest['quest_rewards_list'] = array();
  foreach($tmp as $quest_reward_str)
  {
    list($quest_reward_id, $quest_reward_amount) = explode(',', $quest_reward_str);
    $quest['quest_rewards_list'][$quest_reward_id] = $quest_reward_amount;
  }

  return $quest;
}

function qst_templatize($quest, $for_display = true)
{
  global $lang;

  $tmp = array();
  foreach($quest['quest_rewards_list'] as $quest_reward_id => $quest_reward_amount)
  {
    $tmp[] = array(
      'ID'     => $quest_reward_id,
      'NAME'   => $for_display ? str_replace(' ', '&nbsp;', $lang['tech'][$quest_reward_id]) : $lang['tech'][$quest_reward_id],
      'AMOUNT' => $quest_reward_amount,
    );
  }

  return array(
    'QUEST_ID'           => $quest['quest_id'],
    'QUEST_NAME'         => $quest['quest_name'],
    'QUEST_TYPE'         => $quest['quest_type'],
    'QUEST_DESCRIPTION'  => $for_display ? sys_bbcodeParse($quest['quest_description']) : $quest['quest_description'],
    'QUEST_CONDITIONS'   => $quest['quest_condition'],
    'QUEST_UNIT_ID'      => $quest['quest_unit_id'],
    'QUEST_UNIT_NAME'    => $lang['tech'][$quest['quest_unit_id']],
    'QUEST_UNIT_AMOUNT'  => $quest['quest_unit_amount'],
    'QUEST_STATUS'       => intval($quest['quest_status_status']),
    'QUEST_STATUS_NAME'  => $lang['qst_status_list'][intval($quest['quest_status_status'])],
    'quest_rewards_list' => $tmp,
  );
}

function qst_active_triggers($quest_list)
{
  $quest_triggers = array();
  foreach($quest_list as $quest_id => $quest)
  {
    if($quest['quest_status_status'] != QUEST_STATUS_COMPLETE)
    {
      list($quest_unit_id, $quest_unit_amount) = explode(',', $quest['quest_conditions']);
      $quest_triggers[$quest_id] = $quest_unit_id;
    }
  }

  return $quest_triggers;
}

function qst_reward(&$user, &$planet, &$rewards, &$quest_list)
{
  global $lang;

  foreach($rewards as $quest_id => $rewards_list_string)
  {
    $comment_reward = array();
    $planet_reward  = array();
    $user_reward    = array();
    $user_reward_dm = 0;
    $comment = sprintf($lang['qst_msg_complete_body'], $quest_list[$quest_id]['quest_name']);

    $rewards_list_array = explode(';', $rewards_list_string);
    foreach($rewards_list_array as $reward_string)
    {
      list($reward_id, $reward_amount) = explode(',', $reward_string);
      $reward_info = get_unit_param($reward_id);
      $reward_db_name = $reward_info[P_NAME];
      $reward_db_string = "`{$reward_db_name}` = `{$reward_db_name}` + {$reward_amount}";

      if($reward_id == RES_DARK_MATTER)
      {
        $user_reward_dm = $reward_amount;
      }

      if($reward_info['location'] == LOC_USER)
      {
        if($reward_id != RES_DARK_MATTER)
        {
          $user[$reward_db_name] += $reward_amount;
        }
        $user_reward[] = $reward_db_string;
      }
      elseif($reward_info['location'] == LOC_PLANET)
      {
        $planet[$reward_db_name] += $reward_amount;
        $planet_reward[] = $reward_db_string;
      }
      else
      {
        continue;
      }

      $comment_reward[] = $reward_amount . ' ' . $lang['tech'][$reward_id];
    }

    if(!empty($comment_reward))
    {
      $comment .= " {$lang['qst_msg_your_reward']} " . implode(',', $comment_reward);

      if(!empty($user_reward))
      {
        $user_reward = implode(',', $user_reward);
        doquery("UPDATE {{users}} SET {$user_reward} WHERE `id` = {$user['id']} LIMIT 1;");

        if($user_reward_dm)
        {
          rpg_points_change($user['id'], RPG_QUEST, $user_reward_dm, $comment, true);
        }
      }

      if(!empty($planet_reward))
      {
        $planet_reward = implode(',', $planet_reward);
        doquery("UPDATE {{planets}} SET {$planet_reward} WHERE `id` = {$planet['id']} LIMIT 1;");
      }
    }

    sn_db_perform('{{quest_status}}', array(
      'quest_status_quest_id' => $quest_id,
      'quest_status_user_id'  => $user['id'],
      'quest_status_status'   => QUEST_STATUS_COMPLETE
    ));

    msg_send_simple_message($user['id'], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['msg_from_admin'], $lang['qst_msg_complete_subject'], $comment);
  }
}

function get_quest_amount_complete($user_id)
{
  // TODO: Make it faster - rewrite SQL?
  return count(qst_get_quests($user_id, QUEST_STATUS_COMPLETE));
}

// TODO: Move here quest comlpletion checks
// TODO: Check mutiply condition quests
/*
function qst_check_completion(&$user, &$planet, )
{
}
*/

?>
