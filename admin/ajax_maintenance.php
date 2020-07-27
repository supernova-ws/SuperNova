<?php /** @noinspection SqlResolve */

define('IN_ADMIN', true);

require('../includes/init.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  SnTemplate::messageBox($lang['sys_noalloaw'], $lang['sys_noaccess']);
  die();
}

define('IN_AJAX', true);

lng_include('admin');

$totaltime = microtime(true);
$pack_until = date("Y-m-01 00:00:00", SN_TIME_NOW - PERIOD_MONTH * 3);

// [#] info_best_battles 1b0
$best_reports = array();
if(defined('MODULE_INFO_BEST_BATTLES_QUERY')) {
  $query = doquery(MODULE_INFO_BEST_BATTLES_QUERY);
  while($row = db_fetch($query)) {
    $best_reports[] = $row['ube_report_id'];
  }
}
$best_reports = !empty($best_reports) ? ' AND ube_report_id NOT IN (' . implode(',', $best_reports) . ')' : '';


$ques = array(
//  'DELETE {{users}}.* FROM {{users}} WHERE `user_as_ally` IS NULL and `onlinetime` < unix_timestamp(now()) - ( 60 * 60 * 24 * 45) and metamatter_total <= 0;',

  // Выводим из отпуска игроков, которые находятся там более 4 недель
//  'UPDATE {{users}}
//  SET vacation = 0, vacation_next = 0
//  WHERE
//    authlevel = 0 AND user_as_ally IS NULL AND user_bot = ' . USER_BOT_PLAYER . ' /* Не админы, Не Альянсы, Не боты */
//    AND vacation > 0 AND banaday = 0 /* В отпуске и не в бане */
//    AND vacation < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Находящиеся в отпуске более 4 недель */;',

//  // Игроки удаляются по Регламенту
//  'DELETE FROM `{{users}}` WHERE
//    authlevel = 0 AND user_as_ally IS NULL AND user_bot = ' . USER_BOT_PLAYER . ' AND metamatter_total = 0 AND /* Не админы, Не Альянсы, Не боты, Не Бессмертные*/
//    metamatter = 0 AND /* Нету ММ */
//    vacation = 0 AND banaday = 0 AND /* Не в отпуске, Не в бане */
//    (
//      (onlinetime - register_time < 5 * 60 AND UNIX_TIMESTAMP() - onlinetime > 2*7 *86400)
//      OR (onlinetime - register_time < 30 * 60 AND UNIX_TIMESTAMP() - onlinetime > 4*7 *86400)
//      OR (onlinetime - register_time < 10 * 60*60 AND UNIX_TIMESTAMP() - onlinetime > 6*7 *86400)
//      OR (UNIX_TIMESTAMP() - onlinetime > 8*7 *86400)
//    );',

  // Игроки, которые не были активны более 4 недель становятся I-шками. Для них
  // Отключаем получение писем
//  'UPDATE {{users}}
//  SET OPTIONS = ""
//  WHERE
//    authlevel = 0 AND user_as_ally IS NULL AND user_bot = ' . USER_BOT_PLAYER . ' AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
//    AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
//  // Отключаем производство на планетах
//  'UPDATE {{users}} AS u
//    JOIN {{planets}} AS p ON p.id_owner = u.id
//  SET
//    metal_perhour = 0,
//    crystal_perhour  = 0,
//    deuterium_perhour  = 0,
//    metal_mine_porcent = 0,
//    crystal_mine_porcent = 0,
//    deuterium_sintetizer_porcent = 0,
//    solar_plant_porcent = 0,
//    fusion_plant_porcent = 0,
//    solar_satelit_porcent = 0,
//    ship_sattelite_sloth_porcent = 0
//  WHERE
//		authlevel = 0 AND user_as_ally IS NULL AND user_bot = ' . USER_BOT_PLAYER . ' AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
//		AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
  // Удаляем все здания из очереди
//  'DELETE q FROM {{users}} AS u JOIN {{que}} AS q ON q.que_player_id = u.id
//  WHERE
//		authlevel = 0 AND user_as_ally IS NULL AND user_bot = ' . USER_BOT_PLAYER . ' AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
//		AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
  // Возвращаем все флоты ???
  // Пока не будем делать запрос - за 4 недели всяко все флоты должны вернутся...
  // TODO I-шки - неделя на разграбление - или сколько там стата хранится...

  // Удаляем планеты без пользователей
  'DELETE FROM `{{planets}}` WHERE `id_owner` not in (select id from `{{users}}`) AND id_owner <> 0;', // TODO NO FK Переписать на джоине
  // Удаляем юниты без планет
  'DELETE un FROM `{{unit}}` AS un
    LEFT JOIN `{{planets}}` AS pl ON pl.id = un.unit_location_id
  WHERE unit_location_type = ' . LOC_PLANET . ' AND pl.id IS NULL;',
  // Удаляем пустые юниты с 0 уровнем (кроме Капитана) - TODO - перенести в модуль, если нужно!
//  'DELETE FROM {{unit}} WHERE unit_location_type = ' . LOC_PLANET . ' AND unit_level = 0 AND unit_type <> ' . UNIT_CAPTAIN,
  // Удаляем очереди на ничьих планетах
  'DELETE q FROM `{{que}}` AS q
    LEFT JOIN `{{planets}}` AS p ON p.id = q.que_planet_id
  WHERE
    que_type IN (' . QUE_STRUCTURES . ', ' . QUE_HANGAR . ', ' . SUBQUE_FLEET . ', ' . SUBQUE_DEFENSE . ')
    AND
    (p.id_owner = 0 OR p.id_owner IS NULL);',

  // Удаляем пустые САБы
  'DELETE FROM `{{aks}}` WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM `{{fleets}}`);', // TODO Переписать на джоине

  // UBE reports
  "DELETE FROM `{{ube_report}}` WHERE `ube_report_time_combat` < DATE_SUB(NOW(), INTERVAL 60 DAY) {$best_reports};", // TODO Настройка

  // Чистка сообщений - ВРЕМЕННО ОТКЛЮЧЕНО
//  'DELETE FROM `{{messages}}`  WHERE `message_owner`  not in (select id from {{users}});', // TODO NO FK
  // Удаляются сообщения, старше  4 недель, кроме личных и Альянсовских
  'DELETE FROM `{{messages}}` WHERE
    UNIX_TIMESTAMP() - message_time > 4*7 * 24 * 60 * 60 AND
    message_type NOT IN (' . MSG_TYPE_PLAYER . ', ' . MSG_TYPE_ALLIANCE . ', ' . MSG_TYPE_ADMIN . ');',
  // Удаляются сообщения у пользователей, которые неактивны больше 4 недель - кроме личных и Альянсовских
  'DELETE m FROM `{{users}}` AS u
  JOIN `{{messages}}` AS m ON m.message_owner = u.id
  WHERE
    message_type NOT IN (' . MSG_TYPE_PLAYER . ', ' . MSG_TYPE_ALLIANCE . ') AND
    authlevel = 0 AND  user_as_ally IS NULL AND /* Не админы, Не Альянсы */
    UNIX_TIMESTAMP() - onlinetime > 4*7 *86400;',

  'DELETE FROM `{{chat}}` WHERE timestamp < unix_timestamp(now()) - (60 * 60 * 24 * 14);',

  // Recalculate Alliance members
  "UPDATE `{{alliance}}` as a LEFT JOIN (SELECT ally_id, count(*) as ally_memeber_count FROM `{{users}}` WHERE ally_id IS NOT NULL GROUP BY ally_id) as u ON u.ally_id = a.id
    SET a.`ally_members` = u.ally_memeber_count;",
  // Deleting empty Alliances - ВРЕМЕННО ОТКЛЮЧЕНО
//  'DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} WHERE `user_as_ally` IS NOT NULL group by ally_id);',
//  'DELETE FROM {{alliance}} WHERE ally_members <= 0;',
  "UPDATE `{{users}}` SET ally_id = null, ally_name = null, ally_tag = null, ally_register_time = 0, ally_rank_id = 0 WHERE ally_id not in (select id from `{{alliance}}`);",

  // Пакуем данные по логу ТМ
  array(
    "INSERT INTO {{log_dark_matter}}
      (log_dark_matter_timestamp, log_dark_matter_username, log_dark_matter_reason, log_dark_matter_amount,
      log_dark_matter_comment, log_dark_matter_page, log_dark_matter_sender)
    SELECT
      '{$pack_until}', IF(u.username IS NULL, ldm.log_dark_matter_username, u.username), " . RPG_CUMULATIVE . ", sum(ldm.log_dark_matter_amount),
      'Баланс на {$pack_until}', 'admin/ajax_maintenance.php', ldm.log_dark_matter_sender
    FROM
      {{log_dark_matter}} AS ldm
      LEFT JOIN {{users}} AS u ON u.id = ldm.log_dark_matter_sender
    WHERE
      ldm.log_dark_matter_timestamp < '{$pack_until}'
    GROUP BY
      log_dark_matter_sender;",

    "DELETE FROM `{{log_dark_matter}}` WHERE log_dark_matter_timestamp < '{$pack_until}';",
  ),

  // Пакуем статистические данные по онлайну пользователей
  array(
    "REPLACE INTO `{{log_users_online}}`
      (online_timestamp, online_count, online_aggregated)
    SELECT
      FROM_UNIXTIME((UNIX_TIMESTAMP(online_timestamp) DIV " . PERIOD_MINUTE_10 . ") * (" . PERIOD_MINUTE_10 . ")), ceil(avg(online_count)), " . LOG_ONLIINE_AGGREGATE_PERIOD_MINUTE_10 . "
    FROM
      `{{log_users_online}}`
    WHERE
      online_timestamp < '{$pack_until}' AND online_aggregated = " . LOG_ONLIINE_AGGREGATE_NONE . "
    GROUP BY
      (UNIX_TIMESTAMP(online_timestamp) DIV " . PERIOD_MINUTE_10 . ") * (" . PERIOD_MINUTE_10 . ");",

    "DELETE FROM `{{log_users_online}}` WHERE online_timestamp < '{$pack_until}' AND online_aggregated = " . LOG_ONLIINE_AGGREGATE_NONE,
  ),

  // Удаляем старые записи из логов
  "DELETE FROM `{{logs}}` WHERE log_timestamp < '{$pack_until}';",
  // Удаляем записи о маинтенансе, апдейте и пересчете статистики более чем недельной давности - они нам уже не нужны
  'DELETE FROM `{{logs}}` WHERE
    `log_code` IN (' . LOG_INFO_DB_CHANGE . ', ' . LOG_INFO_MAINTENANCE . ', ' . LOG_INFO_STAT_START . ', ' . LOG_INFO_STAT_PROCESS . ', ' . LOG_INFO_STAT_FINISH . ')
    AND `log_timestamp` < DATE_SUB(NOW(),INTERVAL 7 DAY);',


  // TODO Удаляем устройства, на которые никто не ссылается
//  "DELETE sd FROM `{{security_device}}` AS sd
//    LEFT JOIN `{{security_player_entry}}` AS spe ON spe.device_id = sd.device_id
//  WHERE player_id IS NULL;",
  // Удаляем браузеры, на которые никто не ссылается
//  "DELETE sb FROM `{{security_browser}}` AS sb
//    LEFT JOIN `{{security_player_entry}}` AS spe ON spe.browser_id = sb.browser_id
//  WHERE player_id IS NULL;",

  // Удаляем записи визитов без пользователей
//  'DELETE FROM `{{counter}}` WHERE `user_id` NOT IN (SELECT `id` FROM `{{users}}`);',
);

function sn_maintenance_pack_user_list($user_list) {
  $user_list = explode(',', $user_list);
  foreach($user_list as $key => $user_id) {
    if(!is_numeric($user_id)) {
      unset($user_list[$key]);
    }
  }

  $result = array();
  if(!empty($user_list)) {
    $query = doquery("SELECT `id` FROM `{{users}}` WHERE `id` in (" . implode(',', $user_list) . ")");
    while($row = db_fetch($query)) {
      $result[] = $row['id'];
    }
  }

  return implode(',', $result);
}

global $config, $debug, $lang;

sn_db_transaction_start();
$old_server_status = SN::$config->pass()->game_disable;
$old_server_status == GAME_DISABLE_NONE ? SN::$config->pass()->game_disable = GAME_DISABLE_MAINTENANCE : false;
sn_db_transaction_commit();

foreach($ques as $que_transaction) {
  sn_db_transaction_start();

  !is_array($que_transaction) ? $que_transaction = array($que_transaction) : false;
  foreach($que_transaction as $que) {
    set_time_limit(120);
    $QryResult = doquery($que);
    //$msg .= '<hr>' . $que . '<hr>';
    $que = str_replace(array('{{', '}}'), '', $que);
    //$que = str_replace('{{', '', $que);
    //$que = str_replace('}}', '', $que);

    $msg .=
      '<li>' . htmlspecialchars($que) .
        ' --- <span style="' . ($QryResult ? 'ok">OK' : 'error">FAILED!') . '</span> ' .
      SN::$db->db_affected_rows() . ' ' . $lang['adm_records'] .
      "</li>";

    $debug->warning($que . ' --- ' . ($QryResult ? 'OK' : 'FAILED!') . ' ' . SN::$db->db_affected_rows() . ' ' . $lang['adm_records'], 'System maintenance', LOG_INFO_MAINTENANCE);
  }

  sn_db_transaction_commit();
}

sn_db_transaction_start();
SN::$config->pass()->stats_hide_player_list = sn_maintenance_pack_user_list(SN::$config->pass()->stats_hide_player_list);
$debug->warning('Упакован stats_hide_player_list', 'System maintenance', LOG_INFO_MAINTENANCE);
sn_db_transaction_commit();

sn_db_transaction_start();
SN::$config->db_saveItem('game_watchlist', sn_maintenance_pack_user_list(SN::$config->pass()->game_watchlist));
$debug->warning('Упакован game_watchlist', 'System maintenance', LOG_INFO_MAINTENANCE);
sn_db_transaction_commit();

SN::$config->db_saveItem('users_amount', db_user_count());
SN::$config->db_saveItem('game_disable', $old_server_status);

$_GET['admin_update'] = 1;

include_once('../scheduler.php');
$totaltime = microtime(true) - $totaltime;

$result = $result ? "<li>{$lang['adm_stat_title']} - {$result}</li>" : '';
$result = '<div align="left"><ul>' . $msg . $result . '</ul></div>';
echo json_encode($result . ' ' . $totaltime);
