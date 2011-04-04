<?php

/**
 * changelog.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 1)
{
  AdminMessage($lang['adm_err_denied']);
}

lng_include('changelog');
$template = gettemplate('changelog_table');

$parse = $lang;

foreach ($lang['changelog'] as $a => $b)
{

  $parse['version_number'] = $a;
  $parse['description'] = nl2br($b);

  $body .= parsetemplate($template, $parse);
}

$parse['body'] = $body;

$page .= parsetemplate(gettemplate('changelog_body'), $parse);
display($page, "Changelog", false, '', true);

// Created by Perberos. All rights reversed (C) 2006

?>