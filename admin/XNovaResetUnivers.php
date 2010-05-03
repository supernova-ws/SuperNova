<?php

/**
 * XNovaResetUnivers.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

includeLang('admin');

function XNovaResetUnivers ( $CurrentUser ) {
	global $lang;

	if ($CurrentUser['authlevel'] >= 3) {

		// Copier la table users et planets vers des tables de replis !
		doquery( "RENAME TABLE {{table}} TO {{table}}_s", 'planets' );
		doquery( "RENAME TABLE {{table}} TO {{table}}_s", 'users' );

		// Recreer la structure des tables renommées
		doquery( "CREATE  TABLE IF NOT EXISTS {{table}} ( LIKE {{table}}_s );", 'planets');
		doquery( "CREATE  TABLE IF NOT EXISTS {{table}} ( LIKE {{table}}_s );", 'users');

		// Vider toutes les tables !
		doquery( "TRUNCATE TABLE {{table}}", 'aks');
		doquery( "TRUNCATE TABLE {{table}}", 'alliance');
		doquery( "TRUNCATE TABLE {{table}}", 'annonce');
		doquery( "TRUNCATE TABLE {{table}}", 'banned');
		doquery( "TRUNCATE TABLE {{table}}", 'buddy');
		doquery( "TRUNCATE TABLE {{table}}", 'chat');
		doquery( "TRUNCATE TABLE {{table}}", 'galaxy');
		doquery( "TRUNCATE TABLE {{table}}", 'errors');
		doquery( "TRUNCATE TABLE {{table}}", 'fleets');
		doquery( "TRUNCATE TABLE {{table}}", 'iraks');
		doquery( "TRUNCATE TABLE {{table}}", 'lunas');
		doquery( "TRUNCATE TABLE {{table}}", 'messages');
		doquery( "TRUNCATE TABLE {{table}}", 'notes');
		doquery( "TRUNCATE TABLE {{table}}", 'rw');
		doquery( "TRUNCATE TABLE {{table}}", 'statpoints');

		$AllUsers  = doquery ("SELECT `username`,`password`,`email`, `email_2`,`authlevel`,`galaxy`,`system`,`planet`, `sex`, `dpath`, `onlinetime`, `register_time`, `id_planet` FROM {{table}} WHERE 1;", 'users_s');
		$LimitTime = time() - (15 * (24 * (60 * 60)));
		$TransUser = 0;
		while ( $TheUser = mysql_fetch_assoc($AllUsers) ) {
			if ( $TheUser['onlinetime'] > $LimitTime ) {
				$UserPlanet     = doquery ("SELECT `name` FROM {{table}} WHERE `id` = '". $TheUser['id_planet']."';", 'planets_s', true);
				if ($UserPlanet['name'] != "") {
					// Creation de l'utilisateur
					$QryInsertUser  = "INSERT INTO {{table}} SET ";
					$QryInsertUser .= "`username` = '".      $TheUser['username']      ."', ";
					$QryInsertUser .= "`email` = '".         $TheUser['email']         ."', ";
					$QryInsertUser .= "`email_2` = '".       $TheUser['email_2']       ."', ";
					$QryInsertUser .= "`sex` = '".           $TheUser['sex']           ."', ";
					$QryInsertUser .= "`id_planet` = '0', ";
					$QryInsertUser .= "`authlevel` = '".     $TheUser['authlevel']     ."', ";
					$QryInsertUser .= "`dpath` = '".         $TheUser['dpath']         ."', ";
					$QryInsertUser .= "`galaxy` = '".        $TheUser['galaxy']        ."', ";
					$QryInsertUser .= "`system` = '".        $TheUser['system']        ."', ";
					$QryInsertUser .= "`planet` = '".        $TheUser['planet']        ."', ";
					$QryInsertUser .= "`register_time` = '". $TheUser['register_time'] ."', ";
					$QryInsertUser .= "`password` = '".      $TheUser['password']      ."';";
					doquery( $QryInsertUser, 'users');

					// On cherche le numero d'enregistrement de l'utilisateur fraichement cr��
					$NewUser        = doquery("SELECT `id` FROM {{table}} WHERE `username` = '". $TheUser['username'] ."' LIMIT 1;", 'users', true);

					CreateOnePlanetRecord ($TheUser['galaxy'], $TheUser['system'], $TheUser['planet'], $NewUser['id'], $UserPlanet['name'], true);
					// Recherche de la reference de la nouvelle planete (qui est unique normalement !
					$PlanetID       = doquery("SELECT `id` FROM {{table}} WHERE `id_owner` = '". $NewUser['id'] ."' LIMIT 1;", 'planets', true);

					// Mise a jour de l'enregistrement utilisateur avec les infos de sa planete mere
					$QryUpdateUser  = "UPDATE {{table}} SET ";
					$QryUpdateUser .= "`id_planet` = '".      $PlanetID['id'] ."', ";
					$QryUpdateUser .= "`current_planet` = '". $PlanetID['id'] ."' ";
					$QryUpdateUser .= "WHERE ";
					$QryUpdateUser .= "`id` = '".             $NewUser['id']  ."';";
					doquery( $QryUpdateUser, 'users');
					$TransUser++;
				}
			}
		} // while
		// Mise a jour du nombre de joueurs inscripts
		doquery("UPDATE {{table}} SET `config_value` = '". $TransUser ."' WHERE `config_name` = 'users_amount' LIMIT 1;", 'config');

		// Menage on vire les tables transitoires
		doquery("DROP TABLE {{table}}", 'planets_s');
		doquery("DROP TABLE {{table}}", 'users_s');

		AdminMessage ( $TransUser . $lang['adm_rz_done'], $lang['adm_rz_ttle'] );
	} else {
		AdminMessage ( $lang['sys_noalloaw'], $lang['sys_noaccess'] );
	}
	return $Page;
}

	$mode      = $_POST['mode'];
	$PageTpl   = gettemplate("admin/reset_body");
	$parse     = $lang;

	if ($mode == 'reset') {
		XNovaResetUnivers ( $user );
	} else {
		$Page = parsetemplate($PageTpl, $parse);
		display ($Page, $lang['Reset'], false, '', true);
	}
?>