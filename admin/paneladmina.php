<?php

/**
 * paneladmina.php
 *
 * @version 1.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$GET_action = sys_get_param_str('action');
$GET_result = sys_get_param_str('result');
$Pattern    = sys_get_param_str('player');
$NewLvl     = intval($_GET['authlvl']);
$ip         = sys_get_param_str('ip');

$PanelMainTPL = gettemplate('admin/admin_panel_main');

$parse                  = $lang;
$parse['adm_sub_form1'] = '';
$parse['adm_sub_form2'] = '';
$parse['adm_sub_form3'] = '';

// Afficher les templates
if (isset($GET_result)) {
  switch ($GET_result){
    case 'usr_search':
      $SelUser = db_user_by_username('%'. $Pattern .'%', false, '*', true, true);
      $UsrMain = db_planet_by_id($SelUser['id_planet'], false, 'name');

      $bloc                   = $lang;
      $bloc['answer1']        = $SelUser['id'];
      $bloc['answer2']        = $SelUser['username'];
      $bloc['answer3']        = $SelUser['user_lastip'];
      $bloc['answer4']        = $SelUser['email'];
      $bloc['answer5']        = $lang['adm_usr_level'][ $SelUser['authlevel'] ];
      $bloc['answer6']        = $lang['adm_usr_genre'][ $SelUser['gender'] ];
      $bloc['answer7']        = "[".$SelUser['id_planet']."] ".$UsrMain['name'];
      $bloc['answer8']        = "[".$SelUser['galaxy'].":".$SelUser['system'].":".$SelUser['planet']."] ";
      $bloc['qst_quest_complete'] = get_quest_amount_complete($SelUser['id']);
      $bloc['user_id'] = $SelUser['id'];
      $SubPanelTPL            = gettemplate('admin/admin_panel_asw1');
      $parse['adm_sub_form2'] = parsetemplate( $SubPanelTPL, $bloc );
      break;

    case 'usr_data':
      print('Временно не работает');
      break;

    case 'usr_level':

      # only for admins
      if ($user['authlevel'] < 3 || $NewLevel >= $user['authlevel'])
      {
        message($lang['sys_noalloaw'], $lang['sys_noaccess']);
        die();
      }

      $selected_user = db_user_by_username($Pattern, false, 'id');
      $QryUpdate = db_user_set_by_id($selected_user['id'], "`authlevel` = '{$NewLvl}'");
      $Message    = $lang['adm_mess_lvl1']. " ". $Pattern ." ".$lang['adm_mess_lvl2'];
      $Message   .= "<font color=\"red\">".$lang['adm_usr_level'][ $NewLvl ]."</font>!";

      AdminMessage ( $Message, $lang['adm_mod_level'] );
      break;

    case 'ip_search':

      $bloc                   = $lang;
      $bloc['adm_this_ip']    = $ip;
      $SelUser = db_user_list("`user_lastip` = '{$ip}'");
      //while ( $Usr = db_fetch($SelUser) ) {
      foreach($SelUser as $Usr) {
        $UsrMain = db_planet_by_id($Usr['id_planet'], false, 'name');
        $bloc['adm_plyer_lst'] .= "<tr><th>".$Usr['username']."</th><th>[".$Usr['galaxy'].":".$Usr['system'].":".$Usr['planet']."] ".$UsrMain['name']."</th></tr>";
      }
      $SubPanelTPL            = gettemplate('admin/admin_panel_asw2');
      $parse['adm_sub_form2'] = parsetemplate( $SubPanelTPL, $bloc );
      break;
    default:
      break;
  }
}

// Traiter les reponses aux formulaires
if (isset($GET_action)) {
  $bloc                   = $lang;
  switch ($GET_action){
    case 'usr_search':
      $SubPanelTPL            = gettemplate('admin/admin_panel_frm1');
      break;

    case 'usr_data':
      $SubPanelTPL            = gettemplate('admin/admin_panel_frm4');
      break;

    case 'usr_level':
      # only for admins
      if ($user['authlevel'] != 3)
      {
        message($lang['sys_noalloaw'], $lang['sys_noaccess']);
        die();
      }


      for ($Lvl = 0; $Lvl < 4; $Lvl++) {
        $bloc['adm_level_lst'] .= '<option value="'. $Lvl .'">'. $lang['adm_usr_level'][ $Lvl ] ."</option>";
      }
      $SubPanelTPL            = gettemplate('admin/admin_panel_frm3');
      break;

    case 'ip_search':
      $SubPanelTPL            = gettemplate('admin/admin_panel_frm2');
      break;

    default:
      break;
  }
  $parse['adm_sub_form2'] = parsetemplate( $SubPanelTPL, $bloc );
}

$page = parsetemplate( $PanelMainTPL, $parse );
display( $page, $lang['panel_mainttl'], false, '', true );
?>
