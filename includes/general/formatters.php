<?php
/**
 * Created by Gorlum 04.12.2017 4:25
 */

/**
 * Format $value to ID
 *
 * @param     $value
 * @param int $default
 *
 * @return float|int
 */
function idval($value, $default = 0) {
  $value = floatval($value);

  return preg_match('#^(\d*)#', $value, $matches) && $matches[1] ? floatval($matches[1]) : $default;
}


/**
 * @param int|float      $number
 * @param true|int|float $compareTo
 *    true             - compare to zero from above i.e. resources amount ($number > 0 for positive)
 *    numeric positive - compare to $compareTo from below i.e. price with resource amount ($number < $compareTo for positive)
 *    numeric negative - compare to -$compareTo from above i.e. resource amount with price ($n > -$compareTo for positive)
 *
 * @return string
 */
function prettyNumberGetClass($number, $compareTo) {
  $n = floor($number);

  if ($compareTo === true) {
    $class = $n == 0 ? 'zero' : ($n > 0 ? 'positive' : 'negative');
  } elseif ($compareTo >= 0) {
    $class = $n == $compareTo ? 'zero' : ($n < $compareTo ? 'positive' : 'negative');
  } else {
    $class = ($n == -$compareTo) ? 'zero' : ($n < -$compareTo ? 'negative' : 'positive');
  }

  return $class;
}

/**
 * Return number floored, formatted and styled with "span"
 *
 * @param int|float $number
 *
 * @return string
 *
 * // TODO - this should be made in templates
 */
function prettyNumberStyledDefault($number) {
  return prettyNumberStyledCompare($number, true);
}

/**
 * @param int|float $number
 * @param int|float $compareTo
 *
 * @return string
 *
 * // TODO - this should be made in templates
 */
function prettyNumberStyledCompare($number, $compareTo) {
  return
    '<span class="' . prettyNumberGetClass($number, $compareTo) . '">' .
    HelperString::numberFloorAndFormat($number) .
    '</span>';
}

// ----------------------------------------------------------------------------------------------------------------
function pretty_time($seconds) {
  $day = floor($seconds / (24 * 3600));

  return sprintf('%s%02d:%02d:%02d', $day ? $day . classSupernova::$lang['sys_day_short'] . ' ' : '', floor($seconds / 3600 % 24), floor($seconds / 60 % 60), floor($seconds / 1 % 60));
}

function sys_time_human($time, $full = false) {
  global $lang;

  $seconds = $time % 60;
  $time = floor($time / 60);
  $minutes = $time % 60;
  $time = floor($time / 60);
  $hours = $time % 24;
  $time = floor($time / 24);

  return
    ($full || $time ? "{$time} {$lang['sys_day']}&nbsp;" : '') .
    ($full || $hours ? "{$hours} {$lang['sys_hrs']}&nbsp;" : '') .
    ($full || $minutes ? "{$minutes} {$lang['sys_min']}&nbsp;" : '') .
    ($full || !$time || $seconds ? "{$seconds} {$lang['sys_sec']}" : '');
}

function sys_time_human_system($time) {
  return $time ? date(FMT_DATE_TIME_SQL, $time) . " ({$time}), " . sys_time_human(SN_TIME_NOW - $time) : '{NEVER}';
}

if (!function_exists('strptime')) {
  function strptime($date, $format) {
    $masks = array(
      '%d' => '(?P<d>[0-9]{2})',
      '%m' => '(?P<m>[0-9]{2})',
      '%Y' => '(?P<Y>[0-9]{4})',
      '%H' => '(?P<H>[0-9]{2})',
      '%M' => '(?P<M>[0-9]{2})',
      '%S' => '(?P<S>[0-9]{2})',
      // usw..
    );

    $rexep = "#" . strtr(preg_quote($format), $masks) . "#";
    if (preg_match($rexep, $date, $out)) {
      $ret = array(
        "tm_sec"  => (int)$out['S'],
        "tm_min"  => (int)$out['M'],
        "tm_hour" => (int)$out['H'],
        "tm_mday" => (int)$out['d'],
        "tm_mon"  => $out['m'] ? $out['m'] - 1 : 0,
        "tm_year" => $out['Y'] > 1900 ? $out['Y'] - 1900 : 0,
      );
    } else {
      $ret = false;
    }

    return $ret;
  }
}


function js_safe_string($string) {
  return str_replace(array("\r", "\n"), array('\r', '\n'), addslashes($string));
}

function sys_safe_output($string) {
  return str_replace(array("&", "\"", "<", ">", "'"), array("&amp;", "&quot;", "&lt;", "&gt;", "&apos;"), $string);
}

function str_raw2unsafe($raw) {
  return trim(strip_tags($raw));
}

function ip2longu($ip) {
  return sprintf('%u', floatval(ip2long($ip)));
}
