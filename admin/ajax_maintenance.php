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

  // Выводим из отпуска игроков, которые находятся там более 8 недель
  'UPDATE {{users}}
  SET vacation = 0, vacation_next = 0
  WHERE
    authlevel = 0 AND user_as_ally IS NULL AND user_bot = 0 /* Не админы, Не Альянсы, Не боты */
    AND vacation > 0 AND banaday = 0 /* В отпуске и не в бане */
    AND vacation < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 8 WEEK)) /* Находящиеся в отпуске более 8 недель */;',

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


  'DELETE FROM `{{messages}}`  WHERE `message_owner`  not in (select id from {{users}});', // TODO NO FK
  'DELETE FROM `{{planets}}`   WHERE `id_owner`       not in (select id from {{users}}) AND id_owner <> 0;', // TODO NO FK
  'DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});',


  'DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} WHERE `user_as_ally` IS NOT NULL group by ally_id);',
//  'DELETE FROM {{statpoints}} WHERE stat_type=2 AND id_owner not in (select id from {{alliance}});', // TODO CHECK!

  // UBE reports
  'DELETE FROM `{{ube_report}}` WHERE `ube_report_time_combat` < DATE_SUB(now(), INTERVAL 60 day);',
  'DELETE FROM {{messages}} WHERE message_time < unix_timestamp(now()) - (60 * 60 * 24 * 30);',
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
