<?php
/**
 * Created by Gorlum 03.09.2016 15:41
 */

namespace Planet;

use \classSupernova;
use \classLocale;
use Common\GlobalContainer;
use DBStatic\DBStaticFleetACS;
use DBStatic\DBStaticNote;
use DBStatic\DBStaticPlanet;

class PlanetRenderer {

  public function __construct(GlobalContainer $c) {

  }

  /**
   * @param array $playerRow
   * @param array $planetRow
   *
   * @return array
   */
  // TODO - redo to unit/unitlist renderer
  public function renderAvailableShips($playerRow, $planetRow) {
    $record_index = 0;
    $ship_list = array();
    foreach (classSupernova::$gc->groupFleet as $n => $unit_id) {
      $unit_level = mrc_get_level($playerRow, $planetRow, $unit_id, false, true);
      if ($unit_level <= 0) {
        continue;
      }
      $ship_data = get_ship_data($unit_id, $playerRow);
      $ship_list[$unit_id] = array(
        '__INDEX'          => $record_index++,
        'ID'               => $unit_id,
        'NAME'             => classLocale::$lang['tech'][$unit_id],
        'AMOUNT'           => $unit_level,
        'AMOUNT_TEXT'      => pretty_number($unit_level),
        'CONSUMPTION'      => $ship_data['consumption'],
        'CONSUMPTION_TEXT' => pretty_number($ship_data['consumption']),
        'SPEED'            => $ship_data['speed'],
        'SPEED_TEXT'       => pretty_number($ship_data['speed']),
        'CAPACITY'         => $ship_data['capacity'],
        'CAPACITY_TEXT'    => pretty_number($ship_data['capacity']),
      );
    }

    sortUnitRenderedList($ship_list, classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT], classSupernova::$user_options[PLAYER_OPTION_FLEET_SHIP_SORT_INVERSE]);

    return $ship_list;
  }

  public function renderPlanetButton(&$planetRow) {
    global $note_priority_classes;

    $result = array(
      'NAME'       => $planetRow['name'],
      'GALAXY'     => $planetRow['galaxy'],
      'SYSTEM'     => $planetRow['system'],
      'PLANET'     => $planetRow['planet'],
      'TYPE'       => $planetRow['planet_type'],
      'TYPE_PRINT' => classLocale::$lang['fl_shrtcup'][$planetRow['planet_type']],
    );

    if (isset($planetRow['priority'])) {
      $result += array(
        'PRIORITY'       => $planetRow['priority'],
        'PRIORITY_CLASS' => $note_priority_classes[$planetRow['priority']],
      );
    }

    if (isset($planetRow['id'])) {
      $result += array(
        'ID' => $planetRow['id'],
      );
    }

    return $result;
  }

  /**
   * @return array
   */
  public function renderACSList(array $dbOwnerRow) {
    $result = array();

    $query = DBStaticFleetACS::db_acs_get_list();
    while ($row = db_fetch($query)) {
      $members = explode(',', $row['eingeladen']);
      foreach ($members as $memberId) {
        if ($memberId == $dbOwnerRow['id']) {
          $result[] = classSupernova::$gc->planetRenderer->renderPlanetButton($row);
        }
      }
    }

    return $result;
  }

  /**
   * @return array
   */
  // TODO - move to Notes
  public function renderPlanetShortcuts(array $dbOwnerRow) {
    $result = array();

    // Building list of shortcuts
    $query = DBStaticNote::db_note_list_select_by_owner_and_planet($dbOwnerRow);
    while ($planetRow = db_fetch($query)) {
      $result[] = classSupernova::$gc->planetRenderer->renderPlanetButton($planetRow);
    }

    return $result;
  }

  /**
   * Building list of own planets & moons
   *
   * @return array
   */
  public function renderOwnPlanets(array $dbOwnerRow, array $dbSourcePlanetRow) {
    $result = array();

    $colonies = DBStaticPlanet::db_planet_list_sorted($dbOwnerRow);
    if (count($colonies) <= 1) {
      return $result;
    }

    foreach ($colonies as $planetRow) {
      if ($planetRow['id'] == $dbSourcePlanetRow['id']) {
        continue;
      }

      $result[] = classSupernova::$gc->planetRenderer->renderPlanetButton($planetRow);
    }

    return $result;
  }

}
