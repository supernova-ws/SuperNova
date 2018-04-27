<?php
/**
 * Created by Gorlum 08.01.2018 14:46
 */

namespace Core;


use Common\Interfaces\IContainer;
use \DBAL\DbQuery;
use \DBAL\ActiveRecord;
use Common\Traits\TContainer;
use Exception;
use SN;

/**
 * Basic persistent entity class (lives in DB)
 *
 * Support direct ActiveRecord access
 * Support locked reads on transactions
 *
 * Represents in-game entity which have representation in DB (aka one or more connected ActiveRecords)
 *
 * @package Core
 *
 * @property int|string $id - bigint -
 *
 * @method array asArray() Extracts values as array [$propertyName => $propertyValue] (from ActiveRecord)
 * @method bool update() Updates DB record(s) in DB (from ActiveRecord)
 */
class EntityDb extends Entity implements IContainer {
  use TContainer;

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = ''; // \\DBAL\\ActiveRecord

  /**
   * Name translation table
   *
   * [entityPropertyName => containerPropertyName]
   *
   * @var string[] $_containerTranslateNames
   */
  protected $_containerTranslateNames = [];


  /**
   * @var ActiveRecord $_container
   */
  protected $_container;

  protected $_isNew = true;
  protected $_forUpdate = DbQuery::DB_SHARED;

  /**
   * EntityDb constructor.
   */
  public function __construct() {
    if (empty($this->_activeClass)) {
      /** @noinspection PhpUnhandledExceptionInspection */
      throw new Exception("Class " . get_called_class() . " have no _activeClass property declared!");
    }

    $this->reset();
  }

  /**
   * @return $this
   */
  public function reset() {
//    unset($this->_container); // Strange thing - after this string it is not possible to set _container property again!
    $this->_container = new $this->_activeClass();

    $this->_isNew = true;
    $this->_forUpdate = DbQuery::DB_SHARED;

    return $this;
  }

  /**
   * Reload Entity - if possible
   *
   * If entity is write-once - this method should decide if any real I/O would be performed
   *
   * @return static
   *
   * @throws Exception
   */
  public function reload() {
    // TODO - immutable entities which does not need reload
    $dbId = !empty($this->_container) ? $this->_container->id : 0;

    // If entity is new or no DB ID supplied - throw exception
    if ($this->isNew() || !$dbId) {
      /** @noinspection PhpUnhandledExceptionInspection */
      throw new Exception("Can't reload empty or new Entity: isNew = '{$this->isNew()}', dbId = '{$dbId}', className = " . get_called_class());
    }

    return $this->dbLoadRecord($dbId);
  }


  /**
   * Is entity is new
   *
   * True for newly created and deleted entities
   * Code should decide this from context
   *
   * @return bool
   */
  public function isNew() {
    return $this->_isNew;
  }

  /**
   * Load entity from DB
   *
   * This basic class supports transaction locking
   *
   * If entity is multi-tabled - this method should care about loading and arrange all necessary information
   * If descendant entity is lock-sensitive - this method should issue all necessary locks (including parental locks)
   *
   * @param int|float $id
   *
   * @return $this
   */
  public function dbLoadRecord($id) {
    $this->reset();

    if (sn_db_transaction_check(false)) {
      $this->setForUpdate();
    }

    /** @var ActiveRecord $className */
    $className = $this->_activeClass;
    $container = $className::findById($id);
    if (!empty($container)) {
      $this->_isNew = false;
      $this->_container = $container;
    }

    return $this;
  }

  /**
   * Set "for update" flag
   *
   * @param bool $forUpdate - DbQuery::DB_FOR_UPDATE | DbQuery::DB_SHARED
   *
   * @return $this
   */
  public function setForUpdate($forUpdate = DbQuery::DB_FOR_UPDATE) {
    /** @var ActiveRecord $className */
    $className = $this->_activeClass;
    $className::setForUpdate($forUpdate);

    return $this;
  }

  /**
   * @return bool
   */
  public function dbUpdate() {
    return $this->_getContainer()->update();
  }

  /**
   * @return ActiveRecord
   */
  public function _getContainer() {
    return $this->_container;
  }

  /**
   * Translate entity property name to container property name
   *
   * Just a little sugar to avoid renaming all and everywhere
   *
   * @param string $name
   *
   * @return string
   */
  protected function _containerTranslatePropertyName($name) {
    return !empty($this->_containerTranslateNames[$name]) ? $this->_containerTranslateNames[$name] : $name;
  }

  /**
   * Saves entity to DB. Also handles updates (and in future - deletes. DELETE CURRENTLY NOT SUPPORTED!)
   *
   * @return bool
   * @throws \Exception
   */
  public function save() {
    $result = false;

    if ($this->isNew()) {
      // New record - INSERT

      // TODO - some checks that fleet recrod is valid itself
      // May be something like method isValid()?

      $result = !$this->isEmpty() && $this->_getContainer()->insert() && SN::$gc->repoV2->set($this);
    } elseif (!$this->isEmpty()) {
      // Record not new and not empty - UPDATE
      $result = $this->_getContainer()->update();
    } else {
      // Record not new and empty - DELETE

    }

    return $result;
  }

}
