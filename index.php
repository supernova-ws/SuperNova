<?php

  $module_name = isset($_GET['module']) ? trim(strip_tags($_GET['module'])) : '';
  if($module_name)
  {
    require_once('common.' . substr(strrchr(__FILE__, '.'), 1));
    if(isset($sn_module[$module_name]))
    {
      $parse_result = is_callable(array($module_name, 'request_parse')) ? $sn_module[$module_name]->request_parse($user, $planetrow) : array();
      $render_result = is_callable(array($module_name, 'page_render')) ? $sn_module[$module_name]->page_render($template, $parse_result) : array();
    }
  }

  ob_start();
  header('location: overview.php');
  ob_end_flush();
  die();
?>
