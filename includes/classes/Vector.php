<?php

class Vector {

  public $galaxy = 0;
  public $system = 0;
  public $planet = 0;
  public $type = PT_NONE;

  /**
   * UniverseVector constructor.
   *
   * @param int|string       $galaxy
   * @param int|Vector|array $system
   * @param int              $planet
   * @param int              $type
   */
  public function __construct($galaxy = 0, $system = 0, $planet = 0, $type = PT_NONE) {
    if(is_string($galaxy) && $galaxy == VECTOR_READ_VECTOR && is_object($system) && $system instanceof Vector) {
      $this->readFromVector($system);
    } elseif(is_string($galaxy) && $galaxy == VECTOR_READ_PARAMS && is_array($system)) {
      $this->readFromParamFleets($system);
    } else {
      $this->galaxy = $galaxy;
      $this->system = $system;
      $this->planet = $planet;
      $this->type = $type;
    }
  }


  public function readFromParamFleets($planetrow = array()) {
    $this->galaxy = sys_get_param_int('galaxy', $planetrow['galaxy']);
    $this->system = sys_get_param_int('system', $planetrow['system']);
    $this->planet = sys_get_param_int('planet', $planetrow['planet']);
    $this->type = sys_get_param_int('planet_type', $planetrow['planet_type']);
  }

  /**
   * @param Vector $vector
   */
  public function readFromVector($vector) {
    $this->galaxy = $vector->galaxy;
    $this->system = $vector->system;
    $this->planet = $vector->planet;
    $this->type = $vector->type;
  }

  public function compareWithPlanetRow($planetRow) {

  }

}
