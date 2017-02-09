<?php

/**
 * Created by Gorlum 09.02.2017 11:43
 */
interface IContainer {

  public function __set($name, $value);

  public function __isset($name);

  public function __get($name);

  public function __unset($name);

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty();

  /**
   * Clears container contents
   */
  public function clear();


}
