<?php

/*
Function wrapping

Due glitch in PHP 5.3.1 SuperNova is incompatible with this version
Reference: https://bugs.php.net/bug.php?id=50394

*/

require_once('general/math.php');
require_once('general_pname.php');

function sn_function_call($func_name, $func_arg = array())
{
  global $functions; // All data in $functions should be normalized to valid 'callable' state: '<function_name>'|array('<object_name>', '<method_name>')

  if(is_array($functions[$func_name]) && !is_callable($functions[$func_name]))
  {
    // Chain-callable functions should be made as following:
    // 1. Never use incomplete calls with parameters "by default"
    // 2. Reserve last parameter for cumulative result
    // 3. Use same format for original value and cumulative result (if there is original value)
    // 4. Honor cumulative result
    // 5. Return cumulative result
    foreach($functions[$func_name] as $func_chain_name)
    {
      // По идее - это уже тут не нужно, потому что оно все должно быть callable к этому моменту
      // Но для старых модулей...
      if(is_callable($func_chain_name))
      {
        $result = call_user_func_array($func_chain_name, $func_arg);
      }
    }
  }
  else
  {
    // TODO: This is left for backward compatibility. Appropriate code should be rewrote!
    $func_name = isset($functions[$func_name]) && is_callable($functions[$func_name]) ? $functions[$func_name] : ('sn_' . $func_name);
    if(is_callable($func_name))
    {
      $result = call_user_func_array($func_name, $func_arg);
    }
  }

  return $result;
}

function execute_hooks(&$hook_list, &$template) {
  if(!empty($hook_list)) {
    foreach($hook_list as $hook) {
      if(is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable)))) {
        $template = call_user_func($hook_call, $template);
      }
    }
  }
}

// ----------------------------------------------------------------------------------------------------------------
// Fonction de lecture / ecriture / exploitation de templates
function sys_file_read($filename)
{
  return @file_get_contents($filename);
}

function sys_file_write($filename, $content)
{
  return @file_put_contents($filename, $content, FILE_APPEND);
}

function get_game_speed(){return sn_function_call('get_game_speed', array(&$result));}
function sn_get_game_speed(&$result) {
  global $config;

  return $result = $config->game_speed;
}

function flt_server_flight_speed_multiplier(){return sn_function_call('flt_server_flight_speed_multiplier', array(&$result));}
function sn_flt_server_flight_speed_multiplier(&$result) {
  global $config;

  return $result = $config->fleet_speed;
}

function game_resource_multiplier(){return sn_function_call('game_resource_multiplier', array(&$result));}
function sn_game_resource_multiplier(&$result) {
  global $config;

  return $result = $config->resource_multiplier;
}

/**
 pretty_number implementation for SuperNova

 $n - number to format
 $floor: (ignored if $limit set)
   integer   - floors to $floor numbers after decimal points
   true      - floors number before format
   otherwise - floors to 2 numbers after decimal points
 $color:
   true    - colors number to green if positive or zero; red if negative
   0
   numeric - colors number to green if less then $color; red if greater
 $limit:
   0/false - proceed with $floor
   numeric - divides number to segments by power of $limit and adds 'k' for each segment
             makes sense for 1000, but works with any number
             generally converts "15000" to "15k", "2000000" to "2kk" etc
 $style
   null  - standard result
   true  - return only style class for current params
   false - return array('text' => $ret, 'class' => $class), where $ret - unstyled
 */

function pretty_number($n, $floor = true, $color = false, $limit = false, $style = null)
{
  $n = floatval($n);
  if(is_int($floor))
  {
    $n = round($n, $floor); // , PHP_ROUND_HALF_DOWN
  }
  elseif($floor === true)
  {
    $n = floor($n);
    $floor = 0;
  }
  else
  {
    $floor = 2;
  }

  $ret = $n;

  $suffix = '';
  if($limit)
  {
    if($ret > 0)
    {
      while($ret > $limit)
      {
        $suffix .= 'k';
        $ret = round($ret / 1000);
      }
    }
    else
    {
      while($ret < -$limit)
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
    elseif($color >= 0)
    {
      $class = $n == $color ? 'zero' : ($n < $color ? 'positive' : 'negative');
    }
    else
    {
      $class = ($n == -$color) ? 'zero' : ($n < -$color ? 'negative' : 'positive');
    }

    if(!isset($style))
    {
      $ret = "<span class='{$class}'>{$ret}</span>";
    }
    else
    {
      $ret = $style ? $ret = $class : $ret = array('text' => $ret, 'class' => $class);
    }
  }

  return $ret;
}

// ----------------------------------------------------------------------------------------------------------------
function pretty_time($seconds) {
  global $lang;

  $day = floor($seconds / (24 * 3600));
  return sprintf("%s%02d:%02d:%02d", $day ? "{$day}{$lang['sys_day_short']} " : '', floor($seconds / 3600 % 24), floor($seconds / 60 % 60), floor($seconds / 1 % 60));
}

// ----------------------------------------------------------------------------------------------------------------
function eco_planet_fields_max($planet) {
  return $planet['field_max'] + ($planet['planet_type'] == PT_PLANET ? mrc_get_level($user, $planet, STRUC_TERRAFORMER) * 5 : (mrc_get_level($user, $planet, STRUC_MOON_STATION) * 3));
}

// ----------------------------------------------------------------------------------------------------------------
function flt_get_missile_range($user) {
  return max(0, mrc_get_level($user, false, TECH_ENGINE_ION) * 5 - 1);
}

// ----------------------------------------------------------------------------------------------------------------
function GetSpyLevel(&$user) {
  return mrc_modify_value($user, false, array(MRC_SPY, TECH_SPY), 0);
}

// ----------------------------------------------------------------------------------------------------------------
function GetMaxFleets(&$user) {
  return mrc_modify_value($user, false, array(MRC_COORDINATOR, TECH_COMPUTER), 1);
}

// ----------------------------------------------------------------------------------------------------------------
/*
function GetMaxExpeditions(&$user)
{
  return floor(sqrt(mrc_get_level($user, false, TECH_EXPEDITION)));
}
*/

// ----------------------------------------------------------------------------------------------------------------
// Check input string for forbidden words
//
function CheckInputStrings($String)
{
  global $ListCensure;

  return preg_replace($ListCensure, '*', $String);
}

function is_email($email) {
  return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
}

function is_id($value)
{
  return preg_match('/^\d+$/', $value) && ($value >= 0);
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
  return db_escape(sys_get_param($param_name, $default));
}
/*
function sys_get_param_safe($param_name, $default = '')
{
  return db_escape(strip_tags(sys_get_param($param_name, $default)));
}
*/
function sys_get_param_date_sql($param_name, $default = '2000-01-01')
{
  $val = sys_get_param($param_name, $default);
  return preg_match(PREG_DATE_SQL_RELAXED, $val) ? $val : $default;
}

function sys_get_param_str_unsafe($param_name, $default = '')
{
  return str_raw2unsafe(sys_get_param($param_name, $default));
}

function sys_get_param_str($param_name, $default = '')
{
  return db_escape(sys_get_param_str_unsafe($param_name, $default));
}

function sys_get_param_str_both($param_name, $default = '')
{
  $param = sys_get_param($param_name, $default);
  $param_unsafe = str_raw2unsafe($param);
  return array(
    'raw' => $param,
    'unsafe' => $param_unsafe,
    'safe' => db_escape($param_unsafe),
  );
}

function sys_get_param_phone($param_name, $default = '')
{
  $phone_raw = sys_get_param_str_unsafe($param_name, $default = '');
  if($phone_raw)
  {
    $phone = $phone_raw[0] == '+' ? '+' : '';
    for($i = 0; $i < strlen($phone_raw); $i++)
    {
      $ord = ord($phone_raw[$i]);
      if($ord >= 48 && $ord <= 57)
      {
        $phone .= $phone_raw[$i];
      }
    }
    $phone = strlen($phone) < 11 ? '' : $phone;
  }
  else
  {
    $phone = '';
  }

  return array('raw' => $phone_raw, 'phone' => $phone);
}

function GetPhalanxRange($phalanx_level)
{
  return $phalanx_level > 1 ? pow($phalanx_level, 2) - 1 : 0;
}

function CheckAbandonPlanetState(&$planet)
{
  if($planet['destruyed'] && $planet['destruyed'] <= SN_TIME_NOW)
  {
    db_planet_delete_by_id($planet['id']);
  }
}

function eco_get_total_cost($unit_id, $unit_level)
{
  global $config;

  static $rate, $sn_group_resources_all, $sn_group_resources_loot;
  if(!$rate)
  {
    $sn_group_resources_all = sn_get_groups('resources_all');
    $sn_group_resources_loot = sn_get_groups('resources_loot');

    $rate[RES_METAL] = $config->rpg_exchange_metal;
    $rate[RES_CRYSTAL] = $config->rpg_exchange_crystal / $config->rpg_exchange_metal;
    $rate[RES_DEUTERIUM] = $config->rpg_exchange_deuterium / $config->rpg_exchange_metal;
  }

  $unit_cost_data = get_unit_param($unit_id, 'cost');
  if(!is_array($unit_cost_data))
  {
    return array('total' => 0);
  }
  $factor = isset($unit_cost_data['factor']) ? $unit_cost_data['factor'] : 1;
  $cost_array = array(BUILD_CREATE => array(), 'total' => 0);
  $unit_level = $unit_level > 0 ? $unit_level : 0;
  foreach($unit_cost_data as $resource_id => $resource_amount)
  {
    if(!in_array($resource_id, $sn_group_resources_all))
    {
      continue;
    }
//    $cost_array[BUILD_CREATE][$resource_id] = $resource_amount * ($factor == 1 ? $unit_level : ((pow($factor, $unit_level) - $factor) / ($factor - 1)));
    $cost_array[BUILD_CREATE][$resource_id] = round($resource_amount * ($factor == 1 ? $unit_level : ((1 - pow($factor, $unit_level)) / (1 - $factor))));
    if(in_array($resource_id, $sn_group_resources_loot))
    {
      $cost_array['total'] += $cost_array[BUILD_CREATE][$resource_id] * $rate[$resource_id];
    }
  }

  return $cost_array;
}

function sn_unit_purchase($unit_id){}

function sn_unit_relocate($unit_id, $from, $to){}

/*
  ЭТО ПРОСТОЙ ВРАППЕР ДЛЯ БД! Здесь НЕТ никаких проверок! ВСЕ проверки должны быть сделаны заранее!
  Враппер возвращает уровень для указанного UNIT_ID и заполняет поле в соответствующей записи
  TODO: Он может быть перекрыт для возвращения дополнительной информации о юните - например, о Капитане (пока не реализовано)

  $context
    'location' - где искать данный тип юнита: LOC_USER
    'user' - &$user

  $options
    'for_update' - блокировать запись до конца транзакции
*/
/*
function unit_get_level($unit_id, &$context = null, $options = null){return sn_function_call('unit_get_level', array($unit_id, &$context, $options, &$result));}
function sn_unit_get_level($unit_id, &$context = null, $options = null, &$result)
{
  $unit_db_name = pname_resource_name($unit_id);
  $for_update = $options['for_update'];

  $unit_level = 0;
  if($context['location'] == LOC_USER)
  {
    $user = &$context['user'];
    if(!$user['id'])
    {
      $user[$unit_id]['unit_level'] = $user[$unit_db_name];
    }
    elseif($for_update || !isset($user[$unit_id]))
    {
      $unit_level = db_unit_by_location($user['id'], $context['location'], $user['id'], $unit_id, $for_update);
      $unit_level['unit_time_start'] = strtotime($unit_level['unit_time_start']);
      $unit_level['unit_time_finish'] = strtotime($unit_level['unit_time_finish']);
      $user[$unit_id] = $unit_level;
    }
    $unit_level = intval($user[$unit_id]['unit_level']);
  }
  elseif($context['location'] == LOC_PLANET)
  {
    $planet = &$context['planet'];
    if(!$planet['id'])
    {
      $planet[$unit_id]['unit_level'] = $planet[$unit_db_name];
    }
    elseif($for_update || !isset($planet[$unit_id]))
    {
      $unit_level = db_unit_by_location(0, $context['location'], $planet['id'], $unit_id, $for_update);
      $unit_level['unit_time_start'] = strtotime($unit_level['unit_time_start']);
      $unit_level['unit_time_finish'] = strtotime($unit_level['unit_time_finish']);
      $planet[$unit_id] = $unit_level;
    }
    $unit_level = intval($planet[$unit_id]['unit_level']);
  }

  return $result = $unit_level;
}
*/

function mrc_get_level(&$user, $planet = array(), $unit_id, $for_update = false, $plain = false){return sn_function_call(__FUNCTION__, array(&$user, $planet, $unit_id, $for_update, $plain, &$result));}
function sn_mrc_get_level(&$user, $planet = array(), $unit_id, $for_update = false, $plain = false, &$result) {
  $mercenary_level = 0;
  $unit_db_name = pname_resource_name($unit_id);

  if(in_array($unit_id, sn_get_groups(array('plans', 'mercenaries', 'tech', 'artifacts')))) {
    $unit = classSupernova::db_get_unit_by_location($user['id'], LOC_USER, $user['id'], $unit_id);
    $mercenary_level = is_array($unit) && $unit['unit_level'] ? $unit['unit_level'] : 0;
  } elseif(in_array($unit_id, sn_get_groups(array('structures', 'fleet', 'defense')))) {
    $unit = classSupernova::db_get_unit_by_location(is_array($user) ? $user['id'] : $planet['id_owner'], LOC_PLANET, $planet['id'], $unit_id);
    $mercenary_level = is_array($unit) && $unit['unit_level'] ? $unit['unit_level'] : 0;
  } elseif(in_array($unit_id, sn_get_groups('governors'))) {
    $mercenary_level = $unit_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0;
  } elseif($unit_id == RES_DARK_MATTER || $unit_id == RES_METAMATTER) {
    $mercenary_level = $user[$unit_db_name];
  } elseif(in_array($unit_id, sn_get_groups(array('resources_loot'))) || $unit_id == UNIT_SECTOR) {
    $mercenary_level = !empty($planet) ? $planet[$unit_db_name] : $user[$unit_db_name];
  }

  return $result = $mercenary_level;
}

function mrc_modify_value(&$user, $planet = array(), $mercenaries, $value) {return sn_function_call('mrc_modify_value', array(&$user, $planet, $mercenaries, $value));}
function sn_mrc_modify_value(&$user, $planet = array(), $mercenaries, $value, $base_value = null)
{
  if(!is_array($mercenaries))
  {
    $mercenaries = array($mercenaries);
  }

  $base_value = isset($base_value) ? $base_value : $value;

  foreach($mercenaries as $mercenary_id)
  {
    $mercenary_level = mrc_get_level($user, $planet, $mercenary_id);

    $mercenary = get_unit_param($mercenary_id);
    $mercenary_bonus = $mercenary['bonus'];

    switch($mercenary['bonus_type'])
    {
      case BONUS_PERCENT_CUMULATIVE:
        $value *= 1 + $mercenary_level * $mercenary_bonus / 100;
      break;

      case BONUS_PERCENT:
        $mercenary_level = $mercenary_bonus < 0 && $mercenary_level * $mercenary_bonus < -90 ? -90 / $mercenary_bonus : $mercenary_level;
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
function sys_random_string($length = 16, $allowed_chars = SN_SYS_SEC_CHARS_ALLOWED) {
  $allowed_length = strlen($allowed_chars);

  $random_string = '';
  for($i = 0; $i < $length; $i++) {
    $random_string .= $allowed_chars[mt_rand(0, $allowed_length - 1)];
  }

  return $random_string;
}

function js_safe_string($string)
{
  return str_replace(array("\r", "\n"), array('\r', '\n'), addslashes($string));
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
      } elseif ($user[$option_name] == '') {
        $user[$option_name] = 0;
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

function mymail($email_unsafe, $title, $body, $from = '', $html = false) {
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

  if($html) {
    $body = '<html><head><base href="' . SN_ROOT_VIRTUAL . '"></head><body>' . nl2br($body) . '</body></html>';
  }

  $title = '=?UTF-8?B?' . base64_encode($title) . '?=';

  return @mail($email_unsafe, $title, $body, $head);
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
    ($full || !$time || $seconds ? "{$seconds} {$lang['sys_sec']}" : '');
}

function sys_time_human_system($time) {
  return $time ? date(FMT_DATE_TIME_SQL, $time) . " ({$time}), " . sys_time_human(SN_TIME_NOW - $time) : '{NEVER}';
}

function sys_redirect($url)
{
  header("Location: {$url}");
  ob_end_flush();
  die();
}

// TODO Для полноценного функионирования апдейтера пакет функций, включая эту должен быть вынесен раньше - или грузить general.php до апдейтера
function sys_get_unit_location($user, $planet, $unit_id){return sn_function_call('sys_get_unit_location', array($user, $planet, $unit_id));}
function sn_sys_get_unit_location($user, $planet, $unit_id)
{
  return get_unit_param($unit_id, 'location');
}

function sn_ali_fill_user_ally(&$user) {
  if(!$user['ally_id']) {
    return;
  }

  if(!isset($user['ally'])) {
    $user['ally'] = doquery("SELECT * FROM {{alliance}} WHERE `id` = {$user['ally_id']} LIMIT 1;", true);
  }

  if(!isset($user['ally']['player'])) {
    $user['ally']['player'] = db_user_by_id($user['ally']['ally_user_id'], true, '*', false);
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

function get_engine_data($user, $engine_info)
{
  $sn_data_tech_bonus = get_unit_param($engine_info['tech'], 'bonus');

  $user_tech_level = intval(mrc_get_level($user, false, $engine_info['tech']));

  $engine_info['speed_base'] = $engine_info['speed'];
  $tech_bonus = ($user_tech_level - $engine_info['min_level']) * $sn_data_tech_bonus / 100;
  $tech_bonus = $tech_bonus < -0.9 ? -0.95 : $tech_bonus;
  $engine_info['speed'] = floor(mrc_modify_value($user, false, array(MRC_NAVIGATOR), $engine_info['speed']) * (1 + $tech_bonus));

  $engine_info['consumption_base'] = $engine_info['consumption'];
  $tech_bonus = ($user_tech_level - $engine_info['min_level']) * $sn_data_tech_bonus / 1000;
  $tech_bonus = $tech_bonus > 0.5 ? 0.5 : ($tech_bonus < 0 ? $tech_bonus * 2 : $tech_bonus);
  $engine_info['consumption'] = ceil($engine_info['consumption'] * (1 - $tech_bonus));

  return $engine_info;
}

function get_ship_data($ship_id, $user)
{
  $ship_data = array();
  if(in_array($ship_id, sn_get_groups(array('fleet', 'missile'))))
  {
    foreach(get_unit_param($ship_id, 'engine') as $engine_info)
    {
      $tech_level = intval(mrc_get_level($user, false, $engine_info['tech']));
      if(empty($ship_data) || $tech_level >= $engine_info['min_level'])
      {
        $ship_data = $engine_info;
        $ship_data['tech_level'] = $tech_level;
      }
    }
    $ship_data = get_engine_data($user, $ship_data);
    $ship_data['capacity'] = get_unit_param($ship_id, 'capacity');
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

function sn_sys_sector_buy($redirect = 'overview.php') {
  global $lang, $user, $planetrow;

  if(!sys_get_param_str('sector_buy') || $planetrow['planet_type'] != PT_PLANET) {
    return;
  }

  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true, '*');
  $planetrow = db_planet_by_id($planetrow['id'], true, '*');
  // Тут не надо делать обсчет - ресурсы мы уже посчитали, очередь (и количество зданий) - тоже
//  $planetrow = sys_o_get_updated($user, $planetrow, SN_TIME_NOW);
//  $user = $planetrow['user'];
//  $planetrow = $planetrow['planet'];
  $sector_cost = eco_get_build_data($user, $planetrow, UNIT_SECTOR, mrc_get_level($user, $planetrow, UNIT_SECTOR), true);
  $sector_cost = $sector_cost[BUILD_CREATE][RES_DARK_MATTER];
  if($sector_cost <= $user[get_unit_param(RES_DARK_MATTER, P_NAME)]) {
    $planet_name_text = uni_render_planet($planetrow);
    if(rpg_points_change($user['id'], RPG_SECTOR, -$sector_cost, sprintf($lang['sys_sector_purchase_log'],
        $user['username'], $user['id'], $planet_name_text, $lang['sys_planet_type'][$planetrow['planet_type']], $planetrow['id'], $sector_cost)
    )) {
      $sector_db_name = pname_resource_name(UNIT_SECTOR);
      db_planet_set_by_id($planetrow['id'], "{$sector_db_name} = {$sector_db_name} + 1");
    } else {
      sn_db_transaction_rollback();
    }
  }
  sn_db_transaction_commit();

  sys_redirect($redirect);
}

function sn_sys_handler_add(&$functions, $handler_list, $class_module_name = '', $sub_type = '')
{
  if(isset($handler_list) && is_array($handler_list) && !empty($handler_list))
  {
    foreach($handler_list as $function_name => $function_data)
    {
      if(is_string($function_data))
      {
        $override_with = &$function_data;
      }
      elseif(isset($function_data['callable']))
      {
        $override_with = &$function_data['callable'];
      }

      $overwrite = $override_with[0] == '*';
      if($overwrite)
      {
        $override_with = substr($override_with, 1);
      }

      if(($point_position = strpos($override_with, '.')) === false && $class_module_name)
      {
        $override_with = array($class_module_name, $override_with);
      }
      elseif($point_position == 0)
      {
        $override_with = substr($override_with, 1);
      }
      elseif($point_position > 0)
      {
        $override_with = array(substr($override_with, 0, $point_position), substr($override_with, $point_position + 1));
      }

      if($overwrite)
      {
        $functions[$function_name] = array();
      }
      elseif(!isset($functions[$function_name]))
      {
        $functions[$function_name] = array();
        $sn_function_name = 'sn_' . $function_name . ($sub_type ? '_' . $sub_type : '');
        //if(is_callable($sn_function_name))
        {
          $functions[$function_name][] = $sn_function_name;
        }
      }

      $functions[$function_name][] = $function_data;
    }
  }
}

// TODO - поменять название
// Может принимать: (array)$user, $nick_render_array, $nick_render_array_html, $nick_render_string_compact
function player_nick_render_to_html($result, $options = false){
  // TODO - обрабатывать разные случаи: $user, $render_nick_array, $string

  if(is_string($result) && strpos($result, ':{i:')) {
    $result = player_nick_uncompact($result);
  }

  if(is_array($result)) {
    if(isset($result['id'])) {
      $result = player_nick_render_current_to_array($result, $options);
    }
    if(!isset($result[NICK_HTML])) {
      $result = player_nick_render_array_to_html($result);
    }
    unset($result[NICK_HTML]);
    // unset($result[NICK_ID]);
    ksort($result);
    $result = implode('', $result);
  }

  return $result;
}


function player_nick_compact($nick_array) {
  ksort($nick_array);
  return serialize($nick_array);
}

function player_nick_uncompact($nick_string) {
  try {
    $result = unserialize($nick_string);
    // ksort($result); // Всегда ksort-ый в player_nick_compact()
  } catch(exception $e) {
    $result = strpos($nick_string, ':{i:') ? null : $nick_string; // fallback if it is already string - for old chat strings, for example
  }
  return $result;
}

function player_nick_render_array_to_html($nick_array){return sn_function_call('player_nick_render_array_to_html', array($nick_array, &$result));}
function sn_player_nick_render_array_to_html($nick_array, &$result) {
  global $config, $user;

  // ALL STRING ARE UNSAFE!!!
  if(isset($nick_array[NICK_BIRTHSDAY])) {
    $result[NICK_BIRTHSDAY] = '<img src="design/images/birthday.png" />';
  }

  if(isset($nick_array[NICK_VACATION])) {
    $result[NICK_VACATION] = '<img src="design/images/icon_vacation.png" />';
  }

  if(isset($nick_array[NICK_GENDER])) {
    $result[NICK_GENDER] = '<img src="' . ($user['dpath'] ? $user['dpath'] : DEFAULT_SKINPATH) . 'images/gender_' . $nick_array[NICK_GENDER] . '.png" />';
  }

  if(isset($nick_array[NICK_AUTH_LEVEL]) || isset($nick_array[NICK_PREMIUM])) {
    switch($nick_array[NICK_AUTH_LEVEL]) {
      case 4:
        $highlight = $config->chat_highlight_developer;
        break;

      case 3:
        $highlight = $config->chat_highlight_admin;
        break;

      case 2:
        $highlight = $config->chat_highlight_operator;
        break;

      case 1:
        $highlight = $config->chat_highlight_moderator;
        break;

      default:
        $highlight = isset($nick_array[NICK_PREMIUM]) ? $config->chat_highlight_premium : '';
    }

    if($highlight) {
      list($result[NICK_HIGHLIGHT], $result[NICK_HIGHLIGHT_END]) = explode('$1', $highlight);
    }
    // $result = preg_replace("#(.+)#", $highlight, $result);
  }

  if(isset($nick_array[NICK_CLASS])) {
    $result[NICK_CLASS] = '<span ' . $nick_array[NICK_CLASS] .'>';
    $result[NICK_CLASS_END] = '</span>';
  }

  $result[NICK_NICK] = sys_safe_output($nick_array[NICK_NICK]);

  if(isset($nick_array[NICK_ALLY])) {
    $result[NICK_ALLY] = '[' . sys_safe_output($nick_array[NICK_ALLY]) . ']';
  }

  $result[NICK_HTML] = true;

  return $result;
}

function player_nick_render_current_to_array($render_user, $options = false){return sn_function_call('player_nick_render_current_to_array', array($render_user, $options, &$result));}
function sn_player_nick_render_current_to_array($render_user, $options = false, &$result) {
  /*
  $options = $options !== true ? $options :
    array(
      'color' => true,
      'icons' => true,
      'gender' => true,
      'birthday' => true,
      'ally' => true,
    );
  */


  if($render_user['user_birthday'] && ($options === true || isset($options['icons']) || isset($options['birthday'])) && (date('Y', SN_TIME_NOW) . date('-m-d', strtotime($render_user['user_birthday'])) == date('Y-m-d', SN_TIME_NOW))) {
    $result[NICK_BIRTHSDAY] = '';
  }

  if($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['gender']) && $options['gender'])) {
    $result[NICK_GENDER] = $render_user['gender'] == GENDER_UNKNOWN ? 'unknown' : ($render_user['gender'] == GENDER_FEMALE ? 'female' : 'male');
  }

  if(($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['vacancy']) && $options['vacancy'])) && $render_user['vacation']) {
    $result[NICK_VACATION] = $render_user['vacation'];
  }

  if($options === true || (isset($options['color']) && $options['color'])) {
    if($user_auth_level = $render_user['authlevel']) {
      $result[NICK_AUTH_LEVEL] = $user_auth_level;
    }
    if($user_premium = mrc_get_level($render_user, false, UNIT_PREMIUM)) {
      $result[NICK_PREMIUM] = $user_premium;
    }
  }

  if((isset($options['class']) && $options['class'])) {
    $result[NICK_CLASS] = (isset($result_options[NICK_CLASS]) ? ' ' . $result_options[NICK_CLASS] : '') . $options['class'];
  }

  if($render_user['ally_tag'] && ($options === true || (isset($options['ally']) && $options['ally']))) {
    $result[NICK_ALLY] = $render_user['ally_tag'];
  }

  $result[NICK_NICK] = $render_user['username'];

  return $result;
}



































// TODO sys_stat_get_user_skip_list() ПЕРЕДЕЛАТЬ!
function sys_stat_get_user_skip_list() {
  global $config;

  $result = array();

  $user_skip_list = array();

  if($config->stats_hide_admins) {
    $user_skip_list[] = '`authlevel` > 0';
  }

  if($config->stats_hide_player_list) {
    $temp = explode(',', $config->stats_hide_player_list);
    foreach($temp as $user_id) {
      $user_id = floatval($user_id);
      if($user_id) {
        $user_skip_list[] = '`id` = ' . $user_id;
      }
    }
  }

  if(!empty($user_skip_list)) {
    $user_skip_list = implode(' OR ', $user_skip_list);
    $user_skip_query = db_user_list($user_skip_list);
    if(!empty($user_skip_query)) {
      foreach($user_skip_query as $user_skip_row) {
        $result[$user_skip_row['id']] = $user_skip_row['id'];
      }
    }
  }

  return $result;
}

// function player_nick_render_to_html($render_user, $options = false){return sn_function_call('player_nick_render_to_html', array($render_user, $options, &$result));}
// function sn_render_player_nick($render_user, $options = false, &$result)

function get_unit_param($unit_id, $param_name = null, $user = null, $planet = null){return sn_function_call('get_unit_param', array($unit_id, $param_name, $user, $planet, &$result));}
function sn_get_unit_param($unit_id, $param_name = null, $user = null, $planet = null, &$result)
{
  global $sn_data;

  $result = isset($sn_data[$unit_id])
    ? ($param_name === null
      ? $sn_data[$unit_id]
      : (isset($sn_data[$unit_id][$param_name]) ? $sn_data[$unit_id][$param_name] : $result)
    )
    : $result;

  return $result;
}

function sn_get_groups($groups){return sn_function_call('sn_get_groups', array($groups, &$result));}
function sn_sn_get_groups($groups, &$result)
{
  $result = is_array($result) ? $result : array();
  foreach($groups = is_array($groups) ? $groups : array($groups) as $group_name)
  {
    $result += is_array($a_group = get_unit_param(UNIT_GROUP, $group_name)) ? $a_group : array();
  }

  return $result;
}

// Format $value to ID
function idval($value, $default = 0)
{
  $value = floatval($value);
  return preg_match('#^(\d*)#', $value, $matches) && $matches[1] ? floatval($matches[1]) : $default;
}

function unit_requirements_render($user, $planetrow, $unit_id){return sn_function_call('unit_requirements_render', array($user, $planetrow, $unit_id, &$result));}
function sn_unit_requirements_render($user, $planetrow, $unit_id, &$result)
{
  global $lang, $config;

  $sn_data_unit = get_unit_param($unit_id);

  $result = is_array($result) ? $result : array();
  if($sn_data_unit['require'] && !($sn_data_unit[P_UNIT_TYPE] == UNIT_MERCENARIES && $config->empire_mercenary_temporary))
  {
    foreach($sn_data_unit['require'] as $require_id => $require_level)
    {
      $level_got = mrc_get_level($user, $planetrow, $require_id);
      $level_basic = mrc_get_level($user, $planetrow, $require_id, false, true);
      $result[] = array(
        'NAME' => $lang['tech'][$require_id],
        //'CLASS' => $require_level > $level_got ? 'negative' : ($require_level == $level_got ? 'zero' : 'positive'),
        'REQUEREMENTS_MET' => intval($require_level <= $level_got ? REQUIRE_MET : REQUIRE_MET_NOT),
        'LEVEL_REQUIRE' => $require_level,
        'LEVEL' => $level_got,
        'LEVEL_BASIC' => $level_basic,
        'LEVEL_BONUS' => max(0, $level_got - $level_basic),
        'ID' => $require_id,
      );
    }
  }

  return $result;
}

function ally_get_ranks(&$ally)
{
  global $ally_rights;

  $ranks = array();

  if($ally['ranklist'])
  {
    $str_ranks = explode(';', $ally['ranklist']);
    foreach($str_ranks as $str_rank)
    {
      if(!$str_rank)
      {
        continue;
      }

      $tmp = explode(',', $str_rank);
      $rank_id = count($ranks);
      foreach($ally_rights as $key => $value)
      {
        $ranks[$rank_id][$value] = $tmp[$key];
      }
    }
  }

  return $ranks;
}

function sys_player_new_adjust($user_id, $planet_id){return sn_function_call('sys_player_new_adjust', array($user_id, $planet_id, &$result));}
function sn_sys_player_new_adjust($user_id, $planet_id, &$result) {
}

function array_merge_recursive_numeric($array1, $array2) {
  foreach($array2 as $key => $value) {
    if(!isset($array1[$key]) || !is_array($array1[$key])) {
      $array1[$key] = $value;
    } else {
      $array1[$key] = array_merge_recursive_numeric($array1[$key], $value);
    }
  }

  return $array1;
}

/*
 * Простенький бенчмарк
 */
function sn_benchmark($message = '', $commented = true)
{
  static $microtime, $memory;

  if(!$microtime)
  {
    $microtime = SN_TIME_MICRO;
    $memory = SN_MEM_START;
  }

  $microtime_now = microtime(true);
  $memory_now = memory_get_usage();

  print("\r\n");
  if($commented)print("<!--\r\n");
  print('!BENCHMARK! ' . $message . "\r\n");
  print('Time: ' . round($microtime_now - $microtime, 5) . '/' . round($microtime_now - SN_TIME_MICRO, 5) . "\r\n");
  print("Memory: " . number_format($memory_now - $memory) . '/' . number_format($memory_now - SN_MEM_START) . "\r\n");
  if($commented)print("-->\r\n");

  $microtime = $microtime_now;
  $memory = $memory_now;
}

function sn_sys_array_cumulative_sum(&$array)
{
  $accum = 0;
  foreach($array as &$value)
  {
    $accum += $value;
    $value = $accum;
  }
}

function planet_density_price_chart($planet_density_index) {
  $sn_data_density = sn_get_groups('planet_density');
  $density_price_chart = array(0 => array(), 1 => array());
  $reverse_flag = false;
  foreach($sn_data_density as $density_id => $density_data) {
    if($density_id == PLANET_DENSITY_NONE) {
      continue;
    } elseif($density_id == $planet_density_index) {
      $reverse_flag = true;
      //continue;
    } elseif($reverse_flag) {
      $density_price_chart[1][$density_id] = $density_data[UNIT_PLANET_DENSITY_RARITY];
    } else {
      $density_price_chart[0][$density_id] = $density_data[UNIT_PLANET_DENSITY_RARITY];
    }
  }
//  pdump($density_price_chart);

  $density_price_chart[0] = array_reverse($density_price_chart[0], true);
  sn_sys_array_cumulative_sum($density_price_chart[0]);
  $density_price_chart[0] = array_reverse($density_price_chart[0], true);

  sn_sys_array_cumulative_sum($density_price_chart[1]);

  $density_price_chart = $density_price_chart[0] + array($planet_density_index => 0) + $density_price_chart[1];

  return $density_price_chart;
}

function sn_sys_planet_core_transmute(&$user, &$planetrow) {
  if(!sys_get_param_str('transmute')) {
    return array();
  }

  global $lang;

  try {
    if($planetrow['planet_type'] != PT_PLANET) {
      throw new exception($lang['ov_core_err_not_a_planet'], ERR_ERROR);
    }

    if($planetrow['density_index'] == ($new_density_index = sys_get_param_id('density_type'))) {
      throw new exception($lang['ov_core_err_same_density'], ERR_WARNING);
    }

    sn_db_transaction_start();
    $user = db_user_by_id($user['id'], true, '*');
    $planetrow = db_planet_by_id($planetrow['id'], true, '*');
//    $global_data = sys_o_get_updated($user, $planetrow['id'], SN_TIME_NOW);
//    $user = $global_data['user'];
//    $planetrow = $global_data['planet'];

    $planet_density_index = $planetrow['density_index'];

    $density_price_chart = planet_density_price_chart($planet_density_index);
    if(!isset($density_price_chart[$new_density_index])) {
      // Hack attempt
      throw new exception($lang['ov_core_err_denisty_type_wrong'], ERR_ERROR);
    }

    $user_dark_matter = mrc_get_level($user, false, RES_DARK_MATTER);
    $transmute_cost = get_unit_param(UNIT_PLANET_DENSITY, 'cost');
    $transmute_cost = $transmute_cost[RES_DARK_MATTER] * $density_price_chart[$new_density_index];
    if($user_dark_matter < $transmute_cost) {
      throw new exception($lang['ov_core_err_no_dark_matter'], ERR_ERROR);
    }

    $sn_data_planet_density = sn_get_groups('planet_density');
    foreach($sn_data_planet_density as $key => $value) {
      if($key == $new_density_index) {
        break;
      }
      $prev_density_index = $key;
    }

    $new_density = round(($sn_data_planet_density[$new_density_index][UNIT_PLANET_DENSITY] + $sn_data_planet_density[$prev_density_index][UNIT_PLANET_DENSITY]) / 2);

    rpg_points_change($user['id'], RPG_PLANET_DENSITY_CHANGE, -$transmute_cost,
      array(
        'Planet %1$s ID %2$d at coordinates %3$s changed density type from %4$d "%5$s" to %6$d "%7$s". New density is %8$d kg/m3',
        $planetrow['name'],
        $planetrow['id'],
        uni_render_coordinates($planetrow),
        $planet_density_index,
        $lang['uni_planet_density_types'][$planet_density_index],
        $new_density_index,
        $lang['uni_planet_density_types'][$new_density_index],
        $new_density
      )
    );

    db_planet_set_by_id($planetrow['id'], "`density` = {$new_density}, `density_index` = {$new_density_index}");
    sn_db_transaction_commit();

    $planetrow['density'] = $new_density;
    $planetrow['density_index'] = $new_density_index;
    $result = array(
      'STATUS'  => ERR_NONE,
      'MESSAGE' => sprintf($lang['ov_core_err_none'], $lang['uni_planet_density_types'][$planet_density_index], $lang['uni_planet_density_types'][$new_density_index], $new_density),
    );
  } catch(exception $e) {
    sn_db_transaction_rollback();
    $result = array(
      'STATUS'  => $e->getCode(),
      'MESSAGE' => $e->getMessage(),
    );
  }

  return $result;
}

function sn_module_get_active_count($group = '*')
{
  global $sn_module_list;

  $active_modules = 0;
  if(isset($sn_module_list[$group]) && is_array($sn_module_list[$group]))
  {
    foreach($sn_module_list[$group] as $payment_module)
    {
      $active_modules += $payment_module->manifest['active'];
    }
  }

  return $active_modules;
}

function get_resource_exchange()
{
  static $rates;

  if(!$rates)
  {
    global $config;

    $rates = array(
      RES_METAL => 'rpg_exchange_metal',
      RES_CRYSTAL => 'rpg_exchange_crystal',
      RES_DEUTERIUM => 'rpg_exchange_deuterium',
      RES_DARK_MATTER => 'rpg_exchange_darkMatter',
    );

    foreach($rates as &$rate)
    {
      $rate = $config->$rate;
    }
  }

  return $rates;
}

function get_unit_cost_in(&$cost, $in_resource = RES_METAL)
{
  static $rates;

  if(!$rates)
  {
    $rates = get_resource_exchange();
  }

  $metal_cost = 0;
  foreach($cost as $resource_id => $resource_value)
  {
    $metal_cost += $rates[$resource_id] * $resource_value;
  }

  return $metal_cost;
}

function get_player_current_expeditions(&$user) {
  $FlyingExpeditions  = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{fleets}} WHERE `fleet_owner` = {$user['id']} AND `fleet_mission` = '" . MT_EXPLORE . "';", true);
  return $FlyingExpeditions['expedi'];
}

function get_player_max_expeditons(&$user, $astrotech = -1)
{
  if($astrotech == -1) {
    if(!isset($user[UNIT_PLAYER_EXPEDITIONS_MAX]))
    {
      $astrotech = mrc_get_level($user, false, TECH_ASTROTECH);
      $user[UNIT_PLAYER_EXPEDITIONS_MAX] = $astrotech >= 1 ? floor(sqrt($astrotech - 1)) : 0;
    }

    return $user[UNIT_PLAYER_EXPEDITIONS_MAX];
  } else {
    return $astrotech >= 1 ? floor(sqrt($astrotech - 1)) : 0;
  }
}

function get_player_max_expedition_duration(&$user, $astrotech = -1)
{
  return $astrotech == -1 ? mrc_get_level($user, false, TECH_ASTROTECH) : $astrotech;
}

function get_player_max_colonies(&$user, $astrotech = -1) {
  global $config;

  if($astrotech == -1) {
    if(!isset($user[UNIT_PLAYER_COLONIES_MAX])) {

      $expeditions = get_player_max_expeditons($user);
      $astrotech = mrc_get_level($user, false, TECH_ASTROTECH);
      $colonies = $astrotech - $expeditions;

      $user[UNIT_PLAYER_COLONIES_MAX] = $config->player_max_colonies < 0 ? $colonies : min($config->player_max_colonies, $colonies);
    }

    return $user[UNIT_PLAYER_COLONIES_MAX];
  } else {
    $expeditions = get_player_max_expeditons($user, $astrotech);
    // $astrotech = mrc_get_level($user, false, TECH_ASTROTECH);
    $colonies = $astrotech - $expeditions;

    return $config->player_max_colonies < 0 ? $colonies : min($config->player_max_colonies, $colonies);
  }
}

function get_player_current_colonies(&$user)
{
  return $user[UNIT_PLAYER_COLONIES_CURRENT] = isset($user[UNIT_PLAYER_COLONIES_CURRENT]) ? $user[UNIT_PLAYER_COLONIES_CURRENT] : max(0, db_planet_count_by_type($user['id']) - 1);
}

function flt_send_back(&$fleet_row)
{
  $fleet_id = round(is_array($fleet_row) && isset($fleet_row['fleet_id']) && $fleet_row['fleet_id'] ? $fleet_row['fleet_id'] : $fleet_row);
  if(!$fleet_id)
  {
    return false;
  }

  return doquery("UPDATE {{fleets}} SET `fleet_mess` = 1 WHERE `fleet_id` = {$fleet_id} LIMIT 1;");
}

function flt_destroy(&$fleet_row) {
  $fleet_id = round(is_array($fleet_row) && isset($fleet_row['fleet_id']) && $fleet_row['fleet_id'] ? $fleet_row['fleet_id'] : $fleet_row);
  if(!$fleet_id) {
    return false;
  }

  return doquery("DELETE FROM {{fleets}} WHERE `fleet_id` = {$fleet_id} LIMIT 1;");
}

function str_raw2unsafe($raw) {
  return trim(strip_tags($raw));
}

function ip2longu($ip) {
  return sprintf('%u', floatval(ip2long($ip)));
}


function sn_powerup_get_price_matrix($powerup_id, $powerup_unit = false, $level_max = null, $plain = false){return sn_function_call('sn_powerup_get_price_matrix', array($powerup_id, $powerup_unit, $level_max, $plain, &$result));}
function sn_sn_powerup_get_price_matrix($powerup_id, $powerup_unit = false, $level_max = null, $plain = false, &$result) {
  global $sn_powerup_buy_discounts;

  $result = array();

  $powerup_data = get_unit_param($powerup_id);
  //pdump($powerup_data, '$powerup_data');
  $is_upgrade = !empty($powerup_unit) && $powerup_unit;

  // pdump($powerup_unit, '$powerup_unit');
  $level_current = $term_original = $time_left = 0;
  if($is_upgrade) {
    $time_finish = strtotime($powerup_unit['unit_time_finish']);
    $time_left = max(0, $time_finish - SN_TIME_NOW);
    if($time_left > 0) {
      $term_original = $time_finish - strtotime($powerup_unit['unit_time_start']);
      $level_current = $powerup_unit['unit_level'];
    }
  }

  $level_max = $level_max > $powerup_data[P_MAX_STACK] ? $level_max : $powerup_data[P_MAX_STACK];
  $original_cost = 0;
  for($i = 1; $i <= $level_max; $i++) {
    $base_cost = eco_get_total_cost($powerup_id, $i);
    $base_cost = $base_cost[BUILD_CREATE][RES_DARK_MATTER];
    foreach($sn_powerup_buy_discounts as $period => $discount) {
      $upgrade_price = floor($base_cost * $discount * $period / PERIOD_MONTH);
      $result[$i][$period] = $upgrade_price;
      $original_cost = $is_upgrade && $i == $level_current && $period <= $term_original ? $upgrade_price : $original_cost;
    }
  }

  if($is_upgrade && $time_left) {
    $term_original = round($term_original / PERIOD_DAY);
    $time_left = min(floor($time_left / PERIOD_DAY), $term_original);
    $cost_left = $term_original > 0 ? ceil($time_left / $term_original * $original_cost) : 0;

    array_walk_recursive($result, function(&$value) use ($cost_left) {
      $value -= $cost_left;
    });
  }

  return $result;
}

function note_assign(&$template, $note_row) {
  global $note_priority_classes, $lang;

  $template->assign_block_vars('note', array(
    'ID' => $note_row['id'],
    'TIME' => $note_row['time'],
    'TIME_TEXT' => date(FMT_DATE_TIME, $note_row['time']),
    'PRIORITY' => $note_row['priority'],
    'PRIORITY_CLASS' => $note_priority_classes[$note_row['priority']],
    'PRIORITY_TEXT' => $lang['sys_notes_priorities'][$note_row['priority']],
    'TITLE' => htmlentities($note_row['title'], ENT_COMPAT, 'UTF-8'),
    'GALAXY' => intval($note_row['galaxy']),
    'SYSTEM' => intval($note_row['system']),
    'PLANET' => intval($note_row['planet']),
    'PLANET_TYPE' => intval($note_row['planet_type']),
    'PLANET_TYPE_TEXT' => $lang['sys_planet_type'][$note_row['planet_type']],
    'PLANET_TYPE_TEXT_SHORT' => $lang['sys_planet_type_sh'][$note_row['planet_type']],
    'TEXT' => sys_bbcodeParse(htmlentities($note_row['text'], ENT_COMPAT, 'UTF-8')),
    'TEXT_EDIT' => htmlentities($note_row['text'], ENT_COMPAT, 'UTF-8'),
    'STICKY' => intval($note_row['sticky']),
  ));
}

function user_time_diff_get() {
  $result = !empty($_COOKIE[SN_COOKIE_T]) ? explode(';', $_COOKIE[SN_COOKIE_T]) : null;
  $result = array(
    PLAYER_OPTION_TIME_DIFF => isset($result[PLAYER_OPTION_TIME_DIFF]) ? $result[PLAYER_OPTION_TIME_DIFF] : '',
    PLAYER_OPTION_TIME_DIFF_UTC_OFFSET => isset($result[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]) ? $result[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET] : 0,
    PLAYER_OPTION_TIME_DIFF_FORCED => isset($result[PLAYER_OPTION_TIME_DIFF_FORCED]) ? $result[PLAYER_OPTION_TIME_DIFF_FORCED] : 0,
    PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => isset($result[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME]) ? $result[PLAYER_OPTION_TIME_DIFF_MEASURE_TIME] : '2010-01-01',
  );
  return $result;
}

function user_time_diff_set($user_time_diff) {
  // Переопределяем массив, что бы элементы были в правильном порядке
  $user_time_diff = array(
    PLAYER_OPTION_TIME_DIFF => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF] : '',
    PLAYER_OPTION_TIME_DIFF_UTC_OFFSET => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF_UTC_OFFSET] : 0,
    PLAYER_OPTION_TIME_DIFF_FORCED => isset($user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED]) ? $user_time_diff[PLAYER_OPTION_TIME_DIFF_FORCED] : 0,
    PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
  );

  $user_time_diff_str = implode(';', $user_time_diff);
  sn_setcookie(SN_COOKIE_T, $user_time_diff_str, SN_TIME_NOW + PERIOD_MONTH);
}

function user_time_diff_probe() {
  $result = array(
    PLAYER_OPTION_TIME_DIFF => ($time_local = sys_get_param_float('localtime')) ? round($time_local / 1000 - SN_TIME_MICRO) : 0,
    PLAYER_OPTION_TIME_DIFF_UTC_OFFSET => ($time_local_utc_offset = sys_get_param_int('utc_offset')) ? $time_local_utc_offset - date('Z') : 0,
    PLAYER_OPTION_TIME_DIFF_FORCED => sys_get_param_int('PLAYER_OPTION_TIME_DIFF_FORCED'),
    PLAYER_OPTION_TIME_DIFF_MEASURE_TIME => SN_TIME_SQL,
  );

  return $result;
}

function sn_version_compare_extra($version) {
  static $version_regexp = '#(\d+)([a-f])(\d+)(?:\.(\d+))*#';
  preg_match($version_regexp, $version, $version);
  unset($version[0]);
  $version[2] = ord($version[2]) - ord('a');
  return implode('.', $version);
}

function sn_version_compare($ver1, $ver2) {
  return version_compare(sn_version_compare_extra($ver1), sn_version_compare_extra($ver2));
}

function sn_setcookie($name, $value = null, $expire = null, $path = SN_ROOT_RELATIVE, $domain = null, $secure = null, $httponly = null) {
  $_COOKIE[$name] = $value;
  return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
}

function market_get_autoconvert_cost() {
  global $config;

  return $config->rpg_cost_exchange ? $config->rpg_cost_exchange * 3 : 3000;
}