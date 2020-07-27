<?php


use Fleet\MissionExplore;

require_once('general_math.php');
require_once('general_compatibility.php');
require_once('general_params.php');
require_once('general_nickRender.php');
require_once('general_formatters.php');
require_once('general_validators.php');
require_once('general_unitFunctions.php');
require_once('general_playerFunctions.php');
require_once('general_planetFunctions.php');
require_once('general_urlAndHttp.php');

require_once('general_pname.php');

// HOOKS AND HANDLERS ----------------------------------------------------------------------------------------------------------------
/**
 * Function wrapping
 *
 * Due glitch in PHP 5.3.1 SuperNova is incompatible with this version
 * Reference: https://bugs.php.net/bug.php?id=50394
 *
 * @param string $func_name
 * @param array  $func_arg
 *
 * @return mixed
 */
function sn_function_call($func_name, $func_arg = array()) {
  global $functions; // All data in $functions should be normalized to valid 'callable' state: '<function_name>'|array('<object_name>', '<method_name>')

  if (is_array($functions[$func_name]) && !is_callable($functions[$func_name])) {
    // Chain-callable functions should be made as following:
    // 1. Never use incomplete calls with parameters "by default"
    // 2. Reserve last parameter for cumulative result
    // 3. Use same format for original value and cumulative result (if there is original value)
    // 4. Honor cumulative result
    // 5. Return cumulative result
    foreach ($functions[$func_name] as $func_chain_name) {
      // По идее - это уже тут не нужно, потому что оно все должно быть callable к этому моменту
      // Но для старых модулей...
      if (is_callable($func_chain_name)) {
        $result = call_user_func_array($func_chain_name, $func_arg);
      }
    }
  } else {
    // TODO: This is left for backward compatibility. Appropriate code should be rewrote!
    $func_name = isset($functions[$func_name]) && is_callable($functions[$func_name]) ? $functions[$func_name] : ('sn_' . $func_name);
    if (is_callable($func_name)) {
      $result = call_user_func_array($func_name, $func_arg);
    }
  }

  return $result;
}

/**
 * @param        $hook_list
 * @param        $template
 * @param string $hook_type - тип хука 'model' или 'view'
 * @param string $page_name - имя страницы, для которого должен был быть выполнен хук
 */
function execute_hooks(&$hook_list, &$template, $hook_type = null, $page_name = null) {
  if (!empty($hook_list)) {
    foreach ($hook_list as $hook) {
      if (is_callable($hook_call = (is_string($hook) ? $hook : (is_array($hook) ? $hook['callable'] : $hook->callable)))) {
        $template = call_user_func($hook_call, $template, $hook_type, $page_name);
      }
    }
  }
}

function sn_sys_handler_add(&$functions, $handler_list, $class_module_name = '', $sub_type = '') {
  if (isset($handler_list) && is_array($handler_list) && !empty($handler_list)) {
    foreach ($handler_list as $function_name => $function_data) {
      sys_handler_add_one($functions, $function_name, $function_data, $class_module_name, $sub_type);
    }
  }
}

/**
 * Adding one handler for specific function name
 *
 * @param callable[][] $functions
 * @param string       $function_name
 * @param string|array $function_data
 * @param string       $class_module_name
 * @param string       $sub_type
 */
function sys_handler_add_one(&$functions, $function_name, $function_data, $class_module_name, $sub_type) {
  if (is_string($function_data)) {
    $override_with = &$function_data;
  } elseif (isset($function_data['callable'])) {
    $override_with = &$function_data['callable'];
  }

  $overwrite = $override_with[0] == '*';
  $prepend   = $override_with[0] == '+';
  if ($overwrite || $prepend) {
    $override_with = substr($override_with, 1);
  }

  if (($point_position = strpos($override_with, '.')) === false && $class_module_name) {
    $override_with = array($class_module_name, $override_with);
  } elseif ($point_position == 0) {
    $override_with = substr($override_with, 1);
  } elseif ($point_position > 0) {
    $override_with = array(substr($override_with, 0, $point_position), substr($override_with, $point_position + 1));
  }

  if ($overwrite) {
    $functions[$function_name] = array();
  } elseif (!isset($functions[$function_name])) {
    $functions[$function_name] = array();
    $sn_function_name          = 'sn_' . $function_name . ($sub_type ? '_' . $sub_type : '');
    //if(is_callable($sn_function_name))
    {
      $functions[$function_name][] = $sn_function_name;
    }
  }

  if ($prepend) {
    array_unshift($functions[$function_name], $function_data);
  } else {
    $functions[$function_name][] = $function_data;
  }
}


// FLEET FUNCTIONS -----------------------------------------------------------------------------------------------------
/**
 * @param MissionExplore $result
 *
 * @return MissionExplore
 */
function flt_mission_explore_addon_object($result) { return sn_function_call('flt_mission_explore_addon_object', [$result]); }

/**
 * @param MissionExplore $result
 *
 * @return MissionExplore
 */
function sn_flt_mission_explore_addon_object($result) {
  return $result;
}

// FILE FUNCTIONS ----------------------------------------------------------------------------------------------------------------
function sys_file_read($filename) {
  return @file_get_contents($filename);
}

function sys_file_write($filename, $content) {
  return @file_put_contents($filename, $content, FILE_APPEND);
}

function sn_sys_load_php_files($dir_name, $load_extension = 'php') {
  if (file_exists($dir_name)) {
    $dir = opendir($dir_name);
    while (($file = readdir($dir)) !== false) {
      if ($file == '..' || $file == '.') {
        continue;
      }

      $full_filename = $dir_name . $file;
      $extension     = substr($full_filename, -strlen($load_extension));
      if ($extension == $load_extension) {
        require_once($full_filename);
      }
    }
  }
}


// GLOBAL DATA FUNCTIONS -----------------------------------------------------------------------------------------------
/**
 * Simple wrapper to get base or calculated value for supplied unitSnId
 *
 * @param int  $unitSnId
 * @param bool $plain
 *
 * @return float|int
 */
function getValueFromStorage($unitSnId, $plain = false) {
  $valueObject = SN::$gc->valueStorage->getValueObject($unitSnId);

  return $plain ? $valueObject->base : $valueObject->getValue();
}

/**
 * Get game resource multiplier aka mining speed
 *
 * @param bool $plain
 *
 * @return float|int
 */
function game_resource_multiplier($plain = false) {
  return getValueFromStorage(UNIT_SERVER_SPEED_MINING, $plain);
}

/**
 * Get game speed aka manufacturing speed
 *
 * @param bool $plain
 *
 * @return float|int
 */
function get_game_speed($plain = false) {
  return getValueFromStorage(UNIT_SERVER_SPEED_BUILDING, $plain);
}

/**
 * Get fleet flying speed aka... hmph... fleet flying speed
 *
 * @param bool $plain
 *
 * @return float|int
 */
function flt_server_flight_speed_multiplier($plain = false) {
  return getValueFromStorage(UNIT_SERVER_SPEED_FLEET, $plain);
}


/**
 * Получение стоимости ММ в валюте сервера
 *
 * @param bool|false $plain
 *
 * @return mixed
 */
function get_mm_cost($plain = false) {
  $result = null;

  return sn_function_call('get_mm_cost', array($plain, &$result));
}

function sn_get_mm_cost($plain = false, &$result) {
  return $result = SN::$config->payment_currency_exchange_mm_ ? SN::$config->payment_currency_exchange_mm_ : 20000;
}

/**
 * Получение курса обмены валюты в серверную валюту
 *
 * @param $currency_symbol
 *
 * @return float
 */
function get_exchange_rate($currency_symbol) {
  $currency_symbol = strtolower($currency_symbol);
  $config_field    = 'payment_currency_exchange_' . $currency_symbol;

  // Заворачиваем получение стоимости ММ через перекрываемую процедуру
  $exchange_rate = floatval($currency_symbol == 'mm_' ? get_mm_cost() : SN::$config->$config_field);

  return $exchange_rate;
}

function sys_stat_get_user_skip_list() {
  $result = array();

  $user_skip_list = array();

  if (SN::$config->stats_hide_admins) {
    $user_skip_list[] = '`authlevel` > ' . AUTH_LEVEL_REGISTERED;
  }

  if (SN::$config->stats_hide_player_list) {
    $temp = explode(',', SN::$config->stats_hide_player_list);
    foreach ($temp as $user_id) {
      if ($user_id = floatval($user_id)) {
        $user_skip_list[] = '`id` = ' . $user_id;
      }
    }
  }

  if (!empty($user_skip_list)) {
    $user_skip_list  = implode(' OR ', $user_skip_list);
    $user_skip_query = db_user_list($user_skip_list);
    if (!empty($user_skip_query)) {
      foreach ($user_skip_query as $user_skip_row) {
        $result[$user_skip_row['id']] = $user_skip_row['id'];
      }
    }
  }

  return $result;
}

function market_get_autoconvert_cost() {
  return SN::$config->rpg_cost_exchange ? SN::$config->rpg_cost_exchange * 3 : 3000;
}

function sn_powerup_get_price_matrix($powerup_id, $powerup_unit = false, $level_max = null, $plain = false) {
  $result = null;

  return sn_function_call('sn_powerup_get_price_matrix', array($powerup_id, $powerup_unit, $level_max, $plain, &$result));
}

function sn_sn_powerup_get_price_matrix($powerup_id, $powerup_unit = false, $level_max = null, $plain = false, &$result) {
  global $sn_powerup_buy_discounts;

  $result = array();

  $powerup_data = get_unit_param($powerup_id);
  $is_upgrade   = !empty($powerup_unit) && $powerup_unit;

  $level_current = $term_original = $time_left = 0;
  if ($is_upgrade) {
    $time_finish = strtotime($powerup_unit['unit_time_finish']);
    $time_left   = max(0, $time_finish - SN_TIME_NOW);
    if ($time_left > 0) {
      $term_original = $time_finish - strtotime($powerup_unit['unit_time_start']);
      $level_current = $powerup_unit['unit_level'];
    }
  }

  $level_max     = $level_max > $powerup_data[P_MAX_STACK] ? $level_max : $powerup_data[P_MAX_STACK];
  $original_cost = 0;
  for ($i = 1; $i <= $level_max; $i++) {
    $base_cost = eco_get_total_cost($powerup_id, $i);
    $base_cost = $base_cost[BUILD_CREATE][RES_DARK_MATTER];
    foreach ($sn_powerup_buy_discounts as $period => $discount) {
      $upgrade_price       = floor($base_cost * $discount * $period / PERIOD_MONTH);
      $result[$i][$period] = $upgrade_price;
      $original_cost       = $is_upgrade && $i == $level_current && $period <= $term_original ? $upgrade_price : $original_cost;
    }
  }

  if ($is_upgrade && $time_left) {
    $term_original = round($term_original / PERIOD_DAY);
    $time_left     = min(floor($time_left / PERIOD_DAY), $term_original);
    $cost_left     = $term_original > 0 ? ceil($time_left / $term_original * $original_cost) : 0;

    array_walk_recursive($result, function (&$value) use ($cost_left) {
      $value -= $cost_left;
    });
  }

  return $result;
}

/**
 * @param $price_matrix_plain
 * @param $price_matrix_original
 * @param $price_matrix_upgrade
 * @param $user_dark_matter
 *
 * @return array
 *
 * Used in player_premium and interface_batch_operation modules
 */
function price_matrix_templatize(&$price_matrix_plain, &$price_matrix_original, &$price_matrix_upgrade, $user_dark_matter) {
  $prices = array();
  foreach ($price_matrix_original as $level_num => $level_data) {
    $price_per_period = array();
    foreach ($level_data as $period => $price) {
      $price_per_period[$period] = array(
        'PERIOD'             => $period,
        'PRICE_ORIGIN'       => $price,
        'PRICE_ORIGIN_TEXT'  => HelperString::numberFloorAndFormat($price),
        'PRICE_ORIGIN_CLASS' => prettyNumberGetClass($price, $user_dark_matter),
        'PRICE_UPGRADE'      => $price_matrix_upgrade[$level_num][$period],
        'PRICE_UPGRADE_TEXT' => HelperString::numberFloorAndFormat($price_matrix_upgrade[$level_num][$period]),
      );
      if (isset($price_matrix_plain[$level_num][$period])) {
        $price_per_period[$period] += array(
          'PRICE_PLAIN_PERCENT' => ceil(100 - ($price / $price_matrix_plain[$level_num][$period]) * 100),
          'PRICE_PLAIN'         => $price_matrix_plain[$level_num][$period],
          'PRICE_PLAIN_TEXT'    => HelperString::numberFloorAndFormat($price_matrix_plain[$level_num][$period]),
        );
      }
    }

    $prices[$level_num] = array(
      '.'     => array('period' => $price_per_period),
      'LEVEL' => $level_num,
    );
  }

  return $prices;
}


// TOOLS & UTILITIES ----------------------------------------------------------------------------------------------------------------
/**
 * Generates random string of $length symbols from $allowed_chars charset
 *
 * @param int    $length
 * @param string $allowed_chars
 *
 * @return string
 */
function sys_random_string($length = 16, $allowed_chars = SN_SYS_SEC_CHARS_ALLOWED) {
  $allowed_length = strlen($allowed_chars);

  $random_string = '';
  for ($i = 0; $i < $length; $i++) {
    $random_string .= $allowed_chars[mt_rand(0, $allowed_length - 1)];
  }

  return $random_string;
}

function array_merge_recursive_numeric($array1, $array2) {
  if (!empty($array2) && is_array($array2)) {
    foreach ($array2 as $key => $value) {
      $array1[$key] = !isset($array1[$key]) || !is_array($array1[$key]) ? $value : array_merge_recursive_numeric($array1[$key], $value);
    }
  }

  return $array1;
}

function sn_sys_array_cumulative_sum(&$array) {
  $accum = 0;
  foreach ($array as &$value) {
    $accum += $value;
    $value = $accum;
  }
}

function print_rr($var, $capture = false) {
  $print = '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>';
  if ($capture) {
    return $print;
  } else {
    print($print);
  }
}

/**
 * Returns unique string ID for total fleets on planet
 *
 * @param array $planetTemplatized
 *
 * @return int|string
 */
function getUniqueFleetId($planetTemplatized) {
  return empty($planetTemplatized['id']) ? 0 : sprintf(FLEET_ID_TEMPLATE, $planetTemplatized['id']);
}

/**
 * @param array $context
 *
 * @return array
 */
function getLocationFromContext($context = []) {
  if (!empty($context[LOC_FLEET])) {
    return [LOC_FLEET, $context[LOC_FLEET]['fleet_id']];
  } elseif (!empty($context[LOC_PLANET])) {
    return [LOC_PLANET, $context[LOC_PLANET]['id']];
  } elseif (!empty($context[LOC_USER])) {
    return [LOC_USER, $context[LOC_USER]['id']];
  } else {
    return [LOC_SERVER, 0];
  }

}


//


// MAIL ----------------------------------------------------------------------------------------------------------------
function mymail($email_unsafe, $title, $body, $from = '', $html = false) {
  $from = trim($from ? $from : SN::$config->game_adminEmail);

  $head = '';
  $head .= "Content-Type: text/" . ($html ? 'html' : 'plain') . "; charset=utf-8 \r\n";
  $head .= "Date: " . date('r') . " \r\n";
  $head .= "Return-Path: " . SN::$config->game_adminEmail . " \r\n";
  $head .= "From: {$from} \r\n";
  $head .= "Sender: {$from} \r\n";
  $head .= "Reply-To: {$from} \r\n";
//  $head .= "Organization: {$org} \r\n";
  $head .= "X-Sender: {$from} \r\n";
  $head .= "X-Priority: 3 \r\n";

  $body = str_replace("\r\n", "\n", $body);
  $body = str_replace("\n", "\r\n", $body);

  if ($html) {
    $body = '<html><head><base href="' . SN_ROOT_VIRTUAL . '"></head><body>' . nl2br($body) . '</body></html>';
  }

  $title = '=?UTF-8?B?' . base64_encode($title) . '?=';

  return @mail($email_unsafe, $title, $body, $head);
}


// VERSION FUNCTIONS ----------------------------------------------------------------------------------------------------------------
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


// MODULES FUNCTIONS ---------------------------------------------------------------------------------------------------
/**
 * Return Award module or NULL
 *
 * For typecasting
 *
 * @return null|player_award
 */
function moduleAward() {
  return SN::$gc->modules->getModule('player_award');
}

/**
 * Return Captain module or NULL
 *
 * For typecasting
 *
 * @return null|unit_captain
 */
function moduleCaptain() {
  return SN::$gc->modules->getModule('unit_captain');
}

/**
 * Updates users online count
 *
 * We should move this to separate function due to ambiguency of pass() method
 *
 * @param $usersOnline
 */
function dbUpdateUsersOnline($usersOnline) {
  SN::$config->pass()->var_online_user_count = $usersOnline;
}

/**
 * Updates total user count
 *
 * We should move this to separate function due to ambiguency of pass() method
 *
 * @param $userCount
 */
function dbUpdateUsersCount($userCount) {
  SN::$config->pass()->users_amount = $userCount;
}
