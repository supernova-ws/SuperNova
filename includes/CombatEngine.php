<?php

/**
 * CombatEngine.php
 *
 * @version 1
 * @copyright 2008 By Chlorel
 */


// Tech : A : 11  B : 11  C : 13

// Points de structure  	4.000
// Puissance du bouclier 	10
// Valeur d'attaque	 	50

// A : 105
// B :  21
// C : 920

// 204 => array('metal'=>3000   ,'crystal'=>1000   ,'deuterium'=>0      ,'energy'=>0,'factor'=>1,'consumption'=>20  ,'speed'=>12500    ,'capacity'=>50
//             ,'shield'=>10   ,'attack'=>50    ,'sd'=>array(202=>2,203=>1,204=>1,205=>1,206=>1,207=>1,208=>1,209=>1,210=>5,211=>1,212=>5,213=>1,214=>1,215=>1,401=>1,402=>1,403=>1,404=>1,405=>1,406=>1,407=>1,408=>1)),

// Attaque  : 'Valeur Attaque' + (( 'Valeur Attaque' * 'Technologie Armes' ) / 10 )
// Bouclier : arrondi ('Puissance du bouclier' + (( 'Puissance du bouclier' * 'Technologie Bouclier' ) / 10 ))
// Coque    : Points de structure + (( Points de structure * 'Technologie Protection des vaisseaux spatiaux' ) / 10 )

/*
202 => 'Petit transporteur',
203 => 'Grand transporteur',
204 => 'Chasseur l&eacute;ger',
205 => 'Chasseur lourd',
206 => 'Croiseur',
207 => 'Vaisseau de bataille',
208 => 'Vaisseau de colonisation',
209 => 'Recycleur',
210 => 'Sonde espionnage',
211 => 'Bombardier',
212 => 'Satellite solaire',
213 => 'Destructeur',
214 => 'Etoile de la mort',
215 => 'Traqueur',

401 => 'Lanceur de missiles',
402 => 'Artillerie laser l&eacute;g&egrave;re',
403 => 'Artillerie laser lourde',
404 => 'Canon de Gauss',
405 => 'Artillerie &agrave; ions',
406 => 'Lanceur de plasma',
407 => 'Petit bouclier',
408 => 'Grand bouclier',

*/

$CombatCaps = array(
202 => array ( 'shield' =>    10, 'attack' =>      5, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
203 => array ( 'shield' =>    25, 'attack' =>      5, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
204 => array ( 'shield' =>    10, 'attack' =>     50, 'sd' => array (202 =>   2, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
205 => array ( 'shield' =>    25, 'attack' =>    150, 'sd' => array (202 =>   3, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
206 => array ( 'shield' =>    50, 'attack' =>    400, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   6, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>  10, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
207 => array ( 'shield' =>   200, 'attack' =>   1000, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   8, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
208 => array ( 'shield' =>   100, 'attack' =>     50, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
209 => array ( 'shield' =>    10, 'attack' =>      1, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
210 => array ( 'shield' =>     0, 'attack' =>      0, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    0, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0 )),
211 => array ( 'shield' =>   500, 'attack' =>   1000, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>  20, 402 =>  20, 403 =>  10, 404 =>   1, 405 =>  10, 406 =>   1, 407 =>   1, 408 =>   1 )),
212 => array ( 'shield' =>    10, 'attack' =>      1, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    1, 211 =>   1, 212 =>    0, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
213 => array ( 'shield' =>   500, 'attack' =>   2000, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   2, 401 =>   1, 402 =>  10, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
214 => array ( 'shield' => 50000, 'attack' => 200000, 'sd' => array (202 => 250, 203 => 250, 204 => 200, 205 => 100, 206 =>  33, 207 =>  30, 208 => 250, 209 => 250, 210 => 1250, 211 =>  25, 212 => 1250, 213 =>   5, 214 =>   1, 215 =>  15, 401 => 200, 402 => 200, 403 => 100, 404 =>  50, 405 => 100, 406 =>   1, 407 =>   1, 408 =>   1 )),
215 => array ( 'shield' =>   400, 'attack' =>    700, 'sd' => array (202 =>   3, 203 =>   3, 204 =>   1, 205 =>   4, 206 =>   4, 207 =>   7, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1, 401 =>   1, 402 =>   1, 403 =>   1, 404 =>   1, 405 =>   1, 406 =>   1, 407 =>   1, 408 =>   1 )),
216 => array ( 'shield' => 1000000, 'attack' => 1000000, 'sd' => array (202 => 250, 203 => 250, 204 => 200, 205 => 100, 206 =>  33, 207 =>  30, 208 => 250, 209 => 250, 210 => 1250, 211 =>  25, 212 => 1250, 213 =>   5, 214 =>   1, 215 =>  15, 401 => 200, 402 => 200, 403 => 100, 404 =>  50, 405 => 100, 406 =>   1, 407 =>   1, 408 =>   1 )),

401 => array ( 'shield' =>    20, 'attack' =>     80, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
402 => array ( 'shield' =>    25, 'attack' =>    100, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
403 => array ( 'shield' =>   100, 'attack' =>    250, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
404 => array ( 'shield' =>   200, 'attack' =>   1100, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
405 => array ( 'shield' =>   500, 'attack' =>    150, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
406 => array ( 'shield' =>   300, 'attack' =>   3000, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
407 => array ( 'shield' =>  2000, 'attack' =>      1, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
408 => array ( 'shield' =>  2000, 'attack' =>      1, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),
409 => array ( 'shield' =>  1000000, 'attack' =>      1000000, 'sd' => array (202 =>   1, 203 =>   1, 204 =>   1, 205 =>   1, 206 =>   1, 207 =>   1, 208 =>   1, 209 =>   1, 210 =>    5, 211 =>   1, 212 =>    5, 213 =>   1, 214 =>   1, 215 =>   1) ),


502 => array ( 'shield' =>     1, 'attack' =>      1 ),
503 => array ( 'shield' =>     1, 'attack' =>  12000 )
);

$AtFleet = array ( 203 => 2 );
$AtTechn = array ( 109 => 3, 110 => 5, 111 => 3 );
$AtCount = 1;

$DfFleet = array ( 402 => 9 );
$DfTechn = array ( 109 => 7, 110 => 7, 111 => 5 );
$DfCount = 1;


// Données en entrée :
// $AttackFleet -> array de tableaux des flottes attaquantes
// $AttackTech  -> array de tableaux des technologies des attaquants ainsi que leur id
// $AttackCount -> Nbre d'attaquants  ( de 1 a 5 )
// $TargetFleet -> array de tableaux des flottes defenseurs
// $TargetTech  -> array de tableaux des technologies des defenseurs ainsi que leur id
// $TargetCount -> Nbre de defenseurs ( de 1 a 5 )
//
function FleetCombat ( $AttackFleet, $AttackTech, $AttackCount, $TargetFleet, $TargetTech, $TargetCount ) {

}

function GetWeaponsPerType ( $TypeArray, $Tech ) {
	global $capacity;
	// Calcul de la force d'Attaque
	if (!is_null($TypeArray)) {
		foreach($TypeArray as $Type => $Count) {
			$Attack[$Type]      = round ($capacity[$Type]['attack'] + (($capacity[$Type]['attack'] * $Tech['109']) / 10));
			$Units['attack']   += $Count * $Attack[$Type];
		}
	}

}

function GetShiedsPerType ( $TypeArray, $Tech ) {
	global $capacity;
	// Calcul des points de Bouclier
	if (!is_null($TypeArray)) {
		foreach($TypeArray as $Type => $Count) {
			$Shield[$Type]      = round ($capacity[$Type]['shield'] + (($capacity[$Type]['shield'] * $Tech['110']) / 10));
			$Units['shield']   += $Count * $Shield[$Type];
		}
	}

}

function GetHullPerType ( $TypeArray, $Tech ) {
	global $pricelist;
	// Calcul des points de Coque
	if (!is_null($TypeArray)) {
		$Units['metal']     = 0;
		$Units['crystal']   = 0;
		$Units['deuterium'] = 0;
		foreach($TypeArray as $Type => $Count) {
			$Hull[$Type]         = ($pricelist[$Type]['metal'] + $pricelist[$Type]['crystal']) + ((($pricelist[$Type]['metal'] + $pricelist[$Type]['crystal']) * $Tech['111']) / 10);
			$Units['metal']     += $Count * $pricelist[$Type]['metal'];
			$Units['crystal']   += $Count * $pricelist[$Type]['crystal'];
			$Units['deuterium'] += $Count * $pricelist[$Type]['deuterium'];
		}
	}

}
?>