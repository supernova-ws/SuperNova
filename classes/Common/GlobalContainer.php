<?php

namespace Common;

use \classSupernova;

/**
 * Class GlobalContainer
 *
 * Used to describe internal structures of container
 *
 * Variables ------------------------------------------------------------------------------------------------------------
 * @property string       $cachePrefix
 *
 * Services ------------------------------------------------------------------------------------------------------------
 * @property \debug       $debug
 * @property \db_mysql    $db
 * @property \classCache  $cache
 * @property \classConfig $config
 * @property \Repository  $repository
 * @property \Storage     $storage
 *
 * Models --------------------------------------------------------------------------------------------------------------
 * @property \TextModel   $textModel
 *
 * @package Common
 *
 */

// * Unused --------------------------------------------------------------------------------------------------------------
// * @property \Common\Types        $types
// * @property \DbQueryConstructor  $query
// * @property \DbRowDirectOperator $dbGlobalRowOperator
// * @property \SnDbCachedOperator $cacheOperator - really DB record operator. But let it be
// * @property \classLocale         $localePlayer
// *
// * @property string $snCacheClass
// * @property \SnCache $snCache
// *
// * @property string $buddyClass
// * @property \Buddy\BuddyModel $buddyModel
// *
// * @property \V2Unit\V2UnitModel $unitModel
// * @property \V2Unit\V2UnitList $unitList
// *
// * @property V2FleetModel $fleetModel
// *
// * @property PlanetRenderer $planetRenderer
// * @property \FleetRenderer $fleetRenderer
// * @property $dbOperator - makes CRUD to DB:

class GlobalContainer extends ContainerPlus {

  public function __construct(array $values = array()) {
    parent::__construct($values);

    $gc = $this;

    // Services --------------------------------------------------------------------------------------------------------
    // Default db
    $gc->db = function (GlobalContainer $c) {
      classSupernova::$db = new \db_mysql($c);

      return classSupernova::$db;
    };

    $gc->debug = function (GlobalContainer $c) {
      return new \debug();
    };

    $gc->cache = function (GlobalContainer $gc) {
      return new \classCache($gc->cachePrefix);
    };

    $gc->config = function (GlobalContainer $gc) {
      return new \classConfig($gc->cachePrefix);
    };


    $gc->repository = function (GlobalContainer $gc) {
      return new \Repository($gc);
    };

    $gc->storage = function (GlobalContainer $gc) {
      return new \Storage($gc);
    };


    // Models ----------------------------------------------------------------------------------------------------------
    $gc->textModel = function (GlobalContainer $gc) {
      return new \TextModel($gc);
    };


//    $gc->types = function ($c) {
//      return new \Common\Types();
//    };
//
//    $gc->dbOperator = function (GlobalContainer $c) {
//      return new \classConfig(classSupernova::$cache_prefix);
//    };
//
//    $gc->localePlayer = function (GlobalContainer $c) {
//      return new \classLocale($c->config->server_locale_log_usage);
//    };
//
//    $gc->dbGlobalRowOperator = function (GlobalContainer $c) {
//      return new \DbRowDirectOperator($c->db);
//    };
//
//    $gc->query = $gc->factory(function (GlobalContainer $c) {
//      return new \DbQueryConstructor($c->db);
//    });
//
//    $gc->cacheOperator = function (GlobalContainer $gc) {
//      return new \SnDbCachedOperator($gc);
//    };
//
//    $gc->snCacheClass = 'SnCache';
//    $gc->snCache = function (GlobalContainer $gc) {
//      return $gc->db->snCache;
//    };
//
//    $gc->buddyClass = 'Buddy\BuddyModel';
//    $gc->buddyModel = function (GlobalContainer $c) {
//      return new $c->buddyClass($c);
//    };
//
//    $gc->unitModel = function (GlobalContainer $c) {
//      return new \V2Unit\V2UnitModel($c);
//    };
//    $gc->unitList = $this->factory(function (GlobalContainer $c) {
//      return new \V2Unit\V2UnitList($c);
//    });
//
//    $gc->fleetModel = function (GlobalContainer $c) {
//      return new V2FleetModel($c);
//    };
//
//    $gc->planetRenderer = function (GlobalContainer $c) {
//      return new PlanetRenderer($c);
//    };
//
//    $gc->fleetRenderer = function (GlobalContainer $c) {
//      return new \FleetRenderer($c);
//    };
//
//    $gc->groupFleet = function (GlobalContainer $c) {
//      return sn_get_groups('fleet');
//    };
//
//    $gc->groupFleetAndMissiles = function (GlobalContainer $c) {
//      return sn_get_groups(array('fleet', GROUP_STR_MISSILES));
//    };
//
//    $gc->groupRecyclers = function (GlobalContainer $c) {
//      return sn_get_groups('flt_recyclers');
//    };

  }

}
