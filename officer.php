<?php

/**
 * officer.php
 * Handles officer hire
 *
 * @package roleplay
 * @version 2.0
 *
 * Revision History
 * ================
 * 2.0 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Utilizes PTE
 *
 * 1.2 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [~] Security checks & tests
 *
 * 1.1 copyright 2008 By Chlorel for XNova
 *   [~] Réécriture Chlorel pour integration complete dans XNova
 *
 * 1.0 copyright 2008 By Tom1991 for XNova
 *   [!] Version originelle (Tom1991)
 *
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

include("includes/includes/mrc_mercenary.php");

mrc_mercenary_render($user);

?>
