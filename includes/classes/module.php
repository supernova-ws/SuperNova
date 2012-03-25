<?php

class sn_module
{
  public $manifest = array(
    'package' => 'core',
    'name' => 'sn_module',
    'version' => '1c0',
    'copyright' => 'Project "SuperNova.WS" #34a9.2# copyright © 2009-2012 Gorlum',

    'installed' => true,
    'active' => true,
  );

  protected $config = array();

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

    // Overriding function if any
    if(isset($this->manifest['functions']))
    {
      global $functions;

      foreach($this->manifest['functions'] as $function_name => $override_with)
      {
        $functions[$function_name] = array($class_module_name, $override_with);
      }
    }
  }

  function check_status()
  {
  }
}

abstract class sn_module_payment extends sn_module
{
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

?>
