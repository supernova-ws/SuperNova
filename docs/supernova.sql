/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50714
 Source Host           : localhost:3306
 Source Schema         : supernova

 Target Server Type    : MySQL
 Target Server Version : 50714
 File Encoding         : 65001

 Date: 27/07/2020 18:05:32
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sn_account
-- ----------------------------
DROP TABLE IF EXISTS `sn_account`;
CREATE TABLE `sn_account`
(
    `account_id`               bigint(20) UNSIGNED                                       NOT NULL AUTO_INCREMENT,
    `account_name`             varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci    NOT NULL DEFAULT '',
    `account_password`         char(32) CHARACTER SET latin1 COLLATE latin1_general_ci   NOT NULL DEFAULT '',
    `account_salt`             char(16) CHARACTER SET latin1 COLLATE latin1_general_ci   NOT NULL DEFAULT '',
    `account_email`            varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci    NOT NULL DEFAULT '',
    `account_email_verified`   tinyint(1) UNSIGNED                                       NOT NULL DEFAULT 0,
    `account_register_time`    timestamp(0)                                              NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    `account_language`         varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'ru',
    `account_metamatter`       bigint(20)                                                NOT NULL DEFAULT 0 COMMENT 'Metamatter amount',
    `account_metamatter_total` bigint(20)                                                NOT NULL DEFAULT 0 COMMENT 'Total Metamatter amount ever bought',
    `account_immortal`         timestamp(0)                                              NULL     DEFAULT NULL,
    PRIMARY KEY (`account_id`) USING BTREE,
    UNIQUE INDEX `I_account_name` (`account_name`) USING BTREE,
    INDEX `I_account_email` (`account_email`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_account_translate
-- ----------------------------
DROP TABLE IF EXISTS `sn_account_translate`;
CREATE TABLE `sn_account_translate`
(
    `provider_id`         tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Account provider',
    `provider_account_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Account ID on provider',
    `user_id`             bigint(20) UNSIGNED NOT NULL COMMENT 'User ID',
    `timestamp`           timestamp(0)        NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`provider_id`, `provider_account_id`, `user_id`) USING BTREE,
    INDEX `user_id` (`user_id`) USING BTREE,
    CONSTRAINT `FK_account_translate_user_id` FOREIGN KEY (`user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ad_promo_codes
-- ----------------------------
DROP TABLE IF EXISTS `sn_ad_promo_codes`;
CREATE TABLE `sn_ad_promo_codes`
(
    `id`          int(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `code`        varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL COMMENT 'Promo code itself. Unique',
    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Promo code description',
    `reg_only`    tinyint(1)                                              NOT NULL DEFAULT 1,
    `from`        datetime(0)                                             NULL     DEFAULT NULL,
    `to`          datetime(0)                                             NULL     DEFAULT NULL,
    `max_use`     int(10) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT 'Max time code can be used. 0 - unlimited',
    `used_times`  int(10) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT 'How many time code was used',
    `adjustments` mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci   NOT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_promo_code` (`code`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ad_promo_codes_uses
-- ----------------------------
DROP TABLE IF EXISTS `sn_ad_promo_codes_uses`;
CREATE TABLE `sn_ad_promo_codes_uses`
(
    `id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `promo_code_id` int(10) UNSIGNED    NOT NULL,
    `user_id`       bigint(20) UNSIGNED NOT NULL,
    `use_time`      timestamp(0)        NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `FK_user_id` (`user_id`) USING BTREE,
    INDEX `I_promo_code_id` (`promo_code_id`, `user_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_aks
-- ----------------------------
DROP TABLE IF EXISTS `sn_aks`;
CREATE TABLE `sn_aks`
(
    `id`             bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `name`           varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `teilnehmer`     text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `flotten`        text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `ankunft`        int(32)                                                NULL     DEFAULT NULL,
    `galaxy`         int(2)                                                 NULL     DEFAULT NULL,
    `system`         int(4)                                                 NULL     DEFAULT NULL,
    `planet`         int(2)                                                 NULL     DEFAULT NULL,
    `planet_type`    tinyint(1) UNSIGNED                                    NOT NULL DEFAULT 0,
    `eingeladen`     varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `fleet_end_time` int(11)                                                NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_alliance
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance`;
CREATE TABLE `sn_alliance`
(
    `id`                    bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `ally_name`             varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL     DEFAULT '',
    `ally_tag`              varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci   NULL     DEFAULT '',
    `ally_owner`            bigint(20) UNSIGNED                                     NULL     DEFAULT NULL,
    `ally_register_time`    int(11)                                                 NOT NULL DEFAULT 0,
    `ally_description`      text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `ally_web`              varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT '',
    `ally_text`             text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `ally_image`            tinyint(1) UNSIGNED                                     NOT NULL DEFAULT 0,
    `ally_request`          text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `ally_request_waiting`  text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `ally_request_notallow` tinyint(1) UNSIGNED                                     NOT NULL DEFAULT 0,
    `ally_owner_range`      varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL     DEFAULT '',
    `ally_ranks`            text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `ally_members`          int(11)                                                 NOT NULL DEFAULT 0,
    `ranklist`              text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `total_rank`            int(10) UNSIGNED                                        NOT NULL DEFAULT 0,
    `total_points`          bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0,
    `ally_user_id`          bigint(20) UNSIGNED                                     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `i_ally_name` (`ally_name`) USING BTREE,
    UNIQUE INDEX `i_ally_tag` (`ally_tag`) USING BTREE,
    INDEX `I_ally_user_id` (`ally_user_id`) USING BTREE,
    INDEX `FK_alliance_owner` (`ally_owner`) USING BTREE,
    CONSTRAINT `FK_alliance_owner` FOREIGN KEY (`ally_owner`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_ally_ally_user_id` FOREIGN KEY (`ally_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_alliance_diplomacy
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_diplomacy`;
CREATE TABLE `sn_alliance_diplomacy`
(
    `alliance_diplomacy_id`              bigint(20) UNSIGNED                                                                                                            NOT NULL AUTO_INCREMENT,
    `alliance_diplomacy_ally_id`         bigint(20) UNSIGNED                                                                                                            NULL     DEFAULT NULL,
    `alliance_diplomacy_contr_ally_id`   bigint(20) UNSIGNED                                                                                                            NULL     DEFAULT NULL,
    `alliance_diplomacy_contr_ally_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci                                                                         NULL     DEFAULT '',
    `alliance_diplomacy_relation`        set ('neutral','war','peace','confederation','federation','union','master','slave') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'neutral',
    `alliance_diplomacy_relation_last`   set ('neutral','war','peace','confederation','federation','union','master','slave') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'neutral',
    `alliance_diplomacy_time`            int(11)                                                                                                                        NOT NULL DEFAULT 0,
    PRIMARY KEY (`alliance_diplomacy_id`) USING BTREE,
    UNIQUE INDEX `alliance_diplomacy_id` (`alliance_diplomacy_id`) USING BTREE,
    INDEX `alliance_diplomacy_ally_id` (`alliance_diplomacy_ally_id`, `alliance_diplomacy_contr_ally_id`,
                                        `alliance_diplomacy_time`) USING BTREE,
    INDEX `alliance_diplomacy_ally_id_2` (`alliance_diplomacy_ally_id`, `alliance_diplomacy_time`) USING BTREE,
    INDEX `FK_diplomacy_contr_ally_id` (`alliance_diplomacy_contr_ally_id`) USING BTREE,
    INDEX `FK_diplomacy_contr_ally_name` (`alliance_diplomacy_contr_ally_name`) USING BTREE,
    CONSTRAINT `FK_diplomacy_ally_id` FOREIGN KEY (`alliance_diplomacy_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_diplomacy_contr_ally_id` FOREIGN KEY (`alliance_diplomacy_contr_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_diplomacy_contr_ally_name` FOREIGN KEY (`alliance_diplomacy_contr_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_alliance_negotiation
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_negotiation`;
CREATE TABLE `sn_alliance_negotiation`
(
    `alliance_negotiation_id`              bigint(20) UNSIGNED                                                                                                            NOT NULL AUTO_INCREMENT,
    `alliance_negotiation_ally_id`         bigint(20) UNSIGNED                                                                                                            NULL     DEFAULT NULL,
    `alliance_negotiation_ally_name`       varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci                                                                         NULL     DEFAULT '',
    `alliance_negotiation_contr_ally_id`   bigint(20) UNSIGNED                                                                                                            NULL     DEFAULT NULL,
    `alliance_negotiation_contr_ally_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci                                                                         NULL     DEFAULT '',
    `alliance_negotiation_relation`        set ('neutral','war','peace','confederation','federation','union','master','slave') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'neutral',
    `alliance_negotiation_time`            int(11)                                                                                                                        NOT NULL DEFAULT 0,
    `alliance_negotiation_propose`         text CHARACTER SET utf8 COLLATE utf8_general_ci                                                                                NULL,
    `alliance_negotiation_response`        text CHARACTER SET utf8 COLLATE utf8_general_ci                                                                                NULL,
    `alliance_negotiation_status`          tinyint(1)                                                                                                                     NOT NULL DEFAULT 0,
    PRIMARY KEY (`alliance_negotiation_id`) USING BTREE,
    UNIQUE INDEX `alliance_negotiation_id` (`alliance_negotiation_id`) USING BTREE,
    INDEX `alliance_negotiation_ally_id` (`alliance_negotiation_ally_id`, `alliance_negotiation_contr_ally_id`, `alliance_negotiation_time`) USING BTREE,
    INDEX `alliance_negotiation_ally_id_2` (`alliance_negotiation_ally_id`, `alliance_negotiation_time`) USING BTREE,
    INDEX `FK_negotiation_ally_name` (`alliance_negotiation_ally_name`) USING BTREE,
    INDEX `FK_negotiation_contr_ally_id` (`alliance_negotiation_contr_ally_id`) USING BTREE,
    INDEX `FK_negotiation_contr_ally_name` (`alliance_negotiation_contr_ally_name`) USING BTREE,
    CONSTRAINT `FK_negotiation_ally_id` FOREIGN KEY (`alliance_negotiation_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_negotiation_ally_name` FOREIGN KEY (`alliance_negotiation_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_negotiation_contr_ally_id` FOREIGN KEY (`alliance_negotiation_contr_ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_negotiation_contr_ally_name` FOREIGN KEY (`alliance_negotiation_contr_ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_alliance_requests
-- ----------------------------
DROP TABLE IF EXISTS `sn_alliance_requests`;
CREATE TABLE `sn_alliance_requests`
(
    `id_user`        bigint(20) UNSIGNED                             NOT NULL DEFAULT 0,
    `id_ally`        bigint(20) UNSIGNED                             NOT NULL DEFAULT 0,
    `request_text`   text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    `request_time`   int(11)                                         NOT NULL DEFAULT 0,
    `request_denied` tinyint(1) UNSIGNED                             NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_user`, `id_ally`) USING BTREE,
    INDEX `I_alliance_requests_id_ally` (`id_ally`, `id_user`) USING BTREE,
    CONSTRAINT `FK_alliance_request_ally_id` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_alliance_request_user_id` FOREIGN KEY (`id_user`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_annonce
-- ----------------------------
DROP TABLE IF EXISTS `sn_annonce`;
CREATE TABLE `sn_annonce`
(
    `id`       bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `user`     varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `galaxie`  int(11)                                                NOT NULL DEFAULT 0,
    `systeme`  int(11)                                                NOT NULL DEFAULT 0,
    `metala`   bigint(11)                                             NOT NULL DEFAULT 0,
    `cristala` bigint(11)                                             NOT NULL DEFAULT 0,
    `deuta`    bigint(11)                                             NOT NULL DEFAULT 0,
    `metals`   bigint(11)                                             NOT NULL DEFAULT 0,
    `cristals` bigint(11)                                             NOT NULL DEFAULT 0,
    `deuts`    bigint(11)                                             NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `id` (`id`) USING BTREE,
    INDEX `I_annonce_user` (`user`, `id`) USING BTREE,
    CONSTRAINT `FK_annonce_user` FOREIGN KEY (`user`) REFERENCES `sn_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_announce
-- ----------------------------
DROP TABLE IF EXISTS `sn_announce`;
CREATE TABLE `sn_announce`
(
    `idAnnounce`  bigint(11) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `tsTimeStamp` timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Date & Time of announce',
    `strAnnounce` text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `detail_url`  varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Link to more details about update',
    `user_id`     bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'Announcer user ID',
    `user_name`   varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL     DEFAULT NULL COMMENT 'Announcer user name',
    PRIMARY KEY (`idAnnounce`) USING BTREE,
    INDEX `indTimeStamp` (`tsTimeStamp`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_auth_vkontakte_account
-- ----------------------------
DROP TABLE IF EXISTS `sn_auth_vkontakte_account`;
CREATE TABLE `sn_auth_vkontakte_account`
(
    `user_id`      bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `access_token` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `expires_in`   timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    `email`        varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `first_name`   varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `last_name`    varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `account_id`   bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Account ID',
    PRIMARY KEY (`user_id`) USING BTREE,
    INDEX `FK_vkontakte_account_id` (`account_id`) USING BTREE,
    CONSTRAINT `FK_vkontakte_account_id` FOREIGN KEY (`account_id`) REFERENCES `sn_account` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_banned
-- ----------------------------
DROP TABLE IF EXISTS `sn_banned`;
CREATE TABLE `sn_banned`
(
    `ban_id`           bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `ban_user_id`      bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Banned user ID',
    `ban_user_name`    varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '',
    `ban_reason`       varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `ban_time`         int(11)                                                 NOT NULL DEFAULT 0,
    `ban_until`        int(11)                                                 NOT NULL DEFAULT 0,
    `ban_issuer_id`    bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Banner ID',
    `ban_issuer_name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '',
    `ban_issuer_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '',
    PRIMARY KEY (`ban_id`) USING BTREE,
    INDEX `ID` (`ban_id`) USING BTREE,
    INDEX `I_ban_user_id` (`ban_user_id`) USING BTREE,
    INDEX `I_ban_issuer_id` (`ban_issuer_id`) USING BTREE,
    CONSTRAINT `FK_ban_issuer_id` FOREIGN KEY (`ban_issuer_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_ban_user_id` FOREIGN KEY (`ban_user_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_bashing
-- ----------------------------
DROP TABLE IF EXISTS `sn_bashing`;
CREATE TABLE `sn_bashing`
(
    `bashing_id`        bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `bashing_user_id`   bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `bashing_planet_id` bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `bashing_time`      int(11)             NOT NULL DEFAULT 0,
    PRIMARY KEY (`bashing_id`) USING BTREE,
    UNIQUE INDEX `bashing_id` (`bashing_id`) USING BTREE,
    INDEX `bashing_user_id` (`bashing_user_id`, `bashing_planet_id`, `bashing_time`) USING BTREE,
    INDEX `bashing_planet_id` (`bashing_planet_id`) USING BTREE,
    INDEX `bashing_time` (`bashing_time`) USING BTREE,
    CONSTRAINT `FK_bashing_planet_id` FOREIGN KEY (`bashing_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_bashing_user_id` FOREIGN KEY (`bashing_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_blitz_registrations
-- ----------------------------
DROP TABLE IF EXISTS `sn_blitz_registrations`;
CREATE TABLE `sn_blitz_registrations`
(
    `id`                       bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `server_id`                smallint(5) UNSIGNED                                   NULL     DEFAULT 0,
    `round_number`             smallint(5) UNSIGNED                                   NULL     DEFAULT 0,
    `user_id`                  bigint(20) UNSIGNED                                    NULL     DEFAULT NULL,
    `timestamp`                timestamp(0)                                           NULL     DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
    `blitz_name`               varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `blitz_password`           varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci  NOT NULL DEFAULT '',
    `blitz_player_id`          bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0,
    `blitz_status`             tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 0,
    `blitz_place`              tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 0,
    `blitz_points`             decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0,
    `blitz_online`             int(10) UNSIGNED                                       NOT NULL DEFAULT 0,
    `blitz_reward_dark_matter` bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_blitz_server_round_user` (`server_id`, `round_number`, `user_id`) USING BTREE,
    INDEX `I_blitz_user_id` (`user_id`) USING BTREE,
    CONSTRAINT `FK_user_id` FOREIGN KEY (`user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_blitz_statpoints
-- ----------------------------
DROP TABLE IF EXISTS `sn_blitz_statpoints`;
CREATE TABLE `sn_blitz_statpoints`
(
    `stat_date`      int(11)                 NOT NULL DEFAULT 0,
    `id_owner`       bigint(20) UNSIGNED     NULL     DEFAULT NULL,
    `id_ally`        bigint(20) UNSIGNED     NULL     DEFAULT NULL,
    `stat_type`      tinyint(3) UNSIGNED     NULL     DEFAULT 0,
    `stat_code`      tinyint(3) UNSIGNED     NOT NULL DEFAULT 0,
    `tech_rank`      int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `tech_old_rank`  int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `tech_points`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `tech_count`     decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `build_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `build_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `build_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `build_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `defs_rank`      int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `defs_old_rank`  int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `defs_points`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `defs_count`     decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `fleet_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `fleet_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `fleet_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `fleet_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `res_rank`       int(11) UNSIGNED        NULL     DEFAULT 0 COMMENT 'Rank by resources',
    `res_old_rank`   int(11) UNSIGNED        NULL     DEFAULT 0 COMMENT 'Old rank by resources',
    `res_points`     decimal(65, 0) UNSIGNED NULL     DEFAULT 0 COMMENT 'Resource stat points',
    `res_count`      decimal(65, 0) UNSIGNED NULL     DEFAULT 0 COMMENT 'Resource count',
    `total_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `total_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `total_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `total_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `server_id`      smallint(5) UNSIGNED    NULL     DEFAULT 0,
    `round_number`   smallint(5) UNSIGNED    NULL     DEFAULT 0
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_buddy
-- ----------------------------
DROP TABLE IF EXISTS `sn_buddy`;
CREATE TABLE `sn_buddy`
(
    `BUDDY_ID`        bigint(20) UNSIGNED                                 NOT NULL AUTO_INCREMENT COMMENT 'Buddy table row ID',
    `BUDDY_SENDER_ID` bigint(20) UNSIGNED                                 NULL     DEFAULT NULL COMMENT 'Buddy request sender ID',
    `BUDDY_OWNER_ID`  bigint(20) UNSIGNED                                 NULL     DEFAULT NULL COMMENT 'Buddy request recipient ID',
    `BUDDY_STATUS`    tinyint(1) UNSIGNED                                 NOT NULL DEFAULT 0 COMMENT 'Buddy request status',
    `BUDDY_REQUEST`   tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Buddy request text',
    PRIMARY KEY (`BUDDY_ID`) USING BTREE,
    UNIQUE INDEX `BUDDY_ID` (`BUDDY_ID`) USING BTREE,
    INDEX `I_BUDDY_SENDER_ID` (`BUDDY_SENDER_ID`, `BUDDY_OWNER_ID`) USING BTREE,
    INDEX `I_BUDDY_OWNER_ID` (`BUDDY_OWNER_ID`, `BUDDY_SENDER_ID`) USING BTREE,
    CONSTRAINT `FK_BUDDY_OWNER_ID` FOREIGN KEY (`BUDDY_OWNER_ID`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_BUDDY_SENDER_ID` FOREIGN KEY (`BUDDY_SENDER_ID`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_captain
-- ----------------------------
DROP TABLE IF EXISTS `sn_captain`;
CREATE TABLE `sn_captain`
(
    `captain_id`      bigint(20) UNSIGNED     NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
    `captain_unit_id` bigint(20) UNSIGNED     NULL     DEFAULT NULL COMMENT 'Link to `unit` record',
    `captain_xp`      decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Captain expirience',
    `captain_level`   bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Captain level so far',
    `captain_shield`  bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Captain shield bonus level',
    `captain_armor`   bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Captain armor bonus level',
    `captain_attack`  bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Captain defense bonus level',
    PRIMARY KEY (`captain_id`) USING BTREE,
    UNIQUE INDEX `captain_id` (`captain_id`) USING BTREE,
    INDEX `I_captain_unit_id` (`captain_unit_id`) USING BTREE,
    CONSTRAINT `FK_captain_unit_id` FOREIGN KEY (`captain_unit_id`) REFERENCES `sn_unit` (`unit_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_chat
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat`;
CREATE TABLE `sn_chat`
(
    `messageid`                   bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `chat_message_sender_id`      bigint(20) UNSIGNED                                    NULL     DEFAULT NULL COMMENT 'Message sender ID',
    `chat_message_sender_name`    varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT '' COMMENT 'Message sender name',
    `user`                        text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL COMMENT 'Chat message user name',
    `chat_message_recipient_id`   bigint(20) UNSIGNED                                    NULL     DEFAULT NULL COMMENT 'Message recipient ID',
    `chat_message_recipient_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT '' COMMENT 'Message sender name',
    `message`                     text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `timestamp`                   int(11)                                                NOT NULL DEFAULT 0,
    `ally_id`                     int(11)                                                NOT NULL DEFAULT 0,
    PRIMARY KEY (`messageid`) USING BTREE,
    UNIQUE INDEX `messageid` (`messageid`) USING BTREE,
    INDEX `i_ally_idmess` (`ally_id`, `messageid`) USING BTREE,
    INDEX `I_chat_message_sender_id` (`chat_message_sender_id`) USING BTREE,
    INDEX `I_chat_message_recipient_id` (`chat_message_recipient_id`) USING BTREE,
    CONSTRAINT `FK_chat_message_sender_recipient_id` FOREIGN KEY (`chat_message_recipient_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_chat_message_sender_user_id` FOREIGN KEY (`chat_message_sender_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_chat_player
-- ----------------------------
DROP TABLE IF EXISTS `sn_chat_player`;
CREATE TABLE `sn_chat_player`
(
    `chat_player_id`           bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
    `chat_player_player_id`    bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Chat player record owner',
    `chat_player_activity`     timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'Last player activity in chat',
    `chat_player_invisible`    tinyint(4)                                              NOT NULL DEFAULT 0 COMMENT 'Player invisibility',
    `chat_player_muted`        int(11)                                                 NOT NULL DEFAULT 0 COMMENT 'Player is muted',
    `chat_player_mute_reason`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Player mute reason',
    `chat_player_refresh_last` int(11)                                                 NOT NULL DEFAULT 0 COMMENT 'Player last refresh time',
    PRIMARY KEY (`chat_player_id`) USING BTREE,
    UNIQUE INDEX `chat_player_id` (`chat_player_id`) USING BTREE,
    INDEX `I_chat_player_id` (`chat_player_player_id`) USING BTREE,
    INDEX `I_chat_player_refresh_last` (`chat_player_refresh_last`) USING BTREE,
    CONSTRAINT `FK_chat_player_id` FOREIGN KEY (`chat_player_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_config
-- ----------------------------
DROP TABLE IF EXISTS `sn_config`;
CREATE TABLE `sn_config`
(
    `config_name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `config_value` text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    PRIMARY KEY (`config_name`) USING BTREE,
    INDEX `i_config_name` (`config_name`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_confirmations
-- ----------------------------
DROP TABLE IF EXISTS `sn_confirmations`;
CREATE TABLE `sn_confirmations`
(
    `id`          bigint(11)                                             NOT NULL AUTO_INCREMENT,
    `id_user`     bigint(11)                                             NOT NULL DEFAULT 0,
    `type`        smallint(6)                                            NOT NULL DEFAULT 0,
    `code`        varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `email`       varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `create_time` timestamp(0)                                           NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
    `provider_id` tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 0,
    `account_id`  bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_confirmations_unique` (`provider_id`, `account_id`, `type`, `email`) USING BTREE,
    INDEX `i_code_email` (`code`, `email`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_counter
-- ----------------------------
DROP TABLE IF EXISTS `sn_counter`;
CREATE TABLE `sn_counter`
(
    `counter_id`      bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `visit_time`      timestamp(0)        NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    `visit_length`    int(10) UNSIGNED    NOT NULL DEFAULT 0,
    `hits`            int(10) UNSIGNED    NOT NULL DEFAULT 1,
    `user_id`         bigint(20) UNSIGNED NULL     DEFAULT 0,
    `player_entry_id` bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `page_url_id`     int(10) UNSIGNED    NULL     DEFAULT NULL,
    `query_string_id` bigint(20) UNSIGNED NULL     DEFAULT NULL,
    PRIMARY KEY (`counter_id`) USING BTREE,
    UNIQUE INDEX `counter_id` (`counter_id`) USING BTREE,
    INDEX `I_counter_page_url_id` (`page_url_id`) USING BTREE,
    INDEX `I_counter_visit_time` (`visit_time`, `counter_id`) USING BTREE,
    INDEX `I_counter_query_string_id` (`query_string_id`) USING BTREE,
    INDEX `I_counter_player_entry_id` (`player_entry_id`, `user_id`) USING BTREE,
    INDEX `I_counter_user_id` (`user_id`, `player_entry_id`) USING BTREE,
    CONSTRAINT `FK_counter_page_url_id` FOREIGN KEY (`page_url_id`) REFERENCES `sn_security_url` (`url_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival`;
CREATE TABLE `sn_festival`
(
    `id`     smallint(5) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `start`  datetime(0)                                             NOT NULL COMMENT 'Festival start datetime',
    `finish` datetime(0)                                             NOT NULL COMMENT 'Festival end datetime',
    `name`   varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Название акции/ивента',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_festival_date_range` (`start`, `finish`, `id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_gifts
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_gifts`;
CREATE TABLE `sn_festival_gifts`
(
    `id`          bigint(20) UNSIGNED                                      NOT NULL AUTO_INCREMENT,
    `highspot_id` int(10) UNSIGNED                                         NULL     DEFAULT NULL,
    `from`        bigint(20) UNSIGNED                                      NULL     DEFAULT NULL,
    `to`          bigint(20) UNSIGNED                                      NULL     DEFAULT NULL,
    `amount`      bigint(20) UNSIGNED                                      NOT NULL,
    `disclosure`  tinyint(1) UNSIGNED                                      NOT NULL DEFAULT 0,
    `message`     varchar(4096) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_highspot_id` (`highspot_id`, `from`, `to`) USING BTREE,
    INDEX `I_to_from` (`highspot_id`, `to`, `from`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_highspot
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_highspot`;
CREATE TABLE `sn_festival_highspot`
(
    `id`          int(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `festival_id` smallint(5) UNSIGNED                                    NULL     DEFAULT NULL,
    `class`       tinyint(3) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'Highspot class',
    `start`       datetime(0)                                             NOT NULL COMMENT 'Highspot start datetime',
    `finish`      datetime(0)                                             NOT NULL COMMENT 'Highspot end datetime',
    `name`        varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `params`      text CHARACTER SET utf8 COLLATE utf8_unicode_ci         NOT NULL COMMENT 'Параметры хайспота в виде JSON-encoded',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_highspot_order` (`start`, `finish`, `id`) USING BTREE,
    INDEX `I_highspot_festival_id` (`festival_id`, `start`, `finish`, `id`) USING BTREE,
    CONSTRAINT `FK_highspot_festival_id` FOREIGN KEY (`festival_id`) REFERENCES `sn_festival` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_highspot_activity
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_highspot_activity`;
CREATE TABLE `sn_festival_highspot_activity`
(
    `id`          int(10) UNSIGNED                                NOT NULL AUTO_INCREMENT,
    `highspot_id` int(10) UNSIGNED                                NULL     DEFAULT NULL,
    `class`       smallint(5) UNSIGNED                            NOT NULL DEFAULT 0 COMMENT 'Класс события - ID модуля события',
    `type`        tinyint(1) UNSIGNED                             NOT NULL DEFAULT 0 COMMENT 'Тип активити: 1 - триггер, 2 - хук',
    `start`       datetime(0)                                     NOT NULL COMMENT 'Запланированное время запуска',
    `finish`      datetime(0)                                     NULL     DEFAULT NULL COMMENT 'Реальное время запуска',
    `params`      text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Параметры активити в виде сериализованного архива',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_festival_activity_order` (`start`, `finish`, `id`) USING BTREE,
    INDEX `I_festival_activity_highspot_id` (`highspot_id`, `start`, `finish`, `id`) USING BTREE,
    CONSTRAINT `FK_festival_activity_highspot_id` FOREIGN KEY (`highspot_id`) REFERENCES `sn_festival_highspot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_ois_player
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_ois_player`;
CREATE TABLE `sn_festival_ois_player`
(
    `highspot_id` int(10) UNSIGNED    NOT NULL COMMENT 'Highspot ID',
    `player_id`   bigint(20) UNSIGNED NOT NULL COMMENT 'Player ID',
    `ois_count`   int(10) UNSIGNED    NULL DEFAULT NULL COMMENT 'OiS player controlled last tick',
    PRIMARY KEY (`highspot_id`, `player_id`) USING BTREE,
    INDEX `I_player_highspot` (`player_id`, `highspot_id`) USING BTREE,
    CONSTRAINT `FK_ois_highspot` FOREIGN KEY (`highspot_id`) REFERENCES `sn_festival_highspot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ois_player` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_unit
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_unit`;
CREATE TABLE `sn_festival_unit`
(
    `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `highspot_id` int(10) UNSIGNED    NULL     DEFAULT NULL,
    `player_id`   bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `unit_id`     bigint(20)          NOT NULL DEFAULT 0,
    `unit_level`  bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_festival_unit_player_id` (`player_id`, `highspot_id`) USING BTREE,
    INDEX `I_festival_unit_highspot_id` (`highspot_id`, `unit_id`, `player_id`) USING BTREE,
    CONSTRAINT `FK_festival_unit_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `sn_festival_highspot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_festival_unit_player` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_festival_unit_log
-- ----------------------------
DROP TABLE IF EXISTS `sn_festival_unit_log`;
CREATE TABLE `sn_festival_unit_log`
(
    `id`          bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `highspot_id` int(10) UNSIGNED                                        NULL     DEFAULT NULL,
    `player_id`   bigint(20) UNSIGNED                                     NOT NULL COMMENT 'User ID',
    `player_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '',
    `unit_id`     bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0,
    `timestamp`   timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    `unit_level`  int(11)                                                 NOT NULL DEFAULT 0,
    `unit_image`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_festival_unit_log_player_id` (`player_id`, `highspot_id`, `id`) USING BTREE,
    INDEX `I_festival_unit_log_highspot_id` (`highspot_id`, `unit_id`, `player_id`) USING BTREE,
    CONSTRAINT `FK_festival_unit_log_hispot` FOREIGN KEY (`highspot_id`) REFERENCES `sn_festival_highspot` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_festival_unit_log_player` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_fleets
-- ----------------------------
DROP TABLE IF EXISTS `sn_fleets`;
CREATE TABLE `sn_fleets`
(
    `fleet_id`                 bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `fleet_owner`              bigint(20) UNSIGNED                                    NULL     DEFAULT NULL,
    `fleet_mission`            int(11)                                                NOT NULL DEFAULT 0,
    `fleet_amount`             bigint(11)                                             NOT NULL DEFAULT 0,
    `fleet_array`              text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `fleet_start_time`         int(11)                                                NOT NULL DEFAULT 0,
    `fleet_start_planet_id`    bigint(20) UNSIGNED                                    NULL     DEFAULT NULL COMMENT 'Fleet start planet ID',
    `fleet_start_galaxy`       int(11)                                                NOT NULL DEFAULT 0,
    `fleet_start_system`       int(11)                                                NOT NULL DEFAULT 0,
    `fleet_start_planet`       int(11)                                                NOT NULL DEFAULT 0,
    `fleet_start_type`         int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_time`           int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_stay`           int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_planet_id`      bigint(20) UNSIGNED                                    NULL     DEFAULT NULL COMMENT 'Fleet end planet ID',
    `fleet_end_galaxy`         int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_system`         int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_planet`         int(11)                                                NOT NULL DEFAULT 0,
    `fleet_end_type`           int(11)                                                NOT NULL DEFAULT 0,
    `fleet_resource_metal`     decimal(65, 0)                                         NULL     DEFAULT 0,
    `fleet_resource_crystal`   decimal(65, 0)                                         NULL     DEFAULT 0,
    `fleet_resource_deuterium` decimal(65, 0)                                         NULL     DEFAULT 0,
    `fleet_target_owner`       int(11)                                                NOT NULL DEFAULT 0,
    `fleet_group`              varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
    `fleet_mess`               int(11)                                                NOT NULL DEFAULT 0,
    `start_time`               int(11)                                                NULL     DEFAULT 0,
    PRIMARY KEY (`fleet_id`) USING BTREE,
    UNIQUE INDEX `fleet_id` (`fleet_id`) USING BTREE,
    INDEX `fleet_origin` (`fleet_start_galaxy`, `fleet_start_system`, `fleet_start_planet`) USING BTREE,
    INDEX `fleet_dest` (`fleet_end_galaxy`, `fleet_end_system`, `fleet_end_planet`) USING BTREE,
    INDEX `fleet_start_time` (`fleet_start_time`) USING BTREE,
    INDEX `fllet_end_time` (`fleet_end_time`) USING BTREE,
    INDEX `fleet_owner` (`fleet_owner`) USING BTREE,
    INDEX `i_fl_targ_owner` (`fleet_target_owner`) USING BTREE,
    INDEX `fleet_both` (`fleet_start_galaxy`, `fleet_start_system`, `fleet_start_planet`, `fleet_start_type`,
                        `fleet_end_galaxy`, `fleet_end_system`, `fleet_end_planet`) USING BTREE,
    INDEX `fleet_mess` (`fleet_mess`) USING BTREE,
    INDEX `fleet_group` (`fleet_group`) USING BTREE,
    INDEX `I_fleet_start_planet_id` (`fleet_start_planet_id`) USING BTREE,
    INDEX `I_fleet_end_planet_id` (`fleet_end_planet_id`) USING BTREE,
    CONSTRAINT `FK_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_fleet_planet_end` FOREIGN KEY (`fleet_end_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_fleet_planet_start` FOREIGN KEY (`fleet_start_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_iraks
-- ----------------------------
DROP TABLE IF EXISTS `sn_iraks`;
CREATE TABLE `sn_iraks`
(
    `id`                 bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `fleet_end_time`     int(11) UNSIGNED    NOT NULL DEFAULT 0,
    `fleet_end_galaxy`   int(2) UNSIGNED     NULL     DEFAULT 0,
    `fleet_end_system`   int(4) UNSIGNED     NULL     DEFAULT 0,
    `fleet_end_planet`   int(2) UNSIGNED     NULL     DEFAULT 0,
    `fleet_start_galaxy` int(2) UNSIGNED     NULL     DEFAULT 0,
    `fleet_start_system` int(4) UNSIGNED     NULL     DEFAULT 0,
    `fleet_start_planet` int(2) UNSIGNED     NULL     DEFAULT 0,
    `fleet_owner`        bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `fleet_target_owner` bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `fleet_amount`       bigint(20) UNSIGNED NULL     DEFAULT 0,
    `primaer`            int(32)             NULL     DEFAULT NULL,
    `fleet_start_type`   smallint(6)         NOT NULL DEFAULT 1,
    `fleet_end_type`     smallint(6)         NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_iraks_fleet_owner` (`fleet_owner`) USING BTREE,
    INDEX `I_iraks_fleet_target_owner` (`fleet_target_owner`) USING BTREE,
    CONSTRAINT `FK_iraks_fleet_owner` FOREIGN KEY (`fleet_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_iraks_fleet_target_owner` FOREIGN KEY (`fleet_target_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_lng_usage_stat
-- ----------------------------
DROP TABLE IF EXISTS `sn_lng_usage_stat`;
CREATE TABLE `sn_lng_usage_stat`
(
    `lang_code` char(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci      NOT NULL,
    `string_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `file`      varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
    `line`      smallint(6)                                             NOT NULL,
    `is_empty`  tinyint(1)                                              NOT NULL,
    `locale`    mediumtext CHARACTER SET utf8 COLLATE utf8_unicode_ci   NULL,
    PRIMARY KEY (`lang_code`, `string_id`, `file`, `line`, `is_empty`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_log_dark_matter
-- ----------------------------
DROP TABLE IF EXISTS `sn_log_dark_matter`;
CREATE TABLE `sn_log_dark_matter`
(
    `log_dark_matter_id`        bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `log_dark_matter_timestamp` timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Human-readable record timestamp',
    `log_dark_matter_username`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '' COMMENT 'Username',
    `log_dark_matter_reason`    int(10) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT 'Reason ID for dark matter adjustment',
    `log_dark_matter_amount`    int(10)                                                 NOT NULL DEFAULT 0 COMMENT 'Amount of dark matter change',
    `log_dark_matter_comment`   text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL COMMENT 'Comments',
    `log_dark_matter_page`      varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
    `log_dark_matter_sender`    bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'User ID which make log record',
    PRIMARY KEY (`log_dark_matter_id`) USING BTREE,
    UNIQUE INDEX `log_dark_matter_id` (`log_dark_matter_id`) USING BTREE,
    INDEX `i_log_dark_matter_sender_id` (`log_dark_matter_sender`, `log_dark_matter_id`) USING BTREE,
    INDEX `i_log_dark_matter_reason_sender_id` (`log_dark_matter_reason`, `log_dark_matter_sender`,
                                                `log_dark_matter_id`) USING BTREE,
    INDEX `i_log_dark_matter_amount` (`log_dark_matter_amount`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_log_halloween_2015
-- ----------------------------
DROP TABLE IF EXISTS `sn_log_halloween_2015`;
CREATE TABLE `sn_log_halloween_2015`
(
    `log_hw2015_id` bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `player_id`     bigint(20) UNSIGNED                                    NOT NULL COMMENT 'User ID',
    `player_name`   varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `unit_snid`     bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0,
    `timestamp`     timestamp(0)                                           NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`log_hw2015_id`) USING BTREE,
    INDEX `player_id` (`player_id`, `log_hw2015_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_log_metamatter
-- ----------------------------
DROP TABLE IF EXISTS `sn_log_metamatter`;
CREATE TABLE `sn_log_metamatter`
(
    `id`           bigint(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `timestamp`    timestamp(0)                                                NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Human-readable record timestamp',
    `user_id`      bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0 COMMENT 'User ID which make log record',
    `username`     varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '' COMMENT 'Username',
    `reason`       int(10) UNSIGNED                                            NOT NULL DEFAULT 0 COMMENT 'Reason ID for metamatter adjustment',
    `amount`       bigint(10)                                                  NOT NULL DEFAULT 0 COMMENT 'Amount of metamatter change',
    `comment`      text CHARACTER SET utf8 COLLATE utf8_general_ci             NULL COMMENT 'Comments',
    `page`         varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci     NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
    `provider_id`  tinyint(3) UNSIGNED                                         NOT NULL DEFAULT 1 COMMENT 'Account provider',
    `account_id`   bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `account_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '',
    `server_name`  varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'http://localhost/supernova/',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `id` (`id`) USING BTREE,
    INDEX `I_log_metamatter_sender_id` (`user_id`, `id`) USING BTREE,
    INDEX `I_log_metamatter_reason_sender_id` (`reason`, `user_id`, `id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_log_users_online
-- ----------------------------
DROP TABLE IF EXISTS `sn_log_users_online`;
CREATE TABLE `sn_log_users_online`
(
    `online_timestamp`  timestamp(0)         NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Measure time',
    `online_count`      smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Users online',
    `online_aggregated` tinyint(1) UNSIGNED  NOT NULL DEFAULT 0,
    PRIMARY KEY (`online_timestamp`, `online_aggregated`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_logs
-- ----------------------------
DROP TABLE IF EXISTS `sn_logs`;
CREATE TABLE `sn_logs`
(
    `log_timestamp` timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Human-readable record timestamp',
    `log_username`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '' COMMENT 'Username',
    `log_title`     varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT 'Log entry' COMMENT 'Short description',
    `log_text`      text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `log_page`      varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Page that makes entry to log',
    `log_code`      int(10) UNSIGNED                                        NOT NULL DEFAULT 0,
    `log_sender`    bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'User ID which make log record',
    `log_time`      int(11) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT 'Machine-readable timestamp',
    `log_dump`      mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci   NOT NULL COMMENT 'Machine-readable dump of variables',
    `log_id`        bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`log_id`) USING BTREE,
    UNIQUE INDEX `log_id` (`log_id`) USING BTREE,
    INDEX `i_log_username` (`log_username`) USING BTREE,
    INDEX `i_log_time` (`log_time`) USING BTREE,
    INDEX `i_log_sender` (`log_sender`) USING BTREE,
    INDEX `i_log_code` (`log_code`) USING BTREE,
    INDEX `i_log_page` (`log_page`(255)) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_messages
-- ----------------------------
DROP TABLE IF EXISTS `sn_messages`;
CREATE TABLE `sn_messages`
(
    `message_id`      bigint(11)                                             NOT NULL AUTO_INCREMENT,
    `message_owner`   int(11)                                                NOT NULL DEFAULT 0,
    `message_sender`  int(11)                                                NOT NULL DEFAULT 0,
    `message_time`    int(11)                                                NOT NULL DEFAULT 0,
    `message_type`    int(11)                                                NOT NULL DEFAULT 0,
    `message_from`    varchar(48) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `message_subject` varchar(48) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `message_text`    text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `message_json`    tinyint(1) UNSIGNED                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`message_id`) USING BTREE,
    INDEX `i_owner_time` (`message_owner`, `message_time`) USING BTREE,
    INDEX `i_sender_time` (`message_sender`, `message_time`) USING BTREE,
    INDEX `i_time` (`message_time`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_notes
-- ----------------------------
DROP TABLE IF EXISTS `sn_notes`;
CREATE TABLE `sn_notes`
(
    `id`          bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `owner`       bigint(20) UNSIGNED                                    NULL     DEFAULT NULL,
    `time`        int(11)                                                NULL     DEFAULT NULL,
    `priority`    tinyint(1)                                             NULL     DEFAULT NULL,
    `title`       varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `galaxy`      smallint(6) UNSIGNED                                   NOT NULL DEFAULT 0,
    `system`      smallint(6) UNSIGNED                                   NOT NULL DEFAULT 0,
    `planet`      smallint(6) UNSIGNED                                   NOT NULL DEFAULT 0,
    `planet_type` tinyint(4) UNSIGNED                                    NOT NULL DEFAULT 1,
    `text`        text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL,
    `sticky`      tinyint(1) UNSIGNED                                    NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `id` (`id`) USING BTREE,
    INDEX `I_notes_owner` (`owner`) USING BTREE,
    INDEX `I_owner_priority_time` (`owner`, `priority`, `time`) USING BTREE,
    CONSTRAINT `FK_notes_owner` FOREIGN KEY (`owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_payment
-- ----------------------------
DROP TABLE IF EXISTS `sn_payment`;
CREATE TABLE `sn_payment`
(
    `payment_id`                 bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT COMMENT 'Internal payment ID',
    `payment_status`             int(11)                                                NULL     DEFAULT 0 COMMENT 'Payment status',
    `payment_user_id`            bigint(20) UNSIGNED                                    NULL     DEFAULT NULL,
    `payment_user_name`          varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `payment_amount`             decimal(60, 5)                                         NULL     DEFAULT 0.00000 COMMENT 'Amount paid',
    `payment_currency`           varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci  NULL     DEFAULT '' COMMENT 'Payment currency',
    `payment_dark_matter_paid`   decimal(65, 0)                                         NULL     DEFAULT 0 COMMENT 'Real DM paid for',
    `payment_dark_matter_gained` decimal(65, 0)                                         NULL     DEFAULT 0 COMMENT 'DM gained by player (with bonuses)',
    `payment_date`               timestamp(0)                                           NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Payment server timestamp',
    `payment_comment`            text CHARACTER SET utf8 COLLATE utf8_general_ci        NULL COMMENT 'Payment comment',
    `payment_module_name`        varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT '' COMMENT 'Payment module name',
    `payment_method_id`          smallint(6)                                            NULL     DEFAULT NULL,
    `payment_external_id`        varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT '' COMMENT 'External payment ID in payment system',
    `payment_external_date`      datetime(0)                                            NULL     DEFAULT NULL COMMENT 'External payment timestamp in payment system',
    `payment_external_lots`      decimal(65, 5)                                         NOT NULL DEFAULT 0.00000 COMMENT 'Payment system lot amount',
    `payment_external_amount`    decimal(65, 5)                                         NOT NULL DEFAULT 0.00000 COMMENT 'Money incoming from payment system',
    `payment_external_currency`  varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci  NOT NULL DEFAULT '' COMMENT 'Payment system currency',
    `payment_test`               tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Is this a test payment?',
    `payment_provider_id`        tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 1 COMMENT 'Payment account provider',
    `payment_account_id`         bigint(20) UNSIGNED                                    NOT NULL,
    `payment_account_name`       varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`payment_id`) USING BTREE,
    INDEX `I_payment_user` (`payment_user_id`, `payment_user_name`) USING BTREE,
    INDEX `I_payment_module_internal_id` (`payment_module_name`, `payment_external_id`) USING BTREE,
    INDEX `I_payment_method_id` (`payment_method_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_planets
-- ----------------------------
DROP TABLE IF EXISTS `sn_planets`;
CREATE TABLE `sn_planets`
(
    `id`                           bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT,
    `name`                         varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Planet',
    `id_owner`                     bigint(20) UNSIGNED                                    NULL     DEFAULT NULL,
    `galaxy`                       smallint(6)                                            NOT NULL DEFAULT 0,
    `system`                       smallint(6)                                            NOT NULL DEFAULT 0,
    `planet`                       smallint(6)                                            NOT NULL DEFAULT 0,
    `planet_type`                  tinyint(4)                                             NOT NULL DEFAULT 1,
    `metal`                        decimal(65, 5)                                         NOT NULL DEFAULT 0.00000,
    `crystal`                      decimal(65, 5)                                         NOT NULL DEFAULT 0.00000,
    `deuterium`                    decimal(65, 5)                                         NOT NULL DEFAULT 0.00000,
    `energy_max`                   decimal(65, 0)                                         NOT NULL DEFAULT 0,
    `energy_used`                  decimal(65, 0)                                         NOT NULL DEFAULT 0,
    `last_jump_time`               int(11)                                                NOT NULL DEFAULT 0,
    `metal_perhour`                int(11)                                                NOT NULL DEFAULT 0,
    `crystal_perhour`              int(11)                                                NOT NULL DEFAULT 0,
    `deuterium_perhour`            int(11)                                                NOT NULL DEFAULT 0,
    `metal_mine_porcent`           tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `crystal_mine_porcent`         tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `deuterium_sintetizer_porcent` tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `solar_plant_porcent`          tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `fusion_plant_porcent`         tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `solar_satelit_porcent`        tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10,
    `last_update`                  int(11)                                                NULL     DEFAULT NULL,
    `que_processed`                int(11) UNSIGNED                                       NOT NULL DEFAULT 0,
    `image`                        varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normaltempplanet01',
    `points`                       bigint(20)                                             NULL     DEFAULT 0,
    `ranks`                        bigint(20)                                             NULL     DEFAULT 0,
    `id_level`                     tinyint(4)                                             NOT NULL DEFAULT 0,
    `destruyed`                    int(11)                                                NOT NULL DEFAULT 0,
    `diameter`                     int(11)                                                NOT NULL DEFAULT 12800,
    `field_max`                    smallint(5) UNSIGNED                                   NOT NULL DEFAULT 163,
    `field_current`                smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0,
    `temp_min`                     smallint(6)                                            NOT NULL DEFAULT 0,
    `temp_max`                     smallint(6)                                            NOT NULL DEFAULT 40,
    `metal_max`                    decimal(65, 0)                                         NULL     DEFAULT 100000,
    `crystal_max`                  decimal(65, 0)                                         NULL     DEFAULT 100000,
    `deuterium_max`                decimal(65, 0)                                         NULL     DEFAULT 100000,
    `parent_planet`                bigint(20) UNSIGNED                                    NULL     DEFAULT 0,
    `debris_metal`                 bigint(20) UNSIGNED                                    NULL     DEFAULT 0,
    `debris_crystal`               bigint(20) UNSIGNED                                    NULL     DEFAULT 0,
    `PLANET_GOVERNOR_ID`           smallint(6)                                            NOT NULL DEFAULT 0,
    `PLANET_GOVERNOR_LEVEL`        smallint(6)                                            NOT NULL DEFAULT 0,
    `planet_teleport_next`         int(11)                                                NOT NULL DEFAULT 0 COMMENT 'Next teleport time',
    `ship_sattelite_sloth_porcent` tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 10 COMMENT 'Terran Sloth production',
    `density`                      smallint(6)                                            NOT NULL DEFAULT 5500 COMMENT 'Planet average density kg/m3',
    `density_index`                tinyint(4)                                             NOT NULL DEFAULT 4 COMMENT 'Planet cached density index',
    `position_original`            smallint(6)                                            NOT NULL DEFAULT 0,
    `field_max_original`           smallint(6)                                            NOT NULL DEFAULT 0,
    `temp_min_original`            smallint(6)                                            NOT NULL DEFAULT 0,
    `temp_max_original`            smallint(6)                                            NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `id` (`id`) USING BTREE,
    INDEX `owner_type` (`id_owner`, `planet_type`) USING BTREE,
    INDEX `id_level` (`id_level`) USING BTREE,
    INDEX `i_last_update` (`last_update`) USING BTREE,
    INDEX `GSPT` (`galaxy`, `system`, `planet`, `planet_type`) USING BTREE,
    INDEX `i_parent_planet` (`parent_planet`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_player_award
-- ----------------------------
DROP TABLE IF EXISTS `sn_player_award`;
CREATE TABLE `sn_player_award`
(
    `id`               bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `award_type_id`    int(11)             NULL     DEFAULT NULL COMMENT 'Award type i.e. order, medal, pennant, rank etc',
    `award_id`         int(11)             NULL     DEFAULT NULL COMMENT 'Global award unit ID',
    `award_variant_id` int(11)             NULL     DEFAULT NULL COMMENT 'Multiply award subtype i.e. for same reward awarded early',
    `player_id`        bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `awarded`          timestamp(0)        NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'When was awarded',
    `active_from`      datetime(0)         NULL     DEFAULT NULL,
    `active_to`        datetime(0)         NULL     DEFAULT NULL,
    `hide`             tinyint(1)          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_award_player` (`player_id`, `award_type_id`) USING BTREE,
    CONSTRAINT `FK_player_award_user_id` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_player_ignore
-- ----------------------------
DROP TABLE IF EXISTS `sn_player_ignore`;
CREATE TABLE `sn_player_ignore`
(
    `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `player_id`  bigint(20) UNSIGNED NOT NULL,
    `ignored_id` bigint(20) UNSIGNED NOT NULL,
    `subsystem`  tinyint(4)          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_player_ignore_all` (`player_id`, `ignored_id`, `subsystem`) USING BTREE,
    INDEX `I_player_ignore_ignored` (`ignored_id`) USING BTREE,
    CONSTRAINT `FK_player_ignore_ignored` FOREIGN KEY (`ignored_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_player_ignore_player` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_player_name_history
-- ----------------------------
DROP TABLE IF EXISTS `sn_player_name_history`;
CREATE TABLE `sn_player_name_history`
(
    `player_id`   bigint(20) UNSIGNED                                    NULL     DEFAULT NULL COMMENT 'Player ID',
    `player_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Historical player name',
    `timestamp`   timestamp(0)                                           NOT NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0) COMMENT 'When player changed name',
    PRIMARY KEY (`player_name`) USING BTREE,
    INDEX `I_player_name_history_id_name` (`player_id`, `player_name`) USING BTREE,
    CONSTRAINT `FK_player_name_history_id` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_player_options
-- ----------------------------
DROP TABLE IF EXISTS `sn_player_options`;
CREATE TABLE `sn_player_options`
(
    `player_id` bigint(20) UNSIGNED                                       NOT NULL DEFAULT 0,
    `option_id` smallint(5) UNSIGNED                                      NOT NULL DEFAULT 0,
    `value`     varchar(16000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`player_id`, `option_id`) USING BTREE,
    CONSTRAINT `FK_player_options_user_id` FOREIGN KEY (`player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_powerup
-- ----------------------------
DROP TABLE IF EXISTS `sn_powerup`;
CREATE TABLE `sn_powerup`
(
    `powerup_id`          bigint(20) UNSIGNED   NOT NULL AUTO_INCREMENT,
    `powerup_user_id`     bigint(20) UNSIGNED   NULL     DEFAULT NULL,
    `powerup_planet_id`   bigint(20) UNSIGNED   NULL     DEFAULT NULL,
    `powerup_category`    smallint(6)           NOT NULL DEFAULT 0,
    `powerup_unit_id`     mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
    `powerup_unit_level`  smallint(5) UNSIGNED  NOT NULL DEFAULT 0,
    `powerup_time_start`  int(11)               NOT NULL DEFAULT 0,
    `powerup_time_finish` int(11)               NOT NULL DEFAULT 0,
    PRIMARY KEY (`powerup_id`) USING BTREE,
    INDEX `I_powerup_user_id` (`powerup_user_id`) USING BTREE,
    INDEX `I_powerup_planet_id` (`powerup_planet_id`) USING BTREE,
    INDEX `I_user_powerup_time` (`powerup_user_id`, `powerup_unit_id`, `powerup_time_start`,
                                 `powerup_time_finish`) USING BTREE,
    INDEX `I_planet_powerup_time` (`powerup_planet_id`, `powerup_unit_id`, `powerup_time_start`,
                                   `powerup_time_finish`) USING BTREE,
    CONSTRAINT `FK_powerup_planet_id` FOREIGN KEY (`powerup_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_powerup_user_id` FOREIGN KEY (`powerup_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_que
-- ----------------------------
DROP TABLE IF EXISTS `sn_que`;
CREATE TABLE `sn_que`
(
    `que_id`                bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT COMMENT 'Internal que id',
    `que_player_id`         bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Que owner ID',
    `que_planet_id`         bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Which planet this que item belongs',
    `que_planet_id_origin`  bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Planet spawner ID',
    `que_type`              tinyint(1) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'Que type',
    `que_time_left`         decimal(20, 5) UNSIGNED                                 NOT NULL DEFAULT 0.00000 COMMENT 'Build time left from last activity',
    `que_unit_id`           bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'Unit ID',
    `que_unit_amount`       bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0 COMMENT 'Amount left to build',
    `que_unit_mode`         tinyint(1)                                              NOT NULL DEFAULT 0 COMMENT 'Build/Destroy',
    `que_unit_level`        int(10) UNSIGNED                                        NOT NULL DEFAULT 0 COMMENT 'Unit level. Informational field',
    `que_unit_time`         decimal(20, 5)                                          NOT NULL DEFAULT 0.00000 COMMENT 'Time to build one unit. Informational field',
    `que_unit_price`        varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Price per unit - for correct trim/clear in case of global price events',
    `que_unit_one_time_raw` decimal(20, 5)                                          NOT NULL DEFAULT 0.00000,
    PRIMARY KEY (`que_id`) USING BTREE,
    UNIQUE INDEX `que_id` (`que_id`) USING BTREE,
    INDEX `I_que_player_type_planet` (`que_player_id`, `que_type`, `que_planet_id`, `que_id`) USING BTREE,
    INDEX `I_que_player_type` (`que_player_id`, `que_type`, `que_id`) USING BTREE,
    INDEX `I_que_planet_id` (`que_planet_id`) USING BTREE,
    INDEX `FK_que_planet_id_origin` (`que_planet_id_origin`) USING BTREE,
    CONSTRAINT `FK_que_planet_id` FOREIGN KEY (`que_planet_id`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_que_planet_id_origin` FOREIGN KEY (`que_planet_id_origin`) REFERENCES `sn_planets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_que_player_id` FOREIGN KEY (`que_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_quest
-- ----------------------------
DROP TABLE IF EXISTS `sn_quest`;
CREATE TABLE `sn_quest`
(
    `quest_id`          bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `quest_name`        varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL     DEFAULT NULL,
    `quest_description` text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `quest_conditions`  text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `quest_rewards`     text CHARACTER SET utf8 COLLATE utf8_general_ci         NULL,
    `quest_type`        tinyint(4)                                              NULL     DEFAULT NULL,
    `quest_order`       int(11)                                                 NOT NULL DEFAULT 0,
    PRIMARY KEY (`quest_id`) USING BTREE,
    UNIQUE INDEX `quest_id` (`quest_id`) USING BTREE,
    INDEX `quest_type` (`quest_type`, `quest_order`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_quest_status
-- ----------------------------
DROP TABLE IF EXISTS `sn_quest_status`;
CREATE TABLE `sn_quest_status`
(
    `quest_status_id`       bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `quest_status_quest_id` bigint(20) UNSIGNED                                     NULL     DEFAULT NULL,
    `quest_status_user_id`  bigint(20) UNSIGNED                                     NOT NULL DEFAULT 0,
    `quest_status_progress` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `quest_status_status`   tinyint(4)                                              NOT NULL DEFAULT 1,
    PRIMARY KEY (`quest_status_id`) USING BTREE,
    UNIQUE INDEX `quest_status_id` (`quest_status_id`) USING BTREE,
    INDEX `quest_status_user_id` (`quest_status_user_id`, `quest_status_quest_id`, `quest_status_status`) USING BTREE,
    INDEX `FK_quest_status_quest_id` (`quest_status_quest_id`) USING BTREE,
    CONSTRAINT `FK_quest_status_quest_id` FOREIGN KEY (`quest_status_quest_id`) REFERENCES `sn_quest` (`quest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_quest_status_user_id` FOREIGN KEY (`quest_status_user_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_referrals
-- ----------------------------
DROP TABLE IF EXISTS `sn_referrals`;
CREATE TABLE `sn_referrals`
(
    `id`          bigint(20) UNSIGNED NOT NULL DEFAULT 0,
    `id_partner`  bigint(20) UNSIGNED NULL     DEFAULT NULL,
    `dark_matter` decimal(65, 0)      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `id_partner` (`id_partner`) USING BTREE,
    CONSTRAINT `FK_referrals_id` FOREIGN KEY (`id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_referrals_id_partner` FOREIGN KEY (`id_partner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_security_browser
-- ----------------------------
DROP TABLE IF EXISTS `sn_security_browser`;
CREATE TABLE `sn_security_browser`
(
    `browser_id`         bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `browser_user_agent` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `timestamp`          timestamp(0)                                            NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`browser_id`) USING BTREE,
    INDEX `I_browser_user_agent` (`browser_user_agent`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = latin1
  COLLATE = latin1_bin
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_security_device
-- ----------------------------
DROP TABLE IF EXISTS `sn_security_device`;
CREATE TABLE `sn_security_device`
(
    `device_id`     bigint(20) UNSIGNED                              NOT NULL AUTO_INCREMENT,
    `device_cypher` char(16) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
    `timestamp`     timestamp(0)                                     NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`device_id`) USING BTREE,
    INDEX `I_device_cypher` (`device_cypher`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = latin1
  COLLATE = latin1_bin
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_security_player_entry
-- ----------------------------
DROP TABLE IF EXISTS `sn_security_player_entry`;
CREATE TABLE `sn_security_player_entry`
(
    `id`          bigint(20) UNSIGNED                                  NOT NULL AUTO_INCREMENT,
    `device_id`   bigint(20) UNSIGNED                                  NOT NULL DEFAULT 0,
    `browser_id`  bigint(20) UNSIGNED                                  NOT NULL DEFAULT 0,
    `user_ip`     int(10) UNSIGNED                                     NOT NULL DEFAULT 0,
    `user_proxy`  varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
    `first_visit` timestamp(0)                                         NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_player_entry_unique` (`device_id`, `browser_id`, `user_ip`, `user_proxy`) USING BTREE,
    INDEX `I_player_entry_browser_id` (`browser_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = latin1
  COLLATE = latin1_bin
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_security_query_strings
-- ----------------------------
DROP TABLE IF EXISTS `sn_security_query_strings`;
CREATE TABLE `sn_security_query_strings`
(
    `id`           bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `query_string` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`) USING BTREE,
    UNIQUE INDEX `I_query_string` (`query_string`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_security_url
-- ----------------------------
DROP TABLE IF EXISTS `sn_security_url`;
CREATE TABLE `sn_security_url`
(
    `url_id`     int(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `url_string` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`url_id`) USING BTREE,
    UNIQUE INDEX `I_url_string` (`url_string`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = latin1
  COLLATE = latin1_bin
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_server_patches
-- ----------------------------
DROP TABLE IF EXISTS `sn_server_patches`;
CREATE TABLE `sn_server_patches`
(
    `id`      int(10) UNSIGNED NOT NULL COMMENT 'Patch internal ID',
    `applied` timestamp(0)     NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_applied` (`applied`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_statpoints
-- ----------------------------
DROP TABLE IF EXISTS `sn_statpoints`;
CREATE TABLE `sn_statpoints`
(
    `stat_date`      int(11)                 NOT NULL DEFAULT 0,
    `id_owner`       bigint(20) UNSIGNED     NULL     DEFAULT NULL,
    `id_ally`        bigint(20) UNSIGNED     NULL     DEFAULT NULL,
    `stat_type`      tinyint(3) UNSIGNED     NULL     DEFAULT 0,
    `stat_code`      tinyint(3) UNSIGNED     NOT NULL DEFAULT 0,
    `tech_rank`      int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `tech_old_rank`  int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `tech_points`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `tech_count`     decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `build_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `build_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `build_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `build_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `defs_rank`      int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `defs_old_rank`  int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `defs_points`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `defs_count`     decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `fleet_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `fleet_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `fleet_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `fleet_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `res_rank`       int(11) UNSIGNED        NULL     DEFAULT 0 COMMENT 'Rank by resources',
    `res_old_rank`   int(11) UNSIGNED        NULL     DEFAULT 0 COMMENT 'Old rank by resources',
    `res_points`     decimal(65, 0) UNSIGNED NULL     DEFAULT 0 COMMENT 'Resource stat points',
    `res_count`      decimal(65, 0) UNSIGNED NULL     DEFAULT 0 COMMENT 'Resource count',
    `total_rank`     int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `total_old_rank` int(11) UNSIGNED        NOT NULL DEFAULT 0,
    `total_points`   decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    `total_count`    decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0,
    INDEX `TECH` (`tech_points`) USING BTREE,
    INDEX `BUILDS` (`build_points`) USING BTREE,
    INDEX `DEFS` (`defs_points`) USING BTREE,
    INDEX `FLEET` (`fleet_points`) USING BTREE,
    INDEX `TOTAL` (`total_points`) USING BTREE,
    INDEX `i_stats_owner` (`id_owner`, `stat_type`, `stat_code`, `tech_rank`, `build_rank`, `defs_rank`, `fleet_rank`,
                           `total_rank`) USING BTREE,
    INDEX `I_stats_id_ally` (`id_ally`, `stat_type`, `stat_code`) USING BTREE,
    INDEX `I_stats_type_code` (`stat_type`, `stat_code`) USING BTREE,
    CONSTRAINT `FK_stats_id_ally` FOREIGN KEY (`id_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_stats_id_owner` FOREIGN KEY (`id_owner`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_survey
-- ----------------------------
DROP TABLE IF EXISTS `sn_survey`;
CREATE TABLE `sn_survey`
(
    `survey_id`          int(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `survey_announce_id` bigint(11) UNSIGNED                                     NULL DEFAULT NULL,
    `survey_question`    varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `survey_until`       datetime(0)                                             NULL DEFAULT NULL,
    PRIMARY KEY (`survey_id`) USING BTREE,
    INDEX `I_survey_announce_id` (`survey_announce_id`) USING BTREE,
    CONSTRAINT `FK_survey_announce_id` FOREIGN KEY (`survey_announce_id`) REFERENCES `sn_announce` (`idAnnounce`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_survey_answers
-- ----------------------------
DROP TABLE IF EXISTS `sn_survey_answers`;
CREATE TABLE `sn_survey_answers`
(
    `survey_answer_id`   int(10) UNSIGNED                                        NOT NULL AUTO_INCREMENT,
    `survey_parent_id`   int(10) UNSIGNED                                        NULL DEFAULT NULL,
    `survey_answer_text` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    PRIMARY KEY (`survey_answer_id`) USING BTREE,
    INDEX `I_survey_answers_survey_parent_id` (`survey_parent_id`) USING BTREE,
    CONSTRAINT `FK_survey_answers_survey_parent_id` FOREIGN KEY (`survey_parent_id`) REFERENCES `sn_survey` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_survey_votes
-- ----------------------------
DROP TABLE IF EXISTS `sn_survey_votes`;
CREATE TABLE `sn_survey_votes`
(
    `survey_vote_id`          int(10) UNSIGNED                                       NOT NULL AUTO_INCREMENT,
    `survey_parent_id`        int(10) UNSIGNED                                       NULL DEFAULT NULL,
    `survey_parent_answer_id` int(10) UNSIGNED                                       NULL DEFAULT NULL,
    `survey_vote_user_id`     bigint(20) UNSIGNED                                    NULL DEFAULT NULL,
    `survey_vote_user_name`   varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    PRIMARY KEY (`survey_vote_id`) USING BTREE,
    INDEX `I_survey_votes_survey_parent_id` (`survey_parent_id`) USING BTREE,
    INDEX `I_survey_votes_survey_parent_answer_id` (`survey_parent_answer_id`) USING BTREE,
    INDEX `I_survey_votes_user_id` (`survey_vote_user_id`) USING BTREE,
    CONSTRAINT `FK_survey_votes_survey_parent_answer_id` FOREIGN KEY (`survey_parent_answer_id`) REFERENCES `sn_survey_answers` (`survey_answer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_survey_votes_survey_parent_id` FOREIGN KEY (`survey_parent_id`) REFERENCES `sn_survey` (`survey_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_survey_votes_user_id` FOREIGN KEY (`survey_vote_user_id`) REFERENCES `sn_users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_text
-- ----------------------------
DROP TABLE IF EXISTS `sn_text`;
CREATE TABLE `sn_text`
(
    `id`       bigint(20) UNSIGNED                                     NOT NULL AUTO_INCREMENT,
    `parent`   bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Parent record. NULL - no parent',
    `context`  bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Tutorial context. NULL - main screen',
    `prev`     bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Previous text part. NULL - first part',
    `next`     bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Next text part. NULL - final part',
    `next_alt` bigint(20) UNSIGNED                                     NULL     DEFAULT NULL COMMENT 'Alternative next text part. NULL - no alternative',
    `title`    varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Text title',
    `content`  text CHARACTER SET utf8 COLLATE utf8_unicode_ci         NULL COMMENT 'Content - 64k fits to all!',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `I_text_parent` (`parent`) USING BTREE,
    INDEX `I_text_prev` (`prev`) USING BTREE,
    INDEX `I_text_next` (`next`) USING BTREE,
    INDEX `I_text_next_alt` (`next_alt`) USING BTREE,
    CONSTRAINT `FK_text_next` FOREIGN KEY (`next`) REFERENCES `sn_text` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_text_next_alt` FOREIGN KEY (`next_alt`) REFERENCES `sn_text` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_text_parent` FOREIGN KEY (`parent`) REFERENCES `sn_text` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_text_prev` FOREIGN KEY (`prev`) REFERENCES `sn_text` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_unicode_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report`;
CREATE TABLE `sn_ube_report`
(
    `ube_report_id`                      bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT COMMENT 'Report ID',
    `ube_report_cypher`                  char(32) CHARACTER SET utf8 COLLATE utf8_general_ci    NOT NULL DEFAULT '' COMMENT '16 char secret report ID',
    `ube_report_time_combat`             datetime(0)                                            NOT NULL COMMENT 'Combat time',
    `ube_report_time_process`            timestamp(0)                                           NOT NULL DEFAULT CURRENT_TIMESTAMP(0) COMMENT 'Time when combat was processed',
    `ube_report_time_spent`              decimal(11, 8) UNSIGNED                                NOT NULL DEFAULT 0.00000000 COMMENT 'Time in seconds spent for combat calculations',
    `ube_report_mission_type`            tinyint(1) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Mission type',
    `ube_report_combat_admin`            tinyint(1) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Does admin participates in combat?',
    `ube_report_combat_result`           tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Combat outcome',
    `ube_report_combat_sfr`              tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Small Fleet Reconnaissance',
    `ube_report_planet_id`               bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Player planet ID',
    `ube_report_planet_name`             varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
    `ube_report_planet_size`             smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player diameter',
    `ube_report_planet_galaxy`           smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate galaxy',
    `ube_report_planet_system`           smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate system',
    `ube_report_planet_planet`           smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate planet',
    `ube_report_planet_planet_type`      tinyint(4)                                             NOT NULL DEFAULT 1 COMMENT 'Player planet type',
    `ube_report_moon`                    tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Moon result: was, none, failed, created, destroyed',
    `ube_report_moon_chance`             decimal(9, 6) UNSIGNED                                 NOT NULL DEFAULT 0.000000 COMMENT 'Moon creation chance',
    `ube_report_moon_size`               smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Moon size',
    `ube_report_moon_reapers`            tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Moon reapers result: none, died, survived',
    `ube_report_moon_destroy_chance`     tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Moon destroy chance',
    `ube_report_moon_reapers_die_chance` tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Moon reapers die chance',
    `ube_report_debris_metal`            decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Metal debris',
    `ube_report_debris_crystal`          decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Crystal debris',
    `ube_report_capture_result`          tinyint(3) UNSIGNED                                    NOT NULL DEFAULT 0,
    `ube_report_debris_total_in_metal`   decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Total debris in metal',
    PRIMARY KEY (`ube_report_id`) USING BTREE,
    UNIQUE INDEX `ube_report_id` (`ube_report_id`) USING BTREE,
    INDEX `I_ube_report_cypher` (`ube_report_cypher`) USING BTREE,
    INDEX `I_ube_report_time_combat` (`ube_report_time_combat`) USING BTREE,
    INDEX `I_ube_report_time_debris_id` (`ube_report_time_process`, `ube_report_debris_total_in_metal`,
                                         `ube_report_id`) USING BTREE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report_fleet
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_fleet`;
CREATE TABLE `sn_ube_report_fleet`
(
    `ube_report_fleet_id`                 bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
    `ube_report_id`                       bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Report ID',
    `ube_report_fleet_player_id`          bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Owner ID',
    `ube_report_fleet_fleet_id`           bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Fleet ID',
    `ube_report_fleet_planet_id`          bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Player attack bonus',
    `ube_report_fleet_planet_name`        varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Planet' COMMENT 'Player planet name',
    `ube_report_fleet_planet_galaxy`      smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate galaxy',
    `ube_report_fleet_planet_system`      smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate system',
    `ube_report_fleet_planet_planet`      smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0 COMMENT 'Player planet coordinate planet',
    `ube_report_fleet_planet_planet_type` tinyint(4)                                             NOT NULL DEFAULT 1 COMMENT 'Player planet type',
    `ube_report_fleet_bonus_attack`       decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Fleet attack bonus',
    `ube_report_fleet_bonus_shield`       decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Fleet shield bonus',
    `ube_report_fleet_bonus_armor`        decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Fleet armor bonus',
    `ube_report_fleet_resource_metal`     decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Fleet metal amount',
    `ube_report_fleet_resource_crystal`   decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Fleet crystal amount',
    `ube_report_fleet_resource_deuterium` decimal(65, 0) UNSIGNED                                NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium amount',
    PRIMARY KEY (`ube_report_fleet_id`) USING BTREE,
    UNIQUE INDEX `ube_report_fleet_id` (`ube_report_fleet_id`) USING BTREE,
    INDEX `FK_ube_report_fleet_ube_report` (`ube_report_id`) USING BTREE,
    CONSTRAINT `FK_ube_report_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report_outcome_fleet
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_outcome_fleet`;
CREATE TABLE `sn_ube_report_outcome_fleet`
(
    `ube_report_outcome_fleet_id`                         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
    `ube_report_id`                                       bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
    `ube_report_outcome_fleet_fleet_id`                   bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',
    `ube_report_outcome_fleet_resource_lost_metal`        decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet metal loss from units',
    `ube_report_outcome_fleet_resource_lost_crystal`      decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet crystal loss from units',
    `ube_report_outcome_fleet_resource_lost_deuterium`    decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium loss from units',
    `ube_report_outcome_fleet_resource_dropped_metal`     decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet metal dropped due reduced cargo',
    `ube_report_outcome_fleet_resource_dropped_crystal`   decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet crystal dropped due reduced cargo',
    `ube_report_outcome_fleet_resource_dropped_deuterium` decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet deuterium dropped due reduced cargo',
    `ube_report_outcome_fleet_resource_loot_metal`        decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot metal',
    `ube_report_outcome_fleet_resource_loot_crystal`      decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot crystal',
    `ube_report_outcome_fleet_resource_loot_deuterium`    decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Looted/Lost from loot deuterium',
    `ube_report_outcome_fleet_resource_lost_in_metal`     decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Fleet total resource loss in metal',
    PRIMARY KEY (`ube_report_outcome_fleet_id`) USING BTREE,
    UNIQUE INDEX `ube_report_outcome_fleet_id` (`ube_report_outcome_fleet_id`) USING BTREE,
    INDEX `I_ube_report_outcome_fleet_report_fleet` (`ube_report_id`, `ube_report_outcome_fleet_fleet_id`) USING BTREE,
    CONSTRAINT `FK_ube_report_outcome_fleet_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report_outcome_unit
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_outcome_unit`;
CREATE TABLE `sn_ube_report_outcome_unit`
(
    `ube_report_outcome_unit_id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
    `ube_report_id`                      bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Report ID',
    `ube_report_outcome_unit_fleet_id`   bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Fleet ID',
    `ube_report_outcome_unit_unit_id`    bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit ID',
    `ube_report_outcome_unit_restored`   decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Unit restored',
    `ube_report_outcome_unit_lost`       decimal(65, 0)      NOT NULL DEFAULT 0 COMMENT 'Unit lost',
    `ube_report_outcome_unit_sort_order` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit pass-through sort order to maintain same output',
    PRIMARY KEY (`ube_report_outcome_unit_id`) USING BTREE,
    UNIQUE INDEX `ube_report_outcome_unit_id` (`ube_report_outcome_unit_id`) USING BTREE,
    INDEX `I_ube_report_outcome_unit_report_order` (`ube_report_id`, `ube_report_outcome_unit_sort_order`) USING BTREE,
    CONSTRAINT `FK_ube_report_outcome_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report_player
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_player`;
CREATE TABLE `sn_ube_report_player`
(
    `ube_report_player_id`           bigint(20) UNSIGNED                                    NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
    `ube_report_id`                  bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Report ID',
    `ube_report_player_player_id`    bigint(20) UNSIGNED                                    NOT NULL DEFAULT 0 COMMENT 'Player ID',
    `ube_report_player_name`         varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'Player name',
    `ube_report_player_attacker`     tinyint(1)                                             NOT NULL DEFAULT 0 COMMENT 'Is player an attacker?',
    `ube_report_player_bonus_attack` decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Player attack bonus',
    `ube_report_player_bonus_shield` decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Player shield bonus',
    `ube_report_player_bonus_armor`  decimal(11, 2)                                         NOT NULL DEFAULT 0.00 COMMENT 'Player armor bonus',
    PRIMARY KEY (`ube_report_player_id`) USING BTREE,
    UNIQUE INDEX `ube_report_player_id` (`ube_report_player_id`) USING BTREE,
    INDEX `I_ube_report_player_player_id` (`ube_report_player_player_id`) USING BTREE,
    INDEX `FK_ube_report_player_ube_report` (`ube_report_id`) USING BTREE,
    CONSTRAINT `FK_ube_report_player_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_ube_report_unit
-- ----------------------------
DROP TABLE IF EXISTS `sn_ube_report_unit`;
CREATE TABLE `sn_ube_report_unit`
(
    `ube_report_unit_id`          bigint(20) UNSIGNED     NOT NULL AUTO_INCREMENT COMMENT 'Record DB ID',
    `ube_report_id`               bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Report ID',
    `ube_report_unit_player_id`   bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Owner ID',
    `ube_report_unit_fleet_id`    bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Fleet ID',
    `ube_report_unit_round`       tinyint(4)              NOT NULL DEFAULT 0 COMMENT 'Round number',
    `ube_report_unit_unit_id`     bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Unit ID',
    `ube_report_unit_count`       decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit count',
    `ube_report_unit_boom`        smallint(5) UNSIGNED    NOT NULL DEFAULT 0 COMMENT 'Unit booms',
    `ube_report_unit_attack`      decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit attack',
    `ube_report_unit_shield`      decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit shield',
    `ube_report_unit_armor`       decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit armor',
    `ube_report_unit_attack_base` decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base attack',
    `ube_report_unit_shield_base` decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base shield',
    `ube_report_unit_armor_base`  decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit base armor',
    `ube_report_unit_sort_order`  bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Unit pass-through sort order to maintain same output',
    PRIMARY KEY (`ube_report_unit_id`) USING BTREE,
    UNIQUE INDEX `ube_report_unit_id` (`ube_report_unit_id`) USING BTREE,
    INDEX `I_ube_report_unit_report_round_fleet_order` (`ube_report_id`, `ube_report_unit_round`,
                                                        `ube_report_unit_fleet_id`,
                                                        `ube_report_unit_sort_order`) USING BTREE,
    INDEX `I_ube_report_unit_report_unit_order` (`ube_report_id`, `ube_report_unit_sort_order`) USING BTREE,
    INDEX `I_ube_report_unit_order` (`ube_report_unit_sort_order`) USING BTREE,
    CONSTRAINT `FK_ube_report_unit_ube_report` FOREIGN KEY (`ube_report_id`) REFERENCES `sn_ube_report` (`ube_report_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_unit
-- ----------------------------
DROP TABLE IF EXISTS `sn_unit`;
CREATE TABLE `sn_unit`
(
    `unit_id`            bigint(20) UNSIGNED     NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
    `unit_player_id`     bigint(20) UNSIGNED     NULL     DEFAULT NULL COMMENT 'Unit owner',
    `unit_location_type` tinyint(4)              NOT NULL DEFAULT 0 COMMENT 'Location type: universe, user, planet (moon?), fleet',
    `unit_location_id`   bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Location ID',
    `unit_type`          bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Unit type',
    `unit_snid`          bigint(20) UNSIGNED     NOT NULL DEFAULT 0 COMMENT 'Unit SuperNova ID',
    `unit_level`         decimal(65, 0) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Unit level or count - dependent of unit_type',
    `unit_time_start`    datetime(0)             NULL     DEFAULT NULL COMMENT 'Unit activation start time',
    `unit_time_finish`   datetime(0)             NULL     DEFAULT NULL COMMENT 'Unit activation end time',
    PRIMARY KEY (`unit_id`) USING BTREE,
    INDEX `I_unit_player_location_snid` (`unit_player_id`, `unit_location_type`, `unit_location_id`,
                                         `unit_snid`) USING BTREE,
    INDEX `I_unit_record_search` (`unit_snid`, `unit_player_id`, `unit_level`, `unit_id`) USING BTREE,
    INDEX `I_unit_location` (`unit_location_type`, `unit_location_id`) USING BTREE,
    INDEX `I_unit_type_snid` (`unit_type`, `unit_snid`) USING BTREE,
    CONSTRAINT `FK_unit_player_id` FOREIGN KEY (`unit_player_id`) REFERENCES `sn_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_universe
-- ----------------------------
DROP TABLE IF EXISTS `sn_universe`;
CREATE TABLE `sn_universe`
(
    `universe_galaxy` smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0,
    `universe_system` smallint(5) UNSIGNED                                   NOT NULL DEFAULT 0,
    `universe_name`   varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
    `universe_price`  bigint(20)                                             NOT NULL DEFAULT 0,
    PRIMARY KEY (`universe_galaxy`, `universe_system`) USING BTREE
) ENGINE = InnoDB
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sn_users
-- ----------------------------
DROP TABLE IF EXISTS `sn_users`;
CREATE TABLE `sn_users`
(
    `id`                       bigint(20) UNSIGNED                                         NOT NULL AUTO_INCREMENT,
    `username`                 varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '' COMMENT 'Player name',
    `authlevel`                tinyint(3) UNSIGNED                                         NOT NULL DEFAULT 0,
    `vacation`                 int(11) UNSIGNED                                            NULL     DEFAULT 0,
    `banaday`                  int(10) UNSIGNED                                            NOT NULL DEFAULT 0 COMMENT 'User ban status',
    `dark_matter`              bigint(20)                                                  NULL     DEFAULT 0,
    `dark_matter_total`        bigint(20)                                                  NOT NULL DEFAULT 0 COMMENT 'Total Dark Matter amount ever gained',
    `player_rpg_explore_xp`    bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `player_rpg_explore_level` bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `ally_id`                  bigint(20) UNSIGNED                                         NULL     DEFAULT NULL,
    `ally_tag`                 varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci       NULL     DEFAULT NULL,
    `ally_name`                varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci      NULL     DEFAULT NULL,
    `ally_register_time`       int(11)                                                     NOT NULL DEFAULT 0,
    `ally_rank_id`             int(11)                                                     NOT NULL DEFAULT 0,
    `lvl_minier`               bigint(20) UNSIGNED                                         NOT NULL DEFAULT 1,
    `xpminier`                 bigint(20) UNSIGNED                                         NULL     DEFAULT 0,
    `player_rpg_tech_xp`       bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `player_rpg_tech_level`    bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `lvl_raid`                 bigint(20) UNSIGNED                                         NOT NULL DEFAULT 1,
    `xpraid`                   bigint(20) UNSIGNED                                         NULL     DEFAULT 0,
    `raids`                    bigint(20) UNSIGNED                                         NULL     DEFAULT 0,
    `raidsloose`               bigint(20) UNSIGNED                                         NULL     DEFAULT 0,
    `raidswin`                 bigint(20) UNSIGNED                                         NULL     DEFAULT 0,
    `new_message`              int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_alliance`             int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_joueur`               int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_attaque`              int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_spy`                  int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_exploit`              int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_transport`            int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_expedition`           int(11)                                                     NOT NULL DEFAULT 0,
    `mnl_buildlist`            int(11)                                                     NOT NULL DEFAULT 0,
    `msg_admin`                bigint(11) UNSIGNED                                         NULL     DEFAULT 0,
    `bana`                     int(11)                                                     NULL     DEFAULT NULL,
    `deltime`                  int(10) UNSIGNED                                            NULL     DEFAULT 0,
    `news_lastread`            int(10) UNSIGNED                                            NULL     DEFAULT 0,
    `total_rank`               int(10) UNSIGNED                                            NOT NULL DEFAULT 0,
    `total_points`             bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `password`                 varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '',
    `salt`                     char(16) CHARACTER SET latin1 COLLATE latin1_general_ci     NOT NULL DEFAULT '',
    `email`                    varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '',
    `email_2`                  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci      NOT NULL DEFAULT '',
    `lang`                     varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci       NOT NULL DEFAULT 'ru',
    `avatar`                   tinyint(1) UNSIGNED                                         NOT NULL DEFAULT 0,
    `sign`                     mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci       NULL,
    `id_planet`                int(11)                                                     NOT NULL DEFAULT 0,
    `galaxy`                   int(11)                                                     NOT NULL DEFAULT 0,
    `system`                   int(11)                                                     NOT NULL DEFAULT 0,
    `planet`                   int(11)                                                     NOT NULL DEFAULT 0,
    `current_planet`           int(11)                                                     NOT NULL DEFAULT 0,
    `user_lastip`              varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci     NULL     DEFAULT NULL COMMENT 'User last IP',
    `user_last_proxy`          varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci     NOT NULL DEFAULT '',
    `user_last_browser_id`     bigint(20) UNSIGNED                                         NULL     DEFAULT NULL,
    `register_time`            int(10) UNSIGNED                                            NULL     DEFAULT 0,
    `onlinetime`               int(10) UNSIGNED                                            NULL     DEFAULT 0,
    `que_processed`            int(11) UNSIGNED                                            NOT NULL DEFAULT 0,
    `template`                 varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci      NOT NULL DEFAULT 'OpenGame',
    `skin`                     varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci      NOT NULL DEFAULT 'EpicBlue',
    `design`                   tinyint(4) UNSIGNED                                         NOT NULL DEFAULT 1,
    `noipcheck`                tinyint(4) UNSIGNED                                         NOT NULL DEFAULT 1,
    `options`                  mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci       NULL COMMENT 'Packed user options',
    `user_as_ally`             bigint(20) UNSIGNED                                         NULL     DEFAULT NULL,
    `metal`                    decimal(65, 5)                                              NOT NULL DEFAULT 0.00000,
    `crystal`                  decimal(65, 5)                                              NOT NULL DEFAULT 0.00000,
    `deuterium`                decimal(65, 5)                                              NOT NULL DEFAULT 0.00000,
    `user_birthday`            date                                                        NULL     DEFAULT NULL COMMENT 'User birthday',
    `user_birthday_celebrated` date                                                        NULL     DEFAULT NULL COMMENT 'Last time where user got birthday gift',
    `player_race`              int(11)                                                     NOT NULL DEFAULT 0 COMMENT 'Player\'s race',
    `vacation_next`            int(11)                                                     NOT NULL DEFAULT 0 COMMENT 'Next datetime when player can go on vacation',
    `metamatter`               bigint(20)                                                  NOT NULL DEFAULT 0 COMMENT 'Metamatter amount',
    `metamatter_total`         bigint(20)                                                  NOT NULL DEFAULT 0 COMMENT 'Total Metamatter amount ever bought',
    `admin_protection`         tinyint(3) UNSIGNED                                         NOT NULL DEFAULT 0 COMMENT 'Protection of administration planets',
    `user_bot`                 tinyint(1) UNSIGNED                                         NOT NULL DEFAULT 0,
    `gender`                   tinyint(1) UNSIGNED                                         NOT NULL DEFAULT 0,
    `immortal`                 timestamp(0)                                                NULL     DEFAULT NULL,
    `parent_account_id`        bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `parent_account_global`    bigint(20) UNSIGNED                                         NOT NULL DEFAULT 0,
    `server_name`              varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '',
    PRIMARY KEY (`id`) USING BTREE,
    INDEX `i_ally_id` (`ally_id`) USING BTREE,
    INDEX `i_ally_name` (`ally_name`) USING BTREE,
    INDEX `i_username` (`username`) USING BTREE,
    INDEX `i_ally_online` (`ally_id`, `onlinetime`) USING BTREE,
    INDEX `onlinetime` (`onlinetime`) USING BTREE,
    INDEX `i_register_time` (`register_time`) USING BTREE,
    INDEX `FK_users_ally_tag` (`ally_tag`) USING BTREE,
    INDEX `I_user_user_as_ally` (`user_as_ally`) USING BTREE,
    INDEX `I_user_birthday` (`user_birthday`, `user_birthday_celebrated`) USING BTREE,
    INDEX `I_user_id_name` (`id`, `username`) USING BTREE,
    INDEX `I_users_last_browser_id` (`user_last_browser_id`) USING BTREE,
    INDEX `I_users_parent_account_id` (`parent_account_id`) USING BTREE,
    INDEX `I_users_parent_account_global` (`parent_account_global`) USING BTREE,
    CONSTRAINT `FK_user_user_as_ally` FOREIGN KEY (`user_as_ally`) REFERENCES `sn_alliance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_users_ally_id` FOREIGN KEY (`ally_id`) REFERENCES `sn_alliance` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_users_ally_name` FOREIGN KEY (`ally_name`) REFERENCES `sn_alliance` (`ally_name`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_users_ally_tag` FOREIGN KEY (`ally_tag`) REFERENCES `sn_alliance` (`ally_tag`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `FK_users_browser_id` FOREIGN KEY (`user_last_browser_id`) REFERENCES `sn_security_browser` (`browser_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 2
  CHARACTER SET = utf8
  COLLATE = utf8_general_ci
  ROW_FORMAT = Dynamic;

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
