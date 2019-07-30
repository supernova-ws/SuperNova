<?php

namespace Player;
/**
 * User: Gorlum
 * Date: 14.10.2015
 * Time: 0:28
 */
class playerTimeDiff {
  /**
   * Разница в ходе часов в секундах. Т.е. разница между GMT-временами браузера и сервера
   * @var int
   */
  public $gmt_diff = 0;
  /**
   * Разница в секундах между часовыми поясами браузера и сервера
   * @var int
   */
  public $zone_offset = 0;
  /**
   * Форсированный пересчёт времени
   *
   * @var int
   */
  public $force_measure = 0;
  /**
   * Метка времени прошлой синхронизации часов
   *
   * @var string
   */
  public $last_measure_time = '2000-01-01';


  static function user_time_diff_probe() {
    // Определяем время в браузере
    $client_time = strtotime(sys_get_param('client_gmt')); // Попытка определить по GMT-времени браузера. В нём будет часовой пояс (GMT), поэтому время будет автоматически преобразовано в часовой пояс сервера
    !$client_time ? $client_time = round(sys_get_param_float('timeBrowser') / 1000) : false; // Попытка определить по Date.valueOf() - миллисекунды с начала эпохи UNIX_TIME
    !$client_time ? $client_time = SN_TIME_NOW : false; // Если все попытки провалились - тупо берем время сервера

    $result = array(
      PLAYER_OPTION_TIME_DIFF              => $client_time - SN_TIME_NOW,
      PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => ($browser_utc_offset = sys_get_param_int('utc_offset')) ? $browser_utc_offset - date('Z') : 0,
      PLAYER_OPTION_TIME_DIFF_FORCED       => sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED'),
      PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
    );

    return $result;
  }

  static function user_time_diff_set($user_time_diff) {
    // Переопределяем массив, что бы элементы были в правильном порядке
    $user_time_diff = array(
      PLAYER_OPTION_TIME_DIFF              => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF] : '',
      PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET] : 0,
      PLAYER_OPTION_TIME_DIFF_FORCED       => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED] : 0,
      PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
    );

    $user_time_diff_str = implode(';', $user_time_diff);
    sn_setcookie(SN_COOKIE_T, $user_time_diff_str, SN_TIME_NOW + PERIOD_MONTH);
  }

  static function user_time_diff_get() {
    $result = !empty($_COOKIE[SN_COOKIE_T]) ? explode(';', $_COOKIE[SN_COOKIE_T]) : null;
    $result = array(
      PLAYER_OPTION_TIME_DIFF              => isset($result[PLAYER_OPTION_TIME_DIFF]) ? $result[PLAYER_OPTION_TIME_DIFF] : '',
      PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => isset($result[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]) ? $result[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET] : 0,
      PLAYER_OPTION_TIME_DIFF_FORCED       => isset($result[PLAYER_OPTION_TIME_DIFF_FORCED]) ? $result[PLAYER_OPTION_TIME_DIFF_FORCED] : 0,
      PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => isset($result[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) ? $result[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME] : '2000-01-01',
    );

    return $result;
  }

  static public function sn_options_timediff($timeDiff, $force, $clear) {
//  $timeDiff = sys_get_param_int('PLAYER_OPTION_TIME_DIFF');
//  $force = sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED');
//  $clear = sys_get_param_int('opt_time_diff_clear');
    $user_time_diff = playerTimeDiff::user_time_diff_get();
    if ($force) {
      playerTimeDiff::user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF              => $timeDiff,
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED       => 1,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    } elseif ($clear || $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
      playerTimeDiff::user_time_diff_set(array(
        PLAYER_OPTION_TIME_DIFF              => '',
        PLAYER_OPTION_TIME_DIFF_UTC_OFFSET   => 0,
        PLAYER_OPTION_TIME_DIFF_FORCED       => 0,
        PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    }
  }

  /**
   * @return int
   */
  static public function timeProbeAjax() {
    $userTimeDiff = playerTimeDiff::user_time_diff_get();
    if ($userTimeDiff[PLAYER_OPTION_TIME_DIFF_FORCED]) {
      $time_diff = intval($userTimeDiff[PLAYER_OPTION_TIME_DIFF]);
    } else {
      $userTimeDiff = playerTimeDiff::user_time_diff_probe();
      playerTimeDiff::user_time_diff_set($userTimeDiff);
      $time_diff = $userTimeDiff[PLAYER_OPTION_TIME_DIFF] + $userTimeDiff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET];
    }

    return $time_diff;
  }

  /**
   * @return mixed
   */
  static public function defineTimeDiff() {
    $user_time_diff = playerTimeDiff::user_time_diff_get();

    $time_diff = $user_time_diff[PLAYER_OPTION_TIME_DIFF] + $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET];

    define('SN_CLIENT_TIME_DIFF', $time_diff);
    define('SN_CLIENT_TIME_LOCAL', SN_TIME_NOW + SN_CLIENT_TIME_DIFF);
    define('SN_CLIENT_TIME_DIFF_GMT', $user_time_diff[PLAYER_OPTION_TIME_DIFF]);

    return $time_diff; // Разница в GMT-времени между клиентом и сервером. Реальная разница в ходе часов
  }

  /**
   * @return int
   */
  static public function timeDiffTemplate() {
    $user_time_diff          = playerTimeDiff::user_time_diff_get();
    $user_time_measured_unix = intval(isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) ? strtotime($user_time_diff[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) : 0);
    $measureTimeDiff         = intval(
      empty($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED])
      &&
      (SN_TIME_NOW - $user_time_measured_unix > PERIOD_HOUR || $user_time_diff[PLAYER_OPTION_TIME_DIFF] == '')
    );

    return $measureTimeDiff;
  }

}
