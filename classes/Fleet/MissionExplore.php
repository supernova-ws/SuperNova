<?php
/**
 * Created by Gorlum 07.12.2017 13:44
 */

namespace Fleet;

use \classSupernova;

/**
 * Class MissionExplore
 * @package Fleet
 */
class MissionExplore extends MissionData {
  // -------------------------------------------------------------------------------------------------------------------
  /**
   * @var int $hardDmLimit
   */
  protected $hardDmLimit = 10000;

  /**
   * @var int $hardResourcesLimit
   */
  protected $hardResourcesLimit = 10000000;

  /**
   * @var int $hardShipCostResourceLimit
   */
  protected $hardShipCostResourceLimit = 10000000;

  /**
   * List of ships which should be excluded from found list
   *
   * @var int[] $shipsToRemove
   */
  protected $shipsToRemove = [SHIP_COLONIZER, SHIP_SPY,];

  // -------------------------------------------------------------------------------------------------------------------
  /**
   * @var float[] $rates
   */
  protected static $rates;


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

  protected $possibleOutcomes = [];

  /**
   * Random result which defines mission outcome
   *
   * @var int $randomValue
   */
  protected $randomValue = 0;

  /**
   * Primary outcome ID
   *
   * Shows global outcome
   *
   * @var int $outcomeType
   */
  protected $outcomeType = FLT_EXPEDITION_OUTCOME_NONE;

  /**
   * Current outcome description
   *
   * @var array $outcomeInfo
   */
  protected $outcomeInfo = [];

  /**
   * Shows secondary outcome if there is any
   *
   * Secondary outcome calculated as round((outcome['value'] - randomValue) / outcome['chance'] * 100, 6) - i.e. it is float percent
   *
   * @var float $secondaryPercent
   */
  protected $secondaryPercent = 0;

  /**
   * Secondary outcome description
   *
   * @var mixed $secondaryInfo
   */
  protected $secondaryInfo;

  /**
   * @var int $darkMatterFound
   */
  protected $darkMatterFound = 0;

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
      static::$rates = classSupernova::$gc->economicHelper->getResourcesExchange();
    }
  }

  /**
   * @throws \Exception
   */
  protected function outcomeShipsLostAll() {
    $this->applyShipLoss(1);
  }

  /**
   * Mission outcome: Lost some ships
   *
   * @throws \Exception
   */
  protected function outcomeShipsLostPart() {
    // Loosing from 2/10 to 9/10 of fleet
    $fleetPartLost = $this->getRandomFleetPartLostMultiplier();
    $this->applyShipLoss($fleetPartLost);
  }

  /**
   * @param float $multiplier
   *
   * @throws \Exception
   */
  protected function applyShipLoss($multiplier) {
    foreach ($this->fleetRecord->getShipList() as $unit_id => $unit_amount) {
      $shipsLost = ceil($unit_amount * $multiplier);
      $this->shipsLost[$unit_id] += $shipsLost;
      $this->fleetRecord->changeShipCount($unit_id, -$shipsLost);
    }
  }

  /**
   * @throws \Exception
   */
  protected function outcomeShipsFound() {
    // Рассчитываем эквивалент найденного флота в метале
    $found_in_metal = min(
      $this->secondaryInfo[P_MULTIPLIER] * $this->fleetRecord->getCostInMetal(),
      $this->getGameMiningSpeed() * $this->hardShipCostResourceLimit
    );
    //  13 243 754 000 g x1
    //  60 762 247 000 a x10
    // 308 389 499 488 000 b x500

    // TODO - убрать рассовые корабли
    $can_be_found = $this->possibleShipsCosts();
//    unset($can_be_found[SHIP_COLONIZER]);
//    unset($can_be_found[SHIP_SPY]);

    $this->shipsFound = [];
    while (count($can_be_found) && $found_in_metal >= max($can_be_found)) {
      $found_index = mt_rand(1, count($can_be_found)) - 1;
      $found_ship = array_slice($can_be_found, $found_index, 1, true);
      $found_ship_cost = reset($found_ship);
      $found_ship_id = key($found_ship);

      if ($found_ship_cost > $found_in_metal) {
        unset($can_be_found[$found_ship_id]);
      } else {
        $found_ship_count = mt_rand(1, floor($found_in_metal / $found_ship_cost));
        $this->shipsFound[$found_ship_id] += $found_ship_count;
        $found_in_metal -= $found_ship_count * $found_ship_cost;
      }
    }

    if (empty($this->shipsFound)) {
      $this->message[] = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    } else {
      foreach ($this->shipsFound as $unit_id => $unit_amount) {
        $this->fleetRecord->changeShipCount($unit_id, $unit_amount);
      }
    }

  }

  /**
   * @throws \Exception
   */
  protected function outcomeResourcesFound() {
    // Рассчитываем количество найденных ресурсов
    $found_in_metal = ceil(
      min(
      // Resources found
        $this->secondaryInfo[P_MULTIPLIER] * $this->fleetRecord->getCostInMetal(),
        // Not more then maximum fleet capacity
        $this->fleetRecord->getCapacity(),
        // Hard limit
        $this->getGameMiningSpeed() * $this->hardResourcesLimit
      )
      // Randomizing from 90% to 110%
      * $this->getRandomFoundResourcesMultiplier()
    );

    // 30-70%% - metal
    $this->resourcesFound[RES_METAL] = floor($found_in_metal * $this->getRandomFoundMetalMultiplier());
    $found_in_metal -= $this->resourcesFound[RES_METAL];

    // 50-100%% of rest - is crystal
    $found_in_crystal = floor($found_in_metal * static::$rates[RES_METAL] / static::$rates[RES_CRYSTAL]);
    $this->resourcesFound[RES_CRYSTAL] = floor($this->getRandomFoundCrystalMultiplier() * $found_in_crystal);
    $found_in_crystal -= $this->resourcesFound[RES_CRYSTAL];

    // Rest - deuterium
    $this->resourcesFound[RES_DEUTERIUM] = floor($found_in_crystal * static::$rates[RES_CRYSTAL] / static::$rates[RES_DEUTERIUM]);

    if (array_sum($this->resourcesFound) <= 0) {
      $this->message = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    } else {
      foreach ($this->resourcesFound as $resourceId => $foundAmount) {
        if (!$foundAmount) {
          continue;
        }

        $this->fleetRecord->changeResource($resourceId, $foundAmount);
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
        $this->secondaryInfo[P_MULTIPLIER] * $this->fleetRecord->getCostInMetal() / static::$rates[RES_DARK_MATTER],
        // Hard limit
        $this->hardDmLimit
      )
      // 75-100%%
//      * mt_rand(750000, 1000000) / 1000000);
      * $this->getRandomFoundDmMultiplier()
    );

    if (!$this->darkMatterFound) {
      $this->message[] = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['no_result'];
    } else {
      $this->dbAccountChangeDarkMatterAmount();
    }
  }


  protected function prependMainMessage() {
    $messages = $this->lang['flt_mission_expedition']['outcomes'][$this->outcomeType]['messages'];
    // There is variants and one variant selected
    if (isset($this->secondaryInfo[P_MESSAGE_ID]) && !empty($messages[$this->secondaryInfo[P_MESSAGE_ID]])) {
      // Selecting selected variant
      $messages = $messages[$this->secondaryInfo[P_MESSAGE_ID]];
    }

    // Adding outcome message to the front of other info
    array_unshift($this->message, is_string($messages) ? $messages :
      (is_array($messages) ? $messages[mt_rand(0, count($messages) - 1)] : ''));
  }

  /**
   * @param array $mission_data
   */
  public function process() {
    if (!isset($this->fleet_event) || $this->fleet_event != EVENT_FLT_ACOMPLISH) {
      return;
    }

//    var_dump('accomplished');
//    return;


    $this->fillOutcomes();
    $this->randomizeOutcome();

//    $this->randomValue = 7; // TODO - REMOVE DEBUG!!!!!!!!!!!!!!!!

    $this->getOutcomeInfo();
    $this->getSecondaryOutcome();

//    var_dump($this->randomValue);
//    var_dump($this->outcomeType);
//    var_dump($this->outcomeInfo);
//    var_dump($this->secondaryPercent);
//    var_dump($this->outcomeInfo);
//    var_dump($this->possibleOutcomes);
//    return;

//    $mission_outcome = $this->outcomeInfo[P_MISSION_EXPEDITION_OUTCOME];

    // Вычисляем вероятность выпадения данного числа в общем пуле
//    $outcome_percent = ($this->outcomeInfo['value'] - $outcome_value) / $this->outcomeInfo['chance'];


//    $msg_text_addon = '';
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

    $this->prependMainMessage();

    // TODO - ДОПИСАТЬ!
    flt_mission_explore_addon_object($this);

    return;

//    db_user_set_by_id($this->fleetRecord->fleet_owner, "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");
    $this->dbPlayerUpdateExpeditionExpirience();

//    if ($this->darkMatterFound) {
//      rpg_points_change($this->fleet['fleet_owner'], RPG_EXPEDITION, $this->darkMatterFound, 'Expedition Bonus');
//      $this->message[] = sprintf($this->lang['flt_mission_expedition']['found_dark_matter'], $this->darkMatterFound);
//    }

    $this->messageDumpUnits('found_dark_matter_new', [RES_DARK_MATTER => $this->darkMatterFound]);
    $this->messageDumpUnits('lost_fleet_new', $this->shipsLost);
    $this->messageDumpUnits('found_fleet_new', $this->shipsFound);
    $this->messageDumpUnits('found_resources_new', $this->resourcesFound);

//    $this->fleetRecord->update();
    $this->dbFleetRecordUpdate();

    $this->messageOutcome();
  }

  /**
   */
  protected function fillOutcomes() {
    $this->possibleOutcomes = sn_get_groups(GROUP_MISSION_EXPLORE_OUTCOMES);
    $flt_stay_hours = ($this->fleetRecord->fleet_end_stay - $this->fleetRecord->fleet_start_time) / 3600 * $this->getGameExpeditionSpeed();

    $this->possibleOutcomes[FLT_EXPEDITION_OUTCOME_NONE]['chance'] = ceil($this->possibleOutcomes[FLT_EXPEDITION_OUTCOME_NONE]['chance'] / pow($flt_stay_hours, 1 / 1.7));

    $chance_max = 0;
    foreach ($this->possibleOutcomes as $key => &$value) {
      // Removing outcomes without chances
      if (empty($value['chance'])) {
        unset($this->possibleOutcomes[$key]);
        continue;
      }
      // Calculating limiting value for each outcome
      $value['value'] = $value['chance'] + $chance_max;
      $chance_max = $value['value'];
    }
  }

  /**
   * Wrapper for randomizing outcome
   */
  protected function randomizeOutcome() {
    $lastOutcome = end($this->possibleOutcomes);
    $chance_max = !is_array($lastOutcome) || empty($lastOutcome) ? 0 : $lastOutcome['value'];

    $this->randomValue = $this->getRandomOutcomeValue(0, $chance_max);
  }

  /**
   */
  protected function getOutcomeInfo() {
    $this->outcomeInfo = $this->possibleOutcomes[FLT_EXPEDITION_OUTCOME_NONE];
    foreach ($this->possibleOutcomes as $key => $value) {
      if ($this->randomValue <= $value['value']) {
        $this->outcomeInfo = $value;
        break;
      }
    }

    $this->outcomeType = $this->outcomeInfo[P_MISSION_EXPEDITION_OUTCOME];
  }

  protected function getSecondaryOutcome() {
    $this->secondaryPercent = round(($this->outcomeInfo['value'] - $this->randomValue) / $this->outcomeInfo['chance'] * 100, 6);

    if (!empty($this->outcomeInfo[P_MISSION_EXPEDITION_OUTCOME_SECONDARY])) {
      // First value is secondary outcome by default
      $this->secondaryInfo = reset($this->outcomeInfo[P_MISSION_EXPEDITION_OUTCOME_SECONDARY]);
      foreach ($this->outcomeInfo[P_MISSION_EXPEDITION_OUTCOME_SECONDARY] as $percent => $secondaryDescription) {
        if ($percent <= $this->secondaryPercent) {
          $this->secondaryInfo = $secondaryDescription;
          break;
        }
      }
    }
  }

  /**
   * @return int[] - [(int)$shipId => (int)costInMetal]
   */
  protected function possibleShipsCosts() {
    // Рассчитываем стоимость самого дорого корабля в металле
    $max_metal_cost = 0;
    foreach ($this->fleetRecord->getShipList() as $ship_id => $ship_amount) {
      $max_metal_cost = max($max_metal_cost, $this->fleetRecord->getShipCostInMetal($ship_id));
    }

    // Ограничиваем корабли только теми, чья стоимость в металле меньше или равно стоимости самого дорогого корабля
    $can_be_found = array();
    foreach ($this->fleetRecord->getShipList() as $shipId => $shipAmount) {
      $metalCost = $this->fleetRecord->getShipCostInMetal($shipId);
      if (!empty($metalCost) && $metalCost < $max_metal_cost) {
        $can_be_found[$ship_id] = $metalCost;
      }
    }

    // Убираем колонизаторы и шпионов - миллиарды шпионов и колонизаторов нам не нужны
    foreach ($this->shipsToRemove as $unitId) {
      unset($can_be_found[$unitId]);
    }

    return $can_be_found;
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
    $msg_text = sprintf(implode("\r\n", $this->message), $this->fleetRecord->id, uni_render_coordinates($this->fleet, 'fleet_end_'));
    $msg_sender = classSupernova::$lang['flt_mission_expedition']['msg_sender'];
    $msg_title = classSupernova::$lang['flt_mission_expedition']['msg_title'];
    $this->msgSendMessage($msg_sender, $msg_title, $msg_text);
  }


  // Wrappers to mock ==================================================================================================

  // DB wrappers -------------------------------------------------------------------------------------------------------
  protected function dbFleetRecordUpdate() {
    $this->fleetRecord->update();
  }

  protected function dbPlayerUpdateExpeditionExpirience() {
    db_user_set_by_id($this->fleetRecord->fleet_owner, "`player_rpg_explore_xp` = `player_rpg_explore_xp` + 1");
  }

  protected function dbAccountChangeDarkMatterAmount() {
    rpg_points_change($this->fleet['fleet_owner'], RPG_EXPEDITION, $this->darkMatterFound, 'Expedition Bonus');
  }


  // Global procedures wrappers ----------------------------------------------------------------------------------------

  /**
   * Wrapper for game expedition speed
   *
   * @return float|int
   */
  protected function getGameExpeditionSpeed() {
    return classSupernova::$config->game_speed_expedition ? classSupernova::$config->game_speed_expedition : 1;
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
    msg_send_simple_message($this->fleetRecord->fleet_owner, '', $this->fleetRecord->fleet_end_stay, MSG_TYPE_EXPLORE, $msg_sender, $msg_title, $msg_text);
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
