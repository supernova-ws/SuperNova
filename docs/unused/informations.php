<?php // informations.php ->Si vous savez pas lire, c'est les infos du serveur OMG 

define('INSIDE', true);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);

    $parse = $lang;
    //preguntamos quien fue el ultimo en registrarse
    $query = doquery('SELECT username FROM {{table}} ORDER BY register_time DESC','users',true);
    $parse['last_user'] = $query['username'];
    //preguntamos quien fue el ultimo en registrarse
    $query = doquery("SELECT COUNT(DISTINCT(id)) FROM {{table}} WHERE onlinetime>".(time()-900),'users',true);
    $parse['online_users'] = $query[0];
    //$count = doquery(","users",true);
    $parse['users_amount'] = $game_config['users_amount'];
    $parse['fleet_speed'] = $game_config['fleet_speed'];
    $parse['gamefast'] = $game_config['game_speed'];
    $parse['ressperhour'] = $game_config['resource_multiplier'];
    // MISE A JOUR DES PARSES : AJOUTS
    //LE 12/01/2K7
    $parse['resource_multiplier'] = $game_config['resource_multiplier'];
    $parse['metal_basic_income'] = $game_config['metal_basic_income'];
    $parse['crystal_basic_income'] = $game_config['crystal_basic_income'];
    $parse['deuterium_basic_income'] = $game_config['deuterium_basic_income'];
    $parse['stats'] = $game_config['stats'];
    //SECONDE SAUCEE DU MEME JOUR !
    $parse['COOKIE_NAME'] = $game_config['COOKIE_NAME'];
    $parse['max_galaxy'] = $game_config['max_galaxy'];
    $parse['max_system'] = $game_config['max_system'];
    $parse['default_lang'] = $game_config['default_lang'];

    //Utilisation du parse adequat
		
	display(parsetemplate(gettemplate('informations_corps'), $parse), '', false);
    
// Created by Perberos. All rights reversed (C) 2006 (he's juste written this copyrights, and the includes...)
//CREATED AND DESIGNED BY BONO, NO RIGHTS RESERVED
// Enfin si... je dec..
?>