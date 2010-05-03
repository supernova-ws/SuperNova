<?php
/*
#############################################################################
#  Filename: IsVacationMode.php
#  Create date: Monday, March 10, 2008    20:13:20
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
function IsVacationMode($CurrentUser){
   global $game_config;

   if($CurrentUser['urlaubs_modus'] == 1){
   $query = doquery("SELECT * FROM `{{table}}` WHERE id_owner = '{$CurrentUser['id']}'", 'planets');
   while($id = mysql_fetch_array($query)){
      doquery("UPDATE {{table}} SET
               metal_perhour = '".$game_config['metal_basic_income']."',
               crystal_perhour = '".$game_config['crystal_basic_income']."',
               deuterium_perhour = '".$game_config['deuterium_basic_income']."',
               metal_mine_porcent = '0',
               crystal_mine_porcent = '0',
               deuterium_sintetizer_porcent = '0',
               solar_plant_porcent = '0',
               fusion_plant_porcent = '0',
               solar_satelit_porcent = '0'
             WHERE id = '{$id['id']}' AND `planet_type` = '1' ", 'planets');
      }
      return true;
   }
   return false;
}
?>