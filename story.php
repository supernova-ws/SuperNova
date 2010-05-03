<?php // login2.php :: Permite identificar al usuario, crear la cookie. Y lo redirige a index.php
ob_start(); 
define('INSIDE', true);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

includeLang('login');

if($_POST){ //si no se establecio un post manda al login.php

	//Se realiza una quiery buscando el nombre de usuario
	$login = doquery("SELECT * FROM {{table}} WHERE `username` = '".mysql_escape_string($_POST['username'])."' LIMIT 1","users",true);

	if($login) //Si se encuentra un usuario, $login es una array
	{ //Se identifica la contrase 
		
		if($login['password'] == md5($_POST['password']))
		{
			//
			//Se da un mensaje de aprovacion, y se redirecciona.
			//Se puede optar por no utilizar, y solo hacer un header location
			//Para mantener mas tiempo la expiracion de la cookie
			//
			if (isset($_POST["rememberme"]))
			{
				$expiretime = time()+31536000; $rememberme = 1;
			}
			else
			{
				$expiretime = 0;
				$rememberme = 0;
			}
			
			@include('config.php');
			$cookie = $login["id"] . " " . $login["username"] . " " . md5($login["password"] . "--" . $dbsettings["secretword"]) . " " . $rememberme;
			
			setcookie($game_config['COOKIE_NAME'], $cookie, $expiretime, "/", "", 0);
			
			unset($dbsettings);
			//echo '<META HTTP-EQUIV="refresh" content="3;URL=javascript:self.location=\'index.php\';">'."\n";
			header("Location: ./index.php");
			die();
		}
		else
		{//Muestra un mensaje de error.
			
			message($lang['Login_FailPassword'],$lang['Login_Error']);
			
		}
		
	}
	else
	{ //Cuando $login no contiene datos de jugadores
		
		message($lang['Login_FailUser'],$lang['Login_Error']);
		
	}

}
else
{//Vista normal

	$parse = $lang;
	//preguntamos quien fue el ultimo en registrarse
	$query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC','users',true);
	$parse['last_user'] = $query['username'];
	//preguntamos quien fue el ultimo en registrarse
	$query = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>".(time()-900),'users',true);
	$parse['online_users'] = $query[0];
	//$count = doquery(","users",true);
	$parse['users_amount'] = $game_config['users_amount'];
	
	$page = parsetemplate(gettemplate('story_body'), $parse);

	display($page,$lang['Login']);

}
ob_end_flush();
// Created by Perberos. All rights reversed (C) 2006
?>
