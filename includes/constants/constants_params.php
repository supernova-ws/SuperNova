<?php
/**
 * Created by Gorlum 27.03.2018 10:41
 */

// Unit params
// define('GROUP_PARAMS', 1000000000);
// Зарезервировано для параметров: 1.000.000.000-2.000.000.000
define('P_MAX_STACK', 'max');
define('P_NAME', 'name'); // Вот тут будет следующая фаза - избавится вообще от обращения к P_NAME и перевести все обращения к UNIT_ID
define('P_UNIT_TYPE', 'type');
define('P_UNIT_TEMPORARY', 'temporary');
define('P_COST', 'cost');
define('P_COST_METAL', 'metal_cost');
define('P_FACTOR', 'factor');
define('P_REQUIRE', 'require');
define('P_STORAGE', 'storage');
define('P_STACKABLE', 'stackable'); // COMPLETE
define('P_DEPLOY', 'deploy');
define('P_BONUS_VALUE', 'bonus');
define('P_BONUS_TYPE', 'bonus_type');
define('P_CAPACITY', 'capacity');
define('P_UNIT_SIZE', 'size');
define('P_SPEED', 'speed');
define('P_UNIT_PRODUCTION', 'production');
define('P_UNIT_HIDE_FROM_BUILD', 'P_UNIT_HIDE_FROM_BUILD');

define('P_CHANCE', 'chance');

define('P_CHAT', 'chat');
define('P_CHAT_COMMANDS', 'commands');
define('P_CHAT_ALIASES', 'aliases');

define('P_RACE', 'player_race');

define('P_UNIT_GRANTS', 'P_UNIT_GRANTS'); // Что дает этот юнит

define('P_ATTACK', 'attack');
define('P_SHIELD', 'shield');
define('P_ARMOR', 'armor');
define('P_AMPLIFY', 'amplify');
define('P_DEFENSE', 'defense');
define('P_STRUCTURE', 'structure');
define('P_LOCATION', 'location');
define('P_CONSUMPTION', 'consumption');
define('P_UNIT_ENGINE', 'engine');

define('P_ID', 'id');
define('P_SNID', 'snid');

define('P_TABLE_NAME', 'table_name');
define('P_OWNER_INFO', 'owner_info');
define('P_OWNER_FIELD', 'owner_field');

define('P_WHERE', 'P_WHERE');
define('P_WHERE_STR', 'P_WHERE_STR');
define('P_FIELDS_STR', 'P_FIELDS_STR');
define('P_QUERY_STR', 'P_QUERY_STR');
define('P_ACTION_STR', 'P_ACTION_STR');
define('P_VERSION', 'P_VERSION');

define('P_THRUST', 'P_THRUST');
define('P_HULL_SIZE', 'P_HULL_SIZE');
define('P_CALC', 'P_CALC');
define('P_WEIGHT', 'P_WEIGHT');
define('P_PART_TYPE', 'P_PART_TYPE');
define('P_HULL_CAPACITY', 'P_HULL_CAPACITY');
define('P_COST_TOTAL', 'unit_cost');

define('P_OPTIONS', 'options');
define('P_ONLY_DARK_MATTER', 'P_ONLY_DARK_MATTER');
define('P_TIME_RAW', 'P_TIME_RAW');

define('P_MINING_IS_MANAGED', 'P_MINING_IS_MANAGED'); // Флаг, означающий что добычей ресурсов можно управлять

const P_MISSION_EXPEDITION_OUTCOME = 'outcome';
const P_MISSION_EXPEDITION_OUTCOME_TYPE = 'outcome_type';
const P_MISSION_EXPEDITION_OUTCOME_SECONDARY = 'secondary';
const P_MULTIPLIER = 'multiplier';
const P_MESSAGE_ID = 'messageId';

const P_FLEET_ATTACK_RES_LIST = 'resource_list';
const P_FLEET_ATTACK_SPEED_PERCENT_TENTH = 'fleet_speed_percent_tenth';
const P_FLEET_ATTACK_STAY_TIME = 'stay_time';
const P_FLEET_ATTACK_TARGET_STRUCTURE = 'target_structure';
const P_FLEET_ATTACK_FLYING_COUNT = 'flying_fleets';
const P_FLEET_ATTACK_FLEET_GROUP = 'fleet_group';
const P_FLEET_ATTACK_RESOURCES_SUM = 'resources';
