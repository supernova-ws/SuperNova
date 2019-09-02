<?php
/**
 * Created by Gorlum 18.02.2017 19:37
 */

define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

$constants = get_defined_constants(true);
$rpgConstants = array();
foreach($constants['user'] as $constantName => $constantValue) {
  if(substr($constantName, 0, 4) == 'RPG_') {
    $rpgConstants[$constantValue] = $constantName;
  }
}

$spent = array();

$result = SN::$db->doquery(
  "SELECT
    CONCAT(log_dark_matter_reason, '_', IF(sign(sum(log_dark_matter_amount)) > 0, 1, -1)) as `BALANCE`,
    log_dark_matter_reason as `REASON`,
    sum(log_dark_matter_amount) as `DM_AMOUNT`,
    count(log_dark_matter_amount) as `DM_COUNT`,
    sign(sum(log_dark_matter_amount)) as `SIGN`
  FROM `{{log_dark_matter}}`
  GROUP BY log_dark_matter_reason, IF(sign((log_dark_matter_amount)) > 0, 1, -1) ORDER BY sum(log_dark_matter_amount) DESC;"
);

while($row = SN::$db->db_fetch($result)) {
  $row['CONSTANT'] = $rpgConstants[$row['REASON']];

  $row['DM_AMOUNT_TEXT'] = HelperString::numberFloorAndFormat($row['DM_AMOUNT']);

//  $row['TOTAL_AMOUNT'] = $row['DM_AMOUNT'];
//  $row['TOTAL_COUNT'] = $row['DM_COUNT'];
//  $row['TOTAL_AMOUNT_TEXT'] = pretty_number($row['TOTAL_AMOUNT']);

  $spent[$row['BALANCE']] = $row;
}

$result = SN::$db->doquery(
  "SELECT
    CONCAT(reason, '_', IF(sign(sum(amount)) > 0, 1, -1)) as `BALANCE`,
    reason as `REASON`,
    sum(amount) as `MM_AMOUNT`,
    count(amount) as `MM_COUNT`,
    sign(sum(amount)) as `SIGN`
  FROM `{{log_metamatter}}`
  GROUP BY reason, if(sign((amount)) > 0, 1, -1) ORDER BY sum(amount) DESC;"
);

while($row = SN::$db->db_fetch($result)) {
  if(empty($spent[$row['BALANCE']])) {
    $spent[$row['BALANCE']] = array();
  }

  $row['CONSTANT'] = $rpgConstants[$row['REASON']];
  $row['MM_AMOUNT_TEXT'] = HelperString::numberFloorAndFormat($row['MM_AMOUNT']);

  $spent[$row['BALANCE']] = array_merge_recursive_numeric($spent[$row['BALANCE']], $row);
}

foreach($spent as &$row) {
  @$row['TOTAL_COUNT'] = $row['MM_COUNT'] + $row['DM_COUNT'];
  @$row['TOTAL_AMOUNT'] = $row['MM_AMOUNT'] + $row['DM_AMOUNT'];
  @$row['TOTAL_AMOUNT_TEXT'] = HelperString::numberFloorAndFormat($row['TOTAL_AMOUNT']);
  @$row['TOTAL_COUNT_TEXT'] = HelperString::numberFloorAndFormat($row['TOTAL_COUNT']);
}

usort($spent, function ($a, $b) {
  return $a['TOTAL_AMOUNT'] < $b['TOTAL_AMOUNT'] ? -1 :
    ($a['TOTAL_AMOUNT'] > $b['TOTAL_AMOUNT'] ? 1 : 0);
});


$template = SnTemplate::gettemplate("admin/admin_analyze_matter", true);
foreach ($spent as $row) {
  $template->assign_block_vars('spent', $row);
}
$fromDate = SN::$db->doQueryAndFetch("SELECT min(log_dark_matter_timestamp) FROM `{{log_dark_matter}}`;");
$template->assign_var("MIN_DATE", reset($fromDate));


SnTemplate::display($template, '{Анализ расхода и прихода материи}');
