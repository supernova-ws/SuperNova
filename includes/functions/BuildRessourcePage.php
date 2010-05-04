<?php

/**
 * BuildRessourcePage.php
 *
 * @version 2.0s - Security checked for SQL-injection by Gorlum for http://supernova.ws
 * @version 2.0 - copyright 2010 almost fully rewrote and optimized by Gorlum for http://supernova.ws
 * @copyright 2008 by ShadoV for XNova
 */

function INT_recalcStorageBar($Caps, &$parse, $strResource){
  global $lang, $game_config;

  $parse[$strResource.'_basic_income'] = $game_config[$strResource.'_basic_income'] * $game_config['resource_multiplier'];
  if ($Caps['planet'][$strResource.'_max'] < $Caps['planet'][$strResource]) {
    $parse[$strResource.'_max'] = "<font color=\"#ff0000\">";
  } else {
    $parse[$strResource.'_max'] = "<font color=\"#00ff00\">";
  }
  $parse[$strResource.'_max']        .= pretty_number($Caps['planet'][$strResource.'_max'] / 1000) ." ". $lang['k']."</font>";
  $totalProduction                    = floor( $Caps['planet'][$strResource.'_perhour'] * $Caps['production'] + $Caps[$strResource.'_perhour'][0]);
  $parse[$strResource.'_total']       = colorNumber(pretty_number($totalProduction));
  $parse['daily_'.$strResource]               = colorNumber(pretty_number($totalProduction * 24));
  $parse['weekly_'.$strResource]              = colorNumber(pretty_number($totalProduction * 24 * 7));
  $parse['monthly_'.$strResource]             = colorNumber(pretty_number($totalProduction * 24 * 30));
  $parse[$strResource.'_storage']     = floor($Caps['planet'][$strResource] / $Caps['planet'][$strResource.'_max'] * 100) . $lang['o/o'];
  $parse[$strResource.'_storage_bar'] = floor($Caps['planet'][$strResource] / $Caps['planet'][$strResource.'_max'] * 100 * 2.5);

  if ($parse[$strResource . '_storage_bar'] > (100 * 2.5)) {
    $parse[$strResource . '_storage_bar'] = 250;
    $parse[$strResource . '_storage_barcolor'] = '#C00000';
  } elseif ($parse[$strResource . '_storage_bar'] > (80 * 2.5)) {
    $parse[$strResource . '_storage_barcolor'] = '#C0C000';
  } else {
    $parse[$strResource . '_storage_barcolor'] = '#00C000';
  }
};

function BuildRessourcePage ( $CurrentUser, $CurrentPlanet ) {
  global $lang, $ProdGrid, $resource, $reslist, $game_config, $_POST;

  CheckPlanetUsedFields ( $CurrentPlanet );

  $RessBodyTPL = gettemplate('resources');
  $RessRowTPL  = gettemplate('resources_row');

  $ValidList['percent'] = array (  0,  10,  20,  30,  40,  50,  60,  70,  80,  90, 100 );
  $SubQry               = "";
  if ($_POST) {
    foreach($_POST as $Field => $Value) {
      $FieldName = SYS_mysqlSmartEscape($Field)."_porcent";
      if ( isset( $CurrentPlanet[ $FieldName ] ) ) {
        if ( ! in_array( $Value, $ValidList['percent']) ) {
          header("Location: overview.php");
          exit;
        }

        $Values                       = $Value / 10;
        $CurrentPlanet[ $FieldName ]  = $Values;
        $SubQry                      .= ", `".$FieldName."` = '".$Values."'";
      }
    }
  }

  $parse  = $lang;

  // -------------------------------------------------------------------------------------------------------
  $parse['resource_row']               = "";
  $BuildTemp                           = $CurrentPlanet[ 'temp_max' ];

  $Caps = ECO_getPlanetCaps($CurrentUser, $CurrentPlanet);
  foreach($reslist['prod'] as $ProdID) {
    if ($CurrentPlanet[$resource[$ProdID]] > 0 && isset($ProdGrid[$ProdID])) {
      $Field                 = $resource[$ProdID] ."_porcent";
      $CurrRow               = array();
      $CurrRow['name']       = $resource[$ProdID];
      $CurrRow['porcent']    = $CurrentPlanet[$Field];
      $CurrRow['type']       = $lang['tech'][$ProdID];
      $CurrRow['level']      = ($ProdID > 200) ? $lang['quantity'] : $lang['level'];
      $CurrRow['level_type'] = $CurrentPlanet[ $resource[$ProdID] ];

      for ($Option = 10; $Option >= 0; $Option--) {
        $OptValue = $Option * 10;
        if ($Option == $CurrRow['porcent']) {
          $OptSelected    = " selected=selected";
        } else {
          $OptSelected    = "";
        }
        $CurrRow['option'] .= "<option value=\"".$OptValue."\"".$OptSelected.">".$OptValue."%</option>";
      }

      $CurrRow['energy_type']    = colorNumber(pretty_number($Caps['energy'][$ProdID]));
      $CurrRow['metal_type']     = colorNumber(pretty_number($Caps['metal_perhour'][$ProdID]     * $Caps['production']));
      $CurrRow['crystal_type']   = colorNumber(pretty_number($Caps['crystal_perhour'][$ProdID]   * $Caps['production']));
      $CurrRow['deuterium_type'] = colorNumber(pretty_number($Caps['deuterium_perhour'][$ProdID] * $Caps['production']));

      $parse['resource_row'] .= parsetemplate($RessRowTPL, $CurrRow);
    }
  }

  $parse['Production_of_resources_in_the_planet'] =
  str_replace('%s', $CurrentPlanet['name'], $lang['Production_of_resources_in_the_planet']);

  INT_recalcStorageBar($Caps, $parse, 'metal');
  INT_recalcStorageBar($Caps, $parse, 'crystal');
  INT_recalcStorageBar($Caps, $parse, 'deuterium');

  $parse['energy_basic_income']    = $game_config['energy_basic_income']    * $game_config['resource_multiplier'];
  $parse['energy_total']           = colorNumber( pretty_number( $Caps['planet']['energy_max'] - $Caps['planet']['energy_used'] ));

  $parse['production_level_bar'] = floor(250 * $Caps['production']);
  $parse['production_level']     = floor($Caps['production'] * 100);
  $parse['production_level_barcolor'] = '#00ff00';

  $QryUpdatePlanet  = "UPDATE {{table}} SET ";
  $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."' ";
  $QryUpdatePlanet .= $SubQry;
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."';";
  doquery( $QryUpdatePlanet, 'planets');
  if ($game_config['OverviewClickBanner'] != '') {
    $parse['ClickBanner'] = stripslashes( $game_config['OverviewClickBanner'] );
  }
  $page = parsetemplate( $RessBodyTPL, $parse );

  display($page, '');
}

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 Mise en module initiale (creation)
// 2.0 Almost fully rewrote and optimized by Gorlum for http://ogame.triolan.com.ua

?>