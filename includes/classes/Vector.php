<?php

class Vector {

  public static $knownGalaxies = 0;
  public static $knownSystems = 0;
  public static $knownPlanets = 0;
  protected static $_isStaticInit = false;

  public $galaxy = 0;
  public $system = 0;
  public $planet = 0;
  public $type = PT_NONE;

  public static function _staticInit() {
    if(static::$_isStaticInit) {
      return;
    }
    static::$knownGalaxies = intval(classSupernova::$config->game_maxGalaxy);
    static::$knownSystems = intval(classSupernova::$config->game_maxSystem);
    static::$knownPlanets = intval(classSupernova::$config->game_maxPlanet);
    static::$_isStaticInit = true;
  }

  /**
   * UniverseVector constructor.
   *
   * @param int|string       $galaxy
   * @param int|Vector|array $system
   * @param int              $planet
   * @param int              $type
   */
  public function __construct($galaxy = 0, $system = 0, $planet = 0, $type = PT_NONE) {
    // static::_staticInit();

    if(is_string($galaxy) && $galaxy == VECTOR_READ_VECTOR && is_object($system) && $system instanceof Vector) {
      $this->readFromVector($system);
    } elseif(is_string($galaxy) && $galaxy == VECTOR_READ_PARAMS && is_array($system)) {
      $this->readFromParamFleets($system);
    } else {
      $this->galaxy = intval($galaxy);
      $this->system = intval($system);
      $this->planet = intval($planet);
      $this->type = intval($type);
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

  /**
   * @param array $planetRow
   */
  public function isEqualToPlanet($planetRow) {
    return $this->distanceFromCoordinates($planetRow) == 0;
  }

  /**
   * @param Vector $vector
   */
  public function distance($vector) {
    if($this->galaxy != $vector->galaxy) {
      $distance = abs($this->galaxy - $vector->galaxy) * classSupernova::$config->uni_galaxy_distance;
    } elseif($this->system != $vector->system) {
      $distance = abs($this->system - $vector->system) * 5 * 19 + 2700;
    } elseif($this->planet != $vector->planet) {
      $distance = abs($this->planet - $vector->planet) * 5 + 1000;
      // TODO - uncomment
//    } elseif($this->type != PT_NONE && $vector->type != PT_NONE && $this->type == $vector->type) {
//      $distance = 0;
    } else {
      $distance = 5;
    }

    return $distance;
  }

  /**
   * @param array $coordinates
   */
  public function distanceFromCoordinates($coordinates) {
    return $this->distance(static::convertToVector($coordinates));
  }

  /**
   * @param array $coordinates
   */
  public static function convertToVector($coordinates, $prefix = '') {
    $galaxy = !empty($coordinates[$prefix . 'galaxy']) ? intval($coordinates[$prefix . 'galaxy']) : 0;
    $system = !empty($coordinates[$prefix . 'system']) ? intval($coordinates[$prefix . 'system']) : 0;
    $planet = !empty($coordinates[$prefix . 'planet']) ? intval($coordinates[$prefix . 'planet']) : 0;
    $type = !empty($coordinates[$prefix . 'type'])
      ? intval($coordinates[$prefix . 'type'])
      : (!empty($coordinates[$prefix . 'planet_type']) ? intval($coordinates[$prefix . 'planet_type']) : 0);

    return new static($galaxy, $system, $planet, $type);
  }

  public static function distanceBetweenCoordinates($from, $to) {
    $fromVector = static::convertToVector($from);

    return $fromVector->distanceFromCoordinates($to);
  }

  /**
   * @return bool
   */
  public function isInUniverse() {
    return
      $this->galaxy > 0 && $this->galaxy <= static::$knownGalaxies &&
      $this->system > 0 && $this->system <= static::$knownSystems;
  }

  /**
   * @return bool
   */
  public function isInSystem() {
    return $this->planet > 0 && $this->planet <= static::$knownPlanets;
  }

  /**
   * @return bool
   */
  public function isInKnownSpace() {
    return $this->isInUniverse() && $this->isInSystem();
  }

}

Vector::_staticInit();
