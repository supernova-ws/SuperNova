<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if($config->game_mode == GAME_BLITZ) {
  message($lang['sys_blitz_page_disabled'], $lang['sys_error'], 'overview.php', 10);
  die();
}

if(!$config->game_blitz_register) {
  message($lang['sys_blitz_registration_disabled'], $lang['sys_error'], 'overview.php', 10);
  die();
}


$template = gettemplate('blitz_register', true);

if($config->game_blitz_register == BLITZ_REGISTER_OPEN) {
  if(sys_get_param_str('register_me')) {
    sn_db_transaction_start();
    $is_registered = doquery("SELECT `id` FROM {{blitz_registrations}} WHERE `user_id` = {$user['id']} FOR UPDATE;", true);
pdump($is_registered, $user['id']);
    if(empty($is_registered)) {
      doquery("INSERT INTO {{blitz_registrations}} SET `user_id` = {$user['id']};");
    }
    sn_db_transaction_commit();
  } elseif (sys_get_param_str('register_me_not')) {
    doquery("DELETE FROM {{blitz_registrations}} WHERE `user_id` = {$user['id']};");
  }
}


$announce_id   = sys_get_param_id('id');
$text          = sys_get_param_str('text');
$announce_time = sys_get_param_str('dtDateTime');
$detail_url    = sys_get_param_str('detail_url');
$mode          = sys_get_param_str('mode');


if ($user['authlevel'] >= 3)  {
  if (!empty($text)) {
    $idAnnounce = sys_get_param_id('id');
    $announce_time = strtotime($announce_time);
    $announce_time = $announce_time ? $announce_time : $time_now; // ("FROM_UNIXTIME(".time().")")

    if ($mode == 'edit') {
      doquery("UPDATE {{announce}} SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}' WHERE `idAnnounce`={$idAnnounce}");
    } else {
      doquery("INSERT INTO {{announce}}
        SET `tsTimeStamp` = FROM_UNIXTIME({$announce_time}), `strAnnounce`='{$text}', detail_url = '{$detail_url}',
        `user_id` = {$user['id']}, `user_name` = '" . mysql_real_escape_string($user['username']) . "'");
    }

    if($announce_time <= $time_now) {
      if($announce_time > $config->var_news_last && $announce_time == $time_now) {
        $config->db_saveItem('var_news_last', $announce_time);
      }

      if(sys_get_param_int('news_mass_mail')) {
        $text = sys_get_param('text') . ($detail_url ? " <a href=\"{$detail_url}\"><span class=\"positive\">{$lang['news_more']}</span></a>" : '');
        msg_send_simple_message('*', 0, 0, MSG_TYPE_ADMIN, $lang['sys_administration'], $lang['news_title'], $text);
      }
    }

    $mode = '';
  }

  switch($mode) {
    case 'del':
      doquery( "DELETE FROM {{announce}} WHERE `idAnnounce`={$announce_id} LIMIT 1;");
      $mode = '';
    break;

    case 'edit':
      $template->assign_var('ID', $announce_id);

    case 'copy':
      $announce = doquery("SELECT * FROM {{announce}} WHERE `idAnnounce`={$announce_id} LIMIT 1;", '', true);
    break;
  }
} else {
  $annQuery = 'WHERE UNIX_TIMESTAMP(`tsTimeStamp`)<=' . intval($time_now);
}


$player_registered = false;
$query = doquery("SELECT u.* FROM {{blitz_registrations}} AS br JOIN {{users}} AS u ON u.id = br.user_id;");
while($row = mysql_fetch_assoc($query)) {
  $template->assign_block_vars('registrations', array(
    'ID' => $row['id'],
    'NAME' => player_nick_render_to_html($row, array('icons' => true, 'color' => 'true')),
  ));
  if($row['id'] == $user['id']) {
    $player_registered = $row;
  }
}

// pdump($player_registered);

$template->assign_vars(array(
  'REGISTRATION_OPEN' => $config->game_blitz_register == BLITZ_REGISTER_OPEN,
  'PLAYER_REGISTERED' => !empty($player_registered),
//  'AUTHLEVEL'       => $user['authlevel'],
////  'total'           => mysql_num_rows($allAnnounces),
//  'MODE'            => $mode,
//  'tsTimeStamp'     => $announce['tsTimeStamp'],
//  'strAnnounce'     => $announce['strAnnounce'],
//  'DETAIL_URL'      => $announce['detail_url'],
//  'time_now'        => $time_now,
));

display($template, $lang['sys_blitz_global_button']);
