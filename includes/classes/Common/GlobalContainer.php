<?php

namespace Common;

use \classSupernova;

/**
 * Class GlobalContainer
 *
 * Used to describe internal structures of container
 *
 * @package Pimple
 *
 * @property \debug               $debug
 * @property \db_mysql            $db
 * @property \classCache          $cache
 * @property \classConfig         $config
 * @property \classLocale         $localePlayer
 * @property \DbQueryConstructor  $query
 * @property \DbRowDirectOperator $dbGlobalRowOperator
 * @property \SnDbCachedOperator  $cacheOperator - really DB record operator. But let it be
 * @property string               $snCacheClass
 * @property \SnCache             $snCache
 * @property \Common\Types        $types
 * @property string               $buddyClass
 * @property \Buddy\BuddyModel    $buddyModel
 * @property \V2Unit\V2UnitModel  $unitModel
 */
class GlobalContainer extends ContainerPlus {

  public function __construct(array $values = array()) {
    parent::__construct($values);

    $gc = $this;

    // Default db
    $gc->db = function ($c) {
      classSupernova::$db = $db = new \db_mysql($c);

      return $db;
    };

    $gc->debug = function ($c) {
      return new \debug();
    };

    $gc->types = function ($c) {
      return new \Common\Types();
    };

    $gc->cache = function ($c) {
      return new \classCache(classSupernova::$cache_prefix);
    };

    $gc->config = function ($c) {
      return new \classConfig(classSupernova::$cache_prefix);
    };

    $gc->localePlayer = function (GlobalContainer $c) {
      return new \classLocale($c->config->server_locale_log_usage);
    };

    $gc->dbGlobalRowOperator = function (GlobalContainer $c) {
      return new \DbRowDirectOperator($c->db);
    };

    $gc->query = $gc->factory(function (GlobalContainer $c) {
      return new \DbQueryConstructor($c->db);
    });

    $gc->cacheOperator = function (GlobalContainer $gc) {
      return new \SnDbCachedOperator($gc);
    };

    $gc->snCacheClass = 'SnCache';
    $gc->snCache = function (GlobalContainer $gc) {
      return $gc->db->snCache;
    };

    $gc->buddyClass = 'Buddy\BuddyModel';
    $gc->buddyModel = function (GlobalContainer $c) {
      return new $c->buddyClass($c);
    };

    $gc->unitModel = function (GlobalContainer $c) {
      return new \V2Unit\V2UnitModel($c);
    };
  }

}
