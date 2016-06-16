<?php

/**
 * Class UnitList
 * Indexed by DB_ID - as it should be!
 *
 *
 *
 * Hints for IDE - inherited from ArrayAccessV2
 *
 * @method Unit offsetGet($offset)
 * @property Unit[] $_container
 *
 */
class UnitList extends ContainerArrayOfObject implements IDbRow, ILocation {


  // Properties ********************************************************************************************************

  // ILocation implementation ==========================================================================================

  /**
   * Type of this location
   *
   * @var int $locationType
   */
  protected static $locationType = LOC_UNIT_LIST;
  /**
   * @var ILocation $locatedAt
   */
  protected $locatedAt = null;


  // New properties ====================================================================================================

  /**
   * @var Unit[] $mapUnitIdToDb
   */
  // Нужно для корректного сохранения новых юнитов. Их db_id = 0, поэтому при добавлении в контейнер они будут перезаписывать друг друга
  // Соответственно - при сохраненнии флота надо проходить dbSave именно по $mapUnitIdToDb
  protected $mapUnitIdToDb = array();


  // Methods ***********************************************************************************************************

  // ILocation implementation ==========================================================================================

  public function getPlayerOwnerId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getPlayerOwnerId() : null;
  }

  public function getLocationType() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : LOC_NONE;
  }

  public function getLocationDbId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : null;
  }

  // TODO - достаточно установить один раз Unit::LocatedAt на UnitList, что бы затем все юниты автоматически брали наиболее актуальный locatedAt
  public function setLocatedAt($location) {
    $this->locatedAt = $location;
    // TODO - по факту не нужно - достточно один раз поставить на $this
//    foreach($this->_container as $unit) {
//      $unit->setLocatedAt($this->locatedAt);
//    }
  }

  public function getLocatedAt() {
    return $this->locatedAt;
  }

  public function getLocatedAtType() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : LOC_NONE;
  }

  public function getLocatedAtDbId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : 0;
  }


  // ArrayAccessV2 inheritance =========================================================================================

  /**
   * Adds link to unit object also to mapUnitIdToDb
   *
   * @param mixed $offset
   * @param Unit  $value
   */
  public function offsetSet($offset, $value) {
    if (isset($this->mapUnitIdToDb[$value->unitId])) {
      classSupernova::$debug->error('UnitList::offsetSet: Unit with UnitId ' . $value->unitId . ' already exists');
    }
    $this->mapUnitIdToDb[$value->unitId] = $value;
    parent::offsetSet($offset, $value);
  }

  public function offsetUnset($offset) {
    if (!empty($this[$offset]->unitId)) {
//      $unit_id = $this[$offset]->unitId;
//      $this->mapUnitIdToDb[$unit_id] = null;
//      unset($this->mapUnitIdToDb[$unit_id]);
      unset($this->mapUnitIdToDb[$this[$offset]->unitId]);
    }
    parent::offsetUnset($offset);
  }


  // IDbRow implementation =============================================================================================

  /**
   * Loading object from DB by primary ID
   * Real location should be set before calling this method
   *
   * @param int $dbId - dbId is generally unused here. However it works as flag: 0 - just reset; (negative) - just reset; (positive) - proceed with loading
   */
  public function dbLoad($dbId, $lockSkip = false) {
//    $this->_reset();

    if ($dbId <= 0) {
      return;
    }

    if (!is_object($this->locatedAt)) {
      classSupernova::$debug->error('UnitList::dbLoad have no locatedAt field set');
    }

    $unit_array = classSupernova::db_get_unit_list_by_location(0, $this->getLocationType(), $this->getLocationDbId());
    if (!is_array($unit_array)) {
      return;
    }

    foreach ($unit_array as $unit_db_row) {
      $unit = $this->_createElement();
      $unit->dbRowParse($unit_db_row);

      // TODO - сюда вставить разборку бонусов данного юнитлиста - тех бонусов, которые Grants данный юнит добавить в список бонусов юнит-листа

      $this[$unit->dbId] = $unit;
    }

    // TODO - Применить бонусы от location
    // Точнее - опустить бонусы с юнитлиста (те, которые Grants) на каждый юнит (те, которые receives)
    // Вообще-то Receives это будут параметры каждого юнита
  }

  public function dbSave() {
    if (!is_object($this->locatedAt)) {
      classSupernova::$debug->error('UnitList::dbSave have no locatedAt field set');
    }

    foreach ($this->mapUnitIdToDb as $unit) {
      $unit_db_id = $unit->dbId;
      $unit->dbSave();

      if ($unit->isEmpty()) {
        // Removing unit object
        // TODO - change when there will be common bus for all objects
        // ...or should I? If COUNT is empty - it means that object does not exists in DB. So it should be deleted from PHP memory and cache too
        unset($this[$unit_db_id]);
      } else {
        if ($unit->dbId <= 0) {
          classSupernova::$debug->error('Error writing unit to DB');
        }
        // If unit is new then putting unit object to container
        if (empty($this->_container[$unit->dbId])) {
          $this->_container[$unit->dbId] = $unit;
        }
      }
    }
  }





  // Other =============================================================================================================

  /**
   * @return Unit
   *
   * @version 41a50.9
   */
  // TODO - Factory
  public function _createElement() {
    $unit = new Unit();
    $unit->setLocatedAt($this);

    return $unit;
  }

  /**
   * Set unit count of $unit_id to $unit_count
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int $unit_id
   * @param int $unit_count
   */
  public function unitSetCount($unit_id, $unit_count = 0) {
    $this->unitAdjustCount($unit_id, $unit_count, true);
  }

  public function unitGetCount($unit_id) {
    if (empty($this->mapUnitIdToDb[$unit_id])) {
      throw new Exception('Unit [' . $unit_id . '] is not exists in UnitList');
    }

    return $this->mapUnitIdToDb[$unit_id]->count;
  }

  /**
   * Adjust unit count of $unit_id by $unit_count - or just replace value
   * If there is no $unit_id - it will be created and saved to DB on dbSave
   *
   * @param int  $unit_id
   * @param int  $unit_count
   * @param bool $replace_value
   */
  public function unitAdjustCount($unit_id, $unit_count = 0, $replace_value = false) {
    if (empty($this->mapUnitIdToDb[$unit_id])) {
      // If unit not exists - creating one and setting all attributes
      $this->mapUnitIdToDb[$unit_id] = $this->_createElement();
      $this->mapUnitIdToDb[$unit_id]->setUnitId($unit_id);
      $this->mapUnitIdToDb[$unit_id]->setLocatedAt($this);
    }

    if ($replace_value) {
      $this->mapUnitIdToDb[$unit_id]->count = $unit_count;
    } else {
      $this->mapUnitIdToDb[$unit_id]->adjustCount($unit_count);
    }
  }

  /**
   * Get unit list in array as $unit_id => $unit_count
   *
   * @return array
   */
  public function unitsGetArray() {
    $result = array();
    foreach ($this->mapUnitIdToDb as $unit) {
      $result[$unit->unitId] = $unit->count;
    }

    return $result;
  }

  public function unitsCountApplyLossMultiplier($ships_lost_multiplier) {
    foreach ($this->mapUnitIdToDb as $unit_id => $unit) {
      $unit->count = floor($unit->count * $ships_lost_multiplier);
    }
  }

  public function unitsCount() {
    return $this->unitsPropertySumById(0, 'count');
  }

  /**
   * Get count of units in UnitList by unit_id (or all units if unit_id == 0)
   *
   * @param int $unit_id - 0 - all units
   *
   * @return int
   */
  public function unitsCountById($unit_id = 0) {
    return $this->unitsPropertySumById($unit_id, 'count');
  }

  /**
   * @param int    $unit_id
   * @param string $propertyName
   *
   * @return int
   */
  public function unitsPropertySumById($unit_id = 0, $propertyName = 'count') {
    $result = 0;
    foreach ($this->mapUnitIdToDb as $unit) {
      if (!$unit_id || $unit->unitId == $unit_id) {
        $result += $unit->$propertyName;
      }
    }

    return $result;
  }

  // TODO - WRONG FOR STRUCTURES
  public function shipsCapacity() {
    return $this->shipsPoolPropertySumById(0, 'capacity');
  }

  // TODO - WRONG FOR STRUCTURES
  public function shipsPoolPropertySumById($unit_id = 0, $propertyName = 'count') {
    $result = 0;
    foreach ($this->mapUnitIdToDb as $unit) {
      if (!$unit_id || $unit->unitId == $unit_id) {
        $result += $unit->$propertyName * $unit->count;
      }
    }

    return $result;
  }

  public function shipsIsEnoughOnPlanet($dbOwnerRow, $dbPlanetRow) {
    foreach ($this->mapUnitIdToDb as $unitId => $unit) {
      if ($unit->count > mrc_get_level($dbOwnerRow, $dbPlanetRow, $unit->unitId)) {
        return false;
      }
    }

    return true;
  }

  /**
   * @return array
   * @throws Exception
   */
  public function unitsRender() {
    /**
     * @var Fleet $objFleet
     */
    $objFleet = $this->getLocatedAt();
    if (empty($objFleet)) {
      throw new Exception('No fleet owner on UnitList::unitsRender() in ' . __FILE__ . '@' . __LINE__);
    }

    $tplShips = array();
    foreach ($this->mapUnitIdToDb as $unit) {
      $ship_id = $unit->unitId;
      $ship_count = $unit->count;
      if (!UnitShip::is_in_group($ship_id) || $ship_count <= 0) {
        continue;
      }

      $ship_base_data = get_ship_data($ship_id, $objFleet->dbOwnerRow);
//      $template->assign_block_vars('fleets.ships', array(
      $tplShips[] = array(
        'ID'          => $ship_id,
        'NAME'        => classLocale::$lang['tech'][$ship_id],
        'AMOUNT'      => $ship_count,
        'AMOUNT_TEXT' => pretty_number($ship_count),
        'CONSUMPTION' => $ship_base_data['consumption'],
        'SPEED'       => $ship_base_data['speed'],
        'CAPACITY'    => $ship_base_data['capacity'],
      );
    }

    return $tplShips;
  }

  /**
   * @param $user
   *
   * @return int|mixed
   */
  // TODO - REDO!!!!
  public function shipsSpeedMin($user) {
    $speeds = array();
    if (!empty($this->mapUnitIdToDb)) {
      foreach ($this->mapUnitIdToDb as $ship_id => $unit) {
        if ($unit->getCount() > 0 && in_array($unit->unitId, sn_get_groups(array('fleet', 'missile')))) {
          $single_ship_data = get_ship_data($unit->unitId, $user);
          $speeds[] = $single_ship_data['speed'];
        }
      }
    }

    return empty($speeds) ? 0 : min($speeds);
  }


  // TODO - REDO!!!!
  public function travelData($speed_percent = 10, $distance, $dbOwnerRow) {
    $consumption = 0;
    $capacity = 0;
    $duration = 0;

    $speed_percent = $speed_percent ? max(min($speed_percent, 10), 1) : 10;

    $game_fleet_speed = flt_server_flight_speed_multiplier();
    $fleet_speed = $this->shipsSpeedMin($dbOwnerRow);
    $real_speed = $speed_percent * sqrt($fleet_speed);

    if ($fleet_speed && $game_fleet_speed) {
      $duration = max(1, round((35000 / $speed_percent * sqrt($distance * 10 / $fleet_speed) + 10) / $game_fleet_speed));

      foreach ($this->mapUnitIdToDb as $ship_id => $unit) {
        if (!$unit->unitId || $unit->getCount() <= 0) {
          continue;
        }

        $single_ship_data = get_ship_data($unit->unitId, $dbOwnerRow);
        $single_ship_data['speed'] = $single_ship_data['speed'] < 1 ? 1 : $single_ship_data['speed'];

        $consumption += $single_ship_data['consumption'] * $unit->getCount() * pow($real_speed / sqrt($single_ship_data['speed']) / 10 + 1, 2);
        $capacity += $single_ship_data['capacity'] * $unit->getCount();
      }

      $consumption = round($distance * $consumption / 35000) + 1;
    }

    return array(
      'fleet_speed'            => $fleet_speed,
      'distance'               => $distance,
      'duration'               => $duration,
      'consumption'            => $consumption,
      'capacity'               => $capacity,
      'hold'                   => $capacity - $consumption,
      'transport_effectivness' => $consumption ? $capacity / $consumption : 0,
    );
  }

  /**
   * @param $group
   *
   * @return bool
   */
  public function unitsInGroup($group) {
    foreach ($this->mapUnitIdToDb as $unitId => $unit) {
      if (!in_array($unitId, $group)) {
        return false;
      }
    }

    return true;
  }

  public function unitsIsAllMovable($dbOwnerRow) {
    foreach ($this->mapUnitIdToDb as $unitId => $unit) {
      $single_ship_data = get_ship_data($unit->unitId, $dbOwnerRow);
      if ($single_ship_data['speed'] <= 0) {
        return false;
      }
    }

    return true;
  }

  public function unitsPositive() {
    foreach ($this->mapUnitIdToDb as $unitId => $unit) {
      if ($unit->count < 1) {
        return false;
      }
    }

    return true;
  }

  /**
   * @param array $dbOwnerRow
   * @param int   $sourcePlanetRowId
   *
   * @return array
   */
  public function db_prepare_old_changeset_for_planet($dbOwnerRow, $sourcePlanetRowId) {
    $db_changeset = array();
    foreach ($this->mapUnitIdToDb as $unit) {
      $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit->unitId, -$unit->count, $dbOwnerRow, $sourcePlanetRowId);
    }

    return $db_changeset;
  }


  // TODO - DEBUG - REMOVE =============================================================================================
  public function _dump() {
    print(__FILE__ . ':' . __LINE__ . "<br />");
    print("Located at " . $this->getLocationDbId() . " type " . $this->getLocationType() . "<br />");

    print('<table border="1">');
    print('<tr>');

    print('<th>');
    print('dbId');
    print('</th>');

    print('<th>');
    print('type');
    print('</th>');

    print('<th>');
    print('unitId');
    print('</th>');

    print('<th>');
    print('count');
    print('</th>');

    print('<th>');
    print('playerOwnerId');
    print('</th>');

    print('<th>');
    print('location');
    print('</th>');

    print('<th>');
    print('locationType');
    print('</th>');

    print('<th>');
    print('locationDbId');
    print('</th>');

    print('<th>');
    print('timeStart');
    print('</th>');

    print('<th>');
    print('timeFinish');
    print('</th>');

    print('</tr>');

    foreach ($this->mapUnitIdToDb as $unit) {
      print('<tr>');

      print('<td>');
      print($unit->dbId);
      print('</td>');

      print('<td>');
      $type = $unit->getType();
      print("[{$type}] " . classLocale::$lang['tech'][$type]);
      print('</td>');

      print('<td>');
      print("[{$unit->unitId}] " . classLocale::$lang['tech'][$unit->unitId]);
      print('</td>');

      print('<td>');
      print($unit->count);
      print('</td>');

      print('<td>');
      print($unit->getPlayerOwnerId());
      print('</td>');

      print('<td>');
//      print($unit->location);
      print('</td>');

      print('<td>');
      print($unit->getLocationType());
      print('</td>');

      print('<td>');
      print($unit->getLocationDbId());
      print('</td>');

      print('<td>');
      print($unit->getTimeStart());
      print('</td>');

      print('<td>');
      print($unit->getTimeFinish());
      print('</td>');

      print('</tr>');
    }
    print('</table>');
  }

  public function unitZeroDbId() {
    foreach ($this->mapUnitIdToDb as $unit) {
      $unit->zeroDbId();
    }
  }

  public function unitZeroCount() {
    foreach ($this->mapUnitIdToDb as $unit) {
      $unit->count = 0;
    }
  }

}
