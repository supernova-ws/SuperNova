<?php
/**
 * Created by Gorlum 13.10.2017 2:34
 */

namespace Meta\Economic;

use \classSupernova;

class ResourceCalculations {
  /**
   * @var float[][] $storageMatrix - [resourceId => [unitId => resourceStorageSize]]
   */
  public $storageMatrix = null;
  public $production_full = [];
  public $production = [];
  public $efficiency = 0;
  public $total = [];
  public $energy = [];
  public $total_production_full = [];

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

    static::$mineSpeedNormal = game_resource_multiplier(true);
    static::$mineSpeedCurrent = game_resource_multiplier();
    static::$storageScaling = classSupernova::$config->eco_scale_storage ? static::$mineSpeedNormal : 1;

    static::$groupFactories = sn_get_groups('factories');

    // Filling capacity functions list
    foreach (sn_get_groups('storages') as $unit_id) {
      foreach (get_unit_param($unit_id, P_STORAGE) as $resource_id => $function) {
        static::$storageCapacityFuncs[$unit_id][$resource_id] = $function;
      }
    }

    static::$basicPlanetIncomeTable[RES_METAL][0] = classSupernova::$config->metal_basic_income;
    static::$basicPlanetIncomeTable[RES_CRYSTAL][0] = classSupernova::$config->crystal_basic_income;
    static::$basicPlanetIncomeTable[RES_DEUTERIUM][0] = classSupernova::$config->deuterium_basic_income;
    static::$basicPlanetIncomeTable[RES_ENERGY][0] = classSupernova::$config->energy_basic_income;

    static::$basicPlanetStorageTable[RES_METAL][0] = classSupernova::$config->eco_planet_storage_metal;
    static::$basicPlanetStorageTable[RES_CRYSTAL][0] = classSupernova::$config->eco_planet_storage_crystal;
    static::$basicPlanetStorageTable[RES_DEUTERIUM][0] = classSupernova::$config->eco_planet_storage_deuterium;
    static::$basicPlanetStorageTable[RES_ENERGY][0] = 0;
  }

  public function __construct() {
    static::initStatic();

    $this->groupModifiers = sn_get_groups(GROUP_MODIFIERS_NAME);
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

    $planet_row['metal_max'] = $this->getStorage(RES_METAL);
    $planet_row['crystal_max'] = $this->getStorage(RES_CRYSTAL);
    $planet_row['deuterium_max'] = $this->getStorage(RES_DEUTERIUM);

    if ($planet_row['planet_type'] == PT_MOON) {
      $planet_row['metal_perhour'] = 0;
      $planet_row['crystal_perhour'] = 0;
      $planet_row['deuterium_perhour'] = 0;
      $planet_row['energy_used'] = 0;
      $planet_row['energy_max'] = 0;

      return $this;
    }

    $this->production_full = static::$basicPlanetIncomeTable;
    foreach (static::$groupFactories as $unit_id) {
      $unit_data_production = get_unit_param($unit_id, P_UNIT_PRODUCTION);
      $unit_level = mrc_get_level($user, $planet_row, $unit_id);
      $unit_load = $planet_row[pname_factory_production_field_name($unit_id)];

      foreach ($unit_data_production as $resource_id => $function) {
        $this->production_full[$resource_id][$unit_id] = $function($unit_level, $unit_load, $user, $planet_row);
      }
    }

    // Applying core resource production multipliers to all mining info, including basic mining
    if (!empty($densityInfo = $this->groupPlanetDensities[$planet_row['density_index']][UNIT_RESOURCES])) {
      foreach ($densityInfo as $resourceId => $densityMultiplier) {
        if (!empty($this->production_full[$resourceId])) {
          foreach ($this->production_full[$resourceId] as $miningUnitId => &$miningAmount) {
            $miningAmount *= $densityMultiplier;
          }
        }
      }
    }

    // Applying game speed
    foreach ($this->production_full as $resourceId => &$miningData) {
      foreach ($miningData as $miningUnitId => &$miningAmount) {
        $miningAmount *= ($resourceId == RES_ENERGY ? static::$mineSpeedNormal : static::$mineSpeedCurrent);
      }
    }

    // Applying modifiers
    foreach ($this->production_full as &$resourceProductionTable) {
      foreach ($resourceProductionTable as $mineId => &$mineProduction) {
        $mineProduction = floor(mrc_modify_value($user, $planet_row, $this->groupModifiers[MODIFIER_RESOURCE_PRODUCTION], $mineProduction));
      }
    }

    foreach ($this->production_full as $resource_id => $resource_data) {
      $this->total_production_full[$resource_id] = array_sum($resource_data);
    }

    $this->production = $this->production_full;

    if ($this->production[RES_ENERGY][STRUC_MINE_FUSION]) {
      $deuterium_balance = array_sum($this->production[RES_DEUTERIUM]);
      $energy_balance = array_sum($this->production[RES_ENERGY]);
      if ($deuterium_balance < 0 || $energy_balance < 0) {
        $this->production[RES_DEUTERIUM][STRUC_MINE_FUSION] = $this->production[RES_ENERGY][STRUC_MINE_FUSION] = 0;
      }
    }

    foreach ($this->production[RES_ENERGY] as $energy) {
      $this->energy[$energy >= 0 ? BUILD_CREATE : BUILD_DESTROY] += $energy;
    }

    $this->energy[BUILD_DESTROY] = -$this->energy[BUILD_DESTROY];

    $this->efficiency = $this->energy[BUILD_DESTROY] > $this->energy[BUILD_CREATE]
      ? $this->energy[BUILD_CREATE] / $this->energy[BUILD_DESTROY]
      : 1;

    foreach ($this->production as $resource_id => &$resource_data) {
      if ($this->efficiency != 1) {
        foreach ($resource_data as $unit_id => &$resource_production) {
          if (!($unit_id == STRUC_MINE_FUSION && $resource_id == RES_DEUTERIUM) && $unit_id != 0 && !($resource_id == RES_ENERGY && $resource_production >= 0)) {
            $resource_production = $resource_production * $this->efficiency;
          }
        }
      }
      $this->total[$resource_id] = array_sum($resource_data);
      $this->total[$resource_id] = $this->total[$resource_id] >= 0 ? floor($this->total[$resource_id]) : ceil($this->total[$resource_id]);
    }

    $planet_row['metal_perhour'] = $this->total[RES_METAL];
    $planet_row['crystal_perhour'] = $this->total[RES_CRYSTAL];
    $planet_row['deuterium_perhour'] = $this->total[RES_DEUTERIUM];

    $planet_row['energy_max'] = $this->energy[BUILD_CREATE];
    $planet_row['energy_used'] = $this->energy[BUILD_DESTROY];

    return $this;
  }

  public function getStorage($resourceId) {
    return
      is_array($this->storageMatrix[$resourceId]) && !empty($this->storageMatrix[$resourceId])
        ? array_sum($this->storageMatrix[$resourceId])
        : 0;
  }

}
