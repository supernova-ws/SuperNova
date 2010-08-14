<?php

/**
 * BuildRessourcePage.php
 *
 * 2.1 - copyright 2010 by Gorlum for http://supernova.ws
 *   [~] - Security checked for SQL-injection
 * 2.0 - copyright 2010 by Gorlum for http://supernova.ws
 *   [+] - almost fully rewrote and optimized
 * 1.0 copyright 2008 by ShadoV for XNova
 *   [+] - Mise en module initiale (creation)
 *
**/

function INT_recalcStorageBar($caps, &$parse, $resource_name){
  global $lang, $config;

  $resource_income_name = $resource_name.'_basic_income';
  $resource_max_name    = $resource_name.'_max';

  $parse[$resource_income_name] = $config->$resource_income_name * $config->resource_multiplier;
  if ($caps['planet'][$resource_max_name] < $caps['planet'][$resource_name]) {
    $parse[$resource_max_name] = "<font color=\"#ff0000\">";
  } else {
    $parse[$resource_max_name] = "<font color=\"#00ff00\">";
  }
  $parse[$resource_max_name]           .= pretty_number($caps['planet'][$resource_max_name] / 1000) ." ". $lang['k']."</font>";
  $totalProduction                      = floor( $caps['planet'][$resource_name.'_perhour'] * $caps['production'] + $caps[$resource_name.'_perhour'][0]);
  $parse[$resource_name.'_total']       = colorNumber(pretty_number($totalProduction));
  $parse['daily_'.$resource_name]       = colorNumber(pretty_number($totalProduction * 24));
  $parse['weekly_'.$resource_name]      = colorNumber(pretty_number($totalProduction * 24 * 7));
  $parse['monthly_'.$resource_name]     = colorNumber(pretty_number($totalProduction * 24 * 30));
  $parse[$resource_name.'_storage']     = floor($caps['planet'][$resource_name] / $caps['planet'][$resource_max_name] * 100) . $lang['o/o'];
  $parse[$resource_name.'_storage_bar'] = floor($caps['planet'][$resource_name] / $caps['planet'][$resource_max_name] * 100 * 2.5);

  if ($parse[$resource_name . '_storage_bar'] > (100 * 2.5)) {
    $parse[$resource_name . '_storage_bar']      = 250;
    $parse[$resource_name . '_storage_barcolor'] = '#C00000';
  } elseif ($parse[$resource_name . '_storage_bar'] > (80 * 2.5)) {
    $parse[$resource_name . '_storage_barcolor'] = '#C0C000';
  } else {
    $parse[$resource_name . '_storage_barcolor'] = '#00C000';
  }
};

function BuildRessourcePage ( $CurrentUser, $CurrentPlanet ) {
  global $lang, $ProdGrid, $resource, $reslist, $config, $_POST;

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

  $caps = ECO_getPlanetCaps($CurrentUser, $CurrentPlanet);
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

      $CurrRow['energy_type']    = colorNumber(pretty_number($caps['energy'][$ProdID]));
      $CurrRow['metal_type']     = colorNumber(pretty_number($caps['metal_perhour'][$ProdID]     * $caps['production']));
      $CurrRow['crystal_type']   = colorNumber(pretty_number($caps['crystal_perhour'][$ProdID]   * $caps['production']));
      $CurrRow['deuterium_type'] = colorNumber(pretty_number($caps['deuterium_perhour'][$ProdID] * $caps['production']));

      $parse['resource_row'] .= parsetemplate($RessRowTPL, $CurrRow);
    }
  }

  $parse['Production_of_resources_in_the_planet'] =
  str_replace('%s', $CurrentPlanet['name'], $lang['Production_of_resources_in_the_planet']);

  INT_recalcStorageBar($caps, $parse, 'metal');
  INT_recalcStorageBar($caps, $parse, 'crystal');
  INT_recalcStorageBar($caps, $parse, 'deuterium');

  $parse['energy_basic_income']    = $config->energy_basic_income * $config->resource_multiplier;
  $parse['energy_total']           = colorNumber( pretty_number( $caps['planet']['energy_max'] - $caps['planet']['energy_used'] ));

  $parse['production_level_bar'] = floor(250 * $caps['production']);
  $parse['production_level']     = floor($caps['production'] * 100);
  $parse['production_level_barcolor'] = '#00ff00';

  $QryUpdatePlanet  = "UPDATE {{table}} SET ";
  $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."' ";
  $QryUpdatePlanet .= $SubQry;
  $QryUpdatePlanet .= "WHERE ";
  $QryUpdatePlanet .= "`id` = '". $CurrentPlanet['id'] ."';";
  doquery( $QryUpdatePlanet, 'planets');
  $page = parsetemplate( $RessBodyTPL, $parse );

  display($page, '');
}
?>