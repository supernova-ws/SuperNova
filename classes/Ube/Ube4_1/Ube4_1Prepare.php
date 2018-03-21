<?php
/**
 * Created by Gorlum 13.02.2018 8:17
 */

namespace Ube\Ube4_1;

use Planet\DBStaticPlanet;

class Ube4_1Prepare {
  const CONVERT_TECHS = [
    TECH_WEAPON => UBE_ATTACK,
    TECH_ARMOR  => UBE_ARMOR,
    TECH_SHIELD => UBE_SHIELD,
  ];
  const CONVERT_UNIT_PARAMS = [
    UBE_ATTACK => 'attack',
    UBE_ARMOR  => 'armor',
    UBE_SHIELD => 'shield',
  ];

  /**
   * Заполняет начальные данные по данным миссии
   *
   * @param $mission_data
   *
   * @return array
   */
  public function prepareFromMissionArray(&$mission_data, &$fleet_list_on_hold, $acs_fleet_list) {
    /*
    UBE_OPTIONS[UBE_LOADED]
    UBE_OPTIONS[UBE_SIMULATOR_STATIC]
    UBE_OPTIONS[UBE_EXCHANGE]
    UBE_OPTIONS[UBE_MOON_WAS]
    */

    $fleet_row = &$mission_data['fleet'];
    $destination_planet = &$mission_data['dst_planet'];

    $ube_time = $fleet_row['fleet_start_time'];
    $combat_data = [
      UBE_TIME           => $ube_time,
      UBE_OBJ_PREPARATOR => $this,
    ];
    // TODO: Не допускать атаки игроком своих же флотов - т.е. холд против атаки
    // Готовим инфу по атакуемой планете
    $this->ube_attack_prepare_planet($combat_data, $destination_planet);

    // Готовим инфу по удержанию
    foreach ($fleet_list_on_hold as $fleet) {
      $this->ube_attack_prepare_fleet($combat_data, $fleet, false);
    }

    // Готовим инфу по атакующим
    foreach ($acs_fleet_list as $fleet) {
      $this->ube_attack_prepare_fleet($combat_data, $fleet, true);
    }

    // Готовим опции
    $combat_data[UBE_OPTIONS][UBE_MOON_WAS] = $destination_planet['planet_type'] == PT_MOON || is_array(DBStaticPlanet::db_planet_by_parent($destination_planet['id'], true, '`id`'));
    $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] = $fleet_row['fleet_mission'];
    global $config;
    $combat_data[UBE_OPTIONS][UBE_METHOD] = $config->game_ube_method ? $config->game_ube_method : 0;

    $this->sn_ube_combat_prepare_first_round($combat_data);

    return $combat_data;
  }

  // ------------------------------------------------------------------------------------------------
  // Заполняет данные по игроку
  protected function ube_attack_prepare_player(&$combat_data, $player_id, $is_attacker) {
    if (!isset($combat_data[UBE_PLAYERS][$player_id])) {
      $combat_data[UBE_PLAYERS][$player_id] = [
        UBE_ATTACKER => $is_attacker,
      ];
      $player_info = &$combat_data[UBE_PLAYERS][$player_id];

      $player_data = db_user_by_id($player_id, true);
      $player_info[UBE_NAME] = $player_data['username'];
      $player_info[UBE_AUTH_LEVEL] = $player_data['authlevel'];
      $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] = $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] || $player_data['authlevel']; // Участвует ли админ в бою?
      $player_info[UBE_PLAYER_DATA] = $player_data;

      $admiral_bonus = mrc_get_level($player_data, false, MRC_ADMIRAL) * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
      foreach (static::CONVERT_TECHS as $unit_id => $ube_id) {
        $player_info[UBE_BONUSES][$ube_id] += mrc_get_level($player_data, [], $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $admiral_bonus;
      }
    } else {
      $combat_data[UBE_PLAYERS][$player_id][UBE_ATTACKER] = $combat_data[UBE_PLAYERS][$player_id][UBE_ATTACKER] || $is_attacker;
    }
  }


  /**
   * Заполняет данные по флоту
   *
   * Через жопу для сохранения обратной совместимости
   *
   * @param $combat_data
   * @param $fleet
   * @param $is_attacker
   *
   * @deprecated
   */
  public function ube_attack_prepare_fleet(&$combat_data, &$fleet, $is_attacker) {
    $fleet_owner_id = $fleet['fleet_owner'];
    $fleet_id = $fleet['fleet_id'];

    $this->ube_attack_prepare_player($combat_data, $fleet_owner_id, $is_attacker);

    $fleet_data = sys_unit_str2arr($fleet['fleet_array']);

    $combat_data[UBE_FLEETS][$fleet_id][UBE_OWNER] = $fleet_owner_id;
    $fleet_info = &$combat_data[UBE_FLEETS][$fleet_id];
    $fleet_info[UBE_FLEET_GROUP] = $fleet['fleet_group'];
    foreach ($fleet_data as $unit_id => $unit_count) {
      if (!$unit_count) {
        continue;
      }

      $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);
      if ($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
        $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
      }
    }

    $fleet_info[UBE_RESOURCES] = array(
      RES_METAL     => $fleet['fleet_resource_metal'],
      RES_CRYSTAL   => $fleet['fleet_resource_crystal'],
      RES_DEUTERIUM => $fleet['fleet_resource_deuterium'],
    );

    $fleet_info[UBE_PLANET] = array(
      // TODO: Брать имя и кэшировать ИД и имя планеты?
      PLANET_GALAXY => $fleet['fleet_start_galaxy'],
      PLANET_SYSTEM => $fleet['fleet_start_system'],
      PLANET_PLANET => $fleet['fleet_start_planet'],
      PLANET_TYPE   => $fleet['fleet_start_type'],
    );

    // Calling other functions in call-chain
    // TODO - THIS IS WRONG! YOU SHOULD NEVER DO THIS! THIS IS DIRTY HACK!
    sn_function_call('ube_attack_prepare_fleet', array(&$combat_data, &$fleet, $is_attacker));
  }

  /**
   * Заполняет данные по планете
   *
   * @param $combat_data
   * @param $planet
   */
  protected function ube_attack_prepare_planet(&$combat_data, &$planet) {
    $player_id = $planet['id_owner'];

    $this->ube_attack_prepare_player($combat_data, $player_id, false);

    $player = &$combat_data[UBE_PLAYERS][$player_id][UBE_PLAYER_DATA];

    $combat_data[UBE_FLEETS][0] = array(UBE_OWNER => $player_id);
    $fleet_info = &$combat_data[UBE_FLEETS][0];

    foreach (sn_get_groups('combat') as $unit_id) {
      if ($unit_count = mrc_get_level($player, $planet, $unit_id)) {
        $fleet_info[UBE_COUNT][$unit_id] = $unit_count;
      }
    }

    foreach (sn_get_groups('resources_loot') as $resource_id) {
      $fleet_info[UBE_RESOURCES][$resource_id] = floor(mrc_get_level($player, $planet, $resource_id));
    }

    if ($fortifier_level = mrc_get_level($player, $planet, MRC_FORTIFIER)) {
      $fortifier_bonus = $fortifier_level * get_unit_param(MRC_FORTIFIER, P_BONUS_VALUE) / 100;
      foreach (Ube4_1Calc::BONUS_LIST as $ube_id) {
        $fleet_info[UBE_BONUSES][$ube_id] += $fortifier_bonus;
      }
    }

    $combat_data[UBE_OUTCOME][UBE_PLANET] = $fleet_info[UBE_PLANET] = array(
      PLANET_ID     => $planet['id'],
      PLANET_NAME   => $planet['name'],
      PLANET_GALAXY => $planet['galaxy'],
      PLANET_SYSTEM => $planet['system'],
      PLANET_PLANET => $planet['planet'],
      PLANET_TYPE   => $planet['planet_type'],
      PLANET_SIZE   => $planet['diameter'],
    );

    $combat_data[UBE_OPTIONS][UBE_DEFENDER_ACTIVE] = $player['onlinetime'] >= $combat_data[UBE_TIME] - PLAYER_INACTIVE_TIMEOUT;
  }


  public function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender) {
    $combat_data = [
      UBE_OPTIONS => [
        UBE_SIMULATOR        => true,
        UBE_SIMULATOR_STATIC => sys_get_param_int('simulator'),
        UBE_MISSION_TYPE     => MT_ATTACK,
      ],

      UBE_PLAYERS => [],

      UBE_FLEETS => [],

      UBE_OBJ_PREPARATOR => $this,
    ];

    $this->sn_ube_simulator_fill_side($combat_data, $sym_defender, false);
    $this->sn_ube_simulator_fill_side($combat_data, $sym_attacker, true);

    $this->sn_ube_combat_prepare_first_round($combat_data);

    return $combat_data;
  }

  // ------------------------------------------------------------------------------------------------
  // Преобразовывает данные симулятора в данные для расчета боя
  protected function sn_ube_simulator_fill_side(&$combat_data, $side_info, $attacker, $player_id = -1) {
    $player_id = $player_id == -1 ? count($combat_data[UBE_PLAYERS]) : $player_id;

    foreach ($side_info as $fleet_data) {
      $combat_data[UBE_PLAYERS][$player_id][UBE_NAME] = $attacker ? 'Attacker' : 'Defender';
      $combat_data[UBE_PLAYERS][$player_id][UBE_ATTACKER] = $attacker;

      $combat_data[UBE_FLEETS][$player_id][UBE_OWNER] = $player_id;
      foreach ($fleet_data as $unit_id => $unit_count) {
        if (!$unit_count) {
          continue;
        }

        $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);

        if ($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
          $combat_data[UBE_FLEETS][$player_id][UBE_COUNT][$unit_id] = $unit_count;
        } elseif ($unit_type == UNIT_RESOURCES) {
          $combat_data[UBE_FLEETS][$player_id][UBE_RESOURCES][$unit_id] = $unit_count;
        } elseif ($unit_type == UNIT_TECHNOLOGIES) {
          $combat_data[UBE_PLAYERS][$player_id][UBE_BONUSES][static::CONVERT_TECHS[$unit_id]] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
        } elseif ($unit_type == UNIT_GOVERNORS) {
          if ($unit_id == MRC_FORTIFIER) {
            foreach (static::CONVERT_TECHS as $ube_id) {
              $combat_data[UBE_FLEETS][$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            }
          }
        } elseif ($unit_type == UNIT_MERCENARIES) {
          if ($unit_id == MRC_ADMIRAL) {
            foreach (static::CONVERT_TECHS as $ube_id) {
              $combat_data[UBE_PLAYERS][$player_id][UBE_BONUSES][$ube_id] += $unit_count * get_unit_param($unit_id, P_BONUS_VALUE) / 100;
            }
          }
        }
      }
    }
  }

  // ------------------------------------------------------------------------------------------------
  protected function sn_ube_combat_prepare_first_round(&$combat_data) {
    // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
    $first_round_data = array();
    foreach ($combat_data[UBE_FLEETS] as $fleet_id => &$fleet_info) {
      $fleet_info[UBE_COUNT] = is_array($fleet_info[UBE_COUNT]) ? $fleet_info[UBE_COUNT] : array();
      $player_data = &$combat_data[UBE_PLAYERS][$fleet_info[UBE_OWNER]];
      $fleet_info[UBE_FLEET_TYPE] = $player_data[UBE_ATTACKER] ? UBE_ATTACKERS : UBE_DEFENDERS;

      foreach (Ube4_1Calc::BONUS_LIST as $bonus_id => $bonus_value) {
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
        foreach (Ube4_1Calc::BONUS_LIST as $bonus_id => $bonus_value) {
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
  }

}
