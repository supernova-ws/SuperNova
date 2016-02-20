<?php

require_once('UBEReport.php');



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
function ube_attack_prepare_fleet_from_object($ube, &$fleet, $is_attacker) { return sn_function_call(__FUNCTION__, array($ube, &$fleet, $is_attacker)); }


/**
 * @param array $fleet_row
 * @param UBE   $ube
 *
 * @return mixed
 */
function flt_planet_capture_from_object(&$fleet_row, $ube) { return sn_function_call(__FUNCTION__, array(&$fleet_row, $ube, &$result)); }
/**
 * @param array $fleet_row
 * @param UBE   $ube
 * @param mixed $result
 *
 * @return mixed
 */
function sn_flt_planet_capture_from_object(&$fleet_row, $ube, &$result) { return $result; }


class UBE {

  public $combat_data = array();

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

  public $options = array();

  public $is_simulator = false;

  /**
   * [$resource_id] => $rate
   *
   * @var array
   */
  public $resource_exchange_rates = array();

  /**
   * [$player_id]
   *
   * @var array
   */
  public $players = array();

  /**
   * [$player_id]
   *
   * @var array
   */
  public $fleets = array();

  /**
   * [$round_id]
   *
   * @var array
   */
  public $rounds = array();

  /**
   * [UBE_xxx]
   *
   * @var array
   */
  public $outcome = array();

  /**
   * Заполняет начальные данные по данным миссии
   *
   * @param Mission $objMission
   */
  // OK0
  function ube_attack_prepare(&$objMission) {
    /*
    UBE_OPTIONS[UBE_LOADED]
    UBE_OPTIONS[UBE_EXCHANGE]
    UBE_OPTIONS[UBE_MOON_WAS]
    */

    $combat_data = &$this->combat_data;

    global $config;

    $this->resource_exchange_rates = get_resource_exchange();

    $objFleet = $objMission->fleet;

    $destination_planet = &$objMission->dst_planet;

    $combat_data = array();
    $this->combat_timestamp = $objFleet->time_arrive_to_target;

// TODO: Не допускать атаки игроком своих же флотов - т.е. холд против атаки
    // Готовим инфу по атакуемой планете
    $this->ube_attack_prepare_planet($destination_planet);

    // Готовим инфу по удержанию
    $target_coordinates = $objFleet->target_coordinates_typed();
    $fleet_list_on_hold = fleet_list_on_hold($target_coordinates['galaxy'], $target_coordinates['system'], $target_coordinates['planet'], $target_coordinates['type'], $this->combat_timestamp);
    foreach($fleet_list_on_hold as $fleet) {
      $this->ube_attack_prepare_fleet($fleet, false);
    }

    // Готовим инфу по атакующим
    if($objFleet->fleet_group) {
      $acs_fleet_list = fleet_list_by_group($objFleet->fleet_group);
      foreach($acs_fleet_list as $fleet) {
        $this->ube_attack_prepare_fleet($fleet, true);
      }
    } else {
      $this->ube_attack_prepare_fleet($objFleet->make_db_row(), true);
    }

    // Готовим опции
    $this->options[UBE_MOON_WAS] = $destination_planet['planet_type'] == PT_MOON || is_array(db_planet_by_parent($destination_planet['id'], true, '`id`'));
    $this->options[UBE_MISSION_TYPE] = $objFleet->mission_type;
    $this->options[UBE_METHOD] = $config->game_ube_method ? $config->game_ube_method : 0;
  }

  /**
   * Заполняет данные по планете
   *
   * @param $combat_data
   * @param $planet
   */
  // OK0
  function ube_attack_prepare_planet(&$planet) {
    $combat_data = &$this->combat_data;
    global $ube_combat_bonus_list;

    $player_id = $planet['id_owner'];

    $this->ube_attack_prepare_player($player_id, false);

    $player = &$this->players[$player_id][UBE_PLAYER_DATA];

    $this->fleets[0] = array(UBE_OWNER => $player_id);
    $fleet_info = &$this->fleets[0];

    foreach(sn_get_groups('combat') as $unit_id) {
      if($unit_count = mrc_get_level($player, $planet, $unit_id)) {
        $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
      }
    }

    foreach(sn_get_groups('resources_loot') as $resource_id) {
      $fleet_info[UBE_RESOURCES][$resource_id] = floor(mrc_get_level($player, $planet, $resource_id));
    }

    if($fortifier_level = mrc_get_level($player, $planet, MRC_FORTIFIER)) {
      $fortifier_bonus = $fortifier_level * get_unit_param(MRC_FORTIFIER, P_BONUS_VALUE) / 100;
      foreach($ube_combat_bonus_list as $ube_id) {
        $fleet_info[UBE_BONUSES][$ube_id] += $fortifier_bonus;
      }
    }

    $this->outcome[UBE_PLANET] = $fleet_info[UBE_PLANET] = array(
      PLANET_ID     => $planet['id'],
      PLANET_NAME   => $planet['name'],
      PLANET_GALAXY => $planet['galaxy'],
      PLANET_SYSTEM => $planet['system'],
      PLANET_PLANET => $planet['planet'],
      PLANET_TYPE   => $planet['planet_type'],
      PLANET_SIZE   => $planet['diameter'],
    );

    $this->options[UBE_DEFENDER_ACTIVE] = $player['onlinetime'] >= $this->combat_timestamp - UBE_DEFENDER_ACTIVE_TIMEOUT;
  }

  // ------------------------------------------------------------------------------------------------
  // Заполняет данные по игроку
  // OK0
  function ube_attack_prepare_player($player_id, $is_attacker) {
    $combat_data = &$this->combat_data;
    global $ube_convert_techs;

    if(!isset($this->players[$player_id])) {
      $this->players[$player_id] = array(UBE_ATTACKER => $is_attacker);
      $player_info = &$this->players[$player_id];

      $player_data = db_user_by_id($player_id, true);
      $player_info[UBE_NAME] = $player_data['username'];
      $player_info[UBE_AUTH_LEVEL] = $player_data['authlevel'];
      $this->options[UBE_COMBAT_ADMIN] = $this->options[UBE_COMBAT_ADMIN] || $player_data['authlevel']; // Участвует ли админ в бою?
      $player_info[UBE_PLAYER_DATA] = $player_data;

      $admiral_bonus = mrc_get_level($player_data, false, MRC_ADMIRAL) * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
      foreach($ube_convert_techs as $unit_id => $ube_id) {
        $player_info[UBE_BONUSES][$ube_id] += mrc_get_level($player_data, false, $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $admiral_bonus;
      }
    } else {
      $this->players[$player_id][UBE_ATTACKER] = $this->players[$player_id][UBE_ATTACKER] || $is_attacker;
    }
  }

  /**
   * @param array $fleet_row
   * @param bool $is_attacker
   */
  // ------------------------------------------------------------------------------------------------
  // Заполняет данные по флоту
  // OK0
  function ube_attack_prepare_fleet(&$fleet_row, $is_attacker) {
    $fleet_owner_id = $fleet_row['fleet_owner'];
    $fleet_id = $fleet_row['fleet_id'];

    $this->ube_attack_prepare_player($fleet_owner_id, $is_attacker);

//  $fleet_data = sys_unit_str2arr($fleet['_fleet_array']);
    $fleet_data = Fleet::static_proxy_string_to_array($fleet_row);

    $this->fleets[$fleet_id][UBE_OWNER] = $fleet_owner_id;
    $fleet_info = &$this->fleets[$fleet_id];
    $fleet_info[UBE_FLEET_GROUP] = $fleet_row['fleet_group'];
    foreach($fleet_data as $unit_id => $unit_count) {
      if(!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
      }
    }

    $fleet_info[UBE_RESOURCES] = array(
      RES_METAL     => $fleet_row['fleet_resource_metal'],
      RES_CRYSTAL   => $fleet_row['fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet_row['fleet_resource_deuterium'],
    );

    $fleet_info[UBE_PLANET] = array(
//    PLANET_ID => $fleet['fleet_start_id'],
//    PLANET_NAME => $fleet['fleet_start_name'],
      PLANET_GALAXY => $fleet_row['fleet_start_galaxy'],
      PLANET_SYSTEM => $fleet_row['fleet_start_system'],
      PLANET_PLANET => $fleet_row['fleet_start_planet'],
      PLANET_TYPE   => $fleet_row['fleet_start_type'],
    );

    // TODO - Вызов основной функции!!!
    ube_attack_prepare_fleet_from_object($this, $fleet_row, $is_attacker);
  }


































  // ------------------------------------------------------------------------------------------------
  // Общий алгоритм расчета боя
  // OK0
  function sn_ube_combat() {
    $combat_data = &$this->combat_data;

    // TODO: Сделать атаку по типам,  когда они будут

    $start = microtime(true);
    $this->sn_ube_combat_prepare_first_round();

    for($round = 1; $round <= 10; $round++) {
      // Готовим данные для раунда
      $this->sn_ube_combat_round_prepare($round);

      // Проводим раунд
      $this->sn_ube_combat_round_crossfire_fleet($round);

      // Анализируем итоги текущего раунда и готовим данные для следующего
      if($this->sn_ube_combat_round_analyze($round) != UBE_COMBAT_RESULT_DRAW) {
        break;
      }
    }
    $combat_data[UBE_TIME_SPENT] = microtime(true) - $start;

    // Делать это всегда - нам нужны результаты боя: луна->обломки->количество осташихся юнитов
    $this->sn_ube_combat_analyze();
  }

  // ------------------------------------------------------------------------------------------------
  // OK0
  function sn_ube_combat_prepare_first_round() {
    $combat_data = &$this->combat_data;

    global $ube_combat_bonus_list, $ube_convert_to_techs;

    // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
    $first_round_data = array();
    foreach($this->fleets as $fleet_id => &$fleet_info) {
      $fleet_info[UBE_COUNT] = is_array($fleet_info[UBE_COUNT]) ? $fleet_info[UBE_COUNT] : array();
      $player_data = &$this->players[$fleet_info[UBE_OWNER]];
      $fleet_info[UBE_FLEET_TYPE] = $player_data[UBE_ATTACKER] ? UBE_ATTACKERS : UBE_DEFENDERS;

      foreach($ube_combat_bonus_list as $bonus_id => $bonus_value) {
        // Вычисляем бонус игрока
        $bonus_value = isset($player_data[UBE_BONUSES][$bonus_id]) ? $player_data[UBE_BONUSES][$bonus_id] : 0;
        // Добавляем к бонусам флота бонусы игрока
        $fleet_info[UBE_BONUSES][$bonus_id] += $bonus_value;
      }

      $first_round_data[$fleet_id][UBE_COUNT] = $fleet_info[UBE_PRICE] = array();
      foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
        if($unit_count <= 0) {
          continue;
        }

        $unit_info = get_unit_param($unit_id);
        // Заполняем информацию о кораблях в информации флота
        foreach($ube_combat_bonus_list as $bonus_id => $bonus_value) {
          $fleet_info[$bonus_id][$unit_id] = floor($unit_info[$ube_convert_to_techs[$bonus_id]] * (1 + $fleet_info[UBE_BONUSES][$bonus_id]));
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
    $this->rounds[0][UBE_FLEETS] = $first_round_data;
    $this->rounds[1][UBE_FLEETS] = $first_round_data;
    $this->sn_ube_combat_round_prepare(0);
  }

  // ------------------------------------------------------------------------------------------------
  // Вычисление дополнительной информации для расчета раунда
  // OK0
  function sn_ube_combat_round_prepare($round) {
    global $ube_combat_bonus_list;

    $round_data = &$this->rounds[$round];
    foreach($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data) {
      // Кэшируем переменные для легкого доступа к подмассивам
      $fleet_info = &$this->fleets[$fleet_id];
      $fleet_data[UBE_FLEET_INFO] = &$fleet_info;
      $fleet_type = $fleet_info[UBE_FLEET_TYPE];

      foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        if($unit_count <= 0) {
          continue;
        }

// TODO:  Добавить процент регенерации щитов

        // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
        $fleet_data[UBE_ATTACK_BASE][$unit_id] = floor($fleet_info[UBE_ATTACK][$unit_id] * ($this->is_simulator ? 1 : mt_rand(80, 120) / 100));
        $fleet_data[UBE_SHIELD_BASE][$unit_id] = floor($fleet_info[UBE_SHIELD][$unit_id] * ($this->is_simulator ? 1 : mt_rand(80, 120) / 100));
        $fleet_data[UBE_ARMOR_BASE][$unit_id] = floor($fleet_info[UBE_ARMOR][$unit_id]);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

        $fleet_data[UBE_ATTACK][$unit_id] = $fleet_data[UBE_ATTACK_BASE][$unit_id] * $unit_count;
        $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * $unit_count;
        $fleet_data[UBE_SHIELD_REST][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id];
        // $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * ($combat_data[UBE_OPTIONS][UBE_METHOD] ? $unit_count : 1);
        // $fleet_data[UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR_BASE][$unit_id] * $unit_count;
      }

      // Суммируем данные по флоту
      foreach($ube_combat_bonus_list as $bonus_id) {
        $round_data[$fleet_type][$bonus_id][$fleet_id] += is_array($fleet_data[$bonus_id]) ? array_sum($fleet_data[$bonus_id]) : 0;
      }
    }

    // Суммируем данные по атакующим и защитникам
    foreach($ube_combat_bonus_list as $bonus_id) {
      $round_data[UBE_TOTAL][UBE_DEFENDERS][$bonus_id] = array_sum($round_data[UBE_DEFENDERS][$bonus_id]);
      $round_data[UBE_TOTAL][UBE_ATTACKERS][$bonus_id] = array_sum($round_data[UBE_ATTACKERS][$bonus_id]);
    }

    // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
    foreach($round_data[UBE_FLEETS] as &$fleet_data) {
      $fleet_type = $fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE];
      foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        $fleet_data[UBE_DAMAGE_PERCENT][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id] / $round_data[UBE_TOTAL][$fleet_type][UBE_ARMOR];
      }
    }
  }

  // Рассчитывает результат столкновения флотов ака раунд
  // OK0
  function sn_ube_combat_round_crossfire_fleet($round) {
    if(BE_DEBUG === true) {
      // sn_ube_combat_helper_round_header($round);
    }

    $round_data = &$this->rounds[$round];
    // Проводим бой. Сталкиваем каждый корабль атакующего с каждым кораблем атакуемого
    foreach($round_data[UBE_ATTACKERS][UBE_ATTACK] as $attack_fleet_id => $temp) {
      $attack_fleet_data = &$round_data[UBE_FLEETS][$attack_fleet_id];
      foreach($round_data[UBE_DEFENDERS][UBE_ATTACK] as $defend_fleet_id => $temp2) {
        $defend_fleet_data = &$round_data[UBE_FLEETS][$defend_fleet_id];

        foreach($attack_fleet_data[UBE_COUNT] as $attack_unit_id => $attack_unit_count) {
          // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
          foreach($defend_fleet_data[UBE_COUNT] as $defend_unit_id => $defend_unit_count) {
            $this->sn_ube_combat_round_crossfire_unit2($attack_fleet_data, $defend_fleet_data, $attack_unit_id, $defend_unit_id);
            $this->sn_ube_combat_round_crossfire_unit2($defend_fleet_data, $attack_fleet_data, $defend_unit_id, $attack_unit_id);
          }
        }
      }
    }

    if(BE_DEBUG === true) {
      // sn_ube_combat_helper_round_footer();
    }
  }

  // ------------------------------------------------------------------------------------------------
  // Рассчитывает результат столкновения двух юнитов ака ход
  // OK0
  function sn_ube_combat_round_crossfire_unit2(&$attack_fleet_data, &$defend_fleet_data, $attack_unit_id, $defend_unit_id) {
    if($defend_fleet_data[UBE_COUNT][$defend_unit_id] <= 0) {
      return;
    }

    // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
    $direct_damage = floor($attack_fleet_data[UBE_ATTACK][$attack_unit_id] * $defend_fleet_data[UBE_DAMAGE_PERCENT][$defend_unit_id]);

    // Применяем амплифай, если есть
    $amplify = $attack_fleet_data[UBE_FLEET_INFO][UBE_AMPLIFY][$attack_unit_id][$defend_unit_id];
    $amplify = $amplify ? $amplify : 1;
    $amplified_damage = floor($direct_damage * $amplify);

    // Проверяем - не взорвался ли текущий юнит
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed);

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
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed);
  }

  // OK0
  function sn_ube_combat_round_crossfire_unit_damage_current(&$defend_fleet_data, $defend_unit_id, &$amplified_damage, &$units_lost, &$units_boomed) {
    $unit_is_lost = false;

    $units_boomed = $units_boomed ? $units_boomed : 0;
    $units_lost = $units_lost ? $units_lost : 0;
    $boom_limit = 75; // Взрываемся на 75% прочности
    if($defend_fleet_data[UBE_COUNT][$defend_unit_id] > 0 && $amplified_damage) {

      $damage_to_shield = min($amplified_damage, $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id]);
      $amplified_damage -= $damage_to_shield;
      $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] -= $damage_to_shield;

      $damage_to_armor = min($amplified_damage, $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id]);
      $amplified_damage -= $damage_to_armor;
      $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] -= $damage_to_armor;

      // Если брони не осталось - юнит потерян
      if($defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] <= 0) {
        $unit_is_lost = true;
      } // Если броня осталось, но не осталось щитов - прошел дамадж по броне и надо проверить - не взорвался ли корабль
      elseif($defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] <= 0) {
        $last_unit_hp = $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id];
        $last_unit_percent = $last_unit_hp / $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id] * 100;

        $random = $this->is_simulator ? $boom_limit / 2 : mt_rand(0, 100);
        if($last_unit_percent <= $boom_limit && $last_unit_percent <= $random) {
//pdump($last_unit_percent, 'Юнит взорвался');
          $unit_is_lost = true;
          $units_boomed++;
          $damage_to_armor += $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id];
          $defend_fleet_data[UBE_UNITS_BOOM][$defend_unit_id]++;
          $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] = 0;
        }
      }

      $defend_fleet_data[UBE_ARMOR][$defend_unit_id] -= $damage_to_armor;
      $defend_fleet_data[UBE_SHIELD][$defend_unit_id] -= $damage_to_shield;

      if($unit_is_lost) {
        $units_lost++;
        $defend_fleet_data[UBE_COUNT][$defend_unit_id]--;
        if($defend_fleet_data[UBE_COUNT][$defend_unit_id]) {
//pdump($defend_fleet_data[UBE_COUNT][$defend_unit_id], 'Еще остались юниты');
          $defend_fleet_data[UBE_ARMOR_REST][$defend_unit_id] = $defend_fleet_data[UBE_ARMOR_BASE][$defend_unit_id];
          $defend_fleet_data[UBE_SHIELD_REST][$defend_unit_id] = $defend_fleet_data[UBE_SHIELD_BASE][$defend_unit_id];
        }
      }
    }

    return $unit_is_lost;
  }





















// ------------------------------------------------------------------------------------------------
// Анализирует результаты раунда и генерирует данные для следующего раунда
  // OK0
  function sn_ube_combat_round_analyze($round) {
    $round_data = &$this->rounds[$round];
    $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW;

    $outcome = array();
    $next_round_fleet = array();
    foreach($round_data[UBE_FLEETS] as $fleet_id => &$fleet_data) {
      if(array_sum($fleet_data[UBE_COUNT]) <= 0) {
        continue;
      }

      foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
        if($unit_count <= 0) {
          continue;
        }
        $next_round_fleet[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
        $next_round_fleet[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_data[UBE_ARMOR][$unit_id];
        $next_round_fleet[$fleet_id][UBE_ARMOR_REST][$unit_id] = $fleet_data[UBE_ARMOR_REST][$unit_id];
        $outcome[$fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE]] = 1;
      }
    }

    // Проверяем - если кого-то не осталось или не осталось обоих - заканчиваем цикл
    if(count($outcome) == 0 || $round == 10) {
      $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW_END;
    } elseif(count($outcome) == 1) {
      $round_data[UBE_OUTCOME] = isset($outcome[UBE_ATTACKERS]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    } elseif(count($outcome) == 2) {
      if($round < 10) {
        $this->rounds[$round + 1][UBE_FLEETS] = $next_round_fleet;
      }
    }

    return ($round_data[UBE_OUTCOME]);
  }















  // ------------------------------------------------------------------------------------------------
  // Разбирает данные боя для генерации отчета
  // OK0
  function sn_ube_combat_analyze() {
    // Переменные для быстрого доступа к подмассивам
    $outcome = &$this->outcome;
    $fleets_info = &$this->fleets;
    $last_round_data = &$this->rounds[count($this->rounds) - 1];

    $outcome[UBE_DEBRIS] = array();

    // Генерируем результат боя
    foreach($fleets_info as $fleet_id => &$fleet_info) {
      $fleet_type = $fleet_info[UBE_FLEET_TYPE];
      // Инициализируем массив результатов для флота
      $outcome[UBE_FLEETS][$fleet_id] = array(UBE_UNITS_LOST => array());
      $outcome[$fleet_type][UBE_FLEETS][$fleet_id] = &$outcome[UBE_FLEETS][$fleet_id];

      // Переменные для быстрого доступа к подмассивам
      $fleet_outcome = &$outcome[UBE_FLEETS][$fleet_id];
      $fleet_data = &$last_round_data[UBE_FLEETS][$fleet_id];

      foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
        // Вычисляем сколько юнитов осталось и сколько потеряно
        $units_left = $fleet_data[UBE_COUNT][$unit_id];

        // Восстановление обороны - 75% от уничтоженной
        if($fleet_info[UBE_TYPE][$unit_id] == UNIT_DEFENCE) {
          $giveback_chance = 75; // TODO Configure
          $units_lost = $unit_count - $units_left;
          if($this->is_simulator) { // for simulation just return 75% of loss
            $units_giveback = round($units_lost * $giveback_chance / 100);
          } else {
            if($unit_count > 10) { // if there were more then 10 defense elements - mass-calculating giveback
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

        $units_lost = $unit_count - $units_left;

        // Вычисляем емкость трюмов оставшихся кораблей
        $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] += $fleet_info[UBE_CAPACITY][$unit_id] * $units_left;

        // Вычисляем потери в ресурсах
        if($units_lost) {
          $fleet_outcome[UBE_UNITS_LOST][$unit_id] = $units_lost;

          foreach($fleet_info[UBE_PRICE] as $resource_id => $unit_prices) {
            if(!$unit_prices[$unit_id]) {
              continue;
            }

            // ...чистыми
            $resources_lost = $units_lost * $unit_prices[$unit_id];
            $fleet_outcome[UBE_RESOURCES_LOST][$resource_id] += $resources_lost;

            // Если это корабль - прибавляем потери к обломкам на орбите
            if($fleet_info[UBE_TYPE][$unit_id] == UNIT_SHIPS) {
              $outcome[UBE_DEBRIS][$resource_id] += floor($resources_lost * ($this->is_simulator ? 30 : mt_rand(20, 40)) / 100); // TODO: Configurize
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
      $fleet_total_resources = empty($fleet_info[UBE_RESOURCES]) ? 0 : array_sum($fleet_info[UBE_RESOURCES]);
      // Если на борту нет ресурсов - зачем нам все это?
      if($fleet_total_resources == 0) {
        continue;
      }

      // Емкость трюмов флота
      $fleet_capacity = $outcome[$fleet_type][UBE_CAPACITY][$fleet_id];
      // Если емкость трюмов меньше количество ресурсов - часть ресов выбрасываем нахуй
      if($fleet_capacity < $fleet_total_resources) {
        $left_percent = $fleet_capacity / $fleet_total_resources; // Сколько ресурсов будет оставлено
        foreach($fleet_info[UBE_RESOURCES] as $resource_id => $resource_amount) {
          // Не просчитываем ресурсы, которых нет на борту кораблей флота
          if(!$resource_amount) {
            continue;
          }

          // TODO Восстанавливаем ошибку округления - придумать нормальный алгоритм - вроде round() должно быть достаточно. Проверить
          $fleet_outcome[UBE_RESOURCES][$resource_id] = round($left_percent * $resource_amount);
          $resource_dropped = $resource_amount - $fleet_outcome[UBE_RESOURCES][$resource_id];
          $fleet_outcome[UBE_CARGO_DROPPED][$resource_id] = $resource_dropped;

          $outcome[UBE_DEBRIS][$resource_id] += round($resource_dropped * ($this->is_simulator ? 50 : mt_rand(30, 70)) / 100); // TODO: Configurize
          $fleet_outcome[UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $resource_dropped * $this->resource_exchange_rates[$resource_id];
        }
        $fleet_total_resources = array_sum($fleet_outcome[UBE_RESOURCES]);
      }

      $outcome[$fleet_type][UBE_CAPACITY][$fleet_id] = $fleet_capacity - $fleet_total_resources;
    }

    $outcome[UBE_COMBAT_RESULT] = !isset($last_round_data[UBE_OUTCOME]) || $last_round_data[UBE_OUTCOME] == UBE_COMBAT_RESULT_DRAW_END ? UBE_COMBAT_RESULT_DRAW : $last_round_data[UBE_OUTCOME];
    // SFR - Small Fleet Reconnaissance ака РМФ
    $outcome[UBE_SFR] = count($this->rounds) == 2 && $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_LOSS;

    if(!$this->options[UBE_LOADED]) {
      if($this->options[UBE_MOON_WAS]) {
        $outcome[UBE_MOON] = UBE_MOON_WAS;
      } else {
        $this->sn_ube_combat_analyze_moon($outcome, $this->is_simulator);
      }

      // Лутаем ресурсы - если аттакер выиграл
      if($outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN) {
        $this->sn_ube_combat_analyze_loot();
        if($this->options[UBE_MOON_WAS] && $this->options[UBE_MISSION_TYPE] == MT_DESTROY) {
          $this->sn_ube_combat_analyze_moon_destroy();
        }
      }
    }

  }

  // ------------------------------------------------------------------------------------------------
  // OK0
  function sn_ube_combat_analyze_moon(&$outcome, $is_simulator) {
    $outcome[UBE_DEBRIS_TOTAL] = 0;
    foreach(array(RES_METAL, RES_CRYSTAL) as $resource_id) {
      $outcome[UBE_DEBRIS_TOTAL] += $outcome[UBE_DEBRIS][$resource_id];
    }

    if($outcome[UBE_DEBRIS_TOTAL]) {
      // TODO uni_calculate_moon_chance
      $moon_chance = min($outcome[UBE_DEBRIS_TOTAL] / 1000000, 30); // TODO Configure
      $moon_chance = $moon_chance >= 1 ? $moon_chance : 0;
      $outcome[UBE_MOON_CHANCE] = $moon_chance;
      if($moon_chance) {
        if($is_simulator || mt_rand(1, 100) <= $moon_chance) {
          $outcome[UBE_MOON_SIZE] = round($is_simulator ? $moon_chance * 150 + 1999 : mt_rand($moon_chance * 100 + 1000, $moon_chance * 200 + 2999));
          $outcome[UBE_MOON] = UBE_MOON_CREATE_SUCCESS;

          if($outcome[UBE_DEBRIS_TOTAL] <= 30000000) {
            $outcome[UBE_DEBRIS_TOTAL] = 0;
            $outcome[UBE_DEBRIS] = array();
          } else {
            $moon_debris_spent = 30000000;
            $moon_debris_left_percent = ($outcome[UBE_DEBRIS_TOTAL] - $moon_debris_spent) / $outcome[UBE_DEBRIS_TOTAL];

            $outcome[UBE_DEBRIS_TOTAL] = 0;
            foreach(array(RES_METAL, RES_CRYSTAL) as $resource_id) {
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
  // OK0
  function sn_ube_combat_analyze_loot() {
    $planet_resource_list = &$this->fleets[0][UBE_RESOURCES];
    $outcome = &$this->outcome;

    $planet_looted_in_metal = 0;
    $planet_resource_looted = array();
    $planet_resource_total = is_array($planet_resource_list) ? array_sum($planet_resource_list) : 0;
    if($planet_resource_total && ($total_capacity = array_sum($outcome[UBE_ATTACKERS][UBE_CAPACITY]))) {
      // Можно вывести только половину ресурсов, но не больше, чем общая вместимость флотов атакующих
      $planet_lootable = min($planet_resource_total / 2, $total_capacity);
      // Вычисляем процент вывоза. Каждого ресурса будет вывезено в одинаковых пропорциях
      $planet_lootable_percent = $planet_lootable / $planet_resource_total;

      // Вычисляем какой процент общей емкости трюмов атакующих будет задействован
      $total_lootable = min($planet_lootable, $total_capacity);

      // Вычисляем сколько ресурсов вывезено
      foreach($outcome[UBE_ATTACKERS][UBE_CAPACITY] as $fleet_id => $fleet_capacity) {
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
        $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOOTED] = $fleet_loot_data;
        $outcome[UBE_FLEETS][$fleet_id][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] += $looted_in_metal;
        $planet_looted_in_metal += $looted_in_metal;
      }
    }

    $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOST_IN_METAL][RES_METAL] -= $planet_looted_in_metal;
    $outcome[UBE_FLEETS][0][UBE_RESOURCES_LOOTED] = $planet_resource_looted;
  }


  // ------------------------------------------------------------------------------------------------
  // OK0
  function sn_ube_combat_analyze_moon_destroy() {
    $combat_data = &$this->combat_data;

    // TODO: $is_simulator
    $reapers = 0;
    foreach($this->rounds[count($this->rounds) - 1][UBE_FLEETS] as $fleet_data) {
      if($fleet_data[UBE_FLEET_INFO][UBE_FLEET_TYPE] == UBE_ATTACKERS) {
        foreach($fleet_data[UBE_COUNT] as $unit_id => $unit_count) {
          // TODO: Работа по группам - группа "Уничтожители лун"
          $reapers += ($unit_id == SHIP_HUGE_DEATH_STAR) ? $unit_count : 0;
        }
      }
    }

    $moon_size = $this->outcome[UBE_PLANET][PLANET_SIZE];
    if($reapers) {
      $random = mt_rand(1, 100);
      $this->outcome[UBE_MOON_DESTROY_CHANCE] = max(1, min(99, round((100 - sqrt($moon_size)) * sqrt($reapers))));
      $this->outcome[UBE_MOON_REAPERS_DIE_CHANCE] = round(sqrt($moon_size) / 2 + sqrt($reapers));
      $this->outcome[UBE_MOON] = $random <= $this->outcome[UBE_MOON_DESTROY_CHANCE] ? UBE_MOON_DESTROY_SUCCESS : UBE_MOON_DESTROY_FAILED;
      $random = mt_rand(1, 100);
      $this->outcome[UBE_MOON_REAPERS] = $random <= $this->outcome[UBE_MOON_REAPERS_DIE_CHANCE] ? UBE_MOON_REAPERS_DIED : UBE_MOON_REAPERS_RETURNED;
    } else {
      $this->outcome[UBE_MOON_REAPERS] = UBE_MOON_REAPERS_NONE;
    }
  }




































  // ------------------------------------------------------------------------------------------------
  /**
   * Записывает результат боя в БД
   *
   * @return mixed
   */
  // OK0
  function ube_combat_result_apply() {
    $destination_user_id = $this->fleets[0][UBE_OWNER];
    $destination_user_db_row = &$this->players[$destination_user_id][UBE_PLAYER_DATA];

    $outcome = &$this->outcome;
    $planet_info = &$outcome[UBE_PLANET];
    $planet_id = $planet_info[PLANET_ID];
    // Обновляем поле обломков на планете
    if(!$this->options[UBE_COMBAT_ADMIN] && !empty($outcome[UBE_DEBRIS])) {
      db_planet_set_by_gspt($planet_info[PLANET_GALAXY], $planet_info[PLANET_SYSTEM], $planet_info[PLANET_PLANET], PT_PLANET,
        "`debris_metal` = `debris_metal` + " . floor($outcome[UBE_DEBRIS][RES_METAL]) . ", `debris_crystal` = `debris_crystal` + " . floor($outcome[UBE_DEBRIS][RES_CRYSTAL])
      );
    }

    $fleet_group_id_list = array(); // Для САБов

    $fleets_outcome = &$outcome[UBE_FLEETS];
    foreach($this->fleets as $fleet_id => &$fleet_info) {
      $fleet_info[UBE_FLEET_GROUP] ? $fleet_group_id_list[$fleet_info[UBE_FLEET_GROUP]] = $fleet_info[UBE_FLEET_GROUP] : false;

      $this_fleet_outcome = &$fleets_outcome[$fleet_id];

      empty($fleet_info[UBE_COUNT]) ? $fleet_info[UBE_COUNT] = array() : false;
      empty($this_fleet_outcome[UBE_UNITS_LOST]) ? $this_fleet_outcome[UBE_UNITS_LOST] = array() : false;

      $ship_count_initial = array_sum($fleet_info[UBE_COUNT]);
      $ship_count_lost = array_sum($this_fleet_outcome[UBE_UNITS_LOST]); // Если будет сделано восстановлении более, чем начальное число - тут надо сделать сумму по модулю

      if($fleet_id) {
        // Флот
        $objFleet = new Fleet();
        $objFleet->set_db_id($fleet_id);

        // Если это была миссия Уничтожения И звезда смерти взорвалась И мы работаем с аттакерами - значит все аттакеры умерли
        if($fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS && $outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_DIED) {
          $ship_count_lost = $ship_count_initial;
        }

        if($ship_count_lost == $ship_count_initial) {
          $objFleet->method_db_fleet_delete();
        } else {
          if($ship_count_lost) {
            $fleet_real_array = array();
            // Просматриваем результаты изменения флотов
            foreach($fleet_info[UBE_COUNT] as $unit_id => $unit_count) {
              // Перебираем аутком на случай восстановления юнитов
              if($units_left = $unit_count - (float)$this_fleet_outcome[UBE_UNITS_LOST][$unit_id]) {
                $fleet_real_array[$unit_id] = $units_left;
              };
            }
            $objFleet->replace_ships($fleet_real_array);
          }

          $resource_delta_fleet = $this->ube_combat_result_calculate_resources($this_fleet_outcome);
          $objFleet->update_resources($resource_delta_fleet);

          // Если защитник и не РМФ - отправляем флот назад
          if(($fleet_info[UBE_FLEET_TYPE] == UBE_DEFENDERS && !$outcome[UBE_SFR]) || $fleet_info[UBE_FLEET_TYPE] == UBE_ATTACKERS) {
            $objFleet->mark_fleet_as_returned();
          }
          $objFleet->flush_changes_to_db();
        }

        unset($objFleet);
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
          db_planet_set_by_id($planet_id, implode(',', $temp));
        }

        if($ship_count_lost) {
          $db_changeset = array();
          foreach($this_fleet_outcome[UBE_UNITS_LOST] as $unit_id => $units_lost) {
            $db_changeset['unit'][] = sn_db_unit_changeset_prepare($unit_id, -$units_lost, $destination_user_db_row, $planet_id);
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

    if($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS) {
      $moon_row = uni_create_moon($planet_info[PLANET_GALAXY], $planet_info[PLANET_SYSTEM], $planet_info[PLANET_PLANET], $destination_user_id, $outcome[UBE_MOON_SIZE], '', false);
      $outcome[UBE_MOON_NAME] = $moon_row['name'];
      unset($moon_row);
    } elseif($outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS) {
      db_planet_delete_by_id($planet_id);
    }

    $bashing_list = array();
    foreach($this->players as $player_id => $player_info) {
      if(!$player_info[UBE_ATTACKER]) {
        continue;
      }
      if($outcome[UBE_MOON] != UBE_MOON_DESTROY_SUCCESS) {
        $bashing_list[] = "({$player_id}, {$planet_id}, {$this->combat_timestamp})";
      }
      if($this->options[UBE_MISSION_TYPE] == MT_ATTACK && $this->options[UBE_DEFENDER_ACTIVE]) {
        $str_loose_or_win = $outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN ? 'raidswin' : 'raidsloose';
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
    $combat_data = &$this->combat_data;

    global $lang;

    // TODO: Отсылать каждому игроку сообщение на его языке!

    $outcome = &$this->outcome;
    $planet_info = &$outcome[UBE_PLANET];

    // Генерируем текст письма
    $text_common = sprintf($lang['ube_report_msg_body_common'],
      date(FMT_DATE_TIME, $this->combat_timestamp),
      $lang['sys_planet_type_sh'][$planet_info[PLANET_TYPE]],
      $planet_info[PLANET_GALAXY],
      $planet_info[PLANET_SYSTEM],
      $planet_info[PLANET_PLANET],
      htmlentities($planet_info[PLANET_NAME], ENT_COMPAT, 'UTF-8'),
      $lang[$outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_WIN ? 'ube_report_info_outcome_win' :
        ($outcome[UBE_COMBAT_RESULT] == UBE_COMBAT_RESULT_DRAW ? 'ube_report_info_outcome_draw' : 'ube_report_info_outcome_loss')]
    );

    $text_defender = '';
    foreach($outcome[UBE_DEBRIS] as $resource_id => $resource_amount) {
      if($resource_id == RES_DEUTERIUM) {
        continue;
      }

      $text_defender .= "{$lang['tech'][$resource_id]}: " . pretty_number($resource_amount) . '<br />';
    }
    if($text_defender) {
      $text_defender = "{$lang['ube_report_msg_body_debris']}{$text_defender}<br />";
    }

    if($outcome[UBE_MOON] == UBE_MOON_CREATE_SUCCESS) {
      $text_defender .= "{$lang['ube_report_moon_created']} {$outcome[UBE_MOON_SIZE]} {$lang['sys_kilometers_short']}<br /><br />";
    } elseif($outcome[UBE_MOON] == UBE_MOON_CREATE_FAILED) {
      $text_defender .= "{$lang['ube_report_moon_chance']} {$outcome[UBE_MOON_CHANCE]}%<br /><br />";
    }

    if($this->options[UBE_MISSION_TYPE] == MT_DESTROY) {
      if($outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_NONE) {
        $text_defender .= $lang['ube_report_moon_reapers_none'];
      } else {
        $text_defender .= "{$lang['ube_report_moon_reapers_wave']}. {$lang['ube_report_moon_reapers_chance']} {$outcome[UBE_MOON_DESTROY_CHANCE]}%. ";
        $text_defender .= $lang[$outcome[UBE_MOON] == UBE_MOON_DESTROY_SUCCESS ? 'ube_report_moon_reapers_success' : 'ube_report_moon_reapers_failure'] . "<br />";

        $text_defender .= "{$lang['ube_report_moon_reapers_outcome']} {$outcome[UBE_MOON_REAPERS_DIE_CHANCE]}%. ";
        $text_defender .= $lang[$outcome[UBE_MOON_REAPERS] == UBE_MOON_REAPERS_RETURNED ? 'ube_report_moon_reapers_survive' : 'ube_report_moon_reapers_died'];
      }
      $text_defender .= '<br /><br />';
    }

    $text_defender .= "{$lang['ube_report_info_link']}: <a href=\"index.php?page=battle_report&cypher=$this->report_cypher\">{$this->report_cypher}</a>";

    // TODO: Оптимизировать отсылку сообщений - отсылать пакетами
    foreach($this->players as $player_id => $player_info) {
      $message = $text_common . ($outcome[UBE_SFR] && $player_info[UBE_ATTACKER] ? $lang['ube_report_msg_body_sfr'] : $text_defender);
      msg_send_simple_message($player_id, '', $this->combat_timestamp, MSG_TYPE_COMBAT, $lang['sys_mess_tower'], $lang['sys_mess_attack_report'], $message);
    }

  }


  // OK0
  function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender) {
    $combat_data = &$this->combat_data;

    $this->is_simulator = sys_get_param_int('simulator');
    $this->is_simulator = !empty($this->is_simulator);
    $this->options = array(
      UBE_MISSION_TYPE => MT_ATTACK,
    );

    $this->players = array();
    $this->fleets = array();

    $combat_data = array();

    $this->sn_ube_simulator_fill_side($sym_defender, false);
    $this->sn_ube_simulator_fill_side($sym_attacker, true);

    return ($combat_data);
  }

  // ------------------------------------------------------------------------------------------------
  // Преобразовывает данные симулятора в данные для расчета боя
  // OK0
  function sn_ube_simulator_fill_side($side_info, $attacker, $player_id = -1) {
    $combat_data = &$this->combat_data;
    global $ube_convert_techs;

    $player_id = $player_id == -1 ? count($this->players) : $player_id;

    foreach($side_info as $fleet_data) {
      $this->players[$player_id][UBE_NAME] = $attacker ? 'Attacker' : 'Defender';
      $this->players[$player_id][UBE_ATTACKER] = $attacker;

      $this->fleets[$player_id][UBE_OWNER] = $player_id;
      foreach($fleet_data as $unit_id => $unit_count) {
        if(!$unit_count) {
          continue;
        }

        $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);

        if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
          $this->fleets[$player_id][UBE_COUNT][$unit_id] = $unit_count;
        } elseif($unit_type == UNIT_RESOURCES) {
          $this->fleets[$player_id][UBE_RESOURCES][$unit_id] = $unit_count;
        } elseif($unit_type == UNIT_TECHNOLOGIES) {
          $this->players[$player_id][UBE_BONUSES][$ube_convert_techs[$unit_id]] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
        } elseif($unit_type == UNIT_GOVERNORS) {
          if($unit_id == MRC_FORTIFIER) {
            foreach($ube_convert_techs as $ube_id) {
              $this->fleets[$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            }
          }
        } elseif($unit_type == UNIT_MERCENARIES) {
          if($unit_id == MRC_ADMIRAL) {
            foreach($ube_convert_techs as $ube_id) {
              $this->players[$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            }
          }
        }
      }
    }
  }



  function set_option_from_config() {
    global $config;
    $this->options[UBE_METHOD] = $config->game_ube_method ? $config->game_ube_method : 0;
  }

  function get_time_spent() {
    return $this->combat_data[UBE_TIME_SPENT];
  }

  function get_cypher() {
    return $this->report_cypher;
  }


  /**
   * Статик кусок из flt_mission_attack()
   *
   * @param Mission $objMission
   * @param array $fleet_row
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

    return false;
  }

  static function sn_battle_report_view(&$template) {
    global $template_result, $lang;

    $ube_report = new UBEReport();
    $ube = $ube_report->sn_ube_report_load(sys_get_param_str('cypher')); //  $combat_data = sn_ube_report_load(sys_get_param_str('cypher'));
    if($ube != UBE_REPORT_NOT_FOUND) { //  if($combat_data != UBE_REPORT_NOT_FOUND) {
      $ube_report->sn_ube_report_generate($ube, $template_result); //    sn_ube_report_generate($combat_data, $template_result);

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

    if(sys_get_param_str('reload'))
    {
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

}
