<?php

/**
 * maintenance.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */
define('INSIDE', true);
define('INSTALL', false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if ($user['authlevel'] < 3)
{
  AdminMessage(classLocale::$lang['adm_err_denied']);
}

$script = '
<script type="text/javascript">
$(document).ready(function() {
  $.post("admin/ajax_maintenance.php", function(result) {
    $("#admin_message").html(result);
  }, "json" );
});
</script>';

AdminMessage($script . '<img src=design/images/progressbar.gif><br>' . classLocale::$lang['sys_wait'], classLocale::$lang['adm_maintenance_title']);
