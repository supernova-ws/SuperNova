<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= " - {$lang['eco_mrk_info']}";

$template = SnTemplate::gettemplate('market_info', true);

$template->assign_vars(array(
  'RES_DARK_MATTER' => mrc_get_level($user, null, RES_DARK_MATTER),
  'PAGE_HEADER' => $page_title,
));

$info_action = sys_get_param_int('action');
if($info_action)
{
  try
  {
    sn_db_transaction_start();

    $user = db_user_by_id($user['id'], true);
    if(mrc_get_level($user, null, RES_DARK_MATTER) < $config->rpg_cost_info)
    {
      throw new Exception(MARKET_NO_DM, ERR_ERROR);
    }

    switch($info_action)
    {
      case MARKET_INFO_PLAYER:
        $user_info_name_unsafe = sys_get_param_str_unsafe('user_info_name');
        if(!$user_info_name_unsafe)
        {
          throw new Exception(MARKET_INFO_PLAYER_WRONG, ERR_ERROR);
        }

        if(is_id($user_info_name_unsafe))
        {
          $user_info = db_user_by_id($user_info_name_unsafe, true, '`id`, `username`', true);
        }
        if(!is_array($user_info))
        {
          $user_info = db_user_by_username($user_info_name_unsafe, true);
        }
        if(!is_array($user_info))
        {
          throw new Exception(MARKET_INFO_PLAYER_NOT_FOUND, ERR_ERROR);
        }
        if($user_info['id'] == $user['id'])
        {
          throw new Exception(MARKET_INFO_PLAYER_SAME, ERR_ERROR);
        }

        $msg_text = array();
        foreach(sn_get_groups('mercenaries') as $mercenary_id)
        {
          $msg_text[] = "{$lang['tech'][$mercenary_id]} - " . (($mercenary_level = mrc_get_level($user_info, false, $mercenary_id)) ? "{$lang['sys_level']} {$mercenary_level}" : $lang['eco_mrk_info_not_hired']);
        }
        if($mercenary_level = mrc_get_level($user_info, false, UNIT_PREMIUM))
        {
          $msg_text[] = "{$lang['tech'][UNIT_PREMIUM]} - {$mercenary_level} {$lang['sys_level']}";
        }
        $msg_text = sprintf($lang['eco_mrk_info_player_message'], $user_info['id'], $user_info['username']) . "\r\n" . implode("\r\n", $msg_text);

        msg_send_simple_message($user['id'], 0, SN_TIME_NOW, MSG_TYPE_SPY, $lang['eco_mrk_info_msg_from'], "{$lang['eco_mrk_info_player']} ID {$user_info['id']} [{$user_info['username']}]", $msg_text, false, true);
      break;

      default:
        throw new Exception(MARKET_INFO_WRONG, ERR_ERROR);
      break;
    }

    if(!rpg_points_change($user['id'], RPG_MARKET_INFO_MERCENARY, -$config->rpg_cost_info, "Using Black Market page {$lang['eco_mrk_info']} - getting info about user ID {$user_info['id']}"))
    {
      // TODO: throw new Exception(MARKET_INFO_PLAYER_SAME, ERR_ERROR);
    }

    sn_db_transaction_commit();
    throw new Exception($info_action, ERR_NONE);
  }
  catch (Exception $e)
  {
    sn_db_transaction_rollback();

    $template->assign_block_vars('result', array(
      'STATUS' => $e->getCode(),
      'MESSAGE' => $lang['eco_mrk_errors'][$e->getMessage()],
    ));
    /*
    return array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => $e->getMessage()
    );
    */
  }

}
