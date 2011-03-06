<?php

/**
 * banned.php
 *
 * List of all issued bans
 *
 * @version 1.0 Created by e-Zobar (XNova Team). All rights reversed (C) 2008
 *
 */

$allow_anonymous = true;
$skip_ban_check = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('banned');

$parse = $lang;

$query = doquery("SELECT * FROM {{banned}} ORDER BY `id`;");
$i=0;
while($u = mysql_fetch_array($query)){
  $parse['banned'] .=
    "<tr align=center><td class=b><b>".$u[1]."</td></b>".
    "<td class=b><b>".$u[2]."</b></td>".
    "<td class=b><b>".date(FMT_DATE_TIME,$u[4])."</b></td>".
    "<td class=b><b>".date(FMT_DATE_TIME,$u[5])."</b></td>".
    "<td class=b><b>".$u[6]."</b></td></tr>";
  $i++;
}

$parse['banned_count'] = $i;

display(parsetemplate(gettemplate('banned_body', true), $parse), 'Banned');

?>
