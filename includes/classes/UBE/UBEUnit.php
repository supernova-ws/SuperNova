<?php

/**
 * Class UBEUnit
 */
class UBEUnit {
//  public $db_id = 0;
  public $unit_id = 0;
  public $count = 0;
  public $type = 0;

  public $capacity = 0; // UnitShip
  public $price = array(); // UnitBuildable
  public $amplify = array(); // UnitUBE ????


  public $attack_bonus = 0;
  public $shield_bonus = 0;
  public $armor_bonus = 0;

  public $unit_randomized_attack = 0;
  public $unit_randomized_shield = 0;
  public $unit_randomized_armor = 0;

  public $units_lost = 0; // Количество ПОТЕРЯННЫХ юнитов, т.е. уничтоженных и невосстановленных юнитов
  public $units_destroyed = 0; // Количество реально уничтоженных юнитов
  public $units_restored = 0;

  public $pool_attack = 0;
  public $pool_shield = 0;
  public $pool_armor = 0;

  public $unit_count_boom = 0;

  public $share_of_side_armor = 0;
  public $attack_income = 0;


  /**
   * @param Bonus $bonus
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function fill_unit_info(Bonus $bonus) {
    $unit_info = get_unit_param($this->unit_id);
    // Заполняем информацию о кораблях в информации флота
    $this->attack_bonus = floor($unit_info[P_ATTACK] * $bonus->calcBonus(P_ATTACK));
    $this->shield_bonus = floor($unit_info[P_SHIELD] * $bonus->calcBonus(P_SHIELD));
    $this->armor_bonus = floor($unit_info[P_ARMOR] * $bonus->calcBonus(P_ARMOR));

    $this->pool_armor = $this->armor_bonus * $this->count;

    $this->amplify = $unit_info[P_AMPLIFY];
    $this->capacity = $unit_info[P_CAPACITY];
    $this->type = $unit_info[P_UNIT_TYPE];
    $this->price[RES_METAL] = $unit_info[P_COST][RES_METAL];
    $this->price[RES_CRYSTAL] = $unit_info[P_COST][RES_CRYSTAL];
    $this->price[RES_DEUTERIUM] = $unit_info[P_COST][RES_DEUTERIUM];
    $this->price[RES_DARK_MATTER] = $unit_info[P_COST][RES_DARK_MATTER];
  }


  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function ube_analyze_unit($is_simulator) {
    // Вычисляем сколько юнитов осталось и сколько потеряно
//    $this->units_lost = $this->count - $UBERoundFleetCombat->unit_list[$this->unit_id]->count;
    $this->units_lost = $this->units_destroyed;

    // Восстановление обороны - 75% от уничтоженной
    $this->restore_unit($is_simulator);

    // Приводим количество юнитов к текущему состоянию - НИНАДА! $count у нас всегда актуальный
//    $this->count -= $this->units_lost;
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function restore_unit($is_simulator) {
    if($this->type != UNIT_DEFENCE || $this->units_lost <= 0) {
      return;
    }

    if($is_simulator) {
      $units_giveback = round($this->units_lost * UBE_DEFENCE_RESTORATION_CHANCE_AVG / 100); // for simulation just return 75% of loss
    } else {
      // Checking - should we trigger mass-restore
      if($this->units_lost >= UBE_DEFENCE_RESTORATION_MASS_COUNT) {
        // For large amount - mass-restoring defence
        $units_giveback = round($this->units_lost * mt_rand(UBE_DEFENCE_RESTORATION_CHANCE_MIN, UBE_DEFENCE_RESTORATION_CHANCE_MAX) / 100);
      } else {
        // For small amount - restoring defence per single unit
        $units_giveback = 0;
        for($i = 1; $i <= $this->units_lost; $i++) {
          if(mt_rand(1, 100) <= UBE_DEFENCE_RESTORATION_CHANCE_AVG) {
            $units_giveback++;
          }
        }
      }
    }

    $this->units_restored = $units_giveback;
    $this->units_lost -= $units_giveback;
    $this->count += $units_giveback;
  }

  /**
   * Готовит юнит к следующему раунду
   *
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function prepare_for_next_round($is_simulator) {
    // TODO:  Добавить процент регенерации щитов

    if($this->count <= 0) {
      return;
    }

    // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
    $this->unit_randomized_attack = floor($this->attack_bonus * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    $this->unit_randomized_shield = floor($this->shield_bonus * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    $this->unit_randomized_armor = floor($this->armor_bonus);// * ($is_simulator ? 1 : mt_rand(80, 120) / 100));

    $this->pool_attack = $this->unit_randomized_attack * $this->count;
    $this->pool_shield = $this->unit_randomized_shield * $this->count;

    $this->unit_count_boom = 0;
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function receive_damage($is_simulator) {
    // TODO - Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры
    if($this->count <= 0) {
      return;
    }

    $start_count = $this->count;

    // Общая защита одного юнита
    $pool_base_defence = $this->unit_randomized_shield + $this->unit_randomized_armor;

    // Вычисляем, сколько юнитов взорвалось полностью, но не больше, чем их осталось во флоте
    $units_lost = min(floor($this->attack_income / $pool_base_defence), $this->count); // $units_lost_full всегда не больше $this->count

    // Уменьшаем дамадж на ту же сумму
    $this->attack_income -= $units_lost * $pool_base_defence;

    // Уменьшаем общие щиты на щиты уничтоженных юнитов, но не больше, чем есть
    $this->pool_shield -= min($units_lost * $this->unit_randomized_shield, $this->pool_shield);
    // Уменьшаем общую броню на броню уничтоженных юнитов, но не больше, чем есть
    $this->pool_armor -= min($units_lost * $this->unit_randomized_armor, $this->pool_armor);
    // Вычитаем уничтоженные юниты из общего количества юнитов
    $this->count -= $units_lost;

    // Проверяем - не взорвался ли текущий юнит
    while($this->count > 0 && $this->attack_income > 0) {
      $this->attack_damaged_unit($is_simulator);
    }

    $this->units_destroyed += $start_count - $this->count;
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  function attack_damaged_unit($is_simulator) {
//    // Нет юнитов или не осталось атак - ничего не делаем
//    // Не нужно???????
//    if($this->count <= 0 || $this->attack_income <= 0) {
//      return;
//    }

    // Вычисляем остаток щитов на текущем корабле
    $shield_left = $this->pool_shield % $this->unit_randomized_shield;
    // Вычисляем остаток брони
    $armor_left = $this->pool_armor % $this->unit_randomized_armor;
    // Проверка - не атакуем ли мы целый корабль
    // Такое может быть, если на прошлой итерации поврежденный корабль был взорван и еще осталась входящяя атака
    if($shield_left == 0 && $armor_left == 0) {
      $shield_left = $this->unit_randomized_shield;
      $armor_left = $this->unit_randomized_armor;
    }

    // Сколько прошло дамаджа по щитам
    $damage_to_shield = min($shield_left, $this->attack_income);

    // Уменьшаем атаку на дамадж, поглощенный щитами
    $this->attack_income -= $damage_to_shield;

    // Вычитаем этот дамадж из щитов пула
    $this->pool_shield -= $damage_to_shield;
    // Если весь дамадж был поглощён щитами - выходим
    if($this->attack_income <= 0) {
      return;
    }


    // Сколько прошло дамаджа по броне
    $damage_to_armor = min($armor_left, $this->attack_income);

    // Уменьшаем атаку на дамадж, поглощенный бронёй
    $this->attack_income -= $damage_to_armor;

    // Вычитаем этот дамадж из брони пула
    $this->pool_armor -= $damage_to_armor;
    // Вычитаем дамадж из брони текущего корабля
    $armor_left -= $damage_to_armor;

    // Проверяем - осталась ли броня на текущем корабле и вааще
    if($this->pool_armor <= 0 || $armor_left <= 0) {
      // Не осталось - корабль уничтожен
      $this->count--;

      return;
    }

    // Броня осталась. Проверяем - не взорвался ли корабль
    $armor_left_percent = $armor_left / $this->unit_randomized_armor * 100;
    // Проверяем % здоровья
    // TODO - сделать динамический процент для каждого вида юнитов
    if($armor_left_percent <= UBE_CRITICAL_DAMAGE_THRESHOLD) {
      // Дамадж пошёл по структуре. Чем более поврежден юнит - тем больше шансов у него взорваться
      $random = $is_simulator ? UBE_CRITICAL_DAMAGE_THRESHOLD / 2 : mt_rand(0, 100);
      if(mt_rand(0, 100) >= $armor_left_percent) {
        $this->count--;
        $this->unit_count_boom++;

        return;
      }
    }
  }

  // DEBUG FUNCTIONS ***************************************************************************************************
  /**
   *
   *
   * @version 41a5.23
   */
  public static function unit_dump_footer() {
    print('</table><br>');
  }

  /**
   *
   *
   * @version 41a5.23
   */
  public static function unit_dump_header() {
    print('<table border="1">');
    print('<tr>');
    print('<th>desc</th>');
    print('<th>unit_id</th>');
    print('<th colspan="2">count</th>');
//  print('<th>type</th>');
//  print('<th>attack_bonus</th>');
//  print('<th>shield_bonus</th>');
//  print('<th>armor_bonus</th>');
//  print('<th>unit_randomized_attack</th>');
//  print('<th>unit_randomized_shield</th>');
//  print('<th>unit_randomized_armor</th>');
    print('<th colspan="2">units_destroyed</th>');
//  print('<th>pool_attack</th>');
    print('<th colspan="2">pool_shield</th>');
    print('<th colspan="2">pool_armor</th>');
    print('<th colspan="2">boom</th>');
    print('<th colspan="2">attack_income</th>');
//  print('<th>units_lost</th>');
//  print('<th>units_restored</th>');
//  print('<th>capacity</th>');
    print('<th>armor_share</th>');
    print('</tr>');
  }

  /**
   * @param string       $field
   * @param UBEUnit|null $before
   *
   * @version 41a5.23
   */
  function unit_dump_delta($field, UBEUnit $before = null) {
//  print("<td" . ($before != null ? ' colspan=2' : '') . ">");
    print("<td>");
    print(pretty_number($this->$field));
    print("</td>");
    print("<td>");
    if(!empty($before)) {
      print('' . pretty_number($this->$field - $before->$field) . '');
    }
    print("</td>");
  }

  /**
   * @param string       $desc
   * @param UBEUnit|null $before
   *
   * @version 41a5.23
   */
  function unit_dump($desc = '', UBEUnit $before = null) {
    global $lang;

    print('<tr align="right">');
    print("<td>{$desc}</td>");
    print("<td>[{$this->unit_id}]{$lang['tech_short'][$this->unit_id]}</td>");
//  print("<td>" . unit_dump_delta($current, 'count', $before) . "</td>");
    $this->unit_dump_delta('count', $before);
//  print("<td>" . $this->type . "</td>");
//  print("<td>" . $this->attack_bonus . "</td>");
//  print("<td>" . $this->shield_bonus . "</td>");
//  print("<td>" . $this->armor_bonus . "</td>");
//  print("<td>" . $this->unit_randomized_attack . "</td>");
//  print("<td>" . $this->unit_randomized_shield . "</td>");
//  print("<td>" . $this->unit_randomized_armor . "</td>");
    $this->unit_dump_delta('units_destroyed', $before);
//  $this->unit_dump_delta($this, 'pool_attack', $before);
    $this->unit_dump_delta('pool_shield', $before);
    $this->unit_dump_delta('pool_armor', $before);
    $this->unit_dump_delta('unit_count_boom', $before);
    $this->unit_dump_delta('attack_income', $before);
//  print("<td>" . $this->units_lost . "</td>");
//  print("<td>" . $this->units_restored . "</td>");
//  print("<td>" . $this->capacity . "</td>");
    print("<td>" . round($this->share_of_side_armor, 4) . "</td>");
    print('</tr>');
  }

}
