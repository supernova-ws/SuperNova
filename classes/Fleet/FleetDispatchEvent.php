<?php /** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection, PhpCastIsUnnecessaryInspection, PhpDeprecationInspection */

/** Created by Gorlum 27.10.2024 18:42 */

namespace Fleet;

use mysqli_result;
use Planet\DBStaticPlanet;
use SN;

/**
 * @property array      $fleet          Fleet on which event time happens
 * @property string     $eventTimeStamp When event happened in timeline
 * @property string     $event          Event type
 *
 * @property ?int       $srcPlanetId
 * @property bool|array $srcPlanetRow
 * @property ?int       $srcPlanetOwnerId
 * @property ?int       $dstPlanetId
 * @property bool|array $dstPlanetRow
 * @property ?int       $dstPlanetOwnerId
 *
 * @property array      $missionInfoNew
 * @property int        $missionId
 */
class FleetDispatchEvent {
  const IS_ATTACK = 'isAttack';
  const IS_TRANSPORT = 'isTransport';

  const F_FLEET_ID = 'fleet_id';
  const F_FLEET_OWNER_ID = 'fleet_owner';
  const F_PLANET_ID = 'id';
  const F_PLANET_OWNER_ID = 'id_owner';
  const IS_LOCK_SOURCE = 'isLockSource';

  /** @var array[] $sn_groups_mission */
  public static $sn_groups_mission = [];
  /** @var int[] $userIdsToLock */
  public $userIdsToLock = [];
  /** @var int[] $planetIdsToLock */
  public $planetIdsToLock = [];
  /** @var int[] $fleetIdsToLock */
  public $fleetIdsToLock = [];

  public static $MISSIONS = [
    MT_ATTACK    => [self::IS_TRANSPORT => false, self::IS_ATTACK => true,],
    MT_AKS       => [self::IS_TRANSPORT => false, self::IS_ATTACK => true,],
    MT_DESTROY   => [self::IS_TRANSPORT => false, self::IS_ATTACK => true,],
    MT_HOLD      => [self::IS_TRANSPORT => false,],
    MT_SPY       => [self::IS_TRANSPORT => false, self::IS_LOCK_SOURCE => true, 'AJAX' => true,],
    MT_TRANSPORT => [self::IS_TRANSPORT => true, self::IS_LOCK_SOURCE => true,],
    MT_RELOCATE  => [self::IS_TRANSPORT => true, self::IS_LOCK_SOURCE => true,],
    MT_RECYCLE   => [self::IS_TRANSPORT => false, 'AJAX' => true,],
    MT_EXPLORE   => [self::IS_TRANSPORT => false,],
    MT_COLONIZE  => [self::IS_TRANSPORT => true,],
    MT_MISSILE   => [self::IS_TRANSPORT => false, 'AJAX' => true,],
  ];

  public function __construct($fleetRow, $eventType) {
    if (empty(self::$sn_groups_mission)) {
      self::$sn_groups_mission = sn_get_groups('missions');
    }

    $this->fleet = $fleetRow;
    $this->event = $eventType;

    $this->srcPlanetOwnerId = $fleetRow[self::F_FLEET_OWNER_ID];

    $this->missionId      = (int)$this->fleet[FleetDispatcher::F_FLEET_MISSION];
    $this->missionInfoNew = self::$MISSIONS[$this->missionId];

    $this->eventTimeStamp = (int)$this->fleet[$this->event === EVENT_FLT_ARRIVE ? 'fleet_start_time' :
      ($this->event === EVENT_FLT_ACCOMPLISH ? 'fleet_end_stay' : /* EVENT_FLT_RETURN */
        'fleet_end_time')];

    /** @var Fleet $fleetObject */
    $fleetObject = (object)$this->fleet;

    // Always locking flying fleet and fleet owner
    $this->userIdsToLock   = [(int)$fleetObject->fleet_owner ?: 0 => true,];
    $this->fleetIdsToLock  = [(int)$fleetObject->fleet_id => true,];
    $this->planetIdsToLock = [];

    // Some locks make sense only on specific event types
    if ($this->event === EVENT_FLT_RETURN) {
      // There is no means or ways how returning fleet can influence other planet(s) then starting one
      $isLockSource = true;
    } else {
      // Locking destination planet always
      // There are no means or sense to make a mission which does not affect destination planet
      $this->getDstPlanetRowFromFleet();
      // Locking dest planet
      $this->planetIdsToLock[$this->dstPlanetId] = true;
      // Locking destination planet always implies locking destination user
      // Locking planet owner ID
      $this->userIdsToLock[$this->dstPlanetOwnerId] = true;

      // If the fleet is a part of existing fleet group - locking also fleets in group and their respected owners
      // Only doing it for EVENT_FLT_ARRIVE - currently no other event types does trigger fleet group actions
      if (!empty($fleetObject->fleet_group) && $this->event === EVENT_FLT_ARRIVE) {
        $fleetGroupId = (int)$fleetObject->fleet_group;
        foreach (DbFleetStatic::db_fleet_list("`fleet_group` = {$fleetGroupId}") as $fleetInGroup) {
          $this->userIdsToLock[(int)$fleetInGroup[self::F_FLEET_OWNER_ID] ?: 0] = true;
          $this->fleetIdsToLock[(int)$fleetInGroup[self::F_FLEET_ID] ?: 0]      = true;
        }
      }

      // We may need to lock source planet/user for some extra info such as planet name/username, coordinates etc
      $isLockSource = !empty($this->missionInfoNew[self::IS_LOCK_SOURCE]);
    }

    if ($isLockSource) {
      $this->getSrcPlanetRowFromFleet();
      $this->planetIdsToLock[$this->srcPlanetId]    = true;
      $this->userIdsToLock[$this->srcPlanetOwnerId] = true;
    }

    if (!empty($this->missionInfoNew[self::IS_ATTACK])) {
      $fleetsOnHold = DbFleetStatic::fleet_list_on_hold(
        $fleetObject->fleet_end_galaxy,
        $fleetObject->fleet_end_system,
        $fleetObject->fleet_end_planet,
        $fleetObject->fleet_end_type,
        $this->eventTimeStamp
      );
      foreach ($fleetsOnHold as $aFleet) {
        $this->userIdsToLock[(int)$aFleet[self::F_FLEET_OWNER_ID] ?: 0] = true;
        $this->fleetIdsToLock[(int)$aFleet[self::F_FLEET_ID] ?: 0]      = true;
      }
    }

    unset($this->userIdsToLock[0]);
    unset($this->planetIdsToLock[0]);
    unset($this->fleetIdsToLock[0]);

    $this->userIdsToLock   = array_keys($this->userIdsToLock);
    $this->planetIdsToLock = array_keys($this->planetIdsToLock);
    $this->fleetIdsToLock  = array_keys($this->fleetIdsToLock);
  }

  /**
   * LOCK - Lock all records which can be used with mission
   *
   * @return array|bool|mysqli_result|null
   */
  public function lockEventRecords() {
    // $query = [];
    $locks = [];

    if (!empty($this->userIdsToLock)) {
      $locks['users'] = $this->userIdsToLock;
//      /** @noinspection SqlResolve */
//      $query[] = "SELECT 1 FROM `{{users}}` WHERE `id` IN (" . implode(',', $this->userIdsToLock) . ") FOR UPDATE";
    }
    if (!empty($this->planetIdsToLock)) {
      $locks['planets'] = $this->planetIdsToLock;
//      /** @noinspection SqlResolve */
//      $query[] = "SELECT 1 FROM `{{planets}}` WHERE `id` IN (" . implode(',', $this->planetIdsToLock) . ") FOR UPDATE";
    }
    if (!empty($this->fleetIdsToLock)) {
      $locks['fleets'] = $this->fleetIdsToLock;
//      /** @noinspection SqlResolve */
//      $query[] = "SELECT 1 FROM `{{fleets}}` WHERE `fleet_id` IN (" . implode(',', $this->fleetIdsToLock) . ") FOR UPDATE";
    }

    // Really - no checks here. We should lock at least flying fleet and fleet owner

//    return doquery(implode(' UNION ', $query));
    return SN::$gc->db->lockRecords($locks);
  }

  public static function sortEvents(&$eventList) {
    uasort($eventList, function (FleetDispatchEvent $a, FleetDispatchEvent $b) {
      return
        // Сравниваем время флотов - кто раньше, тот и первый обрабатывается
          $a->eventTimeStamp > $b->eventTimeStamp ? 1 : ($a->eventTimeStamp < $b->eventTimeStamp ? -1 :
          // Если время - одинаковое, сравниваем события флотов
          // Если события - одинаковые, то флоты равны
          ($a->event == $b->event ? 0 :
            // Если события разные - первыми считаем прибывающие флоты
            ($a->event == EVENT_FLT_ARRIVE ? 1 : ($b->event == EVENT_FLT_ARRIVE ? -1 :
              // Если нет прибывающих флотов - дальше считаем флоты, которые закончили миссию
              ($a->event == EVENT_FLT_ACCOMPLISH ? 1 : ($b->event == EVENT_FLT_ACCOMPLISH ? -1 :
                // Если нет флотов, закончивших задание - остались возвращающиеся флоты, которые равны между собой
                // TODO: Добавить еще проверку по ID флота и/или времени запуска - что бы обсчитывать их в порядке запуска
                (
                0 // Вообще сюда доходить не должно - будет отсекаться на равенстве событий
                )
              ))
            ))
          )
        );
    });
  }

  /**
   */
  public function refreshMissionData() {
    if (!empty($this->srcPlanetId) && !empty($this->srcPlanetOwnerId)) {
//       $this->srcPlanetRow = DBStaticPlanet::db_planet_by_vector($this->fleet, 'fleet_start_');
      $updateResult = sys_o_get_updated($this->srcPlanetOwnerId, $this->srcPlanetId, $this->eventTimeStamp);

      $this->updateSrcPlanetRow($updateResult['planet']);
    }

    if (!empty($this->dstPlanetId) && !empty($this->dstPlanetOwnerId)) {
      $updateResult = sys_o_get_updated($this->dstPlanetOwnerId, $this->dstPlanetId, $this->eventTimeStamp);

      $this->updateDstPlanetRow($updateResult['planet']);
    }
  }

  /**
   * @return array|false
   */
  public function refreshFleet() {
    $this->fleet = DbFleetStatic::db_fleet_get($this->fleet[self::F_FLEET_ID]);

    return $this->fleet;
  }

  public function getSrcPlanetRowFromFleet() {
    $this->srcPlanetRow = DBStaticPlanet::db_planet_by_vector($this->fleet, 'fleet_start_');

    $this->updateSrcPlanetRow($this->srcPlanetRow);

    return $this->srcPlanetRow;
  }

  /**
   * @param array $srcPlanetRow
   *
   * @return array|bool
   */
  public function updateSrcPlanetRow($srcPlanetRow) {
//    $this->srcPlanetRow = is_array($srcPlanetRow) ? $srcPlanetRow : DBStaticPlanet::db_planet_by_vector($this->fleet, 'fleet_start_');
    $this->srcPlanetRow = $srcPlanetRow;

    $this->srcPlanetId = !empty($this->srcPlanetRow[self::F_PLANET_ID]) ? (int)$this->srcPlanetRow[self::F_PLANET_ID] : 0;
    // Starting planet can change owner while fleet mission - and even change planet ID
    // It can happen due to teleport shenanigans or because of planet capturing (in certain game modes)
    $this->srcPlanetOwnerId = !empty($this->srcPlanetRow[self::F_PLANET_OWNER_ID]) ? (int)$this->srcPlanetRow[self::F_PLANET_OWNER_ID] : $this->srcPlanetOwnerId;

    return $this->srcPlanetRow;
  }


  public function getDstPlanetRowFromFleet() {
    $this->dstPlanetRow = DBStaticPlanet::db_planet_by_vector($this->fleet, 'fleet_end_');

    $this->updateDstPlanetRow($this->dstPlanetRow);

    return $this->dstPlanetRow;
  }

  /**
   * @param ?array $dstPlanetRow
   *
   * @return array|bool|null
   */
  public function updateDstPlanetRow($dstPlanetRow = null) {
    $this->dstPlanetRow = $dstPlanetRow;

    $this->dstPlanetId = !empty($this->dstPlanetRow[self::F_PLANET_ID]) ? (int)$this->dstPlanetRow[self::F_PLANET_ID] : 0;
    // Retrieving destination owner ID
    $this->dstPlanetOwnerId = !empty($this->dstPlanetRow[self::F_PLANET_OWNER_ID]) ? (int)$this->dstPlanetRow[self::F_PLANET_OWNER_ID] : 0;

    return $this->dstPlanetRow;
  }

}
