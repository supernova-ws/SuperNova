<?php
/**
 * Created by PhpStorm.
 * User: Gorlum
 * Date: 26.12.2015
 * Time: 17:19
 */

/**
 * Normalize and make ID safe
 *
 * @param $db_row
 *
 * @return float|int
 */
function db_normalize_id($db_row, $field_name = 'id') {
  return idval(is_array($db_row) && !empty($db_row[$field_name]) ? $db_row[$field_name] : $db_row);
}

/**
 * Makes set safe
 *
 * @param array $set
 * @param bool $delta - Is it delta set?
 *
 * @return string
 */
function db_set_make_safe_string($set, $delta = false) {
  $set_safe = array();
  foreach($set as $field => $value) {
    if(empty($field)) {
      continue;
    }

    $field = '`' . db_escape($field) . '`';
    $new_value = $value;
    if($value === null) {
      $new_value = 'NULL';
    } elseif(is_string($value) && (string)($new_value = floatval($value)) != (string)$value) {
      // non-float
      $new_value = '"' . db_escape($value) . '"';
    } elseif($delta) {
      // float and DELTA-set
      $new_value = "{$field} + ({$new_value})";
    }
    $set_safe[] = "{$field} = {$new_value}";
  }

  $set_safe = implode(',', $set_safe);

  return $set_safe;
}
