<?php
/*
#############################################################################
#  Filename: CheckIfIsBuilding.php
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
function CheckIfIsBuilding($CurrentUser){
   $query = doquery("SELECT * FROM `{{table}}` WHERE id_owner = '{$CurrentUser['id']}'", 'planets');
   while($id = mysql_fetch_array($query)){
      if($id['b_building'] != 0){
         if($id['b_building'] != ""){
            return true;
         }
      }elseif($id['b_tech'] != 0){
         if($id['b_tech'] != ""){
            return true;
         }
      }elseif($id['b_hangar'] != 0){
         if($id['b_hangar'] != ""){
            return true;
         }
      }
   }
   $fleets = doquery("SELECT * FROM `{{table}}` WHERE `fleet_owner` = '{$CurrentUser['id']}'", 'fleets',true);
   if($fleets){
      return true;
   }
   return false;
}
?>