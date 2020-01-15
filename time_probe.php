<?php

use Player\playerTimeDiff;

define('IN_AJAX', true);

require_once('common.' . substr(strrchr(__FILE__, '.'), 1));

/*
$time_local  = $time_server + $time_diff
$time_diff   = $time_local  - $time_server
$time_server = $time_local  - $time_diff
*/

if ($font_size = sys_get_param_str('font_size')) {
  if (strpos($font_size, '%') !== false) {
    // Размер шрифта в процентах
    $font_size = min(max(floatval($font_size), FONT_SIZE_PERCENT_MIN), FONT_SIZE_PERCENT_MAX) . '%';
  } elseif (strpos($font_size, 'px') !== false) {
    // Размер шрифта в пикселях
    $font_size = min(max(floatval($font_size), FONT_SIZE_PIXELS_MIN), FONT_SIZE_PIXELS_MAX) . 'px';
  } else {
    // Не мышонка, не лягушка...
    $font_size = FONT_SIZE_PERCENT_DEFAULT_STRING;
  }

  sn_setcookie(SN_COOKIE_F, $font_size, SN_TIME_NOW + PERIOD_YEAR);
  SN::$user_options[PLAYER_OPTION_BASE_FONT_SIZE] = $font_size;
} elseif (isParamExists('webpSupport')) {
  sn_setcookie(SN_COOKIE_WEBP, sys_get_param_int('webpSupport'), SN_TIME_NOW + PERIOD_HOUR);
} else {
  echo playerTimeDiff::timeProbeAjax();
}
