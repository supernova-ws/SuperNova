<?php

/**
 * adm_payment.php
 *
 * @version #41a6.41#
 * @copyright 2013-2015 by Gorlum for http://supernova.ws
 */


define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3) {
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$template = gettemplate('admin/adm_payment', true);

$payer_list = array(
  -1 => classLocale::$lang['adm_pay_filter_all'],
);

$query = db_payment_list_payers();
while($row = db_fetch($query)) {
  $payer_list[$row['payment_user_id']] = '[' . $row['payment_user_id'] . '] ' . $row['payment_user_name'];
}
tpl_assign_select($template, 'payer', $payer_list);

$module_list = array(
  '' => classLocale::$lang['adm_pay_filter_all'],
);

$query = db_payment_list_modules();
while($row = db_fetch($query)) {
  $module_list[$row['payment_module_name']] = $row['payment_module_name'];
}
tpl_assign_select($template, 'module', $module_list);

tpl_assign_select($template, 'status', classLocale::$lang['adm_pay_filter_status']);
tpl_assign_select($template, 'test', classLocale::$lang['adm_pay_filter_test']);

$flt_payer = sys_get_param_int('flt_payer', -1);
$flt_module = sys_get_param_str('flt_module');
$flt_status = sys_get_param_int('flt_status', -1);
$flt_test = sys_get_param_int('flt_test', 0);

$query = db_payment_list_get($flt_payer, $flt_status, $flt_test, $flt_module);

while($row = db_fetch($query)) {
  $row2 = array();
  foreach($row as $key => $value) {
    $row2[strtoupper($key)] = $value;
  }
  $template->assign_block_vars('payment', $row2);
}

$template->assign_vars(array(
  'FLT_PAYER'  => $flt_payer,
  'FLT_STATUS' => $flt_status,
  'FLT_TEST'   => $flt_test,
  'FLT_MODULE' => $flt_module,
));

display($template, classLocale::$lang['adm_pay'], false, '', true);
