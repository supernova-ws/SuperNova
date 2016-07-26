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
 * @property string              $buddyClass
 * @property \Buddy\Buddy        $buddy
 * @property \DbQueryConstructor $query
 * @property \DbRowSimple        $dbRowOperator
 *
 * property Vector              $vector // TODO
 * method \Buddy\Buddy rowGetById(GlobalContainer $container, \Buddy\Buddy $object, int $buddy_id)
 */
class GlobalContainer extends ContainerPlus {

}
