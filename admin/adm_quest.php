<?php

/**
 * quest.php
 *
 * @v1 (c) copyright 2011 by Gorlum for http://supernova.ws
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN'  , true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_OPERATOR);

roughQuestRenderWrapper();
