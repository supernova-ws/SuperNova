<?php

class UBERound {

  public $round_number = 0;

  /**
   * [$fleet_id][UBE_COUNT/UBE_ARMOR/UBE_ARMOR_REST/...] [$unit_id]
   *
   * set_first_round
   * UBE_COUNT
   * UBE_ARMOR
   * UBE_ARMOR_REST
   * UBE_SHIELD_REST
   *
   * UBE_FLEET_INFO // UBE_ATTACKERS // UBE_DEFENDERS
   *
   * UBE_ATTACK_BASE
   * UBE_SHIELD_BASE
   * UBE_ARMOR_BASE
   * UBE_ATTACK
   * UBE_SHIELD
   * UBE_SHIELD_REST
   *
   * UBE_DAMAGE_PERCENT
   *
   * @var array
   */
  public $round_fleets = array(); // [UBE_FLEETS] // rounds_old[$round][UBE_FLEETS]

  /**
   * @var UBEFleet[]
   */
  public $fleet_info = array(); // [UBE_FLEET_INFO]

  // [$bonus_id][$fleet_id]
  /**
   * [UBE_ATTACK][$fleet_id]
   * [UBE_SHIELD][$fleet_id]
   * [UBE_ARMOR][$fleet_id]
   *
   * @var array
   */
  public $fleet_stat_list_attackers = array(); // [$fleet_type] // $fleets[$fleet_id][UBE_FLEET_TYPE]
  // [$bonus_id][$fleet_id]
  public $fleet_stat_list_defenders = array(); // [$fleet_type] // $fleets[$fleet_id][UBE_FLEET_TYPE]

  public $UBE_TOTAL = array(); // [UBE_FLEET_INFO][UBE_ASA]

  public $UBE_OUTCOME = UBE_COMBAT_RESULT_DRAW;

  public function __construct() {
    $this->round_fleets = array();
  }

  public function __clone() {

  }

  public function set_first_round($fleet_id, $unit_id, UBEFleet $objFleet) {
    $this->round_fleets[$fleet_id][UBE_COUNT] [$unit_id] = $objFleet->UBE_COUNT [$unit_id]; // $first_round_data[$fleet_id][UBE_COUNT][$unit_id] = $unit_count;
    $this->round_fleets[$fleet_id][UBE_ARMOR] [$unit_id] = $objFleet->UBE_ARMOR [$unit_id] * $objFleet->UBE_COUNT[$unit_id]; // $first_round_data[$fleet_id][UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id] * $unit_count;
    $this->round_fleets[$fleet_id][UBE_ARMOR_REST] [$unit_id] = $objFleet->UBE_ARMOR [$unit_id]; // $first_round_data[$fleet_id][UBE_ARMOR_REST][$unit_id] = $fleet_info[UBE_ARMOR][$unit_id];
    $this->round_fleets[$fleet_id][UBE_SHIELD_REST][$unit_id] = $objFleet->UBE_SHIELD[$unit_id]; // $first_round_data[$fleet_id][UBE_SHIELD_REST][$unit_id] = $fleet_info[UBE_SHIELD][$unit_id];
  }


  /**
   * @param UBEFleetList $fleets
   * @param              $is_simulator
   */
  public function sn_ube_combat_round_prepare(UBEFleetList $fleets, $is_simulator) {
    foreach($this->round_fleets as $fleet_id => $temp) {
      // Кэшируем переменные для легкого доступа к подмассивам
      $this->fleet_info[$fleet_id] = $fleets[$fleet_id];

      foreach($this->round_fleets[$fleet_id][UBE_COUNT] as $unit_id => $unit_count) {
        if($unit_count <= 0) {
          continue;
        }

// TODO:  Добавить процент регенерации щитов

        // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
        $this->round_fleets[$fleet_id][UBE_ATTACK_BASE][$unit_id] = floor($this->fleet_info[$fleet_id]->UBE_ATTACK[$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
        $this->round_fleets[$fleet_id][UBE_SHIELD_BASE][$unit_id] = floor($this->fleet_info[$fleet_id]->UBE_SHIELD[$unit_id] * ($is_simulator ? 1 : mt_rand(80, 120) / 100));
        $this->round_fleets[$fleet_id][UBE_ARMOR_BASE][$unit_id] = floor($this->fleet_info[$fleet_id]->UBE_ARMOR[$unit_id]);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

        $this->round_fleets[$fleet_id][UBE_ATTACK][$unit_id] = $this->round_fleets[$fleet_id][UBE_ATTACK_BASE][$unit_id] * $unit_count;
        $this->round_fleets[$fleet_id][UBE_SHIELD][$unit_id] = $this->round_fleets[$fleet_id][UBE_SHIELD_BASE][$unit_id] * $unit_count;
        $this->round_fleets[$fleet_id][UBE_SHIELD_REST][$unit_id] = $this->round_fleets[$fleet_id][UBE_SHIELD_BASE][$unit_id];
        // $fleet_data[UBE_SHIELD][$unit_id] = $fleet_data[UBE_SHIELD_BASE][$unit_id] * ($combat_data[UBE_OPTIONS][UBE_METHOD] ? $unit_count : 1);
        // $fleet_data[UBE_ARMOR][$unit_id] = $fleet_info[UBE_ARMOR_BASE][$unit_id] * $unit_count;
      }

      // Суммируем данные по флоту
      if($this->fleet_info[$fleet_id]->is_attacker == UBE_PLAYER_IS_ATTACKER) {
        $this->fleet_stat_list_attackers[UBE_ATTACK][$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_ATTACK]) ? array_sum($this->round_fleets[$fleet_id][UBE_ATTACK]) : 0;
        $this->fleet_stat_list_attackers[UBE_SHIELD][$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_SHIELD]) ? array_sum($this->round_fleets[$fleet_id][UBE_SHIELD]) : 0;
        $this->fleet_stat_list_attackers[UBE_ARMOR] [$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_ARMOR]) ? array_sum($this->round_fleets[$fleet_id][UBE_ARMOR]) : 0;
      } else {
        $this->fleet_stat_list_defenders[UBE_ATTACK][$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_ATTACK]) ? array_sum($this->round_fleets[$fleet_id][UBE_ATTACK]) : 0;
        $this->fleet_stat_list_defenders[UBE_SHIELD][$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_SHIELD]) ? array_sum($this->round_fleets[$fleet_id][UBE_SHIELD]) : 0;
        $this->fleet_stat_list_defenders[UBE_ARMOR] [$fleet_id] += is_array($this->round_fleets[$fleet_id][UBE_ARMOR]) ? array_sum($this->round_fleets[$fleet_id][UBE_ARMOR]) : 0;
      }
    }

    // Суммируем данные по атакующим и защитникам
//    foreach($ube_combat_bonus_list as $bonus_id) {
//      $this->rounds_old[$round][UBE_TOTAL][UBE_DEFENDERS][$bonus_id] = array_sum($this->rounds_old[$round][UBE_DEFENDERS][$bonus_id]);
//      $this->rounds_old[$round][UBE_TOTAL][UBE_ATTACKERS][$bonus_id] = array_sum($this->rounds_old[$round][UBE_ATTACKERS][$bonus_id]);
//    }
    $this->UBE_TOTAL[UBE_PLAYER_IS_DEFENDER][UBE_ATTACK] = array_sum($this->fleet_stat_list_defenders[UBE_ATTACK]);
    $this->UBE_TOTAL[UBE_PLAYER_IS_DEFENDER][UBE_SHIELD] = array_sum($this->fleet_stat_list_defenders[UBE_SHIELD]);
    $this->UBE_TOTAL[UBE_PLAYER_IS_DEFENDER][UBE_ARMOR] = array_sum($this->fleet_stat_list_defenders[UBE_ARMOR]);

    $this->UBE_TOTAL[UBE_PLAYER_IS_ATTACKER][UBE_ATTACK] = array_sum($this->fleet_stat_list_attackers[UBE_ATTACK]);
    $this->UBE_TOTAL[UBE_PLAYER_IS_ATTACKER][UBE_SHIELD] = array_sum($this->fleet_stat_list_attackers[UBE_SHIELD]);
    $this->UBE_TOTAL[UBE_PLAYER_IS_ATTACKER][UBE_ARMOR] = array_sum($this->fleet_stat_list_attackers[UBE_ARMOR]);

    // Высчитываем долю атаки, приходящейся на юнит равную отношению брони юнита к общей броне - крупные цели атакуют чаще
    foreach($this->round_fleets as $fleet_id => &$fleet_data) {
      $fleet_type = $this->fleet_info[$fleet_id]->is_attacker;
      foreach($this->round_fleets[$fleet_id][UBE_COUNT] as $unit_id => $unit_count) {
        $this->round_fleets[$fleet_id][UBE_DAMAGE_PERCENT][$unit_id] = $this->round_fleets[$fleet_id][UBE_ARMOR][$unit_id] / $this->UBE_TOTAL[$fleet_type][UBE_ARMOR];
      }
    }
  }

  // Рассчитывает результат столкновения флотов ака раунд
  // OK0
  function sn_ube_combat_round_crossfire_fleet(UBE $ube) {
    if(BE_DEBUG === true) {
      // sn_ube_combat_helper_round_header($round);
    }

    // Проводим бой. Сталкиваем каждый корабль атакующего с каждым кораблем атакуемого
    foreach($this->fleet_stat_list_attackers[UBE_ATTACK] as $attack_fleet_id => $temp) {
      $attack_fleet_data = &$this->round_fleets[$attack_fleet_id];
      foreach($this->fleet_stat_list_defenders[UBE_ATTACK] as $defend_fleet_id => $temp2) {
        $defend_fleet_data = &$this->round_fleets[$defend_fleet_id];

        foreach($attack_fleet_data[UBE_COUNT] as $attack_unit_id => $attack_unit_count) {
          // if($attack_unit_count <= 0) continue; // TODO: Это пока нельзя включать - вот если будут "боевые порядки юнитов..."
          foreach($defend_fleet_data[UBE_COUNT] as $defend_unit_id => $defend_unit_count) {
            $this->sn_ube_combat_round_crossfire_unit2($attack_fleet_data, $defend_fleet_data, $attack_unit_id, $defend_unit_id, $ube, $attack_fleet_id);
            $this->sn_ube_combat_round_crossfire_unit2($defend_fleet_data, $attack_fleet_data, $defend_unit_id, $attack_unit_id, $ube, $defend_fleet_id);
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
  function sn_ube_combat_round_crossfire_unit2(&$attack_fleet_data, &$defend_fleet_data, $attack_unit_id, $defend_unit_id, UBE $ube, $attack_fleet_id) {
    if($defend_fleet_data[UBE_COUNT][$defend_unit_id] <= 0) {
      return;
    }

    // Вычисляем прямой дамадж от атакующего юнита с учетом размера атакуемого
    $direct_damage = floor($attack_fleet_data[UBE_ATTACK][$attack_unit_id] * $defend_fleet_data[UBE_DAMAGE_PERCENT][$defend_unit_id]);

    // Применяем амплифай, если есть
    $amplify = $this->fleet_info[$attack_fleet_id]->UBE_AMPLIFY[$attack_unit_id][$defend_unit_id];
    $amplify = $amplify ? $amplify : 1;
    $amplified_damage = floor($direct_damage * $amplify);

    // Проверяем - не взорвался ли текущий юнит
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed, $ube);

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
    $this->sn_ube_combat_round_crossfire_unit_damage_current($defend_fleet_data, $defend_unit_id, $amplified_damage, $units_lost, $units_boomed, $ube);
  }

  // OK0
  function sn_ube_combat_round_crossfire_unit_damage_current(&$defend_fleet_data, $defend_unit_id, &$amplified_damage, &$units_lost, &$units_boomed, UBE $ube) {
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

        $random = $ube->is_simulator ? $boom_limit / 2 : mt_rand(0, 100);
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
    $this->UBE_OUTCOME = UBE_COMBAT_RESULT_DRAW;

    $outcome = array();
    $next_round_fleet = array();
    $nextRound = null;
    foreach($this->round_fleets as $fleet_id => &$fleet_data) {
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
        $outcome[$this->fleet_info[$fleet_id]->is_attacker] = 1;
      }
    }

    // Проверяем результат боя
    if(count($outcome) == 0 || $round == 10) {
      // Если кого-то не осталось или не осталось обоих - заканчиваем цикл
      $round_data[UBE_OUTCOME] = UBE_COMBAT_RESULT_DRAW_END;
    } elseif(count($outcome) == 1) {
      // Если осталась одна сторона - она и выиграла
      $round_data[UBE_OUTCOME] = isset($outcome[UBE_PLAYER_IS_ATTACKER]) ? UBE_COMBAT_RESULT_WIN : UBE_COMBAT_RESULT_LOSS;
    } elseif(count($outcome) == 2) {
      if($round < 10) {
        $nextRound = new UBERound();
        $nextRound->round_fleets = $next_round_fleet;
      }
    }

    return $nextRound;
  }


  /**
   * @param $round_row
   * @param $is_attacker
   */
  public function load_from_report($round_row, $is_attacker) {
    $this->round_number = $round_row['ube_report_unit_round'];
    $fleet_id = $round_row['ube_report_unit_fleet_id'];

    if($is_attacker == UBE_PLAYER_IS_ATTACKER) {
      $this->fleet_stat_list_attackers[UBE_ATTACK][$fleet_id] = 0;
    } else {
      $this->fleet_stat_list_defenders[UBE_ATTACK][$fleet_id] = 0;
    }

    if(!isset($this->round_fleets[$fleet_id])) {
      $this->round_fleets[$fleet_id] = array();
    }

    $unit_id = $round_row['ube_report_unit_unit_id'];
    $this->round_fleets[$fleet_id][UBE_COUNT][$unit_id] = $round_row['ube_report_unit_count'];
    $this->round_fleets[$fleet_id][UBE_UNITS_BOOM][$unit_id] = $round_row['ube_report_unit_boom'];

    $this->round_fleets[$fleet_id][UBE_ATTACK][$unit_id] = $round_row['ube_report_unit_attack'];
    $this->round_fleets[$fleet_id][UBE_SHIELD][$unit_id] = $round_row['ube_report_unit_shield'];
    $this->round_fleets[$fleet_id][UBE_ARMOR][$unit_id] = $round_row['ube_report_unit_armor'];

    $this->round_fleets[$fleet_id][UBE_ATTACK_BASE][$unit_id] = $round_row['ube_report_unit_attack_base'];
    $this->round_fleets[$fleet_id][UBE_SHIELD_BASE][$unit_id] = $round_row['ube_report_unit_shield_base'];
    $this->round_fleets[$fleet_id][UBE_ARMOR_BASE][$unit_id] = $round_row['ube_report_unit_armor_base'];

  }



  // REPORT RENDER *****************************************************************************************************
  /**
   * @param UBE      $ube
   * @param UBERound $previousRound
   *
   * @return array
   */
  public function report_render_round($ube, $previousRound) {
    $fleet_list_template = array(
      true  => array(),
      false => array(),
    );

    foreach($this->round_fleets[UBE_ATTACK] as $fleet_id => $temp) {
      $fleet_owner_id = $ube->fleet_list[$fleet_id]->UBE_OWNER;

      $fleet_template = $this->report_render_round_fleet(
        $fleet_id,
        $previousRound,
        $ube->players[$fleet_owner_id],
        $ube->fleet_list[$fleet_id]->UBE_PLANET
      );

      $fleet_list_template[$fleet_template['IS_ATTACKER']][] = $fleet_template;
    }

    // Здесь аттакеры и дефендеры вперемешку. Сортируем аттакер->дефендер
    return array_merge($fleet_list_template[true], $fleet_list_template[false]);
  }

  /**
   * @param           $fleet_id
   * @param UBERound  $previousRound
   * @param UBEPlayer $UBEPlayer
   * @param           $planet_ube_row
   *
   * @return array
   */
  protected function report_render_round_fleet($fleet_id, UBERound $previousRound, UBEPlayer $UBEPlayer, $planet_ube_row) {
    global $lang;

    $side = $UBEPlayer->player_side_get();

    $template_fleet = array(
      'ID'          => $fleet_id,
      'IS_ATTACKER' => $side == UBE_PLAYER_IS_ATTACKER,
      'PLAYER_NAME' => $UBEPlayer->player_name_get(true),
    );

    if(is_array($planet_ube_row)) {
      $template_fleet += $planet_ube_row;
      $template_fleet[PLANET_NAME] = $template_fleet[PLANET_NAME] ? htmlentities($template_fleet[PLANET_NAME], ENT_COMPAT, 'UTF-8') : '';
      $template_fleet['PLANET_TYPE_TEXT'] = $lang['sys_planet_type_sh'][$template_fleet['PLANET_TYPE']];
    }

    $template_fleet['.']['ship'] = $this->report_render_round_fleet_ships($this->round_fleets[$fleet_id], $previousRound->round_fleets[$fleet_id]);

    return $template_fleet;
  }

  /**
   * @param $current_data
   * @param $previous_data
   *
   * @return array
   */
  protected function report_render_round_fleet_ships($current_data, $previous_data) {
    global $lang;

    $template_ships = array();
    foreach($current_data[UBE_COUNT] as $unit_id => $unit_count) {
      $shields_original = $current_data[UBE_SHIELD_BASE][$unit_id] * $previous_data[UBE_COUNT][$unit_id];

      $template_ships[] = array(
        'ID'          => $unit_id,
        'NAME'        => $lang['tech'][$unit_id],
        'ATTACK'      => pretty_number($current_data[UBE_ATTACK][$unit_id]),
        'SHIELD'      => pretty_number($shields_original),
        'SHIELD_LOST' => pretty_number($shields_original - $current_data[UBE_SHIELD][$unit_id]),
        'ARMOR'       => pretty_number($previous_data[UBE_ARMOR][$unit_id]),
        'ARMOR_LOST'  => pretty_number($previous_data[UBE_ARMOR][$unit_id] - $current_data[UBE_ARMOR][$unit_id]),
        'UNITS'       => pretty_number($previous_data[UBE_COUNT][$unit_id]),
        'UNITS_LOST'  => pretty_number($previous_data[UBE_COUNT][$unit_id] - $current_data[UBE_COUNT][$unit_id]),
        'UNITS_BOOM'  => pretty_number($current_data[UBE_UNITS_BOOM][$unit_id]),
      );
    }

    return $template_ships;
  }

}
