<?php

classSupernova::$sn_mvc['view']['battle_report'][] = 'sn_battle_report_view';

/**
 * @param null|template $template
 *
 * @return null|template
 */
function sn_battle_report_view($template = null) {
  require_once('classes/UBE/UBE.php');

  return UBE::sn_battle_report_view($template);
}
