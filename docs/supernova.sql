/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50158
Source Host           : localhost:3306
Source Database       : supernova

Target Server Type    : MYSQL
Target Server Version : 50158
File Encoding         : 65001

Date: 2011-12-27 15:39:08
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
  `planet_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `eingeladen` varchar(50) DEFAULT NULL,
  `fleet_end_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_aks
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance`;
CREATE TABLE `sn_alliance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ally_name` varchar(32) DEFAULT '',
  `ally_tag` varchar(8) DEFAULT '',
  `ally_owner` bigint(20) unsigned DEFAULT NULL,
  `ally_register_time` int(11) NOT NULL DEFAULT '0',
  `ally_description` text,
  `ally_web` varchar(255) DEFAULT '',
  `ally_text` text,
  `ally_image` varchar(255) DEFAULT '',
  `ally_request` text,
  `ally_request_waiting` text,
  `ally_request_notallow` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ally_owner_range` varchar(32) DEFAULT '',
  `ally_ranks` text,
  `ally_members` int(11) NOT NULL DEFAULT '0',
  `ranklist` text,
  `total_rank` int(10) unsigned NOT NULL DEFAULT '0',
  `total_points` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_ally_name` (`ally_name`),
  UNIQUE KEY `i_ally_tag` (`ally_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_alliance
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance_diplomacy`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_diplomacy`;
CREATE TABLE `sn_alliance_diplomacy` (
  `alliance_diplomacy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_diplomacy_ally_id` bigint(20) unsigned DEFAULT NULL,
  `alliance_diplomacy_contr_ally_id` bigint(20) unsigned DEFAULT NULL,
  `alliance_diplomacy_contr_ally_name` varchar(32) DEFAULT '',
  `alliance_diplomacy_relation` set('neutral','war','peace','confederation','federation','union','master','slave') NOT NULL DEFAULT 'neutral',
  `alliance_diplomacy_relation_last` set('neutral','war','peace','confederation','federation','union','master','slave') NOT NULL DEFAULT 'neutral',
  `alliance_diplomacy_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_diplomacy_id`),
  UNIQUE KEY `alliance_diplomacy_id` (`alliance_diplomacy_id`),
  KEY `alliance_diplomacy_ally_id` (`alliance_diplomacy_ally_id`,`alliance_diplomacy_contr_ally_id`,`alliance_diplomacy_time`),
  KEY `alliance_diplomacy_ally_id_2` (`alliance_diplomacy_ally_id`,`alliance_diplomacy_time`),
  KEY `FK_diplomacy_contr_ally_id` (`alliance_diplomacy_contr_ally_id`),
  KEY `FK_diplomacy_contr_ally_name` (`alliance_diplomacy_contr_ally_name`),
  CONSTRAINT `FK_diplomacy_ally_id` FOREIGN KEY (`alliance_diplomacy_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_diplomacy_contr_ally_id` FOREIGN KEY (`alliance_diplomacy_contr_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_diplomacy_contr_ally_name` FOREIGN KEY (`alliance_diplomacy_contr_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_alliance_diplomacy
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance_negotiation`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_negotiation`;
CREATE TABLE `sn_alliance_negotiation` (
  `alliance_negotiation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_negotiation_ally_id` bigint(20) unsigned DEFAULT NULL,
  `alliance_negotiation_ally_name` varchar(32) DEFAULT '',
  `alliance_negotiation_contr_ally_id` bigint(20) unsigned DEFAULT NULL,
  `alliance_negotiation_contr_ally_name` varchar(32) DEFAULT '',
  `alliance_negotiation_relation` set('neutral','war','peace','confederation','federation','union','master','slave') NOT NULL DEFAULT 'neutral',
  `alliance_negotiation_time` int(11) NOT NULL DEFAULT '0',
  `alliance_negotiation_propose` text,
  `alliance_negotiation_response` text,
  `alliance_negotiation_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_negotiation_id`),
  UNIQUE KEY `alliance_negotiation_id` (`alliance_negotiation_id`),
  KEY `alliance_negotiation_ally_id` (`alliance_negotiation_ally_id`,`alliance_negotiation_contr_ally_id`,`alliance_negotiation_time`),
  KEY `alliance_negotiation_ally_id_2` (`alliance_negotiation_ally_id`,`alliance_negotiation_time`),
  KEY `FK_negotiation_ally_name` (`alliance_negotiation_ally_name`),
  KEY `FK_negotiation_contr_ally_id` (`alliance_negotiation_contr_ally_id`),
  KEY `FK_negotiation_contr_ally_name` (`alliance_negotiation_contr_ally_name`),
  CONSTRAINT `FK_negotiation_ally_id` FOREIGN KEY (`alliance_negotiation_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_negotiation_contr_ally_id` FOREIGN KEY (`alliance_negotiation_contr_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_negotiation_ally_name` FOREIGN KEY (`alliance_negotiation_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_negotiation_contr_ally_name` FOREIGN KEY (`alliance_negotiation_contr_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_alliance_negotiation
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_alliance_requests`
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_requests`;
CREATE TABLE `sn_alliance_requests` (
  `id_user` bigint(20) unsigned NOT NULL DEFAULT '0',
  `id_ally` bigint(20) unsigned NOT NULL DEFAULT '0',
  `request_text` text,
  `request_time` int(11) NOT NULL DEFAULT '0',
  `request_denied` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_user`,`id_ally`),
  KEY `I_alliance_requests_id_ally` (`id_ally`,`id_user`),
  CONSTRAINT `FK_alliance_request_user_id` FOREIGN KEY (`id_user`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_alliance_request_ally_id` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_alliance_requests
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_annonce`
-- ----------------------------
DROP TABLE IF EXISTS `sn_annonce`;
CREATE TABLE `sn_annonce` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(64) DEFAULT NULL,
  `galaxie` int(11) NOT NULL DEFAULT '0',
  `systeme` int(11) NOT NULL DEFAULT '0',
  `metala` bigint(11) NOT NULL DEFAULT '0',
  `cristala` bigint(11) NOT NULL DEFAULT '0',
  `deuta` bigint(11) NOT NULL DEFAULT '0',
  `metals` bigint(11) NOT NULL DEFAULT '0',
  `cristals` bigint(11) NOT NULL DEFAULT '0',
  `deuts` bigint(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `I_annonce_user` (`user`,`id`),
  CONSTRAINT `FK_annonce_user` FOREIGN KEY (`user`) REFERENCES `sn_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `strAnnounce` text,
  `detail_url` varchar(250) NOT NULL DEFAULT '' COMMENT 'Link to more details about update',
  PRIMARY KEY (`idAnnounce`),
  KEY `indTimeStamp` (`tsTimeStamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_announce
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_banned`
-- ----------------------------
DROP TABLE IF EXISTS `sn_banned`;
CREATE TABLE `sn_banned` (
  `ban_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ban_user_name` varchar(64) NOT NULL DEFAULT '',
  `ban_reason` varchar(128) NOT NULL DEFAULT '',
  `ban_time` int(11) NOT NULL DEFAULT '0',
  `ban_until` int(11) NOT NULL DEFAULT '0',
  `ban_issuer_name` varchar(64) NOT NULL DEFAULT '',
  `ban_issuer_email` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ban_id`),
  KEY `ID` (`ban_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_banned
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_bashing`
-- ----------------------------
DROP TABLE IF EXISTS `sn_bashing`;
CREATE TABLE `sn_bashing` (
  `bashing_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bashing_user_id` bigint(20) unsigned DEFAULT NULL,
  `bashing_planet_id` bigint(20) unsigned DEFAULT NULL,
  `bashing_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bashing_id`),
  UNIQUE KEY `bashing_id` (`bashing_id`),
  KEY `bashing_user_id` (`bashing_user_id`,`bashing_planet_id`,`bashing_time`),
  KEY `bashing_planet_id` (`bashing_planet_id`),
  KEY `bashing_time` (`bashing_time`),
  CONSTRAINT `FK_bashing_user_id` FOREIGN KEY (`bashing_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_bashing
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_buddy`
-- ----------------------------
DROP TABLE IF EXISTS `sn_buddy`;
CREATE TABLE `sn_buddy` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender` bigint(20) unsigned DEFAULT NULL,
  `owner` bigint(20) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `I_buddy_sender` (`sender`),
  KEY `I_buddy_owner` (`owner`),
  CONSTRAINT `FK_buddy_sender_id` FOREIGN KEY (`sender`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_buddy_owner_id` FOREIGN KEY (`owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_buddy
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_chat`
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat`;
CREATE TABLE `sn_chat` (
  `messageid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL DEFAULT '',
  `message` text,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageid`),
  UNIQUE KEY `messageid` (`messageid`),
  KEY `i_ally_idmess` (`ally_id`,`messageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_chat
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_config`
-- ----------------------------
DROP TABLE IF EXISTS `sn_config`;
CREATE TABLE `sn_config` (
  `config_name` varchar(64) NOT NULL DEFAULT '',
  `config_value` text,
  PRIMARY KEY (`config_name`),
  KEY `i_config_name` (`config_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `sn_confirmations`
-- ----------------------------
DROP TABLE IF EXISTS `sn_confirmations`;
CREATE TABLE `sn_confirmations` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `id_user` bigint(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `code` varchar(16) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `i_code_email` (`code`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_confirmations
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_counter`
-- ----------------------------
DROP TABLE IF EXISTS `sn_counter`;
CREATE TABLE `sn_counter` (
  `counter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL DEFAULT '0',
  `page` varchar(255) DEFAULT '0',
  `url` varchar(255) DEFAULT '0',
  `user_id` bigint(20) unsigned DEFAULT '0',
  `user_name` varchar(64) DEFAULT '',
  `ip` varchar(250) DEFAULT NULL COMMENT 'User last IP',
  `proxy` varchar(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)',
  PRIMARY KEY (`counter_id`),
  UNIQUE KEY `counter_id` (`counter_id`),
  KEY `i_user_id` (`user_id`),
  KEY `i_ip` (`ip`),
  KEY `I_counter_user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_counter
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_fleets`
-- ----------------------------
DROP TABLE IF EXISTS `sn_fleets`;
CREATE TABLE `sn_fleets` (
  `fleet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `fleet_resource_metal` decimal(65,0) DEFAULT '0',
  `fleet_resource_crystal` decimal(65,0) DEFAULT '0',
  `fleet_resource_deuterium` decimal(65,0) DEFAULT '0',
  `fleet_target_owner` int(11) NOT NULL DEFAULT '0',
  `fleet_group` varchar(15) NOT NULL DEFAULT '0',
  `fleet_mess` int(11) NOT NULL DEFAULT '0',
  `start_time` int(11) DEFAULT '0',
  `processing_start` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fleet_id`),
  UNIQUE KEY `fleet_id` (`fleet_id`),
  KEY `fleet_origin` (`fleet_start_galaxy`,`fleet_start_system`,`fleet_start_planet`),
  KEY `fleet_dest` (`fleet_end_galaxy`,`fleet_end_system`,`fleet_end_planet`),
  KEY `fleet_start_time` (`fleet_start_time`),
  KEY `fllet_end_time` (`fleet_end_time`),
  KEY `fleet_owner` (`fleet_owner`),
  KEY `i_fl_targ_owner` (`fleet_target_owner`),
  KEY `fleet_both` (`fleet_start_galaxy`,`fleet_start_system`,`fleet_start_planet`,`fleet_start_type`,`fleet_end_galaxy`,`fleet_end_system`,`fleet_end_planet`),
  KEY `fleet_mess` (`fleet_mess`),
  KEY `fleet_group` (`fleet_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_fleets
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_iraks`
-- ----------------------------
DROP TABLE IF EXISTS `sn_iraks`;
CREATE TABLE `sn_iraks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fleet_end_time` int(11) unsigned NOT NULL DEFAULT '0',
  `fleet_end_galaxy` int(2) unsigned DEFAULT '0',
  `fleet_end_system` int(4) unsigned DEFAULT '0',
  `fleet_end_planet` int(2) unsigned DEFAULT '0',
  `fleet_start_galaxy` int(2) unsigned DEFAULT '0',
  `fleet_start_system` int(4) unsigned DEFAULT '0',
  `fleet_start_planet` int(2) unsigned DEFAULT '0',
  `fleet_owner` bigint(20) unsigned DEFAULT NULL,
  `fleet_target_owner` bigint(20) unsigned DEFAULT NULL,
  `fleet_amount` bigint(20) unsigned DEFAULT '0',
  `primaer` int(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `I_iraks_fleet_owner` (`fleet_owner`),
  KEY `I_iraks_fleet_target_owner` (`fleet_target_owner`),
  CONSTRAINT `FK_iraks_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_iraks_fleet_target_owner` FOREIGN KEY (`fleet_target_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_iraks
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_logs`
-- ----------------------------
DROP TABLE IF EXISTS `sn_logs`;
CREATE TABLE `sn_logs` (
  `log_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp',
  `log_username` varchar(64) NOT NULL DEFAULT '' COMMENT 'Username',
  `log_title` varchar(64) NOT NULL DEFAULT 'Log entry' COMMENT 'Short description',
  `log_text` text,
  `log_page` varchar(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
  `log_code` int(10) unsigned NOT NULL DEFAULT '0',
  `log_sender` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID which make log record',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Machine-readable timestamp',
  `log_dump` text COMMENT 'Machine-readable dump of variables',
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `log_id` (`log_id`),
  KEY `i_log_username` (`log_username`),
  KEY `i_log_time` (`log_time`),
  KEY `i_log_sender` (`log_sender`),
  KEY `i_log_code` (`log_code`),
  KEY `i_log_page` (`log_page`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_logs
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_log_dark_matter`
-- ----------------------------
DROP TABLE IF EXISTS `sn_log_dark_matter`;
CREATE TABLE `sn_log_dark_matter` (
  `log_dark_matter_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_dark_matter_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Human-readable record timestamp',
  `log_dark_matter_username` varchar(64) NOT NULL DEFAULT '' COMMENT 'Username',
  `log_dark_matter_reason` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Reason ID for dark matter adjustment',
  `log_dark_matter_amount` int(10) NOT NULL DEFAULT '0' COMMENT 'Amount of dark matter change',
  `log_dark_matter_comment` text COMMENT 'Comments',
  `log_dark_matter_page` varchar(512) NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
  `log_dark_matter_sender` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'User ID which make log record',
  PRIMARY KEY (`log_dark_matter_id`),
  UNIQUE KEY `log_dark_matter_id` (`log_dark_matter_id`),
  KEY `i_log_dark_matter_sender_id` (`log_dark_matter_sender`,`log_dark_matter_id`),
  KEY `i_log_dark_matter_reason_sender_id` (`log_dark_matter_reason`,`log_dark_matter_sender`,`log_dark_matter_id`),
  KEY `i_log_dark_matter_amount` (`log_dark_matter_amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_log_dark_matter
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_mercenaries`
-- ----------------------------
DROP TABLE IF EXISTS `sn_mercenaries`;
CREATE TABLE `sn_mercenaries` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `id` int(4) NOT NULL DEFAULT '0',
  `from` int(11) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `level` int(2) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_mercenaries
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
  KEY `i_owner_time` (`message_owner`,`message_time`),
  KEY `i_sender_time` (`message_sender`,`message_time`),
  KEY `i_time` (`message_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_messages
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_notes`
-- ----------------------------
DROP TABLE IF EXISTS `sn_notes`;
CREATE TABLE `sn_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner` bigint(20) unsigned DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `priority` tinyint(1) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `I_notes_owner` (`owner`),
  CONSTRAINT `FK_notes_owner` FOREIGN KEY (`owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_notes
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_planets`
-- ----------------------------
DROP TABLE IF EXISTS `sn_planets`;
CREATE TABLE `sn_planets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT 'Planet',
  `id_owner` bigint(20) unsigned DEFAULT NULL,
  `galaxy` smallint(6) NOT NULL DEFAULT '0',
  `system` smallint(6) NOT NULL DEFAULT '0',
  `planet` smallint(6) NOT NULL DEFAULT '0',
  `planet_type` tinyint(4) NOT NULL DEFAULT '1',
  `metal` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `crystal` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `deuterium` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `energy_max` decimal(65,0) NOT NULL DEFAULT '0',
  `energy_used` decimal(65,0) NOT NULL DEFAULT '0',
  `metal_mine` smallint(6) NOT NULL DEFAULT '0',
  `crystal_mine` smallint(6) NOT NULL DEFAULT '0',
  `deuterium_sintetizer` smallint(6) NOT NULL DEFAULT '0',
  `solar_plant` smallint(6) NOT NULL DEFAULT '0',
  `fusion_plant` smallint(6) NOT NULL DEFAULT '0',
  `robot_factory` smallint(6) NOT NULL DEFAULT '0',
  `nano_factory` smallint(6) NOT NULL DEFAULT '0',
  `hangar` smallint(6) NOT NULL DEFAULT '0',
  `metal_store` smallint(6) NOT NULL DEFAULT '0',
  `crystal_store` smallint(6) NOT NULL DEFAULT '0',
  `deuterium_store` smallint(6) NOT NULL DEFAULT '0',
  `laboratory` smallint(6) NOT NULL DEFAULT '0',
  `nano` smallint(6) DEFAULT '0',
  `terraformer` smallint(6) NOT NULL DEFAULT '0',
  `ally_deposit` smallint(6) NOT NULL DEFAULT '0',
  `silo` smallint(6) NOT NULL DEFAULT '0',
  `mondbasis` smallint(6) NOT NULL DEFAULT '0',
  `phalanx` smallint(6) NOT NULL DEFAULT '0',
  `sprungtor` smallint(6) NOT NULL DEFAULT '0',
  `last_jump_time` int(11) NOT NULL DEFAULT '0',
  `small_ship_cargo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `big_ship_cargo` bigint(20) unsigned NOT NULL DEFAULT '0',
  `supercargo` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Supercargo ship count',
  `planet_cargo_hyper` bigint(20) unsigned NOT NULL DEFAULT '0',
  `recycler` bigint(20) unsigned NOT NULL DEFAULT '0',
  `colonizer` bigint(20) unsigned NOT NULL DEFAULT '0',
  `spy_sonde` bigint(20) unsigned NOT NULL DEFAULT '0',
  `solar_satelit` bigint(20) unsigned NOT NULL DEFAULT '0',
  `light_hunter` bigint(20) unsigned NOT NULL DEFAULT '0',
  `heavy_hunter` bigint(20) unsigned NOT NULL DEFAULT '0',
  `crusher` bigint(20) unsigned NOT NULL DEFAULT '0',
  `battle_ship` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bomber_ship` bigint(20) unsigned NOT NULL DEFAULT '0',
  `battleship` bigint(20) unsigned NOT NULL DEFAULT '0',
  `destructor` bigint(20) unsigned NOT NULL DEFAULT '0',
  `dearth_star` bigint(20) unsigned NOT NULL DEFAULT '0',
  `supernova` bigint(20) unsigned NOT NULL DEFAULT '0',
  `misil_launcher` bigint(20) unsigned NOT NULL DEFAULT '0',
  `small_laser` bigint(20) unsigned NOT NULL DEFAULT '0',
  `big_laser` bigint(20) unsigned NOT NULL DEFAULT '0',
  `gauss_canyon` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ionic_canyon` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buster_canyon` bigint(20) unsigned NOT NULL DEFAULT '0',
  `small_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `big_protection_shield` tinyint(1) NOT NULL DEFAULT '0',
  `planet_protector` tinyint(1) NOT NULL DEFAULT '0',
  `interceptor_misil` bigint(20) unsigned NOT NULL DEFAULT '0',
  `interplanetary_misil` bigint(20) unsigned NOT NULL DEFAULT '0',
  `metal_perhour` int(11) NOT NULL DEFAULT '0',
  `crystal_perhour` int(11) NOT NULL DEFAULT '0',
  `deuterium_perhour` int(11) NOT NULL DEFAULT '0',
  `metal_mine_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `crystal_mine_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `deuterium_sintetizer_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `solar_plant_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `fusion_plant_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `solar_satelit_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `que` text COMMENT 'Planet que',
  `b_tech` int(11) NOT NULL DEFAULT '0',
  `b_tech_id` smallint(6) NOT NULL DEFAULT '0',
  `b_hangar` int(11) NOT NULL DEFAULT '0',
  `b_hangar_id` text,
  `last_update` int(11) DEFAULT NULL,
  `image` varchar(64) NOT NULL DEFAULT 'normaltempplanet01',
  `points` bigint(20) DEFAULT '0',
  `ranks` bigint(20) DEFAULT '0',
  `id_level` tinyint(4) NOT NULL DEFAULT '0',
  `destruyed` int(11) NOT NULL DEFAULT '0',
  `diameter` int(11) NOT NULL DEFAULT '12800',
  `field_max` smallint(5) unsigned NOT NULL DEFAULT '163',
  `field_current` smallint(5) unsigned NOT NULL DEFAULT '0',
  `temp_min` smallint(6) NOT NULL DEFAULT '0',
  `temp_max` smallint(6) NOT NULL DEFAULT '40',
  `metal_max` decimal(65,0) DEFAULT '100000',
  `crystal_max` decimal(65,0) DEFAULT '100000',
  `deuterium_max` decimal(65,0) DEFAULT '100000',
  `parent_planet` bigint(20) unsigned DEFAULT '0',
  `debris_metal` bigint(20) unsigned DEFAULT '0',
  `debris_crystal` bigint(20) unsigned DEFAULT '0',
  `PLANET_GOVERNOR_ID` smallint(6) NOT NULL DEFAULT '0',
  `PLANET_GOVERNOR_LEVEL` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
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
  KEY `GSPT` (`galaxy`,`system`,`planet`,`planet_type`),
  KEY `i_parent_planet` (`parent_planet`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `sn_quest`
-- ----------------------------
DROP TABLE IF EXISTS `sn_quest`;
CREATE TABLE `sn_quest` (
  `quest_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quest_name` varchar(255) DEFAULT NULL,
  `quest_description` text,
  `quest_conditions` text,
  `quest_rewards` text,
  `quest_type` tinyint(4) DEFAULT NULL,
  `quest_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`quest_id`),
  UNIQUE KEY `quest_id` (`quest_id`),
  KEY `quest_type` (`quest_type`,`quest_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_quest
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_quest_status`
-- ----------------------------
DROP TABLE IF EXISTS `sn_quest_status`;
CREATE TABLE `sn_quest_status` (
  `quest_status_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `quest_status_quest_id` bigint(20) unsigned DEFAULT NULL,
  `quest_status_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `quest_status_progress` varchar(255) NOT NULL DEFAULT '',
  `quest_status_status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`quest_status_id`),
  UNIQUE KEY `quest_status_id` (`quest_status_id`),
  KEY `quest_status_user_id` (`quest_status_user_id`,`quest_status_quest_id`,`quest_status_status`),
  KEY `FK_quest_status_quest_id` (`quest_status_quest_id`),
  CONSTRAINT `FK_quest_status_quest_id` FOREIGN KEY (`quest_status_quest_id`) REFERENCES `sn_quest` (`quest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_quest_status_user_id` FOREIGN KEY (`quest_status_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_quest_status
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_referrals`
-- ----------------------------
DROP TABLE IF EXISTS `sn_referrals`;
CREATE TABLE `sn_referrals` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `id_partner` bigint(20) unsigned DEFAULT NULL,
  `dark_matter` decimal(65,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_partner` (`id_partner`),
  CONSTRAINT `FK_referrals_id` FOREIGN KEY (`id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_referrals_id_partner` FOREIGN KEY (`id_partner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_referrals
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_rw`
-- ----------------------------
DROP TABLE IF EXISTS `sn_rw`;
CREATE TABLE `sn_rw` (
  `report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_owner1` bigint(20) unsigned DEFAULT NULL,
  `id_owner2` bigint(20) unsigned DEFAULT NULL,
  `rid` varchar(72) NOT NULL DEFAULT '',
  `raport` text,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `owners` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  UNIQUE KEY `report_id` (`report_id`),
  KEY `id_owner1` (`id_owner1`,`rid`),
  KEY `id_owner2` (`id_owner2`,`rid`),
  KEY `time` (`time`),
  KEY `i_rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_rw
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_shortcut`
-- ----------------------------
DROP TABLE IF EXISTS `sn_shortcut`;
CREATE TABLE `sn_shortcut` (
  `shortcut_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shortcut_user_id` bigint(20) unsigned DEFAULT NULL,
  `shortcut_planet_id` bigint(20) unsigned DEFAULT NULL,
  `shortcut_galaxy` tinyint(3) unsigned DEFAULT '0',
  `shortcut_system` smallint(5) unsigned DEFAULT '0',
  `shortcut_planet` tinyint(3) unsigned DEFAULT '0',
  `shortcut_planet_type` tinyint(1) NOT NULL DEFAULT '1',
  `shortcut_text` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`shortcut_id`),
  UNIQUE KEY `shortcut_id` (`shortcut_id`),
  UNIQUE KEY `shortcut_id_2` (`shortcut_id`),
  KEY `i_shortcut_user_id` (`shortcut_user_id`),
  KEY `i_shortcut_planet_id` (`shortcut_planet_id`),
  CONSTRAINT `FK_shortcut_planet_id` FOREIGN KEY (`shortcut_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_shortcut_user_id` FOREIGN KEY (`shortcut_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_shortcut
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_statpoints`
-- ----------------------------
DROP TABLE IF EXISTS `sn_statpoints`;
CREATE TABLE `sn_statpoints` (
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
  KEY `TECH` (`tech_points`),
  KEY `BUILDS` (`build_points`),
  KEY `DEFS` (`defs_points`),
  KEY `FLEET` (`fleet_points`),
  KEY `TOTAL` (`total_points`),
  KEY `i_stats_owner` (`id_owner`,`stat_type`,`stat_code`,`tech_rank`,`build_rank`,`defs_rank`,`fleet_rank`,`total_rank`),
  KEY `I_stats_id_ally` (`id_ally`),
  CONSTRAINT `FK_stats_id_owner` FOREIGN KEY (`id_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_statpoints
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_users`
-- ----------------------------
DROP TABLE IF EXISTS `sn_users`;
CREATE TABLE `sn_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `authlevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `vacation` int(11) unsigned DEFAULT '0',
  `banaday` int(11) unsigned DEFAULT '0',
  `dark_matter` bigint(20) DEFAULT '0',
  `spy_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `computer_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `military_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `defence_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `shield_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `energy_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hyperspace_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `combustion_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `impulse_motor_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hyperspace_motor_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `laser_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ionic_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `buster_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `intergalactic_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expedition_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `colonisation_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `graviton_tech` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_amiral` smallint(5) unsigned NOT NULL DEFAULT '0',
  `mrc_academic` smallint(5) unsigned DEFAULT '0',
  `rpg_espion` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_commandant` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_stockeur` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_destructeur` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_general` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_raideur` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rpg_empereur` smallint(5) unsigned NOT NULL DEFAULT '0',
  `player_artifact_list` text,
  `ally_id` bigint(20) unsigned DEFAULT NULL,
  `ally_tag` varchar(8) DEFAULT NULL,
  `ally_name` varchar(32) DEFAULT NULL,
  `ally_register_time` int(11) NOT NULL DEFAULT '0',
  `ally_rank_id` int(11) NOT NULL DEFAULT '0',
  `player_que` text,
  `lvl_minier` bigint(20) unsigned NOT NULL DEFAULT '1',
  `xpminier` bigint(20) unsigned DEFAULT '0',
  `player_rpg_tech_xp` bigint(20) unsigned NOT NULL DEFAULT '0',
  `player_rpg_tech_level` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lvl_raid` bigint(20) unsigned NOT NULL DEFAULT '1',
  `xpraid` bigint(20) unsigned DEFAULT '0',
  `raids` bigint(20) unsigned DEFAULT '0',
  `raidsloose` bigint(20) unsigned DEFAULT '0',
  `raidswin` bigint(20) unsigned DEFAULT '0',
  `new_message` int(11) NOT NULL DEFAULT '0',
  `mnl_alliance` int(11) NOT NULL DEFAULT '0',
  `mnl_joueur` int(11) NOT NULL DEFAULT '0',
  `mnl_attaque` int(11) NOT NULL DEFAULT '0',
  `mnl_spy` int(11) NOT NULL DEFAULT '0',
  `mnl_exploit` int(11) NOT NULL DEFAULT '0',
  `mnl_transport` int(11) NOT NULL DEFAULT '0',
  `mnl_expedition` int(11) NOT NULL DEFAULT '0',
  `mnl_buildlist` int(11) NOT NULL DEFAULT '0',
  `msg_admin` bigint(11) unsigned DEFAULT '0',
  `b_tech_planet` int(11) NOT NULL DEFAULT '0',
  `bana` int(11) DEFAULT NULL,
  `deltime` int(10) unsigned DEFAULT '0',
  `news_lastread` int(10) unsigned DEFAULT '0',
  `total_rank` int(10) unsigned NOT NULL DEFAULT '0',
  `total_points` bigint(20) unsigned NOT NULL DEFAULT '0',
  `password` varchar(64) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `email_2` varchar(64) NOT NULL DEFAULT '',
  `lang` varchar(8) NOT NULL DEFAULT 'ru',
  `sex` char(1) DEFAULT 'M',
  `avatar` varchar(255) NOT NULL DEFAULT '',
  `sign` mediumtext,
  `id_planet` int(11) NOT NULL DEFAULT '0',
  `galaxy` int(11) NOT NULL DEFAULT '0',
  `system` int(11) NOT NULL DEFAULT '0',
  `planet` int(11) NOT NULL DEFAULT '0',
  `current_planet` int(11) NOT NULL DEFAULT '0',
  `user_agent` mediumtext NOT NULL,
  `user_lastip` varchar(250) DEFAULT NULL COMMENT 'User last IP',
  `user_proxy` varchar(250) NOT NULL DEFAULT '' COMMENT 'User proxy (if any)',
  `register_time` int(10) unsigned DEFAULT '0',
  `onlinetime` int(10) unsigned DEFAULT '0',
  `dpath` varchar(255) NOT NULL DEFAULT '',
  `design` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `noipcheck` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `options` mediumtext COMMENT 'Packed user options',
  `planet_sort` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `planet_sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spio_anz` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_tooltiptime` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `settings_fleetactions` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `settings_allylogo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `settings_esp` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_wri` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_bud` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_mis` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_rep` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `i_ally_id` (`ally_id`),
  KEY `i_ally_name` (`ally_name`),
  KEY `i_username` (`username`),
  KEY `i_ally_online` (`ally_id`,`onlinetime`),
  KEY `onlinetime` (`onlinetime`),
  KEY `i_register_time` (`register_time`),
  KEY `FK_users_ally_tag` (`ally_tag`),
  CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_users_ally_tag` FOREIGN KEY (`ally_tag`) REFERENCES `sn_alliance` (`ally_tag`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
INSERT INTO `sn_config` VALUES ('db_version', '32');
INSERT INTO `sn_config` VALUES ('debug', '0');
INSERT INTO `sn_config` VALUES ('Defs_Cdr', '30');
INSERT INTO `sn_config` VALUES ('deuterium_basic_income', 0);
INSERT INTO `sn_config` VALUES ('eco_scale_storage', 1);
INSERT INTO `sn_config` VALUES ('eco_stockman_fleet', '');
INSERT INTO `sn_config` VALUES ('energy_basic_income', '0');
INSERT INTO `sn_config` VALUES ('fleet_bashing_attacks', 3);
INSERT INTO `sn_config` VALUES ('fleet_bashing_interval', 30 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_scope', 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_war_delay', 12 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_waves', 3);
INSERT INTO `sn_config` VALUES ('fleet_buffing_check', 1);
INSERT INTO `sn_config` VALUES ('Fleet_Cdr', '30');
INSERT INTO `sn_config` VALUES ('fleet_speed', '1');
INSERT INTO `sn_config` VALUES ('flt_lastUpdate', UNIX_TIMESTAMP(NOW()));
INSERT INTO `sn_config` VALUES ('game_adminEmail', 'root@localhost');
INSERT INTO `sn_config` VALUES ('game_counter', '0');
INSERT INTO `sn_config` VALUES ('game_default_language', 'ru');
INSERT INTO `sn_config` VALUES ('game_default_skin', 'skins/EpicBlue/');
INSERT INTO `sn_config` VALUES ('game_default_template', 'OpenGame');
INSERT INTO `sn_config` VALUES ('game_disable', 0);
INSERT INTO `sn_config` VALUES ('game_disable_reason', 'SuperNova is in maintenance mode! Please return later!');
INSERT INTO `sn_config` VALUES ('game_email_pm', 0);
INSERT INTO `sn_config` VALUES ('game_maxGalaxy', 5);
INSERT INTO `sn_config` VALUES ('game_maxPlanet', 15);
INSERT INTO `sn_config` VALUES ('game_maxSystem', 199);
INSERT INTO `sn_config` VALUES ('game_mode', 0);
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
INSERT INTO `sn_config` VALUES ('player_delete_time', 45 * 24*60*60);
INSERT INTO `sn_config` VALUES ('player_max_colonies', '9');
INSERT INTO `sn_config` VALUES ('player_vacation_time', 2 * 24*60*60);
INSERT INTO `sn_config` VALUES ('quest_total', '0');
INSERT INTO `sn_config` VALUES ('resource_multiplier', '1');
INSERT INTO `sn_config` VALUES ('rpg_bonus_divisor', '10');
INSERT INTO `sn_config` VALUES ('rpg_bonus_minimum', '10000');
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
INSERT INTO `sn_users` (`id`, `username`, `password`, `email`, `email_2`, `authlevel`, `id_planet`, `galaxy`, `system`, `planet`, `current_planet`, `register_time`, `onlinetime`, `noipcheck`, `sex`) VALUES (1, 'admin',  '21232f297a57a5a743894a0e4a801fc3', 'root@localhost', 'root@localhost', 3, 1, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 1, 'M');

-- ----------------------------
-- Administrator's planet
-- ----------------------------
INSERT INTO `sn_planets` (`id`, `name`, `id_owner`, `id_level`, `galaxy`, `system`, `planet`, `planet_type`, `last_update`) VALUES (1, 'Planet', 1, 0, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()));
