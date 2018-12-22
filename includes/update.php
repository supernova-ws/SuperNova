<?php

/** @noinspection SqlResolve */

/**
 * update.php
 *
 * Automated DB upgrade system
 *
 * @package supernova
 * @version 26
 *
 * 25 - copyright (c) 2009-2011 Gorlum for http://supernova.ws
 * [!] Now it's all about transactions...
 * [~] Converted doquery to internal wrapper with logging ability
 * 24 - copyright (c) 2009-2011 Gorlum for http://supernova.ws
 * [+] Converted pre v18 entries to use later implemented functions
 * v18-v23 - copyright (c) 2009-2010 Gorlum for http://supernova.ws
 * [!] DB code updates
 * 17 - copyright (c) 2009-2010 Gorlum for http://supernova.ws
 * [~] PCG1 compliant
 *
 * v01-v16 copyright (c) 2009-2010 Gorlum for http://supernova.ws
 * [!] DB code updates
 */

if (!defined('INIT')) {
//  include_once('init.php');
  die('Unauthorized access');
}

define('IN_UPDATE', true);

require_once 'includes/upd_helpers.php';

global $sn_cache, $new_version, $config, $debug, $sys_log_disabled, $upd_log, $update_tables, $update_indexes, $update_indexes_full, $update_foreigns;

$config->reset();
$config->db_loadAll();
$config->db_prefix    = SN::$db->db_prefix; // Оставить пока для совместимости
$config->cache_prefix = SN::$cache_prefix;
$config->debug        = 0;


//$config->db_loadItem('db_version');
if ($config->db_version == DB_VERSION) {
} elseif ($config->db_version > DB_VERSION) {
  $config->db_saveItem('var_db_update_end', SN_TIME_NOW);
  die(
  'Internal error! Auotupdater detects DB version greater then can be handled!<br />
    Possible you have out-of-date SuperNova version<br />
    Please upgrade your server from <a href="http://github.com/supernova-ws/SuperNova">GIT repository</a>'
  );
}

$upd_log     = '';
$new_version = floatval($config->db_version);
$minVersion  = 40;
if ($new_version < $minVersion) {
  die("This version does not supports upgrades from SN below v{$minVersion}. Please, use SN v42 to upgrade old database.<br />
Эта версия игры не поддерживает обновление движка версий ниже v{$minVersion}. Пожалуйста, используйте SN v42 для апгрейда со старых версий игры.");
}

upd_check_key('upd_lock_time', 300, !isset($config->upd_lock_time));

set_time_limit($config->upd_lock_time + 10);

upd_log_message('Update started. Disabling server');

$old_server_status = $config->db_loadItem('game_disable');
$config->db_saveItem('game_disable', GAME_DISABLE_UPDATE);

upd_log_message('Server disabled. Loading table info...');
$update_tables  = array();
$update_indexes = array();
$query          = upd_do_query('SHOW TABLES;', true);
while ($row = db_fetch_row($query)) {
  upd_load_table_info($row[0]);
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

upd_do_query('SET FOREIGN_KEY_CHECKS=0;', true);


ini_set('memory_limit', '1G');

switch ($new_version) {
  /** @noinspection PhpMissingBreakStatementInspection */
  case 40:
    upd_log_version_update();

    if (empty($update_tables['festival'])) {
      upd_create_table('festival', " (
          `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
          `start` datetime NOT NULL COMMENT 'Festival start datetime',
          `finish` datetime NOT NULL COMMENT 'Festival end datetime',
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название акции/ивента',
          PRIMARY KEY (`id`),
          KEY `I_festival_date_range` (`start`,`finish`,`id`) USING BTREE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      );

      upd_create_table('festival_highspot', " (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `festival_id` smallint(5) unsigned DEFAULT NULL,
          `class` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Highspot class',
          `start` datetime NOT NULL COMMENT 'Highspot start datetime',
          `finish` datetime NOT NULL COMMENT 'Highspot end datetime',
          `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          PRIMARY KEY (`id`),
          KEY `I_highspot_order` (`start`,`finish`,`id`),
          KEY `I_highspot_festival_id` (`festival_id`,`start`,`finish`,`id`) USING BTREE,
          CONSTRAINT `FK_highspot_festival_id` FOREIGN KEY (`festival_id`) REFERENCES `{{festival}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      );

      upd_create_table('festival_highspot_activity', " (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `highspot_id` int(10) unsigned DEFAULT NULL,
          `class` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Класс события - ID модуля события',
          `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Тип активити: 1 - триггер, 2 - хук',
          `start` datetime NOT NULL COMMENT 'Запланированное время запуска',
          `finish` datetime DEFAULT NULL COMMENT 'Реальное время запуска',
          `params` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Параметры активити в виде сериализованного архива',
          PRIMARY KEY (`id`),
          KEY `I_festival_activity_order` (`start`,`finish`,`id`) USING BTREE,
          KEY `I_festival_activity_highspot_id` (`highspot_id`,`start`,`finish`,`id`) USING BTREE,
          CONSTRAINT `FK_festival_activity_highspot_id` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      );
    }

    if (empty($update_tables['festival_unit'])) {
      upd_create_table('festival_unit', " (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `highspot_id` int(10) unsigned DEFAULT NULL,
          `player_id` bigint(20) unsigned DEFAULT NULL,
          `unit_id` bigint(20) NOT NULL DEFAULT '0',
          `unit_level` bigint(20) unsigned NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `I_festival_unit_player_id` (`player_id`,`highspot_id`) USING BTREE,
          KEY `I_festival_unit_highspot_id` (`highspot_id`,`unit_id`,`player_id`) USING BTREE,
          CONSTRAINT `FK_festival_unit_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_festival_unit_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      );
    }

    // 2015-12-21 06:06:09 41a0.12
    if (empty($update_tables['festival_unit_log'])) {
      upd_create_table('festival_unit_log', " (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `highspot_id` int(10) unsigned DEFAULT NULL,
          `player_id` bigint(20) unsigned NOT NULL COMMENT 'User ID',
          `player_name` varchar(32) NOT NULL DEFAULT '',
          `unit_id` bigint(20) unsigned NOT NULL DEFAULT '0',
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `unit_level` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `I_festival_unit_log_player_id` (`player_id`,`highspot_id`,`id`) USING BTREE,
          KEY `I_festival_unit_log_highspot_id` (`highspot_id`,`unit_id`,`player_id`) USING BTREE,
          CONSTRAINT `FK_festival_unit_log_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_festival_unit_log_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    // 2015-12-22 00:00:32 41a0.17
    upd_alter_table('festival_unit_log', "ADD COLUMN `unit_image` varchar(255) NOT NULL DEFAULT ''", empty($update_tables['festival_unit_log']['unit_image']));

    // 2016-01-15 10:57:17 41a1.4
    upd_alter_table(
      'security_browser',
      "MODIFY COLUMN `browser_user_agent` VARCHAR(250) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''",
      $update_tables['security_browser']['browser_user_agent']['Collation'] == 'latin1_bin'
    );

    if ($update_indexes_full['security_browser']['I_browser_user_agent']['browser_user_agent']['Index_type'] == 'BTREE') {
      upd_alter_table('security_browser', "DROP KEY `I_browser_user_agent`", true);
      upd_alter_table('security_browser', "ADD KEY `I_browser_user_agent` (`browser_user_agent`) USING HASH", true);
    }

    // 2016-12-03 20:36:46 41a61.0
    if (empty($update_tables['auth_vkontakte_account'])) {
      upd_create_table('auth_vkontakte_account', " (
          `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `access_token` varchar(250) NOT NULL DEFAULT '',
          `expires_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `email` varchar(250) NOT NULL DEFAULT '',

          `first_name` varchar(250) NOT NULL DEFAULT '',
          `last_name` varchar(250) NOT NULL DEFAULT '',

          `account_id` bigint(20) unsigned NULL COMMENT 'Account ID',

          PRIMARY KEY (`user_id`),
          CONSTRAINT `FK_vkontakte_account_id` FOREIGN KEY (`account_id`) REFERENCES `{{account}}` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    upd_do_query('COMMIT;', true);

    // 2017-02-03 16:10:49 41b1
    $new_version = 41;

  /** @noinspection PhpMissingBreakStatementInspection */
  case 41:
    upd_log_version_update();
    // 2017-02-07 09:43:45 42a0
    upd_check_key('game_news_overview_show', 2 * 7 * 24 * 60 * 60, !isset($config->game_news_overview_show));

    // 2017-02-13 13:44:18 42a17
    upd_check_key('tutorial_first_item', 1, !isset($config->tutorial_first_item));

    // 2017-02-14 17:13:45 42a20.11
    // TODO - REMOVE DROP TABLE AND CONDITION!
    if (!isset($update_indexes['text']['I_text_next_alt'])) {
      upd_drop_table('text');
      upd_create_table('text',
        "
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `parent` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent record. NULL - no parent',
        `context` bigint(20) unsigned DEFAULT NULL COMMENT 'Tutorial context. NULL - main screen',
        `prev` bigint(20) unsigned DEFAULT NULL COMMENT 'Previous text part. NULL - first part',
        `next` bigint(20) unsigned DEFAULT NULL COMMENT 'Next text part. NULL - final part',
        `next_alt` bigint(20) unsigned DEFAULT NULL COMMENT 'Alternative next text part. NULL - no alternative',
        `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text title',
        `content` text COLLATE utf8_unicode_ci COMMENT 'Content - 64k fits to all!',
        PRIMARY KEY (`id`),
        KEY `I_text_parent` (`parent`),
        KEY `I_text_prev` (`prev`),
        KEY `I_text_next` (`next`),
        KEY `I_text_next_alt` (`next_alt`),
        CONSTRAINT `FK_text_parent` FOREIGN KEY (`parent`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT `FK_text_prev` FOREIGN KEY (`prev`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT `FK_text_next` FOREIGN KEY (`next`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT `FK_text_next_alt` FOREIGN KEY (`next_alt`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ",
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );
    }

    // 2017-02-22 01:46:23 42a23.6
    // RPG_MARKET = 6, RPG_MARKET_EXCHANGE = 35
    upd_do_query("UPDATE `{{log_dark_matter}}` SET `log_dark_matter_reason` = " . 35 . " WHERE `log_dark_matter_reason` = " . 6);
    upd_do_query("UPDATE `{{log_metamatter}}` SET `reason` = " . 35 . " WHERE `reason` = " . 6);

    // 2017-03-06 00:43:16 42a26.4
    if (empty($update_tables['festival_gifts'])) {
      upd_create_table('festival_gifts',
        "
        `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        `highspot_id` int(10) unsigned DEFAULT NULL,
        `from` bigint(20) unsigned DEFAULT NULL,
        `to` bigint(20) unsigned DEFAULT NULL,
        `amount` bigint(20) unsigned NOT NULL,
        PRIMARY KEY (`id`),
        KEY `I_highspot_id` (`highspot_id`,`from`,`to`) USING BTREE,
        KEY `I_to_from` (`highspot_id`,`to`,`from`) USING BTREE
        ",
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );
    }

    // 2017-03-11 20:09:51 42a26.15
    if (empty($update_tables['users']['skin'])) {
      upd_alter_table(
        'users',
        [
          "ADD COLUMN `template` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OpenGame' AFTER `que_processed`",
          "ADD COLUMN `skin` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'EpicBlue' AFTER `template`",
        ],
        empty($update_tables['users']['skin'])
      );

      $query = upd_do_query("SELECT `id`, `dpath` FROM `{{users}}` FOR UPDATE");
      while ($row = db_fetch($query)) {
        $skinName = '';
        if (!$row['dpath']) {
          $skinName = 'EpicBlue';
        } elseif (substr($row['dpath'], 0, 6) == 'skins/') {
          $skinName = substr($row['dpath'], 6, -1);
        } else {
          $skinName = $row['dpath'];
        }
        if ($skinName) {
          $skinName = db_escape($skinName);
          upd_do_query("UPDATE `{{users}}` SET `skin` = '{$skinName}' WHERE `id` = {$row['id']};");
        }
      }
    }

    upd_alter_table(
      'users',
      array(
        "DROP COLUMN `dpath`",
      ),
      !empty($update_tables['users']['dpath'])
    );

    // 2017-06-12 13:47:36 42c1
    upd_do_query('COMMIT;', true);
    $new_version = 42;

  /** @noinspection PhpMissingBreakStatementInspection */
  case 42:
    // 2017-10-11 09:51:49 43a4.3
    upd_alter_table('messages', [
      "ADD COLUMN `message_json` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `message_text`",
    ], empty($update_tables['messages']['message_json']));


    // 2017-10-17 09:49:24 43a6.0
    // Removing old index i_user_id
    upd_alter_table('counter', [
      'DROP KEY `i_user_id`'
    ], !empty($update_indexes_full['counter']['i_user_id']));
    // Adding new index I_counter_user_id
    upd_alter_table('counter', [
      'ADD KEY `I_counter_user_id` (`user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)'
    ], empty($update_indexes_full['counter']['I_counter_user_id']));

    // Adding new field visit_length
    upd_alter_table('counter', [
      "ADD COLUMN `visit_length` int unsigned NOT NULL DEFAULT 0 AFTER `visit_time`",
    ], empty($update_tables['counter']['visit_length']));

    // Adding key for logger update
    upd_alter_table('counter', [
      'ADD KEY `I_counter_visit_time` (`visit_time`, `counter_id`)'
    ], empty($update_indexes_full['counter']['I_counter_visit_time']));

    // 2017-10-18 09:27:27 43a6.1
    upd_alter_table('counter', [
      "ADD COLUMN `hits` int unsigned NOT NULL DEFAULT 1 AFTER `visit_length`",
    ], empty($update_tables['counter']['hits']));

    // 2017-11-24 05:07:29 43a7.16
    upd_alter_table('festival_highspot', [
      "ADD COLUMN `params` text NOT NULL DEFAULT '' COMMENT 'Параметры хайспота в виде JSON-encoded' AFTER `name`",
    ], empty($update_tables['festival_highspot']['params']));

    // 2017-11-26 06:40:25 43a8.3
    upd_do_query(
      "INSERT INTO `{{player_award}}` (award_type_id, award_id, player_id, awarded)
        SELECT 2300, 2301, trans.user_id, acc.account_immortal
        FROM `{{account}}` AS acc
          JOIN `{{account_translate}}` AS trans ON trans.provider_id = 1 AND trans.provider_account_id = acc.account_id
          LEFT JOIN `{{player_award}}` AS award ON award.award_id = 2301 AND award.player_id = trans.user_id
        WHERE acc.account_metamatter_total >= {$config->player_metamatter_immortal} AND award.id IS NULL;"
    );

    // 2018-02-27 08:32:46 43a12.8
    if (empty($update_tables['server_patches'])) {
      upd_create_table(
        'server_patches',
        [
          "`id` int unsigned COMMENT 'Patch internal ID'",
          "`applied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
          "PRIMARY KEY (`id`)",
          "KEY `I_applied` (`applied`)"
        ],
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );
    }

    updPatchApply(1, function () {
      $q = upd_do_query("SELECT `messageid`, `user` FROM `{{chat}}`", true);
      while ($row = db_fetch($q)) {
        if (strpos($row['user'], 'a:') !== 0) {
          continue;
        }

        try {
          upd_do_query(
            "UPDATE `{{chat}}` SET `user` = '" . db_escape(
              json_encode(
                unserialize($row['user'])
                , JSON_FORCE_OBJECT
              )
            ) . "' WHERE `messageid` = " . floatval($row['messageid'])
          );
        } catch (Exception $e) {
        };
      }
    });

    // 2018-03-07 09:23:41 43a13.23 + 2018-03-07 12:00:47 43a13.24
    updPatchApply(2, function () use ($update_tables) {
      upd_alter_table('festival_gifts', [
        "ADD COLUMN `disclosure` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `amount`",
        "ADD COLUMN `message` VARCHAR(4096) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `disclosure`",
      ], empty($update_tables['festival_gifts']['disclosure']));
    });

    // 2018-03-12 13:23:10 43a13.33
    updPatchApply(3, function () use ($update_tables) {
      upd_alter_table('player_options',
        [
          "MODIFY COLUMN `value` VARCHAR(16000) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''",
        ],
        $update_tables['player_options']['value']['Type'] == 'varchar(1900)'
      );
    });

    // 2018-03-24 21:31:51 43a16.16 - OiS
    updPatchApply(4, function () use ($update_tables) {
      if (empty($update_tables['festival_ois_player'])) {
        upd_create_table(
          'festival_ois_player',
          [
            "`highspot_id` int(10) unsigned COMMENT 'Highspot ID'",
            "`player_id` bigint(20) unsigned COMMENT 'Player ID'",
            "`ois_count` int(10) unsigned COMMENT 'OiS player controlled last tick'",
            "PRIMARY KEY (`highspot_id`, `player_id`)",
            "KEY `I_player_highspot` (`player_id`, `highspot_id`)",
            "CONSTRAINT `FK_ois_highspot` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT `FK_ois_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    });

    // 2018-03-25 08:11:39 43a16.21
    updPatchApply(5, function () use ($update_tables) {
      upd_alter_table(
        'que',
        "ADD COLUMN `que_unit_one_time_raw` DECIMAL(20,5) NOT NULL DEFAULT 0",
        empty($update_tables['que']['que_unit_one_time_raw'])
      );
    });

    upd_do_query('COMMIT;', true);

    $new_version = 43;

  case 43:
    // 2018-12-21 14:00:41 44a5 Module "ad_promo_code" support
    updPatchApply(6, function () use ($update_tables) {
      if (empty($update_tables['ad_promo_codes'])) {
        upd_create_table(
          'ad_promo_codes',
          [
            "`id` int(10) unsigned NOT NULL AUTO_INCREMENT",
            "`code` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Promo code itself. Unique'",
            "`description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Promo code description'",
            "`reg_only` tinyint(1) NOT NULL DEFAULT '1'",
            "`from` datetime DEFAULT NULL",
            "`to` datetime DEFAULT NULL",
            "`max_use` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Max time code can be used. 0 - unlimited'",
            "`used_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'How many time code was used'",
            "`adjustments` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL",

            "PRIMARY KEY (`id`)",
            "UNIQUE KEY `I_promo_code` (`code`)",
          ],
//          'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }

      if (empty($update_tables['ad_promo_codes_uses'])) {
        upd_create_table(
          'ad_promo_codes_uses',
          [
            "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
            "`promo_code_id` int(10) unsigned NOT NULL",
            "`user_id` bigint(20) unsigned NOT NULL",
            "`use_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",

            "PRIMARY KEY (`id`)",
            "KEY `FK_user_id` (`user_id`)",
            "KEY `I_promo_code_id` (`promo_code_id`,`user_id`)",
          ],
//          'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    });

    // 2018-12-22 11:42:20 44a12
    updPatchApply(7, function () use ($update_tables, $update_indexes, $config, $update_foreigns) {
      // Creating table for HTTP query strings
      upd_create_table(
        'security_query_strings',
        [
          "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`query_string` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT ''",
          "PRIMARY KEY (`id`)",
          "UNIQUE KEY `I_query_string` (`query_string`)",
        ],
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );

      // Adjusting table `counter` to use HTTP query string instead of full URLs
      upd_alter_table('counter', [
        "DROP FOREIGN KEY `FK_counter_plain_url_id`",
        "DROP KEY `I_counter_plain_url_id`",
        "DROP COLUMN `plain_url_id`",

        "ADD COLUMN `query_string_id` bigint(20) unsigned DEFAULT NULL AFTER `page_url_id`",
        "ADD KEY `I_counter_query_string_id` (`query_string_id`)",

        "ADD COLUMN `player_entry_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`",
        "ADD KEY `I_counter_player_entry_id` (`player_entry_id`)",

        "DROP KEY `I_counter_device_id`",
        "ADD KEY `I_counter_device_id` (device_id, browser_id, user_ip, user_proxy)",
      ], empty($update_tables['counter']['query_string_id']));

      // Adjusting `security_player_entry` to match new structure
      upd_alter_table('security_player_entry', [
        // Adding temporary key for `player_id` field - needs for FOREIGN KEY
        "ADD KEY `I_player_entry_player_id` (`player_id`)",
        // Replacing primary index with secondary one
        "DROP PRIMARY KEY",

        // Adding main index column
        "ADD COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT FIRST",
        "ADD PRIMARY KEY (`id`)",

        // Foreign keys is not needed - we want to maintain info about player entries even if dictionary info is deleted
        "DROP FOREIGN KEY `FK_security_player_entry_browser_id`",
        "DROP FOREIGN KEY `FK_security_player_entry_device_id`",
        "DROP FOREIGN KEY `FK_security_player_entry_player_id`",
      ], empty($update_tables['security_player_entry']['id']));

      if (!empty($update_tables['counter']['device_id'])) {
        $oldLockTime           = $config->upd_lock_time;
        $config->upd_lock_time = 60;

        upd_drop_table('spe_temp');
        upd_create_table(
          'spe_temp',
          [
            "`device_id` bigint(20) unsigned NOT NULL DEFAULT '0'",
            "`browser_id` bigint(20) unsigned NOT NULL DEFAULT '0'",
            "`user_ip` int(10) unsigned NOT NULL DEFAULT '0'",
            "`user_proxy` varchar(255) COLLATE latin1_bin NOT NULL DEFAULT ''",
            "`first_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",

            "UNIQUE KEY `I_temp_key` (`device_id`,`browser_id`,`user_ip`,`user_proxy`)",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
        // Repopulating temp table with records with `user_id` == NULL
        upd_do_query(
          "INSERT IGNORE INTO `{{spe_temp}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, min(`first_visit`) 
          FROM `{{security_player_entry}}`
          GROUP BY `device_id`, `browser_id`, `user_ip`, `user_proxy`"
        );
        // Populating temp table with data from `counter`
        upd_do_query(
          "INSERT IGNORE INTO `{{spe_temp}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, min(`visit_time`)
          FROM `{{counter}}`
          GROUP BY `device_id`, `browser_id`, `user_ip`, `user_proxy`"
        );

        // Deleting all records from `security_player_entry`
        upd_do_query("TRUNCATE TABLE `{{security_player_entry}}`;");
        // Adding unique index for all significant fields
        upd_alter_table('security_player_entry', [
          "ADD UNIQUE KEY `I_player_entry_unique` (`device_id`, `browser_id`, `user_ip`, `user_proxy`)",
        ], empty($update_indexes['security_player_entry']['I_player_entry_unique']));
        // Filling `security_player_entry` from temp table
        upd_do_query(
          "INSERT IGNORE INTO `{{security_player_entry}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`
          FROM `{{spe_temp}}`"
        );
        // Dropping temp table - it has no use anymore
        upd_drop_table('spe_temp');

        // Updating counter to match player entries
        upd_do_query(
          "UPDATE `{{counter}}` AS c
          LEFT JOIN `{{security_player_entry}}` AS spe
            ON spe.device_id = c.device_id AND spe.browser_id = c.browser_id
                AND spe.user_ip = c.user_ip AND spe.user_proxy = c.user_proxy
        SET c.player_entry_id = spe.id"
        );

        upd_alter_table('security_player_entry', [
          // Removing unused field `security_player_entry`.`player_id`
          "DROP COLUMN `player_id`",
          // Removing index which is superseded by new index `I_player_entry_unique`
          "DROP KEY `I_player_entry_device_id`",
        ], !empty($update_indexes['security_player_entry']['I_player_entry_device_id']));

        // Remove unused fields from `counter` table
        upd_alter_table('counter', [
          "DROP COLUMN `device_id`",
          "DROP COLUMN `browser_id`",
          "DROP COLUMN `user_ip`",
          "DROP COLUMN `user_proxy`",
        ], !empty($update_tables['counter']['device_id']));

        $config->upd_lock_time = $oldLockTime;
      }
    }, PATCH_PRE_CHECK);

    // TODO - remove when make this patch OK
    upd_alter_table('security_player_entry', [
      // Foreign keys is not needed - we want to maintain info about player entries even if dictionary info is deleted
      "DROP FOREIGN KEY `FK_security_player_entry_browser_id`",
      "DROP FOREIGN KEY `FK_security_player_entry_device_id`",
      "DROP FOREIGN KEY `FK_security_player_entry_player_id`",
    ], empty($update_foreigns['security_player_entry']['FK_security_player_entry_browser_id']));

//    // #ctv
//    updPatchApply(8, function() use ($update_tables, $update_indexes) {
//    }, PATCH_PRE_CHECK);

//    $new_version = 44;
}
upd_log_message('Upgrade complete.');

upd_do_query('SET FOREIGN_KEY_CHECKS=1;', true);

SN::$cache->unset_by_prefix('lng_');

if ($new_version) {
  $config->db_saveItem('db_version', $new_version);
  upd_log_message("<span style='color: green;'>DB version is now {$new_version}</span>");
} else {
  upd_log_message("DB version didn't changed from {$config->db_version}");
}

$config->db_loadAll();
/*
if($user['authlevel'] >= 3) {
  print(str_replace("\r\n", '<br>', $upd_log));
}
*/
SN::$db->schema()->clear();

upd_log_message('Restoring server status');
$config->db_saveItem('game_disable', $old_server_status);
