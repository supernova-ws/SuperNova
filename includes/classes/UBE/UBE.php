<?php

spl_autoload_register(function ($class) {
  require_once $class . '.php';
});

class UBE {
  /**
   * Кодовая строка для доступа к отчёту
   *
   * @var string
   */
  public $report_cypher = '';
  /**
   * Время, когда произошел бой - НЕ ВРЕМЯ, КОГДА ОН ОБСЧИТАН!
   *
   * @var int
   */
  public $combat_timestamp = 0;
  /**
   * Время, потраченное на обсчёт
   *
   * @var int
   */
  public $time_spent = 0;
  public $options_method = 0;
  /**
   * Является ли этот экземпляр боя загруженным из БД
   *
   * @var bool
   */
  public $is_ube_loaded = false;
  public $is_admin_in_combat = false;
  public $is_defender_active_player = true;
  public $is_simulator = false;

  public $mission_type_id = MT_NONE;
  public $combat_result = UBE_COMBAT_RESULT_DRAW;
  /**
   * Флаг РМФ
   *
   * @var int
   */
  public $is_small_fleet_recce = 0;
  public $capture_result = UBE_CAPTURE_DISABLED;
  /**
   * [$resource_id] => $rate
   *
   * @var array
   */
  public $resource_exchange_rates = array();



  /**
   * @var UBEPlayerList
   */
  public $players = null;

  /**
   * @var UBEFleetList
   */
  public $fleet_list = null;

  /**
   * @var UBERoundList
   */
  public $rounds = null;

  /**
   * @var UBEOutcome
   */
  public $outcome = null;

  /**
   * @var UBEMoonCalculator
   */
  public $moon_calculator = null;

  /**
   * @var UBEDebris
   */
  public $debris = null;

  /**
   * @var array
   */
  public $ube_planet_info = array(
//    PLANET_ID     => $report_row['ube_report_planet_id'],
//    PLANET_NAME   => $report_row['ube_report_planet_name'],
//    PLANET_SIZE   => $report_row['ube_report_planet_size'],
//    PLANET_GALAXY => $report_row['ube_report_planet_galaxy'],
//    PLANET_SYSTEM => $report_row['ube_report_planet_system'],
//    PLANET_PLANET => $report_row['ube_report_planet_planet'],
//    PLANET_TYPE   => $report_row['ube_report_planet_planet_type'],
  );


  // [UBE_CAPTURE_RESULT]

  public function __construct() {
    $this->players = new UBEPlayerList();
    $this->fleet_list = new UBEFleetList();
    $this->outcome = new UBEOutcome();
    $this->moon_calculator = new UBEMoonCalculator();
    $this->debris = new UBEDebris();
    $this->rounds = new UBERoundList();
  }

  /**
   * Заполняет начальные данные по данным миссии
   *
   * @param Mission $objMission
   */
  // OK0
  function ube_attack_prepare(&$objMission) {
    $objFleet = $objMission->fleet;
    $destination_planet = &$objMission->dst_planet;

    // Готовим опции
    $this->combat_timestamp = $objFleet->time_arrive_to_target;
    $this->resource_exchange_rates = get_resource_exchange();
    $this->mission_type_id = $objFleet->mission_type;
    $this->set_option_from_config();

    $this->moon_calculator->load_status($destination_planet);

// TODO: Не допускать атаки игроком своих же флотов - т.е. холд против атаки
    // Готовим инфу по атакуемой планете
    $this->ube_attack_prepare_planet($destination_planet);

    // Готовим инфу по удержанию
    $target_coordinates = $objFleet->target_coordinates_typed();
    $fleet_list_on_hold = fleet_list_on_hold($target_coordinates['galaxy'], $target_coordinates['system'], $target_coordinates['planet'], $target_coordinates['type'], $this->combat_timestamp);
    foreach($fleet_list_on_hold as $fleet) {
      $this->ube_attack_prepare_fleet($fleet, false);
    }
    // TODO - НАДО ВЫНЕСТИ РАБОТУ С ПЛЕЕРАМИ ИЗ PREPARE FLEET! ПОтому что могут поменятся статы - АТТАКЕР/ДЕФЕНДЕР И, СООТВЕТСТВЕННО, БОНУСЫ!!!

    // Готовим инфу по атакующим
    if($objFleet->fleet_group) {
      $acs_fleet_list = fleet_list_by_group($objFleet->fleet_group);
      foreach($acs_fleet_list as $fleet) {
        $this->ube_attack_prepare_fleet($fleet, true);
      }
    } else {
      $this->ube_attack_prepare_fleet($objFleet->make_db_row(), true);
    }

  }

  /**
   * Заполняет данные по планете
   *
   * @param $combat_data
   * @param $planet
   */
  // OK0
  function ube_attack_prepare_planet(&$planet) {
    global $ube_combat_bonus_list;

    $player_id = $planet['id_owner'];

    $this->ube_attack_prepare_player($player_id, false);
    $player_db_row = $this->players[$player_id]->player_db_row_get();

    $this->fleet_list[0] = new UBEFleet();
    $this->fleet_list[0]->UBE_OWNER = $player_id;

    foreach(sn_get_groups('combat') as $unit_id) {
      if($unit_count = mrc_get_level($player_db_row, $planet, $unit_id)) {
        $this->fleet_list[0]->unit_list[$unit_id]->count = $unit_count;
      }
    }

    foreach(sn_get_groups('resources_loot') as $resource_id) {
      $this->fleet_list[0]->UBE_RESOURCES[$resource_id] = floor(mrc_get_level($player_db_row, $planet, $resource_id));
    }

    if($fortifier_level = mrc_get_level($player_db_row, $planet, MRC_FORTIFIER)) {
      $fortifier_bonus = $fortifier_level * get_unit_param(MRC_FORTIFIER, P_BONUS_VALUE) / 100;
      foreach($ube_combat_bonus_list as $ube_id) {
        $this->fleet_list[0]->UBE_BONUSES[$ube_id] += $fortifier_bonus;
      }
    }

    $this->fleet_list[0]->UBE_PLANET = array(
      PLANET_ID     => $planet['id'],
      PLANET_NAME   => $planet['name'],
      PLANET_GALAXY => $planet['galaxy'],
      PLANET_SYSTEM => $planet['system'],
      PLANET_PLANET => $planet['planet'],
      PLANET_TYPE   => $planet['planet_type'],
      PLANET_SIZE   => $planet['diameter'],
    );
    $this->ube_planet_info = $this->fleet_list[0]->UBE_PLANET;

    $this->is_defender_active_player = $player_db_row['onlinetime'] >= $this->combat_timestamp - UBE_DEFENDER_ACTIVE_TIMEOUT;
  }

  // ------------------------------------------------------------------------------------------------
  // Заполняет данные по игроку
  // OK0
  /**
   * @param int  $player_id
   * @param bool $is_attacker
   */
  function ube_attack_prepare_player($player_id, $is_attacker) {
    $this->players->db_load_player_by_id($player_id);

    $this->players[$player_id]->player_side_switch($is_attacker);
    $this->is_admin_in_combat = $this->is_admin_in_combat || $this->players[$player_id]->player_auth_level_get(); // Участвует ли админ в бою?
  }

  /**
   * @param array $fleet_row
   * @param bool  $is_attacker
   */
  // ------------------------------------------------------------------------------------------------
  // Заполняет данные по флоту
  // OK0
  function ube_attack_prepare_fleet(&$fleet_row, $is_attacker) {
    $UBEFleet = new UBEFleet();
    $UBEFleet->read_from_row($fleet_row);

    $this->fleet_list[$UBEFleet->fleet_id] = $UBEFleet;

    $this->ube_attack_prepare_player($UBEFleet->UBE_OWNER, $is_attacker);

    // TODO - Вызов основной функции!!!
    ube_attack_prepare_fleet_from_object($this, $fleet_row, $is_attacker);
  }


































  // ------------------------------------------------------------------------------------------------
  // Общий алгоритм расчета боя
  // OK0
  function sn_ube_combat() {
    // TODO: Сделать атаку по типам,  когда они будут

    $start = microtime(true);

    $this->fleet_list->load_from_players($this->players);

    // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
    $this->rounds->prepare_zero_round($this->fleet_list, $this->is_simulator);

    $this->rounds[1] = clone $this->rounds[0];
    $this->rounds[1]->round_number = 1;

    for($round = 1; $round <= 10; $round++) {
      // Проводим раунд
      $this->rounds[$round]->fleet_combat_data->calculate_attack_results($this); // OK3

      // Анализируем итоги текущего раунда и готовим данные для следующего
      $nextRound = $this->rounds[$round]->sn_ube_combat_round_analyze($round);

      if($this->rounds[$round]->UBE_OUTCOME != UBE_COMBAT_RESULT_DRAW) {
        break;
      }

      $this->rounds[$round + 1] = $nextRound;

      // Готовим данные для раунда
      $nextRound->fleet_combat_data->sn_ube_combat_round_prepare($this->fleet_list, $this->is_simulator);
    }
    $this->time_spent = microtime(true) - $start;

    // Делать это всегда - нам нужны результаты боя: луна->обломки->количество осташихся юнитов
    $this->sn_ube_combat_analyze();
  }























// ------------------------------------------------------------------------------------------------
// Анализирует результаты раунда и генерирует данные для следующего раунда
  // OK0
  function sn_ube_combat_round_analyze($round) {
    $objRound = $this->rounds[$round];
    $nextRound = $objRound->sn_ube_combat_round_analyze($round);

    if($objRound->UBE_OUTCOME == UBE_COMBAT_RESULT_DRAW) {
      $this->rounds[$round + 1] = $nextRound;
    }

    return $objRound->UBE_OUTCOME;
  }















  // ------------------------------------------------------------------------------------------------
  // Разбирает данные боя для генерации отчета
  // OK0
  function sn_ube_combat_analyze() {
    // Переменные для быстрого доступа к подмассивам
    $lastRound = $this->rounds->get_last_element();

    $this->debris->debris_reset();

    // Генерируем результат боя
    foreach($this->fleet_list->_container as $fleet_id => $fleet_info) {
      // Инициализируем массив результатов для флота
      $this->outcome->init_fleet($fleet_id, $this->fleet_list[$fleet_id]->is_attacker == UBE_PLAYER_IS_ATTACKER);

      // Переменные для быстрого доступа к подмассивам
      $fleet_outcome = &$this->outcome->outcome_fleets[$fleet_id];
      foreach($this->fleet_list[$fleet_id]->unit_list->_container as $unit_id => $UBEFleetUnit) {
        // Вычисляем сколько юнитов осталось и сколько потеряно
        $units_left = $lastRound->fleet_combat_data[$fleet_id]->unit_combat[$unit_id]->count;

        // Восстановление обороны - 75% от уничтоженной
        if($this->fleet_list[$fleet_id]->unit_list[$unit_id]->type == UNIT_DEFENCE) {
          $giveback_chance = 75; // TODO Configure
          $units_lost = $UBEFleetUnit->count - $units_left;
          if($this->is_simulator) { // for simulation just return 75% of loss
            $units_giveback = round($units_lost * $giveback_chance / 100);
          } else {
            if($UBEFleetUnit->count > 10) { // if there were more then 10 defense elements - mass-calculating giveback
              $units_giveback = round($units_lost * mt_rand($giveback_chance * 0.8, $giveback_chance * 1.2) / 100);
            } else { //if there were less then 10 defense elements - calculating giveback per element
              $units_giveback = 0;
              for($i = 1; $i <= $units_lost; $i++) {
                if(mt_rand(1, 100) <= $giveback_chance) {
                  $units_giveback++;
                }
              }
            }
          }
          $units_left += $units_giveback;
          $fleet_outcome[UBE_DEFENCE_RESTORE][$unit_id] = $units_giveback;
        }

        // TODO: Сбор металла/кристалла от обороны

        $units_lost = $UBEFleetUnit->count - $units_left;

        // Вычисляем емкость трюмов оставшихся кораблей
        if(UBE_PLAYER_IS_ATTACKER == $this->fleet_list[$fleet_id]->is_attacker) {
          $this->outcome->capacity_attackers[$fleet_id] += $this->fleet_list[$fleet_id]->unit_list[$unit_id]->capacity * $units_left;
        } else {
          $this->outcome->capacity_defenders[$fleet_id] += $this->fleet_list[$fleet_id]->unit_list[$unit_id]->capacity * $units_left;
        }

        // Вычисляем потери в ресурсах
        if($units_lost) {
          $fleet_outcome[UBE_UNITS_LOST][$unit_id] = $units_lost;

          foreach($this->fleet_list[$fleet_id]->unit_list[$unit_id]->price as $resource_id => $unit_resource_price) {
            if(!$unit_resource_price) {
              continue;
            }

            // ...чистыми
            $resources_lost = $units_lost * $unit_resource_price;
            $fleet_outcome[UBE_RESOURCES_LOST][$resource_id] += $resources_lost;

            // Если это корабль - прибавляем потери к обломкам на орбите
            if($this->fleet_list[$fleet_id]->unit_list[$unit_id]->type == UNIT_SHIPS) {
              $this->debris->debris_add_resource($resource_id, floor($resources_lost * ($this->is_simulator ? 30 : mt_rand(20, 40)) / 100)); // TODO: Configurize
            }

            // ...в металле
            $resources_lost_in_metal = $resources_lost * $this->resource_exchange_rates[$resource_id];
            $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resources_lost_in_metal;
          }
        }
      }

      // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
      if($fleet_id == 0) {
        continue;
      }

      // Количество ресурсов флота
      $fleet_total_resources = empty($this->fleet_list[$fleet_id]->UBE_RESOURCES) ? 0 : array_sum($this->fleet_list[$fleet_id]->UBE_RESOURCES);
      // Если на борту нет ресурсов - зачем нам все это?
      if($fleet_total_resources == 0) {
        continue;
      }

      // Емкость трюмов флота
      if(UBE_PLAYER_IS_ATTACKER == $this->fleet_list[$fleet_id]->is_attacker) {
        $fleet_capacity = $this->outcome->capacity_attackers[$fleet_id];
      } else {
        $fleet_capacity = $this->outcome->capacity_defenders[$fleet_id];
      }

      // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
      if($fleet_capacity < $fleet_total_resources) {
        $left_percent = $fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
        foreach($this->fleet_list[$fleet_id]->UBE_RESOURCES as $resource_id => $resource_amount) {
          // Не просчитываем ресурсы, которых нет на борту кораблей флота
          if(!$resource_amount) {
            continue;
          }

          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $fleet_outcome[UBE_RESOURCES][$resource_id] = round($left_percent * $resource_amount);
          $resource_dropped = $resource_amount - $fleet_outcome[UBE_RESOURCES][$resource_id];
          $fleet_outcome[UBE_CARGO_DROPPED][$resource_id] = $resource_dropped;

          $this->debris->debris_add_resource($resource_id, floor($resource_dropped * ($this->is_simulator ? 50 : mt_rand(30, 70)) / 100)); // TODO: Configurize

          $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resource_dropped * $this->resource_exchange_rates[$resource_id];
        }
        $fleet_total_resources = array_sum($fleet_outcome[UBE_RESOURCES]);
      }

      if(UBE_PLAYER_IS_ATTACKER == $this->fleet_list[$fleet_id]->is_attacker) {
        $this->outcome->capacity_attackers[$fleet_id] = $fleet_capacity - $fleet_total_resources;
      } else {
        $this->outcome->capacity_defenders[$fleet_id] = $fleet_capacity - $fleet_total_resources;
      }
    }

    $this->combat_result = !isset($lastRound->UBE_OUTCOME) || $lastRound->UBE_OUTCOME == UBE_COMBAT_RESULT_DRAW_END ? UBE_COMBAT_RESULT_DRAW : $lastRound->UBE_OUTCOME;
    // SFR - Small Fleet Reconnaissance ака РМФ
    $this->is_small_fleet_recce = $this->rounds->count() == 2 && $this->combat_result == UBE_COMBAT_RESULT_LOSS;

    if(!$this->is_ube_loaded) {
      $this->moon_calculator->calculate_moon($this);

      // Лутаем ресурсы - если аттакер выиграл
      if($this->combat_result == UBE_COMBAT_RESULT_WIN) {
        $this->sn_ube_combat_analyze_loot();
      }
    }

  }

  // ------------------------------------------------------------------------------------------------
  // OK0
  function sn_ube_combat_analyze_loot() {
    $planet_resource_list = $this->fleet_list[0]->UBE_RESOURCES;

    $planet_looted_in_metal = 0;
    $planet_resource_looted = array();
    $planet_resource_total = is_array($planet_resource_list) ? array_sum($planet_resource_list) : 0;
    if($planet_resource_total && ($total_capacity = array_sum($this->outcome->capacity_attackers))) {
      // Можно вывести только половину ресурсов, но не больше, чем общая вместимость флотов атакующих
      $planet_lootable = min($planet_resource_total / 2, $total_capacity);
      // Вычисляем процент вывоза. Каждого ресурса будет вывезено в одинаковых пропорциях
      $planet_lootable_percent = $planet_lootable / $planet_resource_total;

      // Вычисляем какой процент общей емкости трюмов атакующих будет задействован
      $total_lootable = min($planet_lootable, $total_capacity);

      // Вычисляем сколько ресурсов вывезено
      foreach($this->outcome->capacity_attackers as $fleet_id => $fleet_capacity) {
        $looted_in_metal = 0;
        $fleet_loot_data = array();
        foreach($planet_resource_list as $resource_id => $resource_amount) {
          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $fleet_lootable_percent = $fleet_capacity / $total_capacity;
          $looted = round($resource_amount * $planet_lootable_percent * $fleet_lootable_percent);
          $fleet_loot_data[$resource_id] = -$looted;
          $planet_resource_looted[$resource_id] += $looted;
          $looted_in_metal -= $looted * $this->resource_exchange_rates[$resource_id];
        }
        $this->outcome->outcome_fleets[$fleet_id][UBE_RESOURCES_LOOTED] = $fleet_loot_data;
        $this->outcome->outcome_fleets[$fleet_id][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $looted_in_metal;
        $planet_looted_in_metal += $looted_in_metal;
      }
    }

    $this->outcome->outcome_fleets[0][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] -= $planet_looted_in_metal;
    $this->outcome->outcome_fleets[0][UBE_RESOURCES_LOOTED] = $planet_resource_looted;
  }




































  // ------------------------------------------------------------------------------------------------
  /**
   * Записывает результат боя в БД
   *
   * @return mixed
   */
  // OK0
  function ube_combat_result_apply() {
    $destination_user_id = $this->fleet_list[0]->UBE_OWNER;

    // Обновляем поле обломков на планете
    if(!$this->is_admin_in_combat && $this->debris->debris_total() > 0) {
      db_planet_set_by_gspt($this->ube_planet_info[PLANET_GALAXY], $this->ube_planet_info[PLANET_SYSTEM], $this->ube_planet_info[PLANET_PLANET], PT_PLANET,
        "`debris_metal` = `debris_metal` + " . $this->debris->debris_get_resource(RES_METAL) . ", `debris_crystal` = `debris_crystal` + " . $this->debris->debris_get_resource(RES_CRYSTAL)
      );
    }

    $fleet_group_id_list = array(); // Для САБов

    foreach($this->fleet_list->_container as $fleet_id => $UBEFleet) {
      $UBEFleet->UBE_FLEET_GROUP ? $fleet_group_id_list[$UBEFleet->UBE_FLEET_GROUP] = $UBEFleet->UBE_FLEET_GROUP : false;

      $this_fleet_outcome = &$this->outcome->outcome_fleets[$fleet_id];

      empty($this_fleet_outcome[UBE_UNITS_LOST]) ? $this_fleet_outcome[UBE_UNITS_LOST] = array() : false;

      $ship_count_initial = $UBEFleet->unit_list->get_unit_count();
      $ship_count_lost = array_sum($this_fleet_outcome[UBE_UNITS_LOST]); // Если будет сделано восстановлении более, чем начальное число - тут надо сделать сумму по модулю

      if($fleet_id) {
        // Флот
        $objFleet2 = new Fleet();
        $objFleet2->set_db_id($fleet_id);

        // Если это была миссия Уничтожения И звезда смерти взорвалась И мы работаем с аттакерами - значит все аттакеры умерли
        if($UBEFleet->is_attacker == UBE_PLAYER_IS_ATTACKER && $this->moon_calculator->get_reapers_status() == UBE_MOON_REAPERS_DIED) {
          $ship_count_lost = $ship_count_initial;
        }

        if($ship_count_lost == $ship_count_initial) {
          $objFleet2->method_db_fleet_delete();
        } else {
          if($ship_count_lost) {
            $fleet_real_array = array();
            // Просматриваем результаты изменения флотов
            foreach($UBEFleet->unit_list->_container as $unit_id => $UBEFleetUnit) {
              // Перебираем аутком на случай восстановления юнитов
              if(($units_left = $UBEFleetUnit->count - (float)$this_fleet_outcome[UBE_UNITS_LOST][$unit_id]) > 0) {
                $fleet_real_array[$unit_id] = $units_left;
              };
            }
            $objFleet2->replace_ships($fleet_real_array);
          }

          $resource_delta_fleet = $this->ube_combat_result_calculate_resources($this_fleet_outcome);
          $objFleet2->update_resources($resource_delta_fleet);

          // Если защитник и не РМФ - отправляем флот назад
          if(($UBEFleet->is_attacker == UBE_PLAYER_IS_DEFENDER && !$this->is_small_fleet_recce) || $UBEFleet->is_attacker == UBE_PLAYER_IS_ATTACKER) {
            $objFleet2->mark_fleet_as_returned();
          }
          $objFleet2->flush_changes_to_db();
        }

        unset($objFleet2);
      } else {
        // Планета

        // Сохраняем изменения ресурсов - если они есть
        $resource_delta = $this->ube_combat_result_calculate_resources($this_fleet_outcome);
        if(!empty($resource_delta)) {
          $temp = array();
          foreach($resource_delta as $resource_id => $resource_amount) {
            $resource_db_name = pname_resource_name($resource_id);
            $temp[] = "`{$resource_db_name}` = `{$resource_db_name}` + ({$resource_amount})";
          }
          db_planet_set_by_id($this->ube_planet_info[PLANET_ID], implode(',', $temp));
        }

        if($ship_count_lost) {
          $db_changeset = array();
          $planet_row_cache = $this->players[$destination_user_id]->player_db_row_get();
          foreach($this_fleet_outcome[UBE_UNITS_LOST] as $unit_id => $units_lost) {
            $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, -$units_lost, $planet_row_cache, $this->ube_planet_info[PLANET_ID]);
          }
          db_changeset_apply($db_changeset);
        }
      }
    }

    // TODO: Связать сабы с флотами констраинтами ON DELETE SET NULL
    if(!empty($fleet_group_id_list)) {
      $fleet_group_id_list = implode(',', $fleet_group_id_list);
      doquery("DELETE FROM {{aks}} WHERE `id` IN ({$fleet_group_id_list})");
    }

    $this->moon_calculator->db_apply_result($this->ube_planet_info, $destination_user_id);

    $bashing_list = array();
    $players_sides = $this->players->get_player_sides();
    foreach($players_sides as $player_id => $player_side) {
      if($player_side != UBE_PLAYER_IS_ATTACKER) {
        continue;
      }
      if($this->moon_calculator->get_status() != UBE_MOON_DESTROY_SUCCESS) {
        $bashing_list[] = "({$player_id}, {$this->ube_planet_info[PLANET_ID]}, {$this->combat_timestamp})";
      }
      if($this->mission_type_id == MT_ATTACK && $this->is_defender_active_player) {
        $str_loose_or_win = $this->combat_result == UBE_COMBAT_RESULT_WIN ? 'raidswin' : 'raidsloose';
        db_user_set_by_id($player_id, "`xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1");
      }
    }
    if(!empty($bashing_list)) {
      $bashing_list = implode(',', $bashing_list);
      doquery("INSERT INTO {{bashing}} (bashing_user_id, bashing_planet_id, bashing_time) VALUES {$bashing_list};");
    }

    ube_combat_result_apply_from_object($this);
  }

  /**
   * Расчёт изменения ресурсов во флоте/на планете
   *
   * @param $fleet_outcome
   *
   * @return array
   */
  // OK0
  function ube_combat_result_calculate_resources(&$fleet_outcome) {
    $resource_delta_fleet = array();
    // Если во флоте остались юниты или это планета - генерируем изменение ресурсов
    foreach(sn_get_groups('resources_loot') as $resource_id) {
      $resource_change = (float)$fleet_outcome[UBE_RESOURCES_LOOTED][$resource_id] + (float)$fleet_outcome[UBE_CARGO_DROPPED][$resource_id];
      if($resource_change) {
        $resource_delta_fleet[$resource_id] = -($resource_change);
      }
    }

    return $resource_delta_fleet;
  }



















  // ------------------------------------------------------------------------------------------------
  // Рассылает письма всем участникам боя
  function sn_ube_message_send() {
    global $lang;

    // TODO: Отсылать каждому игроку сообщение на его языке!

    $planet_info = &$this->ube_planet_info;

    // Генерируем текст письма
    $text_common = sprintf($lang['ube_report_msg_body_common'],
      date(FMT_DATE_TIME, $this->combat_timestamp),
      $lang['sys_planet_type_sh'][$planet_info[PLANET_TYPE]],
      $planet_info[PLANET_GALAXY],
      $planet_info[PLANET_SYSTEM],
      $planet_info[PLANET_PLANET],
      htmlentities($planet_info[PLANET_NAME], ENT_COMPAT, 'UTF-8'),
      $lang[$this->combat_result == UBE_COMBAT_RESULT_WIN ? 'ube_report_info_outcome_win' :
        ($this->combat_result == UBE_COMBAT_RESULT_DRAW ? 'ube_report_info_outcome_draw' : 'ube_report_info_outcome_loss')]
    );

    $text_defender = '';
    $debris = $this->debris->debris_get();
    foreach($debris as $resource_id => $resource_amount) {
      if($resource_id == RES_DEUTERIUM) {
        continue;
      }

      $text_defender .= "{$lang['tech'][$resource_id]}: " . pretty_number($resource_amount) . '<br />';
    }
    if($text_defender) {
      $text_defender = "{$lang['ube_report_msg_body_debris']}{$text_defender}<br />";
    }

    $text_defender .= $this->moon_calculator->message_generate($this);

    $text_defender .= "{$lang['ube_report_info_link']}: <a href=\"index.php?page=battle_report&cypher=$this->report_cypher\">{$this->report_cypher}</a>";

    // TODO: Оптимизировать отсылку сообщений - отсылать пакетами
    $player_sides = $this->players->get_player_sides();
    foreach($player_sides as $player_id => $player_side) {
      $message = $text_common . ($this->is_small_fleet_recce && ($player_side == UBE_PLAYER_IS_ATTACKER) ? $lang['ube_report_msg_body_sfr'] : $text_defender);
      msg_send_simple_message($player_id, '', $this->combat_timestamp, MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $message);
    }

  }


  // OK0
  function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender) {
    $this->is_simulator = sys_get_param_int('simulator');
    $this->is_simulator = !empty($this->is_simulator);
    $this->mission_type_id = MT_ATTACK;

    $this->players = new UBEPlayerList();
    $this->fleet_list = new UBEFleetList();

    $this->sn_ube_simulator_fill_side($sym_defender, false);
    $this->sn_ube_simulator_fill_side($sym_attacker, true);
  }

  // ------------------------------------------------------------------------------------------------
  // Преобразовывает данные симулятора в данные для расчета боя
  // OK0
  function sn_ube_simulator_fill_side($side_info, $attacker, $player_id = -1) {
    global $ube_convert_techs;

    $player_id = $player_id == -1 ? $this->players->count() : $player_id;
    $fleet_id = $player_id; // FOR SIMULATOR!

    foreach($side_info as $fleet_data) {
      $this->players[$player_id]->player_name_set($attacker ? 'Attacker' : 'Defender');
      $this->players[$player_id]->player_side_switch($attacker);

      $objFleet = new UBEFleet();
      $this->fleet_list[$fleet_id] = $objFleet;

      $this->fleet_list[$fleet_id]->UBE_OWNER = $player_id;
      foreach($fleet_data as $unit_id => $unit_count) {
        if(!$unit_count) {
          continue;
        }

        $this->fleet_list[$fleet_id]->unit_list[$unit_id] = new UBEFleetUnit();

        $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);

        if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
          $this->fleet_list[$fleet_id]->unit_list[$unit_id]->count = $unit_count;
        } elseif($unit_type == UNIT_RESOURCES) {
          $this->fleet_list[$fleet_id]->UBE_RESOURCES[$unit_id] = $unit_count;
        } elseif($unit_type == UNIT_TECHNOLOGIES) {
          $this->players[$player_id]->player_bonus_add($unit_id, $unit_count, $ube_convert_techs[$unit_id]);
        } elseif($unit_type == UNIT_GOVERNORS) {
          if($unit_id == MRC_FORTIFIER) {
//            foreach($ube_convert_techs as $ube_id) {
//              $this->fleet_list[$fleet_id]->UBE_BONUSES[$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
//            }
            // Фортифаер даёт бонус ко всему
            $this->fleet_list[$fleet_id]->UBE_BONUSES[UBE_ATTACK] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            $this->fleet_list[$fleet_id]->UBE_BONUSES[UBE_SHIELD] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            $this->fleet_list[$fleet_id]->UBE_BONUSES[UBE_ARMOR] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
          }
        } elseif($unit_type == UNIT_MERCENARIES) {
          if($unit_id == MRC_ADMIRAL) {
            foreach($ube_convert_techs as $ube_id) {
              $this->players[$player_id]->player_bonus_add($unit_id, $unit_count, $ube_id);
            }
          }
        }
      }
    }
  }


  function set_option_from_config() {
    global $config;
    $this->options_method = $config->game_ube_method ? $config->game_ube_method : 0;
  }

  function get_time_spent() {
    return $this->time_spent;
  }

  function get_cypher() {
    return $this->report_cypher;
  }


  /**
   * Статик кусок из flt_mission_attack()
   *
   * @param Mission $objMission
   * @param array   $fleet_row
   *
   * @return bool
   */
  static function flt_mission_attack($objMission, $fleet_row) {
    $ube = new UBE();
    $ube->ube_attack_prepare($objMission); //  $combat_data = ube_attack_prepare($objMission);

    $ube->sn_ube_combat(); //  sn_ube_combat($combat_data);

    // TODO - Используется модулем skirmish! Переписать!
    flt_planet_capture_from_object($fleet_row, $ube); //  flt_planet_capture($fleet_row, $combat_data);

    $ube_report = new UBEReport();
    $ube_report->sn_ube_report_save($ube); //  sn_ube_report_save($combat_data);

    $ube->ube_combat_result_apply(); //  ube_combat_result_apply($combat_data);


    $ube->sn_ube_message_send(); //  sn_ube_message_send($combat_data);
    die('DIE at ' . __FILE__ . ' ' . __LINE__);

    return false;
  }

  static function sn_battle_report_view(&$template) {
    global $template_result, $lang;

    $ube_report = new UBEReport();
    $ube = $ube_report->sn_ube_report_load(sys_get_param_str('cypher'));
    if($ube != UBE_REPORT_NOT_FOUND) {
      $ube_report->sn_ube_report_generate($ube, $template_result);

      $template = gettemplate('ube_combat_report', $template);
      $template->assign_vars(array(
        'PAGE_HEADER' => $lang['ube_report_info_page_header'],
      ));
    } else {
      message($lang['sys_msg_ube_report_err_not_found'], $lang['sys_error']);
    }

    return $template;
  }

  static function display_simulator(&$sym_attacker, &$sym_defender) {
    global $template_result;

    $ube = new UBE();
    $ube->sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender); //  $combat_data = UNUSED_sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender);

    $ube->set_option_from_config(); //  $combat_data[UBE_OPTIONS][UBE_METHOD] = $config->game_ube_method ? $config->game_ube_method : 0;
    $ube->sn_ube_combat(); //  sn_ube_combat($combat_data);
    $ube_report = new UBEReport();

    // Это используется для тестов - отключено в стандартном режиме
//  if(!sys_get_param_int('simulator') || sys_get_param_str('reload')) {
//    sn_ube_report_save($combat_data);
//  }

    if(sys_get_param_str('reload')) {
      $ube_new = $ube_report->sn_ube_report_load($ube->get_cypher()); // $combat_data = sn_ube_report_load($combat_data[UBE_REPORT_CYPHER]);
      if($ube_new != UBE_REPORT_NOT_FOUND && is_object($ube_new)) {
        $ube = $ube_new;
      }
    }

    // Рендерим их в темплейт
    $ube_report->sn_ube_report_generate($ube, $template_result); // sn_ube_report_generate($combat_data, $template_result);

    $template_result['MICROTIME'] = $ube->get_time_spent(); // $template_result['MICROTIME'] = $combat_data[UBE_TIME_SPENT];

    $template = gettemplate('ube_combat_report', true);
    $template->assign_recursive($template_result);
    display($template, '', false, '', false, false, true);
  }

  /**
   * @param array  $report_row
   * @param string $report_cypher
   */
  public function load_from_report_row($report_row, $report_cypher) {
    $this->is_ube_loaded = true;

    $this->report_cypher = $report_cypher;

    $this->combat_timestamp = strtotime($report_row['ube_report_time_combat']);
    $this->time_spent = $report_row['ube_report_time_spent'];
    $this->is_admin_in_combat = $report_row['ube_report_combat_admin'];
    $this->mission_type_id = $report_row['ube_report_mission_type'];
    $this->combat_result = $report_row['ube_report_combat_result'];

    $this->is_small_fleet_recce = intval($report_row['ube_report_combat_sfr']);
    $this->capture_result = $report_row['ube_report_capture_result'];

    $this->ube_planet_info = array(
      PLANET_ID     => $report_row['ube_report_planet_id'],
      PLANET_NAME   => $report_row['ube_report_planet_name'],
      PLANET_SIZE   => $report_row['ube_report_planet_size'],
      PLANET_GALAXY => $report_row['ube_report_planet_galaxy'],
      PLANET_SYSTEM => $report_row['ube_report_planet_system'],
      PLANET_PLANET => $report_row['ube_report_planet_planet'],
      PLANET_TYPE   => $report_row['ube_report_planet_planet_type'],
    );

    $this->moon_calculator->load_from_report($report_row);

    $this->debris->debris_reset();
    $this->debris->debris_add_resource(RES_METAL, $report_row['ube_report_debris_metal']);
    $this->debris->debris_add_resource(RES_CRYSTAL, $report_row['ube_report_debris_crystal']);

    $query = doquery("SELECT * FROM {{ube_report_player}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($player_row = db_fetch($query)) {
      $this->players->init_player_from_report_info($player_row);
    }

    $query = doquery("SELECT * FROM {{ube_report_fleet}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($fleet_row = db_fetch($query)) {
      $objFleet = new UBEFleet(); // TODO ХУУУУУУУУУУУУУУУЙЙЙЙЙЙЙЙЙЙЙЙНЯЯЯЯЯЯЯЯЯЯЯЯЯЯЯЯЯЯЯ
      $objFleet->load_from_report($fleet_row, $this);
      $this->fleet_list[$objFleet->fleet_id] = $objFleet;
    }

    $this->rounds->db_load_round_list_from_report_row($report_row, $this);

    $this->outcome->db_load_from_report_row($report_row, $this);
  }

}

// ------------------------------------------------------------------------------------------------
/**
 * Записывает результат боя в БД
 *
 * @param UBE $ube
 *
 * @return mixed
 */
function ube_combat_result_apply_from_object(UBE $ube) { return sn_function_call(__FUNCTION__, array($ube)); }

// ------------------------------------------------------------------------------------------------
// Заполняет данные по флоту
/**
 * @param UBE   $ube
 * @param array $fleet
 * @param bool  $is_attacker
 *
 * @return mixed
 */
function ube_attack_prepare_fleet_from_object(UBE $ube, &$fleet, $is_attacker) { return sn_function_call(__FUNCTION__, array($ube, &$fleet, $is_attacker)); }


/**
 * @param array $fleet_row
 * @param UBE   $ube
 *
 * @return mixed
 */
function flt_planet_capture_from_object(array &$fleet_row, UBE $ube) { return sn_function_call(__FUNCTION__, array(&$fleet_row, $ube, &$result)); }

/**
 * @param array $fleet_row
 * @param UBE   $ube
 * @param mixed $result
 *
 * @return mixed
 */
function sn_flt_planet_capture_from_object(&$fleet_row, UBE $ube, &$result) { return $result; }

global $ube_combat_bonus_list, $ube_convert_techs, $ube_convert_to_techs;

$ube_combat_bonus_list = array(
  UBE_ATTACK => UBE_ATTACK,
  UBE_ARMOR  => UBE_ARMOR,
  UBE_SHIELD => UBE_SHIELD,
);

$ube_convert_techs = array(
  TECH_WEAPON => UBE_ATTACK,
  TECH_ARMOR  => UBE_ARMOR,
  TECH_SHIELD => UBE_SHIELD,
);

$ube_convert_to_techs = array(
  UBE_ATTACK => 'attack',
  UBE_ARMOR  => 'armor',
  UBE_SHIELD => 'shield',
);

