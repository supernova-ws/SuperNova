<?php

include_once('common.' . substr(strrchr(__FILE__, '.'), 1));

if(SN::$gc->modules->countModulesInGroup('payment') && !defined('SN_GOOGLE')) {
  sys_redirect('metamatter.php');
}

$template = SnTemplate::gettemplate('dark_matter', true);

lng_include('infos');
$template->assign_vars(array(
  'URL_DARK_MATTER' => SN::$config->url_dark_matter,
  'DARK_MATTER_DESCRIPTION' => $lang['info'][RES_DARK_MATTER]['description'],

  'PAYMENT_AVAILABLE' => SN::$gc->modules->countModulesInGroup('payment') && !defined('SN_GOOGLE'),

  'PAGE_HEADER' =>$lang['sys_dark_matter'],
));

SnTemplate::display($template, $lang['sys_dark_matter']);
