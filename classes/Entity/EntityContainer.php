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

  /** @noinspection PhpMissingParentConstructorInspection */
  /**
   * Entity\EntityContainer constructor.
   *
   * @param EntityModel $model
   */
  public function __construct($model) {
    $this->model = $model;
    $this->accessors = $model->getAccessors();
  }

  /**
   * @param EntityModel $model
   */
  public function setModel(EntityModel $model) {
    $this->model = $model;
  }

  /**
   * @return EntityModel
   */
  public function getModel() {
    return $this->model;
  }

}
