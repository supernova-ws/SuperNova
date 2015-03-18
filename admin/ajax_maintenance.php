<?php

define('IN_ADMIN', true);

require('../includes/init.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  message($lang['sys_noalloaw'], $lang['sys_noaccess']);
  die();
}

define('IN_AJAX', true);

lng_include('admin');

$totaltime = microtime(true);
$pack_until = date("Y-m-01 00:00:00", SN_TIME_NOW - PERIOD_MONTH * 3);

$ques = array(
//  'DELETE {{users}}.* FROM {{users}} WHERE `user_as_ally` IS NULL and `onlinetime` < unix_timestamp(now()) - ( 60 * 60 * 24 * 45) and metamatter_total <= 0;',

// FK_notes_owner  'DELETE FROM `{{notes}}`     WHERE `owner`          not in (select id from {{users}});',
// FK_fleet_owner  'DELETE FROM `{{fleets}}`    WHERE `fleet_owner`    not in (select id from {{users}});',
// FK 'DELETE FROM `{{buddy}}`     WHERE `sender`         not in (select id from {{users}});',
// FK  'DELETE FROM `{{buddy}}`     WHERE `owner`          not in (select id from {{users}});',
// Not used  'DELETE FROM `{{annonce}}`   WHERE `user`           not in (select id from {{users}});',
//  'DELETE FROM `{{messages}}`  WHERE `message_sender` not in (select id from {{users}});',

  // Выводим из отпуска игроков, которые находятся там более 4 недель
  'UPDATE {{users}}
  SET vacation = 0, vacation_next = 0
  WHERE
    authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 /* Не админы, Не Альянсы, Не боты */
    AND vacation > 0 AND banaday = 0 /* В отпуске и не в бане */
    AND vacation < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Находящиеся в отпуске более 4 недель */;',

  // Игроки удаляются по Регламенту
  'DELETE FROM `{{users}}` WHERE
    authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 AND metamatter_total = 0 AND /* Не админы, Не Альянсы, Не боты, Не Бессмертные*/
    vacation = 0 AND banaday = 0 AND /* Не в отпуске, Не в бане */
    (
      (onlinetime - register_time < 5 * 60 AND UNIX_TIMESTAMP() - onlinetime > 2*7 *86400)
      OR (onlinetime - register_time < 30 * 60 AND UNIX_TIMESTAMP() - onlinetime > 4*7 *86400)
      OR (onlinetime - register_time < 10 * 60*60 AND UNIX_TIMESTAMP() - onlinetime > 6*7 *86400)

      OR (UNIX_TIMESTAMP() - onlinetime > 8*7 *86400)
    );',

  // Игроки, которые не были активны более 4 недель становятся I-шками. Для них
  // Отключаем получение писем
  'UPDATE {{users}}
  SET OPTIONS = ""
  WHERE
    authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
    AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
  // Отключаем производство на планетах
  'UPDATE {{users}} AS u
    JOIN {{planets}} AS p ON p.id_owner = u.id
  SET
    metal_perhour = 0,
    crystal_perhour  = 0,
    deuterium_perhour  = 0,
    metal_mine_porcent = 0,
    crystal_mine_porcent = 0,
    deuterium_sintetizer_porcent = 0,
    solar_plant_porcent = 0,
    fusion_plant_porcent = 0,
    solar_satelit_porcent = 0,
    ship_sattelite_sloth_porcent = 0
  WHERE
		authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
		AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
  // Удаляем все здания из очереди
  'DELETE q FROM {{users}} AS u JOIN {{que}} AS q ON q.que_player_id = u.id
  WHERE
		authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 AND vacation = 0 /* Не админы, Не Альянсы, Не боты, Не в отпуске */
		AND onlinetime < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 4 WEEK)) /* Не выходившие в онлайн более 4 недель */;',
  // Возвращаем все флоты ???
  // Пока не будем делать запрос - за 4 недели всяко все флоты должны вернутся...
  // TODO I-шки - неделя на разграбление - или сколько там стата хранится...

//  -- DELETE
//SELECT
//	id, username AS `Name`, user_as_ally AS `is_ally`,
//	FROM_UNIXTIME(register_time) as `register`, FROM_UNIXTIME(onlinetime) AS `online`,
//	ROUND((onlinetime - register_time)/60/60, 2) as `played, h`,
//	(onlinetime - register_time)/((UNIX_TIMESTAMP() - onlinetime)/24/7) AS 'hrs/week',
//	metamatter_total as `MM`, total_points AS 'points', dark_matter as `DM Now`, dark_matter_total as `DM Ever`,
//	(SELECT sum(log_dark_matter_amount) FROM game_log_dark_matter AS dm WHERE dm.log_dark_matter_sender = id AND dm.log_dark_matter_amount > 0) as "DM logged"
///**/FROM `game_users`
//
//WHERE
///* Не админы */
//authlevel = 0 AND
///* Не Альянсы */
//user_as_ally is null AND
///* Не боты */
//user_bot = 0 AND
///* Не Бессмертные */
//metamatter_total = 0 AND
///* Зареганные в 2014 или позже */
//register_time >= UNIX_TIMESTAMP("2014-01-01") AND
///* Не в отпуске */
//vacation = 0 AND
///* Не в бане */
//banaday = 0 AND
//(
//-- Зарегались больше недели назад и ничего не сделали в игре
//-- (total_points <= 0 AND UNIX_TIMESTAMP() - onlinetime > 1*7 *86400)
//-- Зарегались более недели назад и никогда не заходили
//-- OR (onlinetime <= 0 AND UNIX_TIMESTAMP() - onlinetime > 1*7 *86400)
//
//-- Зарегались более недели назад и провели меньше минуты в игре. Такие уже не вернутся ИМХО
//-- OR (onlinetime - register_time < 1 * 60 AND UNIX_TIMESTAMP() - onlinetime > 1*7 *86400)
//-- OR
//(onlinetime - register_time < 5 * 60 AND UNIX_TIMESTAMP() - onlinetime > 2*7 *86400)
//OR (onlinetime - register_time < 30 * 60 AND UNIX_TIMESTAMP() - onlinetime > 4*7 *86400)
//OR (onlinetime - register_time < 120 * 60 AND UNIX_TIMESTAMP() - onlinetime > 2*30 *86400)
//-- OR (onlinetime - register_time < 300 * 60 AND UNIX_TIMESTAMP() - onlinetime > 3*30 *86400)
//-- OR (onlinetime - register_time < 600 * 60 AND UNIX_TIMESTAMP() - onlinetime > 4*30 *86400)
//
//-- Не логинились 3 месяца
//OR (UNIX_TIMESTAMP() - onlinetime > 3*30 *86400)
//
//-- Больше двух месяцев не логинился и не тратил ТМ вообще
//-- OR (dark_matter = dark_matter_total AND UNIX_TIMESTAMP() - onlinetime > 2*30 *86400)
//
//-- Был онлайн больше 2 месяцев назад и не играл хотя бы час в неделю за каждую неделю регистрации
//-- OR((onlinetime - register_time)/((UNIX_TIMESTAMP() - onlinetime)/24/7) < 1 AND UNIX_TIMESTAMP() - onlinetime > 2*30 *86400)
//
//
//
//
//
//
//
//
//
//-- Зарегались до НГ и с НГ пор ничего не делали - отсечка по НГ-ивентам
//-- OR (dark_matter_total <= 40000 and FROM_UNIXTIME(register_time) < "2015-01-01" AND FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 2 month))
//
//-- OR (total_points <= 1100000 and FROM_UNIXTIME(register_time) < "2014-07-01" and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 3 month))
//
///*
//(dark_matter <= 40000 and total_points = 0 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 2 week))
//OR
//(dark_matter <= 50000 and onlinetime - register_time < 1 * 60 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 1 week))
//OR
//(dark_matter <= 60000 and onlinetime - register_time < 5 * 60 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 2 week))
//OR
//(dark_matter <= 70000 and onlinetime - register_time < 10 * 60 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 3 week))
//OR
//(dark_matter <= 80000 and onlinetime - register_time < 20 * 60 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 1 month))
//
//-- Зарегались более месяца назад и провели в игре не более 20 минут со времени регистрации
//OR (onlinetime - register_time < 20 * 60 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 1 month))
//-- Зарегались более недели назад и никогда не заходили
//OR (onlinetime <= 0 and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 1 week))
//*/
///*
//(onlinetime - register_time < 1 * 60 and FROM_UNIXTIME(register_time) < DATE_SUB(date(now()),INTERVAL 1 week))
//OR
//(onlinetime - register_time < 10 * 60 and FROM_UNIXTIME(register_time) < DATE_SUB(date(now()),INTERVAL 2 week))
//OR
//(onlinetime - register_time < 15 * 60 and FROM_UNIXTIME(register_time) < DATE_SUB(date(now()),INTERVAL 3 week))
//OR
//(onlinetime - register_time < 30 * 60 and FROM_UNIXTIME(register_time) < DATE_SUB(date(now()),INTERVAL 1 month))
//*/
///*
//OR
//(total_points < 90000 AND FROM_UNIXTIME(register_time) < DATE_SUB(date(now()),INTERVAL 3 month) and FROM_UNIXTIME(onlinetime) < DATE_SUB(date(now()),INTERVAL 3 month))
//*/
//) ORDER BY onlinetime desc, register_time desc;



  'DELETE FROM `{{messages}}`  WHERE `message_owner`  not in (select id from {{users}});', // TODO NO FK
  'DELETE FROM `{{planets}}`   WHERE `id_owner`       not in (select id from {{users}}) AND id_owner <> 0;', // TODO NO FK
  'DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});',


  'DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} WHERE `user_as_ally` IS NOT NULL group by ally_id);',
//  'DELETE FROM {{statpoints}} WHERE stat_type=2 AND id_owner not in (select id from {{alliance}});', // TODO CHECK!

  // UBE reports
  'DELETE FROM `{{ube_report}}` WHERE `ube_report_time_combat` < DATE_SUB(now(), INTERVAL 60 day);',


  // Чистка сообщений
  // Удаляются сообщения, старше  4 недель, кроме личных и Альянсовских
  'DELETE FROM {{messages}} WHERE
    UNIX_TIMESTAMP() - message_time > 4*7 * 24 * 60 * 60 AND
    message_type NOT IN (' . MSG_TYPE_PLAYER . ', ' . MSG_TYPE_ALLIANCE . ');',
  // Удаляются сообщения у пользователей, которые неактивны больше 4 недель - кроме личных и Альянсовских
  'DELETE m FROM `{{users}}` AS u
  JOIN game_messages AS m ON m.message_owner = u.id
  WHERE
    message_type NOT IN (' . MSG_TYPE_PLAYER . ', ' . MSG_TYPE_ALLIANCE . ') AND
    authlevel = 0 AND  user_as_ally IS NULL AND /* Не админы, Не Альянсы */
    UNIX_TIMESTAMP() - onlinetime > 4*7 *86400;',

  'DELETE FROM {{chat}} WHERE timestamp < unix_timestamp(now()) - (60 * 60 * 24 * 14);',

  // Recalculate Alliance members
  "UPDATE {{alliance}} as a LEFT JOIN (SELECT ally_id, count(*) as ally_memeber_count FROM {{users}} WHERE ally_id IS NOT NULL GROUP BY ally_id) as u ON u.ally_id = a.id
    SET a.`ally_members` = u.ally_memeber_count;",

  // Deleting empty Alliances
  'DELETE FROM {{alliance}} WHERE ally_members <= 0;',
  "UPDATE {{users}} SET ally_id = null, ally_name = null, ally_tag = null, ally_register_time = 0, ally_rank_id = 0 WHERE ally_id not in (select id from {{alliance}});",
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

    "DELETE FROM {{log_dark_matter}} WHERE log_dark_matter_timestamp < '{$pack_until}';",
  ),

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

    "DELETE FROM {{log_users_online}} WHERE online_timestamp < '{$pack_until}' AND online_aggregated = " . LOG_ONLIINE_AGGREGATE_NONE,
  ),

  "DELETE FROM {{logs}} WHERE log_timestamp < '{$pack_until}';",
);

function sn_maintenance_pack_user_list($user_list) {
  $user_list = explode(',', $user_list);
  foreach($user_list as $key => $user_id) {
    if(!ceil(floatval($user_id))) {
      unset($user_list[$key]);
    }
  }

  $result = array();
  if(!empty($user_list)) {
    $query = doquery("SELECT `id` FROM {{users}} WHERE `id` in (" . implode(',', $user_list) . ")");
    while($row = mysql_fetch_assoc($query)) {
      $result[] = $row['id'];
    }
  }

  return implode(',', $result);
}

global $config, $debug, $lang;

sn_db_transaction_start();
$old_server_status = $config->db_loadItem('game_disable');
$old_server_status == GAME_DISABLE_NONE ? $config->db_saveItem('game_disable', GAME_DISABLE_MAINTENANCE) : false;
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
        mysql_affected_rows($link) . ' ' . $lang['adm_records'] .
      "</li>";

    $debug->warning($que . ' --- ' . ($QryResult ? 'OK' : 'FAILED!') . ' ' . mysql_affected_rows($link) . ' ' . $lang['adm_records'], 'System maintenance', LOG_INFO_MAINTENANCE);
  }

  sn_db_transaction_commit();
}

sn_db_transaction_start();
$config->db_saveItem('stats_hide_player_list', sn_maintenance_pack_user_list($config->db_loadItem('stats_hide_player_list')));
$debug->warning('Упакован stats_hide_player_list', 'System maintenance', LOG_INFO_MAINTENANCE);
sn_db_transaction_commit();

sn_db_transaction_start();
$config->db_saveItem('game_watchlist', sn_maintenance_pack_user_list($config->db_loadItem('game_watchlist')));
$debug->warning('Упакован game_watchlist', 'System maintenance', LOG_INFO_MAINTENANCE);
sn_db_transaction_commit();

$config->db_saveItem('users_amount', db_user_count());
$config->db_saveItem('game_disable', $old_server_status);

$_GET['admin_update'] = 1;

include_once('../scheduler.php');
$totaltime = microtime(true) - $totaltime;

$result = $result ? "<li>{$lang['adm_stat_title']} - {$result}</li>" : '';
$result = '<div align="left"><ul>' . $msg . $result . '</ul></div>';
echo json_encode($result . ' ' . $totaltime);
