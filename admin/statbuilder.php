<?php

/**
 * StatBuilder.php
 *
 * @version 1.1 (c) copyright 2010 by Gorlum for http://supernova.ws
 *   [*] All calculations moved to StatFunctions.php - thus we can utilize them in automatized stats calculations
 * @version 1
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);
require_once('../common.' . substr(strrchr(__FILE__, '.'), 1));

global $lang, $user;

SnTemplate::messageBoxAdminAccessDenied(AUTH_LEVEL_ADMINISTRATOR);

if(SN_TIME_NOW >= SN::$config->pass()->var_stat_update_admin_forced && SN_TIME_NOW >= SN::$config->pass()->var_stat_update_end)
{
  SN::$config->pass()->var_stat_update_admin_forced = SN_TIME_NOW + 120;

  $script = '<script type="text/javascript">
  $(document).ready(function() {
    // send requests
    $.post("scheduler.php?admin_update=1&' . SN_TIME_NOW . '", function(result) {
      // format result
      // alert(xml);
      // var result = [ $("message", xml).text() ];
      // output result
      // $("#admin_message").html(result.join(""));
      $("#admin_message").html(result);
    }, "json" );
  });
  </script>';

  SnTemplate::messageBoxAdmin("{$script}<img src=\"design/images/progressbar.gif\"><br>{$lang['sys_wait']}", $lang['adm_stat_title'], '', 0);
}
else
{
  SnTemplate::messageBoxAdmin($lang['adm_stat_already_started'], $lang['adm_stat_title'], 'admin/overview.php');
}

// require_once('../scheduler.php');
