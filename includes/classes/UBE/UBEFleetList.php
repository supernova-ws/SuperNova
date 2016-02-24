<?php

/**
 * Class UBEFleetList
 *
 * @method UBEFleet offsetGet($offset)
 * @property UBEFleet[] $_container
 */
class UBEFleetList extends ArrayAccessV2 {

  // TODO - сделать флитлистами??
  public $fleet_attackers_outcomes = array();
  public $fleet_defenders_outcomes = array();

  // TODO - как-нибудь свернуть
  public $capacity_attackers = array();
  public $capacity_defenders = array();

  public function load_from_players(UBEPlayerList $players) {
    foreach($this->_container as $fleet_id => $objFleet) {
      // TODO - эта последовательность должна быть при загрузке флота (?)

      $objFleet->copy_stats_from_player($players[$objFleet->UBE_OWNER]);

      // Вычисляем бонус игрока и добавляем его к бонусам флота
      $objFleet->bonuses_add_float($players[$objFleet->UBE_OWNER]->player_bonus_get_all());
      // TODO
//      $objFleet->add_planet_bonuses();
//      $objFleet->add_fleet_bonuses();
//      $objFleet->add_ship_bonuses();

      $objFleet->calculate_battle_stats();
    }
  }

  public function db_load_fleets_outcome($report_row, UBE $ube) {
    $query = doquery("SELECT * FROM {{ube_report_outcome_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_fleet_fleet_id'];
      $this[$fleet_id]->load_outcome_from_report_row($row);
      $this->link_fleet_to_side($this[$fleet_id]);
    }

    $query = doquery("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_unit_fleet_id'];
      $this[$fleet_id]->load_unit_outcome_from_row($row);
    }
  }

  /**
   * @param UBEFleet $fleet_info
   */
  // MOVE TO UBEFleet
  public function init_fleet(UBEFleet $fleet_info, UBE $ube) {
    $fleet_info->outcome = array(UBE_UNITS_LOST => array());
    $ube->fleet_list->link_fleet_to_side($fleet_info);
  }

  public function link_fleet_to_side(UBEFleet $fleet_info) {
    if($fleet_info->is_attacker) {
      $this->fleet_attackers_outcomes[$fleet_info->fleet_id] = &$fleet_info->outcome;
    } else {
      $this->fleet_defenders_outcomes[$fleet_info->fleet_id] = &$fleet_info->outcome;
    }
  }


  // REPORT RENDER *****************************************************************************************************
  /**
   * @param UBE $ube
   * @param     $template_result
   */
  public function report_render_fleets_outcome(UBE $ube, &$template_result) {
    $this->report_render_outcome_side($this->fleet_attackers_outcomes, $ube, $template_result);
    $this->report_render_outcome_side($this->fleet_defenders_outcomes, $ube, $template_result);
  }

  // ------------------------------------------------------------------------------------------------
  // Генерирует данные для отчета из разобранных данных боя
  /**
   * @param     $side_fleet
   * @param UBE $ube
   * @param     $template_result
   */
  public function report_render_outcome_side($side_fleet, UBE $ube, &$template_result) {
    if(empty($side_fleet) || !is_array($side_fleet)) {
      return;
    }

    foreach($side_fleet as $fleet_id => $temp) {
      $fleet_owner_id = $this[$fleet_id]->UBE_OWNER;

      $template_result['.']['loss'][] = array(
        'ID'          => $fleet_id,
        'NAME'        => $ube->players[$fleet_owner_id]->player_name_get(),
        'IS_ATTACKER' => $ube->players[$fleet_owner_id]->player_side_get() == UBE_PLAYER_IS_ATTACKER,
        '.'           => array(
          'param' => $this[$fleet_id]->report_render_outcome_side_fleet(),
        ),
      );
    }
  }

}
