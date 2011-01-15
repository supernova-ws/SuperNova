<?php

/**
 * ResearchBuildingPage.php
 *
 * @version 1.2s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 1.2
 * @copyright 2008 by Chlorel for XNova
 */

/**
 * GetRestPrice.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

// Calcul du surplus de ressources disponible apres l'achat d'un Element (Batiment / Recherche / Defense / Vaisseau )
// $user       -> Le Joueur lui meme
// $planet     -> La planete sur laquelle l'Element doit etre construit
// $Element    -> L'Element que l'on convoite
// $userfactor -> true  pour un batiment ou une recherche
// -> false pour une defense ou un vaisseau
//
// Reponse : Chaine de caractère mise en forme prete a etre affichée
function GetRestPrice ($user, $planet, $Element, $userfactor = true) {
  global $pricelist, $resource, $lang, $sn_data;

  if ($userfactor) {
    $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
  }

  $array = array(
    RES_METAL        => $lang["sys_metal"],
    RES_CRYSTAL        => $lang["sys_crystal"],
    RES_DEUTERIUM        => $lang["sys_deuterium"],
    'energy_max' => $lang["sys_energy"]
    );

  $fleet_list = flt_get_fleets_to_planet($planet);
  $fleet_own_count = $fleet_list['own']['count'];

  $text  = "<br><font color=\"#7f7f7f\">{$lang['Rest_ress']}: ";
  $text1 = "";
  foreach ($array as $res_id => $ResTitle) {
    if(is_numeric($res_id))
    {
      $ResType = $sn_data[$res_id]['name'];
    }
    else
    {
      $ResType = $res_id;
    }

    if ($pricelist[$Element][$ResType] != 0) {
      $text .= "{$ResTitle}: ";
      if ($userfactor) {
        $cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
      } else {
        $cost = floor($pricelist[$Element][$ResType]);
      }

      $resource_left = $planet[$ResType] - $cost;
      if ($resource_left < 0) {
        $color_rgb = '127, 95, 96';
      } else {
        $color_rgb = '95, 127, 108';
      }
      $resource_left = pretty_number($resource_left);
      $text .= "<b style=\"color: rgb({$color_rgb});\">{$resource_left}</b> ";

      if($fleet_own_count)
      {
        $resource_left = $planet[$ResType] + $fleet_list['own']['total'][$res_id] - $cost;
        if ($resource_left < 0) {
          $color_rgb = '127, 95, 96';
        } else {
          $color_rgb = '95, 127, 108';
        }
        $resource_left = pretty_number($resource_left);
        $text1 .= "{$ResTitle}: <b style=\"color: rgb({$color_rgb});\">{$resource_left}</b>, ";
      }
    }
  }
  $text .= '</font>';

  if($fleet_own_count)
  {
    $text1 = substr($text1, 0, -2);
    $text .= "<br><font color=\"#7f7f7f\">{$lang['Rest_ress_fleet']}: {$text1}</font>";
  }

  return $text;
}

// Page de Construction de niveau de Recherche
// $CurrentPlanet -> Planete sur laquelle la construction est lancée
//                   Parametre passé par adresse, cela permet de mettre les valeurs a jours
//                   dans le programme appelant
// $CurrentUser   -> Utilisateur qui a lancé la construction
// $InResearch    -> Indicateur qu'il y a une Recherche en cours
// $ThePlanet     -> Planete sur laquelle se realise la technologie eventuellement
function ResearchBuildingPage (&$CurrentPlanet, $CurrentUser, $InResearch, $ThePlanet, $que) {
  global $lang, $resource, $reslist, $phpEx, $dpath, $_GET, $config;

  $TheCommand = SYS_mysqlSmartEscape($_GET['cmd']);
  $Techno     = intval($_GET['tech']);

  $NoResearchMessage = "";
  $bContinue         = true;
  // Deja est qu'il y a un laboratoire sur la planete ???
  if ($CurrentPlanet[$resource[31]] == 0) {
    message($lang['no_laboratory'], $lang['Research']);
  }
  // Ensuite ... Est ce que la labo est en cours d'upgrade ?
  if (eco_lab_is_building($config, $que)) {
    $NoResearchMessage = $lang['labo_on_update'];
    $bContinue         = false;
  }

  // Boucle d'interpretation des eventuelles commandes
  if (isset($TheCommand)) {
    if (!$Techno) {
      $debug->error("Buguser: ".$user['username']." (".$user['id'].")<br />Free research","Bug use");
      die();
    };


    if ( is_numeric($Techno) ) {
      if ( in_array($Techno, $reslist['tech']) ) {
        // Bon quand on arrive ici ... On sait deja qu'on a une technologie valide

        if ( is_array ($ThePlanet) ) {
          $WorkingPlanet = $ThePlanet;
        } else {
          $WorkingPlanet = $CurrentPlanet;
        }
        switch($TheCommand){
          case 'cancel':
            if ($ThePlanet['b_tech_id'] == $Techno) {
              $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
              $WorkingPlanet['metal']      += $costs['metal'];
              $WorkingPlanet['crystal']    += $costs['crystal'];
              $WorkingPlanet['deuterium']  += $costs['deuterium'];
              $WorkingPlanet['b_tech_id']   = 0;
              $WorkingPlanet["b_tech"]      = 0;
              $CurrentUser['b_tech_planet'] = 0;
              $UpdateData                   = true;
              $InResearch                   = false;
              $QryUpdatePlanet2  = "UPDATE {{table}} SET ";
              $QryUpdatePlanet2 .= "`b_tech_id` = '".$WorkingPlanet['b_tech_id']."', ";
              $QryUpdatePlanet2 .= "`b_tech` = '".$WorkingPlanet['b_tech']."', ";
              $QryUpdatePlanet2 .= "`metal` = '".$WorkingPlanet['metal']."', ";
              $QryUpdatePlanet2 .= "`crystal` = '".$WorkingPlanet['crystal']."', ";
              $QryUpdatePlanet2 .= "`deuterium` = '".$WorkingPlanet['deuterium']."' ";
              $QryUpdatePlanet2 .= "WHERE ";
              $QryUpdatePlanet2 .= "`id` = '".$WorkingPlanet['id']."';";
              doquery( $QryUpdatePlanet2, 'planets');
            }
            break;
          case 'search':
            if ( !($InResearch || eco_unit_busy($CurrentUser, $CurrentPlanet, false, $Techno)) )
            {
              if ( eco_can_build_unit($CurrentUser, $WorkingPlanet, $Techno) && IsElementBuyable($CurrentUser, $WorkingPlanet, $Techno) ) {
                $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
                $WorkingPlanet['metal']      -= $costs['metal'];
                $WorkingPlanet['crystal']    -= $costs['crystal'];
                $WorkingPlanet['deuterium']  -= $costs['deuterium'];
                $WorkingPlanet["b_tech_id"]   = $Techno;
                $WorkingPlanet["b_tech"]      = time() + GetBuildingTime($CurrentUser, $WorkingPlanet, $Techno);
                $CurrentUser["b_tech_planet"] = $WorkingPlanet["id"];
                $UpdateData                   = true;
                $InResearch                   = true;

                $QryUpdatePlanet3  = "UPDATE {{table}} SET ";
                $QryUpdatePlanet3 .= "`b_tech_id` = '".$WorkingPlanet['b_tech_id']."', ";
                $QryUpdatePlanet3 .= "`b_tech` = '".$WorkingPlanet['b_tech']."', ";
                $QryUpdatePlanet3 .= "`metal` = '".$WorkingPlanet['metal']."', ";
                $QryUpdatePlanet3 .= "`crystal` = '".$WorkingPlanet['crystal']."', ";
                $QryUpdatePlanet3 .= "`deuterium` = '".$WorkingPlanet['deuterium']."' ";
                $QryUpdatePlanet3 .= "WHERE ";
                $QryUpdatePlanet3 .= "`id` = '".$WorkingPlanet['id']."';";
                doquery( $QryUpdatePlanet3, 'planets');
              }
            }else{
              $NoResearchMessage = $lang['build_research_in_progress'];
            };
            break;
        }
        if ($UpdateData == true) {
          $QryUpdateUser  = "UPDATE {{table}} SET ";
          $QryUpdateUser .= "`b_tech_planet` = '".$CurrentUser['b_tech_planet']."' ";
          $QryUpdateUser .= "WHERE ";
          $QryUpdateUser .= "`id` = '".$CurrentUser['id']."';";
          doquery( $QryUpdateUser, 'users');
        }
        //byo; FIXed by PekopT 05.08.2008 thread   http://forum.ragezone.com/showthread.php?p=3734880#post3734880
        $CurrentPlanet = $WorkingPlanet;
        if ( is_array ($ThePlanet) ) {
          $ThePlanet     = $WorkingPlanet;
        } else {
          $CurrentPlanet = $WorkingPlanet;
          if ($TheCommand == 'search') {
            $ThePlanet = $CurrentPlanet;
          }
        }
      }
    } else {
      $bContinue = false;
    }
  }

  $TechRowTPL = gettemplate('buildings_research_row');
  $TechScrTPL = gettemplate('buildings_research_script');

  foreach($lang['tech'] as $Tech => $TechName) {
    if ($Tech > 105 && $Tech <= 199) {
      if ( eco_can_build_unit($CurrentUser, $CurrentPlanet, $Tech)) {
        $RowParse                = $lang;
        $RowParse['dpath']       = $dpath;
        $RowParse['tech_id']     = $Tech;
        $building_level          = $CurrentUser[$resource[$Tech]];
        $RowParse['tech_level']  = ($building_level == 0) ? "" : "( ". $lang['level']. " ".$building_level." )";
        $RowParse['tech_name']   = $TechName;
        $RowParse['tech_descr']  = $lang['info'][$Tech]['description_short'];
        $RowParse['tech_price']  = GetElementPrice($CurrentUser, $CurrentPlanet, $Tech);
        $SearchTime              = GetBuildingTime($CurrentUser, $CurrentPlanet, $Tech);
        $RowParse['search_time'] = ShowBuildTime($SearchTime);
        $RowParse['tech_restp']  = $lang['Rest_ress'] ." ". GetRestPrice ($CurrentUser, $CurrentPlanet, $Tech, true);
        $CanBeDone               = IsElementBuyable($CurrentUser, $CurrentPlanet, $Tech);

        // Arbre de decision de ce que l'on met dans la derniere case de la ligne
        if (!$InResearch) {
          $LevelToDo = 1 + $CurrentUser[$resource[$Tech]];
          if ($CanBeDone) {
            if (eco_lab_is_building ( $config, $que )) {
              // Le laboratoire est cours de construction ou d'evolution
              // Et dans la config du systeme, on ne permet pas la recherche pendant
              // que le labo est en construction ou evolution !
              if ($LevelToDo == 1) {
                $TechnoLink  = "<font color=#FF0000>". $lang['Rechercher'] ."</font>";
              } else {
                $TechnoLink  = "<font color=#FF0000>". $lang['Rechercher'] ."<br>".$lang['level']." ".$LevelToDo."</font>";
              }
            } else {
              $TechnoLink  = "<a href=\"buildings.php?mode=research&cmd=search&tech=".$Tech."\">";
              if ($LevelToDo == 1) {
                $TechnoLink .= "<font color=#00FF00>". $lang['Rechercher'] ."</font>";
              } else {
                $TechnoLink .= "<font color=#00FF00>". $lang['Rechercher'] ."<br>".$lang['level']." ".$LevelToDo."</font>";
              }
              $TechnoLink  .= "</a>";
            }
          } else {
            if ($LevelToDo == 1) {
              $TechnoLink  = "<font color=#FF0000>". $lang['Rechercher'] ."</font>";
            } else {
              $TechnoLink  = "<font color=#FF0000>". $lang['Rechercher'] ."<br>".$lang['level']." ".$LevelToDo."</font>";
            }
          }

        } else {
          // Y a une construction en cours
          if ($ThePlanet["b_tech_id"] == $Tech) {
            // C'est le technologie en cours de recherche
            $bloc       = $lang;
            if ($ThePlanet['id'] != $CurrentPlanet['id']) {
              // Ca se passe sur une autre planete
              $bloc['tech_time']  = $ThePlanet["b_tech"] - time();
              $bloc['tech_name']  = $lang['on'] ."<br>". $ThePlanet["name"];
              $bloc['tech_home']  = $ThePlanet["id"];
              $bloc['tech_id']    = $ThePlanet["b_tech_id"];
            } else {
              // Ca se passe sur la planete actuelle
              $bloc['tech_time']  = $CurrentPlanet["b_tech"] - time();
              $bloc['tech_name']  = "";
              $bloc['tech_home']  = $CurrentPlanet["id"];
              $bloc['tech_id']    = $CurrentPlanet["b_tech_id"];
            }
            $TechnoLink  = parsetemplate($TechScrTPL, $bloc);
          } else {
            // Technologie pas en cours recherche
            $TechnoLink  = "<center>-</center>";
          }
        }
        $RowParse['tech_link']  = $TechnoLink;
        $TechnoList            .= parsetemplate($TechRowTPL, $RowParse);
      }
    }
  }

  $PageParse                = $lang;
  $PageParse['noresearch']  = $NoResearchMessage;
  $PageParse['technolist']  = $TechnoList;
  $Page                    .= parsetemplate(gettemplate('buildings_research'), $PageParse);

  display( $Page, $lang['Research'] );
}

// History revision
// 1.0 - Release initiale / modularisation / Reecriture / Commentaire / Mise en forme
// 1.1 - BUG affichage de la techno en cours
// 1.2 - Restructuration modification pour permettre d'annuller proprement une techno en cours
?>