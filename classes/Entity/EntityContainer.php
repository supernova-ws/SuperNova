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

  protected function processNumeric($name, $value) {
    if(!is_int($value) && !is_float($value)) {
      return;
    }

    // If no original value and new value is set - then we take new value as old value
    if(empty($this->original[$name]) && !empty($value)) {
      $this->original[$name] = $value;
    } elseif(!empty($this->original[$name]) && $value != $this->original[$name]) {
      // New value not equal original value. We should update delta
      $this->delta[$name] = $value - $this->original[$name];
    }
  }

  public function __set($name, $value) {
    $properties = $this->model->getProperties();
    if(isset($properties[$name][P_DB_FIELD_TYPE])) {
      $value = \classSupernova::$gc->types->castAs($properties[$name][P_DB_FIELD_TYPE], $value);
    }

    parent::__set($name, $value);
    $this->processNumeric($name, $value);
  }

}
