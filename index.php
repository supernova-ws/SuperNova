<?php
  // TODO: DEPRECATED!
  if($module_name = isset($_GET['module']) ? trim(strip_tags($_GET['module'])) : '')
  {
    require_once('common.' . substr(strrchr(__FILE__, '.'), 1));
    if(isset($sn_module[$module_name]))
    {
      $parse_result = is_callable(array($module_name, 'request_parse')) ? $sn_module[$module_name]->request_parse($user, $planetrow) : array();
      $render_result = is_callable(array($module_name, 'page_render')) ? $sn_module[$module_name]->page_render($template, $parse_result) : array();
    }
  }

  if(isset($sn_page_name) || ($sn_page_name = isset($_GET['page']) ? trim(strip_tags($_GET['page'])) : ''))
  {
    require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

    if($sn_page_name)
    {
      // Loading global language files
      lng_load_i18n($sn_i18n['pages']['']);
      // Loading page-specific language files
      lng_load_i18n($sn_i18n['pages'][$sn_page_name]);

      if($sn_mvc['model'][$sn_page_name])
      {
        foreach($sn_mvc['model'][$sn_page_name] as $hook)
        {
          if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable))))
          {
            call_user_func($hook_call);
          }
        }
      }

      if($sn_mvc['view'][$sn_page_name])
      {
        foreach($sn_mvc['view'][$sn_page_name] as $hook)
        {
          if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable))))
          {
            $template = call_user_func($hook_call, $template);
          }
//          $template = call_user_func(is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable), $template);
        }
      }

      display($template);

  /*
      if(isset($sn_module[$module_name]))
      {
        $parse_result = is_callable(array($module_name, 'request_parse')) ? $sn_module[$module_name]->request_parse($user, $planetrow) : array();
        $render_result = is_callable(array($module_name, 'page_render')) ? $sn_module[$module_name]->page_render($template, $parse_result) : array();
      }
  */
    }
  }

  ob_start();
  header('location: overview.php');
  ob_end_flush();
  die();

?>
