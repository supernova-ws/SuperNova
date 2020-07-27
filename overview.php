<?php
/**
 * index.php - overview.php
 *
 * 2.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [-] Removed News frame
 *     [-] Time & Usersonline moved to Top-Frame
 * 2.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Complying with PCG
 * 2.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Redo flying fleet list
 * 2.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Planets on planet list now have indication of planet fill
 *     [+] Planets on planet list now have indication when there is enemy fleet flying to planet
 * 2.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [+] Now there is full planet list on right side of screen a-la oGame
 *     [+] Planet list now include icons for buildings/tech/fleet on progress
 * 1.5 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Subplanet timers now use sn_timer.js library
 * 1.4 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] All mainplanet timers now use new sn_timer.js library
 * 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of player infos
 * 1.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
 *     [*] Adjusted layouts of planet infos
 * 1.1 - Security checks by Gorlum for http://supernova.ws
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

//define('SN_RENDER_NAVBAR_PLANET', false);

use Pages\Deprecated\PageOverview;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$pageOverview = new PageOverview();
$pageOverview->route();
