<?php

/**
 * SetNextQueueElementOnTop.php
 *
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

function SetNextQueueElementOnTop ( &$CurrentPlanet, $CurrentUser ) {
	global $lang, $resource;

	// Garde fou ... Si le temps de construction n'est pas 0 on ne fait rien !!!
	if ($CurrentPlanet['b_building'] == 0) {
		$CurrentQueue  = $CurrentPlanet['b_building_id'];
		if ($CurrentQueue != 0) {
			$QueueArray = explode ( ";", $CurrentQueue );
			$Loop       = true;
			while ($Loop == true) {
				$ListIDArray         = explode ( ",", $QueueArray[0] );
				$Element             = $ListIDArray[0];
				$Level               = $ListIDArray[1];
				$BuildTime           = $ListIDArray[2];
				$BuildEndTime        = $ListIDArray[3];
				$BuildMode           = $ListIDArray[4]; // pour savoir si on construit ou detruit
				$HaveNoMoreLevel     = false;

				if ($BuildMode == 'destroy') {
					$ForDestroy = true;
				} else {
					$ForDestroy = false;
				}
				$HaveRessources = IsElementBuyable ($CurrentUser, $CurrentPlanet, $Element, true, $ForDestroy);
				if ($ForDestroy) {
					if ($CurrentPlanet[$resource[$Element]] == 0) {
						$HaveRessources  = false;
						$HaveNoMoreLevel = true;
					}
				}
				if ( $HaveRessources == true ) {
					$Needed                        = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, $ForDestroy);
					$CurrentPlanet['metal']       -= $Needed['metal'];
					$CurrentPlanet['crystal']     -= $Needed['crystal'];
					$CurrentPlanet['deuterium']   -= $Needed['deuterium'];
					$CurrentTime                   = time();
					$BuildEndTime                  = $BuildEndTime;
					$NewQueue                      = implode ( ";", $QueueArray );
					if ($NewQueue == "") {
						$NewQueue                      = '0';
					}
					$Loop                          = false;
				} else {
					$ElementName = $lang['tech'][$Element];
					if ($HaveNoMoreLevel == true) {
						$Message     = sprintf ($lang['sys_nomore_level'], $ElementName );
					} else {
						$Needed      = GetBuildingPrice ($CurrentUser, $CurrentPlanet, $Element, true, $ForDestroy);
						$Message     = sprintf ($lang['sys_notenough_money'], $ElementName,
												pretty_number ($CurrentPlanet['metal']), $lang['Metal'],
												pretty_number ($CurrentPlanet['crystal']), $lang['Crystal'],
												pretty_number ($CurrentPlanet['deuterium']), $lang['Deuterium'],
												pretty_number ($Needed['metal']), $lang['Metal'],
												pretty_number ($Needed['crystal']), $lang['Crystal'],
												pretty_number ($Needed['deuterium']), $lang['Deuterium']);
					}

					SendSimpleMessage ( $CurrentUser['id'], '', '', 99, $lang['sys_buildlist'], $lang['sys_buildlist_fail'], $Message);

					array_shift( $QueueArray );
					$ActualCount         = count ( $QueueArray );
					if ( $ActualCount == 0 ) {
							$BuildEndTime  = '0';
							$NewQueue      = '0';
							$Loop          = false;
					}else{
						  $NewQueue = array();
						  foreach ($QueueArray as $key => $ListQueue){
							 $ListIDArray = explode ( ",", $ListQueue );
							 $ListIDArray[3] -= $BuildTime; //$BuildEndTime
							 $NewQueue[] = implode(",",$ListIDArray);               
						  }
						  unset($QueueArray);
						  $QueueArray = $NewQueue;
					}
				}
			} // while
		} else {
			$BuildEndTime  = '0';
			$NewQueue      = '0';
		}

		// Ecriture de la mise a jour dans la BDD
		$CurrentPlanet['b_building']    = $BuildEndTime;
		$CurrentPlanet['b_building_id'] = $NewQueue;

		$QryUpdatePlanet  = "UPDATE {{table}} SET ";
		$QryUpdatePlanet .= "`metal` = '".         $CurrentPlanet['metal']         ."' , ";
		$QryUpdatePlanet .= "`crystal` = '".       $CurrentPlanet['crystal']       ."' , ";
		$QryUpdatePlanet .= "`deuterium` = '".     $CurrentPlanet['deuterium']     ."' , ";
		$QryUpdatePlanet .= "`b_building` = '".    $CurrentPlanet['b_building']    ."' , ";
		$QryUpdatePlanet .= "`b_building_id` = '". $CurrentPlanet['b_building_id'] ."' ";
		$QryUpdatePlanet .= "WHERE ";
		$QryUpdatePlanet .= "`id` = '" .           $CurrentPlanet['id']            . "';";
		doquery( $QryUpdatePlanet, 'planets');

	}

	return;
}
?>