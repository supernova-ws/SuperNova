<?php

/**
 * Created by Gorlum 10.02.2017 0:07
 */

namespace Core;

use \Common\ContainerPlus;
use Core\GlobalContainer;
use TextEntity;
use TextModel;

/**
 * Class Core\Repository
 *
 * Holds current entity objects
 *
 * @deprecated
 */
class Repository {

  /**
   * "Not an Object" marker
   */
  const NaO = '\\NaO';


  /**
   * @var ContainerPlus $_repository
   */
  protected $_repository;

 /**
   * @var ContainerPlus $_oldRepo
   */
  protected $_oldRepo;

  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  /**
   * Core\Repository constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  /**
   * @param TextModel  $model
   * @param int|string $id
   *
   * @return TextEntity
   * @deprecated
   */
  public function getById($model, $id) {

    // TODO - is_object()

    // Index is fully qualified class name plus ID like Namespace\ClassName\$id
    $entityIndex = get_class($model) . '\\' . $id;
    if (!isset($this->_oldRepo[$entityIndex])) {
      $entity = $model->loadById($id);
      if ($entity && !$entity->isEmpty()) {
        $this->_oldRepo[$entityIndex] = $entity;
      }
    } else {
      $entity = $this->_oldRepo[$entityIndex];
    }

    return $entity;
  }

  /**
   * Returns collection name for supplied object
   *
   * @param $object
   *
   * @return string
   */
  protected function getCollectionName($object) {
    return is_object($object) ? get_class($object) : self::NaO;
  }

  public function get($entityClass, $id) {
    $entityIndex = get_class($entityClass) . '\\' . $id;
    if(!isset($this->_repository[$entityIndex])) {

    }

    return $this->_repository[$entityIndex];
  }

  protected function getPool($entityClass) {

  }

  public function registerFactory($factory) {

  }

}
