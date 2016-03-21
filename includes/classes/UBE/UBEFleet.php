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

  /**
   * @var Bonus $fleet_bonus
   */
  public $fleet_bonus = null;

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
   * [P_ATTACK/P_ARMOR/P_SHIELD]
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
    $this->fleet_bonus = new Bonus();
  }

  public function __clone() {
    $this->unit_list = clone $this->unit_list;
  }

  /**
   * @param UBEPlayerList $players
   *
   * @version 41a6.16
   */
  public function ube_load_from_players(UBEPlayerList $players) {
    $this->is_attacker = $players[$this->owner_id]->getSide();

    // Вычисляем бонус игрока и добавляем его к бонусам флота
    $this->fleet_bonus->mergeBonus($players[$this->owner_id]->player_bonus);
    // TODO
//      $objFleet->add_planet_bonuses();
//      $objFleet->add_fleet_bonuses();
//      $objFleet->add_ship_bonuses();

    $this->unit_list->addBonus($this->fleet_bonus);
  }


  /**
   * @param     $fleet_row
   * @param UBE $ube
   *
   * @version 41a6.16
   */
  public function load_from_report($fleet_row, UBE $ube) {
    $this->db_id = $fleet_row['ube_report_fleet_fleet_id'];
    $this->owner_id = $fleet_row['ube_report_fleet_player_id'];
    $this->is_attacker = $ube->players[$fleet_row['ube_report_fleet_player_id']]->getSide() == UBE_PLAYER_IS_ATTACKER ? UBE_PLAYER_IS_ATTACKER : UBE_PLAYER_IS_DEFENDER;

    $this->UBE_PLANET = array(
      PLANET_ID     => $fleet_row['ube_report_fleet_planet_id'],
      PLANET_NAME   => $fleet_row['ube_report_fleet_planet_name'],
      PLANET_GALAXY => $fleet_row['ube_report_fleet_planet_galaxy'],
      PLANET_SYSTEM => $fleet_row['ube_report_fleet_planet_system'],
      PLANET_PLANET => $fleet_row['ube_report_fleet_planet_planet'],
      PLANET_TYPE   => $fleet_row['ube_report_fleet_planet_planet_type'],
    );

    $this->fleet_bonus->setBonusList(array(
      P_ATTACK => array(
        UNIT_REPORT_FLEET => $fleet_row['ube_report_fleet_bonus_attack'],
      ),
      P_SHIELD => array(
        UNIT_REPORT_FLEET => $fleet_row['ube_report_fleet_bonus_shield'],
      ),
      P_ARMOR  => array(
        UNIT_REPORT_FLEET => $fleet_row['ube_report_fleet_bonus_armor'],
      ),
    ));

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
   * @version 41a6.16
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

      (float)$this->fleet_bonus->calcBonus(P_ATTACK),
      (float)$this->fleet_bonus->calcBonus(P_SHIELD),
      (float)$this->fleet_bonus->calcBonus(P_ARMOR),
    );
  }

  /**
   * @param Fleet $objFleet
   *
   * @version 41a6.16
   */
  public function read_from_fleet_object(Fleet $objFleet) {
    $this->db_id = $objFleet->dbId;
    $this->owner_id = $objFleet->playerOwnerId;
    $this->group_id = $objFleet->group_id;

    $fleet_unit_list = $objFleet->get_unit_list();
    foreach($fleet_unit_list as $unit_id => $unit_count) {
      if(!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $this->unit_list->unitAdjustCount($unit_id, $unit_count);
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

  public function read_from_planet_row(array &$planet_row, UBEPlayer $player) {
    $player_db_row = $player->getDbRow();

    $this->owner_id = $planet_row['id_owner'];

    $sn_group_combat = sn_get_groups('combat');
//    $planet_unit_list = db_unit_by_location($player_id, LOC_PLANET, $planet['id']);
//    foreach($planet_unit_list as $unit_db_row) {
//      if(in_array($unit_db_row['unit_snid'], $sn_group_combat)) {
//        $this->fleet_list[0]->unit_list->insert_unit($unit_db_row['unit_snid'], $unit_db_row['unit_level']);
//      }
//    }

    foreach($sn_group_combat as $unit_id) {
      if($unit_count = mrc_get_level($player_db_row, $planet_row, $unit_id)) {
        $this->unit_list->unitAdjustCount($unit_id, $unit_count);
      }
    }

    foreach(sn_get_groups('resources_loot') as $resource_id) {
      $this->resource_list[$resource_id] = floor(mrc_get_level($player_db_row, $planet_row, $resource_id));
    }

    $this->UBE_PLANET = array(
      PLANET_ID     => $planet_row['id'],
      PLANET_NAME   => $planet_row['name'],
      PLANET_GALAXY => $planet_row['galaxy'],
      PLANET_SYSTEM => $planet_row['system'],
      PLANET_PLANET => $planet_row['planet'],
      PLANET_TYPE   => $planet_row['planet_type'],
      PLANET_SIZE   => $planet_row['diameter'],
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
    foreach($this->unit_list->_container as $UBEUnit) {
      if($UBEUnit->units_restored) {
        $UBE_DEFENCE_RESTORE[$UBEUnit->unitId] = $UBEUnit->units_restored;
      }
      if($UBEUnit->units_lost) {
        $UBE_UNITS_LOST[$UBEUnit->unitId] = $UBEUnit->units_lost;
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
    foreach($this->unit_list->_container as $UBEUnit) {
      if($UBEUnit->units_lost || $UBEUnit->units_restored) {
        $unit_sort_order++;
        $sql_perform_report_unit[] = array(
          $ube_report_id,
          $fleet_id,

          $UBEUnit->unitId,
          (float)$UBEUnit->units_restored,
          (float)$UBEUnit->units_lost,

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
    foreach($this->unit_list->_container as $UBEUnit) {
      $this->fleet_capacity += $UBEUnit->capacity * $UBEUnit->getCount();

      if($UBEUnit->units_lost) {
        foreach($UBEUnit->price as $resource_id => $unit_resource_price) {
          if(!$unit_resource_price) {
            continue;
          }

          $resources_lost = $UBEUnit->units_lost * $unit_resource_price;
          $this->resources_lost_on_units[$resource_id] += $resources_lost;
          // Если это корабль - прибавляем потери к обломкам на орбите
          // TODO - опция выбрасывания обороны в обломки
          if($UBEUnit->getType() == UNIT_SHIPS) {
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
    $ship_count_initial = $this->unit_list->unitsCount();
    $ship_count_lost = $this->unit_list->unitCountLost();

    $objFleet2 = new Fleet();
    $objFleet2->setDbId($this->db_id);

    // Если это была миссия Уничтожения И звезда смерти взорвалась И мы работаем с аттакерами - значит все аттакеры умерли
    if($this->is_attacker == UBE_PLAYER_IS_ATTACKER && $reapers_status == UBE_MOON_REAPERS_DIED) {
      $objFleet2->db_delete_this_fleet();
    } elseif($ship_count_initial == 0) { // $ship_count_lost == $ship_count_initial ||
      $objFleet2->db_delete_this_fleet();
    } else {
      if($ship_count_lost) {
        $fleet_real_array = array();
        // Просматриваем результаты изменения флотов
        foreach($this->unit_list->_container as $UBEUnit) {
          // Перебираем аутком на случай восстановления юнитов
          if(($units_left = $UBEUnit->getCount() - (float)$UBEUnit->units_lost) > 0) {
            $fleet_real_array[$UBEUnit->unitId] = $units_left;
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
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function prepare_for_next_round($is_simulator) {
    $this->unit_list->prepare_for_next_round($is_simulator);

    $this->total_stats[P_ATTACK] = $this->unit_list->getSumProperty('pool_attack');
    $this->total_stats[P_SHIELD] = $this->unit_list->getSumProperty('pool_shield');
    $this->total_stats[P_ARMOR] = $this->unit_list->getSumProperty('pool_armor');
  }

  /**
   * @param UBEASA $side_ASA
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function calculate_unit_partial_data(UBEASA $side_ASA) {
    $this->fleet_share_of_side_armor = $this->total_stats[P_ARMOR] / $side_ASA->getArmor();

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
   * @version 41a6.16
   */
  public function attack_fleet(UBEFleet $defending_fleet, $is_simulator) {
    UBEDebug::unit_dump_header();

    foreach($this->unit_list->_container as $attacking_unit_pool) {
      UBEDebug::unit_dump($attacking_unit_pool, 'attacker');

      // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
      foreach($defending_fleet->unit_list->_container as $defending_unit_pool) {
        if($defending_unit_pool->isEmpty()) {
          continue;
        }

        // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
        $direct_attack = $attacking_unit_pool->pool_attack * $defending_unit_pool->share_of_side_armor;
        $attacker_amplify = !empty($attacking_unit_pool->amplify[$defending_unit_pool->unitId])
          ? $attacking_unit_pool->amplify[$defending_unit_pool->unitId]
          : 1;
        // Применяем амплифай, если есть
        $defending_unit_pool->attack_income = floor($direct_attack * $attacker_amplify);

        $before = UBEDebug::unit_dump_defender($attacking_unit_pool, $defending_unit_pool, $defending_fleet->db_id);

        $defending_unit_pool->receive_damage($is_simulator);

        UBEDebug::unit_dump($defending_unit_pool, 'after', $before);
      }
    }

    UBEDebug::unit_dump_footer();
  }

  /**
   * @return int
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function get_unit_count() {
    return $this->unit_list->unitsCount();
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
