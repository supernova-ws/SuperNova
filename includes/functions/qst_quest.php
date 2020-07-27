<?php

use \DBAL\DbQuery;
use DBAL\OldDbChangeSet;

function roughQuestRenderWrapper() {
  global $lang, $user;
  lng_include('quest');

  $template = SnTemplate::gettemplate('quest', true);
  qst_render_page($lang, $user, $template);

  SnTemplate::display($template, $lang['qst_quests']);
}

/**
 * @param classLocale $lang
 * @param array       $user
 * @param template    $template
 */
function qst_render_page(classLocale $lang, array $user, template $template) {
  $questCurrentManaged = null;
  $in_admin = defined('IN_ADMIN') && IN_ADMIN === true;

  $user_id = sys_get_param_id('user_id', false);
  $currentMode = sys_get_param_str('mode');

  if ($in_admin) {
    $questCurrentManaged = questPageModelManage($lang, $template, $currentMode);
  } elseif (!$user_id) {
    $user_id = $user['id'];
  }

  $quest_list = qst_get_quests($user_id, SN::$user_options[PLAYER_OPTION_QUEST_LIST_FILTER]);
  $template->assign_vars(array(
    'AUTHLEVEL' => $user['authlevel'],
    'TOTAL'     => count($quest_list),
    'mode'      => $currentMode,
    'USER_ID'   => $user_id,
    'IN_ADMIN'  => $in_admin,

    'QUEST_STATUS_NOT_STARTED' => QUEST_STATUS_NOT_STARTED,
    'QUEST_STATUS_STARTED'     => QUEST_STATUS_STARTED,
    'QUEST_STATUS_COMPLETE'    => QUEST_STATUS_COMPLETE,

    'PLAYER_OPTION_QUEST_LIST_FILTER' => SN::$user_options[PLAYER_OPTION_QUEST_LIST_FILTER],
  ));

  foreach ($lang['qst_status_list'] as $statusId => $statusName) {
    $template->assign_block_vars('status', array(
      'ID'   => $statusId,
      'NAME' => $statusName,
    ));
  }

  if (!empty($questCurrentManaged)) {
    $quest_templatized = qst_templatize(qst_quest_parse($questCurrentManaged));
  } else {
    $quest_templatized['quest_rewards_list'] = [];
  }

  questTemplatizeReward($quest_templatized, $lang);

  qst_assign_to_template($template, $quest_templatized);

  foreach ($quest_list as $quest_data) {
    qst_assign_to_template($template, qst_templatize($quest_data, true), 'quest');
  }

  foreach (questUnitsAllowed() as $unit_id) {
    $template->assign_block_vars('allowed_unit', array(
      'ID'   => $unit_id,
      'NAME' => $lang['tech'][$unit_id],
    ));
  }
}

/**
 * @param classLocale $lang
 * @param template    $template
 * @param string      $mode
 */
function questPageModelManage(classLocale $lang, template $template, &$mode) {
  $questManaged = null;

  $quest_id = sys_get_param_id('id');
  $quest_name = sys_get_param_str_unsafe('QUEST_NAME');
  if (!empty($quest_name)) {
    try {
      // TODO: Change quest type
      $quest_type = 0;
      $theQuery = DbQuery::build()
        ->setTable('quest')
        ->setValues([
          'quest_type'        => $quest_type,
          'quest_name'        => $quest_name,
          'quest_description' => sys_get_param_str_unsafe('QUEST_DESCRIPTION'),
          'quest_conditions'  => questGetConditionFromParams($lang),
          'quest_rewards'     => questGetRewardsStringFromParams($lang),
        ]);

      if ($mode == 'edit') {
        $theQuery
          ->setWhereArray(['quest_id' => $quest_id])
          ->setOneRow()
          ->doUpdate();
      } else {
        $theQuery->doInsert();
      }

      // TODO: Add mass mail for new quests
      /*
      if(sys_get_param_int('news_mass_mail')) {
        msg_send_simple_message('*', 0, 0, MSG_TYPE_PLAYER, $lang['sys_administration'], $lang['news_title'], $text);
      }
      */
    } catch (Exception $e) {
      SnTemplate::messageBox($e->getMessage(), $lang['sys_error']);
    }

    $mode = '';
  }

  switch ($mode) {
    case 'del':
      doquery("DELETE FROM `{{quest}}` WHERE `quest_id` = {$quest_id} LIMIT 1;");
      $mode = '';
    break;

    /** @noinspection PhpMissingBreakStatementInspection */
    case 'edit':
      $template->assign_var('QUEST_ID', $quest_id);

    case 'copy':
      $questManaged = doquery("SELECT * FROM `{{quest}}` WHERE `quest_id` = {$quest_id} LIMIT 1;", '', true);
    break;
  }

  $query = doquery("SELECT count(*) AS count FROM `{{quest}}`;", '', true);
  SN::$config->pass()->quest_total = $query['count'];

  return $questManaged;
}

/**
 * @param array       $quest_templatized
 * @param classLocale $lang
 */
function questTemplatizeReward(&$quest_templatized, $lang) {
  foreach (questAllowedRewardsList() as $unit_id) {
    $found = false;
    foreach ($quest_templatized['quest_rewards_list'] as $quest_templatized_reward) {
      if ($quest_templatized_reward['ID'] == $unit_id) {
        $found = true;
        break;
      }
    }

    if (!$found) {
      $quest_templatized['quest_rewards_list'][$unit_id] = array(
        'ID'     => $unit_id,
        'NAME'   => $lang['tech'][$unit_id],
        'AMOUNT' => 0,
      );
    }
  }
}

/**
 * @param $lang
 *
 * @return string
 * @throws Exception
 */
function questGetRewardsStringFromParams($lang) {
  $quest_reward_allowed = questAllowedRewardsList();
  $quest_rewards = [];
  foreach (sys_get_param('QUEST_REWARDS_LIST') as $quest_rewards_id => $quest_rewards_amount) {
    if (!in_array($quest_rewards_id, $quest_reward_allowed)) {
      throw new Exception($lang['qst_adm_err_reward_type']);
    }

    $quest_rewards_amount = round($quest_rewards_amount);
    if ($quest_rewards_amount < 0) {
      throw new Exception($lang['qst_adm_err_reward_amount']);
    } elseif ($quest_rewards_amount > 0) {
      $quest_rewards[intval($quest_rewards_id)] = $quest_rewards_amount;
    }
  }
  if (empty($quest_rewards)) {
    throw new Exception($lang['qst_adm_err_reward_empty']);
  }
  $quest_rewards_string = sys_unit_arr2str($quest_rewards);

  return $quest_rewards_string;
}

/**
 * @param $lang
 *
 * @return string
 * @throws Exception
 */
function questGetConditionFromParams($lang) {
  $quest_unit_id = sys_get_param_int('QUEST_UNIT_ID');
  if (!in_array($quest_unit_id, questUnitsAllowed())) {
    throw new Exception($lang['qst_adm_err_unit_id']);
  }
  $quest_unit_amount = sys_get_param_float('QUEST_UNIT_AMOUNT');
  if ($quest_unit_amount <= 0) {
    throw new Exception($lang['qst_adm_err_unit_amount']);
  }
  $quest_conditions = "{$quest_unit_id},{$quest_unit_amount}";

  return $quest_conditions;
}

/**
 * @return array
 */
function questAllowedRewardsList() {
  return sn_get_groups('quest_rewards');
}

/**
 * @return array
 */
function questUnitsAllowed() {
  return sn_get_groups(['structures', 'tech', 'fleet', 'defense']);
}

function qst_get_quests($user_id = false, $status = QUEST_STATUS_ALL) {
  $quest_list = array();

  if ($user_id) {
    if ($status !== QUEST_STATUS_ALL) {
      $query_add_where = "";
      if ($status == null || $status == QUEST_STATUS_NOT_STARTED) {
        $query_add_where .= "AND qs.quest_status_status IS NULL";
      } elseif ($status == QUEST_STATUS_EXCEPT_COMPLETE) {
        $query_add_where .= "AND (qs.quest_status_status IS NULL OR qs.quest_status_status = " . QUEST_STATUS_STARTED . ")";
      } else {
        $query_add_where .= "AND qs.quest_status_status = {$status}";
      }
    }
    $query_add_select = ", qs.quest_status_progress, qs.quest_status_status";
    $query_add_from = "LEFT JOIN {{quest_status}} AS qs ON qs.quest_status_quest_id = q.quest_id AND qs.quest_status_user_id = {$user_id}";
  }

  $query = doquery(
    "SELECT q.* {$query_add_select}
      FROM `{{quest}}` AS q {$query_add_from}
      WHERE 1 {$query_add_where}
    ;"
  );

  while ($quest = db_fetch($query)) {
    $quest_list[$quest['quest_id']] = qst_quest_parse($quest);
  }

  return $quest_list;
}

/**
 * @param template    $template
 * @param array       $quest_templatized
 * @param string|bool $block_name
 */
function qst_assign_to_template(&$template, $quest_templatized, $block_name = false) {
  if ($block_name) {
    $template->assign_block_vars($block_name, $quest_templatized);
  } else {
    $template->assign_vars($quest_templatized);
    if (!empty($quest_templatized['quest_rewards_list'])) {
      foreach ($quest_templatized['quest_rewards_list'] as $quest_reward) {
        $template->assign_block_vars(($block_name ? $block_name . '.' : '') . 'quest_rewards_list', $quest_reward);
      }
    }
  }
}

function qst_quest_parse($quest) {
  list($quest['quest_unit_id'], $quest['quest_unit_amount']) = explode(',', $quest['quest_conditions']);

  $quest['quest_rewards_list'] = sys_unit_str2arr($quest['quest_rewards']);

  return $quest;
}

function qst_templatize($quest, $for_display = true) {
  global $lang;

  $tmp = array();
  foreach ($quest['quest_rewards_list'] as $quest_reward_id => $quest_reward_amount) {
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
    'QUEST_DESCRIPTION'  => $for_display ? HelperString::nl2br($quest['quest_description']) : $quest['quest_description'],
    'QUEST_CONDITIONS'   => $quest['quest_condition'],
    'QUEST_UNIT_ID'      => $quest['quest_unit_id'],
    'QUEST_UNIT_NAME'    => $lang['tech'][$quest['quest_unit_id']],
    'QUEST_UNIT_AMOUNT'  => $quest['quest_unit_amount'],
    'QUEST_STATUS'       => intval($quest['quest_status_status']),
    'QUEST_STATUS_NAME'  => $lang['qst_status_list'][intval($quest['quest_status_status'])],
    'quest_rewards_list' => $tmp,
  );
}

function qst_active_triggers($quest_list) {
  $quest_triggers = array();
  foreach ($quest_list as $quest_id => $quest) {
    if ($quest['quest_status_status'] != QUEST_STATUS_COMPLETE) {
      list($quest_unit_id, $quest_unit_amount) = explode(',', $quest['quest_conditions']);
      $quest_triggers[$quest_id] = $quest_unit_id;
    }
  }

  return $quest_triggers;
}

/**
 * @param           $user
 * @param           $rewards
 * @param           $quest_list
 * @param integer[] $quest_statuses
 */
function qst_reward(&$user, &$rewards, &$quest_list, &$quest_statuses) {
  if (empty($quest_statuses)) {
    return;
  }

  global $lang;

  foreach ($quest_statuses as $quest_id => $quest_status) {
    $quest_list[$quest_id]['quest_status_status'] = $quest_status;

    $questStatus = DbQuery::build()
      ->setTable('quest_status')
      ->setWhereArray(array(
        'quest_status_quest_id' => $quest_id,
        'quest_status_user_id'  => $user['id'],
      ))
      ->doSelectFetch();

    if (empty($questStatus)) {
      DbQuery::build()
        ->setTable('quest_status')
        ->setValues(array(
          'quest_status_quest_id' => $quest_id,
          'quest_status_user_id'  => $user['id'],
          'quest_status_status'   => $quest_status
        ))
        ->doInsert();
    } elseif ($questStatus['quest_status_status'] != $quest_status) {
      DbQuery::build()
        ->setTable('quest_status')
        ->setWhereArray(array(
          'quest_status_quest_id' => $quest_id,
          'quest_status_user_id'  => $user['id'],
        ))
        ->setValues(array(
          'quest_status_status' => $quest_status
        ))
        ->doUpdate();
    }
  }

  if (empty($rewards)) {
    return;
  }

  $db_changeset = array();
  $total_rewards = array();
  $comment_dm = '';

  foreach ($rewards as $quest_id => $user_data) {
    foreach ($user_data as $user_id => $planet_data) {
      foreach ($planet_data as $planet_id => $reward_list) {
        $comment = sprintf($lang['qst_msg_complete_body'], $quest_list[$quest_id]['quest_name']);
        $comment_dm .= isset($reward_list[RES_DARK_MATTER]) ? $comment : '';

        $comment_reward = array();
        foreach ($reward_list as $unit_id => $unit_amount) {
          $comment_reward[] = $unit_amount . ' ' . $lang['tech'][$unit_id];
          $total_rewards[$user_id][$planet_id][$unit_id] += $unit_amount;
        }
        $comment .= " {$lang['qst_msg_your_reward']} " . implode(',', $comment_reward);

        msg_send_simple_message($user['id'], 0, SN_TIME_NOW, MSG_TYPE_ADMIN, $lang['msg_from_admin'], $lang['qst_msg_complete_subject'], $comment);

        DbQuery::build()
          ->setTable('quest_status')
          ->setValues(array(
            'quest_status_quest_id' => $quest_id,
            'quest_status_user_id'  => $user_id,
            'quest_status_status'   => QUEST_STATUS_COMPLETE
          ))
          ->doInsert();
      }
    }
  }

  $group_resources = sn_get_groups('resources_loot');
  $quest_rewards_allowed = questAllowedRewardsList();
  if (!empty($total_rewards)) {
    foreach ($total_rewards as $user_id => $planet_data) {
      $user_row = db_user_by_id($user_id);
      foreach ($planet_data as $planet_id => $unit_data) {
        $local_changeset = array();
        foreach ($unit_data as $unit_id => $unit_amount) {
          if (!isset($quest_rewards_allowed[$unit_id])) {
            continue;
          }

          if ($unit_id == RES_DARK_MATTER) {
            rpg_points_change($user['id'], RPG_QUEST, $unit_amount, $comment_dm);
          } elseif (isset($group_resources[$unit_id])) {
            $local_changeset[pname_resource_name($unit_id)] = array('delta' => $unit_amount);
          } else // Проверим на юниты
          {
            $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($unit_id, $unit_amount, $user_row, $planet_id);
          }
        }

        if (!empty($local_changeset)) {
          $planet_id = $planet_id == 0 && isset($user_row['id_planet']) ? $user_row['id_planet'] : $planet_id;
          $db_changeset[$planet_id ? 'planets' : 'users'][] = array(
            'action'  => SQL_OP_UPDATE,
            P_VERSION => 1,
            'where'   => array(
              "id" => $planet_id ? $planet_id : $user_id,
            ),
            'fields'  => $local_changeset,
          );
        }
      }
    }

    OldDbChangeSet::db_changeset_apply($db_changeset);
  }
}

function get_quest_amount_complete($user_id) {
  return count(qst_get_quests($user_id, QUEST_STATUS_COMPLETE));
}

function get_quest_amount_in_progress($user_id) {
  return count(qst_get_quests($user_id, QUEST_STATUS_STARTED));
}
