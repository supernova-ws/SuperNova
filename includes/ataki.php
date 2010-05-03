<?php
/*
Some rights reserved
Code by jacekowski (jacekowski@wklej.org)
licensed under CC BY-NC-SA http://creativecommons.org/licenses/by-nc-sa/2.5/ http://creativecommons.org/licenses/by-nc-sa/2.5/pl/
all violations of BY, NC and SA rule will be punished
*/
function walka ($CurrentSet, $TargetSet, $CurrentTechno, $TargetTechno) {
	global $pricelist, $CombatCaps, $game_config;
	$runda       = array();
	$atakujacy_n = array();
	$wrog_n      = array();

	// Calcul des points de Structure de l'attaquant
	if (!is_null($CurrentSet)) {
		$atakujacy_zlom_poczatek['metal']   = 0;
		$atakujacy_zlom_poczatek['crystal'] = 0;
		foreach($CurrentSet as $a => $b) {
			$atakujacy_zlom_poczatek['metal']   = $atakujacy_zlom_poczatek['metal']   + $CurrentSet[$a]['count'] * $pricelist[$a]['metal'];
			$atakujacy_zlom_poczatek['crystal'] = $atakujacy_zlom_poczatek['crystal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['crystal'];
		}
	}

	// Calcul des points de Structure du défenseur
	$wrog_zlom_poczatek['metal']    = 0;
	$wrog_zlom_poczatek['crystal'] = 0;
	$wrog_poczatek = $TargetSet;
	if (!is_null($TargetSet)) {
		foreach($TargetSet as $a => $b) {
			if ($a < 300) {
				$wrog_zlom_poczatek['metal']   = $wrog_zlom_poczatek['metal']   + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
				$wrog_zlom_poczatek['crystal'] = $wrog_zlom_poczatek['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
			} else {
				$wrog_zlom_poczatek_obrona['metal']   = $wrog_zlom_poczatek_obrona['metal']   + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
				$wrog_zlom_poczatek_obrona['crystal'] = $wrog_zlom_poczatek_obrona['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
			}
		}
	}

	for ($i = 1; $i <= 7; $i++) {
		$atakujacy_atak   = 0;
		$wrog_atak        = 0;
		$atakujacy_obrona = 0;
		$wrog_obrona      = 0;
		$atakujacy_ilosc  = 0;
		$wrog_ilosc       = 0;
		$wrog_tarcza      = 0;
		$atakujacy_tarcza = 0;

		if (!is_null($CurrentSet)) {
			foreach($CurrentSet as $a => $b) {
				$CurrentSet[$a]["obrona"] = $CurrentSet[$a]['count'] * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10 * (1 + (0.1 * ($CurrentTechno["defence_tech"]) + (0.05 * $user['rpg_amiral'])));
				$rand = rand(80, 120) / 100;
				$CurrentSet[$a]["tarcza"] = $CurrentSet[$a]['count'] * $CombatCaps[$a]['shield'] * (1 + (0.1 * $CurrentTechno["shield_tech"]) + (0.05 * $user['rpg_amiral'])) * $rand;
				$atak_statku = $CombatCaps[$a]['attack'];
				$technologie = (1 + (0.1 * $CurrentTechno["military_tech"]+(0.05 * $user['rpg_amiral'])));
				$rand = rand(80, 120) / 100;
				$ilosc = $CurrentSet[$a]['count'];
				$CurrentSet[$a]["atak"] = $ilosc * $atak_statku * $technologie * $rand;
				$atakujacy_atak = $atakujacy_atak + $CurrentSet[$a]["atak"];
				$atakujacy_obrona = $atakujacy_obrona + $CurrentSet[$a]["obrona"];
				$atakujacy_ilosc = $atakujacy_ilosc + $CurrentSet[$a]['count'];
			}
		} else {
			$atakujacy_ilosc = 0;
			break;
		}

		if (!is_null($TargetSet)) {
			foreach($TargetSet as $a => $b) {
				$TargetSet[$a]["obrona"] = $TargetSet[$a]['count'] * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10 * (1 + (0.1 * ($TargetTechno["defence_tech"]) + (0.05 * $user['rpg_amiral'])));
				$rand = rand(80, 120) / 100;
				$TargetSet[$a]["tarcza"] = $TargetSet[$a]['count'] * $CombatCaps[$a]['shield'] * (1 + (0.1 * $TargetTechno["shield_tech"])+ (0.05 * $user['rpg_amiral'])) * $rand;
				$atak_statku = $CombatCaps[$a]['attack'];
				$technologie = (1 + (0.1 * $TargetTechno["military_tech"]) + (0.05 * $user['rpg_amiral']));
				$rand = rand(80, 120) / 100;
				$ilosc = $TargetSet[$a]['count'];
				$TargetSet[$a]["atak"] = $ilosc * $atak_statku * $technologie * $rand;
				$wrog_atak = $wrog_atak + $TargetSet[$a]["atak"];
				$wrog_obrona = $wrog_obrona + $TargetSet[$a]["obrona"];
				$wrog_ilosc = $wrog_ilosc + $TargetSet[$a]['count'];
			}
		} else {
			$wrog_ilosc = 0;
			$runda[$i]["atakujacy"] = $CurrentSet;
			$runda[$i]["wrog"] = $TargetSet;
			$runda[$i]["atakujacy"]["atak"] = $atakujacy_atak;
			$runda[$i]["wrog"]["atak"] = $wrog_atak;
			$runda[$i]["atakujacy"]['count'] = $atakujacy_ilosc;
			$runda[$i]["wrog"]['count'] = $wrog_ilosc;
			break;
		}

		$runda[$i]["atakujacy"] = $CurrentSet;
		$runda[$i]["wrog"] = $TargetSet;
		$runda[$i]["atakujacy"]["atak"] = $atakujacy_atak;
		$runda[$i]["wrog"]["atak"] = $wrog_atak;
		$runda[$i]["atakujacy"]['count'] = $atakujacy_ilosc;
		$runda[$i]["wrog"]['count'] = $wrog_ilosc;

		if (($atakujacy_ilosc == 0) or ($wrog_ilosc == 0)) {
			break;
		}
		foreach($CurrentSet as $a => $b) {
			if ($atakujacy_ilosc > 0) {
				$wrog_moc = $CurrentSet[$a]['count'] * $wrog_atak / $atakujacy_ilosc;
				if ($CurrentSet[$a]["tarcza"] < $wrog_moc) {
					$max_zdjac = floor($CurrentSet[$a]['count'] * $wrog_ilosc / $atakujacy_ilosc);
					$wrog_moc = $wrog_moc - $CurrentSet[$a]["tarcza"];
					$atakujacy_tarcza = $atakujacy_tarcza + $CurrentSet[$a]["tarcza"];
					$ile_zdjac = floor(($wrog_moc / (($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10)));
					if ($ile_zdjac > $max_zdjac) {
						$ile_zdjac = $max_zdjac;
					}
					$atakujacy_n[$a]['count'] = ceil($CurrentSet[$a]['count'] - $ile_zdjac);
					if ($atakujacy_n[$a]['count'] <= 0) {
						$atakujacy_n[$a]['count'] = 0;
					}
				} else {
					$atakujacy_n[$a]['count'] = $CurrentSet[$a]['count'];
					$atakujacy_tarcza = $atakujacy_tarcza + $wrog_moc;
				}
			} else {
				$atakujacy_n[$a]['count'] = $CurrentSet[$a]['count'];
				$atakujacy_tarcza = $atakujacy_tarcza + $wrog_moc;
			}
		}

		foreach($TargetSet as $a => $b) {
			if ($wrog_ilosc > 0) {
				$atakujacy_moc = $TargetSet[$a]['count'] * $atakujacy_atak / $wrog_ilosc;
				if ($TargetSet[$a]["tarcza"] < $atakujacy_moc) {
					$max_zdjac = floor($TargetSet[$a]['count'] * $atakujacy_ilosc / $wrog_ilosc);
					$atakujacy_moc = $atakujacy_moc - $TargetSet[$a]["tarcza"];
					$wrog_tarcza = $wrog_tarcza + $TargetSet[$a]["tarcza"];
					$ile_zdjac = floor(($atakujacy_moc / (($pricelist[$a]['metal'] + $pricelist[$a]['crystal']) / 10)));
					if ($ile_zdjac > $max_zdjac) {
						$ile_zdjac = $max_zdjac;
					}
					$wrog_n[$a]['count'] = ceil($TargetSet[$a]['count'] - $ile_zdjac);
					if ($wrog_n[$a]['count'] <= 0) {
						$wrog_n[$a]['count'] = 0;
					}
				} else {
					$wrog_n[$a]['count'] = $TargetSet[$a]['count'];
					$wrog_tarcza = $wrog_tarcza + $atakujacy_moc;
				}
			} else {
				$wrog_n[$a]['count'] = $TargetSet[$a]['count'];
				$wrog_tarcza = $wrog_tarcza + $atakujacy_moc;
			}
		}

		foreach($CurrentSet as $a => $b) {
			foreach ($CombatCaps[$a]['sd'] as $c => $d) {
				if (isset($TargetSet[$c])) {
					$wrog_n[$c]['count'] = $wrog_n[$c]['count'] - floor($d * rand(50, 100) / 100);
					if ($wrog_n[$c]['count'] <= 0) {
						$wrog_n[$c]['count'] = 0;
					}
				}
			}
		}

		foreach($TargetSet as $a => $b) {
			foreach ($CombatCaps[$a]['sd'] as $c => $d) {
				if (isset($CurrentSet[$c])) {
					$atakujacy_n[$c]['count'] = $atakujacy_n[$c]['count'] - floor($d * rand(50, 100) / 100);
					if ($atakujacy_n[$c]['count'] <= 0) {
						$atakujacy_n[$c]['count'] = 0;
					}
				}
			}
		}

		$runda[$i]["atakujacy"]["tarcza"] = $atakujacy_tarcza;
		$runda[$i]["wrog"]["tarcza"] = $wrog_tarcza;
		// print_r($runda[$i]);
		$TargetSet = $wrog_n;
		$CurrentSet = $atakujacy_n;
	}

	if (($atakujacy_ilosc == 0) or ($wrog_ilosc == 0)) {
		if (($atakujacy_ilosc == 0) and ($wrog_ilosc == 0)) {
			$wygrana = "r";
		} else {
			if ($atakujacy_ilosc == 0) {
				$wygrana = "w";
			} else {
				$wygrana = "a";
			}
		}
	} else {
		$i = sizeof($runda);
		$runda[$i]["atakujacy"] = $CurrentSet;
		$runda[$i]["wrog"] = $TargetSet;
		$runda[$i]["atakujacy"]["atak"] = $atakujacy_atak;
		$runda[$i]["wrog"]["atak"] = $wrog_atak;
		$runda[$i]["atakujacy"]['count'] = $atakujacy_ilosc;
		$runda[$i]["wrog"]['count'] = $wrog_ilosc;
		$wygrana = "r";
	}
	$atakujacy_zlom_koniec['metal'] = 0;
	$atakujacy_zlom_koniec['crystal'] = 0;
	if (!is_null($CurrentSet)) {
		foreach($CurrentSet as $a => $b) {
			$atakujacy_zlom_koniec['metal'] = $atakujacy_zlom_koniec['metal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['metal'];
			$atakujacy_zlom_koniec['crystal'] = $atakujacy_zlom_koniec['crystal'] + $CurrentSet[$a]['count'] * $pricelist[$a]['crystal'];
		}
	}
	$wrog_zlom_koniec['metal'] = 0;
	$wrog_zlom_koniec['crystal'] = 0;
	if (!is_null($TargetSet)) {
		foreach($TargetSet as $a => $b) {
			if ($a < 300) {
				$wrog_zlom_koniec['metal'] = $wrog_zlom_koniec['metal'] + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
				$wrog_zlom_koniec['crystal'] = $wrog_zlom_koniec['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
			} else {
				$wrog_zlom_koniec_obrona['metal'] = $wrog_zlom_koniec_obrona['metal'] + $TargetSet[$a]['count'] * $pricelist[$a]['metal'];
				$wrog_zlom_koniec_obrona['crystal'] = $wrog_zlom_koniec_obrona['crystal'] + $TargetSet[$a]['count'] * $pricelist[$a]['crystal'];
			}
		}
	}
	$ilosc_wrog = 0;
	$straty_obrona_wrog = 0;
	if (!is_null($TargetSet)) {
		foreach($TargetSet as $a => $b) {
			if ($a > 300) {
				$straty_obrona_wrog = $straty_obrona_wrog + (($wrog_poczatek[$a]['count'] - $TargetSet[$a]['count']) * ($pricelist[$a]['metal'] + $pricelist[$a]['crystal']));
				$TargetSet[$a]['count'] = $TargetSet[$a]['count'] + (($wrog_poczatek[$a]['count'] - $TargetSet[$a]['count']) * rand(60, 80) / 100);
				$ilosc_wrog = $ilosc_wrog + $TargetSet[$a]['count'];
			}
		}
	}
	if (($ilosc_wrog > 0) and ($atakujacy_ilosc == 0)) {
		$wygrana = "w";
	}

	$zlom['metal']    = ((($atakujacy_zlom_poczatek['metal']   - $atakujacy_zlom_koniec['metal'])   + ($wrog_zlom_poczatek['metal']   - $wrog_zlom_koniec['metal']))   * ($game_config['Fleet_Cdr'] / 100));
	$zlom['crystal']  = ((($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']) + ($wrog_zlom_poczatek['crystal'] - $wrog_zlom_koniec['crystal'])) * ($game_config['Fleet_Cdr'] / 100));

	$zlom['metal']   += ((($atakujacy_zlom_poczatek['metal']   - $atakujacy_zlom_koniec['metal'])   + ($wrog_zlom_poczatek['metal']   - $wrog_zlom_koniec['metal']))   * ($game_config['Defs_Cdr'] / 100));
	$zlom['crystal'] += ((($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']) + ($wrog_zlom_poczatek['crystal'] - $wrog_zlom_koniec['crystal'])) * ($game_config['Defs_Cdr'] / 100));

	$zlom["atakujacy"] = (($atakujacy_zlom_poczatek['metal'] - $atakujacy_zlom_koniec['metal']) + ($atakujacy_zlom_poczatek['crystal'] - $atakujacy_zlom_koniec['crystal']));
	$zlom["wrog"]      = (($wrog_zlom_poczatek['metal']      - $wrog_zlom_koniec['metal'])      + ($wrog_zlom_poczatek['crystal']      - $wrog_zlom_koniec['crystal']) + $straty_obrona_wrog);
	return array("atakujacy" => $CurrentSet, "wrog" => $TargetSet, "wygrana" => $wygrana, "dane_do_rw" => $runda, "zlom" => $zlom);
}
?>