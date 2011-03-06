<?php

/**
 * credit.php
 *
 * @version 1.0
 * @copyright 2008 by e-Zobar for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('credit');

$parse   = $lang;
$parse['admin_email'] = $config->game_adminEmail;
$parse['url_forum'] = '/phpBB3/';

display(parsetemplate(gettemplate('credit_body'), $parse), $lang['cred_credit'], false, "", false, false);

?>
