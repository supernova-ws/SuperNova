<?php
/**
 * Created by Gorlum 05.03.2018 12:57
 */

namespace Pages\Deprecated;

use Account;
use Planet\DBStaticPlanet;
use SN;
use SnTemplate;
use template;

class PageAdminUserView extends PageDeprecated {
  private static $formats = [
    'sys_time_human_system'     => [
      'register_time',
      'onlinetime',
      'ally_register_time',
      'news_lastread',
      'banaday',
      'vacation',
      'vacation_next',
      'deltime',
      'que_processed',
    ],
    'prettyNumberStyledDefault' => [
      'account_metamatter',
      'account_metamatter_total',

      'metal',
      'crystal',
      'deuterium',
      'dark_matter',
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
      'total_points',

      // Message counts
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
    ],
  ];

  private static $blocks = [
    'Аккаунт'                       => [
//      'account_id',
      'account_name',
      'account_password',
      'account_salt',
      'account_email',
      'account_email_verified',
      'account_register_time',
      'account_language',
      'account_metamatter',
      'account_metamatter_total',
    ],
    'Основная информация'           => [
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
    ],
    'Профиль пользователя'          => [
      'email',
      'email_2',
      'gender',
      'avatar',
      'sign',
      'user_birthday',
    ],
    'Ресурсы'                       => [
      'metal',
      'crystal',
      'deuterium',
      'dark_matter',
      'dark_matter_total',
      'metamatter',
      'metamatter_total',
      'player_race',
    ],
    'Альянс'                        => [
      'ally_id',
      'ally_tag',
      'ally_name',
      'ally_register_time',
      'ally_rank_id',
      'user_as_ally',
    ],
    'Очки'                          => [
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
    ],
    'Блокировка, отпуск и удаление' => [
      'immortal',
      'bana',
      'banaday',
      'vacation',
      'vacation_next',
      'deltime',
    ],
    'Основные настройки интерфейса' => [
      'lang',
      'template',
      'skin',
      'design',
    ],
    'Новости и сообщения'           => [
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
    ],
    'Прочие настройки'              => [
      'noipcheck',
      'options',
      'user_time_measured',
    ],
    'Системные поля'                => [
      'que_processed',
      'user_birthday_celebrated',
      'user_bot',
      'parent_account_id',
      'parent_account_global',
    ],
  ];

  private static $extras = [
    'account_password' => [
      'TYPE' => 'password',
    ],
  ];

  /**
   * @param null|template $template
   *
   * @return null|template
   */
  public static function modelStatic($template = null) {
    global $user;

    define('IN_ADMIN', true);

    SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

    $user_id = sys_get_param_id('uid');
    if (!($user_row = db_user_by_id($user_id))) {
      return $template;
    }

    if (empty($user['authlevel']) || $user['authlevel'] < $user_row['authlevel']) {
      SnTemplate::messageBoxAdmin(SN::$lang['admin_title_access_denied']);
    }


    if (!empty(sys_get_param('account_password_change')) && ($password = trim(sys_get_param('account_password')))) {
      $account = new Account();
      $account->dbGetByPlayerId($user_id);

      $account->db_set_password($password, '');
    }

    return $template;
  }

  public static function viewStatic($template = null) {
    global $user;

    define('IN_ADMIN', true);

    SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

    $user_id = sys_get_param_id('uid');
    if (!($user_row = db_user_by_id($user_id))) {
      SnTemplate::messageBoxAdmin(sprintf(SN::$lang['adm_dm_user_none'], $user_id));
    }

    if (empty($user['authlevel']) || $user['authlevel'] <= $user_row['authlevel']) {
      SnTemplate::messageBoxAdmin(SN::$lang['admin_title_access_denied']);
    }

    $account = new \Account();
    $account->dbGetByPlayerId($user_id);
    foreach ([
      'account_id',
      'account_name',
      'account_password',
      'account_salt',
      'account_email',
      'account_email_verified',
      'account_register_time',
      'account_language',
      'account_metamatter',
      'account_metamatter_total',
    ] as $accountField) {
      $user_row[$accountField] = $account->$accountField;
    }

    if (!empty($user_row['user_last_browser_id'])) {
      $temp = doquery("SELECT `browser_user_agent` FROM `{{security_browser}}` WHERE `browser_id` = {$user_row['user_last_browser_id']}", true);
      $user_row['browser_user_agent'] = $temp['browser_user_agent'];
    }

    foreach (self::$formats as $callable => $field_list) {
      foreach ($field_list as $field_name) {
        $user_row[$field_name] = call_user_func($callable, $user_row[$field_name]);
      }
    }

    $template = SnTemplate::gettemplate('admin/admin_user', true);

    $result = [
      'PAGE_HEADER' => "[{$user_row['id']}] {$user_row['username']}",
      'USER_ID'     => $user_row['id'],
    ];
    $exclude = $user_row;
    foreach (self::$blocks as $title => $fields) {
//      $template->assign_recursive(['.' => ['block' => [self::userBlockAssign($exclude, $title, $fields)]]]);
      $result['.']['block'][] = self::userBlockAssign($exclude, $title, $fields);
    }

    if (!empty($exclude)) {
      $result['.']['block'][] = self::userBlockAssign($exclude, '!!! НЕИЗВЕСТНЫЕ ПАРАМЕТРЫ !!!', array_keys($exclude));
    }

//    $pl = reset(DBStaticPlanet::db_planet_list_sorted($user_row));
//    var_dump($pl);
//    var_dump(uni_render_planet_full($pl, '', false, true));

    foreach(DBStaticPlanet::db_planet_list_sorted($user_row) as $planetRow) {
      $result['.']['planet'][] = [
        'ID' => $planetRow['id'],
        'NAME' => $planetRow['name'],
        'NAME_RENDERED' => uni_render_planet_full($planetRow, '', false, true),
      ];
    }

    $template->assign_recursive($result);

    return $template;
  }

  /**
   * @param array      $exclude
   * @param int|string $title
   * @param array      $fields
   *
   * @return array
   */
  private static function userBlockAssign(&$exclude, $title, $fields) {
    $block = [
      'TITLE' => $title,
    ];
    foreach ($fields as $field) {
      $fieldBlock = [
        'NAME'  => $field,
        'VALUE' => isset($exclude[$field]) ? $exclude[$field] : 'N/A',
      ];
      $fieldBlock += self::renderExtra($field);
      $block['.']['field'][] = $fieldBlock;
      unset($exclude[$field]);
    }

    return $block;

  }

  private static function renderExtra($field) {
    $result = [];

    if (isset(self::$extras[$field])) {
      foreach (self::$extras[$field] as $extraName => $extraContent) {
        $result[$extraName] = $extraContent;
      }
    }

    return $result;
  }

}
