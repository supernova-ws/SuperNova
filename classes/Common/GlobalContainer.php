<?php

namespace Common;

use Bonus\BonusCatalog;
use Bonus\ValueStorage;
use Bonus\ValueBonused;
use Core\SnPimp;
use Modules\ModulesManager;
use \SN;
use \General;
use \Core\Watchdog;
use \Core\Repository;
use \Event\EventBus;
use \Meta\Economic\EconomicHelper;
use Player\PlayerLevelHelper;


/**
 * Class GlobalContainer
 *
 * Used to describe internal structures of container
 *
 * Variables ------------------------------------------------------------------------------------------------------------
 * @property string                 $cachePrefix
 *
 * Services ------------------------------------------------------------------------------------------------------------
 * @property \debug                 $debug
 * @property \db_mysql              $db
 * @property \classCache            $cache
 * @property \classConfig           $config
 * @property \Core\Repository       $repository
 * @property \Storage               $storage
 * @property \Design                $design
 * @property \BBCodeParser          $bbCodeParser
 * @property \Fleet\FleetDispatcher $fleetDispatcher
 * @property Watchdog               $watchdog
 * @property EventBus               $eventBus
 *
 * @property ValueStorage           $valueStorage
 * @property BonusCatalog           $bonusCatalog
 *
 * @property General                $general
 * @property EconomicHelper         $economicHelper
 *
 * @property PlayerLevelHelper      $playerLevelHelper
 * @property SnPimp                 $pimp
 *
 * @property ModulesManager         $modules
 *
 * Dummy objects -------------------------------------------------------------------------------------------------------
 * @property \TheUser               $theUser
 *
 * Models --------------------------------------------------------------------------------------------------------------
 * @property \TextModel             $textModel
 * @property string                 $skinEntityClass
 * @property \SkinModel             $skinModel
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
      SN::$db = new \db_mysql($c);

      return SN::$db;
    };

    $gc->debug = function (/** @noinspection PhpUnusedParameterInspection */
      GlobalContainer $c) {
      return new \debug();
    };

    $gc->cache = function (GlobalContainer $gc) {
      return new \classCache($gc->cachePrefix);
    };

    $gc->config = function (GlobalContainer $gc) {
      return new \classConfig($gc->cachePrefix);
    };


    $gc->repository = function (GlobalContainer $gc) {
      return new Repository($gc);
    };

    $gc->storage = function (GlobalContainer $gc) {
      return new \Storage($gc);
    };

    $gc->design = function (GlobalContainer $gc) {
      return new \Design($gc);
    };

    $gc->bbCodeParser = function (GlobalContainer $gc) {
      return new \BBCodeParser($gc);
    };

    $gc->fleetDispatcher = function (GlobalContainer $gc) {
      return new \Fleet\FleetDispatcher($gc);
    };

    $gc->watchdog = function (GlobalContainer $gc) {
      return new Watchdog($gc);
    };

    $gc->eventBus = function (GlobalContainer $gc) {
      return new EventBus($gc);
    };

    $gc->valueStorage = function (GlobalContainer $gc) {
      return new ValueStorage([]);
    };

    $gc->bonusCatalog = function (GlobalContainer $gc) {
      return new BonusCatalog($gc);
    };

    $gc->general = function (GlobalContainer $gc) {
      return new General($gc);
    };

    $gc->economicHelper = function (GlobalContainer $gc) {
      return new EconomicHelper($gc);
    };

    $gc->playerLevelHelper = function (GlobalContainer $gc) {
      return new PlayerLevelHelper($gc);
    };

    $gc->pimp = function (GlobalContainer $gc) {
      return new SnPimp($gc);
    };

    $gc->modules = function (GlobalContainer $gc) {
      return new ModulesManager($gc);
    };

    // Dummy objects ---------------------------------------------------------------------------------------------------
    $gc->theUser = function (GlobalContainer $gc) {
      return new \TheUser($gc);
    };


    // Models ----------------------------------------------------------------------------------------------------------
    $gc->skinEntityClass = \SkinV2::class;
    $gc->skinModel = function (GlobalContainer $gc) {
      return new \SkinModel($gc);
    };

    $gc->textModel = function (GlobalContainer $gc) {
      return new \TextModel($gc);
    };


//    $gc->types = function ($c) {
//      return new \Common\Types();
//    };
//
//    $gc->dbOperator = function (GlobalContainer $c) {
//      return new \classConfig(SN::$cache_prefix);
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
