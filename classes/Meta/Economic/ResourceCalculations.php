<?php
/**
 * Created by Gorlum 13.10.2017 2:34
 */

namespace Meta\Economic;

use \SN;

class ResourceCalculations {
  /**
   * @var float[][] $storageMatrix - [resourceId => [unitId => storageSize]]
   */
  public $storageMatrix = null;
  /**
   * @var float[][] $productionFullMatrix - [resourceId => [productionUnitId => productionAmount]]
   */
  public $productionFullMatrix = [];
  public $productionCurrentMatrix = [];
  public $efficiency = 0;
  public $energy = [];

  protected $groupModifiers = [];
  protected $groupPlanetDensities = [];

  protected static $groupFactories = [];

  protected static $storageCapacityFuncs = [];

  protected static $staticInitialized = false;
  protected static $mineSpeedCurrent = 1;
  protected static $mineSpeedNormal = 1;
  protected static $storageScaling = 1;

  protected static $basicPlanetIncomeTable = [];
  protected static $basicPlanetStorageTable = [];

  public static function initStatic() {
    if (static::$staticInitialized) {
      return;
    }

    static::$mineSpeedNormal  = game_resource_multiplier(true);
    static::$mineSpeedCurrent = game_resource_multiplier();
    static::$storageScaling   = SN::$config->eco_scale_storage ? static::$mineSpeedNormal : 1;

    static::$groupFactories = sn_get_groups('factories');

    // Filling capacity functions list
    foreach (sn_get_groups('storages') as $unit_id) {
      foreach (get_unit_param($unit_id, P_STORAGE) as $resource_id => $function) {
        static::$storageCapacityFuncs[$unit_id][$resource_id] = $function;
      }
    }

    static::$basicPlanetIncomeTable[RES_METAL][0]     = floatval(SN::$config->metal_basic_income);
    static::$basicPlanetIncomeTable[RES_CRYSTAL][0]   = floatval(SN::$config->crystal_basic_income);
    static::$basicPlanetIncomeTable[RES_DEUTERIUM][0] = floatval(SN::$config->deuterium_basic_income);
    static::$basicPlanetIncomeTable[RES_ENERGY][0]    = floatval(SN::$config->energy_basic_income);

    static::$basicPlanetStorageTable[RES_METAL][0]     = floatval(SN::$config->eco_planet_storage_metal);
    static::$basicPlanetStorageTable[RES_CRYSTAL][0]   = floatval(SN::$config->eco_planet_storage_crystal);
    static::$basicPlanetStorageTable[RES_DEUTERIUM][0] = floatval(SN::$config->eco_planet_storage_deuterium);
    static::$basicPlanetStorageTable[RES_ENERGY][0]    = 0;
  }

  public function __construct() {
    static::initStatic();

    $this->groupModifiers       = sn_get_groups(GROUP_MODIFIERS_NAME);
    $this->groupPlanetDensities = sn_get_groups('planet_density');
  }

  public function eco_get_planet_caps(&$user, &$planet_row, $production_time = 0) {
    // TODO Считать $production_time для термоядерной электростанции
    $this->storageMatrix = static::$basicPlanetStorageTable;
    foreach (static::$storageCapacityFuncs as $unit_id => $capacityFuncList) {
      foreach ($capacityFuncList as $resource_id => $function) {
        $this->storageMatrix[$resource_id][$unit_id] = floor(static::$storageScaling *
          mrc_modify_value($user, $planet_row, $this->groupModifiers[MODIFIER_RESOURCE_CAPACITY], $function(mrc_get_level($user, $planet_row, $unit_id)))
        );
      }
    }

    $planet_row['metal_max']     = $this->getStorage(RES_METAL);
    $planet_row['crystal_max']   = $this->getStorage(RES_CRYSTAL);
    $planet_row['deuterium_max'] = $this->getStorage(RES_DEUTERIUM);

    if ($planet_row['planet_type'] == PT_MOON) {
      $planet_row['metal_perhour']     = 0;
      $planet_row['crystal_perhour']   = 0;
      $planet_row['deuterium_perhour'] = 0;
      $planet_row['energy_used']       = 0;
      $planet_row['energy_max']        = 0;

      return $this;
    }

    $this->fillProductionMatrix($user, $planet_row);
    $this->applyDensityModifiers($planet_row['density_index']);
    $this->applyProductionSpeed();
    $this->applyCapitalRates($user['id_planet'] == $planet_row['id']);
    $this->applyProductionModifiers($user, $planet_row);

    $this->productionCurrentMatrix = $this->productionFullMatrix;

    $this->calculateEnergyBalance();
    $this->applyEfficiency();

    $planet_row['metal_perhour']     = $this->getProduction(RES_METAL);
    $planet_row['crystal_perhour']   = $this->getProduction(RES_CRYSTAL);
    $planet_row['deuterium_perhour'] = $this->getProduction(RES_DEUTERIUM);

    $planet_row['energy_max']  = $this->energy[BUILD_CREATE];
    $planet_row['energy_used'] = $this->energy[BUILD_DESTROY];

    return $this;
  }

  public function getStorage($resourceId) {
    return
      is_array($this->storageMatrix[$resourceId]) && !empty($this->storageMatrix[$resourceId])
        ? array_sum($this->storageMatrix[$resourceId])
        : 0;
  }

  public function getProductionFull($resourceId) {
    return
      is_array($this->productionFullMatrix[$resourceId]) && !empty($this->productionFullMatrix[$resourceId])
        ? array_sum($this->productionFullMatrix[$resourceId])
        : 0;
  }

  public function getProduction($resourceId) {
    $production = is_array($this->productionCurrentMatrix[$resourceId]) && !empty($this->productionCurrentMatrix[$resourceId])
      ? array_sum($this->productionCurrentMatrix[$resourceId])
      : 0;

    return $production >= 0 ? floor($production) : ceil($production);
  }

  /**
   * Applying core resource production multipliers to all mining info, including basic mining
   *
   * @param $planetDensity
   */
  protected function applyDensityModifiers($planetDensity) {
    if (!empty($densityInfo = $this->groupPlanetDensities[$planetDensity][UNIT_RESOURCES])) {
      foreach ($densityInfo as $resourceId => $densityMultiplier) {
        if (!empty($this->productionFullMatrix[$resourceId])) {
          foreach ($this->productionFullMatrix[$resourceId] as $miningUnitId => &$miningAmount) {
            $miningAmount *= $densityMultiplier;
          }
        }
      }
    }
  }

  /**
   * Applying game speed to mining rates
   *
   * @param bool $isCapital
   */
  protected function applyCapitalRates($isCapital) {
    if (
      !$isCapital
      ||
      empty(SN::$gc->config->planet_capital_mining_rate)
      ||
      SN::$gc->config->planet_capital_mining_rate == 1
    ) {
      return;
    }

    foreach ($this->productionFullMatrix as $resourceId => &$miningData) {
      foreach ($miningData as $miningUnitId => &$miningAmount) {
        $miningAmount *= SN::$gc->config->planet_capital_mining_rate;
      }
    }
  }

  /**
   * Applying game speed to mining rates
   */
  protected function applyProductionSpeed() {
    foreach ($this->productionFullMatrix as $resourceId => &$miningData) {
      foreach ($miningData as $miningUnitId => &$miningAmount) {
        $miningAmount *= ($resourceId == RES_ENERGY ? static::$mineSpeedNormal : static::$mineSpeedCurrent);
      }
    }
  }

  /**
   * Applying resource production modifiers
   *
   * @param $user
   * @param $planet_row
   */
  protected function applyProductionModifiers(&$user, &$planet_row) {
    foreach ($this->productionFullMatrix as &$resourceProductionTable) {
      foreach ($resourceProductionTable as $mineId => &$mineProduction) {
        $mineProduction = floor(mrc_modify_value($user, $planet_row, $this->groupModifiers[MODIFIER_RESOURCE_PRODUCTION], $mineProduction));
      }
    }
  }

  protected function calculateEnergyBalance() {
    if ($this->productionCurrentMatrix[RES_ENERGY][STRUC_MINE_FUSION]) {
      $deuterium_balance = array_sum($this->productionCurrentMatrix[RES_DEUTERIUM]);
      $energy_balance    = array_sum($this->productionCurrentMatrix[RES_ENERGY]);
      if ($deuterium_balance < 0 || $energy_balance < 0) {
        $this->productionCurrentMatrix[RES_DEUTERIUM][STRUC_MINE_FUSION] = $this->productionCurrentMatrix[RES_ENERGY][STRUC_MINE_FUSION] = 0;
      }
    }

    foreach ($this->productionCurrentMatrix[RES_ENERGY] as $energy) {
      $this->energy[$energy >= 0 ? BUILD_CREATE : BUILD_DESTROY] += $energy;
    }

    $this->energy[BUILD_DESTROY] = -$this->energy[BUILD_DESTROY];
  }

  /**
   * Calculate total efficiency and applying to production
   */
  protected function applyEfficiency() {
    $this->efficiency = $this->energy[BUILD_DESTROY] > $this->energy[BUILD_CREATE]
      ? $this->energy[BUILD_CREATE] / $this->energy[BUILD_DESTROY]
      : 1;

    // If mines on 100% efficiency - doing nothing
    if ($this->efficiency == 1) {
      return;
    }

    // Adjusting current production with efficiency
    foreach ($this->productionCurrentMatrix as $resource_id => &$resource_data) {
      foreach ($resource_data as $unit_id => &$resource_production) {
        if (!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM) && $unit_id != 0 && !($resource_id == RES_ENERGY && $resource_production >= 0)) {
          $resource_production = $resource_production * $this->efficiency;
        }
      }
    }
  }

  /**
   * @param $user
   * @param $planet_row
   */
  protected function fillProductionMatrix(&$user, &$planet_row) {
    $this->productionFullMatrix = static::$basicPlanetIncomeTable;
    foreach (static::$groupFactories as $unit_id) {
      $unit_data_production = get_unit_param($unit_id, P_UNIT_PRODUCTION);
      $unit_level           = mrc_get_level($user, $planet_row, $unit_id);
      $unit_load            = $planet_row[pname_factory_production_field_name($unit_id)];

      foreach ($unit_data_production as $resource_id => $function) {
        $this->productionFullMatrix[$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row);
      }
    }
  }

}
