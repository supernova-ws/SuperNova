<?php

namespace Common;
use EntityContainer;
use IEntityContainer;

interface IEntityOperator {
  /**
   * @param IEntityContainer $cEntity
   *
   * @return array
   */
  public function getById($cEntity);

  /**
   * @param IEntityContainer $cEntity
   */
  public function deleteById($cEntity);

  /**
   * @param IEntityContainer $cEntity
   */
  public function insert($cEntity);

}
