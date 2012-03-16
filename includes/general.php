<?php

/*
Function wrapping

Due glitch in PHP 5.3.1 SuperNova is incompatible with this version
Reference: https://bugs.php.net/bug.php?id=50394

*/
function sn_function_call($func_name, $func_arg = array())
{
  global $functions;

  $func_name = isset($functions[$func_name]) && function_exists($functions[$func_name]) ? $functions[$func_name] : ('sn_' . $func_name);

  return call_user_func_array($func_name, $func_arg);
}

// ----------------------------------------------------------------------------------------------------------------
// Fonction de lecture / ecriture / exploitation de templates
function sys_file_read($filename)
{
  return @file_get_contents($filename);
}

function sys_file_write($filename, $content)
{
  return @file_put_contents($filename, $content);
}

function get_game_speed()
{
  return $GLOBALS['config']->game_speed;
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
    else
    {
      $class = $n == -$color ? 'zero' : (-$n < $color ? 'positive' : 'negative');
    }

    $ret = "<span class='{$class}'>{$ret}</span>";
  }

  return $ret;
}

// ----------------------------------------------------------------------------------------------------------------
function pretty_time($seconds)
{
  global $lang;

  $day = floor($seconds / (24 * 3600));
  return sprintf("%s%02d:%02d:%02d", $day ? "{$day}{$lang['sys_day_short']} " : '', floor($seconds / 3600 % 24), floor($seconds / 60 % 60), floor($seconds / 1 % 60));
}

// ----------------------------------------------------------------------------------------------------------------
//
// Calcul de la place disponible sur une planete
//
function eco_planet_fields_max($planet)
{
  return $planet['field_max'] + ($planet['planet_type'] == PT_PLANET ? $planet[$GLOBALS['sn_data'][STRUC_TERRAFORMER]['name']] * 5 : $planet[$GLOBALS['sn_data'][STRUC_MOON_STATION]['name']] * 3);
}

// ----------------------------------------------------------------------------------------------------------------
function flt_get_missile_range($user)
{
  return max(0, mrc_get_level($user, false, TECH_ENIGNE_ION) * 5 - 1);
}

// ----------------------------------------------------------------------------------------------------------------
function GetSpyLevel(&$user)
{
  return mrc_modify_value($user, false, array(MRC_SPY, TECH_SPY), 0);
}

// ----------------------------------------------------------------------------------------------------------------
function GetMaxFleets(&$user)
{
  return mrc_modify_value($user, false, array(MRC_COORDINATOR, TECH_COMPUTER), 1);
}

// ----------------------------------------------------------------------------------------------------------------
function GetMaxExpeditions(&$user)
{
  return floor(sqrt(mrc_get_level($user, false, TECH_EXPEDITION)));
//  return floor(sqrt($user[$GLOBALS['sn_data'][TECH_EXPEDITION]['name']]));
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
    sn_sys_logout(false, true);

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

function is_id($value)
{
  return preg_match('/^\d+$/', $value);
}

function sys_get_param($param_name, $default = '')
{
  return $_POST[$param_name] !== NULL ? $_POST[$param_name] : ($_GET[$param_name] !== NULL ? $_GET[$param_name] : $default);
}

function sys_get_param_id($param_name, $default = 0)
{
  return is_id($value = sys_get_param($param_name, $default)) ? $value : $default;
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

function eco_get_total_cost($unit_id, $unit_level)
{
  global $sn_data, $config;

  $rate[RES_METAL] = $config->rpg_exchange_metal;
  $rate[RES_CRYSTAL] = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
  $rate[RES_DEUTERIUM] = $config->rpg_exchange_deterium / $config->rpg_exchange_metal;

  $unit_cost_data = &$sn_data[$unit_id]['cost'];
  $factor = $unit_cost_data['factor'];
  $cost_array = array(BUILD_CREATE => array(), 'total' => 0);
  $unit_level = $unit_level > 0 ? $unit_level : 0;
  foreach($unit_cost_data as $resource_id => $resource_amount)
  {
    if(!in_array($resource_id, $sn_data['groups']['resources_all']))
    {
      continue;
    }

    $cost_array[BUILD_CREATE][$resource_id] = $resource_amount * ($factor == 1 ? $unit_level : ((pow($factor, $unit_level) - $factor) / ($factor - 1)));
    if(in_array($resource_id, $sn_data['groups']['resources_loot']))
    {
      $cost_array['total'] += $cost_array[BUILD_CREATE][$resource_id] * $rate[$resource_id];
    }
  }

  return $cost_array;
}

function mrc_get_level(&$user, $planet = array(), $unit_id, $for_update = false, $plain = false){return sn_function_call('mrc_get_level', array(&$user, $planet, $unit_id, $for_update, $plain));}
function sn_mrc_get_level(&$user, $planet = array(), $unit_id, $for_update = false, $plain = false)
{
// TODO: Add caching for known items
  global $config, $sn_data, $time_now;

  $mercenary_level = 0;
  $unit_db_name = $sn_data[$unit_id]['name'];
  if(in_array($unit_id, $sn_data['groups']['mercenaries']))
  {
    if(!$user['id'])
    {
      $user[$unit_id]['powerup_unit_level'] = $user[$unit_db_name];
    }
    elseif($for_update || !isset($user[$unit_id]))
    {
      $time_restriction = $config->empire_mercenary_temporary ? " AND powerup_time_start <= {$time_now} AND powerup_time_finish >= {$time_now} " : '';
      $mercenary_level = doquery("SELECT * FROM {{powerup}} WHERE powerup_user_id = {$user['id']} AND powerup_unit_id = {$unit_id} {$time_restriction} LIMIT 1" . ($for_update ? ' FOR UPDATE' : '') . ";", '', true);
      $user[$unit_id] = $mercenary_level;
    }
    $mercenary_level = intval($user[$unit_id]['powerup_unit_level']);
  }
  elseif(in_array($unit_id, $sn_data['groups']['governors']))
  {
    $mercenary_level = $unit_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0;
  }
  elseif(in_array($unit_id, $sn_data['groups']['tech']) || $unit_id == RES_DARK_MATTER)
  {
    $mercenary_level = $user[$unit_db_name];
  }
  elseif(in_array($unit_id, array_merge($sn_data['groups']['resources_loot'], $sn_data['groups']['structures'], $sn_data['groups']['fleet'], $sn_data['groups']['defense'])))
  {
    $mercenary_level =  !empty($planet) ? $planet[$unit_db_name] : $user[$unit_db_name];
  }

  return $mercenary_level;
}

function mrc_modify_value(&$user, $planet = array(), $mercenaries, $value) {return sn_function_call('mrc_modify_value', array(&$user, $planet, $mercenaries, $value));}
function sn_mrc_modify_value(&$user, $planet = array(), $mercenaries, $value, $base_value = null)
{
  global $sn_data;

  if(!is_array($mercenaries))
  {
    $mercenaries = array($mercenaries);
  }

  $base_value = isset($base_value) ? $base_value : $value;

  foreach($mercenaries as $mercenary_id)
  {
    $mercenary_level = mrc_get_level($user, $planet, $mercenary_id);

    $mercenary = &$sn_data[$mercenary_id];
    $mercenary_bonus = $mercenary['bonus'];

    switch($mercenary['bonus_type'])
    {
      case BONUS_PERCENT_CUMULATIVE:
        $value *= 1 + $mercenary_level * $mercenary_bonus / 100;
      break;

      case BONUS_PERCENT:
        $value += $base_value * $mercenary_level * $mercenary_bonus / 100;
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
function sys_random_string($length = 16, $allowed_chars = SN_SYS_SEC_CHARS_ALLOWED)
{
  $allowed_length = strlen($allowed_chars);

  $random_string = '';
  for($i = 0; $i < $length; $i++)
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
  if(!empty($fleet_string))
  {
    $arrTemp = explode(';', $fleet_string);
    foreach($arrTemp as $temp)
    {
      if($temp)
      {
        $temp = explode(',', $temp);
        if(!empty($temp[0]) && !empty($temp[1]))
        {
          $fleet_array[$temp[0]] += $temp[1];
        }
      }
    }
  }

  return $fleet_array;
}

function sys_unit_arr2str($unit_list)
{
  $fleet_string = array();
  if(isset($unit_list))
  {
    if(!is_array($unit_list))
    {
      $unit_list = array($unit_list => 1);
    }

    foreach($unit_list as $unit_id => $unit_count)
    {
      if($unit_id && $unit_count)
      {
        $fleet_string[] = "{$unit_id},{$unit_count}";
      }
    }
  }

  return implode(';', $fleet_string);
}

function mymail($to, $title, $body, $from = '', $html = false)
{
  global $config, $lang;

  $from = trim($from ? $from : $config->game_adminEmail);

  $head  = '';
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

  if($html)
  {
    $body = '<html><head><base href="' . SN_ROOT_VIRTUAL . '"></head><body>' . nl2br($body) . '</body></html>';
  }

  $title = '=?UTF-8?B?' . base64_encode($title) . '?=';

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

function sys_redirect($url)
{
  header("Location: {$url}");
  ob_end_flush();
  die();
}

function sys_get_unit_location($user, $planet, $unit_id){return sn_function_call('sys_get_unit_location', array($user, $planet, $unit_id));}
function sn_sys_get_unit_location($user, $planet, $unit_id)
{
  global $sn_data;

  return $sn_data[$unit_id]['location'];
}

function sn_ali_fill_user_ally(&$user)
{
  if(!$user['ally_id'])
  {
    return;
  }

  if(!isset($user['ally']))
  {
    $user['ally'] = doquery("SELECT * FROM {{alliance}} WHERE `id` = {$user['ally_id']} LIMIT 1;", true);
  }

  if(!isset($user['ally']['player']))
  {
    $user['ally']['player'] = doquery("SELECT * FROM {{users}} WHERE `id` = {$user['ally']['ally_user_id']} LIMIT 1;", true);
  }
}

function sn_get_url_contents($url)
{
  if(function_exists('curl_init'))
  {
    $crl = curl_init();
    $timeout = 5;
    curl_setopt ($crl, CURLOPT_URL,$url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $return = curl_exec($crl);
    curl_close($crl);
  }
  else
  {
    $return = @file_get_contents($url);
  }

  return $return;
}

function get_ship_data($ship_id, $user)
{
  global $sn_data;

  $ship_data = array();
  if(in_array($ship_id, $sn_data['groups']['fleet']))
  {
    foreach($sn_data[$ship_id]['engine'] as $engine_info)
    {
//      if($user[$sn_data[$engine_info['tech']]['name']] >= $engine_info['min_level'])
      if(mrc_get_level($user, false, $engine_info['tech']) >= $engine_info['min_level'])
      {
        $ship_data = $engine_info;
      }
    }
    $ship_data['speed'] = floor(mrc_modify_value($user, false, array(MRC_NAVIGATOR, $ship_data['tech']), $ship_data['speed']));

    $ship_data['capacity'] = $sn_data[$ship_id]['capacity'];
  }

  return $ship_data;
}

if(!function_exists('strptime'))
{
  function strptime($date, $format)
  {
    $masks = array(
      '%d' => '(?P<d>[0-9]{2})',
      '%m' => '(?P<m>[0-9]{2})',
      '%Y' => '(?P<Y>[0-9]{4})',
      '%H' => '(?P<H>[0-9]{2})',
      '%M' => '(?P<M>[0-9]{2})',
      '%S' => '(?P<S>[0-9]{2})',
     // usw..
    ); 

    $rexep = "#".strtr(preg_quote($format), $masks)."#";
    if(preg_match($rexep, $date, $out))
    {
      $ret = array(
        "tm_sec"  => (int) $out['S'],
        "tm_min"  => (int) $out['M'],
        "tm_hour" => (int) $out['H'],
        "tm_mday" => (int) $out['d'],
        "tm_mon"  => $out['m'] ? $out['m'] - 1 : 0,
        "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
      );
    }
    else
    {
      $ret = false;
    }
    return $ret;
  }
}

?>
