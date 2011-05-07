<?php

function qst_render_page()
{
  global $sn_data, $lang, $user, $template, $config;

  $user_id = sys_get_param_int('user_id', false);
  $mode    = sys_get_param_str('mode');

  $quest_units_allowed = array_merge($sn_data['groups']['structures'], $sn_data['groups']['tech']);

  $in_admin = defined('IN_ADMIN') && IN_ADMIN == true;

//  if($user['authlevel'] >= 3)
  if($in_admin)
  {
    $quest_id = sys_get_param_int('id');
    $quest_description = sys_get_param_str('QUEST_DESCRIPTION');
    if (!empty($quest_description))
    {
      $quest_name = sys_get_param_str('QUEST_NAME');
      try
      {
        $quest_rewards_amount = sys_get_param_int('QUEST_REWARDS_AMOUNT');
        if($quest_rewards_amount <= 0)
        {
          throw new Exception($lang['qst_adm_err_reward_amount']);
        }

        $quest_rewards = RES_DARK_MATTER . ",{$quest_rewards_amount}";

        $quest_unit_id        = sys_get_param_int('QUEST_UNIT_ID');
        if(!in_array($quest_unit_id, $quest_units_allowed))
        {
          throw new Exception($lang['qst_adm_err_unit_id']);
        }

        $quest_unit_amount    = sys_get_param_int('QUEST_UNIT_AMOUNT');
        if($quest_unit_amount <= 0)
        {
          throw new Exception($lang['qst_adm_err_unit_amount']);
        }
        $quest_conditions = "{$quest_unit_id},{$quest_unit_amount}";

        // TODO: Change quest type
        $quest_type = 0;

        if($mode == 'edit')
        {
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

        /*
        // TODO: Add mass mail for new quests
        if(sys_get_param_int('news_mass_mail'))
        {
          if($detail_url)
          {
            $text = "{$text} <a href=\"{$detail_url}\">{$lang['news_more']}</a>";
          }

          doquery("INSERT INTO {{messages}} (message_owner, message_time, message_type, message_from, message_subject, message_text) SELECT `id`, unix_timestamp(now()), 1, '{$lang['sys_administration']}', '{$lang['news_title']}', '{$text}' FROM {{users}};");
          doquery("UPDATE {{users}} SET {$messfields[1]} = {$messfields[1]} + 1, {$messfields[100]} = {$messfields[100]} + 1;");
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
  $templatized = array(
    'AUTHLEVEL' => $user['authlevel'],
    'TOTAL'     => count($quest_list),
    'mode'      => $mode,
    'USER_ID'   => $user_id,
    'IN_ADMIN'  => $in_admin,
  );

  if($quest)
  {
    $templatized = array_merge(qst_templatize(qst_quest_parse($quest)), $templatized);
  }

  $template->assign_vars($templatized);

  foreach($quest_list as $quest)
  {
    $template->assign_block_vars('quest', qst_templatize($quest));
  }

  foreach($quest_units_allowed as $unit_id)
  {
    $template->assign_block_vars('unit', array(
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

function qst_quest_parse($quest)
{
  list($quest['quest_unit_id'], $quest['quest_unit_amount']) = explode(',', $quest['quest_conditions']);
  list($quest['quest_rewards_id'], $quest['quest_rewards_amount']) = explode(',', $quest['quest_rewards']);

  return $quest;
}

function qst_templatize($quest)
{
  global $lang;

  return array(
    'QUEST_ID'             => $quest['quest_id'],
    'QUEST_NAME'           => $quest['quest_name'],
    'QUEST_TYPE'           => $quest['quest_type'],
    'QUEST_DESCRIPTION'    => sys_bbcodeParse($quest['quest_description']),
    'QUEST_CONDITIONS'     => $quest['quest_condition'],
    'QUEST_REWARDS_ID'     => $quest['quest_rewards_id'],
    'QUEST_REWARDS_NAME'   => $lang['tech'][$quest['quest_rewards_id']],
    'QUEST_REWARDS_AMOUNT' => $quest['quest_rewards_amount'],
    'QUEST_UNIT_ID'        => $quest['quest_unit_id'],
    'QUEST_UNIT_NAME'      => $lang['tech'][$quest['quest_unit_id']],
    'QUEST_UNIT_AMOUNT'    => $quest['quest_unit_amount'],
    'QUEST_STATUS'         => $quest['quest_status_status'],
    'QUEST_STATUS_NAME'    => $lang['qst_status_list'][$quest['quest_status_status']],
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

function qst_reward(&$user, &$rewards, &$quest_list)
{
  global $lang;

  foreach($rewards as $quest_id => $reward_amount)
  {
    $comment = sprintf($lang['qst_msg_complete_body'], $reward_amount, $quest_list[$quest_id]['quest_name']);
    rpg_points_change($user['id'], $reward_amount, $comment);
    msg_send_simple_message($user['id'], 0, $time_now, 1, $lang['msg_from_admin'], $lang['qst_msg_complete_subject'], $comment);

    sn_db_perform('{{quest_status}}', array(
      'quest_status_quest_id' => $quest_id,
      'quest_status_user_id'  => $user['id'],
      'quest_status_status'   => QUEST_STATUS_COMPLETE
    ));

    $user['rpg_points'] += $reward_amount;
  }
}

function get_quest_amount_complete($user_id)
{
  // TODO: Make it faster - rewrite SQL?
  return count(qst_get_quests($user_id, QUEST_STATUS_COMPLETE));
}
?>
