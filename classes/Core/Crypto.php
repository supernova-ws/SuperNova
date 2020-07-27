<?php
/**
 * Created by Gorlum 13.02.2020 11:49
 */

namespace Core;

/**
 * Cryptography & signature-related tools
 *
 * @package Core
 */
class Crypto {
  /**
   * @var GlobalContainer $gc
   */
  protected $gc;

  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  /**
   * @param string $string
   *
   * @return string
   */
  public function sign($string) {
    return $this->hash($string);
  }

  public function signCheck($string, $sign) {
    return $this->hash($string) === $sign;
  }

  /**
   * Used hash function
   *
   * @param $string
   *
   * @return string
   */
  public function hash($string) {
    return md5($string);
  }

}
