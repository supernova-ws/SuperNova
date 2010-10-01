<?php

function sys_user_options_pack(&$user)
{
  global $user_options;

  $options = '';
  foreach($user_options as $option_name => $option_value)
  {
    if(!$user[$option_name])
    {
      $user[$option_name] = $option_value;
    }
    $options .= "{$option_name}^{$user[$option_name]}|";
  }

  return $options;
}

function sys_user_options_unpack(&$user)
{
  global $user_options;

  $options = $user_options;

  $opt_unpack = explode('|', $user['options']);
  foreach($opt_unpack as $option)
  {
    if($option)
    {
      $option = explode('^', $option);
      if(isset($options[$option[0]]))
      {
        $options[$option[0]] = $option[1];
        $user[$option[0]] = $option[1];
      }
    }
  }

  return $options;
}

?>