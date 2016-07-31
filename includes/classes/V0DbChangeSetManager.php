<?php

/**
 * Created by Gorlum 31.07.2016 20:22
 */
class V0DbChangeSetManager {

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

      default:
        die('Неподдерживаемая операция в classSupernova::db_changeset_condition_compile');
    }

    $conditions[P_QUERY_STR] = $conditions[P_ACTION_STR] . ' ' . $conditions[P_FIELDS_STR] . (' WHERE ' . $conditions[P_WHERE_STR]);
  }

  public static function db_changeset_apply($db_changeset, $flush_delayed = false) {
    $result = true;
    if (!is_array($db_changeset) || empty($db_changeset)) {
      return $result;
    }

    foreach ($db_changeset as $table_name => &$table_data) {
      // TODO - delayed changeset
      foreach ($table_data as $record_id => &$conditions) {
        static::db_changeset_condition_compile($conditions, $table_name);

        if ($conditions['action'] != SQL_OP_DELETE && !$conditions[P_FIELDS_STR]) {
          continue;
        }
        if ($conditions['action'] == SQL_OP_DELETE && !$conditions[P_WHERE_STR]) {
          continue;
        } // Защита от случайного удаления всех данных в таблице

        if ($conditions[P_LOCATION] != LOC_NONE) {
          switch ($conditions['action']) {
            case SQL_OP_DELETE:
              $result = classSupernova::db_del_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR]) && $result;
            break;
            case SQL_OP_UPDATE:
              $result = classSupernova::db_upd_record_list($conditions[P_LOCATION], $conditions[P_WHERE_STR], $conditions[P_FIELDS_STR]) && $result;
            break;
            case SQL_OP_INSERT:
              $result = classSupernova::db_ins_record($conditions[P_LOCATION], $conditions[P_FIELDS_STR]) && $result;
            break;
            default:
              die('Неподдерживаемая операция в classSupernova::db_changeset_apply');
          }
        } else {
          $result = classSupernova::$db->doExecute($conditions[P_QUERY_STR]) && $result;
        }
      }
    }

    return $result;
  }


}
