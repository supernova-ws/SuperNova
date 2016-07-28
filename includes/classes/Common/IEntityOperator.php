<?php

namespace Common;

use \Entity;


interface IEntityOperator {
  /**
   * @param Entity $entity
   */
  public function getById($entity);

  /**
   * @param Entity $entity
   */
  public function deleteById($entity);

  /**
   * @param Entity $entity
   */
  public function insert($entity);

}