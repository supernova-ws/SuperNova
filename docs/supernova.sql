/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50141
Source Host           : localhost:3306
Source Database       : supernova

Target Server Type    : MYSQL
Target Server Version : 50141
File Encoding         : 65001

Date: 2013-10-13 22:36:25
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
  `ally_image` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ally_request` text,
  `ally_request_waiting` text,
  `ally_request_notallow` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ally_owner_range` varchar(32) DEFAULT '',
  `ally_ranks` text,
  `ally_members` int(11) NOT NULL DEFAULT '0',
  `ranklist` text,
  `total_rank` int(10) unsigned NOT NULL DEFAULT '0',
  `total_points` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ally_user_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `i_ally_name` (`ally_name`),
  UNIQUE KEY `i_ally_tag` (`ally_tag`),
  KEY `I_ally_user_id` (`ally_user_id`),
  KEY `FK_alliance_owner` (`ally_owner`),
  CONSTRAINT `FK_alliance_owner` FOREIGN KEY (`ally_owner`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ally_ally_user_id` FOREIGN KEY (`ally_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  CONSTRAINT `FK_negotiation_ally_name` FOREIGN KEY (`alliance_negotiation_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_negotiation_contr_ally_id` FOREIGN KEY (`alliance_negotiation_contr_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  CONSTRAINT `FK_alliance_request_ally_id` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_alliance_request_user_id` FOREIGN KEY (`id_user`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `ban_user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Banned user ID',
  `ban_user_name` varchar(64) NOT NULL DEFAULT '',
  `ban_reason` varchar(128) NOT NULL DEFAULT '',
  `ban_time` int(11) NOT NULL DEFAULT '0',
  `ban_until` int(11) NOT NULL DEFAULT '0',
  `ban_issuer_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Banner ID',
  `ban_issuer_name` varchar(64) NOT NULL DEFAULT '',
  `ban_issuer_email` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ban_id`),
  KEY `ID` (`ban_id`),
  KEY `I_ban_user_id` (`ban_user_id`),
  KEY `I_ban_issuer_id` (`ban_issuer_id`),
  CONSTRAINT `FK_ban_user_id` FOREIGN KEY (`ban_user_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ban_issuer_id` FOREIGN KEY (`ban_issuer_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
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
  CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_bashing_user_id` FOREIGN KEY (`bashing_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_bashing
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_buddy`
-- ----------------------------
DROP TABLE IF EXISTS `sn_buddy`;
CREATE TABLE `sn_buddy` (
  `BUDDY_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Buddy table row ID',
  `BUDDY_SENDER_ID` bigint(20) unsigned DEFAULT NULL COMMENT 'Buddy request sender ID',
  `BUDDY_OWNER_ID` bigint(20) unsigned DEFAULT NULL COMMENT 'Buddy request recipient ID',
  `BUDDY_STATUS` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Buddy request status',
  `BUDDY_REQUEST` tinytext COMMENT 'Buddy request text',
  PRIMARY KEY (`BUDDY_ID`),
  UNIQUE KEY `BUDDY_ID` (`BUDDY_ID`),
  KEY `I_BUDDY_SENDER_ID` (`BUDDY_SENDER_ID`,`BUDDY_OWNER_ID`),
  KEY `I_BUDDY_OWNER_ID` (`BUDDY_OWNER_ID`,`BUDDY_SENDER_ID`),
  CONSTRAINT `FK_BUDDY_SENDER_ID` FOREIGN KEY (`BUDDY_SENDER_ID`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BUDDY_OWNER_ID` FOREIGN KEY (`BUDDY_OWNER_ID`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_buddy
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_captain`
-- ----------------------------
DROP TABLE IF EXISTS `sn_captain`;
CREATE TABLE `sn_captain` (
  `captain_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `captain_unit_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Link to `unit` record',
  `captain_xp` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Captain expirience',
  `captain_level` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Captain level so far',
  `captain_shield` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Captain shield bonus level',
  `captain_armor` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Captain armor bonus level',
  `captain_attack` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Captain defense bonus level',
  PRIMARY KEY (`captain_id`),
  UNIQUE KEY `captain_id` (`captain_id`),
  KEY `I_captain_unit_id` (`captain_unit_id`),
  CONSTRAINT `FK_captain_unit_id` FOREIGN KEY (`captain_unit_id`) REFERENCES `sn_unit` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_captain
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_chat`
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat`;
CREATE TABLE `sn_chat` (
  `messageid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chat_message_sender_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Message sender ID',
  `chat_message_sender_name` varchar(64) DEFAULT '' COMMENT 'Message sender name',
  `user` text COMMENT 'Chat message user name',
  `chat_message_recipient_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Message recipient ID',
  `chat_message_recipient_name` varchar(64) DEFAULT '' COMMENT 'Message sender name',
  `message` text,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `ally_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`messageid`),
  UNIQUE KEY `messageid` (`messageid`),
  KEY `i_ally_idmess` (`ally_id`,`messageid`),
  KEY `I_chat_message_sender_id` (`chat_message_sender_id`),
  KEY `I_chat_message_recipient_id` (`chat_message_recipient_id`),
  CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_chat
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_chat_player`
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat_player`;
CREATE TABLE `sn_chat_player` (
  `chat_player_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `chat_player_player_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Chat player record owner',
  `chat_player_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last player activity in chat',
  `chat_player_invisible` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Player invisibility',
  `chat_player_muted` int(11) NOT NULL DEFAULT '0' COMMENT 'Player is muted',
  `chat_player_mute_reason` varchar(256) NOT NULL DEFAULT '' COMMENT 'Player mute reason',
  `chat_player_refresh_last` int(11) NOT NULL DEFAULT '0' COMMENT 'Player last refresh time',
  PRIMARY KEY (`chat_player_id`),
  UNIQUE KEY `chat_player_id` (`chat_player_id`),
  KEY `I_chat_player_id` (`chat_player_player_id`),
  KEY `I_chat_player_refresh_last` (`chat_player_refresh_last`),
  CONSTRAINT `FK_chat_player_id` FOREIGN KEY (`chat_player_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_chat_player
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
  `fleet_owner` bigint(20) unsigned DEFAULT NULL,
  `fleet_mission` int(11) NOT NULL DEFAULT '0',
  `fleet_amount` bigint(11) NOT NULL DEFAULT '0',
  `fleet_array` text,
  `fleet_start_time` int(11) NOT NULL DEFAULT '0',
  `fleet_start_planet_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Fleet start planet ID',
  `fleet_start_galaxy` int(11) NOT NULL DEFAULT '0',
  `fleet_start_system` int(11) NOT NULL DEFAULT '0',
  `fleet_start_planet` int(11) NOT NULL DEFAULT '0',
  `fleet_start_type` int(11) NOT NULL DEFAULT '0',
  `fleet_end_time` int(11) NOT NULL DEFAULT '0',
  `fleet_end_stay` int(11) NOT NULL DEFAULT '0',
  `fleet_end_planet_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Fleet end planet ID',
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
  KEY `fleet_group` (`fleet_group`),
  KEY `I_fleet_start_planet_id` (`fleet_start_planet_id`),
  KEY `I_fleet_end_planet_id` (`fleet_end_planet_id`),
  CONSTRAINT `FK_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fleet_planet_end` FOREIGN KEY (`fleet_end_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_fleet_planet_start` FOREIGN KEY (`fleet_start_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
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
  `fleet_start_type` smallint(6) NOT NULL DEFAULT '1',
  `fleet_end_type` smallint(6) NOT NULL DEFAULT '1',
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
  KEY `I_owner_priority_time` (`owner`,`priority`,`time`),
  CONSTRAINT `FK_notes_owner` FOREIGN KEY (`owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_notes
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_payment`
-- ----------------------------
DROP TABLE IF EXISTS `sn_payment`;
CREATE TABLE `sn_payment` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal payment ID',
  `payment_status` int(11) DEFAULT '0' COMMENT 'Payment status',
  `payment_user_id` bigint(20) unsigned DEFAULT NULL,
  `payment_user_name` varchar(64) DEFAULT NULL,
  `payment_amount` decimal(60,5) DEFAULT '0.00000' COMMENT 'Amount paid',
  `payment_currency` varchar(3) DEFAULT '' COMMENT 'Payment currency',
  `payment_dark_matter_paid` decimal(65,0) DEFAULT '0' COMMENT 'Real DM paid for',
  `payment_dark_matter_gained` decimal(65,0) DEFAULT '0' COMMENT 'DM gained by player (with bonuses)',
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Payment server timestamp',
  `payment_comment` text COMMENT 'Payment comment',
  `payment_module_name` varchar(255) DEFAULT '' COMMENT 'Payment module name',
  `payment_external_id` varchar(255) DEFAULT '' COMMENT 'External payment ID in payment system',
  `payment_external_date` datetime DEFAULT NULL COMMENT 'External payment timestamp in payment system',
  `payment_external_lots` decimal(65,5) NOT NULL DEFAULT '0.00000' COMMENT 'Payment system lot amount',
  `payment_external_amount` decimal(65,5) NOT NULL DEFAULT '0.00000' COMMENT 'Money incoming from payment system',
  `payment_external_currency` varchar(3) NOT NULL DEFAULT '' COMMENT 'Payment system currency',
  PRIMARY KEY (`payment_id`),
  KEY `I_payment_user` (`payment_user_id`,`payment_user_name`),
  KEY `I_payment_module_internal_id` (`payment_module_name`,`payment_external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_payment
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
  `planet_teleport_next` int(11) NOT NULL DEFAULT '0' COMMENT 'Next teleport time',
  `ship_sattelite_sloth` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Terran Sloth',
  `ship_bomber_envy` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Lunar Envy',
  `ship_recycler_gluttony` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Mercurian Gluttony',
  `ship_fighter_wrath` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Venerian Wrath',
  `ship_battleship_pride` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Martian Pride',
  `ship_cargo_greed` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Republican Greed',
  `ship_sattelite_sloth_porcent` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT 'Terran Sloth production',
  `ship_orbital_heavy` bigint(20) NOT NULL DEFAULT '0' COMMENT 'HOPe - Heavy Orbital Platform',
  `density` smallint(6) NOT NULL DEFAULT '5500' COMMENT 'Planet average density kg/m3',
  `density_index` tinyint(4) NOT NULL DEFAULT '4' COMMENT 'Planet cached density index',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `owner_type` (`id_owner`,`planet_type`),
  KEY `i_metal` (`metal`),
  KEY `id_level` (`id_level`),
  KEY `i_last_update` (`last_update`),
  KEY `GSPT` (`galaxy`,`system`,`planet`,`planet_type`),
  KEY `i_parent_planet` (`parent_planet`),
  KEY `I_ship_sattelite_sloth` (`ship_sattelite_sloth`,`id_level`),
  KEY `I_ship_bomber_envy` (`ship_bomber_envy`,`id_level`),
  KEY `I_ship_recycler_gluttony` (`ship_recycler_gluttony`,`id_level`),
  KEY `I_ship_fighter_wrath` (`ship_fighter_wrath`,`id_level`),
  KEY `I_ship_battleship_pride` (`ship_battleship_pride`,`id_level`),
  KEY `I_ship_cargo_greed` (`ship_cargo_greed`,`id_level`),
  KEY `I_metal_mine` (`id_owner`,`metal_mine`),
  KEY `I_crystal_mine` (`id_owner`,`crystal_mine`),
  KEY `I_deuterium_sintetizer` (`id_owner`,`deuterium_sintetizer`),
  KEY `I_solar_plant` (`id_owner`,`solar_plant`),
  KEY `I_fusion_plant` (`id_owner`,`fusion_plant`),
  KEY `I_robot_factory` (`id_owner`,`robot_factory`),
  KEY `I_hangar` (`id_owner`,`hangar`),
  KEY `I_nano_factory` (`id_owner`,`nano_factory`),
  KEY `I_laboratory` (`id_owner`,`laboratory`),
  KEY `I_nano` (`id_owner`,`nano`),
  KEY `I_silo` (`id_owner`,`silo`),
  KEY `I_metal_store` (`id_owner`,`metal_store`),
  KEY `I_crystal_store` (`id_owner`,`crystal_store`),
  KEY `I_deuterium_store` (`id_owner`,`deuterium_store`),
  KEY `I_ally_deposit` (`id_owner`,`ally_deposit`),
  KEY `I_terraformer` (`id_owner`,`terraformer`),
  KEY `I_mondbasis` (`id_owner`,`mondbasis`),
  KEY `I_phalanx` (`id_owner`,`phalanx`),
  KEY `I_sprungtor` (`id_owner`,`sprungtor`),
  KEY `I_light_hunter` (`id_owner`,`light_hunter`),
  KEY `I_heavy_hunter` (`id_owner`,`heavy_hunter`),
  KEY `I_crusher` (`id_owner`,`crusher`),
  KEY `I_battle_ship` (`id_owner`,`battle_ship`),
  KEY `I_bomber_ship` (`id_owner`,`bomber_ship`),
  KEY `I_battleship` (`id_owner`,`battleship`),
  KEY `I_destructor` (`id_owner`,`destructor`),
  KEY `I_dearth_star` (`id_owner`,`dearth_star`),
  KEY `I_supernova` (`id_owner`,`supernova`),
  KEY `I_small_ship_cargo` (`id_owner`,`small_ship_cargo`),
  KEY `I_big_ship_cargo` (`id_owner`,`big_ship_cargo`),
  KEY `I_supercargo` (`id_owner`,`supercargo`),
  KEY `I_planet_cargo_hyper` (`id_owner`,`planet_cargo_hyper`),
  KEY `I_recycler` (`id_owner`,`recycler`),
  KEY `I_colonizer` (`id_owner`,`colonizer`),
  KEY `I_spy_sonde` (`id_owner`,`spy_sonde`),
  KEY `I_solar_satelit` (`id_owner`,`solar_satelit`),
  KEY `I_misil_launcher` (`id_owner`,`misil_launcher`),
  KEY `I_small_laser` (`id_owner`,`small_laser`),
  KEY `I_big_laser` (`id_owner`,`big_laser`),
  KEY `I_gauss_canyon` (`id_owner`,`gauss_canyon`),
  KEY `I_ionic_canyon` (`id_owner`,`ionic_canyon`),
  KEY `I_buster_canyon` (`id_owner`,`buster_canyon`),
  KEY `I_small_protection_shield` (`id_owner`,`small_protection_shield`),
  KEY `I_big_protection_shield` (`id_owner`,`big_protection_shield`),
  KEY `I_planet_protector` (`id_owner`,`planet_protector`),
  KEY `I_interceptor_misil` (`id_owner`,`interceptor_misil`),
  KEY `I_interplanetary_misil` (`id_owner`,`interplanetary_misil`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for `sn_player_name_history`
-- ----------------------------
DROP TABLE IF EXISTS `sn_player_name_history`;
CREATE TABLE `sn_player_name_history` (
  `player_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Player ID',
  `player_name` varchar(32) NOT NULL COMMENT 'Historical player name',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'When player changed name',
  PRIMARY KEY (`player_name`),
  KEY `I_player_name_history_id_name` (`player_id`,`player_name`),
  CONSTRAINT `FK_player_name_history_id` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `sn_powerup`
-- ----------------------------
DROP TABLE IF EXISTS `sn_powerup`;
CREATE TABLE `sn_powerup` (
  `powerup_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `powerup_user_id` bigint(20) unsigned DEFAULT NULL,
  `powerup_planet_id` bigint(20) unsigned DEFAULT NULL,
  `powerup_category` smallint(6) NOT NULL DEFAULT '0',
  `powerup_unit_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `powerup_unit_level` smallint(5) unsigned NOT NULL DEFAULT '0',
  `powerup_time_start` int(11) NOT NULL DEFAULT '0',
  `powerup_time_finish` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`powerup_id`),
  KEY `I_powerup_user_id` (`powerup_user_id`),
  KEY `I_powerup_planet_id` (`powerup_planet_id`),
  KEY `I_user_powerup_time` (`powerup_user_id`,`powerup_unit_id`,`powerup_time_start`,`powerup_time_finish`),
  KEY `I_planet_powerup_time` (`powerup_planet_id`,`powerup_unit_id`,`powerup_time_start`,`powerup_time_finish`),
  CONSTRAINT `FK_powerup_planet_id` FOREIGN KEY (`powerup_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_powerup_user_id` FOREIGN KEY (`powerup_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_powerup
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_que`
-- ----------------------------
DROP TABLE IF EXISTS `sn_que`;
CREATE TABLE `sn_que` (
  `que_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Internal que id',
  `que_player_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Que owner ID',
  `que_planet_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Which planet this que item belongs',
  `que_planet_id_origin` bigint(20) unsigned DEFAULT NULL COMMENT 'Planet spawner ID',
  `que_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Que type',
  `que_time_left` decimal(20,5) unsigned NOT NULL DEFAULT '0.00000' COMMENT 'Build time left from last activity',
  `que_unit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit ID',
  `que_unit_amount` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Amount left to build',
  `que_unit_mode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Build/Destroy',
  `que_unit_level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit level. Informational field',
  `que_unit_time` decimal(20,5) NOT NULL DEFAULT '0.00000' COMMENT 'Time to build one unit. Informational field',
  `que_unit_price` varchar(128) NOT NULL DEFAULT '' COMMENT 'Price per unit - for correct trim/clear in case of global price events',
  PRIMARY KEY (`que_id`),
  UNIQUE KEY `que_id` (`que_id`),
  KEY `I_que_player_type_planet` (`que_player_id`,`que_type`,`que_planet_id`,`que_id`),
  KEY `I_que_player_type` (`que_player_id`,`que_type`,`que_id`),
  KEY `I_que_planet_id` (`que_planet_id`),
  KEY `FK_que_planet_id_origin` (`que_planet_id_origin`),
  CONSTRAINT `FK_que_player_id` FOREIGN KEY (`que_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_que_planet_id` FOREIGN KEY (`que_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_que_planet_id_origin` FOREIGN KEY (`que_planet_id_origin`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_que
-- ----------------------------

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
  CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_stats_id_owner` FOREIGN KEY (`id_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_statpoints
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report`;
CREATE TABLE `sn_ube_report` (
  `ube_report_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Report ID',
  `ube_report_cypher` char(32) NOT NULL DEFAULT '' COMMENT '16 char secret report ID',
  `ube_report_time_combat` datetime NOT NULL COMMENT 'Combat time',
  `ube_report_time_process` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Time when combat was processed',
  `ube_report_time_spent` decimal(11,8) unsigned NOT NULL DEFAULT '0.00000000' COMMENT 'Time in seconds spent for combat calculations',
  `ube_report_mission_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Mission type',
  `ube_report_combat_admin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Does admin participates in combat?',
  `ube_report_combat_result` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Combat outcome',
  `ube_report_combat_sfr` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Small Fleet Reconnaissance',
  `ube_report_planet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet ID',
  `ube_report_planet_name` varchar(64) NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
  `ube_report_planet_size` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player diameter',
  `ube_report_planet_galaxy` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate galaxy',
  `ube_report_planet_system` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate system',
  `ube_report_planet_planet` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate planet',
  `ube_report_planet_planet_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Player planet type',
  `ube_report_moon` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Moon result: was, none, failed, created, destroyed',
  `ube_report_moon_chance` decimal(9,6) unsigned NOT NULL DEFAULT '0.000000' COMMENT 'Moon creation chance',
  `ube_report_moon_size` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Moon size',
  `ube_report_moon_reapers` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Moon reapers result: none, died, survived',
  `ube_report_moon_destroy_chance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Moon destroy chance',
  `ube_report_moon_reapers_die_chance` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Moon reapers die chance',
  `ube_report_debris_metal` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Metal debris',
  `ube_report_debris_crystal` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Crystal debris',
  PRIMARY KEY (`ube_report_id`),
  UNIQUE KEY `ube_report_id` (`ube_report_id`),
  KEY `I_ube_report_cypher` (`ube_report_cypher`),
  KEY `I_ube_report_time_combat` (`ube_report_time_combat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report_fleet`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_fleet`;
CREATE TABLE `sn_ube_report_fleet` (
  `ube_report_fleet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
  `ube_report_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Report ID',
  `ube_report_fleet_player_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Owner ID',
  `ube_report_fleet_fleet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet ID',
  `ube_report_fleet_planet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Player attack bonus',
  `ube_report_fleet_planet_name` varchar(64) NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
  `ube_report_fleet_planet_galaxy` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate galaxy',
  `ube_report_fleet_planet_system` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate system',
  `ube_report_fleet_planet_planet` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Player planet coordinate planet',
  `ube_report_fleet_planet_planet_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Player planet type',
  `ube_report_fleet_bonus_attack` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Fleet attack bonus',
  `ube_report_fleet_bonus_shield` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Fleet shield bonus',
  `ube_report_fleet_bonus_armor` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Fleet armor bonus',
  `ube_report_fleet_resource_metal` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet metal amount',
  `ube_report_fleet_resource_crystal` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet crystal amount',
  `ube_report_fleet_resource_deuterium` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet deuterium amount',
  PRIMARY KEY (`ube_report_fleet_id`),
  UNIQUE KEY `ube_report_fleet_id` (`ube_report_fleet_id`),
  KEY `FK_ube_report_fleet_ube_report` (`ube_report_id`),
  CONSTRAINT `FK_ube_report_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report_fleet
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report_outcome_fleet`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_outcome_fleet`;
CREATE TABLE `sn_ube_report_outcome_fleet` (
  `ube_report_outcome_fleet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
  `ube_report_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Report ID',
  `ube_report_outcome_fleet_fleet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet ID',
  `ube_report_outcome_fleet_resource_lost_metal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet metal loss from units',
  `ube_report_outcome_fleet_resource_lost_crystal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet crystal loss from units',
  `ube_report_outcome_fleet_resource_lost_deuterium` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet deuterium loss from units',
  `ube_report_outcome_fleet_resource_dropped_metal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet metal dropped due reduced cargo',
  `ube_report_outcome_fleet_resource_dropped_crystal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet crystal dropped due reduced cargo',
  `ube_report_outcome_fleet_resource_dropped_deuterium` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet deuterium dropped due reduced cargo',
  `ube_report_outcome_fleet_resource_loot_metal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Looted/Lost from loot metal',
  `ube_report_outcome_fleet_resource_loot_crystal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Looted/Lost from loot crystal',
  `ube_report_outcome_fleet_resource_loot_deuterium` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Looted/Lost from loot deuterium',
  `ube_report_outcome_fleet_resource_lost_in_metal` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Fleet total resource loss in metal',
  PRIMARY KEY (`ube_report_outcome_fleet_id`),
  UNIQUE KEY `ube_report_outcome_fleet_id` (`ube_report_outcome_fleet_id`),
  KEY `I_ube_report_outcome_fleet_report_fleet` (`ube_report_id`,`ube_report_outcome_fleet_fleet_id`),
  CONSTRAINT `FK_ube_report_outcome_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report_outcome_fleet
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report_outcome_unit`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_outcome_unit`;
CREATE TABLE `sn_ube_report_outcome_unit` (
  `ube_report_outcome_unit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
  `ube_report_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Report ID',
  `ube_report_outcome_unit_fleet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet ID',
  `ube_report_outcome_unit_unit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit ID',
  `ube_report_outcome_unit_restored` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Unit restored',
  `ube_report_outcome_unit_lost` decimal(65,0) NOT NULL DEFAULT '0' COMMENT 'Unit lost',
  `ube_report_outcome_unit_sort_order` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit pass-through sort order to maintain same output',
  PRIMARY KEY (`ube_report_outcome_unit_id`),
  UNIQUE KEY `ube_report_outcome_unit_id` (`ube_report_outcome_unit_id`),
  KEY `I_ube_report_outcome_unit_report_order` (`ube_report_id`,`ube_report_outcome_unit_sort_order`),
  CONSTRAINT `FK_ube_report_outcome_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report_outcome_unit
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report_player`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_player`;
CREATE TABLE `sn_ube_report_player` (
  `ube_report_player_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `ube_report_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Report ID',
  `ube_report_player_player_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Player ID',
  `ube_report_player_name` varchar(64) NOT NULL DEFAULT '' COMMENT 'Player name',
  `ube_report_player_attacker` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is player an attacker?',
  `ube_report_player_bonus_attack` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Player attack bonus',
  `ube_report_player_bonus_shield` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Player shield bonus',
  `ube_report_player_bonus_armor` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT 'Player armor bonus',
  PRIMARY KEY (`ube_report_player_id`),
  UNIQUE KEY `ube_report_player_id` (`ube_report_player_id`),
  KEY `I_ube_report_player_player_id` (`ube_report_player_player_id`),
  KEY `FK_ube_report_player_ube_report` (`ube_report_id`),
  CONSTRAINT `FK_ube_report_player_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report_player
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_ube_report_unit`
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_unit`;
CREATE TABLE `sn_ube_report_unit` (
  `ube_report_unit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
  `ube_report_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Report ID',
  `ube_report_unit_player_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Owner ID',
  `ube_report_unit_fleet_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Fleet ID',
  `ube_report_unit_round` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Round number',
  `ube_report_unit_unit_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit ID',
  `ube_report_unit_count` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit count',
  `ube_report_unit_boom` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit booms',
  `ube_report_unit_attack` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit attack',
  `ube_report_unit_shield` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit shield',
  `ube_report_unit_armor` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit armor',
  `ube_report_unit_attack_base` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit base attack',
  `ube_report_unit_shield_base` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit base shield',
  `ube_report_unit_armor_base` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit base armor',
  `ube_report_unit_sort_order` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit pass-through sort order to maintain same output',
  PRIMARY KEY (`ube_report_unit_id`),
  UNIQUE KEY `ube_report_unit_id` (`ube_report_unit_id`),
  KEY `I_ube_report_unit_report_round_fleet_order` (`ube_report_id`,`ube_report_unit_round`,`ube_report_unit_fleet_id`,`ube_report_unit_sort_order`),
  KEY `I_ube_report_unit_report_unit_order` (`ube_report_id`,`ube_report_unit_sort_order`),
  KEY `I_ube_report_unit_order` (`ube_report_unit_sort_order`),
  CONSTRAINT `FK_ube_report_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_ube_report_unit
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_unit`
-- ----------------------------
DROP TABLE IF EXISTS `sn_unit`;
CREATE TABLE `sn_unit` (
  `unit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `unit_player_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Unit owner',
  `unit_location_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Location type: universe, user, planet (moon?), fleet',
  `unit_location_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Location ID',
  `unit_type` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit type',
  `unit_snid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit SuperNova ID',
  `unit_level` decimal(65,0) unsigned NOT NULL DEFAULT '0' COMMENT 'Unit level or count - dependent of unit_type',
  `unit_time_start` datetime DEFAULT NULL COMMENT 'Unit activation start time',
  `unit_time_finish` datetime DEFAULT NULL COMMENT 'Unit activation end time',
  PRIMARY KEY (`unit_id`),
  KEY `I_unit_player_location_snid` (`unit_player_id`,`unit_location_type`,`unit_location_id`,`unit_snid`),
  KEY `I_unit_record_search` (`unit_snid`,`unit_player_id`,`unit_level`,`unit_id`),
  CONSTRAINT `FK_unit_player_id` FOREIGN KEY (`unit_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_unit
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_universe`
-- ----------------------------
DROP TABLE IF EXISTS `sn_universe`;
CREATE TABLE `sn_universe` (
  `universe_galaxy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `universe_system` smallint(5) unsigned NOT NULL DEFAULT '0',
  `universe_name` varchar(32) NOT NULL DEFAULT '',
  `universe_price` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`universe_galaxy`,`universe_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sn_universe
-- ----------------------------

-- ----------------------------
-- Table structure for `sn_users`
-- ----------------------------
DROP TABLE IF EXISTS `sn_users`;
CREATE TABLE `sn_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT 'Player name',
  `authlevel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `vacation` int(11) unsigned DEFAULT '0',
  `banaday` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User ban status',
  `dark_matter` bigint(20) DEFAULT '0',
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
  `avatar` tinyint(1) unsigned NOT NULL DEFAULT '0',
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
  `user_time_diff` int(11) DEFAULT NULL COMMENT 'User time difference with server time',
  `user_time_utc_offset` int(11) DEFAULT NULL COMMENT 'User time difference with server time',
  `user_time_diff_forced` tinyint(1) DEFAULT '0' COMMENT 'User time difference forced with time zone selection flag',
  `dpath` varchar(255) NOT NULL DEFAULT '',
  `design` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `noipcheck` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `options` mediumtext COMMENT 'Packed user options',
  `planet_sort` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `planet_sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `spio_anz` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_tooltiptime` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `settings_fleetactions` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `settings_esp` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_wri` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_bud` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_mis` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `settings_rep` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_as_ally` bigint(20) unsigned DEFAULT NULL,
  `metal` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `crystal` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `deuterium` decimal(65,5) NOT NULL DEFAULT '0.00000',
  `user_birthday` date DEFAULT NULL COMMENT 'User birthday',
  `user_birthday_celebrated` date DEFAULT NULL COMMENT 'Last time where user got birthday gift',
  `player_race` int(11) NOT NULL DEFAULT '0' COMMENT 'Player''s race',
  PRIMARY KEY (`id`),
  KEY `i_ally_id` (`ally_id`),
  KEY `i_ally_name` (`ally_name`),
  KEY `i_username` (`username`),
  KEY `i_ally_online` (`ally_id`,`onlinetime`),
  KEY `onlinetime` (`onlinetime`),
  KEY `i_register_time` (`register_time`),
  KEY `FK_users_ally_tag` (`ally_tag`),
  KEY `I_user_user_as_ally` (`user_as_ally`),
  KEY `I_user_birthday` (`user_birthday`,`user_birthday_celebrated`),
  KEY `I_user_id_name` (`id`,`username`),
  CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_users_ally_tag` FOREIGN KEY (`ally_tag`) REFERENCES `sn_alliance` (`ally_tag`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_user_user_as_ally` FOREIGN KEY (`user_as_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Default server configuration
-- ----------------------------
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuCode', '<script type=\"text/javascript\"><!--\r\ngoogle_ad_client = \"pub-1914310741599503\";\r\n/* oGame */\r\ngoogle_ad_slot = \"2544836773\";\r\ngoogle_ad_width = 125;\r\ngoogle_ad_height = 125;\r\n//-->\r\n</script>\r\n<script type=\"text/javascript\"\r\nsrc=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">\r\n</script>\r\n');
INSERT INTO `sn_config` VALUES ('advGoogleLeftMenuIsOn', '1');
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
INSERT INTO `sn_config` VALUES ('chat_timeout', '900');
INSERT INTO `sn_config` VALUES ('COOKIE_NAME', 'SuperNova');
INSERT INTO `sn_config` VALUES ('crystal_basic_income', '20');
INSERT INTO `sn_config` VALUES ('db_prefix', 'sn_');
INSERT INTO `sn_config` VALUES ('db_version', '36');
INSERT INTO `sn_config` VALUES ('debug', '0');
INSERT INTO `sn_config` VALUES ('Defs_Cdr', '30');
INSERT INTO `sn_config` VALUES ('deuterium_basic_income', '0');
INSERT INTO `sn_config` VALUES ('eco_scale_storage', '1');
INSERT INTO `sn_config` VALUES ('eco_stockman_fleet', '');
INSERT INTO `sn_config` VALUES ('empire_mercenary_base_period', 30 * 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('empire_mercenary_temporary', '1');
INSERT INTO `sn_config` VALUES ('energy_basic_income', '0');
INSERT INTO `sn_config` VALUES ('fleet_bashing_attacks', '3');
INSERT INTO `sn_config` VALUES ('fleet_bashing_interval', 30 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_scope', 24 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_war_delay', 12 * 60 * 60);
INSERT INTO `sn_config` VALUES ('fleet_bashing_waves', '3');
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
INSERT INTO `sn_config` VALUES ('game_email_pm', '0');
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
INSERT INTO `sn_config` VALUES ('game_user_changename', '2');
INSERT INTO `sn_config` VALUES ('game_user_changename_cost', 100000);
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
INSERT INTO `sn_config` VALUES ('payment_currency_default', 'UAH');
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_dm_', 2500);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_eur', 0.09259259259259);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_rub', 4.0);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_usd', 0.125);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wme', 0.0952380952381);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmr', 4.1);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmu', 1.05);
INSERT INTO `sn_config` VALUES ('payment_currency_exchange_wmz', 0.126582278481);
INSERT INTO `sn_config` VALUES ('payment_lot_price', '1');
INSERT INTO `sn_config` VALUES ('payment_lot_size', '2500');
INSERT INTO `sn_config` VALUES ('planet_capital_cost', 25000);
INSERT INTO `sn_config` VALUES ('planet_teleport_cost', 50000);
INSERT INTO `sn_config` VALUES ('planet_teleport_timeout', 1 * 24*60*60);
INSERT INTO `sn_config` VALUES ('player_delete_time', 45 * 24*60*60);
INSERT INTO `sn_config` VALUES ('player_max_colonies', '9');
INSERT INTO `sn_config` VALUES ('player_vacation_time', 7 * 24*60*60);
INSERT INTO `sn_config` VALUES ('player_vacation_timeout', 7 * 24*60*60);
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
INSERT INTO `sn_config` VALUES ('stats_hide_admins', 1);
INSERT INTO `sn_config` VALUES ('stats_hide_player_list', '');
INSERT INTO `sn_config` VALUES ('stats_hide_pm_link', 0);
INSERT INTO `sn_config` VALUES ('stats_schedule', 'd@04:00:00');
INSERT INTO `sn_config` VALUES ('tpl_minifier', '0');
INSERT INTO `sn_config` VALUES ('uni_price_galaxy', '10000');
INSERT INTO `sn_config` VALUES ('uni_price_system', '1000');
INSERT INTO `sn_config` VALUES ('upd_lock_time', '60');
INSERT INTO `sn_config` VALUES ('url_dark_matter', '');
INSERT INTO `sn_config` VALUES ('url_faq', '');
INSERT INTO `sn_config` VALUES ('url_forum', '');
INSERT INTO `sn_config` VALUES ('url_rules', '');
INSERT INTO `sn_config` VALUES ('users_amount', '1');
INSERT INTO `sn_config` VALUES ('user_birthday_celebrate', '0');
INSERT INTO `sn_config` VALUES ('user_birthday_gift', '0');
INSERT INTO `sn_config` VALUES ('user_birthday_range', '30');
INSERT INTO `sn_config` VALUES ('user_vacation_disable', '0');
INSERT INTO `sn_config` VALUES ('var_db_update', '0');
INSERT INTO `sn_config` VALUES ('var_db_update_end', '0');
INSERT INTO `sn_config` VALUES ('var_news_last', '0');
INSERT INTO `sn_config` VALUES ('var_stat_update', '0');
INSERT INTO `sn_config` VALUES ('var_stat_update_end', '0');
INSERT INTO `sn_config` VALUES ('var_stat_update_msg', '');

-- ----------------------------
-- Administrator's account
-- Login: admin
-- Password: admin
-- ----------------------------
INSERT INTO `sn_users` (`id`, `username`, `password`, `email`, `email_2`, `authlevel`, `id_planet`, `galaxy`, `system`, `planet`, `current_planet`, `register_time`, `onlinetime`, `noipcheck`, `sex`) VALUES (1, 'admin',  '21232f297a57a5a743894a0e4a801fc3', 'root@localhost', 'root@localhost', 3, 1, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()), UNIX_TIMESTAMP(NOW()), 1, 'M');

-- ----------------------------
-- Reserved 'admin' name
-- ----------------------------
INSERT INTO `sn_player_name_history` VALUES ('1', 'admin', '2013-10-13 22:35:51');

-- ----------------------------
-- Administrator's planet
-- ----------------------------
INSERT INTO `sn_planets` (`id`, `name`, `id_owner`, `id_level`, `galaxy`, `system`, `planet`, `planet_type`, `last_update`) VALUES (1, 'Planet', 1, 0, 1, 1, 1, 1, UNIX_TIMESTAMP(NOW()));

