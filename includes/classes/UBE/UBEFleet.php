<?php

/**
 * Class UBEFleet
 */
class UBEFleet {
  public $fleet_id = 0;
  public $owner_id = 0; // REPLACE WITH LINK TO OWNER!
  public $is_attacker = UBE_PLAYER_IS_DEFENDER;

  /**
   * @var UBEFleetUnitList
   */
  public $unit_list = null;

  public $group_id = 0;

  public $UBE_PLANET = array();

  public $UBE_BONUSES = array(); // [UBE_ATTACK]

  public $UBE_CAPTAIN = array();

  public $fleet_capacity = 0;

  /**
   * @var array
   */
  public $resources_lost_in_metal = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );

  public $resources_looted = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );

  public $cargo_dropped = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );

  public $resources_lost = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );

  public $resources = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );


  /**
   * UBEFleet constructor.
   */
  // OK5
  public function __construct() {
    $this->unit_list = new UBEFleetUnitList();
    $this->resources_lost_in_metal = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );
  }

  public function __clone() {
    $this->unit_list = clone $this->unit_list;
  }

  /**
   * @param UBEPlayer $player
   */
  // OK3
  public function copy_stats_from_player(UBEPlayer $player) {
    $this->is_attacker = $player->player_side_get();
  }

  /**
   * @param array $player_bonuses
   */
  // OK5
  public function bonuses_add_float(array $player_bonuses) {
    // Вычисляем бонус игрока и добавляем его к бонусам флота
    $this->UBE_BONUSES[UBE_ATTACK] += $player_bonuses[UBE_ATTACK];
    $this->UBE_BONUSES[UBE_SHIELD] += $player_bonuses[UBE_SHIELD];
    $this->UBE_BONUSES [UBE_ARMOR] += $player_bonuses [UBE_ARMOR];

  }

  /**
   *
   */
  // OK5
  public function calculate_battle_stats() {
    $this->unit_list->fill_unit_info($this->UBE_BONUSES);
  }


  public function load_from_report($fleet_row, UBE $ube) {
    $this->fleet_id = $fleet_row['ube_report_fleet_fleet_id'];
    $this->owner_id = $fleet_row['ube_report_fleet_player_id'];
    $this->is_attacker = $ube->players[$fleet_row['ube_report_fleet_player_id']]->player_side_get() == UBE_PLAYER_IS_ATTACKER ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;

    $this->UBE_PLANET = array(
      PLANET_ID     => $fleet_row['ube_report_fleet_planet_id'],
      PLANET_NAME   => $fleet_row['ube_report_fleet_planet_name'],
      PLANET_GALAXY => $fleet_row['ube_report_fleet_planet_galaxy'],
      PLANET_SYSTEM => $fleet_row['ube_report_fleet_planet_system'],
      PLANET_PLANET => $fleet_row['ube_report_fleet_planet_planet'],
      PLANET_TYPE   => $fleet_row['ube_report_fleet_planet_planet_type'],
    );

    $this->UBE_BONUSES = array(
      UBE_ATTACK => $fleet_row['ube_report_fleet_bonus_attack'],
      UBE_SHIELD => $fleet_row['ube_report_fleet_bonus_shield'],
      UBE_ARMOR  => $fleet_row['ube_report_fleet_bonus_armor'],
    );

    $this->resources = array(
      RES_METAL     => $fleet_row['ube_report_fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['ube_report_fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['ube_report_fleet_resource_deuterium'],
    );
  }

  public function sql_generate_array($ube_report_id) {
    return array(
      $ube_report_id,
      $this->owner_id,
      $this->fleet_id,

      (float)$this->UBE_PLANET[PLANET_ID],
      "'" . db_escape($this->UBE_PLANET[PLANET_NAME]) . "'",
      (int)$this->UBE_PLANET[PLANET_GALAXY],
      (int)$this->UBE_PLANET[PLANET_SYSTEM],
      (int)$this->UBE_PLANET[PLANET_PLANET],
      (int)$this->UBE_PLANET[PLANET_TYPE],

      (float)$this->resources[RES_METAL],
      (float)$this->resources[RES_CRYSTAL],
      (float)$this->resources[RES_DEUTERIUM],

      (float)$this->UBE_BONUSES[UBE_ATTACK],
      (float)$this->UBE_BONUSES[UBE_SHIELD],
      (float)$this->UBE_BONUSES[UBE_ARMOR],
    );
  }

  public function read_from_row($fleet_row) {
    $this->fleet_id = $fleet_row['fleet_id'];
    $this->owner_id = $fleet_row['fleet_owner'];
    $this->group_id = $fleet_row['fleet_group'];

    $fleet_unit_list = Fleet::static_proxy_string_to_array($fleet_row);
    foreach($fleet_unit_list as $unit_id => $unit_count) {
      if(!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $this->unit_list->insert_unit($unit_id, $unit_count);
      }
    }

    $this->resources = array(
      RES_METAL     => $fleet_row['fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['fleet_resource_deuterium'],
    );

    $this->UBE_PLANET = array(
//    PLANET_ID => $fleet['fleet_start_id'],
//    PLANET_NAME => $fleet['fleet_start_name'],
      PLANET_GALAXY => $fleet_row['fleet_start_galaxy'],
      PLANET_SYSTEM => $fleet_row['fleet_start_system'],
      PLANET_PLANET => $fleet_row['fleet_start_planet'],
      PLANET_TYPE   => $fleet_row['fleet_start_type'],
    );
  }


  public function load_outcome_from_report_row(array $row) {
    $this->cargo_dropped = array(
      RES_METAL     => $row['ube_report_outcome_fleet_resource_dropped_metal'],
      RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_dropped_crystal'],
      RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_dropped_deuterium'],
    );
    $this->resources_looted = array(
      RES_METAL     => $row['ube_report_outcome_fleet_resource_loot_metal'],
      RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_loot_crystal'],
      RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_loot_deuterium'],
    );
    $this->resources_lost = array(
      RES_METAL     => $row['ube_report_outcome_fleet_resource_lost_metal'],
      RES_CRYSTAL   => $row['ube_report_outcome_fleet_resource_lost_crystal'],
      RES_DEUTERIUM => $row['ube_report_outcome_fleet_resource_lost_deuterium'],
    );
    $this->resources_lost_in_metal = array(
      RES_METAL => $row['ube_report_outcome_fleet_resource_lost_in_metal'],
    );
  }

  /**
   * @param $row
   */
  public function load_unit_outcome_from_row($row) {
    $unit_id = $row['ube_report_outcome_unit_unit_id'];
    // fleet_attackers[$fleet_id] и fleet_defenders[$fleet_id] содержат ССЫЛКИ на outcome_fleets[$fleet_id] - поэтому можно сразу писать в outcome_fleets
    $this->unit_list[$unit_id]->units_lost = $row['ube_report_outcome_unit_lost'];
    $this->unit_list[$unit_id]->defence_restored = $row['ube_report_outcome_unit_restored'];
  }


  /**
   * @param $ube_report_id
   *
   * @return array
   */
  public function sql_generate_outcome_fleet_array($ube_report_id) {
    return array(
      $ube_report_id,
      $this->fleet_id,

      (float)$this->resources_lost[RES_METAL],
      (float)$this->resources_lost[RES_CRYSTAL],
      (float)$this->resources_lost[RES_DEUTERIUM],
      (float)$this->cargo_dropped[RES_METAL],
      (float)$this->cargo_dropped[RES_CRYSTAL],
      (float)$this->cargo_dropped[RES_DEUTERIUM],
      (float)$this->resources_looted[RES_METAL],
      (float)$this->resources_looted[RES_CRYSTAL],
      (float)$this->resources_looted[RES_DEUTERIUM],
      (float)$this->resources_lost_in_metal[RES_METAL],
    );
  }

  public function report_render_outcome_side_fleet() {
    $UBE_DEFENCE_RESTORE = array();
    $UBE_UNITS_LOST = array();
    foreach($this->unit_list->_container as $unit_id => $UBEFleetUnit) {
      if($UBEFleetUnit->defence_restored) {
        $UBE_DEFENCE_RESTORE[$unit_id] = $UBEFleetUnit->defence_restored;
      }
      if($UBEFleetUnit->units_lost) {
        $UBE_UNITS_LOST[$unit_id] = $UBEFleetUnit->units_lost;
      }
    }

    return array_merge(
      $this->report_render_outcome_side_fleet_line($UBE_DEFENCE_RESTORE, 'ube_report_info_restored'),
      $this->report_render_outcome_side_fleet_line($UBE_UNITS_LOST, 'ube_report_info_loss_final'),
      $this->report_render_outcome_side_fleet_line($this->resources_lost, 'ube_report_info_loss_resources'),
      $this->report_render_outcome_side_fleet_line($this->cargo_dropped, 'ube_report_info_loss_dropped'),
      $this->report_render_outcome_side_fleet_line($this->resources_looted, $this->is_attacker == UBE_PLAYER_IS_ATTACKER ? 'ube_report_info_loot_gained' : 'ube_report_info_loss_looted'),
      $this->report_render_outcome_side_fleet_line($this->resources_lost_in_metal, 'ube_report_info_loss_in_metal')
    );
  }

  // ------------------------------------------------------------------------------------------------
  // Рендерит таблицу общего результата боя
  /**
   * @param $array
   * @param $lang_header_index
   *
   * @return array
   */
  protected function report_render_outcome_side_fleet_line(&$array, $lang_header_index) {
    global $lang;

    $result = array();
    if(!empty($array)) {
      foreach($array as $unit_id => $unit_count) {
        if($unit_count) {
          $result[] = array(
            'NAME' => $lang['tech'][$unit_id],
            'LOSS' => pretty_number($unit_count),
          );
        }
      }
      if($lang_header_index && count($result)) {
        array_unshift($result, array('NAME' => $lang[$lang_header_index]));
      }
    }

    return $result;
  }


  public function sql_generate_outcome_unit_array(&$sql_perform_report_unit, $ube_report_id) {
    $fleet_id = $this->fleet_id;

    $unit_sort_order = 0;
    foreach($this->unit_list->_container as $unit_id => $UBEFleetUnit) {
      if($UBEFleetUnit->units_lost || $UBEFleetUnit->defence_restored) {
        $unit_sort_order++;
        $sql_perform_report_unit[] = array(
          $ube_report_id,
          $fleet_id,

          $unit_id,
          (float)$UBEFleetUnit->defence_restored,
          (float)$UBEFleetUnit->units_lost,

          $unit_sort_order,
        );
      }
    }
  }

  /**
   * Расчёт изменения ресурсов во флоте/на планете
   *
   * @return array
   */
  // OK0
  function ube_combat_result_calculate_resources() {
    $resource_delta_fleet = array();
    // Если во флоте остались юниты или это планета - генерируем изменение ресурсов
    foreach(sn_get_groups('resources_loot') as $resource_id) {
      $resource_change = (float)$this->resources_looted[$resource_id] + (float)$this->cargo_dropped[$resource_id];
      if($resource_change) {
        $resource_delta_fleet[$resource_id] = -($resource_change);
      }
    }

    return $resource_delta_fleet;
  }

}
