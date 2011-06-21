<?php

/**
 * quest.php
 *
 * @v1 (c) copyright 2011 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

require('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('quest');
$template = gettemplate('quest', true);

qst_render_page();

display($template, $lang['qst_quests']);

?>
