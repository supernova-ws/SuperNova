<?php
/**
 * Created by Gorlum 12.10.2017 13:20
 */

namespace Traits;


trait TJsonSerializable {

  /**
   * @param $json
   */
  public static function fromJson($json) {

  }

  /**
   * @return string
   */
  public function toJson() {
    return json_encode($this);
  }

}
