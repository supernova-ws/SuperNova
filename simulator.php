<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if (sys_get_param_int('BE_DEBUG') && !defined('BE_DEBUG')) {
  define('BE_DEBUG', true);
}

require_once("includes/includes/ube_attack_calculate.php");
require_once('includes/includes/coe_simulator_helpers.php');

$replay = $_GET['replay'] ? $_GET['replay'] : $_POST['replay'];
$execute = intval($_GET['execute']);
$sym_defender = $_POST['defender'] ? $_POST['defender'] : array();
$sym_attacker = $_POST['attacker'] ? $_POST['attacker'] : array();

if ($replay) {
  $unpacked = sn_ube_simulator_decode_replay($replay);

  $sym_defender = $unpacked['D'];
  $sym_attacker = $unpacked['A'];
} else {
  $sym_defender = array(0 => $sym_defender);
  $sym_attacker = array(1 => $sym_attacker);
}

if ($_POST['submit'] || $execute) {
  $replay = sn_ube_simulator_encode_replay($sym_defender, 'D');
  $replay .= sn_ube_simulator_encode_replay($sym_attacker, 'A');

  $ubePrepare = new \Ube\Ube4_1\Ube4_1Prepare();
  $combat_data = $ubePrepare->sn_ube_simulator_fleet_converter($sym_attacker, $sym_defender);

  $combat_data[UBE_OPTIONS][UBE_METHOD] = SN::$config->game_ube_method ? SN::$config->game_ube_method : 0;
  $ubeCalc = new \Ube\Ube4_1\Ube4_1Calc();
  $ubeCalc->sn_ube_combat($combat_data);
  // Это используется для тестов - отключено в стандартном режиме
//  if(!sys_get_param_int('simulator') || sys_get_param_str('reload')) {
//    sn_ube_report_save($combat_data);
//  }

  if (sys_get_param_str('reload')) {
    $combat_data = sn_ube_report_load($combat_data[UBE_REPORT_CYPHER]);
  }

//debug($combat_data);
  // Рендерим их в темплейт
  sn_ube_report_generate($combat_data, $template_result);

  $template_result['MICROTIME'] = $combat_data[UBE_TIME_SPENT];

  $template = SnTemplate::gettemplate('ube_combat_report', true);
  $template->assign_recursive($template_result);

  $template->assign_vars(array(
    'MENU'   => false,
    'NAVBAR' => false,
  ));

  SnTemplate::display($template);
} else {
  $template = SnTemplate::gettemplate('simulator', true);
  $techs_and_officers = array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR, MRC_ADMIRAL);

  foreach ($techs_and_officers as $tech_id) {
    if (!$sym_attacker[1][$tech_id]) {
      $sym_attacker[1][$tech_id] = mrc_get_level($user, false, $tech_id);
    }
  }

  $show_groups = array(
    UNIT_TECHNOLOGIES => array(TECH_WEAPON, TECH_SHIELD, TECH_ARMOR),
    UNIT_MERCENARIES  => array(MRC_ADMIRAL),
    UNIT_SHIPS        => sn_get_groups('fleet'),
    UNIT_RESOURCES    => sn_get_groups('resources_loot'),
    UNIT_GOVERNORS    => array(MRC_FORTIFIER),
    UNIT_DEFENCE      => sn_get_groups('defense_active'),
  );
  foreach ($show_groups as $unit_group_id => $unit_group) {
    $template->assign_block_vars('simulator', array(
      'GROUP' => $unit_group_id,
      'NAME'  => $lang['tech'][$unit_group_id],
    ));

    foreach ($unit_group as $unit_id) {
      $tab++;

      $value = mrc_get_level($user, $planetrow, $unit_id);

      $template->assign_block_vars('simulator', array(
        'NUM'      => $tab < 9 ? "0{$tab}" : $tab,
        'ID'       => $unit_id,
        'GROUP'    => $unit_group_id,
        'NAME'     => $lang['tech'][$unit_id],
        'ATTACKER' => intval($sym_attacker[1][$unit_id]),
        'DEFENDER' => intval($sym_defender[0][$unit_id]),
        'VALUE'    => $value,
      ));
    }
  }

  $template->assign_vars(array(
    'BE_DEBUG'       => BE_DEBUG,
    'UNIT_DEFENCE'   => UNIT_DEFENCE,
    'UNIT_GOVERNORS' => UNIT_GOVERNORS,
  ));

  $template->assign_vars(array(
    'NAVBAR' => false,
  ));
  SnTemplate::display($template, $lang['coe_combatSimulator']);
}
