<?php
/**
 * Created by Gorlum 03.09.2019 1:26
 */

namespace Common\Traits;


trait TSingleton {

  private static $_me = null;

  /**
   * @return static|null
   */
  public static function me() {
    if (empty(static::$_me)) {
      static::$_me = new static();
    }

    return static::$_me;
  }

}
