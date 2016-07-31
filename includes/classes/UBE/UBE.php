<?php

use Mission\Mission;

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
   * @var Mission $combatMission
   */
  public $combatMission = null;


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

  /**
   * @var Bonus $planet_bonus
   */
  public $planet_bonus = null;

  public function __construct() {
    $this->players = new UBEPlayerList();
    $this->fleet_list = new UBEFleetList();
    $this->moon_calculator = new UBEMoonCalculator();
    $this->debris = new UBEDebris();
    $this->rounds = new UBERoundList();
    $this->planet_bonus = new Bonus();
  }

  /**
   * Заполняет начальные данные по данным миссии
   *
   * @param Mission $objMission
   *
   */
  public function loadDataFromMission(&$objMission) {
    $this->combatMission = $objMission;

    // Готовим опции
    $this->resource_exchange_rates = get_resource_exchange();
    $this->set_option_from_config();

    $this->combat_timestamp = $this->combatMission->fleet->time_arrive_to_target;
    $this->mission_type_id = $this->combatMission->fleet->mission_type;

    // Готовим инфу по атакующим
    $this->fleet_list->ubeInitGetAttackers($this->combatMission->fleet, $this->players);

    // Готовим инфу по удержанию
    $this->fleet_list->ubeInitGetFleetsOnHold($this->combatMission->fleet, $this->players);

    // Готовим инфу по атакуемой планете
    $this->ubeInitPreparePlanet();

    $this->moon_calculator->ubeInitLoadStatis($this->combatMission->dst_planet);
    $this->is_admin_in_combat = $this->players->authLevelMax > 0; // Участвует ли админ в бою?
  }

  /**
   * Заполняет данные по планете
   *
   * @internal param array $planet
   *
   */
  public function ubeInitPreparePlanet() {
    $player_id = $this->combatMission->dst_planet['id_owner'];

    $this->players->db_load_player_by_id($player_id, UBE_PLAYER_IS_DEFENDER);

    $player_db_row = $this->players[$player_id]->getDbRow();
    if($fortifier_level = mrc_get_level($player_db_row, $this->combatMission->dst_planet, MRC_FORTIFIER)) {
      $this->planet_bonus->add_unit_by_snid(MRC_FORTIFIER, $fortifier_level);
    }

    $this->fleet_list->ube_insert_from_planet_row($this->combatMission->dst_planet, $this->players[$player_id], $this->planet_bonus);

    $this->fleet_list->ube_load_from_players($this->players);

    $this->ube_planet_info = $this->fleet_list[0]->UBE_PLANET;
    $this->is_defender_active_player = $player_db_row['onlinetime'] >= $this->combat_timestamp - UBE_DEFENDER_ACTIVE_TIMEOUT;
  }

  /**
   * Общий алгоритм расчета боя
   *
   */
  protected function sn_ube_combat() {
    // TODO: Сделать атаку по типам,  когда они будут
//$this->is_simulator = true;
    $start = microtime(true);

    // Готовим информацию для первого раунда - проводим все нужные вычисления из исходных данных
pvar_dump($this->fleet_list[79]);
    // TODO - тут уже должна быть подсчитана вся инфа для боя, откуда бы не пришли данные - из симулятора или из миссии
    $this->fleet_list->ube_prepare_for_next_round($this->is_simulator);
pvar_dump($this->fleet_list[79]);
pdie();

    $this->rounds[0] = new UBERound(0);
    $this->rounds[0]->make_snapshot($this->fleet_list);

    for($round = 1; $round <= 10; $round++) {
      // Проводим раунд
      defined('DEBUG_UBE') ? print("Round {$round}<br>") : false;

      $this->fleet_list->ube_calculate_attack_results($this);

      defined('DEBUG_UBE') ? print('<hr>') : false;

      $this->rounds[$round] = new UBERound($round);
      $this->rounds[$round]->make_snapshot($this->fleet_list);

      // Анализируем итоги текущего раунда и готовим данные для следующего
      $this->combat_result = $this->fleet_list->ubeAnalyzeFleetOutcome($round);
      if($this->combat_result != UBE_COMBAT_RESULT_DRAW) {
        break;
      }

      // Готовим данные для раунда
      $this->fleet_list->ube_prepare_for_next_round($this->is_simulator);
    }
    $this->time_spent = microtime(true) - $start;

    // Делать это всегда - нам нужны результаты боя: луна->обломки->количество осташихся юнитов
    $this->sn_ube_combat_analyze();
  }


  /**
   * Разбирает данные боя для генерации отчета
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function sn_ube_combat_analyze() {
//    $lastRound = $this->rounds->get_last_element();
//    $this->combat_result = !isset($lastRound->round_outcome) || $lastRound->round_outcome == UBE_COMBAT_RESULT_DRAW_END ? UBE_COMBAT_RESULT_DRAW : $lastRound->round_outcome;
    // SFR - Small Fleet Reconnaissance ака РМФ
    $this->is_small_fleet_recce = $this->rounds->count() == 2 && $this->combat_result == UBE_COMBAT_RESULT_LOSS;

//    $this->debris->_reset();
    // Генерируем результат боя
    $this->fleet_list->ube_analyze_fleets($this->is_simulator, $this->debris, $this->resource_exchange_rates);

    if(!$this->is_ube_loaded) {
      $this->moon_calculator->calculate_moon($this);

      // Лутаем ресурсы - если аттакер выиграл
      if($this->combat_result == UBE_COMBAT_RESULT_WIN) {
        $this->sn_ube_combat_analyze_loot();
      }
    }

  }

  /**
   *
   *
   * @version 2016-02-25 23:42:45 41a4.68
   */
  public function sn_ube_combat_analyze_loot() {
    $planet_looted_in_metal = 0;
    $planet_resource_looted = array(
      RES_METAL     => 0,
      RES_CRYSTAL   => 0,
      RES_DEUTERIUM => 0,
    );

    if(
      (($planet_resource_total = $this->fleet_list[0]->get_resources_amount()) > 0)
      &&
      (($total_capacity = $this->fleet_list->ube_get_capacity_attackers()) > 0)
    ) {
      // Можно вывести только половину ресурсов, но не больше, чем общая вместимость флотов атакующих
      $planet_lootable = min($planet_resource_total / 2, $total_capacity);

      // Вычисляем процент вывоза. Каждого ресурса будет вывезено в одинаковых пропорциях
      $planet_lootable_percent = $planet_lootable / $planet_resource_total;

      // Вычисляем сколько ресурсов вывезено
      foreach($this->fleet_list->_container as $fleet_id => $fleet) {
        $looted_in_metal = 0;
        foreach($this->fleet_list[0]->resource_list as $resource_id => $resource_amount) {
          // Вычисляем какой процент общей емкости трюмов атакующих будет задействован
          $fleet_lootable_percent = $fleet->fleet_capacity / $total_capacity;
          $looted = floor($resource_amount * $planet_lootable_percent * $fleet_lootable_percent);

          $fleet->resources_looted[$resource_id] = -$looted;
          $planet_resource_looted[$resource_id] += $looted;
          $looted_in_metal -= $looted * $this->resource_exchange_rates[$resource_id];
        }
        $fleet->resources_lost_in_metal[RES_METAL] += $looted_in_metal;

        $planet_looted_in_metal += $looted_in_metal;
      }
    }

    $this->fleet_list[0]->resources_looted = $planet_resource_looted;
    $this->fleet_list[0]->resources_lost_in_metal[RES_METAL] -= $planet_looted_in_metal;
  }




































  // ------------------------------------------------------------------------------------------------
  /**
   * Записывает результат боя в БД
   *
   * @return mixed
   *
   */
  public function ube_combat_result_apply() {
    $destination_user_id = $this->fleet_list[0]->owner_id;

    // Обновляем поле обломков на планете
    if(!$this->is_admin_in_combat && $this->debris->debris_total() > 0) {
      DBStaticPlanet::db_planet_set_by_gspt($this->ube_planet_info[PLANET_GALAXY], $this->ube_planet_info[PLANET_SYSTEM], $this->ube_planet_info[PLANET_PLANET], PT_PLANET,
        "`debris_metal` = `debris_metal` + " . $this->debris->debris_get_resource(RES_METAL) . ", `debris_crystal` = `debris_crystal` + " . $this->debris->debris_get_resource(RES_CRYSTAL)
      );
    }

    foreach($this->fleet_list->_container as $fleet_id => $UBEFleet) {
      $ship_count_lost = $UBEFleet->unit_list->unitCountLost();

      if($fleet_id) {
        // Флот
        $UBEFleet->db_save_combat_result_fleet($this->is_small_fleet_recce, $this->moon_calculator->get_reapers_status());
      } else {
        // Планета

        // Сохраняем изменения ресурсов - если они есть
        $resource_delta = $UBEFleet->ube_combat_result_calculate_resources();
        if(!empty($resource_delta)) {
          $temp = array();
          foreach($resource_delta as $resource_id => $resource_amount) {
            $resource_db_name = pname_resource_name($resource_id);
            $temp[] = "`{$resource_db_name}` = `{$resource_db_name}` + ({$resource_amount})";
          }
          DBStaticPlanet::db_planet_set_by_id($this->ube_planet_info[PLANET_ID], implode(',', $temp));
        }

        if($ship_count_lost) {
          $db_changeset = array();
          $planet_row_cache = $this->players[$destination_user_id]->getDbRow();
          foreach($UBEFleet->unit_list->_container as $UBEUnit) {
            $db_changeset['unit'][] = sn_db_unit_changeset_prepare($UBEUnit->unitId, -$UBEUnit->units_lost, $planet_row_cache, $this->ube_planet_info[PLANET_ID]);
          }
          classSupernova::db_changeset_apply($db_changeset);
        }
      }
    }

    // TODO: Связать сабы с флотами констраинтами ON DELETE SET NULL
    // Для САБов
    $fleet_group_id_list = $this->fleet_list->ube_get_groups();
    if(!empty($fleet_group_id_list)) {
      $fleet_group_id_list = implode(',', $fleet_group_id_list);
      DBStaticFleetACS::db_acs_delete_by_list($fleet_group_id_list);
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
        DBStaticUser::db_user_set_by_id($player_id, "`xpraid` = `xpraid` + 1, `raids` = `raids` + 1, `{$str_loose_or_win}` = `{$str_loose_or_win}` + 1");
      }
    }
    if(!empty($bashing_list)) {
      $bashing_list = implode(',', $bashing_list);
      DBStaticFleetBashing::db_bashing_insert($bashing_list);
    }

    ube_combat_result_apply_from_object($this);
  }


  /**
   * Рассылает письма всем участникам боя
   */
  public function sn_ube_message_send() {
    $classLocale = classLocale::$lang;

    // TODO: Отсылать каждому игроку сообщение на его языке!

    $planet_info = &$this->ube_planet_info;

    // Генерируем текст письма
    $text_common = sprintf(classLocale::$lang['ube_report_msg_body_common'],
      date(FMT_DATE_TIME, $this->combat_timestamp),
      classLocale::$lang['sys_planet_type_sh'][$planet_info[PLANET_TYPE]],
      $planet_info[PLANET_GALAXY],
      $planet_info[PLANET_SYSTEM],
      $planet_info[PLANET_PLANET],
      htmlentities($planet_info[PLANET_NAME], ENT_COMPAT, 'UTF-8'),
      classLocale::$lang[$this->combat_result == UBE_COMBAT_RESULT_WIN ? 'ube_report_info_outcome_win' :
        ($this->combat_result == UBE_COMBAT_RESULT_DRAW ? 'ube_report_info_outcome_draw' : 'ube_report_info_outcome_loss')]
    );

    $text_defender = '';
    $debris = $this->debris->get_debris();
    foreach($debris as $resource_id => $resource_amount) {
      if($resource_id == RES_DEUTERIUM) {
        continue;
      }

      $text_defender .= "{$classLocale['tech'][$resource_id]}: " . pretty_number($resource_amount) . '<br />';
    }
    if($text_defender) {
      $text_defender = "{$classLocale['ube_report_msg_body_debris']}{$text_defender}<br />";
    }

    $text_defender .= $this->moon_calculator->message_generate($this);

    $text_defender .= "{$classLocale['ube_report_info_link']}: <a href=\"index.php?page=battle_report&cypher=$this->report_cypher\">{$this->report_cypher}</a>";

    // TODO: Оптимизировать отсылку сообщений - отсылать пакетами
    $player_sides = $this->players->get_player_sides();
    foreach($player_sides as $player_id => $player_side) {
      $message = $text_common . ($this->is_small_fleet_recce && ($player_side == UBE_PLAYER_IS_ATTACKER) ? classLocale::$lang['ube_report_msg_body_sfr'] : $text_defender);
      DBStaticMessages::msg_send_simple_message($player_id, '', $this->combat_timestamp, MSG_TYPE_COMBAT, classLocale::$lang['sys_mess_tower'], classLocale::$lang['sys_mess_attack_report'], $message);
    }

  }

  /**
   * @param $sym_attacker
   * @param $sym_defender
   */
  public function sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender) {
//    $this->is_simulator = sys_get_param_int('simulator');
//    $this->is_simulator = !empty($this->is_simulator);
    $this->is_simulator = true;
    $this->mission_type_id = MT_ATTACK;

    $this->players = new UBEPlayerList();
    $this->fleet_list = new UBEFleetList();

    $this->sn_ube_simulator_fill_side($sym_defender, false);
    $this->sn_ube_simulator_fill_side($sym_attacker, true);
  }

  /**
   * Преобразовывает данные симулятора в данные для расчета боя
   *
   * @param     $side_info
   * @param     $attacker
   * @param int $player_id
   *
   */
  public function sn_ube_simulator_fill_side($side_info, $attacker, $player_id = -1) {
    $player_id = $player_id == -1 ? $this->players->count() : $player_id;
    $fleet_id = $player_id; // FOR SIMULATOR!

    if(empty($this->players[$player_id])) {
      $this->players[$player_id] = new UBEPlayer();
    }

    foreach($side_info as $fleet_data) {
      $this->players[$player_id]->name = $player_id;
      $this->players[$player_id]->setSide($attacker);

      $objFleet = new UBEFleet();
      $this->fleet_list[$fleet_id] = $objFleet;

      $this->fleet_list[$fleet_id]->owner_id = $player_id;
      foreach($fleet_data as $unit_id => $unit_count) {
        if(!$unit_count) {
          continue;
        }

        $unit_type = get_unit_param($unit_id, P_UNIT_TYPE);

        if($unit_type == UNIT_SHIPS || $unit_type == UNIT_DEFENCE) {
          $this->fleet_list[$fleet_id]->unit_list->unitAdjustCount($unit_id, $unit_count);
        } elseif($unit_type == UNIT_RESOURCES) {
          $this->fleet_list[$fleet_id]->resource_list[$unit_id] = $unit_count;
        } elseif($unit_type == UNIT_TECHNOLOGIES) {
          if($unit_id == TECH_WEAPON) {
            $this->players[$player_id]->player_bonus->add_unit_by_snid(TECH_WEAPON, $unit_count);
          } elseif($unit_id == TECH_SHIELD) {
            $this->players[$player_id]->player_bonus->add_unit_by_snid(TECH_SHIELD, $unit_count);
          } elseif($unit_id == TECH_ARMOR) {
            $this->players[$player_id]->player_bonus->add_unit_by_snid(TECH_ARMOR, $unit_count);
          }
        } elseif($unit_type == UNIT_GOVERNORS) {
          if($unit_id == MRC_FORTIFIER) {
            // Фортифаер даёт бонус ко всему
            $this->planet_bonus->add_unit_by_snid(MRC_FORTIFIER, $unit_count);
          }
        } elseif($unit_type == UNIT_MERCENARIES) {
          if($unit_id == MRC_ADMIRAL) {
            $this->players[$player_id]->player_bonus->add_unit_by_snid(MRC_ADMIRAL, $unit_count);
          }
        }
      }
    }
  }


  /**
   *
   */
  public function set_option_from_config() {
    $this->options_method = classSupernova::$config->game_ube_method ? classSupernova::$config->game_ube_method : 0;
  }

  /**
   * @return int
   */
  public function get_time_spent() {
    return $this->time_spent;
  }

  /**
   * @return string
   */
  public function get_cypher() {
    return $this->report_cypher;
  }


  /**
   * Статик кусок из flt_mission_attack()
   *
   * @param Mission $objMission
   *
   * @return bool
   *
   */
  static function flt_mission_attack($objMission) {
    $ube = new UBE();
    $ube->loadDataFromMission($objMission);

    $ube->sn_ube_combat();

    flt_planet_capture_from_object($ube);

    $ube_report = new UBEReport();
    $ube_report->sn_ube_report_save($ube);

    $ube->ube_combat_result_apply();

    $ube->sn_ube_message_send();

    defined('DEBUG_UBE') ? die('DIE at ' . __FILE__ . ' ' . __LINE__) : false;

    return false;
  }

  /**
   * @param $template
   *
   * @return template
   */
  static function sn_battle_report_view(&$template) {
    global $template_result;

    $ube_report = new UBEReport();
    $ube = $ube_report->sn_ube_report_load(sys_get_param_str('cypher'));
    if($ube != UBE_REPORT_NOT_FOUND) {
      $ube_report->sn_ube_report_generate($ube, $template_result);

      $template = gettemplate('ube_combat_report', $template);
      $template->assign_vars(array(
        'PAGE_HEADER' => classLocale::$lang['ube_report_info_page_header'],
      ));
    } else {
      message(classLocale::$lang['sys_msg_ube_report_err_not_found'], classLocale::$lang['sys_error']);
    }

    return $template;
  }

  /**
   * @param $sym_attacker
   * @param $sym_defender
   */
  static function display_simulator(&$sym_attacker, &$sym_defender) {
    global $template_result;

    $ube = new UBE();
    $ube->sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender);

    $ube->set_option_from_config();
    $ube->sn_ube_combat();
    $ube_report = new UBEReport();

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

    $this->debris->load_from_report_row($report_row);

    $query = classSupernova::$db->doSelect("SELECT * FROM {{ube_report_player}} WHERE `ube_report_id` = {$report_row['ube_report_id']}");
    while($player_row = db_fetch($query)) {
      $this->players->init_player_from_report_info($player_row);
    }

    $this->fleet_list->ube_db_load_from_report_row($report_row, $this);

    $this->rounds->db_load_round_list_from_report_row($report_row, $this);

    $this->fleet_list->ube_db_load_fleets_outcome($report_row);
  }

}


// ------------------------------------------------------------------------------------------------
/**
 * Записывает результат боя в БД
 * @see unit_captain::ube_combat_result_apply_from_object
 *
 * @param UBE $ube
 *
 * @return mixed
 *
 */
function ube_combat_result_apply_from_object(UBE $ube) { return sn_function_call(__FUNCTION__, array($ube)); }

/**
 * Заполняет данные по флоту
 * @see unit_captain::ube_attack_prepare_fleet_from_object
 *
 * @param UBEFleet $UBEFleet
 *
 * @return mixed
 *
 */
function ube_attack_prepare_fleet_from_object(UBEFleet $UBEFleet) { return sn_function_call(__FUNCTION__, array($UBEFleet)); }

/**
 * @see game_skirmish::flt_planet_capture_from_object
 *
 * @param UBE $ube
 *
 * @return mixed
 *
 */
function flt_planet_capture_from_object(UBE $ube) { return sn_function_call(__FUNCTION__, array($ube, &$result)); }
