/*
Navicat MySQL Data Transfer

Source Database       : supernova

Target Server Type    : MYSQL
Target Server Version : 50137
File Encoding         : 65001
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `sn_aks`
-- ----------------------------
DROP TABLE IF EXISTS `sn_aks`;
CREATE TABLE `sn_aks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `teilnehmer` text,
  `flotten` text,
  `ankunft` int(32) DEFAULT NULL,
  `galaxy` int(2) DEFAULT NULL,
  `system` int(4) DEFAULT NULL,
  `planet` int(2) DEFAULT NULL,
  `planet_type` int(11) NOT NULL DEFAULT '1',
  `eingeladen` varchar(50) DEFAULT NULL,
  `fleet_end_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_aks
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance`;
CREATE TABLE `sn_alliance` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `ally_name` varchar(32) DEFAULT '',
  `ally_tag` varchar(8) DEFAULT '',
  `ally_owner` int(11) NOT NULL DEFAULT '0',
  `ally_register_time` int(11) NOT NULL DEFAULT '0',
  `ally_description` text,
  `ally_web` varchar(255) DEFAULT '',
  `ally_text` text,
  `ally_image` varchar(255) DEFAULT '',
  `ally_request` text,
  `ally_request_waiting` text,
  `ally_request_notallow` tinyint(4) NOT NULL DEFAULT '0',
  `ally_owner_range` varchar(32) DEFAULT '',
  `ally_ranks` text,
  `ally_members` int(11) NOT NULL DEFAULT '0',
  `ranklist` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_alliance
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance_requests`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_requests`;
CREATE TABLE `sn_alliance_requests` (
  `id_user` int(11) NOT NULL,
  `id_ally` int(11) NOT NULL DEFAULT '0',
  `request_text` text,
  `request_time` int(11) NOT NULL DEFAULT '0',
  `request_denied` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_ally`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_alliance_requests
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_annonce`
-- ----------------------------
DROP TABLE IF EXISTS `sn_annonce`;
CREATE TABLE `sn_annonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` text NOT NULL,
  `galaxie` int(11) NOT NULL DEFAULT '0',
  `systeme` int(11) NOT NULL DEFAULT '0',
  `metala` bigint(11) NOT NULL DEFAULT '0',
  `cristala` bigint(11) NOT NULL DEFAULT '0',
  `deuta` bigint(11) NOT NULL DEFAULT '0',
  `metals` bigint(11) NOT NULL DEFAULT '0',
  `cristals` bigint(11) NOT NULL DEFAULT '0',
  `deuts` bigint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_annonce
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_announce`
-- ----------------------------
DROP TABLE IF EXISTS `sn_announce`;
CREATE TABLE `sn_announce` (
  `idAnnounce` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `tsTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date & Time of announce',
  `strAnnounce` text NOT NULL,
  PRIMARY KEY (`idAnnounce`),
  KEY `indTimeStamp` (`tsTimeStamp`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_announce
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_banned`
-- ----------------------------
DROP TABLE IF EXISTS `sn_banned`;
CREATE TABLE `sn_banned` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `who` varchar(11) NOT NULL DEFAULT '',
  `theme` text NOT NULL,
  `who2` varchar(11) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  `longer` int(11) NOT NULL DEFAULT '0',
  `author` varchar(11) NOT NULL DEFAULT '',
  `email` varchar(20) NOT NULL DEFAULT '',
  KEY `ID` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_banned
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_buddy`
-- ----------------------------
DROP TABLE IF EXISTS `sn_buddy`;
CREATE TABLE `sn_buddy` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL DEFAULT '0',
  `owner` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(3) NOT NULL DEFAULT '0',
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_buddy
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_chat`
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat`;
CREATE TABLE `sn_chat` (
  `messageid` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageid`),
  KEY `i_ally_idmess` (`ally_id`,`messageid`)
) ENGINE=InnoDB AUTO_INCREMENT=6821 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_chat
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_config`
-- ----------------------------
DROP TABLE IF EXISTS `sn_config`;
CREATE TABLE `sn_config` (
  `config_name` varchar(64) NOT NULL DEFAULT '',
  `config_value` text NOT NULL,
  PRIMARY KEY (`config_name`),
  KEY `i_config_name` (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_config
-- ----------------------------
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuCode', '<tr><td align=center><script type=\"text/javascript\"><!--\r\ngoogle_ad_client = \"pub-1914310741599503\";\r\n/* oGame */\r\ngoogle_ad_slot = \"2544836773\";\r\ngoogle_ad_width = 125;\r\ngoogle_ad_height = 125;\r\n//-->\r\n</script>\r\n<script type=\"text/javascript\"\r\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\r\n</script></td></tr>\r\n');
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuIsOn', '0');
INSERT INTO `sn_config` VALUES ('BannerOverviewFrame', '1');
INSERT INTO `sn_config` VALUES ('BannerURL', '/scripts/createbanner.php');
INSERT INTO `sn_config` VALUES ('banner_source_post', '../images/bann.png');
INSERT INTO `sn_config` VALUES ('BuildLabWhileRun', '0');
INSERT INTO `sn_config` VALUES ('chat_admin_msgFormat', '[c=purple]$2[/c]');
INSERT INTO `sn_config` VALUES ('close_reason', 'Извините, производится техобслуживание.');
INSERT INTO `sn_config` VALUES ('COOKIE_NAME', 'supernova0');
INSERT INTO `sn_config` VALUES ('crystal_basic_income', '20');
INSERT INTO `sn_config` VALUES ('dbVersion', '1');
INSERT INTO `sn_config` VALUES ('debug', '0');
INSERT INTO `sn_config` VALUES ('Defs_Cdr', '30');
INSERT INTO `sn_config` VALUES ('deuterium_basic_income', '0');
INSERT INTO `sn_config` VALUES ('energy_basic_income', '0');
INSERT INTO `sn_config` VALUES ('Fleet_Cdr', '30');
INSERT INTO `sn_config` VALUES ('fleet_speed', '2500');
INSERT INTO `sn_config` VALUES ('ForumUserbarFrame', '1');
INSERT INTO `sn_config` VALUES ('forum_url', '/forum');
INSERT INTO `sn_config` VALUES ('game_date_withTime', 'd.m.Y h:i:s');
INSERT INTO `sn_config` VALUES ('game_disable', '1');
INSERT INTO `sn_config` VALUES ('game_maxGalaxy', '5');
INSERT INTO `sn_config` VALUES ('game_maxPlanet', '15');
INSERT INTO `sn_config` VALUES ('game_maxSystem', '199');
INSERT INTO `sn_config` VALUES ('game_mode', '0');
INSERT INTO `sn_config` VALUES ('game_name', 'SuperNova.WS');
INSERT INTO `sn_config` VALUES ('game_speed', '2500');
INSERT INTO `sn_config` VALUES ('initial_fields', '163');
INSERT INTO `sn_config` VALUES ('int_banner_background', 'images/banner.png');
INSERT INTO `sn_config` VALUES ('int_banner_fontInfo', 'terminator.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_fontRaids', 'klmnfp2005.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_fontUniverse', 'cristal.ttf');
INSERT INTO `sn_config` VALUES ('int_banner_showInOverview', '1');
INSERT INTO `sn_config` VALUES ('int_banner_URL', '/banner.php?type=banner');
INSERT INTO `sn_config` VALUES ('int_userbar_background', 'images/userbar.png');
INSERT INTO `sn_config` VALUES ('int_userbar_font', 'arialbd.ttf');
INSERT INTO `sn_config` VALUES ('int_userbar_showInOverview', '1');
INSERT INTO `sn_config` VALUES ('int_userbar_URL', '/banner.php?type=userbar');
INSERT INTO `sn_config` VALUES ('LastSettedGalaxyPos', '1');
INSERT INTO `sn_config` VALUES ('LastSettedPlanetPos', '1');
INSERT INTO `sn_config` VALUES ('LastSettedSystemPos', '1');
INSERT INTO `sn_config` VALUES ('metal_basic_income', '40');
INSERT INTO `sn_config` VALUES ('noobprotection', '1');
INSERT INTO `sn_config` VALUES ('noobprotectionmulti', '5');
INSERT INTO `sn_config` VALUES ('noobprotectiontime', '5000');
INSERT INTO `sn_config` VALUES ('OverviewBanner', '0');
INSERT INTO `sn_config` VALUES ('OverviewExternChat', '0');
INSERT INTO `sn_config` VALUES ('OverviewExternChatCmd', '');
INSERT INTO `sn_config` VALUES ('OverviewNewsFrame', '1');
INSERT INTO `sn_config` VALUES ('OverviewNewsText', '');
INSERT INTO `sn_config` VALUES ('resource_multiplier', '1');
INSERT INTO `sn_config` VALUES ('urlaubs_modus_erz', '0');
INSERT INTO `sn_config` VALUES ('UserbarOverviewFrame', '1');
INSERT INTO `sn_config` VALUES ('UserbarURL', '/scripts/userbar.php');
INSERT INTO `sn_config` VALUES ('userbar_source', '../images/userbar.png');
INSERT INTO `sn_config` VALUES ('users_amount', '1');
INSERT INTO `sn_config` VALUES ('stats_lastUpdated', '0');
INSERT INTO `sn_config` VALUES ('stats_schedule', 'd@04:00:00');

-- ----------------------------
-- Table structure for `sn_errors`
-- ----------------------------
DROP TABLE IF EXISTS `sn_errors`;
CREATE TABLE `sn_errors` (
  `error_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `error_sender` varchar(32) NOT NULL DEFAULT '0',
  `error_time` int(11) NOT NULL DEFAULT '0',
  `error_type` varchar(32) NOT NULL DEFAULT 'unknown',
  `error_text` text,
  `error_page` text,
  `error_backtrace` text,
  PRIMARY KEY (`error_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_errors
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_fleets`
-- ----------------------------
DROP TABLE IF EXISTS `sn_fleets`;
CREATE TABLE `sn_fleets` (
  `fleet_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `fleet_owner` int(11) NOT NULL DEFAULT '0',
  `fleet_mission` int(11) NOT NULL DEFAULT '0',
  `fleet_amount` bigint(11) NOT NULL DEFAULT '0',
  `fleet_array` text,
  `fleet_start_time` int(11) NOT NULL DEFAULT '0',
  `fleet_start_galaxy` int(11) NOT NULL DEFAULT '0',
  `fleet_start_system` int(11) NOT NULL DEFAULT '0',
  `fleet_start_planet` int(11) NOT NULL DEFAULT '0',
  `fleet_start_type` int(11) NOT NULL DEFAULT '0',
  `fleet_end_time` int(11) NOT NULL DEFAULT '0',
  `fleet_end_stay` int(11) NOT NULL DEFAULT '0',
  `fleet_end_galaxy` int(11) NOT NULL DEFAULT '0',
  `fleet_end_system` int(11) NOT NULL DEFAULT '0',
  `fleet_end_planet` int(11) NOT NULL DEFAULT '0',
  `fleet_end_type` int(11) NOT NULL DEFAULT '0',
  `fleet_resource_metal` bigint(11) NOT NULL DEFAULT '0',
  `fleet_resource_crystal` bigint(11) NOT NULL DEFAULT '0',
  `fleet_resource_deuterium` bigint(11) NOT NULL DEFAULT '0',
  `fleet_target_owner` int(11) NOT NULL DEFAULT '0',
  `fleet_group` varchar(15) NOT NULL DEFAULT '0',
  `fleet_mess` int(11) NOT NULL DEFAULT '0',
  `start_time` int(11) DEFAULT NULL,
  `processing_start` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fleet_id`),
  KEY `fleet_origin` (`fleet_start_galaxy`,`fleet_start_system`,`fleet_start_planet`),
  KEY `fleet_dest` (`fleet_end_galaxy`,`fleet_end_system`,`fleet_end_planet`),
  KEY `fleet_start_time` (`fleet_start_time`),
  KEY `fllet_end_time` (`fleet_end_time`),
  KEY `fleet_owner` (`fleet_owner`),
  KEY `i_fl_targ_owner` (`fleet_target_owner`),
  KEY `fleet_both` (`fleet_start_galaxy`,`fleet_start_system`,`fleet_start_planet`,`fleet_start_type`,`fleet_end_galaxy`,`fleet_end_system`,`fleet_end_planet`)
) ENGINE=InnoDB AUTO_INCREMENT=11220 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_fleets
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_fleet_log`
-- ----------------------------
DROP TABLE IF EXISTS `sn_fleet_log`;
CREATE TABLE `sn_fleet_log` (
  `id_owner` int(11) DEFAULT NULL,
  `last_update` int(11) DEFAULT NULL,
  `metal` double(132,8) NOT NULL DEFAULT '0.00000000',
  `crystal` double(132,8) NOT NULL DEFAULT '0.00000000',
  `deuterium` double(132,8) NOT NULL DEFAULT '0.00000000',
  `small_ship_cargo` bigint(11) NOT NULL DEFAULT '0',
  `big_ship_cargo` bigint(11) NOT NULL DEFAULT '0',
  `light_hunter` bigint(11) NOT NULL DEFAULT '0',
  `heavy_hunter` bigint(11) NOT NULL DEFAULT '0',
  `crusher` bigint(11) NOT NULL DEFAULT '0',
  `battle_ship` bigint(11) NOT NULL DEFAULT '0',
  `colonizer` bigint(11) NOT NULL DEFAULT '0',
  `recycler` bigint(11) NOT NULL DEFAULT '0',
  `spy_sonde` bigint(11) NOT NULL DEFAULT '0',
  `bomber_ship` bigint(11) NOT NULL DEFAULT '0',
  `solar_satelit` bigint(11) NOT NULL DEFAULT '0',
  `destructor` bigint(11) NOT NULL DEFAULT '0',
  `dearth_star` bigint(11) NOT NULL DEFAULT '0',
  `battleship` bigint(11) NOT NULL DEFAULT '0',
  `supernova` bigint(11) NOT NULL DEFAULT '0',
  `misil_launcher` bigint(11) NOT NULL DEFAULT '0',
  `small_laser` bigint(11) NOT NULL DEFAULT '0',
  `big_laser` bigint(11) NOT NULL DEFAULT '0',
  `gauss_canyon` bigint(11) NOT NULL DEFAULT '0',
  `ionic_canyon` bigint(11) NOT NULL DEFAULT '0',
  `buster_canyon` bigint(11) NOT NULL DEFAULT '0',
  `small_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `big_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `planet_protector` tinyint(1) NOT NULL DEFAULT '0',
  `interceptor_misil` int(11) NOT NULL DEFAULT '0',
  `interplanetary_misil` int(11) NOT NULL DEFAULT '0',
  `phalanx` bigint(11) NOT NULL DEFAULT '0',
  `sprungtor` bigint(11) NOT NULL DEFAULT '0',
  `last_jump_time` int(11) NOT NULL DEFAULT '0',
  `nano` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_fleet_log
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_galaxy`
-- ----------------------------
DROP TABLE IF EXISTS `sn_galaxy`;
CREATE TABLE `sn_galaxy` (
  `galaxy` int(2) NOT NULL DEFAULT '0',
  `system` int(3) NOT NULL DEFAULT '0',
  `planet` int(2) NOT NULL DEFAULT '0',
  `id_planet` int(11) NOT NULL DEFAULT '0',
  `metal` bigint(11) NOT NULL DEFAULT '0',
  `crystal` bigint(11) NOT NULL DEFAULT '0',
  `id_luna` int(11) NOT NULL DEFAULT '0',
  `luna` int(2) NOT NULL DEFAULT '0',
  KEY `galaxy` (`galaxy`),
  KEY `system` (`system`),
  KEY `planet` (`planet`),
  KEY `GSP` (`galaxy`,`system`,`planet`),
  KEY `i_id_planet` (`id_planet`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_galaxy
-- ----------------------------
INSERT INTO `sn_galaxy` VALUES ('1', '1', '1', '1', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `sn_iraks`
-- ----------------------------
DROP TABLE IF EXISTS `sn_iraks`;
CREATE TABLE `sn_iraks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zeit` int(32) DEFAULT NULL,
  `galaxy` int(2) DEFAULT NULL,
  `system` int(4) DEFAULT NULL,
  `planet` int(2) DEFAULT NULL,
  `galaxy_angreifer` int(2) DEFAULT NULL,
  `system_angreifer` int(4) DEFAULT NULL,
  `planet_angreifer` int(2) DEFAULT NULL,
  `owner` int(32) DEFAULT NULL,
  `zielid` int(32) DEFAULT NULL,
  `anzahl` int(32) DEFAULT NULL,
  `primaer` int(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_iraks
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_logs`
-- ----------------------------
DROP TABLE IF EXISTS `sn_logs`;
CREATE TABLE `sn_logs` (
  `log_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `log_time` int(11) NOT NULL DEFAULT '0',
  `log_type` int(3) NOT NULL,
  `log_sender` varchar(32) NOT NULL DEFAULT '0',
  `log_title` varchar(32) NOT NULL DEFAULT 'unknown',
  `log_text` text,
  `log_page` text,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_lunas`
-- ----------------------------
DROP TABLE IF EXISTS `sn_lunas`;
CREATE TABLE `sn_lunas` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `id_luna` int(11) NOT NULL DEFAULT '0',
  `name` varchar(11) NOT NULL DEFAULT 'Lune',
  `image` varchar(11) NOT NULL DEFAULT 'mond',
  `destruyed` int(11) NOT NULL DEFAULT '0',
  `id_owner` int(11) DEFAULT NULL,
  `galaxy` int(11) DEFAULT NULL,
  `system` int(11) DEFAULT NULL,
  `lunapos` int(11) DEFAULT NULL,
  `temp_min` int(11) NOT NULL DEFAULT '0',
  `temp_max` int(11) NOT NULL DEFAULT '0',
  `diameter` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IGSP` (`id_owner`,`galaxy`,`system`,`lunapos`)
) ENGINE=InnoDB AUTO_INCREMENT=350 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_lunas
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_messages`
-- ----------------------------
DROP TABLE IF EXISTS `sn_messages`;
CREATE TABLE `sn_messages` (
  `message_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `message_owner` int(11) NOT NULL DEFAULT '0',
  `message_sender` int(11) NOT NULL DEFAULT '0',
  `message_time` int(11) NOT NULL DEFAULT '0',
  `message_type` int(11) NOT NULL DEFAULT '0',
  `message_from` varchar(48) DEFAULT NULL,
  `message_subject` varchar(48) DEFAULT NULL,
  `message_text` text,
  PRIMARY KEY (`message_id`),
  KEY `owner` (`message_owner`),
  KEY `owner_type` (`message_owner`,`message_type`),
  KEY `sender_type` (`message_sender`,`message_type`)
) ENGINE=InnoDB AUTO_INCREMENT=23906 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_messages
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_notes`
-- ----------------------------
DROP TABLE IF EXISTS `sn_notes`;
CREATE TABLE `sn_notes` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `priority` tinyint(1) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_notes
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_planets`
-- ----------------------------
DROP TABLE IF EXISTS `sn_planets`;
CREATE TABLE `sn_planets` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `id_owner` int(11) DEFAULT NULL,
  `id_level` int(11) NOT NULL DEFAULT '0',
  `galaxy` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  `planet` int(11) NOT NULL DEFAULT '0',
  `last_update` int(11) DEFAULT NULL,
  `planet_type` int(11) NOT NULL DEFAULT '1',
  `destruyed` int(11) NOT NULL DEFAULT '0',
  `b_building` int(11) NOT NULL DEFAULT '0',
  `b_building_id` text NOT NULL,
  `b_tech` int(11) NOT NULL DEFAULT '0',
  `b_tech_id` int(11) NOT NULL DEFAULT '0',
  `b_hangar` int(11) NOT NULL DEFAULT '0',
  `b_hangar_id` text NOT NULL,
  `b_hangar_plus` int(11) NOT NULL DEFAULT '0',
  `image` varchar(32) NOT NULL DEFAULT 'normaltempplanet01',
  `diameter` int(11) NOT NULL DEFAULT '12800',
  `points` bigint(20) DEFAULT '0',
  `ranks` bigint(20) DEFAULT '0',
  `field_current` int(11) NOT NULL DEFAULT '0',
  `field_max` int(11) NOT NULL DEFAULT '163',
  `temp_min` int(3) NOT NULL DEFAULT '-17',
  `temp_max` int(3) NOT NULL DEFAULT '23',
  `metal` double(132,8) NOT NULL DEFAULT '0.00000000',
  `metal_perhour` int(11) NOT NULL DEFAULT '0',
  `metal_max` bigint(20) DEFAULT '100000',
  `crystal` double(132,8) NOT NULL DEFAULT '0.00000000',
  `crystal_perhour` int(11) NOT NULL DEFAULT '0',
  `crystal_max` bigint(20) DEFAULT '100000',
  `deuterium` double(132,8) NOT NULL DEFAULT '0.00000000',
  `deuterium_perhour` int(11) NOT NULL DEFAULT '0',
  `deuterium_max` bigint(20) DEFAULT '100000',
  `energy_used` int(11) NOT NULL DEFAULT '0',
  `energy_max` int(11) NOT NULL DEFAULT '0',
  `metal_mine` int(11) NOT NULL DEFAULT '0',
  `crystal_mine` int(11) NOT NULL DEFAULT '0',
  `deuterium_sintetizer` int(11) NOT NULL DEFAULT '0',
  `solar_plant` int(11) NOT NULL DEFAULT '0',
  `fusion_plant` int(11) NOT NULL DEFAULT '0',
  `robot_factory` int(11) NOT NULL DEFAULT '0',
  `nano_factory` int(11) NOT NULL DEFAULT '0',
  `hangar` int(11) NOT NULL DEFAULT '0',
  `metal_store` int(11) NOT NULL DEFAULT '0',
  `crystal_store` int(11) NOT NULL DEFAULT '0',
  `deuterium_store` int(11) NOT NULL DEFAULT '0',
  `laboratory` int(11) NOT NULL DEFAULT '0',
  `terraformer` int(11) NOT NULL DEFAULT '0',
  `ally_deposit` int(11) NOT NULL DEFAULT '0',
  `silo` int(11) NOT NULL DEFAULT '0',
  `small_ship_cargo` bigint(11) NOT NULL DEFAULT '0',
  `big_ship_cargo` bigint(11) NOT NULL DEFAULT '0',
  `light_hunter` bigint(11) NOT NULL DEFAULT '0',
  `heavy_hunter` bigint(11) NOT NULL DEFAULT '0',
  `crusher` bigint(11) NOT NULL DEFAULT '0',
  `battle_ship` bigint(11) NOT NULL DEFAULT '0',
  `colonizer` bigint(11) NOT NULL DEFAULT '0',
  `recycler` bigint(11) NOT NULL DEFAULT '0',
  `spy_sonde` bigint(11) NOT NULL DEFAULT '0',
  `bomber_ship` bigint(11) NOT NULL DEFAULT '0',
  `solar_satelit` bigint(11) NOT NULL DEFAULT '0',
  `destructor` bigint(11) NOT NULL DEFAULT '0',
  `dearth_star` bigint(11) NOT NULL DEFAULT '0',
  `battleship` bigint(11) NOT NULL DEFAULT '0',
  `supernova` bigint(11) NOT NULL DEFAULT '0',
  `misil_launcher` bigint(11) NOT NULL DEFAULT '0',
  `small_laser` bigint(11) NOT NULL DEFAULT '0',
  `big_laser` bigint(11) NOT NULL DEFAULT '0',
  `gauss_canyon` bigint(11) NOT NULL DEFAULT '0',
  `ionic_canyon` bigint(11) NOT NULL DEFAULT '0',
  `buster_canyon` bigint(11) NOT NULL DEFAULT '0',
  `small_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `big_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `planet_protector` tinyint(1) NOT NULL DEFAULT '0',
  `interceptor_misil` int(11) NOT NULL DEFAULT '0',
  `interplanetary_misil` int(11) NOT NULL DEFAULT '0',
  `metal_mine_porcent` int(11) NOT NULL DEFAULT '10',
  `crystal_mine_porcent` int(11) NOT NULL DEFAULT '10',
  `deuterium_sintetizer_porcent` int(11) NOT NULL DEFAULT '10',
  `solar_plant_porcent` int(11) NOT NULL DEFAULT '10',
  `fusion_plant_porcent` int(11) NOT NULL DEFAULT '10',
  `solar_satelit_porcent` int(11) NOT NULL DEFAULT '10',
  `mondbasis` bigint(11) NOT NULL DEFAULT '0',
  `phalanx` bigint(11) NOT NULL DEFAULT '0',
  `sprungtor` bigint(11) NOT NULL DEFAULT '0',
  `last_jump_time` int(11) NOT NULL DEFAULT '0',
  `nano` int(11) DEFAULT '0',
  `parent_planet` bigint(11) unsigned DEFAULT '0',
  `debris_metal` bigint(11) unsigned DEFAULT '0',
  `debris_crystal` bigint(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `owner_type` (`id_owner`,`planet_type`),
  KEY `i_metal` (`metal`),
  KEY `id_level` (`id_level`),
  KEY `i_metal_mine` (`metal_mine`,`id_level`),
  KEY `i_crystal_mine` (`crystal_mine`,`id_level`),
  KEY `i_deuterium_sintetizer` (`deuterium_sintetizer`,`id_level`),
  KEY `i_solar_plant` (`solar_plant`,`id_level`),
  KEY `i_fusion_plant` (`fusion_plant`,`id_level`),
  KEY `i_robot_factory` (`robot_factory`,`id_level`),
  KEY `i_nano_factory` (`nano_factory`,`id_level`),
  KEY `i_hangar` (`hangar`,`id_level`),
  KEY `i_metal_store` (`metal_store`,`id_level`),
  KEY `i_crystal_store` (`crystal_store`,`id_level`),
  KEY `i_deuterium_store` (`deuterium_store`,`id_level`),
  KEY `i_laboratory` (`laboratory`,`id_level`),
  KEY `i_silo` (`silo`,`id_level`),
  KEY `i_small_ship_cargo` (`small_ship_cargo`,`id_level`),
  KEY `i_big_ship_cargo` (`big_ship_cargo`,`id_level`),
  KEY `i_light_hunter` (`light_hunter`,`id_level`),
  KEY `i_heavy_hunter` (`heavy_hunter`,`id_level`),
  KEY `i_crusher` (`crusher`,`id_level`),
  KEY `i_battle_ship` (`battle_ship`,`id_level`),
  KEY `i_colonizer` (`colonizer`,`id_level`),
  KEY `i_recycler` (`recycler`,`id_level`),
  KEY `i_spy_sonde` (`spy_sonde`,`id_level`),
  KEY `i_bomber_ship` (`bomber_ship`,`id_level`),
  KEY `i_solar_satelit` (`solar_satelit`,`id_level`),
  KEY `i_destructor` (`destructor`,`id_level`),
  KEY `i_dearth_star` (`dearth_star`,`id_level`),
  KEY `i_battleship` (`battleship`,`id_level`),
  KEY `i_misil_launcher` (`misil_launcher`,`id_level`),
  KEY `i_small_laser` (`small_laser`,`id_level`),
  KEY `i_big_laser` (`big_laser`,`id_level`),
  KEY `i_gauss_canyon` (`gauss_canyon`,`id_level`),
  KEY `i_ionic_canyon` (`ionic_canyon`,`id_level`),
  KEY `i_buster_canyon` (`buster_canyon`,`id_level`),
  KEY `i_small_protection_shield` (`small_protection_shield`,`id_level`),
  KEY `i_big_protection_shield` (`big_protection_shield`,`id_level`),
  KEY `i_interceptor_misil` (`interceptor_misil`,`id_level`),
  KEY `i_interplanetary_misil` (`interplanetary_misil`,`id_level`),
  KEY `i_nano` (`nano`,`id_level`),
  KEY `i_last_update` (`last_update`),
  KEY `GSPT` (`galaxy`,`system`,`planet`,`planet_type`)
) ENGINE=InnoDB AUTO_INCREMENT=22388 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_planets
-- ----------------------------
INSERT INTO `sn_planets` VALUES ('1', 'Планета', '1', '0', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '', '0', 'wasserplanet05', '12750', '0', '0', '0', '163', '-58', '-18', '0', '40', '0', '0', '20', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '10', '10', '10', '10', '0', '10', '0', '0', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `sn_referrals`
-- ----------------------------
DROP TABLE IF EXISTS `sn_referrals`;
CREATE TABLE `sn_referrals` (
  `id` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Referral ID (from table USERS)',
  `id_partner` bigint(11) unsigned NOT NULL COMMENT 'Partner with whom refferal affilates (from table USERS)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_referrals
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_rw`
-- ----------------------------
DROP TABLE IF EXISTS `sn_rw`;
CREATE TABLE `sn_rw` (
  `id_owner1` int(11) NOT NULL DEFAULT '0',
  `id_owner2` int(11) NOT NULL DEFAULT '0',
  `rid` varchar(72) NOT NULL DEFAULT '',
  `raport` text NOT NULL,
  `a_zestrzelona` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `owners` varchar(255) NOT NULL DEFAULT '0',
  UNIQUE KEY `rid` (`rid`),
  KEY `id_owner1` (`id_owner1`,`rid`),
  KEY `id_owner2` (`id_owner2`,`rid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_rw
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_statpoints`
-- ----------------------------
DROP TABLE IF EXISTS `sn_statpoints`;
CREATE TABLE `sn_statpoints` (
  `id_owner` int(11) NOT NULL DEFAULT '0',
  `id_ally` int(11) NOT NULL DEFAULT '0',
  `stat_type` int(2) NOT NULL DEFAULT '0',
  `stat_code` int(11) NOT NULL DEFAULT '0',
  `tech_rank` int(11) NOT NULL DEFAULT '0',
  `tech_old_rank` int(11) NOT NULL DEFAULT '0',
  `tech_points` bigint(20) NOT NULL DEFAULT '0',
  `tech_count` int(11) NOT NULL DEFAULT '0',
  `build_rank` int(11) NOT NULL DEFAULT '0',
  `build_old_rank` int(11) NOT NULL DEFAULT '0',
  `build_points` bigint(20) NOT NULL DEFAULT '0',
  `build_count` int(11) NOT NULL DEFAULT '0',
  `defs_rank` int(11) NOT NULL DEFAULT '0',
  `defs_old_rank` int(11) NOT NULL DEFAULT '0',
  `defs_points` bigint(20) NOT NULL DEFAULT '0',
  `defs_count` int(11) NOT NULL DEFAULT '0',
  `fleet_rank` int(11) NOT NULL DEFAULT '0',
  `fleet_old_rank` int(11) NOT NULL DEFAULT '0',
  `fleet_points` bigint(20) NOT NULL DEFAULT '0',
  `fleet_count` int(11) NOT NULL DEFAULT '0',
  `total_rank` int(11) NOT NULL DEFAULT '0',
  `total_old_rank` int(11) NOT NULL DEFAULT '0',
  `total_points` bigint(20) NOT NULL DEFAULT '0',
  `total_count` int(11) NOT NULL DEFAULT '0',
  `stat_date` int(11) NOT NULL DEFAULT '0',
  KEY `TECH` (`tech_points`),
  KEY `BUILDS` (`build_points`),
  KEY `DEFS` (`defs_points`),
  KEY `FLEET` (`fleet_points`),
  KEY `TOTAL` (`total_points`),
  KEY `i_stats_owner` (`id_owner`,`stat_type`,`stat_code`,`tech_rank`,`build_rank`,`defs_rank`,`fleet_rank`,`total_rank`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_statpoints
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_update`
-- ----------------------------
DROP TABLE IF EXISTS `sn_update`;
CREATE TABLE `sn_update` (
  `lastupdate` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of sn_update
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_users`
-- ----------------------------
DROP TABLE IF EXISTS `sn_users`;
CREATE TABLE `sn_users` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `email_2` varchar(64) NOT NULL DEFAULT '',
  `lang` varchar(8) NOT NULL DEFAULT 'ru',
  `authlevel` tinyint(4) NOT NULL DEFAULT '0',
  `sex` char(1) DEFAULT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `sign` text,
  `id_planet` int(11) NOT NULL DEFAULT '0',
  `galaxy` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  `planet` int(11) NOT NULL DEFAULT '0',
  `current_planet` int(11) NOT NULL DEFAULT '0',
  `user_lastip` varchar(16) NOT NULL DEFAULT '',
  `user_agent` text NOT NULL,
  `register_time` int(11) NOT NULL DEFAULT '0',
  `onlinetime` int(11) NOT NULL DEFAULT '0',
  `dpath` varchar(255) NOT NULL DEFAULT '',
  `design` tinyint(4) NOT NULL DEFAULT '1',
  `noipcheck` tinyint(4) NOT NULL DEFAULT '1',
  `planet_sort` tinyint(1) NOT NULL DEFAULT '0',
  `planet_sort_order` tinyint(1) NOT NULL DEFAULT '0',
  `spio_anz` tinyint(4) NOT NULL DEFAULT '1',
  `settings_tooltiptime` tinyint(4) NOT NULL DEFAULT '5',
  `settings_fleetactions` tinyint(4) NOT NULL DEFAULT '0',
  `settings_allylogo` tinyint(4) NOT NULL DEFAULT '0',
  `settings_esp` tinyint(4) NOT NULL DEFAULT '1',
  `settings_wri` tinyint(4) NOT NULL DEFAULT '1',
  `settings_bud` tinyint(4) NOT NULL DEFAULT '1',
  `settings_mis` tinyint(4) NOT NULL DEFAULT '1',
  `settings_rep` tinyint(4) NOT NULL DEFAULT '0',
  `urlaubs_modus` tinyint(4) NOT NULL DEFAULT '0',
  `urlaubs_until` int(11) NOT NULL DEFAULT '0',
  `db_deaktjava` tinyint(4) NOT NULL DEFAULT '0',
  `new_message` int(11) NOT NULL DEFAULT '0',
  `fleet_shortcut` text,
  `b_tech_planet` int(11) NOT NULL DEFAULT '0',
  `spy_tech` int(11) NOT NULL DEFAULT '0',
  `computer_tech` int(11) NOT NULL DEFAULT '0',
  `military_tech` int(11) NOT NULL DEFAULT '0',
  `defence_tech` int(11) NOT NULL DEFAULT '0',
  `shield_tech` int(11) NOT NULL DEFAULT '0',
  `energy_tech` int(11) NOT NULL DEFAULT '0',
  `hyperspace_tech` int(11) NOT NULL DEFAULT '0',
  `combustion_tech` int(11) NOT NULL DEFAULT '0',
  `impulse_motor_tech` int(11) NOT NULL DEFAULT '0',
  `hyperspace_motor_tech` int(11) NOT NULL DEFAULT '0',
  `laser_tech` int(11) NOT NULL DEFAULT '0',
  `ionic_tech` int(11) NOT NULL DEFAULT '0',
  `buster_tech` int(11) NOT NULL DEFAULT '0',
  `intergalactic_tech` int(11) NOT NULL DEFAULT '0',
  `expedition_tech` int(11) NOT NULL DEFAULT '0',
  `colonisation_tech` int(11) NOT NULL DEFAULT '0',
  `graviton_tech` int(11) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  `ally_name` varchar(32) DEFAULT '',
  `ally_request` int(11) NOT NULL DEFAULT '0',
  `ally_request_text` text,
  `ally_register_time` int(11) NOT NULL DEFAULT '0',
  `ally_rank_id` int(11) NOT NULL DEFAULT '0',
  `current_luna` int(11) NOT NULL DEFAULT '0',
  `kolorminus` varchar(11) NOT NULL DEFAULT 'red',
  `kolorplus` varchar(11) NOT NULL DEFAULT '#00FF00',
  `kolorpoziom` varchar(11) NOT NULL DEFAULT 'yellow',
  `rpg_geologue` int(11) NOT NULL DEFAULT '0',
  `rpg_amiral` int(11) NOT NULL DEFAULT '0',
  `rpg_ingenieur` int(11) NOT NULL DEFAULT '0',
  `rpg_technocrate` int(11) NOT NULL DEFAULT '0',
  `rpg_espion` int(11) NOT NULL DEFAULT '0',
  `rpg_constructeur` int(11) NOT NULL DEFAULT '0',
  `rpg_scientifique` int(11) NOT NULL DEFAULT '0',
  `rpg_commandant` int(11) NOT NULL DEFAULT '0',
  `rpg_points` int(11) NOT NULL DEFAULT '0',
  `rpg_stockeur` int(11) NOT NULL DEFAULT '0',
  `rpg_defenseur` int(11) NOT NULL DEFAULT '0',
  `rpg_destructeur` int(11) NOT NULL DEFAULT '0',
  `rpg_general` int(11) NOT NULL DEFAULT '0',
  `rpg_bunker` int(11) NOT NULL DEFAULT '0',
  `rpg_raideur` int(11) NOT NULL DEFAULT '0',
  `rpg_empereur` int(11) NOT NULL DEFAULT '0',
  `lvl_minier` int(11) NOT NULL DEFAULT '1',
  `lvl_raid` int(11) NOT NULL DEFAULT '1',
  `xpraid` int(11) NOT NULL DEFAULT '0',
  `xpminier` int(11) NOT NULL DEFAULT '0',
  `raids` bigint(20) NOT NULL DEFAULT '0',
  `raidsloose` bigint(20) NOT NULL DEFAULT '0',
  `raidswin` bigint(20) NOT NULL DEFAULT '0',
  `p_infligees` bigint(20) NOT NULL DEFAULT '0',
  `mnl_alliance` int(11) NOT NULL DEFAULT '0',
  `mnl_joueur` int(11) NOT NULL DEFAULT '0',
  `mnl_attaque` int(11) NOT NULL DEFAULT '0',
  `mnl_spy` int(11) NOT NULL DEFAULT '0',
  `mnl_exploit` int(11) NOT NULL DEFAULT '0',
  `mnl_transport` int(11) NOT NULL DEFAULT '0',
  `mnl_expedition` int(11) NOT NULL DEFAULT '0',
  `mnl_buildlist` int(11) NOT NULL DEFAULT '0',
  `bana` int(11) DEFAULT NULL,
  `urlaubs_modus_time` int(11) NOT NULL DEFAULT '0',
  `deltime` int(11) NOT NULL DEFAULT '0',
  `aktywnosc` varchar(255) NOT NULL DEFAULT '',
  `kod_aktywujacy` varchar(255) NOT NULL DEFAULT '',
  `kiler` varchar(255) NOT NULL DEFAULT '',
  `time_aktyw` int(11) NOT NULL DEFAULT '0',
  `deleteme` int(11) NOT NULL DEFAULT '0',
  `ataker` int(11) NOT NULL DEFAULT '0',
  `atakin` int(11) NOT NULL DEFAULT '0',
  `banaday` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `i_username` (`username`),
  KEY `i_ally_online` (`ally_id`,`onlinetime`),
  KEY `onlinetime` (`onlinetime`),
  KEY `i_register_time` (`register_time`)
) ENGINE=InnoDB AUTO_INCREMENT=6058 DEFAULT CHARSET=cp1251;

-- ----------------------------
-- Records of sn_users
-- ----------------------------
INSERT INTO `sn_users` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'root@localhost', 'root@localhost', 'ru', '3', 'M', '', null, '1', '1', '1', '1', '1', '', '', '0', '0', 'skins/EpicBlue/', '1', '0', '0', '0', '1', '5', '0', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', null, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '', '0', '0', '0', '', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, '0', '0', '1', '22ff9c58a14126d28556df7c5a427b49', 'd41d8cd98f00b204e9800998ecf8427e', '1247405734', '0', '0', '0', null);
