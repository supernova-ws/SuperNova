<?php

namespace Common;

interface IEntityOperator {
  /**
   * @param IEntity $entity
   *
   * @return array
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
