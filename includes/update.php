<?php /** @noinspection SqlResolve */

/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

use Core\Updater;

/**
 * update.php
 *
 * Automated DB upgrade system
 *
 * @package supernova
 */

if (!defined('INIT')) {
  die('Unauthorized access');
}

if (defined('IN_UPDATE')) {
  die('Update already started');
}

const IN_UPDATE = true;

global $sn_cache, $debug, $sys_log_disabled;

$updater = new Updater();

switch ($updater->new_version) {
  /** @noinspection PhpMissingBreakStatementInspection */
  case 40:
    $updater->upd_log_version_update();
    $updater->transactionStart();

    if (!$updater->isTableExists('festival')) {
      $updater->upd_create_table('festival',
        [
          "`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT",
          "`start` datetime NOT NULL COMMENT 'Festival start datetime'",
          "`finish` datetime NOT NULL COMMENT 'Festival end datetime'",
          "`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название акции/ивента'",
          "PRIMARY KEY (`id`)",
          "KEY `I_festival_date_range` (`start`,`finish`,`id`) USING BTREE"
        ],
        "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
      );

      $updater->upd_create_table('festival_highspot',
        [
          "`id` int(10) unsigned NOT NULL AUTO_INCREMENT",
          "`festival_id` smallint(5) unsigned DEFAULT NULL",
          "`class` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Highspot class'",
          "`start` datetime NOT NULL COMMENT 'Highspot start datetime'",
          "`finish` datetime NOT NULL COMMENT 'Highspot end datetime'",
          "`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''",
          "PRIMARY KEY (`id`)",
          "KEY `I_highspot_order` (`start`,`finish`,`id`)",
          "KEY `I_highspot_festival_id` (`festival_id`,`start`,`finish`,`id`) USING BTREE",
          "CONSTRAINT `FK_highspot_festival_id` FOREIGN KEY (`festival_id`) REFERENCES `{{festival}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        ],
        "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
      );

      $updater->upd_create_table('festival_highspot_activity',
        [
          "`id` int(10) unsigned NOT NULL AUTO_INCREMENT",
          "`highspot_id` int(10) unsigned DEFAULT NULL",
          "`class` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Класс события - ID модуля события'",
          "`type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Тип активити: 1 - триггер, 2 - хук'",
          "`start` datetime NOT NULL COMMENT 'Запланированное время запуска'",
          "`finish` datetime DEFAULT NULL COMMENT 'Реальное время запуска'",
          "`params` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Параметры активити в виде сериализированного архива'",
          "PRIMARY KEY (`id`)",
          "KEY `I_festival_activity_order` (`start`,`finish`,`id`) USING BTREE",
          "KEY `I_festival_activity_highspot_id` (`highspot_id`,`start`,`finish`,`id`) USING BTREE",
          "CONSTRAINT `FK_festival_activity_highspot_id` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        ],
        "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci"
      );

      /** @noinspection SpellCheckingInspection */
      $updater->upd_create_table('festival_unit',
        [
          "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`highspot_id` int(10) unsigned DEFAULT NULL",
          "`player_id` bigint(20) unsigned DEFAULT NULL",
          "`unit_id` bigint(20) NOT NULL DEFAULT '0'",
          "`unit_level` bigint(20) unsigned NOT NULL DEFAULT '0'",
          "PRIMARY KEY (`id`)",
          "KEY `I_festival_unit_player_id` (`player_id`,`highspot_id`) USING BTREE",
          "KEY `I_festival_unit_highspot_id` (`highspot_id`,`unit_id`,`player_id`) USING BTREE",
          "CONSTRAINT `FK_festival_unit_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          "CONSTRAINT `FK_festival_unit_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        ],
        "ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;"
      );

      /** @noinspection SpellCheckingInspection */
      $updater->upd_create_table('festival_unit_log',
        [
          "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`highspot_id` int(10) unsigned DEFAULT NULL",
          "`player_id` bigint(20) unsigned NOT NULL COMMENT 'User ID'",
          "`player_name` varchar(32) NOT NULL DEFAULT ''",
          "`unit_id` bigint(20) unsigned NOT NULL DEFAULT '0'",
          "`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
          "`unit_level` int(11) NOT NULL DEFAULT '0'",
          "`unit_image` varchar(255) NOT NULL DEFAULT ''",
          "PRIMARY KEY (`id`)",
          "KEY `I_festival_unit_log_player_id` (`player_id`,`highspot_id`,`id`) USING BTREE",
          "KEY `I_festival_unit_log_highspot_id` (`highspot_id`,`unit_id`,`player_id`) USING BTREE",
          "CONSTRAINT `FK_festival_unit_log_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          "CONSTRAINT `FK_festival_unit_log_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );
    }

    // 2016-01-15 10:57:17 41a1.4
    $updater->upd_alter_table(
      'security_browser',
      "MODIFY COLUMN `browser_user_agent` VARCHAR(250) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''",
      $updater->getFieldDescription('security_browser', 'browser_user_agent')->Collation == 'latin1_bin'
    );

//    if ($updater->getIndexDescription('security_browser', 'I_browser_user_agent')['browser_user_agent']['Index_type'] == 'BTREE') {
    if ($updater->getIndexDescription('security_browser', 'I_browser_user_agent')->Index_type == 'BTREE') {
      $updater->upd_alter_table('security_browser', "DROP KEY `I_browser_user_agent`", true);
      $updater->upd_alter_table('security_browser', "ADD KEY `I_browser_user_agent` (`browser_user_agent`) USING HASH", true);
    }

    // 2016-12-03 20:36:46 41a61.0
    if (!$updater->isTableExists('auth_vkontakte_account')) {
      $updater->upd_create_table('auth_vkontakte_account',
        [
          "`user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`access_token` varchar(250) NOT NULL DEFAULT ''",
          "`expires_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
          "`email` varchar(250) NOT NULL DEFAULT ''",
          "`first_name` varchar(250) NOT NULL DEFAULT ''",
          "`last_name` varchar(250) NOT NULL DEFAULT ''",
          "`account_id` bigint(20) unsigned NULL COMMENT 'Account ID'",
          "PRIMARY KEY (`user_id`)",
          "CONSTRAINT `FK_vkontakte_account_id` FOREIGN KEY (`account_id`) REFERENCES `{{account}}` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE"
        ],
        "ENGINE=InnoDB DEFAULT CHARSET=utf8"
      );
    }

    // 2017-02-03 16:10:49 41b1
    $updater->new_version = 41;
    $updater->transactionCommit();

  /** @noinspection PhpMissingBreakStatementInspection */
  case 41:
    $updater->upd_log_version_update();
    $updater->transactionStart();

    // 2017-02-07 09:43:45 42a0
    $updater->upd_check_key('game_news_overview_show', 2 * 7 * 24 * 60 * 60, !isset(SN::$gc->config->game_news_overview_show));

    // 2017-02-13 13:44:18 42a17
    $updater->upd_check_key('tutorial_first_item', 1, !isset(SN::$gc->config->tutorial_first_item));

    // 2017-02-14 17:13:45 42a20.11
    // TODO - REMOVE DROP TABLE AND CONDITION!
    if (!$updater->isIndexExists('text', 'I_text_next_alt')) {
      $updater->upd_drop_table('text');
      $updater->upd_create_table('text',
        [
          "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`parent` bigint(20) unsigned DEFAULT NULL COMMENT 'Parent record. NULL - no parent'",
          "`context` bigint(20) unsigned DEFAULT NULL COMMENT 'Tutorial context. NULL - main screen'",
          "`prev` bigint(20) unsigned DEFAULT NULL COMMENT 'Previous text part. NULL - first part'",
          "`next` bigint(20) unsigned DEFAULT NULL COMMENT 'Next text part. NULL - final part'",
          "`next_alt` bigint(20) unsigned DEFAULT NULL COMMENT 'Alternative next text part. NULL - no alternative'",
          "`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text title'",
          "`content` text COLLATE utf8_unicode_ci COMMENT 'Content - 64k fits to all!'",
          "PRIMARY KEY (`id`)",
          "KEY `I_text_parent` (`parent`)",
          "KEY `I_text_prev` (`prev`)",
          "KEY `I_text_next` (`next`)",
          "KEY `I_text_next_alt` (`next_alt`)",
          "CONSTRAINT `FK_text_parent` FOREIGN KEY (`parent`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
          "CONSTRAINT `FK_text_prev` FOREIGN KEY (`prev`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
          "CONSTRAINT `FK_text_next` FOREIGN KEY (`next`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
          "CONSTRAINT `FK_text_next_alt` FOREIGN KEY (`next_alt`) REFERENCES `{{text}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
        ],
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );
    }

    // 2017-02-22 01:46:23 42a23.6
    // RPG_MARKET = 6, RPG_MARKET_EXCHANGE = 35
    $updater->upd_do_query("UPDATE `{{log_dark_matter}}` SET `log_dark_matter_reason` = " . 35 . " WHERE `log_dark_matter_reason` = " . 6);
    $updater->upd_do_query("UPDATE `{{log_metamatter}}` SET `reason` = " . 35 . " WHERE `reason` = " . 6);

    // 2017-03-06 00:43:16 42a26.4
    if (!$updater->isTableExists('festival_gifts')) {
      $updater->upd_create_table('festival_gifts',
        [
          "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
          "`highspot_id` int(10) unsigned DEFAULT NULL",
          "`from` bigint(20) unsigned DEFAULT NULL",
          "`to` bigint(20) unsigned DEFAULT NULL",
          "`amount` bigint(20) unsigned NOT NULL",
          "PRIMARY KEY (`id`)",
          "KEY `I_highspot_id` (`highspot_id`,`from`,`to`) USING BTREE",
          "KEY `I_to_from` (`highspot_id`,`to`,`from`) USING BTREE",
        ],
        'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
      );
    }

    // 2017-03-11 20:09:51 42a26.15
    if (!$updater->isFieldExists('users', 'skin')) {
      $updater->upd_alter_table(
        'users',
        [
          "ADD COLUMN `template` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OpenGame' AFTER `que_processed`",
          "ADD COLUMN `skin` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'EpicBlue' AFTER `template`",
        ],
        !$updater->isFieldExists('users', 'skin')
      );

      $query = $updater->upd_do_query("SELECT `id`, `dpath` FROM `{{users}}` FOR UPDATE");
      while ($row = db_fetch($query)) {
        $skinName = '';
        /** @noinspection SpellCheckingInspection */
        if (!$row['dpath']) {
          $skinName = 'EpicBlue';
        } /** @noinspection SpellCheckingInspection */
        elseif (substr($row['dpath'], 0, 6) == 'skins/') {
          /** @noinspection SpellCheckingInspection */
          $skinName = substr($row['dpath'], 6, -1);
        } else {
          /** @noinspection SpellCheckingInspection */
          $skinName = $row['dpath'];
        }
        if ($skinName) {
          $skinName = SN::$db->db_escape($skinName);
          $updater->upd_do_query("UPDATE `{{users}}` SET `skin` = '{$skinName}' WHERE `id` = {$row['id']};");
        }
      }
    }

    /** @noinspection SpellCheckingInspection */
    $updater->upd_alter_table('users', ["DROP COLUMN `dpath`",], $updater->isFieldExists('users', 'dpath'));

    // 2017-06-12 13:47:36 42c1
    $updater->new_version = 42;
    $updater->transactionCommit();

  /** @noinspection PhpMissingBreakStatementInspection */
  case 42:
    $updater->upd_log_version_update();
    $updater->transactionStart();

    // 2017-10-11 09:51:49 43a4.3
    $updater->upd_alter_table('messages',
      ["ADD COLUMN `message_json` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `message_text`",],
      !$updater->isFieldExists('messages', 'message_json')
    );


    // 2017-10-17 09:49:24 43a6.0
    // Removing old index i_user_id
    $updater->upd_alter_table('counter', ['DROP KEY `i_user_id`',], $updater->isIndexExists('counter', 'i_user_id'));
    // Adding new index I_counter_user_id
    $updater->upd_alter_table('counter',
      [
        'ADD KEY `I_counter_user_id` (`user_id`, `device_id`, `browser_id`, `user_ip`, `user_proxy`)'
      ],
      !$updater->isIndexExists('counter', 'I_counter_user_id')
    );

    // Adding new field visit_length
    $updater->upd_alter_table('counter', [
      "ADD COLUMN `visit_length` int unsigned NOT NULL DEFAULT 0 AFTER `visit_time`",
    ], !$updater->isFieldExists('counter', 'visit_length'));

    // Adding key for logger update
    $updater->upd_alter_table('counter', [
      'ADD KEY `I_counter_visit_time` (`visit_time`, `counter_id`)'
    ], !$updater->isIndexExists('counter', 'I_counter_visit_time'));

    // 2017-10-18 09:27:27 43a6.1
    $updater->upd_alter_table('counter', [
      "ADD COLUMN `hits` int unsigned NOT NULL DEFAULT 1 AFTER `visit_length`",
    ], !$updater->isFieldExists('counter', 'hits'));

    // 2017-11-24 05:07:29 43a7.16
    $updater->upd_alter_table('festival_highspot', [
      "ADD COLUMN `params` text NOT NULL DEFAULT '' COMMENT 'Параметры хайспота в виде JSON-encoded' AFTER `name`",
    ], !$updater->isFieldExists('festival_highspot', 'params'));

    // 2017-11-26 06:40:25 43a8.3
    $player_metamatter_immortal = SN::$gc->config->player_metamatter_immortal;
    $updater->upd_do_query(
      "INSERT INTO `{{player_award}}` (award_type_id, award_id, player_id, awarded)
        SELECT 2300, 2301, trans.user_id, acc.account_immortal
        FROM `{{account}}` AS acc
          JOIN `{{account_translate}}` AS trans ON trans.provider_id = 1 AND trans.provider_account_id = acc.account_id
          LEFT JOIN `{{player_award}}` AS award ON award.award_id = 2301 AND award.player_id = trans.user_id
        WHERE acc.account_metamatter_total >= {$player_metamatter_immortal} AND award.id IS NULL;"
    );

    // 2018-02-27 08:32:46 43a12.8
    if (!$updater->isTableExists('server_patches')) {
      $updater->upd_create_table(
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

    $updater->updPatchApply(1, function () use ($updater) {
      $q = $updater->upd_do_query("SELECT `messageid`, `user` FROM `{{chat}}`", true);
      while ($row = db_fetch($q)) {
        if (strpos($row['user'], 'a:') !== 0) {
          continue;
        }

        try {
          /** @noinspection SpellCheckingInspection */
          $updater->upd_do_query(
            "UPDATE `{{chat}}` SET `user` = '" . SN::$db->db_escape(
              json_encode(
                unserialize($row['user'])
                , JSON_FORCE_OBJECT
              )
            ) . "' WHERE `messageid` = " . floatval($row['messageid'])
          );
        } catch (Exception $e) {
        }
      }
    });

    // 2018-03-07 09:23:41 43a13.23 + 2018-03-07 12:00:47 43a13.24
    $updater->updPatchApply(2, function () use ($updater) {
      $updater->upd_alter_table('festival_gifts', [
        "ADD COLUMN `disclosure` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `amount`",
        "ADD COLUMN `message` VARCHAR(4096) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' AFTER `disclosure`",
      ], !$updater->isFieldExists('festival_gifts', 'disclosure'));
    });

    // 2018-03-12 13:23:10 43a13.33
    $updater->updPatchApply(3, function () use ($updater) {
      $updater->upd_alter_table('player_options',
        [
          "MODIFY COLUMN `value` VARCHAR(16000) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''",
        ],
        $updater->getFieldDescription('player_options', 'value')->Type == 'varchar(1900)'
      );
    });

    // 2018-03-24 21:31:51 43a16.16 - OiS
    $updater->updPatchApply(4, function () use ($updater) {
      if (!$updater->isTableExists('festival_ois_player')) {
        $updater->upd_create_table(
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
    $updater->updPatchApply(5, function () use ($updater) {
      $updater->upd_alter_table(
        'que',
        "ADD COLUMN `que_unit_one_time_raw` DECIMAL(20,5) NOT NULL DEFAULT 0",
        !$updater->isFieldExists('que', 'que_unit_one_time_raw')
      );
    });

    $updater->new_version = 43;
    $updater->transactionCommit();

  /** @noinspection PhpMissingBreakStatementInspection */
  case 43:
    // !!!!!!!!! This one does not start transaction !!!!!!!!!!!!
    $updater->upd_log_version_update();

    // 2018-12-21 14:00:41 44a5 Module "ad_promo_code" support
    $updater->updPatchApply(6, function () use ($updater) {
      if (!$updater->isTableExists('ad_promo_codes')) {
        $updater->upd_create_table(
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
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }

      if (!$updater->isTableExists('ad_promo_codes_uses')) {
        $updater->upd_create_table(
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
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    });

    // 2018-12-22 11:42:20 44a12
    $updater->updPatchApply(7, function () use ($updater) {
      // Creating table for HTTP query strings
      $updater->upd_create_table(
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
      $updater->upd_alter_table('counter', [
        "DROP FOREIGN KEY `FK_counter_plain_url_id`",
        "DROP KEY `I_counter_plain_url_id`",
        "DROP COLUMN `plain_url_id`",

        "ADD COLUMN `query_string_id` bigint(20) unsigned DEFAULT NULL AFTER `page_url_id`",
        "ADD KEY `I_counter_query_string_id` (`query_string_id`)",

        "ADD COLUMN `player_entry_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`",
        "ADD KEY `I_counter_player_entry_id` (`player_entry_id`, `user_id`)",

        "DROP KEY `I_counter_device_id`",
        "ADD KEY `I_counter_device_id` (device_id, browser_id, user_ip, user_proxy)",
      ], !$updater->isFieldExists('counter', 'query_string_id'));

      // Adjusting `security_player_entry` to match new structure
      $updater->upd_alter_table('security_player_entry', [
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
      ], !$updater->isFieldExists('security_player_entry', 'id'));

      if ($updater->isFieldExists('counter', 'device_id')) {
        $oldLockTime                   = SN::$gc->config->upd_lock_time;
        SN::$gc->config->upd_lock_time = 300;

        $updater->transactionStart();
        $updater->upd_drop_table('spe_temp');
        $updater->upd_create_table(
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
        $updater->upd_do_query(
          "INSERT IGNORE INTO `{{spe_temp}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, min(`first_visit`) 
          FROM `{{security_player_entry}}`
          GROUP BY `device_id`, `browser_id`, `user_ip`, `user_proxy`"
        );
        // Populating temp table with data from `counter`
        $updater->upd_do_query(
          "INSERT IGNORE INTO `{{spe_temp}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, min(`visit_time`)
          FROM `{{counter}}`
          GROUP BY `device_id`, `browser_id`, `user_ip`, `user_proxy`"
        );

        // Deleting all records from `security_player_entry`
        $updater->upd_do_query("TRUNCATE TABLE `{{security_player_entry}}`;");
        // Adding unique index for all significant fields
        $updater->upd_alter_table('security_player_entry', [
          "ADD UNIQUE KEY `I_player_entry_unique` (`device_id`, `browser_id`, `user_ip`, `user_proxy`)",
        ], !$updater->isIndexExists('security_player_entry', 'I_player_entry_unique'));
        // Filling `security_player_entry` from temp table
        $updater->upd_do_query(
          "INSERT IGNORE INTO `{{security_player_entry}}` (`device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`)
          SELECT `device_id`, `browser_id`, `user_ip`, `user_proxy`, `first_visit`
          FROM `{{spe_temp}}`"
        );
        // Dropping temp table - it has no use anymore
        $updater->upd_drop_table('spe_temp');

        // Updating counter to match player entries
        /** @noinspection SqlWithoutWhere */
        $updater->upd_do_query(
          "UPDATE `{{counter}}` AS c
          LEFT JOIN `{{security_player_entry}}` AS spe
            ON spe.device_id = c.device_id AND spe.browser_id = c.browser_id
                AND spe.user_ip = c.user_ip AND spe.user_proxy = c.user_proxy
        SET c.player_entry_id = spe.id"
        );

        $updater->upd_alter_table('security_player_entry', [
          "DROP KEY `I_player_entry_device_id`",
          "DROP KEY `I_player_entry_player_id`",
          // Removing unused field `security_player_entry`.`player_id`
          "DROP COLUMN `player_id`",
        ], $updater->isFieldExists('security_player_entry', 'player_id'));
// todo - вынести вниз в отдельный патч (?) Сверить с живыми
        // Remove unused fields from `counter` table
        $updater->upd_alter_table('counter', [
          "DROP KEY `I_counter_user_id`",
          "ADD KEY `I_counter_user_id` (`user_id`, `player_entry_id`)",

          "DROP FOREIGN KEY `FK_counter_device_id`",
          "DROP KEY `I_counter_device_id`",
          "DROP COLUMN `device_id`",

          "DROP FOREIGN KEY `FK_counter_browser_id`",
          "DROP KEY `I_counter_browser_id`",
          "DROP COLUMN `browser_id`",

          "DROP COLUMN `user_ip`",
          "DROP COLUMN `user_proxy`",
        ], $updater->isFieldExists('counter', 'device_id'));

        SN::$gc->config->upd_lock_time = $oldLockTime;
        $updater->transactionCommit();
      }
    });

    $updater->new_version = 44;
    $updater->transactionCommit();

  /** @noinspection PhpMissingBreakStatementInspection */
  case 44:
    // !!!!!!!!! This one does not start transaction !!!!!!!!!!!!
    $updater->upd_log_version_update();

    // 2019-08-15 00:10:48 45a8
    $updater->updPatchApply(8, function () use ($updater) {
      if (!$updater->isTableExists('player_ignore')) {
        $updater->upd_create_table(
          'player_ignore',
          [
            "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
            "`player_id` bigint(20) unsigned NOT NULL",
            "`ignored_id` bigint(20) unsigned NOT NULL",
            "`subsystem` tinyint(4) NOT NULL DEFAULT '0'",
            "PRIMARY KEY (`id`)",
            "UNIQUE KEY `I_player_ignore_all` (`player_id`,`ignored_id`,`subsystem`) USING BTREE",
            "KEY `I_player_ignore_ignored` (`ignored_id`)",
            "CONSTRAINT `FK_player_ignore_ignored` FOREIGN KEY (`ignored_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT `FK_player_ignore_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    }, PATCH_REGISTER);

    // 2019-08-21 20:14:18 45a19
    $updater->updPatchApply(9, function () use ($updater) {
      $updater->upd_alter_table('payment', [
        'ADD COLUMN `payment_method_id` smallint DEFAULT NULL AFTER `payment_module_name`',
        'ADD KEY `I_payment_method_id` (`payment_method_id`)',
      ], !$updater->isFieldExists('payment', 'payment_method_id'));
    }, PATCH_REGISTER);

    // 2020-02-18 21:00:19 45a71
    $updater->updPatchApply(10, function () use ($updater) {
      $name = classConfig::FLEET_UPDATE_MAX_RUN_TIME;
      if (!SN::$gc->config->pass()->$name) {
        SN::$gc->config->pass()->$name = 30;
      }
    }, PATCH_REGISTER);

    $updater->new_version = 45;
    $updater->transactionCommit();

  /** @noinspection PhpMissingBreakStatementInspection */
  case 45:
    // !!!!!!!!! This one does not start transaction !!!!!!!!!!!!
    $updater->upd_log_version_update();

    // 2021-03-03 13:41:05 46a13
    $updater->updPatchApply(11, function () use ($updater) {
      $updater->upd_alter_table('festival_gifts', [
        'ADD COLUMN `gift_unit_id` bigint(20) NOT NULL DEFAULT 0 AFTER `amount`',
      ], !$updater->isFieldExists('festival_gifts', 'gift_unit_id'));
    }, PATCH_REGISTER);

    // 2024-04-13 13:04:16 46a127
    $updater->updPatchApply(12, function () use ($updater) {
      $updater->upd_alter_table('config', [
        "ADD COLUMN `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
        "ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
      ], !$updater->isFieldExists('config', 'created_at'));

      if (!$updater->isTableExists('festival_config')) {
        $updater->upd_create_table(
          'festival_config',
          [
            "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
            "`festival_id` smallint(5) unsigned NULL DEFAULT NULL",
            "`highspot_id` int(10) unsigned NULL DEFAULT NULL",

            "`config_name` varchar(64) NOT NULL",
            "`config_value` mediumtext NOT NULL",

            "`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
            "`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",

            "PRIMARY KEY (`id`)",

            "KEY `I_festival_config_festival` (`festival_id`,`config_name`) USING BTREE",
            "UNIQUE KEY `I_festival_config_highspot` (`highspot_id`,`festival_id`,`config_name`) USING BTREE",

            "CONSTRAINT `FK_festival_config_festival_id` FOREIGN KEY (`festival_id`) REFERENCES `{{festival}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT `FK_festival_config_highspot_id` FOREIGN KEY (`highspot_id`) REFERENCES `{{festival_highspot}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );

        // module_festival_69_highspot_1396_code
        $query = $updater->upd_do_query("SELECT * FROM {{config}} WHERE `config_name` LIKE 'module_festival_%_highspot_%';");
        $total = $patched = 0;
        while ($row = db_fetch($query)) {
          $total++;
          if (preg_match('/module_festival_(\d+)_highspot_(\d+)_(.+)/', $row['config_name'], $matches)) {
            /*
             74|array(4)
                0 => string(38) module_festival_13_highspot_275_status
                1 => string(2) 13
                2 => string(3) 275
                3 => string(6) status
             * */
            $festival = $updater->upd_do_query("SELECT `id` FROM {{festival}} WHERE `id` = {$matches[1]};", true);
            $highspot = $updater->upd_do_query("SELECT `id` FROM {{festival_highspot}} WHERE `id` = {$matches[2]};", true);
            if (!empty($festival->num_rows) && !empty($highspot->num_rows)) {
              $matches[3] = "'" . SN::$db->db_escape($matches[3]) . "'";
              $matches[4] = "'" . SN::$db->db_escape($row['config_value']) . "'";
              $updater->upd_do_query("
                REPLACE INTO {{festival_config}}
                SET
                  `festival_id` = {$matches[1]},
                  `highspot_id` = {$matches[2]},
                  `config_name` = {$matches[3]},
                  `config_value` = {$matches[4]}
                ;");
              $patched++;
            } elseif (empty($festival->num_rows)) {
              $updater->upd_log_message("Warning! Festival ID {$matches[1]} not found");
            } elseif (empty($highspot->num_rows)) {
              $updater->upd_log_message("Warning! Highspot ID {$matches[2]} not found");
            }
          }
        }

        $updater->upd_log_message("Migrated {$patched}/{$total} festival configuration records");
      }

      $updater->upd_alter_table('que', ['DROP KEY `que_id`',], $updater->isIndexExists('que', 'que_id'));
      $updater->upd_alter_table('counter', ['DROP KEY `counter_id`',], $updater->isIndexExists('counter', 'counter_id'));
      $updater->upd_alter_table('captain', ['DROP KEY `captain_id`',], $updater->isIndexExists('captain', 'captain_id'));
    }, PATCH_REGISTER);

    // 2024-10-21 21:08:03 46a147
    $updater->updPatchApply(13, function () use ($updater) {
      $updater->indexDropIfExists('planets', 'id');
      $updater->indexDropIfExists('users', 'I_user_id_name');

      $updater->indexReplace(
        'que',
        'I_que_planet_id',
        ['que_planet_id', 'que_player_id',],
        function () use ($updater) {
          $updater->constraintDropIfExists('que', 'FK_que_planet_id');
        },
        function () use ($updater) {
          //    CONSTRAINT `FK_que_player_id` FOREIGN KEY (`que_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
          $updater->upd_alter_table(
            'que',
            ['ADD CONSTRAINT `FK_que_planet_id` FOREIGN KEY (`que_planet_id`) REFERENCES `{{planets}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',],
            true
          );
        }
      );
    }, PATCH_REGISTER);

    // 2025-02-25 12:29:49 46a154
    $updater->updPatchApply(14, function () use ($updater) {
      if (!$updater->isTableExists('ban_ip')) {
        $updater->upd_create_table(
          'ban_ip',
          [
            "`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT",
            "`ipv4_from` int unsigned COMMENT 'IP v4 range start'",
            "`ipv4_to` int unsigned COMMENT 'IP v4 range end'",

            "`created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When ban was issued'",
            "`expired_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When ban will expire'",

            "PRIMARY KEY (`id`)",

            "KEY `I_ban_ip_v4` (`ipv4_from`,`ipv4_to`, `expired_at`) USING BTREE",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    }, PATCH_REGISTER);

    // 2025-12-15 12:30:09 46a225
    $updater->updPatchApply(15, function () use ($updater) {
      if (!$updater->isTableExists('stories')) {
        $updater->upd_create_table(
          'stories',
          [
            "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Story ID'",
            "`name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Story name'",
            "`path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Path to story files from SN root'",

            "PRIMARY KEY (`id`) USING BTREE",

            "INDEX `I_stories_name`(`name`) USING BTREE"
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
        $updater->upd_do_query("
                INSERT IGNORE INTO {{stories}} 
                SET
                  `id` = 1,
                  `name` = 'simple_story',
                  `path` = 'modules/core_stories/stories/simple_story/'
                ;"
        );
      }
      if (!$updater->isTableExists('story_rewards')) {
        $updater->upd_create_table(
          'story_rewards',
          [
            "`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT",
            "`story_id` int(10) UNSIGNED NULL DEFAULT NULL",
            "`player_id` bigint(20) UNSIGNED NULL DEFAULT NULL",
            "`reward_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Story\'s internal reward ID'",
            "`received` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL",

            "PRIMARY KEY (`id`) USING BTREE",
            "INDEX `I_story_rewards_story`(`story_id`, `player_id`, `reward_id`) USING BTREE",
            "INDEX `I_story_rewards_player`(`player_id`) USING BTREE",
            "CONSTRAINT `FK_story_rewards_player` FOREIGN KEY (`player_id`) REFERENCES `{{users}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
            "CONSTRAINT `FK_story_rewards_story` FOREIGN KEY (`story_id`) REFERENCES `{{stories}}` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          ],
          'ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci'
        );
      }
    }, PATCH_REGISTER);

    $updater->new_version = 47;
    $updater->transactionCommit();

  case 46:
//    // #ctv
//    $updater->updPatchApply(16, function() use ($updater) {
//    }, PATCH_PRE_CHECK);

//   TODO - UNCOMMENT ON RELEASE!
//    $updater->new_version = 46;
//    $updater->transactionCommit();

}

$updater->successTermination = true;
// DO NOT DELETE ! This will invoke destructor !
unset($updater);
