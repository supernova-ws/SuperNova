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
if($new_version < 37) {
  die('This version does not supports upgrades from SN below v37. Please, use SN v40 to upgrade old database.<br />
Эта версия игры не поддерживает обновление движка версий ниже 37й. Пожалуйста, используйте SN v40 для апгрейда со старых версий игры.');
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


    upd_alter_table('security_browser', array(
      "ADD COLUMN `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    ), !isset($update_tables['security_browser']['timestamp']));
    upd_alter_table('security_device', array(
      "ADD COLUMN `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP",
    ), !isset($update_tables['security_device']['timestamp']));


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

    // 2015-04-19 23:46:50 40a0.0
    // TODO Смерджить после выливания на живой
    upd_alter_table('users', array(
      "DROP KEY `I_user_account_id`",
    ), isset($update_indexes['users']['I_user_account_id']));

    upd_alter_table('users', array(
      "ADD COLUMN `parent_account_global` tinyint(1) unsigned NOT NULL DEFAULT 0",
    ), !isset($update_tables['users']['parent_account_global']));

    upd_alter_table('users', array(
      "MODIFY COLUMN `parent_account_global` bigint(20) unsigned NOT NULL DEFAULT 0",
      "ADD KEY `I_users_parent_account_id` (`parent_account_id`)",
      "ADD KEY `I_users_parent_account_global` (`parent_account_global`)",
    ), empty($update_indexes['users']['I_users_parent_account_id']));

    // 2015-05-02 15:11:07 40a0.1

    upd_do_query("TRUNCATE TABLE {{confirmations}};");
    upd_alter_table('confirmations', array(
      "ADD COLUMN `provider_id` tinyint unsigned NOT NULL DEFAULT 0",
      "ADD COLUMN `account_id` bigint unsigned NOT NULL DEFAULT 0",
      "ADD UNIQUE KEY I_confirmations_unique (`provider_id`, `account_id`, `type`, `email`)"
    ), empty($update_tables['confirmations']['provider_id']));

    upd_alter_table('users', array(
      "DROP KEY `I_user_account_id`",
    ), isset($update_indexes['users']['I_user_account_id']));


    $virtual_exploded = explode('/', SN_ROOT_VIRTUAL_PARENT);
    // TODO - переделать всё на db_loadItem... НАВЕРНОЕ
    upd_check_key('server_email', 'root@' . $virtual_exploded[2], !$config->db_loadItem('server_email'));

    upd_alter_table('survey_votes', array(
      "DROP FOREIGN KEY `FK_survey_votes_user`",
      "DROP KEY `I_survey_votes_user`",
    ), !empty($update_foreigns['survey_votes']['FK_survey_votes_user']) && $update_foreigns['survey_votes']['FK_survey_votes_user'] == 'survey_vote_user_id,users,id;survey_vote_user_name,users,username;');

    upd_alter_table('survey_votes', array(
      "ADD KEY `I_survey_votes_user_id` (`survey_vote_user_id`)",
    ), empty($update_indexes['survey_votes']['I_survey_votes_user_id']));
    upd_alter_table('survey_votes', array(
      "ADD CONSTRAINT `FK_survey_votes_user_id` FOREIGN KEY (`survey_vote_user_id`) REFERENCES `{{users}}` (`id`) ON DELETE SET NULL ON UPDATE CASCADE",
    ), empty($update_foreigns['survey_votes']['FK_survey_votes_user_id']));

    // 2015-05-03 12:55:15 40a0.26

    upd_do_query(
      'update {{users}} as u join {{planets}} as p on p.id = u.id_planet
      set u.system = p.system, u.planet = p.planet
      where u.system = 0 and user_as_ally is null and current_planet > 0;');

    // 2015-05-03 23:03:52 40a1.0

    function propagade_player_options($old_option_name, $new_option_id) {
      global $update_tables;

      if(!empty($update_tables['users'][$old_option_name])) {
        upd_do_query(
          "REPLACE INTO {{player_options}} (`player_id`, `option_id`, `value`)
          SELECT `id`, {$new_option_id}, `{$old_option_name}`
          FROM {{users}}
          WHERE `user_as_ally` is null and `user_bot` = " . USER_BOT_PLAYER);
        // TODO - UNCOMMENT !!!
        upd_alter_table('users', array("DROP COLUMN `{$old_option_name}`",));
      }
    }

    propagade_player_options('spio_anz', PLAYER_OPTION_FLEET_SPY_DEFAULT);
    propagade_player_options('settings_esp', PLAYER_OPTION_UNIVERSE_ICON_SPYING);
    propagade_player_options('settings_mis', PLAYER_OPTION_UNIVERSE_ICON_MISSILE);
    propagade_player_options('settings_wri', PLAYER_OPTION_UNIVERSE_ICON_PM);
    propagade_player_options('settings_statistics', PLAYER_OPTION_UNIVERSE_ICON_STATS);
    propagade_player_options('settings_info', PLAYER_OPTION_UNIVERSE_ICON_PROFILE);
    propagade_player_options('settings_bud', PLAYER_OPTION_UNIVERSE_ICON_BUDDY);

    propagade_player_options('planet_sort', PLAYER_OPTION_PLANET_SORT);
    propagade_player_options('planet_sort_order', PLAYER_OPTION_PLANET_SORT_INVERSE);
    propagade_player_options('settings_tooltiptime', PLAYER_OPTION_TOOLTIP_DELAY);

    upd_alter_table('users', "DROP COLUMN `settings_fleetactions`", !empty($update_tables['users']['settings_fleetactions']));
    upd_alter_table('users', "DROP COLUMN `settings_rep`", !empty($update_tables['users']['settings_rep']));

    upd_alter_table('users', "DROP COLUMN `player_que`", !empty($update_tables['users']['player_que']));
    upd_alter_table('users', "DROP COLUMN `user_time_measured`", !empty($update_tables['users']['user_time_measured']));
    upd_alter_table('users', "DROP COLUMN `user_time_diff`", !empty($update_tables['users']['user_time_diff']));
    upd_alter_table('users', "DROP COLUMN `user_time_utc_offset`", !empty($update_tables['users']['user_time_utc_offset']));
    upd_alter_table('users', "DROP COLUMN `user_time_diff_forced`", !empty($update_tables['users']['user_time_diff_forced']));

    // 2015-08-03 15:05:26 40a6.0

    if(empty($update_tables['planets']['position_original'])) {
      upd_alter_table('planets', array(
        "ADD COLUMN `position_original` smallint NOT NULL DEFAULT 0",
        "ADD COLUMN `field_max_original` smallint NOT NULL DEFAULT 0",
        "ADD COLUMN `temp_min_original` smallint NOT NULL DEFAULT 0",
        "ADD COLUMN `temp_max_original` smallint NOT NULL DEFAULT 0",
      ), empty($update_tables['planets']['position_original']));

      // Для того, что бы не поменялась выработка на старых планетах
      upd_do_query('UPDATE {{planets}} SET `temp_min` = `temp_max`');

      // Оригинальные значения для статистики
      upd_do_query('UPDATE {{planets}} SET `position_original` = `planet`, `field_max_original` = `field_max`, `temp_min_original` = `temp_min`, `temp_max_original` = `temp_max`;');

      // Миграция тяжмета в оливин
      upd_do_query('UPDATE {{planets}} SET `density_index` = ' . PLANET_DENSITY_METAL_PERIDOT . ' WHERE `density_index` = 7'); // deprecated define('PLANET_DENSITY_METAL_HEAVY', 7);

      // Добавляем планету-странника
      upd_check_key('game_maxPlanet', 16, $config->game_maxPlanet == 15);
    }

    // 2015-08-19 04:41:57 40a8.10

    upd_do_query('UPDATE {{planets}} SET `image` = "normaltempplanet01" WHERE `image` = "planet" OR `image` = "normaltemp01"');

    // 2015-08-27 19:14:05 40a10.0

    // Старая версия таблицы
    if(!empty($update_tables['account']['account_is_global']) || empty($update_tables['account']['account_immortal'])) {
      upd_drop_table('account');
      upd_drop_table('account_translate');
    }

    if(empty($update_tables['account'])) {
      upd_create_table('account', " (
          `account_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `account_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '',
          `account_password` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
          `account_salt` char(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
          `account_email` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '',
          `account_email_verified` tinyint(1) unsigned NOT NULL DEFAULT 0,
          `account_register_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `account_language` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'ru',
          `account_metamatter` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Metamatter amount',
          `account_metamatter_total` BIGINT(20) NOT NULL DEFAULT 0 COMMENT 'Total Metamatter amount ever bought',
          `account_immortal` TIMESTAMP NULL,
          PRIMARY KEY (`account_id`),
          UNIQUE KEY `I_account_name` (`account_name`),
          KEY `I_account_email` (`account_email`) -- Для поиска дубликатов по емейлу
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

      upd_create_table('account_translate', " (
          `provider_id` tinyint unsigned NOT NULL DEFAULT " . ACCOUNT_PROVIDER_LOCAL . " COMMENT 'Account provider',
          `provider_account_id` bigint(20) unsigned NOT NULL COMMENT 'Account ID on provider',
          `user_id` bigint(20) unsigned NOT NULL COMMENT 'User ID',
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

          PRIMARY KEY (`provider_id`, `provider_account_id`, `user_id`),
          KEY (`user_id`),
          CONSTRAINT `FK_account_translate_user_id` FOREIGN KEY (`user_id`) REFERENCES `{{users}}` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");

      upd_do_query(
        "INSERT IGNORE INTO {{account}}
            (`account_id`, `account_name`, `account_password`, `account_salt`, `account_email`, `account_register_time`, `account_language`, `account_metamatter`, `account_metamatter_total`, `account_immortal`)
          SELECT
            `id`, `username`, `password`, `salt`, `email_2`, FROM_UNIXTIME(register_time), `lang`, `metamatter`, `metamatter_total`, `immortal`
          FROM {{users}} WHERE `user_as_ally` IS NULL AND `user_bot` = " . USER_BOT_PLAYER . ";"
      );

      upd_do_query(
        "REPLACE INTO {{account_translate}} (`provider_id`, `provider_account_id`, `user_id`, `timestamp`)
          SELECT " . ACCOUNT_PROVIDER_LOCAL . ", a.account_id, u.id, a.`account_register_time`
            FROM {{users}} AS u
            JOIN {{account}} AS a ON
              a.account_name = u.username
              AND a.account_password = u.password
              AND a.account_salt = u.salt;"
      );
    }

    // 2015-08-31 12:34:21 40a10.8
    upd_do_query('UPDATE {{planets}} SET `diameter` = SQRT(`field_max`) * 1000 WHERE `diameter` > 1000000');

    // 2015-09-05 17:07:15 40a10.17
    upd_alter_table('ube_report', "ADD COLUMN `ube_report_capture_result` tinyint unsigned NOT NULL DEFAULT " . UBE_CAPTURE_DISABLED, empty($update_tables['ube_report']['ube_report_capture_result']));

    // 2015-09-07 21:11:48 40a10.19
    upd_alter_table('security_url', "MODIFY COLUMN `url_string` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''", empty($update_tables['security_url']['ube_report_capture_result']));

    // 2015-09-24 11:39:37 40a10.25
    if(empty($update_tables['log_metamatter']['provider_id'])) {
      upd_alter_table('log_metamatter', array(
        "ADD COLUMN `provider_id` tinyint unsigned NOT NULL DEFAULT " . ACCOUNT_PROVIDER_LOCAL . " COMMENT 'Account provider'",
        "ADD COLUMN `account_id` bigint(20) unsigned NOT NULL DEFAULT 0",
        "ADD COLUMN `account_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT ''",
        "ADD COLUMN `server_name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '" . SN_ROOT_VIRTUAL . "'",
      ), empty($update_tables['log_metamatter']['provider_id']));

      upd_do_query("UPDATE {{log_metamatter}} SET `account_id` = `user_id`, `account_name` = `username`");

      upd_alter_table('payment', array(
        "ADD COLUMN `payment_provider_id` tinyint unsigned NOT NULL DEFAULT " . ACCOUNT_PROVIDER_LOCAL . " COMMENT 'Payment account provider'",
        "ADD COLUMN `payment_account_id` bigint(20) unsigned NOT NULL",
        "ADD COLUMN `payment_account_name` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT ''",
      ), !$update_tables['payment']['payment_account_id']);
    }

    upd_do_query("UPDATE {{log_metamatter}} SET `account_id` = `user_id`, `account_name` = `username`");


    // 2015-10-14 01:35:55 40a13.8
    upd_check_key('db_manual_lock_enabled', 0, !isset($config->db_manual_lock_enabled));

    upd_check_key('eco_planet_starting_metal', 500, !isset($config->eco_planet_starting_metal));
    upd_check_key('eco_planet_starting_crystal', 500, !isset($config->eco_planet_starting_crystal));
    upd_check_key('eco_planet_starting_deuterium', 0, !isset($config->eco_planet_starting_deuterium));

    upd_check_key('eco_planet_storage_metal', 500000, !isset($config->eco_planet_storage_metal));
    upd_check_key('eco_planet_storage_crystal', 500000, !isset($config->eco_planet_storage_crystal));
    upd_check_key('eco_planet_storage_deuterium', 500000, !isset($config->eco_planet_storage_deuterium));

    upd_check_key('security_write_full_url_disabled', 1, !isset($config->security_write_full_url_disabled));

    // http://1whois.ru?url=
    upd_check_key('geoip_whois_url', 'https://who.is/whois-ip/ip-address/', !isset($config->core_geoip_whois_url));

    upd_check_key('ube_capture_points_diff', 2, !isset($config->ube_capture_points_diff));

    // 2015-10-17 14:46:32 40a15.5
    upd_check_key('game_users_online_timeout', 15 * 60, !isset($config->game_users_online_timeout));

    // 2015-10-22 14:37:58 40a17.5
    upd_check_key('locale_cache_disable', 0, !isset($config->locale_cache_disable));

    // 2015-10-30 19:09:01 40a19.5
    upd_check_key('event_halloween_2015_lock', 0, !isset($config->event_halloween_2015_lock));
    upd_check_key('event_halloween_2015_unit', 0, !isset($config->event_halloween_2015_unit));
    upd_check_key('event_halloween_2015_code', '', !isset($config->event_halloween_2015_code));
    upd_check_key('event_halloween_2015_timestamp', SN_TIME_SQL, !isset($config->event_halloween_2015_timestamp));
    upd_check_key('event_halloween_2015_units_used', serialize(array()), !isset($config->event_halloween_2015_units_used));
    if(empty($update_tables['log_halloween_2015'])) {
      upd_create_table('log_halloween_2015', " (
      `log_hw2015_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `player_id` bigint(20) unsigned NOT NULL COMMENT 'User ID',
      `player_name` varchar(32) NOT NULL DEFAULT '',
      `unit_snid` bigint(20) unsigned NOT NULL DEFAULT 0,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

      PRIMARY KEY (`log_hw2015_id`),
      KEY (`player_id`, `log_hw2015_id` DESC) -- For list on Imperator page
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    }

    // 2015-11-28 06:30:27 40a19.21
    if(!isset($update_tables['ube_report']['ube_report_debris_total_in_metal'])) {
      upd_alter_table('ube_report', array(
        "ADD COLUMN `ube_report_debris_total_in_metal` DECIMAL(65,0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total debris in metal'",
//        "ADD KEY `I_ube_report_debris_id` (`ube_report_debris_total_in_metal` DESC, `ube_report_id` ASC)", // For Best Battles module
        "ADD KEY `I_ube_report_time_debris_id` (`ube_report_time_process` DESC, `ube_report_debris_total_in_metal` DESC, `ube_report_id` ASC)", // For Best Battles module
      ), !isset($update_tables['ube_report']['ube_report_debris_total_in_metal']));

      $config_rpg_exchange_metal = floatval($config->rpg_exchange_metal) ? floatval($config->rpg_exchange_metal) : 1;
      $config_rpg_exchange_crystal = floatval($config->rpg_exchange_crystal) ? floatval($config->rpg_exchange_crystal) : 1;

      upd_do_query("UPDATE `{{ube_report}}`
        SET `ube_report_debris_total_in_metal` = (`ube_report_debris_metal` + `ube_report_debris_crystal` * {$config_rpg_exchange_crystal}) / {$config_rpg_exchange_metal}");
    }

    // 2015-12-06 15:10:58 40b1.0
    if(!empty($update_indexes['planets']['I_metal_mine'])) {
      upd_alter_table('planets', "DROP KEY `I_metal`", $update_indexes['planets']['I_metal']);
      upd_alter_table('planets', "DROP KEY `I_ship_sattelite_sloth`", $update_indexes['planets']['I_ship_sattelite_sloth']);
      upd_alter_table('planets', "DROP KEY `I_ship_bomber_envy`", $update_indexes['planets']['I_ship_bomber_envy']);
      upd_alter_table('planets', "DROP KEY `I_ship_recycler_gluttony`", $update_indexes['planets']['I_ship_recycler_gluttony']);
      upd_alter_table('planets', "DROP KEY `I_ship_fighter_wrath`", $update_indexes['planets']['I_ship_fighter_wrath']);
      upd_alter_table('planets', "DROP KEY `I_ship_battleship_pride`", $update_indexes['planets']['I_ship_battleship_pride']);
      upd_alter_table('planets', "DROP KEY `I_ship_cargo_greed`", $update_indexes['planets']['I_ship_cargo_greed']);
      upd_alter_table('planets', "DROP KEY `I_metal_mine`", $update_indexes['planets']['I_metal_mine']);
      upd_alter_table('planets', "DROP KEY `I_crystal_mine`", $update_indexes['planets']['I_crystal_mine']);
      upd_alter_table('planets', "DROP KEY `I_deuterium_sintetizer`", $update_indexes['planets']['I_deuterium_sintetizer']);
      upd_alter_table('planets', "DROP KEY `I_solar_plant`", $update_indexes['planets']['I_solar_plant']);
      upd_alter_table('planets', "DROP KEY `I_fusion_plant`", $update_indexes['planets']['I_fusion_plant']);
      upd_alter_table('planets', "DROP KEY `I_robot_factory`", $update_indexes['planets']['I_robot_factory']);
      upd_alter_table('planets', "DROP KEY `I_hangar`", $update_indexes['planets']['I_hangar']);
      upd_alter_table('planets', "DROP KEY `I_nano_factory`", $update_indexes['planets']['I_nano_factory']);
      upd_alter_table('planets', "DROP KEY `I_laboratory`", $update_indexes['planets']['I_laboratory']);
      upd_alter_table('planets', "DROP KEY `I_nano`", $update_indexes['planets']['I_nano']);
      upd_alter_table('planets', "DROP KEY `I_silo`", $update_indexes['planets']['I_silo']);
      upd_alter_table('planets', "DROP KEY `I_metal_store`", $update_indexes['planets']['I_metal_store']);
      upd_alter_table('planets', "DROP KEY `I_crystal_store`", $update_indexes['planets']['I_crystal_store']);
      upd_alter_table('planets', "DROP KEY `I_deuterium_store`", $update_indexes['planets']['I_deuterium_store']);
      upd_alter_table('planets', "DROP KEY `I_ally_deposit`", $update_indexes['planets']['I_ally_deposit']);
      upd_alter_table('planets', "DROP KEY `I_terraformer`", $update_indexes['planets']['I_terraformer']);
      upd_alter_table('planets', "DROP KEY `I_mondbasis`", $update_indexes['planets']['I_mondbasis']);
      upd_alter_table('planets', "DROP KEY `I_phalanx`", $update_indexes['planets']['I_phalanx']);
      upd_alter_table('planets', "DROP KEY `I_sprungtor`", $update_indexes['planets']['I_sprungtor']);
      upd_alter_table('planets', "DROP KEY `I_light_hunter`", $update_indexes['planets']['I_light_hunter']);
      upd_alter_table('planets', "DROP KEY `I_heavy_hunter`", $update_indexes['planets']['I_heavy_hunter']);
      upd_alter_table('planets', "DROP KEY `I_crusher`", $update_indexes['planets']['I_crusher']);
      upd_alter_table('planets', "DROP KEY `I_battle_ship`", $update_indexes['planets']['I_battle_ship']);
      upd_alter_table('planets', "DROP KEY `I_bomber_ship`", $update_indexes['planets']['I_bomber_ship']);
      upd_alter_table('planets', "DROP KEY `I_battleship`", $update_indexes['planets']['I_battleship']);
      upd_alter_table('planets', "DROP KEY `I_destructor`", $update_indexes['planets']['I_destructor']);
      upd_alter_table('planets', "DROP KEY `I_dearth_star`", $update_indexes['planets']['I_dearth_star']);
      upd_alter_table('planets', "DROP KEY `I_supernova`", $update_indexes['planets']['I_supernova']);
      upd_alter_table('planets', "DROP KEY `I_small_ship_cargo`", $update_indexes['planets']['I_small_ship_cargo']);
      upd_alter_table('planets', "DROP KEY `I_big_ship_cargo`", $update_indexes['planets']['I_big_ship_cargo']);
      upd_alter_table('planets', "DROP KEY `I_supercargo`", $update_indexes['planets']['I_supercargo']);
      upd_alter_table('planets', "DROP KEY `I_planet_cargo_hyper`", $update_indexes['planets']['I_planet_cargo_hyper']);
      upd_alter_table('planets', "DROP KEY `I_recycler`", $update_indexes['planets']['I_recycler']);
      upd_alter_table('planets', "DROP KEY `I_colonizer`", $update_indexes['planets']['I_colonizer']);
      upd_alter_table('planets', "DROP KEY `I_spy_sonde`", $update_indexes['planets']['I_spy_sonde']);
      upd_alter_table('planets', "DROP KEY `I_solar_satelit`", $update_indexes['planets']['I_solar_satelit']);
      upd_alter_table('planets', "DROP KEY `I_misil_launcher`", $update_indexes['planets']['I_misil_launcher']);
      upd_alter_table('planets', "DROP KEY `I_small_laser`", $update_indexes['planets']['I_small_laser']);
      upd_alter_table('planets', "DROP KEY `I_big_laser`", $update_indexes['planets']['I_big_laser']);
      upd_alter_table('planets', "DROP KEY `I_gauss_canyon`", $update_indexes['planets']['I_gauss_canyon']);
      upd_alter_table('planets', "DROP KEY `I_ionic_canyon`", $update_indexes['planets']['I_ionic_canyon']);
      upd_alter_table('planets', "DROP KEY `I_buster_canyon`", $update_indexes['planets']['I_buster_canyon']);
      upd_alter_table('planets', "DROP KEY `I_small_protection_shield`", $update_indexes['planets']['I_small_protection_shield']);
      upd_alter_table('planets', "DROP KEY `I_big_protection_shield`", $update_indexes['planets']['I_big_protection_shield']);
      upd_alter_table('planets', "DROP KEY `I_planet_protector`", $update_indexes['planets']['I_planet_protector']);
      upd_alter_table('planets', "DROP KEY `I_interceptor_misil`", $update_indexes['planets']['I_interceptor_misil']);
      upd_alter_table('planets', "DROP KEY `I_interplanetary_misil`", $update_indexes['planets']['I_interplanetary_misil']);
    }

    // #ctv

    upd_do_query('COMMIT;', true);
    $new_version = 40;

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

    // #ctv
    upd_do_query('COMMIT;', true);
    // $new_version = 42;
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
unset($sn_cache->tables);
sys_refresh_tablelist();

upd_log_message('Restoring server status');
$config->db_saveItem('game_disable', $old_server_status);
