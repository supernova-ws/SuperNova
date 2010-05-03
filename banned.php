<?php

/**
 * banned.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
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
        "<tr><td class=b><center><b>".$u[1]."</center></td></b>".
  "<td class=b><center><b>".$u[2]."</center></b></td>".
  "<td class=b><center><b>".date("d/m/Y G:i:s",$u[4])."</center></b></td>".
  "<td class=b><center><b>".date("d/m/Y G:i:s",$u[5])."</center></b></td>".
  "<td class=b><center><b>".$u[6]."</center></b></td></tr>";
  $i++;
}

if ($i=="0")
 $parse['banned'] .= "<tr><th class=b colspan=6>Il n'y a pas de joueurs bannis</th></tr>";
else
  $parse['banned'] .= "<tr><th class=b colspan=6>Il y a {$i} joueurs bannis</th></tr>";

display(parsetemplate(gettemplate('banned_body'), $parse), 'Banned');

// Created by e-Zobar (XNova Team). All rights reversed (C) 2008
?>