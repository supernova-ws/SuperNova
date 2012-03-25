<?php

class sn_module
{
  public $manifest = array();

  function sn_module($filename = __FILE__)
  {
    global $sn_module;

    $this->manifest['root_relative'] = $module_root_relative = str_replace(array(SN_ROOT_PHYSICAL, basename($filename)), '', str_replace('\\', '/', $filename));

    $module_name = get_class($this);
    $sn_module[$module_name] = $this;

    // Overriding function if any
    if(isset($this->manifest['functions']))
    {
      global $functions;

      foreach($this->manifest['functions'] as $function_name => $override_with)
      {
        $functions[$function_name] = array($module_name, $override_with);
      }
    }
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
    }
  }
}

?>
