<?php

/**
 * Class UBEFleet
 */
class UBEFleet {
  public $db_id = 0;
  public $owner_id = 0; // REPLACE WITH LINK TO OWNER!
  public $group_id = 0;

  /**
   * @var UBEUnitList
   */
  public $unit_list = null;
  public $resource_list = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );

  public $is_attacker = UBE_PLAYER_IS_DEFENDER;

  public $UBE_PLANET = array();

  public $UBE_BONUSES = array(); // [UBE_ATTACK]

  public $captain_row = array();

  public $fleet_capacity = 0;

  /**
   * @var array
   */
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

  public $resources_lost_on_units = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );
  public $resources_lost_on_ships = array(
    RES_METAL     => 0,
    RES_CRYSTAL   => 0,
    RES_DEUTERIUM => 0,
  );
  public $resources_lost_in_metal = array(
    RES_METAL => 0,
  );


  /**
   * [UBE_ATTACK/UBE_ARMOR/UBE_SHIELD]
   *
   * @var array[]
   */
  public $total_stats = array();

  /**
   * Доля флота в общем вкладе в броню стороны (Аттакера/Дефендера)
   *
   * @var float
   */
  public $fleet_share_of_side_armor = 0.0;


  /**
   * UBEFleet constructor.
   */
  public function __construct() {
    $this->unit_list = new UBEUnitList();
    $this->resources_lost_in_metal = array(
      RES_METAL => 0,
    );
  }

  public function __clone() {
    $this->unit_list = clone $this->unit_list;
  }

  /**
   * @param UBEPlayer $player
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function copy_stats_from_player(UBEPlayer $player) {
    $this->is_attacker = $player->player_side_get();
  }

  /**
   * @param array $player_bonuses
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function bonuses_add_player(array $player_bonuses) {
    // Вычисляем бонус игрока и добавляем его к бонусам флота
    $this->UBE_BONUSES[UBE_ATTACK] += $player_bonuses[UBE_ATTACK];
    $this->UBE_BONUSES[UBE_SHIELD] += $player_bonuses[UBE_SHIELD];
    $this->UBE_BONUSES [UBE_ARMOR] += $player_bonuses [UBE_ARMOR];

  }

  /**
   *
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function calculate_battle_stats() {
    $this->unit_list->fill_unit_info($this->UBE_BONUSES);
  }


  /**
   * @param     $fleet_row
   * @param UBE $ube
   *
   * @version 41a5.8
   */
  public function load_from_report($fleet_row, UBE $ube) {
    $this->db_id = $fleet_row['ube_report_fleet_fleet_id'];
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

    $this->resource_list = array(
      RES_METAL     => $fleet_row['ube_report_fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['ube_report_fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['ube_report_fleet_resource_deuterium'],
    );
  }

  /**
   * @param $ube_report_id
   *
   * @return array
   *
   * @version 41a5.8
   */
  public function sql_generate_array($ube_report_id) {
    return array(
      $ube_report_id,
      $this->owner_id,
      $this->db_id,

      (float)$this->UBE_PLANET[PLANET_ID],
      "'" . db_escape($this->UBE_PLANET[PLANET_NAME]) . "'",
      (int)$this->UBE_PLANET[PLANET_GALAXY],
      (int)$this->UBE_PLANET[PLANET_SYSTEM],
      (int)$this->UBE_PLANET[PLANET_PLANET],
      (int)$this->UBE_PLANET[PLANET_TYPE],

      (float)$this->resource_list[RES_METAL],
      (float)$this->resource_list[RES_CRYSTAL],
      (float)$this->resource_list[RES_DEUTERIUM],

      (float)$this->UBE_BONUSES[UBE_ATTACK],
      (float)$this->UBE_BONUSES[UBE_SHIELD],
      (float)$this->UBE_BONUSES[UBE_ARMOR],
    );
  }

  /**
   * @param Fleet $objFleet
   *
   * @version 41a5.8
   */
  public function read_from_fleet_object(Fleet $objFleet) {
    $this->db_id = $objFleet->db_id;
    $this->owner_id = $objFleet->owner_id;
    $this->group_id = $objFleet->group_id;

    $fleet_unit_list = $objFleet->get_unit_list();
    foreach($fleet_unit_list as $unit_id => $unit_count) {
      if(!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $this->unit_list->insert_unit($unit_id, $unit_count);
      }
    }

    $resources = $objFleet->get_resource_list();
    $this->resource_list = array(
      RES_METAL     => $resources[RES_METAL],
      RES_CRYSTAL   => $resources[RES_CRYSTAL],
      RES_DEUTERIUM => $resources[RES_DEUTERIUM],
    );

    $launch_coordinates = $objFleet->launch_coordinates_typed();
    $this->UBE_PLANET = array(
//    PLANET_ID => $fleet['fleet_start_id'],
//    PLANET_NAME => $fleet['fleet_start_name'],
      PLANET_GALAXY => $launch_coordinates['galaxy'],
      PLANET_SYSTEM => $launch_coordinates['system'],
      PLANET_PLANET => $launch_coordinates['planet'],
      PLANET_TYPE   => $launch_coordinates['type'],
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
    $this->resources_lost_on_units = array(
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
    $this->unit_list[$unit_id]->units_restored = $row['ube_report_outcome_unit_restored'];
  }


  /**
   * @param $ube_report_id
   *
   * @return array
   */
  public function sql_generate_outcome_fleet_array($ube_report_id) {
    return array(
      $ube_report_id,
      $this->db_id,

      (float)$this->resources_lost_on_units[RES_METAL],
      (float)$this->resources_lost_on_units[RES_CRYSTAL],
      (float)$this->resources_lost_on_units[RES_DEUTERIUM],
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
      if($UBEFleetUnit->units_restored) {
        $UBE_DEFENCE_RESTORE[$unit_id] = $UBEFleetUnit->units_restored;
      }
      if($UBEFleetUnit->units_lost) {
        $UBE_UNITS_LOST[$unit_id] = $UBEFleetUnit->units_lost;
      }
    }

    return array_merge(
      $this->report_render_outcome_side_fleet_line($UBE_DEFENCE_RESTORE, 'ube_report_info_restored'),
      $this->report_render_outcome_side_fleet_line($UBE_UNITS_LOST, 'ube_report_info_loss_final'),
      $this->report_render_outcome_side_fleet_line($this->resources_lost_on_units, 'ube_report_info_loss_resources'),
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
    $fleet_id = $this->db_id;

    $unit_sort_order = 0;
    foreach($this->unit_list->_container as $unit_id => $UBEFleetUnit) {
      if($UBEFleetUnit->units_lost || $UBEFleetUnit->units_restored) {
        $unit_sort_order++;
        $sql_perform_report_unit[] = array(
          $ube_report_id,
          $fleet_id,

          $unit_id,
          (float)$UBEFleetUnit->units_restored,
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

  /**
   * // Вычисляем ёмкость трюмов оставшихся кораблей
   * // Вычисляем потери в ресурсах
   *
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function calc_fleet_stats() {
    $this->resources_lost_on_units = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );
    $this->resources_lost_on_ships = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );
    $this->cargo_dropped = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );

    $this->fleet_capacity = 0;
    foreach($this->unit_list->_container as $unit_id => $UBEFleetUnit) {
      $this->fleet_capacity += $UBEFleetUnit->capacity * $UBEFleetUnit->count;

      if($UBEFleetUnit->units_lost) {
        foreach($UBEFleetUnit->price as $resource_id => $unit_resource_price) {
          if(!$unit_resource_price) {
            continue;
          }

          $resources_lost = $UBEFleetUnit->units_lost * $unit_resource_price;
          $this->resources_lost_on_units[$resource_id] += $resources_lost;
          // Если это корабль - прибавляем потери к обломкам на орбите
          // TODO - опция выбрасывания обороны в обломки
          if($UBEFleetUnit->type == UNIT_SHIPS) {
            $this->resources_lost_on_ships[$resource_id] += $resources_lost;
          }
        }
      }
    }

    // Количество ресурсов флота
    $fleet_total_resources = array_sum($this->resource_list);

    // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
    // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
    if($this->db_id != 0 && $this->fleet_capacity < $fleet_total_resources) {
      $drop_share = 1 - $this->fleet_capacity / $fleet_total_resources; // Какая часть ресурсов выброшена
      foreach($this->resource_list as $resource_id => &$resource_amount) {
        // Не просчитываем ресурсы, которых нет на борту кораблей флота
        if(!$resource_amount) {
          continue;
        }

        $this->cargo_dropped[$resource_id] = ceil($resource_amount * $drop_share);
        $resource_amount -= $this->cargo_dropped[$resource_id];
      }
      $fleet_total_resources = array_sum($this->resource_list);
    }

    $this->fleet_capacity -= $fleet_total_resources;
  }

  public function db_save_combat_result_fleet($is_small_fleet_recce, $reapers_status) {
    $ship_count_initial = $this->unit_list->get_units_count();
    $ship_count_lost = $this->unit_list->get_units_lost();

    $objFleet2 = new Fleet();
    $objFleet2->set_db_id($this->db_id);

    // Если это была миссия Уничтожения И звезда смерти взорвалась И мы работаем с аттакерами - значит все аттакеры умерли
    if($this->is_attacker == UBE_PLAYER_IS_ATTACKER && $reapers_status == UBE_MOON_REAPERS_DIED) {
      $objFleet2->method_db_delete_this_fleet();
    } elseif($ship_count_initial == 0) { // $ship_count_lost == $ship_count_initial ||
      $objFleet2->method_db_delete_this_fleet();
    } else {
      if($ship_count_lost) {
        $fleet_real_array = array();
        // Просматриваем результаты изменения флотов
        foreach($this->unit_list->_container as $unit_id => $UBEFleetUnit) {
          // Перебираем аутком на случай восстановления юнитов
          if(($units_left = $UBEFleetUnit->count - (float)$UBEFleetUnit->units_lost) > 0) {
            $fleet_real_array[$unit_id] = $units_left;
          };
        }
        $objFleet2->replace_ships($fleet_real_array);
      }

      $resource_delta_fleet = $this->ube_combat_result_calculate_resources();
      $objFleet2->update_resources($resource_delta_fleet);

      // Если защитник и не РМФ - отправляем флот назад
      if($this->is_attacker == UBE_PLAYER_IS_ATTACKER || ($this->is_attacker == UBE_PLAYER_IS_DEFENDER && !$is_small_fleet_recce)) {
        $objFleet2->mark_fleet_as_returned();
      }
      $objFleet2->flush_changes_to_db();
    }

    unset($objFleet2);
  }

  /**
   * @param string $stat_name UBE_ATTACK/UBE_SHIELD/UBE_ARMOR...etc
   *
   * @return int
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function get_fleet_total_stat($stat_name) {
    $result = 0;

    foreach($this->unit_list->_container as $unit_id => $UBERoundCombatUnit) {
      switch($stat_name) {
        case UBE_ATTACK:
          $result += $UBERoundCombatUnit->pool_attack;
        break;

        case UBE_SHIELD:
          $result += $UBERoundCombatUnit->pool_shield;
        break;

        case UBE_ARMOR:
          $result += $UBERoundCombatUnit->pool_armor;
        break;
      }
    }

    return $result;
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function prepare_for_next_round($is_simulator) {
    $this->unit_list->prepare_for_next_round($is_simulator);

    $this->total_stats[UBE_ATTACK] = $this->get_fleet_total_stat(UBE_ATTACK);
    $this->total_stats[UBE_SHIELD] = $this->get_fleet_total_stat(UBE_SHIELD);
    $this->total_stats[UBE_ARMOR] = $this->get_fleet_total_stat(UBE_ARMOR);
  }

  /**
   * @param UBEASA $side_ASA
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function calculate_unit_partial_data(UBEASA $side_ASA) {
    $this->fleet_share_of_side_armor = $this->total_stats[UBE_ARMOR] / $side_ASA->getArmor();

    foreach($this->unit_list->_container as $UBEUnit) {
      $UBEUnit->share_of_side_armor = $UBEUnit->pool_armor / $side_ASA->getArmor();
    }
  }

  /**
   * @param UBEFleetList $fleet_list
   * @param              $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function attack_fleets(UBEFleetList $fleet_list, $is_simulator) {
    foreach($fleet_list->_container as $defending_fleet) {
      // Не атакуются флоты на своей стороне
      if($this->is_attacker == $defending_fleet->is_attacker) {
        continue;
      }
      $this->attack_fleet($defending_fleet, $is_simulator);
    }
  }

  /**
   * @param UBEFleet $defending_fleet
   * @param          $is_simulator
   *
   * @version 41a5.8
   */
  public function attack_fleet(UBEFleet $defending_fleet, $is_simulator) {
    if(defined('DEBUG_UBE')) {
      UBEUnit::unit_dump_header();
    }
    foreach($this->unit_list->_container as $attack_unit_id => $attacking_unit_pool) {
      if(defined('DEBUG_UBE')) {
        $attacking_unit_pool->unit_dump('attacker');
      }
      // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
      foreach($defending_fleet->unit_list->_container as $defend_unit_id => $defending_unit_pool) {
        if($defending_unit_pool->count <= 0) {
          continue;
        }

        // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
        $direct_attack = $attacking_unit_pool->pool_attack * $defending_unit_pool->share_of_side_armor;
        $attacker_amplify = !empty($attacking_unit_pool->amplify[$defend_unit_id])
          ? $attacking_unit_pool->amplify[$defend_unit_id]
          : 1;
        // Применяем амплифай, если есть
        $defending_unit_pool->attack_income = floor($direct_attack * $attacker_amplify);
        if(defined('DEBUG_UBE')) {
          global $lang;

          print("[{$attacking_unit_pool->unit_id}]{$lang['tech'][$attacking_unit_pool->unit_id]}" .
            ' attacks ' .
            $defending_fleet->db_id . '@' . "[{$defending_unit_pool->unit_id}]{$lang['tech'][$defending_unit_pool->unit_id]}" .
            ' with ' . pretty_number($defending_unit_pool->attack_income) .
            '<br>'
          );
          $before = clone $defending_unit_pool;
          $defending_unit_pool->unit_dump('before');
        }

        $defending_unit_pool->receive_damage($is_simulator);
        if(defined('DEBUG_UBE')) {
          $defending_unit_pool->unit_dump('after', $before);
        }
      }
    }
    if(defined('DEBUG_UBE')) {
      UBEUnit::unit_dump_footer();
    }
  }

  /**
   * @return int
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function get_unit_count() {
    $result = 0;
    foreach($this->unit_list->_container as $unit_id => $UBERoundCombatUnit) {
      $result += $UBERoundCombatUnit->count;
    }

    return $result;
  }

  /**
   * @return int|number
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function get_resources_amount() {
    return empty($this->resource_list) || !is_array($this->resource_list) ? 0 : array_sum($this->resource_list);
  }

}
