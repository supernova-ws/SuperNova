<?php

/**
 * Class UBEFleetList
 *
 * @method UBEFleet offsetGet($offset)
 * @property UBEFleet[] $_container
 */
class UBEFleetList extends ArrayAccessV2 {

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
    }

    $query = doquery("SELECT * FROM {{ube_report_outcome_unit}} WHERE `ube_report_id` = {$report_row['ube_report_id']} ORDER BY `ube_report_outcome_unit_sort_order`");
    while($row = db_fetch($query)) {
      $fleet_id = $row['ube_report_outcome_unit_fleet_id'];
      $this[$fleet_id]->load_unit_outcome_from_row($row);
    }
  }

  // REPORT RENDER *****************************************************************************************************
  /**
   * Генерирует данные для отчета из разобранных данных боя
   *
   * @param UBE $ube
   *
   * @return array
   */
  public function report_render_fleets_outcome(UBE $ube) {
    $result = array(
      UBE_PLAYER_IS_ATTACKER => array(),
      UBE_PLAYER_IS_DEFENDER => array(),
    );

    foreach($this->_container as $fleet_id => $UBEFleet) {
      $result[$UBEFleet->is_attacker][] = array(
        'ID'          => $fleet_id,
        'NAME'        => $ube->players[$UBEFleet->owner_id]->player_name_get(),
        'IS_ATTACKER' => $UBEFleet->is_attacker == UBE_PLAYER_IS_ATTACKER,
        '.'           => array(
          'param' => $UBEFleet->report_render_outcome_side_fleet(),
        ),
      );
    }

    return array_merge($result[UBE_PLAYER_IS_ATTACKER], $result[UBE_PLAYER_IS_DEFENDER]);
  }


  public function sn_ube_combat_analyze(UBERound $lastRound, $is_simulator, UBEDebris $debris, array $resource_exchange_rates) {
    // Генерируем результат боя
    foreach($this->_container as $fleet_id => $UBEFleet) {
      // Инициализируем массив результатов для флота
//      $this->init_fleet_outcome_and_link_to_side($UBEFleet);

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
          $UBEFleetUnit->defence_restored = $units_giveback;
        }

        // TODO: Сбор металла/кристалла от обороны

        $units_lost = $UBEFleetUnit->count - $units_left;

        // Вычисляем емкость трюмов оставшихся кораблей
        $UBEFleet->fleet_capacity += $UBEFleetUnit->capacity * $units_left;

        // Вычисляем потери в ресурсах
        if($units_lost) {
          $UBEFleetUnit->units_lost = $units_lost;

          foreach($UBEFleetUnit->price as $resource_id => $unit_resource_price) {
            if(!$unit_resource_price) {
              continue;
            }

            // ...чистыми
            $resources_lost = $units_lost * $unit_resource_price;
            $UBEFleet->resources_lost[$resource_id] += $resources_lost;

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
            $UBEFleet->resources_lost_in_metal[RES_METAL] += $resources_lost_in_metal;
          }
        }
      }

      // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
      if($fleet_id == 0) {
        continue;
      }
      // Количество ресурсов флота
      $fleet_total_resources = array_sum($UBEFleet->resources);
      // Если на борту нет ресурсов - зачем нам все это?
      if($fleet_total_resources == 0) {
        continue;
      }

      // Емкость трюмов флота
      $fleet_capacity = $UBEFleet->fleet_capacity;

      // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
      if($UBEFleet->fleet_capacity < $fleet_total_resources) {
        $left_percent = $UBEFleet->fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
        foreach($UBEFleet->resources as $resource_id => &$resource_amount) {
          // Не просчитываем ресурсы, которых нет на борту кораблей флота
          if(!$resource_amount) {
            continue;
          }

          $UBEFleet->cargo_dropped[$resource_id] = $resource_amount - ceil($left_percent * $resource_amount);
          $resource_amount -= $UBEFleet->cargo_dropped[$resource_id];

          $debris->debris_add_resource(
            $resource_id,
            floor($UBEFleet->cargo_dropped[$resource_id] *
              ($is_simulator
                ? UBE_CARGO_DROPPED_TO_DEBRIS_AVG
                : mt_rand(UBE_CARGO_DROPPED_TO_DEBRIS_MIN, UBE_CARGO_DROPPED_TO_DEBRIS_MAX)
              ) / 100
            )
          ); // TODO: Configurize

          $UBEFleet->resources_lost_in_metal[RES_METAL] += $UBEFleet->cargo_dropped[$resource_id] * $resource_exchange_rates[$resource_id];
        }
        $fleet_total_resources = array_sum($UBEFleet->resources);
      }

      $UBEFleet->fleet_capacity -= $fleet_total_resources;
    }
  }

  public function get_groups() {
    $result = array();
    foreach($this->_container as $UBEFleet) {
      if($UBEFleet->group_id) {
        $result[$UBEFleet->group_id] = $UBEFleet->group_id;
      }
    }

    return $result;
  }

  public function get_capacity_attackers() {
    $result = 0;
    foreach($this->_container as $UBEFleet) {
      if($UBEFleet->is_attacker) {
        $result += $UBEFleet->fleet_capacity;
      }
    }

    return $result;
  }

}
