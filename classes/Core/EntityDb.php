<?php
/**
 * Created by Gorlum 08.01.2018 14:46
 */

namespace Core;


use \DBAL\DbQuery;
use \DBAL\ActiveRecord;
use Common\Traits\TContainer;

/**
 * Class EntityDb
 *
 * Represents in-game entity which have representation in DB (aka one or more connected ActiveRecords)
 *
 * @package Core
 *
 * @method array asArray() Extracts values as array [$propertyName => $propertyValue] (from ActiveRecord)
 * @method bool update() Updates DB record(s) in DB (from ActiveRecord)
 */
class EntityDb extends Entity implements \Common\Interfaces\IContainer {
  use TContainer;

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = ''; // \\DBAL\\ActiveRecord

  /**
   * @var ActiveRecord $_container
   */
  protected $_container;

  protected $_isNew = true;
  protected $_isDeleted = false;

  /**
   * @return ActiveRecord
   */
  public function _getContainer() {
    return $this->_container;
  }

  /**
   * EntityDb constructor.
   */
  public function __construct() {
    $this->reset();
//    $this->dbLoadRecord($id);
  }

  /**
   * Set flag "for update"
   *
   * @param bool $forUpdate - DbQuery::DB_FOR_UPDATE | DbQuery::DB_SHARED
   */
  public function setForUpdate($forUpdate = DbQuery::DB_FOR_UPDATE) {
    $className = $this->_activeClass;
    /**
     * @var ActiveRecord $className
     */
    $className::setForUpdate($forUpdate);

    return $this;
  }

  /**
   * @param int|float $id
   *
   * @return static
   */
  public function dbLoadRecord($id) {
    $this->reset();

    /**
     * @var ActiveRecord $className
     */
    $className = $this->_activeClass;
    $container = $className::findById($id);
    if(!empty($container)) {
      $this->_isNew = false;
      $this->_container = $container;
    }

    return $this;
  }

  /**
   *
   */
  public function dbUpdate() {
    $this->_getContainer()->update();
  }


  public function isNew() {
    return $this->_isNew;
  }

  public function isDeleted() {
    return $this->_isDeleted;
  }

  public function reset() {
//    unset($this->_container);
    $this->_container = new $this->_activeClass();

    $this->_isNew = true;
    $this->_isDeleted = false;

    return $this;
  }

}
