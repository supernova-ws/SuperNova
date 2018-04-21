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

  /**
   * Fleet row from DB
   *
   * @var array|null $fleet
   */
  public $fleet;

  /**
   * @var array|null
   */
  public $dst_user;

  /**
   * @var array|null
   */
  public $dst_planet;

  /**
   * @var array|null
   */
  public $src_user;

  /**
   * @var array|null
   */
  public $src_planet;

  /**
   * @var array|null
   */
  public $fleet_event;

  /**
   * @var \General $general
   */
  protected $general;

  /**
   * @var \classLocale $lang
   */
  protected $lang;

  /**
   * MissionData constructor.
   *
   * @param array $missionArray
   */
  public function __construct($missionArray) {
    $this->general = $this->getDefaultGeneral();
    $this->lang = $this->getDefaultLang();

    $this->fromMissionArray($missionArray);
  }

  /**
   * @param GlobalContainer $gc
   */
  public function changeGc($gc) {
    $this->general = $gc->general;
  }

  /**
   * @param array $missionArray
   *
   * @return static
   */
  public static function buildFromArray($missionArray) {
    return new static($missionArray);
  }

  /**
   * @param array $missionArray
   */
  protected function fromMissionArray($missionArray) {
    $this->fleet = is_array($missionArray['fleet']) && !empty($missionArray['fleet']) ? $missionArray['fleet'] : null;
    $this->dst_user = is_array($missionArray['dst_user']) && !empty($missionArray['dst_user']) ? $missionArray['dst_user'] : null;
    $this->dst_planet = is_array($missionArray['dst_planet']) && !empty($missionArray['dst_planet']) ? $missionArray['dst_planet'] : null;
    $this->src_user = is_array($missionArray['src_user']) && !empty($missionArray['src_user']) ? $missionArray['src_user'] : null;
    $this->src_planet = is_array($missionArray['src_planet']) && !empty($missionArray['src_planet']) ? $missionArray['src_planet'] : null;
    $this->fleet_event = !empty($missionArray['fleet_event']) ? $missionArray['fleet_event'] : null;

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
