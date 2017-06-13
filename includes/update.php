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

global $sn_cache, $new_version, $config, $debug, $sys_log_disabled, $upd_log, $update_tables, $update_indexes, $update_indexes_full, $update_foreigns;

$config->reset();
$config->db_loadAll();
$config->db_prefix = classSupernova::$db->db_prefix; // Оставить пока для совместимости
$config->cache_prefix = classSupernova::$cache_prefix;
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

$upd_log = '';
$new_version = floatval($config->db_version);
$minVersion = 40;
if($new_version < $minVersion) {
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
$query = upd_do_query('SHOW TABLES;', true);
while($row = db_fetch_row($query)) {
  upd_load_table_info($row[0]);
}
upd_log_message('Table info loaded. Now looking DB for upgrades...');

upd_do_query('SET FOREIGN_KEY_CHECKS=0;', true);


ini_set('memory_limit', '1024M');

switch($new_version) {
  case 40:
    upd_log_version_update();

    if(empty($update_tables['festival'])) {
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

    if(empty($update_tables['festival_unit'])) {
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
    if(empty($update_tables['festival_unit_log'])) {
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

    if($update_indexes_full['security_browser']['I_browser_user_agent']['browser_user_agent']['Index_type'] == 'BTREE') {
      upd_alter_table('security_browser', "DROP KEY `I_browser_user_agent`", true);
      upd_alter_table('security_browser', "ADD KEY `I_browser_user_agent` (`browser_user_agent`) USING HASH", true);
    }

    // 2016-12-03 20:36:46 41a61.0
    if(empty($update_tables['auth_vkontakte_account'])) {
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
    if(empty($update_tables['users']['skin'])) {
      upd_alter_table(
        'users',
        array(
          "ADD COLUMN `template` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'OpenGame' AFTER `que_processed`",
          "ADD COLUMN `skin` VARCHAR(64) CHARSET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'EpicBlue' AFTER `template`",
        ),
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

  case 42:
    upd_do_query('COMMIT;', true);

  // #ctv

//    $new_version = 43;

}
upd_log_message('Upgrade complete.');

upd_do_query('SET FOREIGN_KEY_CHECKS=1;', true);

classSupernova::$cache->unset_by_prefix('lng_');

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
classSupernova::$db->schema()->clear();

upd_log_message('Restoring server status');
$config->db_saveItem('game_disable', $old_server_status);
