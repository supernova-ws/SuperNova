<?php
/**
 * Created by Gorlum 13.08.2016 15:18
 */

namespace Common;


class snMath {

  /**
   * Эта функция выдает нормально распределенное случайное число с матожиднием $mu и стандартным отклонением $sigma
   *
   * $strict - количество $sigma, по которым идет округление функции. Т.е. $strict = 3 означает, что диапазон значений обрезается по +-3 * $sigma
   * Используется http://ru.wikipedia.org/wiki/Преобразование_Бокса_—_Мюллера
   *
   * @param int  $mu
   * @param int  $sigma
   * @param bool $strict
   *
   * @return int
   */
  public static function sn_rand_gauss($mu = 0, $sigma = 1, $strict = false) {
    // http://ru.wikipedia.org/wiki/Среднеквадратическое_отклонение
    // При $mu = 0 (график симметричный, цифры только для половины графика)
    // От 0 до $sigma ~ 34.1%
    // От $sigma до 2 * $sigma ~ 13.6%
    // От 2 * $sigma до 3 * $sigma ~ 2.1%
    // От 3 * $sigma до бесконечности ~ 0.15%
    // Не менее 99.7% случайных величин лежит в пределах +-3 $sigma

    $max_rand = mt_getrandmax();
    $random = cos(2 * pi() * (mt_rand(1, $max_rand) / $max_rand)) * sqrt(-2 * log(mt_rand(1, $max_rand) / $max_rand));
    $random = $strict === false ? $random : ($random > $strict ? $strict : ($random < -$strict ? -$strict : $random));

    return $mu + $sigma * $random;
  }


  /**
   * Функция возвращает случайное нормально распределенное целое число из указанного промежутка
   *
   * @param float      $range_start - Начало диапазона
   * @param float      $range_end - Конец диапазона
   * @param bool|int   $round - До скольки знаков округлять результат. False - не округлять, True - округлять до целого, 1 - округлять до десятков, 2 - до сотен итд
   * @param int        $strict - В сколько сигм надо уложить результат
   * @param bool|false $cut_extreme - надо ли обрезать крайние значения. Например, при $strict = 2 их слишком много
   *
   * @return float|int
   */
  public static function sn_rand_gauss_range($range_start, $range_end, $round = true, $strict = 4, $cut_extreme = false) {
    if ($cut_extreme) {
      $range_start--;
      $range_end++;
    }
    do {
      $random = static::sn_rand_gauss(($range_start + $range_end) / 2, ($range_end - $range_start) / $strict / 2, $strict);
      $round_emul = pow(10, $round === true ? 0 : $round);
      $result = $round ? round($random * $round_emul) / $round_emul : $random;
    } while ($cut_extreme && ($result == $range_start || $result == $range_end));

    return $result;
  }

  /**
   * Returns average of array
   *
   * @param array $arr
   *
   * @return float
   */
  public static function average($arr) {
    return is_array($arr) && count($arr) ? array_sum($arr) / count($arr) : 0;
  }

  /**
   * Return median of values - list of them or array of values
   *
   * @return bool|float|mixed
   */
  public static function median() {
    $args = func_get_args();

    switch (func_num_args()) {
      case 0:
        // trigger_error('median() requires at least one parameter',E_USER_WARNING);
        return false;
      break;

      case 1:
        $args = array_pop($args);
      // fallthrough

      default:
        if (!is_array($args)) {
          // trigger_error('median() requires a list of numbers to operate on or an array of numbers', E_USER_NOTICE);
          return false;
        }

        sort($args);

        $n = count($args);
        $h = intval($n / 2);

        if ($n % 2 == 0) {
          $median = ($args[$h] + $args[$h - 1]) / 2;
        } else {
          $median = $args[$h];
        }

      break;
    }

    return $median;
  }

  /**
   * Basic linear equation system solver
   *
   * @param array $linear
   * @param int   $from
   * @param bool  $logProcess
   */
  public static function linear_calc(&$linear, $from = 0, $logProcess = false) {
    for ($i = $from; $i < count($linear); $i++) {
      $eq = &$linear[$i];
      for ($j = count($eq) - 1; $j >= $from; $j--) {
        $eq[$j] /= $eq[$from];
      }
    }
    if ($logProcess) {
      pdump($linear, 'Нормализовано по х' . $from);
    }

    for ($i = $from + 1; $i < count($linear); $i++) {
      $eq = &$linear[$i];
      for ($j = count($eq) - 1; $j >= $from; $j--) {
        $eq[$j] -= $linear[$from][$j];
      }
    }
    if ($logProcess) {
      pdump($linear, 'Подставили х' . $from);
    }

    if ($from < count($linear) - 1) {
      static::linear_calc($linear, $from + 1, $logProcess);
    }

    if ($from) {
      for ($i = 0; $i < $from; $i++) {
        $eq = &$linear[$i];
        for ($j = count($eq) - 1; $j >= $from; $j--) {
          $eq[$j] = $eq[$j] - $eq[$from] * $linear[$from][$j];
        }
      }
      if ($logProcess) {
        pdump($linear, 'Подставили обратно х' . $from);
      }
    } else {
      if ($logProcess) {
        pdump($linear, 'Результат' . $from);
      }
      foreach ($linear as $index => &$eq) {
        pdump($eq[count($linear)], 'x' . $index);
      }
    }
  }

  /**
   * Calculates geometric progression sum for N-th element
   *
   * @param int   $n
   * @param float $b1
   * @param float $q
   *
   * @return float
   */
  public static function geometry_progression_sum($n, $b1, $q) {
    return $q != 1 ? ($b1 * (pow($q, $n) - 1) / ($q - 1)) : ($n * $b1);
  }

  /**
   * Floor to less preferable value
   *
   * @param float $value
   *
   * @return float
   */
  public static function sn_floor($value) {
    return $value >= 0 ? floor($value) : ceil($value);
  }

}
