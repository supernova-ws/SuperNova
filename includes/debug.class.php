<?php
/*
 * debug.class.php ::  Clase Debug, maneja reporte de eventos
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

if(!defined('INSIDE')){ die("attemp hacking");}

class debug
{
  var $log,$numqueries;

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
  { global $ugamela_root_path;
    echo  "<br><table><tr><td class=k colspan=4><a href=".$ugamela_root_path."admin/settings.php>Debug Log</a>:</td></tr>".$this->log."</table>";
    die();
  }

  function error($message,$title)
  {
    global $link,$game_config;
    if($game_config['debug']==1){
      echo "<h2>$title</h2><br><font color=red>$message</font><br><hr>";
      echo  "<table>".$this->log."</table>";
    }
    //else{
      //A futuro, se creara una tabla especial, para almacenar
      //los errores que ocurran.
      global $user,$ugamela_root_path,$phpEx;
      include($ugamela_root_path . 'config.'.$phpEx);
      if(!$link) die('mySQL no esta disponible por el momento, sentimos el inconveniente...');
      $query = "INSERT INTO `{{table}}` SET
        `error_sender` = '{$user['id']}' ,
        `error_time` = '".time()."' ,
        `error_type` = '{$title}' ,
        `error_text` = '".mysql_escape_string($message)."' ,
        `error_page` = '".mysql_escape_string($_SERVER['HTTP_REFERER'])."',
        `error_backtrace` = '".mysql_escape_string(dump(debug_backtrace()))."'
        ;";
      $sqlquery = mysql_query(str_replace("{{table}}", $dbsettings["prefix"].'errors',$query))
        or die('error fatal');
      $query = "explain select * from {{table}}";
      $q = mysql_fetch_array(mysql_query(str_replace("{{table}}", $dbsettings["prefix"].
        'errors', $query))) or die('error fatal: ');


      if (!function_exists('message'))
        echo "Пожалуйста свяжитесь с админом, если ошибка повториться. Ошибка №: <b>".$q['rows']."</b>";
      else
        message("Пожалуйста свяжитесь с админом, если ошибка повториться. Ошибка №: <b>".$q['rows']."</b>", "Ошибка");
    //}

    die();
  }

  function warning($message, $title = "System Message", $log_type = 0)
  {
    global $link, $game_config;

    global $user,$ugamela_root_path,$phpEx;
    include($ugamela_root_path . 'config.'.$phpEx);
    if(!$link)
      $link = mysql_connect($dbsettings["server"], $dbsettings["user"], $dbsettings["pass"]);
    $query = "INSERT INTO `{{table}}` SET
      `log_time` = '".time()."' ,
      `log_type` = '{$log_type}',
      `log_sender` = '{$user['id']}' ,
      `log_title` = '{$title}' ,
      `log_text` = '".mysql_escape_string($message)."' ,
      `log_page` = '".mysql_escape_string($_SERVER['HTTP_REFERER'])."';";
    $sqlquery = mysql_query(str_replace("{{table}}", $dbsettings["prefix"].'logs',$query));
  }
}

?>
