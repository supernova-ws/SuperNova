<?php

/**
 * neusuw.php
 *
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

if ($IsUserChecked == false) {
	includeLang('login');
	header("Location: login.php");
}

check_urlaubmodus ($user);
includeLang('usuw');

$lang['PHP_SELF'] = 'neusuw.'.$phpEx;

if($_POST && $mode == "change"){ //Array ( [db_character]

	$iduser = $user["id"];
	$avatar = $_POST["avatar"];
	$dpath = $_POST["dpath"];
	//Mostrar skin
	if(isset($_POST["design"])&& $_POST["design"] == 'on'){
		$design = "1";
	}else{
		$design = "0";
	}
	//Desactivar comprobaci? de IP
	if(isset($_POST["noipcheck"])&& $_POST["noipcheck"] == 'on'){
		$noipcheck = "1";
	}else{
		$noipcheck = "0";
	}
	//Nombre de usuario
	if(isset($_POST["db_character"])&& $_POST["db_character"] != ''){
		$username = $_POST['db_character'];
	}else{
		$username = $user['username'];
	}
	//Cantidad de sondas de espionaje
	if(isset($_POST["spio_anz"])&&is_numeric($_POST["spio_anz"])){
		$spio_anz = $_POST["spio_anz"];
	}else{
		$spio_anz = "1";
	}
	//Mostrar tooltip durante
	if(isset($_POST["settings_tooltiptime"]) && is_numeric($_POST["settings_tooltiptime"])){
		$settings_tooltiptime = $_POST["settings_tooltiptime"];
	}else{
		$settings_tooltiptime = "1";
	}
	//Maximo mensajes de flotas
	if(isset($_POST["settings_fleetactions"]) && is_numeric($_POST["settings_fleetactions"])){
		$settings_fleetactions = $_POST["settings_fleetactions"];
	}else{
		$settings_fleetactions = "1";
	}//
	//Mostrar logos de los aliados
	if(isset($_POST["settings_allylogo"]) && $_POST["settings_allylogo"] == 'on'){
		$settings_allylogo = "1";
	}else{
		$settings_allylogo = "0";
	}
	//Espionaje
	if(isset($_POST["settings_esp"]) && $_POST["settings_esp"] == 'on'){
		$settings_esp = "1";
	}else{
		$settings_esp = "0";
	}
	//Escribir mensaje
	if(isset($_POST["settings_wri"]) && $_POST["settings_wri"] == 'on'){
		$settings_wri = "1";
	}else{
		$settings_wri = "0";
	}
	//A?dir a lista de amigos
	if(isset($_POST["settings_bud"]) && $_POST["settings_bud"] == 'on'){
		$settings_bud = "1";
	}else{
		$settings_bud = "0";
	}
	//Ataque con misiles
	if(isset($_POST["settings_mis"]) && $_POST["settings_mis"] == 'on'){
		$settings_mis = "1";
	}else{
		$settings_mis = "0";
	}
	//Ver reporte
	if(isset($_POST["settings_rep"]) && $_POST["settings_rep"] == 'on'){
		$settings_rep = "1";
	}else{
		$settings_rep = "0";
	}
	//Modo vacaciones
	if(isset($_POST["urlaubs_modus"]) && $_POST["urlaubs_modus"] == 'on'){
		$urlaubs_modus = "1";
	}else{
		$urlaubs_modus = "0";
	}
	//Borrar cuenta
	if(isset($_POST["db_deaktjava"]) && $_POST["db_deaktjava"] == 'on'){
		$db_deaktjava = "1";
	}else{
		$db_deaktjava = "0";
	}
	doquery("UPDATE {{table}} SET
	`email` = '$db_email',
	`avatar` = '$avatar',
	`dpath` = '$dpath',
	`design` = '$design',
	`noipcheck` = '$noipcheck',
	`spio_anz` = '$spio_anz',
	`settings_tooltiptime` = '$settings_tooltiptime',
	`settings_fleetactions` = '$settings_fleetactions',
	`settings_allylogo` = '$settings_allylogo',
	`settings_esp` = '$settings_esp',
	`settings_wri` = '$settings_wri',
	`settings_bud` = '$settings_bud',
	`settings_mis` = '$settings_mis',
	`settings_rep` = '$settings_rep',
	`urlaubs_modus` = '$urlaubs_modus',
	`db_deaktjava` = '$db_deaktjava',
	`kolorminus` = '$kolorminus',
	`kolorplus` = '$kolorplus',
	`kolorpoziom` = '$kolorpoziom'
	WHERE `id` = '$iduser' LIMIT 1","users");


	if(isset($_POST["db_password"]) && md5($_POST["db_password"]) == $user["password"]){

		if($_POST["newpass1"] == $_POST["newpass2"]){
			$newpass = md5($_POST["newpass1"]);
			doquery("UPDATE {{table}} SET `password` = '{$newpass}' WHERE `id` = '{$user['id']}' LIMIT 1","users");
			setcookie(COOKIE_NAME, "", time()-100000, "/", "", 0);//le da el expire
			message($lang['succeful_changepass'],$lang['changue_pass']);
		}

	}
	if($user['username']!=$_POST["db_character"]){

		$query = doquery("SELECT id FROM {{table}} WHERE username='{$_POST["db_character"]}'",'users',true);
		if(!$query){
			doquery("UPDATE {{table}} SET username='{$username}' WHERE id='{$user['id']}' LIMIT 1","users");
			setcookie(COOKIE_NAME, "", time()-100000, "/", "", 0);//le da el expire
			message($lang['succeful_changename'],$lang['changue_name']);
		}
	}
	message($lang['succeful_save'],$lang['Options']);
}
else
{

	$parse = $lang;

	$parse['dpath'] = $dpath;
	$parse['user_username'] = $user['username'];
	$parse['user_email'] = $user['email'];
	$parse['user_email_2'] = $user['email_2'];
	$parse['user_dpath'] = $user['dpath'];
	$parse['user_avatar'] = $user['avatar'];
	$parse['user_spio_anz'] = $user['spio_anz'];
	$parse['user_settings_tooltiptime'] = $user['settings_tooltiptime'];
	$parse['user_settings_fleetactions'] = $user['settings_fleetactions'];
	$parse['user_design'] = ($user['design'] == 1) ? " checked='checked'":'';
	$parse['user_noipcheck'] = ($user['noipcheck'] == 1) ? " checked='checked'":'';
	$parse['user_settings_allylogo'] = ($user['settings_allylogo'] == 1) ? " checked='checked'/":'';
	$parse['user_db_deaktjava'] = ($user['db_deaktjava'] == 1) ? " checked='checked'/":'';
	$parse['user_urlaubs_modus'] = ($user['urlaubs_modus'] == 1)?" checked='checked'/":'';
	$parse['user_settings_rep'] = ($user['settings_rep'] == 1) ? " checked='checked'/":'';
	$parse['user_settings_esp'] = ($user['settings_esp'] == 1) ? " checked='checked'/":'';
	$parse['user_settings_wri'] = ($user['settings_wri'] == 1) ? " checked='checked'/":'';
	$parse['user_settings_mis'] = ($user['settings_mis'] == 1) ? " checked='checked'/":'';
	$parse['user_settings_bud'] = ($user['settings_bud'] == 1) ? " checked='checked'/":'';
	$parse['kolorminus'] = $user['kolorminus'];
	$parse['kolorplus'] = $user['kolorplus'];
	$parse['kolorpoziom'] = $user['kolorpoziom'];

	display(parsetemplate(gettemplate('usuw_body'), $parse), $lang['Usuw']);
	die();
}

// Created by Perberos. All rights reversed (C) 2006
?>