<?php

/**
 * DefensesBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
  * version 1.2 by F.E.A.R. aka PekopT, www.kodportal.ru, 2008
 * (adding  Del Fleet&Defense Queue)
 */

// Page de Construction d'Elements de Defense
// $CurrentPlanet -> Planete sur laquelle la construction est lancée
//                   Parametre passé par adresse, cela permet de mettre les valeurs a jours
//                   dans le programme appelant
// $User   -> Utilisateur qui a lancé la construction
//

function CheckDefSettingsInQueue ( $CurrentPlanet ) {
  global $lang;

    if ($CurrentPlanet['b_building_id'] != "0") {
    $BuildQueue = $CurrentPlanet['b_building_id'];
    if (strpos ($BuildQueue, ";")) {
      $Queue = explode (";", $BuildQueue);
      $CurrentBuilding = $Queue[0];
    } else {
      // Y a pas de queue de construction la liste n'a qu'un seul element
      $CurrentBuilding = $BuildQueue;
    }

    if ($CurrentBuilding == 21) {
      $return = false;
    } else {
      $return = true;
    }

  } else {
    $return = true;
  }

  return $return;
}


function DefensesBuildingPage ( &$CurrentPlanet, $User ) {
  global $CurrentPlanetrow, $lang, $pricelist, $resource, $phpEx, $dpath, $_POST, $debug, $_GET;

  $GET_action  = SYS_mysqlSmartEscape($_GET['action']);
  $GET_mode    = SYS_mysqlSmartEscape($_GET['mode']);
  $POST_fmenge = $_POST['fmenge'];

  if(isset($GET_action)){
    switch($GET_action){
      case "cancelqueue":

  $d_m = 'Canceled hangar que with Planet Defense in it multiplies resources.<br>User cancelling defense: ' . $CurrentPlanet['b_hangar_id'];
  $debug->warning($d_m,'Canceling Hangar Que', 300);

          $ElementQueue = explode(';', $CurrentPlanet['b_hangar_id']);
          foreach($ElementQueue as $ElementLine => $Element) {
            if ($Element != '') {
              $Element = explode(',', $Element);
              $ResourcesToUpd[metal] += floor($pricelist[$Element[0]][metal] * $Element[1]);
              $ResourcesToUpd[crystal] += floor($pricelist[$Element[0]][crystal] * $Element[1]);
              $ResourcesToUpd[deuterium] += floor($pricelist[$Element[0]][deuterium] * $Element[1]);
            }
          }

          $SetRes = "UPDATE `{{table}}` SET ";
          $SetRes .= "`metal` = metal + '" . $ResourcesToUpd[metal] . "', ";
          $SetRes .= "`crystal` = crystal + '" . $ResourcesToUpd[crystal] . "', ";
          $SetRes .= "`deuterium` = deuterium + '" . $ResourcesToUpd[deuterium] . "', ";
          $SetRes .= "`b_hangar` = '', ";
          $SetRes .= "`b_hangar_id` = ''";
          $SetRes .= " WHERE `id` = '" . $CurrentPlanet['id'] . "'";
          doquery($SetRes, 'planets');



          // PREVENT SUBMITS?
          header("location: " . $_SERVER['PHP_SELF'] . "?mode=" . $GET_mode);
          exit;

        break;
    }
  }

  // Getting numbers of construction restricted by number (missiles & shields)
  // counting those one the planet and those on the current que
  $built = GetRestrictedConstructionNum($CurrentPlanet);

  if (isset($POST_fmenge)) {
    $ResourcesToUpd = array();

    $BuildArray = explode (";", $CurrentPlanet['b_hangar_id']);
    $SiloSpace = max(0, $CurrentPlanet[ $resource[44] ] * 10 - $built[502] - $built[503] * 2);
    foreach($POST_fmenge as $Element => $Count) {
      $Element = intval($Element);
      $Count   = intval($Count);
      if ($Count > MAX_FLEET_OR_DEFS_PER_ROW) {
        $Count = MAX_FLEET_OR_DEFS_PER_ROW;
      }

  if ($Element==409) {
    $d_m = 'Canceled hangar que with Planet Defense in it multiplies resources.<br>User building Planet Defense: ' . dump($POST_fmenge);
    $debug->warning($d_m,'Building Planet Defense', 300);
  }

      if ($Count AND $Element) {
        // On verifie si on a les technologies necessaires a la construction de l'element
        if ( IsTechnologieAccessible ($User, $CurrentPlanet, $Element) ) {
          // On verifie combien on sait faire de cet element au max
          $MaxElements = GetMaxConstructibleElements ( $Element, $CurrentPlanet );

          switch ($Element) {
            case 502:
              $Count = min($SiloSpace, $Count, $MaxElements);
              break;
            case 503:
              $Count = min(floor($SiloSpace/2), $Count, $MaxElements);
              break;
            case 407:
            case 408:
            case 409:
              $Count = $built[$Element] >=1 ? 0 : 1;
              break;
            default:
              $Count = min($Count, $MaxElements);
              break;
          };

          $Ressource = GetElementRessources ( $Element, $Count );

          if ($Count >= 1 &&
              $Ressource['metal']<=$CurrentPlanet['metal'] &&
              $Ressource['crystal']<=$CurrentPlanet['crystal'] &&
              $Ressource['deuterium']<=$CurrentPlanet['deuterium']
              ) {
            $built[$Element] += $Count;
            $BuildTime = GetBuildingTime($User, $CurrentPlanet, $Element);

            $CurrentPlanet['metal']           -= $Ressource['metal'];
            $CurrentPlanet['crystal']         -= $Ressource['crystal'];
            $CurrentPlanet['deuterium']       -= $Ressource['deuterium'];
            $CurrentPlanet['b_hangar_id']     .= "". $Element .",". $Count .";";

            $ResourcesToUpd['metal']     += $Ressource['metal'];
            $ResourcesToUpd['crystal']   += $Ressource['crystal'];
            $ResourcesToUpd['deuterium'] += $Ressource['deuterium'];
          }
        }
      }
    }

    if (array_sum($ResourcesToUpd)>0){
      $SetRes = "UPDATE `{{table}}` SET ";
      $SetRes .= "`metal` = metal - '" . $ResourcesToUpd['metal'] . "', ";
      $SetRes .= "`crystal` = crystal - '" . $ResourcesToUpd['crystal'] . "', ";
      $SetRes .= "`deuterium` = deuterium - '" . $ResourcesToUpd['deuterium'] . "', ";
      $SetRes .= "`b_hangar` = '', ";
      $SetRes .= "`b_hangar_id` = '". $CurrentPlanet['b_hangar_id'] ."'";
      $SetRes .= " WHERE `id` = '" . $CurrentPlanet['id'] . "'";
      doquery($SetRes, 'planets');
    }
  }

  $SiloSpace = max(0, $CurrentPlanet[ $resource[44] ] * 10 - $built[502] - $built[503] * 2);
  // -------------------------------------------------------------------------------------------------------
  // S'il n'y a pas de Chantier ...
  if ($CurrentPlanet[$resource[21]] == 0) {
    // Veuillez avoir l'obligeance de construire le Chantier Spacial !!
    message($lang['need_hangar'], $lang['tech'][21]);
  }

  // -------------------------------------------------------------------------------------------------------
  // Construction de la page du Chantier (car si j'arrive ici ... c'est que j'ai tout ce qu'il faut pour ...
  $TabIndex  = 0;
  $PageTable = "";
  foreach($lang['tech'] as $Element => $ElementName) {
    if ($Element > 400 && $Element <= 599) {
      if (IsTechnologieAccessible($User, $CurrentPlanet, $Element)) {
        // Disponible à la construction

        // On regarde si on peut en acheter au moins 1
        $CanBuildOne         = IsElementBuyable($User, $CurrentPlanet, $Element, false);
        // On regarde combien de temps il faut pour construire l'element
        $BuildOneElementTime = GetBuildingTime($User, $CurrentPlanet, $Element);
        // Disponibilité actuelle
        $ElementCount        = $CurrentPlanet[$resource[$Element]];
        $ElementNbre         = ($ElementCount == 0) ? "" : " (".$lang['dispo'].": " . pretty_number($ElementCount) . ")";

        // Construction des 3 cases de la ligne d'un element dans la page d'achat !
        // Début de ligne
        $PageTable .= "\n<tr>";

        // Imagette + Link vers la page d'info
        $PageTable .= "<th class=l>";
        $PageTable .= "<a href=infos.".$phpEx."?gid=".$Element.">";
        $PageTable .= "<img border=0 src=\"".$dpath."gebaeude/".$Element.".gif\" align=top width=120 height=120></a>";
        $PageTable .= "</th>";

        // Description
        $PageTable .= "<td class=l>";
        $PageTable .= "<a href=infos.".$phpEx."?gid=".$Element.">".$ElementName."</a> ".$ElementNbre."<br>";
        $PageTable .= "".$lang['res']['descriptions'][$Element]."<br>";
        // On affiche le 'prix' avec eventuellement ce qui manque en ressource
        $PageTable .= GetElementPrice($User, $CurrentPlanet, $Element, false);

        // On affiche le temps de construction (c'est toujours tellement plus joli)
        $PageTable .= ShowBuildTime($BuildOneElementTime);
        //$baubar= GetMaxConstructibleShips($CurrentPlanet, $Element);

        $baubar= GetMaxConstructibleElements ( $Element, $CurrentPlanet );

        switch ($Element) {
          case 502:
            $baubar = min($SiloSpace, $baubar);
            $restrict = 1;
            break;
          case 503:
            $baubar = min(floor($SiloSpace/2), $baubar);
            $restrict = 1;
            break;
          case 407:
          case 408:
          case 409:
            $baubar = $built[$Element] >=1 ? 0 : 1;
            $restrict = 2;
            break;
          default:
            $restrict = 0;
            break;
        }

        $PageTable .= "<br><br>".$lang['can_build']."<font color=lime><strong>" . pretty_number ($baubar) . "</strong></font>";

        // Case nombre d'elements a construire
        $PageTable .= "<th class=k>";
        // Si ... Et Seulement si je peux construire je mets la p'tite zone de saisie
        if ($CanBuildOne) {
          if (CheckDefSettingsInQueue ( $CurrentPlanet )) {
            if ($restrict == 2 AND $baubar == 0) {
              $PageTable .= "<font color=\"red\">".$lang['only_one']."</font>";
            } elseif ($restrict == 1 AND !$baubar) {
              $PageTable .= "<font color=\"red\">".$lang['b_no_silo_space']."</font>";
            } else {
              $TabIndex++;
              $PageTable .= "<input type=text name=fmenge[".$Element."] alt='".$lang['tech'][$Element]."' size=5 maxlength=5 value=0 tabindex=".$TabIndex.">";
            }
          }else {
            $NoFleetMessage = $lang['fleet_on_update'];
          }
        }
        $PageTable .= "</th>";

        // Fin de ligne (les 3 cases sont construites !!
        $PageTable .= "</tr>";
      }
    }
  }

  if ($CurrentPlanet['b_hangar_id'] != '') {
    $BuildQueue = ElementBuildListBox( $User, $CurrentPlanet );
  }

  $parse = $lang;
  // La page se trouve dans $PageTable;
  $parse['buildlist']    = $PageTable;
  // Et la liste de constructions en cours dans $BuildQueue;
  $parse['buildinglist'] = $BuildQueue;
  $parse['noresearch']  = $NoFleetMessage;
  // fragmento de template
  $page .= parsetemplate(gettemplate('buildings_defense'), $parse);

  display($page, $lang['Defense']);

}
// Version History
// - 1.0 Modularisation
// - 1.1 Correction mise en place d'une limite max d'elements constructibles par ligne
// - 1.2 Correction limitation bouclier meme si en queue de fabrication
//
?>
