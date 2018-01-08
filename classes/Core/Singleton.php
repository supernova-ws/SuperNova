<?php
/**
 * Created by Gorlum 08.01.2018 15:33
 */

namespace Core;


class Singleton {

  private static $_singleton;

  public static function singleton() {
    if (empty(static::$_singleton)) {
      static::$_singleton = new static();
    }

    return static::$_singleton;
  }

}
