<?php

namespace Common;

interface IEntityOperator {
  /**
   * @param IEntity $entity
   */
  public function getById($entity);

  /**
   * @param IEntity $entity
   */
  public function deleteById($entity);

  /**
   * @param IEntity $entity
   */
  public function insert($entity);

}
