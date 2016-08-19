<?php

namespace Vector;

use classConfig;

class Vector {

  const READ_VECTOR = 'readVector';
  const READ_PARAMS_FLEET = 'readParamsFleet';

  public static $knownGalaxies = 0;
  public static $knownSystems = 0;
  public static $knownPlanets = 0;
  public static $galaxyDistance = 20000;
  protected static $_isStaticInit = false;

  public $galaxy = 0;
  public $system = 0;
  public $planet = 0;
  public $type = PT_NONE;

  /**
   * @param classConfig $config
   */
  public static function _staticInit($config) {
    if (static::$_isStaticInit) {
      return;
    }

    static::$knownGalaxies = intval($config->game_maxGalaxy);
    static::$knownSystems = intval($config->game_maxSystem);
    static::$knownPlanets = intval($config->game_maxPlanet);
    static::$galaxyDistance = intval($config->uni_galaxy_distance);
    static::$_isStaticInit = true;
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
   * @param string $paramName
   * @param array  $planetRow
   *
   * @return int
   */
  protected function getParamInt($paramName, $planetRow) {
    $default = empty($planetRow[$paramName]) ? 0 : $planetRow[$paramName];

    return sys_get_param_int($paramName, $default);
  }

  /**
   * @param array $planetRow
   */
  public function readFromParamFleets($planetRow = array()) {
    $this->galaxy = $this->getParamInt('galaxy', $planetRow);
    $this->system = $this->getParamInt('system', $planetRow);
    $this->planet = $this->getParamInt('planet', $planetRow);
    $this->type = $this->getParamInt('planet_type', $planetRow);
  }

  /**
   * Vector constructor.
   *
   * @param int|string       $galaxy
   * @param int|Vector|array $system
   * @param int              $planet
   * @param int              $type
   */
  public function __construct($galaxy = 0, $system = 0, $planet = 0, $type = PT_NONE) {
    // static::_staticInit();

    if (is_string($galaxy) && $galaxy == Vector::READ_VECTOR && is_object($system) && $system instanceof Vector) {
      $this->readFromVector($system);
    } elseif (is_string($galaxy) && $galaxy == Vector::READ_PARAMS_FLEET && is_array($system)) {
      $this->readFromParamFleets($system);
    } else {
      $this->galaxy = intval($galaxy);
      $this->system = intval($system);
      $this->planet = intval($planet);
      $this->type = intval($type);
    }
  }

  /**
   * @param Vector $vector
   * @param bool   $returnZero
   *
   * @return int|number
   */
  public function distance($vector, $returnZero = false) {
    if ($this->galaxy != $vector->galaxy) {
      $distance = abs($this->galaxy - $vector->galaxy) * static::$galaxyDistance;
    } elseif ($this->system != $vector->system) {
      $distance = abs($this->system - $vector->system) * 5 * 19 + 2700;
    } elseif ($this->planet != $vector->planet) {
      $distance = abs($this->planet - $vector->planet) * 5 + 1000;
    } elseif ($returnZero && $this->type == $vector->type) {
      // && $this->type != PT_NONE && $vector->type != PT_NONE
      $distance = 0;
    } else {
      $distance = 5;
    }

    return $distance;
  }

  /**
   * @param array  $coordinates
   * @param string $prefix
   *
   * @return static
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

  /**
   * @param array $coordinates
   * @param bool  $returnZero
   *
   * @return int|number
   */
  public function distanceFromCoordinates($coordinates, $returnZero = false) {
    return $this->distance(static::convertToVector($coordinates), $returnZero);
  }

  /**
   * @param array $from
   * @param array $to
   * @param bool  $returnZero
   *
   * @return int|number
   */
  public static function distanceBetweenCoordinates($from, $to, $returnZero = false) {
    return static::convertToVector($from)->distanceFromCoordinates($to, $returnZero);
  }

  /**
   * @param array $planetRow
   *
   * @return bool
   */
  public function isSameLocation($planetRow) {
    return $this->distanceFromCoordinates($planetRow, true) == 0;
  }

  /**
   * @return bool
   */
  public function isInUniverse() {
    return
      $this->galaxy >= 1 && $this->galaxy <= static::$knownGalaxies &&
      $this->system >= 1 && $this->system <= static::$knownSystems;
  }

  /**
   * @return bool
   */
  public function isInSystem() {
    return $this->planet >= 1 && $this->planet <= static::$knownPlanets;
  }

  /**
   * @return bool
   */
  public function isInKnownSpace() {
    return $this->isInUniverse() && $this->isInSystem();
  }

}
