SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Default server configuration
-- ----------------------------
REPLACE INTO `sn_config` VALUES ('advGoogleLeftMenuCode',
                                '<script type=\"text/javascript\"><!--\r\ngoogle_ad_client = \"pub-1914310741599503\";\r\n/* oGame */\r\ngoogle_ad_slot = \"2544836773\";\r\ngoogle_ad_width = 125;\r\ngoogle_ad_height = 125;\r\n//-->\r\n</script>\r\n<script type=\"text/javascript\"\r\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\r\n</script>\r\n');
REPLACE INTO `sn_config` VALUES ('advGoogleLeftMenuIsOn', 1);
REPLACE INTO `sn_config` VALUES ('adv_conversion_code_payment', '');
REPLACE INTO `sn_config` VALUES ('adv_conversion_code_register', '');
REPLACE INTO `sn_config` VALUES ('adv_seo_javascript', '');
REPLACE INTO `sn_config` VALUES ('adv_seo_meta_description', '');
REPLACE INTO `sn_config` VALUES ('adv_seo_meta_keywords', '');
REPLACE INTO `sn_config` VALUES ('ali_bonus_algorithm', '0');
REPLACE INTO `sn_config` VALUES ('ali_bonus_brackets', 10);
REPLACE INTO `sn_config` VALUES ('ali_bonus_brackets_divisor', 50);
REPLACE INTO `sn_config` VALUES ('ali_bonus_divisor', '10000000');
REPLACE INTO `sn_config` VALUES ('ali_bonus_members', '10');
REPLACE INTO `sn_config` VALUES ('allow_buffing', '0');
REPLACE INTO `sn_config` VALUES ('ally_help_weak', '0');
REPLACE INTO `sn_config` VALUES ('avatar_max_height', '128');
REPLACE INTO `sn_config` VALUES ('avatar_max_width', '128');
REPLACE INTO `sn_config` VALUES ('BuildLabWhileRun', '0');
REPLACE INTO `sn_config` VALUES ('chat_highlight_admin', '<span class=\"nick_admin\">$1</span>');
REPLACE INTO `sn_config` VALUES ('chat_highlight_developer', '<span class=\"nick_developer\">$1</span>');
REPLACE INTO `sn_config` VALUES ('chat_highlight_moderator', '<font color=green>$1</font>');
REPLACE INTO `sn_config` VALUES ('chat_highlight_operator', '<font color=red>$1</font>');
REPLACE INTO `sn_config` VALUES ('chat_highlight_premium', '<span class=\"nick_premium\">$1</span>');
REPLACE INTO `sn_config` VALUES ('chat_refresh_rate', 5);
REPLACE INTO `sn_config` VALUES ('chat_timeout', 15 * 60);
REPLACE INTO `sn_config` VALUES ('COOKIE_NAME', 'SuperNova');
REPLACE INTO `sn_config` VALUES ('crystal_basic_income', '20');
REPLACE INTO `sn_config` VALUES ('db_manual_lock_enabled', '0');
REPLACE INTO `sn_config` VALUES ('db_prefix', 'sn_');
REPLACE INTO `sn_config` VALUES ('db_version', '40');
REPLACE INTO `sn_config` VALUES ('debug', '0');
REPLACE INTO `sn_config` VALUES ('Defs_Cdr', '30');
REPLACE INTO `sn_config` VALUES ('deuterium_basic_income', '0');
REPLACE INTO `sn_config` VALUES ('eco_planet_starting_crystal', '500');
REPLACE INTO `sn_config` VALUES ('eco_planet_starting_deuterium', '0');
REPLACE INTO `sn_config` VALUES ('eco_planet_starting_metal', '500');
REPLACE INTO `sn_config` VALUES ('eco_planet_storage_crystal', '500000');
REPLACE INTO `sn_config` VALUES ('eco_planet_storage_deuterium', '500000');
REPLACE INTO `sn_config` VALUES ('eco_planet_storage_metal', '500000');
REPLACE INTO `sn_config` VALUES ('eco_scale_storage', '1');
REPLACE INTO `sn_config` VALUES ('eco_stockman_fleet', '');
REPLACE INTO `sn_config` VALUES ('eco_stockman_fleet_populate', '1');
REPLACE INTO `sn_config` VALUES ('empire_mercenary_base_period', 30 * 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('empire_mercenary_temporary', '1');
REPLACE INTO `sn_config` VALUES ('energy_basic_income', '0');
REPLACE INTO `sn_config` VALUES ('event_halloween_2015_code', '');
REPLACE INTO `sn_config` VALUES ('event_halloween_2015_lock', '0');
REPLACE INTO `sn_config` VALUES ('event_halloween_2015_timestamp', NOW());
REPLACE INTO `sn_config` VALUES ('event_halloween_2015_unit', '0');
REPLACE INTO `sn_config` VALUES ('event_halloween_2015_units_used', 'a:0:{}');
REPLACE INTO `sn_config` VALUES ('fleet_bashing_attacks', 3);
REPLACE INTO `sn_config` VALUES ('fleet_bashing_interval', 30 * 60);
REPLACE INTO `sn_config` VALUES ('fleet_bashing_scope', 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('fleet_bashing_war_delay', 12 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('fleet_bashing_waves', 3);
REPLACE INTO `sn_config` VALUES ('Fleet_Cdr', '30');
REPLACE INTO `sn_config` VALUES ('fleet_speed', '1');
REPLACE INTO `sn_config` VALUES ('fleet_update_interval', 4);
REPLACE INTO `sn_config` VALUES ('fleet_update_last', NOW());
REPLACE INTO `sn_config` VALUES ('fleet_update_lock', '');
REPLACE INTO `sn_config` VALUES ('game_adminEmail', 'root@localhost');
REPLACE INTO `sn_config` VALUES ('game_counter', '0');
REPLACE INTO `sn_config` VALUES ('game_default_language', 'ru');
REPLACE INTO `sn_config` VALUES ('game_default_skin', 'skins/EpicBlue/');
REPLACE INTO `sn_config` VALUES ('game_default_template', 'OpenGame');
REPLACE INTO `sn_config` VALUES ('game_disable', '4');
REPLACE INTO `sn_config` VALUES ('game_disable_reason', 'SuperNova is in maintenance mode! Please return later!');
REPLACE INTO `sn_config` VALUES ('game_email_pm', '0');
REPLACE INTO `sn_config` VALUES ('game_maxGalaxy', '5');
REPLACE INTO `sn_config` VALUES ('game_maxPlanet', '15');
REPLACE INTO `sn_config` VALUES ('game_maxSystem', '199');
REPLACE INTO `sn_config` VALUES ('game_mode', '0');
REPLACE INTO `sn_config` VALUES ('game_multiaccount_enabled', '0');
REPLACE INTO `sn_config` VALUES ('game_name', 'SuperNova');
REPLACE INTO `sn_config` VALUES ('game_news_actual', '259200');
REPLACE INTO `sn_config` VALUES ('game_news_overview', '3');
REPLACE INTO `sn_config` VALUES ('game_noob_factor', '5');
REPLACE INTO `sn_config` VALUES ('game_noob_points', '5000');
REPLACE INTO `sn_config` VALUES ('game_speed', '1');
REPLACE INTO `sn_config` VALUES ('game_speed_expedition', '1');
REPLACE INTO `sn_config` VALUES ('game_users_online_timeout', 15 * 60);
REPLACE INTO `sn_config` VALUES ('game_user_changename', '2');
REPLACE INTO `sn_config` VALUES ('game_user_changename_cost', 100000);
REPLACE INTO `sn_config` VALUES ('geoip_whois_url', 'https://who.is/whois-ip/ip-address/');
REPLACE INTO `sn_config` VALUES ('initial_fields', '163');
REPLACE INTO `sn_config` VALUES ('int_banner_background', 'design/images/banner.png');
REPLACE INTO `sn_config` VALUES ('int_banner_fontInfo', 'terminator.ttf');
REPLACE INTO `sn_config` VALUES ('int_banner_fontRaids', 'klmnfp2005.ttf');
REPLACE INTO `sn_config` VALUES ('int_banner_fontUniverse', 'cristal.ttf');
REPLACE INTO `sn_config` VALUES ('int_banner_showInOverview', '1');
REPLACE INTO `sn_config` VALUES ('int_banner_URL', 'banner.php?type=banner');
REPLACE INTO `sn_config` VALUES ('int_format_date', 'd.m.Y');
REPLACE INTO `sn_config` VALUES ('int_format_time', 'H:i:s');
REPLACE INTO `sn_config` VALUES ('int_userbar_background', 'design/images/userbar.png');
REPLACE INTO `sn_config` VALUES ('int_userbar_font', 'arialbd.ttf');
REPLACE INTO `sn_config` VALUES ('int_userbar_showInOverview', '1');
REPLACE INTO `sn_config` VALUES ('int_userbar_URL', 'banner.php?type=userbar');
REPLACE INTO `sn_config` VALUES ('LastSettedGalaxyPos', '1');
REPLACE INTO `sn_config` VALUES ('LastSettedPlanetPos', '1');
REPLACE INTO `sn_config` VALUES ('LastSettedSystemPos', '1');
REPLACE INTO `sn_config` VALUES ('locale_cache_disable', '0');
REPLACE INTO `sn_config` VALUES ('metal_basic_income', '40');
REPLACE INTO `sn_config` VALUES ('payment_currency_default', 'USD');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_dm_', '20000');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_eur', '0.9');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_mm_', '20000');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_rub', '60');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_uah', '30');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_usd', '1');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_wmb', '18000');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_wme', '0.9');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_wmr', '60');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_wmu', '30');
REPLACE INTO `sn_config` VALUES ('payment_currency_exchange_wmz', '1');
REPLACE INTO `sn_config` VALUES ('payment_lot_price', '1');
REPLACE INTO `sn_config` VALUES ('payment_lot_size', '2500');
REPLACE INTO `sn_config` VALUES ('planet_capital_cost', 25000);
REPLACE INTO `sn_config` VALUES ('planet_teleport_cost', 50000);
REPLACE INTO `sn_config` VALUES ('planet_teleport_timeout', 1 * 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('player_delete_time', 45 * 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('player_max_colonies', 9);
REPLACE INTO `sn_config` VALUES ('player_metamatter_immortal', '100000');
REPLACE INTO `sn_config` VALUES ('player_vacation_time', 7 * 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('player_vacation_timeout', 7 * 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('quest_total', '0');
REPLACE INTO `sn_config` VALUES ('resource_multiplier', '1');
REPLACE INTO `sn_config` VALUES ('rpg_bonus_divisor', '10');
REPLACE INTO `sn_config` VALUES ('rpg_bonus_minimum', '10000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_banker', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_exchange', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_info', '10000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_pawnshop', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_scraper', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_stockman', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_cost_trader', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_exchange_crystal', '2');
REPLACE INTO `sn_config` VALUES ('rpg_exchange_darkMatter', '400');
REPLACE INTO `sn_config` VALUES ('rpg_exchange_deuterium', '4');
REPLACE INTO `sn_config` VALUES ('rpg_exchange_metal', '1');
REPLACE INTO `sn_config` VALUES ('rpg_flt_explore', '1000');
REPLACE INTO `sn_config` VALUES ('rpg_scrape_crystal', '0.50');
REPLACE INTO `sn_config` VALUES ('rpg_scrape_deuterium', '0.25');
REPLACE INTO `sn_config` VALUES ('rpg_scrape_metal', '0.75');
REPLACE INTO `sn_config` VALUES ('secret_word', 'SuperNova');
REPLACE INTO `sn_config` VALUES ('security_ban_extra', '');
REPLACE INTO `sn_config` VALUES ('security_write_full_url_disabled', '1');
REPLACE INTO `sn_config` VALUES ('server_email', 'root@localhost');
REPLACE INTO `sn_config` VALUES ('server_log_online', '0');
REPLACE INTO `sn_config` VALUES ('server_que_length_hangar', '5');
REPLACE INTO `sn_config` VALUES ('server_que_length_research', '1');
REPLACE INTO `sn_config` VALUES ('server_que_length_structures', '5');
REPLACE INTO `sn_config` VALUES ('server_start_date', DATE_FORMAT(CURDATE(), '%d.%m.%Y'));
REPLACE INTO `sn_config` VALUES ('server_updater_check_auto', '0');
REPLACE INTO `sn_config` VALUES ('server_updater_check_last', '0');
REPLACE INTO `sn_config` VALUES ('server_updater_check_period', 24 * 60 * 60);
REPLACE INTO `sn_config` VALUES ('server_updater_check_result', '-1');
REPLACE INTO `sn_config` VALUES ('server_updater_id', '0');
REPLACE INTO `sn_config` VALUES ('server_updater_key', '');
REPLACE INTO `sn_config` VALUES ('stats_hide_admins', 1);
REPLACE INTO `sn_config` VALUES ('stats_hide_player_list', '');
REPLACE INTO `sn_config` VALUES ('stats_hide_pm_link', 0);
REPLACE INTO `sn_config` VALUES ('stats_history_days', 7);
REPLACE INTO `sn_config` VALUES ('stats_minimal_interval', 10 * 60);
REPLACE INTO `sn_config` VALUES ('stats_php_memory', '1024M');
REPLACE INTO `sn_config` VALUES ('stats_schedule', '04:00:00');
REPLACE INTO `sn_config` VALUES ('tpl_minifier', 1);
REPLACE INTO `sn_config` VALUES ('ube_capture_points_diff', 2);
REPLACE INTO `sn_config` VALUES ('uni_galaxy_distance', 20000);
REPLACE INTO `sn_config` VALUES ('uni_price_galaxy', '10000');
REPLACE INTO `sn_config` VALUES ('uni_price_system', '1000');
REPLACE INTO `sn_config` VALUES ('upd_lock_time', '60');
REPLACE INTO `sn_config` VALUES ('url_dark_matter', '');
REPLACE INTO `sn_config` VALUES ('url_faq', 'http://faq.supernova.ws/');
REPLACE INTO `sn_config` VALUES ('url_forum', '');
REPLACE INTO `sn_config` VALUES ('url_purchase_metamatter', '');
REPLACE INTO `sn_config` VALUES ('url_rules', '');
REPLACE INTO `sn_config` VALUES ('users_amount', 1);
REPLACE INTO `sn_config` VALUES ('user_birthday_celebrate', '0');
REPLACE INTO `sn_config` VALUES ('user_birthday_gift', '0');
REPLACE INTO `sn_config` VALUES ('user_birthday_range', 30);
REPLACE INTO `sn_config` VALUES ('user_vacation_disable', '0');
REPLACE INTO `sn_config` VALUES ('var_db_update', '0');
REPLACE INTO `sn_config` VALUES ('var_db_update_end', '0');
REPLACE INTO `sn_config` VALUES ('var_news_last', '0');
REPLACE INTO `sn_config` VALUES ('var_online_user_count', 0);
REPLACE INTO `sn_config` VALUES ('var_online_user_time', 0);
REPLACE INTO `sn_config` VALUES ('var_stat_update', '0');
REPLACE INTO `sn_config` VALUES ('var_stat_update_end', '0');
REPLACE INTO `sn_config` VALUES ('var_stat_update_msg', '');

-- ----------------------------
-- Administrator's account
-- Login: admin
-- Password: admin
-- ----------------------------
REPLACE INTO `sn_account`
SET
  `account_id`       = 1,
  `account_name`     = 'admin',
  `account_password` = '21232f297a57a5a743894a0e4a801fc3',
  `account_email`    = 'root@localhost',
  `account_language` = 'ru';

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
-- Administrator's user record
-- Login: admin
-- Password: admin
-- ----------------------------
REPLACE INTO `sn_users`
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
-- Reserved 'admin' name
-- ----------------------------
REPLACE INTO `sn_player_name_history`
SET
  player_id   = 1,
  player_name = 'admin';

-- ----------------------------
-- Administrator's planet
-- ----------------------------
REPLACE INTO `sn_planets`
SET
  `id`          = 1,
  `name`        = 'Planet',
  `id_owner`    = 1,
  `id_level`    = 0,
  `galaxy`      = 1,
  `system`      = 1,
  `planet`      = 1,
  `planet_type` = 1,
  `last_update` = UNIX_TIMESTAMP(NOW());


# -- ----------------------------
# -- Administrator's in-game options
# -- ----------------------------
# REPLACE INTO `sn_player_options` (`player_id`,`option_id`, `value`) VALUES
#   ('1', '12', '1'),
#   ('1', '15', '1'),
#   ('1', '14', '1'),
#   ('1', '16', '1'),
#   ('1', '17', '1'),
#   ('1', '18', '1'),
#   ('1', '19', '1'),
#   ('1', '20', '0'),
#   ('1', '21', '0'),
#   ('1', '22', '500')
# ;
