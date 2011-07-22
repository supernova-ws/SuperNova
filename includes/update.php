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

$debug_value = $config->debug;
$config->debug = 0;

$config->db_loadItem('db_version');
if($config->db_version == DB_VERSION)
{
}
elseif($config->db_version > DB_VERSION)
{
  $GLOBALS['config']->db_saveItem('var_db_update_end', $GLOBALS['time_now']);
  die('Internal error! Auotupdater detects DB version greater then can be handled!<br>Possible you have out-of-date SuperNova version<br>Pleas upgrade your server from <a href="http://github.com/supernova-ws/SuperNova">GIT repository</a>.');
}

if($config->db_version < 26)
{
  $GLOBALS['sys_log_disabled'] = true;
}

$upd_log = '';
$new_version = floatval($config->db_version);
upd_check_key('upd_lock_time', 60, !isset($config->upd_lock_time));

upd_log_message('Update starting. Loading table info...');
$update_tables  = array();
$update_indexes = array();
$query = upd_do_query('SHOW TABLES;');
while($row = mysql_fetch_row($query))
{
  upd_load_table_info($row[0]);
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

switch($new_version)
{
  case 0:
  case 1:
  case 2:
  case 3:
  case 4:
  case 5:
  case 6:
  case 7:
  case 8:
  case 9:
    upd_log_version_update();

    upd_alter_table('planets', "ADD `debris_metal` bigint(11) unsigned DEFAULT '0'", !$update_tables['planets']['debris_metal']);
    upd_alter_table('planets', "ADD `debris_crystal` bigint(11) unsigned DEFAULT '0'", !$update_tables['planets']['debris_crystal']);

    upd_alter_table('planets', array("ADD `parent_planet` bigint(11) unsigned DEFAULT '0'","ADD KEY `i_parent_planet` (`parent_planet`)"), !$update_tables['planets']['parent_planet']);
    upd_do_query(
      "UPDATE `{{planets}}` AS lu
        LEFT JOIN `{{planets}}` AS pl
          ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
      SET lu.parent_planet=pl.id WHERE lu.planet_type=3;"
    );
    upd_drop_table('lunas');

    if($update_tables['galaxy'])
    {
      upd_do_query(
        'UPDATE `{{planets}}`
          LEFT JOIN `{{galaxy}}` ON {{galaxy}}.id_planet = {{planets}}.id
        SET
          {{planets}}.debris_metal = {{galaxy}}.metal,
          {{planets}}.debris_crystal = {{galaxy}}.crystal
        WHERE {{galaxy}}.metal>0 OR {{galaxy}}.crystal>0;'
      );
    }
    upd_drop_table('galaxy');

    upd_create_table('counter',
      "(
        `id` bigint(11) NOT NULL AUTO_INCREMENT,
        `time` int(11) NOT NULL DEFAULT '0',
        `page` varchar(255) CHARACTER SET utf8 DEFAULT '0',
        `user_id` bigint(11) DEFAULT '0',
        `ip` varchar(15) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `i_user_id` (`user_id`),
        KEY `i_ip` (`ip`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    );
    upd_alter_table('counter', "ADD `url` varchar(255) CHARACTER SET utf8 DEFAULT ''", !$update_tables['counter']['url']);

    upd_alter_table('fleets', array(
      "ADD KEY `fleet_mess` (`fleet_mess`)",
      "ADD KEY `fleet_group` (`fleet_group`)"
    ), !$update_indexes['fleets']['fleet_mess']);

    upd_alter_table('referrals', "ADD `dark_matter` bigint(11) NOT NULL DEFAULT '0' COMMENT 'How much player have aquired Dark Matter'", !$update_tables['referrals']['dark_matter']);
    upd_alter_table('referrals', "ADD KEY `id_partner` (`id_partner`)", !$update_indexes['referrals']['id_partner']);

    upd_check_key('rpg_bonus_divisor', 10);
    upd_check_key('rpg_officer', 3);

    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('BannerURL', 'banner_source_post', 'BannerOverviewFrame',
      'close_reason', 'dbVersion', 'ForumUserBarFrame', 'OverviewBanner', 'OverviewClickBanner', 'OverviewExternChat',
      'OverviewExternChatCmd', 'OverviewNewsText', 'UserbarURL', 'userbar_source');");

    $dm_change_legit = true;

    upd_do_query(
      "UPDATE {{referrals}} AS r
        LEFT JOIN {{users}} AS u
          ON u.id = r.id
      SET r.dark_matter = u.lvl_minier + u.lvl_raid;"
    );
    upd_add_more_time();

    if($update_tables['users']['rpg_points'])
    {
      upd_do_query(
        "UPDATE {{users}} AS u
          RIGHT JOIN {{referrals}} AS r
            ON r.id_partner = u.id AND r.dark_matter >= {$config->rpg_bonus_divisor}
        SET u.rpg_points = u.rpg_points + FLOOR(r.dark_matter/{$config->rpg_bonus_divisor});"
      );
    }

    $dm_change_legit = false;
  upd_do_query('COMMIT;', true);
  $new_version = 10;

  case 10:
  case 11:
  case 12:
  case 13:
  case 14:
  case 15:
  case 16:
  case 17:
  case 18:
  case 19:
  case 20:
  case 21:
    upd_log_version_update();

    upd_create_table('alliance_requests',
      "(
        `id_user` int(11) NOT NULL,
        `id_ally` int(11) NOT NULL DEFAULT '0',
        `request_text` text,
        `request_time` int(11) NOT NULL DEFAULT '0',
        `request_denied` tinyint(1) unsigned NOT NULL DEFAULT '0',
        PRIMARY KEY (`id_user`,`id_ally`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
    );

    upd_alter_table('announce', "ADD `detail_url` varchar(250) NOT NULL DEFAULT '' COMMENT 'Link to more details about update'", !$update_tables['announce']['detail_url']);

    upd_alter_table('counter', array("MODIFY `ip` VARCHAR(250) COMMENT 'User last IP'", "ADD `proxy` VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)'"), !$update_tables['counter']['proxy']);

    upd_alter_table('statpoints', array(
      "ADD `res_rank` INT(11) DEFAULT 0 COMMENT 'Rank by resources'",
      "ADD `res_old_rank` INT(11) DEFAULT 0 COMMENT 'Old rank by resources'",
      "ADD `res_points` BIGINT(20) DEFAULT 0 COMMENT 'Resource stat points'",
      "ADD `res_count` BIGINT(20) DEFAULT 0 COMMENT 'Old rank by resources'"
    ), !$update_tables['statpoints']['res_rank']);

    upd_alter_table('planets', "ADD `supercargo` bigint(11) NOT NULL DEFAULT '0' COMMENT 'Supercargo ship count'", !$update_tables['planets']['supercargo']);

    upd_alter_table('users', "DROP COLUMN `current_luna`", $update_tables['users']['current_luna']);
    upd_alter_table('users', array("DROP COLUMN `aktywnosc`", "DROP COLUMN `time_aktyw`", "DROP COLUMN `kiler`",
      "DROP COLUMN `kod_aktywujacy`", "DROP COLUMN `ataker`", "DROP COLUMN `atakin`"), $update_tables['users']['ataker']);
    upd_alter_table('users', "ADD `options` TEXT COMMENT 'Packed user options'", !$update_tables['users']['options']);
    upd_alter_table('users', "ADD `news_lastread` int(11) NOT NULL DEFAULT '0' COMMENT 'News last read tag'", !$update_tables['users']['news_lastread']);
    upd_alter_table('users', array("MODIFY `user_lastip` VARCHAR(250) COMMENT 'User last IP'", "ADD `user_proxy` VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)'"), !$update_tables['users']['user_proxy']);

    upd_drop_table('update');

    upd_check_key('fleet_speed', $config->fleet_speed/2500, $config->fleet_speed >= 2500);
    upd_check_key('game_counter', 0);
    upd_check_key('game_default_language', 'ru');
    upd_check_key('game_default_skin', 'skins/EpicBlue/');
    upd_check_key('game_default_template', 'OpenGame');
    upd_check_key('game_news_overview', 3);
    upd_check_key('game_news_actual', 259200);
    upd_check_key('game_noob_factor', 5, !isset($config->game_noob_factor));
    upd_check_key('game_noob_points', 5000, !isset($config->game_noob_points));
    upd_check_key('game_speed', $config->game_speed/2500, $config->game_speed >= 2500);
    upd_check_key('int_format_date', 'd.m.Y');
    upd_check_key('int_format_time', 'H:i:s', true);
    upd_check_key('int_banner_background', 'design/images/banner.png', true);
    upd_check_key('int_userbar_background', 'design/images/userbar.png', true);
    upd_check_key('player_max_colonies', $config->player_max_planets ? ($config->player_max_planets - 1) : 9);
    upd_check_key('url_forum', $config->forum_url, !isset($config->url_forum));
    upd_check_key('url_rules', $config->rules_url, !isset($config->url_rules));
    upd_check_key('url_dark_matter', '', !isset($config->url_dark_matter));

    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN (
      'game_date_withTime', 'player_max_planets', 'OverviewNewsFrame', 'forum_url', 'rules_url'
    );");

  upd_do_query('COMMIT;', true);
  $new_version = 22;

  case 22:
    upd_log_version_update();

    upd_alter_table('planets', "ADD `governor` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Planet governor'", !$update_tables['planets']['governor']);
    upd_alter_table('planets', "ADD `governor_level` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Governor level'", !$update_tables['planets']['governor_level']);
    upd_alter_table('planets', "ADD `que` varchar(4096) NOT NULL DEFAULT '' COMMENT 'Planet que'", !$update_tables['planets']['que']);

    if($update_tables['planets']['b_building'])
    {
      $planet_query = upd_do_query('SELECT * FROM {{planets}} WHERE `b_building` <> 0;');
      $const_que_structures = QUE_STRUCTURES;
      while($planet_data = mysql_fetch_assoc($planet_query))
      {
        $old_que = explode(';', $planet_data['b_building_id']);
        foreach($old_que as $old_que_item_string)
        {
          if(!$old_que_item_string)
          {
            continue;
          }

          $old_que_item = explode(',', $old_que_item_string);
          if($old_que_item[4] == 'build')
          {
            $old_que_item[4] = BUILD_CREATE;
          }
          else
          {
            $old_que_item[4] = BUILD_DESTROY;
          }

          $old_que_item[3] = $old_que_item[3] > $planet_data['last_update'] ? $old_que_item[3] - $planet_data['last_update'] : 1;
          $planet_data['que'] = "{$old_que_item[0]},1,{$old_que_item[3]},{$old_que_item[4]},{$const_que_structures};{$planet_data['que']}";
        }
        upd_do_query("UPDATE {{planets}} SET `que` = '{$planet_data['que']}', `b_building` = '0', `b_building_id` = '0' WHERE `id` = '{$planet_data['id']}' LIMIT 1;", true);
      }
    }

    upd_create_table('mercenaries',
      "(
        `id` bigint(11) NOT NULL AUTO_INCREMENT,
        `id_user` bigint(11) NOT NULL,
        `mercenary` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
        `time_start` int(11) NOT NULL DEFAULT '0',
        `time_finish` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        KEY `i_user_mercenary_time` (`id_user`, `mercenary`, `time_start`, `time_finish`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    );
  upd_do_query('COMMIT;', true);
  $new_version = 23;

  case 23:
  case 24:
    upd_log_version_update();

    upd_create_table('confirmations',
      "(
        `id` bigint(11) NOT NULL AUTO_INCREMENT,
        `id_user` bigint(11) NOT NULL DEFAULT 0,
        `type` SMALLINT NOT NULL DEFAULT 0,
        `code` NVARCHAR(16) NOT NULL DEFAULT '',
        `email` NVARCHAR(64) NOT NULL DEFAULT '',
        `create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `i_code_email` (`code`, `email`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    );

    if($update_tables['users']['urlaubs_until'])
    {
      upd_alter_table('users', "ADD `vacation` int(11) NOT NULL DEFAULT '0' COMMENT 'Time when user can leave vacation mode'", !$update_tables['users']['vacation']);
      upd_do_query('UPDATE {{users}} SET `vacation` = `urlaubs_until` WHERE `urlaubs_modus` <> 0;');
      upd_alter_table('users', 'DROP COLUMN `urlaubs_until`, DROP COLUMN `urlaubs_modus`, DROP COLUMN `urlaubs_modus_time`', $update_tables['users']['urlaubs_until']);
    }

    upd_check_key('user_vacation_disable', $config->urlaubs_modus_erz, !isset($config->user_vacation_disable));
    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('urlaubs_modus_erz');");

  upd_do_query('COMMIT;', true);
  $new_version = 25;

  case 25:
    upd_log_version_update();

    upd_alter_table('rw', array(
      "DROP COLUMN `a_zestrzelona`",
      "DROP INDEX `rid`",
      "ADD COLUMN `report_id` bigint(11) NOT NULL AUTO_INCREMENT FIRST",
      "ADD PRIMARY KEY (`report_id`)",
      "ADD INDEX `i_rid` (`rid`)"
    ), !$update_tables['rw']['report_id']);

    upd_add_more_time();
    upd_create_table('logs_backup', "AS (SELECT * FROM {$config->db_prefix}logs);");

    upd_alter_table('logs', array(
      "MODIFY COLUMN `log_id` INT(1)",
      "DROP PRIMARY KEY"
    ), !$update_tables['logs']['log_timestamp']);

/*
    mysql_query('commit');
    upd_do_query('COMMIT;', true);
debug($update_tables['logs'], 2);

    upd_do_query('START TRANSACTION;', true);
    upd_alter_table('logs', array(
      "DROP COLUMN `log_id`"
    ), !$update_tables['logs']['log_timestamp']);
    upd_do_query('COMMIT;', true);
debug($update_tables['logs'], 3);
die();
debug($update_tables['logs']['log_id'], 31);
    upd_do_query('START TRANSACTION;', true);
*/

    upd_alter_table('logs', array(
      "DROP COLUMN `log_id`",
      "ADD COLUMN `log_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp' FIRST",
      "ADD COLUMN `log_username` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'Username' AFTER `log_timestamp`",
      "MODIFY COLUMN `log_title` VARCHAR(64) NOT NULL DEFAULT 'Log entry' COMMENT 'Short description' AFTER `log_username`",
      "MODIFY COLUMN `log_page` VARCHAR(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log' AFTER `log_text`",
      "CHANGE COLUMN `log_type` `log_code` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `log_page`",
      "MODIFY COLUMN `log_sender` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID which make log record' AFTER `log_code`",
      "MODIFY COLUMN `log_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Machine-readable timestamp' AFTER `log_sender`",
      "ADD COLUMN `log_dump` TEXT NOT NULL DEFAULT '' COMMENT 'Machine-readable dump of variables' AFTER `log_time`",
      "ADD INDEX `i_log_username` (`log_username`)",
      "ADD INDEX `i_log_time` (`log_time`)",
      "ADD INDEX `i_log_sender` (`log_sender`)",
      "ADD INDEX `i_log_code` (`log_code`)",
      "ADD INDEX `i_log_page` (`log_page`)",
      "CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci"
    ), !$update_tables['logs']['log_timestamp']);
    upd_do_query('DELETE FROM `{{logs}}` WHERE `log_code` = 303;');

    if($update_tables['errors'])
    {
      upd_do_query('INSERT INTO `{{logs}}` (`log_code`, `log_sender`, `log_title`, `log_text`, `log_page`, `log_time`) SELECT 500, `error_sender`, `error_type`, `error_text`, `error_page`, `error_time` FROM `{{errors}}`;');
      if($update_tables['errors_backup'])
      {
        upd_drop_table('errors_backup');
      }
      mysql_query("ALTER TABLE {$config->db_prefix}errors RENAME TO {$config->db_prefix}errors_backup;");
      upd_drop_table('errors');
    }

    upd_alter_table('logs', 'ORDER BY log_time');

    upd_alter_table('logs', array("ADD COLUMN `log_id` SERIAL", "ADD PRIMARY KEY (`log_id`)"), !$update_tables['logs']['log_id']);

    upd_do_query('UPDATE `{{logs}}` SET `log_timestamp` = FROM_UNIXTIME(`log_time`);');
    upd_do_query('UPDATE `{{logs}}` AS l LEFT JOIN `{{users}}` AS u ON u.id = l.log_sender SET l.log_username = u.username WHERE l.log_username IS NOT NULL;');

    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 190 WHERE `log_code` = 100 AND `log_title` = 'Stat update';");
    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 191 WHERE `log_code` = 101 AND `log_title` = 'Stat update';");
    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 192 WHERE `log_code` = 102 AND `log_title` = 'Stat update';");
    $GLOBALS['sys_log_disabled'] = false;

  upd_do_query('COMMIT;', true);
  $new_version = 26;

  case 26:
    upd_log_version_update();

    $GLOBALS['sys_log_disabled'] = false;

    upd_alter_table('planets', "ADD INDEX `i_parent_planet` (`parent_planet`)", !$update_indexes['planets']['i_parent_planet']);
    upd_alter_table('messages', "DROP INDEX `owner`", $update_indexes['messages']['owner']);
    upd_alter_table('messages', "DROP INDEX `owner_type`", $update_indexes['messages']['owner_type']);
    upd_alter_table('messages', "DROP INDEX `sender_type`", $update_indexes['messages']['sender_type']);

    upd_alter_table('messages', array(
      "ADD INDEX `i_owner_time` (`message_owner`, `message_time`)",
      "ADD INDEX `i_sender_time` (`message_sender`, `message_time`)",
      "ADD INDEX `i_time` (`message_time`)"
    ), !$update_indexes['messages']['i_owner_time']);

    upd_drop_table('fleet_log');

    upd_do_query("UPDATE `{{planets}}` SET `metal` = 0 WHERE `metal` < 0;");
    upd_do_query("UPDATE `{{planets}}` SET `crystal` = 0 WHERE `crystal` < 0;");
    upd_do_query("UPDATE `{{planets}}` SET `deuterium` = 0 WHERE `deuterium` < 0;");
    upd_alter_table('planets', array(
       "DROP COLUMN `b_building`",
       "DROP COLUMN `b_building_id`"
    ), $update_tables['planets']['b_building']);

    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('noobprotection', 'noobprotectionmulti', 'noobprotectiontime', 'chat_admin_msgFormat');");

    upd_do_query("DELETE FROM `{{logs}}` WHERE `log_code` = 501;");
    upd_do_query("DELETE FROM `{{logs}}` WHERE `log_title` IN ('Canceling Hangar Que', 'Building Planet Defense');");

    upd_check_key('chat_admin_highlight', '<font color=purple>$1</font>', !isset($config->chat_admin_highlight));

    upd_check_key('int_banner_URL', 'banner.php?type=banner', $config->int_banner_URL == '/banner.php?type=banner');
    upd_check_key('int_userbar_URL', 'banner.php?type=userbar', $config->int_userbar_URL == '/banner.php?type=userbar');

    upd_do_query('DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});');

    upd_alter_table('users', 'CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

    if(!$update_tables['shortcut'])
    {
      upd_create_table('shortcut',
        "(
          `shortcut_id` SERIAL,
          `shortcut_user_id` BIGINT(11) UNSIGNED NOT NULL DEFAULT 0,
          `shortcut_planet_id` bigint(11) NOT NULL DEFAULT 0,
          `shortcut_galaxy` int(3) NOT NULL DEFAULT 0,
          `shortcut_system` int(3) NOT NULL DEFAULT 0,
          `shortcut_planet` int(3) NOT NULL DEFAULT 0,
          `shortcut_planet_type` tinyint(1) NOT NULL DEFAULT 1,
          `shortcut_text` NVARCHAR(64) NOT NULL DEFAULT '',

          PRIMARY KEY (`shortcut_id`),
          KEY `i_shortcut_user_id` (`shortcut_user_id`),
          KEY `i_shortcut_planet_id` (`shortcut_planet_id`),

          CONSTRAINT `FK_shortcut_user_id` FOREIGN KEY (`shortcut_user_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      $temp_planet_types = array(PT_PLANET, PT_DEBRIS, PT_MOON);

      $query = upd_do_query("SELECT id, fleet_shortcut FROM {{users}} WHERE fleet_shortcut > '';");
      while($user_data = mysql_fetch_assoc($query))
      {
        $shortcuts = explode("\r\n", $user_data['fleet_shortcut']);
        foreach($shortcuts as $shortcut)
        {
          if(!$shortcut)
          {
            continue;
          }

          $shortcut = explode(',', $shortcut);
          $shortcut[0] = mysql_real_escape_string($shortcut[0]);
          $shortcut[1] = intval($shortcut[1]);
          $shortcut[2] = intval($shortcut[2]);
          $shortcut[3] = intval($shortcut[3]);
          $shortcut[4] = intval($shortcut[4]);

          if($shortcut[0] && $shortcut[1] && $shortcut[2] && $shortcut[3] && in_array($shortcut[4], $temp_planet_types))
          {
            upd_do_query("INSERT INTO {$config->db_prefix}shortcut (shortcut_user_id, shortcut_galaxy, shortcut_system, shortcut_planet, shortcut_planet_type, shortcut_text) VALUES ({$user_data['id']}, {$shortcut[1]}, {$shortcut[2]}, {$shortcut[3]}, {$shortcut[4]}, '{$shortcut[0]}');", true);
          }
        }
      }

      upd_alter_table('users', 'DROP COLUMN `fleet_shortcut`');
    };

    upd_check_key('url_faq', '', !isset($config->url_faq));

    upd_do_query('COMMIT;', true);
    $new_version = 27;

  case 27:
    upd_log_version_update();

    upd_check_key('chat_highlight_moderator', '<font color=green>$1</font>', !isset($config->chat_highlight_moderator));
    upd_check_key('chat_highlight_operator', '<font color=red>$1</font>', !isset($config->chat_highlight_operator));
    upd_check_key('chat_highlight_admin', $config->chat_admin_highlight, !isset($config->chat_highlight_admin));

    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('chat_admin_highlight');");

    upd_alter_table('banned', array(
      "CHANGE COLUMN id ban_id bigint(20) unsigned NOT NULL AUTO_INCREMENT",
      "CHANGE COLUMN `who` `ban_user_name` VARCHAR(64) NOT NULL DEFAULT ''",
      "CHANGE COLUMN `theme` `ban_reason` VARCHAR(128) NOT NULL DEFAULT ''",
      "CHANGE COLUMN `time` `ban_time` int(11) NOT NULL DEFAULT 0",
      "CHANGE COLUMN `longer` `ban_until` int(11) NOT NULL DEFAULT 0",
      "CHANGE COLUMN `author` `ban_issuer_name` VARCHAR(64) NOT NULL DEFAULT ''",
      "CHANGE COLUMN `email` `ban_issuer_email` VARCHAR(64) NOT NULL DEFAULT ''",
      "DROP COLUMN who2",
      "ADD PRIMARY KEY (`ban_id`)"/*,
      "RENAME TO {$config->db_prefix}ban"*/
    ), !$update_tables['banned']['ban_id']);

    upd_alter_table('alliance', array(
      "MODIFY COLUMN `id` SERIAL",
      "ADD CONSTRAINT UNIQUE KEY `i_ally_name` (`ally_name`)",
      "CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci",
      "ENGINE=InnoDB"
    ), !$update_indexes['alliance']['i_ally_name']);

    $upd_relation_types = "'neutral', 'war', 'peace', 'confederation', 'federation', 'union', 'master', 'slave'";
    upd_create_table('alliance_diplomacy',
      "(
        `alliance_diplomacy_id` SERIAL,
        `alliance_diplomacy_ally_id` bigint(11) UNSIGNED DEFAULT NULL,
        `alliance_diplomacy_contr_ally_id` bigint(11) UNSIGNED DEFAULT NULL,
        `alliance_diplomacy_contr_ally_name` varchar(32) DEFAULT '',
        `alliance_diplomacy_relation` SET({$upd_relation_types}) NOT NULL default 'neutral',
        `alliance_diplomacy_relation_last` SET({$upd_relation_types}) NOT NULL default 'neutral',
        `alliance_diplomacy_time` INT(11) NOT NULL DEFAULT 0,

        PRIMARY KEY (`alliance_diplomacy_id`),
        KEY (`alliance_diplomacy_ally_id`, `alliance_diplomacy_contr_ally_id`, `alliance_diplomacy_time`),
        KEY (`alliance_diplomacy_ally_id`, `alliance_diplomacy_time`),

        CONSTRAINT  `FK_diplomacy_ally_id`         FOREIGN KEY (`alliance_diplomacy_ally_id`)         REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_diplomacy_contr_ally_id`   FOREIGN KEY (`alliance_diplomacy_contr_ally_id`)   REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_diplomacy_contr_ally_name` FOREIGN KEY (`alliance_diplomacy_contr_ally_name`) REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_create_table('alliance_negotiation',
      "(
        `alliance_negotiation_id` SERIAL,
        `alliance_negotiation_ally_id` bigint(11) UNSIGNED DEFAULT NULL,
        `alliance_negotiation_ally_name` varchar(32) DEFAULT '',
        `alliance_negotiation_contr_ally_id` bigint(11) UNSIGNED DEFAULT NULL,
        `alliance_negotiation_contr_ally_name` varchar(32) DEFAULT '',
        `alliance_negotiation_relation` SET({$upd_relation_types}) NOT NULL default 'neutral',
        `alliance_negotiation_time` INT(11) NOT NULL DEFAULT 0,
        `alliance_negotiation_propose` TEXT,
        `alliance_negotiation_response` TEXT,
        `alliance_negotiation_status` SMALLINT NOT NULL DEFAULT 0,

        PRIMARY KEY (`alliance_negotiation_id`),
        KEY (`alliance_negotiation_ally_id`, `alliance_negotiation_contr_ally_id`, `alliance_negotiation_time`),
        KEY (`alliance_negotiation_ally_id`, `alliance_negotiation_time`),

        CONSTRAINT  `FK_negotiation_ally_id`         FOREIGN KEY (`alliance_negotiation_ally_id`)         REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_ally_name`       FOREIGN KEY (`alliance_negotiation_ally_name`)       REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_contr_ally_id`   FOREIGN KEY (`alliance_negotiation_contr_ally_id`)   REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_contr_ally_name` FOREIGN KEY (`alliance_negotiation_contr_ally_name`) REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_alter_table('users', array("MODIFY COLUMN `id` SERIAL", "CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci"), true);
    upd_alter_table('planets', array("MODIFY COLUMN `id` SERIAL", "CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci"), true);

    upd_create_table('bashing',
      "(
        `bashing_id` SERIAL,
        `bashing_user_id` bigint(11) UNSIGNED DEFAULT NULL,
        `bashing_planet_id` bigint(11) UNSIGNED DEFAULT NULL,
        `bashing_time` INT(11) NOT NULL DEFAULT 0,

        PRIMARY KEY (`bashing_id`),
        KEY (`bashing_user_id`, `bashing_planet_id`, `bashing_time`),
        KEY (`bashing_planet_id`),
        KEY (`bashing_time`),

        CONSTRAINT  `FK_bashing_user_id`   FOREIGN KEY (`bashing_user_id`)   REFERENCES `{$config->db_prefix}users`   (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `{$config->db_prefix}planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_check_key('fleet_bashing_war_delay', 12 * 60 * 60, !isset($config->fleet_bashing_war_delay));
    upd_check_key('fleet_bashing_scope', 24 * 60 * 60, !isset($config->fleet_bashing_scope));
    upd_check_key('fleet_bashing_interval', 30 * 60, !isset($config->fleet_bashing_interval));
    upd_check_key('fleet_bashing_waves', 3, !isset($config->fleet_bashing_waves));
    upd_check_key('fleet_bashing_attacks', 3, !isset($config->fleet_bashing_attacks));

  upd_do_query('COMMIT;', true);
  $new_version = 28;

  case 28:
  case 28.1:
    upd_log_version_update();

    upd_create_table('quest',
      "(
        `quest_id` SERIAL,
        `quest_name` VARCHAR(255) DEFAULT NULL,
        `quest_description` TEXT,
        `quest_conditions` TEXT,
        `quest_rewards` TEXT,
        `quest_type` TINYINT DEFAULT NULL,
        `quest_order` INT NOT NULL DEFAULT 0,

        PRIMARY KEY (`quest_id`)
        ,KEY (`quest_type`, `quest_order`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_create_table('quest_status',
      "(
        `quest_status_id` SERIAL,
        `quest_status_quest_id` bigint(20) UNSIGNED DEFAULT NULL,
        `quest_status_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT '0',
        `quest_status_progress` VARCHAR(255) NOT NULL DEFAULT '',
        `quest_status_status` TINYINT NOT NULL DEFAULT 1,

        PRIMARY KEY (`quest_status_id`)
        ,KEY (`quest_status_user_id`, `quest_status_quest_id`, `quest_status_status`)
        ,CONSTRAINT `FK_quest_status_quest_id` FOREIGN KEY (`quest_status_quest_id`) REFERENCES `{$config->db_prefix}quest` (`quest_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_quest_status_user_id`  FOREIGN KEY (`quest_status_user_id`)  REFERENCES `{$config->db_prefix}users` (`id`)       ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_check_key('quest_total', 0, !isset($config->quest_total));

    for($i = 0; $i < 25; $i++)
    {
      upd_alter_table('alliance', array("DROP INDEX `id_{$i}`",), $update_indexes['alliance']["id_{$i}"]);
      upd_alter_table('users', array("DROP INDEX `id_{$i}`",), $update_indexes['users']["id_{$i}"]);
    }

    upd_alter_table('alliance', array('DROP INDEX `id`',), $update_indexes['alliance']['id']);
    upd_alter_table('alliance', array('DROP INDEX `ally_name`',), $update_indexes['alliance']['ally_name']);
    upd_alter_table('alliance', array('ADD UNIQUE INDEX `i_ally_tag` (`ally_tag`)',), !$update_indexes['alliance']['i_ally_tag']);
    upd_alter_table('alliance', array('MODIFY COLUMN `ranklist` TEXT',), true);

    upd_alter_table('users', array('DROP INDEX `id`',), $update_indexes['users']['id']);
    upd_alter_table('users', "CHANGE COLUMN `rpg_points` `dark_matter` int(11) DEFAULT 0", $update_tables['users']['rpg_points']);

    upd_alter_table('users', array(
      'DROP COLUMN `ally_request`',
      'DROP COLUMN `ally_request_text`',
    ), $update_tables['users']['ally_request_text']);

    upd_alter_table('users', array(
      'ADD INDEX `i_ally_id` (`ally_id`)',
      'ADD INDEX `i_ally_name` (`ally_name`)',
    ), !$update_indexes['users']['i_ally_id']);

    upd_alter_table('users', array(
      "ADD `msg_admin` bigint(11) unsigned DEFAULT '0' AFTER mnl_buildlist"
    ), !$update_tables['users']['msg_admin']);

    if(!$update_foreigns['users']['FK_users_ally_id'])
    {
      upd_alter_table('users', array(
        'MODIFY COLUMN `ally_name` VARCHAR(32) DEFAULT NULL',
        'MODIFY COLUMN `ally_id` BIGINT(20) UNSIGNED DEFAULT NULL',
      ), strtoupper($update_tables['users']['ally_id']['Type']) != 'BIGINT(20) UNSIGNED');

      upd_do_query('DELETE FROM {{alliance}} WHERE id not in (select ally_id from {{users}} group by ally_id);');
      upd_do_query("UPDATE {{users}} SET `ally_id` = null, ally_name = null, ally_register_time = 0, ally_rank_id = 0 WHERE `ally_id` NOT IN (SELECT id FROM {{alliance}});");
      upd_do_query("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON u.ally_id = a.id SET u.ally_name = a.ally_name WHERE u.ally_id IS NOT NULL;");

      upd_alter_table('users', array(
         "ADD CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
         "ADD CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_foreigns['users']['FK_users_ally_id']);
    }

    upd_alter_table('planets', array(
      "MODIFY COLUMN `debris_metal` BIGINT(20) UNSIGNED DEFAULT 0",
      "MODIFY COLUMN `debris_crystal` BIGINT(20) UNSIGNED DEFAULT 0",
    ), strtoupper($update_tables['planets']['debris_metal']['Type']) != 'BIGINT(20) UNSIGNED');

    $illegal_moon_query = upd_do_query("SELECT id FROM `{{planets}}` WHERE `id_owner` <> 0 AND `planet_type` = 3 AND `parent_planet` <> 0 AND `parent_planet` NOT IN (SELECT `id` FROM {{planets}} WHERE `planet_type` = 1);");
    while($illegal_moon_row = mysql_fetch_assoc($illegal_moon_query))
    {
      upd_do_query("DELETE FROM {{planets}} WHERE id = {$illegal_moon_row['id']} LIMIT 1;", true);
    }

    upd_check_key('allow_buffing', isset($config->fleet_buffing_check) ? !$config->fleet_buffing_check : 0, !isset($config->allow_buffing));
    upd_check_key('ally_help_weak', 0, !isset($config->ally_help_weak));

  upd_do_query('COMMIT;', true);
  $new_version = 29;

  case 29:
    upd_log_version_update();

    upd_check_key('game_email_pm', 0, !isset($config->game_email_pm));
    upd_check_key('player_vacation_time', 2*24*60*60, !isset($config->player_vacation_time));
    upd_check_key('player_delete_time', 45*24*60*60, !isset($config->player_delete_time));

    upd_create_table('log_dark_matter',
      "(
        `log_dark_matter_id` SERIAL,
        `log_dark_matter_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp',
        `log_dark_matter_username` varchar(64) NOT NULL DEFAULT '' COMMENT 'Username',
        `log_dark_matter_reason` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Reason ID for dark matter adjustment',
        `log_dark_matter_amount` INT(10) NOT NULL DEFAULT 0 COMMENT 'Amount of dark matter change',
        `log_dark_matter_comment` TEXT COMMENT 'Comments',
        `log_dark_matter_page` varchar(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
        `log_dark_matter_sender` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID which make log record',

        PRIMARY KEY (`log_dark_matter_id`),
        KEY `i_log_dark_matter_sender_id` (`log_dark_matter_sender`, `log_dark_matter_id`),
        KEY `i_log_dark_matter_reason_sender_id` (`log_dark_matter_reason`, `log_dark_matter_sender`, `log_dark_matter_id`),
        KEY `i_log_dark_matter_amount` (`log_dark_matter_amount`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );
    upd_do_query('COMMIT;', true);

    $records = 1;
    while($records)
    {
      upd_do_query('START TRANSACTION;', true);
      $query = upd_do_query("SELECT * FROM {{logs}} WHERE log_code = 102 order by log_id LIMIT 1000;");
      $records = mysql_numrows($query);
      while($row = mysql_fetch_assoc($query))
      {
        $result = preg_match('/^Player ID (\d+) Dark Matter was adjusted with (\-?\d+). Reason: (.+)$/', $row['log_text'], $matches);

        $reason = RPG_NONE;
        $comment = $matches[3];
        switch($matches[3])
        {
          case 'Level Up For Structure Building':
            $reason = RPG_STRUCTURE;
          break;

          case 'Level Up For Raiding':
          case 'Level Up For Raids':
            $reason = RPG_RAID;
            $comment = 'Level Up For Raiding';
          break;

          case 'Expedition Bonus':
            $reason = RPG_EXPEDITION;
          break;

          default:
            if(preg_match('/^Using Black Market page (\d+)$/', $comment, $matches2))
            {
              $reason = RPG_MARKET;
            }
            elseif(preg_match('/^Spent for officer (.+) ID (\d+)$/', $comment, $matches2))
            {
              $reason = RPG_MERCENARY;
              $comment = "Spent for mercenary {$matches2[1]} GUID {$matches2[2]}";
            }
            elseif(preg_match('/^Incoming From Referral ID\ ?(\d+)$/', $comment, $matches2))
            {
              $reason = RPG_REFERRAL;
              $comment = "Incoming from referral ID {$matches[1]}";
            }
            elseif(preg_match('/^Through admin interface for user .* ID \d+ (.*)$/', $comment, $matches2))
            {
              $reason = RPG_ADMIN;
              $comment = $matches2[1];
            }
          break;
        }

        if($matches[2])
        {
          $row['log_username'] = mysql_real_escape_string($row['log_username']);
          $row['log_page'] = mysql_real_escape_string($row['log_page']);
          $comment = mysql_real_escape_string($comment);

          upd_do_query(
            "INSERT INTO {{log_dark_matter}} (`log_dark_matter_timestamp`, `log_dark_matter_username`, `log_dark_matter_reason`,
              `log_dark_matter_amount`, `log_dark_matter_comment`, `log_dark_matter_page`, `log_dark_matter_sender`)
            VALUES (
              '{$row['log_timestamp']}', '{$row['log_username']}', {$reason},
              {$matches[2]}, '{$comment}', '{$row['log_page']}', {$row['log_sender']});"
          , true);
        }
      }

      upd_do_query("DELETE FROM {{logs}} WHERE log_code = 102 LIMIT 1000;", true);
      upd_do_query('COMMIT;', true);
    }

    foreach($update_tables as $table_name => $cork)
    {
      $row = mysql_fetch_assoc(upd_do_query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$db_name}' AND TABLE_NAME = '{$config->db_prefix}{$table_name}';", true));
      if($row['ENGINE'] != 'InnoDB')
      {
        upd_alter_table($table_name, 'ENGINE=InnoDB', true);
      }
      if($row['TABLE_COLLATION'] != 'utf8_general_ci')
      {
        upd_alter_table($table_name, 'CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci', true);
      }
    }

  upd_do_query('COMMIT;', true);
//  $new_version = 29.1;

//  case 29.1:
//    upd_log_version_update();
//
//  upd_do_query('COMMIT;', true);
//  $new_version = 29.1;

};
upd_log_message('Upgrade complete.');

if($new_version)
{
  $config->db_saveItem('db_version', $new_version);
  upd_log_message("<font color=green>DB version is now {$new_version}</font>");
}
else
{
  upd_log_message("DB version didn't changed from {$config->db_version}");
}

if($user['authlevel'] >= 3)
{
  print(str_replace("\r\n", '<br>', $upd_log));
}

function upd_do_query($query, $no_log = false)
{
  global $update_tables;

  upd_add_more_time();
  if(!$no_log)
  {
    upd_log_message("Performing query '{$query}'");
  }

  $db_prefix = sn_db_connect($query);
  if(!(strpos($query, '{{') === false))
  {
    foreach($update_tables as $tableName => $cork)
    {
      $query = str_replace("{{{$tableName}}}", $db_prefix.$tableName, $query);
    }
  }

  $result = mysql_query($query) or
    die('Query error for ' . $query . ': ' . mysql_error());

  return $result;
}

function upd_check_key($key, $default_value, $condition = false)
{
  global $config;

  $config->db_loadItem($key);
  if($condition || !isset($config->$key))
  {
    upd_add_more_time();
    if(!$GLOBALS['sys_log_disabled'])
    {
      upd_log_message("Updating config key '{$key}' with value '{$default_value}'");
    }
    $config->db_saveItem($key, $default_value);
  }
}

function upd_log_version_update()
{
  doquery('START TRANSACTION;');
  upd_add_more_time();
  upd_log_message("Detected outdated version {$GLOBALS['new_version']}. Upgrading...");
}

function upd_add_more_time($time = 0)
{
  global $config, $time_now;

  $time = $time ? $time : ($config->upd_lock_time ? $config->upd_lock_time : 30);

  if(!$GLOBALS['sys_log_disabled'])
  {
    $config->db_saveItem('var_db_update_end', $time_now + $time);
  }
  set_time_limit($time);
}

function upd_log_message($message)
{
  if($GLOBALS['sys_log_disabled'])
  {
//    print("{$message}<br />");
  }
  else
  {
    $GLOBALS['upd_log'] .= "{$message}\r\n";
    $GLOBALS['debug']->warning($message, 'Database Update', 103);
  }
}

function upd_unset_table_info($table_name)
{
  global $update_tables, $update_indexes, $update_foreigns;

  if(isset($update_tables[$table_name]))
  {
    unset($update_tables[$table_name]);
  }

  if(isset($update_indexes[$table_name]))
  {
    unset($update_indexes[$table_name]);
  }

  if(isset($update_foreigns[$table_name]))
  {
    unset($update_foreigns[$table_name]);
  }

}

function upd_load_table_info($prefix_table_name, $prefixed = true)
{
  global $config, $update_tables, $update_indexes, $update_foreigns, $db_name;

  $tableName = $prefixed ? str_replace($config->db_prefix, '', $prefix_table_name) : $prefix_table_name;
  $prefix_table_name = $prefixed ? $prefix_table_name : $config->db_prefix . $prefix_table_name;

  upd_unset_table_info($tableName);

  $q1 = doquery("SHOW COLUMNS FROM {$prefix_table_name};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = doquery("SHOW INDEX FROM {$prefix_table_name};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
  }

  $q1 = doquery("select * FROM information_schema.KEY_COLUMN_USAGE where TABLE_SCHEMA = '{$db_name}' AND TABLE_NAME = '{$prefix_table_name}' AND REFERENCED_TABLE_NAME is not null;");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $table_referenced = str_replace($config->db_prefix, '', $r1['REFERENCED_TABLE_NAME']);

    $update_foreigns[$tableName][$r1['CONSTRAINT_NAME']] .= "{$r1['COLUMN_NAME']},{$table_referenced},{$r1['REFERENCED_COLUMN_NAME']};";
  }
}

function upd_alter_table($table, $alters, $condition = true)
{
  global $config;

  if(!$condition)
  {
    return;
  }

  upd_add_more_time();
  $alters_print = is_array($alters) ? dump($alters) : $alters;
  upd_log_message("Altering table '{$table}' with alterations {$alters_print}");

  if(!is_array($alters))
  {
    $alters = array($alters);
  }

  $qry = "ALTER TABLE {$config->db_prefix}{$table} " . implode(',', $alters) . ';';

  $result = mysql_query($qry);
  $error = mysql_error();
  if($error)
  {
    die("Altering error for table `{$table}`: {$error}<br />{$alters_print}");
  }

//  if(strpos('RENAME TO', strtoupper(implode(',', $alters))) === false)
  {
    upd_load_table_info($table, false);
  }

  return $result;
}

function upd_drop_table($table_name)
{
  global $config;

  mysql_query("DROP TABLE IF EXISTS {$config->db_prefix}{$table_name};");

  upd_unset_table_info($table_name);
}

function upd_create_table($table_name, $declaration)
{
  global $config, $update_tables;

  if(!$update_tables[$table_name])
  {
    doquery('set foreign_key_checks = 0;');
    $result = mysql_query("CREATE TABLE IF NOT EXISTS `{$config->db_prefix}{$table_name}` {$declaration}");
    $error = mysql_error();
    if($error)
    {
      die("Creating error for table `{$table_name}`: {$error}<br />" . dump($declaration));
    }
    doquery('set foreign_key_checks = 1;');
    upd_load_table_info($table_name, false);
    sys_refresh_tablelist($config->db_prefix);
  }

  return $result;
}

$config->debug = $debug_value;

unset($sn_cache->tables);
sys_refresh_tablelist($config->db_prefix);

?>
