<?php

/** Created by Gorlum 21.10.2025 18:05 */

if ( ! function_exists('pre')) {
  // Debug tools v2025-10-21.01

  define('START', microtime(true)); // SHOULD NEVER BE REMOVED!

  function dieHere($msg = '')
  {
    $p = debug_backtrace()[1];
    print("<br />\nDied: {$p['file']}@{$p['line']}<br />\n" . ($msg ? 'Die message: ' . $msg . "<br />\n" : ''));
    die();
  }

  /**
   * @param mixed $value     <p>The variable you want to export.</p>
   * @param mixed ...$values [optional]
   *
   * @return void
   */
  function pre()
  {
    if (func_num_args() <= 0) {
      return;
    }

    foreach (func_get_args() ?: [] as $var) {
      print "<pre>";
      var_export($var);
//                print_r(
//                    $var === null ? 'NULL' :
//                        (($type = gettype($var)) == 'object' || $type == 'array'
//                            ? $var :
//                            ($type === 'string'
//                                ? $type . '(' . strlen($var) . ') `' . $var . '`' :
//                                ($type == 'boolean'
//                                    ? ($var ? 'true' : 'false')
//                                    : $type . ' ' . print_r($var, true)
//                                )
//                            )
//                        )
//                );
      print "</pre>";
    }
    here();
  }

  /**
   * @return void
   */
  function here()
  {
    $trace = debug_backtrace();
    $i     = 0;
    while (in_array(($trace[$i++])['function'], ['pre', 'pred', 'prej', 'predj', 'hered', __FUNCTION__])) { /**/
    }
    $p = $trace[$i - 2];
    // $p     = in_array($trace[1]['function'], ['pred', 'prej', 'predj',]) ? $trace[1] : $trace[0];
//            print("\n{$p['file']}@{$p['line']}<br />\n" . (is_string($die) ? 'Die message: ' . $die . "<br />\n" : ''));
    print("\n{$p['file']}@{$p['line']}<br />\n");
  }

  function hered() {
    print 'Stopped at ' . TIME_NOW_SQL_MICRO . ' ';
    here();
    die();
  }

  function pred()
  {
    call_user_func_array('pre', func_get_args());
    die();
  }

  /**
   * @param mixed $value     <p>The variable you want to export.</p>
   * @param mixed ...$values [optional]
   *
   * @return void
   */
  function prej()
  {
    $args = [];
    foreach (func_get_args() as $arg) {
      $args[] = json_encode($arg, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    if (func_num_args() <= 0) {
      return;
    }

    foreach ($args as $var) {
      print "<pre>";
      print($var);
      print "</pre>";
    }
    here();
  }

  function predj()
  {
    call_user_func_array('prej', func_get_args());
    die();
  }

  function gLog($data, $force = true, $filename = '_log.txt')
  {
    if ($force) {
      if (is_array($data)) {
        $data = implode('', $data);
      }

      @file_put_contents(
        __DIR__ . '/../../application/logs/' . $filename,
        ($data ? debug . tools . phpdate('Y-m-d H:i:s - ') . $data : '') . "\n", FILE_APPEND);
    }
  }

  function dT($msg = '', $die = null)
  {
    var_dump((! $msg ?: $msg . ' ') . (microtime(true) - START));
    $die === null ?: die($die);
  }

  /**
   * Nicer dumper for servers w/o xDebug installed
   *
   * @param mixed  $var Var to dump
   * @param string $msg Message to preface dump
   * @param mixed  $die Should we die() after dump and if yes - how we should die()
   *
   * @return void
   */
  function pdump($var, $msg = '', $die = null)
  {
    echo '<pre>' . ($msg ? "$msg = " : '');
    var_dump($var);
    echo '</pre>';
    if ($die !== null) {
      die($die);
    }
  }

  function ptrace($die = false)
  {
    $trace = debug_backtrace();
    array_shift($trace);
    foreach ($trace as &$item) {
      if ( ! empty($item['object'])) {
        $item['object'] = class_name($item['object']);
      }
    }
    pdump($trace);

    if ($die) {
      die();
    }
  }

// Gorlum's debug tools [END] ------------------------------

  /**
   * @param null|string $msg Message to print. If NULL - nothing printed, just time logged
   * @param null|mixed  $die If not NULL - die, using $die as message/exit code/etc ( @see die() )
   *
   * @return void
   */
  function dT2($msg = '', $die = null)
  {
    static $events = [];

    $current = (object)['ts' => microtime(true), 'msg' => $msg,];

    $prev = end($events);
    if ( ! is_object($prev)) {
      $prev = (object)['ts' => START, 'msg' => 'COUNTER START',];
    }
    $events[] = $current;

    //$fromStart = $current->ts - START;
    $fromLast = $current->ts - $prev->ts;
    if ($msg !== null) {
      print(number_format($fromLast, 8) . "s" . (! $msg ?: ' - ' . $msg) . "<br />\n\r");
    }
    $die === null ?: die($die);
  }
}
