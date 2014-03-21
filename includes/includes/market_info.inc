<?php

if(!defined('SN_IN_MARKET') || SN_IN_MARKET !== true)
{
  $debug->error("Attempt to call market page mode {$mode} directly - not from market.php", 'Forbidden', 403);
}

$page_title .= " - {$lang['eco_mrk_info']}";

$template = gettemplate('market_info', true);

$template->assign_vars(array(
  'RES_DARK_MATTER' => $user[$sn_data[RES_DARK_MATTER]['name']],
  'PAGE_HEADER' => $page_title,
));

$info_action = sys_get_param_int('action');
if($info_action)
{
  try
  {
    doquery('START TRANSACTION;');

    $user = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['id']} LIMIT 1 FOR UPDATE;", true);
    if($user[$sn_data[RES_DARK_MATTER]['name']] < $config->rpg_cost_info)
    {
      throw new Exception(MARKET_NO_DM, ERR_ERROR);
    }

    switch($info_action)
    {
      case MARKET_INFO_PLAYER:
        if(!$user_info_name = sys_get_param_str('user_info_name'))
        {
          throw new Exception(MARKET_INFO_PLAYER_WRONG, ERR_ERROR);
        }

        if(is_id($user_info_name))
        {
          $user_info = doquery("SELECT `id`, `username` FROM {{users}} WHERE `id` = {$user_info_name} AND `user_as_ally` IS NULL LIMIT 1 FOR UPDATE;", true);
        }
        if(!is_array($user_info))
        {
          $user_info = doquery("SELECT `id`, `username` FROM {{users}} WHERE `username` LIKE '{$user_info_name}' AND `user_as_ally` IS NULL LIMIT 1 FOR UPDATE;", true);
        }
        if(!is_array($user_info))
        {
          throw new Exception(MARKET_INFO_PLAYER_NOT_FOUND, ERR_ERROR);
        }
        if($user_info == $user['id'])
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

        msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_SPY, $lang['eco_mrk_info_msg_from'], "{$lang['eco_mrk_info_player']} ID {$user_info['id']} [{$user_info['username']}]", $msg_text, false, true);
      break;

      default:
        throw new Exception(MARKET_INFO_WRONG, ERR_ERROR);
      break;
    }

    if(!rpg_points_change($user['id'], RPG_MARKET, -$config->rpg_cost_info, "Using Black Market page {$lang['eco_mrk_info']} - getting info about user ID {$user_info['id']}"))
    {
      // TODO: throw new Exception(MARKET_INFO_PLAYER_SAME, ERR_ERROR);
    }

    doquery('COMMIT;');
    throw new Exception($info_action, ERR_NONE);
  }
  catch (Exception $e)
  {
    doquery('ROLLBACK;');

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
