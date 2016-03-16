<?php

/**
 * Class UnitList
 * Indexed by DB_ID - as it should be!
 *
 * Hints for IDE - inherited from ArrayAccessV2
 *
 * @method Unit offsetGet($offset)
 * @property Unit[] $_container
 *
 */
class UnitList extends ArrayAccessV2 {
  public $playerOwnerId = 0;
  /**
   * @var Player|Fleet $locatedAt
   */
  public $locatedAt = null;
  public $locatedAtType = LOC_NONE;
  public $locatedAtDbId = 0;

  /**
   * @var Unit[] $mapUnitIdToDb
   */
  // Нужно для корректного сохранения новых юнитов. Их db_id = 0, поэтому при добавлении в контейнер они будут перезаписывать друг друга
  // Соответственно - при сохраненнии флота надо проходить dbSave именно по $mapUnitIdToDb
  public $mapUnitIdToDb = array(); // TEMPORARY - MOVE TO UnitList


  /**
   * @return Unit
   *
   * @version 41a6.2
   */
  public function _createElement() {
    return new Unit();
  }

  public function unitSetCount($unit_id, $unit_count = 0) {
    $this->unitAdjustCount($unit_id, $unit_count, true);
  }

  public function unitAdjustCount($unit_id, $unit_count = 0, $replace_value = false) {
    if(empty($this->mapUnitIdToDb[$unit_id])) {
      // If unit not exists - creating one and setting all attributes
      $this->mapUnitIdToDb[$unit_id] = $this->_createElement();
      $this->mapUnitIdToDb[$unit_id]->setUnitId($unit_id);
      $this->mapUnitIdToDb[$unit_id]->setLocationAndOwner($this->playerOwnerId, $this->locatedAt);
    }

    if($replace_value) {
      $this->mapUnitIdToDb[$unit_id]->setCount($unit_count);
    } else {
      $this->mapUnitIdToDb[$unit_id]->adjustCount($unit_count);
    }
  }

  /**
   * @param Player|Fleet $location
   */
  public function loadByLocation($location) {
    $this->_reset();
//    $this->location = $location;
    $this->locatedAtType = $location::$locationType;
    $this->locatedAtDbId = $location->getLocationDbId();


    $unit_array = classSupernova::db_get_unit_list_by_location(0, $this->locatedAtType, $this->locatedAtDbId);
    if(!is_array($unit_array)) {
      return;
    }

    foreach($unit_array as $unit_db_row) {
      $unit = $this->_createElement();
      $unit->setLocationAndOwner($location->getPlayerOwnerId(), $location);
      $unit->dbRowParse($unit_db_row);

      // TODO - сюда вставить разборку бонусов данного юнитлиста - тех бонусов, которые Grants данный юнит добавить в список бонусов юнит-листа

      if(!empty($this[$unit->db_id])) {
        classSupernova::$debug->error('Unit is already exists in _container');
      }

      $this[$unit->db_id] = $unit;
      $this->mapUnitIdToDb[$unit->unitId] = $unit;
    }

    // TODO - Применить бонусы от location
    // Точнее - опустить бонусы с юнитлиста (те, которые Grants) на каждый юнит (те, которые receives)
    // Вообще-то Receives это будут параметры каждого юнита
  }

  public function getUnitListArray() {
    $result = array();
    foreach($this->mapUnitIdToDb as $unit) {
      $result[$unit->unitId] = $unit->count;
    }

    return $result;
  }

  public function getUnitCount($unit_id) {
    $result = 0;
    foreach($this->mapUnitIdToDb as $unit) {
      if($unit->unitId == $unit_id) {
        $result += $unit->count;
      }
    }

    return $result;
  }


  /**
   * @param int          $playerOwnerId
   * @param Player|Fleet $location
   */
  public function dbSave($playerOwnerId, $location) {
    $this->playerOwnerId = $playerOwnerId;
    $this->locatedAt = $location;
    $this->locatedAtType = $location::$locationType;
    $this->locatedAtDbId = $location->getDbId();

    foreach($this->mapUnitIdToDb as $unit) {
      $unit->setLocationAndOwner($this->playerOwnerId, $location);
      $unit_db_id = $unit->db_id;
      $unit->dbSave();

      if($unit->count == 0) {
        // Removing unit object
        unset($this[$unit_db_id]);
        unset($this->mapUnitIdToDb[$unit->unitId]);
      } else {
        if(empty($unit->db_id)) {
          classSupernova::$debug->error('Error writing unit to DB');
        }
        // If unit is new then putting unit object to container
        if(empty($this->_container[$unit->db_id])) {
          $this->_container[$unit->db_id] = $unit;
        }
      }
    }
  }


  public function _reset() {
    if(!empty($this->mapUnitIdToDb)) {
      foreach($this->mapUnitIdToDb as $unit_id => $object) {
        unset($this->mapUnitIdToDb[$unit_id]);
      }
    }

    if(!empty($this->_container)) {
      foreach($this->_container as $unit_db_id => $object) {
        unset($this->_container[$unit_db_id]);
      }
    }
  }

  public function _dump() {
    global $lang;

    print(__FILE__ . ':' . __LINE__ . "<br />");
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

    foreach($this->mapUnitIdToDb as $unit) {
      print('<tr>');

      print('<td>');
      print($unit->db_id);
      print('</td>');

      print('<td>');
      print("[{$unit->type}] {$lang['tech'][$unit->type]}");
      print('</td>');

      print('<td>');
      print("[{$unit->unitId}] {$lang['tech'][$unit->unitId]}");
      print('</td>');

      print('<td>');
      print($unit->count);
      print('</td>');

      print('<td>');
      print($unit->playerOwnerId);
      print('</td>');

      print('<td>');
//      print($unit->location);
      print('</td>');

      print('<td>');
      print($unit->locationType);
      print('</td>');

      print('<td>');
      print($unit->locationDbId);
      print('</td>');

      print('<td>');
      print($unit->timeStart);
      print('</td>');

      print('<td>');
      print($unit->timeFinish);
      print('</td>');

      print('</tr>');
    }
    print('</table>');
  }

}
