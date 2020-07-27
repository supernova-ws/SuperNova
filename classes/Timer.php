<?php
/**
 * Created by Gorlum 05.08.2018 8:05
 */

class Timer {
  /**
   * @var array[] $times [
   *                        'time' => (float)microtime(true),
   *                        'location' => (string)filename&line,
   *                        'message' => (string)attachedMessage
   *                     ]
   */
  private static $times = [];

  public static function init() {
    if (empty(static::$times)) {
      static::$times[] = ['time' => microtime(true)];
    }
  }

  /**
   * @param string $message Message to attach to timestamp
   */
  public static function mark($message = '') {
    static::init();

    foreach (debug_backtrace() as $trace) {
      if (empty($trace['class']) || $trace['class'] != static::class) {
        break;
      }

      $realCall = $trace;
    }

    static::$times[] = [
      'time'     => microtime(true),
      'location' => $realCall['file'] . '@' . $realCall['line'],
      'message'  => $message,
    ];
  }


  /**
   * @param string $message
   *
   * @param bool   $fromStart
   *
   * @return string
   */
  public static function elapsed($message = '', $fromStart = false) {
    $prevTime = $fromStart ? static::first()['time'] : static::last()['time'];

    static::mark($message);

    empty($prevTime) ? $prevTime = static::first()['time'] : false;

    return number_format(static::last()['time'] - $prevTime, 6) . 's';
  }

  public static function msg($message, $fromStart = false) {
    print
      $message .
      ($fromStart ? ' FROM START ' : ' in ') .
      static::elapsed($message, $fromStart) .
      ' ---- ' . static::last()['location'] .
      "\n<br />";
  }

  /**
   * @return mixed
   */
  public static function last() {
    return end(static::$times);
  }

  /**
   * @return mixed
   */
  public static function first() {
    return reset(static::$times);
  }

  public static function getLog() {
    return static::$times;
  }

}
