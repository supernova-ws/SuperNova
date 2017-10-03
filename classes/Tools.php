<?php

/**
 * Created by Gorlum 01.03.2017 13:58
 */

/**
 * Common set of system-wide tools
 *
 * Should be broken to several service classes
 */
class Tools {

  /**
   * Return number style based by percent of sample
   *
   * Color coding (.style - color - ranges):
   *   .white - white - value == 0 && sample <= 0 - handles division by zero
   *   .ok - green - value <= 50%
   *   .info - blue - 50% < value <= 75%
   *   .notice - yellow - 75% < value <= 90%
   *   .warning - orange - 90% < value <= 100%
   *   .error - red -  value > 100%
   *
   * @param float $maximum
   * @param float $value
   *
   * @return string - style for number
   */
  public static function fillPercentStyle($maximum, $value) {
    switch (true) {
      case $maximum == 0 && $value == 0:
        $result = 'zero_number';
      break;
      case $value > $maximum:
        $result = 'error';
      break;
      case $value == $maximum:
        $result = 'warning';
      break;
      case $maximum == 0:
        $result = 'zero_number';
      break;

      case ($percent = $value / $maximum) > 0.9:
        $result = 'warning';
      break;
      case $percent > 0.75:
        $result = 'notice';
      break;
      case $percent > 0.50:
        $result = 'info';
      break;
      default:
        $result = 'ok';
      break;
    }

    return $result;
  }

  /**
   * Ues Tools::fillPercentStyle to span-tag
   *
   * @param $value
   * @param $sample
   *
   * @return string
   * @see Tools::fillPercentStyle
   */
  public static function numberPercentSpan($value, $sample) {
    return "<span class=\"" . self::fillPercentStyle($value, $sample) . "\">" . HelperString::numberFloorAndFormat($value) . "</span>";
  }

}
