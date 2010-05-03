<?php

/**
 * GetMissileRange.php
 *
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

function GetMissileRange () {
	global $resource, $user;

	if ($user[$resource[117]] > 0) {
		$MissileRange = ($user[$resource[117]] * 5) - 1;
	} elseif ($user[$resource[117]] == 0) {
		$MissileRange = 0;
	}

	return $MissileRange;
}
?>