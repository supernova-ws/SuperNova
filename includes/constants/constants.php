<?php

if(defined('__SN_CONSTANTS_DEFINED') && __SN_CONSTANTS_DEFINED === true) {
  return;
}

define('__SN_CONSTANTS_DEFINED', true);

defined('INSIDE') or die('Hacking attempt');

define('DB_VERSION_MIN', '40'); // Minimal supported version of DB
define('DB_VERSION', '45');
define('SN_RELEASE', '45');
define('SN_VERSION', '45d2');
define('SN_RELEASE_STABLE', '45d0'); // Latest stable release

define('SN_TIME_NOW', intval(SN_TIME_MICRO));
define('SN_TIME_ZONE_OFFSET', date('Z'));

define('FMT_DATE_SQL', 'Y-m-d');
define('FMT_TIME_SQL', 'H:i:s');
define('FMT_DATE_TIME_SQL', 'Y-m-d H:i:s');
define('SN_TIME_SQL', date(FMT_DATE_TIME_SQL, SN_TIME_NOW));
define('SN_TODAY_SQL', date('Y-m-d', SN_TIME_NOW));
define('SN_TODAY_UNIX', strtotime(SN_TODAY_SQL));

const SN_DATE_PREHISTORIC_SQL = '2000-01-01';
define('SN_DATE_PREHISTORIC_UNIX', strtotime(SN_DATE_PREHISTORIC_SQL));

define('SN_TIME_NOW_GMT_STRING', gmdate(DATE_ATOM, SN_TIME_NOW));

// Getting relative HTTP root to game resources
// I.e. in https://server.com/supernova/index.php SN_ROOT_RELATIVE will become '/supernova/'
// It needed to make game work on sub-folders and do not mess with cookies
// Not very accurate - heavily relies on filesystem paths and may fail on complicate web server setups
$sn_root_relative = str_replace(array('\\', '//'), '/', getcwd() . '/');
$sn_root_relative = str_replace(SN_ROOT_PHYSICAL, '', $sn_root_relative);
$sn_root_relative = $sn_root_relative . basename($_SERVER['SCRIPT_NAME']);
// Removing script name to obtain HTTP root
define('SN_ROOT_RELATIVE', str_replace($sn_root_relative, '', $_SERVER['SCRIPT_NAME']));

$_server_http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
define('SN_ROOT_VIRTUAL', 'http' . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $_server_http_host . SN_ROOT_RELATIVE);

$_server_server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
if(strpos(strtolower($_server_server_name), 'google.') !== false) {
  define('SN_GOOGLE', true);
}
define('SN_ROOT_VIRTUAL_PARENT', str_replace('//google.', '//', SN_ROOT_VIRTUAL));

define('FLEET_ID_TEMPLATE', 'f%sown');

// PHP extension on this server
define('PHP_EX', strpos($temp = substr(strrchr(__FILE__, '.'), 1), '/') === false ? $temp : 'php');
// Dotted PHP extension on this server
define('DOT_PHP_EX', '.' . PHP_EX);

define(
  'INITIAL_PAGE',
  isset($_GET['page'])
    ? trim(strip_tags($_GET['page']))
    : str_replace(DOT_PHP_EX, '', str_replace(SN_ROOT_RELATIVE, '', str_replace('\\', '/', $_SERVER['SCRIPT_NAME'])))
);

require_once "constants_params.php";
require_once "constants_units.php";
require_once "constants_rpg.php";
require_once "constants_ube.php";
require_once "constants_payments.php";

// Game type constants starts with GAME_
define('GAME_SUPERNOVA', 0);
define('GAME_OGAME'    , 1);
define('GAME_BLITZ'    , 2);

// Date & time range constants
define('DATE_FOREVER', 2000000000);

define('PERIOD_MINUTE', 60);
define('PERIOD_HOUR', PERIOD_MINUTE * 60);
define('PERIOD_DAY', PERIOD_HOUR * 24);
define('PERIOD_WEEK', PERIOD_DAY * 7);
define('PERIOD_MONTH', PERIOD_DAY * 30);
define('PERIOD_YEAR', PERIOD_DAY * 365);
define('PERIOD_FOREVER', PERIOD_YEAR * 100);

define('PERIOD_MINUTE_2' , PERIOD_MINUTE * 2);
define('PERIOD_MINUTE_3' , PERIOD_MINUTE * 3);
define('PERIOD_MINUTE_5' , PERIOD_MINUTE * 5);
define('PERIOD_MINUTE_10', PERIOD_MINUTE * 10);
define('PERIOD_MINUTE_15', PERIOD_MINUTE * 15);
define('PERIOD_DAY_3'    , PERIOD_DAY * 3);
define('PERIOD_WEEK_2'   , PERIOD_WEEK * 2);
define('PERIOD_WEEK_4'   , PERIOD_WEEK * 4);
define('PERIOD_MONTH_2'  , PERIOD_MONTH * 2);
define('PERIOD_MONTH_3'  , PERIOD_MONTH * 3);

define('FONT_SIZE_PERCENT_MIN', 56.25);
define('FONT_SIZE_PERCENT_DEFAULT', 68.75);
define('FONT_SIZE_PERCENT_MAX', 131.25);
define('FONT_SIZE_PERCENT_STEP', 12.5);
define('FONT_SIZE_PERCENT_DEFAULT_STRING', FONT_SIZE_PERCENT_DEFAULT . '%');

define('FONT_SIZE_PIXELS_BROWSER_BASE', 16);
define('FONT_SIZE_PIXELS_MIN', 9);
define('FONT_SIZE_PIXELS_DEFAULT', 11);
define('FONT_SIZE_PIXELS_MAX', 21);
define('FONT_SIZE_PIXELS_STEP', 1);
define('FONT_SIZE_PIXELS_DEFAULT_STRING', FONT_SIZE_PIXELS_DEFAULT . 'px');

define('DEFAULT_PICTURE_EXTENSION_DOTTED', '.jpg');

// Operation error status HARDCODED!
define('ERR_NONE'               , 0); // No error
define('ERR_WARNING'            , 1); // There is warning - something altering normal operation process
define('ERR_ERROR'              , 2); // There is error - something permits operation from process
define('ERR_HACK'               , 4); // Operation is qualified as hack attempt
define('ERR_NOTICE'             , 8); // There is notice - nothing really critical but operator should know
// New GLOBAL operation results
//define('RESULT_DEFAULT' , 0); // Default result - all went OK or result really doesn't matter
//define('RESULT_WARNING' , 1);
//define('RESULT_ERROR'   , 2);
//define('RESULT_HACKING' , 3);
//define('RESULT_PAYMENT_ERR_REQUEST_UNSUPPORTED', 5);


// ****************************************************************************************************************
// SHOULD BE REPLACED WITH CONFIG!
define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
define('MAX_ATTACK_ROUNDS', 10);
//define('MAX_OVERFLOW', 1);
define('BASE_STORAGE_SIZE', 500000);
define('HIDE_1ST_FROM_STATS', 0);
define('STATS_RUN_INTERVAL_MINIMUM', PERIOD_MINUTE_10); // Minimal interval to run stat updates
define('HIDE_BUILDING_RECORDS', 0);
define('SHOW_ADMIN', 1);

define('UNIVERSE_RANDOM_PLANET_START', 16); // Позиция начала рандомизации планет
define('UNIVERSE_RANDOM_PLANET_TEMPERATURE_DECREASE', 5); // Шаг тзменения минимальной температуры рандомной планеты

define('PLANET_DENSITY_TO_DARK_MATTER_RATE', 10);

// Pattern to parse planet coordinates like [1:123:14] - no expedition [x:x:16] will pass!
define('PLANET_COORD_PREG', '/^\[([1-9]):([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]):(1[0-5]|[1-9])\]$/i');
// Pattern to parse scheduler '[[[[[YYYY-]MM-]DD ]HH:]MM:]SS'
define('SCHEDULER_PREG', '/^(?:(?:(?:(?:(?:(2\d\d\d)-)?(1[0-2]|0[1-9])-)?(?:(3[01]|[0-2]\d)\ ))?(?:(2[0-3]|[01]\d):))?(?:([0-5]\d):))?([0-5]\d)$/i');
define('SCHEDULER_PREG2', '/^(?:\w\@)?(?:(?:(?:(?:(?:(\d*)-)?(\d*)-)?(?:(\d*)\ ))?(?:(\d*):))?(?:(\d*):))?(\d*)?$/i');
define('PREG_DATE_SQL', '/(20[1-9][0-9])\-(1[0-2]|0[1-9])\-(3[01]|[12]\d|0[1-9]) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])/');
define('PREG_DATE_SQL_FULL', '/(20[1-9][0-9]|19[0-9][0-9])\-(1[0-2]|0[1-9])\-(3[01]|[12]\d|0[1-9]) (2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])/');
define('PREG_DATE_SQL_RELAXED', '/(20[1-9][0-9])(?:\-(1[0-2]|0[1-9])(?:\-(3[01]|[12]\d|0[1-9])(?: (2[0-3]|[01][0-9])(?::([0-5][0-9])(?::([0-5][0-9]))?)?)?)?)?/');

define('LOGIN_PASSWORD_RESET_CONFIRMATION_LENGTH', 6);
define('SN_SYS_SEC_CHARS_CONFIRMATION', '0123456789');
define('AUTH_PASSWORD_RESET_CONFIRMATION_EXPIRE', PERIOD_DAY);
define('AUTH_PLAYER_NAME_LENGTH', 32);

// define('LOGIN_REGISTER_CHARACTERS_PROHIBITED', '/\\ |^&\'?"`<>[]{}()%');
define('LOGIN_REGISTER_CHARACTERS_PROHIBITED', "`'\"\\/ |^&?<>[]{}()%;\n\r\t\v\f\x00\x1a");

// Default allowed chars for random string
define('SN_SYS_SEC_CHARS_ALLOWED', 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghkmnpqrstuvwxyz0123456789');

// Mot qui sont interdit a la saisie !
global $ListCensure;
$ListCensure = array ( '/</', '/>/', '/script/i', '/doquery/i', '/http/i', '/javascript/i');

// Confirmation record types
define('CONFIRM_REGISTRATION'  , 1);
define('CONFIRM_PASSWORD_RESET', 2);
define('CONFIRM_DELETE'        , 3);

define('AFFILIATE_MM_TO_REFERRAL_DM', 2);

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

define('QUEST_STATUS_EXCEPT_COMPLETE' , -2);
define('QUEST_STATUS_ALL' , -1);
define('QUEST_STATUS_NOT_STARTED' , 0);
define('QUEST_STATUS_STARTED'     , 1);
define('QUEST_STATUS_COMPLETE'    , 2);

define('TYPE_EMPTY', '');
define('TYPE_INTEGER', 'integer');
define('TYPE_DOUBLE', 'double');
define('TYPE_FLOAT', 'double'); // Just a nickname to match PHP used type 'float'
define('TYPE_BOOLEAN', 'boolean');
define('TYPE_NULL', 'NULL');
define('TYPE_ARRAY', 'array');
define('TYPE_STRING', 'string');

// *** Combat-related constants
// *** Mission Type constants starts with MT_
define('MT_NONE'     ,  0);
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
define('PT_NONE', 0);
define('PT_ALL', 0);
define('PT_PLANET', 1);
define('PT_DEBRIS', 2);
define('PT_MOON'  , 3);

// *** Unit locations - shows db table where unit belong
// Also cache indexes
define('LOC_AUTODETECT', -2);
define('LOC_NONE',    -1); // Deprecated
define('LOC_UNIVERSE', 0);
define('LOC_PLANET',   1);
define('LOC_DEBRIS',   2); // Translates to `planets` table planet_type = 1, `debris_*` fields
define('LOC_MOON',     3); // Translates to `planets` table planet_type = 3
define('LOC_PLAYER',   4);
define('LOC_USER',     LOC_PLAYER); // Deprecated alias for LOC_PLAYER
define('LOC_FLEET',    5);
define('LOC_ALLY',     6);
define('LOC_SERVER',   7); // Located on server

// ТОЛЬКО ВНУТРЕНЕЕ!!!
define('LOC_UNIT',    'LOC_UNIT');
define('LOC_QUE',     'LOC_QUE');
define('LOC_LOCATION','LOC_LOCATION');
define('LOC_LOCKS','LOC_LOCKS');

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
define('EVENT_FLEET_NONE', 0);
define('EVENT_FLEET_ARRIVE', 1);
define('EVENT_FLEET_STAY'  , 2);
define('EVENT_FLEET_RETURN', 3);


// Log system codes
define('LOG_DEFAULT', 0); // Код по умолчанию
// 1xx - Информационные коды
define('LOG_INFORMATION', 100);
define('LOG_INFO_DM_CHANGE', 102); // Изменение количества Тёмной Материи
define('LOG_INFO_DB_CHANGE', 103); // Изменение структуры БД
define('LOG_INFO_UNI_RENAME', 104); // Переименование объекта Вселенной
define('LOG_INFO_PREMIUM_CANCEL', 105); // Пользователь отменил премиум аккаунт
define('LOG_INFO_PAYMENT', 110); // Записи системы платежей
define('LOG_INFO_MAINTENANCE', 180); // Записи системы платежей
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
define('LOG_ERR_INT_CAPTAIN_DUPLICATE', 505); // Таймаут менеджера флотов
define('LOG_ERR_INT_NOT_ENOUGH_DARK_MATTER', 506); // Ошибка снятия ТМ
// 9xx - Отладка
// define('LOG_DEBUG', 900); // Отладка Странный глюк - не хочет делать define LOG_DEBUG без ошибки!
define('LOG_DEBUG_SQL', 910); // Отладка SQL



define('PASSWORD_LENGTH_MIN', 4);
define('LOGIN_LENGTH_MIN', 4);

define('AUTH_COOKIE_DELIMETER', '_');
define('AUTH_COOKIE_IMPERSONATE_SUFFIX', '_I');

define('AUTH_FEATURE_EMAIL_CHANGE', 1);
define('AUTH_FEATURE_PASSWORD_RESET', 2);
define('AUTH_FEATURE_FORCE_PLAYER_NAME_SELECT', 3);
define('AUTH_FEATURE_PASSWORD_CHANGE', 4);
define('AUTH_FEATURE_HAS_PASSWORD', 5);

// Login statuses
define('LOGIN_UNDEFINED', 0);
define('LOGIN_SUCCESS', 1);
// define('LOGIN_SUCCESS_CREATE_PROFILE', 2);
define('LOGIN_ERROR_PASSWORD', 3);
define('LOGIN_ERROR_USERNAME', 4);
// define('LOGIN_ERROR_ACTIVE'          , 5);
// define('LOGIN_ERROR_EXTERNAL_AUTH'   , 6);
// define('LOGIN_ERROR_COOKIE'          , 7);

define('REGISTER_ERROR_USERNAME_WRONG', 8);
define('REGISTER_ERROR_ACCOUNT_NAME_EXISTS', 9);
define('REGISTER_ERROR_PASSWORD_INSECURE', 10);
define('REGISTER_SUCCESS', 11);

define('PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS', 12);
define('PASSWORD_RESTORE_ERROR_TOO_OFTEN', 13);
define('PASSWORD_RESTORE_SUCCESS_CODE_SENT', 14);
define('PASSWORD_RESTORE_ERROR_SENDING', 15);
define('PASSWORD_RESTORE_ERROR_CODE_WRONG', 16);
define('PASSWORD_RESTORE_ERROR_CODE_TOO_OLD', 17);

define('AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE', 18);
define('PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT', 19);
define('PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR', 20);
define('REGISTER_ERROR_PASSWORD_DIFFERENT', 21);
define('REGISTER_ERROR_EMAIL_EXISTS', 22);
define('REGISTER_ERROR_BLITZ_MODE', 23);
define('LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS', 24);
define('LOGIN_ERROR_USERNAME_EMPTY', 25);
define('LOGIN_ERROR_PASSWORD_EMPTY', 26);
define('LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION', 27);
define('LOGIN_ERROR_USERNAME_ALLY_OR_BOT', 28);
define('LOGIN_ERROR_PASSWORD_TRIMMED', 29);
define('REGISTER_ERROR_EMAIL_EMPTY', 30);
define('REGISTER_ERROR_EMAIL_WRONG', 31);
define('REGISTER_ERROR_USERNAME_SHORT', 32);

define('IMPERSONATOR_OK', 33);

define('REGISTER_ERROR_ACCOUNT_CREATE', 34);

define('PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT', 35);
define('PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS', 36);
define('AUTH_LOGIN_INSIDE_ERROR_ACCOUNT_NOT_EXISTS', 37);
define('AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION', 38);

define('REGISTER_ERROR_PLAYER_NAME_TRIMMED', 39);
define('REGISTER_ERROR_PLAYER_NAME_EMPTY', 40);
define('REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS', 41);
define('REGISTER_ERROR_PLAYER_NAME_SHORT', 42);
define('REGISTER_ERROR_PLAYER_NAME_EXISTS', 43);

define('LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET', 44);
define('PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL', 45);
define('PASSWORD_RESTORE_ERROR_CODE_EMPTY', 46);

define('REGISTER_EXTERNAL_AUTH_ERROR', 1000);






//throw new exception(REGISTER_ERROR_PLAYER_NAME_TRIMMED, ERR_ERROR);
//throw new exception(REGISTER_ERROR_PLAYER_NAME_EMPTY, ERR_ERROR);
//throw new exception(REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS, ERR_ERROR);
//throw new exception(REGISTER_ERROR_PLAYER_NAME_SHORT, ERR_ERROR);
//throw new exception(REGISTER_ERROR_PLAYER_NAME_EXISTS, ERR_ERROR);


define('AUTH_LEVEL_ANONYMOUS', -10);
define('AUTH_LEVEL_GUEST', -5);
define('AUTH_LEVEL_REGISTERED', 0);
define('AUTH_LEVEL_MODERATOR', 1);
define('AUTH_LEVEL_OPERATOR', 2);
define('AUTH_LEVEL_ADMINISTRATOR', 3);
define('AUTH_LEVEL_DEVELOPER', 4);
define('AUTH_LEVEL_SYSTEM', 99);

define('ACCOUNT_PROVIDER_NONE', 0);
define('ACCOUNT_PROVIDER_LOCAL', 1);
define('ACCOUNT_PROVIDER_CENTRAL', 2);
define('ACCOUNT_PROVIDER_VKONTAKTE', 3);

// F_INPUT - constants
// define('F_INPUT',                      'F_INPUT');
// define('F_IS_REGISTER',                'F_IS_REGISTER');
// define('F_LOGIN_UNSAFE',               'F_LOGIN_UNSAFE');
//define('F_LOGIN_SAFE',                 'F_LOGIN_SAFE');
//define('F_LOGIN_PASSWORD_RAW',         'F_LOGIN_PASSWORD_RAW');
//define('F_LOGIN_PASSWORD_RAW_TRIMMED', 'F_LOGIN_PASSWORD_RAW_TRIMMED');
// define('F_LOGIN_PASSWORD_REPEAT_RAW',  'F_LOGIN_PASSWORD_REPEAT_RAW');
// define('F_EMAIL_UNSAFE',               'F_EMAIL_UNSAFE');
// define('F_LANGUAGE_SAFE',              'F_LANGUAGE_SAFE');
// define('F_REMEMBER_ME_SAFE',           'F_REMEMBER_ME_SAFE');

// define('F_PASSWORD_RESET_CODE_SAFE',        'F_PASSWORD_RESET_CODE');

define('F_HIDDEN', 'F_HIDDEN');

// Global template_result fields
define('AUTH_LEVEL', 'AUTH_LEVEL');

//define('F_DEVICE_ID',     'F_DEVICE_ID');
//define('F_DEVICE_CYPHER', 'F_DEVICE_CYPHER');

define('F_PROVIDER_ID',   'F_PROVIDER_ID');
// define('F_PROVIDER_LIST', 'F_PROVIDER_LIST');

define('F_IMPERSONATE_STATUS', 'F_IMPERSONATE_STATUS');
define('F_IMPERSONATE_OPERATOR', 'F_IMPERSONATE_OPERATOR');

define('F_LOGIN_STATUS',  'F_LOGIN_STATUS');
define('F_LOGIN_MESSAGE', 'F_LOGIN_MESSAGE');

define('F_PLAYER_REGISTER_STATUS',  'F_PLAYER_REGISTER_STATUS');
define('F_PLAYER_REGISTER_MESSAGE', 'F_PLAYER_REGISTER_MESSAGE');

define('F_USER_ID', 'F_USER_ID');
define('F_USER', 'F_USER');
define('F_USER_IS_AUTHORIZED', 'F_USER_IS_AUTHORIZED');
define('F_ACCOUNT_IS_AUTHORIZED', 'F_ACCOUNT_IS_AUTHORIZED');


// define('F_ACCOUNT_ID', 'F_ACCOUNT_ID');
// define('F_ACCOUNT', 'F_ACCOUNT');
define('F_ACCOUNTS_AUTHORISED', 'F_ACCOUNTS_AUTHORISED');
// define('F_PASSWORD_MATCHED', 'F_ACCOUNT_PASSWORD_MATCH');


define('F_PASSWORD', 'F_PASSWORD');

define('F_LOGIN_SUGGESTED_NAME', 'F_LOGIN_SUGGESTED_NAME');

define('F_LOGIN_ACCOUNT', 'F_LOGIN_ACCOUNT');
define('F_LOGIN_ACCOUNT_NAME', 'F_LOGIN_ACCOUNT');
define('F_LOGIN_ACCOUNT_GLOBAL', 'F_LOGIN_ACCOUNT_GLOBAL');
define('F_PASSWORD_NEW', 'F_PASSWORD_NEW');

// define('F_BROWSER', 'F_BROWSER');
// define('F_BROWSER_ID', 'F_BROWSER_ID');

// define('F_PAGE', 'F_PAGE');
// define('F_PAGE_ID', 'F_PAGE_ID');
// define('F_URL', 'F_URL');
// define('F_URL_ID', 'F_URL_ID');

define('F_BANNED_STATUS', 'F_BANNED_STATUS');
define('F_BANNED_MESSAGE', 'F_BANNED_MESSAGE');

define('F_VACATION_STATUS', 'F_VACATION_STATUS');


define('F_GAME_DISABLE', 'F_GAME_DISABLE');
define('F_GAME_DISABLE_REASON', 'F_GAME_DISABLE_REASON');



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
define('ATTACK_SPIES_LONLY'       , 34);
define('ATTACK_TOO_FAR'           , 35);
define('ATTACK_OVERLOADED'        , 36);
define('ATTACK_MISSION_ABSENT'    , 37);
define('ATTACK_WRONG_UNIT'        , 38);
define('ATTACK_ZERO_SPEED'        , 39);
define('ATTACK_SHIP_COUNT_WRONG'  , 40);
define('ATTACK_RESOURCE_COUNT_WRONG', 41);
define('ATTACK_MORATORIUM', 42);
define('ATTACK_CHILD_PROTECTION', 43);
define('ATTACK_ACS_MAX_FLEETS', 44);


// *** Races - Homeworlds
define('RACE_NONE'    , 0);
define('RACE_EARTH'   , 1);
define('RACE_MOON'    , 2);
define('RACE_MERCURY' , 3);
define('RACE_VENUS'   , 4);
define('RACE_MARS'    , 5);
define('RACE_ASTEROID', 6);
// define('MARKET_INFO'         , 7);



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
//define('BONUS_PERCENT_CUMULATIVE' , 5);  // Cumulative percent on base value
//define('BONUS_PERCENT_DEGRADED' ,   6);  // Bonus amount degraded with increase as pow(bonus, level) (?)
//define('BONUS_SPEED',               7);  // Speed bonus

// *** Action constant (build should be replaced with ACTION)
define('BUILD_CREATE' ,  1);
define('BUILD_DESTROY', -1);
define('BUILD_AUTOCONVERT', 2);

define('ACTION_SELL'       , -1);
define('ACTION_NOTHING'    ,  0);
define('ACTION_BUY'        ,  1);
define('ACTION_USE'        ,  2);
define('ACTION_DELETE'     ,  3);

// *** Check unit availability codes
define('BUILD_ALLOWED'         , 0); // HARDCODED! DO NOT CHANGE!
define('BUILD_REQUIRE_NOT_MEET', 1);
define('BUILD_AMOUNT_WRONG'    , 2);
define('BUILD_QUE_WRONG'       , 3);
define('BUILD_QUE_UNIT_WRONG'  , 4);
define('BUILD_INDESTRUCTABLE'  , 5);
define('BUILD_NO_RESOURCES'    , 6);
define('BUILD_NO_UNITS'        , 7);
define('BUILD_UNIT_BUSY'       , 8);
define('BUILD_QUE_FULL'        , 9);
define('BUILD_SILO_FULL'       ,10);
define('BUILD_MAX_REACHED'     ,11);
define('BUILD_SECTORS_NONE'    ,12);
define('BUILD_AUTOCONVERT_AVAILABLE', 13);
define('BUILD_HIGHSPOT_NOT_ACTIVE', 14);


// *** Que types
define('QUE_STRUCTURES', 1);
define('QUE_HANGAR'    , 4);
define('QUE_RESEARCH'  , 7);
define('QUE_MERCENARY' , 600); // UNIT_MERCENARIES
// *** Subque types
define('SUBQUE_PLANET'  , 1);
define('SUBQUE_MOON'    , 3);
define('SUBQUE_FLEET'   , 4);
define('SUBQUE_DEFENSE' , 6);
define('SUBQUE_RESEARCH', 7);

// *** Que items
define('QI_UNIT_ID'   , 0);
define('QI_AMOUNT'    , 1);
define('QI_TIME'      , 2);
define('QI_MODE'      , 3);
define('QI_QUE_ID'    , 4);
define('QI_QUE_TYPE'  , 4);
define('QI_PLANET_ID' , 5);


// *** Units

// *** Sort options
define('SORT_ASCENDING' , 0);
define('SORT_DESCENDING', 1);

define('SORT_ID'             , 0);
define('SORT_LOCATION'       , 1);
define('SORT_NAME'           , 2);
define('SORT_SIZE'           , 3);
define('SORT_EMAIL'          , 4);
define('SORT_IP'             , 5);
define('SORT_TIME_REGISTERED', 6);
define('SORT_TIME_LAST_VISIT', 7);
define('SORT_TIME_BAN_UNTIL' , 8);
define('SORT_REFERRAL_COUNT' , 9);
define('SORT_REFERRAL_DM'    , 10);
define('SORT_VACATION'       , 11);


define('HULL_SIZE_TINY', 1);
define('HULL_SIZE_SMALL', 2);
define('HULL_SIZE_MEDIUM', 3);
define('HULL_SIZE_LARGE', 4);
define('HULL_SIZE_HUGE', 5);


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
define('ADM_TOOL_INFO_SQL', 6);
define('ADM_PTL_TEST', 7);
define('ADM_COUNTER_RECALC', 8);

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

define('CHAT_MODE_COMMON', 0);
define('CHAT_MODE_ALLY', 1);

define('BUDDY_REQUEST_WAITING', 0);
define('BUDDY_REQUEST_ACTIVE', 1);
define('BUDDY_REQUEST_DENIED', 2);

define('REQUIRE_MET_NOT', 0);
define('REQUIRE_MET', 1);


define('CHAT_OPTION_SWITCH', 1);

// Modifier constants
define('MODIFIER_NONE', 0);
define('MODIFIER_RESOURCE_CAPACITY', 1);
define('MODIFIER_RESOURCE_PRODUCTION', 2);
// define('MODIFIER_FUSION_OUTPUT', 3);

define('SQL_OP_DELETE', -1);
define('SQL_OP_UPDATE', 0);
define('SQL_OP_INSERT', 1);
define('SQL_OP_REPLACE', 3);


define('SERVER_PLAYER_NAME_CHANGE_NONE', 0);
define('SERVER_PLAYER_NAME_CHANGE_FREE', 1);
define('SERVER_PLAYER_NAME_CHANGE_PAY', 2);




define('FLT_EXPEDITION_OUTCOME_NONE', 0);
define('FLT_EXPEDITION_OUTCOME_LOST_FLEET', 1);
define('FLT_EXPEDITION_OUTCOME_FOUND_FLEET', 2);
define('FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES', 3);
define('FLT_EXPEDITION_OUTCOME_FOUND_DM', 4);
define('FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT', 5);
define('FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL', 6);

const FLT_EXPEDITION_OUTCOME_TYPE_BAD = -1;
const FLT_EXPEDITION_OUTCOME_TYPE_NEUTRAL = 0;
const FLT_EXPEDITION_OUTCOME_TYPE_GOOD = 1;

// Обязательно оставить, что бы arrive < accomplish < return
define('EVENT_FLT_ARRIVE', 1); // Fleet arrive to destination
define('EVENT_FLT_ACOMPLISH', 2); // Fleet ends his mission by timer
define('EVENT_FLT_RETURN', 3); // Fleet returns to starting planet



// define('NICK_ID',               -1);
define('NICK_HTML',              0);

define('NICK_FIRST',             1);
define('NICK_RACE',           1000);
define('NICK_GENDER',         2000);
define('NICK_AWARD',          3000);
define('NICK_VACATION',       3500);
define('NICK_BIRTHDAY',       4000);
define('NICK_RANK',           4500);
define('NICK_RANK_NO_TEXT',   4750);
define('NICK_PREMIUM',        5000);
define('NICK_AUTH_LEVEL',     6000);

define('NICK_HIGHLIGHT',      6300);
define('NICK_CLASS',          6450);

define('NICK_NICK_CLASS',     6600);
define('NICK_NICK',           7000);
define('NICK_NICK_CLASS_END', 7300);

define('NICK_ALLY_CLASS',     7600);
define('NICK_ALLY',           8000);
define('NICK_ALLY_CLASS_END', 8300);

define('NICK_CLASS_END',      8450);
define('NICK_HIGHLIGHT_END',  8600);

define('NICK_LAST',           9999);
define('NICK_SORT',          10000);

// Настройки игрока
define('PLAYER_OPTION_MENU_SORT', 1);
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON', 2);
define('PLAYER_OPTION_MENU_SHOW_ON_BUTTON', 3);
define('PLAYER_OPTION_MENU_HIDE_ON_BUTTON', 4);
define('PLAYER_OPTION_MENU_HIDE_ON_LEAVE', 5);
define('PLAYER_OPTION_MENU_UNPIN_ABSOLUTE', 6);
define('PLAYER_OPTION_MENU_ITEMS_AS_BUTTONS', 7);
define('PLAYER_OPTION_SOUND_ENABLED', 8);
define('PLAYER_OPTION_FLEET_SHIP_SORT', 9);
define('PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE', 10);
define('PLAYER_OPTION_CURRENCY_DEFAULT', 11);
define('PLAYER_OPTION_FLEET_SPY_DEFAULT', 12);
// define('PLAYER_OPTION_FLEET_MESS_AMOUNT_MAX', 13);
define('PLAYER_OPTION_UNIVERSE_ICON_SPYING', 14);
define('PLAYER_OPTION_UNIVERSE_ICON_MISSILE', 15);
define('PLAYER_OPTION_UNIVERSE_ICON_PM', 16);
define('PLAYER_OPTION_UNIVERSE_ICON_STATS', 17);
define('PLAYER_OPTION_UNIVERSE_ICON_PROFILE', 18);
define('PLAYER_OPTION_UNIVERSE_ICON_BUDDY', 19);
define('PLAYER_OPTION_PLANET_SORT', 20);
define('PLAYER_OPTION_PLANET_SORT_INVERSE', 21);
define('PLAYER_OPTION_TOOLTIP_DELAY', 22);
// define('PLAYER_OPTION_UNIVERSE_PLAYER_AVATAR_SHOW', 23);
define('PLAYER_OPTION_BASE_FONT_SIZE', 24);
define('PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE', 25);
define('PLAYER_OPTION_BUILDING_SORT', 26);
define('PLAYER_OPTION_BUILDING_SORT_INVERSE', 27);
define('PLAYER_OPTION_NAVBAR_DISABLE_PLANET', 28);
define('PLAYER_OPTION_NAVBAR_DISABLE_HANGAR', 29);
define('PLAYER_OPTION_NAVBAR_DISABLE_QUESTS', 30);
define('PLAYER_OPTION_ANIMATION_DISABLED', 31);
define('PLAYER_OPTION_MENU_WHITE_TEXT', 32);
define('PLAYER_OPTION_MENU_OLD', 33);
define('PLAYER_OPTION_UNIVERSE_OLD', 34);
define('PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE', 35);
define('PLAYER_OPTION_DESIGN_DISABLE_BORDERS', 36);
define('PLAYER_OPTION_TECH_TREE_TABLE', 37);
define('PLAYER_OPTION_PROGRESS_BARS_DISABLED', 38);
define('PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH', 39);
define('PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS', 40);
define('PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS', 41);
define('PLAYER_OPTION_NAVBAR_RESEARCH_WIDE', 42);
define('PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER', 43);
define('PLAYER_OPTION_NAVBAR_PLANET_VERTICAL', 44);
define('PLAYER_OPTION_FLEET_SHIP_SELECT_OLD', 45);
define('PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED', 46);
define('PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY', 47);
define('PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION', 48);
define('PLAYER_OPTION_TUTORIAL_DISABLED', 49);
define('PLAYER_OPTION_TUTORIAL_WINDOWED', 50);
define('PLAYER_OPTION_TUTORIAL_CURRENT', 51);
define('PLAYER_OPTION_TUTORIAL_FINISHED', 52);
define('PLAYER_OPTION_NAVBAR_PLANET_OLD', 53);
define('PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE', 54);
define('PLAYER_OPTION_QUEST_LIST_FILTER', 55);
define('PLAYER_OPTION_LOGIN_REWARDED_LAST', 56);
define('PLAYER_OPTION_LOGIN_REWARDED_LAST_VIEWED', 57);
define('PLAYER_OPTION_LOGIN_REWARD_STREAK_BEGAN', 58);
define('PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE', 59);

// -------------------
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED', 0);
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_NORMAL', 1);
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_HIDDEN', 2);




define('PLAYER_OPTION_SORT_ORDER_PLAIN', 0);
define('PLAYER_OPTION_SORT_ORDER_REVERSE', 1);

define('PLAYER_OPTION_SORT_DEFAULT', 1); // Starting from 1 - because '0' is "not selected" or "no data"
define('PLAYER_OPTION_SORT_NAME', 2);
define('PLAYER_OPTION_SORT_SPEED', 3);
define('PLAYER_OPTION_SORT_COUNT', 4);
define('PLAYER_OPTION_SORT_ID', 5);
define('PLAYER_OPTION_SORT_CREATE_TIME_LENGTH', 6);



define('GENDER_UNKNOWN', 0);
define('GENDER_MALE', 1);
define('GENDER_FEMALE', 2);

define('GAME_DISABLE_NONE', 0);
define('GAME_DISABLE_REASON', 1);
define('GAME_DISABLE_UPDATE', 2);
define('GAME_DISABLE_STAT', 3);
define('GAME_DISABLE_INSTALL', 4);
define('GAME_DISABLE_EVENT_BLACK_MOON', 5);
define('GAME_DISABLE_MAINTENANCE', 6);
define('GAME_DISABLE_EVENT_OIS', 7);

define('USER_BOT_PLAYER', 0);
define('USER_BOT_BLACK_MOON', 1);
define('USER_BOT_OIS', 2);

define('LOG_ONLIINE_AGGREGATE_NONE', 0);
define('LOG_ONLIINE_AGGREGATE_PERIOD_MINUTE_10', 1);

define('BLITZ_REGISTER_DISABLED', 0);
define('BLITZ_REGISTER_OPEN'    , 1);
define('BLITZ_REGISTER_CLOSED'  , 2);
define('BLITZ_REGISTER_SHOW_LOGIN', 3);
define('BLITZ_REGISTER_DISCLOSURE_NAMES', 4);

define('EVENT_HALLOWEEN_LOCKED', 1);
define('EVENT_HALLOWEEN_CODE', 2);

define('SKIN_IMAGE_MISSED_FIELD', '_no_image');
define('SKIN_IMAGE_MISSED_FILE_PATH', 'design/images/_no_image.png');
/**
 * Оригинальный вид тэга из темплейта без ведущей I_
 */
define('SKIN_IMAGE_TAG_RAW', 0);
/**
 * Тэг, в котором отресолвлены зависимости, а так же отсотированы и переупорядочены опции
 * Этот вид тэга будет использоваться как ключ для поиска в контейнере компилированных строк
 */
define('SKIN_IMAGE_TAG_RESOLVED', 1);
/**
 * ID изображения для адресации через skin.ini
 */
define('SKIN_IMAGE_TAG_IMAGE_ID', 2);
/**
 * Параметры тэга в массиве - отсортированные и переупорядоченные
 */
define('SKIN_IMAGE_TAG_PARAMS', 3);

define('DB_SELECT_PLAIN', false);
define('DB_SELECT_FOR_UPDATE', true);

/**
 * Defining some constants
 */
define('AUTH_LOGIN_EXTERNAL_NAME', 'auth_ext');
define('AUTH_LOGIN_EXTERNAL_MODE', 'auth_mode');

define('AUTH_VKONTAKTE', 'vkontakte');
define('AUTH_VKONTAKTE_PARAM_STATE', 'state');
define('AUTH_VKONTAKTE_PARAM_CODE', 'code');
define('AUTH_VKONTAKTE_PARAM_ERROR', 'code');
define('AUTH_VKONTAKTE_CODE_GET', 1);

define('URL_PARAM_SEPARATOR', '*');

define('ACCESSOR_NORMAL', false);
define('ACCESSOR_SHARED', true);

define("P_ACCESSOR_SET", '__set'); // DO NOT CHANGE!!!
define("P_ACCESSOR_GET", '__get'); // DO NOT CHANGE!!!
define("P_ACCESSOR_UNSET", '__unset'); // DO NOT CHANGE!!!
define("P_ACCESSOR_ISSET", '__isset'); // DO NOT CHANGE!!!
define("P_ACCESSOR_IMPORT", 'import');
define("P_ACCESSOR_EXPORT", 'export');

define('HTML_ENCODE_NONE', 0); // Do nothing - just bypass
define('HTML_ENCODE_PREFORM', 1); // perform HTML encoding
define('HTML_ENCODE_NL2BR', 2); // should line breaks be converted to <br />
define('HTML_ENCODE_STRIP_HTML', 4); // should HTML be cut from text
define('HTML_ENCODE_JS_SAFE', 8); // should be string encoded for use in JavaScript
define('HTML_ENCODE_SPACE', 16); // should be spaces replaced with $nbsp;

define('HTML_ENCODE_MULTILINE', HTML_ENCODE_PREFORM | HTML_ENCODE_NL2BR);
define('HTML_ENCODE_MULTILINE_JS', HTML_ENCODE_MULTILINE | HTML_ENCODE_JS_SAFE);

define('PAGE_OPTION_EARLY_HEADER', 'early_header');
define('PAGE_OPTION_TITLE', 'page_title');

define('FIELD_MVC', 'mvc');
define('FIELD_MODEL', 'model');
define('FIELD_VIEW', 'view');
define('MVC_OPTIONS', 'options');
define('MVC_HOOK_LOCKS', 'MVC_HOOK_LOCKS');

define('THIS_STRING', '$this');

define('MENU_SERVER_LOGO_DEFAULT', 'design/images/supernova.png');

define('GAME_FLEET_HANDLER_MAX_TIME', 3); // How long Flying Fleet Handler can work

define('ALLIANCE_HEAD_INACTIVE_TIMEOUT', PERIOD_DAY * 30);
const PLAYER_INACTIVE_TIMEOUT = PERIOD_WEEK; // Player inactivity timeout to become 'i'-marked player
const PLAYER_INACTIVE_TIMEOUT_LONG = PERIOD_WEEK_4; // Player inactivity to become 'I'-marked player



define('EVENT_ANY', -2);
define('EVENT_ALL', -1);
define('EVENT_NONE', 0);

define('STR_OBSERVER_ENTRY_METHOD_NAME', '_update');

define('SN_SQL_TYPE_NAME_TIMESTAMP', 'timestamp');
define('SN_SQL_DEFAULT_CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP');
define('SN_SQL_EXTRA_AUTO_INCREMENT', 'auto_increment');

define('STRING_IS_ESCAPED', true);
define('STRING_NEED_ESCAPING', false);
define('STRING_IS_JSON_ENCODED', true);

define('GROUP_MODIFIERS_NAME', 'modifiers');

// Paging constants
define('PAGING_PAGE_SIZE_MINIMUM', 5); // Minimum page size
define('PAGING_PAGE_SIZE_DEFAULT', 10); // Default page size
define('PAGING_PAGE_SIZE_DEFAULT_MESSAGES', 10); // Default page size for messaging
define('PAGING_PAGE_SIZE_DEFAULT_PAYMENTS', 25); // Default page size for payments
define('PAGING_SIZE_MAX_DELTA', 3); // Maximum delta from current page to write all pages numbers when paging

define('TEMPLATE_EXTRA_ARRAY', 'template_extra');

define('PATCH_REGISTER', false);
define('PATCH_PRE_CHECK', true);

const PLAYER_RANK_20 = 20;
const PLAYER_RANK_MAX = PLAYER_RANK_20;

const GROUP_DESIGN_OPTION_BLOCKS = 'GROUP_DESIGN_OPTION_BLOCKS';
const GROUP_DESIGN_BLOCK_TUTORIAL = 0; //+
const GROUP_DESIGN_BLOCK_FLEET_COMPOSE = 1; //+
const GROUP_DESIGN_BLOCK_UNIVERSE = 2; //+
const GROUP_DESIGN_BLOCK_NAVBAR = 3; //+
const GROUP_DESIGN_BLOCK_RESOURCEBAR = 4; //+
const GROUP_DESIGN_BLOCK_PLANET_SORT = 6; //+
const GROUP_DESIGN_BLOCK_COMMON_ONE = 7; //+
const GROUP_DESIGN_BLOCK_COMMON_TWO = 8; //+

const MODULE_LOAD_ORDER_CORE_AUTH             = 1;
const MODULE_LOAD_ORDER_AUTH_LOCAL            = 2;
const MODULE_LOAD_ORDER_AUTH_VKONTAKTE        = 3;
const MODULE_LOAD_ORDER_PAYMENT_SECONDARY     = 90000;
const MODULE_LOAD_ORDER_UNIT_RES_METAMATTER   = 99999;
const MODULE_LOAD_ORDER_DEFAULT               = 100000;     // HARDCODED
const MODULE_LOAD_ORDER_CORE_SHIP_CONSTRUCTOR = 999999;     // RESERVED
const MODULE_LOAD_ORDER_MENU_CUSTOMIZE        = 200000000;
const MODULE_LOAD_ORDER_LATEST                = 2000000000; // HARDCODED
const MODULE_LOAD_ORDER_GAME_SKIRMISH         = 2000100000;

// Template block names
const TPL_BLOCK_REQUIRE = 'require';

const PAGE_OPTION_FLEET_UPDATE_SKIP = 'fleet_update_skip';
const PAGE_OPTION_ADMIN = 'admin_page';

const FLEET_STATUS_FLYING = 0;
const FLEET_STATUS_RETURNING = 1;

const MENU_FIELD_AUTH_LEVEL = 'AUTH_LEVEL';

const REQUIRE_HIGHSPOT = 'festival_highspot';

const UNIVERSE_GALAXY_DISTANCE = 20000;
