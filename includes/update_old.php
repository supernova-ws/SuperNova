<?php

if(!defined('IN_UPDATE') || IN_UPDATE !== true) {
  die('Hack attempt');
}

switch($new_version) {
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
    upd_drop_table('lunas');

    if($update_tables['galaxy']) {
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

    if($update_tables['users']['rpg_points']) {
      upd_do_query(
        "UPDATE {{users}} AS u
          RIGHT JOIN {{referrals}} AS r
            ON r.id_partner = u.id AND r.dark_matter >= " . classSupernova::$config->rpg_bonus_divisor . "
        SET u.rpg_points = u.rpg_points + FLOOR(r.dark_matter/" . classSupernova::$config->rpg_bonus_divisor . ");"
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
    upd_alter_table('users', "ADD `news_lastread` int(11) NOT NULL DEFAULT '0' COMMENT 'News last read date'", !$update_tables['users']['news_lastread']);
    upd_alter_table('users', array("MODIFY `user_lastip` VARCHAR(250) COMMENT 'User last IP'", "ADD `user_proxy` VARCHAR(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)'"), !$update_tables['users']['user_proxy']);

    upd_drop_table('update');

    upd_check_key('fleet_speed', classSupernova::$config->fleet_speed / 2500, classSupernova::$config->fleet_speed >= 2500);
    upd_check_key('game_counter', 0);
    upd_check_key('game_default_language', 'ru');
    upd_check_key('game_default_skin', 'skins/EpicBlue/');
    upd_check_key('game_default_template', 'OpenGame');
    upd_check_key('game_news_overview', 3);
    upd_check_key('game_news_actual', 259200);
    upd_check_key('game_noob_factor', 5, !isset(classSupernova::$config->game_noob_factor));
    upd_check_key('game_noob_points', 5000, !isset(classSupernova::$config->game_noob_points));
    upd_check_key('game_speed', classSupernova::$config->game_speed / 2500, classSupernova::$config->game_speed >= 2500);
    upd_check_key('int_format_date', 'd.m.Y');
    upd_check_key('int_format_time', 'H:i:s', true);
    upd_check_key('int_banner_background', 'design/images/banner.png', true);
    upd_check_key('int_userbar_background', 'design/images/userbar.png', true);
    upd_check_key('player_max_colonies', classSupernova::$config->player_max_planets ? (classSupernova::$config->player_max_planets - 1) : 9);
    upd_check_key('url_forum', classSupernova::$config->forum_url, !isset(classSupernova::$config->url_forum));
    upd_check_key('url_rules', classSupernova::$config->rules_url, !isset(classSupernova::$config->url_rules));
    upd_check_key('url_dark_matter', '', !isset(classSupernova::$config->url_dark_matter));

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

    if($update_tables['planets']['b_building']) {
      $planet_query = upd_do_query('SELECT * FROM {{planets}} WHERE `b_building` <> 0;');
      $const_que_structures = QUE_STRUCTURES;
      while($planet_data = db_fetch($planet_query)) {
        $old_que = explode(';', $planet_data['b_building_id']);
        foreach($old_que as $old_que_item_string) {
          if(!$old_que_item_string) {
            continue;
          }

          $old_que_item = explode(',', $old_que_item_string);
          if($old_que_item[4] == 'build') {
            $old_que_item[4] = BUILD_CREATE;
          } else {
            $old_que_item[4] = BUILD_DESTROY;
          }

          $old_que_item[3] = $old_que_item[3] > $planet_data['last_update'] ? $old_que_item[3] - $planet_data['last_update'] : 1;
          $planet_data['que'] = "{$old_que_item[0]},1,{$old_que_item[3]},{$old_que_item[4]},{$const_que_structures};{$planet_data['que']}";
        }
        upd_do_query("UPDATE {{planets}} SET `que` = '{$planet_data['que']}', `b_building` = '0', `b_building_id` = '0' WHERE `id` = '{$planet_data['id']}' LIMIT 1;", true);
      }
    }

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

    if($update_tables['users']['urlaubs_until']) {
      upd_alter_table('users', "ADD `vacation` int(11) NOT NULL DEFAULT '0' COMMENT 'Time when user can leave vacation mode'", !$update_tables['users']['vacation']);
      upd_do_query('UPDATE {{users}} SET `vacation` = `urlaubs_until` WHERE `urlaubs_modus` <> 0;');
      upd_alter_table('users', 'DROP COLUMN `urlaubs_until`, DROP COLUMN `urlaubs_modus`, DROP COLUMN `urlaubs_modus_time`', $update_tables['users']['urlaubs_until']);
    }

    upd_check_key('user_vacation_disable', classSupernova::$config->urlaubs_modus_erz, !isset(classSupernova::$config->user_vacation_disable));
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
    upd_create_table('logs_backup', "AS (SELECT * FROM {{logs}});");

    upd_alter_table('logs', array(
      "MODIFY COLUMN `log_id` INT(1)",
      "DROP PRIMARY KEY"
    ), !$update_tables['logs']['log_timestamp']);

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

    if($update_tables['errors']) {
      upd_do_query('INSERT INTO `{{logs}}` (`log_code`, `log_sender`, `log_title`, `log_text`, `log_page`, `log_time`) SELECT 500, `error_sender`, `error_type`, `error_text`, `error_page`, `error_time` FROM `{{errors}}`;');
      if($update_tables['errors_backup']) {
        upd_drop_table('errors_backup');
      }
      upd_alter_table('errors', ' RENAME TO ' . classSupernova::$config->db_prefix . 'errors_backup');

      upd_drop_table('errors');
    }

    upd_alter_table('logs', 'ORDER BY log_time');

    upd_alter_table('logs', array("ADD COLUMN `log_id` SERIAL", "ADD PRIMARY KEY (`log_id`)"), !$update_tables['logs']['log_id']);

    upd_do_query('UPDATE `{{logs}}` SET `log_timestamp` = FROM_UNIXTIME(`log_time`);');
    upd_do_query('UPDATE `{{logs}}` AS l LEFT JOIN `{{users}}` AS u ON u.id = l.log_sender SET l.log_username = u.username WHERE l.log_username IS NOT NULL;');

    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 190 WHERE `log_code` = 100 AND `log_title` = 'Stat update';");
    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 191 WHERE `log_code` = 101 AND `log_title` = 'Stat update';");
    upd_do_query("UPDATE `{{logs}}` SET `log_code` = 192 WHERE `log_code` = 102 AND `log_title` = 'Stat update';");
    $sys_log_disabled = false;

    upd_do_query('COMMIT;', true);
    $new_version = 26;

  case 26:
    upd_log_version_update();

    $sys_log_disabled = false;

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

    upd_check_key('chat_admin_highlight', '<font color=purple>$1</font>', !isset(classSupernova::$config->chat_admin_highlight));

    upd_check_key('int_banner_URL', 'banner.php?type=banner', classSupernova::$config->int_banner_URL == '/banner.php?type=banner');
    upd_check_key('int_userbar_URL', 'banner.php?type=userbar', classSupernova::$config->int_userbar_URL == '/banner.php?type=userbar');

    upd_alter_table('users', 'CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');

    if(!$update_tables['shortcut']) {
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

          CONSTRAINT `FK_shortcut_user_id` FOREIGN KEY (`shortcut_user_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      $temp_planet_types = array(PT_PLANET, PT_DEBRIS, PT_MOON);

      $query = upd_do_query("SELECT id, fleet_shortcut FROM {{users}} WHERE fleet_shortcut > '';");
      while($user_data = db_fetch($query)) {
        $shortcuts = explode("\r\n", $user_data['fleet_shortcut']);
        foreach($shortcuts as $shortcut) {
          if(!$shortcut) {
            continue;
          }

          $shortcut = explode(',', $shortcut);
          $shortcut[0] = db_escape($shortcut[0]);
          $shortcut[1] = intval($shortcut[1]);
          $shortcut[2] = intval($shortcut[2]);
          $shortcut[3] = intval($shortcut[3]);
          $shortcut[4] = intval($shortcut[4]);

          if($shortcut[0] && $shortcut[1] && $shortcut[2] && $shortcut[3] && in_array($shortcut[4], $temp_planet_types)) {
            $db_prefix = classSupernova::$config->db_prefix;
            upd_do_query("INSERT INTO {$db_prefix}shortcut (shortcut_user_id, shortcut_galaxy, shortcut_system, shortcut_planet, shortcut_planet_type, shortcut_text) VALUES ({$user_data['id']}, {$shortcut[1]}, {$shortcut[2]}, {$shortcut[3]}, {$shortcut[4]}, '{$shortcut[0]}');", true);
          }
        }
      }

      upd_alter_table('users', 'DROP COLUMN `fleet_shortcut`');
    };

    upd_check_key('url_faq', '', !isset(classSupernova::$config->url_faq));

    upd_do_query('COMMIT;', true);
    $new_version = 27;

  case 27:
    upd_log_version_update();

    upd_check_key('chat_highlight_moderator', '<font color=green>$1</font>', !isset(classSupernova::$config->chat_highlight_moderator));
    upd_check_key('chat_highlight_operator', '<font color=red>$1</font>', !isset(classSupernova::$config->chat_highlight_operator));
    upd_check_key('chat_highlight_admin', classSupernova::$config->chat_admin_highlight ? classSupernova::$config->chat_admin_highlight : '<font color=puple>$1</font>', !isset(classSupernova::$config->chat_highlight_admin));

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
      "ADD PRIMARY KEY (`ban_id`)"
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

        CONSTRAINT  `FK_diplomacy_ally_id`         FOREIGN KEY (`alliance_diplomacy_ally_id`)         REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_diplomacy_contr_ally_id`   FOREIGN KEY (`alliance_diplomacy_contr_ally_id`)   REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_diplomacy_contr_ally_name` FOREIGN KEY (`alliance_diplomacy_contr_ally_name`) REFERENCES `{{alliance}}` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
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

        CONSTRAINT  `FK_negotiation_ally_id`         FOREIGN KEY (`alliance_negotiation_ally_id`)         REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_ally_name`       FOREIGN KEY (`alliance_negotiation_ally_name`)       REFERENCES `{{alliance}}` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_contr_ally_id`   FOREIGN KEY (`alliance_negotiation_contr_ally_id`)   REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_negotiation_contr_ally_name` FOREIGN KEY (`alliance_negotiation_contr_ally_name`) REFERENCES `{{alliance}}` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
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

        CONSTRAINT  `FK_bashing_user_id`   FOREIGN KEY (`bashing_user_id`)   REFERENCES `{{users}}`   (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_check_key('fleet_bashing_war_delay', 12 * 60 * 60, !isset(classSupernova::$config->fleet_bashing_war_delay));
    upd_check_key('fleet_bashing_scope', 24 * 60 * 60, !isset(classSupernova::$config->fleet_bashing_scope));
    upd_check_key('fleet_bashing_interval', 30 * 60, !isset(classSupernova::$config->fleet_bashing_interval));
    upd_check_key('fleet_bashing_waves', 3, !isset(classSupernova::$config->fleet_bashing_waves));
    upd_check_key('fleet_bashing_attacks', 3, !isset(classSupernova::$config->fleet_bashing_attacks));

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
        ,CONSTRAINT `FK_quest_status_quest_id` FOREIGN KEY (`quest_status_quest_id`) REFERENCES `{{quest}}` (`quest_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ,CONSTRAINT `FK_quest_status_user_id`  FOREIGN KEY (`quest_status_user_id`)  REFERENCES `{{users}}` (`id`)       ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;"
    );

    upd_check_key('quest_total', 0, !isset(classSupernova::$config->quest_total));

    for($i = 0; $i < 25; $i++) {
      upd_alter_table('alliance', array("DROP INDEX `id_{$i}`",), $update_indexes['alliance']["id_{$i}"]);
      upd_alter_table('users', array("DROP INDEX `id_{$i}`",), $update_indexes['users']["id_{$i}"]);
      upd_alter_table('planets', array("DROP INDEX `id_{$i}`",), $update_indexes['planets']["id_{$i}"]);
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

    if(!$update_foreigns['users']['FK_users_ally_id']) {
      upd_alter_table('users', array(
        'MODIFY COLUMN `ally_name` VARCHAR(32) DEFAULT NULL',
        'MODIFY COLUMN `ally_id` BIGINT(20) UNSIGNED DEFAULT NULL',
      ), strtoupper($update_tables['users']['ally_id']['Type']) != 'BIGINT(20) UNSIGNED');

      upd_do_query('DELETE FROM {{alliance}} WHERE id NOT IN (SELECT ally_id FROM {{users}} GROUP BY ally_id);');
      upd_do_query("UPDATE {{users}} SET `ally_id` = NULL, ally_name = NULL, ally_register_time = 0, ally_rank_id = 0 WHERE `ally_id` NOT IN (SELECT id FROM {{alliance}});");
      upd_do_query("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON u.ally_id = a.id SET u.ally_name = a.ally_name, u.ally_tag = a.ally_tag WHERE u.ally_id IS NOT NULL;");

      upd_alter_table('users', array(
        "ADD CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `{{alliance}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `{{alliance}}` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_foreigns['users']['FK_users_ally_id']);
    }

    upd_alter_table('planets', array(
      "MODIFY COLUMN `debris_metal` BIGINT(20) UNSIGNED DEFAULT 0",
      "MODIFY COLUMN `debris_crystal` BIGINT(20) UNSIGNED DEFAULT 0",
    ), strtoupper($update_tables['planets']['debris_metal']['Type']) != 'BIGINT(20) UNSIGNED');

    $illegal_moon_query = upd_do_query("SELECT id FROM `{{planets}}` WHERE `id_owner` <> 0 AND `planet_type` = 3 AND `parent_planet` <> 0 AND `parent_planet` NOT IN (SELECT `id` FROM {{planets}} WHERE `planet_type` = 1);");
    while($illegal_moon_row = db_fetch($illegal_moon_query)) {
      upd_do_query("DELETE FROM {{planets}} WHERE id = {$illegal_moon_row['id']} LIMIT 1;", true);
    }

    upd_check_key('allow_buffing', isset(classSupernova::$config->fleet_buffing_check) ? !classSupernova::$config->fleet_buffing_check : 0, !isset(classSupernova::$config->allow_buffing));
    upd_check_key('ally_help_weak', 0, !isset(classSupernova::$config->ally_help_weak));

    upd_do_query('COMMIT;', true);
    $new_version = 29;

  case 29:
    upd_log_version_update();

    upd_check_key('game_email_pm', 0, !isset(classSupernova::$config->game_email_pm));
    upd_check_key('player_vacation_time', 2 * 24 * 60 * 60, !isset(classSupernova::$config->player_vacation_time));
    upd_check_key('player_delete_time', 45 * 24 * 60 * 60, !isset(classSupernova::$config->player_delete_time));

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
    while($records) {
      upd_do_query('START TRANSACTION;', true);
      $query = upd_do_query("SELECT * FROM {{LOGS}} WHERE log_code = 102 ORDER BY log_id LIMIT 1000;");
      $records = classSupernova::$db->db_num_rows($query);
      while($row = db_fetch($query)) {
        $result = preg_match('/^Player ID (\d+) Dark Matter was adjusted with (\-?\d+). Reason: (.+)$/', $row['log_text'], $matches);

        $reason = RPG_NONE;
        $comment = $matches[3];
        switch($matches[3]) {
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
            if(preg_match('/^Using Black Market page (\d+)$/', $comment, $matches2)) {
              $reason = RPG_MARKET;
            } elseif(preg_match('/^Spent for officer (.+) ID (\d+)$/', $comment, $matches2)) {
              $reason = RPG_MERCENARY;
              $comment = "Spent for mercenary {$matches2[1]} GUID {$matches2[2]}";
            } elseif(preg_match('/^Incoming From Referral ID\ ?(\d+)$/', $comment, $matches2)) {
              $reason = RPG_REFERRAL;
              $comment = "Incoming from referral ID {$matches[1]}";
            } elseif(preg_match('/^Through admin interface for user .* ID \d+ (.*)$/', $comment, $matches2)) {
              $reason = RPG_ADMIN;
              $comment = $matches2[1];
            }
          break;
        }

        if($matches[2]) {
          $row['log_username'] = db_escape($row['log_username']);
          $row['log_page'] = db_escape($row['log_page']);
          $comment = db_escape($comment);

          upd_do_query(
            "INSERT INTO {{log_dark_matter}} (`log_dark_matter_timestamp`, `log_dark_matter_username`, `log_dark_matter_reason`,
              `log_dark_matter_amount`, `log_dark_matter_comment`, `log_dark_matter_page`, `log_dark_matter_sender`)
            VALUES (
              '{$row['log_timestamp']}', '{$row['log_username']}', {$reason},
              {$matches[2]}, '{$comment}', '{$row['log_page']}', {$row['log_sender']});"
            , true);
        }
      }

      upd_do_query("DELETE FROM {{LOGS}} WHERE log_code = 102 LIMIT 1000;", true);
      upd_do_query('COMMIT;', true);
    }

    foreach($update_tables as $table_name => $cork) {
      $row = db_fetch(upd_do_query("SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . db_escape(classSupernova::$db_name) . "' AND TABLE_NAME = '" . classSupernova::$config->db_prefix . "{$table_name}';", true));
      if($row['ENGINE'] != 'InnoDB') {
        upd_alter_table($table_name, 'ENGINE=InnoDB', true);
      }
      if($row['TABLE_COLLATION'] != 'utf8_general_ci') {
        upd_alter_table($table_name, 'CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci', true);
      }
    }

    upd_do_query('COMMIT;', true);
    $new_version = 30;

  case 30:
    upd_log_version_update();

    upd_alter_table('users', array(
      "ADD `player_que` TEXT"
    ), !$update_tables['users']['player_que']);

    upd_alter_table('planets', array(
      "CHANGE COLUMN `governor` `PLANET_GOVERNOR_ID` SMALLINT(5) NOT NULL DEFAULT 0",
      "CHANGE COLUMN `governor_level` `PLANET_GOVERNOR_LEVEL` SMALLINT(5) NOT NULL DEFAULT 0",
    ), !$update_tables['planets']['PLANET_GOVERNOR_ID']);

    if($update_tables['users']['rpg_geologue']) {
      classSupernova::$db->doUpdate("UPDATE `{{users}}` SET `dark_matter` = `dark_matter` + (`rpg_geologue` + `rpg_ingenieur` + `rpg_constructeur` + `rpg_technocrate` + `rpg_scientifique` + `rpg_defenseur`) * 3;");

      upd_alter_table('users', array(
        "DROP COLUMN `rpg_geologue`",
        "DROP COLUMN `rpg_ingenieur`",
        "DROP COLUMN `rpg_constructeur`",
        "DROP COLUMN `rpg_technocrate`",
        "DROP COLUMN `rpg_scientifique`",
        "DROP COLUMN `rpg_defenseur`",
      ), $update_tables['users']['rpg_geologue']);
    }

    if($update_tables['users']['rpg_bunker']) {
      classSupernova::$db->doUpdate("UPDATE {{users}} SET `dark_matter` = `dark_matter` + (`rpg_bunker`) * 3;");

      upd_alter_table('users', array(
        "DROP COLUMN `rpg_bunker`",
      ), $update_tables['users']['rpg_bunker']);
    }

    upd_alter_table('users', array(
      "DROP COLUMN `p_infligees`",
      "MODIFY COLUMN `dark_matter` BIGINT(20) DEFAULT '0' AFTER `lvl_raid`",
    ), $update_tables['users']['p_infligees']);

    upd_alter_table('users', array(
      "ADD COLUMN `mrc_academic` SMALLINT(3) DEFAULT 0",
    ), !$update_tables['users']['mrc_academic']);

    upd_alter_table('users', array(
      "DROP COLUMN `db_deaktjava`",
      "DROP COLUMN `kolorminus`",
      "DROP COLUMN `kolorplus`",
      "DROP COLUMN `kolorpoziom`",
      "DROP COLUMN `deleteme`",

      "MODIFY COLUMN `xpraid` BIGINT(20) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `xpminier` BIGINT(20) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `raids` BIGINT(20) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `raidsloose` BIGINT(20) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `raidswin` BIGINT(20) UNSIGNED DEFAULT '0'",

      "MODIFY COLUMN `register_time` INT(10) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `onlinetime` INT(10) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `news_lastread` INT(10) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `deltime` INT(10) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `banaday` INT(10) UNSIGNED DEFAULT '0'",
      "MODIFY COLUMN `vacation` INT(10) UNSIGNED DEFAULT '0'",
    ), strtoupper($update_tables['users']['xpraid']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_alter_table('users', array(
      "ADD COLUMN `total_rank` INT(10) UNSIGNED NOT NULL DEFAULT 0",
      "ADD COLUMN `total_points` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0",
    ), !isset($update_tables['users']['total_rank']));
    classSupernova::$db->doUpdate("UPDATE {{users}} AS u JOIN {{statpoints}} AS sp ON sp.id_owner = u.id AND sp.stat_code = 1 AND sp.stat_type = 1 SET u.total_rank = sp.total_rank, u.total_points = sp.total_points;");

    upd_alter_table('alliance', array(
      "ADD COLUMN `total_rank` INT(10) UNSIGNED NOT NULL DEFAULT 0",
      "ADD COLUMN `total_points` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0",
    ), !isset($update_tables['alliance']['total_rank']));
    classSupernova::$db->doUpdate("UPDATE {{alliance}} AS a JOIN {{statpoints}} AS sp ON sp.id_owner = a.id AND sp.stat_code = 1 AND sp.stat_type = 2 SET a.total_rank = sp.total_rank, a.total_points = sp.total_points;");

    if(!isset($update_tables['users']['ally_tag'])) {
      upd_alter_table('users', array(
        "ADD COLUMN `ally_tag` varchar(8) DEFAULT NULL AFTER `ally_id`",
      ), !isset($update_tables['users']['ally_tag']));
      classSupernova::$db->doUpdate("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON a.id = u.ally_id SET u.ally_tag = a.ally_tag, u.ally_name = a.ally_name;");
      classSupernova::$db->doUpdate("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON a.id = u.ally_id SET u.ally_id = NULL, u.ally_tag = NULL, u.ally_name = NULL, u.ally_register_time = 0, ally_rank_id = 0 WHERE a.id IS NULL;");
      upd_alter_table('users', array(
        "ADD CONSTRAINT `FK_users_ally_tag` FOREIGN KEY (`ally_tag`) REFERENCES `{{alliance}}` (`ally_tag`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_foreigns['users']['FK_users_ally_tag']);
    }

    upd_alter_table('users', array(
      "ADD COLUMN `player_artifact_list` TEXT",
    ), !isset($update_tables['users']['player_artifact_list']));

    if(!isset($update_tables['users']['player_rpg_tech_xp'])) {
      upd_check_key('eco_scale_storage', 1, !isset(classSupernova::$config->eco_scale_storage));

      upd_alter_table('users', array(
        "ADD COLUMN `player_rpg_tech_level` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `dark_matter`",
        "ADD COLUMN `player_rpg_tech_xp` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `dark_matter`",
      ), !isset($update_tables['users']['player_rpg_tech_xp']));

      classSupernova::$db->doUpdate("UPDATE {{users}} AS u LEFT JOIN {{statpoints}} AS s ON s.id_owner = u.id AND s.stat_type = 1 AND s.stat_code = 1 SET u.player_rpg_tech_xp = s.tech_points;");
    }

    upd_alter_table('planets', array(
      "ADD COLUMN `planet_cargo_hyper` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 AFTER `big_ship_cargo`",
    ), !isset($update_tables['planets']['planet_cargo_hyper']));

    upd_do_query('COMMIT;', true);
    $new_version = 31;

  case 31:
    upd_log_version_update();

    upd_alter_table('aks', array(
      "MODIFY COLUMN `planet_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0",
    ), strtoupper($update_tables['aks']['planet_type']['Type']) != 'TINYINT(1) UNSIGNED');

    upd_alter_table('alliance', array(
      "MODIFY COLUMN `ally_request_notallow` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0",
      "MODIFY COLUMN `ally_owner` BIGINT(20) UNSIGNED DEFAULT NULL",
    ), strtoupper($update_tables['alliance']['ally_owner']['Type']) != 'BIGINT(20) UNSIGNED');

    if(strtoupper($update_tables['alliance_diplomacy']['alliance_diplomacy_ally_id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_alter_table('alliance_diplomacy', array(
        "DROP FOREIGN KEY `FK_diplomacy_ally_id`",
        "DROP FOREIGN KEY `FK_diplomacy_contr_ally_id`"
      ), true);

      upd_alter_table('alliance_diplomacy', array(
        "MODIFY COLUMN `alliance_diplomacy_ally_id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `alliance_diplomacy_contr_ally_id` BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD CONSTRAINT `FK_diplomacy_ally_id`       FOREIGN KEY (`alliance_diplomacy_ally_id`)       REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_diplomacy_contr_ally_id` FOREIGN KEY (`alliance_diplomacy_contr_ally_id`) REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['alliance_negotiation']['alliance_negotiation_ally_id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_alter_table('alliance_negotiation', array(
        "DROP FOREIGN KEY `FK_negotiation_ally_id`",
        "DROP FOREIGN KEY `FK_negotiation_contr_ally_id`"
      ), true);

      upd_alter_table('alliance_negotiation', array(
        "MODIFY COLUMN `alliance_negotiation_status` TINYINT(1) NOT NULL DEFAULT 0",
        "MODIFY COLUMN `alliance_negotiation_ally_id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `alliance_negotiation_contr_ally_id` BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD CONSTRAINT `FK_negotiation_ally_id`       FOREIGN KEY (`alliance_negotiation_ally_id`)       REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_negotiation_contr_ally_id` FOREIGN KEY (`alliance_negotiation_contr_ally_id`) REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['alliance_requests']['id_user']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{alliance_requests}} WHERE id_user NOT IN (SELECT id FROM {{users}}) OR id_ally NOT IN (SELECT id FROM {{alliance}});', true);

      upd_alter_table('alliance_requests', array(
        "MODIFY COLUMN `id_user` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `id_ally` BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_alliance_requests_id_ally` (`id_ally`, `id_user`)",

        "ADD CONSTRAINT `FK_alliance_request_user_id` FOREIGN KEY (`id_user`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_alliance_request_ally_id` FOREIGN KEY (`id_ally`) REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['annonce']['id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{annonce}} WHERE USER NOT IN (SELECT username FROM {{users}});', true);

      upd_alter_table('annonce', array(
        "MODIFY COLUMN `id` SERIAL",
        "MODIFY COLUMN `user` VARCHAR(64) DEFAULT NULL",

        "ADD KEY `I_annonce_user` (`user`, `id`)",

        "ADD CONSTRAINT `FK_annonce_user` FOREIGN KEY (`user`) REFERENCES `{{users}}` (`username`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['bashing']['bashing_user_id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_alter_table('bashing', array(
        "DROP FOREIGN KEY `FK_bashing_user_id`",
        "DROP FOREIGN KEY `FK_bashing_planet_id`",
      ), true);

      upd_alter_table('bashing', array(
        "MODIFY COLUMN `bashing_user_id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `bashing_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD CONSTRAINT `FK_bashing_user_id`   FOREIGN KEY (`bashing_user_id`)   REFERENCES `{{users}}`   (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['buddy']['id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{buddy}} WHERE sender NOT IN (SELECT id FROM {{users}}) OR OWNER NOT IN (SELECT id FROM {{users}});', true);

      upd_alter_table('buddy', array(
        "MODIFY COLUMN `id` SERIAL",
        "MODIFY COLUMN `sender` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `owner` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0",

        "ADD KEY `I_buddy_sender` (`sender`)",
        "ADD KEY `I_buddy_owner` (`owner`)",

        "ADD CONSTRAINT `FK_buddy_sender_id` FOREIGN KEY (`sender`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_buddy_owner_id`  FOREIGN KEY (`owner`)  REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('chat', array(
      "MODIFY COLUMN `messageid` SERIAL",
    ), strtoupper($update_tables['chat']['messageid']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_alter_table('counter', array(
      "CHANGE COLUMN `id` `counter_id` SERIAL",

      "MODIFY COLUMN `user_id` BIGINT(20) UNSIGNED DEFAULT 0",

      "ADD COLUMN `user_name` VARCHAR(64) DEFAULT '' AFTER `user_id`",

      "ADD KEY `I_counter_user_name` (`user_name`)",
    ), strtoupper($update_tables['counter']['counter_id']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_alter_table('fleets', array(
      "MODIFY COLUMN `fleet_id` SERIAL",
      "MODIFY COLUMN `fleet_resource_metal` DECIMAL(65,0) DEFAULT '0'",
      "MODIFY COLUMN `fleet_resource_crystal` DECIMAL(65,0) DEFAULT '0'",
      "MODIFY COLUMN `fleet_resource_deuterium` DECIMAL(65,0) DEFAULT '0'",
    ), strtoupper($update_tables['fleets']['fleet_resource_metal']['Type']) != 'DECIMAL(65,0)');

    if(strtoupper($update_tables['iraks']['fleet_owner']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{iraks}} WHERE OWNER NOT IN (SELECT id FROM {{users}}) OR zielid NOT IN (SELECT id FROM {{users}});', true);

      upd_alter_table('iraks', array(
        "CHANGE COLUMN `zeit` `fleet_end_time` INT(11) UNSIGNED NOT NULL DEFAULT 0",
        "CHANGE COLUMN `zielid` `fleet_target_owner` BIGINT(20) UNSIGNED DEFAULT NULL",
        "CHANGE COLUMN `owner` `fleet_owner` BIGINT(20) UNSIGNED DEFAULT NULL",
        "CHANGE COLUMN `anzahl` `fleet_amount` BIGINT(20) UNSIGNED DEFAULT 0",
        "CHANGE COLUMN `galaxy_angreifer` `fleet_start_galaxy` INT(2) UNSIGNED DEFAULT 0",
        "CHANGE COLUMN `system_angreifer` `fleet_start_system` INT(4) UNSIGNED DEFAULT 0",
        "CHANGE COLUMN `planet_angreifer` `fleet_start_planet` INT(2) UNSIGNED DEFAULT 0",

        "CHANGE COLUMN `galaxy` `fleet_end_galaxy` INT(2) UNSIGNED DEFAULT 0",
        "CHANGE COLUMN `system` `fleet_end_system` INT(4) UNSIGNED DEFAULT 0",
        "CHANGE COLUMN `planet` `fleet_end_planet` INT(2) UNSIGNED DEFAULT 0",

        "ADD KEY `I_iraks_fleet_owner` (`fleet_owner`)",
        "ADD KEY `I_iraks_fleet_target_owner` (`fleet_target_owner`)",

        "ADD CONSTRAINT `FK_iraks_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_iraks_fleet_target_owner` FOREIGN KEY (`fleet_target_owner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['notes']['owner']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{notes}} WHERE OWNER NOT IN (SELECT id FROM {{users}});', true);

      upd_alter_table('notes', array(
        "MODIFY COLUMN id SERIAL",
        "MODIFY COLUMN `owner` BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_notes_owner` (`owner`)",

        "ADD CONSTRAINT `FK_notes_owner` FOREIGN KEY (`owner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('planets', array(
      "MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT",
      "MODIFY COLUMN `name` VARCHAR(64) DEFAULT 'Planet' NOT NULL",
      "MODIFY COLUMN `id_owner` BIGINT(20) UNSIGNED DEFAULT NULL",
      "MODIFY COLUMN `galaxy` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `system` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `planet` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `planet_type` TINYINT NOT NULL DEFAULT '1'",

      "MODIFY COLUMN `metal` DECIMAL(65,5) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `crystal` DECIMAL(65,5) NOT NULL DEFAULT '0' AFTER `metal`",
      "MODIFY COLUMN `deuterium` DECIMAL(65,5) NOT NULL DEFAULT '0' AFTER `crystal`",
      "MODIFY COLUMN `energy_max` DECIMAL(65,0) NOT NULL DEFAULT '0' AFTER `deuterium`",
      "MODIFY COLUMN `energy_used` DECIMAL(65,0) NOT NULL DEFAULT '0' AFTER `energy_max`",

      "MODIFY COLUMN `metal_mine` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `crystal_mine` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `deuterium_sintetizer` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `solar_plant` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `fusion_plant` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `robot_factory` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `nano_factory` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `hangar` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `metal_store` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `crystal_store` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `deuterium_store` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `laboratory` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `nano` SMALLINT DEFAULT '0' AFTER `laboratory`",
      "MODIFY COLUMN `terraformer` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `ally_deposit` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `silo` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mondbasis` SMALLINT NOT NULL DEFAULT '0' AFTER `silo`",
      "MODIFY COLUMN `phalanx` SMALLINT NOT NULL DEFAULT '0' AFTER `mondbasis`",
      "MODIFY COLUMN `sprungtor` SMALLINT NOT NULL DEFAULT '0' AFTER `phalanx`",
      "MODIFY COLUMN `last_jump_time` int(11) NOT NULL DEFAULT '0' AFTER `sprungtor`",

      "MODIFY COLUMN `small_ship_cargo` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `big_ship_cargo` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `supercargo` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Supercargo ship count' AFTER `big_ship_cargo`",
      "MODIFY COLUMN `planet_cargo_hyper` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `supercargo`",
      "MODIFY COLUMN `recycler` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `planet_cargo_hyper`",
      "MODIFY COLUMN `colonizer` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `recycler`",
      "MODIFY COLUMN `spy_sonde` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `colonizer`",
      "MODIFY COLUMN `solar_satelit` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `spy_sonde`",

      "MODIFY COLUMN `light_hunter` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `heavy_hunter` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `crusher` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `battle_ship` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `bomber_ship` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `battleship` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0' AFTER `bomber_ship`",
      "MODIFY COLUMN `destructor` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `dearth_star` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `supernova` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",

      "MODIFY COLUMN `misil_launcher` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `small_laser` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `big_laser` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `gauss_canyon` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `ionic_canyon` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `buster_canyon` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",

      "MODIFY COLUMN `small_protection_shield` tinyint(1) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `big_protection_shield` tinyint(1) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `planet_protector` tinyint(1) NOT NULL DEFAULT '0'",

      "MODIFY COLUMN `interceptor_misil` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `interplanetary_misil` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'",

      "MODIFY COLUMN `metal_perhour` INT NOT NULL DEFAULT '0' AFTER `interplanetary_misil`",
      "MODIFY COLUMN `crystal_perhour` INT NOT NULL DEFAULT '0' AFTER `metal_perhour`",
      "MODIFY COLUMN `deuterium_perhour` INT NOT NULL DEFAULT '0' AFTER `crystal_perhour`",

      "MODIFY COLUMN `metal_mine_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",
      "MODIFY COLUMN `crystal_mine_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",
      "MODIFY COLUMN `deuterium_sintetizer_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",
      "MODIFY COLUMN `solar_plant_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",
      "MODIFY COLUMN `fusion_plant_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",
      "MODIFY COLUMN `solar_satelit_porcent` TINYINT UNSIGNED NOT NULL DEFAULT '10'",

      "MODIFY COLUMN `que` TEXT COMMENT 'Planet que' AFTER `solar_satelit_porcent`",
//      "MODIFY COLUMN `b_tech` INT(11) NOT NULL DEFAULT 0 AFTER `que`",
//      "MODIFY COLUMN `b_tech_id` SMALLINT NOT NULL DEFAULT 0 AFTER `b_tech`",
      "MODIFY COLUMN `b_hangar` INT(11) NOT NULL DEFAULT '0' AFTER `que`",
      "MODIFY COLUMN `b_hangar_id` TEXT AFTER `b_hangar`",
      "MODIFY COLUMN `last_update` INT(11) DEFAULT NULL AFTER `b_hangar_id`",

      "MODIFY COLUMN `image` varchar(64) NOT NULL DEFAULT 'normaltempplanet01' AFTER `last_update`",
      "MODIFY COLUMN `points` bigint(20) DEFAULT '0' AFTER `image`",
      "MODIFY COLUMN `ranks` bigint(20) DEFAULT '0' AFTER `points`",
      "MODIFY COLUMN `id_level` TINYINT NOT NULL DEFAULT '0' AFTER `ranks`",
      "MODIFY COLUMN `destruyed` int(11) NOT NULL DEFAULT '0' AFTER `id_level`",
      "MODIFY COLUMN `diameter` int(11) NOT NULL DEFAULT '12800' AFTER `destruyed`",
      "MODIFY COLUMN `field_max` SMALLINT UNSIGNED NOT NULL DEFAULT '163' AFTER `diameter`",
      "MODIFY COLUMN `field_current` SMALLINT UNSIGNED NOT NULL DEFAULT '0' AFTER `field_max`",
      "MODIFY COLUMN `temp_min` SMALLINT NOT NULL DEFAULT '0' AFTER `field_current`",
      "MODIFY COLUMN `temp_max` SMALLINT NOT NULL DEFAULT '40' AFTER `temp_min`",

      "MODIFY COLUMN `metal_max` DECIMAL(65,0) DEFAULT '100000' AFTER `temp_max`",
      "MODIFY COLUMN `crystal_max` DECIMAL(65,0) DEFAULT '100000' AFTER `metal_max`",
      "MODIFY COLUMN `deuterium_max` DECIMAL(65,0) DEFAULT '100000' AFTER `crystal_max`",

      "MODIFY COLUMN `debris_metal` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `debris_crystal` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `PLANET_GOVERNOR_ID` SMALLINT NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `PLANET_GOVERNOR_LEVEL` SMALLINT NOT NULL DEFAULT '0'",

      "MODIFY COLUMN `parent_planet` BIGINT(20) unsigned DEFAULT '0'",

      "DROP COLUMN `b_hangar_plus`",
    ), isset($update_tables['planets']['b_hangar_plus']));

    if(strtoupper($update_tables['referrals']['id_partner']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{referrals}} WHERE id NOT IN (SELECT id FROM {{users}}) OR id_partner NOT IN (SELECT id FROM {{users}});', true);

      upd_alter_table('referrals', array(
        "MODIFY COLUMN `id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `id_partner` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `dark_matter` DECIMAL(65,0) NOT NULL DEFAULT '0'",

        "ADD CONSTRAINT `FK_referrals_id` FOREIGN KEY (`id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_referrals_id_partner` FOREIGN KEY (`id_partner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('rw', array(
      "MODIFY COLUMN `report_id` SERIAL",
      "MODIFY COLUMN `id_owner1` BIGINT(20) UNSIGNED",
      "MODIFY COLUMN `id_owner2` BIGINT(20) UNSIGNED",
    ), strtoupper($update_tables['rw']['id_owner1']['Type']) != 'BIGINT(20) UNSIGNED');

    if(strtoupper($update_tables['shortcut']['shortcut_user_id']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{shortcut}} WHERE shortcut_user_id NOT IN (SELECT id FROM {{users}}) OR shortcut_planet_id NOT IN (SELECT id FROM {{planets}});', true);

      upd_alter_table('shortcut', array(
        "MODIFY COLUMN `shortcut_id` SERIAL",
        "MODIFY COLUMN `shortcut_user_id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `shortcut_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `shortcut_galaxy` TINYINT UNSIGNED DEFAULT 0",
        "MODIFY COLUMN `shortcut_system` SMALLINT UNSIGNED DEFAULT 0",
        "MODIFY COLUMN `shortcut_planet` TINYINT UNSIGNED DEFAULT 0",

        "ADD CONSTRAINT `FK_shortcut_planet_id` FOREIGN KEY (`shortcut_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    if(strtoupper($update_tables['statpoints']['id_owner']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_do_query('DELETE FROM {{statpoints}} WHERE id_owner NOT IN (SELECT id FROM {{users}}) OR id_ally NOT IN (SELECT id FROM {{alliance}});', true);

      upd_alter_table('statpoints', array(
        "MODIFY COLUMN `stat_date` int(11) NOT NULL DEFAULT '0' FIRST",
        "MODIFY COLUMN `id_owner` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `id_ally` BIGINT(20) UNSIGNED DEFAULT NULL",
        "MODIFY COLUMN `stat_type` TINYINT UNSIGNED DEFAULT 0",
        "MODIFY COLUMN `stat_code` TINYINT UNSIGNED NOT NULL DEFAULT '0'",

        "MODIFY COLUMN `tech_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `tech_old_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `tech_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `tech_count` DECIMAL(65,0) UNSIGNED UNSIGNED NOT NULL DEFAULT '0'",

        "MODIFY COLUMN `build_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `build_old_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `build_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `build_count` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",

        "MODIFY COLUMN `defs_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `defs_old_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `defs_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `defs_count` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",

        "MODIFY COLUMN `fleet_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `fleet_old_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `fleet_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `fleet_count` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",

        "MODIFY COLUMN `res_rank` INT(11) UNSIGNED DEFAULT '0' COMMENT 'Rank by resources' AFTER `fleet_count`",
        "MODIFY COLUMN `res_old_rank` INT(11) UNSIGNED DEFAULT '0' COMMENT 'Old rank by resources'AFTER `res_rank`",
        "MODIFY COLUMN `res_points` DECIMAL(65,0) UNSIGNED DEFAULT '0' COMMENT 'Resource stat points' AFTER `res_old_rank`",
        "MODIFY COLUMN `res_count` DECIMAL(65,0) UNSIGNED DEFAULT '0' COMMENT 'Resource count' AFTER `res_points`",

        "MODIFY COLUMN `total_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `total_old_rank` INT(11) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `total_points` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",
        "MODIFY COLUMN `total_count` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT '0'",

        "ADD KEY `I_stats_id_ally` (`id_ally`)",

        "ADD CONSTRAINT `FK_stats_id_owner` FOREIGN KEY (`id_owner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('users', array(
      "MODIFY COLUMN `authlevel` tinyint unsigned NOT NULL DEFAULT '0' AFTER `username`",
      "MODIFY COLUMN `vacation` int(11) unsigned DEFAULT '0' AFTER `authlevel`",
      "MODIFY COLUMN `banaday` int(11) unsigned DEFAULT '0' AFTER `vacation`",
      "MODIFY COLUMN `dark_matter` bigint(20) DEFAULT '0' AFTER `banaday`",
      "MODIFY COLUMN `spy_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `computer_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `military_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `defence_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `shield_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `energy_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `hyperspace_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `combustion_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `impulse_motor_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `hyperspace_motor_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `laser_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `ionic_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `buster_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `intergalactic_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `expedition_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `colonisation_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `graviton_tech` SMALLINT UNSIGNED NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `player_artifact_list` text AFTER `graviton_tech`",
      "MODIFY COLUMN `ally_id` bigint(20) unsigned DEFAULT NULL AFTER `player_artifact_list`",
      "MODIFY COLUMN `ally_tag` varchar(8) DEFAULT NULL AFTER `ally_id`",
      "MODIFY COLUMN `ally_name` varchar(32) DEFAULT NULL AFTER `ally_tag`",
      "MODIFY COLUMN `ally_register_time` int(11) NOT NULL DEFAULT '0' AFTER `ally_name`",
      "MODIFY COLUMN `ally_rank_id` int(11) NOT NULL DEFAULT '0' AFTER `ally_register_time`",
      "MODIFY COLUMN `player_que` text AFTER `ally_rank_id`",
      "MODIFY COLUMN `lvl_minier` bigint(20) unsigned NOT NULL DEFAULT '1'",
      "MODIFY COLUMN `xpminier` bigint(20) unsigned DEFAULT '0' AFTER `lvl_minier`",
      "MODIFY COLUMN `player_rpg_tech_xp` bigint(20) unsigned NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `player_rpg_tech_level` bigint(20) unsigned NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `lvl_raid` bigint(20) unsigned NOT NULL DEFAULT '1' AFTER `player_rpg_tech_level`",
      "MODIFY COLUMN `xpraid` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `raids` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `raidsloose` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `raidswin` bigint(20) unsigned DEFAULT '0'",
      "MODIFY COLUMN `new_message` int(11) NOT NULL DEFAULT '0' AFTER `raidswin`",
      "MODIFY COLUMN `mnl_alliance` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_joueur` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_attaque` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_spy` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_exploit` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_transport` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_expedition` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `mnl_buildlist` int(11) NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `msg_admin` bigint(11) unsigned DEFAULT '0'",
//      "MODIFY COLUMN `b_tech_planet` int(11) NOT NULL DEFAULT '0' AFTER `msg_admin`",
      "MODIFY COLUMN `deltime` int(10) unsigned DEFAULT '0'",
      "MODIFY COLUMN `news_lastread` int(10) unsigned DEFAULT '0'",
      "MODIFY COLUMN `total_rank` int(10) unsigned NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `total_points` bigint(20) unsigned NOT NULL DEFAULT '0'",
      "MODIFY COLUMN `password` varchar(64) NOT NULL DEFAULT '' AFTER `total_points`",
      "MODIFY COLUMN `email` varchar(64) NOT NULL DEFAULT '' AFTER `password`",
      "MODIFY COLUMN `email_2` varchar(64) NOT NULL DEFAULT '' AFTER `email`",
      "MODIFY COLUMN `lang` varchar(8) NOT NULL DEFAULT 'ru' AFTER `email_2`",
      "MODIFY COLUMN `sex` char(1) DEFAULT NULL AFTER `lang`",
      "MODIFY COLUMN `avatar` varchar(255) NOT NULL DEFAULT '' AFTER `sex`",
      "MODIFY COLUMN `sign` mediumtext AFTER `avatar`",
      "MODIFY COLUMN `id_planet` int(11) NOT NULL DEFAULT '0' AFTER `sign`",
      "MODIFY COLUMN `galaxy` int(11) NOT NULL DEFAULT '0' AFTER `id_planet`",
      "MODIFY COLUMN `system` int(11) NOT NULL DEFAULT '0' AFTER `galaxy`",
      "MODIFY COLUMN `planet` int(11) NOT NULL DEFAULT '0' AFTER `system`",
      "MODIFY COLUMN `current_planet` int(11) NOT NULL DEFAULT '0' AFTER `planet`",
      "MODIFY COLUMN `user_agent` mediumtext NOT NULL AFTER `current_planet`",
      "MODIFY COLUMN `user_lastip` varchar(250) DEFAULT NULL COMMENT 'User last IP' AFTER `user_agent`",
      "MODIFY COLUMN `user_proxy` varchar(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)' AFTER `user_lastip`",
      "MODIFY COLUMN `register_time` int(10) unsigned DEFAULT '0' AFTER `user_proxy`",
      "MODIFY COLUMN `onlinetime` int(10) unsigned DEFAULT '0' AFTER `register_time`",
      "MODIFY COLUMN `dpath` varchar(255) NOT NULL DEFAULT '' AFTER `onlinetime`",
      "MODIFY COLUMN `design` tinyint(4) unsigned NOT NULL DEFAULT '1' AFTER `dpath`",
      "MODIFY COLUMN `noipcheck` tinyint(4) unsigned NOT NULL DEFAULT '1' AFTER `design`",
      "MODIFY COLUMN `options` mediumtext COMMENT 'Packed user options' AFTER `noipcheck`",
      "MODIFY COLUMN `planet_sort` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `options`",
      "MODIFY COLUMN `planet_sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `planet_sort`",
      "MODIFY COLUMN `spio_anz` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `planet_sort_order`",
      "MODIFY COLUMN `settings_tooltiptime` tinyint(1) unsigned NOT NULL DEFAULT '5' AFTER `spio_anz`",
      "MODIFY COLUMN `settings_fleetactions` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `settings_tooltiptime`",
      "MODIFY COLUMN `settings_esp` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `settings_allylogo`",
      "MODIFY COLUMN `settings_wri` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `settings_esp`",
      "MODIFY COLUMN `settings_bud` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `settings_wri`",
      "MODIFY COLUMN `settings_mis` tinyint(1) unsigned NOT NULL DEFAULT '1' AFTER `settings_bud`",
      "MODIFY COLUMN `settings_rep` tinyint(1) unsigned NOT NULL DEFAULT '0' AFTER `settings_mis`",
    ), strtoupper($update_tables['users']['id_owner']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_do_query('COMMIT;', true);
    $new_version = 32;

  case 32:
    upd_log_version_update();

    upd_check_key('avatar_max_width', 128, !isset(classSupernova::$config->avatar_max_width));
    upd_check_key('avatar_max_height', 128, !isset(classSupernova::$config->avatar_max_height));

    upd_alter_table('users', array(
      "MODIFY COLUMN `avatar` tinyint(1) unsigned NOT NULL DEFAULT '0'",
    ), strtoupper($update_tables['users']['avatar']['Type']) != 'TINYINT(1) UNSIGNED');

    upd_alter_table('alliance', array(
      "MODIFY COLUMN `ally_image` tinyint(1) unsigned NOT NULL DEFAULT '0'",
    ), strtoupper($update_tables['alliance']['ally_image']['Type']) != 'TINYINT(1) UNSIGNED');

    upd_alter_table('users', array(
      "DROP COLUMN `settings_allylogo`",
    ), isset($update_tables['users']['settings_allylogo']));

    if(!isset($update_tables['powerup'])) {
      upd_do_query("DROP TABLE IF EXISTS {{mercenaries}};");

      upd_create_table('powerup',
        "(
          `powerup_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `powerup_user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
          `powerup_planet_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
          `powerup_category` SMALLINT NOT NULL DEFAULT 0,
          `powerup_unit_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
          `powerup_unit_level` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `powerup_time_start` int(11) NOT NULL DEFAULT '0',
          `powerup_time_finish` int(11) NOT NULL DEFAULT '0',

          PRIMARY KEY (`powerup_id`),
          KEY `I_powerup_user_id` (`powerup_user_id`),
          KEY `I_powerup_planet_id` (`powerup_planet_id`),
          KEY `I_user_powerup_time` (`powerup_user_id`, `powerup_unit_id`, `powerup_time_start`, `powerup_time_finish`),
          KEY `I_planet_powerup_time` (`powerup_planet_id`, `powerup_unit_id`, `powerup_time_start`, `powerup_time_finish`),

          CONSTRAINT `FK_powerup_user_id` FOREIGN KEY (`powerup_user_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_powerup_planet_id` FOREIGN KEY (`powerup_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('empire_mercenary_temporary', 0, !isset(classSupernova::$config->empire_mercenary_temporary));
      upd_check_key('empire_mercenary_base_period', PERIOD_MONTH, !isset(classSupernova::$config->empire_mercenary_base_period));

      $update_query_template = "UPDATE {{users}} SET id = id %s WHERE id = %d LIMIT 1;";
      $user_list = upd_do_query("SELECT * FROM {{users}};");
      while($user_row = db_fetch($user_list)) {
        $update_query_str = '';
        foreach(sn_get_groups('mercenaries') as $mercenary_id) {
          $mercenary_data_name = get_unit_param($mercenary_id, P_NAME);
          if($mercenary_level = $user_row[$mercenary_data_name]) {
            $update_query_str = ", `{$mercenary_data_name}` = 0";
            upd_do_query("DELETE FROM {{powerup}} WHERE powerup_user_id = {$user_row['id']} AND powerup_unit_id = {$mercenary_id} LIMIT 1;");
            upd_do_query("INSERT {{powerup}} SET powerup_user_id = {$user_row['id']}, powerup_unit_id = {$mercenary_id}, powerup_unit_level = {$mercenary_level};");
          }
        }

        if($update_query_str) {
          upd_do_query(sprintf($update_query_template, $update_query_str, $user_row['id']));
        }
      }
    }

    if(!isset($update_tables['universe'])) {
      upd_create_table('universe',
        "(
          `universe_galaxy` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `universe_system` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `universe_name` varchar(32) NOT NULL DEFAULT '',
          `universe_price` bigint(20) NOT NULL DEFAULT 0,

          PRIMARY KEY (`universe_galaxy`, `universe_system`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('uni_price_galaxy', 10000, !isset(classSupernova::$config->uni_price_galaxy));
      upd_check_key('uni_price_system', 1000, !isset(classSupernova::$config->uni_price_system));
    }

    // ========================================================================
    // Ally player
    // Adding config variable
    upd_check_key('ali_bonus_members', 10, !isset(classSupernova::$config->ali_bonus_members));

    // ------------------------------------------------------------------------
    // Modifying tables
    if(strtoupper($update_tables['users']['user_as_ally']['Type']) != 'BIGINT(20) UNSIGNED') {
      upd_alter_table('users', array(
        "ADD COLUMN user_as_ally BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_user_user_as_ally` (`user_as_ally`)",

        "ADD CONSTRAINT `FK_user_user_as_ally` FOREIGN KEY (`user_as_ally`) REFERENCES `{{alliance}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);

      upd_alter_table('alliance', array(
        "ADD COLUMN ally_user_id BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_ally_user_id` (`ally_user_id`)",

        "ADD CONSTRAINT `FK_ally_ally_user_id` FOREIGN KEY (`ally_user_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    // ------------------------------------------------------------------------
    // Creating players for allies
    $ally_row_list = doquery("SELECT `id`, `ally_tag` FROM {{alliance}} WHERE ally_user_id IS NULL;");
    while($ally_row = db_fetch($ally_row_list)) {
      $ally_user_name = db_escape("[{$ally_row['ally_tag']}]");
      doquery("INSERT INTO {{users}} SET `username` = '{$ally_user_name}', `register_time` = " . SN_TIME_NOW . ", `user_as_ally` = {$ally_row['id']};");
      $ally_user_id = classSupernova::$db->db_insert_id();
      classSupernova::$db->doUpdate("UPDATE {{alliance}} SET ally_user_id = {$ally_user_id} WHERE id = {$ally_row['id']} LIMIT 1;");
    }
    // Renaming old ally players TODO: Remove on release
    upd_do_query("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON u.user_as_ally = a.id SET u.username = CONCAT('[', a.ally_tag, ']') WHERE u.user_as_ally IS NOT NULL AND u.username = '';");
    // Setting last online time to old ally players TODO: Remove on release
    upd_do_query("UPDATE {{users}} SET `onlinetime` = " . SN_TIME_NOW . " WHERE onlinetime = 0;");

    // ------------------------------------------------------------------------
    // Creating planets for allies
    $ally_user_list = doquery("SELECT `id`, `username` FROM {{users}} WHERE `user_as_ally` IS NOT NULL AND `id_planet` = 0;");
    while($ally_user_row = db_fetch($ally_user_list)) {
      $ally_planet_name = db_escape($ally_user_row['username']);
      doquery("INSERT INTO {{planets}} SET `name` = '{$ally_planet_name}', `last_update` = " . SN_TIME_NOW . ", `id_owner` = {$ally_user_row['id']};");
      $ally_planet_id = classSupernova::$db->db_insert_id();
      classSupernova::$db->doUpdate("UPDATE {{users}} SET `id_planet` = {$ally_planet_id} WHERE `id` = {$ally_user_row['id']} LIMIT 1;");
    }

    upd_do_query("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON u.ally_id = a.id SET u.ally_name = a.ally_name, u.ally_tag = a.ally_tag WHERE u.ally_id IS NOT NULL;");

    upd_alter_table('users', array(
      "DROP COLUMN `rpg_amiral`",
      "DROP COLUMN `mrc_academic`",
      "DROP COLUMN `rpg_espion`",
      "DROP COLUMN `rpg_commandant`",
      "DROP COLUMN `rpg_stockeur`",
      "DROP COLUMN `rpg_destructeur`",
      "DROP COLUMN `rpg_general`",
      "DROP COLUMN `rpg_raideur`",
      "DROP COLUMN `rpg_empereur`",

      "ADD COLUMN `metal` decimal(65,5) NOT NULL DEFAULT '0.00000'",
      "ADD COLUMN `crystal` decimal(65,5) NOT NULL DEFAULT '0.00000'",
      "ADD COLUMN `deuterium` decimal(65,5) NOT NULL DEFAULT '0.00000'",
    ), $update_tables['users']['rpg_amiral']);


    // ========================================================================
    // User que
    // Adding db field
    upd_alter_table('users', "ADD `que` varchar(4096) NOT NULL DEFAULT '' COMMENT 'User que'", !$update_tables['users']['que']);
    // Converting old data to new one and dropping old fields
    if($update_tables['users']['b_tech_planet']) {
      $query = doquery("SELECT * FROM {{planets}} WHERE `b_tech_id` <> 0;");
      while($planet_row = db_fetch($query)) {
        $que_item_string = "{$planet_row['b_tech_id']},1," . max(0, $planet_row['b_tech'] - SN_TIME_NOW) . "," . BUILD_CREATE . "," . QUE_RESEARCH;
        classSupernova::$db->doUpdate("UPDATE {{users}} SET `que` = '{$que_item_string}' WHERE `id` = {$planet_row['id_owner']} LIMIT 1;");
      }

      upd_alter_table('planets', array(
        "DROP COLUMN `b_tech`",
        "DROP COLUMN `b_tech_id`",
      ), $update_tables['planets']['b_tech']);

      upd_alter_table('users', "DROP COLUMN `b_tech_planet`", $update_tables['users']['b_tech_planet']);
    }

    if(!$update_tables['powerup']['powerup_category']) {
      upd_alter_table('powerup', "ADD COLUMN `powerup_category` SMALLINT NOT NULL DEFAULT 0 AFTER `powerup_planet_id`", !$update_tables['powerup']['powerup_category']);

      classSupernova::$db->doUpdate("UPDATE {{powerup}} SET powerup_category = " . BONUS_MERCENARY);
    }

    upd_check_key('rpg_cost_info', 10000, !isset(classSupernova::$config->rpg_cost_info));
    upd_check_key('tpl_minifier', 0, !isset(classSupernova::$config->tpl_minifier));

    upd_check_key('server_updater_check_auto', 0, !isset(classSupernova::$config->server_updater_check_auto));
    upd_check_key('server_updater_check_period', PERIOD_DAY, !isset(classSupernova::$config->server_updater_check_period));
    upd_check_key('server_updater_check_last', 0, !isset(classSupernova::$config->server_updater_check_last));
    upd_check_key('server_updater_check_result', SNC_VER_NEVER, !isset(classSupernova::$config->server_updater_check_result));
    upd_check_key('server_updater_key', '', !isset(classSupernova::$config->server_updater_key));
    upd_check_key('server_updater_id', 0, !isset(classSupernova::$config->server_updater_id));

    upd_check_key('ali_bonus_algorithm', 0, !isset(classSupernova::$config->ali_bonus_algorithm));
    upd_check_key('ali_bonus_divisor', 10000000, !isset(classSupernova::$config->ali_bonus_divisor));
    upd_check_key('ali_bonus_brackets', 10, !isset(classSupernova::$config->ali_bonus_brackets));
    upd_check_key('ali_bonus_brackets_divisor', 50, !isset(classSupernova::$config->ali_bonus_brackets_divisor));

    if(!classSupernova::$config->db_loadItem('rpg_flt_explore')) {
      $inflation_rate = 1000;

      classSupernova::$config->db_saveItem('rpg_cost_banker', classSupernova::$config->rpg_cost_banker * $inflation_rate);
      classSupernova::$config->db_saveItem('rpg_cost_exchange', classSupernova::$config->rpg_cost_exchange * $inflation_rate);
      classSupernova::$config->db_saveItem('rpg_cost_pawnshop', classSupernova::$config->rpg_cost_pawnshop * $inflation_rate);
      classSupernova::$config->db_saveItem('rpg_cost_scraper', classSupernova::$config->rpg_cost_scraper * $inflation_rate);
      classSupernova::$config->db_saveItem('rpg_cost_stockman', classSupernova::$config->rpg_cost_stockman * $inflation_rate);
      classSupernova::$config->db_saveItem('rpg_cost_trader', classSupernova::$config->rpg_cost_trader * $inflation_rate);

      classSupernova::$config->db_saveItem('rpg_exchange_darkMatter', classSupernova::$config->rpg_exchange_darkMatter / $inflation_rate * 4);

      classSupernova::$config->db_saveItem('rpg_flt_explore', $inflation_rate);

      classSupernova::$db->doUpdate("UPDATE {{users}} SET `dark_matter` = `dark_matter` * {$inflation_rate};");

      $query = doquery("SELECT * FROM {{quest}}");
      while($row = db_fetch($query)) {
        $query_add = '';
        $quest_reward_list = explode(';', $row['quest_rewards']);
        foreach($quest_reward_list as &$quest_reward) {
          list($reward_resource, $reward_amount) = explode(',', $quest_reward);
          if($reward_resource == RES_DARK_MATTER) {
            $quest_reward = "{$reward_resource}," . $reward_amount * 1000;
          }
        }
        $new_rewards = implode(';', $quest_reward_list);
        if($new_rewards != $row['quest_rewards']) {
          classSupernova::$db->doUpdate("UPDATE {{quest}} SET `quest_rewards` = '{$new_rewards}' WHERE quest_id = {$row['quest_id']} LIMIT 1;");
        }
      }
    }

    upd_check_key('rpg_bonus_minimum', 10000, !isset(classSupernova::$config->rpg_bonus_minimum));
    upd_check_key('rpg_bonus_divisor',
      !isset(classSupernova::$config->rpg_bonus_divisor) ? 10 : (classSupernova::$config->rpg_bonus_divisor >= 1000 ? floor(classSupernova::$config->rpg_bonus_divisor / 1000) : classSupernova::$config->rpg_bonus_divisor),
      !isset(classSupernova::$config->rpg_bonus_divisor) || classSupernova::$config->rpg_bonus_divisor >= 1000);

    upd_check_key('var_news_last', 0, !isset(classSupernova::$config->var_news_last));

    upd_do_query('COMMIT;', true);
    $new_version = 33;

  case 33:
    upd_log_version_update();

    upd_alter_table('users', array(
      "ADD `user_birthday` DATE DEFAULT NULL COMMENT 'User birthday'",
      "ADD `user_birthday_celebrated` DATE DEFAULT NULL COMMENT 'Last time where user got birthday gift'",

      "ADD KEY `I_user_birthday` (`user_birthday`, `user_birthday_celebrated`)",
    ), !$update_tables['users']['user_birthday']);

    upd_check_key('user_birthday_gift', 0, !isset(classSupernova::$config->user_birthday_gift));
    upd_check_key('user_birthday_range', 30, !isset(classSupernova::$config->user_birthday_range));
    upd_check_key('user_birthday_celebrate', 0, !isset(classSupernova::$config->user_birthday_celebrate));

    if(!isset($update_tables['payment'])) {
      upd_alter_table('users', array(
        "ADD KEY `I_user_id_name` (`id`, `username`)",
      ), !$update_indexes['users']['I_user_id_name']);

      upd_create_table('payment',
        "(
          `payment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Internal payment ID',
          `payment_user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
          `payment_user_name` VARCHAR(64) DEFAULT NULL,
          `payment_amount` DECIMAL(60,5) DEFAULT 0 COMMENT 'Amount paid',
          `payment_currency` VARCHAR(3) DEFAULT '' COMMENT 'Payment currency',
          `payment_dm` DECIMAL(65,0) DEFAULT 0 COMMENT 'DM gained',
          `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Payment server timestamp',
          `payment_comment` TEXT COMMENT 'Payment comment',

          `payment_module_name` VARCHAR(255) DEFAULT '' COMMENT 'Payment module name',
          `payment_internal_id` VARCHAR(255) DEFAULT '' COMMENT 'Internal payment ID in payment system',
          `payment_internal_date` DATETIME COMMENT 'Internal payment timestamp in payment system',

          PRIMARY KEY (`payment_id`),
          KEY `I_payment_user` (`payment_user_id`, `payment_user_name`),
          KEY `I_payment_module_internal_id` (`payment_module_name`, `payment_internal_id`),

          CONSTRAINT `FK_payment_user` FOREIGN KEY (`payment_user_id`, `payment_user_name`) REFERENCES `{{users}}` (`id`, `username`) ON UPDATE CASCADE ON DELETE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('payment_currency_default', 'UAH', !isset(classSupernova::$config->payment_currency_default));
    }
    upd_check_key('payment_lot_size', 1000, !isset(classSupernova::$config->payment_lot_size));
    upd_check_key('payment_lot_price', 1, !isset(classSupernova::$config->payment_lot_price));

    // Updating category for Mercenaries
    upd_do_query("UPDATE {{powerup}} SET powerup_category = " . UNIT_MERCENARIES . " WHERE powerup_unit_id > 600 AND powerup_unit_id < 700;");

    // Convert Destructor to Death Star schematic
    upd_do_query("UPDATE {{powerup}}
      SET powerup_time_start = 0, powerup_time_finish = 0, powerup_category = " . UNIT_PLANS . ", powerup_unit_id = " . UNIT_PLAN_SHIP_DEATH_STAR . "
      WHERE (powerup_time_start = 0 OR powerup_time_finish >= UNIX_TIMESTAMP()) AND powerup_unit_id = 612;");
    // Convert Assasin to SuperNova schematic
    upd_do_query("UPDATE {{powerup}}
      SET powerup_time_start = 0, powerup_time_finish = 0, powerup_category = " . UNIT_PLANS . ", powerup_unit_id = " . UNIT_PLAN_SHIP_SUPERNOVA . "
      WHERE (powerup_time_start = 0 OR powerup_time_finish >= UNIX_TIMESTAMP()) AND powerup_unit_id = 614;");

    upd_alter_table('iraks', array(
      "ADD `fleet_start_type` SMALLINT NOT NULL DEFAULT 1",
      "ADD `fleet_end_type` SMALLINT NOT NULL DEFAULT 1",
    ), !$update_tables['iraks']['fleet_start_type']);


    if(!$update_tables['payment']['payment_status']) {
      upd_alter_table('payment', array(
        "ADD COLUMN `payment_status` INT DEFAULT 0 COMMENT 'Payment status' AFTER `payment_id`",

        "CHANGE COLUMN `payment_dm` `payment_dark_matter_paid` DECIMAL(65,0) DEFAULT 0 COMMENT 'Real DM paid for'",
        "ADD COLUMN `payment_dark_matter_gained` DECIMAL(65,0) DEFAULT 0 COMMENT 'DM gained by player (with bonuses)' AFTER `payment_dark_matter_paid`",

        "CHANGE COLUMN `payment_internal_id` `payment_external_id` VARCHAR(255) DEFAULT '' COMMENT 'External payment ID in payment system'",
        "CHANGE COLUMN `payment_internal_date` `payment_external_date` DATETIME COMMENT 'External payment timestamp in payment system'",
        "ADD COLUMN `payment_external_lots` decimal(65,5) NOT NULL DEFAULT '0.00000' COMMENT 'Payment system lot amount'",
        "ADD COLUMN `payment_external_amount` decimal(65,5) NOT NULL DEFAULT '0.00000' COMMENT 'Money incoming from payment system'",
        "ADD COLUMN `payment_external_currency` VARCHAR(3) NOT NULL DEFAULT '' COMMENT 'Payment system currency'",
      ), !$update_tables['payment']['payment_status']);
    }

    upd_do_query("UPDATE {{powerup}} SET powerup_time_start = 0, powerup_time_finish = 0 WHERE powerup_category = " . UNIT_PLANS . ";");

    upd_check_key('server_start_date', date('d.m.Y', SN_TIME_NOW), !isset(classSupernova::$config->server_start_date));
    upd_check_key('server_que_length_structures', 5, !isset(classSupernova::$config->server_que_length_structures));
    upd_check_key('server_que_length_hangar', 5, !isset(classSupernova::$config->server_que_length_hangar));

    upd_check_key('chat_highlight_moderator', '<span class="nick_moderator">$1</span>', classSupernova::$config->chat_highlight_admin == '<font color=green>$1</font>');
    upd_check_key('chat_highlight_operator', '<span class="nick_operator">$1</span>', classSupernova::$config->chat_highlight_admin == '<font color=red>$1</font>');
    upd_check_key('chat_highlight_admin', '<span class="nick_admin">$1</span>', classSupernova::$config->chat_highlight_admin == '<font color=purple>$1</font>');

    upd_check_key('chat_highlight_premium', '<span class="nick_premium">$1</span>', !isset(classSupernova::$config->chat_highlight_premium));

    upd_do_query("UPDATE {{planets}} SET `PLANET_GOVERNOR_LEVEL` = CEILING(`PLANET_GOVERNOR_LEVEL`/2) WHERE PLANET_GOVERNOR_ID = " . MRC_ENGINEER . " AND `PLANET_GOVERNOR_LEVEL` > 8;");


    upd_do_query('COMMIT;', true);
    $new_version = 34;

  case 34:
    upd_log_version_update();

    upd_alter_table('planets', array(
      "ADD COLUMN `planet_teleport_next` INT(11) NOT NULL DEFAULT 0 COMMENT 'Next teleport time'",
    ), !$update_tables['planets']['planet_teleport_next']);

    upd_check_key('planet_teleport_cost', 50000, !isset(classSupernova::$config->planet_teleport_cost));
    upd_check_key('planet_teleport_timeout', PERIOD_DAY * 1, !isset(classSupernova::$config->planet_teleport_timeout));

    upd_check_key('planet_capital_cost', 25000, !isset(classSupernova::$config->planet_capital_cost));

    upd_alter_table('users', array(
      "ADD COLUMN `player_race` INT(11) NOT NULL DEFAULT 0 COMMENT 'Player\'s race'",
    ), !$update_tables['users']['player_race']);

    upd_alter_table('chat', array(
      "MODIFY COLUMN `user` TEXT COMMENT 'Chat message user name'",
    ), strtoupper($update_tables['chat']['user']['Type']) != 'TEXT');

    upd_alter_table('planets', array(
      "ADD `ship_sattelite_sloth` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Terran Sloth'",
      "ADD `ship_bomber_envy` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Lunar Envy'",
      "ADD `ship_recycler_gluttony` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Mercurian Gluttony'",
      "ADD `ship_fighter_wrath` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Venerian Wrath'",
      "ADD `ship_battleship_pride` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Martian Pride'",
      "ADD `ship_cargo_greed` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Republican Greed'",
    ), !$update_tables['planets']['ship_sattelite_sloth']);

    upd_alter_table('planets', array(
      "ADD `ship_sattelite_sloth_porcent` TINYINT(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT 'Terran Sloth production'",
      "ADD KEY `I_ship_sattelite_sloth` (`ship_sattelite_sloth`, `id_level`)",
      "ADD KEY `I_ship_bomber_envy` (`ship_bomber_envy`, `id_level`)",
      "ADD KEY `I_ship_recycler_gluttony` (`ship_recycler_gluttony`, `id_level`)",
      "ADD KEY `I_ship_fighter_wrath` (`ship_fighter_wrath`, `id_level`)",
      "ADD KEY `I_ship_battleship_pride` (`ship_battleship_pride`, `id_level`)",
      "ADD KEY `I_ship_cargo_greed` (`ship_cargo_greed`, `id_level`)",
    ), !$update_tables['planets']['ship_sattelite_sloth_porcent']);

    upd_check_key('stats_hide_admins', 1, !isset(classSupernova::$config->stats_hide_admins));
    upd_check_key('stats_hide_player_list', '', !isset(classSupernova::$config->stats_hide_player_list));

    upd_check_key('adv_seo_meta_description', '', !isset(classSupernova::$config->adv_seo_meta_description));
    upd_check_key('adv_seo_meta_keywords', '', !isset(classSupernova::$config->adv_seo_meta_keywords));

    upd_check_key('stats_hide_pm_link', '0', !isset(classSupernova::$config->stats_hide_pm_link));

    upd_alter_table('notes', array(
      "ADD INDEX `I_owner_priority_time` (`owner`, `priority`, `time`)",
    ), !$update_indexes['notes']['I_owner_priority_time']);

    if(!$update_tables['buddy']['BUDDY_ID']) {
      upd_alter_table('buddy', array(
        "CHANGE COLUMN `id` `BUDDY_ID` SERIAL COMMENT 'Buddy\Buddy table row ID'",
        "CHANGE COLUMN `active` `BUDDY_STATUS` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Buddy\Buddy request status'",
        "CHANGE COLUMN `text` `BUDDY_REQUEST` TINYTEXT DEFAULT '' COMMENT 'Buddy\Buddy request text'", // 255 chars

        "DROP INDEX `id`",

        "DROP FOREIGN KEY `FK_buddy_sender_id`",
        "DROP FOREIGN KEY `FK_buddy_owner_id`",
        "DROP INDEX `I_buddy_sender`",
        "DROP INDEX `I_buddy_owner`",
      ), !$update_tables['buddy']['BUDDY_ID']);

      upd_alter_table('buddy', array(
        "CHANGE COLUMN `sender` `BUDDY_SENDER_ID` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Buddy\Buddy request sender ID'",
        "CHANGE COLUMN `owner` `BUDDY_OWNER_ID` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Buddy\Buddy request recipient ID'",
      ), !$update_tables['buddy']['BUDDY_SENDER']);

      $query = upd_do_query("SELECT `BUDDY_ID`, `BUDDY_SENDER_ID`, `BUDDY_OWNER_ID` FROM {{buddy}} ORDER BY `BUDDY_ID`;");
      $found = $lost = array();
      while($row = db_fetch($query)) {
        $index = min($row['BUDDY_SENDER_ID'], $row['BUDDY_OWNER_ID']) . ';' . max($row['BUDDY_SENDER_ID'], $row['BUDDY_OWNER_ID']);
        if(!isset($found[$index])) {
          $found[$index] = $row['BUDDY_ID'];
        } else {
          $lost[] = $row['BUDDY_ID'];
        }
      }
      $lost = implode(',', $lost);
      if($lost) {
        upd_do_query("DELETE FROM {{buddy}} WHERE `BUDDY_ID` IN ({$lost})");
      }

      upd_alter_table('buddy', array(
        "ADD KEY `I_BUDDY_SENDER_ID` (`BUDDY_SENDER_ID`, `BUDDY_OWNER_ID`)",
        "ADD KEY `I_BUDDY_OWNER_ID` (`BUDDY_OWNER_ID`, `BUDDY_SENDER_ID`)",

        "ADD CONSTRAINT `FK_BUDDY_SENDER_ID` FOREIGN KEY (`BUDDY_SENDER_ID`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_BUDDY_OWNER_ID` FOREIGN KEY (`BUDDY_OWNER_ID`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), !$update_indexes['buddy']['I_BUDDY_SENDER_ID']);
    }

    upd_do_query('COMMIT;', true);
    $new_version = 35;

  case 35:
    upd_log_version_update();

    upd_do_query("UPDATE {{users}} SET `ally_name` = NULL, `ally_tag` = NULL, ally_register_time = 0, ally_rank_id = 0 WHERE `ally_id` IS NULL");

    if(!$update_tables['ube_report']) {
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

    if(!$update_tables['ube_report_player']) {
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
          CONSTRAINT `FK_ube_report_player_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{{ube_report}}` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_fleet']) {
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
          CONSTRAINT `FK_ube_report_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{{ube_report}}` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_unit']) {
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
          CONSTRAINT `FK_ube_report_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{{ube_report}}` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_outcome_fleet']) {
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
          CONSTRAINT `FK_ube_report_outcome_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{{ube_report}}` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['ube_report_outcome_unit']) {
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
          CONSTRAINT `FK_ube_report_outcome_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `{{ube_report}}` (`ube_report_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['unit']) {
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
          CONSTRAINT `FK_unit_player_id` FOREIGN KEY (`unit_player_id`) REFERENCES `{{users}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['captain']) {
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
          CONSTRAINT `FK_captain_unit_id` FOREIGN KEY (`captain_unit_id`) REFERENCES `{{unit}}` (`unit_id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    if(!$update_tables['fleets']['fleet_start_planet_id']) {
      upd_alter_table('fleets', array(
        "ADD `fleet_start_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Fleet start planet ID' AFTER `fleet_start_time`",
        "ADD `fleet_end_planet_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Fleet end planet ID' AFTER `fleet_end_stay`",

        "ADD KEY `I_fleet_start_planet_id` (`fleet_start_planet_id`)",
        "ADD KEY `I_fleet_end_planet_id` (`fleet_end_planet_id`)",

        "ADD CONSTRAINT `FK_fleet_planet_start` FOREIGN KEY (`fleet_start_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_fleet_planet_end` FOREIGN KEY (`fleet_end_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_tables['fleets']['fleet_start_planet_id']);

      upd_do_query("
        UPDATE {{fleets}} AS f
         LEFT JOIN {{planets}} AS p_s ON p_s.galaxy = f.fleet_start_galaxy AND p_s.system = f.fleet_start_system AND p_s.planet = f.fleet_start_planet AND p_s.planet_type = f.fleet_start_type
         LEFT JOIN {{planets}} AS p_e ON p_e.galaxy = f.fleet_end_galaxy AND p_e.system = f.fleet_end_system AND p_e.planet = f.fleet_end_planet AND p_e.planet_type = f.fleet_end_type
        SET f.fleet_start_planet_id = p_s.id, f.fleet_end_planet_id = p_e.id
      ");
    }

    upd_alter_table('fleets', array("DROP COLUMN `processing_start`"), $update_tables['fleets']['processing_start']);

    if(!$update_tables['chat_player']) {
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

          CONSTRAINT `FK_chat_player_id` FOREIGN KEY (`chat_player_player_id`) REFERENCES `{{users}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    upd_alter_table('chat', array(
      "ADD `chat_message_sender_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Message sender ID' AFTER `messageid`",
      "ADD `chat_message_recipient_id` BIGINT(20) UNSIGNED DEFAULT NULL COMMENT 'Message recipient ID' AFTER `user`",

      "ADD KEY `I_chat_message_sender_id` (`chat_message_sender_id`)",
      "ADD KEY `I_chat_message_recipient_id` (`chat_message_recipient_id`)",

      "ADD CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
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

      "ADD CONSTRAINT `FK_ban_user_id` FOREIGN KEY (`ban_user_id`) REFERENCES `{{users}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_ban_issuer_id` FOREIGN KEY (`ban_issuer_id`) REFERENCES `{{users}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
    ), !$update_tables['banned']['ban_user_id']);

    upd_do_query('COMMIT;', true);
    $new_version = 36;

  case 36:
    upd_log_version_update();

    upd_alter_table('payment', array(
      "DROP FOREIGN KEY `FK_payment_user`",
    ), $update_foreigns['payment']['FK_payment_user']);

    if($update_foreigns['chat']['FK_chat_message_sender_user_id'] != 'chat_message_sender_id,users,id;') {
      upd_alter_table('chat', array(
        "DROP FOREIGN KEY `FK_chat_message_sender_user_id`",
        "DROP FOREIGN KEY `FK_chat_message_sender_recipient_id`",
      ), true);

      upd_alter_table('chat', array(
        "ADD CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        "ADD CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    upd_alter_table('users', array(
      "ADD `user_time_diff` INT(11) DEFAULT NULL COMMENT 'User time difference with server time' AFTER `onlinetime`",
      "ADD `user_time_diff_forced` TINYINT(1) DEFAULT 0 COMMENT 'User time difference forced with time zone selection flag' AFTER `user_time_diff`",
    ), !$update_tables['users']['user_time_diff']);

    upd_alter_table('planets', array(
      "ADD `ship_orbital_heavy` bigint(20) NOT NULL DEFAULT '0' COMMENT 'HOPe - Heavy Orbital Platform'",
    ), !$update_tables['planets']['ship_orbital_heavy']);

    upd_check_key('chat_refresh_rate', 5, !isset(classSupernova::$config->chat_refresh_rate));

    upd_alter_table('chat_player', array(
      "ADD `chat_player_refresh_last`  INT(11) NOT NULL DEFAULT 0 COMMENT 'Player last refresh time'",

      "ADD KEY `I_chat_player_refresh_last` (`chat_player_refresh_last`)",
    ), !$update_tables['chat_player']['chat_player_refresh_last']);

    upd_alter_table('ube_report', array(
      "ADD KEY `I_ube_report_time_combat` (`ube_report_time_combat`)",
    ), !$update_indexes['ube_report']['I_ube_report_time_combat']);

    if(!$update_tables['unit']['unit_time_start']) {
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

    if(!$update_tables['que']) {
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

          CONSTRAINT `FK_que_player_id` FOREIGN KEY (`que_player_id`) REFERENCES `{{users}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          CONSTRAINT `FK_que_planet_id` FOREIGN KEY (`que_planet_id`) REFERENCES `{{planets}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          CONSTRAINT `FK_que_planet_id_origin` FOREIGN KEY (`que_planet_id_origin`) REFERENCES `{{planets}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    // Конвертирум очередь исследований
    if($update_tables['users']['que']) {
      $que_lines = array();
      $que_query = upd_do_query("SELECT * FROM {{users}} WHERE `que`");
      while($que_row = db_fetch($que_query)) {
        $que_data = explode(',', $que_row['que']);

        if(!in_array($que_data[QI_UNIT_ID], sn_get_groups('tech'))) {
          continue;
        }

        $que_data[QI_TIME] = $que_data[QI_TIME] >= 0 ? $que_data[QI_TIME] : 0;
        // Если планета пустая - ставим главку
        $que_data[QI_PLANET_ID] = $que_data[QI_PLANET_ID] ? $que_data[QI_PLANET_ID] : $que_row['id_planet'];
        if($que_data[QI_PLANET_ID]) {
          $que_planet_check = db_fetch(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
          if(!$que_planet_check['id']) {
            $que_data[QI_PLANET_ID] = $que_row['id_planet'];
            $que_planet_check = db_fetch(upd_do_query("SELECT `id` FROM {{planets}} WHERE `id` = {$que_data[QI_PLANET_ID]}"));
            if(!$que_planet_check['id']) {
              $que_data[QI_PLANET_ID] = 'NULL';
            }
          }
        } else {
          $que_data[QI_PLANET_ID] = 'NULL';
        }

        $unit_info = get_unit_param($que_data[QI_UNIT_ID]);
        $unit_level = $que_row[$unit_info[P_NAME]];
        $unit_factor = $unit_info[P_COST][P_FACTOR] ? $unit_info[P_COST][P_FACTOR] : 1;
        $price_increase = pow($unit_factor, $unit_level);
        $unit_level++;
        $unit_cost = array();
        foreach($unit_info[P_COST] as $resource_id => $resource_amount) {
          if($resource_id === P_FACTOR || $resource_id == RES_ENERGY || !($resource_cost = $resource_amount * $price_increase)) {
            continue;
          }
          $unit_cost[] = $resource_id . ',' . floor($resource_cost);
        }
        $unit_cost = implode(';', $unit_cost);

        $que_lines[] = "({$que_row['id']},{$que_data[QI_PLANET_ID]}," . QUE_RESEARCH . ",{$que_data[QI_TIME]},{$que_data[QI_UNIT_ID]},1," .
          BUILD_CREATE . ",{$unit_level},{$que_data[QI_TIME]},'{$unit_cost}')";
      }

      if(!empty($que_lines)) {
        upd_do_query('INSERT INTO `{{que}}` (`que_player_id`,`que_planet_id_origin`,`que_type`,`que_time_left`,`que_unit_id`,`que_unit_amount`,`que_unit_mode`,`que_unit_level`,`que_unit_time`,`que_unit_price`) VALUES ' . implode(',', $que_lines));
      }

      upd_alter_table('users', array(
        "DROP COLUMN `que`",
      ), $update_tables['users']['que']);
    }


    upd_check_key('server_que_length_research', 1, !isset(classSupernova::$config->server_que_length_research));


    // Ковертируем технологии в таблицы
    if($update_tables['users']['graviton_tech']) {
      upd_do_query("DELETE FROM {{unit}} WHERE unit_type = " . UNIT_TECHNOLOGIES);

      $que_lines = array();
      $user_query = upd_do_query("SELECT * FROM {{users}}");
      upd_add_more_time(300);
      $sn_group_tech = sn_get_groups('tech');
      while($user_row = db_fetch($user_query)) {
        foreach($sn_group_tech as $tech_id) {
          if($tech_level = intval($user_row[get_unit_param($tech_id, P_NAME)])) {
            $que_lines[] = "({$user_row['id']}," . LOC_USER . ",{$user_row['id']}," . UNIT_TECHNOLOGIES . ",{$tech_id},{$tech_level})";
          }
        }
      }

      if(!empty($que_lines)) {
        upd_do_query("INSERT INTO {{unit}} (unit_player_id, unit_location_type, unit_location_id, unit_type, unit_snid, unit_level) VALUES " . implode(',', $que_lines));
      }

      upd_alter_table('users', array(
        "DROP COLUMN `graviton_tech`",
      ), $update_tables['users']['graviton_tech']);
    }

    if(!$update_indexes['unit']['I_unit_record_search']) {
      upd_alter_table('unit', array(
        "ADD KEY `I_unit_record_search` (`unit_snid`,`unit_player_id`,`unit_level` DESC,`unit_id`)",
      ), !$update_indexes['unit']['I_unit_record_search']);

      foreach(sn_get_groups(array('structures', 'fleet', 'defense')) as $unit_id) {
        $planet_units[get_unit_param($unit_id, P_NAME)] = 1;
      }
      $drop_index = array();
      $create_index = &$drop_index; // array();
      foreach($planet_units as $unit_name => $unit_create) {
        if($update_indexes['planets']['I_' . $unit_name]) {
          $drop_index[] = "DROP KEY I_{$unit_name}";
        }
        if($update_indexes['planets']['i_' . $unit_name]) {
          $drop_index[] = "DROP KEY i_{$unit_name}";
        }

        if($unit_create) {
          $create_index[] = "ADD KEY `I_{$unit_name}` (`id_owner`, {$unit_name} DESC)";
        }
      }
      upd_alter_table('planets', $drop_index, true);
    }

    upd_alter_table('users', array(
      "ADD `user_time_utc_offset` INT(11) DEFAULT NULL COMMENT 'User time difference with server time' AFTER `user_time_diff`",
    ), !$update_tables['users']['user_time_utc_offset']);

    if(!$update_foreigns['alliance']['FK_alliance_owner']) {
      upd_do_query("UPDATE {{alliance}} SET ally_owner = NULL WHERE ally_owner NOT IN (SELECT id FROM {{users}})");

      upd_alter_table('alliance', array(
        "ADD CONSTRAINT `FK_alliance_owner` FOREIGN KEY (`ally_owner`) REFERENCES `{{users}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
      ), !$update_foreigns['alliance']['FK_alliance_owner']);

      upd_do_query("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id NOT IN (SELECT id FROM {{alliance}}) OR alliance_negotiation_contr_ally_id NOT IN (SELECT id FROM {{alliance}})");

      upd_do_query("DELETE FROM {{alliance_negotiation}} WHERE alliance_negotiation_ally_id = alliance_negotiation_contr_ally_id");
      upd_do_query("DELETE FROM {{alliance_diplomacy}} WHERE alliance_diplomacy_ally_id = alliance_diplomacy_contr_ally_id");
    }

    upd_alter_table('fleets', array(
      'MODIFY COLUMN `fleet_owner` BIGINT(20) UNSIGNED DEFAULT NULL',
      "ADD CONSTRAINT `FK_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
    ), strtoupper($update_tables['fleets']['fleet_owner']['Type']) != 'BIGINT(20) UNSIGNED');

    upd_check_key('chat_highlight_developer', '<span class="nick_developer">$1</span>', !classSupernova::$config->chat_highlight_developer);

    if(!$update_tables['player_name_history']) {
      upd_check_key('game_user_changename_cost', 100000, !classSupernova::$config->game_user_changename_cost);
      upd_check_key('game_user_changename', SERVER_PLAYER_NAME_CHANGE_PAY, classSupernova::$config->game_user_changename != SERVER_PLAYER_NAME_CHANGE_PAY);

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

          CONSTRAINT `FK_player_name_history_id` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_do_query("REPLACE INTO {{player_name_history}} (`player_id`, `player_name`) SELECT `id`, `username` FROM {{users}} WHERE `user_as_ally` IS NULL;");
    }

    upd_alter_table('planets', array(
      "ADD `density` SMALLINT NOT NULL DEFAULT 5500 COMMENT 'Planet average density kg/m3'",
      "ADD `density_index` TINYINT NOT NULL DEFAULT " . PLANET_DENSITY_STANDARD . " COMMENT 'Planet cached density index'",
    ), !$update_tables['planets']['density_index']);

    if($update_tables['users']['player_artifact_list']) {
      upd_alter_table('unit', "DROP KEY `unit_id`", $update_indexes['unit']['unit_id']);

      upd_alter_table('unit', "ADD KEY `I_unit_player_location_snid` (`unit_player_id`, `unit_location_type`, `unit_location_id`, `unit_snid`)", !$update_indexes['unit']['I_unit_player_location_snid']);
      upd_alter_table('unit', "DROP KEY `I_unit_player_id_temporary`", $update_indexes['unit']['I_unit_player_id_temporary']);

      $sn_data_artifacts = sn_get_groups('artifacts');
      $db_changeset = array();

      $query = upd_do_query("SELECT `id`, `player_artifact_list` FROM {{users}} WHERE `player_artifact_list` IS NOT NULL AND `player_artifact_list` != '' FOR UPDATE");
      while($row = db_fetch($query)) {
        $artifact_list = explode(';', $row['player_artifact_list']);
        if(!$row['player_artifact_list'] || empty($artifact_list)) {
          continue;
        }
        foreach($artifact_list as $key => &$value) {
          $value = explode(',', $value);
          if(!isset($value[1]) || $value[1] <= 0 || !isset($sn_data_artifacts[$value[0]])) {
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

    upd_check_key('payment_currency_exchange_dm_', 2500, !classSupernova::$config->payment_currency_exchange_dm_ || classSupernova::$config->payment_currency_exchange_dm_ == 1000);
    upd_check_key('payment_currency_exchange_eur', 0.09259259259259, !classSupernova::$config->payment_currency_exchange_eur);
    upd_check_key('payment_currency_exchange_rub', 4.0, !classSupernova::$config->payment_currency_exchange_rub);
    upd_check_key('payment_currency_exchange_usd', 0.125, !classSupernova::$config->payment_currency_exchange_usd);
    upd_check_key('payment_currency_exchange_wme', 0.0952380952381, !classSupernova::$config->payment_currency_exchange_usd);
    upd_check_key('payment_currency_exchange_wmr', 4.1, !classSupernova::$config->payment_currency_exchange_wmr);
    upd_check_key('payment_currency_exchange_wmu', 1.05, !classSupernova::$config->payment_currency_exchange_wmu);
    upd_check_key('payment_currency_exchange_wmz', 0.126582278481, !classSupernova::$config->payment_currency_exchange_wmz);

    upd_do_query('COMMIT;', true);
    $new_version = 37;

}
