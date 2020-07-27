<?php

namespace Player;
/**
 * User: Gorlum
 * Date: 14.10.2015
 * Time: 0:28
 */
class playerTimeDiff {
  const TIME_DIFF = 0; // Чистая разница в ходе часов в секундах
  const TIME_DIFF_UTC_OFFSET = 1; // Разница между часовыми поясами в секундах
  const TIME_DIFF_FORCED = 2;
  const TIME_DIFF_MEASURE_TIME = 3; // Время когда происходил замер

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


  static protected function user_time_diff_probe() {
    // Определяем время в браузере
    $client_time = strtotime(sys_get_param('client_gmt')); // Попытка определить по GMT-времени браузера. В нём будет часовой пояс (GMT), поэтому время будет автоматически преобразовано в часовой пояс сервера
    !$client_time ? $client_time = round(sys_get_param_float('timeBrowser') / 1000) : false; // Попытка определить по Date.valueOf() - миллисекунды с начала эпохи UNIX_TIME
    !$client_time ? $client_time = SN_TIME_NOW : false; // Если все попытки провалились - тупо берем время сервера

    $result = array(
      self::TIME_DIFF              => $client_time - SN_TIME_NOW,
      self::TIME_DIFF_UTC_OFFSET   => ($browser_utc_offset = sys_get_param_int('utc_offset')) ? $browser_utc_offset - date('Z') : 0,
      self::TIME_DIFF_FORCED       => sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED'),
      self::TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
    );

    return $result;
  }

  static protected function user_time_diff_set($user_time_diff) {
    // Переопределяем массив, что бы элементы были в правильном порядке
    !is_array($user_time_diff) ? $user_time_diff = [] : false;
    $user_time_diff = self::sortDiffArray($user_time_diff);
    $user_time_diff[self::TIME_DIFF_MEASURE_TIME] = SN_TIME_SQL;

    $user_time_diff_str = implode(';', $user_time_diff);
    sn_setcookie(SN_COOKIE_T, $user_time_diff_str, SN_TIME_NOW + PERIOD_MONTH);
  }

  static protected function user_time_diff_get() {
    $user_time_diff = !empty($_COOKIE[SN_COOKIE_T]) ? explode(';', $_COOKIE[SN_COOKIE_T]) : null;
    !is_array($user_time_diff) ? $user_time_diff = [] : false;
    $user_time_diff = self::sortDiffArray($user_time_diff);

    return $user_time_diff;
  }

  static public function sn_options_timediff($timeDiff, $force, $clear) {
    $user_time_diff = playerTimeDiff::user_time_diff_get();
    if ($force) {
      playerTimeDiff::user_time_diff_set(array(
        self::TIME_DIFF              => $timeDiff,
        self::TIME_DIFF_UTC_OFFSET   => 0,
        self::TIME_DIFF_FORCED       => 1,
        self::TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    } elseif ($clear || $user_time_diff[self::TIME_DIFF_FORCED]) {
      playerTimeDiff::user_time_diff_set(array(
        self::TIME_DIFF              => '',
        self::TIME_DIFF_UTC_OFFSET   => 0,
        self::TIME_DIFF_FORCED       => 0,
        self::TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
      ));
    }
  }

  /**
   * @return int
   */
  static public function timeProbeAjax() {
    $userTimeDiff = playerTimeDiff::user_time_diff_get();
    if ($userTimeDiff[self::TIME_DIFF_FORCED]) {
      $time_diff = intval($userTimeDiff[self::TIME_DIFF]);
    } else {
      $userTimeDiff = playerTimeDiff::user_time_diff_probe();
      playerTimeDiff::user_time_diff_set($userTimeDiff);
      $time_diff = $userTimeDiff[self::TIME_DIFF] + $userTimeDiff[self::TIME_DIFF_UTC_OFFSET];
    }

    return $time_diff;
  }

  /**
   * @return mixed
   */
  static public function defineTimeDiff() {
    $user_time_diff = playerTimeDiff::user_time_diff_get();

    $time_diff = $user_time_diff[self::TIME_DIFF] + $user_time_diff[self::TIME_DIFF_UTC_OFFSET];

    define('SN_CLIENT_TIME_DIFF', $time_diff);
    define('SN_CLIENT_TIME_LOCAL', SN_TIME_NOW + SN_CLIENT_TIME_DIFF);
    define('SN_CLIENT_TIME_DIFF_GMT', $user_time_diff[self::TIME_DIFF]);

    return $time_diff; // Разница в GMT-времени между клиентом и сервером. Реальная разница в ходе часов
  }

  /**
   * @return int
   */
  static public function timeDiffTemplate() {
    $user_time_diff          = playerTimeDiff::user_time_diff_get();
    $user_time_measured_unix = intval(isset($user_time_diff[self::TIME_DIFF_MEASURE_TIME]) ? strtotime($user_time_diff[self::TIME_DIFF_MEASURE_TIME]) : 0);
    $measureTimeDiff         = intval(
      empty($user_time_diff[self::TIME_DIFF_FORCED])
      &&
      (SN_TIME_NOW - $user_time_measured_unix > PERIOD_HOUR || $user_time_diff[self::TIME_DIFF] == '')
    );

    return $measureTimeDiff;
  }

  /**
   * @return int
   */
  static public function getTimeDiffForced() {
    $user_time_diff        = playerTimeDiff::user_time_diff_get();
    $user_time_diff_forced = $user_time_diff[self::TIME_DIFF_FORCED];

    return $user_time_diff_forced;
  }

  /**
   * @param array $user_time_diff
   *
   * @return array
   */
  protected static function sortDiffArray(array $user_time_diff) {
    $user_time_diff = [
      self::TIME_DIFF              => isset($user_time_diff[self::TIME_DIFF]) ? $user_time_diff[self::TIME_DIFF] : '',
      self::TIME_DIFF_UTC_OFFSET   => isset($user_time_diff[self::TIME_DIFF_UTC_OFFSET]) ? $user_time_diff[self::TIME_DIFF_UTC_OFFSET] : 0,
      self::TIME_DIFF_FORCED       => isset($user_time_diff[self::TIME_DIFF_FORCED]) ? $user_time_diff[self::TIME_DIFF_FORCED] : 0,
      self::TIME_DIFF_MEASURE_TIME => isset($user_time_diff[self::TIME_DIFF_MEASURE_TIME]) ? $user_time_diff[self::TIME_DIFF_MEASURE_TIME] : '2000-01-01',
    ];

    return $user_time_diff;
  }

}
