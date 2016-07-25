<?php

namespace Pimple;

use Vector\Vector;

/**
 * Class GlobalContainer
 *
 * Used to describe internal structures of container
 *
 * @package Pimple
 *
 * @property \debug              $debug
 * @property \db_mysql           $db
 * @property \classCache         $cache
 * @property \classConfig        $config
 * @property \Buddy              $buddy
 * property Vector              $vector // TODO
 * @property \DbQueryConstructor $query
 * @property \DbRowSimple        $dbRowOperator
 *
 * method \Buddy rowGetById(GlobalContainer $container, \Buddy $object, int $buddy_id)
 */
class GlobalContainer extends ContainerPlus {

}
