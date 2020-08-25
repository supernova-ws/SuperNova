SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Records of sn_server_patches
-- ----------------------------
INSERT INTO `sn_server_patches` VALUES (1, '2018-11-12 07:50:14');
INSERT INTO `sn_server_patches` VALUES (2, '2018-11-12 07:50:14');
INSERT INTO `sn_server_patches` VALUES (3, '2018-11-12 07:50:14');
INSERT INTO `sn_server_patches` VALUES (4, '2018-11-12 07:50:14');
INSERT INTO `sn_server_patches` VALUES (6, '2020-07-27 13:20:18');
INSERT INTO `sn_server_patches` VALUES (7, '2020-07-27 13:20:18');
INSERT INTO `sn_server_patches` VALUES (8, '2020-07-27 13:20:18');
INSERT INTO `sn_server_patches` VALUES (9, '2020-07-27 13:20:19');
INSERT INTO `sn_server_patches` VALUES (10, '2020-07-27 13:20:19');

-- ----------------------------
-- Default server configuration
-- ----------------------------
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuCode', '<script type=\"text/javascript\"><!--\r\ngoogle_ad_client = \"pub-1914310741599503\";\r\n/* oGame */\r\ngoogle_ad_slot = \"2544836773\";\r\ngoogle_ad_width = 125;\r\ngoogle_ad_height = 125;\r\n//-->\r\n</script>\r\n<script type=\"text/javascript\"\r\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\r\n</script>\r\n');
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuIsOn', '1');
INSERT INTO `sn_config` VALUES ('adv_conversion_code_payment', '');
INSERT INTO `sn_config` VALUES ('adv_conversion_code_register', '');
INSERT INTO `sn_config` VALUES ('adv_seo_javascript', '');
INSERT INTO `sn_config` VALUES ('adv_seo_meta_description', '');
INSERT INTO `sn_config` VALUES ('adv_seo_meta_keywords', '');
INSERT INTO `sn_config` VALUES ('ali_bonus_algorithm', '0');
INSERT INTO `sn_config` VALUES ('ali_bonus_brackets', '10');
INSERT INTO `sn_config` VALUES ('ali_bonus_brackets_divisor', '50');
INSERT INTO `sn_config` VALUES ('ali_bonus_divisor', '10000000');
INSERT INTO `sn_config` VALUES ('ali_bonus_members', '10');
INSERT INTO `sn_config` VALUES ('allow_buffing', '0');
INSERT INTO `sn_config` VALUES ('ally_help_weak', '0');
INSERT INTO `sn_config` VALUES ('avatar_max_height', '128');
INSERT INTO `sn_config` VALUES ('avatar_max_width', '128');
INSERT INTO `sn_config` VALUES ('BuildLabWhileRun', '0');
INSERT INTO `sn_config` VALUES ('chat_highlight_admin', '<span class=\"nick_admin\">$1</span>');
INSERT INTO `sn_config` VALUES ('chat_highlight_developer', '<span class=\"nick_developer\">$1</span>');
INSERT INTO `sn_config` VALUES ('chat_highlight_moderator', '<font color=green>$1</font>');
INSERT INTO `sn_config` VALUES ('chat_highlight_operator', '<font color=red>$1</font>');
INSERT INTO `sn_config` VALUES ('chat_highlight_premium', '<span class=\"nick_premium\">$1</span>');
INSERT INTO `sn_config` VALUES ('chat_refresh_rate', '5');
INSERT INTO `sn_config` VALUES ('chat_timeout', 15 * 60);
INSERT INTO `sn_config` VALUES ('COOKIE_NAME', 'SuperNova');
INSERT INTO `sn_config` VALUES ('crystal_basic_income', '20');
INSERT INTO `sn_config` VALUES ('db_manual_lock_enabled', '0');
INSERT INTO `sn_config` VALUES ('db_prefix', 'sn_');
INSERT INTO `sn_config` VALUES ('db_version', '45');
INSERT INTO `sn_config` VALUES ('debug', '0');
INSERT INTO `sn_config` VALUES ('Defs_Cdr', '30');
INSERT INTO `sn_config` VALUES ('deuterium_basic_income', '0');
INSERT INTO `sn_config` VALUES ('eco_planet_starting_crystal', '500');
INSERT INTO `sn_config` VALUES ('eco_planet_starting_deuterium', '0');
INSERT INTO `sn_config` VALUES ('eco_planet_starting_metal', '500');
INSERT INTO `sn_config` VALUES ('eco_planet_storage_crystal', '500000');
INSERT INTO `sn_config` VALUES ('eco_planet_storage_deuterium', '500000');
INSERT INTO `sn_config` VALUES ('eco_planet_storage_metal', '500000');
INSERT INTO `sn_config` VALUES ('eco_scale_storage', '1');
INSERT INTO `sn_config` VALUES ('eco_stockman_fleet', '');
INSERT INTO `sn_config` VALUES ('eco_stockman_fleet_populate', '1');
INSERT INTO `sn_config` VALUES ('empire_mercenary_base_period', 30 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('empire_mercenary_temporary', '1');
INSERT INTO `sn_config` VALUES ('energy_basic_income', '0');
INSERT INTO `sn_config` VALUES ('fleet_bashing_attacks', 3);
INSERT INTO `sn_config` VALUES ('fleet_bashing_interval', 30 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_scope', 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_war_delay', 12 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_waves', 3);
INSERT INTO `sn_config` VALUES ('Fleet_Cdr', '30');
INSERT INTO `sn_config` VALUES ('fleet_speed', '1');
INSERT INTO `sn_config` VALUES ('fleet_update_interval', '4');
INSERT INTO `sn_config` VALUES ('fleet_update_last', NOW());
INSERT INTO `sn_config` VALUES ('fleet_update_lock', '');
INSERT INTO `sn_config` VALUES ('fleet_update_max_run_time', '30');
INSERT INTO `sn_config` VALUES ('game_adminEmail', 'root@localhost');
INSERT INTO `sn_config` VALUES ('game_counter', '0');
INSERT INTO `sn_config` VALUES ('game_default_language', 'ru');
INSERT INTO `sn_config` VALUES ('game_default_skin', 'skins/EpicBlue/');
INSERT INTO `sn_config` VALUES ('game_default_template', 'OpenGame');
INSERT INTO `sn_config` VALUES ('game_disable', '0');
INSERT INTO `sn_config` VALUES ('game_disable_reason', 'SuperNova is in maintenance mode! Please return later!');
INSERT INTO `sn_config` VALUES ('game_email_pm', '0');
INSERT INTO `sn_config` VALUES ('game_installed', '0');
INSERT INTO `sn_config` VALUES ('game_maxGalaxy', '5');
INSERT INTO `sn_config` VALUES ('game_maxPlanet', '15');
INSERT INTO `sn_config` VALUES ('game_maxSystem', '199');
INSERT INTO `sn_config` VALUES ('game_mode', '0');
INSERT INTO `sn_config` VALUES ('game_multiaccount_enabled', '0');
INSERT INTO `sn_config` VALUES ('game_name', 'SuperNova');
INSERT INTO `sn_config` VALUES ('game_news_actual', '259200');
INSERT INTO `sn_config` VALUES ('game_news_overview', '3');
INSERT INTO `sn_config` VALUES ('game_news_overview_show', 2 * 7 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('game_noob_factor', '5');
INSERT INTO `sn_config` VALUES ('game_noob_points', '5000');
INSERT INTO `sn_config` VALUES ('game_speed', '1');
INSERT INTO `sn_config` VALUES ('game_speed_expedition', '1');
INSERT INTO `sn_config` VALUES ('game_users_online_timeout', 15 * 60);
INSERT INTO `sn_config` VALUES ('game_user_changename', '2');
INSERT INTO `sn_config` VALUES ('game_user_changename_cost', '100000');
INSERT INTO `sn_config` VALUES ('geoip_whois_url', 'https://who.is/whois-ip/ip-address/');
INSERT INTO `sn_config` VALUES ('initial_fields', '163');
INSERT INTO `sn_config` VALUES ('int_banner_background', 'design/images/banner.png');
INSERT INTO `sn_config` VALUES ('int_banner_fontInfo', 'terminator.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_fontRaids', 'klmnfp2005.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_fontUniverse', 'cristal.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_showInOverview', '1');
INSERT INTO `sn_config` VALUES ('int_banner_URL', 'banner.php?type=banner');
INSERT INTO `sn_config` VALUES ('int_format_date', 'd.m.Y');
INSERT INTO `sn_config` VALUES ('int_format_time', 'H:i:s');
INSERT INTO `sn_config` VALUES ('int_userbar_background', 'design/images/userbar.png');
INSERT INTO `sn_config` VALUES ('int_userbar_font', 'arialbd.ttf');
INSERT INTO `sn_config` VALUES ('int_userbar_showInOverview', '1');
INSERT INTO `sn_config` VALUES ('int_userbar_URL', 'banner.php?type=userbar');
INSERT INTO `sn_config` VALUES ('LastSettedGalaxyPos', '1');
INSERT INTO `sn_config` VALUES ('LastSettedPlanetPos', '1');
INSERT INTO `sn_config` VALUES ('LastSettedSystemPos', '1');
INSERT INTO `sn_config` VALUES ('locale_cache_disable', '0');
INSERT INTO `sn_config` VALUES ('metal_basic_income', '40');
INSERT INTO `sn_config` VALUES ('payment_currency_default', 'USD');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_dm_', '20000');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_eur', '0.9');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_mm_', '20000');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_rub', '60');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_uah', '30');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_usd', '1');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmb', '18000');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wme', '0.9');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmr', '60');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmu', '30');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmz', '1');
INSERT INTO `sn_config` VALUES ('payment_lot_price', '1');
INSERT INTO `sn_config` VALUES ('payment_lot_size', '2500');
INSERT INTO `sn_config` VALUES ('planet_capital_cost', '25000');
INSERT INTO `sn_config` VALUES ('planet_teleport_cost', '50000');
INSERT INTO `sn_config` VALUES ('planet_teleport_timeout', 1 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('player_delete_time', 45 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('player_max_colonies', '9');
INSERT INTO `sn_config` VALUES ('player_metamatter_immortal', '100000');
INSERT INTO `sn_config` VALUES ('player_vacation_time', 7 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('player_vacation_timeout', 7 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('quest_total', '0');
INSERT INTO `sn_config` VALUES ('resource_multiplier', '1');
INSERT INTO `sn_config` VALUES ('rpg_bonus_divisor', '10');
INSERT INTO `sn_config` VALUES ('rpg_bonus_minimum', '10000');
INSERT INTO `sn_config` VALUES ('rpg_cost_banker', '1000');
INSERT INTO `sn_config` VALUES ('rpg_cost_exchange', '1000');
INSERT INTO `sn_config` VALUES ('rpg_cost_info', '10000');
INSERT INTO `sn_config` VALUES ('rpg_cost_pawnshop', '1000');
INSERT INTO `sn_config` VALUES ('rpg_cost_scraper', '1000');
INSERT INTO `sn_config` VALUES ('rpg_cost_stockman', '1000');
INSERT INTO `sn_config` VALUES ('rpg_cost_trader', '1000');
INSERT INTO `sn_config` VALUES ('rpg_exchange_crystal', '2');
INSERT INTO `sn_config` VALUES ('rpg_exchange_darkMatter', '400');
INSERT INTO `sn_config` VALUES ('rpg_exchange_deuterium', '4');
INSERT INTO `sn_config` VALUES ('rpg_exchange_metal', '1');
INSERT INTO `sn_config` VALUES ('rpg_flt_explore', '1000');
INSERT INTO `sn_config` VALUES ('rpg_scrape_crystal', '0.50');
INSERT INTO `sn_config` VALUES ('rpg_scrape_deuterium', '0.25');
INSERT INTO `sn_config` VALUES ('rpg_scrape_metal', '0.75');
INSERT INTO `sn_config` VALUES ('secret_word', 'SuperNova');
INSERT INTO `sn_config` VALUES ('security_ban_extra', '');
INSERT INTO `sn_config` VALUES ('security_write_full_url_disabled', '1');
INSERT INTO `sn_config` VALUES ('server_email', 'root@localhost');
INSERT INTO `sn_config` VALUES ('server_log_online', '0');
INSERT INTO `sn_config` VALUES ('server_que_length_hangar', '5');
INSERT INTO `sn_config` VALUES ('server_que_length_research', '1');
INSERT INTO `sn_config` VALUES ('server_que_length_structures', '5');
INSERT INTO `sn_config` VALUES ('server_start_date', DATE_FORMAT(CURDATE(), '%d.%m.%Y'));
INSERT INTO `sn_config` VALUES ('server_updater_check_auto', '0');
INSERT INTO `sn_config` VALUES ('server_updater_check_last', '0');
INSERT INTO `sn_config` VALUES ('server_updater_check_period', 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('server_updater_check_result', '-1');
INSERT INTO `sn_config` VALUES ('server_updater_id', '0');
INSERT INTO `sn_config` VALUES ('server_updater_key', '');
INSERT INTO `sn_config` VALUES ('stats_hide_admins', '1');
INSERT INTO `sn_config` VALUES ('stats_hide_player_list', '');
INSERT INTO `sn_config` VALUES ('stats_hide_pm_link', '0');
INSERT INTO `sn_config` VALUES ('stats_history_days', '7');
INSERT INTO `sn_config` VALUES ('stats_minimal_interval', 10 * 60);
INSERT INTO `sn_config` VALUES ('stats_php_memory', '1024M');
INSERT INTO `sn_config` VALUES ('stats_schedule', '04:00:00');
INSERT INTO `sn_config` VALUES ('tpl_minifier', '1');
INSERT INTO `sn_config` VALUES ('tutorial_first_item', '1');
INSERT INTO `sn_config` VALUES ('ube_capture_points_diff', '2');
INSERT INTO `sn_config` VALUES ('uni_galaxy_distance', '20000');
INSERT INTO `sn_config` VALUES ('uni_price_galaxy', '10000');
INSERT INTO `sn_config` VALUES ('uni_price_system', '1000');
INSERT INTO `sn_config` VALUES ('upd_lock_time', '60');
INSERT INTO `sn_config` VALUES ('url_dark_matter', '');
INSERT INTO `sn_config` VALUES ('url_faq', 'http://faq.supernova.ws/');
INSERT INTO `sn_config` VALUES ('url_forum', '');
INSERT INTO `sn_config` VALUES ('url_purchase_metamatter', '');
INSERT INTO `sn_config` VALUES ('url_rules', '');
INSERT INTO `sn_config` VALUES ('users_amount', '1');
INSERT INTO `sn_config` VALUES ('user_birthday_celebrate', '0');
INSERT INTO `sn_config` VALUES ('user_birthday_gift', '0');
INSERT INTO `sn_config` VALUES ('user_birthday_range', '30');
INSERT INTO `sn_config` VALUES ('user_vacation_disable', '0');
INSERT INTO `sn_config` VALUES ('var_db_update', '0');
INSERT INTO `sn_config` VALUES ('var_db_update_end', '0');
INSERT INTO `sn_config` VALUES ('var_news_last', '0');
INSERT INTO `sn_config` VALUES ('var_online_user_count', 0);
INSERT INTO `sn_config` VALUES ('var_online_user_time', 0);
INSERT INTO `sn_config` VALUES ('var_stat_update', '0');
INSERT INTO `sn_config` VALUES ('var_stat_update_end', '0');
INSERT INTO `sn_config` VALUES ('var_stat_update_msg', '');
INSERT INTO `sn_config` VALUES ('var_stat_update_next', '');

-- ----------------------------
-- Administrator's account
-- Login: admin
-- Password: admin
-- ----------------------------
INSERT INTO `sn_account`
SET
    `account_id`       = 1,
    `account_name`     = 'admin',
    `account_password` = '21232f297a57a5a743894a0e4a801fc3',
    `account_email`    = 'root@localhost',
    `account_language` = 'ru';

-- ----------------------------
-- Administrator's user record
-- Login: admin
-- Password: admin
-- ----------------------------
INSERT INTO `sn_users`
SET
    `id`             = 1,
    `username`       = 'admin',
    `password`       = '21232f297a57a5a743894a0e4a801fc3',
    `email`          = 'root@localhost',
    `email_2`        = 'root@localhost',
    `authlevel`      = 3,
    `id_planet`      = 1,
    `galaxy`         = 1,
    `system`         = 1,
    `planet`         = 1,
    `current_planet` = 1,
    `register_time`  = UNIX_TIMESTAMP(NOW()),
    `onlinetime`     = UNIX_TIMESTAMP(NOW()),
    `noipcheck`      = 1;

-- ----------------------------
-- Administrator's account translation to user record
-- ----------------------------
REPLACE INTO `sn_account_translate`
SET
    `provider_id`         = 1,
    `provider_account_id` = 1,
    `user_id`             = 1,
    `timestamp`           = NOW();

-- ----------------------------
-- Reserved 'admin' name
-- ----------------------------
INSERT INTO `sn_player_name_history`
SET
    player_id   = 1,
    player_name = 'admin';

-- ----------------------------
-- Administrator's planet
-- ----------------------------
INSERT INTO `sn_planets`
SET
    `id`          = 1,
    `name`        = 'Planet',
    `id_owner`    = 1,
    `id_level`    = 0,
    `galaxy`      = 1,
    `system`      = 1,
    `planet`      = 1,
    `planet_type` = 1,
    `last_update` = UNIX_TIMESTAMP(NOW())
-- 'normaltempplanet01'
;

SET FOREIGN_KEY_CHECKS = 1;
