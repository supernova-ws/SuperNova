<?php

/**
 * deletuser.php
 *
 * @version 1.0
 * @copyright 2008 by Tom1991 for XNova
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 3)
{
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$PageTpl = gettemplate("admin/deletuser");

if ($mode != "delet")
{
  $parse['adm_bt_delet'] = classLocale::$lang['adm_bt_delet'];
}

$Page = parsetemplate($PageTpl, $parse);
display($Page, classLocale::$lang['adminpanel'], false, '', true);

?>
