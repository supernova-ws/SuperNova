<?php

namespace Entity;

use Common\ContainerAccessors;

/**
 * Class Entity\EntityContainer
 *
 * @property array $row - Entity row read from DB
 */
class EntityContainer extends ContainerAccessors {
  /**
   * @var EntityModel $model
   */
  protected $model;

  /**
   * @var \Common\Accessors $accessors
   */
  protected $accessors;

  /**
   * @var array $original
   */
  protected $original = array();

  /**
   * @var array $delta
   */
  protected $delta = array();

  /** @noinspection PhpMissingParentConstructorInspection */
  /**
   * Entity\EntityContainer constructor.
   *
   * @param EntityModel $model
   */
  public function __construct($model) {
    $this->setModel($model);
  }

  /**
   * @param EntityModel $model
   */
  public function setModel(EntityModel $model) {
    $this->model = $model;
    $this->accessors = $model->getAccessors();
  }

  /**
   * @return EntityModel
   */
  public function getModel() {
    return $this->model;
  }

  public function __set($name, $value) {
    $properties = $this->model->getProperties();
    if (isset($properties[$name][P_DB_FIELD_TYPE])) {
      $value = \classSupernova::$gc->types->castAs($properties[$name][P_DB_FIELD_TYPE], $value);
    }

    parent::__set($name, $value);
    // If it is first assign - saving this value as original
    if (!array_key_exists($name, $this->original)) {
      $this->original[$name] = $value;
    } // If it is not first assign
    elseif ($value != $this->original[$name]) {
      $this->delta[$name] = $value;
      // New value not equal original value. We should update delta
      if((is_int($value) || is_float($value))) {
        $this->delta[$name] -= $this->original[$name];
      }
    }
  }

  public function clear() {
    parent::clear();
    $this->original = array();
    $this->delta = array();
  }


  public function isChanged() {
    return !empty($this->delta);
  }

  public function getDeltas() {
    return $this->delta;
  }

}
