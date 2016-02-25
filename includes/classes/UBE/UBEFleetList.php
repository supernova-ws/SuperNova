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

      $objFleet->copy_stats_from_player($players[$objFleet->owner_id]);

      // Вычисляем бонус игрока и добавляем его к бонусам флота
      $objFleet->bonuses_add_float($players[$objFleet->owner_id]->player_bonus_get_all());
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
  public function init_fleet_outcome_and_link_to_side(UBEFleet $fleet_info) {
    $fleet_info->outcome = array(UBE_UNITS_LOST => array());
    $this->link_fleet_to_side($fleet_info);
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
      $fleet_owner_id = $this[$fleet_id]->owner_id;

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


  public function sn_ube_combat_analyze(UBERound $lastRound, $is_simulator, UBEDebris $debris, array $resource_exchange_rates) {
    // Генерируем результат боя
    foreach($this->_container as $fleet_id => $UBEFleet) {
      // Инициализируем массив результатов для флота
      $this->init_fleet_outcome_and_link_to_side($UBEFleet);

      foreach($UBEFleet->unit_list->_container as $unit_id => $UBEFleetUnit) {
        // Вычисляем сколько юнитов осталось и сколько потеряно
        $units_left = $lastRound->fleet_combat_data[$fleet_id]->unit_list[$unit_id]->count;

        // Восстановление обороны - 75% от уничтоженной
        if($UBEFleetUnit->type == UNIT_DEFENCE) {
          $units_lost = $UBEFleetUnit->count - $units_left;
          if($is_simulator) {
            $units_giveback = round($units_lost * UBE_DEFENCE_RESTORATION_CHANCE_AVG / 100); // for simulation just return 75% of loss
          } else {
            // Checking - should we trigger mass-restore
            if($UBEFleetUnit->count >= UBE_DEFENCE_RESTORATION_MASS_COUNT) {
              // For large amount - mass-restoring defence
              $units_giveback = round($units_lost * mt_rand(UBE_DEFENCE_RESTORATION_CHANCE_MIN, UBE_DEFENCE_RESTORATION_CHANCE_MAX) / 100);
            } else {
              // For small amount - restoring defence per single unit
              $units_giveback = 0;
              for($i = 1; $i <= $units_lost; $i++) {
                if(mt_rand(1, 100) <= UBE_DEFENCE_RESTORATION_CHANCE_AVG) {
                  $units_giveback++;
                }
              }
            }
          }
          $units_left += $units_giveback;
          $UBEFleet->outcome[UBE_DEFENCE_RESTORE][$unit_id] = $units_giveback;
        }

        // TODO: Сбор металла/кристалла от обороны

        $units_lost = $UBEFleetUnit->count - $units_left;

        // Вычисляем емкость трюмов оставшихся кораблей
        if(UBE_PLAYER_IS_ATTACKER == $UBEFleet->is_attacker) {
          $this->capacity_attackers[$fleet_id] += $UBEFleetUnit->capacity * $units_left;
        } else {
          $this->capacity_defenders[$fleet_id] += $UBEFleetUnit->capacity * $units_left;
        }

        // Вычисляем потери в ресурсах
        if($units_lost) {
          $UBEFleet->outcome[UBE_UNITS_LOST][$unit_id] = $units_lost;

          foreach($UBEFleetUnit->price as $resource_id => $unit_resource_price) {
            if(!$unit_resource_price) {
              continue;
            }

            // ...чистыми
            $resources_lost = $units_lost * $unit_resource_price;
            $UBEFleet->outcome[UBE_RESOURCES_LOST][$resource_id] += $resources_lost;

            // Если это корабль - прибавляем потери к обломкам на орбите
            if($UBEFleetUnit->type == UNIT_SHIPS) {
              $debris->debris_add_resource(
                $resource_id,
                floor($resources_lost *
                  ($is_simulator
                    ? UBE_SHIP_WRECKS_TO_DEBRIS_AVG
                    : mt_rand(UBE_SHIP_WRECKS_TO_DEBRIS_MIN, UBE_SHIP_WRECKS_TO_DEBRIS_MAX)
                  )
                  / 100
                )
              );
            }

            // ...в металле
            $resources_lost_in_metal = $resources_lost * $resource_exchange_rates[$resource_id];
            $UBEFleet->outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resources_lost_in_metal;
          }
        }
      }

      // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
      if($fleet_id == 0) {
        continue;
      }
      // Количество ресурсов флота
      $fleet_total_resources = empty($UBEFleet->UBE_RESOURCES) ? 0 : array_sum($UBEFleet->UBE_RESOURCES);
      // Если на борту нет ресурсов - зачем нам все это?
      if($fleet_total_resources == 0) {
        continue;
      }

      // Емкость трюмов флота
      if(UBE_PLAYER_IS_ATTACKER == $UBEFleet->is_attacker) {
        $fleet_capacity = $this->capacity_attackers[$fleet_id];
      } else {
        $fleet_capacity = $this->capacity_defenders[$fleet_id];
      }

      // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
      if($fleet_capacity < $fleet_total_resources) {
        $left_percent = $fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
        foreach($UBEFleet->UBE_RESOURCES as $resource_id => $resource_amount) {
          // Не просчитываем ресурсы, которых нет на борту кораблей флота
          if(!$resource_amount) {
            continue;
          }

          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $UBEFleet->outcome[UBE_RESOURCES][$resource_id] = round($left_percent * $resource_amount);
          $resource_dropped = $resource_amount - $UBEFleet->outcome[UBE_RESOURCES][$resource_id];
          $UBEFleet->outcome[UBE_CARGO_DROPPED][$resource_id] = $resource_dropped;

          $debris->debris_add_resource(
            $resource_id,
            floor($resource_dropped *
              ($is_simulator
                ? UBE_CARGO_DROPPED_TO_DEBRIS_AVG
                : mt_rand(UBE_CARGO_DROPPED_TO_DEBRIS_MIN, UBE_CARGO_DROPPED_TO_DEBRIS_MAX)
              ) / 100
            )
          ); // TODO: Configurize

          $UBEFleet->outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resource_dropped * $resource_exchange_rates[$resource_id];
        }
        $fleet_total_resources = array_sum($UBEFleet->outcome[UBE_RESOURCES]);
      }

      if(UBE_PLAYER_IS_ATTACKER == $UBEFleet->is_attacker) {
        $this->capacity_attackers[$fleet_id] = $fleet_capacity - $fleet_total_resources;
      } else {
        $this->capacity_defenders[$fleet_id] = $fleet_capacity - $fleet_total_resources;
      }
    }
  }

}
