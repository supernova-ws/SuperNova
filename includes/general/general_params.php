<?php
/**
 * Created by Gorlum 04.12.2017 4:13
 */

function isParamExists($paramName) {
  return array_key_exists($paramName, $_GET) || array_key_exists($paramName, $_POST);
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return string|array
 */
function sys_get_param($param_name, $default = '') {
  return $_POST[$param_name] !== null ? $_POST[$param_name] : ($_GET[$param_name] !== null ? $_GET[$param_name] : $default);
}

/**
 * @param string           $param_name
 * @param int|float|string $default
 *
 * @return int|float|string
 */
function sys_get_param_id($param_name, $default = 0) {
  return is_id($value = sys_get_param($param_name, $default)) ? $value : $default;
}

/**
 * @param string $param_name
 * @param int    $default
 *
 * @return int
 */
function sys_get_param_int($param_name, $default = 0) {
  $value = sys_get_param($param_name, $default);

  return $value === 'on' ? 1 : ($value === 'off' ? $default : intval($value));
}

/**
 * @param string $param_name
 * @param float  $default
 *
 * @return float
 */
function sys_get_param_float($param_name, $default = 0.0) {
  return floatval(sys_get_param($param_name, $default));
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return string
 */
function sys_get_param_escaped($param_name, $default = '') {
  return db_escape(sys_get_param($param_name, $default));
}

/**
 * Get list of units from environment ($_GET, $_POST etc)
 *
 * @param string $param_name
 * @param array  $default
 *
 * @return float[] - [int $unitId] => float $unitAmount
 */
function sys_get_param_unit_array($param_name, $default = []) {
  $result = $default;

  if (is_array($params = sys_get_param($param_name)) && !empty($params)) {
    $result = [];
    foreach (sys_get_param('resources') as $unitId => $unitAmount) {
      $result[intval($unitId)] = floatval($unitAmount);
    }
  }

  return $result;
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return string
 */
function sys_get_param_date_sql($param_name, $default = '2000-01-01') {
  $val = sys_get_param($param_name, $default);

  return preg_match(PREG_DATE_SQL_RELAXED, $val) ? $val : $default;
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return string
 */
function sys_get_param_str_unsafe($param_name, $default = '') {
  return str_raw2unsafe(sys_get_param($param_name, $default));
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return string
 */
function sys_get_param_str($param_name, $default = '') {
  return db_escape(sys_get_param_str_unsafe($param_name, $default));
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return array
 */
function sys_get_param_str_both($param_name, $default = '') {
  $param = sys_get_param($param_name, $default);
  $param_unsafe = str_raw2unsafe($param);

  return array(
    'raw'    => $param,
    'unsafe' => $param_unsafe,
    'safe'   => db_escape($param_unsafe),
  );
}

/**
 * @param string $param_name
 * @param string $default
 *
 * @return array
 */
function sys_get_param_phone($param_name, $default = '') {
  $phone_raw = sys_get_param_str_unsafe($param_name, $default = '');
  if ($phone_raw) {
    $phone = $phone_raw[0] == '+' ? '+' : '';
    for ($i = 0; $i < strlen($phone_raw); $i++) {
      $ord = ord($phone_raw[$i]);
      if ($ord >= 48 && $ord <= 57) {
        $phone .= $phone_raw[$i];
      }
    }
    $phone = strlen($phone) < 11 ? '' : $phone;
  } else {
    $phone = '';
  }

  return array('raw' => $phone_raw, 'phone' => $phone);
}
