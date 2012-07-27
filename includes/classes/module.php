<?php

class sn_module
{
  public $manifest = array(
    'package' => 'core',
    'name' => 'sn_module',
    'version' => '1c0',
    'copyright' => 'Project "SuperNova.WS" #35a8.1
# copyright © 2009-2012 Gorlum',

//    'require' => null,
    'root_relative' => '',

    'installed' => true,
    'active' => true,

    // 'constants' array - contents of this array would be instaled into engine
    'constants' => array(
//      'UNIT_STRUCTURE_NEW' => 999999,
    ),

    'vars' => array(), // Just a placeholder. vars assigned via special method __assign_vars(). See below

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
    'menu' => array(
    ),

    // 'page' array - defines pages which will handle this module and appropriate handlers
    'page' => array(
    ),
  );

  protected $config = array();

  function __assign_vars()
  {
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

  function sn_module($filename = __FILE__)
  {
    // Getting module PHP class name
    $class_module_name = get_class($this);

    // Getting module root relative to SN
    $this->manifest['root_relative'] = $module_root_relative = str_replace(array(SN_ROOT_PHYSICAL, basename($filename)), '', str_replace('\\', '/', $filename));

    // TODO: Load configuration from DB. Manifest setting
    // Trying to load configuration from file
    if(file_exists($config_filename = dirname($filename) . '/config.php'))
    {
      include($config_filename);
      $module_config_array = $class_module_name . '_config';
      $this->config = $$module_config_array;
    }

    // Registering module
    global $sn_module;
    $sn_module[$class_module_name] = $this;

    // Checking module status - is it installed and active
    $this->check_status();
    if(!$this->manifest['active'])
    {
      return;
    }

    // Setting constants - if any
    if(isset($this->manifest['constants']) && is_array($this->manifest['constants']) && !empty($this->manifest['constants']))
    {
      foreach($this->manifest['constants'] as $constant_name => $constant_value)
      {
        if(!defined($constant_name))
        {
          define($constant_name, $constant_value);
        }
      }
    }

    // Adding vars - if any
    // Due to possible introduce of new constants in previous step vars is assigned via special method to honor new constants
    // Assignation can work with simple variables and with multidimensional arrays - for ex. 'sn_data[groups][test]'
    // New values from module variables will overwrite previous values (for root variables) and array elements with corresponding indexes (for arrays)
    // Constants as array indexes are honored - it's make valid such declarations as 'sn_data[ques][QUE_STRUCTURES]'
    $this->manifest['vars'] = $this->__assign_vars();
    if(!empty($this->manifest['vars']))
    {
      $vars_assigned = array();
      foreach($this->manifest['vars'] as $var_name => $var_value)
      {
        $sub_vars = explode('[', str_replace(']', '', $var_name));
        $var_name = $sub_vars[0];

        if(!isset($vars_assigned[$var_name]))
        {
          $vars_assigned[$var_name] = true;
          global $$var_name;
        }

        $pointer = &$$var_name;
        if(($n = count($sub_vars)) > 1)
        {
          for($i = 1; $i < $n; $i++)
          {
            if(defined($sub_vars[$i]))
            {
              $sub_vars[$i] = constant($sub_vars[$i]);
            }

            if(!isset($pointer[$sub_vars[$i]]) && $i != $n)
            {
              $pointer[$sub_vars[$i]] = array();
            }
            $pointer = &$pointer[$sub_vars[$i]];
          }
        }

        if(!isset($pointer) || !is_array($pointer))
        {
          $pointer = $var_value;
        }
        elseif(is_array($$var_name))
        {
          $pointer = $var_value + $pointer;
        }
      }
    }
    // Overriding function if any
    global $functions;
    sn_sys_handler_add($functions, $this->manifest['functions'], $this);//$class_module_name
/*
    if(isset($this->manifest['functions']) && is_array($this->manifest['functions']) && !empty($this->manifest['functions']))
    {
      global $functions;

      foreach($this->manifest['functions'] as $function_name => $override_with)
      {
        $overwrite = $override_with[0] == '*';
        if($overwrite)
        {
          $override_with = substr($override_with, 1);
        }

        if(($point_position = strpos($override_with, '.')) === false)
        {
          $override_with = array($class_module_name, $override_with);
        }
        elseif($point_position == 0)
        {
          $override_with = substr($override_with, 1);
        }
        else
        {
          $override_with = array(substr($override_with, 0, $point_position), substr($override_with, $point_position + 1));
        }

        if($overwrite)
        {
          $functions[$function_name] = array();
        }
        elseif(!isset($functions[$function_name]))
        {
          $functions[$function_name] = array();
          if(is_callable("sn_{$function_name}"))
          {
            $functions[$function_name][] = "sn_{$function_name}";
          }
        }

        $functions[$function_name][] = $override_with;
      }
    }
*/
    // Pathcing game menu - if any
    if(isset($this->manifest['menu']) && is_array($this->manifest['menu']) && !empty($this->manifest['menu']))
    {
      global $sn_menu_extra;

      foreach($this->manifest['menu'] as $menu_item_name => $menu_item_data)
      {
        $sn_menu_extra[$menu_item_name] = $menu_item_data;
      }
    }

    global $sn_mvc;
    foreach($sn_mvc as $handler_type => &$handler_data)
    {
      sn_sys_handler_add($handler_data, $this->manifest['mvc'][$handler_type], $this, $handler_type);
    }

    if(isset($this->manifest['i18n']) && is_array($this->manifest['i18n']) && !empty($this->manifest['i18n']))
    {
      global $sn_i18n;
      foreach($this->manifest['i18n'] as $i18n_page_name => &$i18n_file_list)
      {
        foreach($i18n_file_list as &$i18n_file_data)
        {
          if(is_array($i18n_file_data) && !$i18n_file_data['path'])
          {
            $i18n_file_data['path'] = $module_root_relative;
          }
        }
        if(!isset($sn_i18n['pages'][$i18n_page_name]))
        {
          $sn_i18n['pages'][$i18n_page_name] = array();
        }
        $sn_i18n['pages'][$i18n_page_name] += $i18n_file_list;
      }
    }
  }

  function check_status()
  {
  }
}

abstract class sn_module_payment extends sn_module
{
  static $bonus_table = array(
    100000 => 0.1,
    200000 => 0.2,
    300000 => 0.3,
    400000 => 0.4,
    500000 => 0.5,
  );

  // Function converts money values between currencies
  function currency_convert($value, $currency_from = '', $currency_to = '')
  {
    return $value;
  }

  // Function calculates bonused DM amount for bulk purchase and ($direct = false) vice versa
  static function bonus_calculate($dark_matter, $direct = true)
  {
    $bonus = 0;
    $dark_matter_new = $dark_matter;
    if(!empty(self::$bonus_table) && $dark_matter >= $bonus_table[0])
    {
      if($direct)
      {
        foreach(self::$bonus_table as $dm_for_bonus => $multiplyer)
        {
          if($dm_for_bonus <= $dark_matter)
          {
            $dark_matter_new = $dark_matter * (1 + $multiplyer);
          }
          else
          {
            break;
          }
        }
      }
      else
      {

        foreach(self::$bonus_table as $dm_for_bonus => $multiplyer)
        {
          $temp = $dm_for_bonus * (1 + $multiplyer);
          if($dark_matter >= $temp)
          {
            $dark_matter_new = round($dark_matter / (1 + $multiplyer));
          }
          else
          {
            break;
          }
        }
      }
    }

    return $dark_matter_new;
  }

  // Function calculates amount of dark_matter for entered money and vice versa
  static function exchange($dark_matter = 0, $money = 0, $currency = '')
  {
    global $config;

    if(!$dark_matter && !$money)
    {
      return 0;
    }

    $currency = $currency ? $currency : $config->payment_currency_default;
    if($money)
    {
      $dark_matter = $money * $config->payment_lot_size / $config->payment_lot_price;
/*
      $bonus = ($dark_matter - ($dark_matter % 100000)) / 100000 / 10;
      $bonus = min(0.5, $bonus);
      $dark_matter *= 1 + $bonus;
*/
      return floor($dark_matter);
    }
    elseif($dark_matter)
    {
      $money = $dark_matter * $config->payment_lot_price / $config->payment_lot_size;

      return round($money, 2);
    }
  }
}

/*
  // Function calculates amount of dark_matter for entered money and vice versa
  static function exchange($dark_matter = 0, $money = 0, $currency = '')
  {
    if(!$dark_matter && !$money)
    {
      return 0;
    }

    global $config;
    $currency = $currency ? $currency : $config->payment_currency_default;
    if($money)
    {
      $dark_matter = $money  * $config->payment_lot_size / $config->payment_lot_price;
      $bonus = ($dark_matter - ($dark_matter % 100000)) / 100000 / 10;
      $bonus = min(0.5, $bonus);
      $dark_matter *= 1 + $bonus;

      return floor($dark_matter);
    }
    elseif($dark_matter)
    {
      $money = round($dark_matter / $config->payment_lot_size * $config->payment_lot_price, 2);

      return $money;
    }
  }
}
*/
?>
