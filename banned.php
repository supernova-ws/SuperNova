<?php

/**
 * banned.php
 *
 * List of all issued bans
 *
 * @version 1.0 Created by e-Zobar (XNova Team). All rights reversed (C) 2008
 *
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

includeLang('banned');

$parse = $lang;
$parse['dpath'] = $dpath;
$parse['mf']    = '_self';


$query = doquery("SELECT * FROM {{table}} ORDER BY `id`;",'banned');
$i=0;
while($u = mysql_fetch_array($query)){
  $parse['banned'] .=
    "<tr align=center><td class=b><b>".$u[1]."</td></b>".
    "<td class=b><b>".$u[2]."</b></td>".
    "<td class=b><b>".date($config->game_date_withTime,$u[4])."</b></td>".
    "<td class=b><b>".date($config->game_date_withTime,$u[5])."</b></td>".
    "<td class=b><b>".$u[6]."</b></td></tr>";
  $i++;
}

$parse['banned_count'] = $i;

display(parsetemplate(gettemplate('banned_body'), $parse), 'Banned');
?>