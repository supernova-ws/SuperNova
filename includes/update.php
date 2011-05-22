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
$query = doquery('SHOW TABLES;');
while($row = mysql_fetch_row($query))
{
  $tableName = str_replace($config->db_prefix, '', $row[0]);

  $q1 = doquery("SHOW COLUMNS FROM {$row[0]};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_tables[$tableName][$r1['Field']] = $r1;
  }

  $q1 = doquery("SHOW INDEX FROM {$row[0]};");
  while($r1 = mysql_fetch_assoc($q1))
  {
    $update_indexes[$tableName][$r1['Key_name']] .= "{$r1['Column_name']},";
  }
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

switch($new_version)
{
  case 0:
    upd_log_version_update();
    upd_alter_table('planets', array(
      "ADD `parent_planet` bigint(11) unsigned DEFAULT '0'",
      "ADD KEY `i_parent_planet` (`parent_planet`)"
    ), !$update_tables['planets']['parent_planet']);
    upd_do_query(
      "UPDATE `{{planets}}` AS lu
        LEFT JOIN `{{planets}}` AS pl
          ON pl.galaxy=lu.galaxy AND pl.system=lu.system AND pl.planet=lu.planet AND pl.planet_type=1
      SET lu.parent_planet=pl.id WHERE lu.planet_type=3;"
    );
  doquery('COMMIT;');
  $new_version = 1;

  case 1:
    upd_log_version_update();
    if(!$update_tables['counter'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}counter` (
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
    };
  doquery('COMMIT;');
  $new_version = 2;

  case 2:
    upd_log_version_update();
    if($update_tables['lunas'])
    {
      mysql_query("DROP TABLE IF EXISTS {$config->db_prefix}lunas;");
    }
  doquery('COMMIT;');
  $new_version = 3;

  case 3:
    upd_log_version_update();
    upd_alter_table('counter', "ADD `url` varchar(255) CHARACTER SET utf8 DEFAULT ''", !$update_tables['counter']['url']);
  doquery('COMMIT;');
  $new_version = 4;

  case 4:
    upd_log_version_update();
    upd_alter_table('planets', "ADD `debris_metal` bigint(11) unsigned DEFAULT '0'", !$update_tables['planets']['debris_metal']);
    upd_alter_table('planets', "ADD `debris_crystal` bigint(11) unsigned DEFAULT '0'", !$update_tables['planets']['debris_crystal']);

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
  doquery('COMMIT;');
  $new_version = 5;

  case 5:
    upd_log_version_update();
    mysql_query("DROP TABLE IF EXISTS `{$config->db_prefix}galaxy`;");
  doquery('COMMIT;');
  $new_version = 6;

  case 6:
    upd_log_version_update();
    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('BannerURL', 'banner_source_post', 'BannerOverviewFrame',
      'close_reason', 'dbVersion', 'ForumUserBarFrame', 'OverviewBanner', 'OverviewClickBanner', 'OverviewExternChat',
      'OverviewExternChatCmd', 'OverviewNewsText', 'UserbarURL', 'userbar_source');");
  doquery('COMMIT;');
  $new_version = 7;

  case 7:
    upd_log_version_update();
    upd_alter_table('fleets', array(
      "ADD KEY `fleet_mess` (`fleet_mess`)",
      "ADD KEY `fleet_group` (`fleet_group`)"
    ), !$update_indexes['fleets']['fleet_mess']);
  doquery('COMMIT;');
  $new_version = 8;

  case 8:
    upd_log_version_update();

    upd_alter_table('referrals', "ADD `dark_matter` bigint(11) NOT NULL DEFAULT '0' COMMENT 'How much player have aquired Dark Matter'", !$update_tables['referrals']['dark_matter']);
    upd_alter_table('referrals', "ADD KEY `id_partner` (`id_partner`)", !$update_indexes['referrals']['id_partner']);

    upd_check_key('rpg_bonus_divisor', 10);
    upd_check_key('rpg_officer', 3);
  doquery('COMMIT;');
  $new_version = 9;

  case 9:
    upd_log_version_update();

    $dm_change_legit = true;

    upd_do_query(
      "UPDATE {{referrals}} AS r
        LEFT JOIN {{users}} AS u
          ON u.id = r.id
      SET r.dark_matter = u.lvl_minier + u.lvl_raid;"
    );
    upd_add_more_time();

    upd_do_query(
      "UPDATE {{users}} AS u
        RIGHT JOIN {{referrals}} AS r
          ON r.id_partner = u.id AND r.dark_matter >= {$config->rpg_bonus_divisor}
      SET u.rpg_points = u.rpg_points + FLOOR(r.dark_matter/{$config->rpg_bonus_divisor});"
    );

    $dm_change_legit = false;
  doquery('COMMIT;');
  $new_version = 10;

  case 10:
    upd_log_version_update();
    upd_check_key('game_news_overview', 3);
    upd_check_key('game_news_actual', 259200);
  doquery('COMMIT;');
  $new_version = 11;

  case 11:
    upd_log_version_update();

    upd_alter_table('users', array(
      "DROP COLUMN `aktywnosc`",
      "DROP COLUMN `time_aktyw`",
      "DROP COLUMN `kiler`",
      "DROP COLUMN `kod_aktywujacy`",
      "DROP COLUMN `ataker`",
      "DROP COLUMN `atakin`"
    ), $update_tables['users']['ataker']);

    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('OverviewNewsFrame');");
  doquery('COMMIT;');
  $new_version = 12;

  case 12:
    upd_log_version_update();
    upd_alter_table('planets', "ADD `supercargo` bigint(11) NOT NULL DEFAULT '0' COMMENT 'Supercargo ship count'", !$update_tables['planets']['supercargo']);
    if(!$update_tables['alliance_requests'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}alliance_requests` (
          `id_user` int(11) NOT NULL,
          `id_ally` int(11) NOT NULL DEFAULT '0',
          `request_text` text,
          `request_time` int(11) NOT NULL DEFAULT '0',
          `request_denied` tinyint(1) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id_user`,`id_ally`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
      );
    };
  doquery('COMMIT;');
  $new_version = 13;

  case 13:
    upd_log_version_update();
    mysql_query("DROP TABLE IF EXISTS `{$config->db_prefix}update`;");
  $new_version = 14;

  case 14:
    upd_log_version_update();
    upd_check_key('rules_url', '/rules.php');
  doquery('COMMIT;');
  $new_version = 15;

  case 15:
    upd_log_version_update();
    upd_alter_table('users', "DROP COLUMN `current_luna`", $update_tables['users']['current_luna']);
    upd_alter_table('users', "ADD `options` TEXT COMMENT 'Packed user options'", !$update_tables['users']['options']);
  doquery('COMMIT;');
  $new_version = 16;

  case 16:
    upd_log_version_update();
    upd_check_key('game_speed', $config->game_speed/2500, $config->game_speed >= 2500);
    upd_check_key('fleet_speed', $config->fleet_speed/2500, $config->fleet_speed >= 2500);
    upd_check_key('player_max_colonies', $config->player_max_planets ? ($config->player_max_planets - 1) : 9);
    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('player_max_planets');");
    upd_alter_table('users', "ADD `news_lastread` int(11) NOT NULL DEFAULT '0' COMMENT 'News last read tag'", !$update_tables['users']['news_lastread']);
  doquery('COMMIT;');
  $new_version = 17;

  case 17:
    upd_log_version_update();
    upd_check_key('game_default_language', 'ru');
    upd_check_key('game_default_skin', 'skins/EpicBlue/');
    upd_check_key('game_default_template', 'OpenGame');
    upd_alter_table('announce', "ADD `detail_url` varchar(250) NOT NULL DEFAULT '' COMMENT 'Link to more details about update'", !$update_tables['announce']['detail_url']);
  doquery('COMMIT;');
  $new_version = 18;

  case 18:
    upd_log_version_update();

    upd_check_key('game_counter', 1);

    upd_check_key('int_format_date', 'd.m.Y');
    upd_check_key('int_format_time', 'H:i:s');
    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('game_date_withTime');");

    upd_alter_table('users', array(
      "MODIFY `user_lastip` VARCHAR(250) COMMENT 'User last IP'",
      "ADD `user_proxy` VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)'"
    ), !$update_tables['users']['user_proxy']);

    upd_alter_table('counter', array(
      "MODIFY `ip` VARCHAR(250) COMMENT 'User last IP'",
      "ADD `proxy` VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)'"
    ), !$update_tables['counter']['proxy']);
  doquery('COMMIT;');
  $new_version = 19;

  case 19:
    upd_log_version_update();
    upd_check_key('int_format_time', 'H:i:s', true);
    upd_check_key('int_banner_background', 'design/images/banner.png', true);
    upd_check_key('int_userbar_background', 'design/images/userbar.png', true);
    upd_do_query('UPDATE {{planets}} SET `metal_mine` = `metal_mine` - 1 WHERE `metal_mine` > 5;');
  doquery('COMMIT;');
  $new_version = 20;

  case 20:
    upd_log_version_update();
    upd_alter_table('statpoints', array(
      "ADD `res_rank` INT(11) DEFAULT 0 COMMENT 'Rank by resources'",
      "ADD `res_old_rank` INT(11) DEFAULT 0 COMMENT 'Old rank by resources'",
      "ADD `res_points` BIGINT(20) DEFAULT 0 COMMENT 'Resource stat points'",
      "ADD `res_count` BIGINT(20) DEFAULT 0 COMMENT 'Old rank by resources'"
    ), !$update_tables['statpoints']['res_rank']);
  doquery('COMMIT;');
  $new_version = 21;

  case 21:
    upd_log_version_update();
    upd_check_key('game_noob_points', 5000, true);
    upd_check_key('game_noob_factor', 5, true);

    upd_check_key('url_forum', $config->forum_url, !$config->url_forum);
    upd_check_key('url_rules', $config->rules_url, !$config->url_rules);
    upd_check_key('url_dark_matter', '/dark_matter_get.php', !$config->url_dark_matter);
    upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('forum_url', 'rules_url');");

  doquery('COMMIT;');
  $new_version = 22;

  case 22:
    upd_log_version_update();
    upd_alter_table('planets', "ADD `governor` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Planet governor'", !$update_tables['planets']['governor']);
    upd_alter_table('planets', "ADD `governor_level` smallint unsigned NOT NULL DEFAULT '0' COMMENT 'Governor level'", !$update_tables['planets']['governor_level']);
    upd_alter_table('planets', "ADD `que` varchar(4096) NOT NULL DEFAULT '' COMMENT 'Planet que'", !$update_tables['planets']['que']);

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
      upd_do_query("UPDATE {{planets}} SET `que` = '{$planet_data['que']}', `b_building` = '0', `b_building_id` = '0' WHERE `id` = '{$planet_data['id']}' LIMIT 1;");
    }

    if(!$update_tables['mercenaries'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}mercenaries` (
          `id` bigint(11) NOT NULL AUTO_INCREMENT,
          `id_user` bigint(11) NOT NULL,
          `mercenary` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `time_start` int(11) NOT NULL DEFAULT '0',
          `time_finish` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `i_user_mercenary_time` (`id_user`, `mercenary`, `time_start`, `time_finish`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    };
  doquery('COMMIT;');
  $new_version = 23;

  case 23:
    upd_log_version_update();
    if(!$update_tables['confirmations'])
    {
      $result = mysql_query(
        "CREATE TABLE `{$config->db_prefix}confirmations` (
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
    };
  doquery('COMMIT;');
  $new_version = 24;

  case 24:
    upd_log_version_update();

    if(!$update_tables['users']['vacation'])
    {
      upd_alter_table('users', "ADD `vacation` int(11) NOT NULL DEFAULT '0' COMMENT 'Time when user can leave vacation mode'", !$update_tables['users']['vacation']);
      upd_do_query('UPDATE {{users}} SET `vacation` = `urlaubs_until` WHERE `urlaubs_modus` <> 0;');
    }
    upd_alter_table('users', 'DROP COLUMN `urlaubs_until`, DROP COLUMN `urlaubs_modus`, DROP COLUMN `urlaubs_modus_time`');

    if(isset($config->urlaubs_modus_erz))
    {
      upd_check_key('user_vacation_disable', $config->urlaubs_modus_erz, !isset($config->user_vacation_disable));
      upd_do_query("DELETE FROM {{config}} WHERE `config_name` IN ('urlaubs_modus_erz');");
      unset($config->urlaubs_modus_erz);
    }

  doquery('COMMIT;');
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

    if(!$update_tables['logs']['log_timestamp'])
    {
      upd_add_more_time(300);
      if(!$update_tables['logs_backup'])
      {
        mysql_query("CREATE TABLE {$config->db_prefix}logs_backup AS (SELECT * FROM logs);");
      }

      mysql_query("ALTER TABLE {$config->db_prefix}logs
        DROP COLUMN `log_id`,
        ADD COLUMN `log_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp' FIRST,
        ADD COLUMN `log_username` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'Username' AFTER `log_timestamp`,
        MODIFY COLUMN `log_title` VARCHAR(64) NOT NULL DEFAULT 'Log entry' COMMENT 'Short description' AFTER `log_username`,
        MODIFY COLUMN `log_page` VARCHAR(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log' AFTER `log_text`,
        CHANGE COLUMN `log_type` `log_code` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `log_page`,
        MODIFY COLUMN `log_sender` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'User ID which make log record' AFTER `log_code`,
        MODIFY COLUMN `log_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Machine-readable timestamp' AFTER `log_sender`,
        ADD COLUMN `log_dump` TEXT NOT NULL DEFAULT '' COMMENT 'Machine-readable dump of variables' AFTER `log_time`,
        ADD INDEX `i_log_username` (`log_username`),
        ADD INDEX `i_log_time` (`log_time`),
        ADD INDEX `i_log_sender` (`log_sender`),
        ADD INDEX `i_log_code` (`log_code`),
        ADD INDEX `i_log_page` (`log_page`),
        CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci"
      );

      doquery('DELETE FROM `{{logs}}` WHERE `log_code` = 303;');

      if($update_tables['errors'])
      {
        upd_do_query('INSERT INTO `{{logs}}` (`log_code`, `log_sender`, `log_title`, `log_text`, `log_page`, `log_time`) SELECT 500, `error_sender`, `error_type`, `error_text`, `error_page`, `error_time` FROM `{{errors}}`;');
        doquery("ALTER TABLE {{errors} RENAME TO {$config->db_prefix}errors_backup;");
      }

      upd_alter_table('logs', 'ORDER BY log_time');
      upd_alter_table('logs', array(
        "ADD COLUMN `log_id` SERIAL",
        "ADD PRIMARY KEY (`log_id`)"
      ));
      upd_do_query('UPDATE `{{logs}}` SET `log_timestamp` = FROM_UNIXTIME(`log_time`);');
      upd_do_query('UPDATE `{{logs}}` AS l LEFT JOIN `{{users}}` AS u ON u.id = l.log_sender SET l.log_username = u.username WHERE l.log_username IS NOT NULL;');

      upd_do_query("UPDATE `{{logs}}` SET `log_code` = 190 WHERE `log_code` = 100 AND `log_title` = 'Stat update';");
      upd_do_query("UPDATE `{{logs}}` SET `log_code` = 191 WHERE `log_code` = 101 AND `log_title` = 'Stat update';");
      upd_do_query("UPDATE `{{logs}}` SET `log_code` = 192 WHERE `log_code` = 102 AND `log_title` = 'Stat update';");
    }
    $GLOBALS['sys_log_disabled'] = false;

  doquery('COMMIT;');
  $new_version = 26;

  case 26:
    $GLOBALS['sys_log_disabled'] = false;
    upd_log_version_update();
    upd_alter_table('planets', "ADD INDEX `i_parent_planet` (`parent_planet`)", !$update_indexes['planets']['i_parent_planet']);
    upd_alter_table('messages', "DROP INDEX `owner`", $update_indexes['messages']['owner']);
    upd_alter_table('messages', "DROP INDEX `owner_type`", $update_indexes['messages']['owner_type']);
    upd_alter_table('messages', "DROP INDEX `sender_type`", $update_indexes['messages']['sender_type']);

    upd_alter_table('messages', array(
      "ADD INDEX `i_owner_time` (`message_owner`, `message_time`)",
      "ADD INDEX `i_sender_time` (`message_sender`, `message_time`)",
      "ADD INDEX `i_time` (`message_time`)"
    ), !$update_indexes['messages']['i_owner_time']);

    mysql_query("DROP TABLE IF EXISTS {$config->db_prefix}fleet_log;");

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

    doquery('DELETE FROM {{aks}} WHERE `id` NOT IN (SELECT DISTINCT `fleet_group` FROM {{fleets}});');

    if(!$update_tables['shortcut'])
    {
      upd_alter_table('users', 'CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

      doquery('set foreign_key_checks = 0;');
      $result = mysql_query(
        "CREATE TABLE `{$config->db_prefix}shortcut` (
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
      doquery('set foreign_key_checks = 1;');

      sys_refresh_tablelist($config->db_prefix);

      $temp_planet_types = array(PT_PLANET, PT_DEBRIS, PT_MOON);

      $query = doquery("SELECT id, fleet_shortcut FROM {{users}} WHERE fleet_shortcut > '';");
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
            doquery("INSERT INTO {$config->db_prefix}shortcut (shortcut_user_id, shortcut_galaxy, shortcut_system, shortcut_planet, shortcut_planet_type, shortcut_text) VALUES ({$user_data['id']}, {$shortcut[1]}, {$shortcut[2]}, {$shortcut[3]}, {$shortcut[4]}, '{$shortcut[0]}');");
          }
        }
      }

      upd_alter_table('users', 'DROP COLUMN `fleet_shortcut`');
    };

    upd_check_key('url_faq', '', !isset($config->url_faq));

    doquery('COMMIT;');
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

    if(!$update_indexes['alliance']['i_ally_name'])
    {
      mysql_query(
        "ALTER TABLE {$config->db_prefix}alliance
          MODIFY COLUMN `id` SERIAL,
          ADD CONSTRAINT UNIQUE KEY `i_ally_name` (`ally_name`),
          CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci,
          ENGINE=InnoDB;"
      );
    }

    $upd_relation_types = "'neutral', 'war', 'peace', 'confederation', 'federation', 'union', 'master', 'slave'";
    if(!$update_tables['alliance_diplomacy'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}alliance_diplomacy` (
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
    }

    if(!$update_tables['alliance_negotiation'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}alliance_negotiation` (
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
    }

    if(!$update_tables['bashing'])
    {
      mysql_query(
        "ALTER TABLE {$config->db_prefix}users
          MODIFY COLUMN `id` SERIAL,
          CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;"
      );

      mysql_query(
        "ALTER TABLE {$config->db_prefix}planets
          MODIFY COLUMN `id` SERIAL,
          CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;"
      );

      mysql_query(
        "CREATE TABLE `{$config->db_prefix}bashing` (
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
    }

    upd_check_key('fleet_bashing_war_delay', 12 * 60 * 60, !isset($config->fleet_bashing_war_delay));
    upd_check_key('fleet_bashing_scope', 24 * 60 * 60, !isset($config->fleet_bashing_scope));
    upd_check_key('fleet_bashing_interval', 30 * 60, !isset($config->fleet_bashing_interval));
    upd_check_key('fleet_bashing_waves', 3, !isset($config->fleet_bashing_waves));
    upd_check_key('fleet_bashing_attacks', 3, !isset($config->fleet_bashing_attacks));

  doquery('COMMIT;');
  $new_version = 28;

  case 28: upd_log_version_update();
    if(!$update_tables['quest'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}quest` (
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
    }
    else
    {
      // mysql_query('alter table {$config->db_prefix}quest add           KEY (`quest_type`, `quest_order`)');
    }

    if(!$update_tables['quest_status'])
    {
      mysql_query(
        "CREATE TABLE `{$config->db_prefix}quest_status` (
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
    }
    else
    {
      // mysql_query('alter table {$config->db_prefix}quest_status modify column `quest_status_status` TINYINT DEFAULT 1;');
    }

    upd_alter_table('users', "CHANGE COLUMN `rpg_points` `dark_matter` int(11) DEFAULT 0", $update_tables['users']['rpg_points']);

    upd_check_key('quest_total', 0, !isset($config->quest_total));

    upd_do_query("UPDATE {{users}} SET `ally_id` = null, ally_name = null, ally_register_time = 0, ally_rank_id = 0 WHERE `ally_id` NOT IN (SELECT id FROM {{alliance}});");

    upd_alter_table('alliance', array(
       'DROP INDEX `id_2`',
       'DROP INDEX `id_3`',
       'DROP INDEX `id_4`',
       'DROP INDEX `id_5`',
       'DROP INDEX `id_6`',
       'DROP INDEX `id_7`',
       'DROP INDEX `id_8`',
       'DROP INDEX `id_9`',
       'DROP INDEX `id_10`',
       'DROP INDEX `id_11`',
       'DROP INDEX `id_12`',
       'DROP INDEX `ally_name`',
       'ADD UNIQUE INDEX `i_ally_tag` (`ally_tag`)',
       'MODIFY COLUMN `ranklist` TEXT',
    ), $update_indexes['alliance']['id_2']);

    if($update_tables['users']['ally_request_text'])
    {
      upd_alter_table('users', array(
        'DROP INDEX `id_2`',
        'DROP INDEX `id_3`',
        'DROP INDEX `id_4`',
        'DROP INDEX `id_5`',
        'DROP INDEX `id_6`',
        'DROP INDEX `id_7`',
        'DROP INDEX `id_8`',
        'DROP INDEX `id_9`',
        'DROP INDEX `id_10`',
        'DROP INDEX `id_11`',
        'DROP INDEX `id_12`',
        'DROP INDEX `id_13`',
        'DROP INDEX `id_14`',
        'DROP INDEX `id_15`',
        'DROP INDEX `id_16`',
        'DROP INDEX `id_17`',
        'DROP INDEX `id_18`',
        'DROP INDEX `id_19`',
        'DROP INDEX `id_20`',
        'DROP INDEX `id_21`',
        'DROP COLUMN `ally_request`',
        'DROP COLUMN `ally_request_text`',
        'MODIFY COLUMN `ally_name` VARCHAR(32) DEFAULT NULL',
        'MODIFY COLUMN `ally_id` BIGINT(20) UNSIGNED DEFAULT NULL',
        'ADD UNIQUE INDEX `i_ally_id` (`ally_id`)',
        'ADD UNIQUE INDEX `i_ally_name` (`ally_name`)',
      ), true);

      upd_alter_table('users', array(
         'ADD CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
         'ADD CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE',
      ), true);
    }

/*
    $result = upd_alter_table('users', array(
       'ADD CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
       'ADD CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `{$config->db_prefix}alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE',
    ), true);
*/


  doquery('COMMIT;');
  // $new_version = 28.1;
/*
  // alter table game_counter add index `i_time_id` (`time`, `id`);
*/
};
//$GLOBALS['config']->db_saveItem('flt_lastUpdate', 0);
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

if ( $user['authlevel'] >= 3 )
{
  print(str_replace("\r\n", '<br>', $upd_log));
}

function upd_do_query($query)
{
  upd_add_more_time();
  upd_log_message("Performing query '{$query}'");

  return doquery($query);
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

  $qry = "ALTER TABLE {$config->db_prefix}{$table}";
  foreach($alters as $alteration)
  {
    if($alteration)
    {
      $qry .= " {$alteration},";
    }
  }
  $qry = substr($qry, 0, -1) . ';';

  return mysql_query($qry);
}

function upd_check_key($key, $default_value, $condition = false)
{
  global $config;

  if($condition || !$config->db_loadItem($key))
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

function upd_add_more_time($time = 120)
{
  if(!$GLOBALS['sys_log_disabled'])
  {
    $GLOBALS['config']->db_saveItem('var_db_update_end', $GLOBALS['time_now'] + $time);
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

?>
