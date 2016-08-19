<?php

namespace Common;


interface IUnitLocationV2 {

  /**
   * Return location type
   *
   * @param \Entity\EntityContainer $cEntity
   *
   * @return int
   */
  public function getLocationType($cEntity);

  /**
   * Return location ID
   *
   * @param \Entity\EntityContainer $cEntity
   *
   * @return mixed
   */
  public function getLocationId($cEntity);

}