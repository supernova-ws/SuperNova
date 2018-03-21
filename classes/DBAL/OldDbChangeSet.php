<?php

/**
 * Created by Gorlum 03.03.2017 22:32
 */

namespace DBAL;

use SN;
use Unit\DBStaticUnit;

/**
 * Class DBAL\OldDbChangeSet
 *
 * This class is DEPRECATED!
 * It should not be used in new code from now!
 *
 * @deprecated
 */
class OldDbChangeSet {

  /**
   * @param      $unit_id
   * @param      $unit_value
   * @param      $user
   * @param null $planet_id
   *
   * @return array
   * @deprecated
   */
  public static function db_changeset_prepare_unit($unit_id, $unit_value, $user, $planet_id = null) {
    if (!is_array($user)) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: DBAL\OldDbChangeSet::db_changeset_prepare_unit() - USER is not ARRAY</h1>');
      pdump(debug_backtrace());
      die('USER is not ARRAY');
    }
    if (!isset($user['id']) || !$user['id']) {
      // TODO - remove later
      print('<h1>СООБЩИТЕ ЭТО АДМИНУ: DBAL\OldDbChangeSet::db_changeset_prepare_unit() - USER[id] пустой</h1>');
      pdump($user);
      pdump(debug_backtrace());
      die('USER[id] пустой');
    }
    $planet_id = is_array($planet_id) && isset($planet_id['id']) ? $planet_id['id'] : $planet_id;

    $unit_location = sys_get_unit_location($user, array(), $unit_id);
    $location_id = $unit_location == LOC_USER ? $user['id'] : $planet_id;
    $location_id = $location_id ? $location_id : 'NULL';

    $temp = DBStaticUnit::db_unit_by_location($user['id'], $unit_location, $location_id, $unit_id);
    if (!empty($temp['unit_id'])) {
      $db_changeset = array(
        'action'  => SQL_OP_UPDATE,
        P_VERSION => 1,
        'where'   => array(
          "unit_id" => $temp['unit_id'],
        ),
        'fields'  => array(
          'unit_level' => array(
            'delta' => $unit_value
          ),
        ),
      );
    } else {
      $db_changeset = array(
        'action' => SQL_OP_INSERT,
        'fields' => array(
          'unit_player_id'     => array(
            'set' => $user['id'],
          ),
          'unit_location_type' => array(
            'set' => $unit_location,
          ),
          'unit_location_id'   => array(
            'set' => $unit_location == LOC_USER ? $user['id'] : $planet_id,
          ),
          'unit_type'          => array(
            'set' => get_unit_param($unit_id, P_UNIT_TYPE),
          ),
          'unit_snid'          => array(
            'set' => $unit_id,
          ),
          'unit_level'         => array(
            'set' => $unit_value,
          ),
        ),
      );
    }

    return $db_changeset;
  }

  public static function db_changeset_condition_compile(&$conditions, &$table_name = '') {
    if (!$conditions[P_LOCATION] || $conditions[P_LOCATION] == LOC_NONE) {
      $conditions[P_LOCATION] = LOC_NONE;
      switch ($table_name) {
        case 'users':
        case LOC_USER:
          $conditions[P_TABLE_NAME] = $table_name = 'users';
          $conditions[P_LOCATION] = LOC_USER;
        break;

        case 'planets':
        case LOC_PLANET:
          $conditions[P_TABLE_NAME] = $table_name = 'planets';
          $conditions[P_LOCATION] = LOC_PLANET;
        break;

        case 'unit':
        case LOC_UNIT:
          $conditions[P_TABLE_NAME] = $table_name = 'unit';
          $conditions[P_LOCATION] = LOC_UNIT;
        break;
      }
    }

    $conditions[P_FIELDS_STR] = '';
    if ($conditions['fields']) {
      $fields = array();
      foreach ($conditions['fields'] as $field_name => $field_data) {
        $condition = "`{$field_name}` = ";
        $value = '';
        if ($field_data['delta']) {
          $value = "`{$field_name}`" . ($field_data['delta'] >= 0 ? '+' : '') . $field_data['delta'];
        } elseif ($field_data['set']) {
          $value = (is_string($field_data['set']) ? "'{$field_data['set']}'" : $field_data['set']);
        }

        if ($value) {
          $fields[] = $condition . $value;
        }
      }
      $conditions[P_FIELDS_STR] = implode(',', $fields);
    }

    $conditions[P_WHERE_STR] = '';
    if (!empty($conditions['where'])) {
      if ($conditions[P_VERSION] == 1) {
        $the_conditions = array();
        foreach ($conditions['where'] as $field_id => $field_value) {
          // Простое условие - $field_id = $field_value
          if (is_string($field_id)) {
            $field_value =
              $field_value === null ? 'NULL' :
                (is_string($field_value) ? "'" . db_escape($field_value) . "'" :
                  (is_bool($field_value) ? intval($field_value) : $field_value));
            $the_conditions[] = "`{$field_id}` = {$field_value}";
          } else {
            die('Неподдерживаемый тип условия');
          }
        }
      } else {
        $the_conditions = &$conditions['where'];
      }
      $conditions[P_WHERE_STR] = implode(' AND ', $the_conditions);
    }

    switch ($conditions['action']) {
      case SQL_OP_DELETE:
        $conditions[P_ACTION_STR] = ("DELETE FROM {{{$table_name}}}");
      break;
      case SQL_OP_UPDATE:
        $conditions[P_ACTION_STR] = ("UPDATE {{{$table_name}}} SET");
      break;
      case SQL_OP_INSERT:
        $conditions[P_ACTION_STR] = ("INSERT INTO {{{$table_name}}} SET");
      break;
      // case SQL_OP_REPLACE: $result = doquery("REPLACE INTO {{{$table_name}}} SET {$fields}") && $result; break;
      default:
        die('Неподдерживаемая операция в DBAL\OldDbChangeSet::db_changeset_condition_compile');
    }

    $conditions[P_QUERY_STR] = $conditions[P_ACTION_STR] . ' ' . $conditions[P_FIELDS_STR] . (' WHERE ' . $conditions[P_WHERE_STR]);
  }

  /**
   * @param $db_changeset
   *
   * @return bool
   * @deprecated
   */
  public static function db_changeset_apply($db_changeset) {
    $result = true;
    if (!is_array($db_changeset) || empty($db_changeset)) {
      return $result;
    }

    foreach ($db_changeset as $table_name => &$table_data) {
      foreach ($table_data as $record_id => &$conditions) {
        OldDbChangeSet::db_changeset_condition_compile($conditions, $table_name);

        if ($conditions['action'] != SQL_OP_DELETE && !$conditions[P_FIELDS_STR]) {
          continue;
        }
        if ($conditions['action'] == SQL_OP_DELETE && !$conditions[P_WHERE_STR]) {
          continue;
        } // Защита от случайного удаления всех данных в таблице

        if ($conditions[P_LOCATION] != LOC_NONE) {
          switch ($conditions['action']) {
            case SQL_OP_DELETE:
              $result = SN::db_del_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR]) && $result;
            break;
            case SQL_OP_UPDATE:
              $result = SN::db_upd_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR], $conditions[P_FIELDS_STR]) && $result;
            break;
            case SQL_OP_INSERT:
              $result = SN::db_ins_record($conditions[P_LOCATION], $conditions[P_FIELDS_STR]) && $result;
            break;
            default:
              die('Неподдерживаемая операция в SN::db_changeset_apply');
          }
        } else {
          $result = doquery($conditions[P_QUERY_STR]) && $result;
        }
      }
    }

    return $result;
  }

}
