<?php

//$sn_mvc['model']['battle_report'][] = 'sn_battle_report_model';
$sn_mvc['view']['battle_report'][] = 'sn_battle_report_view';

/**
 * @param null|template $template
 *
 * @return null|template
 */
function sn_battle_report_view($template = null) {
  global $template_result, $lang;

  require_once('includes/classes/UBE/UBE.php');

  return UBE::sn_battle_report_view($template);
}
