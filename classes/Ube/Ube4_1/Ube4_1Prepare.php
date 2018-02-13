<?php
/**
 * Created by Gorlum 13.02.2018 8:17
 */

namespace Ube\Ube4_1;

use DBStaticPlanet;

class Ube4_1Prepare {

  /**
   * Заполняет начальные данные по данным миссии
   *
   * @param $mission_data
   *
   * @return array
   */
  public function ube_attack_prepare(&$mission_data, &$fleet_list_on_hold) {
    /*
    UBE_OPTIONS[UBE_LOADED]
    UBE_OPTIONS[UBE_SIMULATOR]
    UBE_OPTIONS[UBE_EXCHANGE]
    UBE_OPTIONS[UBE_MOON_WAS]
    */

    $fleet_row = &$mission_data['fleet'];
    $destination_planet = &$mission_data['dst_planet'];

    $ube_time = $fleet_row['fleet_start_time'];
    $combat_data = [
      UBE_TIME => $ube_time,
      UBE_OBJ_PREPARATOR => $this,
      UBE_OBJ_CALCULATOR => new \Ube\Ube4_1\Ube4_1Calc(),
    ];
    // TODO: Не допускать атаки игроком своих же флотов - т.е. холд против атаки
    // Готовим инфу по атакуемой планете
    $this->ube_attack_prepare_planet($combat_data, $destination_planet);

    // Готовим инфу по удержанию
    foreach ($fleet_list_on_hold as $fleet) {
      ube_attack_prepare_fleet($combat_data, $fleet, false);
    }

    // Готовим инфу по атакующим
    if ($fleet_row['fleet_group']) {
      $acs_fleet_list = fleet_list_by_group($fleet_row['fleet_group']);
      foreach ($acs_fleet_list as $fleet) {
        ube_attack_prepare_fleet($combat_data, $fleet, true);
      }
    } else {
      ube_attack_prepare_fleet($combat_data, $fleet_row, true);
    }

    // Готовим опции
    $combat_data[UBE_OPTIONS][UBE_MOON_WAS] = $destination_planet['planet_type'] == PT_MOON || is_array(DBStaticPlanet::db_planet_by_parent($destination_planet['id'], true, '`id`'));
    $combat_data[UBE_OPTIONS][UBE_MISSION_TYPE] = $fleet_row['fleet_mission'];
    global $config;
    $combat_data[UBE_OPTIONS][UBE_METHOD] = $config->game_ube_method ? $config->game_ube_method : 0;

    return $combat_data;
  }

  // ------------------------------------------------------------------------------------------------
// Заполняет данные по игроку
  public function ube_attack_prepare_player(&$combat_data, $player_id, $is_attacker) {
    /**
     * @var \Ube\Ube4_1\Ube4_1Calc $ubeCalc
     */
    $ubeCalc = $combat_data[UBE_OBJ_CALCULATOR];

    if (!isset($combat_data[UBE_PLAYERS][$player_id])) {
      $combat_data[UBE_PLAYERS][$player_id] = array(UBE_ATTACKER => $is_attacker);
      $player_info = &$combat_data[UBE_PLAYERS][$player_id];

      $player_data = db_user_by_id($player_id, true);
      $player_info[UBE_NAME] = $player_data['username'];
      $player_info[UBE_AUTH_LEVEL] = $player_data['authlevel'];
      $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] = $combat_data[UBE_OPTIONS][UBE_COMBAT_ADMIN] || $player_data['authlevel']; // Участвует ли админ в бою?
      $player_info[UBE_PLAYER_DATA] = $player_data;

      $admiral_bonus = mrc_get_level($player_data, false, MRC_ADMIRAL) * get_unit_param(MRC_ADMIRAL, P_BONUS_VALUE) / 100;
      foreach ($ubeCalc->ube_convert_techs as $unit_id => $ube_id) {
        $player_info[UBE_BONUSES][$ube_id] += mrc_get_level($player_data, false, $unit_id) * get_unit_param($unit_id, P_BONUS_VALUE) / 100 + $admiral_bonus;
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
  public function sn_ube_attack_prepare_fleet(&$combat_data, &$fleet, $is_attacker) {
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
  }

  /**
   * Заполняет данные по планете
   *
   * @param $combat_data
   * @param $planet
   */
  protected function ube_attack_prepare_planet(&$combat_data, &$planet) {
    /**
     * @var \Ube\Ube4_1\Ube4_1Calc $ubeCalc
     */
    $ubeCalc = $combat_data[UBE_OBJ_CALCULATOR];

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
      foreach ($ubeCalc->ube_combat_bonus_list as $ube_id) {
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

    $combat_data[UBE_OPTIONS][UBE_DEFENDER_ACTIVE] = $player['onlinetime'] >= $combat_data[UBE_TIME] - 60 * 60 * 24 * 7;
  }

}
