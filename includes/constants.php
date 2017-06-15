<?php

defined('INSIDE') or die('Hacking attempt');

define('DB_VERSION', '42');
define('SN_RELEASE', '42');
define('SN_VERSION', '43a0.7');
define('SN_RELEASE_STABLE', '42c2'); // Latest stable release

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
define('PERIOD_DAY_3'    , PERIOD_DAY * 3);
define('PERIOD_WEEK_2'   , PERIOD_WEEK * 2);
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

// ****************************************************************************************************************
// SHOULD BE REPLACED WITH CONFIG!
define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
define('MAX_ATTACK_ROUNDS', 10);
//define('MAX_OVERFLOW', 1);
define('BASE_STORAGE_SIZE', 500000);
define('HIDE_1ST_FROM_STATS', 0);
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
define('LOC_NONE',    -1);
define('LOC_UNIVERSE', 0);
define('LOC_PLANET',   1);
define('LOC_DEBRIS',   2); // Translates to `planets` table planet_type = 1, `debris_*` fields
define('LOC_MOON',     3); // Translates to `planets` table planet_type = 3
define('LOC_USER',     4);
define('LOC_FLEET',    5);
define('LOC_ALLY',     6);

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
define('RPG_CAPTAIN', 22);
define('RPG_NAME_CHANGE', 23);
define('RPG_PLANET_DENSITY_CHANGE', 24);
define('RPG_CONVERT_MM', 25);
define('RPG_EXPLORE', 26);
define('RPG_PURCHASE_CANCEL', 27);
define('RPG_MERCENARY_DISMISSED', 28);
define('RPG_EVENT_CHRISTMAS', 29);
define('RPG_EVENT_CHRISTMAS_TREE_BURN', 30);
define('RPG_EVENT_BLACK_MOON_TICK', 31);
define('RPG_CUMULATIVE', 32);
define('RPG_GOVERNOR', 33);
define('RPG_BLITZ', 34);
define('RPG_MARKET_EXCHANGE', 35);
define('RPG_MARKET_FLEET', 36);
define('RPG_MARKET_INFO_MERCENARY', 37);
define('RPG_BLITZ_REGISTRATION', 38);
define('RPG_BLITZ_REGISTRATION_CANCEL', 39);
define('RPG_BUILD_AUTOCONVERT', 40);
define('RPG_EVENT_SUPERBORN', 41);
define('RPG_REFERRAL_BOUGHT_MM', 42);
define('RPG_HALLOWEEN', 43);
define('RPG_CHRISTMAS', 44);
define('RPG_EVENT_BIRTHDAY', 45);
define('RPG_EVENT_BIRTHDAY_COMPILED', 46);
define('RPG_BATCH_OPERATION', 47);
define('RPG_EVENT_MARCH8', 48);



// Operation error status HARDCODE!
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

// define('PAYMENT_STATUS_TEST' , -1); // Test payment
define('PAYMENT_STATUS_NONE', 0); // No status
define('PAYMENT_STATUS_COMPLETE', 1); // Money received, DM sent to user
define('PAYMENT_STATUS_CANCELED', 2); // Payment cancelled, MM deduced from user
define('PAYMENT_STATUS_EXPIRED', 3); // Payment cancelled, MM deduced from user

define('PAYMENT_EXPIRE_TIME', 0);

define('SN_PAYMENT_REQUEST_UNDEFINED_ERROR', -1);
define('SN_PAYMENT_REQUEST_OK', 0);
define('SN_PAYMENT_REQUEST_ERROR_UNIT_AMOUNT', 1);
define('SN_PAYMENT_REQUEST_ERROR_PAYLINK_UNSUPPORTED', 2);
define('SN_PAYMENT_REQUEST_IP_WRONG', 3);  // Неправильный IP входящей системы - обычно хак
define('SN_PAYMENT_REQUEST_COMMAND_UNSUPPORTED', 4); // Неподдерживаемая команда - обычно хак
define('SN_PAYMENT_REQUEST_SIGNATURE_INVALID', 5); // Неправильная подпись или не сошлась контрольная сумма - обычно хак
define('SN_MODULE_DISABLED', 6); // Модуль отключен // УНИВЕРСАЛЬНЫЙ ОТВЕТ!
define('SN_PAYMENT_REQUEST_SERVER_WRONG', 7); // Не совпадает УРЛ сервера
define('SN_PAYMENT_REQUEST_USER_NOT_FOUND', 8); // Пользователь не найден
define('SN_PAYMENT_REQUEST_EXTERNAL_ID_WRONG', 9); // Остуствует или неправильный ИД операции в платежной системе
define('SN_PAYMENT_REQUEST_CURRENCY_AMOUNT_INVALID', 10); // Неправильная сумма платежа
define('SN_PAYMENT_REQUEST_DATE_INVALID', 11); // Неправильная дата платежа
define('SN_DB_ERROR_WRITE', 12); // Ошибка записи в БД // УНИВЕРСАЛЬНЫЙ ОТВЕТ!
define('SN_METAMATTER_ERROR_ADJUST', 13); // Ошибка начисления ММ // УНИВЕРСАЛЬНЫЙ ОТВЕТ!
define('SN_PAYMENT_REQUEST_INTERNAL_ID_WRONG', 14); // Остуствует или неправильный внутренний ИД операции
define('SN_PAYMENT_REQUEST_MM_AMOUNT_INVALID', 15); // Неправильное количеств ММ в платеже
define('SN_PAYMENT_REQUEST_ORDER_WRONG', 16); // Неправильно составлен ордер от платежной системы
define('SN_PAYMENT_REQUEST_INVOICE_ALREADY_CANCELLED', 17); // Ошибка отмены платежа - платеж уже отменен
define('SN_PAYMENT_REQUEST_ORDER_NOT_FOUND', 18); // Ошибка 
define('SN_PAYMENT_REQUEST_PAYMENT_NOT_COMPLETE', 19); // Ошибка отмены платежа - платеж еще не закончен
// define('SN_PAYMENT_REQUEST_CANCEL_COMPLETE', 20); // 
define('SN_PAYMENT_REQUEST_INTERNAL_CONFIG_ERROR', 21); // Ошибка конфигурации
define('SN_PAYMENT_REQUEST_DB_ERROR_PAYMENT_CREATE', 22); // Ошибка создания платежа
define('SN_PAYMENT_REQUEST_ERROR_TEST_PAYMENT', 23); // Ошибка - внешний статус платежа не совпадает с запомненным статусом платежа
define('SN_PAYMENT_ERROR_INTERNAL_NO_EXTERNAL_CURRENCY_SET', 24); // Ошибка - не установлена внешняя валюта

define('PAYMENT_DESCRIPTION_MAX', 0);
define('PAYMENT_DESCRIPTION_100', 100);
define('PAYMENT_DESCRIPTION_250', 250);


define('PAYMENT_METHOD_EMONEY', 1000);
define('PAYMENT_METHOD_EMONEY_WEBMONEY_WMZ', 1001);
define('PAYMENT_METHOD_EMONEY_WEBMONEY_WME', 1002);
define('PAYMENT_METHOD_EMONEY_WEBMONEY_WMU', 1003);
define('PAYMENT_METHOD_EMONEY_WEBMONEY_WMR', 1004);
define('PAYMENT_METHOD_EMONEY_WEBMONEY_WMB', 1005);
define('PAYMENT_METHOD_EMONEY_YANDEX', 1006);
define('PAYMENT_METHOD_EMONEY_QIWI', 1007);
define('PAYMENT_METHOD_EMONEY_MAILRU', 1008);
define('PAYMENT_METHOD_EMONEY_ELECSNET', 1009);
define('PAYMENT_METHOD_EMONEY_EASYPAY', 1010);
define('PAYMENT_METHOD_EMONEY_RUR_W1R', 1011);
define('PAYMENT_METHOD_EMONEY_TELEMONEY', 1012);
define('PAYMENT_METHOD_EMONEY_PAYPAL', 1013);

define('PAYMENT_METHOD_BANK_CARD', 2000);
define('PAYMENT_METHOD_BANK_CARD_STANDARD', 2001);
define('PAYMENT_METHOD_BANK_CARD_LIQPAY', 2002);
define('PAYMENT_METHOD_BANK_CARD_EASYPAY', 2003);
define('PAYMENT_METHOD_BANK_CARD_AMERICAN_EXPRESS', 2004);
define('PAYMENT_METHOD_BANK_CARD_JCB', 2005);
define('PAYMENT_METHOD_BANK_CARD_UNIONPAY', 2006);

define('PAYMENT_METHOD_MOBILE', 3000);
define('PAYMENT_METHOD_MOBILE_MEGAPHONE', 3001);
define('PAYMENT_METHOD_MOBILE_MTS', 3002);
define('PAYMENT_METHOD_MOBILE_KYIVSTAR', 3003);
define('PAYMENT_METHOD_MOBILE_SMS', 3004);
define('PAYMENT_METHOD_MOBILE_PAYPAL_ZONG', 3005);
define('PAYMENT_METHOD_MOBILE_XSOLLA', 3006);

define('PAYMENT_METHOD_TERMINAL', 4000);
define('PAYMENT_METHOD_TERMINAL_QIWI', 4001);
define('PAYMENT_METHOD_TERMINAL_ELECSNET', 4002);
define('PAYMENT_METHOD_TERMINAL_ELEMENT', 4003);
define('PAYMENT_METHOD_TERMINAL_KASSIRANET', 4004);
define('PAYMENT_METHOD_TERMINAL_TELEPAY', 4005);
define('PAYMENT_METHOD_TERMINAL_IBOX', 4006);
define('PAYMENT_METHOD_TERMINAL_UKRAINE', 4007);
define('PAYMENT_METHOD_TERMINAL_RUSSIA', 4008);
define('PAYMENT_METHOD_TERMINAL_EASYPAY', 4009);

define('PAYMENT_METHOD_BANK_INTERNET', 5000);
define('PAYMENT_METHOD_BANK_INTERNET_ALFA_BANK', 5001);
define('PAYMENT_METHOD_BANK_INTERNET_RUSSKIY_STANDART', 5002);
define('PAYMENT_METHOD_BANK_INTERNET_PROSMVYAZBANK', 5003);
define('PAYMENT_METHOD_BANK_INTERNET_VTB24', 5004);
define('PAYMENT_METHOD_BANK_INTERNET_OCEAN_BANK', 5005);
define('PAYMENT_METHOD_BANK_INTERNET_HANDY_BANK', 5006);
define('PAYMENT_METHOD_BANK_INTERNET_007', 5007);
define('PAYMENT_METHOD_BANK_INTERNET_008', 5008);
define('PAYMENT_METHOD_BANK_INTERNET_009', 5009);
define('PAYMENT_METHOD_BANK_INTERNET_010', 5010);
define('PAYMENT_METHOD_BANK_INTERNET_011', 5011);
define('PAYMENT_METHOD_BANK_INTERNET_012', 5012);
define('PAYMENT_METHOD_BANK_INTERNET_013', 5013);
define('PAYMENT_METHOD_BANK_INTERNET_014', 5014);
define('PAYMENT_METHOD_BANK_INTERNET_015', 5015);
define('PAYMENT_METHOD_BANK_INTERNET_016', 5016);
define('PAYMENT_METHOD_BANK_INTERNET_017', 5017);
define('PAYMENT_METHOD_BANK_INTERNET_018', 5018);
define('PAYMENT_METHOD_BANK_INTERNET_019', 5019);
define('PAYMENT_METHOD_BANK_INTERNET_020', 5020);
define('PAYMENT_METHOD_BANK_INTERNET_021', 5021);
define('PAYMENT_METHOD_BANK_INTERNET_BANK24', 5022);
define('PAYMENT_METHOD_BANK_INTERNET_PRIVAT24', 5023);
define('PAYMENT_METHOD_BANK_INTERNET_SBERBANK', 5024);

define('PAYMENT_METHOD_BANK_TRANSFER', 6000);

define('PAYMENT_METHOD_OTHER', 9000);
define('PAYMENT_METHOD_OTHER_EVROSET', 9001);
define('PAYMENT_METHOD_OTHER_SVYAZNOY', 9002);
define('PAYMENT_METHOD_OTHER_ROBOKASSA_MOBILE', 9003);

define('PAYMENT_METHOD_GENERIC', 10000);
define('PAYMENT_METHOD_GENERIC_XSOLLA', 10001);
define('PAYMENT_METHOD_GENERIC_ROBOKASSA', 10002);


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
define('BONUS_PERCENT_CUMULATIVE' , 5);  // Cumulative percent on base value
define('BONUS_PERCENT_DEGRADED' ,   6);  // Bonus amount degraded with increase as pow(bonus, level) (?)
define('BONUS_SPEED',               7);  // Speed bonus

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


define('HULL_SIZE_TINY', 1);
define('HULL_SIZE_SMALL', 2);
define('HULL_SIZE_MEDIUM', 3);
define('HULL_SIZE_LARGE', 4);
define('HULL_SIZE_HUGE', 5);


// === Structures
define('UNIT_STRUCTURES', 99);
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

define('UNIT_STRUCTURES_SPECIAL', 40);
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
define('TECH_ENGINE_ION', 117);
define('TECH_ENGINE_HYPER', 118);
define('TECH_LASER', 120);
define('TECH_ION', 121);
define('TECH_PLASMA', 122);
define('TECH_RESEARCH', 123);
define('TECH_EXPEDITION', 124);
define('TECH_COLONIZATION', 150);
define('TECH_ASTROTECH', 151);
define('TECH_GRAVITON', 199);

// === Hangar units
// --- Ships
define('UNIT_SHIPS', 200);

define('SHIP_CARGO_SMALL', 202);
define('SHIP_CARGO_BIG', 203);
define('SHIP_CARGO_SUPER', 201);
define('SHIP_CARGO_HYPER', 218);
define('SHIP_COLONIZER', 208);
define('SHIP_RECYCLER', 209);
define('SHIP_RECYCLER_GLUTTONY', 223);
define('SHIP_SPY', 210);
define('SHIP_SATTELITE_SOLAR', 212);
define('SHIP_CARGO_GREED', 220);
define('SHIP_SATTELITE_SLOTH', 221);


define('SHIP_SMALL_FIGHTER_LIGHT', 204);
define('SHIP_SMALL_FIGHTER_WRATH', 219);
define('SHIP_SMALL_FIGHTER_HEAVY', 205);
define('SHIP_SMALL_FIGHTER_ASSAULT', 217);
define('SHIP_MEDIUM_FRIGATE', 206);
define('SHIP_MEDIUM_DESTROYER', 226);
define('SHIP_MEDIUM_BOMBER_ENVY', 224);
define('SHIP_LARGE_CRUISER', 207);
define('SHIP_LARGE_BOMBER', 211);
define('SHIP_LARGE_BATTLESHIP', 215);
define('SHIP_LARGE_BATTLESHIP_PRIDE', 222);
define('SHIP_LARGE_ORBITAL_HEAVY', 225);
define('SHIP_LARGE_DESTRUCTOR', 213);
define('SHIP_HUGE_DEATH_STAR', 214);
define('SHIP_HUGE_SUPERNOVA', 216);


define('SHIP_NEXT', 227);

// --- Defense
define('UNIT_DEFENCE', 400);
define('UNIT_DEF_TURRET_MISSILE', 401);
define('UNIT_DEF_TURRET_LASER_SMALL', 402);
define('UNIT_DEF_TURRET_LASER_BIG', 403);
define('UNIT_DEF_TURRET_GAUSS', 404);
define('UNIT_DEF_TURRET_ION', 405);
define('UNIT_DEF_TURRET_PLASMA', 406);
define('UNIT_DEF_SHIELD_SMALL', 407);
define('UNIT_DEF_SHIELD_BIG', 408);
define('UNIT_DEF_SHIELD_PLANET', 409);

// --- Missiles
define('UNIT_DEF_MISSILES', 500);
define('UNIT_DEF_MISSILE_INTERCEPTOR', 502);
define('UNIT_DEF_MISSILE_INTERPLANET', 503);

// === Mercenaries
// --- Mercenary list
define('UNIT_MERCENARIES', 600);
define('MRC_ACADEMIC', 606);
define('MRC_ADMIRAL', 602);
define('MRC_STOCKMAN', 607); // OK MODIFIER
define('MRC_SPY', 610);
define('MRC_COORDINATOR', 611);
define('MRC_DESTRUCTOR', 612);
define('MRC_NAVIGATOR', 613);
define('MRC_ASSASIN', 614);
define('MRC_EMPEROR', 615);

// --- Governors list
define('UNIT_GOVERNORS', 680);
define('MRC_TECHNOLOGIST', 601); // OK MODIFIER
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
define('RES_METAMATTER', 950);
define('RES_TIME', 999);

// === Artifacts
define('UNIT_ARTIFACTS', 1000);
define('ART_LHC', 1001);      // Additional moon chance
define('ART_RCD_SMALL', 1002);   // Rapid Colony Deployment - Set of buildings up to 10th level - 10/14/ 3/0 -   405 DM
define('ART_RCD_MEDIUM', 1003);  // Rapid Colony Deployment - Set of buildings up to 15th level - 15/20/ 8/0 -  4704 DM
define('ART_RCD_LARGE', 1004);   // Rapid Colony Deployment - Set of buildings up to 20th level - 20/25/10/1 - 39790 DM
define('ART_HEURISTIC_CHIP', 1005); // Speed up research
define('ART_NANO_BUILDER', 1006); // Speed up building
define('ART_NANO_CONSTRUCTOR', 1007); // RESERVED Speed up hangar constructions
define('ART_HOOK_SMALL', 1008);
define('ART_HOOK_MEDIUM', 1009);
define('ART_HOOK_LARGE', 1010);
define('ART_DENSITY_CHANGER', 1011); // RESERVED
// 1012 RESERVED
define('ART_PLANET_GATE', 1013); // RESERVED Planet gate

// === Blueprints
define('UNIT_PLANS', 1100);
define('UNIT_PLAN_STRUC_MINE_FUSION', 1101);
define('UNIT_PLAN_SHIP_CARGO_SUPER', 1102);
define('UNIT_PLAN_SHIP_CARGO_HYPER', 1103);
define('UNIT_PLAN_SHIP_DEATH_STAR', 1104);
define('UNIT_PLAN_SHIP_SUPERNOVA', 1105);
define('UNIT_PLAN_DEF_SHIELD_PLANET', 1106);
define('UNIT_PLAN_SHIP_ORBITAL_HEAVY', 1107);

define('UNIT_PREMIUM', 1200);
define('UNIT_MASS_OPERATIONS', 1290);
define('UNIT_MASS_OPERATIONS_TEST_DRIVE', 1291);
define('UNIT_SECTOR', 1300);
define('UNIT_RACE', 1400);
define('UNIT_CAPTAIN', 1500);

define('UNIT_PLANET_DENSITY', 1601);
define('UNIT_PLANET_DENSITY_INDEX', 1602);
define('UNIT_PLANET_DENSITY_RARITY', 1603);
define('UNIT_PLANET_DENSITY_RICHNESS', 1604);
define('UNIT_PLANET_DENSITY_MAX_SECTORS', 1605);
define('UNIT_PLANET_DENSITY_PROBABILITY', 1606);
define('UNIT_PLANET_DENSITY_MIN_ASTROTECH', 1607);

define('PLANET_DENSITY_NONE', 0);
define('PLANET_DENSITY_ICE_HYDROGEN', 8); // New
define('PLANET_DENSITY_ICE_METHANE', 1); // Old ICE
define('PLANET_DENSITY_ICE_WATER', 9); // New
define('PLANET_DENSITY_CRYSTAL_RAW', 10); // New
define('PLANET_DENSITY_CRYSTAL_SILICATE', 2);
define('PLANET_DENSITY_CRYSTAL_STONE', 3);
define('PLANET_DENSITY_STANDARD', 4);
define('PLANET_DENSITY_METAL_ORE', 5);
define('PLANET_DENSITY_METAL_PERIDOT', 6);
// define('PLANET_DENSITY_METAL_HEAVY', 7); // deprecated
define('PLANET_DENSITY_METAL_RAW', 11); // New
// MAXIMUM PLANET_DENSITY_METAL_RAW 11

define('PLANET_DENSITY_RICHNESS_NORMAL', 0);
define('PLANET_DENSITY_RICHNESS_AVERAGE', 1);
define('PLANET_DENSITY_RICHNESS_GOOD', 2);
define('PLANET_DENSITY_RICHNESS_PERFECT', 3);



define('UNIT_AWARD', 2000); // Награды игрока 2.000-2.999
define('UNIT_AWARD_ORDER', 2100); // Ордена за Выдающиеся Достижения - например, за спонсорство
define('UNIT_AWARD_ORDER_SPONSOR_BRONZE', 2101);
define('UNIT_AWARD_ORDER_SPONSOR_SILVER', 2102);
define('UNIT_AWARD_ORDER_SPONSOR_GOLD', 2103);
define('UNIT_AWARD_ORDER_SPONSOR_PLATINUM', 2104);
define('UNIT_AWARD_ORDER_SPONSOR_DIAMOND', 2105);
define('UNIT_AWARD_ORDER_SPONSOR_DARK', 2106);
define('UNIT_AWARD_ORDER_SPONSOR_META', 2107);
define('UNIT_AWARD_ORDER_SPONSOR', 2109);
// 2110 - следующая группа орденов

define('UNIT_AWARD_MEDAL', 2200); // Медали за Серъезные Достижения - например, за победу в конкурсе
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE1', 2201); // Блиц-сервер, участник 0-го раунда, 1-е место
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE2', 2202); // Блиц-сервер, участник 0-го раунда, 2-е место
define('UNIT_AWARD_MEDAL_BLITZ_R0_PLACE3', 2203); // Блиц-сервер, участник 0-го раунда, 3-е место
define('UNIT_AWARD_MEDAL_2016_WOMEN_DAY_BEST', 2204);  // Медаль Лучшему Кавалеру за максимум потраченной ММ/максимум одаренных женщин Женщине от Мужчины во время ивента 8 марта 2016 года
define('UNIT_AWARD_MEDAL_2017_WOMEN_DAY_BEST', 2205);  // Медаль Лучшему Кавалеру за максимум потраченной ММ/максимум одаренных женщин Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEDAL_2017_WOMEN_DAY_QUEEN', 2206);  // Медаль Королевы Весны за максимум полученной ММ/максимум полученных подарков от Мужчины во время ивента 8 марта 2017 года

define('UNIT_AWARD_MEMORY', 2300); // Памятные знаки за существование и участие - например "4 года в игре". "Был онлайн в новогоднюю ночь 2013". итд
define('UNIT_AWARD_MEMORY_IMMORTAL', 2301);  // Бессмертный
define('UNIT_AWARD_MEMORY_2015_WOMEN_DAY', 2302);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2015 года
define('UNIT_AWARD_MEMORY_BLITZ_R0', 2303); // Блиц-сервер, участник 0-го раунда
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_SIMPLE', 2304); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_BRONZE', 2305); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_SILVER', 2306); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_GOLD', 2307); // День Рождения СН
define('UNIT_AWARD_MEMORY_SUPER_BORN_2015_PLATINUM', 2308); // День Рождения СН
define('UNIT_AWARD_MEMORY_2016_WOMEN_DAY', 2309);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2016 года
define('UNIT_AWARD_MEMORY_2017_WOMEN_DAY', 2310);  // Значек за подарок Женщине от Мужчины во время ивента 8 марта 2017 года
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_SIMPLE', 2311); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_BRONZE', 2312); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_SILVER', 2313); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_GOLD', 2314); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_PLATINUM', 2315); // День Рождения СН - 2017
define('UNIT_AWARD_MEMORY_SUPER_BORN_2017_DIAMOND', 2316); // День Рождения СН - 2017

define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_SIMPLE', 2317); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_BRONZE', 2318); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_SILVER', 2319); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_GOLD', 2320); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_PLATINUM', 2321); // День Рождения СН - 2016
define('UNIT_AWARD_MEMORY_SUPER_BORN_2016_DIAMOND', 2322); // День Рождения СН - 2016

define('UNIT_AWARD_PENNANT', 2400); // Переходящий вымпел - индикация статуса на сервере: "Топ-1", "Топ", "Сабтоп", "Самый большой флот" итд
define('UNIT_AWARD_BADGE', 2600); // Бейджики/значки за ачивки - например, "Построил 1000 кораблей"
define('UNIT_AWARD_BADGE_BLITZ', 2601); // Медали за Блиц-сервер

define('UNIT_EVENT_UNITS', 3000); // Special event units
define('UNIT_EVENT_CHRISTMAS', 3001); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_TREE', 3002); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_TOP', 3003); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_ELECTRO', 3004); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_GARLAND', 3005); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_RAIN', 3006); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_DECORATION', 3007); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_SERPENTINE', 3008); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_CONFETTI', 3009); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_FIRECRACKER', 3011); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_SWEET', 3012); // Christmas event units
define('UNIT_EVENT_CHRISTMAS_CHINESE', 3013); // Christmas event units

define('UNIT_EVENT_SUPERBORN', 3020); // SuperNova Birthday Units
define('UNIT_EVENT_SUPERBORN_CAKE', 3021);
define('UNIT_EVENT_SUPERBORN_CANDLE', 3022);
define('UNIT_EVENT_SUPERBORN_BOOTSTRAP', 3023);
define('UNIT_EVENT_SUPERBORN_GUIDE', 3024);
define('UNIT_EVENT_SUPERBORN_CLASS', 3025);
define('UNIT_EVENT_SUPERBORN_MODULE', 3026);
define('UNIT_EVENT_SUPERBORN_TEMPLATE', 3027);
define('UNIT_EVENT_SUPERBORN_FUNCTION', 3028);
define('UNIT_EVENT_SUPERBORN_CONSTANT', 3029);
define('UNIT_EVENT_SUPERBORN_XNOVA', 3030);
define('UNIT_EVENT_SUPERBORN_DOC', 3031);
define('UNIT_EVENT_SUPERBORN_CHANGELOG', 3032);
define('UNIT_EVENT_SUPERBORN_HELLOWORLD', 3033);
define('UNIT_EVENT_SUPERBORN_SCREENSHOT', 3034);
define('UNIT_EVENT_SUPERBORN_UNUSED', 3035);
define('UNIT_EVENT_SUPERBORN_HACK', 3036);

// 3040
define('UNIT_EVENT_HALLOWEEN_2015', 3040); // SuperNova Birthday Units
define('UNIT_EVENT_HALLOWEEN_2015_SPOOK', 3041);
define('UNIT_EVENT_HALLOWEEN_2015_CAULDRON', 3042);
define('UNIT_EVENT_HALLOWEEN_2015_SKULL', 3043);
define('UNIT_EVENT_HALLOWEEN_2015_MANSION', 3044);
define('UNIT_EVENT_HALLOWEEN_2015_SURPRISE_BAG', 3045);
define('UNIT_EVENT_HALLOWEEN_2015_GRAVESTONE', 3046);
define('UNIT_EVENT_HALLOWEEN_2015_WITCH_HAT', 3047);
define('UNIT_EVENT_HALLOWEEN_2015_FLAME', 3048);
define('UNIT_EVENT_HALLOWEEN_2015_AUTUMN_LEAVES', 3049);
define('UNIT_EVENT_HALLOWEEN_2015_SPIDER', 3050);
define('UNIT_EVENT_HALLOWEEN_2015_BATS', 3051);
define('UNIT_EVENT_HALLOWEEN_2015_PUMPKIN', 3052);
define('UNIT_EVENT_HALLOWEEN_2015_CHATTE_NOIRE', 3053);
define('UNIT_EVENT_HALLOWEEN_2015_WITCH', 3054);
define('UNIT_EVENT_HALLOWEEN_2015_REAPER', 3055);
define('UNIT_EVENT_HALLOWEEN_2015_AXE', 3056);
define('UNIT_EVENT_HALLOWEEN_2015_COFFIN', 3057);
define('UNIT_EVENT_HALLOWEEN_2015_SCREAM_MASK', 3058);
define('UNIT_EVENT_HALLOWEEN_2015_CANDY', 3059);
define('UNIT_EVENT_HALLOWEEN_2015_KRUEGER', 3060);
define('UNIT_EVENT_HALLOWEEN_2015_VOORHEES', 3061);
define('UNIT_EVENT_HALLOWEEN_2015_LEATHERFACE', 3062);
define('UNIT_EVENT_HALLOWEEN_2015_CHUCKY', 3063);
define('UNIT_EVENT_HALLOWEEN_2015_JIGSAW', 3064);
define('UNIT_EVENT_HALLOWEEN_2015_KRUEGER_HAT', 3065);
define('UNIT_EVENT_HALLOWEEN_2015_KRUEGER_SWEATER', 3066);
define('UNIT_EVENT_HALLOWEEN_2015_KRUEGER_GLOVE', 3067);

define('UNIT_HIGHSPOT_GATHER_CHRISTMAS', 3100); // SuperNova Christmas Gather Units
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_DED', 3102); // 01 - Дед Мороз 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SNEGURKA', 3103); // 02 - Снегурочка 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_GOOD_FELLAS', 3104); // 03 - Список Хороших Детей 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SANTA_HAT', 3105); // 06 - Настоящая шапка деда мороза 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SACK_GIFT', 3106); // 07 - Мешок с подарками 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_CALENDAR', 3107); // 08 - 31 декабря календаря 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_RUDOLPH', 3108); // 04 - Рудольф 1 "Рудольф всегда отличался от других оленей..."
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_DEER', 3109); // 45 - Олени 6
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_TREE_EMPIRE', 3110); // 10 - Имперская ёлка 2
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_MORGUROCHKA', 3111); // 13 - Моргурочка - мутантный дед-мороз 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_HOBO', 3112); // 20 - Безработный наёмник (Дед у холодильника, Дед с подарками) 2
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SACK_TRICK', 3113); // 80 - Мешок с новогодними фокусами 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_KITTEN', 3114); // 30 - Новогодний котёнок 2
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SANTA_MASK', 3115); // 55 - Маска деда-мороза 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SANTA_COSTUME', 3116); // 57 - Игоровой Костюм деда-мороза 2
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SACK_EMPTY', 3117); // 95 - Пустой мешок от подарков 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_BLIZZARD_BALL', 3118); // 05 - Метель в шарике 7
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_BEVERAGE', 3119); // 15 - Новогодний напиток 7
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_GINGER', 3120); // 70 - Пряничный человечек - 1
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_CANDY', 3121); // 96 - Конфета 5 // Трость для пряничного человечка
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_POSTCARD', 3122); // 40 - Новогодняя открытка 2
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_BELLS', 3123); // 93 - Джингл белл 3
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_GIFT_EMPTY', 3124); // 97 - Коробка для подарка  11
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_WREATH', 3125); // 50 - Новогодний венок 10 wreath
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_CANDLE', 3126); // 60 - Новогодние свечи 10
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_BALL', 3127); // 98 - Ёлочные шары 7
define('UNIT_HIGHSPOT_GATHER_CHRISTMAS_SNOWFLAKE', 3128); // 99 - Снежинка 10

// Шелдон: Леонард, в правилах этикета сказано, когда ваш друг расстроен, ему нужно предложить горячий напиток, например, чай.

// 3200

define('UNIT_NEXT', 4000); // !!! Next unit start on 4000 !!!

define('GROUP_PART',         800000);
// Зарезервировано для запчастей: 800.001 - 899.999
// define('GROUP_PART_HULL',    801000); // Корпуса - 1000 штук
// define('GROUP_PART_ARMOR',   802000); // Броня - 1000 штук
// define('GROUP_PART_SHIELD',  803000); // Щиты - 1000 штук
// define('GROUP_PART_WEAPON',  810000); // Оружие - 10000 штук


define('UNIT_GROUP', 'groups'); // 900.000
// Зарезервировано для груп юнитов: 900.001 - 999.999
define('GROUP_UNIT_USER', 1000000);
// Зарезервировано для пользовательских юнитов: 1.000.001 - 1.999.999
define('GROUP_ID_RESERVED', 2000000);
// Зарезервировано для прочих нужд: 2.000.000 - 1.999.999.999
define('GROUP_PARAMS', 1000000000);
// Зарезервировано для параметров: 1.000.000.001 - 1.999.999.999
define('GROUP_DEVELOPERS', 2000000000);
// Пространство для разработчиков: 2.000.000.001 - 2.147.483.647

define('UNIT_PLAYER_COLONIES_CURRENT', 'COLONIES_CURRENT');
define('UNIT_PLAYER_COLONIES_MAX', 'COLONIES_MAX');
define('UNIT_PLAYER_EXPEDITIONS_MAX', 'EXPEDITIONS_MAX');


// Unit params
// define('GROUP_PARAMS', 1000000000);
// Зарезервировано для параметров: 1.000.000.000-2.000.000.000
define('P_MAX_STACK', 'max');
// Все просто 'name' и "name" заменены на P_NAME
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
define('P_CAPACITY', 'capacity');
define('P_UNIT_SIZE', 'size');
define('P_SPEED', 'speed');
define('P_UNIT_PRODUCTION', 'production');

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


define('UBE_COMBAT_RESULT_DRAW_END', -1);
define('UBE_COMBAT_RESULT_DRAW', 0);
define('UBE_COMBAT_RESULT_WIN', 1);
define('UBE_COMBAT_RESULT_LOSS', 2);

define('UBE_MOON_WAS', -1);
define('UBE_MOON_NONE', 0);
define('UBE_MOON_CREATE_SUCCESS', 1);
define('UBE_MOON_CREATE_FAILED', 2);
define('UBE_MOON_DESTROY_SUCCESS', 3);
define('UBE_MOON_DESTROY_FAILED', 4);
define('UBE_MOON_REAPERS_NONE', 5);
define('UBE_MOON_REAPERS_DIED', 6);
define('UBE_MOON_REAPERS_RETURNED', 7);


define('UBE_REPORT_NOT_FOUND', 'UBE_REPORT_NOT_FOUND');

define('UBE_REPORT_CYPHER', 'UBE_REPORT_CYPHER');
define('UBE_REPORT_ID', 'UBE_REPORT_ID');
define('UBE_TIME', 'UBE_TIME');
define('UBE_TIME_SPENT', 'UBE_TIME_SPENT');

define('UBE_OPTIONS', 'UBE_OPTIONS');
define('UBE_COMBAT_ADMIN', 'UBE_COMBAT_ADMIN');
define('UBE_DEFENDER_ACTIVE', 'UBE_DEFENDER_ACTIVE');
define('UBE_MISSION_TYPE', 'UBE_MISSION_TYPE');
define('UBE_LOADED', 'UBE_LOADED');
define('UBE_SIMULATOR', 'UBE_SIMULATOR');
define('UBE_EXCHANGE', 'UBE_EXCHANGE');
define('UBE_METHOD', 'UBE_METHOD');


define('UBE_OUTCOME', 'UBE_OUTCOME');
define('UBE_COMBAT_RESULT', 'UBE_COMBAT_RESULT');
define('UBE_SFR', 'UBE_SFR');
define('UBE_DEBRIS', 'UBE_DEBRIS');
define('UBE_DEBRIS_TOTAL', 'UBE_DEBRIS_TOTAL');

define('UBE_CAPTURE_RESULT', 'UBE_CAPTURE_RESULT');
define('UBE_CAPTURE_DISABLED', 0);
define('UBE_CAPTURE_NON_PLANET', 1);
define('UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND', 2);
define('UBE_CAPTURE_TOO_MUCH_FLEETS', 3);
define('UBE_CAPTURE_NO_ATTACKER_USER_ID', 4);
define('UBE_CAPTURE_NO_DEFENDER_USER_ID', 5);
define('UBE_CAPTURE_CAPITAL', 6);
define('UBE_CAPTURE_TOO_LOW_POINTS', 7);
define('UBE_CAPTURE_NOT_ENOUGH_SLOTS', 8);

define('UBE_CAPTURE_SUCCESSFUL', 100);

define('UBE_PLANET', 'UBE_PLANET');
define('PLANET_ID', 'PLANET_ID');
define('PLANET_NAME', 'PLANET_NAME');
define('PLANET_SIZE', 'PLANET_SIZE');
define('PLANET_GALAXY', 'PLANET_GALAXY');
define('PLANET_SYSTEM', 'PLANET_SYSTEM');
define('PLANET_PLANET', 'PLANET_PLANET');
define('PLANET_TYPE', 'PLANET_TYPE');

define('UBE_MOON', 'UBE_MOON');
define('UBE_MOON_NAME', 'UBE_MOON_NAME');
define('UBE_MOON_CHANCE', 'UBE_MOON_CHANCE');
define('UBE_MOON_SIZE', 'UBE_MOON_SIZE');
define('UBE_MOON_REAPERS', 'UBE_MOON_REAPERS');
define('UBE_MOON_DESTROY_CHANCE', 'UBE_MOON_DESTROY_CHANCE');
define('UBE_MOON_REAPERS_DIE_CHANCE', 'UBE_MOON_REAPERS_DIE_CHANCE');


define('UBE_PLAYERS', 'UBE_PLAYERS');
define('UBE_NAME', 'UBE_NAME');
define('UBE_ATTACKER', 'UBE_ATTACKER');
define('UBE_AUTH_LEVEL', 'UBE_AUTH_LEVEL');
define('UBE_PLAYER_DATA', 'UBE_PLAYER_DATA');


define('UBE_BONUSES', 'UBE_BONUSES');
define('UBE_ATTACK', 'UBE_ATTACK');
define('UBE_SHIELD', 'UBE_SHIELD');
define('UBE_ARMOR', 'UBE_ARMOR');
define('UBE_SHIELD_REST', 'UBE_SHIELD_REST');
define('UBE_ARMOR_REST', 'UBE_ARMOR_REST');


define('UBE_FLEETS', 'UBE_FLEETS');
define('UBE_OWNER', 'UBE_OWNER');
define('UBE_FLEET_GROUP', 'UBE_FLEET_GROUP');
define('UBE_PRICE', 'UBE_PRICE');
define('UBE_AMPLIFY', 'UBE_AMPLIFY');
define('UBE_CAPACITY', 'UBE_CAPACITY');
define('UBE_TYPE', 'UBE_TYPE');
define('UBE_RESOURCES', 'UBE_RESOURCES');
define('UBE_RESOURCES_LOST', 'UBE_RESOURCES_LOST');
define('UBE_CARGO_DROPPED', 'UBE_CARGO_DROPPED');
define('UBE_RESOURCES_LOOTED', 'UBE_RESOURCES_LOOTED');
define('UBE_RESOURCES_LOST_IN_METAL', 'UBE_RESOURCES_LOST_IN_METAL');
define('UBE_FLEET_TYPE', 'UBE_FLEET_TYPE');


define('UBE_COUNT', 'UBE_COUNT');
define('UBE_UNITS_LOST', 'UBE_UNITS_LOST');
define('UBE_DEFENCE_RESTORE', 'UBE_DEFENCE_RESTORE');


define('UBE_ROUNDS', 'UBE_ROUNDS');
define('UBE_FLEET_INFO', 'UBE_FLEET_INFO');
define('UBE_UNITS_BOOM', 'UBE_UNITS_BOOM');
define('UBE_ATTACK_BASE', 'UBE_ATTACK_BASE');
define('UBE_SHIELD_BASE', 'UBE_SHIELD_BASE');
define('UBE_ARMOR_BASE', 'UBE_ARMOR_BASE');
define('UBE_ATTACKERS', 'UBE_ATTACKERS');
define('UBE_DEFENDERS', 'UBE_DEFENDERS');
define('UBE_TOTAL', 'UBE_TOTAL');
define('UBE_DAMAGE_PERCENT', 'UBE_DAMAGE_PERCENT');

define('UBE_CAPTAIN', 'UBE_CAPTAIN');

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
define('NICK_BIRTHSDAY',      4000);
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

// -------------------
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_FIXED', 0);
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_NORMAL', 1);
define('PLAYER_OPTION_MENU_HIDE_SHOW_BUTTON_HIDDEN', 2);

define('PLAYER_OPTION_TIME_DIFF', 0); // Чистая разница в ходе часов в секундах
define('PLAYER_OPTION_TIME_DIFF_UTC_OFFSET', 1); // Разница между часовыми поясами в секундах
define('PLAYER_OPTION_TIME_DIFF_FORCED', 2);
define('PLAYER_OPTION_TIME_DIFF_MEASURE_TIME', 3);

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

define('USER_BOT_PLAYER', 0);
define('USER_BOT_BLACK_MOON', 1);

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
define('CACHER_NOT_INIT', -1);
define('CACHER_NO_CACHE',  0);
define('CACHER_XCACHE'  ,  1);

define('CACHER_LOCK_WAIT', 5); // maximum cacher wait for table unlock in seconds. Can be float

// max timeout cacher can sleep in waiting for unlockDefault = 10000 ms = 0.01s
// really it will sleep mt_rand(100, CACHER_LOCK_SLEEP)
define('CACHER_LOCK_SLEEP', 10000);

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

define('HTML_ENCODE_PREFORM', 1); // perform HTML encoding
define('HTML_ENCODE_NL2BR', 2); // should line breaks be converted to <br />
define('HTML_ENCODE_STRIP_HTML', 4); // should HTML be cut from text
define('HTML_ENCODE_JS_SAFE', 8); // should be string encoded for use in JavaScript

define('HTML_ENCODE_MULTILINE', HTML_ENCODE_PREFORM | HTML_ENCODE_NL2BR);
define('HTML_ENCODE_MULTILINE_JS', HTML_ENCODE_MULTILINE | HTML_ENCODE_JS_SAFE);

define('PAGE_OPTION_EARLY_HEADER', 'early_header');
define('PAGE_OPTION_TITLE', 'page_title');

define('FIELD_MVC', 'mvc');
define('FIELD_MODEL', 'model');
define('FIELD_VIEW', 'view');
define('THIS_STRING', '$this');

define('MENU_SERVER_LOGO_DEFAULT', 'design/images/supernova.png');

define('GAME_FLEET_HANDLER_MAX_TIME', 3); // How long Flying Fleet Handler can work