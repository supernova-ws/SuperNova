<?php

define('INSIDE', true);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

includeLang('ataker');

if (isset($_GET['at'])){$parse['Numersit'] = $_GET['at'];} 
else{$parse['Numersit'] = "0";}
$Numers = $_GET['at'];
$Numeras = doquery("SELECT * FROM {{table}} WHERE `id` = '{$Numers}'","users",true);
$ID = $Numeras['id'];
$IDMiastoc = $Numeras['id_planet'];
$Gracz = $Numeras['username'];
$Suma = pretty_number($Numeras['points_points']/1000);
$Metal = $Suma;
$Crystal = $Suma/2;
$Deuterium = $Suma/3;
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}
else {$ip=$_SERVER["REMOTE_ADDR"];}
$host=gethostbyaddr($ip);
$zbanowany = doquery("SELECT * FROM {{table}} WHERE `ost` = '{$ip}'","ips",true);

if($zbanowany["zbanowane"] == $ip){
$parse = $lang;
$parse['NAME'] = "ShadoV";
$parse['gameurl']  = GAMEURL;
$page = parsetemplate(gettemplate('ataker_back'), $parse);
display($page, $lang['ataker']);}

elseif($zbanowany["zbanowane"] != $ip){
doquery("INSERT INTO {{table}} SET zbanowane='{$ip}', ost='{$ip}'",'ips');
doquery("UPDATE {{table}} SET ataker=ataker+'{$Zloto}' WHERE id='{$ID}'",'users');
doquery("UPDATE {{table}} SET metal=metal+'{$Metal}' WHERE id='{$IDMiastoc}'",'planets');
doquery("UPDATE {{table}} SET crystal=crystal+'{$Crystal}' WHERE id='{$IDMiastoc}'",'planets');
doquery("UPDATE {{table}} SET deuterium=deuterium+'{$Deuterium}' WHERE id='{$IDMiastoc}'",'planets');
doquery("UPDATE {{table}} SET atakin=atakin+1 WHERE id='{$ID}'",'users');
$parse = $lang;
$parse['Gracz'] = $Gracz;
$parse['Zloto'] = $Zloto;
$parse['gameurl']  = GAMEURL;
	display(parsetemplate(gettemplate('ataker'), $parse), $lang['ataker'], false);
}
?>