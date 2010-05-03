<?php
/**
 *
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// Fonctions deja 'au propre'

if (!defined('INSIDE')) {
	die('Hacking attemp');
}

// Functions already 'with the propre'
//define('INSIDE' , true);
//define('INSTALL', false);

$dir = opendir($ugamela_root_path . 'includes/functions');

while (($file = readdir($dir)) !== false) {
	$extension = '.' . substr($file, -3);
	if ($extension == ".$phpEx")
		require_once $ugamela_root_path . 'includes/functions/' . $file;
}

?>