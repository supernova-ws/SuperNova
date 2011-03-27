<?php

/**
 * moonlist.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 2)
{
  AdminMessage($lang['adm_err_denied']);
}

includeLang('overview');

$parse = $lang;
$query = doquery("SELECT * FROM {{planets}} WHERE planet_type='3'");
$i = 0;
while ($u = mysql_fetch_array($query)) {
  $parse['moon'] .= "<tr>"
  . "<td class=b><center><b>" . $u[0] . "</center></b></td>"
  . "<td class=b><center><b>" . $u[1] . "</center></b></td>"
  . "<td class=b><center><b>" . $u[2] . "</center></b></td>"
  . "<td class=b><center><b>" . $u[4] . "</center></b></td>"
  . "<td class=b><center><b>" . $u[5] . "</center></b></td>"
  . "<td class=b><center><b>" . $u[6] . "</center></b></td>"
  . "</tr>";
  $i++;
}

if ($i == "1")
  $parse['moon'] .= "<tr><th class=b colspan=6>Il y a qu'une seule lune</th></tr>";
else
  $parse['moon'] .= "<tr><th class=b colspan=6>Il y a {$i} lunes</th></tr>";

display(parsetemplate(gettemplate('admin/moonlist_body'), $parse), 'Lunalist' , false, '', true);

?>
