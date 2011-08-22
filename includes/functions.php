<?php

function get_fleet_speed()
{
  return $GLOBALS['config']->fleet_speed;
}

function get_game_speed()
{
  return $GLOBALS['config']->game_speed;
}

// ----------------------------------------------------------------------------------------------------------------
//
// Mise en forme de chaines pour affichage
//
// Mise en forme de la durÃ©e sous forme xj xxh xxm xxs
function pretty_time($seconds)
{
  global $lang;

  $day = floor($seconds / (24 * 3600));
  return sprintf("%s%02d:%02d:%02d", $day ? "{$day}{$lang['sys_day_short']} " : '', floor($seconds / 3600 % 24), floor($seconds / 60 % 60), floor($seconds / 1 % 60));
}

// Mise en forme du temps de construction (avec la phrase de description)
function ShowBuildTime($time)
{
  global $lang;

  $time = pretty_time($time);
  return "{$lang['ConstructionTime']}: {$time}";
}

// ----------------------------------------------------------------------------------------------------------------
//
// Fonction de lecture / ecriture / exploitation de templates
//
function ReadFromFile($filename)
{
  return @file_get_contents($filename);
}

function SaveToFile($filename, $content)
{
  return @file_put_contents($filename, $content);
}

// ----------------------------------------------------------------------------------------------------------------
//
function GetNextJumpWaitTime($CurMoon)
{
  global $sn_data, $time_now;

  $JumpGateLevel = $CurMoon[$sn_data[43]['name']];
  if($JumpGateLevel)
  {
    $NextJumpTime = $CurMoon['last_jump_time'] + abs(60 * 60 / $JumpGateLevel);
    $RestWait = $NextJumpTime > $time_now ? $NextJumpTime - $time_now : 0;
  }

  return isset($RestWait) ? $RestWait : 0;
}

/**
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */
// --- Formatting & Coloring numbers
// $n - number to format
// $floor: (ignored if $limit set)
//   integer   - floors to $floor numbers after decimal points
//   true      - floors number before format
//   otherwise - floors to 2 numbers after decimal points
// $color:
//   true    - colors number to green if positive or zero; red if negative
//   0
//   numeric - colors number to green if less then $color; red if greater
// $limit:
//   0/false - proceed with $floor
//   numeric - divieds number to segments by power of $limit and adds 'k' for each segment
//             makes sense for 1000, but works with any number
//             generally converts "15000" to "15k", "2000000" to "2kk" etc

function pretty_number($n, $floor = true, $color = false, $limit = false)
{
  if (is_int($floor))
  {
    $n = round($n, $floor); // , PHP_ROUND_HALF_DOWN
  }
  elseif ($floor === true)
  {
    $n = floor($n);
    $floor = 0;
  }
  else
  {
    $floor = 2;
  }

  $ret = $n;

  if ($limit)
  {

    if ($ret > 0)
    {
      while ($ret > $limit)
      {
        $suffix .= 'k';
        $ret = round($ret / 1000);
      }
    }
    else
    {
      while ($ret < -$limit)
      {
        $suffix .= 'k';
        $ret = round($ret / 1000);
      }
    }
  }

  $ret = number_format($ret, $floor, ',', '.');
  $ret .= $suffix;

  if($color !== false)
  {
    if($color === true)
    {
      $class = $n == 0 ? 'zero' : ($n > 0 ? 'positive' : 'negative');
    }
    elseif($color > 0)
    {
      $class = $n == $color ? 'zero' : ($n < $color ? 'positive' : 'negative');
    }
/*
    elseif($color == 0)
    {
      $class = $n == $color ? 'zero' : ($n >= $color ? 'positive' : 'negative');
    }
*/
    else
    {
      $class = $n == -$color ? 'zero' : (-$n < $color ? 'positive' : 'negative');
    }
    /*
      if ($color < 0)
      {
      $n = -$n;
      }
      $class = $n == $color ? 'zero' : ($n < $color ? 'positive' : 'negative');
     */

    $ret = "<span class='{$class}'>{$ret}</span>";
  }

  return $ret;
}

// ----------------------------------------------------------------------------------------------------------------
//
// Calcul de la place disponible sur une planete
//
function eco_planet_fields_max($planet)
{
  return $planet['field_max'] + ($planet['planet_type'] == PT_PLANET ? $planet[$GLOBALS['sn_data'][33]['name']] * 5 : $planet[$GLOBALS['sn_data'][41]['name']] * 3);
}

// ----------------------------------------------------------------------------------------------------------------
//
//
function GetSpyLevel(&$user)
{
  return mrc_modify_value($user, false, MRC_SPY, $user[$GLOBALS['sn_data'][TECH_SPY]['name']]);
}

// ----------------------------------------------------------------------------------------------------------------
function GetMaxFleets(&$user)
{
  return mrc_modify_value($user, false, MRC_COORDINATOR, 1 + $user[$GLOBALS['sn_data'][TECH_COMPUTER]['name']]);
}

function flt_get_fleets_flying(&$user)
{
  $fleet_flying_list = array();
  $fleet_flying_query = doquery("SELECT * FROM {{fleets}} WHERE fleet_owner = {$user['id']}");
  while($fleet_flying_row = mysql_fetch_assoc($fleet_flying_query))
  {
    $fleet_flying_list[0][] = $fleet_flying_row;
    $fleet_flying_list[$fleet_flying_row['fleet_mission']][] = &$fleet_flying_list[0][count($fleet_flying_list)-1];
  }
  return $fleet_flying_list;
}

// ----------------------------------------------------------------------------------------------------------------
function GetMaxExpeditions(&$user)
{
  return floor(sqrt($user[$GLOBALS['sn_data'][TECH_EXPEDITION]['name']]));
}

// ----------------------------------------------------------------------------------------------------------------
// Check input string for forbidden words
//
function CheckInputStrings($String)
{
  return preg_replace($GLOBALS['ListCensure'], '*', $String);
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine Test de validité d'une adresse email
//
function is_email($email)
{
  return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
}

// ----------------------------------------------------------------------------------------------------------------
// Logs page hit to DB
//
function sys_log_hit()
{
  if (!$GLOBALS['config']->game_counter || $GLOBALS['sys_stop_log_hit'])
  {
    return;
  }

  $GLOBALS['is_watching'] = true;
  $ip = sys_get_user_ip();
  doquery("INSERT INTO {{counter}} (`time`, `page`, `url`, `user_id`, `ip`, `proxy`) VALUES ('{$GLOBALS['time_now']}', '{$_SERVER['PHP_SELF']}', '{$_SERVER['REQUEST_URI']}', '{$GLOBALS['user']['id']}', '{$ip['client']}', '{$ip['proxy']}');");
  $GLOBALS['is_watching'] = false;
}

// ----------------------------------------------------------------------------------------------------------------
//
// Routine pour la gestion du mode vacance
//
function sys_user_vacation($user)
{
  if (sys_get_param_str('vacation') == 'leave')
  {
    if ($user['vacation'] < $GLOBALS['time_now'])
    {
      doquery("UPDATE {{users}} SET `vacation` = '0' WHERE `id` = '{$user['id']}' LIMIT 1;");
      $user['vacation'] = 0;
    }
  }

  if ($user['vacation'])
  {
    $template = gettemplate('vacation', true);

    $template->assign_vars(array(
      'NAME' => $user['username'],
      'VACATION_END' => date(FMT_DATE_TIME, $user['vacation']),
      'CAN_LEAVE' => $user['vacation'] <= $GLOBALS['time_now'],
      'RANDOM' => mt_rand(1, 2),
    ));

    display(parsetemplate($template));
  }

  return false;
}

function sys_get_user_ip()
{
  if ($_SERVER["HTTP_X_FORWARDED_FOR"])
  {
    if ($_SERVER["HTTP_CLIENT_IP"])
    {
      $ip['proxy'] = $_SERVER["HTTP_CLIENT_IP"];
    }
    else
    {
      $ip['proxy'] = $_SERVER["REMOTE_ADDR"];
    }
    $ip['client'] = mysql_real_escape_string($_SERVER["HTTP_X_FORWARDED_FOR"]);
  }
  else
  {
    if ($_SERVER["HTTP_CLIENT_IP"])
    {
      $ip['client'] = $_SERVER["HTTP_CLIENT_IP"];
    }
    else
    {
      $ip['client'] = $_SERVER["REMOTE_ADDR"];
    }
  }

  return array_map('mysql_real_escape_string', $ip);
}

function sys_get_param($param_name, $default = '')
{
  return $_POST[$param_name] !== NULL ? $_POST[$param_name] : ($_GET[$param_name] !== NULL ? $_GET[$param_name] : $default);
}

function sys_get_param_int($param_name, $default = 0)
{
  $value = sys_get_param($param_name, $default);
  return $value === 'on' ? 1 : ($value === 'off' ? $default : intval($value));
}

function sys_get_param_float($param_name, $default = 0)
{
  return floatval(sys_get_param($param_name, $default));
}

function sys_get_param_escaped($param_name, $default = '')
{
  return mysql_real_escape_string(sys_get_param($param_name, $default));
}

function sys_get_param_safe($param_name, $default = '')
{
  return mysql_real_escape_string(strip_tags(sys_get_param($param_name, $default)));
}

function sys_get_param_str_raw($param_name, $default = '')
{
  return strip_tags(trim(sys_get_param($param_name, $default)));
}

function sys_get_param_str($param_name, $default = '')
{
  return mysql_real_escape_string(sys_get_param_str_raw($param_name, $default));
}

function sys_get_param_str_both($param_name, $default = '')
{
  $param = strip_tags(trim(sys_get_param($param_name, $default)));
  return array('raw' => $param, 'str' => mysql_real_escape_string($param));
}

function get_missile_range()
{
  return max(0, $GLOBALS['user'][$GLOBALS['sn_data'][TECH_ENIGNE_ION]['name']] * 5 - 1);
}

function GetPhalanxRange($phalanx_level)
{
  return $phalanx_level > 1 ? pow($phalanx_level, 2) - 1 : 0;
}

function CheckAbandonPlanetState(&$planet)
{
  if ($planet['destruyed'] && $planet['destruyed'] <= $GLOBALS['time_now'])
  {
    doquery("DELETE FROM `{{planets}}` WHERE `id` = '{$planet['id']}' LIMIT 1;");
  }
}

function mrc_modify_value($user, $planet = false, $mercenaries, $value)
{
  global $sn_data;

  if (!is_array($mercenaries))
  {
    $mercenaries = array($mercenaries);
  }

  foreach ($mercenaries as $mercenary_id)
  {
    $mercenary = $sn_data[$mercenary_id];
    $mercenary_bonus = $mercenary['bonus'];
    $mercenary_level = !in_array($mercenary_id, $sn_data['groups']['governors']) ? $user[$mercenary['name']] : ($planet['PLANET_GOVERNOR_ID'] == $mercenary_id ? $planet['PLANET_GOVERNOR_LEVEL'] : 0);

    switch ($mercenary['bonus_type'])
    {
      case BONUS_PERCENT:
        $value *= 1 + $mercenary_level * $mercenary_bonus / 100;
        break;

      case BONUS_ADD:
        $value += $mercenary_level * $mercenary_bonus;
        break;

      case BONUS_ABILITY:
        $value = $mercenary_level ? $mercenary_level : 0;
        break;

      default:
        break;
    }
  }

  return $value;
}

// Generates random string of $length symbols from $allowed_chars charset
// Usefull for password and confirmation code generation
function sys_random_string($length = 16, $allowed_chars = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789')
{
  $allowed_length = strlen($allowed_chars);

  $random_string = '';
  for ($i = 0; $i < $length; $i++)
  {
    $random_string .= $allowed_chars[mt_rand(0, $allowed_length - 1)];
  }

  return $random_string;
}

function js_safe_string($string)
{
  return addslashes($string);
}

function sys_safe_output($string)
{
  return str_replace(array("&", "\"", "<", ">", "'"), array("&amp;", "&quot;", "&lt;", "&gt;", "&apos;"), $string);
}

function sys_user_options_pack(&$user)
{
  global $user_option_list;

  $options = '';
  $option_list = array();
  foreach($user_option_list as $option_group_id => $option_group)
  {
    $option_list[$option_group_id] = array();
    foreach($option_group as $option_name => $option_value)
    {
      if (!isset($user[$option_name]))
      {
        $user[$option_name] = $option_value;
      }
      $options .= "{$option_name}^{$user[$option_name]}|";
      $option_list[$option_group_id][$option_name] = $user[$option_name];
    }
  }

  $user['options'] = $options;
  $user['option_list'] = $option_list;

  return $options;
}

function sys_user_options_unpack(&$user)
{
  global $user_option_list;

  $option_list = array();
  $option_string_list = explode('|', $user['options']);

  foreach($option_string_list as $option_string)
  {
    list($option_name, $option_value) = explode('^', $option_string);
    $option_list[$option_name] = $option_value;
  }

  $final_list = array();
  foreach($user_option_list as $option_group_id => $option_group)
  {
    $final_list[$option_group_id] = array();
    foreach($option_group as $option_name => $option_value)
    {
      if(!isset($option_list[$option_name]))
      {
        $option_list[$option_name] = $option_value;
      }
      $user[$option_name] = $final_list[$option_group_id][$option_name] = $option_list[$option_name];
    }
  }

  $user['option_list'] = $final_list;

  return $final_list;
}

function sys_unit_str2arr($fleet_string)
{
  $fleet_array = array();
  if (!empty($fleet_string))
  {
    $arrTemp = explode(';', $fleet_string);
    foreach ($arrTemp as $temp)
    {
      if ($temp)
      {
        $temp = explode(',', $temp);
        if (!empty($temp[0]) && !empty($temp[1]))
        {
          $fleet_array[$temp[0]] += $temp[1];
        }
      }
    }
  }

  return $fleet_array;
}

function sys_unit_arr2str($fleet_array)
{
  $fleet_string = '';
  if (isset($fleet_array))
  {
    if (!is_array($fleet_array))
    {
      $fleet_array = array($fleet_array => 1);
    }

    foreach ($fleet_array as $unit_id => $unit_count)
    {
      if ($unit_id && $unit_count)
      {
        $fleet_string .= "{$unit_id},{$unit_count};";
      }
    }
  }

  return $fleet_string;
}

function mymail($to, $title, $body, $from = '', $html = false)
{
  global $config, $lang;

  $from = trim($from ? $from : $config->game_adminEmail);

  $head = '';
  $head .= "Content-Type: text/" . ($html ? 'html' : 'plain'). "; charset=utf-8 \r\n";
  $head .= "Date: " . date('r') . " \r\n";
  $head .= "Return-Path: {$config->game_adminEmail} \r\n";
  $head .= "From: {$from} \r\n";
  $head .= "Sender: {$from} \r\n";
  $head .= "Reply-To: {$from} \r\n";
  $head .= "Organization: {$org} \r\n";
  $head .= "X-Sender: {$from} \r\n";
  $head .= "X-Priority: 3 \r\n";
  $body = str_replace("\r\n", "\n", $body);
  $body = str_replace("\n", "\r\n", $body);
  $body = iconv($lang['LANG_INFO']['LANG_ENCODING'] ? $lang['LANG_INFO']['LANG_ENCODING'] : DEFAULT_ENCODING, 'UTF-8', $body);

  if($html)
  {
    $body = '<html><head><base href="' . SN_ROOT_VIRTUAL . '"></head><body>' . nl2br($body) . '</body></html>';
  }

  $title = '=?UTF-8?B?' . base64_encode(iconv($lang['LANG_INFO']['LANG_ENCODING'] ? $lang['LANG_INFO']['LANG_ENCODING'] : DEFAULT_ENCODING, 'UTF-8', $title)) . '?=';

  return @mail($to, $title, $body, $head);
}

function sys_time_human($time, $full = false)
{
  global $lang;

  $seconds = $time % 60;
  $time = floor($time/60);
  $minutes = $time % 60;
  $time = floor($time/60);
  $hours = $time % 24;
  $time = floor($time/24);

  return
    ($full || $time    ? "{$time} {$lang['sys_day']}&nbsp;" : '') .
    ($full || $hours   ? "{$hours} {$lang['sys_hrs']}&nbsp;" : '') .
    ($full || $minutes ? "{$minutes} {$lang['sys_min']}&nbsp;" : '') .
    ($full || $seconds ? "{$seconds} {$lang['sys_sec']}" : '');
}

function nws_render(&$template, $query_where = '', $query_limit = 0)
{
  global $config, $time_now;

  $announce_list = doquery("SELECT *, UNIX_TIMESTAMP(`tsTimeStamp`) AS unix_time FROM {{announce}} {$query_where} ORDER BY `tsTimeStamp` DESC" . ($query_limit ? " LIMIT {$query_limit}" : ''));

  $template->assign_var('NEWS_COUNT', mysql_num_rows($announce_list));

  while ($announce = mysql_fetch_assoc($announce_list))
  {
    $template->assign_block_vars('announces', array(
      'ID'         => $announce['idAnnounce'],
      'TIME'       => $announce['tsTimeStamp'],
      'ANNOUNCE'   => sys_bbcodeParse($announce['strAnnounce']),
      'DETAIL_URL' => $announce['detail_url'],
      'NEW'        => $announce['unix_time'] + $config->game_news_actual >= $time_now,
      'FUTURE'     => $announce['unix_time'] > $time_now,
    ));
  }
}

?>
