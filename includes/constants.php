<?php

/**
 * constants.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------

if(!defined('INSIDE'))
{
  die('Hacking attempt');
}

define('DB_VERSION', '31');
define('SN_RELEASE', '31RC4');
define('SN_VERSION', '31c4');

// Game type constants starts with GAME_
define('GAME_SUPERNOVA', 0);
define('GAME_OGAME'    , 1);

// Pattern to parse planet coordinates like [1:123:14] - no expedition [x:x:16] will pass!
define('PLANET_COORD_PREG', '/^\[([1-9]):([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]):(1[0-5]|[1-9])\]$/i');
// Pattern to parse scheduler '[[[[[YYYY-]MM-]DD ]HH:]MM:]SS'
define('SCHEDULER_PREG', '/^(?:(?:(?:(?:(?:(2\d\d\d)-)?(1[0-2]|0[1-9])-)?(?:(3[01]|[0-2]\d)\ ))?(?:(2[0-3]|[01]\d):))?(?:([0-5]\d):))?([0-5]\d)$/i');

define('MAX_ATTACK_ROUNDS', 10);

// Nombre maximum d'element dans la liste de construction de batiments
define('MAX_BUILDING_QUEUE_SIZE'  , 5);
// Nombre maximum d'element dans une ligne de liste de construction flotte et defenses
define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
// Taux de depassement possible dans l'espace de stockage des hangards ...
// 1.0 pour 100% - 1.1 pour 110% etc ...
define('MAX_OVERFLOW', 1);

// Valeurs de bases pour les colonies ou planetes fraichement crÃ©es
define('BASE_STORAGE_SIZE', 500000);
define('BUILD_METAL'      , 500);
define('BUILD_CRISTAL'    , 500);
define('BUILD_DEUTERIUM'  , 0);

//hide 1st player from stats
define('HIDE_1ST_FROM_STATS', 0);

//deny access to building records page
define('HIDE_BUILDING_RECORDS', 0);

//galaxy_show_admins
define('SHOW_ADMIN', 1);

// Mot qui sont interdit a la saisie !
$ListCensure = array ( '/</', '/>/', '/script/i', '/doquery/i', '/http/i', '/javascript/i');

// Confirmation record types
define('CONFIRM_REGISTRATION'  , 1);
define('CONFIRM_PASSWORD_RESET', 2);
define('CONFIRM_DELETE'        , 3);

// Ally diplomacy statuses
define('ALLY_DIPLOMACY_SELF'         , 'self');
define('ALLY_DIPLOMACY_NEUTRAL'      , 'neutral');
define('ALLY_DIPLOMACY_WAR'          , 'war');
define('ALLY_DIPLOMACY_PEACE'        , 'peace');
define('ALLY_DIPLOMACY_CONFEDERATION', 'confederation');
define('ALLY_DIPLOMACY_FEDERATION'   , 'federation');
define('ALLY_DIPLOMACY_UNION'        , 'union');
define('ALLY_DIPLOMACY_MASTER'       , 'master');
define('ALLY_DIPLOMACY_SLAVE'        , 'slave');

define('ALLY_PROPOSE_SEND', 0);

// Quest types
define('QUEST_TYPE_BUILD'   , 1);
define('QUEST_TYPE_RESEARCH', 2);
define('QUEST_TYPE_COMBAT'  , 3);

define('QUEST_STATUS_NOT_STARTED' , 0);
define('QUEST_STATUS_STARTED'     , 1);
define('QUEST_STATUS_COMPLETE'    , 2);

// *** Combat-related constants
// *** Mission Type constants starts with MT_
define('MT_ATTACK'   ,  1);
define('MT_AKS'      ,  2);
define('MT_TRANSPORT',  3);
define('MT_RELOCATE' ,  4);
define('MT_HOLD'     ,  5);
define('MT_SPY'      ,  6);
define('MT_COLONIZE' ,  7);
define('MT_RECYCLE'  ,  8);
define('MT_DESTROY'  ,  9);
define('MT_MISSILE'  , 10);
define('MT_EXPLORE'  , 15);

// *** Planet Target constants starts with PT_
define('PT_PLANET', 1);
define('PT_DEBRIS', 2);
define('PT_MOON'  , 3);

// *** Unit locations - shows db table where unit belong
define('LOC_USER',   1);
define('LOC_PLANET', 2);

// *** Caching masks
define('CACHE_NOTHING'    ,  0);
define('CACHE_FLEET'      ,  1);
define('CACHE_PLANET'     ,  2);
define('CACHE_USER'       ,  4);
define('CACHE_SOURCE'     ,  8);
define('CACHE_DESTINATION', 16);
define('CACHE_EVENT'      , 32);

define('CACHE_USER_SRC'  , CACHE_USER | CACHE_SOURCE);
define('CACHE_USER_DST'  , CACHE_USER | CACHE_DESTINATION);
define('CACHE_PLANET_SRC', CACHE_PLANET | CACHE_SOURCE);
define('CACHE_PLANET_DST', CACHE_PLANET | CACHE_DESTINATION);
define('CACHE_COMBAT'    , CACHE_FLEET | CACHE_PLANET | CACHE_USER | CACHE_SOURCE | CACHE_DESTINATION);

define('CACHE_ALL'       , CACHE_FLEET | CACHE_PLANET | CACHE_USER | CACHE_SOURCE | CACHE_DESTINATION | CACHE_EVENT);

define('CACHE_NONE'      , CACHE_NOTHING); // Alias for me

// *** Event types
define('EVENT_FLEET_ARRIVE', 1);
define('EVENT_FLEET_STAY'  , 2);
define('EVENT_FLEET_RETURN', 3);

// *** Constants for changing DM
define('RPG_NONE', 0);
define('RPG_STRUCTURE', 1);
define('RPG_RAID', 2);
define('RPG_TECH', 3);
define('RPG_ADMIN', 4);
define('RPG_BUY', 5);
define('RPG_MARKET', 6);
define('RPG_MERCENARY', 7);
define('RPG_QUEST', 8);
define('RPG_EXPEDITION', 9);
define('RPG_REFERRAL', 10);
define('RPG_ARTIFACT', 11);

// Login statuses
define('LOGIN_SUCCESS'               , 1);
define('LOGIN_SUCCESS_CREATE_PROFILE', 2);
define('LOGIN_ERROR_PASSWORD'        , 3);
define('LOGIN_ERROR_USERNAME'        , 4);
define('LOGIN_ERROR_ACTIVE'          , 5);
define('LOGIN_ERROR_EXTERNAL_AUTH'   , 6);

// Option groups
define('OPT_ALL',      0);
define('OPT_MESSAGE',  1);
define('OPT_UNIVERSE', 2);

// Message classes
define('MSG_TYPE_OUTBOX'   ,  -1);
define('MSG_TYPE_SPY'      ,   0);
define('MSG_TYPE_PLAYER'   ,   1);
define('MSG_TYPE_ALLIANCE' ,   2);
define('MSG_TYPE_COMBAT'   ,   3);
define('MSG_TYPE_RECYCLE'  ,   4);
define('MSG_TYPE_TRANSPORT',   5);
define('MSG_TYPE_ADMIN'    ,   6);
define('MSG_TYPE_EXPLORE'  ,  15);
define('MSG_TYPE_QUE'      ,  99);
define('MSG_TYPE_NEW'      , 100);

// Attack verification statuses
define('ATTACK_ALLOWED'          ,  0);
define('ATTACK_NO_TARGET'        ,  1);
define('ATTACK_OWN'              ,  2);
define('ATTACK_WRONG_MISSION'    ,  3);
define('ATTACK_NO_ALLY_DEPOSIT'  ,  4);
define('ATTACK_NO_DEBRIS'        ,  5);
define('ATTACK_VACATION'         ,  6);
define('ATTACK_SAME_IP'          ,  7);
define('ATTACK_BUFFING'          ,  8);
define('ATTACK_ADMIN'            ,  9);
define('ATTACK_NOOB'             , 10);
define('ATTACK_OWN_VACATION'     , 11);
define('ATTACK_NO_SILO'          , 12);
define('ATTACK_NO_MISSILE'       , 13);
define('ATTACK_NO_FLEET'         , 14);
define('ATTACK_NO_SLOTS'         , 15);
define('ATTACK_NO_SHIPS'         , 16);
define('ATTACK_NO_RECYCLERS'     , 17);
define('ATTACK_NO_SPIES'         , 18);
define('ATTACK_NO_COLONIZER'     , 19);
define('ATTACK_MISSILE_TOO_FAR'  , 20);
define('ATTACK_WRONG_STRUCTURE'  , 21);
define('ATTACK_NO_FUEL'          , 22);
define('ATTACK_NO_RESOURCES'     , 23);
define('ATTACK_NO_ACS'           , 24);
define('ATTACK_ACS_MISSTARGET'   , 25);
define('ATTACK_WRONG_SPEED'      , 26);
define('ATTACK_ACS_TOO_LATE'     , 27);
define('ATTACK_BASHING'          , 28);
define('ATTACK_BASHING_WAR_DELAY', 29);
define('ATTACK_ACS_WRONG_TARGET' , 30);

// *** Market variables
// === Market blocks
define('MARKET_ENTRY'        , 0);
define('MARKET_RESOURCES'    , 1);
define('MARKET_SCRAPPER'     , 2);
define('MARKET_STOCKMAN'     , 3);
define('MARKET_EXCHANGE'     , 4);
define('MARKET_BANKER'       , 5);
define('MARKET_PAWNSHOP'     , 6);

// === Market error statuses
define('MARKET_NOTHING'       ,  0);
define('MARKET_DEAL'          ,  1);
define('MARKET_DEAL_TRADE'    ,  2);
define('MARKET_NO_DM'         ,  3);
define('MARKET_NO_RESOURCES'  ,  4);
define('MARKET_ZERO_DEAL'     ,  5);
define('MARKET_NO_SHIPS'      ,  6);
define('MARKET_NOT_A_SHIP'    ,  7);
define('MARKET_NO_STOCK'      ,  8);
define('MARKET_ZERO_RES_STOCK',  9);
define('MARKET_NEGATIVE_SHIPS', 10);


// *** Mercenary/talent bonus types
define('BONUS_NONE'    , 0);  // No bonus
define('BONUS_PERCENT' , 1);  // Percent
define('BONUS_ADD'     , 2);  // Add
define('BONUS_ABILITY' , 3);  // Some ability
define('BONUS_MULTIPLY', 4);  // Multiply by value

// *** Action constat (build should be replaced with ACTION)
define('BUILD_CREATE' ,  1);
define('BUILD_DESTROY', -1);
define('ACTION_SELL',    -1);
define('ACTION_NOTHING',  0);
define('ACTION_BUY' ,     1);
define('ACTION_USE',      2);

// *** Check unit availability codes
define('BUILD_ALLOWED'       , 1);
define('BUILD_AMOUNT_WRONG'  , 2);
define('BUILD_QUE_WRONG'     , 3);
define('BUILD_QUE_UNIT_WRONG', 4);


// *** Que types
define('QUE_STRUCTURES', 1);
define('QUE_HANGAR'    , 4);
define('QUE_RESEARCH'  , 7);

// *** Subque types
define('SUBQUE_PLANET'  , 1);
define('SUBQUE_MOON'    , 3);
define('SUBQUE_FLEET'   , 4);
define('SUBQUE_DEFENSE' , 6);
define('SUBQUE_RESEARCH', 7);

// *** Units
// === Unit types
define('UNIT_STRUCTURE', 0);
define('UNIT_RESEARCH' , 1);
define('UNIT_SHIP'     , 2);
define('UNIT_DEFENSE'  , 4);
define('UNIT_MISSILE'  , 5);
define('UNIT_MERCENARY', 6);
define('UNIT_RESOURCE' , 9);

// *** Sort options
define('SORT_ASCENDING' , 0);
define('SORT_DESCENDING', 1);
define('SORT_ID'        , 0);
define('SORT_LOCATION'  , 1);
define('SORT_NAME'      , 2);
define('SORT_SIZE'      , 3);

// === Structures
define('STRUC_STRUCTURES', 0);
define('STRUC_MINE_METAL', 1);
define('STRUC_MINE_CRYSTAL', 2);
define('STRUC_MINE_DEUTERIUM', 3);
define('STRUC_MINE_SOLAR', 4);
define('STRUC_MINE_FUSION', 12);
define('STRUC_FACTORY_ROBOT', 14);
define('STRUC_FACTORY_NANO', 15);
define('STRUC_FACTORY_HANGAR', 21);
define('STRUC_STORE_METAL', 22);
define('STRUC_STORE_CRYSTAL', 23);
define('STRUC_STORE_DEUTERIUM', 24);
define('STRUC_LABORATORY', 31);
define('STRUC_LABORATORY_NANO', 35);
define('STRUC_TERRAFORMER', 33);
define('STRUC_ALLY_DEPOSIT', 34);
define('STRUC_SILO', 44);
define('STRUC_MOON_STATION', 41);
define('STRUC_MOON_PHALANX', 42);
define('STRUC_MOON_GATE', 43);

// === Techs
define('TECH_TECHNOLOGY', 100);
define('TECH_SPY', 106);
define('TECH_COMPUTER', 108);
define('TECH_WEAPON', 109);
define('TECH_SHIELD', 110);
define('TECH_ARMOR', 111);
define('TECH_ENERGY', 113);
define('TECH_HYPERSPACE', 114);
define('TECH_ENGINE_CHEMICAL', 115);
define('TECH_ENIGNE_ION', 117);
define('TECH_ENGINE_HYPER', 118);
define('TECH_LASER', 120);
define('TECH_ION', 121);
define('TECH_PLASMA', 122);
define('TECH_RESEARCH', 123);
define('TECH_EXPEDITION', 124);
define('TECH_COLONIZATION', 150);
define('TECH_ASTRO', 151);
define('TECH_GRAVITON', 199);

// === Hangar units
// --- Ships
define('SHIP_FLEET', 200);
define('SHIP_CARGO_SUPER', 201);
define('SHIP_CARGO_SMALL', 202);
define('SHIP_CARGO_BIG', 203);
define('SHIP_FIGHTER_LIGHT', 204);
define('SHIP_FIGHTER_HEAVY', 205);
define('SHIP_DESTROYER', 206);
define('SHIP_CRUISER', 207);
define('SHIP_COLONIZER', 208);
define('SHIP_RECYCLER', 209);
define('SHIP_SPY', 210);
define('SHIP_BOMBER', 211);
define('SHIP_SATTELITE_SOLAR', 212);
define('SHIP_DESTRUCTOR', 213);
define('SHIP_DEATH_STAR', 214);
define('SHIP_BATTLESHIP', 215);
define('SHIP_SUPERNOVA', 216);
define('SHIP_FIGHTER_ASSAULT', 217);
define('SHIP_CARGO_HYPER', 218);
// --- Defense
define('DEF_DEFENCE', 400);
define('misil_launcher', 401);
define('small_laser', 402);
define('big_laser', 403);
define('gauss_canyon', 404);
define('ionic_canyon', 405);
define('buster_canyon', 406);
define('small_protection_shield', 407);
define('big_protection_shield', 408);
define('planet_protector', 409);
// --- Missiles
define('interceptor_misil', 502);
define('interplanetary_misil', 503);

// === Mercenaries
// --- Mercenary type
define('MRT_GOVERNOR', 1); // Governor resides on planet
define('MRT_ADVISOR', 2);  // Advisor has imperium-wide effects

// --- Mercenary list
define('MRC_MERCENARIES', 600);
define('MRC_TECHNOLOGIST', 601); // MRC_TECHNOLOGIST

define('MRC_ENGINEER', 605);   // MRC_ENGINEER

define('MRC_ACADEMIC', 606); // 
define('MRC_FORTIFIER', 608);

define('MRC_ADMIRAL', 602);
define('MRC_STOCKMAN', 607);
// define('MRC_DEFENDER', 609);
define('MRC_SPY', 610);
define('MRC_COORDINATOR', 611);
define('MRC_DESTRUCTOR', 612);
define('MRC_NAVIGATOR', 613);
define('MRC_ASSASIN', 614);
define('MRC_EMPEROR', 615);

// === Resources
define('RES_RESOURCES', 900);
define('RES_METAL', 901);
define('RES_CRYSTAL', 902);
define('RES_DEUTERIUM', 903);
define('RES_ENERGY', 904);
define('RES_DARK_MATTER', 905);
define('RES_TIME', 999);

// === Artifacts
define('ART_ARTIFACTS', 1000);
define('ART_LHC', 1001);      // Additional moon chance
define('ART_RCD_SMALL', 1002);   // Rapid Colony Deployment - Set of buildings up to 10th level - 10/14/ 3/0 -   405 TM
define('ART_RCD_MEDIUM', 1003);  // Rapid Colony Deployment - Set of buildings up to 15th level - 15/20/ 8/0 -  4704 ÒÌ
define('ART_RCD_LARGE', 1004);   // Rapid Colony Deployment - Set of buildings up to 20th level - 20/25/10/1 - 39790 ÒÌ
//define('ART_SUPERCOMPUTER', 1005); // Speed up research
//define('ART_PLANET_GATE', 1006);   // Planet gate
//define('ART_NANOBOTS_SMALL', 1007); // Speed up building

?>
