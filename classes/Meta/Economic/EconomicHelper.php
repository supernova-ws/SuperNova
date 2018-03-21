<?php
/**
 * Created by Gorlum 01.10.2017 15:07
 */

namespace Meta\Economic;

use Core\GlobalContainer;

class EconomicHelper {

  /**
   * @var \classConfig $config
   */
  protected $config;

  /**
   * @var array[] $resourceExchangeRates
   */
  protected $resourceExchangeRates = [];

  /**
   * EconomicHelper constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
//    if ($gc instanceof \classConfig) {
//      $this->config = $gc;
//    } elseif ($gc instanceof GlobalContainer) {
//      $this->config = $gc->config;
//    } else {
//      $this->config = \SN::$config;
//    }

    $this->config = $gc->config;
  }

  /**
   * Resets internal exchange cache
   */
  public function resetResourcesExchange() {
    $this->resourceExchangeRates = [];
  }

  /**
   * Gets current exchange rates
   *
   * @return float[] - [int resourceId] -> [float configuredExchangeRate]
   */
  public function getResourcesExchange() {
    if (empty($this->resourceExchangeRates[UNIT_ANY])) {
      $this->resourceExchangeRates[UNIT_ANY] = array(
        RES_METAL       => 'rpg_exchange_metal',
        RES_CRYSTAL     => 'rpg_exchange_crystal',
        RES_DEUTERIUM   => 'rpg_exchange_deuterium',
        RES_DARK_MATTER => 'rpg_exchange_darkMatter',
      );

      foreach ($this->resourceExchangeRates[UNIT_ANY] as &$rate) {
        ($rate = floatval($this->config->$rate)) <= 0 ? $rate = 1 : false;
      }
    }

    return $this->resourceExchangeRates[UNIT_ANY];
  }

  /**
   * Get cost of all resources normalized to selected one
   *
   * Say, get cost of all resources in metal - for main calculations for comparing units between each other
   *
   * @param int $resourceId
   *
   * @return float[] - [int resourceId] -> [float ratesNormalizedToResourceCost]
   */
  public function getResourceExchangeIn($resourceId) {
    if(empty($this->resourceExchangeRates[$resourceId])) {
      $defaultRates = $this->getResourcesExchange();

      $this->resourceExchangeRates[$resourceId] = [];
      foreach($defaultRates as $defaultResourceId => $defaultRate) {
        $this->resourceExchangeRates[$resourceId][$defaultResourceId] = $defaultRate / $defaultRates[$resourceId];
      }
    }

    return $this->resourceExchangeRates[$resourceId];
  }

}
