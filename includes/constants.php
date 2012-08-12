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

// ****************************************************************************************************************
// SHOULD BE REPLACED WITH CONFIG!
define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
define('MAX_ATTACK_ROUNDS', 10);
define('MAX_OVERFLOW', 1);
define('BASE_STORAGE_SIZE', 500000);
define('BUILD_METAL'      , 500);
define('BUILD_CRISTAL'    , 500);
define('BUILD_DEUTERIUM'  , 0);
define('HIDE_1ST_FROM_STATS', 0);
define('HIDE_BUILDING_RECORDS', 0);
define('SHOW_ADMIN', 1);



define('DB_VERSION', '34');
define('SN_RELEASE', '35');
define('SN_VERSION', '35a9.2');
define('SN_RELEASE_STABLE', '34d0'); // Latest stable release

// Game type constants starts with GAME_
define('GAME_SUPERNOVA', 0);
define('GAME_OGAME'    , 1);

// Pattern to parse planet coordinates like [1:123:14] - no expedition [x:x:16] will pass!
define('PLANET_COORD_PREG', '/^\[([1-9]):([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]):(1[0-5]|[1-9])\]$/i');
// Pattern to parse scheduler '[[[[[YYYY-]MM-]DD ]HH:]MM:]SS'
define('SCHEDULER_PREG', '/^(?:(?:(?:(?:(?:(2\d\d\d)-)?(1[0-2]|0[1-9])-)?(?:(3[01]|[0-2]\d)\ ))?(?:(2[0-3]|[01]\d):))?(?:([0-5]\d):))?([0-5]\d)$/i');
define('PREG_DATE_SQL', '/(20[1-9][0-9])\-(1[0-2]|0[1-9])\-(3[01]|[12]\d|0[1-9]) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])/');

// Default allowed chars for random string
define('SN_SYS_SEC_CHARS_ALLOWED', 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghkmnpqrstuvwxyz0123456789');

// Mot qui sont interdit a la saisie !
$ListCensure = array ( '/</', '/>/', '/script/i', '/doquery/i', '/http/i', '/javascript/i');

// Confirmation record types
define('CONFIRM_REGISTRATION'  , 1);
define('CONFIRM_PASSWORD_RESET', 2);
define('CONFIRM_DELETE'        , 3);

// Date & time range constants
define('DATE_FOREVER', 2000000000);

define('PERIOD_MINUTE', 60);
define('PERIOD_HOUR', PERIOD_MINUTE * 60);
define('PERIOD_DAY', PERIOD_HOUR * 24);
define('PERIOD_WEEK', PERIOD_DAY * 7);
define('PERIOD_MONTH', PERIOD_DAY * 30);

define('PERIOD_MINUTE_3' , PERIOD_MINUTE * 3);
define('PERIOD_MINUTE_5' , PERIOD_MINUTE * 5);
define('PERIOD_MINUTE_10', PERIOD_MINUTE * 10);
define('PERIOD_DAY_3'    , PERIOD_DAY * 3);
define('PERIOD_WEEK_2'   , PERIOD_WEEK * 2);
define('PERIOD_MONTH_2'  , PERIOD_MONTH * 2);
define('PERIOD_MONTH_3'  , PERIOD_MONTH * 3);

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
define('RPG_RENAME', 12);
define('RPG_ALLY', 13);
define('RPG_BIRTHDAY', 14);
define('RPG_PURCHASE', 15);
define('RPG_PLANS', 16);
define('RPG_PREMIUM', 17);
define('RPG_SECTOR', 18);
define('RPG_TELEPORT', 19);
define('RPG_CAPITAL', 20);
define('RPG_RACE', 21);


// Operation error status
define('ERR_NONE'               , 0); // No error
define('ERR_WARNING'            , 1); // There is warning - something altering normal operation process
define('ERR_ERROR'              , 2); // There is error - something permits operation from process
define('ERR_HACK'               , 4); // Operation is qualified as hack attempt
// New GLOBAL operation results
//define('RESULT_DEFAULT' , 0); // Default result - all went OK or result really doesn't matter
//define('RESULT_WARNING' , 1);
//define('RESULT_ERROR'   , 2);
//define('RESULT_HACKING' , 3);
//define('RESULT_PAYMENT_ERR_REQUEST_UNSUPPORTED', 5);

define('PAYMENT_STATUS_TEST' , -1); // Test payment
define('PAYMENT_STATUS_NONE' , 0); // No status
define('PAYMENT_STATUS_COMPLETE' , 1); // Money received, DM sent to user


// Log system codes
define('LOG_DEFAULT', 0); // Код по умолчанию
// 1xx - Информационные коды
define('LOG_INFORMATION', 100);
define('LOG_INFO_DM_CHANGE', 102); // Изменение количества Тёмной Материи
define('LOG_INFO_DB_CHANGE', 103); // Изменение структуры БД
define('LOG_INFO_UNI_RENAME', 104); // Переименование объекта Вселенной
define('LOG_INFO_PREMIUM_CANCEL', 105); // Пользователь отменил премиум аккаунт
define('LOG_INFO_STAT_START', 190); // Запуск обновления статистики игроков
define('LOG_INFO_STAT_PROCESS', 191); // Сообщения системы обновления статистики
define('LOG_INFO_STAT_FINISH', 192); // Обновление статистики игроков выполнено успешно
// 2xx - Операция завершена успешно
define('LOG_OK', 200); // OK
//define('LOG_OK_CREATED', 201); // Created
//define('LOG_OK_ACCEPTED', 202); // Accepted
//define('LOG_OK_NON_AUTHORITATE', 203); // Non-Authoritative Information
//define('LOG_OK_NOTHING', 204); // No Content
//define('LOG_OK_RESET', 205); // Reset Content
//define('LOG_OK_PARTIAL', 206); // Partial Content
// 3xx - Предупреждения системы логов
define('LOG_WARN', 300); // Общий код по умолчанию
define('LOG_WARN_BUGUSE', 301); // Возможный багоюз. Когда-то данное действие пользователя приводило к ошибке, дающей ему неоправданное преимущество в игре (удвоение флота, бесплатная или моментальная постройка итд), т.е. являлось хаком. Сейчас эта ошибка устранена, но стоит присмотрется к пользователю - возможно он багоюзер или хакер.
define('LOG_WARN_NO_USER', 303); // Пользователь не найден в системе. Это может означать, что пользователь на странице логина или регестрируется. Или это может означать ошибку
define('LOG_WARN_MULTICOLONIZE', 304); // Колонизация с несколькими кораблями
define('LOG_WARN_HACK', 302); // Попытка взлома. Пользователь передал серверу данные, не совпадаю реальными (например - другой ID пользователя вместо своего, другой ID альянса, вместо своего итд). Обычно это означает попытку взлома. Очень редко это может означать о наличии ошибки в коде игры
define('LOG_WARN_HACK_NEGATIVE_RESOURCE', 305); // Попытка взлома. Пользователь передал отрицательное количество ресурсов на странице Чёрный Рынок->Торговец ресурсами. В нормальных условиях это сделать невозможно. До фикса это приводило к увеличению ресурса на указанную величину.
define('LOG_WARN_HACK_WRONG_UNIT', 306); // Попытка взлома. Пользователь передал в списке кораблей на странице Чёрный Рынок не-корабль. В нормальных условиях это сделать невозможно.
define('LOG_WARN_HACK_NEGATIVE_UNIT', 307); // Попытка взлома. Пользователь передал отрицательное количество кораблей на странице Чёрный Рынок. В нормальных условиях это сделать невозможно.
define('LOG_WARN_BIG_BROTHER', 399); // Запись системы слежения за игроками
// 4xx - Ошибки при запросе клиента
define('LOG_ERR_UNAUTHORIZED', 401); // Unauthorized. Пользователь попытался получить доступ к части сайта, не доступной для его уровня доступа
define('LOG_ERR_DARK_MATTER', 402); // Ошибка изменения количества Тёмной Материи
define('LOG_ERR_FORBIDDEN', 403); // Forbidden. Попытка взлома - пользователь попытался выполнять отдельный файл вне нормального хода операции. Например - попытался выполнить файл, предназначенный только для include
// 5xx - Ошибки сервера - сбой в БД или ошибки в коде движка
define('LOG_ERR_INT', 500); // Ошибки сервера - сбой в БД или ошибки в коде движка
define('LOG_ERR_INT_NEGATIVE_RESOURCE', 501); // У игрока отрицательное количество ресурсов
define('LOG_ERR_INT_NO_PLANET', 502); // Ошибка записи пользователя: у пользователя в качестве родного мира назначена несуществующая планета
define('LOG_ERR_INT_ORPHANE_PLANET', 503); // У планеты нет хозяина
define('LOG_ERR_INT_FLEET_TIMOUT', 504); // Таймаут менеджера флотов

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
define('OPT_INTERFACE', 3);

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
define('ATTACK_ALLOWED'           ,  0);
define('ATTACK_NO_TARGET'         ,  1);
define('ATTACK_OWN'               ,  2);
define('ATTACK_WRONG_MISSION'     ,  3);
define('ATTACK_NO_ALLY_DEPOSIT'   ,  4);
define('ATTACK_NO_DEBRIS'         ,  5);
define('ATTACK_VACATION'          ,  6);
define('ATTACK_SAME_IP'           ,  7);
define('ATTACK_BUFFING'           ,  8);
define('ATTACK_ADMIN'             ,  9);
define('ATTACK_NOOB'              , 10);
define('ATTACK_OWN_VACATION'      , 11);
define('ATTACK_NO_SILO'           , 12);
define('ATTACK_NO_MISSILE'        , 13);
define('ATTACK_NO_FLEET'          , 14);
define('ATTACK_NO_SLOTS'          , 15);
define('ATTACK_NO_SHIPS'          , 16);
define('ATTACK_NO_RECYCLERS'      , 17);
define('ATTACK_NO_SPIES'          , 18);
define('ATTACK_NO_COLONIZER'      , 19);
define('ATTACK_MISSILE_TOO_FAR'   , 20);
define('ATTACK_WRONG_STRUCTURE'   , 21);
define('ATTACK_NO_FUEL'           , 22);
define('ATTACK_NO_RESOURCES'      , 23);
define('ATTACK_NO_ACS'            , 24);
define('ATTACK_ACS_MISSTARGET'    , 25);
define('ATTACK_WRONG_SPEED'       , 26);
define('ATTACK_ACS_TOO_LATE'      , 27);
define('ATTACK_BASHING'           , 28);
define('ATTACK_BASHING_WAR_DELAY' , 29);
define('ATTACK_ACS_WRONG_TARGET'  , 30);
define('ATTACK_SAME'              , 31);
define('ATTACK_RESOURCE_FORBIDDEN', 32);
define('ATTACK_TRANSPORT_EMPTY'   , 33);


// *** Market variables
// === Market blocks
define('MARKET_ENTRY'        , 0);
define('MARKET_RESOURCES'    , 1);
define('MARKET_SCRAPPER'     , 2);
define('MARKET_STOCKMAN'     , 3);
define('MARKET_EXCHANGE'     , 4);
define('MARKET_BANKER'       , 5);
define('MARKET_PAWNSHOP'     , 6);
define('MARKET_INFO'         , 7);

// === Market error statuses
define('MARKET_NOTHING'              ,  0);
define('MARKET_DEAL'                 ,  1);
define('MARKET_DEAL_TRADE'           ,  2);
define('MARKET_NO_DM'                ,  3);
define('MARKET_NO_RESOURCES'         ,  4);
define('MARKET_ZERO_DEAL'            ,  5);
define('MARKET_NO_SHIPS'             ,  6);
define('MARKET_NOT_A_SHIP'           ,  7);
define('MARKET_NO_STOCK'             ,  8);
define('MARKET_ZERO_RES_STOCK'       ,  9);
define('MARKET_NEGATIVE_SHIPS'       , 10);

define('MARKET_INFO_PLAYER'          , 12);
define('MARKET_INFO_WRONG'           , 11);
define('MARKET_INFO_PLAYER_NOT_FOUND', 13);
define('MARKET_INFO_PLAYER_WRONG'    , 14);
define('MARKET_INFO_PLAYER_SAME'     , 15);




// *** Mercenary/talent bonus types
define('BONUS_NONE'    ,            0);  // No bonus
define('BONUS_PERCENT' ,            1);  // Percent on base value
define('BONUS_ADD'     ,            2);  // Add
define('BONUS_ABILITY' ,            3);  // Some ability
define('BONUS_MULTIPLY',            4);  // Multiply by value
define('BONUS_PERCENT_CUMULATIVE' , 5);  // Cumulative percent on base value
define('BONUS_PERCENT_DEGRADED' ,   6);  // Bonus amount degraded with increase as pow(bonus, level) (?)
define('BONUS_SPEED',               7);  // Speed bonus

// *** Action constat (build should be replaced with ACTION)
define('BUILD_CREATE' ,  1);
define('BUILD_DESTROY', -1);

define('ACTION_SELL'       , -1);
define('ACTION_NOTHING'    ,  0);
define('ACTION_BUY'        ,  1);
define('ACTION_USE'        ,  2);
define('ACTION_DELETE'     ,  3);

// *** Check unit availability codes
define('BUILD_ALLOWED'         , 0); // HARDCODE! DO NOT CHANGE!
define('BUILD_REQUIRE_NOT_MEET', 1);
define('BUILD_AMOUNT_WRONG'    , 2);
define('BUILD_QUE_WRONG'       , 3);
define('BUILD_QUE_UNIT_WRONG'  , 4);
define('BUILD_INDESTRUCTABLE'  , 5);
define('BUILD_NO_RESOURCES'    , 6);
define('BUILD_NO_UNITS'        , 7);


// *** Que types
define('QUE_STRUCTURES', 1);
define('QUE_HANGAR'    , 4);
define('QUE_RESEARCH'  , 7);

// *** Que items
define('QI_UNIT_ID'   , 0);
define('QI_AMOUNT'    , 1);
define('QI_TIME'      , 2);
define('QI_MODE'      , 3);
define('QI_QUE_ID'    , 4);
define('QI_PLANET_ID' , 5);

// *** Subque types
define('SUBQUE_PLANET'  , 1);
define('SUBQUE_MOON'    , 3);
define('SUBQUE_FLEET'   , 4);
define('SUBQUE_DEFENSE' , 6);
define('SUBQUE_RESEARCH', 7);

// *** Units

// *** Sort options
define('SORT_ASCENDING' , 0);
define('SORT_DESCENDING', 1);

define('SORT_ID'              , 0);
define('SORT_LOCATION'        , 1);
define('SORT_NAME'            , 2);
define('SORT_SIZE'            , 3);
define('SORT_EMAIL'           , 4);
define('SORT_IP'              , 5);
define('SORT_TIME_REGISTERED' , 6);
define('SORT_TIME_LAST_VISIT' , 7);
define('SORT_TIME_BAN_UNTIL'  , 8);

// === Structures
define('UNIT_STRUCTURES', 0);
define('UNIT_STRUCTURES_SPECIAL', 40);
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
define('UNIT_TECHNOLOGIES', 100);
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
define('UNIT_SHIPS', 200);
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
define('UNIT_DEFENCE', 400);
define('DEF_TURRET_MISSILE', 401);
define('DEF_TURRET_LASER_SMALL', 402);
define('DEF_TURRET_LASER_BIG', 403);
define('DEF_TURRET_GAUSS', 404);
define('DEF_TURRET_ION', 405);
define('DEF_TURRET_PLASMA', 406);
define('UNIT_DEF_SHIELD_SMALL', 407);
define('UNIT_DEF_SHIELD_BIG', 408);
define('UNIT_DEF_SHIELD_PLANET', 409);
// --- Missiles
define('DEF_MISSILE_INTERCEPTOR', 502);
define('DEF_MISSILE_INTERPLANET', 503);

// === Mercenaries
// --- Mercenary list
define('UNIT_MERCENARIES', 600);
define('MRC_ACADEMIC', 606);
define('MRC_ADMIRAL', 602);
define('MRC_STOCKMAN', 607);
define('MRC_SPY', 610);
define('MRC_COORDINATOR', 611);
define('MRC_DESTRUCTOR', 612);
define('MRC_NAVIGATOR', 613);
define('MRC_ASSASIN', 614);
define('MRC_EMPEROR', 615);

// --- Governors list
define('UNIT_GOVERNORS', 680);
define('MRC_TECHNOLOGIST', 601);
define('MRC_ENGINEER', 605);
define('MRC_FORTIFIER', 608);

// Bonus category
define('BONUS_SERVER', 0);
define('BONUS_MERCENARY', UNIT_MERCENARIES); // DO NOT MOVE ABOVE MERCENARIES SECTION!

// === Resources
define('UNIT_RESOURCES', 900);
define('RES_METAL', 901);
define('RES_CRYSTAL', 902);
define('RES_DEUTERIUM', 903);
define('RES_ENERGY', 904);
define('RES_DARK_MATTER', 905);
define('RES_TIME', 999);

// === Artifacts
define('UNIT_ARTIFACTS', 1000);
define('ART_LHC', 1001);      // Additional moon chance
define('ART_RCD_SMALL', 1002);   // Rapid Colony Deployment - Set of buildings up to 10th level - 10/14/ 3/0 -   405 DM
define('ART_RCD_MEDIUM', 1003);  // Rapid Colony Deployment - Set of buildings up to 15th level - 15/20/ 8/0 -  4704 DM
define('ART_RCD_LARGE', 1004);   // Rapid Colony Deployment - Set of buildings up to 20th level - 20/25/10/1 - 39790 DM
//define('ART_SUPERCOMPUTER', 1005); // Speed up research
//define('ART_PLANET_GATE', 1006);   // Planet gate
//define('ART_NANOBOTS_SMALL', 1007); // Speed up building

// === Blueprints
define('UNIT_PLANS', 1100);
define('UNIT_PLAN_STRUC_MINE_FUSION', 1101);
define('UNIT_PLAN_SHIP_CARGO_SUPER', 1102);
define('UNIT_PLAN_SHIP_CARGO_HYPER', 1103);
define('UNIT_PLAN_SHIP_DEATH_STAR', 1104);
define('UNIT_PLAN_SHIP_SUPERNOVA', 1105);
define('UNIT_PLAN_DEF_SHIELD_PLANET', 1106);

define('UNIT_PREMIUM', 1200);
define('UNIT_SECTOR', 1300);

// === Unit types // TODO - convert to GROUP_xxx constants
define('UNIT_STRUCTURE', 0);
define('UNIT_RESEARCH' , 1);
define('UNIT_SHIP'     , 2);
define('UNIT_DEFENSE'  , 4);
define('UNIT_MISSILE'  , 5);
define('UNIT_MERCENARY', 6);
define('UNIT_RESOURCE' , 9);


define('SNC_VER_NEVER', -1);
define('SNC_VER_ERROR_CONNECT', 0);
define('SNC_VER_EXACT', 1);
define('SNC_VER_LESS', 2);
define('SNC_VER_FUTURE', 3);
define('SNC_VER_RELEASE_EXACT', 10);
define('SNC_VER_RELEASE_MINOR', 11);
define('SNC_VER_RELEASE_MAJOR', 12);
define('SNC_VER_RELEASE_ALPHA', 13);
define('SNC_VER_MAINTENANCE', 96);
define('SNC_VER_UNKNOWN_RESPONSE', 97);
define('SNC_VER_INVALID', 98);
define('SNC_VER_STRANGE', 99);
define('SNC_VER_ERROR_SERVER', 100);

define('SNC_VER_REGISTER_UNREGISTERED', 200);
define('SNC_VER_REGISTER_ERROR_MULTISERVER', 201);
define('SNC_VER_REGISTER_ERROR_REGISTERED', 202);
define('SNC_VER_REGISTER_ERROR_NO_NAME', 203);
define('SNC_VER_REGISTER_ERROR_WRONG_URL', 204);
define('SNC_VER_REGISTER_REGISTERED', 299);

define('SNC_VER_ERROR_INCOMPLETE_REQUEST', 301);
define('SNC_VER_ERROR_UNKNOWN_KEY', 302);
define('SNC_VER_ERROR_MISSMATCH_KEY_ID', 303);

define('SNC_MODE_VERSION_CHECK', 0);
define('SNC_MODE_REGISTER', 1);
define('SNC_MODE_CHANGELOG', 2);

define('ALI_BONUS_BY_PLAYERS', 0);
define('ALI_BONUS_BY_SIZE', 1);
define('ALI_BONUS_BY_POINTS', 2);
define('ALI_BONUS_BY_RANK', 3);

// Admin tools constants
define('ADM_TOOL_CONFIG_RELOAD', 1);
define('ADM_TOOL_MD5', 2);
define('ADM_TOOL_FORCE_ALL', 3);
define('ADM_TOOL_FORCE_LAST', 4);
define('ADM_TOOL_INFO_PHP', 5);
define('ADM_TOOL_INFO_MYSQL', 6);

define('STAT_TOTAL', 0);
define('STAT_FLEET', 1);
define('STAT_TECH', 2);
define('STAT_BUILDING', 3);
define('STAT_DEFENSE', 4);
define('STAT_RESOURCE', 5);
define('STAT_RAID_TOTAL', 6);
define('STAT_RAID_WON', 7);
define('STAT_RAID_LOST', 8);
define('STAT_LVL_BUILDING', 9);
define('STAT_LVL_TECH', 10);
define('STAT_LVL_RAID', 11);

?>
