<?php

/**
 * adm_payment.php
 *
 * @version #42a25.4#
 * @copyright 2013-2015 by Gorlum for http://supernova.ws
*/


define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$template  = gettemplate('admin/adm_payment', true);

$payer_list = array(
  -1 => $lang['adm_pay_filter_all'],
);
$query = doquery("SELECT payment_user_id, payment_user_name FROM `{{payment}}` GROUP BY payment_user_id ORDER BY payment_user_name");
while($row = db_fetch($query)) {
  $payer_list[$row['payment_user_id']] = '[' . $row['payment_user_id'] . '] ' . $row['payment_user_name'];
}
tpl_assign_select($template, 'payer', $payer_list);

$module_list = array(
  '' => $lang['adm_pay_filter_all'],
);
$query = doquery("SELECT distinct payment_module_name FROM `{{payment}}` ORDER BY payment_module_name");
while($row = db_fetch($query)) {
  $module_list[$row['payment_module_name']] = $row['payment_module_name'];
}
tpl_assign_select($template, 'module', $module_list);

tpl_assign_select($template, 'status', $lang['adm_pay_filter_status']);
tpl_assign_select($template, 'test', $lang['adm_pay_filter_test']);

$flt_payer = sys_get_param_int('flt_payer', -1);
$flt_module = sys_get_param_str('flt_module');
$flt_status = sys_get_param_int('flt_status', -1);
$flt_test = sys_get_param_int('flt_test', 0);

$query = doquery("SELECT * FROM `{{payment}}` WHERE 1 " .
($flt_payer > 0 ? "AND payment_user_id = {$flt_payer} " : '') .
($flt_status >= 0 ? "AND payment_status = {$flt_status} " : '') .
($flt_test >= 0 ? "AND payment_test = {$flt_test} " : '') .
($flt_module ? "AND payment_module_name = '{$flt_module}' " : '') .
" ORDER BY payment_id desc");

while($row = db_fetch($query)) {
  $row2 = array();
  foreach($row as $key => $value) {
    $row2[strtoupper($key)] = $value;
  }
  $template->assign_block_vars('payment', $row2);
}

$template->assign_vars(array(
  'FLT_PAYER' => $flt_payer,
  'FLT_STATUS' => $flt_status,
  'FLT_TEST' => $flt_test,
  'FLT_MODULE' => $flt_module,
));

display($template, $lang['adm_pay']);
