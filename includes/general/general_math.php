<?php

function geometry_progression_sum($n, $b1, $q) {
  return $q != 1 ? ($b1 * (pow($q, $n) - 1)/($q - 1)) : ($n * $b1);
}

function sn_floor($value)
{
  return $value >= 0 ? floor($value) : ceil($value);
}

// Эта функция выдает нормально распределенное случайное число с матожиднием $mu и стандартным отклонением $sigma
// $strict - количество $sigma, по которым идет округление функции. Т.е. $strict = 3 означает, что диапазон значений обрезается по +-3 * $sigma
// Используется http://ru.wikipedia.org/wiki/Преобразование_Бокса_—_Мюллера
function sn_rand_gauss($mu = 0, $sigma = 1, $strict = false)
{
  // http://ru.wikipedia.org/wiki/Среднеквадратическое_отклонение
  // При $mu = 0 (график симметричный, цифры только для половины графика)
  // От 0 до $sigma ~ 34.1%
  // От $sigma до 2 * $sigma ~ 13.6%
  // От 2 * $sigma до 3 * $sigma ~ 2.1%
  // От 3 * $sigma до бесконечности ~ 0.15%
  // Не менее 99.7% случайных величин лежит в пределах +-3 $sigma

//  $r = sn_rand_0_1();
//  $phi = sn_rand_0_1();
//  $z0 = cos(2 * pi() * $phi) * sqrt(-2 * log($r));
//  return $mu + $sigma * $z0;
  $max_rand = mt_getrandmax();
  $random = cos(2 * pi() * (mt_rand(1, $max_rand) / $max_rand)) * sqrt(-2 * log(mt_rand(1, $max_rand) / $max_rand));
  $random = $strict === false ? $random : ($random > $strict ? $strict : ($random < -$strict ? -$strict : $random));

  return $mu + $sigma * $random;
}

// Функция возвращает случайное нормально распределенное целое число из указанного промежутка
/**
 * @param float      $range_start - Начало диапазона
 * @param float      $range_end - Конец диапазона
 * @param bool|int  $round - До скольки знаков округлять результат. False - не округлять, True - округлять до целого, 1 - округлять до десятков, 2 - до сотен итд
 * @param int        $strict - В сколько сигм надо уложить результат
 * @param bool|false $cut_extreme - надо ли обрезать крайние значения. Например, при $strict = 2 их слишком много
 *
 * @return float|int
 */
function sn_rand_gauss_range($range_start, $range_end, $round = true, $strict = 4, $cut_extreme = false)  {
  if($cut_extreme) {
    $range_start--;
    $range_end++;
  }
  do {
    $random = sn_rand_gauss(($range_start + $range_end) / 2, ($range_end - $range_start) / $strict / 2, $strict);
    $round_emul = pow(10, $round === true ? 0 : $round);
    $result = $round ? round($random * $round_emul) / $round_emul : $random;
  } while ($cut_extreme && ($result == $range_start || $result == $range_end));
  return $result;
}

/**
 * Return median of array or list of arguments
 *
 * @return bool|float
 */
function median() {
  $args = func_get_args();

  switch (func_num_args()) {
    case 0:
      // trigger_error('median() requires at least one parameter',E_USER_WARNING);
      return false;
    break;

    /** @noinspection PhpMissingBreakStatementInspection */
    case 1:
      $args = array_pop($args);
    // fallthrough

    default:
      if (!is_array($args)) {
        // trigger_error('median() requires a list of numbers to operate on or an array of numbers', E_USER_NOTICE);
        return false;
      }

      sort($args);

      $h = intval(count($args) / 2);

      $median = count($args) % 2 == 0 ? ($args[$h] + $args[$h - 1]) / 2 : $args[$h];

    break;
  }

  return $median;
}

/**
 * @param array $array
 *
 * @return float
 */
function avg($array)
{
  return is_array($array) && count($array) ? array_sum($array) / count($array) : 0;
}
function linear_calc(&$linear, $from = 0, $debug = false)
{
  for($i = $from; $i < count($linear); $i++)
  {
    $eq = &$linear[$i];
    for($j = count($eq) - 1; $j >= $from; $j--)
    {
      $eq[$j] /= $eq[$from];
    }
  }
  if($debug) pdump($linear, 'Нормализовано по х' . $from);

  for($i = $from + 1; $i < count($linear); $i++)
  {
    $eq = &$linear[$i];
    for($j = count($eq) - 1; $j >= $from; $j--)
    {
      $eq[$j] -= $linear[$from][$j];
    }
  }
  if($debug) pdump($linear, 'Подставили х' . $from);

  if($from < count($linear) - 1)
  {
    linear_calc($linear, $from + 1, $debug);
  }

  if($from)
  {
    for($i = 0; $i < $from; $i++)
    {
      $eq = &$linear[$i];
      for($j = count($eq) - 1; $j >= $from; $j--)
      {
        $eq[$j] = $eq[$j] - $eq[$from] * $linear[$from][$j];
      }
    }
    if($debug) pdump($linear, 'Подставили обратно х' . $from);
  }
  else
  {
    if($debug) pdump($linear, 'Результат' . $from);
    foreach($linear as $index => &$eq)
    {
      pdump($eq[count($linear)], 'x' . $index);
    }
  }
}

/**
 * Get number's sign
 *
 * @param int|float $number
 *
 * @return int
 */
function sign($number) {
  return ($number > 0) - ($number < 0);
}
