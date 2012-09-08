<?php
  if(isset($sn_page_name) || ($sn_page_name = isset($_GET['page']) ? trim(strip_tags($_GET['page'])) : ''))
  {
    require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

    if($sn_page_name)
    {
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

/*
      $sn_mvc_models = array_merge(is_array($sn_mvc['model']['']) ? $sn_mvc['model'][''] : array(), is_array($sn_mvc['model'][$sn_page_name]) ? $sn_mvc['model'][$sn_page_name] : array());
      if(!empty($sn_mvc_models))
      {
        foreach($sn_mvc_models as $hook)
        {
          if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable))))
          {
            call_user_func($hook_call);
          }
        }
      }
*/

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

      if(!empty($template_result))
      {
        $template->assign_recursive($template_result);
      }
      display($template);

    }
  }

  ob_start();
  header('location: overview.php');
  ob_end_flush();
  die();

?>
