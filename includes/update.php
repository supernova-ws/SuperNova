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

upd_log_message('Update starting. Loading table info...');
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
  case 32:
    upd_log_version_update();

    upd_check_key('avatar_max_width', 128, !isset($config->avatar_max_width));
    upd_check_key('avatar_max_height', 128, !isset($config->avatar_max_height));

    upd_alter_table('users', array(
      "MODIFY COLUMN `avatar` tinyint(1) unsigned NOT NULL DEFAULT '0'",
    ), strtoupper($update_tables['users']['avatar']['Type']) != 'TINYINT(1) UNSIGNED');

    upd_alter_table('alliance', array(
      "MODIFY COLUMN `ally_image` tinyint(1) unsigned NOT NULL DEFAULT '0'",
    ), strtoupper($update_tables['alliance']['ally_image']['Type']) != 'TINYINT(1) UNSIGNED');

    upd_alter_table('users', array(
      "DROP COLUMN `settings_allylogo`",
    ), isset($update_tables['users']['settings_allylogo']));

    if(!isset($update_tables['powerup']))
    {
      upd_do_query("DROP TABLE IF EXISTS {$config->db_prefix}mercenaries;");

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

          CONSTRAINT `FK_powerup_user_id` FOREIGN KEY (`powerup_user_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
          CONSTRAINT `FK_powerup_planet_id` FOREIGN KEY (`powerup_planet_id`) REFERENCES `{$config->db_prefix}planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('empire_mercenary_temporary', 0, !isset($config->empire_mercenary_temporary));
      upd_check_key('empire_mercenary_base_period', PERIOD_MONTH, !isset($config->empire_mercenary_base_period));

      $update_query_template = "UPDATE {{users}} SET id = id %s WHERE id = %d LIMIT 1;";
      $user_list = upd_do_query("SELECT * FROM {{users}};");
      while($user_row = mysql_fetch_assoc($user_list))
      {
        $update_query_str = '';
        foreach($sn_data['groups']['mercenaries'] as $mercenary_id)
        {
          $mercenary_data_name = $sn_data[$mercenary_id]['name'];
          if($mercenary_level = $user_row[$mercenary_data_name])
          {
            $update_query_str = ", `{$mercenary_data_name}` = 0";
            upd_do_query("DELETE FROM {{powerup}} WHERE powerup_user_id = {$user_row['id']} AND powerup_unit_id = {$mercenary_id} LIMIT 1;");
            upd_do_query("INSERT {{powerup}} SET powerup_user_id = {$user_row['id']}, powerup_unit_id = {$mercenary_id}, powerup_unit_level = {$mercenary_level};");
          }
        }

        if($update_query_str)
        {
          upd_do_query(sprintf($update_query_template, $update_query_str, $user_row['id']));
        }
      }
    }

    if(!isset($update_tables['universe']))
    {
      upd_create_table('universe',
        "(
          `universe_galaxy` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `universe_system` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
          `universe_name` varchar(32) NOT NULL DEFAULT '',
          `universe_price` bigint(20) NOT NULL DEFAULT 0,

          PRIMARY KEY (`universe_galaxy`, `universe_system`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('uni_price_galaxy', 10000, !isset($config->uni_price_galaxy));
      upd_check_key('uni_price_system', 1000, !isset($config->uni_price_system));
    }

    // ========================================================================
    // Ally player
    // Adding config variable
    upd_check_key('ali_bonus_members', 10, !isset($config->ali_bonus_members));

    // ------------------------------------------------------------------------
    // Modifying tables
    if(strtoupper($update_tables['users']['user_as_ally']['Type']) != 'BIGINT(20) UNSIGNED')
    {
      upd_alter_table('users', array(
        "ADD COLUMN user_as_ally BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_user_user_as_ally` (`user_as_ally`)",

        "ADD CONSTRAINT `FK_user_user_as_ally` FOREIGN KEY (`user_as_ally`) REFERENCES `{$config->db_prefix}alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);

      upd_alter_table('alliance', array(
        "ADD COLUMN ally_user_id BIGINT(20) UNSIGNED DEFAULT NULL",

        "ADD KEY `I_ally_user_id` (`ally_user_id`)",

        "ADD CONSTRAINT `FK_ally_ally_user_id` FOREIGN KEY (`ally_user_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), true);
    }

    // ------------------------------------------------------------------------
    // Creating players for allies
    $ally_row_list = doquery("SELECT `id`, `ally_tag` FROM {{alliance}} WHERE ally_user_id IS NULL;");
    while($ally_row = mysql_fetch_assoc($ally_row_list))
    {
      $ally_user_name = mysql_escape_string("[{$ally_row['ally_tag']}]");
      doquery("INSERT INTO {{users}} SET `username` = '{$ally_user_name}', `register_time` = {$time_now}, `user_as_ally` = {$ally_row['id']};");
      $ally_user_id = mysql_insert_id();
      doquery("UPDATE {{alliance}} SET ally_user_id = {$ally_user_id} WHERE id = {$ally_row['id']} LIMIT 1;");
    }
    // Renaming old ally players TODO: Remove on release
    upd_do_query("UPDATE {{users}} AS u LEFT JOIN {{alliance}} AS a ON u.user_as_ally = a.id SET u.username = CONCAT('[', a.ally_tag, ']') WHERE u.user_as_ally IS NOT NULL AND u.username = '';");
    // Setting last online time to old ally players TODO: Remove on release
    upd_do_query("UPDATE {{users}} SET `onlinetime` = {$time_now} WHERE onlinetime = 0;");

    // ------------------------------------------------------------------------
    // Creating planets for allies
    $ally_user_list = doquery("SELECT `id`, `username` FROM {{users}} WHERE `user_as_ally` IS NOT NULL AND `id_planet` = 0;");
    while($ally_user_row = mysql_fetch_assoc($ally_user_list))
    {
      $ally_planet_name = mysql_escape_string($ally_user_row['username']);
      doquery("INSERT INTO {{planets}} SET `name` = '{$ally_planet_name}', `last_update` = {$time_now}, `id_owner` = {$ally_user_row['id']};");
      $ally_planet_id = mysql_insert_id();
      doquery("UPDATE {{users}} SET `id_planet` = {$ally_planet_id} WHERE `id` = {$ally_user_row['id']} LIMIT 1;");
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
    if($update_tables['users']['b_tech_planet'])
    {
      $query = doquery("SELECT * FROM {{planets}} WHERE `b_tech_id` <> 0;");
      while($planet_row = mysql_fetch_assoc($query))
      {
        $que_item_string = "{$planet_row['b_tech_id']},1," . max(0, $planet_row['b_tech'] - $time_now) . "," . BUILD_CREATE . "," . QUE_RESEARCH;
        doquery("UPDATE {{users}} SET `que` = '{$que_item_string}' WHERE `id` = {$planet_row['id_owner']} LIMIT 1;");
      }

      upd_alter_table('planets', array(
        "DROP COLUMN `b_tech`",
        "DROP COLUMN `b_tech_id`",
      ), $update_tables['planets']['b_tech']);

      upd_alter_table('users', "DROP COLUMN `b_tech_planet`", $update_tables['users']['b_tech_planet']);
    }

    if(!$update_tables['powerup']['powerup_category'])
    {
      upd_alter_table('powerup', "ADD COLUMN `powerup_category` SMALLINT NOT NULL DEFAULT 0 AFTER `powerup_planet_id`", !$update_tables['powerup']['powerup_category']);

      doquery("UPDATE {{powerup}} SET powerup_category = " . BONUS_MERCENARY);
    }

    upd_check_key('rpg_cost_info', 10000, !isset($config->rpg_cost_info));
    upd_check_key('tpl_minifier', 0, !isset($config->tpl_minifier));

    upd_check_key('server_updater_check_auto', 0, !isset($config->server_updater_check_auto));
    upd_check_key('server_updater_check_period', PERIOD_DAY, !isset($config->server_updater_check_period));
    upd_check_key('server_updater_check_last', 0, !isset($config->server_updater_check_last));
    upd_check_key('server_updater_check_result', SNC_VER_NEVER, !isset($config->server_updater_check_result));
    upd_check_key('server_updater_key', '', !isset($config->server_updater_key));
    upd_check_key('server_updater_id', 0, !isset($config->server_updater_id));

    upd_check_key('ali_bonus_algorithm', 0, !isset($config->ali_bonus_algorithm));
    upd_check_key('ali_bonus_divisor', 10000000, !isset($config->ali_bonus_divisor));
    upd_check_key('ali_bonus_brackets', 10, !isset($config->ali_bonus_brackets));
    upd_check_key('ali_bonus_brackets_divisor', 50, !isset($config->ali_bonus_brackets_divisor));

    if(!$config->db_loadItem('rpg_flt_explore'))
    {
      $inflation_rate = 1000;

      $config->db_saveItem('rpg_cost_banker', $config->rpg_cost_banker * $inflation_rate);
      $config->db_saveItem('rpg_cost_exchange', $config->rpg_cost_exchange * $inflation_rate);
      $config->db_saveItem('rpg_cost_pawnshop', $config->rpg_cost_pawnshop * $inflation_rate);
      $config->db_saveItem('rpg_cost_scraper', $config->rpg_cost_scraper * $inflation_rate);
      $config->db_saveItem('rpg_cost_stockman', $config->rpg_cost_stockman * $inflation_rate);
      $config->db_saveItem('rpg_cost_trader', $config->rpg_cost_trader * $inflation_rate);

      $config->db_saveItem('rpg_exchange_darkMatter', $config->rpg_exchange_darkMatter / $inflation_rate * 4);

      $config->db_saveItem('rpg_flt_explore', $inflation_rate);

      doquery("UPDATE {{users}} SET `dark_matter` = `dark_matter` * {$inflation_rate};");

      $query = doquery("SELECT * FROM {{quest}}");
      while($row = mysql_fetch_assoc($query))
      {
        $query_add = '';
        $quest_reward_list = explode(';', $row['quest_rewards']);
        foreach($quest_reward_list as &$quest_reward)
        {
          list($reward_resource, $reward_amount) = explode(',', $quest_reward);
          if($reward_resource == RES_DARK_MATTER)
          {
            $quest_reward = "{$reward_resource}," . $reward_amount * 1000;
          }
        }
        $new_rewards = implode(';', $quest_reward_list);
        if($new_rewards != $row['quest_rewards'])
        {
          doquery("UPDATE {{quest}} SET `quest_rewards` = '{$new_rewards}' WHERE quest_id = {$row['quest_id']} LIMIT 1;");
        }
      }
    }

    upd_check_key('rpg_bonus_minimum', 10000, !isset($config->rpg_bonus_minimum));
    upd_check_key('rpg_bonus_divisor',
      !isset($config->rpg_bonus_divisor) ? 10 : ($config->rpg_bonus_divisor >= 1000 ? floor($config->rpg_bonus_divisor / 1000) : $config->rpg_bonus_divisor),
      !isset($config->rpg_bonus_divisor) || $config->rpg_bonus_divisor >= 1000);

    upd_check_key('var_news_last', 0, !isset($config->var_news_last));

    upd_do_query('COMMIT;', true);
    $new_version = 33;

  case 33:
    upd_log_version_update();

    upd_alter_table('users', array(
      "ADD `user_birthday` DATE DEFAULT NULL COMMENT 'User birthday'",
      "ADD `user_birthday_celebrated` DATE DEFAULT NULL COMMENT 'Last time where user got birthday gift'",

      "ADD KEY `I_user_birthday` (`user_birthday`, `user_birthday_celebrated`)",
    ), !$update_tables['users']['user_birthday']);

    upd_check_key('user_birthday_gift', 0, !isset($config->user_birthday_gift));
    upd_check_key('user_birthday_range', 30, !isset($config->user_birthday_range));
    upd_check_key('user_birthday_celebrate', 0, !isset($config->user_birthday_celebrate));

    if(!isset($update_tables['payment']))
    {
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

          CONSTRAINT `FK_payment_user` FOREIGN KEY (`payment_user_id`, `payment_user_name`) REFERENCES `{$config->db_prefix}users` (`id`, `username`) ON UPDATE CASCADE ON DELETE NO ACTION
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
      );

      upd_check_key('payment_currency_default', 'UAH', !isset($config->payment_currency_default));
    }
    upd_check_key('payment_lot_size', 1000, !isset($config->payment_lot_size));
    upd_check_key('payment_lot_price', 1, !isset($config->payment_lot_price));

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


    if(!$update_tables['payment']['payment_status'])
    {
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

    upd_check_key('server_start_date', date('d.m.Y', $time_now), !isset($config->server_start_date));
    upd_check_key('server_que_length_structures', 5, !isset($config->server_que_length_structures));
    upd_check_key('server_que_length_hangar', 5, !isset($config->server_que_length_hangar));

    upd_check_key('chat_highlight_moderator', '<span class="nick_moderator">$1</span>', $config->chat_highlight_admin == '<font color=green>$1</font>');
    upd_check_key('chat_highlight_operator', '<span class="nick_operator">$1</span>', $config->chat_highlight_admin == '<font color=red>$1</font>');
    upd_check_key('chat_highlight_admin', '<span class="nick_admin">$1</span>', $config->chat_highlight_admin == '<font color=purple>$1</font>');

    upd_check_key('chat_highlight_premium', '<span class="nick_premium">$1</span>', !isset($config->chat_highlight_premium));

    upd_do_query("UPDATE {{planets}} SET `PLANET_GOVERNOR_LEVEL` = CEILING(`PLANET_GOVERNOR_LEVEL`/2) WHERE PLANET_GOVERNOR_ID = " . MRC_ENGINEER . " AND `PLANET_GOVERNOR_LEVEL` > 8;");


    upd_do_query('COMMIT;', true);
    $new_version = 34;

  case 34:
    upd_log_version_update();

    upd_alter_table('planets', array(
      "ADD COLUMN `planet_teleport_next` INT(11) NOT NULL DEFAULT 0 COMMENT 'Next teleport time'",
    ), !$update_tables['planets']['planet_teleport_next']);

    upd_check_key('planet_teleport_cost', 50000, !isset($config->planet_teleport_cost));
    upd_check_key('planet_teleport_timeout', PERIOD_DAY * 1, !isset($config->planet_teleport_timeout));

    upd_check_key('planet_capital_cost', 25000, !isset($config->planet_capital_cost));

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

    upd_check_key('stats_hide_admins', 1, !isset($config->stats_hide_admins));
    upd_check_key('stats_hide_player_list', '', !isset($config->stats_hide_player_list));

    upd_check_key('adv_seo_meta_description', '', !isset($config->adv_seo_meta_description));
    upd_check_key('adv_seo_meta_keywords', '', !isset($config->adv_seo_meta_keywords));

    upd_check_key('stats_hide_pm_link', '0', !isset($config->stats_hide_pm_link));

    upd_alter_table('notes', array(
      "ADD INDEX `I_owner_priority_time` (`owner`, `priority`, `time`)",
    ), !$update_indexes['notes']['I_owner_priority_time']);

    if(!$update_tables['buddy']['BUDDY_ID'])
    {
      upd_alter_table('buddy', array(
        "CHANGE COLUMN `id` `BUDDY_ID` SERIAL COMMENT 'Buddy table row ID'",
        "CHANGE COLUMN `active` `BUDDY_STATUS` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Buddy request status'",
        "CHANGE COLUMN `text` `BUDDY_REQUEST` TINYTEXT DEFAULT '' COMMENT 'Buddy request text'", // 255 chars

        "DROP INDEX `id`",

        "DROP FOREIGN KEY `FK_buddy_sender_id`",
        "DROP FOREIGN KEY `FK_buddy_owner_id`",
        "DROP INDEX `I_buddy_sender`",
        "DROP INDEX `I_buddy_owner`",
      ), !$update_tables['buddy']['BUDDY_ID']);

      upd_alter_table('buddy', array(
        "CHANGE COLUMN `sender` `BUDDY_SENDER_ID` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Buddy request sender ID'",
        "CHANGE COLUMN `owner` `BUDDY_OWNER_ID` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT 'Buddy request recipient ID'",
      ), !$update_tables['buddy']['BUDDY_SENDER']);

      $query = upd_do_query("SELECT `BUDDY_ID`, `BUDDY_SENDER_ID`, `BUDDY_OWNER_ID` FROM {{buddy}} ORDER BY `BUDDY_ID`;");
      $found = $lost = array();
      while($row = mysql_fetch_assoc($query))
      {
        $index = min($row['BUDDY_SENDER_ID'], $row['BUDDY_OWNER_ID']) . ';' . max($row['BUDDY_SENDER_ID'], $row['BUDDY_OWNER_ID']);
        if(!isset($found[$index]))
        {
          $found[$index] = $row['BUDDY_ID'];
        }
        else
        {
          $lost[] = $row['BUDDY_ID'];
        }
      }
      $lost = implode(',', $lost);
      if($lost)
      {
        upd_do_query("DELETE FROM {{buddy}} WHERE `BUDDY_ID` IN ({$lost})");
      }

      upd_alter_table('buddy', array(
          "ADD KEY `I_BUDDY_SENDER_ID` (`BUDDY_SENDER_ID`, `BUDDY_OWNER_ID`)",
          "ADD KEY `I_BUDDY_OWNER_ID` (`BUDDY_OWNER_ID`, `BUDDY_SENDER_ID`)",

          "ADD CONSTRAINT `FK_BUDDY_SENDER_ID` FOREIGN KEY (`BUDDY_SENDER_ID`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
          "ADD CONSTRAINT `FK_BUDDY_OWNER_ID` FOREIGN KEY (`BUDDY_OWNER_ID`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      ), !$update_indexes['buddy']['I_BUDDY_SENDER_ID']);
    }

    upd_do_query('COMMIT;', true);
    $new_version = 35;

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
          -- `unit_bind_type` TINYINT NOT NULL DEFAULT 0 COMMENT 'Binding - where unit is originally belongs', -- unused so far
          -- `unit_bind_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Location ID', -- unused so far
          `unit_type` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit type',
          `unit_snid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit SuperNova ID',
          -- `unit_dbid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit exemplar DB ID in respective table', -- does it really needs?
          -- `unit_guid` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit unique GUID', -- unused for now. Will be need when GUID would be implemented
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
          -- `captain_level_free` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain level free to spend',

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

    upd_alter_table('chat', array(
      "DROP FOREIGN KEY `FK_chat_message_sender_user_id`",
      "DROP FOREIGN KEY `FK_chat_message_sender_recipient_id`",
    ), true);
    upd_alter_table('chat', array(
      "ADD CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
      "ADD CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `{$config->db_prefix}users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE",
    ), true);

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

    upd_do_query('COMMIT;', true);
//    $new_version = 37;
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
