<?php
/*
 * debug.class.php ::  Clase Debug, maneja reporte de eventos
 *
 * V3.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [+] Full rewrtie & optimize
 *  [*] Now there is fallback procedure if no $link to db detected
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

if(!defined('INSIDE'))
{
  die("attemp hacking");
}

class debug
{
  var $log, $numqueries;

  function debug()
  {
    $this->vars = $this->log = '';
    $this->numqueries = 0;
  }

  function add($mes)
  {
    $this->log .= $mes;
    $this->numqueries++;
  }

  function echo_log()
  {
    global $ugamela_root_path;

    echo "<br><table><tr><td class=k colspan=4><a href=\"{$ugamela_root_path}admin/settings.php\">Debug Log</a>:</td></tr>{$this->log}</table>";
    die();
  }

  function error($message,$title)
  {
    global $link, $config;

    if($config->debug == 1)
    {
      echo "<h2>{$title}</h2><br><font color=red>{$message}</font><br><hr>";
      echo "<table>{$this->log}</table>";
    }

    global $user, $ugamela_root_path, $phpEx;

    include("{$ugamela_root_path}config.{$phpEx}");

    if(!$link)
    {
      $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      mysql_query('/*!40101 SET NAMES \'cp1251\' */');
      mysql_select_db($dbsettings['name']);

      if(!$link)
      {
        die('mySQL server currently unavailable. Please contact Administration...');
      }
    }

    $fatal_error = 'Fatal error: cannot write to `errors` table. Please contact Administration...';

    mysql_query("INSERT INTO `{$dbsettings['prefix']}errors`
      SET
        `error_sender` = '{$user['id']}' ,
        `error_time` = '".time()."' ,
        `error_type` = '{$title}' ,
        `error_text` = '".mysql_escape_string($message)."' ,
        `error_page` = '".mysql_escape_string($_SERVER['HTTP_REFERER'])."',
        `error_backtrace` = '".mysql_escape_string(dump(debug_backtrace()))."'
      ;") or die($fatal_error);

    $q = mysql_fetch_array(mysql_query("explain select * from {$dbsettings['prefix']}errors;"))
      or die($fatal_error);

    $message = "Пожалуйста свяжитесь с админом, если ошибка повториться. Ошибка №: <b>{$q['rows']}</b>";

    if (!function_exists('message'))
    {
      die($message);
    }
    else
    {
      message($message, "Ошибка");
    }
  }

  function warning($message, $title = "System Message", $log_type = 0)
  {
    global $link, $user, $phpEx, $ugamela_root_path;
    include("{$ugamela_root_path}config.{$phpEx}");

    if(!$link)
    {
      $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      mysql_query('/*!40101 SET NAMES \'cp1251\' */');
      mysql_select_db($dbsettings['name']);
    }

    $query = "INSERT INTO `{{table}}` SET
      `log_time` = '".time()."' ,
      `log_type` = '{$log_type}',
      `log_sender` = '{$user['id']}' ,
      `log_title` = '{$title}' ,
      `log_text` = '".mysql_escape_string($message)."' ,
      `log_page` = '".mysql_escape_string($_SERVER['HTTP_REFERER'])."';";
    $sqlquery = mysql_query(str_replace('{{table}}', "{$dbsettings['prefix']}logs", $query));
  }
}

// Copyright (c) 2009-2010 Gorlum for http://supernova.ws
// Dump variables nicer then var_dump()

function dump($value,$varname = "",$level=0,$dumper = "")
{
  if ($varname) $varname .= " = ";

  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) $dumper = '<pre>' . $varname;

  $type = gettype($value);
  $dumper .= $type;

  if ($type=='string')
  {
    $dumper .= '('.strlen($value).')';
    $value = dump($value,"",-1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    $dumper .= '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).$key.' => ';
      $dumper .= dump($value->$key,"",$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    $dumper .= '('.count($value).')';
    foreach($value as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).dump($key,"",-1).' => ';
      $dumper .= dump($val,"",$level+1);
    }
    $value= '';
  }
  $dumper .= " <b>$value</b>";
  if ($level==0) $dumper .= '</pre>';
  return $dumper;
}

function pdump($value,$varname = "",$level=0,$dumper = "")
{
  print('<span style="text-align: left">' . dump($value,$varname,$level,$dumper) . '</span>');
}

function pr($prePrint = false){
  if($prePrint)
    print("<br>");
  print(rand() . "<br>");
}

function pc($prePrint = false){
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint)
    print("<br>");
  print($_PRINT_COUNT_VALUE . "<br>");
}
?>
