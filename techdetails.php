<?php

/**
 * techdetails.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
	includeLang('login');
	header("Location: login.php");
}

check_urlaubmodus ($user);

$Id                  = $_GET['techid'];
$PageTPL             = gettemplate('techtree_details');
$RowsTPL             = gettemplate('techtree_details_rows');

$parse               = $lang;
$parse['te_dt_id']   = $Id;
$parse['te_dt_name'] = $lang['tech'][$Id];
$Liste = "";

if ($Id == 12) {
    $Liste .= "<tr><th>".$lang['tech']['31']." (".$lang['level']." 1)</th></tr>";
    $Liste .= "<tr><td class=\"c\">2</td><tr>";
    $Liste .= "<tr><th>".$lang['tech']['3']." (".$lang['level']." 5)</th></tr>";
    $Liste .= "<tr><th>".$lang['tech']['113']." (".$lang['level']." 3) <a href=\"techtreedetails.php?tech=113\">[i]</a></th></tr>";
}

$parse['Liste'] = $Liste;
$page = parsetemplate($PageTPL, $parse);

display ($page, $lang['Tech'], false, '', false);
?>