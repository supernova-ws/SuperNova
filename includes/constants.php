<?php


/**
 * constants.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------

if ( !defined('INSIDE') )
{
  die('Hacking attempt');
}

define('GAMEURL', "http://{$_SERVER['HTTP_HOST']}/");

// Game type constants starts with GAME_
define('GAME_SUPERNOVA', 0);
define('GAME_OGAME',     1);

// Pattern to parse planet coordinates like [1:123:14] - no expedition [x:x:16] will pass!
define('PLANET_COORD_PREG', '/^\[([1-9]):([1-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]):(1[0-5]|[1-9])\]$/i');
// Pattern to parse scheduler '[[[[[YYYY-]MM-]DD ]HH:]MM:]SS'
define('SCHEDULER_PREG', '/^(?:(?:(?:(?:(?:(2\d\d\d)-)?(1[0-2]|0[1-9])-)?(?:(3[01]|[0-2]\d)\ ))?(?:(2[0-3]|[01]\d):))?(?:([0-5]\d):))?([0-5]\d)$/i');

define('MAX_ATTACK_ROUNDS', 10);

// Nombre de colones pour les rapports d'espionnage
define('SPY_REPORT_ROW'           , 1);
// Cases données par niveau de Base Lunaire
define('FIELDS_BY_MOONBASIS_LEVEL', 15);
// Nombre maximum d'element dans la liste de construction de batiments
define('MAX_BUILDING_QUEUE_SIZE'  , 1);
// Nombre maximum d'element dans une ligne de liste de construction flotte et defenses
define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
// Taux de depassement possible dans l'espace de stockage des hangards ...
// 1.0 pour 100% - 1.1 pour 110% etc ...
define('MAX_OVERFLOW'             , 1);

// Valeurs de bases pour les colonies ou planetes fraichement crées
define('BASE_STORAGE_SIZE', 500000);
define('BUILD_METAL'      , 500);
define('BUILD_CRISTAL'    , 500);
define('BUILD_DEUTERIUM'  , 0);

//hide 1st player from stats
define('HIDE_1ST_FROM_STATS', 0);

//deny access to building records page
define('HIDE_BUILDING_RECORDS', 0);

//darkmater
define('DARKMATER_COST' , 3);

//galaxy_show_admins
define('SHOW_ADMIN', 1);

define('VOCATION_TIME', 2*24*60*60); // 48 hours

// Mot qui sont interdit a la saisie !
$ListCensure = array ( '/</', '/>/', '/script/i', '/doquery/i', '/http/i', '/javascript/i');

// Mission Target constants starts with MT_
define('MT_ATTACK',    1);
define('MT_AKS',       2);
define('MT_TRANSPORT', 3);
define('MT_RELOCATE',  4);
define('MT_HOLD',      5);
define('MT_SPY',       6);
define('MT_COLONIZE',  7);
define('MT_RECYCLE',   8);
define('MT_DESTROY',   9);
define('MT_MISSILE',  10);
define('MT_EXPLORE',  15);

// Planet Target constants starts with PT_
define('PT_PLANET', 1);
define('PT_DEBRIS', 2);
define('PT_MOON',   3);

// Login statuses
define('LOGIN_SUCCESS'               , 1);
define('LOGIN_SUCCESS_CREATE_PROFILE', 2);
define('LOGIN_ERROR_PASSWORD'        , 3);
define('LOGIN_ERROR_USERNAME'        , 4);
define('LOGIN_ERROR_ACTIVE'          , 5);
define('LOGIN_ERROR_EXTERNAL_AUTH'   , 6);

?>
