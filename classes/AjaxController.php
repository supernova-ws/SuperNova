<?php

use Pages\IPage;

/**
 * Created by Gorlum 10.02.2017 6:36
 */
class AjaxController {

  /**
   *
   * Request format:
   *  http://xxx/index.php?page=ajax&mode=MODE&action=ACTION
   *
   * Will call method Pages\Page<MODE>.<ACTION>()
   *
   * @param template|null $template
   *
   * @return template|null
   */
  public static function controller($template = null) {
    global $template_result;

    define('IN_AJAX', true);

    if(!is_object($template)) {
      $template = SnTemplate::gettemplate('_ajax', true);
    }

    $template_result = array_merge($template_result, $merge = array(
      'GLOBAL_DISPLAY_HEADER' => false,
      'GLOBAL_DISPLAY_MENU' => false,
      'GLOBAL_DISPLAY_NAVBAR' => false,

      'IN_AJAX' => true,
    ));

    $template->assign_vars($merge);

    !is_array($template_result['AJAX']) ? $template_result['AJAX'] = array() : false;

    $mode = sys_get_param_str('mode');

    if (
      class_exists($className = 'Pages\\PageAjax' . ucfirst($mode))
      ||
      // Deprecated. Use 1st form! Only for back compatibility!
      class_exists($className = 'Pages\\Page' . ucfirst($mode))
    ) {
      /**
       * @var IPage $page
       */
      $page = new $className();
      if(method_exists($page, 'loadParams')) {
        $page->loadParams();
      }

      if(method_exists($page, $action = sys_get_param_str('action')) && $page->checkAction($action)) {
        $result = $page->$action();
        is_array($result) ? HelperArray::merge($template_result['AJAX'], $result) : false;
      }
    }

    return $template;
  }

  /**
   * @param template|null $template
   *
   * @return template|null
   */
  public static function view($template = null) {
    global $template_result;

    $template->assign_var('AJAX_RENDERED', json_encode($template_result['AJAX']));

    return $template;
  }

}
