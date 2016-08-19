<?php

/**
 * Class EntityContainer
 *
 *
 * @property array $row - Entity row read from DB
 */
class EntityContainer extends ContainerAccessors {
  /**
   * @var EntityModel $model
   */
  protected $model;

  /**
   * EntityContainer constructor.
   * @param EntityModel $model
   */
  public function __construct($model) {
    $this->model = $model;
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

  /**
   * @param string   $varName
   * @param string   $processor
   * @param callable $callable
   */
  public function setAccessor($varName, $processor, $callable) {
    $this->model->setAccessor($varName, $processor, $callable);
  }

  /**
   * @param $varName
   * @param $processor
   *
   * @return callable|null
   */
  protected function getAccessor($varName, $processor) {
    return $this->model->getAccessor($varName, $processor);
  }

}
