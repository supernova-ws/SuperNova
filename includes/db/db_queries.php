<?php

require_once('db_queries_account.php');
require_once('db_queries_users.php');
require_once('db_queries_planets.php');
require_once('db_queries_unit.php');
require_once('db_queries_que.php');


function db_planet_list_admin_list($table_parent_columns, $planet_active, $active_time, $planet_type)
{
  return doquery(
    "SELECT p.*, u.username" . ($table_parent_columns ? ', p1.name AS parent_name' : '') .
    " FROM {{planets}} AS p
      LEFT JOIN {{users}} AS u ON u.id = p.id_owner" .
      ($table_parent_columns ? ' LEFT JOIN {{planets}} AS p1 ON p1.id = p.parent_planet' : '') .
    " WHERE " . ($planet_active ? "p.last_update >= {$active_time}" : "p.planet_type = {$planet_type}"));
}

function db_planet_list_search($searchtext)
{
  return doquery(
    "SELECT
      p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
      u.id as uid, u.username, u.ally_id, u.id_planet,
      u.total_points, u.total_rank,
      u.ally_tag, u.ally_name
    FROM
      {{planets}} AS p
      LEFT JOIN {{users}} AS u ON u.id = p.id_owner
    WHERE
      name LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
    ORDER BY
      ally_tag, username, planet_name
    LIMIT 30;"
  );
}



function db_user_list_search($searchtext)
{
  return doquery(
    "SELECT
      pn.player_name, u.id as uid, u.username, u.ally_id, u.id_planet, u.total_points, u.total_rank,
      p.galaxy, p.system, p.planet, p.planet_type, p.name as planet_name,
      u.ally_tag, u.ally_name
    FROM
      {{player_name_history}} AS pn
      JOIN {{users}} AS u ON u.id = pn.player_id
      LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id=u.id_planet
    WHERE
      player_name LIKE '%{$searchtext}%' AND u.user_as_ally IS NULL
    ORDER BY
      ally_tag, username, planet_name
    LIMIT 30;"
  );
}

function db_buddy_list_by_user($user_id)
{
//  return ($user_id = intval($user_id)) ? doquery(
  return ($user_id = idval($user_id)) ? doquery(
    "SELECT
      b.*,
      IF(b.BUDDY_OWNER_ID = {$user_id}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID) AS BUDDY_USER_ID,
      u.username AS BUDDY_USER_NAME,
      p.name AS BUDDY_PLANET_NAME,
      p.galaxy AS BUDDY_PLANET_GALAXY,
      p.system AS BUDDY_PLANET_SYSTEM,
      p.planet AS BUDDY_PLANET_PLANET,
      a.id AS BUDDY_ALLY_ID,
      a.ally_name AS BUDDY_ALLY_NAME,
      u.onlinetime
    FROM {{buddy}} AS b
      LEFT JOIN {{users}} AS u ON u.id = IF(b.BUDDY_OWNER_ID = {$user_id}, b.BUDDY_SENDER_ID, b.BUDDY_OWNER_ID)
      LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id = id_planet
      LEFT JOIN {{alliance}} AS a ON a.id = u.ally_id
    WHERE (`BUDDY_OWNER_ID` = {$user_id}) OR `BUDDY_SENDER_ID` = {$user_id}
    ORDER BY BUDDY_STATUS, BUDDY_ID"
  ) : false;
}




function db_message_list_outbox_by_user_id($user_id)
{
  // return ($user_id = intval($user_id))
  return ($user_id = idval($user_id))
    ? doquery("SELECT {{messages}}.message_id, {{messages}}.message_owner, {{users}}.id AS message_sender, {{messages}}.message_time,
          {{messages}}.message_type, {{users}}.username AS message_from, {{messages}}.message_subject, {{messages}}.message_text
       FROM
         {{messages}} LEFT JOIN {{users}} ON {{users}}.id = {{messages}}.message_owner WHERE `message_sender` = '{$user_id}' AND `message_type` = 1
       ORDER BY `message_time` DESC;")
    : false;
}






function db_ally_count()
{
  $result = doquery('SELECT COUNT(`id`) AS ally_count FROM {{alliance}}', true);
  return isset($result['ally_count']) ? $result['ally_count'] : 0;
}



function db_unit_records_sum($unit_id, $user_skip_list_unit)
{
  return doquery (
    "SELECT unit_player_id, username, sum(unit_level) as unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          GROUP BY unit_player_id
          ORDER BY sum(unit_level) DESC, unit_player_id
          LIMIT 1;"
    , true);
}

function db_unit_records_plain($unit_id, $user_skip_list_unit)
{
  return doquery (
    "SELECT unit_player_id, username, unit_level
          FROM {{unit}} JOIN {{users}} AS u ON u.id = unit_player_id
          WHERE unit_player_id != 0 AND unit_snid = {$unit_id} {$user_skip_list_unit}
          ORDER BY unit_level DESC, unit_id
          LIMIT 1;"
    , true);
}

function db_stat_list_statistic($who, $is_common_stat, $Rank, $start, $source = false) {
// pdump($source);
  if(!$source) {
    $source = array(
      'statpoints' => 'statpoints',
      'users' => 'users',
      'id' => 'id',
      'username' => 'username',

      'alliance' => 'alliance',

    );
  } else {
    $source = array(
      'statpoints' => 'blitz_statpoints',
      'users' => 'blitz_registrations',
      'id' => 'blitz_player_id',
      'username' => 'blitz_name',

      'alliance' => 'blitz_alliance', // TODO
    );
  }
// pdump($source);
  if($who == 1) {
    if($is_common_stat) { // , UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
      $query_str =
        "SELECT
      @rownum:=@rownum+1 rownum, subject.{$source['id']} as `id`, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.{$source['username']} as `name`, subject.*
    FROM
      (SELECT @rownum:={$start}) r,
      {{{$source['statpoints']}}} as sp
      LEFT JOIN {{{$source['users']}}} AS subject ON subject.{$source['id']} = sp.id_owner
      LEFT JOIN {{{$source['statpoints']}}} AS sp_old ON sp_old.id_owner = subject.{$source['id']} AND sp_old.`stat_type` = 1 AND sp_old.`stat_code` = 2
    WHERE
      sp.`stat_type` = 1 AND sp.`stat_code` = 1
    ORDER BY
      sp.`{$Rank}_rank`, subject.{$source['id']}
    LIMIT
      ". $start .",100;";
    } else { // , UNIX_TIMESTAMP(CONCAT(YEAR(CURRENT_DATE), DATE_FORMAT(`user_birthday`, '-%m-%d'))) AS `nearest_birthday`
      $query_str =
        "SELECT
      @rownum:=@rownum+1 AS rank, subject.{$source['id']} as `id`, @rownum as rank_old, subject.{$Rank} as points, subject.{$source['username']} as name, subject.*
    FROM
      (SELECT @rownum:={$start}) r,
      {{{$source['users']}}} AS subject
    WHERE
      subject.user_as_ally is null
    ORDER BY
      subject.{$Rank} DESC, subject.{$source['id']}
    LIMIT
      ". $start .",100;";
    }
  } else {
    // TODO
    $query_str =
      "SELECT
    @rownum:=@rownum+1 as rownum, subject.id as `id`, sp.{$Rank}_rank as rank, sp.{$Rank}_old_rank as rank_old, sp.{$Rank}_points as points, subject.ally_name as name, subject.ally_tag, subject.ally_members
  FROM
    (SELECT @rownum:={$start}) r,
    {{{$source['statpoints']}}} AS sp
    LEFT JOIN {{{$source['alliance']}}} AS subject ON subject.id = sp.id_ally
    LEFT JOIN {{{$source['statpoints']}}} AS sp_old ON sp_old.id_ally = subject.id AND sp_old.`stat_type` = 2 AND sp_old.`stat_code` = 2
  WHERE
    sp.`stat_type` = 2 AND sp.`stat_code` = 1
  ORDER BY
    sp.`{$Rank}_rank`, subject.id
  LIMIT
    ". $start .",100;";
  }

  return doquery($query_str);
}


function db_stat_list_update_user_stats()
{
  return doquery("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points WHERE user_as_ally IS NULL;");
}

function db_stat_list_update_ally_stats()
{
  return doquery("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_ally = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");
}

function db_stat_list_delete_ally_player()
{
  return doquery('DELETE s FROM {{statpoints}} AS s JOIN {{users}} AS u ON u.id = s.id_owner WHERE s.id_ally IS NULL AND u.user_as_ally IS NOT NULL');
}



function db_chat_player_list_online($chat_refresh_rate, $ally_add)
{
  $sql_date = SN_TIME_NOW - $chat_refresh_rate * 2;

  return doquery(
    "SELECT u.*, cp.*
    FROM {{chat_player}} AS cp
      JOIN {{users}} AS u ON u.id = cp.chat_player_player_id
    WHERE
      `chat_player_refresh_last` >= '{$sql_date}'
      AND (`banaday` IS NULL OR `banaday` <= " . SN_TIME_NOW . ")
      {$ally_add}
    ORDER BY authlevel DESC, `username`");
}

function db_flying_fleet_lock(&$mission_data, &$fleet_row)
{
  return doquery(
    "SELECT 1 FROM {{fleets}} AS f " .
    ($mission_data['dst_user'] || $mission_data['dst_planet'] ? "LEFT JOIN {{users}} AS ud ON ud.id = f.fleet_target_owner " : '') .
    ($mission_data['dst_planet'] ? "LEFT JOIN {{planets}} AS pd ON pd.id = f.fleet_end_planet_id " : '') .

    // Блокировка всех прилетающих и улетающих флотов, если нужно
    ($mission_data['dst_fleets'] ? "LEFT JOIN {{fleets}} AS fd ON fd.fleet_end_planet_id = f.fleet_end_planet_id OR fd.fleet_start_planet_id = f.fleet_end_planet_id " : '') .

    ($mission_data['src_user'] || $mission_data['src_planet'] ? "LEFT JOIN {{users}} AS us ON us.id = f.fleet_owner " : '') .
    ($mission_data['src_planet'] ? "LEFT JOIN {{planets}} AS ps ON ps.id = f.fleet_start_planet_id " : '') .

    "WHERE f.fleet_id = {$fleet_row['fleet_id']} GROUP BY 1 FOR UPDATE"
  );
}

function db_referrals_list_by_id($user_id)
{
  return doquery("SELECT r.*, u.username, u.register_time FROM {{referrals}} AS r LEFT JOIN {{users}} AS u ON u.id = r.id WHERE id_partner = {$user_id}");
}

function db_fleet_list_with_usernames()
{
  return doquery("SELECT f.*, u.username FROM `{{fleets}}` AS f LEFT JOIN {{users}} AS u ON u.id = f.fleet_target_owner ORDER BY `fleet_end_time` ASC;");
}

function db_message_list_admin_by_type($int_type_selected, $StartRec)
{
  return doquery("SELECT
  message_id as `ID`,
  message_from as `FROM`,
  message_owner as `OWNER_ID`,
  u.username as `OWNER_NAME`,
  message_text as `TEXT`,
  FROM_UNIXTIME(message_time) as `TIME`
FROM
  {{messages}} AS m
  LEFT JOIN {{users}} AS u ON u.id = m.message_owner " .
    ($int_type_selected >= 0 ? "WHERE `message_type` = {$int_type_selected} " : '') .
    "ORDER BY
  `message_id` DESC
LIMIT
  {$StartRec}, 25;");
}


function db_ally_list_recalc_counts()
{
  return doquery("UPDATE {{alliance}} as a LEFT JOIN (SELECT ally_id, count(*) as ally_memeber_count FROM {{users}}
      WHERE ally_id IS NOT NULL GROUP BY ally_id) as u ON u.ally_id = a.id SET a.`ally_members` = u.ally_memeber_count;");
}

function db_ally_request_list($ally_id)
{
  return doquery("SELECT {{alliance_requests}}.*, {{users}}.username FROM {{alliance_requests}} LEFT JOIN {{users}} ON {{users}}.id = {{alliance_requests}}.id_user WHERE id_ally='{$ally_id}'");
}


function db_message_insert_all($message_type, $from, $subject, $text)
{
  return doquery($QryInsertMessage = 'INSERT INTO {{messages}} (`message_owner`, `message_sender`, `message_time`, `message_type`, `message_from`, `message_subject`, `message_text`) ' .
    "SELECT `id`, 0, unix_timestamp(now()), {$message_type}, '{$from}', '{$subject}', '{$text}' FROM {{users}}");
}
