<?php

if(isset($sn_page_name) || ($sn_page_name = isset($_GET['page']) ? trim(strip_tags($_GET['page'])) : '')) {
  require_once('common.' . substr(strrchr(__FILE__, '.'), 1));
// pdump($sn_mvc);
  if($sn_page_name) {
    // Loading page-specific language files
    global $template;
    !empty($sn_mvc['model'][$sn_page_name]) and execute_hooks($sn_mvc['model'][$sn_page_name], $template, 'model', $sn_page_name);
    !empty($sn_mvc['view'][$sn_page_name]) and execute_hooks($sn_mvc['view'][$sn_page_name], $template, 'view', $sn_page_name);
/*
    if($sn_mvc['model'][$sn_page_name]) {
      foreach($sn_mvc['model'][$sn_page_name] as $hook) {
        if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable)))) {
          call_user_func($hook_call);
        }
      }
    }

    if($sn_mvc['view'][$sn_page_name]) {
      foreach($sn_mvc['view'][$sn_page_name] as $hook) {
        if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable)))) {
          $template = call_user_func($hook_call, $template);
        }
      }
    }
*/
    if(!empty($template_result) && is_object($template)) {
      $template->assign_recursive($template_result);
    }
    display($template, '', true, '', defined('IN_ADMIN') && (IN_ADMIN === true), true);
  }
}

//pdump($sn_page_name);
//die();

// Добавить обработку редиректов со старых страниц

ob_start();
header('location: overview.php');
ob_end_flush();
die();
