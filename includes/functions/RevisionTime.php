<?php

/**
 * RevisionTime.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function RevisionTime ($seconds) {
	$days      = floor($seconds / 86400);
	$hours     = (floor(($seconds % 86400) / 3600));
	$minutes   = floor(($seconds % 3600) / 60);
	$secs      = $seconds % 60;
	$month_len = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$year      = 1970;
	$done      = 0;
	$month_id  = 1;
	while ($days > $month_lenght) {
		$month_lenght  = ($month_id == 2 ? ($year % 4 == 0 ? 29 : $month_len[$month_id]) : $month_len[$month_id]);
		$days         -= $month_lenght;
		if ($month_id > 12) {
			$month_id = 1;
			$year++;
		} else
			$month_id++;
	}
	$days++;
	$days    = ($days < 10 ? "0" . $days : $days);
	$month   = ($month_id < 10 ? "0" . $month_id : $month_id);
	$hours   = ($hours < 10 ? "0" . $hours : $hours);
	$minutes = ($minutes < 10 ? "0" . $minutes : $minutes);
	$secs    = ($secs < 10 ? "0" . $secs : $secs);
	$ret     = ($seconds > 0 ? "$year-$month-$days<br>GMT $hours:$minutes:$secs" : "");
	return $ret;
}
?>