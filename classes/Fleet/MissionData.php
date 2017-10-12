<?php
/**
 * Created by Gorlum 11.10.2017 13:17
 */

namespace Fleet;


use Common\GlobalContainer;

class MissionData {
  /**
   * @var array|null
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
   * MissionData constructor.
   */
  public function __construct() {
    $this->general = \classSupernova::$gc->general;
  }

  /**
   * @param GlobalContainer $gc
   */
  public function changeGc($gc) {
    $this->general = $gc->general;
  }

  /**
   * @param $mission_data
   *
   * @return static
   */
  public static function buildFromArray($mission_data) {
    $that = new static();

    $that->fleet = is_array($mission_data['fleet']) && !empty($mission_data['fleet']) ? $mission_data['fleet'] : null;
    $that->dst_user = is_array($mission_data['dst_user']) && !empty($mission_data['dst_user']) ? $mission_data['dst_user'] : null;
    $that->dst_planet = is_array($mission_data['dst_planet']) && !empty($mission_data['dst_planet']) ? $mission_data['dst_planet'] : null;
    $that->src_user = is_array($mission_data['src_user']) && !empty($mission_data['src_user']) ? $mission_data['src_user'] : null;
    $that->src_planet = is_array($mission_data['src_planet']) && !empty($mission_data['src_planet']) ? $mission_data['src_planet'] : null;
    $that->fleet_event = is_array($mission_data['fleet_event']) && !empty($mission_data['fleet_event']) ? $mission_data['fleet_event'] : null;

    return $that;
  }

}
