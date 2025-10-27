<?php
/** Created by Gorlum 23.10.2025 20:24 */

namespace DBAL;

class RecordV3 {

  /**
   * @param array|object $data
   *
   * @return static
   */
  public static function castTo($data) {
    if (is_object($data)) {
      $data = get_object_vars($data);
    }

    $object = new static();
    foreach ($data as $key => $value) {
      if (property_exists($object, $key)) {
        $object->$key = $value;
      }
    }

    return $object;
  }

}