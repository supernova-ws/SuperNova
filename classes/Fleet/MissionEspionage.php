<?php
/**
 * Created by Gorlum 11.10.2017 13:16
 */

namespace Fleet;

use \SN;
use Planet\DBStaticPlanet;

class MissionEspionage extends MissionData {

  /**
   * @var MissionEspionageReport $missionReport
   */
  public $missionReport;

  private $target_message = '';

  public function flt_mission_spy() {
    $lang = SN::$lang;
    $fleet_array = sys_unit_str2arr($this->fleet['fleet_array']);

    if (isset($this->dst_user['id']) && isset($this->dst_planet['id']) && isset($this->src_user['id']) && $fleet_array[SHIP_SPY] >= 1) {
      // TODO: Наемники, губернаторы, артефакты и прочее имперское
      $this->doSpying();

      msg_send_simple_message($this->src_user['id'], '', $this->fleet['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_qg'], $lang['sys_mess_spy_report'],
        json_encode($this->missionReport, JSON_UNESCAPED_UNICODE), STRING_NEED_ESCAPING, false, STRING_IS_JSON_ENCODED);

      $this->target_message = "{$lang['sys_mess_spy_enemy_fleet']} {$this->src_planet['name']} " . uni_render_coordinates_href($this->src_planet, '', 3);
      $this->target_message .= " {$lang['sys_mess_spy_seen_at']} {$this->dst_planet['name']} " . uni_render_coordinates($this->dst_planet);
      if($this->missionReport->isSpyDetected()) {
        $this->target_message .= "<br />{$lang['sys_mess_spy_destroyed_enemy']}";
      }

      msg_send_simple_message($this->fleet['fleet_target_owner'], '', $this->fleet['fleet_start_time'], MSG_TYPE_SPY, $lang['sys_mess_spy_control'], $lang['sys_mess_spy_activity'], $this->target_message);
    }

    $this->dbApplyChanges();
  }

  protected function scanGroup($group_name) {
    foreach ($this->general->getGroupsByName($group_name) as $unit_id) {
      $this->missionReport->addUnit($unit_id, mrc_get_level($this->dst_user, $this->dst_planet, $unit_id, false, true));
    }
  }

  protected function doSpying() {
    $this->missionReport = new MissionEspionageReport($this);

    $spy_diff_empire = $this->missionReport->getEmpireSpyDiff();
    $planetSpyDiff = $this->missionReport->getPlanetSpyDiff();

    if ($planetSpyDiff >= 2) {
      $this->scanGroup('fleet');
    }
    if ($planetSpyDiff >= 3) {
      $this->scanGroup('defense');
    }
    if ($planetSpyDiff >= 5) {
      $this->scanGroup('structures');
    }

    if ($spy_diff_empire >= 0) {
      $this->scanGroup('tech');
    }

    // Launching detection calculations
    $this->missionReport->isSpyDetected();
  }

  protected function dbApplyChanges() {
    if (is_object($this->missionReport) && $this->missionReport->isSpyDetected()) {
      DbFleetStatic::db_fleet_delete($this->fleet['fleet_id']);

      $debris_planet_id = $this->dst_planet['planet_type'] == PT_PLANET ? $this->dst_planet['id'] : $this->dst_planet['parent_planet'];

      $spy_cost = get_unit_param(SHIP_SPY, P_COST);

      DBStaticPlanet::db_planet_set_by_id($debris_planet_id,
        "`debris_metal` = `debris_metal` + " . floor($this->missionReport->getProbesNumber() * $spy_cost[RES_METAL] * 0.3) . ", `debris_crystal` = `debris_crystal` + " . floor($this->missionReport->getProbesNumber() * $spy_cost[RES_CRYSTAL] * 0.3));
    } else {
      DbFleetStatic::fleet_send_back($this->fleet);
    }
  }

}
