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

if(!defined('INIT')) {
//  include_once('init.php');
  die('Unauthorized access');
}

define('IN_UPDATE', true);

require('includes/upd_helpers.php');

global $sn_cache, $new_version, $db_prefix, $config, $db_name, $debug, $sys_log_disabled, $upd_log, $update_tables, $update_indexes, $update_foreigns;
global $db_prefix, $cache_prefix;

$config->reset();
$config->db_loadAll();
$config->db_prefix = $db_prefix;
$config->cache_prefix = $cache_prefix;
$config->debug = 0;

//$config->db_loadItem('db_version');
if($config->db_version == DB_VERSION) {
} elseif($config->db_version > DB_VERSION) {
  $config->db_saveItem('var_db_update_end', SN_TIME_NOW);
  die(
    'Internal error! Auotupdater detects DB version greater then can be handled!<br />
    Possible you have out-of-date SuperNova version<br />
    Please upgrade your server from <a href="http://github.com/supernova-ws/SuperNova">GIT repository</a>'
  );
}

if($config->db_version < 26) {
  $sys_log_disabled = true;
}

$upd_log = '';
$new_version = floatval($config->db_version);
upd_check_key('upd_lock_time', 300, !isset($config->upd_lock_time));

set_time_limit($config->upd_lock_time + 10);

upd_log_message('Update started. Disabling server');

$old_server_status = $config->db_loadItem('game_disable');
$config->db_saveItem('game_disable', GAME_DISABLE_UPDATE);

upd_log_message('Server disabled. Loading table info...');
$update_tables  = array();
$update_indexes = array();
$query = upd_do_query('SHOW TABLES;');
while($row = db_fetch_row($query)) {
  upd_load_table_info($row[0]);
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

upd_do_query('SET FOREIGN_KEY_CHECKS=0;');

if($new_version < 32) {
  require_once('update_old.php');
}

ini_set('memory_limit', '1024M');

switch($new_version) {
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
      while($que_row = db_fetch($que_query))
      {
        $que_data = explode(',', $que_row['que']);

        if(!in_array($que_data[QI_UNIT_ID], sn_get_groups('tech')))
        {
          continue;
        }

        $que_data[QI_TIME] = $que_data[QI_TIME] >= 0 ? $que_data[QI_TIME] : 0;
        // Если планета пустая - ставим главку
        $que_data[QI_PLANET_ID] = $que_data[QI_PLANET_ID] ? $que_data[QI_PLANET_ID] : $que_row['id_planet'];
        if($que_data[QI_PLANET_ID])
        {
          $que_planet_check = db_fetch(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
          if(!$que_planet_check['id'])
          {
            $que_data[QI_PLANET_ID] = $que_row['id_planet'];
            $que_planet_check = db_fetch(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
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

        $unit_info = get_unit_param($que_data[QI_UNIT_ID]);
        $unit_level = $que_row[$unit_info[P_NAME]];
        $unit_factor = $unit_info[P_COST][P_FACTOR] ? $unit_info[P_COST][P_FACTOR] : 1;
        $price_increase = pow($unit_factor, $unit_level);
        $unit_level++;
        $unit_cost = array();
        foreach($unit_info[P_COST] as $resource_id => $resource_amount)
        {
          if($resource_id === P_FACTOR || $resource_id == RES_ENERGY || !($resource_cost = $resource_amount * $price_increase))
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
      $sn_group_tech = sn_get_groups('tech');
      while($user_row = db_fetch($user_query))
      {
        foreach($sn_group_tech as $tech_id)
        {
          if($tech_level = intval($user_row[get_unit_param($tech_id, P_NAME)]))
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

      foreach(sn_get_groups(array('structures', 'fleet', 'defense')) as $unit_id)
      {
        $planet_units[get_unit_param($unit_id, P_NAME)] = 1;
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

      $sn_data_artifacts = sn_get_groups('artifacts');
      $db_changeset = array();

      $query = upd_do_query("SELECT `id`, `player_artifact_list` FROM {{users}} WHERE `player_artifact_list` IS NOT NULL AND `player_artifact_list` != '' FOR UPDATE");
      while($row = db_fetch($query))
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
          $db_changeset['unit'][] = upd_db_unit_changeset_prepare($value[0], $value[1], $row);
        }
      }
      upd_db_changeset_apply($db_changeset);

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

    $query = upd_do_query("SELECT * FROM {{que}} WHERE `que_type` = " . QUE_RESEARCH . " AND que_unit_id in (" . TECH_EXPEDITION . "," . TECH_COLONIZATION . ") FOR UPDATE");
    while($row = db_fetch($query))
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
      upd_do_query("DELETE FROM {{que}} WHERE que_id = {$row['que_id']}");
    }

    $query = upd_do_query("SELECT unit_id, unit_snid, unit_level, id_planet FROM {{unit}} AS un
    LEFT JOIN {{users}} AS u ON u.id = un.unit_player_id
    LEFT JOIN {{planets}} AS p ON p.id = u.id_planet
    WHERE unit_snid in (" . TECH_EXPEDITION . "," . TECH_COLONIZATION . ")
    FOR UPDATE");
    while($row = db_fetch($query))
    {
      if(!$row['id_planet'])
      {
        continue;
      }

      $unit_id = $row['unit_snid'];
      $unit_level = $row['unit_level'];
      $price = get_unit_param($unit_id, P_COST);
      $factor = $price['factor'];
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
      upd_do_query("DELETE FROM {{unit}} WHERE unit_id = {$row['unit_id']}");
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

    if(!$update_tables['log_users_online'])
    {
      upd_create_table('log_users_online',"(
        `online_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Measure time',
        `online_count` SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Users online',

        PRIMARY KEY (`online_timestamp`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    upd_check_key('server_log_online', 0, !isset($config->server_log_online));

    upd_alter_table('users', array(
      "ADD `user_time_measured` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'When was time diff measured last time' AFTER `onlinetime`",
    ), !$update_tables['users']['user_time_measured']);

    if($update_tables['rw'])
    {
      upd_do_query("DROP TABLE IF EXISTS {{rw}};");
    }

    if(!$update_tables['player_award'])
    {
      upd_create_table('player_award', "(
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `award_type_id` int(11) DEFAULT NULL COMMENT 'Award type i.e. order, medal, pennant, rank etc',
        `award_id` int(11) DEFAULT NULL COMMENT 'Global award unit ID',
        `award_variant_id` int(11) DEFAULT NULL COMMENT 'Multiply award subtype i.e. for same reward awarded early',
        `player_id` bigint(20) UNSIGNED DEFAULT NULL,
        `awarded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was awarded',
        `active_from` datetime DEFAULT NULL,
        `active_to` datetime DEFAULT NULL,
        `hide` tinyint(1) NOT NULL DEFAULT '0',

        PRIMARY KEY (`id`),
        KEY `I_award_player` (`player_id`,`award_type_id`),

        CONSTRAINT `FK_player_award_user_id` FOREIGN KEY (`player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    upd_alter_table('payment', array(
      "MODIFY COLUMN `payment_module_name` varchar(64) DEFAULT '' COMMENT 'Payment module name'",
      "MODIFY COLUMN `payment_external_id` varchar(64) DEFAULT '' COMMENT 'External payment ID in payment system'",
    ), strtolower($update_tables['payment']['payment_test']['Type']) != 'varchar(64)');

    upd_check_key('stats_schedule', '01 00:00:00', strpos($config->stats_schedule, '@') !== false);

    upd_alter_table('users', array(
      "ADD `admin_protection` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Protection of administration planets'",
    ), !$update_tables['users']['admin_protection']);

    upd_alter_table('announce', array(
      "ADD `user_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Announcer user ID'",
      "ADD `user_name` varchar(32) DEFAULT NULL COMMENT 'Announcer user name'",
    ), !$update_tables['announce']['user_id']);

    upd_do_query('COMMIT;', true);
    $new_version = 38;

  case 38:
    upd_log_version_update();


    if(!isset($update_tables['planets']['que_processed'])) {
      upd_alter_table('planets', array(
        "ADD COLUMN `que_processed` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `last_update`",
      ), true);
      upd_do_query("UPDATE {{planets}} SET que_processed = last_update;");
    }

    if(!isset($update_tables['users']['que_processed'])) {
      upd_alter_table('users', array(
        "ADD COLUMN `que_processed` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `onlinetime`",
      ), true);
      upd_do_query("UPDATE {{users}} SET que_processed = onlinetime;");
    }





    if(isset($update_tables['planets']['que'])) {
      $sn_data_aux = array(
        SHIP_SMALL_FIGHTER_WRATH => array(
          'name' => 'ship_fighter_wrath',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 4000,
            RES_CRYSTAL   => 1500,
            RES_DEUTERIUM => 500,
          ),
        ),
        SHIP_CARGO_GREED => array(
          'name' => 'ship_cargo_greed',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 40000,
            RES_CRYSTAL   => 10000,
            RES_DEUTERIUM => 10000,
          ),
        ),
        SHIP_SATTELITE_SLOTH => array(
          'name' => 'ship_sattelite_sloth',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 1000,
            RES_CRYSTAL   => 1000,
            RES_DEUTERIUM => 1000,
          ),
        ),
        SHIP_LARGE_BATTLESHIP_PRIDE => array(
          'name' => 'ship_battleship_pride',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 40000,
            RES_CRYSTAL   => 40000,
            RES_DEUTERIUM => 20000,
          ),
        ),
        SHIP_RECYCLER_GLUTTONY => array(
          'name' => 'ship_recycler_gluttony',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 10000,
            RES_CRYSTAL   => 10000,
            RES_DEUTERIUM => 3000,
          ),
        ),
        SHIP_MEDIUM_BOMBER_ENVY => array(
          'name' => 'ship_bomber_envy',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 35000,
            RES_CRYSTAL   => 15000,
            RES_DEUTERIUM => 10000,
          ),
        ),
        SHIP_LARGE_ORBITAL_HEAVY => array(
          'name' => 'ship_orbital_heavy',
          'type' => UNIT_SHIPS,
          'cost' => array(
            RES_METAL     => 40000,
            RES_CRYSTAL   => 30000,
            RES_DEUTERIUM => 40000,
          ),
        ),
      );
      $aux_group = array_combine(array_keys($sn_data_aux), array_keys($sn_data_aux));

      $ques_info = sn_get_groups('ques');
      $group_resource_loot = sn_get_groups('resources_loot');
      $planet_unit_list = sn_get_groups(array('structures', 'fleet', 'defense'));
      $planet_unit_list += $aux_group;

      $drop = array(
        'DROP COLUMN `que`',
        'DROP COLUMN `b_hangar`',
        'DROP COLUMN `b_hangar_id`',
      );
      $unit_data_max = 0;
      $que_data_max = 0;
      $units_info = array();
      $que_data = array();
      $unit_data = array();
      $planets = array();

      foreach($planet_unit_list as $unit_id) {
        if(!($unit_name = get_unit_param($unit_id, P_NAME))) {
          $unit_name = $sn_data_aux[$unit_id][P_NAME];
        }
        if(isset($update_tables['planets'][$unit_name])) {
          $drop[] = "DROP COLUMN `{$unit_name}`";

          if(isset($aux_group[$unit_id])) {
            $units_info[$unit_id] = $sn_data_aux[$unit_id];
            $units_info[$unit_id]['que'] = QUE_HANGAR;
          } else {
            $units_info[$unit_id] = get_unit_param($unit_id);
            foreach($ques_info as $que_id => $que_data1) {
              if(in_array($unit_id, $que_data1['unit_list'])) {
                $units_info[$unit_id]['que'] = $que_id;
                break;
              }
            }
          }
        }
      }

      $query = upd_do_query("SELECT * FROM {{planets}} FOR UPDATE");
      while($row = db_fetch($query)) {
        $user_id = $row['id_owner'];
        $planet_id = $row['id'];

        $planets[] = $planet_id;

        // Конвертируем юниты
        $units_levels = array();
        foreach($planet_unit_list as $unit_id) {
          $unit_name = &$units_info[$unit_id][P_NAME];
          if(!isset($row[$unit_name]) || !$row[$unit_name]) continue;
          $units_levels[$unit_id] = $row[$unit_name];
          $unit_data[] = "({$user_id}," . LOC_PLANET . ",{$planet_id},{$units_info[$unit_id][P_UNIT_TYPE]},{$unit_id},{$units_levels[$unit_id]})";
          if(count($unit_data) > 30) {
            $unit_data_max = strlen(implode(',', $unit_data)) > $unit_data_max ? strlen(implode(',', $unit_data)) : $unit_data_max;
            upd_do_query('REPLACE INTO {{unit}} (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_type`, `unit_snid`, `unit_level`) VALUES ' . implode(',', $unit_data) . ';');
            $unit_data = array();
          }
        }

        // Конвертируем очередь построек
        if($row['que']) {
          $que = explode(';', $row['que']);
          foreach($que as $que_item) {
            if(!$que_item) continue;

            $que_item = explode(',', $que_item);

            $unit_id = $que_item[0];
            $build_destroy_divisor = $que_item[3] == BUILD_CREATE ? 1 : 2;

            $unit_level = isset($units_levels[$unit_id]) ? $units_levels[$unit_id] : 0;

            $unit_cost = $units_info[$unit_id][P_COST];
            $unit_factor = $unit_cost[P_FACTOR] ? $unit_cost[P_FACTOR] : 1;
            $price_increase = pow($unit_factor, $unit_level);
            // $unit_time = 0;
            foreach($unit_cost as $resource_id => &$resource_amount) {
              if(!in_array($resource_id, $group_resource_loot)) {
                unset($unit_cost[$resource_id]);
                continue;
              }

              $resource_amount = floor($resource_amount * $price_increase / $build_destroy_divisor);
            }
            $unit_cost = sys_unit_arr2str($unit_cost);
            $units_levels[$unit_id] += $que_item[3];
            $que_data[] = "({$user_id},{$planet_id},{$planet_id},1,{$que_item[2]},{$unit_id},1,{$que_item[3]},{$units_levels[$unit_id]},{$que_item[2]},'{$unit_cost}')";
          }
        }
 
        // Конвертируем очередь верфи
        if($row['b_hangar_id']) {
          $return_resources = array(RES_METAL => 0, RES_CRYSTAL => 0, RES_DEUTERIUM => 0, );
          $hangar_units = sys_unit_str2arr($row['b_hangar_id']);
          foreach($hangar_units as $unit_id => $unit_count) {
            if($unit_count <= 0) continue;
            foreach($units_info[$unit_id][P_COST] as $resource_id => $resource_amount) {
              if(!in_array($resource_id, $group_resource_loot)) continue;
              $return_resources[$resource_id] += $unit_count * $resource_amount;
            }
          }
          if(array_sum($return_resources) > 0) {
            upd_do_query("UPDATE {{planets}} SET `metal` = `metal` + {$return_resources[RES_METAL]}, `crystal` = `crystal` + {$return_resources[RES_CRYSTAL]}, `deuterium` = `deuterium` + {$return_resources[RES_DEUTERIUM]} WHERE `id` = {$planet_id} LIMIT 1");
          }
        }


        if(count($que_data) > 10) {
          $que_data_max = strlen(implode(',', $que_data)) > $que_data_max ? strlen(implode(',', $que_data)) : $que_data_max;
          upd_do_query('INSERT INTO {{que}} (`que_player_id`, `que_planet_id`, `que_planet_id_origin`, `que_type`, `que_time_left`, `que_unit_id`, `que_unit_amount`, `que_unit_mode`, `que_unit_level`, `que_unit_time`, `que_unit_price`) VALUES ' . implode(',', $que_data) . ';');
          $que_data = array();
        }
      }

      if(!empty($unit_data))
        upd_do_query('REPLACE INTO {{unit}} (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_type`, `unit_snid`, `unit_level`) VALUES ' . implode(',', $unit_data) . ';');

      if(!empty($que_data))
        upd_do_query('INSERT INTO {{que}} (`que_player_id`, `que_planet_id`, `que_planet_id_origin`, `que_type`, `que_time_left`, `que_unit_id`, `que_unit_amount`, `que_unit_mode`, `que_unit_level`, `que_unit_time`, `que_unit_price`) VALUES ' . implode(',', $que_data) . ';');

      upd_alter_table('planets', $drop, true);
    }

    upd_do_query("UPDATE `{{alliance}}` AS a
      JOIN `{{users}}` AS u ON a.`id` = u.`user_as_ally` AND `user_as_ally` IS NOT NULL AND `username` = ''
      SET u.`username` = CONCAT('[', a.`ally_tag`, ']');");

    if($update_indexes['statpoints']['I_stats_id_ally'] != 'id_ally,stat_type,stat_code,') {
      upd_do_query("SET FOREIGN_KEY_CHECKS=0;");
      upd_alter_table('statpoints', "DROP FOREIGN KEY `FK_stats_id_ally`", $update_foreigns['statpoints']['FK_stats_id_ally']);
      upd_alter_table('statpoints', "DROP KEY `I_stats_id_ally`", $update_indexes['statpoints']['I_stats_id_ally']);
      upd_alter_table('statpoints', "ADD KEY `I_stats_id_ally` (`id_ally`,`stat_type`,`stat_code`) USING BTREE", !$update_indexes['statpoints']['I_stats_id_ally']);
      upd_alter_table('statpoints', "ADD CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE", !$update_foreigns['statpoints']['FK_stats_id_ally']);
    }

    upd_alter_table('statpoints', "ADD KEY `I_stats_type_code` (`stat_type`,`stat_code`) USING BTREE", !$update_indexes['statpoints']['I_stats_type_code']);

    upd_do_query('UPDATE {{unit}} SET unit_time_start = NULL WHERE unit_time_start = "1970-01-01 03:00:00"');
    upd_do_query('UPDATE {{unit}} SET unit_time_finish = NULL WHERE unit_time_finish = "1970-01-01 03:00:00"');

    upd_alter_table('unit', "ADD KEY `I_unit_location` (unit_location_type,unit_location_id)", !$update_indexes['unit']['I_unit_location']);

    upd_alter_table('logs', array(
      "MODIFY COLUMN `log_dump` MEDIUMTEXT NOT NULL DEFAULT '' COMMENT 'Machine-readable dump of variables' AFTER `log_time`",
    ), $update_tables['logs']['log_dump']['Type'] != 'mediumtext');

    upd_alter_table('users', array(
      "ADD COLUMN `settings_info` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `settings_rep`",
      "ADD COLUMN `settings_statistics` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `settings_rep`",
    ), !isset($update_tables['users']['settings_statistics']));

    upd_create_table('lng_usage_stat',
      "(
        `lang_code` char(2) COLLATE utf8_unicode_ci NOT NULL,
        `string_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
        `file` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
        `line` smallint(6) NOT NULL,
        `is_empty` tinyint(1) NOT NULL,
        `locale` mediumtext COLLATE utf8_unicode_ci,
        PRIMARY KEY (`lang_code`,`string_id`,`file`,`line`,`is_empty`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
    );

    upd_do_query(
      "DELETE {{unit}}
      FROM {{unit}}
      LEFT JOIN {{planets}} ON id = unit_location_id
      WHERE unit_location_type = 1 AND unit_type = 99 AND (
        (planet_type = 1 AND unit_snid IN (41, 42, 43))
        OR
        (planet_type = 3 AND unit_snid NOT IN (14, 15, 21, 34, 41, 42, 43))
      );");

    upd_create_table('player_options', "(
        `player_id` bigint(20) UNSIGNED DEFAULT NULL,
        `option_id` smallint UNSIGNED NOT NULL DEFAULT 0,
        `value` VARCHAR(1900) NOT NULL DEFAULT '',

        PRIMARY KEY (`player_id`, `option_id`),

        CONSTRAINT `FK_player_options_user_id` FOREIGN KEY (`player_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    upd_create_table('security_browser', " (
      `browser_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `browser_user_agent` VARCHAR(250) NOT NULL DEFAULT '',
      PRIMARY KEY (`browser_id`),
      KEY `I_browser_user_agent` (`browser_user_agent`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;");
    upd_create_table('security_device', " (
      `device_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `device_cypher` char(16) NOT NULL DEFAULT '',
      PRIMARY KEY (`device_id`),
      KEY `I_device_cypher` (`device_cypher`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;");
    upd_create_table('security_player_entry', " (
      `player_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `device_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `browser_id` bigint(20) unsigned NOT NULL DEFAULT '0',
      `user_ip` int(10) unsigned DEFAULT NULL,
      `user_proxy` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
      `first_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`player_id`,`device_id`,`browser_id`,`user_ip`,`user_proxy`),
      KEY `I_player_entry_device_id` (`device_id`) USING BTREE,
      KEY `I_player_entry_browser_id` (`browser_id`),
      CONSTRAINT `FK_security_player_entry_device_id` FOREIGN KEY (`device_id`) REFERENCES `{{security_device}}` (`device_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_security_player_entry_browser_id` FOREIGN KEY (`browser_id`) REFERENCES `{{security_browser}}` (`browser_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_security_player_entry_player_id` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;");


    upd_alter_table('users', array(
      "DROP COLUMN `user_agent`",
      "DROP COLUMN `user_proxy`",
    ), isset($update_tables['users']['user_agent']));

    upd_alter_table('users', array(
      "ADD COLUMN `user_last_proxy` VARCHAR(250) NOT NULL DEFAULT '' AFTER `user_lastip`",
      "ADD COLUMN `user_last_browser_id` BIGINT(20) UNSIGNED DEFAULT NULL AFTER `user_last_proxy`",
      "ADD KEY `I_users_last_browser_id` (`user_last_browser_id`)",
      "ADD CONSTRAINT `FK_users_browser_id` FOREIGN KEY (`user_last_browser_id`) REFERENCES `{$config->db_prefix}security_browser` (`browser_id`) ON DELETE SET NULL ON UPDATE CASCADE",
    ), !isset($update_tables['users']['user_last_proxy']));

    if(!isset($update_tables['notes']['planet_type'])) {
      upd_alter_table('notes', array(
//      "ADD COLUMN `planet_name` VARCHAR(64) NOT NULL DEFAULT '' AFTER `title`",
        "ADD COLUMN `galaxy` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 AFTER `title`",
        "ADD COLUMN `system` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 AFTER `galaxy`",
        "ADD COLUMN `planet` SMALLINT(6) UNSIGNED NOT NULL DEFAULT 0 AFTER `system`",
        "ADD COLUMN `planet_type` TINYINT(4) UNSIGNED NOT NULL DEFAULT 1 AFTER `planet`",
        "ADD COLUMN `sticky` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `text`",
      ), !isset($update_tables['notes']['planet_type']));

      upd_do_query(
        "INSERT INTO {{notes}} (`owner`, `galaxy`, `system`, `planet`, `planet_type`, `title`, `text`, `priority`)
          SELECT `shortcut_user_id`, `shortcut_galaxy`, `shortcut_system`, `shortcut_planet`, `shortcut_planet_type`, `shortcut_text`, `shortcut_text`, 2 FROM {{shortcut}}");
    }
    $update_tables['shortcut'] && upd_do_query("DROP TABLE IF EXISTS {{shortcut}};");

    upd_alter_table('users', "ADD COLUMN `user_bot` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0", !isset($update_tables['users']['user_bot']));
    upd_alter_table('unit', "ADD KEY `I_unit_type_snid` (unit_type, unit_snid) USING BTREE", !$update_indexes['unit']['I_unit_type_snid']);

    if($update_tables['users']['settings_tooltiptime']['Type'] != 'smallint(5) unsigned') {
      upd_alter_table('users', array(
        "MODIFY COLUMN `settings_tooltiptime` smallint(5) unsigned NOT NULL DEFAULT '500'",
      ), $update_tables['users']['settings_tooltiptime']['Type'] != 'smallint');

      upd_do_query("UPDATE `{{users}}` SET settings_tooltiptime = 500;");
    }

    if(!isset($update_tables['log_users_online']['online_aggregated'])) {
      upd_alter_table('log_users_online', "ADD COLUMN `online_aggregated` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0", !isset($update_tables['log_users_online']['online_aggregated']));
      upd_alter_table('log_users_online', array(
        "DROP PRIMARY KEY",
        "ADD PRIMARY KEY (`online_timestamp`, `online_aggregated`)",
      ), $update_indexes['log_users_online']['PRIMARY'] != 'online_timestamp,online_aggregated,');
    }

    if(!isset($update_tables['users']['gender'])) {
      upd_alter_table('users', "ADD COLUMN `gender` TINYINT(1) UNSIGNED NOT NULL DEFAULT " . GENDER_UNKNOWN, !isset($update_tables['users']['gender']));
      upd_do_query("UPDATE {{users}} SET `gender` = IF(UPPER(`sex`) = 'F', " . GENDER_FEMALE. ", IF(UPPER(`sex`) = 'M', " . GENDER_MALE . ", " . GENDER_UNKNOWN . "));");
    }
    upd_alter_table('users', "DROP COLUMN `sex`", isset($update_tables['users']['sex']));

// TODO - Решить - паковать баланс или нет
//    $pack_until = "2014-11-01 00:00:00";
//    $temp = db_fetch(upd_do_query("SELECT COUNT(*) AS cnt FROM {{log_dark_matter}} WHERE log_dark_matter_timestamp < '{$pack_until}';"));
//    if($temp['cnt']) {
//      upd_do_query(
//        "INSERT INTO {{log_dark_matter}} (log_dark_matter_timestamp, log_dark_matter_username, log_dark_matter_reason,
//          log_dark_matter_amount, log_dark_matter_comment, log_dark_matter_page, log_dark_matter_sender)
//        SELECT
//          '{$pack_until}', IF(ldm.log_dark_matter_username IS NOT NULL, ldm.log_dark_matter_username, u.username), " . RPG_CUMULATIVE . ",
//          u.dark_matter - sum(ldm.log_dark_matter_amount), 'Баланс на {$pack_until}', 'includes/update.php', u.id
//        FROM
//          {{users}} AS u
//          LEFT JOIN {{log_dark_matter}} AS ldm ON u.id = ldm.log_dark_matter_sender
//        WHERE log_dark_matter_timestamp < '{$pack_until}'
//        GROUP BY
//          u.id;");
//
//      upd_do_query("DELETE FROM {{log_dark_matter}} WHERE log_dark_matter_timestamp < '{$pack_until}';");
//    }

    if(!$update_tables['users']['dark_matter_total']) {
      upd_alter_table('users', "ADD `dark_matter_total` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Total Dark Matter amount ever gained' AFTER `dark_matter`", !$update_tables['users']['dark_matter_total']);
      upd_do_query(
        "UPDATE `{{users}}` AS u
        SET dark_matter_total =
          IF(
            dark_matter < (SELECT IF(sum(log_dark_matter_amount) IS NULL, 0, sum(log_dark_matter_amount)) FROM {{log_dark_matter}} AS dm WHERE dm.log_dark_matter_sender = u.id AND dm.log_dark_matter_amount > 0),
            (SELECT IF(sum(log_dark_matter_amount) IS NULL, 0, sum(log_dark_matter_amount)) FROM {{log_dark_matter}} AS dm WHERE dm.log_dark_matter_sender = u.id AND dm.log_dark_matter_amount > 0),
            dark_matter
          );");
    }

    upd_check_key('player_metamatter_immortal', 100000, !isset($config->player_metamatter_immortal));
    if(!$update_tables['users']['metamatter_total']) {
      upd_alter_table('users', "ADD `metamatter_total` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Total Metamatter amount ever bought'", !$update_tables['users']['metamatter_total']);

      upd_do_query(
        "UPDATE `{{users}}` AS u
        SET metamatter_total =
          IF(
            metamatter_total > (SELECT IF(sum(amount) IS NULL, 0, sum(amount)) FROM {{log_metamatter}} AS mm WHERE mm.user_id = u.id AND mm.amount > 0),
            metamatter_total,
            (SELECT IF(sum(amount) IS NULL, 0, sum(amount)) FROM {{log_metamatter}} AS mm WHERE mm.user_id = u.id AND mm.amount > 0)
          );");

      // upd_do_query('UPDATE {{users}} SET metamatter_total = (SELECT sum(payment_dark_matter_gained) FROM {{payment}} WHERE payment_user_id = id AND payment_status > 0);');
    }
    if(!isset($update_tables['users']['immortal'])) {
      upd_alter_table('users', "ADD COLUMN `immortal` TIMESTAMP NULL", !isset($update_tables['users']['immortal']));
      upd_do_query("UPDATE {{users}} SET `immortal` = NOW() WHERE `metamatter_total` > 0;");
    }
    if(isset($update_tables['player_award'])) {
      upd_do_query(
        "UPDATE {{users}} AS u JOIN {{player_award}} AS pa ON u.id = pa.player_id
          SET metamatter_total = 1, immortal = NOW()
          WHERE award_id = 2301 AND (metamatter_total = 0 OR metamatter_total IS NULL);");
    }

    upd_create_table('blitz_registrations', " (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `server_id` SMALLINT UNSIGNED DEFAULT 0,
      `round_number` SMALLINT UNSIGNED DEFAULT 0,
      `user_id` bigint(20) unsigned DEFAULT NULL,
      `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `blitz_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
      `blitz_password` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
      `blitz_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
      `blitz_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
      `blitz_place` TINYINT UNSIGNED NOT NULL DEFAULT 0,
      `blitz_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0,
      `blitz_online` INT(10) UNSIGNED NOT NULL DEFAULT 0,
      `blitz_reward_dark_matter` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`),
      KEY `I_blitz_user_id` (`user_id`) USING BTREE,
      UNIQUE KEY `I_blitz_server_round_user` (`server_id`, `round_number`, `user_id`),
      CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

//    // TODO - Убрать перед релизом
//    if(empty($update_tables['blitz_registrations']['server_id'])) {
//      upd_alter_table('blitz_registrations', array(
//        "ADD COLUMN `server_id` SMALLINT UNSIGNED DEFAULT 0 AFTER `id`",
//        "ADD COLUMN `round_number` SMALLINT UNSIGNED DEFAULT 0 AFTER `server_id`",
//        "ADD COLUMN `blitz_place` TINYINT UNSIGNED NOT NULL DEFAULT 0",
//        "ADD COLUMN `blitz_status` TINYINT UNSIGNED NOT NULL DEFAULT 0",
//        "ADD COLUMN `blitz_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0",
//        "ADD COLUMN `blitz_online` INT(11) UNSIGNED NOT NULL DEFAULT 0",
//        "ADD COLUMN `blitz_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0",
//        "ADD COLUMN `blitz_reward_dark_matter` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0",
//      ), empty($update_tables['blitz_registrations']['server_id']));
//      // upd_do_query("UPDATE {{users}} SET `immortal` = NOW() WHERE `metamatter_total` > 0;");
//    }
//    upd_create_table('blitz_registrations', " (
//      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
//      `server_id` SMALLINT UNSIGNED DEFAULT 0,
//      `round_number` SMALLINT UNSIGNED DEFAULT 0,
//      `user_id` bigint(20) unsigned DEFAULT NULL,
//      `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//      `blitz_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
//      `blitz_password` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
//      `blitz_player_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
//      `blitz_status` TINYINT UNSIGNED NOT NULL DEFAULT 0,
//      `blitz_place` TINYINT UNSIGNED NOT NULL DEFAULT 0,
//      `blitz_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0,
//      `blitz_online` INT(10) UNSIGNED NOT NULL DEFAULT 0,
//      `blitz_reward_dark_matter` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
//      PRIMARY KEY (`id`),
//      UNIQUE KEY `I_user_id` (`user_id`) USING BTREE,
//      CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
//    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

//    if(!$update_indexes['blitz_registrations']['I_blitz_server_round_user']) {
//      upd_alter_table('blitz_registrations', array(
//        "ADD KEY `I_blitz_user_id` (`user_id`) USING BTREE",
//      ), !$update_indexes['blitz_registrations']['I_blitz_server_round_user']);
//      upd_alter_table('blitz_registrations', array(
//        "DROP KEY `I_user_id`",
//        "ADD UNIQUE KEY `I_blitz_server_round_user` (`server_id`, `round_number`, `user_id`)",
//      ), !$update_indexes['blitz_registrations']['I_blitz_server_round_user']);
//    }

    if(empty($update_tables['blitz_statpoints'])) {
      upd_create_table('blitz_statpoints', " (
        `stat_date` int(11) NOT NULL DEFAULT '0',
        `id_owner` bigint(20) unsigned DEFAULT NULL,
        `id_ally` bigint(20) unsigned DEFAULT NULL,
        `stat_type` tinyint(3) unsigned DEFAULT '0',
        `stat_code` tinyint(3) unsigned NOT NULL DEFAULT '0',
        `tech_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `tech_old_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `tech_points` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `tech_count` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `build_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `build_old_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `build_points` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `build_count` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `defs_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `defs_old_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `defs_points` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `defs_count` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `fleet_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `fleet_old_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `fleet_points` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `fleet_count` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `res_rank` int(11) unsigned DEFAULT '0' COMMENT 'Rank by resources',
        `res_old_rank` int(11) unsigned DEFAULT '0' COMMENT 'Old rank by resources',
        `res_points` decimal(65,0) unsigned DEFAULT '0' COMMENT 'Resource stat points',
        `res_count` decimal(65,0) unsigned DEFAULT '0' COMMENT 'Resource count',
        `total_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `total_old_rank` int(11) unsigned NOT NULL DEFAULT '0',
        `total_points` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `total_count` decimal(65,0) unsigned NOT NULL DEFAULT '0',
        `server_id` SMALLINT UNSIGNED DEFAULT 0,
        `round_number` SMALLINT UNSIGNED DEFAULT 0
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
      /*
        KEY `TECH` (`tech_points`),
        KEY `BUILDS` (`build_points`),
        KEY `DEFS` (`defs_points`),
        KEY `FLEET` (`fleet_points`),
        KEY `TOTAL` (`total_points`),
        KEY `i_stats_owner` (`id_owner`,`stat_type`,`stat_code`,`tech_rank`,`build_rank`,`defs_rank`,`fleet_rank`,`total_rank`),
        KEY `I_stats_id_ally` (`id_ally`,`stat_type`,`stat_code`) USING BTREE,
        KEY `I_stats_type_code` (`stat_type`,`stat_code`) USING BTREE,
        CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `bl0_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_stats_id_owner` FOREIGN KEY (`id_owner`) REFERENCES `bl0_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
       */
    }

    upd_create_table('survey', " (
      `survey_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `survey_announce_id` bigint(11) unsigned DEFAULT NULL,
      `survey_question` varchar(250) NOT NULL,
      `survey_until` datetime DEFAULT NULL,
      PRIMARY KEY (`survey_id`),
      KEY `I_survey_announce_id` (`survey_announce_id`) USING BTREE,
      CONSTRAINT `FK_survey_announce_id` FOREIGN KEY (`survey_announce_id`) REFERENCES `{{announce}}` (`idAnnounce`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    upd_create_table('survey_answers', " (
      `survey_answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `survey_parent_id` int(10) unsigned DEFAULT NULL,
      `survey_answer_text` varchar(250) DEFAULT NULL,
      PRIMARY KEY (`survey_answer_id`),
      KEY `I_survey_answers_survey_parent_id` (`survey_parent_id`) USING BTREE,
      CONSTRAINT `FK_survey_answers_survey_parent_id` FOREIGN KEY (`survey_parent_id`) REFERENCES `{{survey}}` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    upd_create_table('survey_votes', " (
      `survey_vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `survey_parent_id` int(10) unsigned DEFAULT NULL,
      `survey_parent_answer_id` int(10) unsigned DEFAULT NULL,
      `survey_vote_user_id` bigint(20) unsigned DEFAULT NULL,
      `survey_vote_user_name` varchar(32) DEFAULT NULL,
      PRIMARY KEY (`survey_vote_id`),
      KEY `I_survey_votes_survey_parent_id` (`survey_parent_id`) USING BTREE,
      KEY `I_survey_votes_user` (`survey_vote_user_id`,`survey_vote_user_name`) USING BTREE,
      KEY `I_survey_votes_survey_parent_answer_id` (`survey_parent_answer_id`) USING BTREE,
      CONSTRAINT `FK_survey_votes_user` FOREIGN KEY (`survey_vote_user_id`, `survey_vote_user_name`) REFERENCES `{{users}}` (`id`, `username`) ON DELETE NO ACTION ON UPDATE NO ACTION,
      CONSTRAINT `FK_survey_votes_survey_parent_answer_id` FOREIGN KEY (`survey_parent_answer_id`) REFERENCES `{{survey_answers}}` (`survey_answer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_survey_votes_survey_parent_id` FOREIGN KEY (`survey_parent_id`) REFERENCES `{{survey}}` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

    if(empty($update_tables['security_url'])) {
      upd_create_table('security_url', " (
        `url_id` int unsigned NOT NULL AUTO_INCREMENT,
        `url_string` VARCHAR(250) NOT NULL DEFAULT '',
        PRIMARY KEY (`url_id`),
        UNIQUE KEY `I_url_string` (`url_string`)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_bin;");

      function update_security_url($query) {
        static $query_string = "INSERT IGNORE INTO {{security_url}} (`url_string`) VALUES ";

        $strings = array();
        $query = doquery($query);
        while($row = db_fetch($query)) {
          $strings[] = '("' . db_escape($row['url']) . '")';
          if(count($strings) > 100) {
            doquery($query_string . implode(',', $strings));
            $strings = array();
          }
        }
        !empty($strings) ? doquery($query_string . implode(',', $strings)) : false;
      }

      if(isset($update_tables['counter']['page'])) // TODO REMOVE
      {
        update_security_url("SELECT DISTINCT `page` as url FROM {{counter}}");
        update_security_url("SELECT DISTINCT `url` as url FROM {{counter}}");
      }
    }

    upd_alter_table('counter', "DROP COLUMN `user_name`", isset($update_tables['counter']['user_name']));
    upd_alter_table('counter', array(
      "ADD COLUMN `visit_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `counter_id`",
      "ADD COLUMN `device_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`",
      "ADD COLUMN `browser_id` bigint(20) unsigned DEFAULT NULL AFTER `device_id`",
      "ADD COLUMN `user_ip` int(10) unsigned DEFAULT NULL AFTER `browser_id`",
      "CHANGE COLUMN `proxy` `user_proxy` varchar(250) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '' AFTER `user_ip`",
      "ADD COLUMN `page_url_id` int unsigned DEFAULT NULL AFTER `user_proxy`",
      "ADD COLUMN `plain_url_id` int unsigned DEFAULT NULL AFTER `page_url_id`",
      "ADD KEY `I_counter_device_id` (`device_id`) USING BTREE",
      "ADD KEY `I_counter_browser_id` (`browser_id`)",
      "ADD KEY `I_counter_page_url_id` (`page_url_id`)",
      "ADD KEY `I_counter_plain_url_id` (`plain_url_id`)",
      "ADD CONSTRAINT `FK_counter_device_id` FOREIGN KEY (`device_id`) REFERENCES `{{security_device}}` (`device_id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_counter_browser_id` FOREIGN KEY (`browser_id`) REFERENCES `{{security_browser}}` (`browser_id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_counter_page_url_id` FOREIGN KEY (`page_url_id`) REFERENCES `{{security_url}}` (`url_id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_counter_plain_url_id` FOREIGN KEY (`plain_url_id`) REFERENCES `{{security_url}}` (`url_id`) ON DELETE CASCADE ON UPDATE CASCADE",
    ), !isset($update_tables['counter']['device_id']));
    if(isset($update_tables['counter']['ip'])) {
      // upd_do_query('UPDATE `{{counter}}` SET `user_ip` = INET_ATON(`ip`), `user_proxy` = `proxy`, `visit_time` = FROM_UNIXTIME(`time`)');
      upd_do_query('UPDATE `{{counter}}` SET `user_ip` = INET_ATON(`ip`), `visit_time` = FROM_UNIXTIME(`time`)');
      upd_do_query('UPDATE `{{counter}}` AS c JOIN {{security_url}} AS u ON u.url_string = c.page SET c.page_url_id = u.url_id');
      upd_do_query('UPDATE `{{counter}}` AS c JOIN {{security_url}} AS u ON u.url_string = c.url SET c.plain_url_id = u.url_id');

      upd_alter_table('counter', array(
        //"DROP COLUMN `proxy`",
        "DROP COLUMN `ip`",
        "DROP COLUMN `time`",
        "DROP COLUMN `page`",
        "DROP COLUMN `url`",
      ), isset($update_tables['counter']['ip']));
    }

    upd_alter_table('users', array(
      "ADD COLUMN `salt` char(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' AFTER `password`",
    ), !isset($update_tables['users']['salt']));
    // TODO Смерджить после выливания на живой
    upd_alter_table('users', array(
      "ADD COLUMN `parent_account_id` bigint(20) unsigned NOT NULL DEFAULT 0",
      "ADD COLUMN `parent_account_global` tinyint(1) unsigned NOT NULL DEFAULT 0",
      "ADD KEY `I_user_account_id` (`parent_account_id`, `parent_account_global`)",
    ), !isset($update_tables['users']['parent_account_id']));
    // TODO Смерджить после выливания на живой
    upd_alter_table('users', array(
      "ADD COLUMN `server_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT ''",
    ), !isset($update_tables['users']['server_name']));
//    upd_alter_table('users', array(
//      "ADD COLUMN `parent_account_id` bigint(20) unsigned NOT NULL DEFAULT 0",
//      "ADD KEY `I_user_account_id` (`parent_account_id`)",
//    ), !isset($update_tables['users']['parent_account_id']));
//    upd_alter_table('users', array(
//      "ADD COLUMN `parent_account_global` tinyint(1) unsigned NOT NULL DEFAULT 0",
//      "DROP KEY `I_user_account_id`",
//      "ADD KEY `I_user_account_id` (`parent_account_id`, `parent_account_global`)",
//    ), !isset($update_tables['users']['parent_account_id']));


    upd_alter_table('security_browser', array(
      "ADD COLUMN `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    ), !isset($update_tables['security_browser']['timestamp']));
    upd_alter_table('security_device', array(
      "ADD COLUMN `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    ), !isset($update_tables['security_device']['timestamp']));

    // sn_version_compare($ver1, $ver2)

//    // TODO remove after mine
//    upd_alter_table('counter', array(
//      "MODIFY COLUMN `user_ip` int(10) unsigned NULL DEFAULT NULL AFTER `browser_id`",
//    ), strtolower($update_tables['counter']['user_ip']['Null']) == 'no');
//
//    upd_alter_table('security_player_entry', array(
//      "MODIFY COLUMN `user_ip` int(10) unsigned NULL DEFAULT NULL",
//    ), strtolower($update_tables['security_player_entry']['user_ip']['Null']) == 'no');
//    pdump(strtolower($update_tables['security_player_entry']['user_ip']['Null']) == 'no');


    upd_check_key('game_multiaccount_enabled', 0, !isset($config->game_multiaccount_enabled));
    upd_check_key('stats_schedule', '04:00:00', $config->stats_schedule !== '04:00:00');
    upd_check_key('stats_php_memory', '1024M', !isset($config->stats_php_memory));
    upd_check_key('stats_minimal_interval', '600', !isset($config->stats_minimal_interval));

    upd_check_key('fleet_update_interval', 4, !intval($config->fleet_update_interval));
    upd_check_key('fleet_update_last', SN_TIME_SQL, true);
    upd_check_key('fleet_update_lock', '', empty($config->fleet_update_interval));

    upd_check_key('uni_galaxy_distance', 20000, empty($config->uni_galaxy_distance));

    upd_check_key('stats_history_days', 14, !$config->stats_history_days);

    if($config->payment_currency_default != 'USD') {
      upd_check_key('payment_currency_default',      'USD', true);
      upd_check_key('payment_currency_exchange_dm_', 20000, true);
      upd_check_key('payment_currency_exchange_mm_', 20000, true);
      upd_check_key('payment_currency_exchange_usd', 1, true);
    }
    upd_check_key('payment_currency_exchange_wmz', 1.00, true);

    upd_check_key('payment_currency_exchange_eur', 0.90, true);
    upd_check_key('payment_currency_exchange_wme', 0.90, true);

    upd_check_key('payment_currency_exchange_wmb', 18000, !$config->payment_currency_exchange_wmb);

    upd_check_key('payment_currency_exchange_uah', 30, true);
    upd_check_key('payment_currency_exchange_wmu', 30, true);

    upd_check_key('payment_currency_exchange_rub', 60, true);
    upd_check_key('payment_currency_exchange_wmr', 60, true);

    upd_do_query('COMMIT;', true);
    $new_version = 39;

  case 39:
    upd_log_version_update();

    upd_do_query('COMMIT;', true);
    // $new_version = 40;
}
upd_log_message('Upgrade complete.');

upd_do_query('SET FOREIGN_KEY_CHECKS=1;');

if($new_version) {
  $config->db_saveItem('db_version', $new_version);
  upd_log_message("<font color=green>DB version is now {$new_version}</font>");
} else {
  upd_log_message("DB version didn't changed from {$config->db_version}");
}

$config->db_loadAll();
/*
if($user['authlevel'] >= 3) {
  print(str_replace("\r\n", '<br>', $upd_log));
}
*/
unset($sn_cache->tables);
sys_refresh_tablelist($config->db_prefix);

upd_log_message('Restoring server status');
$config->db_saveItem('game_disable', $old_server_status);
