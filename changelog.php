<?php

/**
 * changelog.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('changelog');

$template = gettemplate('changelog_table');


foreach($lang['changelog'] as $a => $b)
{

  $parse['version_number'] = $a;
  $parse['description'] = nl2br($b);

  $body .= parsetemplate($template, $parse);

}

$parse = $lang;
$parse['body'] = $body;

  display(parsetemplate(gettemplate('changelog_body'), $parse), "Change Log");

// Created by Perberos. All rights reversed (C) 2006
?>