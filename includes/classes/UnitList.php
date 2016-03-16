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
//  /**
//   * @var Player|Fleet
//   */
//  public $location = null;

  public $ownerId = 0;
  public $locationId = 0;
  public $locationType = LOC_NONE;

  /**
   * @var Unit[] $mapUnitIdToDb
   */
  // Нужно для корректного сохранения новых юнитов. Их db_id = 0, поэтому при добавлении в контейнер они будут перезаписывать друг друга
  // Соответственно - при сохраненнии флота надо проходить dbSave именно по $mapUnitIdToDb
  public $mapUnitIdToDb = array(); // TEMPORARY - MOVE TO UnitList


  /**
   * @return Unit
   *
   * @version 41a6.0
   */
  public function _createElement() {
    return new Unit();
  }

  public function adjustUnitCount($unit_id, $unit_count = 0) {
    if(empty($this->mapUnitIdToDb[$unit_id])) {
      // Нет такого юнита - добавляем с 0 ДБ_ИД
      $this->insertNewUnit($unit_id, $unit_count);
    } else {
      if($this->mapUnitIdToDb[$unit_id]->count + $unit_count < 0) {
        classSupernova::$debug->error('Can not adjust unit with unit_count value lesser then unit exists');
      }

      $this->mapUnitIdToDb[$unit_id]->count += $unit_count;
    }
  }

  public function setUnitCount($unit_id, $unit_count = 0) {
    if(empty($this->mapUnitIdToDb[$unit_id])) {
      // Нет такого юнита - добавляем с 0 ДБ_ИД
      $this->insertNewUnit($unit_id, $unit_count);
    } else {
      $this->mapUnitIdToDb[$unit_id]->count = $unit_count;
    }
  }

  protected function insertNewUnit($unit_id, $unit_count = 0) {
    if($unit_count < 0) {
      classSupernova::$debug->error('Can not insert unit with negative unit_count');
    }
    $unit = $this->_createElement();
    $unit->unitId = $unit_id;
    $unit->count = $unit_count;
    $this->mapUnitIdToDb[$unit_id] = $unit;
  }

  /**
   * @param Player|Fleet $location
   */
  public function loadByLocation($location) {
    $this->_reset();
//    $this->location = $location;
    $this->locationType = $location::$locationType;
    $this->locationId = $location->db_id;

    $unit_array = classSupernova::db_get_unit_list_by_location(0, $this->locationType, $this->locationId);
    if(!is_array($unit_array)) {
      return;
    }

    foreach($unit_array as $unit_db_row) {
      $unit = $this->_createElement();
      $unit->dbRowParse($unit_db_row);

      if(!empty($this[$unit->db_id])) {
        classSupernova::$debug->error('Unit is already exists in _container');
      }

      $this[$unit->db_id] = $unit;
      $this->mapUnitIdToDb[$unit->unitId] = $unit;
    }

    // TODO - Применить бонусы от location
  }

  public function getUnitListArray() {
    $result = array();
    foreach($this->_container as $unit) {
      $result[$unit->unitId] = $unit->count;
    }

    return $result;
  }

  public function getUnitCount($unit_id) {
    $result = 0;
    foreach($this->_container as $unit) {
      if($unit->unitId == $unit_id) {
        $result += $unit->count;
      }
    }

    return $result;
  }


  public function dbSave($ownerId, $locationType, $locationId) {
    $this->ownerId = $ownerId;
    $this->locationType = $locationType;
    $this->locationId = $locationId;

    foreach($this->mapUnitIdToDb as $unit) {
      $unit->ownerId = $ownerId;
      $unit->locationType = $locationType;
      $unit->locationId = $locationId;
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

}
