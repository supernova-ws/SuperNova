<?php

namespace Common\Interfaces;
/**
 * Created by Gorlum 09.02.2017 11:43
 */
interface IContainer {

  /**
   * @param mixed $name
   * @param mixed $value
   *
   * @return void
   */
  public function __set($name, $value);

  /**
   * @param mixed $name
   *
   * @return bool
   */
  public function __isset($name);

  /**
   * @param mixed $name
   *
   * @return mixed
   */
  public function __get($name);

  /**
   * @param mixed $name
   *
   * @return void
   */
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
