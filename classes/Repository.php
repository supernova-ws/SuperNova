<?php

/**
 * Created by Gorlum 10.02.2017 0:07
 */

use \Common\ContainerPlus;
use \Common\GlobalContainer;

/**
 * Class Repository
 *
 * Holds current entity objects
 */
class Repository {

  /**
   * @var ContainerPlus $repository
   */
  protected $repository;

  /**
   * Repository constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
  }

  /**
   * @param TextModel  $model
   * @param int|string $id
   *
   * @return TextEntity
   */
  public function getById($model, $id) {

    // TODO - is_object()

    // Index is fully qualified class name plus ID like Namespace\ClassName\$id
    $entityIndex = get_class($model) . '\\' . $id;
    if (!isset($this->repository[$entityIndex])) {
      $entity = $model->loadById($id);
      if ($entity && !$entity->isEmpty()) {
        $this->repository[$entityIndex] = $entity;
      }
    } else {
      $entity = $this->repository[$entityIndex];
    }

    return $entity;
  }

}
