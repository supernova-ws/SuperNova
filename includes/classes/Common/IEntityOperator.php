<?php

namespace Common;

use IEntityContainer;

interface IEntityOperator {
  /**
   * @param IEntityModel $cModel
   * @param int|string   $dbId
   *
   * @return array
   */
  public function getById($cModel, $dbId);

  /**
   * @param IEntityModel $cModel
   * @param int|string   $dbId
   *
   * @return int
   */
  public function deleteById($cModel, $dbId);

  /**
   * @param IEntityModel $cModel
   * @param array        $row
   *
   * @return int|string
   */
  public function insert($cModel, $row);

}
