<?php
/**
 * Created by Gorlum 01.12.2017 6:54
 */

namespace Bonus;

use Common\ContainerPlus;
use Common\GlobalContainer;
use \classSupernova;


/**
 * Class ValueStorage
 *
 * Store calculated bonus values
 *
 * In future can be used to cache data in memory cache
 *
 * @package Bonus
 */
class ValueStorage extends ContainerPlus {
  // TODO UNUSED!

  /**
   * @var GlobalContainer
   */
  protected $gc;

  /**
   * @var ValueBonused[][] $values
   */
  protected $values = [];

  /**
   * @return GlobalContainer
   */
  public function getGlobalContainer() {
    return $this->gc;
  }

  public function __construct(array $values = array()) {
    parent::__construct($values);

    $this->gc = classSupernova::$gc;

    $this->initValues();
  }

  protected function initValues() {
    $this[UNIT_SERVER_SPEED_BUILDING] = function (ValueStorage $vs) {
      return new ValueBonused(UNIT_SERVER_SPEED_BUILDING, floatval($vs->getGlobalContainer()->config->game_speed));
    };
    $this[UNIT_SERVER_SPEED_MINING] = function (ValueStorage $vs) {
      return new ValueBonused(UNIT_SERVER_SPEED_MINING, floatval($vs->getGlobalContainer()->config->resource_multiplier));
    };
    $this[UNIT_SERVER_SPEED_FLEET] = function (ValueStorage $vs) {
      return new ValueBonused(UNIT_SERVER_SPEED_FLEET, floatval($vs->getGlobalContainer()->config->fleet_speed));
    };
    $this[UNIT_SERVER_SPEED_EXPEDITION] = function (ValueStorage $vs) {
      return new ValueBonused(UNIT_SERVER_SPEED_EXPEDITION, floatval(1));
    };
  }

  /**
   * Get value object for supplied ID
   *
   * Supports only server and player units... for now
   *
   * @param int   $unitSnId
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   *
   * @return ValueBonused|mixed
   */
  public function getValueObject($unitSnId, $context = []) {
    if (isset($this[$unitSnId])) {
      // Server var
      $valueObject = $this[$unitSnId];
    } else {
      // Not a server var
      $valueObject = new ValueBonused($unitSnId, $this->getLevelNonServer($unitSnId, $context));
    }

    if($valueObject instanceof ValueBonused) {
      $valueObject->getValue($context);
    }

    return $valueObject;
  }

  /**
   * @param int   $unitSnId
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   *
   * @return float|int
   */
  public function getValue($unitSnId, $context = []) {

    if(($vo = $this->getValueObject($unitSnId, $context)) instanceof ValueBonused) {
      $result = $vo->getValue();
    } else {
      $result = $vo;
    }

    return $result;
  }

  /**
   * @param int   $unitSnId
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   *
   * @return float|int
   */
  public function getBase($unitSnId, $context = []) {
    if(($vo = $this->getValueObject($unitSnId, $context)) instanceof ValueBonused) {
      $result = $vo->base;
    } else {
      $result = $vo;
    }

    return $result;
  }

  /**
   * @param int   $unitSnId
   * @param array $context - Context list of locations: [LOC_xxx => (data)]
   *
   * @return mixed
   */
  protected function getLevelNonServer($unitSnId, $context = []) {
//    pdump($unitSnId, 'NON-server');
//      list($locationType, $locationId) = getLocationFromContext($context);
//      $fleet = !empty($context[LOC_FLEET]) ? $context[LOC_FLEET] : [];
    $user = !empty($context[LOC_USER]) ? $context[LOC_USER] : [];
    $planet = !empty($context[LOC_PLANET]) ? $context[LOC_PLANET] : [];

    return mrc_get_level($user, $planet, $unitSnId, true, true);
  }

}
