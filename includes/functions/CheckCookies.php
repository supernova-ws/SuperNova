<?php

/**
 * CheckCookies.php
 *
 * @version 1.1
 * @copyright 2008 By Chlorel for XNova
 */
// TheCookie[0] = `id`
// TheCookie[1] = `username`
// TheCookie[2] = Password + Hashcode
// TheCookie[3] = 1rst Connexion time + 365 J

function CheckCookies ( $IsUserChecked ) {
	global $lang, $game_config, $ugamela_root_path, $phpEx;

	includeLang('cookies');

	$UserRow = array();

	include($ugamela_root_path . 'config.' . $phpEx);

	if (isset($_COOKIE[$game_config['COOKIE_NAME']])) {
		$TheCookie  = explode("/%/", $_COOKIE[$game_config['COOKIE_NAME']]);
		$UserResult = doquery("SELECT * FROM `{{table}}` WHERE `username` = '". $TheCookie[1]. "';", 'users');

		// On verifie s'il y a qu'un seul enregistrement pour ce nom
		if (mysql_num_rows($UserResult) != 1) {
			message( $lang['cookies']['Error1'] );
		}

		$UserRow    = mysql_fetch_array($UserResult);

		// On teste si on a bien le bon UserID
		if ($UserRow["id"] != $TheCookie[0]) {
			message( $lang['cookies']['Error2'] );
		}

		// On teste si le mot de passe est correct !
		if (md5($UserRow["password"] . "--" . $dbsettings["secretword"]) !== $TheCookie[2]) {
			message( $lang['cookies']['Error3'] );
		}

		$NextCookie = implode("/%/", $TheCookie);
		// Au cas ou dans l'ancien cookie il etait question de se souvenir de moi
		// 3600 = 1 Heure // 86400 = 1 Jour // 31536000 = 365 Jours
		// on ajoute au compteur!
		if ($TheCookie[3] == 1) {
			$ExpireTime = time() + 31536000;
		} else {
			$ExpireTime = 0;
		}

		if ($IsUserChecked == false) {
			setcookie ($game_config['COOKIE_NAME'], $NextCookie, $ExpireTime, "/", "", 0);
			$QryUpdateUser  = "UPDATE `{{table}}` SET ";
			$QryUpdateUser .= "`onlinetime` = '". time() ."', ";
			$QryUpdateUser .= "`user_lastip` = '". $_SERVER['REMOTE_ADDR'] ."', ";
			$QryUpdateUser .= "`user_agent` = '". $_SERVER['HTTP_USER_AGENT'] ."' ";
			$QryUpdateUser .= "WHERE ";
			$QryUpdateUser .= "`id` = '". $TheCookie[0] ."' LIMIT 1;";
			doquery( $QryUpdateUser, 'users');
			$IsUserChecked = true;
		} else {
			$QryUpdateUser  = "UPDATE `{{table}}` SET ";
			$QryUpdateUser .= "`onlinetime` = '". time() ."', ";
			$QryUpdateUser .= "`user_lastip` = '". $_SERVER['REMOTE_ADDR'] ."', ";
			$QryUpdateUser .= "`user_agent` = '". $_SERVER['HTTP_USER_AGENT'] ."' ";
			$QryUpdateUser .= "WHERE ";
			$QryUpdateUser .= "`id` = '". $TheCookie[0] ."' LIMIT 1;";
			doquery( $QryUpdateUser, 'users');
			$IsUserChecked = true;
		}
	}

	unset($dbsettings);

	$Return['state']  = $IsUserChecked;
	$Return['record'] = $UserRow;

	return $Return;
}
?>