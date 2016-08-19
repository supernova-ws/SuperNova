<?php

namespace V2Unit;


use Common\GlobalContainer;

class UnitFeature {

  protected $featureId = 'feature';

  public function getId() {
    return $this->featureId;
  }

  /**
   * UnitFeature constructor.
   */
  public function __construct(GlobalContainer $gc) {
    $gc->unitFeatures->featureRegister($this);
  }

}
