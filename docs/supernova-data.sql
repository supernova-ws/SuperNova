-- ----------------------------
-- Default server configuration
-- ----------------------------
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuCode', '(Place here code for banner)');
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuIsOn', '0');
INSERT INTO `sn_config` VALUES ('allow_buffing', '0');
INSERT INTO `sn_config` VALUES ('ally_help_weak', '0');
INSERT INTO `sn_config` VALUES ('BuildLabWhileRun', '0');
INSERT INTO `sn_config` VALUES ('chat_highlight_admin', '<font color=purple>$1</font>');
INSERT INTO `sn_config` VALUES ('chat_highlight_moderator', '<font color=green>$1</font>');
INSERT INTO `sn_config` VALUES ('chat_highlight_operator', '<font color=red>$1</font>');
INSERT INTO `sn_config` VALUES ('chat_timeout', '900');
INSERT INTO `sn_config` VALUES ('COOKIE_NAME', 'SuperNova');
INSERT INTO `sn_config` VALUES ('crystal_basic_income', '20');
INSERT INTO `sn_config` VALUES ('db_version', '28');
INSERT INTO `sn_config` VALUES ('debug', '0');
INSERT INTO `sn_config` VALUES ('Defs_Cdr', '30');
INSERT INTO `sn_config` VALUES ('deuterium_basic_income', '0');
INSERT INTO `sn_config` VALUES ('eco_stockman_fleet', '');
INSERT INTO `sn_config` VALUES ('energy_basic_income', '0');
INSERT INTO `sn_config` VALUES ('fleet_bashing_war_delay', 12 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_scope', 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_interval', 30 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_waves', 3);
INSERT INTO `sn_config` VALUES ('fleet_bashing_attacks', 3);
INSERT INTO `sn_config` VALUES ('fleet_buffing_check', 1);
INSERT INTO `sn_config` VALUES ('Fleet_Cdr', '30');
INSERT INTO `sn_config` VALUES ('fleet_speed', '1');
INSERT INTO `sn_config` VALUES ('flt_lastUpdate', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('game_adminEmail', 'root@localhost');
INSERT INTO `sn_config` VALUES ('game_counter', '0');
INSERT INTO `sn_config` VALUES ('game_default_language', 'ru');
INSERT INTO `sn_config` VALUES ('game_default_skin', 'skins/EpicBlue/');
INSERT INTO `sn_config` VALUES ('game_default_template', 'OpenGame');
INSERT INTO `sn_config` VALUES ('game_disable', '0');
INSERT INTO `sn_config` VALUES ('game_disable_reason', 'SuperNova is in maintenance mode! Please return later!');
INSERT INTO `sn_config` VALUES ('game_maxGalaxy', '5');
INSERT INTO `sn_config` VALUES ('game_maxPlanet', '15');
INSERT INTO `sn_config` VALUES ('game_maxSystem', '199');
INSERT INTO `sn_config` VALUES ('game_mode', '0');
INSERT INTO `sn_config` VALUES ('game_name', 'SuperNova');
INSERT INTO `sn_config` VALUES ('game_news_actual', '259200');
INSERT INTO `sn_config` VALUES ('game_news_overview', '3');
INSERT INTO `sn_config` VALUES ('game_noob_factor', '5');
INSERT INTO `sn_config` VALUES ('game_noob_points', '5000');
INSERT INTO `sn_config` VALUES ('game_speed', '1');
INSERT INTO `sn_config` VALUES ('game_user_changename', '0');
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
INSERT INTO `sn_config` VALUES ('metal_basic_income', '40');
INSERT INTO `sn_config` VALUES ('player_max_colonies', '9');
INSERT INTO `sn_config` VALUES ('quest_total', '0');
INSERT INTO `sn_config` VALUES ('resource_multiplier', '1');
INSERT INTO `sn_config` VALUES ('rpg_bonus_divisor', '10');
INSERT INTO `sn_config` VALUES ('rpg_cost_banker', '1');
INSERT INTO `sn_config` VALUES ('rpg_cost_exchange', '1');
INSERT INTO `sn_config` VALUES ('rpg_cost_pawnshop', '1');
INSERT INTO `sn_config` VALUES ('rpg_cost_scraper', '1');
INSERT INTO `sn_config` VALUES ('rpg_cost_stockman', '1');
INSERT INTO `sn_config` VALUES ('rpg_cost_trader', '1');
INSERT INTO `sn_config` VALUES ('rpg_exchange_crystal', '2');
INSERT INTO `sn_config` VALUES ('rpg_exchange_darkMatter', '100000');
INSERT INTO `sn_config` VALUES ('rpg_exchange_deuterium', '4');
INSERT INTO `sn_config` VALUES ('rpg_exchange_metal', '1');
INSERT INTO `sn_config` VALUES ('rpg_officer', '3');
INSERT INTO `sn_config` VALUES ('rpg_scrape_crystal', '0.50');
INSERT INTO `sn_config` VALUES ('rpg_scrape_deuterium', '0.25');
INSERT INTO `sn_config` VALUES ('rpg_scrape_metal', '0.75');
INSERT INTO `sn_config` VALUES ('stats_schedule', 'd@04:00:00');
INSERT INTO `sn_config` VALUES ('url_dark_matter', '');
INSERT INTO `sn_config` VALUES ('url_faq', '');
INSERT INTO `sn_config` VALUES ('url_forum', '');
INSERT INTO `sn_config` VALUES ('url_rules', '');
INSERT INTO `sn_config` VALUES ('users_amount', '1');
INSERT INTO `sn_config` VALUES ('user_vacation_disable', '0');
INSERT INTO `sn_config` VALUES ('var_db_update', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('var_db_update_end', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('var_stat_update', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('var_stat_update_end', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('var_stat_update_msg', '');

-- ----------------------------
-- Administrator's account
-- Login: admin
-- Password: admin
-- ----------------------------
INSERT INTO `sn_users` (`id`, `username`, `password`, `email`, `email_2`, `authlevel`, `id_planet`, `galaxy`, `system`, `planet`, `current_planet`, `register_time`, `onlinetime`, `noipcheck`) VALUES (1, 'admin',  '21232f297a57a5a743894a0e4a801fc3', 'root@localhost', 'root@localhost', 3, 1, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 1);

-- ----------------------------
-- Administrator's planet
-- ----------------------------
INSERT INTO `sn_planets` (`id`, `name`, `id_owner`, `id_level`, `galaxy`, `system`, `planet`, `planet_type`, `last_update`) VALUES (1, 'Planet', 1, 0, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()));
