<?php
/**
 * Created by Gorlum 05.02.2018 12:31
 */

namespace Ptl;

use PTLTag;
use \template;

class PtlVariableDecorator {
  // Numeric decorators
  const PARAM_NUMERIC = 'num'; // define numeric decorator
  const PARAM_NUMERIC_FLOOR = 'floor';
  const PARAM_NUMERIC_CEIL = 'ceil';
  const PARAM_NUMERIC_ROUND = 'round'; // round=<decimal numbers>

  const PARAM_NUMERIC_FORMAT = 'format';
  const PARAM_NUMERIC_MONEY = 'money';
  const PARAM_NUMERIC_COLOR = 'color'; // Color values: red - negative, yellow - zero, green - positive. Implies "floor" and "format"
//  const PARAM_NUMERIC_LIMIT = 'percent'; // _number_color_value replacement - see Decorators in PTL test
//  const PARAM_NUMERIC_LIMIT = 'limit';

  const PARAM_DATETIME = 'datetime'; // define date and/or time decorator

  /**
   * Сортированный список поддерживаемых параметров
   *
   * @var string[] $allowedParams
   */
  protected static $allowedParams = array(
    self::PARAM_NUMERIC       => '',
    // Will be dumped for all tags which does not have |num
    self::PARAM_NUMERIC_CEIL  => self::PARAM_NUMERIC,
    self::PARAM_NUMERIC_FLOOR => self::PARAM_NUMERIC,
    self::PARAM_NUMERIC_ROUND => self::PARAM_NUMERIC,

    self::PARAM_NUMERIC_FORMAT => self::PARAM_NUMERIC,
    self::PARAM_NUMERIC_MONEY  => self::PARAM_NUMERIC,
    self::PARAM_NUMERIC_COLOR  => self::PARAM_NUMERIC,
//    self::PARAM_NUMERIC_LIMIT  => self::PARAM_NUMERIC,

    self::PARAM_DATETIME       => self::PARAM_DATETIME,
  );

  /**
   * @param string   $strTagFull - full PTL tag with enclosing curly braces
   * @param string   $phpCompiledVar - compiled var reference ready for ECHO command
   * @param template $template - template to apply
   *
   * @return mixed
   */
  public static function decorate($strTagFull, $phpCompiledVar, $template) {
    $ptlTag = new PTLTag(substr($strTagFull, 1, strlen($strTagFull) - 2), $template, static::$allowedParams);

    $phpCompiledVar = static::num($phpCompiledVar, $ptlTag);
    $phpCompiledVar = static::datetime($phpCompiledVar, $ptlTag);

    return $phpCompiledVar;
  }

  /**
   * Return function call
   *
   * @param string   $funcName
   * @param string   $value
   * @param string[] $params
   *
   * @return string
   */
  protected static function func($funcName, $value, $params = []) {
    return $funcName . '(' . $value . (!empty($params) ? ',' . implode(',', $params) : '') . ')';
  }

  protected static function func2($funcName, $params = []) {
    return $funcName . '(' . (!empty($params) ? implode(',', $params) : '') . ')';
  }

  /**
   * @param        $phpCompiledVar
   * @param PTLTag $ptlTag
   */
  protected static function num($phpCompiledVar, $ptlTag) {
    $result = $phpCompiledVar;

    if (array_key_exists(self::PARAM_NUMERIC, $ptlTag->params)) {
      // Just dump other params
      foreach (static::$allowedParams as $paramName => $limitTag) {
        if ($limitTag != self::PARAM_NUMERIC || !array_key_exists($paramName, $ptlTag->params)) {
          continue;
        }

        switch ($paramName) {
          case self::PARAM_NUMERIC_CEIL:
          case self::PARAM_NUMERIC_FLOOR:
            $result = static::func($paramName, $result, '');
          break;

          case self::PARAM_NUMERIC_ROUND:
            $result = static::func($paramName, $result, [intval($ptlTag->params[$paramName])]);
          break;

          case self::PARAM_NUMERIC_FORMAT:
            $result = static::func('HelperString::numberFormat', $result, [0]);
          break;

          case self::PARAM_NUMERIC_MONEY:
            $result = static::func('HelperString::numberFormat', $result, [2]);
          break;

          case self::PARAM_NUMERIC_COLOR:
            $result = static::func('prettyNumberStyledDefault', $result);
          break;
        }
      }

    }

    return $result;
  }

  /**
   * @param        $phpCompiledVar
   * @param PTLTag $ptlTag
   */
  protected static function datetime($phpCompiledVar, $ptlTag) {
    $result = $phpCompiledVar;

    if (array_key_exists(self::PARAM_DATETIME, $ptlTag->params)) {
      // Just dump other params
      foreach (static::$allowedParams as $paramName => $limitTag) {
        if ($limitTag != self::PARAM_DATETIME || !array_key_exists($paramName, $ptlTag->params)) {
          continue;
        }

        switch ($paramName) {
          case self::PARAM_DATETIME:
            $result = "empty($result) ? '' : " . static::func2('date', ["'" . FMT_DATE_TIME_SQL . "'", $result]);
          break;
        }
      }

    }

    return $result;
  }

}
