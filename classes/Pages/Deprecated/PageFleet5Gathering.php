<?php
/**
 * Created by Gorlum 30.09.2017 11:01
 */

namespace Pages\Deprecated;

use Planet\DBStaticPlanet;
use \HelperString;
use SnTemplate;

class PageFleet5Gathering {

  /**
   * @var \classLocale $lang
   */
  protected $lang;

  /**
   * @var float[] $infoTransportCapacity
   */
  protected $infoTransportCapacity;

  public function __construct() {
    global $lang;

    $this->lang = $lang;
    $this->infoTransportCapacity = $this->getTransportUnitsCapacity();
  }

  /**
   * @param array     $user
   * @param array     $planetCurrent
   * @param float[][] $resources_taken - [int|string $planetId][int $resourceId] => float $resourceAmount
   *
   * @return array
   */
  public function flt_build_gathering(&$user, &$planetCurrent, $resources_taken = []) {
    // Caching sn_data names for quick access
    $planet_list = [];

    $takeAllResources = !is_array($resources_taken) || empty($resources_taken);
    if ($takeAllResources) {
      $query = '';
    } else {
      $query = implode(',', array_keys($resources_taken));
      $query = " AND `destruyed` = 0 AND `id` IN ({$query})";
    }

    $planets_db_list = DBStaticPlanet::db_planet_list_sorted($user, $planetCurrent['id'], '*', $query);
    !is_array($planets_db_list) ? $planets_db_list = [] : false;

    foreach ($planets_db_list as $planet_id => &$planetRecord) {
      // begin planet loop
      sn_db_transaction_start();
      // Вот тут надо посчитать - отработать очереди и выяснить, сколько ресов на каждой планете
      $planetRecord = sys_o_get_updated($user, $planetRecord, SN_TIME_NOW, true);
      $planetRecord = $planetRecord['planet'];
      sn_db_transaction_commit();

      if ($takeAllResources) {
        $resources_taken[$planet_id] = 1;
      }

      $planetResources = $this->calcPlanetResources($user, $resources_taken, $planetRecord);
      $fleetCapacityList = $this->calcFleetCapacity($user, $planetRecord);
      $fleetFullCapacity = array_sum($fleetCapacityList);

      $fleet = $this->calcShipAmount($fleetCapacityList, min($planetResources, $fleetFullCapacity));

      $result = ATTACK_NO_FLEET;
      $travel_data = null;
      if (!empty($fleet)) {
        $travel_data = flt_travel_data($user, $planetCurrent, $planetRecord, $fleet, 10);

        if (floor(mrc_get_level($user, $planetRecord, RES_DEUTERIUM, true)) >= $travel_data['consumption']) {
          $will_take = min($planetResources, $fleetFullCapacity) - $travel_data['consumption'];

          $resourcesTaken = $this->fillFleetResources($user, $resources_taken, $planetRecord, $will_take, $fleet);
          $result = ATTACK_ALLOWED;
        } else {
          $result = ATTACK_NO_FUEL;
        }
      }

      $planet_list[$planet_id] =
        [
          'PLANET_DB_DATA' => $planetRecord,
          'ID'             => $planetRecord['id'],
          'NAME'           => $planetRecord['name'],
          'GALAXY'         => $planetRecord['galaxy'],
          'SYSTEM'         => $planetRecord['system'],
          'PLANET'         => $planetRecord['planet'],
          'TYPE'           => $planetRecord['planet_type'],
          'TYPE_PRINT'     => $this->lang['sys_planet_type'][$planetRecord['planet_type']],
          'METAL'          => floor($planetRecord['metal']),
          'CRYSTAL'        => floor($planetRecord['crystal']),
          'DEUTERIUM'      => floor($planetRecord['deuterium']),
          'METAL_TEXT'     => HelperString::numberFloorAndFormat($planetRecord['metal']),
          'CRYSTAL_TEXT'   => HelperString::numberFloorAndFormat($planetRecord['crystal']),
          'DEUTERIUM_TEXT' => HelperString::numberFloorAndFormat($planetRecord['deuterium']),
          'RESOURCES'      => $planetResources,
          'RESOURCES_TEXT' => HelperString::numberFloorAndFormat($planetResources),

          'FLEET'               => $fleet,
          'FLEET_RESOURCES'     => $resourcesTaken,
          'FLEET_CAPACITY'      => $fleetFullCapacity,
          'FLEET_CAPACITY_TEXT' => prettyNumberStyledCompare($fleetFullCapacity, -$planetResources),

          'RESULT' => $result,
//          'MESSAGE' => $this->lang['fl_attack_error'][$result],
        ]
        + (!empty($travel_data) ?
          [
            'FLEET_SPEED'   => $travel_data['fleet_speed'],
            'DISTANCE'      => $travel_data['distance'],
            'DURATION'      => $travel_data['duration'],
            'DURATION_TEXT' => $travel_data['duration'] ? pretty_time($travel_data['duration']) : $this->lang['flt_no_fuel'],
            'CONSUMPTION'   => $travel_data['consumption'],
          ]
          : []);
    } // end planet loop

    return $planet_list;
  }

  /**
   * @return array
   */
  protected function getTransportUnitsCapacity() {
    $transports = [];
    foreach (sn_get_groups('flt_transports') as $transport_id) {
      $transports[$transport_id] = get_unit_param($transport_id, P_CAPACITY);
    }
    arsort($transports);

    return $transports;
  }

  /**
   * @param array $user
   * @param array $resources_taken
   * @param array $planet_db_data
   *
   * @return float
   */
  protected function calcPlanetResources(&$user, $resources_taken, $planet_db_data) {
    $planet_resources = 0;
    foreach (sn_get_groups('resources_loot') as $resource_id) {
      if ($resources_taken[$planet_db_data['id']] == 1 || $resources_taken[$planet_db_data['id']][$resource_id] > 0) {
        $planet_resources += floor(mrc_get_level($user, $planet_db_data, $resource_id, true, true));
      }
    }

    return $planet_resources;
  }

  /**
   * @param array $user
   * @param array $planet_db_data
   *
   * @return float[]
   */
  protected function calcFleetCapacity(&$user, $planet_db_data) {
    $fleetCapacityList = [];
    foreach ($this->infoTransportCapacity as $ship_id => $ship_capacity) {
      if (($ship_count = mrc_get_level($user, $planet_db_data, $ship_id, true, true)) > 0) {
        $fleetCapacityList[$ship_id] = $ship_count * $ship_capacity;
      }
    }

    return $fleetCapacityList;
  }

  /**
   * @param float[] $fleetCapacityList - List of capacities per ship
   * @param float   $maxResourcesToTake - Maximum resources that can be taken from this planet with whole transport fleet
   *
   * @return array
   */
  protected function calcShipAmount($fleetCapacityList, $maxResourcesToTake) {
    $fleet = [];
    foreach ($fleetCapacityList as $ship_id => $shipCapacity) {
      $can_take = min($maxResourcesToTake, $shipCapacity);
      if ($can_take <= 0) {
        continue;
      }

      $fleet[$ship_id] = ceil($can_take / $this->infoTransportCapacity[$ship_id]);

      $maxResourcesToTake -= $can_take;
      if ($maxResourcesToTake <= 0) {
        break;
      }
    }

    return $fleet;
  }

  /**
   * @param array $user
   * @param array $resources_taken
   * @param array $planetRecord
   * @param float $will_take
   * @param array $fleet
   */
  protected function fillFleetResources(&$user, $resources_taken, $planetRecord, $will_take, &$fleet) {
    $result = [];
    foreach (sn_get_groups('resources_loot') as $resource_id) {
      if ($resources_taken[$planetRecord['id']] != 1 && !$resources_taken[$planetRecord['id']][$resource_id]) {
        continue;
      }

      $resource_amount = floor(mrc_get_level($user, $planetRecord, $resource_id, true, true));

      $result[$resource_id] = min($will_take, $resource_amount);
      $will_take -= $resource_amount;

      if ($will_take <= 0) {
        break;
      }
    }

    return $result;
  }


  /**
   * @param array     $playerRecord
   * @param array     $planetRecord
   * @param \template $template
   */
  public function modelFleet5Gathering(&$playerRecord, &$planetRecord, $template) {
    if (empty($resources_taken = sys_get_param('resources')) || !is_array($resources_taken)) {
      return;
    }

    $planet_list = $this->flt_build_gathering($playerRecord, $planetRecord, $resources_taken);

    foreach ($planet_list as $planet_id => $planet_data) {
      if ($planet_data['RESULT'] == ATTACK_ALLOWED) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $planet_data['RESULT'] = flt_t_send_fleet(
          $playerRecord,
          $planet_data['PLANET_DB_DATA'],
          $planetRecord,
          $planet_data['FLEET'],
          $planet_data['FLEET_RESOURCES'],
          MT_TRANSPORT);
      }

      $planet_data['MESSAGE'] = $this->lang['fl_attack_error'][$planet_data['RESULT']];

      $template->assign_block_vars('results', $planet_data);
      if (!empty($planet_data['FLEET']) && $planet_data['RESULT'] == ATTACK_ALLOWED) {
        foreach ($planet_data['FLEET'] as $unit_id => $amount) {
          $template->assign_block_vars('results.units', [
            'ID'     => $unit_id,
            'NAME'   => $this->lang['tech'][$unit_id],
            'AMOUNT' => $amount
          ]);
        }
      }
    }
  }

  /**
   * @param array     $user
   * @param array     $planetrow
   * @param \template $template
   */
  public function viewPage5Gathering(&$user, &$planetrow, $template) {
    $planet_list = $this->flt_build_gathering($user, $planetrow, []);
    foreach ($planet_list as $planet_data) {
//      $planet_data['DURATION'] = $planet_data['DURATION'] ? pretty_time($planet_data['DURATION']) : $this->lang['flt_no_fuel'];
      $template->assign_block_vars('colonies', $planet_data);
    }

    $template->assign_vars([
      'PAGE_HINT'      => $this->lang['fl_page5_hint'],
      'METAL_NEED'     => HelperString::numberFloorAndFormat(max(0, -sys_get_param_float('metal'))),
      'CRYSTAL_NEED'   => HelperString::numberFloorAndFormat(max(0, -sys_get_param_float('crystal'))),
      'DEUTERIUM_NEED' => HelperString::numberFloorAndFormat(max(0, -sys_get_param_float('deuterium'))),
    ]);

    tpl_set_resource_info($template, $planetrow, []);

    SnTemplate::display($template, $this->lang['fl_title']);
  }

}
