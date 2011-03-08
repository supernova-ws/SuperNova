<?php

/**
 * viewreport.php
 *
 * @version 1
 * @copyright 2008 by MadnessRed
 * Created by Anthony for Darkness of Evolution
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('viewreport');

$BodyTPL = gettemplate('viewreport');
$parse   = $lang;

$page = parsetemplate($BodyTPL, $parse);
display($page, $lang['vr_title'], false);

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Created by MadnessRed

?>
