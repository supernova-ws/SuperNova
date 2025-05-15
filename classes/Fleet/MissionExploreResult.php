<?php /** @noinspection PhpDeprecationInspection */

/** Created by Gorlum 09.05.2025 18:56 */

namespace Fleet;

use SN;

class MissionExploreResult {
  /** @var string Key in outcome config for roll value */
  const K_ROLL_VALUE = 'value';

  /** @var int Max DM can be found in 1 expedition */
  const MAX_DM = CONST_10K;

  /** @var int $valueRolled mt_rand() value rolled [0,max_chance] that determine current expedition outcome */
  public $valueRolled = Constants::OUTCOME_NOT_CALCULATED;
  /** @var int $outcome Expedition outcome */
  public $outcome = Constants::OUTCOME_NOT_CALCULATED;
  /**@var array $currentOutcomeConfig Current outcome config */
  public $currentOutcomeConfig = [];

  // Outcomes with variants - sub-outcomes
  /** @var float $subOutcomeProbability Normalized probability [0,1] of sub-outcome */
  public $subOutcomeProbability = Constants::OUTCOME_NOT_CALCULATED;
  /** @var int $subOutcome Secondary outcome (sub-outcome) for variable outcomes */
  public $subOutcome = Constants::OUTCOME_NOT_CALCULATED;
  /** @var float $gainShare Share of total resources to gain - depends on sub-outcome */
  public $gainShare = 0;

  // Units/Resources changes
  /** @var int[] $shipsFound Ships found during current expedition */
  public $shipsFound = [];
  /** @var int[] $shipsLost Ships lost during current expedition */
  public $shipsLost = [];
  /** @var float[] $resourcesFound Resource amounts found during current expedition */
  public $resourcesFound = [];
  /** @var int $darkMatterFound Dark Matter found during current expedition */
  public $darkMatterFound = 0;

  // Other variables
  /** @var array $ships Current list of ships within mission. CAN be changed by outcomes and SHOULD be changed if ships lost/found */
  public $ships = [];
  /** @var int $fleetCapacityFree Free fleet capacity left */
  public $fleetCapacityFree = 0;
  /** @var int $shipsCostInMetal Total ships cost in metal */
  public $shipsCostInMetal = 0;

  /** @var ?FleetDispatchEvent $fleetEvent Event currently processed */
  public $fleetEvent = null;

  /** @var float $timeStart Timestamp with ms when started expedition processing */
  protected $timeStart = 0;

  /** @var array[] $configs */
  public static $configs = [
    Constants::OUTCOME_NONE                       => [
      Constants::K_OUTCOME      => Constants::OUTCOME_NONE,
      Constants::K_OUTCOME_TYPE => Constants::OUTCOME_TYPE_NEUTRAL,
      P_CHANCE                  => Constants::OUTCOME_EXPEDITION_NOTHING_DEFAULT_CHANCE,
    ],
    Constants::EXPEDITION_OUTCOME_LOST_FLEET      => [
      Constants::K_OUTCOME      => Constants::EXPEDITION_OUTCOME_LOST_FLEET,
      Constants::K_OUTCOME_TYPE => Constants::OUTCOME_TYPE_BAD,
      P_CHANCE                  => 9,
    ],
    Constants::EXPEDITION_OUTCOME_LOST_FLEET_ALL  => [
      Constants::K_OUTCOME      => Constants::EXPEDITION_OUTCOME_LOST_FLEET_ALL,
      Constants::K_OUTCOME_TYPE => Constants::OUTCOME_TYPE_BAD,
      P_CHANCE                  => 3,
    ],
    Constants::EXPEDITION_OUTCOME_FOUND_FLEET     => [
      Constants::K_OUTCOME           => Constants::EXPEDITION_OUTCOME_FOUND_FLEET,
      Constants::K_OUTCOME_TYPE      => Constants::OUTCOME_TYPE_GOOD,
      P_CHANCE                       => 200,
      'percent'                      => [0 => 0.1, 1 => 0.02, 2 => 0.01,],
      Constants::K_OUTCOME_SECONDARY => [
        [P_CHANCE => 90, P_MULTIPLIER => 0.01, P_MESSAGE_ID => 2,],
        [P_CHANCE => 9, P_MULTIPLIER => 0.02, P_MESSAGE_ID => 1,],
        [P_CHANCE => 1, P_MULTIPLIER => 0.10, P_MESSAGE_ID => 0,],
      ],
    ],
    Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES => [
      Constants::K_OUTCOME           => Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES,
      Constants::K_OUTCOME_TYPE      => Constants::OUTCOME_TYPE_GOOD,
      P_CHANCE                       => 300,
      'percent'                      => [0 => 0.1, 1 => 0.050, 2 => 0.025,],
      Constants::K_OUTCOME_SECONDARY => [
        [P_CHANCE => 90, P_MULTIPLIER => 0.025, P_MESSAGE_ID => 2,],
        [P_CHANCE => 9, P_MULTIPLIER => 0.050, P_MESSAGE_ID => 1,],
        [P_CHANCE => 1, P_MULTIPLIER => 0.100, P_MESSAGE_ID => 0,],
      ],
    ],
    Constants::EXPEDITION_OUTCOME_FOUND_DM        => [
      Constants::K_OUTCOME           => Constants::EXPEDITION_OUTCOME_FOUND_DM,
      Constants::K_OUTCOME_TYPE      => Constants::OUTCOME_TYPE_GOOD,
      P_CHANCE                       => 100,
      'percent'                      => [0 => 0.0100, 1 => 0.0040, 2 => 0.0010,],
      Constants::K_OUTCOME_SECONDARY => [
        [P_CHANCE => 90, P_MULTIPLIER => 0.0010, /*P_MESSAGE_ID => 2,*/],
        [P_CHANCE => 9, P_MULTIPLIER => 0.0040, /*P_MESSAGE_ID => 1,*/],
        [P_CHANCE => 1, P_MULTIPLIER => 0.0100, /*P_MESSAGE_ID => 0,*/],
      ],
    ],
    /*
    FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT => array(
      'outcome' => FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT,
      P_CHANCE => 10,
    ),
    */
  ];

  /** @var array[] $shipData */
  public static $shipData = [];
  /** @var float[] $rates Resources exchange rates */
  public static $rates = [];

  public function __construct() {
    self::getShipData();
    self::getExchangeRates();
  }

  /**
   * @return int
   */
  public function flt_mission_explore(FleetDispatchEvent $fleetEvent) {
    if ($fleetEvent->event != EVENT_FLT_ACCOMPLISH) {
      return CACHE_NONE;
    }

    // Preparing for expedition
    $this->timeStart = microtime(true);

    $this->resetExpedition($fleetEvent);

    $this->calcSecondaryData();

    // Calculating mission outcome

    // Making a copy of outcome configs to tamper with
    $outcomeConfigs = static::$configs;

    $outcomeConfigs = $this->adjustNoneChance($outcomeConfigs);

    list($outcomeConfigs, $chance_max) = $this->calculateRollValues($outcomeConfigs);


    // Rolling value which wil determine outcome
    $this->valueRolled = mt_rand(0, ceil($chance_max));
    // NOTHING => 200, LOST_FLEET => 209, LOST_FLEET_ALL => 212, FOUND_FLEET => 412, RESOURCES => 712, FOUND_DM => 812
    // $this->valueRolled = 409; // DEBUG  comment!
    // Determining outcome
    foreach ($outcomeConfigs as $key1 => $config) {
      if (!$config[P_CHANCE]) {
        continue;
      }
      $this->outcome        = $key1;
      if ($this->valueRolled <= $config [self::K_ROLL_VALUE]) {
        break;
      }
    }
    // Fallback in case something went wrong
    if ($this->outcome == Constants::OUTCOME_NOT_CALCULATED) {
      $this->outcome = Constants::OUTCOME_NONE;
    }
    $this->currentOutcomeConfig = $outcomeConfigs[$this->outcome];

    // Вычисляем вероятность выпадения данного числа в общем пуле
    $this->subOutcomeProbability = ($this->currentOutcomeConfig[self::K_ROLL_VALUE] - $this->valueRolled) / $this->currentOutcomeConfig[P_CHANCE];
    $this->subOutcome = $this->subOutcomeProbability >= 0.99 ? 0 : ($this->subOutcomeProbability >= 0.90 ? 1 : 2);
    $this->gainShare  = !empty($this->currentOutcomeConfig['percent'][$this->subOutcome])
      ? $this->currentOutcomeConfig['percent'][$this->subOutcome]
      : Constants::OUTCOME_NOT_CALCULATED;

    // Outcome CAN change ONLY object properties and SHOULD NOT mess with real fleet values
    switch ($this->outcome) {
      case Constants::OUTCOME_NONE:
        $this->subOutcome = Constants::OUTCOME_NOT_CALCULATED;
      break;

      case Constants::EXPEDITION_OUTCOME_LOST_FLEET:
        $this->outcomeShipsLostPartially();
        $this->subOutcome = Constants::OUTCOME_NOT_CALCULATED;
      break;

      case Constants::EXPEDITION_OUTCOME_LOST_FLEET_ALL:
        $this->outcomeLostFleetAll();
        $this->subOutcome = Constants::OUTCOME_NOT_CALCULATED;
      break;

      case Constants::EXPEDITION_OUTCOME_FOUND_FLEET:
        $this->outcomeFoundShips();
      break;

      case Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES:
        $this->outcomeFoundResources();
      break;

      case Constants::EXPEDITION_OUTCOME_FOUND_DM:
        $this->outcomeFoundDm();
      break;

      //case FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT:
      //break;

      default:
      break;
    }

    // Calling extra
    $this->flt_mission_explore_addon($this);

    // Applying expedition changes to real fleet data
    $this->applyFleetChanges();

    // Saving expedition result to DB
    $this->saveResult();

    // Sending expedition report to player
    $this->sendReport($this->fleetEvent->fleet);

    return CACHE_FLEET | CACHE_USER_SRC;
  }

  /**
   * @param array $theFleet
   *
   * @return string
   */
  protected function renderUnits(array $theFleet) {
    $add = '';
    foreach ($theFleet as $ship_id => $ship_amount) {
      $add .= SN::$lang['tech'][$ship_id] . ' - ' . $ship_amount . "\r\n";
    }

    return $add;
  }

  /**
   * @param static $outcome
   *
   * @return static
   */
  protected function flt_mission_explore_addon(MissionExploreResult $outcome) {
    /** @see core_festival::expedition_result_adjust(), FestivalActivityPuzzleExpedition::fleet_explore_adjust_result() */
    return sn_function_call(Constants::HOOK_MISSION_EXPLORE_ADDON, [$outcome]);
  }

  /**
   * Reset current expedition state
   *
   * @param ?FleetDispatchEvent $fleetEvent
   *
   * @return void
   */
  protected function resetExpedition(FleetDispatchEvent $fleetEvent = null) {
    $this->fleetEvent = $fleetEvent;

    // Fleet's ship list
    $this->ships = !empty($this->fleetEvent->fleet['fleet_array'])
      ? sys_unit_str2arr($this->fleetEvent->fleet['fleet_array'])
      : [];

    $this->shipsLost       = [];
    $this->shipsFound      = [];
    $this->resourcesFound  = [];
    $this->darkMatterFound = 0;

    $this->valueRolled           = -1;
    $this->outcome               = Constants::OUTCOME_NOT_CALCULATED;
    $this->subOutcomeProbability = -1;
    $this->subOutcome            = Constants::OUTCOME_NOT_CALCULATED;
    $this->gainShare             = 0;

    $this->fleetCapacityFree = 0;
    $this->shipsCostInMetal  = 0;
  }

  /**
   * Get data for ships
   *
   * @return array[]
   */
  protected static function getShipData() {
    if (empty(static::$shipData)) {
      foreach (sn_get_groups('fleet') as $unit_id) {
        $unit_info = get_unit_param($unit_id);
        if ($unit_info[P_UNIT_TYPE] != UNIT_SHIPS || empty($unit_info['engine'][0]['speed'])) {
          continue;
        }
        $unit_info[P_COST_METAL] = get_unit_cost_in($unit_info[P_COST]);

        static::$shipData[$unit_id] = $unit_info;
      }
    }

    return static::$shipData;
  }

  /**
   * Get resource exchange rates
   *
   * @return float[]
   */
  protected static function getExchangeRates() {
    if (empty(static::$rates)) {
      static::$rates = SN::$gc->economicHelper->getResourcesExchange();
    }

    return static::$rates;
  }

  /**
   * Saving expedition results to DB
   *
   * @return void
   */
  protected function saveResult() {
    // Increasing expedition XP - 1 point per Expedition
    db_user_set_by_id($this->fleetEvent->fleetOwnerId, "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");
    // Saving changed data to DB
    if ($this->darkMatterFound >= 1) {
      rpg_points_change($this->fleetEvent->fleetOwnerId, RPG_EXPEDITION, $this->darkMatterFound, 'Expedition Bonus');
    }

    // Checking if the fleet was destroyed entirely
    if (($fleetAmount = array_sum($this->ships)) >= 1) {
      // No - some ships left
      $query_data =
        // Routing fleet to return path
        ['fleet_mess' => FLEET_STATUS_RETURNING]
        // If there were some changes to the fleet - propagating them to DB
        + (!empty($this->shipsLost) || !empty($this->shipsFound)
          ? [
            'fleet_amount' => $fleetAmount,
            'fleet_array'  => sys_unit_arr2str($this->ships),
          ]
          : []
        );

      $query_delta = [];
      // If we found some resources - adding them to cargo bays
      if (!empty($this->resourcesFound) && array_sum($this->resourcesFound) > 0) {
        $query_delta = [
          'fleet_resource_metal'     => $this->resourcesFound[RES_METAL],
          'fleet_resource_crystal'   => $this->resourcesFound[RES_CRYSTAL],
          'fleet_resource_deuterium' => $this->resourcesFound[RES_DEUTERIUM],
        ];
      }

      DbFleetStatic::fleet_update_set($this->fleetEvent->fleetId, $query_data, $query_delta);
    } else {
      // Fleet empty? Removing fleet from DB
      DbFleetStatic::db_fleet_delete($this->fleetEvent->fleetId);
    }
  }

  /**
   * Compile and send expedition report
   *
   * @param array $fleetRow
   *
   * @return void
   */
  protected function sendReport(array $fleetRow) {
    // Generating PM for user
    $langExpeditions = SN::$lang['flt_mission_expedition'];
    // Generating outcome-specific details
    $msg_text_addon = '';
    if ($this->outcome == Constants::EXPEDITION_OUTCOME_FOUND_DM) {
      $msg_text_addon = $this->darkMatterFound >= 1
        ? sprintf($langExpeditions['found_dark_matter'], $this->darkMatterFound)
        : $langExpeditions['outcomes'][$this->outcome]['no_result'];
    }

    if ($this->outcome == Constants::EXPEDITION_OUTCOME_FOUND_RESOURCES) {
      if (array_sum($this->resourcesFound) >= 1) {
        $msg_text_addon = $langExpeditions['found_resources'];
        $msg_text_addon .= $this->renderUnits($this->resourcesFound);
      } else {
        $msg_text_addon = $langExpeditions['outcomes'][$this->outcome]['no_result'];
      }
    }

    if (!empty($this->shipsLost)) {
      $msg_text_addon = $langExpeditions['lost_fleet'];
      $msg_text_addon .= $this->renderUnits($this->shipsLost);
    }

    if ($this->outcome == Constants::EXPEDITION_OUTCOME_FOUND_FLEET) {
      if (empty($this->shipsFound)) {
        $msg_text_addon = $langExpeditions['outcomes'][$this->outcome]['no_result'];
      }
    }

    if (!empty($this->ships) && array_sum($this->ships) >= 1) {
      if (!empty($this->shipsFound)) {
        $msg_text_addon = $langExpeditions['found_fleet'] . $this->renderUnits($this->shipsFound);
      }
    }

    $messages = $langExpeditions['outcomes'][$this->outcome]['messages'];
    if (
      // Outcome have sub-outcomes
      !empty($this->currentOutcomeConfig['percent'])
      // Some outcome rolled
      && $this->subOutcome >= 0
      // Messages are different for different outcomes
      && is_array($messages)
    ) {
      // Selecting messages for specific outcome
      $messages = &$messages[$this->subOutcome];
    }

    $msg_text = is_string($messages)
      // If we have only one variant for message - using it
      ? $messages
      // If we have several message variations - selecting one randomly
      : (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : '');

    $msg_text = sprintf(
        $msg_text,
        $this->fleetEvent->fleetId,
        uni_render_coordinates($fleetRow, 'fleet_end_')
      ) . ($msg_text_addon ? "\r\n" . $msg_text_addon : '');

    msg_send_simple_message(
      $this->fleetEvent->fleetOwnerId,
      '',
      $fleetRow['fleet_end_stay'],
      MSG_TYPE_EXPLORE,
      $langExpeditions['msg_sender'],
      $langExpeditions['msg_title'],
      $msg_text
    );
  }

  /**
   * @return void
   */
  protected function applyFleetChanges() {
    // Shortcut to access and change fleet data in event
    $fleetRow = &$this->fleetEvent->fleet;

    // Adding found ships
    foreach (!empty($this->shipsFound) ? $this->shipsFound : [] as $unit_id => $unit_amount) {
      $this->ships[$unit_id] += $unit_amount;
    }
    // Removing lost ships
    foreach (!empty($this->shipsLost) ? $this->shipsLost : [] as $shipLostId => $shipLostCount) {
      $this->ships[$shipLostId] -= $shipLostCount;
      if ($this->ships[$shipLostId] < 1) {
        unset($this->ships[$shipLostId]);
      }
    }
    // Adjusting ship data in real fleet record
    $fleetRow['fleet_amount'] = array_sum($this->ships);
    $fleetRow['fleet_array']  = sys_unit_arr2str($this->ships);

    // Adjusting resources data in real fleet record
    if (array_sum($this->resourcesFound) >= 1) {
      $fleetRow['fleet_resource_metal']     += $this->resourcesFound[RES_METAL];
      $fleetRow['fleet_resource_crystal']   += $this->resourcesFound[RES_CRYSTAL];
      $fleetRow['fleet_resource_deuterium'] += $this->resourcesFound[RES_DEUTERIUM];
    }

    // Setting fleet to return route
    $fleetRow['fleet_mess'] = FLEET_STATUS_RETURNING;
  }

  /**
   * Calculating fleet cost in metal and free capacity
   *
   * @return void
   */
  protected function calcSecondaryData() {
    // Calculating ship's free capacity and fleet cost in metal
    foreach ($this->ships as $ship_id => $ship_amount) {
      $this->fleetCapacityFree += $ship_amount * static::$shipData[$ship_id][P_CAPACITY];
      $this->shipsCostInMetal  += $ship_amount * static::$shipData[$ship_id][P_COST_METAL];
    }
    // Calculating rest of fleet capacity - room which not occupied with resources
    $this->fleetCapacityFree = max(
      0,
      $this->fleetCapacityFree
      - $this->fleetEvent->fleet['fleet_resource_metal']
      - $this->fleetEvent->fleet['fleet_resource_crystal']
      - $this->fleetEvent->fleet['fleet_resource_deuterium']
    );
  }

  /**
   * @param array $outcomeConfigs
   *
   * @return array
   */
  protected function adjustNoneChance(array $outcomeConfigs) {
    // Calculating how many hours spent in expedition
    $flt_stay_hours =
      ($this->fleetEvent->fleet['fleet_end_stay'] - $this->fleetEvent->fleet['fleet_start_time']) / 3600
      * (SN::$config->game_speed_expedition ?: 1);
    // Adjusting chance for empty outcome - expedition found nothing
    $outcomeConfigs[Constants::OUTCOME_NONE][P_CHANCE] = ceil(Constants::OUTCOME_EXPEDITION_NOTHING_DEFAULT_CHANCE / max(0.1, pow($flt_stay_hours, 1 / 1.7)));

    return $outcomeConfigs;
  }

  /**
   * @param array $outcomeConfigs
   *
   * @return array{0: int, 1: array}
   */
  protected function calculateRollValues(array $outcomeConfigs) {
    // Calculating max chance can be rolled for current expedition
    $chance_max = 0;
    foreach ($outcomeConfigs as $key => &$outcomeConfig) {
      // Removing invalid outcomes - with no chances set or zero chances
      if (empty($outcomeConfig[P_CHANCE])) {
        unset($outcomeConfigs[$key]);
        continue;
      }
      $outcomeConfig[self::K_ROLL_VALUE] = $chance_max = $outcomeConfig[P_CHANCE] + $chance_max;
    }

    return [$outcomeConfigs, $chance_max];
  }

  /**
   * Outcome - ships partially lost
   *
   * @return void
   */
  protected function outcomeShipsLostPartially() {
    // 1-3 pack of 20-30%% -> 20-90%% lost totally
    // Calculating lost share per fleet to maintain mathematical consistency for math model
    $lostShare = mt_rand(1, 3) * (mt_rand(200000, 300000) / CONST_1M);
    foreach ($this->ships as $shipId => $shipCount) {
      $this->shipsLost[$shipId] = ceil($shipCount * $lostShare);
    }
  }

  /**
   * Outcome - lost all fleet
   *
   * @return void
   */
  protected function outcomeLostFleetAll() {
    foreach ($this->ships as $shipsId => $shipCount) {
      $this->shipsLost[$shipsId] += $this->ships[$shipsId];
    }
  }

  /**
   * @return void
   */
  protected function outcomeFoundResources() {
    // Calculating found resources amount in metal
    $found_in_metal = ceil(
      min($this->gainShare * $this->shipsCostInMetal, game_resource_multiplier(true) * CONST_10M, $this->fleetCapacityFree)
      // 95-105%% [0.95 - 1.05]
      * (mt_rand(95 * 10000, 105 * 10000) / CONST_1M)
    ); // game_speed

    // 30-70%% of resources found are found in metal. Large numbers used to add more variability
    $this->resourcesFound[RES_METAL] = floor($found_in_metal * mt_rand(3 * CONST_100K, 7 * CONST_100K) / CONST_1M);
    // Deducing found metal from pool
    $found_in_metal -= $this->resourcesFound[RES_METAL];

    // Converting rest of found metal to crystals. Large numbers used to add more variability
    $found_in_metal = floor($found_in_metal * static::$rates[RES_METAL] / static::$rates[RES_CRYSTAL]);
    // 50-100%% of rest resources are found in crystals
    $this->resourcesFound[RES_CRYSTAL] = floor($found_in_metal * mt_rand(5 * CONST_100K, 10 * CONST_100K) / CONST_1M);
    // Deducing found crystals from pool
    $found_in_metal -= $this->resourcesFound[RES_CRYSTAL];

    // Converting rest of found crystals to deuterium
    $found_in_metal = floor($found_in_metal * static::$rates[RES_CRYSTAL] / static::$rates[RES_DEUTERIUM]);
    // 100% of resources rest are in deuterium
    $this->resourcesFound[RES_DEUTERIUM] = $found_in_metal;
  }

  /**
   *
   * @return void
   */
  protected function outcomeFoundShips() {
    // Рассчитываем эквивалент найденного флота в метале
    $found_in_metal = min($this->gainShare * $this->shipsCostInMetal, game_resource_multiplier(true) * CONST_10M);
    //  13 243 754 000 g x1
    //  60 762 247 000 a x10
    // 308 389 499 488 000 b x500

    // Рассчитываем стоимость самого дорого корабля в металле
    $shipMaxCostInMetal = 0;
    foreach ($this->ships as $ship_id => $ship_amount) {
      $shipMaxCostInMetal = max($shipMaxCostInMetal, static::$shipData[$ship_id][P_COST_METAL]);
    }

    // Ограничиваем корабли только теми, чья стоимость в металле меньше или равно стоимости самого дорогого корабля
    $can_be_found = [];

    foreach (static::$shipData as $ship_id => $ship_info) {
      if (
        $ship_info[P_COST_METAL] <= $shipMaxCostInMetal
        // and not race ship
        && empty($ship_info[P_RACE_SHIP])
        // and not event-related ship
        && empty($ship_info[P_REQUIRE_HIGHSPOT])
      ) {
        $can_be_found[$ship_id] = $ship_info[P_COST_METAL];
      }
    }

    // Убираем колонизаторы и шпионов - миллиарды шпионов и колонизаторов нам не нужны
    unset($can_be_found[SHIP_COLONIZER]);
    unset($can_be_found[SHIP_SPY]);

    while (count($can_be_found) && $found_in_metal >= max($can_be_found)) {
      $found_index     = mt_rand(1, count($can_be_found)) - 1;
      $found_ship      = array_slice($can_be_found, $found_index, 1, true);
      $found_ship_cost = reset($found_ship);
      $found_ship_id   = key($found_ship);

      if ($found_ship_cost > $found_in_metal) {
        unset($can_be_found[$found_ship_id]);
      } else {
        $found_ship_count                 = mt_rand(1, floor($found_in_metal / $found_ship_cost));
        $this->shipsFound[$found_ship_id] += $found_ship_count;
        $found_in_metal                   -= $found_ship_count * $found_ship_cost;
      }
    }
  }

  /**
   * @return void
   */
  protected function outcomeFoundDm() {
    // Рассчитываем количество найденной ТМ
    $this->darkMatterFound = floor(
      min(
        $this->gainShare * $this->shipsCostInMetal / static::$rates[RES_DARK_MATTER],
        self::MAX_DM
      )
      // 75-100%% of calculated value
      * mt_rand(750000, CONST_1M) / CONST_1M
    );
  }

}
