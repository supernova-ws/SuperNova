<?php

function sn_admin_user_view_view($template = null) {
  define('IN_ADMIN', true);

  global $user, $lang;

  if ($user['authlevel'] < 3) {
    AdminMessage($lang['adm_err_denied']);
  }

  lng_include('admin');

  $user_id = sys_get_param_id('uid');
  if (!($user_row = db_user_by_id($user_id))) {
    AdminMessage(sprintf($lang['adm_dm_user_none'], $user_id));
  }

  $template = gettemplate('admin/admin_user', true);

  if (!empty($user_row['user_last_browser_id'])) {
    $temp = doquery("SELECT `browser_user_agent` FROM `{{security_browser}}` WHERE `browser_id` = {$user_row['user_last_browser_id']}", true);
    $user_row['browser_user_agent'] = $temp['browser_user_agent'];
  }

  $formats = array(
    'sys_time_human_system' => array(
      'register_time',
      'onlinetime',
      'ally_register_time',
      'news_lastread',
      'banaday',
      'vacation',
      'vacation_next',
      'deltime',
      'que_processed',
    ),
    'pretty_number'         => array(
      'metal',
      'crystal',
      'deuterium',
      'dark_matter_total',
      'metamatter',
      'metamatter_total',

      'player_rpg_explore_xp',
      'player_rpg_explore_level',
      'lvl_minier',
      'xpminier',
      'player_rpg_tech_xp',
      'player_rpg_tech_level',
      'lvl_raid',
      'xpraid',
      'raids',
      'raidsloose',
      'raidswin',
      'total_rank',
      'total_points',),
  );

// + email
// + account data
  $blocks = array(
    'Основная информация'           => array(
      'id',
      'username',
      'password',
      'salt',
      'user_last_browser_id',
      'browser_user_agent',
      'user_lastip',
      'user_last_proxy',
      'register_time',
      'onlinetime',
      'authlevel',
      'admin_protection',
      'id_planet',
      'galaxy',
      'system',
      'planet',
      'current_planet',
      'server_name',
    ),
    'Профиль пользователя'          => array(
      'email',
      'email_2',
      'gender',
      'avatar',
      'sign',
      'user_birthday',
    ),
    'Ресурсы'                       => array(
      'metal',
      'crystal',
      'deuterium',
      'dark_matter',
      'dark_matter_total',
      'metamatter',
      'metamatter_total',
      'player_race',
    ),
    'Альянс'                        => array(
      'ally_id',
      'ally_tag',
      'ally_name',
      'ally_register_time',
      'ally_rank_id',
      'user_as_ally',
    ),
    'Очки'                          => array(
      'player_rpg_explore_xp',
      'player_rpg_explore_level',
      'lvl_minier',
      'xpminier',
      'player_rpg_tech_xp',
      'player_rpg_tech_level',
      'lvl_raid',
      'xpraid',
      'raids',
      'raidsloose',
      'raidswin',
      'total_rank',
      'total_points',
    ),
    'Блокировка, отпуск и удаление' => array(
      'immortal',
      'bana',
      'banaday',
      'vacation',
      'vacation_next',
      'deltime',
    ),
    'Основные настройки интерфейса' => array(
      'lang',
      'dpath',
      'design',
    ),
    'Новости и сообщения'           => array(
      'news_lastread',
      'new_message',
      'mnl_alliance',
      'mnl_joueur',
      'mnl_attaque',
      'mnl_spy',
      'mnl_exploit',
      'mnl_transport',
      'mnl_expedition',
      'mnl_buildlist',
      'msg_admin',
    ),
    'Прочие настройки'              => array(
      'noipcheck',
      'options',
      'user_time_measured',
    ),
    'Системные поля'                => array(
      'que_processed',
      'user_birthday_celebrated',
      'user_bot',
      'parent_account_id',
      'parent_account_global',
    ),
  );


  foreach ($formats as $callable => $field_list) {
    foreach ($field_list as $field_name) {
      $user_row[$field_name] = call_user_func($callable, $user_row[$field_name]);
    }
  }

  /**
   * @param template $template
   * @param classPersistent $title
   * @param array $fields
   */
  function userBlockAssign(&$exclude, $template, $title, $fields) {
    $template->assign_block_vars('block', array(
      'TITLE' => $title,
    ));
    foreach ($fields as $field) {
      $template->assign_block_vars('block.field', array(
        'NAME' => $field,
        'VALUE' => isset($exclude[$field]) ? $exclude[$field] : 'N/A',
      ));
      unset($exclude[$field]);
    }
  }

  $exclude = $user_row;
  foreach($blocks as $title => $fields) {
    userBlockAssign($exclude, $template, $title, $fields);
  }

  if(!empty($exclude)) {
    userBlockAssign($exclude, $template, '!!! НЕИЗВЕСТНЫЕ ПАРАМЕТРЫ !!!', array_keys($exclude));
  }

  $template->assign_var('PAGE_HEADER', "[{$user_row['id']}] {$user_row['username']}");

  return $template;

}
