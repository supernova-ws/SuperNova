<?php
/**
 * Created by Gorlum 13.02.2018 7:53
 */

namespace Ube\Ube4_1;


class Ube4_1Calc {
  const CONVERT_UNIT_PARAMS = [
    UBE_ATTACK => 'attack',
    UBE_ARMOR  => 'armor',
    UBE_SHIELD => 'shield',
  ];

  public $ube_combat_bonus_list;
  public $ube_convert_techs;

  public function __construct() {
    global $ube_combat_bonus_list, $ube_convert_techs;

    $this->ube_combat_bonus_list = $ube_combat_bonus_list;
    $this->ube_convert_techs = $ube_convert_techs;
  }

  public function sn_ube_combat(&$combat_data) {
    // TODO: Сделать атаку по типам,  когда они будут

    $start = microtime(true);
    $this->sn_ube_combat_prepare_first_round($combat_data);

    for ($round = 1; $round <= 10; $round++) {
      // Готовим данные для раунда
      $this->sn_ube_combat_round_prepare($combat_data, $round);

      // Проводим раунд
      $this->sn_ube_combat_round_crossfire_fleet($combat_data, $round);

      // Анализируем итоги текущего раунда и готовим данные для следующего
      if ($this->sn_ube_combat_round_analyze($combat_data, $round) != UBE_COMBAT_RESULT_DRAW) {
        break;
      }
    }
    $combat_data[UBE_TIME_SPENT] = microtime(true) - $start;

    // Делать это всегда - нам нужны результаты боя: луна->обломки->количество осташихся юнитов
    $this->sn_ube_combat_analyze($combat_data);
  }

  // ------------------------------------------------------------------------------------------------
  protected function sn_ube_combat_prepare_first_round(&$combat_data) {
    // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
    $first_round_data = array();
    foreach ($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info) {
      $fleet_info[UBE_COUNT] = is_array($fleet_info[UBE_COUNT]) ? $fleet_info[UBE_COUNT] : array();
      $player_data = &$combat_data[UBE_PLAYERS][$fleet_info[UBE_OWNER]];
      $fleet_info[UBE_FLEET_TYPE] = $player_data[UBE_ATTACKER] ? UBE_ATTACKERS : UBE_DEFENDERS;

      foreach ($this->ube_combat_bonus_list as $bonus_id => $bonus_value) {
        // Вычисляем бонус игрока
        $bonus_value = isset($player_data[UBE_BONUSES][$bonus_id]) ? $player_data[UBE_BONUSES][$bonus_id] : 0;
        // Добавляем к бонусам флота бонусы игрока
        $fleet_info[UBE_BONUSES][$bonus_id] += $bonus_value;
      }

      $first_round_data[$fleet_id][UBE_COUNT] = $fleet_info[UBE_PRICE] = array();
      foreach ($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
        if ($unit_count <= 0) {
          continue;
        }

        $unit_info = get_unit_param($unit_id);
        // Заполняем информацию о кораблях в информации флота
        foreach ($this->ube_combat_bonus_list as $bonus_id => $bonus_value) {
          $fleet_info[$bonus_id][$unit_id] = floor($unit_info[static::CONVERT_UNIT_PARAMS[$bonus_id]] * (1 + $fleet_info[UBE_BONUSES][$bonus_id]));
        }
        $fleet_info[UBE_AMPLIFY][$unit_id] = $unit_info[P_AMPLIFY];
        // TODO: Переделать через get_ship_data()
        $fleet_info[UBE_CAPACITY][$unit_id] = $unit_info[P_CAPACITY];
        $fleet_info[UBE_TYPE][$unit_id] = $unit_info[P_UNIT_TYPE];
        // TODO: Переделать через список ресурсов
        $fleet_info[UBE_PRICE][RES_METAL]    [$unit_id] = $unit_info[P_COST][RES_METAL];
        $fleet_info[UBE_PRICE][RES_CRYSTAL]  [$unit_id] = $unit_info[P_COST][RES_CRYSTAL];
        $fleet_info[UBE_PRICE][RES_DEUTERIUM][$unit_id] = $unit_info[P_COST][RES_DEUTERIUM];
        $fleet_info[UBE_PRICE][RES_DARK_MATTER][$unit_id] = $unit_info[P_COST][RES_DARK_MATTER];

        // Копируем её в информацию о первом раунде
        $first_round_data[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id] * $unit_count;
        $first_round_data[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
        $first_round_data[$fleet_id][UBE_ARMOR_REST][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id];
        $first_round_data[$fleet_id][UBE_SHIELD_REST][$unit_id] = $fleet_info[UBE_SHIELD][$unit_id];
      }
    }
    $combat_data[UBE_ROUNDS][0][UBE_FLEETS] = $first_round_data;
    $combat_data[UBE_ROUNDS][1][UBE_FLEETS] = $first_round_data;
    $this->sn_ube_combat_round_prepare($combat_data, 0);
  }

// ------------------------------------------------------------------------------------------------
// Вычисление дополнительной информации для расчета раунда
  protected function sn_ube_combat_round_prepare(&$combat_data, $round) {
    $is_simulator = $combat_data[UBE_OPTIONS][UBE_SIMULATOR];

    $round_data = &$combat_data[UBE_ROUNDS][$round];
    foreach ($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data) {
      // Кэшируем переменные для легкого доступа к подмассивам
      $fleet_info = &$combat_data[UBE_FLEETS][$fleet_id];
      $fleet_data[UBE_FLEET_INFO] = &$fleet_info;
      $fleet_type = $fleet_info[UBE_FLEET_TYPE];

      foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        if ($unit_count <= 0) {
          continue;
        }

// TODO:  Добавить процент регенерации щитов

        // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
        $fleet_data[UBE_ATTACK_BASE][$unit_id] = floor($fleet_info[UBE_ATTACK][$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
        $fleet_data[UBE_SHIELD_BASE][$unit_id] = floor($fleet_info[UBE_SHIELD][$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
        $fleet_data[UBE_ARMOR_BASE][$unit_id] = floor($fleet_info[UBE_ARMOR][$unit_id]);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

        $fleet_data[UBE_ATTACK][$unit_id] = $fleet_data[UBE_ATTACK_BASE][$unit_id] * $unit_count;
        $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * $unit_count;
        $fleet_data[UBE_SHIELD_REST][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id];
        // $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * ($combat_data[UBE_OPTIONS][UBE_METHOD] ? $unit_count : 1);
        // $fleet_data[UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR_BASE][$unit_id] * $unit_count;
      }

      // Суммируем данные по флоту
      foreach ($this->ube_combat_bonus_list as $bonus_id) {
        $round_data[$fleet_type][$bonus_id][$fleet_id] += is_array($fleet_data[$bonus_id]) ? array_sum($fleet_data[$bonus_id]) : 0;
      }
    }

    // Суммируем данные по атакующим и защитникам
    foreach ($this->ube_combat_bonus_list as $bonus_id) {
      $round_data[UBE_TOTAL][UBE_DEFENDERS][$bonus_id] = array_sum($round_data[UBE_DEFENDERS][$bonus_id]);
      $round_data[UBE_TOTAL][UBE_ATTACKERS][$bonus_id] = array_sum($round_data[UBE_ATTACKERS][$bonus_id]);
    }

    // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
    foreach ($round_data[UBE_FLEETS] as &$fleet_data) {
      $fleet_type = $fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE];
      foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        $fleet_data[UBE_DAMAGE_PERCENT][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id] / $round_data[UBE_TOTAL][$fleet_type][UBE_ARMOR];
      }
    }
  }

// ------------------------------------------------------------------------------------------------
// Разбирает данные боя для генерации отчета
  protected function sn_ube_combat_analyze(&$combat_data) {
    global $config;

//  $combat_data[UBE_OUTCOME] = array();
    $combat_data[UBE_OPTIONS][UBE_EXCHANGE] = array(RES_METAL => $config->rpg_exchange_metal);

    $exchange = &$combat_data[UBE_OPTIONS][UBE_EXCHANGE];
    foreach (array(RES_CRYSTAL => 'rpg_exchange_crystal', RES_DEUTERIUM => 'rpg_exchange_deuterium', RES_DARK_MATTER => 'rpg_exchange_darkMatter') as $resource_id => $resource_name) {
      $exchange[$resource_id] = $config->$resource_name * $exchange[RES_METAL];
    }
//  $total_resources_value = array_sum($exchange);

    // Переменные для быстрого доступа к подмассивам
    $outcome = &$combat_data[UBE_OUTCOME];
    $fleets_info = &$combat_data[UBE_FLEETS];
    $last_round_data = &$combat_data[UBE_ROUNDS][count($combat_data[UBE_ROUNDS]) - 1];

    $outcome[UBE_DEBRIS] = array();

    // Генерируем результат боя
    foreach ($fleets_info as $fleet_id => &$fleet_info) {
      $fleet_type = $fleet_info[UBE_FLEET_TYPE];
      // Инициализируем массив результатов для флота
      $outcome[UBE_FLEETS][$fleet_id] = array(UBE_UNITS_LOST => array());
      $outcome[$fleet_type][UBE_FLEETS][$fleet_id] = &$outcome[UBE_FLEETS][$fleet_id];

      // Переменные для быстрого доступа к подмассивам
      $fleet_outcome = &$outcome[UBE_FLEETS][$fleet_id];
      $fleet_data = &$last_round_data[UBE_FLEETS][$fleet_id];

      foreach ($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
        // Вычисляем сколько юнитов осталось и сколько потеряно
        $units_left = $fleet_data[UBE_COUNT][$unit_id];

        // Восстановление обороны - 75% от уничтоженной
        if ($fleet_info[UBE_TYPE][$unit_id] == UNIT_DEFENCE) {
          $giveback_chance = 75; // TODO Configure
          $units_lost = $unit_count - $units_left;
          if ($combat_data[UBE_OPTIONS][UBE_SIMULATOR]) { // for simulation just return 75% of loss
            $units_giveback = round($units_lost * $giveback_chance / 100);
          } else {
            if ($unit_count > 10) { // if there were more then 10 defense elements - mass-calculating giveback
              $units_giveback = round($units_lost * mt_rand($giveback_chance * 0.8, $giveback_chance * 1.2) / 100);
            } else { //if there were less then 10 defense elements - calculating giveback per element
              $units_giveback = 0;
              for ($i = 1; $i <= $units_lost; $i++) {
                if (mt_rand(1, 100) <= $giveback_chance) {
                  $units_giveback++;
                }
              }
            }
          }
          $units_left += $units_giveback;
          $fleet_outcome[UBE_DEFENCE_RESTORE][$unit_id] = $units_giveback;
        }

        // TODO: Сбор металла/кристалла от обороны

        $units_lost = $unit_count - $units_left;

        // Вычисляем емкость трюмов оставшихся кораблей
        $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] += $fleet_info[UBE_CAPACITY][$unit_id] * $units_left;

        // Вычисляем потери в ресурсах
        if ($units_lost) {
          $fleet_outcome[UBE_UNITS_LOST][$unit_id] = $units_lost;

          foreach ($fleet_info[UBE_PRICE] as $resource_id => $unit_prices) {
            if (!$unit_prices[$unit_id]) {
              continue;
            }

            // ...чистыми
            $resources_lost = $units_lost * $unit_prices[$unit_id];
            $fleet_outcome[UBE_RESOURCES_LOST][$resource_id] += $resources_lost;

            // Если это корабль - прибавляем потери к обломкам на орбите
            if ($fleet_info[UBE_TYPE][$unit_id] == UNIT_SHIPS) {
              $outcome[UBE_DEBRIS][$resource_id] += floor($resources_lost * ($combat_data[UBE_OPTIONS][UBE_SIMULATOR] ? 30 : mt_rand(20, 40)) / 100); // TODO: Configurize
            }

            // ...в металле
            $resources_lost_in_metal = $resources_lost * $exchange[$resource_id];
            $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resources_lost_in_metal;
          }
        }
      }

      // На планете ($fleet_id = 0) ресурсы в космос не выбрасываются
      if ($fleet_id == 0) {
        continue;
      }

      // Количество ресурсов флота
      $fleet_total_resources = empty($fleet_info[UBE_RESOURCES]) ? 0 : array_sum($fleet_info[UBE_RESOURCES]);
      // Если на борту нет ресурсов - зачем нам все это?
      if ($fleet_total_resources == 0) {
        continue;
      }

      // Емкость трюмов флота
      $fleet_capacity = $outcome[$fleet_type][UBE_CAPACITY][$fleet_id];
      // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
      if ($fleet_capacity < $fleet_total_resources) {
        $left_percent = $fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
        foreach ($fleet_info[UBE_RESOURCES] as $resource_id => $resource_amount) {
          // Не просчитываем ресурсы, которых нет на борту кораблей флота
          if (!$resource_amount) {
            continue;
          }

          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $fleet_outcome[UBE_RESOURCES][$resource_id] = round($left_percent * $resource_amount);
          $resource_dropped = $resource_amount - $fleet_outcome[UBE_RESOURCES][$resource_id];
          $fleet_outcome[UBE_CARGO_DROPPED][$resource_id] = $resource_dropped;

          $outcome[UBE_DEBRIS][$resource_id] += round($resource_dropped * ($combat_data[UBE_OPTIONS][UBE_SIMULATOR] ? 50 : mt_rand(30, 70)) / 100); // TODO: Configurize
          $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resource_dropped * $exchange[$resource_id];
        }
        $fleet_total_resources = array_sum($fleet_outcome[UBE_RESOURCES]);
      }

      $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] = $fleet_capacity - $fleet_total_resources;
    }

    $outcome[UBE_COMBAT_RESULT] = !isset($last_round_data[UBE_OUTCOME]) || $last_round_data[UBE_OUTCOME] == UBE_COMBAT_RESULT_DRAW_END ? UBE_COMBAT_RESULT_DRAW : $last_round_data[UBE_OUTCOME];
    // SFR - Small Fleet Reconnaissance ака РМФ
    $outcome[UBE_SFR] = count($combat_data[UBE_ROUNDS]) == 2 && $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_LOSS;

    if (!$combat_data[UBE_OPTIONS][UBE_LOADED]) {
      if ($combat_data[UBE_OPTIONS][UBE_MOON_WAS]) {
        $outcome[UBE_MOON] = UBE_MOON_WAS;
      } else {
        $this->sn_ube_combat_analyze_moon($outcome, $combat_data[UBE_OPTIONS][UBE_SIMULATOR]);
      }

      // Лутаем ресурсы - если аттакер выиграл
      if ($outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN) {
        $this->sn_ube_combat_analyze_loot($combat_data);
        if ($combat_data[UBE_OPTIONS][UBE_MOON_WAS] && $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] == MT_DESTROY) {
          $this->sn_ube_combat_analyze_moon_destroy($combat_data);
        }
      }
    }

  }

// ------------------------------------------------------------------------------------------------
  protected function sn_ube_combat_analyze_moon(&$outcome, $is_simulator) {
    $outcome[UBE_DEBRIS_TOTAL] = 0;
    foreach (array(RES_METAL, RES_CRYSTAL) as $resource_id) // TODO via array
    {
      $outcome[UBE_DEBRIS_TOTAL] += $outcome[UBE_DEBRIS][$resource_id];
    }

    if ($outcome[UBE_DEBRIS_TOTAL]) {
      // TODO uni_calculate_moon_chance
      $moon_chance = min($outcome[UBE_DEBRIS_TOTAL] / 1000000, 30); // TODO Configure
      $moon_chance = $moon_chance >= 1 ? $moon_chance : 0;
      $outcome[UBE_MOON_CHANCE] = $moon_chance;
      if ($moon_chance) {
        if ($is_simulator || mt_rand(1, 100) <= $moon_chance) {
          $outcome[UBE_MOON_SIZE] = round($is_simulator ? $moon_chance * 150 + 1999 : mt_rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999));
          $outcome[UBE_MOON] = UBE_MOON_CREATE_SUCCESS;

          if ($outcome[UBE_DEBRIS_TOTAL] <= 30000000) {
            $outcome[UBE_DEBRIS_TOTAL] = 0;
            $outcome[UBE_DEBRIS] = array();
          } else {
            $moon_debris_spent = 30000000;
            $moon_debris_left_percent = ($outcome[UBE_DEBRIS_TOTAL] - $moon_debris_spent) / $outcome[UBE_DEBRIS_TOTAL];

            $outcome[UBE_DEBRIS_TOTAL] = 0;
            foreach (array(RES_METAL, RES_CRYSTAL) as $resource_id) // TODO via array
            {
              $outcome[UBE_DEBRIS][$resource_id] = floor($outcome[UBE_DEBRIS][$resource_id] * $moon_debris_left_percent);
              $outcome[UBE_DEBRIS_TOTAL] += $outcome[UBE_DEBRIS][$resource_id];
            }
          }
        } else {
          $outcome[UBE_MOON] = UBE_MOON_CREATE_FAILED;
        }
      }
    } else {
      $outcome[UBE_MOON] = UBE_MOON_NONE;
    }
  }

// ------------------------------------------------------------------------------------------------
  protected function sn_ube_combat_analyze_moon_destroy(&$combat_data) {
    // TODO: $is_simulator
    $reapers = 0;
    foreach ($combat_data[UBE_ROUNDS][count($combat_data[UBE_ROUNDS]) - 1][UBE_FLEETS] as $fleet_data) {
      if ($fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE] == UBE_ATTACKERS) {
        foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
          // TODO: Работа по группам - группа "Уничтожители лун"
          $reapers += ($unit_id == SHIP_HUGE_DEATH_STAR) ? $unit_count : 0;
        }
      }
    }

    $moon_size = $combat_data[UBE_OUTCOME][UBE_PLANET][PLANET_SIZE];
    if ($reapers) {
      $random = mt_rand(1, 100);
      $combat_data[UBE_OUTCOME][UBE_MOON_DESTROY_CHANCE] = max(1, min(99, round((100 - sqrt($moon_size)) * sqrt($reapers))));
      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS_DIE_CHANCE] = round(sqrt($moon_size) / 2 + sqrt($reapers));
      $combat_data[UBE_OUTCOME][UBE_MOON] = $random <= $combat_data[UBE_OUTCOME][UBE_MOON_DESTROY_CHANCE] ? UBE_MOON_DESTROY_SUCCESS : UBE_MOON_DESTROY_FAILED;
      $random = mt_rand(1, 100);
      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = $random <= $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS_DIE_CHANCE] ? UBE_MOON_REAPERS_DIED : UBE_MOON_REAPERS_RETURNED;
    } else {
      $combat_data[UBE_OUTCOME][UBE_MOON_REAPERS] = UBE_MOON_REAPERS_NONE;
    }
  }

// ------------------------------------------------------------------------------------------------
  protected function sn_ube_combat_analyze_loot(&$combat_data) {
    $exchange = &$combat_data[UBE_OPTIONS][UBE_EXCHANGE];
    $planet_resource_list = &$combat_data[UBE_FLEETS][0][UBE_RESOURCES];
    $outcome = &$combat_data[UBE_OUTCOME];

    $planet_looted_in_metal = 0;
    $planet_resource_looted = array();
    $planet_resource_total = is_array($planet_resource_list) ? array_sum($planet_resource_list) : 0;
    if ($planet_resource_total && ($total_capacity = array_sum($outcome[UBE_ATTACKERS][UBE_CAPACITY]))) {
      // Можно вывести только половину ресурсов, но не больше, чем общая вместимость флотов атакующих
      $planet_lootable = min($planet_resource_total / 2, $total_capacity);
      // Вычисляем процент вывоза. Каждого ресурса будет вывезено в одинаковых пропорциях
      $planet_lootable_percent = $planet_lootable / $planet_resource_total;

      // Вычисляем какой процент общей емкости трюмов атакующих будет задействован
      $total_lootable = min($planet_lootable, $total_capacity);

      // Вычисляем сколько ресурсов вывезено
      foreach ($outcome[UBE_ATTACKERS][UBE_CAPACITY] as $fleet_id => $fleet_capacity) {
        $looted_in_metal = 0;
        $fleet_loot_data = array();
        foreach ($planet_resource_list as $resource_id => $resource_amount) {
          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $fleet_lootable_percent = $fleet_capacity / $total_capacity;
          $looted = round($resource_amount * $planet_lootable_percent * $fleet_lootable_percent);
          $fleet_loot_data[$resource_id] = -$looted;
          $planet_resource_looted[$resource_id] += $looted;
          $looted_in_metal -= $looted * $exchange[$resource_id];
        }
        $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOOTED] = $fleet_loot_data;
        $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $looted_in_metal;
        $planet_looted_in_metal += $looted_in_metal;
      }
    }

    $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] -= $planet_looted_in_metal;
    $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOOTED] = $planet_resource_looted;
  }


// ------------------------------------------------------------------------------------------------
// Анализирует результаты раунда и генерирует данные для следующего раунда
  protected function sn_ube_combat_round_analyze(&$combat_data, $round) {
    $round_data = &$combat_data[UBE_ROUNDS][$round];
    $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW;

    $outcome = array();
    $next_round_fleet = array();
    foreach ($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data) {
      if (array_sum($fleet_data[UBE_COUNT]) <= 0) {
        continue;
      }

      foreach ($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        if ($unit_count <= 0) {
          continue;
        }
        $next_round_fleet[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
        $next_round_fleet[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id];
        $next_round_fleet[$fleet_id][UBE_ARMOR_REST][$unit_id] = $fleet_data[UBE_ARMOR_REST][$unit_id];
        $outcome[$fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE]] = 1;
      }
    }

    // Проверяем - если кого-то не осталось или не осталось обоих - заканчиваем цикл
    if (count($outcome) == 0 || $round == 10) {
      $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW_END;
    } elseif (count($outcome) == 1) {
      $round_data[UBE_OUTCOME] = isset($outcome[UBE_ATTACKERS]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    } elseif (count($outcome) == 2) {
      if ($round < 10) {
        $combat_data[UBE_ROUNDS][$round + 1][UBE_FLEETS] = $next_round_fleet;
      }
    }

    return ($round_data[UBE_OUTCOME]);
  }


// ------------------------------------------------------------------------------------------------
// Рассчитывает результат столкновения флотов ака раунд
  protected function sn_ube_combat_round_crossfire_fleet(&$combat_data, $round) {
    if (BE_DEBUG === true) {
      // sn_ube_combat_helper_round_header($round);
    }

    $round_data = &$combat_data[UBE_ROUNDS][$round];
    // Проводим бой. Сталкиваем каждый корабль атакующего с каждым кораблем атакуемого
    foreach ($round_data[UBE_ATTACKERS][UBE_ATTACK] as $attack_fleet_id => $temp) {
      $attack_fleet_data = &$round_data[UBE_FLEETS][$attack_fleet_id];
      foreach ($round_data[UBE_DEFENDERS][UBE_ATTACK] as $defend_fleet_id => $temp2) {
        $defend_fleet_data = &$round_data[UBE_FLEETS][$defend_fleet_id];

        foreach ($attack_fleet_data[UBE_COUNT] as $attack_unit_id => $attack_unit_count) {
          // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
          foreach ($defend_fleet_data[UBE_COUNT] as $defend_unit_id => $defend_unit_count) {
            $this->sn_ube_combat_round_crossfire_unit2($attack_fleet_data, $defend_fleet_data, $attack_unit_id, $defend_unit_id, $combat_data[UBE_OPTIONS]);
            $this->sn_ube_combat_round_crossfire_unit2($defend_fleet_data, $attack_fleet_data, $defend_unit_id, $attack_unit_id, $combat_data[UBE_OPTIONS]);
          }
        }
      }
    }

    if (BE_DEBUG === true) {
      // sn_ube_combat_helper_round_footer();
    }
  }


  // ------------------------------------------------------------------------------------------------
// Рассчитывает результат столкновения двух юнитов ака ход
  protected function sn_ube_combat_round_crossfire_unit2(&$attack_fleet_data, &$defend_fleet_data, $attack_unit_id, $defend_unit_id, &$combat_options) {
    if ($defend_fleet_data[UBE_COUNT][$defend_unit_id] <= 0) {
      return;
    }

    // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
    $direct_damage = floor($attack_fleet_data[UBE_ATTACK][$attack_unit_id] * $defend_fleet_data[UBE_DAMAGE_PERCENT][$defend_unit_id]);

    // Применяем амплифай, если есть
    $amplify = $attack_fleet_data[UBE_FLEET_INFO][UBE_AMPLIFY][$attack_unit_id][$defend_unit_id];
    $amplify = $amplify ? $amplify : 1;
    $amplified_damage = floor($direct_damage * $amplify);

    // Проверяем - не взорвался ли текущий юнит
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed, $combat_options);

    $defend_unit_base_defence = $defend_fleet_data[UBE_SHIELD_BASE][$defend_unit_id] + $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id];

    // todo Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры

    // Вычисляем, сколько юнитов взорвалось полностью
    $units_lost_full = floor($amplified_damage / $defend_unit_base_defence);
    // Уменьшаем дамадж на ту же сумму
    $amplified_damage -= $units_lost_full * $defend_unit_base_defence;
    // Вычисляем, сколько юнитов осталось
    $defend_fleet_data[UBE_COUNT][$defend_unit_id] = max(0, $defend_fleet_data[UBE_COUNT][$defend_unit_id] - $units_lost_full);
    // Уменьшаем броню подразделения на броню потерянных юнитов
    $defend_fleet_data[UBE_ARMOR][$defend_unit_id] -= $units_lost_full * $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id];
    $defend_fleet_data[UBE_SHIELD][$defend_unit_id] -= $units_lost_full * $defend_fleet_data[UBE_SHIELD_BASE][$defend_unit_id];

    // Проверяем - не взорвался ли текущий юнит
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed, $combat_options);
  }

  protected function sn_ube_combat_round_crossfire_unit_damage_current(&$defend_fleet_data, $defend_unit_id, &$amplified_damage, &$units_lost, &$units_boomed, &$combat_options) {
    $unit_is_lost = false;

    $units_boomed = $units_boomed ? $units_boomed : 0;
    $units_lost = $units_lost ? $units_lost : 0;

    $boom_limit = 75; // Взрываемся на 75% прочности
    if ($defend_fleet_data[UBE_COUNT][$defend_unit_id] > 0 && $amplified_damage) {
      // $defend_fleet_info = &$defend_fleet_data[UBE_FLEET_INFO];

      $damage_to_shield = min($amplified_damage, $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id]);
      $amplified_damage -= $damage_to_shield;
      $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] -= $damage_to_shield;

      $damage_to_armor = min($amplified_damage, $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id]);
      $amplified_damage -= $damage_to_armor;
      $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] -= $damage_to_armor;

      // Если брони не осталось - юнит потерян
      if ($defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] <= 0) {
        $unit_is_lost = true;
      } // Если броня осталось, но не осталось щитов - прошел дамадж по броне и надо проверить - не взорвался ли корабль
      elseif ($defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] <= 0) {
        $last_unit_hp = $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id];
        $last_unit_percent = $last_unit_hp / $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id] * 100;

        $random = $combat_options[UBE_SIMULATOR] ? $boom_limit / 2 : mt_rand(0, 100);
        if ($last_unit_percent <= $boom_limit && $last_unit_percent <= $random) {
          $unit_is_lost = true;
          $units_boomed++;
          $damage_to_armor += $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id];
          $defend_fleet_data[UBE_UNITS_BOOM][$defend_unit_id]++;
          $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] = 0;
        }
      }

      $defend_fleet_data[UBE_ARMOR][$defend_unit_id] -= $damage_to_armor;
      $defend_fleet_data[UBE_SHIELD][$defend_unit_id] -= $damage_to_shield;

      if ($unit_is_lost) {
        $units_lost++;
        $defend_fleet_data[UBE_COUNT][$defend_unit_id]--;
        if ($defend_fleet_data[UBE_COUNT][$defend_unit_id]) {
          $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] = $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id];
          $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] = $defend_fleet_data[UBE_SHIELD_BASE][$defend_unit_id];
        }
      }
    }

    return $unit_is_lost;
  }

}
