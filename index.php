<?php

/**
 * @var template $template
 */

global $sn_page_name, $sn_mvc, $template, $template_result;

if(isset($sn_page_name) || ($sn_page_name = isset($_GET['page']) ? trim(strip_tags($_GET['page'])) : '')) {
  require_once('common.' . substr(strrchr(__FILE__, '.'), 1));
  if($sn_page_name) {
    // Loading page-specific language files

    !empty($sn_mvc['model'][$sn_page_name]) and execute_hooks($sn_mvc['model'][$sn_page_name], $template, 'model', $sn_page_name);
    !empty($sn_mvc['view'][$sn_page_name]) and execute_hooks($sn_mvc['view'][$sn_page_name], $template, 'view', $sn_page_name);
    if(!empty($template_result) && is_object($template)) {
      $template->assign_recursive($template_result);
    }

    SnTemplate::display($template);
  }
}

// Добавить обработку редиректов со старых страниц
// @ob_end_flush();
header('Location: overview.php');
die();
