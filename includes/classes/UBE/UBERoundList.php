<?php

/**
 * Class UBERoundList
 *
 * @method UBERound offsetGet($offset)
 * @property UBERound[] $_container
 */
class UBERoundList extends ArrayAccessV2 {

  /**
   * @return UBERound
   */
  public function get_last_element() {
    return end($this->_container);
  }

  // Генерируем отчет по флотам
  /**
   * @param     $template_result
   * @param UBE $ube
   */
  public function round_list_generate_report(&$template_result, UBE $ube) {
    $round_count = $this->count();
    for($round = 1; $round <= $round_count - 1; $round++) {
      $round_template = array(
        'NUMBER' => $round,
        '.'      => array(
          'fleet' => $this->sn_ube_report_round_fleet($ube, $round),
        ),
      );
      $template_result['.']['round'][] = $round_template;
    }
  }

  // ------------------------------------------------------------------------------------------------
  // Парсит инфу о раунде для темплейта
  /**
   * @param UBE $ube
   * @param     $round
   *
   * @return array
   */
  function sn_ube_report_round_fleet(UBE $ube, $round) {
    $round_template = array();

    $this->sn_ube_report_round_fleet_side($ube, $round, UBE_PLAYER_IS_ATTACKER, $round_template);
    $this->sn_ube_report_round_fleet_side($ube, $round, UBE_PLAYER_IS_DEFENDER, $round_template);

    return $round_template;
  }

  function sn_ube_report_round_fleet_side(UBE $ube, $round, $side, &$round_template) {
    global $lang;

    $previousRound = $this[$round - 1];
    $currentRound = $this[$round];

    $is_attacker = $side == UBE_PLAYER_IS_ATTACKER;

    $side_array = $is_attacker ? $currentRound->UBE_ATTACKERS : $currentRound->UBE_DEFENDERS;
    if(empty($side_array)) {
      return;
    }

    foreach($side_array[UBE_ATTACK] as $fleet_id => $temp) {
      $fleet_template = array(
        'ID'          => $fleet_id,
        'IS_ATTACKER' => $is_attacker,
        'PLAYER_NAME' => $ube->players[$ube->fleet_list[$fleet_id]->UBE_OWNER]->player_name_get(true),
      );

      if(is_array($ube->fleet_list[$fleet_id]->UBE_PLANET)) {
        $fleet_template += $ube->fleet_list[$fleet_id]->UBE_PLANET;
        $fleet_template[PLANET_NAME] = $fleet_template[PLANET_NAME] ? htmlentities($fleet_template[PLANET_NAME], ENT_COMPAT, 'UTF-8') : '';
        $fleet_template['PLANET_TYPE_TEXT'] = $lang['sys_planet_type_sh'][$fleet_template['PLANET_TYPE']];
      }

      $fleet_template['.']['ship'] = $currentRound->generate_report_fleet_ship_list($fleet_id, $previousRound);

      $round_template[] = $fleet_template;
    }
  }

}
