<?php
/**
 * maintenance.php
 *
 * @version 1.0
 * @copyright 2009 by Gorlum for http://oGame.Triolan.COM.UA
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

includeLang('admin');
$parse = $lang;

$script = '
<script type="text/javascript">
$(document).ready(function() {
  // send requests
  $.post("admin/maintenance_ajax.php", {rating: $(this).html()}, function(xml) {
    // format result
    var result = [ $("message", xml).text() ];
    // output result
    $("#admin_message").html(result.join($("#admin_message").html));
  } );
});
</script>';

AdminMessage ( $script . '<img src=design/images/progressbar.gif><br>' . $lang['sys_wait'], $lang['adm_maintenance_title'] );

?>
