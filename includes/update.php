<?php
/*
 update.php

 Automated DB upgrade system

 @package supernova
 @version 26

 25 - copyright (c) 2009-2011 Gorlum for http://supernova.ws
   [!] Now it's all about transactions...
   [~] Converted doquery to internal wrapper with logging ability
 24 - copyright (c) 2009-2011 Gorlum for http://supernova.ws
   [+] Converted pre v18 entries to use later implemented functions
 v18-v23 - copyright (c) 2009-2010 Gorlum for http://supernova.ws
   [!] DB code updates
 17 - copyright (c) 2009-2010 Gorlum for http://supernova.ws
   [~] PCG1 compliant

 v01-v16 copyright (c) 2009-2010 Gorlum for http://supernova.ws
   [!] DB code updates
*/

if(!defined('INIT'))
{
  include_once('init.php');
}

define('IN_UPDATE', true);

require('includes/upd_helpers.php');

$config->reset();
$config->db_loadAll();
$config->debug = 0;

//$config->db_loadItem('db_version');
if($config->db_version == DB_VERSION)
{
}
elseif($config->db_version > DB_VERSION)
{
  global $config, $time_now;

  $config->db_saveItem('var_db_update_end', $time_now);
  die('Internal error! Auotupdater detects DB version greater then can be handled!<br>Possible you have out-of-date SuperNova version<br>Pleas upgrade your server from <a href="http://github.com/supernova-ws/SuperNova">GIT repository</a>.');
}

if($config->db_version < 26)
{
  global $sys_log_disabled;
  $sys_log_disabled = true;
}

$upd_log = '';
$new_version = floatval($config->db_version);
upd_check_key('upd_lock_time', 60, !isset($config->upd_lock_time));

upd_log_message('Update started. Disabling server');

$old_server_status = $config->game_disable;
$old_server_reason = $config->game_disable_reason;
$config->db_saveItem('game_disable', 1);
$config->db_saveItem('game_disable_reason', 'Server is updating. Please wait');

upd_log_message('Server disabled. Loading table info...');
$update_tables  = array();
$update_indexes = array();
$query = upd_do_query('SHOW TABLES;');
while($row = mysql_fetch_row($query))
{
  upd_load_table_info($row[0]);
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

upd_do_query('SET FOREIGN_KEY_CHECKS=0;');

if($new_version < 32)
{
  require_once('update_old.php');
}

switch($new_version)
{
  case 35:
    upd_log_version_update();

    upd_do_query("UPDATE {{users}} SET `ally_name` = null, `ally_tag` = null, ally_register_time = 0, ally_rank_id = 0 WHERE `ally_id` IS NULL");

    if(!$update_tables['ube_report'])
    {
      upd_create_table('ube_report',
        "(
          `ube_report_id` SERIAL COMMENT 'Report ID',

          `ube_report_cypher` CHAR(32) NOT NULL DEFAULT '' COMMENT '16 char secret report ID',

          `ube_report_time_combat` DATETIME NOT NULL COMMENT 'Combat time',
          `ube_report_time_process` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time when combat was processed',
          `ube_report_time_spent` DECIMAL(11,8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Time in seconds spent for combat calculations',

          `ube_report_mission_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Mission type',
          `ube_report_combat_admin` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Does admin participates in combat?',

          `ube_report_combat_result` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Combat outcome',
          `ube_report_combat_sfr` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Small Fleet Reconnaissance',

          `ube_report_planet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet ID',
          `ube_report_planet_name` VARCHAR(64) NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
          `ube_report_planet_size` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player diameter',
          `ube_report_planet_galaxy` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate galaxy',
          `ube_report_planet_system` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate system',
          `ube_report_planet_planet` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate planet',
          `ube_report_planet_planet_type` TINYINT NOT NULL DEFAULT 1 COMMENT 'Player planet type',

          `ube_report_moon` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Moon result: was, none, failed, created, destroyed',
          `ube_report_moon_chance` DECIMAL(9,6) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Moon creation chance',
          `ube_report_moon_size` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Moon size',
          `ube_report_moon_reapers` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Moon reapers result: none, died, survived',
          `ube_report_moon_destroy_chance` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Moon destroy chance',
          `ube_report_moon_reapers_die_chance` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Moon reapers die chance',

          `ube_report_debris_metal` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Metal debris',
          `ube_report_debris_crystal` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Crystal debris',

          PRIMARY KEY (`ube_report_id`),
          KEY `I_ube_report_cypher` (`ube_report_cypher`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_player'])
    {
      upd_create_table('ube_report_player',
        "(
          `ube_report_player_id` SERIAL COMMENT 'Record ID',
          `ube_report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
          `ube_report_player_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player ID',

          `ube_report_player_name` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'Player name',
          `ube_report_player_attacker` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Is player an attacker?',

          `ube_report_player_bonus_attack` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Player attack bonus', -- Only for statistics
          `ube_report_player_bonus_shield` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Player shield bonus', -- Only for statistics
          `ube_report_player_bonus_armor` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Player armor bonus', -- Only for statistics

          PRIMARY KEY (`ube_report_player_id`),
          KEY `I_ube_report_player_player_id` (`ube_report_player_player_id`),
          CONSTRAINT `FK_ube_report_player_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{$config->db_prefix}ube_report` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_fleet'])
    {
      upd_create_table('ube_report_fleet',
        "(
          `ube_report_fleet_id` SERIAL COMMENT 'Record DB ID',

          `ube_report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
          `ube_report_fleet_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner ID',
          `ube_report_fleet_fleet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',

          `ube_report_fleet_planet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player attack bonus',
          `ube_report_fleet_planet_name` VARCHAR(64) NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
          `ube_report_fleet_planet_galaxy` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate galaxy',
          `ube_report_fleet_planet_system` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate system',
          `ube_report_fleet_planet_planet` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate planet',
          `ube_report_fleet_planet_planet_type` TINYINT NOT NULL DEFAULT 1 COMMENT 'Player planet type',

          `ube_report_fleet_bonus_attack` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Fleet attack bonus', -- Only for statistics
          `ube_report_fleet_bonus_shield` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Fleet shield bonus', -- Only for statistics
          `ube_report_fleet_bonus_armor` DECIMAL(11,2) NOT NULL DEFAULT 0 COMMENT 'Fleet armor bonus',   -- Only for statistics

          `ube_report_fleet_resource_metal` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet metal amount',
          `ube_report_fleet_resource_crystal` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet crystal amount',
          `ube_report_fleet_resource_deuterium` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium amount',

          PRIMARY KEY (`ube_report_fleet_id`),
          CONSTRAINT `FK_ube_report_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{$config->db_prefix}ube_report` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_unit'])
    {
      // TODO: Сохранять так же имя корабля - на случай конструкторов - не, хуйня. Конструктор может давать имена разные на разных языках
      // Может сохранять имена удаленных кораблей долго?

      // round SIGNED!!! -1 например - для ауткома
      upd_create_table('ube_report_unit',
        "(
          `ube_report_unit_id` SERIAL COMMENT 'Record DB ID',

          `ube_report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
          `ube_report_unit_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner ID',
          `ube_report_unit_fleet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',
          `ube_report_unit_round` TINYINT NOT NULL DEFAULT 0 COMMENT 'Round number',

          `ube_report_unit_unit_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit ID',
          `ube_report_unit_count` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit count',
          `ube_report_unit_boom` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit booms',

          `ube_report_unit_attack` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit attack',
          `ube_report_unit_shield` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit shield',
          `ube_report_unit_armor` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit armor',

          `ube_report_unit_attack_base` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base attack',
          `ube_report_unit_shield_base` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base shield',
          `ube_report_unit_armor_base` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base armor',

          `ube_report_unit_sort_order` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit pass-through sort order to maintain same output',

          PRIMARY KEY (`ube_report_unit_id`),
          KEY `I_ube_report_unit_report_round_fleet_order` (`ube_report_id`, `ube_report_unit_round`, `ube_report_unit_fleet_id`, `ube_report_unit_sort_order`),
          KEY `I_ube_report_unit_report_unit_order` (`ube_report_id`, `ube_report_unit_sort_order`),
          KEY `I_ube_report_unit_order` (`ube_report_unit_sort_order`),
          CONSTRAINT `FK_ube_report_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{$config->db_prefix}ube_report` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_outcome_fleet'])
    {
      upd_create_table('ube_report_outcome_fleet',
        "(
          `ube_report_outcome_fleet_id` SERIAL COMMENT 'Record DB ID',

          `ube_report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
          `ube_report_outcome_fleet_fleet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',

          `ube_report_outcome_fleet_resource_lost_metal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet metal loss from units',
          `ube_report_outcome_fleet_resource_lost_crystal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet crystal loss from units',
          `ube_report_outcome_fleet_resource_lost_deuterium` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium loss from units',

          `ube_report_outcome_fleet_resource_dropped_metal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet metal dropped due reduced cargo',
          `ube_report_outcome_fleet_resource_dropped_crystal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet crystal dropped due reduced cargo',
          `ube_report_outcome_fleet_resource_dropped_deuterium` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium dropped due reduced cargo',

          `ube_report_outcome_fleet_resource_loot_metal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot metal',
          `ube_report_outcome_fleet_resource_loot_crystal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot crystal',
          `ube_report_outcome_fleet_resource_loot_deuterium` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot deuterium',

          `ube_report_outcome_fleet_resource_lost_in_metal` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Fleet total resource loss in metal',

          PRIMARY KEY (`ube_report_outcome_fleet_id`),
          KEY `I_ube_report_outcome_fleet_report_fleet` (`ube_report_id`, `ube_report_outcome_fleet_fleet_id`),
          CONSTRAINT `FK_ube_report_outcome_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{$config->db_prefix}ube_report` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_outcome_unit'])
    {
      upd_create_table('ube_report_outcome_unit',
        "(
          `ube_report_outcome_unit_id` SERIAL COMMENT 'Record DB ID',

          `ube_report_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
          `ube_report_outcome_unit_fleet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',

          `ube_report_outcome_unit_unit_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit ID',
          `ube_report_outcome_unit_restored` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Unit restored',
          `ube_report_outcome_unit_lost` DECIMAL(65,0) NOT NULL DEFAULT 0 COMMENT 'Unit lost',

          `ube_report_outcome_unit_sort_order` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit pass-through sort order to maintain same output',

          PRIMARY KEY (`ube_report_outcome_unit_id`),
          KEY `I_ube_report_outcome_unit_report_order` (`ube_report_id`, `ube_report_outcome_unit_sort_order`),
          CONSTRAINT `FK_ube_report_outcome_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{$config->db_prefix}ube_report` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['unit'])
    {
      upd_create_table('unit',
        "(
          `unit_id` SERIAL COMMENT 'Record ID',

          `unit_player_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Unit owner',
          `unit_location_type` TINYINT NOT NULL DEFAULT 0 COMMENT 'Location type: universe, user, planet (moon?), fleet',
          `unit_location_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Location ID',
          `unit_type` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit type',
          `unit_snid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit SuperNova ID',
          `unit_level` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit level or count - dependent of unit_type',

          PRIMARY KEY (`unit_id`),
          KEY `I_unit_player_location_snid` (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_snid`),
          CONSTRAINT `FK_unit_player_id` FOREIGN KEY (`unit_player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['captain'])
    {
      upd_create_table('captain',
        "(
          `captain_id` SERIAL COMMENT 'Record ID',
          `captain_unit_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Link to `unit` record',

          `captain_xp` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain expirience',
          `captain_level` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain level so far', -- Дублирует запись в unit

          `captain_shield` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain shield bonus level',
          `captain_armor` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain armor bonus level',
          `captain_attack` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain defense bonus level',

          PRIMARY KEY (`captain_id`),
          KEY `I_captain_unit_id` (`captain_unit_id`),
          CONSTRAINT `FK_captain_unit_id` FOREIGN KEY (`captain_unit_id`) REFERENCES `{$config->db_prefix}unit` (`unit_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['fleets']['fleet_start_planet_id'])
    {
      upd_alter_table('fleets', array(
        "ADD `fleet_start_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Fleet start planet ID' AFTER `fleet_start_time`",
        "ADD `fleet_end_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Fleet end planet ID' AFTER `fleet_end_stay`",

        "ADD KEY `I_fleet_start_planet_id` (`fleet_start_planet_id`)",
        "ADD KEY `I_fleet_end_planet_id` (`fleet_end_planet_id`)",

        "ADD CONSTRAINT `FK_fleet_planet_start` FOREIGN KEY (`fleet_start_planet_id`) REFERENCES `{$config->db_prefix}planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_fleet_planet_end` FOREIGN KEY (`fleet_end_planet_id`) REFERENCES `{$config->db_prefix}planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_tables['fleets']['fleet_start_planet_id']);

      upd_do_query("
        UPDATE {{fleets}} AS f
         LEFT JOIN {{planets}} AS p_s ON p_s.galaxy = f.fleet_start_galaxy AND p_s.system = f.fleet_start_system AND p_s.planet = f.fleet_start_planet AND p_s.planet_type = f.fleet_start_type
         LEFT JOIN {{planets}} AS p_e ON p_e.galaxy = f.fleet_end_galaxy AND p_e.system = f.fleet_end_system AND p_e.planet = f.fleet_end_planet AND p_e.planet_type = f.fleet_end_type
        SET f.fleet_start_planet_id = p_s.id, f.fleet_end_planet_id = p_e.id
      ");
    }

    upd_alter_table('fleets', array("DROP COLUMN `processing_start`"), $update_tables['fleets']['processing_start']);

    if(!$update_tables['chat_player'])
    {
      upd_create_table('chat_player',
        "(
          `chat_player_id` SERIAL COMMENT 'Record ID',

          `chat_player_player_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Chat player record owner',
          `chat_player_activity` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last player activity in chat',
          `chat_player_invisible` TINYINT NOT NULL DEFAULT 0 COMMENT 'Player invisibility',
          `chat_player_muted` INT(11) NOT NULL DEFAULT 0 COMMENT 'Player is muted',
          `chat_player_mute_reason` VARCHAR(256) NOT NULL DEFAULT '' COMMENT 'Player mute reason',

          PRIMARY KEY (`chat_player_id`),

          KEY `I_chat_player_id` (`chat_player_player_id`),

          CONSTRAINT `FK_chat_player_id` FOREIGN KEY (`chat_player_player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    upd_alter_table('chat', array(
      "ADD `chat_message_sender_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Message sender ID' AFTER `messageid`",
      "ADD `chat_message_recipient_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Message recipient ID' AFTER `user`",

      "ADD KEY `I_chat_message_sender_id` (`chat_message_sender_id`)",
      "ADD KEY `I_chat_message_recipient_id` (`chat_message_recipient_id`)",

      "ADD CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
    ), !$update_tables['chat']['chat_message_sender_id']);

    upd_alter_table('chat', array(
      "ADD `chat_message_sender_name` VARCHAR(64) DEFAULT '' COMMENT 'Message sender name' AFTER `chat_message_sender_id`",
      "ADD `chat_message_recipient_name` VARCHAR(64) DEFAULT '' COMMENT 'Message sender name' AFTER `chat_message_recipient_id`",
    ), !$update_tables['chat']['chat_message_sender_name']);

    upd_alter_table('users', array(
      "MODIFY COLUMN `banaday` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ban status'",
    ), strtoupper($update_tables['users']['banaday']['Null']) == 'YES');

    upd_alter_table('banned', array(
      "ADD `ban_user_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Banned user ID' AFTER `ban_id`",
      "ADD `ban_issuer_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Banner ID' AFTER `ban_until`",

      "ADD KEY `I_ban_user_id` (`ban_user_id`)",
      "ADD KEY `I_ban_issuer_id` (`ban_issuer_id`)",

      "ADD CONSTRAINT `FK_ban_user_id` FOREIGN KEY (`ban_user_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_ban_issuer_id` FOREIGN KEY (`ban_issuer_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
    ), !$update_tables['banned']['ban_user_id']);

    upd_do_query('COMMIT;', true);
    $new_version = 36;

  case 36:
    upd_log_version_update();

    upd_alter_table('payment', array(
      "DROP FOREIGN KEY `FK_payment_user`",
    ), $update_foreigns['payment']['FK_payment_user']);

    if($update_foreigns['chat']['FK_chat_message_sender_user_id'] != 'chat_message_sender_id,users,id;')
    {
      upd_alter_table('chat', array(
        "DROP FOREIGN KEY `FK_chat_message_sender_user_id`",
        "DROP FOREIGN KEY `FK_chat_message_sender_recipient_id`",
      ), true);

      upd_alter_table('chat', array(
        "ADD CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('users', array(
      "ADD `user_time_diff` INT(11) DEFAULT NULL COMMENT 'User time difference with server time' AFTER `onlinetime`",
      "ADD `user_time_diff_forced` TINYINT(1) DEFAULT 0 COMMENT 'User time difference forced with time zone selection flag' AFTER `user_time_diff`",
    ), !$update_tables['users']['user_time_diff']);

    upd_alter_table('planets', array(
      "ADD `ship_orbital_heavy` bigint(20) NOT NULL DEFAULT '0' COMMENT 'HOPe - Heavy Orbital Platform'",
    ), !$update_tables['planets']['ship_orbital_heavy']);

    upd_check_key('chat_refresh_rate', 5, !isset($config->chat_refresh_rate));

    upd_alter_table('chat_player', array(
      "ADD `chat_player_refresh_last`  INT(11) NOT NULL DEFAULT 0 COMMENT 'Player last refresh time'",

      "ADD KEY `I_chat_player_refresh_last` (`chat_player_refresh_last`)",
    ), !$update_tables['chat_player']['chat_player_refresh_last']);

    upd_alter_table('ube_report', array(
      "ADD KEY `I_ube_report_time_combat` (`ube_report_time_combat`)",
    ), !$update_indexes['ube_report']['I_ube_report_time_combat']);

    if(!$update_tables['unit']['unit_time_start'])
    {
      upd_alter_table('unit', array(
        "ADD COLUMN `unit_time_start` DATETIME NULL DEFAULT NULL COMMENT 'Unit activation start time'",
        "ADD COLUMN `unit_time_finish` DATETIME NULL DEFAULT NULL COMMENT 'Unit activation end time'",
      ), !$update_tables['unit']['unit_time_start']);

      upd_do_query(
        "INSERT INTO {{unit}}
          (unit_player_id, unit_location_type, unit_location_id, unit_type, unit_snid, unit_level, unit_time_start, unit_time_finish)
        SELECT
          `powerup_user_id`, " . LOC_USER . ", `powerup_user_id`, `powerup_category`, `powerup_unit_id`, `powerup_unit_level`
          , IF(`powerup_time_start`, FROM_UNIXTIME(`powerup_time_start`), NULL), IF(`powerup_time_finish`, FROM_UNIXTIME(`powerup_time_finish`), NULL)
        FROM {{powerup}}"
      );
    }

    if(!$update_tables['que'])
    {
      upd_create_table('que',
        "(
          `que_id` SERIAL COMMENT 'Internal que id',

          `que_player_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Que owner ID',
          `que_planet_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Which planet this que item belongs',
          `que_planet_id_origin` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Planet spawner ID',
          `que_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Que type',
          `que_time_left` DECIMAL(20,5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Build time left from last activity',

          `que_unit_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit ID',
          `que_unit_amount` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Amount left to build',
          `que_unit_mode` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Build/Destroy',

          `que_unit_level` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit level. Informational field',
          `que_unit_time` DECIMAL(20,5) NOT NULL DEFAULT 0 COMMENT 'Time to build one unit. Informational field',
          `que_unit_price` VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Price per unit - for correct trim/clear in case of global price events',

          PRIMARY KEY (`que_id`),
          KEY `I_que_player_type_planet` (`que_player_id`, `que_type`, `que_planet_id`, `que_id`), -- For main search
          KEY `I_que_player_type` (`que_player_id`, `que_type`, `que_id`), -- For main search
          KEY `I_que_planet_id` (`que_planet_id`), -- For constraint

          CONSTRAINT `FK_que_player_id` FOREIGN KEY (`que_player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          CONSTRAINT `FK_que_planet_id` FOREIGN KEY (`que_planet_id`) REFERENCES `{$config->db_prefix}planets` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          CONSTRAINT `FK_que_planet_id_origin` FOREIGN KEY (`que_planet_id_origin`) REFERENCES `{$config->db_prefix}planets` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    // Конвертирум очередь исследований
    if($update_tables['users']['que'])
    {
      $que_lines = array();
      $que_query = upd_do_query("SELECT * FROM {{users}} WHERE `que`");
      while($que_row = mysql_fetch_assoc($que_query))
      {
        $que_data = explode(',', $que_row['que']);

        if(!in_array($que_data[QI_UNIT_ID], $sn_data['groups']['tech']))
        {
          continue;
        }

        $que_data[QI_TIME] = $que_data[QI_TIME] >= 0 ? $que_data[QI_TIME] : 0;
        // Если планета пустая - ставим главку
        $que_data[QI_PLANET_ID] = $que_data[QI_PLANET_ID] ? $que_data[QI_PLANET_ID] : $que_row['id_planet'];
        if($que_data[QI_PLANET_ID])
        {
          $que_planet_check = mysql_fetch_assoc(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
          if(!$que_planet_check['id'])
          {
            $que_data[QI_PLANET_ID] = $que_row['id_planet'];
            $que_planet_check = mysql_fetch_assoc(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
            if(!$que_planet_check['id'])
            {
              $que_data[QI_PLANET_ID] = 'NULL';
            }
          }
        }
        else
        {
          $que_data[QI_PLANET_ID] = 'NULL';
        }

        $unit_level = $que_row[$sn_data[$que_data[QI_UNIT_ID]]['name']];
        $unit_factor = $sn_data[$que_data[QI_UNIT_ID]]['cost']['factor'] ? $sn_data[$que_data[QI_UNIT_ID]]['cost']['factor'] : 1;
        $price_increase = pow($unit_factor, $unit_level);
        $unit_level++;
        $unit_cost = array();
        foreach($sn_data[$que_data[QI_UNIT_ID]]['cost'] as $resource_id => $resource_amount)
        {
          if($resource_id === 'factor' || $resource_id == RES_ENERGY || !($resource_cost = $resource_amount * $price_increase))
          {
            continue;
          }
          $unit_cost[] = $resource_id . ',' . floor($resource_cost);
        }
        $unit_cost = implode(';', $unit_cost);

        $que_lines[] = "({$que_row['id']},{$que_data[QI_PLANET_ID]}," . QUE_RESEARCH . ",{$que_data[QI_TIME]},{$que_data[QI_UNIT_ID]},1," .
          BUILD_CREATE . ",{$unit_level},{$que_data[QI_TIME]},'{$unit_cost}')";
      }

      if(!empty($que_lines))
      {
        upd_do_query('INSERT INTO `{{que}}` (`que_player_id`,`que_planet_id_origin`,`que_type`,`que_time_left`,`que_unit_id`,`que_unit_amount`,`que_unit_mode`,`que_unit_level`,`que_unit_time`,`que_unit_price`) VALUES ' . implode(',', $que_lines));
      }

      upd_alter_table('users', array(
        "DROP COLUMN `que`",
      ), $update_tables['users']['que']);
    }


    upd_check_key('server_que_length_research', 1, !isset($config->server_que_length_research));


    // Ковертируем технологии в таблицы
    if($update_tables['users']['graviton_tech'])
    {
      upd_do_query("DELETE FROM {{unit}} WHERE unit_type = " . UNIT_TECHNOLOGIES);

      $que_lines = array();
      $user_query = upd_do_query("SELECT * FROM {{users}}");
      upd_add_more_time(300);
      while($user_row = mysql_fetch_assoc($user_query))
      {
        foreach($sn_data['groups']['tech'] as $tech_id)
        {
          if($tech_level = intval($user_row[$sn_data[$tech_id]['name']]))
          {
            $que_lines[] = "({$user_row['id']}," . LOC_USER . ",{$user_row['id']}," . UNIT_TECHNOLOGIES . ",{$tech_id},{$tech_level})";
          }
        }
      }

      if(!empty($que_lines))
      {
        upd_do_query("INSERT INTO {{unit}} (unit_player_id, unit_location_type, unit_location_id, unit_type, unit_snid, unit_level) VALUES " . implode(',', $que_lines));
      }

      upd_alter_table('users', array(
        "DROP COLUMN `graviton_tech`",
      ), $update_tables['users']['graviton_tech']);
    }

    if(!$update_indexes['unit']['I_unit_record_search'])
    {
      upd_alter_table('unit', array(
        "ADD KEY `I_unit_record_search` (`unit_snid`,`unit_player_id`,`unit_level` DESC,`unit_id`)",
      ), !$update_indexes['unit']['I_unit_record_search']);

      foreach(array_merge($sn_data['groups']['structures'], $sn_data['groups']['fleet'], $sn_data['groups']['defense']) as $unit_id)
      {
        $planet_units[$sn_data[$unit_id]['name']] = 1;
      }
      $drop_index = array();
      $create_index = &$drop_index; // array();
      foreach($planet_units as $unit_name => $unit_create)
      {
        if($update_indexes['planets']['I_' . $unit_name])
        {
          $drop_index[] = "DROP KEY I_{$unit_name}";
        }
        if($update_indexes['planets']['i_' . $unit_name])
        {
          $drop_index[] = "DROP KEY i_{$unit_name}";
        }

        if($unit_create)
        {
          $create_index[] = "ADD KEY `I_{$unit_name}` (`id_owner`, {$unit_name} DESC)";
        }
      }
      upd_alter_table('planets', $drop_index, true);
    }

    upd_alter_table('users', array(
      "ADD `user_time_utc_offset` INT(11) DEFAULT NULL COMMENT 'User time difference with server time' AFTER `user_time_diff`",
    ), !$update_tables['users']['user_time_utc_offset']);

    if(!$update_foreigns['alliance']['FK_alliance_owner'])
    {
      upd_do_query("UPDATE {{alliance}} SET ally_owner = null WHERE ally_owner not in (select id from {{users}})");

      upd_alter_table('alliance', array(
        "ADD CONSTRAINT `FK_alliance_owner` FOREIGN KEY (`ally_owner`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_foreigns['alliance']['FK_alliance_owner']);

      upd_do_query("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id not in (select id from {{alliance}}) OR alliance_negotiation_contr_ally_id not in (select id from {{alliance}})");

      upd_do_query("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id = alliance_negotiation_contr_ally_id");
      upd_do_query("DELETE FROM {{alliance_diplomacy}} WHERE alliance_diplomacy_ally_id = alliance_diplomacy_contr_ally_id");
    }

    upd_alter_table('fleets', array(
      'MODIFY COLUMN `fleet_owner` BIGINT(20) UNSIGNED DEFAULT NULL',
      "ADD CONSTRAINT `FK_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
    ), strtoupper($update_tables['fleets']['fleet_owner']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_check_key('chat_highlight_developer', '<span class="nick_developer">$1</span>', !$config->chat_highlight_developer);

    if(!$update_tables['player_name_history'])
    {
      upd_check_key('game_user_changename_cost', 100000, !$config->game_user_changename_cost);
      upd_check_key('game_user_changename', SERVER_PLAYER_NAME_CHANGE_PAY, $config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_PAY);

      upd_alter_table('users', array(
        "CHANGE COLUMN `username` `username` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'Player name'",
      ));

      upd_create_table('player_name_history',
        "(
          `player_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Player ID',
          `player_name` VARCHAR(32) NOT NULL COMMENT 'Historical player name',
          `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When player changed name',

          PRIMARY KEY (`player_name`),
          KEY `I_player_name_history_id_name` (`player_id`, `player_name`),

          CONSTRAINT `FK_player_name_history_id` FOREIGN KEY (`player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_do_query("REPLACE INTO {{player_name_history}} (`player_id`, `player_name`) SELECT `id`, `username` FROM {{users}} WHERE `user_as_ally` IS NULL;");
    }

    upd_alter_table('planets', array(
      "ADD `density` SMALLINT NOT NULL DEFAULT 5500 COMMENT 'Planet average density kg/m3'",
      "ADD `density_index` TINYINT NOT NULL DEFAULT " . PLANET_DENSITY_STANDARD . " COMMENT 'Planet cached density index'",
    ), !$update_tables['planets']['density_index']);

    if($update_tables['users']['player_artifact_list'])
    {
      upd_alter_table('unit', "DROP KEY `unit_id`", $update_indexes['unit']['unit_id']);

//      upd_alter_table('unit', "ADD KEY `I_unit_player_id_temporary` (`unit_player_id`)", !$update_indexes['unit']['I_unit_player_id_temporary']);
//      upd_alter_table('unit', "DROP KEY `I_unit_player_location_snid`", $update_indexes['unit']['I_unit_player_location_snid']);
      upd_alter_table('unit', "ADD KEY `I_unit_player_location_snid` (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_snid`)", !$update_indexes['unit']['I_unit_player_location_snid']);
      upd_alter_table('unit', "DROP KEY `I_unit_player_id_temporary`", $update_indexes['unit']['I_unit_player_id_temporary']);

      $sn_data_artifacts = &$sn_data['groups']['artifacts'];
      $db_changeset = array();

      $query = upd_do_query("SELECT `id`, `player_artifact_list` FROM {{users}} WHERE `player_artifact_list` IS NOT NULL AND `player_artifact_list` != '' FOR UPDATE");
      while($row = mysql_fetch_assoc($query))
      {
        $artifact_list = explode(';', $row['player_artifact_list']);
        if(!$row['player_artifact_list'] || empty($artifact_list))
        {
          continue;
        }
        foreach($artifact_list as $key => &$value)
        {
          $value = explode(',', $value);
          if(!isset($value[1]) || $value[1] <= 0 || !isset($sn_data_artifacts[$value[0]]))
          {
            unset($artifact_list[$key]);
            continue;
          }
          $db_changeset['unit'][] = sn_db_unit_changeset_prepare($value[0], $value[1], $row);
        }
      }
      sn_db_changeset_apply($db_changeset);

      upd_alter_table('users', "DROP COLUMN `player_artifact_list`", $update_tables['users']['player_artifact_list']);
    }

    upd_alter_table('users', array(
      "DROP COLUMN `spy_tech`",
      "DROP COLUMN `computer_tech`",
      "DROP COLUMN `military_tech`",
      "DROP COLUMN `defence_tech`",
      "DROP COLUMN `shield_tech`",
      "DROP COLUMN `energy_tech`",
      "DROP COLUMN `hyperspace_tech`",
      "DROP COLUMN `combustion_tech`",
      "DROP COLUMN `impulse_motor_tech`",
      "DROP COLUMN `hyperspace_motor_tech`",
      "DROP COLUMN `laser_tech`",
      "DROP COLUMN `ionic_tech`",
      "DROP COLUMN `buster_tech`",
      "DROP COLUMN `intergalactic_tech`",
      "DROP COLUMN `expedition_tech`",
      "DROP COLUMN `colonisation_tech`",
    ), $update_tables['users']['spy_tech']);

    upd_check_key('payment_currency_exchange_dm_', 2500,             !$config->payment_currency_exchange_dm_ || $config->payment_currency_exchange_dm_ == 1000);
    upd_check_key('payment_currency_exchange_eur', 0.09259259259259, !$config->payment_currency_exchange_eur);
    upd_check_key('payment_currency_exchange_rub', 4.0,              !$config->payment_currency_exchange_rub);
    upd_check_key('payment_currency_exchange_usd', 0.125,            !$config->payment_currency_exchange_usd);
    upd_check_key('payment_currency_exchange_wme', 0.0952380952381,  !$config->payment_currency_exchange_usd);
    upd_check_key('payment_currency_exchange_wmr', 4.1,              !$config->payment_currency_exchange_wmr);
    upd_check_key('payment_currency_exchange_wmu', 1.05,             !$config->payment_currency_exchange_wmu);
    upd_check_key('payment_currency_exchange_wmz', 0.126582278481,   !$config->payment_currency_exchange_wmz);

    upd_do_query('COMMIT;', true);
    $new_version = 37;

  case 37:
    upd_log_version_update();

    upd_check_key('player_vacation_timeout', PERIOD_WEEK, $config->player_vacation_timeout != PERIOD_WEEK);
    upd_check_key('player_vacation_time', PERIOD_WEEK ,   $config->player_vacation_time != PERIOD_WEEK);

    upd_alter_table('users', "ADD `vacation_next` INT(11) NOT NULL DEFAULT 0 COMMENT 'Next datetime when player can go on vacation'", !$update_tables['users']['vacation_next']);

    upd_alter_table('users', "ADD `metamatter` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Metamatter amount'", !$update_tables['users']['metamatter']);
    upd_check_key('url_purchase_metamatter', $config->url_dark_matter, !$config->url_purchase_metamatter && $config->url_dark_matter);
    upd_check_key('url_dark_matter', '', $config->url_dark_matter); // TODO REMOVE KEY FROM DB

    upd_check_key('payment_currency_exchange_mm_', 2500, !$config->payment_currency_exchange_mm_);

    if(!$update_tables['log_metamatter'])
    {
      upd_create_table('log_metamatter',
        "(
          `id` SERIAL,
          `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp',
          `user_id` BIGINT(20) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID which make log record',
          `username` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'Username',
          `reason` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reason ID for metamatter adjustment',
          `amount` BIGINT(10) NOT NULL DEFAULT 0 COMMENT 'Amount of metamatter change',
          `comment` TEXT COMMENT 'Comments',
          `page` VARCHAR(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',

          PRIMARY KEY (`id`),
          KEY `I_log_metamatter_sender_id` (`user_id`, `id`),
          KEY `I_log_metamatter_reason_sender_id` (`reason`, `user_id`, `id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
      );
    }

    upd_check_key('adv_seo_javascript', '', !isset($config->adv_seo_javascript));

    upd_alter_table('payment', array(
      "ADD `payment_test` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Is this a test payment?'",
    ), !$update_tables['payment']['payment_test']);

    if($update_tables['payment']['payment_test']['Default'] == 1)
    {
      upd_alter_table('payment', array(
        "MODIFY COLUMN `payment_test` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Is this a test payment?'",
      ));

      upd_do_query('UPDATE {{payment}} SET `payment_test` = 0;');
    }

    upd_do_query('UPDATE {{payment}} SET `payment_test` = 1, `payment_status` = 1 WHERE payment_status = -1;');

    upd_check_key('game_speed_expedition', 1, !$config->game_speed_expedition);

    upd_alter_table('users', array(
      "MODIFY COLUMN `metamatter` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Metamatter amount'",
    ), $update_tables['users']['metamatter']['Type'] == 'int(20)');

    if(!$update_tables['users']['metamatter_total'])
    {
      upd_alter_table('users', "ADD `metamatter_total` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Total Metamatter amount ever bought'", !$update_tables['users']['metamatter_total']);

      upd_do_query('UPDATE {{users}} SET metamatter_total = (SELECT sum(payment_dark_matter_gained) FROM {{payment}} WHERE payment_user_id = id AND payment_status > 0);');
    }

    $query = upd_do_query("SELECT * FROM {{que}} WHERE `que_type` = " . QUE_RESEARCH . " AND que_unit_id in (" . TECH_EXPEDITION . "," . TECH_COLONIZATION . ") FOR UPDATE");
    while($row = mysql_fetch_assoc($query))
    {
      $planet_id = ($row['que_planet_id_origin'] ? $row['que_planet_id_origin'] : $row['que_planet_id']);
      upd_do_query("SELECT id FROM {{planets}} WHERE id = {$planet_id} FOR UPDATE");
      $price = sys_unit_str2arr($row['que_unit_price']);
      upd_do_query("UPDATE {{planets}} SET " .
        "`metal` = `metal` + " . ($price[RES_METAL] ? $price[RES_METAL] : 0) . "," .
        "`crystal` = `crystal` + " . ($price[RES_CRYSTAL] ? $price[RES_CRYSTAL] : 0) . "," .
        "`deuterium` = `deuterium` + " . ($price[RES_DEUTERIUM] ? $price[RES_DEUTERIUM] : 0) .
        " WHERE id = {$planet_id}"
      );
      // pdump($row);
      // pdump(mysql_error());
      upd_do_query("DELETE FROM {{que}} WHERE que_id = {$row['que_id']}");
    }

    $query = upd_do_query("SELECT unit_id, unit_snid, unit_level, id_planet FROM {{unit}} AS un
    LEFT JOIN {{users}} AS u ON u.id = un.unit_player_id
    LEFT JOIN {{planets}} AS p ON p.id = u.id_planet
    WHERE unit_snid in (" . TECH_EXPEDITION . "," . TECH_COLONIZATION . ")
    FOR UPDATE");
    while($row = mysql_fetch_assoc($query))
    {
      if(!$row['id_planet'])
      {
        continue;
      }

      $unit_id = $row['unit_snid'];
      $unit_level = $row['unit_level'];
      $price = $sn_data[$unit_id]['cost'];
      $factor = $sn_data[$unit_id]['cost']['factor'];
      foreach($price as $resource_id => &$resource_amount)
      {
        $resource_amount = $resource_amount * (pow($factor, $unit_level) - 1) / ($factor - 1);
      }
      // upd_do_query
      upd_do_query($q = "UPDATE {{planets}} SET " .
        "`metal` = `metal` + " . ($price[RES_METAL] ? $price[RES_METAL] : 0) . "," .
        "`crystal` = `crystal` + " . ($price[RES_CRYSTAL] ? $price[RES_CRYSTAL] : 0) . "," .
        "`deuterium` = `deuterium` + " . ($price[RES_DEUTERIUM] ? $price[RES_DEUTERIUM] : 0) .
        " WHERE id = {$row['id_planet']}"
      );
      // pdump($q);
      // pdump(mysql_error());
      upd_do_query("DELETE FROM {{unit}} WHERE unit_id = {$row['unit_id']}");
      // pdump(mysql_error());
      // pdump($row);
      // pdump($price);
      // pdump(mysql_error());
    }

    // Удалить из очереди Экспедиционную технологию и вернуть ресы
    // Удалить из очереди Колонизационную технологию и вернуть ресы
    // Вернуть ресы за уже исследованную Колонизационную технологию
    // Вернуть ресы за уже исследованную Экспедиционную технологию
    upd_check_key('player_max_colonies', -1, $config->player_max_colonies >= 0);

/*
//      upd_alter_table('unit', "ADD KEY `I_unit_player_id_temporary` (`unit_player_id`)", !$update_indexes['unit']['I_unit_player_id_temporary']);
//      upd_alter_table('unit', "DROP KEY `I_unit_player_location_snid`", $update_indexes['unit']['I_unit_player_location_snid']);
      upd_alter_table('unit', "ADD KEY `I_unit_player_location_snid` (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_snid`)", !$update_indexes['unit']['I_unit_player_location_snid']);
      upd_alter_table('unit', "DROP KEY `I_unit_player_id_temporary`", $update_indexes['unit']['I_unit_player_id_temporary']);
*/

/*
    upd_alter_table('planets', array(
      "ADD CONSTRAINT `FK_planet_owner` FOREIGN KEY (`id_owner`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE NULL ON UPDATE CASCADE",
    ), !$update_tables['planets']['FK_planet_owner']);
*/

/*
    upd_alter_table('banned', array(
      "DROP CONSTRAINT `FK_ban_user_id`",
    ), $update_foreigns['banned']['FK_ban_user_id']);

    upd_alter_table('banned', array(
      "ADD CONSTRAINT `FK_ban_user_id` FOREIGN KEY (`ban_user_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE NULL ON UPDATE CASCADE",
    ), !$update_tables['banned']['FK_ban_user_id']);
*/
    if(!isset($update_tables['users']['player_rpg_explore_xp']))
    {
      upd_alter_table('users', array(
        "ADD COLUMN `player_rpg_explore_level` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `dark_matter`",
        "ADD COLUMN `player_rpg_explore_xp` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `dark_matter`",
      ), !isset($update_tables['users']['player_rpg_explore_xp']));
    }

    upd_do_query('COMMIT;', true);
    // $new_version = 38;
};
upd_log_message('Upgrade complete.');

upd_do_query('SET FOREIGN_KEY_CHECKS=1;');

if($new_version)
{
  $config->db_saveItem('db_version', $new_version);
  upd_log_message("<font color=green>DB version is now {$new_version}</font>");
}
else
{
  upd_log_message("DB version didn't changed from {$config->db_version}");
}

$config->db_loadAll();

if($user['authlevel'] >= 3)
{
  print(str_replace("\r\n", '<br>', $upd_log));
}

unset($sn_cache->tables);
sys_refresh_tablelist($config->db_prefix);

upd_log_message('Restoring server status');
$config->db_saveItem('game_disable', $old_server_status);
$config->db_saveItem('game_disable_reason', $old_server_reason);
