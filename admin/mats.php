<?php

/**
 * mats.php
 *
 * @version 1.0
 * @copyright 2008 by Xire -AlteGarde-
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

	if ($user['authlevel'] >= "1") {
		includeLang('admin/mats');

		$PanelMainTPL = gettemplate('admin/mats');

		$parse                  = $lang;
		$parse['adm_sub_form1'] = "";
		$parse['adm_sub_form2'] = "";
		$parse['adm_sub_form3'] = "";

		// Afficher les templates
		if (isset($_GET['result'])) {
			switch ($_GET['result']){
				case 'usr_search':
					$Pattern = $_GET['player'];
					$SelUser = doquery("SELECT * FROM {{table}} WHERE `username` LIKE '%". $Pattern ."%' LIMIT 1;", 'users', true);
					$UsrMain = doquery("SELECT `name` FROM {{table}} WHERE `id` = '". $SelUser['id_planet'] ."';", 'planets', true);

					$bloc                   = $lang;
					$bloc['answer1']        = $SelUser['id'];
					$bloc['answer2']        = $SelUser['username'];
					$bloc['answer3']        = $SelUser['user_lastip'];
					$bloc['answer4']        = $SelUser['email'];
					$bloc['answer5']        = $lang['adm_usr_level'][ $SelUser['authlevel'] ];
					$bloc['answer6']        = $lang['adm_usr_genre'][ $SelUser['sex'] ];
					$bloc['answer7']        = "[".$SelUser['id_planet']."] ".$UsrMain['name'];
					$bloc['answer8']        = "[".$SelUser['galaxy'].":".$SelUser['system'].":".$SelUser['planet']."] ";
					$SubPanelTPL            = gettemplate('admin/admin_panel_asw1');
					$parse['adm_sub_form2'] = parsetemplate( $SubPanelTPL, $bloc );
					break;

				case 'usr_data':
					$Pattern = $_GET['player'];
					$SelUser = doquery("SELECT * FROM {{table}} WHERE `username` LIKE '%". $Pattern ."%' LIMIT 1;", 'users', true);
					$UsrMain = doquery("SELECT `name` FROM {{table}} WHERE `id` = '". $SelUser['id_planet'] ."';", 'planets', true);

					$bloc                    = $lang;
					$bloc['answer1']         = $SelUser['id'];
					$bloc['answer2']         = $SelUser['username'];
					$bloc['answer3']         = $SelUser['user_lastip'];
					$bloc['answer4']         = $SelUser['email'];
					$bloc['answer5']         = $lang['adm_usr_level'][ $SelUser['authlevel'] ];
					$bloc['answer6']         = $lang['adm_usr_genre'][ $SelUser['sex'] ];
					$bloc['answer7']         = "[".$SelUser['id_planet']."] ".$UsrMain['name'];
					$bloc['answer8']         = "[".$SelUser['galaxy'].":".$SelUser['system'].":".$SelUser['planet']."] ";
					$SubPanelTPL             = gettemplate('admin/admin_panel_asw1');
					$parse['adm_sub_form1']  = parsetemplate( $SubPanelTPL, $bloc );

					$parse['adm_sub_form2']  = "<table><tbody>";
					$parse['adm_sub_form2'] .= "<tr><td colspan=\"4\" class=\"c\">".$lang['adm_colony']."</td></tr>";
					$UsrColo = doquery("SELECT * FROM {{table}} WHERE `id_owner` = '". $SelUser['id'] ." ORDER BY `galaxy` ASC, `planet` ASC, `system` ASC, `planet_type` ASC';", 'planets');
					while ( $Colo = mysql_fetch_assoc($UsrColo) ) {
						if ($Colo['id'] != $SelUser['id_planet']) {
							$parse['adm_sub_form2'] .= "<tr><th>".$Colo['id']."</th>";
							$parse['adm_sub_form2'] .= "<th>". (($Colo['planet_type'] == 1) ? $lang['adm_planet'] : $lang['adm_moon'] ) ."</th>";
							$parse['adm_sub_form2'] .= "<th>[".$Colo['galaxy'].":".$Colo['system'].":".$Colo['planet']."]</th>";
							$parse['adm_sub_form2'] .= "<th>".$Colo['name']."</th></tr>";
						}
					}
					$parse['adm_sub_form2'] .= "</tbody></table>";

					$parse['adm_sub_form3']  = "<table><tbody>";
					$parse['adm_sub_form3'] .= "<tr><td colspan=\"4\" class=\"c\">".$lang['adm_technos']."</td></tr>";
					for ($Item = 100; $Item <= 199; $Item++) {
						if ($resource[$Item] != "") {
							$parse['adm_sub_form3'] .= "<tr><th>".$lang['tech'][$Item]."</th>";
							$parse['adm_sub_form3'] .= "<th>".$SelUser[$resource[$Item]]."</th></tr>";
						}
					}
					$parse['adm_sub_form3'] .= "</tbody></table>";
					break;

				case 'usr_level':
					$Player     = $_GET['player'];
					$NewLvl     = $_GET['authlvl'];

					$QryUpdate  = doquery("UPDATE {{table}} SET `authlevel` = '".$NewLvl."' WHERE `username` = '".$Player."';", 'users');
					$Message    = $lang['adm_mess_lvl1']. " ". $Player ." ".$lang['adm_mess_lvl2'];
					$Message   .= "<font color=\"red\">".$lang['adm_usr_level'][ $NewLvl ]."</font>!";

					AdminMessage ( $Message, $lang['adm_mod_level'] );
					break;

				case 'ip_search':
					$Pattern    = $_GET['ip'];
					$SelUser    = doquery("SELECT * FROM {{table}} WHERE `user_lastip` = '". $ip ."' LIMIT 10;", 'users');
					$bloc                   = $lang;
					$bloc['adm_this_ip']    = $Pattern;
					while ( $Usr = mysql_fetch_assoc($SelUser) ) {
						$UsrMain = doquery("SELECT `name` FROM {{table}} WHERE `id` = '". $Usr['id_planet'] ."';", 'planets', true);
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
		if (isset($_GET['action'])) {
			$bloc                   = $lang;
			switch ($_GET['action']){
				case 'usr_search':
					$SubPanelTPL            = gettemplate('admin/admin_panel_frm1');
					break;

				case 'usr_data':
					$SubPanelTPL            = gettemplate('admin/admin_panel_frm4');
					break;

				case 'usr_level':
					for ($Lvl = 0; $Lvl < 4; $Lvl++) {
						$bloc['adm_level_lst'] .= "<option value=\"". $Lvl ."\">". $lang['adm_usr_level'][ $Lvl ] ."</option>";
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
	} else {
		message( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}

?>