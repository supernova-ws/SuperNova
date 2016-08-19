<?php

namespace Common;

class Types {

  /**
   * @param string $type TYPE_XXX constant
   * @param mixed  $value Value to cast
   *
   * @return bool|float|int|null|string
   */
  public function castAs($type, $value) {
    // TODO: Here should be some conversions to property type
    switch($type) {
      case TYPE_INTEGER:
        $value = intval($value);
      break;

      case TYPE_DOUBLE:
        $value = floatval($value);
      break;

      case TYPE_BOOLEAN:
        $value = boolval($value);
      break;

      case TYPE_NULL:
        $value = null;
      break;

      case TYPE_ARRAY:
        $value = (array)$value;
      break;

      case TYPE_STRING:
        // Empty type is string
      case TYPE_EMPTY:
        // No-type defaults to string
      default:
        $value = (string)$value;
      break;
    }

    return $value;
  }

  /**
   * @param \EntityContainer $that
   * @param string           $propertyName
   * @param string           $fieldName
   */
  public function dateTimeImport($that, $propertyName, $fieldName) {
    if (isset($that->row[$fieldName])) {
      $dateTime = new \DateTime($that->row[$fieldName]);
    } else {
      $dateTime = null;
    }
    $that->$propertyName = $dateTime;
  }

  /**
   * @param \EntityContainer $that
   * @param string           $propertyName
   * @param string           $fieldName
   */
  public function dateTimeExport($that, $propertyName, $fieldName) {
    $dateTime = $that->$propertyName;
    if ($dateTime instanceof \DateTime) {
      $that->row[$fieldName] = $dateTime->format(FMT_DATE_TIME_SQL);
    } else {
      $that->row[$fieldName] = null;
    }
  }

}
