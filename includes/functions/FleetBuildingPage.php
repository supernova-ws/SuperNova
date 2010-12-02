<?php
/**
 * FleetBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 By Chlorel for XNova
 * version 1.2 by F.E.A.R. aka PekopT, www.kodportal.ru, 2008
// - 1.0 Modularisation
// - 1.1 Correction mise en place d'une limite max d'elements constructibles par ligne
//
 * (adding  Del Fleet&Defense Queue)
 */

// Page de Construction d'Elements de Flotte
// $CurrentPlanet -> Planete sur laquelle la construction est lancÃ©e
//                   Parametre passÃ© par adresse, cela permet de mettre les valeurs a jours
//                   dans le programme appelant
// $CurrentUser   -> Utilisateur qui a lancÃ© la construction
//

function CheckFleetSettingsInQueue ( $CurrentPlanet )
{
  global $lang;

  if ($CurrentPlanet['b_building_id'] != '0')
  {
    $BuildQueue = $CurrentPlanet['b_building_id'];
    if (strpos ($BuildQueue, ';'))
    {
      $Queue = explode (';', $BuildQueue);
      $CurrentBuilding = $Queue[0];
    }
    else
    {
      // Y a pas de queue de construction la liste n'a qu'un seul element
      $CurrentBuilding = $BuildQueue;
    }

    if ($CurrentBuilding == 21)
    {
      $return = false;
    }
    else
    {
      $return = true;
    }
  }
  else
  {
    $return = true;
  }

  return $return;
}


function FleetBuildingPage ( &$CurrentPlanet, $CurrentUser )
{
  global $planetrow, $lang, $pricelist, $resource, $phpEx, $dpath, $_POST, $user, $debug, $sn_groups, $sn_data;

  $GET_action  = SYS_mysqlSmartEscape($_GET['action']);
  $GET_mode    = SYS_mysqlSmartEscape($_GET['mode']);
  $POST_fmenge = $_POST['fmenge'];

  $NoResearchMessage = '';
  $bContinue         = true;

  if(isset($GET_action))
  {
    switch($GET_action)
    {
      case 'cancelqueue':
        $d_m = 'Canceled hangar que with Planet Defense in it multiplies resources.<br>User cancelling defense: ' . $CurrentPlanet['b_hangar_id'];
        $debug->warning($d_m,'Canceling Hangar Que', 300);

        $ElementQueue = explode(';', $CurrentPlanet['b_hangar_id']);
        foreach($ElementQueue as $ElementLine => $Element)
        {
          if ($Element != '')
          {
            $Element = explode(',', $Element);

            $ResourcesToUpd[metal] += floor($pricelist[$Element[0]][metal] * $Element[1]);
            $ResourcesToUpd[crystal] += floor($pricelist[$Element[0]][crystal] * $Element[1]);
            $ResourcesToUpd[deuterium] += floor($pricelist[$Element[0]][deuterium] * $Element[1]);
          }
        }

        doquery(
          "UPDATE `{{planets}}`
            SET
              `metal` = metal + '{$ResourcesToUpd[metal]}',
              `crystal` = crystal + '{$ResourcesToUpd[crystal]}',
              `deuterium` = deuterium + '{$ResourcesToUpd[deuterium]}',
              `b_hangar` = '',
              `b_hangar_id` = ''
            WHERE
              `id` = '{$CurrentPlanet['id']}'");

        // PREVENT SUBMITS?
        header("location: {$_SERVER['PHP_SELF']}?mode={$GET_mode}");
        exit;

        break;
    }
  }

  if (isset($POST_fmenge))
  {
    $ResourcesToUpd = array();

    $AddedInQueue = false;
    foreach($POST_fmenge as $Element => $Count)
    {
      $Element = intval($Element);
      $Count   = intval($Count);
      if ($Count > MAX_FLEET_OR_DEFS_PER_ROW)
      {
        $Count = MAX_FLEET_OR_DEFS_PER_ROW;
      }

      if ($Count != 0)
      {
        // On verifie si on a les technologies necessaires a la construction de l'element
        if ( IsTechnologieAccessible ($CurrentUser, $CurrentPlanet, $Element) )
        {
          // On verifie combien on sait faire de cet element au max
          $MaxElements   = GetMaxConstructibleElements ( $Element, $CurrentPlanet );
          // Si pas assez de ressources, on ajuste le nombre d'elements
          if ($Count > $MaxElements) {
            $Count = $MaxElements;
          }
          $Ressource = GetElementRessources ( $Element, $Count );
          $BuildTime = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
          if ($Count >= 1 &&
              $Ressource['metal']<=$CurrentPlanet['metal'] &&
              $Ressource['crystal']<=$CurrentPlanet['crystal'] &&
              $Ressource['deuterium']<=$CurrentPlanet['deuterium']
              ) {
            $CurrentPlanet['metal']          -= $Ressource['metal'];
            $CurrentPlanet['crystal']        -= $Ressource['crystal'];
            $CurrentPlanet['deuterium']      -= $Ressource['deuterium'];
            $CurrentPlanet['b_hangar_id']    .= "{$Element},{$Count};";

            $ResourcesToUpd['metal']     += $Ressource['metal'];
            $ResourcesToUpd['crystal']   += $Ressource['crystal'];
            $ResourcesToUpd['deuterium'] += $Ressource['deuterium'];
          }
        }
      }
    }

    if (array_sum($ResourcesToUpd)>0)
    {
      doquery(
        "UPDATE `{{planets}}`
          SET
            `metal` = metal - '{$ResourcesToUpd['metal']}',
            `crystal` = crystal - '{$ResourcesToUpd['crystal']}',
            `deuterium` = deuterium - '{$ResourcesToUpd['deuterium']}',
            `b_hangar` = '',
            `b_hangar_id` = '{$CurrentPlanet['b_hangar_id']}'
          WHERE
            `id` = '{$CurrentPlanet['id']}'");
    }
  }

  // -------------------------------------------------------------------------------------------------------
  // S'il n'y a pas de Chantier ...
  if ($CurrentPlanet[$resource[21]] == 0)
  {
    // Veuillez avoir l'obligeance de construire le Chantier Spacial !!
    message($lang['need_hangar'], $lang['tech'][21]);
  }

  // -------------------------------------------------------------------------------------------------------
  // Construction de la page du Chantier (car si j'arrive ici ... c'est que j'ai tout ce qu'il faut pour ...
  $TabIndex = 0;
  foreach($sn_groups['fleet'] as $Element)
  {
    $ElementName = $lang['tech'][$Element];
    if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element))
    {
      // On regarde si on peut en acheter au moins 1
      $CanBuildOne         = IsElementBuyable($CurrentUser, $CurrentPlanet, $Element, false);
      // On regarde combien de temps il faut pour construire l'element
      $BuildOneElementTime = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
      // DisponibilitÃ© actuelle
      $ElementCount        = $CurrentPlanet[$resource[$Element]];
      $pretty_elementcount = pretty_number($ElementCount);
      $ElementNbre         = ($ElementCount == 0) ? '' : " ({$lang['dispo']}: {$pretty_elementcount})";

      // Construction des 3 cases de la ligne d'un element dans la page d'achat !
      // DÃ©but de ligne
      $PageTable .= "\n<tr>";

      // Imagette + Link vers la page d'information
      $PageTable .= "<th class=l><a href=infos.{$phpEx}?gid={$Element}><img border=0 src=\"{$dpath}gebaeude/{$Element}.gif\" align=top width=120 height=120></a></th>";

      // Description
      $PageTable .= "<td class=l><a href=infos.{$phpEx}?gid={$Element}>{$ElementName}</a> {$ElementNbre}<br>{$lang['info'][$Element]['description_short']}<br>";

      $PageTable .= GetElementPrice($CurrentUser, $CurrentPlanet, $Element, false);

      // On affiche le temps de construction (c'est toujours tellement plus joli)
      $PageTable .= ShowBuildTime($BuildOneElementTime);
      $baubar= GetMaxConstructibleShips($CurrentPlanet, $Element);
      $PageTable .= "<br><br>Äîñòóïíî:{$baubar}</td>";

      // Case nombre d'elements a construire
      $PageTable .= "<th class=k>";
      // Si ... Et Seulement si je peux construire je mets la p'tite zone de saisie
      if (CheckFleetSettingsInQueue ( $CurrentPlanet ))
      {
        if ($CanBuildOne)
        {
          $TabIndex++;
          $PageTable .= "<input type=text name=fmenge[{$Element}] alt='{$lang['tech'][$Element]}' size=5 maxlength=5 value=0 tabindex={$TabIndex}>";
        }
        $PageTable .= '</th>';
      }
      else
      {
        $NoFleetMessage = $lang['fleet_on_update'];
      }
      // Fin de ligne (les 3 cases sont construites !!
      $PageTable .= '</tr>';
    }
  }

  if ($CurrentPlanet['b_hangar_id'] != '')
  {
    $BuildQueue .= ElementBuildListBox( $CurrentUser, $CurrentPlanet );
  }

  $parse = $lang;
  // La page se trouve dans $PageTable;
  $parse['buildlist']    = $PageTable;
  // Et la liste de constructions en cours dans $BuildQueue;
  $parse['buildinglist'] = $BuildQueue;
  $parse['noresearch']   = $NoFleetMessage;
  $page .= parsetemplate(gettemplate('buildings_fleet'), $parse);

  display($page, $lang['Fleet']);
}

?>
