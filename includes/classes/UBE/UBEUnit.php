<?php

/**
 * Class UBEUnit
 */
class UBEUnit extends Unit {
//  public $db_id = 0;
//  public $unitId = 0;
//  public $count = 0;
//  public $type = 0;

  // Passport per unit values
  public $capacity = 0; // UnitShip
  public $price = array(); // UnitBuildable
  public $amplify = array(); // UnitUBE ????

  // Bonus per unit values
  public $bonus_attack = 0;
  public $bonus_shield = 0;
  public $bonus_armor = 0;

  // Randomized per unit values
  public $randomized_attack = 0;
  public $randomized_shield = 0;
  public $randomized_armor = 0;

  // Unit pool actual values
  public $pool_attack = 0;
  public $pool_shield = 0;
  public $pool_armor = 0;

  public $units_destroyed = 0; // Количество уничтоженных юнитов во время боя ДО восстановления
  public $units_restored = 0; // Количество восстановленных юнитов
  public $units_lost = 0; // Количество ПОТЕРЯННЫХ юнитов, т.е. уничтоженных и невосстановленных юнитов
  public $unit_count_boom = 0;

  public $share_of_side_armor = 0;
  public $attack_income = 0;

//  /**
//   * @var Bonus $unit_bonus
//   */
//  public $unit_bonus = null;

  public function __construct() {
    parent::__construct();
  }

  public function setUnitId($unitId) {
    parent::setUnitId($unitId);

    // Reset combat stats??
    if($this->unitId) {
      $this->amplify = $this->info[P_AMPLIFY];
      $this->capacity = $this->info[P_CAPACITY];
      $this->price[RES_METAL] = $this->info[P_COST][RES_METAL];
      $this->price[RES_CRYSTAL] = $this->info[P_COST][RES_CRYSTAL];
      $this->price[RES_DEUTERIUM] = $this->info[P_COST][RES_DEUTERIUM];
      $this->price[RES_DARK_MATTER] = $this->info[P_COST][RES_DARK_MATTER];

      $this->calc_unit_combat_stats();
    }
  }

  // TODO - Make version taht will not destroy pool data - for count change inside one round
  public function setUnitCount($unit_count) {
    $this->count = $unit_count;
    $this->calc_pool_stats();
  }

  public function calc_unit_combat_stats($is_simulator = false) {
    $this->bonus_attack = $this->info[P_ATTACK];
    $this->bonus_shield = $this->info[P_SHIELD];
    $this->bonus_armor = $this->info[P_ARMOR];
    if(is_object($this->unit_bonus)) {
      $this->bonus_attack = floor($this->bonus_attack * $this->unit_bonus->calcBonus(P_ATTACK));
      $this->bonus_shield = floor($this->bonus_shield * $this->unit_bonus->calcBonus(P_SHIELD));
      $this->bonus_armor = floor($this->bonus_armor * $this->unit_bonus->calcBonus(P_ARMOR));
    }
    $this->randomized_attack = $this->bonus_attack;
    $this->randomized_shield = $this->bonus_shield;
    $this->randomized_armor = $this->bonus_armor;
    if(!$is_simulator) {
      // TODO - randomize attack if is not simulator
      $this->randomized_attack = floor($this->bonus_attack * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
      $this->randomized_shield = floor($this->bonus_shield * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
      $this->randomized_armor = floor($this->bonus_armor * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    }

    $this->calc_pool_stats();
  }

  /**
   *
   * @version 41a6.10
   */
  public function calc_pool_stats() {
    // Заполняем информацию о кораблях в информации флота
    $this->pool_attack = $this->randomized_attack * $this->getCount();
    $this->pool_shield = $this->randomized_shield * $this->getCount();
    $this->pool_armor = $this->randomized_armor * $this->getCount();
  }

  /**
   * Готовит юнит к следующему раунду
   *
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   * @see UBEUnitList::prepare_for_next_round
   */
  public function prepare_for_next_round($is_simulator) {
    // TODO:  Добавить процент регенерации щитов
//    if($this->count <= 0) {
//      return;
//    }

    // Для не-симулятора - рандомизируем каждый раунд значения атаки и щитов
    $this->randomized_attack = floor($this->bonus_attack * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));
    $this->randomized_shield = floor($this->bonus_shield * ($is_simulator ? 1 : mt_rand(UBE_RANDOMIZE_FROM, UBE_RANDOMIZE_TO) / 100));

    $this->pool_attack = $this->randomized_attack * $this->getCount();
    $this->pool_shield = $this->randomized_shield * $this->getCount();

    $this->unit_count_boom = 0;
  }

  /**
   * Инициализирует боевую информацию юнита - однократно
   *
   * @see addBonus::mergeBonus
   *
   * @param Bonus $bonus
   *
   * @version 41a6.10
   */
  public function addBonus(Bonus $bonus) {
    $this->unit_bonus->mergeBonus($bonus);
    $this->calc_unit_combat_stats();
  }


  /**
   * @param bool $is_simulator
   *
   * @version 41a6.10
   */
  public function ube_analyze_unit($is_simulator) {
    // Вычисляем сколько юнитов осталось и сколько потеряно
    $this->units_lost = $this->units_destroyed;

    // Восстановление обороны - 75% от уничтоженной
    $this->restore_unit($is_simulator);
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function restore_unit($is_simulator) {
    if($this->getType() != UNIT_DEFENCE || $this->units_lost <= 0) {
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
    $this->adjustCount($units_giveback);
  }

  /**
   * @param bool $is_simulator
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function receive_damage($is_simulator) {
    // TODO - Добавить взрывы от полуповрежденных юнитов - т.е. заранее вычислить из убитых юнитов еще количество убитых умножить на вероятность от структуры
    if($this->getCount() <= 0) {
      return;
    }

    $start_count = $this->getCount();

    // Общая защита одного юнита
    $pool_base_defence = $this->randomized_shield + $this->randomized_armor;

    // Вычисляем, сколько юнитов взорвалось полностью, но не больше, чем их осталось во флоте
    $units_lost = min(floor($this->attack_income / $pool_base_defence), $this->getCount()); // $units_lost_full всегда не больше $this->count

    // Уменьшаем дамадж на ту же сумму
    $this->attack_income -= $units_lost * $pool_base_defence;

    // Уменьшаем общие щиты на щиты уничтоженных юнитов, но не больше, чем есть
    $this->pool_shield -= min($units_lost * $this->randomized_shield, $this->pool_shield);
    // Уменьшаем общую броню на броню уничтоженных юнитов, но не больше, чем есть
    $this->pool_armor -= min($units_lost * $this->randomized_armor, $this->pool_armor);
    // Вычитаем уничтоженные юниты из общего количества юнитов
    $this->adjustCount(-$units_lost);

    // Проверяем - не взорвался ли текущий юнит
    while($this->getCount() > 0 && $this->attack_income > 0) {
      $this->attack_damaged_unit($is_simulator);
    }

    $this->units_destroyed += $start_count - $this->getCount();
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
    $shield_left = $this->pool_shield % $this->randomized_shield;
    // Вычисляем остаток брони
    $armor_left = $this->pool_armor % $this->randomized_armor;
    // Проверка - не атакуем ли мы целый корабль
    // Такое может быть, если на прошлой итерации поврежденный корабль был взорван и еще осталась входящяя атака
    if($shield_left == 0 && $armor_left == 0) {
      $shield_left = $this->randomized_shield;
      $armor_left = $this->randomized_armor;
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
      $this->adjustCount(-1);

      return;
    }

    // Броня осталась. Проверяем - не взорвался ли корабль
    $armor_left_percent = $armor_left / $this->randomized_armor * 100;
    // Проверяем % здоровья
    // TODO - сделать динамический процент для каждого вида юнитов
    if($armor_left_percent <= UBE_CRITICAL_DAMAGE_THRESHOLD) {
      // Дамадж пошёл по структуре. Чем более поврежден юнит - тем больше шансов у него взорваться
      $random = $is_simulator ? UBE_CRITICAL_DAMAGE_THRESHOLD / 2 : mt_rand(0, 100);
      if($random >= $armor_left_percent) {
        $this->adjustCount(-1);
        $this->unit_count_boom++;

        return;
      }
    }
  }

}
