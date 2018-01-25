<?php
/**
 * Created by Gorlum 08.01.2018 14:46
 */

namespace Core;


use \DBAL\ActiveRecord;
use Traits\TContainer;

/**
 * Class EntityDb
 *
 * Represents in-game entity which have representation in DB (aka one or more connected ActiveRecords)
 *
 * @package Core
 */
class EntityDb extends Entity implements \IContainer {
  use TContainer;

  /**
   * @var string $_activeClass
   */
  protected $_activeClass = ''; // \\DBAL\\ActiveRecord

  /**
   * @var ActiveRecord $_container
   */
  protected $_container;

  /**
   * @return ActiveRecord
   */
  public function _getContainer() {
    return $this->_container;
  }

  /**
   * EntityDb constructor.
   *
   * @param int $id
   */
  public function __construct($id = 0) {
    $this->dbLoadRecord($id);
  }

  /**
   * @param int|float $id
   *
   * @return ActiveRecord
   */
  public function dbLoadRecord($id) {
    $className = $this->_activeClass;
    $this->_container = $className::findById($id);

    return $this->_container;
  }

  /**
   *
   */
  public function dbUpdate() {
    $this->_getContainer()->update();
  }

  /**
   * @return array
   */
  public function asArray() {
    return $this->_getContainer()->asArray();
  }

}
