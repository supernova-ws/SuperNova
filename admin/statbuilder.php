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

// if($user['authlevel'] < 1)
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

if(SN_TIME_NOW >= classSupernova::$config->db_loadAll('var_stat_update_admin_forced') && SN_TIME_NOW >= classSupernova::$config->db_loadAll('var_stat_update_end'))
{
  classSupernova::$config->db_saveItem('var_stat_update_admin_forced', SN_TIME_NOW + 120);

  $script = '<script type="text/javascript">
  $(document).ready(function() {
    // send requests
    $.post("scheduler.php?admin_update=1", function(result) {
      // format result
      // alert(xml);
      // var result = [ $("message", xml).text() ];
      // output result
      // $("#admin_message").html(result.join(""));
      $("#admin_message").html(result);
    }, "json" );
  });
  </script>';

  AdminMessage("{$script}<img src=\"design/images/progressbar.gif\"><br>{$lang['sys_wait']}", $lang['adm_stat_title'], '', 120);
}
else
{
  AdminMessage($lang['adm_stat_already_started'], $lang['adm_stat_title'], 'admin/overview.php', 5);
}

// require_once('../scheduler.php');
