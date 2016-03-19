<?php

/**
 * Class UnitList
 * Indexed by DB_ID - as it should be!
 *
 *
 * @method setLocation(UnitContainer $location)
 * @see setLocatedAt::setLocation
 *
 * Hints for IDE - inherited from ArrayAccessV2
 *
 * @method Unit offsetGet($offset)
 * @property Unit[] $_container
 *
 */
class UnitList extends ArrayAccessV2 implements IDbRow, ILocatedAt {
  // TODO - UnitList должен передавать всегда LOCAteDAT - а не себя другим юнитам!

  // ILocatedAt from ILocation implementation ************************************************************************

  public function getPlayerOwnerId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getPlayerOwnerId() : null;
  }

  public function getLocationType() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationType() : 0;
  }

  public function getLocationDbId() {
    return is_object($this->locatedAt) ? $this->locatedAt->getLocationDbId() : null;
  }



  // ILocatedAt implementation ***************************************************************************************

  /**
   * @var ILocation $locatedAt
   */
  protected $locatedAt = null;


  public function setLocatedAt($location) {
    $this->locatedAt = $location;
    foreach($this->_container as $unit) {
      $unit->setLocatedAt($location);
    }
  }

  public function getLocatedAt() {
    return $this->locatedAt;
  }




  // IDbRow implementation *********************************************************************************************

  /**
   * @var Unit[] $mapUnitIdToDb
   */
  // Нужно для корректного сохранения новых юнитов. Их db_id = 0, поэтому при добавлении в контейнер они будут перезаписывать друг друга
  // Соответственно - при сохраненнии флота надо проходить dbSave именно по $mapUnitIdToDb
  protected $mapUnitIdToDb = array();

  /**
   * Loading object from DB by primary ID
   * Real location should be set before calling this method
   *
   * @param int $dbId - dbId is generally unused here. However it works as flag: 0 - just reset; (negative) - just reset; (positive) - proceed with loading
   */
  // TODO: Implement dbLoad() method.
  public function dbLoad($dbId) {
    $this->_reset();

    if($dbId <= 0) {
      return;
    }

    if(!is_object($this->locatedAt)) {
      classSupernova::$debug->error('UnitList::dbLoad have no locatedAt field set');
    }

    $unit_array = classSupernova::db_get_unit_list_by_location(0, $this->getLocationType(), $this->getLocationDbId());
    if(!is_array($unit_array)) {
      return;
    }

    foreach($unit_array as $unit_db_row) {
      $unit = $this->_createElement();
      $unit->setLocatedAt($this->locatedAt);
      $unit->dbRowParse($unit_db_row);

      // TODO - сюда вставить разборку бонусов данного юнитлиста - тех бонусов, которые Grants данный юнит добавить в список бонусов юнит-листа

      if(!empty($this[$unit->getDbId()])) {
        classSupernova::$debug->error('Unit is already exists in _container');
      }

      $this[$unit->getDbId()] = $unit;
      $this->mapUnitIdToDb[$unit->unitId] = $unit;
    }

    // TODO - Применить бонусы от location
    // Точнее - опустить бонусы с юнитлиста (те, которые Grants) на каждый юнит (те, которые receives)
    // Вообще-то Receives это будут параметры каждого юнита

    classSupernova::$debug->error('UnitList::dbLoad should be never called directly!');
  }

//  /**
//   * @param UnitContainer $location
//   */
//  public function loadByLocation($location) {
//    $this->_reset();
////    $this->location = $location;
//    $this->locatedAtType = $location::$locationType;
//    $this->locatedAtDbId = $location->getLocationDbId();
//
//
//    $unit_array = classSupernova::db_get_unit_list_by_location(0, $this->locatedAtType, $this->locatedAtDbId);
//    if(!is_array($unit_array)) {
//      return;
//    }
//
//    foreach($unit_array as $unit_db_row) {
//      $unit = $this->_createElement();
//      $unit->setLocation($location);
//      $unit->dbRowParse($unit_db_row);
//
//      // TODO - сюда вставить разборку бонусов данного юнитлиста - тех бонусов, которые Grants данный юнит добавить в список бонусов юнит-листа
//
//      if(!empty($this[$unit->dbId])) {
//        classSupernova::$debug->error('Unit is already exists in _container');
//      }
//
//      $this[$unit->dbId] = $unit;
//      $this->mapUnitIdToDb[$unit->unitId] = $unit;
//    }
//
//    // TODO - Применить бонусы от location
//    // Точнее - опустить бонусы с юнитлиста (те, которые Grants) на каждый юнит (те, которые receives)
//    // Вообще-то Receives это будут параметры каждого юнита
//  }
//

  public function dbSave() {
    if(!is_object($this->locatedAt)) {
      classSupernova::$debug->error('UnitList::dbSave have no locatedAt field set');
    }

    foreach($this->mapUnitIdToDb as $unit) {
      $unit->setLocatedAt($this->locatedAt);
      $unit_db_id = $unit->getDbId();
      $unit->dbSave();

      if($unit->getCount() == 0) {
        // Removing unit object
        unset($this[$unit_db_id]);
        unset($this->mapUnitIdToDb[$unit->unitId]);
      } else {
        if($unit->getDbId() <= 0) {
          classSupernova::$debug->error('Error writing unit to DB');
        }
        // If unit is new then putting unit object to container
        if(empty($this->_container[$unit->getDbId()])) {
          $this->_container[$unit->getDbId()] = $unit;
        }
      }
    }
  }






  // Other *************************************************************************************************************

  /**
   * @return Unit
   *
   * @version 41a6.10
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
      $this->mapUnitIdToDb[$unit_id]->setLocatedAt($this->locatedAt);
    }

    if($replace_value) {
      $this->mapUnitIdToDb[$unit_id]->setCount($unit_count);
    } else {
      $this->mapUnitIdToDb[$unit_id]->adjustCount($unit_count);
    }
  }


  public function getUnitListArray() {
    $result = array();
    foreach($this->mapUnitIdToDb as $unit) {
      $result[$unit->unitId] = $unit->getCount();
    }

    return $result;
  }

  public function getUnitCount($unit_id) {
    $result = 0;
    foreach($this->mapUnitIdToDb as $unit) {
      if($unit->unitId == $unit_id) {
        $result += $unit->getCount();
      }
    }

    return $result;
  }


  // TODO - revise it later
  public function _reset() {
//    if(!empty($this->mapUnitIdToDb)) {
//      foreach($this->mapUnitIdToDb as $unit_id => $object) {
//        unset($this->mapUnitIdToDb[$unit_id]);
//      }
//    }
    unset($this->mapUnitIdToDb);
    $this->mapUnitIdToDb = array();

//    if(!empty($this->_container)) {
//      foreach($this->_container as $unit_db_id => $object) {
//        unset($this->_container[$unit_db_id]);
//      }
//    }
    unset($this->_container);
    $this->_container = array();
  }


  public function _dump() {
    global $lang;

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

    foreach($this->mapUnitIdToDb as $unit) {
      print('<tr>');

      print('<td>');
      print($unit->getDbId());
      print('</td>');

      print('<td>');
      $type = $unit->getType();
      print("[{$type}] {$lang['tech'][$type]}");
      print('</td>');

      print('<td>');
      print("[{$unit->unitId}] {$lang['tech'][$unit->unitId]}");
      print('</td>');

      print('<td>');
      print($unit->getCount());
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
