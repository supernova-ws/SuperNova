<?php
/**
 * Created by Gorlum 11.10.2017 13:17
 */

namespace Fleet;


use Core\GlobalContainer;

class MissionData {

  /**
   * @var Fleet $fleetEntity
   */
  protected $fleetEntity;

  /** @var array|null $fleet Fleet row from DB */
  public $fleet;
  /** @var array|null */
  public $dstUserRow;
  /** @var array|null */
  public $dstPlanetRow;
  /** @var array|null */
  public $fleetOwnerRow;
  /** @var array|null */
  public $srcPlanetRow;
  /** @var array|null */
  public $fleet_event;

  /** @var \General $general */
  protected $general;
  /** @var \classLocale $lang */
  protected $lang;

  /**
   * @param FleetDispatchEvent $fleetEvent
   *
   * @return static
   */
  public static function buildFromArray($fleetEvent) {
    return new static($fleetEvent);
  }

  /**
   * MissionData constructor.
   *
   * @param ?FleetDispatchEvent $fleetEvent
   */
  public function __construct($fleetEvent) {
    $this->general = $this->getDefaultGeneral();
    $this->lang    = $this->getDefaultLang();

    $this->fromMissionArray($fleetEvent);
  }

  /**
   * @param GlobalContainer $gc
   */
  public function changeGc($gc) {
    $this->general = $gc->general;
  }

  /**
   * @param ?FleetDispatchEvent $fleetEvent
   */
  protected function fromMissionArray($fleetEvent) {
    $this->fleet         = $fleetEvent->fleet;
    $this->dstUserRow    = $fleetEvent->dstPlanetOwnerId ? db_user_by_id($fleetEvent->dstPlanetOwnerId) : null;
    $this->dstPlanetRow  = $fleetEvent->dstPlanetId ? $fleetEvent->dstPlanetRow : null;
    $this->fleetOwnerRow = $fleetEvent->fleetOwnerId ? db_user_by_id($fleetEvent->fleet['fleet_owner']) : null;
    $this->srcPlanetRow  = $fleetEvent->srcPlanetId ? $fleetEvent->srcPlanetRow : null;
    $this->fleet_event   = $fleetEvent->event;

    $this->fleetEntity = new Fleet();
    $this->fleetEntity->dbLoadRecord($this->fleet['fleet_id']);
  }

  protected function dbFleetFindRecordById($fleetId) {
    return RecordFleet::findById($fleetId);
  }

  /**
   * @return \classLocale
   */
  protected function getDefaultLang() {
    return \SN::$lang;
  }

  /**
   * @return \General
   */
  protected function getDefaultGeneral() {
    return \SN::$gc->general;
  }

}
