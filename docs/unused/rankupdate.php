<? 
# wget --spider .../punkty.php 

define('INSIDE', true); 
$ugamela_root_path = './'; 
include($ugamela_root_path . 'extension.inc'); 
include($ugamela_root_path . 'common.'.$phpEx); 

if ($IsUserChecked == false) {
	includeLang('login');
	header("Location: login.php");
}

$tabela = "game_users"; // Tabela users z ugamelli 
$tabela_ally = "game_alliance"; // Tabela aliances z ugamelli 
$tabela_config = "game_config"; // Tabela config z ugamelli 

$punktyquery=doquery("SELECT * FROM {{table}} ",'game_users'); 
while($row = mysql_fetch_array($punktyquery)){ 
#PKT OGOLNIE 
$pkt_budynki = $row['points_builds']; 
$pkt_flota = $row['points_fleet_old']; 
$pkt_obrona = $row['points_builds2']; 
$pkt_badania = $row['points_tech_old']; 
$pkt_ogolne = $pkt_budynki + $pkt_flota + $pkt_obrona + $pkt_badania; 
#PKT KATEGORIE 
$pkt_fleet = $row['points_fleet2']; 
$pkt_tech = $row['points_tech2']; 
$id = $row['id']; 


doquery("UPDATE {{table}} SET points_points = {$pkt_ogolne} WHERE id = {$id}",'users'); 
doquery("UPDATE {{table}} SET points_fleet = {$pkt_fleet} WHERE id = {$id}",'users'); 
doquery("UPDATE {{table}} SET points_tech = {$pkt_tech} WHERE id = {$id}",'users'); 
} 
#PKT Sojusze 

$result_ally=doquery("SELECT * FROM {{table}} ",'alliance'); 
while($row_ally = mysql_fetch_array($result_ally)){ 
$pkt_ally_fleet = $row_ally['ally_points_fleet2']; 
$pkt_ally_fleet_old = $row_ally['ally_points_fleet_old']; 
$pkt_ally_tech = $row_ally['ally_points_tech_old']; 
$pkt_ally_build = $row_ally['ally_points_builds_old']; 
$id_ally = $row_ally['id']; 

$pkt_ally_ogolnie = $pkt_ally_build + $pkt_ally_fleet_old + $pkt_ally_tech; 

doquery("UPDATE {{table}} SET ally_points = {$pkt_ally_ogolnie} WHERE id = {$id_ally}",'alliance'); 
doquery("UPDATE {{table}} SET ally_points_fleet = {$pkt_ally_fleet} WHERE id = {$id_ally}",'alliance'); 
doquery("UPDATE {{table}} SET ally_points_tech = {$pkt_ally_tech} WHERE id = {$id_ally}",'alliance'); 

} 
$data = date("D M j G:i:s T Y");  

doquery("UPDATE {{table}} SET config_value = '{$data}' WHERE config_name = 'stats'",'config'); 

$koniec = $game_config['users_amount'];        
$start = 1; 
$query = doquery('SELECT * FROM {{table}} ORDER BY points_points DESC LIMIT 1,'."$koniec".'','users'); 

    while ($row = mysql_fetch_assoc($query)){ 
       $playername_rank =  $row['username']; 
       $rank_old = $row['rank']; 
       $start++; 
       $query_rank = doquery("UPDATE {{table}} SET `rank_old`='{$rank_old}' WHERE `username` = '{$playername_rank}'" ,"users");       
       $query_rank = doquery("UPDATE {{table}} SET `rank`='{$start}' WHERE `username` = '{$playername_rank}'" ,"users"); 
             
             
       } 

echo"The user rankings were updated at this time: $data"; 
?>