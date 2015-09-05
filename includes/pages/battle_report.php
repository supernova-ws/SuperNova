<?php

//$sn_mvc['model']['battle_report'][] = 'sn_battle_report_model';
$sn_mvc['view']['battle_report'][] = 'sn_battle_report_view';

function sn_battle_report_view($template = null)
{
  global $template_result, $lang;

  require_once('includes/includes/ube_report.php');

  $combat_data = sn_ube_report_load(sys_get_param_str('cypher'));

  if($combat_data != UBE_REPORT_NOT_FOUND)
  {
    sn_ube_report_generate($combat_data, $template_result);
    $template = gettemplate('ube_combat_report', $template);
  }
  else
  {
    message($lang['sys_msg_ube_report_err_not_found'], $lang['sys_error']);
  }

  return $template;
}
