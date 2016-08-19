<?php

/**
 * Created by Gorlum 30.07.2016 21:15
 */
class ResultMessages {

  /**
   * @param $e Exception
   * @param array $resultMessages
   *
   * @return int|mixed
   */
  public static function parseException($e, &$resultMessages) {
    $resultMessages[] = array(
      'STATUS'  => in_array($e->getCode(), array(ERR_NONE, ERR_WARNING, ERR_ERROR)) ? $e->getCode() : ERR_ERROR,
      'MESSAGE' => classSupernova::$gc->localePlayer[$e->getMessage()],
    );

    return $e->getCode();
  }

}