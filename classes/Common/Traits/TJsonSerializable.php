<?php
/**
 * Created by Gorlum 12.10.2017 13:20
 */

namespace Common\Traits;


trait TJsonSerializable {

  /**
   * @param $json
   *
   * @return static
   */
  public static function fromJson($json) {
    $stdobj = json_decode($json);
    $temp = serialize($stdobj);

    // Replacing default object name with ours
    $temp = preg_replace('@^O:8:"stdClass":@', 'O:' . strlen(static::class) . ':"' . static::class . '":', $temp);
    // Converting default objects to arrays
    $temp = preg_replace('@O:8:"stdClass":@', 'a:', $temp);

    //Unserialize and walk away like nothing happened
    $that = unserialize($temp);

//    var_dump($that);

    return $that;
  }

  /**
   * @return string
   */
  public function toJson() {
    return json_encode($this);
  }

}
