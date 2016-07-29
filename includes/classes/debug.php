<?php
/**
 * Some helpers to sweeten dev's life
 */

/*
 * debug.php ::  Clase Debug, maneja reporte de eventos
 *
 * V4.0 copyright 2010-2011 by Gorlum for http://supernova.ws
 *  [!] Merged `errors` to `logs`
 *  [+] Now debugger can work with database detached. All messages would be dumped to page
 *  [+] Now `logs` has both human-readable and machine-readable fields
 *
 * V3.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [+] Full rewrtie & optimize
 *  [*] Now there is fallback procedure if no link to db detected
 *
 * V2.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [*] Now error also contains backtrace - to see exact way problem comes
 *  [*] New method 'warning' sends message to dedicated SQL-table for non-errors
 *
 * V1.0 Created by Perberos. All rights reversed (C) 2006
 *
 *  Experiment code!!!
 *
 * vamos a experimentar >:)
 * le veo futuro a las classes, ayudaria mucho a tener un codigo mas ordenado...
 * que esperabas!!! soy newbie!!! D':<
*/

defined('INSIDE') || die();

if(php_sapi_name() == "cli") {
  // In cli-mode
  define('__DEBUG_CRLF', "\r\n");
  define('__DEBUG_LINE', '-------------------------------------------------' . __DEBUG_CRLF);
} else {
  // Not in cli-mode
  define('__DEBUG_CRLF', '<br />');
  define('__DEBUG_LINE', '<hr />');
}


class debug {
  var $log, $numqueries;
  var $log_array;

  private $log_file_handler = null;

  function log_file($message, $ident_change = 0) {
    static $ident = 0;

    if(!defined('SN_DEBUG_LOG')) {
      return;
    }

    if($this->log_file_handler === null) {
      $this->log_file_handler = @fopen(SN_ROOT_PHYSICAL . '/.logs/supernova.log', 'a+');
      @fwrite($this->log_file_handler, "\r\n\r\n");
    }
    $ident_change < 0 ? $ident += $ident_change * 2 : false;
    if($this->log_file_handler) {
      @fwrite($this->log_file_handler, date(FMT_DATE_TIME_SQL, time()) . str_repeat(' ', $ident + 1) . $message . "\r\n");
    }
    $ident_change > 0 ? $ident += $ident_change * 2 : false;
  }

  function debug() {
    $this->vars = $this->log = '';
    $this->numqueries = 0;
  }

  function add($mes) {
    $this->log .= $mes;
    $this->numqueries++;
  }

  function add_to_array($mes) {
    $this->log_array[] = $mes;
  }

  function echo_log() {
    echo '<br><table><tr><td class=k colspan=4><a href="' . SN_ROOT_PHYSICAL . "admin/settings.php\">Debug Log</a>:</td></tr>{$this->log}</table>";
    die();
  }

  function compact_backtrace($backtrace, $long_comment = false) {
    static $exclude_functions = array('doquery', 'db_query', 'db_get_record_list', 'db_user_by_id', 'db_get_user_by_id');

    $result = array();
    $transaction_id = classSupernova::db_transaction_check(false) ? classSupernova::$transaction_id : classSupernova::$transaction_id++;
    $result[] = "tID {$transaction_id}";
    foreach($backtrace as $a_trace) {
      if(in_array($a_trace['function'], $exclude_functions)) {
        continue;
      }
      $function =
        ($a_trace['type']
          ? ($a_trace['type'] == '->'
            ? "({$a_trace['class']})" . get_class($a_trace['object'])
            : $a_trace['class']
          ) . $a_trace['type']
          : ''
        ) . $a_trace['function'] . '()';

      $file = str_replace(SN_ROOT_PHYSICAL, '', str_replace('\\', '/', $a_trace['file']));

      // $result[] = "{$function} ({$a_trace['line']})'{$file}'";
      $result[] = "{$function} - '{$file}' Line {$a_trace['line']}";

      if(!$long_comment) {
        break;
      }
    }


    // $result = implode(',', $result);

    return $result;
  }

  function dump($dump = false, $force_base = false, $deadlock = false) {
    global $user, $planetrow;

    if($dump === false) {
      return;
    }

    $error_backtrace = array();
    $base_dump = false;

    if($force_base === true) {
      $base_dump = true;
    }

    if($dump === true) {
      $base_dump = true;
    } else {
      if(!is_array($dump)) {
        $dump = array('var' => $dump);
      }

      foreach($dump as $dump_var_name => $dump_var) {
        if($dump_var_name == 'base_dump') {
          $base_dump = $dump_var;
        } else {
          $error_backtrace[$dump_var_name] = $dump_var;
        }
      }
    }

    if($deadlock && ($q = db_fetch(classSupernova::$db->mysql_get_innodb_status()))) {
      $error_backtrace['deadlock'] = explode("\n", $q['Status']);
      $error_backtrace['locks'] = SnCache::getLocks();
      $error_backtrace['cSN_data'] = SnCache::getData();
      foreach($error_backtrace['cSN_data'] as &$location) {
        foreach($location as $location_id => &$location_data) {
          $location_data = isset($location_data['username']) ? $location_data['username'] :
            (isset($location_data['name']) ? $location_data['name'] : $location_id);
        }
      }
      $error_backtrace['cSN_queries'] = SnCache::getQueries();
    }

    if($base_dump) {
      if(is_array($this->log_array) && count($this->log_array) > 0) {
        foreach($this->log_array as $log) {
          $error_backtrace['queries'][] = $log;
        }
      }

      $error_backtrace['backtrace'] = debug_backtrace();
      unset($error_backtrace['backtrace'][1]);
      unset($error_backtrace['backtrace'][0]);
      $error_backtrace['$_GET'] = $_GET;
      $error_backtrace['$_POST'] = $_POST;
      $error_backtrace['$_REQUEST'] = $_REQUEST;
      $error_backtrace['$_COOKIE'] = $_COOKIE;
      $error_backtrace['$_SESSION'] = $_SESSION;
      $error_backtrace['$_SERVER'] = $_SERVER;
      $error_backtrace['user'] = $user;
      $error_backtrace['planetrow'] = $planetrow;
    }

    return $error_backtrace;
  }

  function error_fatal($die_message, $details = 'There is a fatal error on page') {
    // TODO - Записывать детали ошибки в лог-файл
    die($die_message);
  }

  function error($message = 'There is a error on page', $title = 'Internal Error', $error_code = 500, $dump = true) {
    global $sys_stop_log_hit, $sys_log_disabled, $user;

    if(empty(classSupernova::$db->connected)) {
      // TODO - писать ошибку в файл
      die('SQL server currently unavailable. Please contact Administration...');
    }

    sn_db_transaction_rollback();

    if(classSupernova::$config->debug == 1) {
      echo "<h2>{$title}</h2><br><font color=red>{$message}</font><br><hr>";
      echo "<table>{$this->log}</table>";
    }

    $fatal_error = 'Fatal error: cannot write to `logs` table. Please contact Administration...';

    $error_text = db_escape($message);
    $error_backtrace = $this->dump($dump, true, strpos($message, 'Deadlock') !== false);

    $userId = empty($user['id']) ? 0 : $user['id'];

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '" . time() . "', `log_code` = '" . db_escape($error_code) . "', `log_sender` = '" . db_escape($userId) . "',
        `log_username` = '" . db_escape($user['user_name']) . "', `log_title` = '" . db_escape($title) . "',  `log_text` = '" . db_escape($message) . "',
        `log_page` = '" . db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE))) . "'" .
//        ($error_backtrace ? ", `log_dump` = '" . db_escape(serialize($error_backtrace)) . "'" : '') . ";";
      ", `log_dump` = '" . ($error_backtrace ? db_escape(serialize($error_backtrace)) : '') . "'" . ";";
      doquery($query, '', false, true) or die($fatal_error . classSupernova::$db->db_error());

      $message = "Пожалуйста, свяжитесь с админом, если ошибка повторится. Ошибка №: <b>" . classSupernova::$db->db_insert_id() . "</b>";

      $sys_stop_log_hit = true;
      $sys_log_disabled = true;
      !function_exists('message') ? die($message) : message($message, 'Ошибка', '', 0, false);
    } else {
//        // TODO Здесь надо писать в файло
      ob_start();
      print("<hr>User ID {$user['id']} raised error code {$error_code} titled '{$title}' with text '{$error_text}' on page {$_SERVER['SCRIPT_NAME']}");

      foreach($error_backtrace as $name => $value) {
        print(__DEBUG_LINE);
        pdump($value, $name);
      }
      ob_end_flush();
      die();
    }
  }

  function warning($message, $title = 'System Message', $log_code = 300, $dump = false) {
    global $user, $sys_log_disabled;

    if(empty(classSupernova::$db->connected)) {
      // TODO - писать ошибку в файл
      die('SQL server currently unavailable. Please contact Administration...');
    }

    $error_backtrace = $this->dump($dump, false);

    $userId = empty($user['id']) ? 0 : $user['id'];

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '" . time() . "', `log_code` = '" . db_escape($log_code) . "', `log_sender` = '" . db_escape($userId) . "',
        `log_username` = '" . db_escape($user['user_name']) . "', `log_title` = '" . db_escape($title) . "',  `log_text` = '" . db_escape($message) . "',
        `log_page` = '" . db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE))) . "'" .
        ", `log_dump` = '" . ($error_backtrace ? db_escape(serialize($error_backtrace)) : '') . "'" . ";";
      doquery($query, '', false, true);
    } else {
//        // TODO Здесь надо писать в файло
      print("<hr>User ID {$user['id']} made log entry with code {$log_code} titled '{$title}' with text '{$message}' on page {$_SERVER['SCRIPT_NAME']}");
    }
  }
}

// Copyright (c) 2009-2010 Gorlum for http://supernova.ws
// Dump variables nicer then var_dump()

function dump($value, $varname = null, $level = 0, $dumper = '') {
  if(isset($varname)) {
    $varname .= " = ";
  }

  if($level == -1) {
    $trans[' '] = '&there4;';
    $trans["\t"] = '&rArr;';
    $trans["\n"] = '&para;;';
    $trans["\r"] = '&lArr;';
    $trans["\0"] = '&oplus;';

    return strtr(htmlspecialchars($value), $trans);
  }
  if($level == 0) {
//    $dumper = '<pre>' . mt_rand(10, 99) . '|' . $varname;
    $dumper = mt_rand(10, 99) . '|' . $varname;
  }

  $type = gettype($value);
  $dumper .= $type;

  if($type == TYPE_STRING) {
    $dumper .= '(' . strlen($value) . ')';
    $value = dump($value, '', -1);
  } elseif($type == TYPE_BOOLEAN) {
    $value = ($value ? 'true' : 'false');
  } elseif($type == 'object') {
    $props = get_class_vars(get_class($value));
    $dumper .= '(' . count($props) . ') <u>' . get_class($value) . '</u>';
    foreach($props as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . $key . ' => ';
      $dumper .= dump($value->$key, '', $level + 1);
    }
    $value = '';
  } elseif($type == TYPE_ARRAY) {
    $dumper .= '(' . count($value) . ')';
    foreach($value as $key => $val) {
      $dumper .= "\n" . str_repeat("\t", $level + 1) . dump($key, '', -1) . ' => ';
      $dumper .= dump($val, '', $level + 1);
    }
    $value = '';
  }
  $dumper .= " <b>$value</b>";
//  if($level == 0) {
//    $dumper .= '</pre>';
//  }

  return $dumper;
}

function pdump($value, $varname = null) {
  $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//  print_rr($backtrace);
//  $backtrace = $backtrace[1];

  $caller = '';
  if(defined('SN_DEBUG_PDUMP_CALLER') && SN_DEBUG_PDUMP_CALLER) {
    $caller = (!empty($backtrace[1]['class']) ? $backtrace[1]['class'] : '') .
      (!empty($backtrace[1]['type']) ? $backtrace[1]['type'] : '') .
      $backtrace[1]['function'] .
      (!empty($backtrace[0]['file'])
        ? (
          ' (' . substr($backtrace[0]['file'], SN_ROOT_PHYSICAL_STR_LEN) .
          (!empty($backtrace[0]['line']) ? ':' . $backtrace[0]['line'] : '') .
          ')'
        )
        : ''
      );
    $caller = "\r\n" . $caller;
  }

  print('<pre style="text-align: left; background-color: #111111; color: #0A0; font-family: Courier, monospace !important; padding: 1em 0; font-weight: 800; font-size: 14px;">' .
    dump($value, $varname) .
    $caller .
    '</pre>'
  );
}

function debug($value, $varname = null) {
  pdump($value, $varname);
}

function pr($prePrint = false) {
  if($prePrint) {
    print(__DEBUG_CRLF);
  }
  print(mt_rand() . __DEBUG_CRLF);
}

function pc($prePrint = false) {
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint) {
    print(__DEBUG_CRLF);
  }
  print($_PRINT_COUNT_VALUE . __DEBUG_CRLF);
}

function prep($message) {
  print('<pre>' . $message . '</pre>');
}

function backtrace_no_arg() {
  $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
  array_shift($trace);

  return $trace;
}

function pvar_dump($expression) {
  print('<pre style="text-align: left; background-color: #111111; color: #0A0; font-family: Courier, monospace !important; padding: 1em 0; font-weight: 800; font-size: 14px;">');
  var_dump($expression);
  print('</pre>');
}

/**
 * Smart die() implementation that knew where it's grave
 *
 * @param string $message
 * @param int $level - shift backtrace to X levels back
 */
function pdie($message = '', $level = 0) {
  $backtrace = debug_backtrace();
  for($i = 0; $i < $level; $i++) {
    array_pop($backtrace);
  }

  die(__DEBUG_LINE . ($message ? $message . ' @ ' : '') . $backtrace[0]['file'] . ':' . $backtrace[0]['line']);
}
