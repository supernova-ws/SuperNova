<?php


/**
 * constants.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// ----------------------------------------------------------------------------------------------------------------

if ( defined('INSIDE') ) {
  define('ADMINEMAIL'               , "gorlum@triolan.ua");
  define('GAMEURL'                  , "http://".$_SERVER['HTTP_HOST']."/");

  define('MAX_ATTACK_ROUNDS', 10);

  // Definition du monde connu !
  define('MAX_GALAXY_IN_WORLD'      , 9);
  define('MAX_SYSTEM_IN_GALAXY'     , 499);
  define('MAX_PLANET_IN_SYSTEM'     , 15);
  // Nombre de colones pour les rapports d'espionnage
  define('SPY_REPORT_ROW'           , 1);
  // Cases données par niveau de Base Lunaire
  define('FIELDS_BY_MOONBASIS_LEVEL', 15);
  // Nombre maximum de colonie par joueur
  define('MAX_PLAYER_PLANETS'       , 9);
  // Nombre maximum d'element dans la liste de construction de batiments
  define('MAX_BUILDING_QUEUE_SIZE'  , 1);
  // Nombre maximum d'element dans une ligne de liste de construction flotte et defenses
  define('MAX_FLEET_OR_DEFS_PER_ROW', 2000);
  // Taux de depassement possible dans l'espace de stockage des hangards ...
  // 1.0 pour 100% - 1.1 pour 110% etc ...
  define('MAX_OVERFLOW'             , 1.00001);

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

  //market
  define('MARKET_MERCHANT', 1);
  define('MARKET_SCRAPER' , 1);

    define('RINOK_LOM'              , 1);
    define('RINOK_FLOT'             , 1);

  //add darkmater then level up
  define('EXP_MULTI_ECO'   , 50);   // Economy to dark matter divisor
  define('EXP_MULTI_RAID'  , 10);   // Raid to dark mater divisor
  define('MAX_ECONOMIC_LVL', 150);  // Maximum economy level

  //galaxy_show_admins
  define('SHOW_ADMIN', 1);

  define('VOCATION_TIME', 2*24*60*60); // 48 hours


  // Debug Level
  define('DEBUG', 1); // Debugging off
  // Mot qui sont interdit a la saisie !
  $ListCensure = array ( "<", ">", "script", "doquery", "http", "javascript");

  // Mission Target constants starts with "MT_"
  define('MT_ATTACK',    1);
  define('MT_AKS',       2);
  define('MT_TRANSPORT', 3);
  define('MT_RELOCATE',  4);
  define('MT_HOLD',      5);
  define('MT_SPY',       6);
  define('MT_COLONIZE',  7);
  define('MT_RECYCLE',   8);
  define('MT_DESTROY',   9);
  define('MT_EXPLORE',  15);


  // Planet Target constants starts with "PT_"
  define('PT_PLANET', 1);
  define('PT_DEBRIS', 2);
  define('PT_MOON',   3);

  define('DATE_TIME', 'd.m.Y H:i:s');

  $game_config_default = array(
    'BannerOverviewFrame' => 1,
    'BannerURL' => "/scripts/createbanner.php",
    'banner_source_post' => "../images/bann.png",
    'BuildLabWhileRun' => 0,
    'close_reason' => "gGame is in maintenance mode! Please return later!",
    'COOKIE_NAME' => "gGame",
    'crystal_basic_income' => 20,
    'debug' => 0,
    'Defs_Cdr' => 30,
    'deuterium_basic_income' => 0,
    'energy_basic_income' => 0,
    'Fleet_Cdr' => 30,
    'fleet_speed' => 2500,
    'ForumUserBarFrame' => 1,
    'forum_url' => "/forum/",
    'game_disable' => 0,
    'game_name' => "gGame",
    'game_speed' => 2500,
    'initial_fields' => 163,
    'LastSettedGalaxyPos' => 0,
    'LastSettedPlanetPos' => 0,
    'LastSettedSystemPos' => 0,
    'metal_basic_income' => 40,
    'noobprotection' => 1,
    'noobprotectionmulti' => 5,
    'noobprotectiontime' => 5000,
    'OverviewBanner' => 1,
    'OverviewClickBanner' => "",
    'OverviewExternChat' => 0,
    'OverviewExternChatCmd' => "",
    'OverviewNewsFrame' => "1",
    'OverviewNewsText' => "Welcome to gGame!",
    'resource_multiplier' => 1,
    'urlaubs_modus_erz' => 0,
    'UserbarOverviewFrame' => 1,
    'UserbarURL' => "/scripts/userbar.php",
    'userbar_source' => "../images/userbar.png",
    'users_amount' => 0
  );
} else {
  die("Hacking attempt");
}
?>
