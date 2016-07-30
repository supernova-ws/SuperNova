<?php

namespace Common;

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
 * @property string               $buddyClass
 * @property \Buddy\BuddyModel    $buddy
 * @property \V2Unit\V2UnitModel  $unit
 * @property \DbQueryConstructor  $query
 * @property \DbRowDirectOperator $dbRowOperator
 *
 * property Vector              $vector // TODO
 * method \Buddy\Buddy rowGetById(GlobalContainer $container, \Buddy\Buddy $object, int $buddy_id)
 */
class GlobalContainer extends ContainerPlus {

}
