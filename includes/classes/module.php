<?php

class sn_module
{
}

abstract class sn_module_payment extends sn_module
{
//  abstract protected function payment_sign();

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
