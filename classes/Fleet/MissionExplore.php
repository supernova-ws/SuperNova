<?php
/**
 * Created by Gorlum 07.12.2017 13:44
 */

namespace Fleet;

use \SN;
use Common\OutcomeManager;

/**
 * Class MissionExplore
 * @package Fleet
 */
class MissionExplore extends MissionData {
  // -------------------------------------------------------------------------------------------------------------------
  /**
   * @var float[] $rates
   */
  protected static $rates;

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * @var int $hardDmLimit
   */
  protected $hardDmLimit = 10000;

  /**
   * @var float $hardResourcesLimit
   */
  protected $hardResourcesLimit = 10000000; // 10m

  /**
   * @var float $hardShipCostResourceLimit
   */
  protected $hardShipCostResourceLimit = 10000000; // 10m

  /**
   * List of ships which should be excluded from found list
   *
   * @var int[] $shipsToRemove
   */
  protected $shipsToRemove = [SHIP_COLONIZER, SHIP_SPY,];

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * @var array $outcome
   */
  protected $outcome = [];

  /**
   * Primary outcome ID
   *
   * Shows global outcome
   *
   * @var int $outcomeType
   */
  protected $outcomeType = FLT_EXPEDITION_OUTCOME_NONE;

  /**
   * Secondary outcome data
   *
   * @var array $secondaryInfo
   */
  protected $secondaryInfo;

  /**
   * @var int $darkMatterFound
   */
  protected $darkMatterFound = 0;

  /**
   * How much ships LOST in expedition
   *
   * @var float[] $shipsLost
   */
  protected $shipsLost = [];

  /**
   * How much ships FOUND in expedition
   *
   * @var float[] $shipsFound
   */
  protected $shipsFound = [];

  /**
   * @var float[] $resourcesFound
   */
  protected $resourcesFound = [];

  /**
   * @var string[] $message
   */
  protected $message = [];


  /**
   * MissionExplore constructor.
   *
   * @param array $missionArray
   */
  public function __construct($missionArray) {
    parent::__construct($missionArray);

    if (empty(static::$rates)) {
      static::$rates = SN::$gc->economicHelper->getResourcesExchange();
    }

    $this->shipsFound = [];
  }

  /**
   * @param float $multiplier
   */
  protected function applyShipLoss($multiplier) {
//    foreach ($this->fleetEntity->getShipList() as $unit_id => $unit_amount) {
//      $shipsLost = ceil($unit_amount * $multiplier);
//      $this->shipsLost[$unit_id] += $shipsLost;
//    }
    foreach ($this->fleetEntity->calcShipLossByMultiplier($multiplier) as $shipId => $lost) {
      $this->shipsLost[$shipId] += $lost;
    }
  }

  /**
   * Mission outcome: Lost all ships
   */
  protected function outcomeShipsLostAll() {
    $this->applyShipLoss(1);
  }

  /**
   * Mission outcome: Lost some ships
   */
  protected function outcomeShipsLostPart() {
    $this->applyShipLoss(1 / mt_rand(1, 3));
  }


  protected function outcomeShipsFound() {
    // Рассчитываем эквивалент найденного флота в метале
    $found_in_metal = min(
      $this->secondaryInfo[P_MULTIPLIER] * $this->fleetEntity->getCostInMetal(),
      $this->getGameMiningSpeed() * $this->hardShipCostResourceLimit
    );

    // TODO - убрать рассовые корабли
    $manager = new OutcomeManager();
    $can_be_found = $this->possibleShipsCosts();
    foreach ($can_be_found as $unitId => $temp) {
      $manager->add($unitId, 1);
    }

    $foundFleet = [];
    while (count($manager) && $found_in_metal >= 0) {
      $foundUnitId = $manager->rollOutcome();
      $found_ship_cost = $can_be_found[$foundUnitId];
      if ($found_ship_cost > $found_in_metal) {
        unset($can_be_found[$foundUnitId]);
        $manager->remove($foundUnitId);
      } else {
        $found_ship_count = mt_rand(1, floor($found_in_metal / $found_ship_cost));
        $foundFleet[$foundUnitId] += $found_ship_count;
        $found_in_metal -= $found_ship_count * $found_ship_cost;
      }
    }

    if (empty($foundFleet)) {
      $this->message[] = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    } else {
      foreach ($foundFleet as $unit_id => $unit_amount) {
        $this->shipsFound[$unit_id] += $unit_amount;
      }
    }

  }

  /**
   *
   */
  protected function outcomeResourcesFound() {
    // Рассчитываем количество найденных ресурсов
    $found_in_metal = ceil(
      min(
      // Resources found
        $this->secondaryInfo[P_MULTIPLIER] * $this->fleetEntity->getCostInMetal(),
        // Not more then maximum fleet capacity
        $this->fleetEntity->getCapacityActual(),
        // Hard limit
        $this->getGameMiningSpeed() * $this->hardResourcesLimit
      )
      // Randomizing from 90% to 110%
      * $this->getRandomFoundResourcesMultiplier()
    );

    $resourcesFound = [];
    // 30-70%% - metal
    $resourcesFound[RES_METAL] = floor($found_in_metal * $this->getRandomFoundMetalMultiplier());
    $found_in_metal -= $resourcesFound[RES_METAL];

    // 50-100%% of rest - is crystal
    $found_in_crystal = floor($found_in_metal * static::$rates[RES_METAL] / static::$rates[RES_CRYSTAL]);
    $resourcesFound[RES_CRYSTAL] = floor($this->getRandomFoundCrystalMultiplier() * $found_in_crystal);
    $found_in_crystal -= $resourcesFound[RES_CRYSTAL];

    // Rest - deuterium
    $resourcesFound[RES_DEUTERIUM] = floor($found_in_crystal * static::$rates[RES_CRYSTAL] / static::$rates[RES_DEUTERIUM]);

    if (array_sum($resourcesFound) <= 0) {
      $this->message = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    } else {
      foreach ($resourcesFound as $resourceId => $foundAmount) {
        $this->resourcesFound += $resourcesFound;
      }
    }
  }

  /**
   *
   */
  protected function outcomeDmFound() {
    // Рассчитываем количество найденной ТМ
    $this->darkMatterFound = floor(
      min(
        $this->secondaryInfo[P_MULTIPLIER] * $this->fleetEntity->getCostInMetal() / static::$rates[RES_DARK_MATTER],
        // Hard limit - not counting game speed
        $this->hardDmLimit
      )
      // 75-100%%
      * $this->getRandomFoundDmMultiplier()
    );

    if (!$this->darkMatterFound) {
      $this->message[] = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    }
  }


  protected function prependMainMessage() {
    $messages = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['messages'];
    // There is variants and one variant selected
    if (isset($this->secondaryInfo[P_MESSAGE_ID]) && !empty($messages[$this->secondaryInfo[P_MESSAGE_ID]])) {
      // Selecting selected variant
      $messages = $messages[$this->secondaryInfo[P_MESSAGE_ID]];
    }
    $message =
      // If there is only one string message - just adding it
      is_string($messages)
        ? $messages
        // If there is array of possible messages - selecting one of them randomly
        : (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : '');

    // Adding outcome message to the front of other info
    array_unshift($this->message, $message);
  }

  /**
   * @param array $mission_data
   */
  public function process() {
    if (!isset($this->fleet_event) || $this->fleet_event != EVENT_FLT_ACOMPLISH) {
      return;
    }

    $this->outcome = $this->rollOutcome();
    $this->outcomeType = $this->outcome[P_MISSION_EXPEDITION_OUTCOME];
    if (!empty($this->outcome[P_MISSION_EXPEDITION_OUTCOME_SECONDARY])) {
      // $this->rollSecondaryOutcome($this->outcome[P_MISSION_EXPEDITION_OUTCOME_SECONDARY]);
      $this->secondaryInfo = OutcomeManager::rollArray($this->outcome[P_MISSION_EXPEDITION_OUTCOME_SECONDARY]);
    }

    switch ($this->outcomeType) {
      case FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL:
        $this->outcomeShipsLostAll();
      break;

      case FLT_EXPEDITION_OUTCOME_LOST_FLEET:
        $this->outcomeShipsLostPart();
      break;

      case FLT_EXPEDITION_OUTCOME_FOUND_FLEET:
        $this->outcomeShipsFound();
      break;

      case FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES:
        $this->outcomeResourcesFound();
      break;

      case FLT_EXPEDITION_OUTCOME_FOUND_DM:
        $this->outcomeDmFound();
      break;

      case FLT_EXPEDITION_OUTCOME_FOUND_ARTIFACT:
      break;

      case FLT_EXPEDITION_OUTCOME_NONE:
      default:
      break;
    }


    // TODO - ДОПИСАТЬ В МОДУЛЕ!
    flt_mission_explore_addon_object($this);


    $this->dbPlayerUpdateExpeditionExpirience();
    if (!empty($this->darkMatterFound)) {
      $this->dbPlayerChangeDarkMatterAmount();
    }
    foreach ($this->shipsFound as $shipsFoundId => $shipsFoundAmount) {
      $this->fleetEntity->changeShipCount($shipsFoundId, $shipsFoundAmount);
    }
    foreach ($this->shipsLost as $shipsLostId => $shipsLostAmount) {
      $this->fleetEntity->changeShipCount($shipsLostId, -$shipsLostAmount);
    }
    foreach ($this->resourcesFound as $resourceId => $foundAmount) {
      $this->fleetEntity->changeResource($resourceId, $foundAmount);
    }
    $this->dbFleetRecordUpdate();

    $this->prependMainMessage();
    $this->messageDumpUnits('found_dark_matter_new', [RES_DARK_MATTER => $this->darkMatterFound]);
    $this->messageDumpUnits('lost_fleet_new', $this->shipsLost);
    $this->messageDumpUnits('found_fleet_new', $this->shipsFound);
    $this->messageDumpUnits('found_resources_new', $this->resourcesFound);
    $this->messageOutcome();
  }

  /**
   * @return mixed|null
   */
  protected function rollOutcome() {
    $possibleOutcomes = sn_get_groups(GROUP_MISSION_EXPLORE_OUTCOMES);

    // Calculating chance that nothing happens
    $flt_stay_hours = ($this->fleetEntity->timeEndStay - $this->fleetEntity->timeArrive) / 3600 * $this->getGameExpeditionSpeed();
    $nothingHappenChance = ceil($possibleOutcomes[FLT_EXPEDITION_OUTCOME_NONE][P_CHANCE] / pow($flt_stay_hours, 1 / 1.7));
    $possibleOutcomes[FLT_EXPEDITION_OUTCOME_NONE][P_CHANCE] = $nothingHappenChance;

    return OutcomeManager::rollArray($possibleOutcomes);
  }

  /**
   * Get ship list and their cost in metal which can be returned from Expedition
   *
   * Race and event-related ships can't be found in expeditions
   *
   * @return float[] - [(int)$shipId => (float)costInMetal]
   */
  protected function possibleShipsCosts() {
    // Рассчитываем стоимость самого дорого корабля в Экспедиции в пересчёте на металл
    $maxMetalCost = max($this->fleetEntity->getShipsBasicCosts(RES_METAL));

    $canBeFound = [];
    // Potentially - every ship in game can be found...
    foreach (sn_get_groups('fleet') as $shipId) {
      $metalCost = getStackableUnitsCost([$shipId => 1], RES_METAL);
      if (
        // Ships that have cost in metal
        !empty($metalCost)
        // ...and costs less then most expensive ship in fleet - to prevent ship cloning
        && $metalCost < $maxMetalCost
        // and not in remove list - aka not colonizer or spy
        && array_search($shipId, $this->shipsToRemove) === false
        // and is ship
        && get_unit_param($shipId, P_UNIT_TYPE) == UNIT_SHIPS
        // and not race ship
        && empty(get_unit_param($shipId, 'player_race'))
        // and not event-related ship
        && empty(get_unit_param($shipId, REQUIRE_HIGHSPOT))
      ) {
        $canBeFound[$shipId] = $metalCost;
      }
    }

//    foreach ($this->shipsToRemove as $unitId) {
//      unset($basicCostsInMetal[$unitId]);
//    }

    return $canBeFound;
  }

  /**
   * @param int   $messageSubId
   * @param array $unitArray - [(int)unitSnId => (float)unitAmount]
   */
  protected function messageDumpUnits($messageSubId, $unitArray) {
    if (empty($unitArray)) {
      return;
    }

    $this->message[] = $this->lang['flt_mission_expedition'][$messageSubId];
    foreach ($unitArray as $unitSnId => $unitAmount) {
      if (empty($unitAmount)) {
        continue;
      }

      $this->message[] = $this->lang['tech'][$unitSnId] . ' x ' . $unitAmount;
    }
  }

  protected function messageOutcome() {
    $msg_text = sprintf(implode("\r\n", $this->message), $this->fleetEntity->id, uni_render_coordinates($this->fleet, 'fleet_end_'));
    $msg_sender = SN::$lang['flt_mission_expedition']['msg_sender'];
    $msg_title = SN::$lang['flt_mission_expedition']['msg_title'];
    $this->msgSendMessage($msg_sender, $msg_title, $msg_text);
  }


  // Wrappers to mock ==================================================================================================

  // DB wrappers -------------------------------------------------------------------------------------------------------
  protected function dbFleetRecordUpdate() {
    $this->fleetEntity->update();
  }

  protected function dbPlayerUpdateExpeditionExpirience() {
    db_user_set_by_id($this->fleetEntity->ownerId, "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");
  }

  protected function dbPlayerChangeDarkMatterAmount() {
    rpg_points_change($this->fleet['fleet_owner'], RPG_EXPEDITION, $this->darkMatterFound, 'Expedition Bonus');
  }


  // Global procedures wrappers ----------------------------------------------------------------------------------------

  /**
   * Wrapper for game expedition speed
   *
   * @return float|int
   */
  protected function getGameExpeditionSpeed() {
    return SN::$config->game_speed_expedition ? SN::$config->game_speed_expedition : 1;
  }

  protected function getGameMiningSpeed() {
    return game_resource_multiplier(true);
  }

  /**
   * Wrapper to send message
   *
   * @param $msg_sender
   * @param $msg_title
   * @param $msg_text
   */
  protected function msgSendMessage($msg_sender, $msg_title, $msg_text) {
    msg_send_simple_message($this->fleetEntity->ownerId, '', $this->fleetEntity->timeEndStay, MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $msg_text);
  }


  // Randomizers -------------------------------------------------------------------------------------------------------

  /**
   * @param int $min
   * @param int $max
   *
   * @return int
   */
  protected function getRandomOutcomeValue($min, $max) {
    return mt_rand($min, $max);
  }

  /**
   * @return float|int
   */
  protected function getRandomFoundDmMultiplier() {
    return mt_rand(750000, 1000000) / 1000000;
  }

  /**
   * @return float|int
   */
  protected function getRandomFoundResourcesMultiplier() {
    return mt_rand(900000, 1100000) / 1000000;
  }

  /**
   * @return float|int
   */
  protected function getRandomFoundMetalMultiplier() {
    return mt_rand(300000, 700000) / 1000000;
  }

  /**
   * @return float|int
   */
  protected function getRandomFoundCrystalMultiplier() {
    return mt_rand(500000, 1000000) / 1000000;
  }

  /**
   * @return float|int
   */
  protected function getRandomFleetPartLostMultiplier() {
    return mt_rand(1, 3) * mt_rand(200000, 300000) / 1000000;
  }

}
