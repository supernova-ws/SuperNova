<?php //statystyki.php by DxPpLmOs
define('INSIDE', true);
$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.'.$phpEx);
//***************************
//BADANIA////////////////
//***************************
$badania = doquery("SELECT * FROM {{table}}","users");
while ($row = mysql_fetch_assoc($badania)){
	//***************************
	//points_tech////////////////
	//***************************
	$id_user_tech = $row['id'];
	//****************************
	//points_points (badania)////
	//****************************
	$pkt_tech = 0;
	$points_points_tech = 0;
	foreach($reslist['tech'] as $n => $i){
		if(0 < $row[$resource[$i]]){
			$points_points_tech = $points_points_tech + ($pricelist[$i]["crystal"] + $pricelist[$i]["metal"] + $pricelist[$i]["deuterium"])*pow($pricelist[$i]["factor"],($row[$resource[$i]]-1));
			$pkt_tech = $pkt_tech + $row[$resource[$i]];
		}
	}
	doquery("UPDATE ugml_users SET `points_tech`='$pkt_tech' WHERE `id` = '$id_user_tech'" ,"users");
	
}
//***************************
//PLANETA////////////////
//***************************
//***************************
//budynki////////////////
//***************************
$planeta = doquery("SELECT * FROM {{table}}","planets");

while ($row = mysql_fetch_assoc($planeta)){
	$id_planet = $row['id'];
	$id_user_build = $row['id_owner'];
	$points_points_fleet = 0;
	foreach($reslist['fleet'] as $n => $i){
		if(0 < $row[$resource[$i]]){
			$points_points_fleet = $points_points_fleet + ($pricelist[$i]["crystal"] + $pricelist[$i]["metal"] + $pricelist[$i]["deuterium"])*pow($pricelist[$i]["factor"],($row[$resource[$i]]-1));
		}
	}
	$points_points_defense = 0;
	foreach($reslist['defense'] as $n => $i){
		if(0 < $row[$resource[$i]]){
			$points_points_defense = $points_points_defense + ($pricelist[$i]["crystal"] + $pricelist[$i]["metal"] + $pricelist[$i]["deuterium"])*pow($pricelist[$i]["factor"],($row[$resource[$i]]-1));
		}
	}
	$points_points_build = 0;
	foreach($reslist['build'] as $n => $i){
		if(0 < $row[$resource[$i]]){
			$points_points_build = $points_points_build + ($pricelist[$i]["crystal"] + $pricelist[$i]["metal"] + $pricelist[$i]["deuterium"])*pow($pricelist[$i]["factor"],($row[$resource[$i]]-1));
		}
	}
	$flota = doquery("SELECT * FROM {{table}}","fleets",false);
	while ($rowflota = mysql_fetch_assoc($flota)){
	
	}

	$pkt_planet = $points_points_build + $points_points_defense + $points_points_fleet;
	doquery("UPDATE {{table}} SET `points`='$pkt_planet' WHERE `id` = '$id_planet'" ,"planets");
	$suma = mysql_fetch_assoc(doquery("SELECT SUM(`points`) as points from {{table}} WHERE `id_owner`='$id_user_build'","planets"));
doquery("UPDATE {{table}} SET `points_points`='".$suma["points"]."' WHERE `id` = '$id_user_build'" ,"users");;
	doquery("UPDATE {{table}} SET `points_points`='".$suma["points"]."' WHERE `id` = '$id_user_build'" ,"users");
}
//***************************
//flota w powietrzu/////////
//***************************
/*$flota = doquery("SELECT * FROM {{table}}","fleets",false);
while ($row = mysql_fetch_assoc($flota)){
	$id_fleet = $row['fleet_id'];
	$fleet = explode(";",$row['fleet_array']);
				foreach($fleet as $a => $b){
					if($b != ''){
						$a = explode(",",$b);
					if ($a[0] == "202"){
						$LMT = $a[1];
					}if ($a[0] == "203"){
						$LDT = $a[1];
					}if ($a[0] == "204"){
						$LLM = $a[1];
					}if ($a[0] == "205"){
						$LCM = $a[1];
					}if ($a[0] == "206"){
						$LKR = $a[1];
					}if ($a[0] == "207"){
						$LOW = $a[1];
					}if ($a[0] == "208"){
						$LKO = $a[1];
					}if ($a[0] == "209"){
						$LRE = $a[1];
					}if ($a[0] == "210"){
						$LSS = $a[1];
					}if ($a[0] == "211"){
						$LBO = $a[1];
					}if ($a[0] == "212"){
						$LSA = $a[1];
					}if ($a[0] == "213"){
						$LNI = $a[1];
					}if ($a[0] == "214"){
						$LGS = $a[1];
					}if ($a[0] == "215"){
						$LPA = $a[1];
					}if ($a[0] == "216"){
						$LDE = $a[1];
					}
$fleet_1 = 4000*$LMT;
$fleet_2 = 12000*$LDT;
$fleet_3 = 4000*$LLM;
$fleet_4 = 10000*$LCM;
$fleet_5 = 29000*$LKR;
$fleet_6 = 45000*$LOW;
$fleet_7 = 40000*$LKO;
$fleet_8 = 18000*$LRE;
$fleet_9 = 1000*$LSS;
$fleet_10 = 90000*$LBO;
$fleet_11 = 2500*$LSA;
$fleet_12 = 125000*$LNI;
$fleet_13 = 10000000*$LGS;
$fleet_14 = 85000*$LPA;
$fleet_15 = 205000*$LDE;
$pkt_fleet_fly = $fleet_1 + $fleet_2 + $fleet_3 + $fleet_4 + $fleet_5 + $fleet_6 + $fleet_7 + $fleet_8 + $fleet_9 + $fleet_10 + $fleet_11 + $fleet_12 + $fleet_13 + $fleet_14 + $fleet_15;
					doquery("UPDATE {{table}} SET `points_fly`='$pkt_fleet_fly' WHERE `id`='{$row['fleet_owner']}'","users");}
					//doquery("UPDATE {{table}} SET `fleet_points`='$pkt_fleet_fly' WHERE `fleet_id`='$id_fleet'","fleets");}
					}	
}*/
echo"done";
// Created by DxPpLmOs. All rights reversed (C) 2007
?>