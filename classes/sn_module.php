<?php

class sn_module {
  /**
   * SN version in which module was committed. Can be treated as version in which module guaranteed to work
   * @var string $versionCommitted
   */
  public $versionCommitted = '#42a28.9#';

  public $manifest = array(
    'package' => 'core',
    'name' => 'sn_module',
    'version' => '1c0',
    'copyright' => 'Project "SuperNova.WS" #42a28.9# copyright © 2009-2017 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'installed' => true,
    'active' => true,

    // 'constants' array - contents of this array would be instaled into engine
    'constants' => array(
//      'UNIT_STRUCTURE_NEW' => 999999,
    ),

    'vars' => array(), // Just a placeholder. vars assigned via special method __assign_vars(). Need 'cause of new constants that can be defined within module. See below

    // 'functions' array - this functions would be installed as hooks
    // Key: overwritable function name to replace
    // Value: which method to use. Format: [*][<object_name>][.]<method>
    // '*' means that new function would replace old one
    // If object_name is ommited but "." is present - hook linked to global function
    // If only "method" present - overwritable linked to appropriate method of current object
    // Function/Method should be accessible on module init
    'functions' => array(
//      'test_object_test_method' => 'test_object.test_method',
//      'test_function' => '.my_test_function',
//      'this_object_test_method' => 'test_method',
    ),

    // 'menu' array - this menu items would be merged into main game menu
    // Array element almost identical to $sn_menu with additional param 'LOCATION'.
    // 'LOCATION' => '-news', // Special atrtribute for modules
    // [-|+][<menu_item_id>]
    // <menu_item_id> identifies menu item aginst new menu item would be placed. When ommited new item placed against whole menu
    // -/+ indicates that new item should be placed before/after identified menu item (or whole menu). If ommited and menu item exists - new item will replace previous one
    // Empty or non-existent LOCATION equivalent to '+' - place item at end of menu
    // Non-existent menu_item_id treated as ommited
    'menu' => array(),

    // 'page' array - defines pages which will handle this module and appropriate handlers
    'page' => array(),
  );

  protected $config = array();

  protected $module_full_class_path = __FILE__;

  /**
   * Динамическое назначение переменных
   *
   * Актуально, когда записываемые данные зависят от статуса игры
   * Например - назначаются константы внутри модуля
   *
   * @return array
   */
  function __assign_vars() {
    return array(
/*
      'sn_data' => array(
        UNIT_STRUCTURE_NEW => array( // Will honor new constants
          'name' => 'robot_factory',
          'type' => UNIT_STRUCTURE,
          'location' => LOC_PLANET,
          'cost' => array(
            RES_METAL     => 400,
            RES_CRYSTAL   => 120,
            RES_DEUTERIUM => 200,
            RES_ENERGY    => 0,
            'factor' => 2,
          ),
          'metal' => 400,
          'crystal' => 120,
          'deuterium' => 200,
          'energy' => 0,
          'factor' => 2,
        ),
      ),
*/
    );
  }

  function __construct($filename = __FILE__) {
    global $sn_module;

    // Getting module PHP class name
    $class_module_name = get_class($this);

    // Getting module root relative to SN
    $this->manifest['root_relative'] = str_replace(array(SN_ROOT_PHYSICAL, basename($filename)), '', str_replace('\\', '/', $filename));

    // TODO: Load configuration from DB. Manifest setting
    // Trying to load configuration from file
    $config_exists = false;
    // Конфигурация может лежать в config_path в манифеста или в корне модуля
    if(isset($this->manifest['config_path']) && file_exists($config_filename = $this->manifest['config_path'] . '/config.php')) {
      $config_exists = true;
    } elseif(file_exists($config_filename = dirname($filename) . '/config.php')) {
      $config_exists = true;
    }

    if($config_exists) {
      include($config_filename);
      $module_config_array = $class_module_name . '_config';
      $this->config = $$module_config_array;
    }

    // Registering module
    $sn_module[$class_module_name] = $this;
  }

  protected function __patch_menu(&$sn_menu_extra, &$menu_patch) {
    if(isset($menu_patch) && is_array($menu_patch) && !empty($menu_patch)) {
      foreach($menu_patch as $menu_item_name => $menu_item_data) {
        $sn_menu_extra[$menu_item_name] = $menu_item_data;
      }
    }
  }


  public function initialize() {
    // Checking module status - is it installed and active
    $this->check_status();
    if(!$this->manifest['active']) {
      return;
    }

    // Setting constants - if any
    if(isset($this->manifest['constants']) && is_array($this->manifest['constants']) && !empty($this->manifest['constants'])) {
      foreach($this->manifest['constants'] as $constant_name => $constant_value) {
        defined($constant_name) or define($constant_name, $constant_value);
      }
    }

    // Adding vars - if any
    // Due to possible introduce of new constants in previous step vars is assigned via special method to honor new constants
    // Assignation can work with simple variables and with multidimensional arrays - for ex. 'sn_data[groups][test]'
    // New values from module variables will overwrite previous values (for root variables) and array elements with corresponding indexes (for arrays)
    // Constants as array indexes are honored - it's make valid such declarations as 'sn_data[ques][QUE_STRUCTURES]'
    $this->manifest['vars'] = $this->__assign_vars();
    if(!empty($this->manifest['vars'])) {
      $vars_assigned = array();
      foreach($this->manifest['vars'] as $var_name => $var_value) {
        $sub_vars = explode('[', str_replace(']', '', $var_name));
        $var_name = $sub_vars[0];

        if(!isset($vars_assigned[$var_name])) {
          $vars_assigned[$var_name] = true;
          global $$var_name;
        }

        $pointer = &$$var_name;
        if(($n = count($sub_vars)) > 1) {
          for($i = 1; $i < $n; $i++) {
            if(defined($sub_vars[$i])) {
              $sub_vars[$i] = constant($sub_vars[$i]);
            }

            if(!isset($pointer[$sub_vars[$i]]) && $i != $n) {
              $pointer[$sub_vars[$i]] = array();
            }
            $pointer = &$pointer[$sub_vars[$i]];
          }
        }

        if(!isset($pointer) || !is_array($pointer)) {
          $pointer = $var_value;
        } elseif(is_array($$var_name)) {
          $pointer = array_merge_recursive_numeric($pointer, $var_value);
        }
      }
    }

    // Overriding function if any
    global $functions;
    sn_sys_handler_add($functions, $this->manifest['functions'], $this);

    // Patching game menu - if any
    global $sn_menu_extra, $sn_menu_admin_extra;
    isset($this->manifest['menu']) and $this->__patch_menu($sn_menu_extra, $this->manifest['menu']);
    isset($this->manifest['menu_admin']) and $this->__patch_menu($sn_menu_admin_extra, $this->manifest['menu_admin']);

    global $sn_mvc;
    foreach($sn_mvc as $handler_type => &$handler_data) {
      sn_sys_handler_add($handler_data, $this->manifest['mvc'][$handler_type], $this, $handler_type);
    }

    if(isset($this->manifest['i18n']) && is_array($this->manifest['i18n']) && !empty($this->manifest['i18n'])) {
      foreach($this->manifest['i18n'] as $i18n_page_name => &$i18n_file_list) {
        foreach($i18n_file_list as &$i18n_file_data) {
          if(is_array($i18n_file_data) && !$i18n_file_data['path']) {
            $i18n_file_data['path'] = $this->manifest['root_relative'];
          }
        }
        if(!isset($sn_mvc['i18n'][$i18n_page_name])) {
          $sn_mvc['i18n'][$i18n_page_name] = array();
        }
        $sn_mvc['i18n'][$i18n_page_name] += $i18n_file_list;
      }
    }

    if(!empty($this->manifest['javascript']) && is_array($this->manifest['javascript'])) {
      foreach($this->manifest['javascript'] as $javascript_page_name => &$javascript_list) {
        !isset($sn_mvc['javascript'][$javascript_page_name]) ? $sn_mvc['javascript'][$javascript_page_name] = array() : false;
        foreach($javascript_list as $script_name => &$script_content) {
          $sn_mvc['javascript'][$javascript_page_name][$script_name] = $script_content;
        }
      }
    }

    if(!empty($this->manifest['css']) && is_array($this->manifest['css'])) {
      foreach($this->manifest['css'] as $javascript_page_name => &$javascript_list) {
        !isset($sn_mvc['css'][$javascript_page_name]) ? $sn_mvc['css'][$javascript_page_name] = array() : false;
        foreach($javascript_list as $script_name => &$script_content) {
          $sn_mvc['css'][$javascript_page_name][$script_name] = $script_content;
        }
      }
    }

    if(!empty($this->manifest['navbar_prefix_button']) && is_array($this->manifest['navbar_prefix_button'])) {
      foreach($this->manifest['navbar_prefix_button'] as $button_image => $button_url_relative) {
        $sn_mvc['navbar_prefix_button'][$button_image] = $button_url_relative;
      }
    }

    if(!empty($this->manifest['navbar_main_button']) && is_array($this->manifest['navbar_main_button'])) {
      foreach($this->manifest['navbar_main_button'] as $button_image => $button_url_relative) {
        $sn_mvc['navbar_main_button'][$button_image] = $button_url_relative;
      }
    }
  }

  function check_status() {
  }

  /**
   * Register pages in $manifest['mvc']['pages'] for further use
   *
   * @param string[] $pages - array of records ['pageName' => 'pageFile']. 'pageFile' is currently unused
   */
  protected function __mvcRegisterPagesOld($pages) {
    !is_array($this->manifest['mvc']['pages']) ? $this->manifest['mvc']['pages'] = array() : false;
    if(is_array($pages) && !empty($pages)) {
      $this->manifest['mvc']['pages'] = array_merge($this->manifest['mvc']['pages'], $pages);
    }
  }

}
