<?php
/**
 * Created by Gorlum 04.06.2017 15:18
 */

namespace Common\Exceptions;

use Throwable;

/**
 * Class ExceptionSnLocalized
 *
 * Localized Exception - uses Message as locale string ID
 *
 * @package Exceptions
 */
class ExceptionSnLocalized extends \Exception {

  /**
   * @var array $sprintf
   */
  protected $sprintf = array();

  /**
   * ExceptionSnLocalized constructor.
   *
   * @param string         $message
   * @param int            $code
   * @param Throwable|null $previous
   * @param array          $sprintf - params for sprintf() call to embed into message
   */
  public function __construct($message = "", $code = 0, Throwable $previous = null, $sprintf = array()) {
    parent::__construct($message, $code, $previous);
    $this->sprintf = $sprintf;
  }

  /**
   * @param string $message
   *
   * @return string
   */
  protected function getPlayerLocalization($message) {
    global $lang;

    return !empty($lang[$message]) ? $lang[$message] : '';
  }

  /**
   * @return string
   */
  public function getMessageLocalized() {
    $message = $this->getPlayerLocalization($this->getMessage());
    if (is_array($this->sprintf) && !empty($this->sprintf)) {
      $message = call_user_func_array('sprintf', array_merge(array($message), $this->sprintf));
    }

    return $message;
  }

}
