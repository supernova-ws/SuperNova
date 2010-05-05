<?php

/**

* add_pts.php

*

* version 1.0

* (c) copyright 2008 By Gildass

* 

*/




define('INSIDE'  , true);

define('INSTALL' , false);

define('IN_ADMIN', true);



$ugamela_root_path = './../';

include($ugamela_root_path . 'extension.inc');

include($ugamela_root_path . 'common.' . $phpEx);



   if ($user['authlevel'] >= 2) {

      includeLang('admin');



      $mode      = $_POST['mode'];



      $PageTpl   = gettemplate("admin/add_pts");

      $parse     = $lang;



      if ($mode == 'addit') {

         $id          = $_POST['id'];

         $rpg_points       = $_POST['rpg_points'];


         $QryUpdateUsers  = "UPDATE {{table}} SET ";

         $QryUpdateUsers .= "`rpg_points` = `rpg_points` + '". $rpg_points ."' ";
         $QryUpdateUsers .= " WHERE `id` = '".$id."' ";


         doquery( $QryUpdateUsers, "users");



         AdminMessage ( $lang['ad_offi_ok'], $lang['ad_offi_poi'] );
          }
          $Page = parsetemplate($PageTpl, $parse);

          display ($Page, $lang['ad_sup_poi'], false, '', true);


   } else {

      AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );

   }



?>
