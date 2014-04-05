<?php

/**
 * officer.php
 * Handles officer hire
 *
 * @package roleplay
 * @version 2.0
 *
 * copyright (c) 2009-2012 by Gorlum for http://supernova.ws
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

include("includes/includes/mrc_mercenary.php");

mrc_mercenary_render($user);
