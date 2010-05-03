<?php

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);
check_urlaubmodus ($user);if ($IsUserChecked == false) {  includeLang('login'); header("Location: login.php");}

function RinokPage ( $CurrentUser, &$CurrentPlanet ) {
  global $lang, $pricelist, $planetrow, $phpEx, $resource, $reslist, $_GET, $_POST, $dpath;
includeLang('schrotti');
$rinok_flot = RINOK_FLOT;
if ($CurrentUser['rpg_points'] >= $rinok_flot) {

if (array_key_exists('shiptypeid', $_POST)) {
  $res_id = $_POST['shiptypeid'];
} else {
  $res_id = 202;
}

if (array_key_exists('number_ships_sell', $_POST)) {
  $number_ships_sell = $_POST['number_ships_sell'];
} else {
  $number_ships_sell = 0;
}

// Herstellungskosten des Schifftyps ermitteln
$price_met = $pricelist[$res_id]['metal'];  // Metal
$price_crys = $pricelist[$res_id]['crystal'];  // Crystal
$price_deut = $pricelist[$res_id]['deuterium'];  // Deuterium
$price_energy = $pricelist[$res_id]['energy'];  // Energy

// Rückgewinnungsfaktoren
$schrotti_rate_met = 0.75;
$schrotti_rate_crys = 0.5;
$schrotti_rate_deut = 0.25;
$schrotti_rate_energy = 0.50;

// Rückgewinnungswerte pro Schiff
$schrotti_met = $price_met * $schrotti_rate_met;
$schrotti_crys = $price_crys * $schrotti_rate_crys;
$schrotti_deut = $price_deut * $schrotti_rate_deut;
$schrotti_energy = $price_energy * $schrotti_rate_energy;

if($_POST){

  if($number_ships_sell > 0 && $planetrow[$resource[$res_id]]!=0){

    if($number_ships_sell > $planetrow[$resource[$res_id]]){
      $number_ships_sell = $planetrow[$resource[$res_id]];
    }
    $rinok_flot = RINOK_FLOT;
          $CurrentUser['rpg_points']         -= $rinok_flot;

          $QryUpdateUser  = "UPDATE {{table}} SET ";
          $QryUpdateUser .= "`rpg_points` = '". $CurrentUser['rpg_points'] ."' ";
          $QryUpdateUser .= "WHERE ";
          $QryUpdateUser .= "`id` = '". $CurrentUser['id'] ."';";
          doquery( $QryUpdateUser, 'users' );


      $CP['metal']      = $number_ships_sell * $schrotti_met;
      $CP['crystal']    = $number_ships_sell * $schrotti_crys;
      $CP['deuterium']  = $number_ships_sell * $schrotti_deut;
      $CP[$resource[$res_id]] = $number_ships_sell;


      $QryUpdatePlanet  = "UPDATE {{table}} SET ";
      $QryUpdatePlanet .= "`metal` = `metal` + '" . $CP['metal']     ."', ";
      $QryUpdatePlanet .= "`crystal` = '".   $CP['crystal']   ."', ";
      $QryUpdatePlanet .= "`deuterium` = '". $CP['deuterium'] ."', ";
      $QryUpdatePlanet .= "`{$resource[$res_id]}` = `{$resource[$res_id]}` - '".   $CP[$resource[$res_id]]   ."' ";
      $QryUpdatePlanet .= "WHERE ";
      $QryUpdatePlanet .= "`id` = '".        $CurrentPlanet['id']        ."';";
      doquery ( $QryUpdatePlanet , 'planets');

  }
}

$parse = $lang;

$parse['shiplist'] = '';
foreach ($reslist['fleet'] as $value) {
  $parse['shiplist'] .= "\n<option ";
  if ($res_id == $value) {
    $parse['shiplist'] .= "selected=\"selected\" ";
  }
  $parse['shiplist'] .= "value=\"".$value."\">";
  $parse['shiplist'] .= $lang['tech'][$value];
  $parse['shiplist'] .= "</option>";
}

$parse['image'] = $res_id;
$parse['dpath'] = $dpath;
$parse['schrotti_met'] = $schrotti_met;
$parse['schrotti_crys'] = $schrotti_crys;
$parse['schrotti_deut'] = $schrotti_deut;
$parse['schrotti_energy'] = $schrotti_energy;
$parse['shiptype_id'] = $res_id;
$parse['max_ships_to_sell'] = $planetrow[$resource[$res_id]];
$parse['Merchant_give_Aluminium'] = str_replace('%met',gettemplate('schrotti_met'),$lang['Merchant_give_Aluminium']);
$parse['Merchant_give_Silicium'] = str_replace('%crys',gettemplate('schrotti_crys'),$lang['Merchant_give_Silicium']);
$parse['Merchant_give_Deuterium'] = str_replace('%deut',gettemplate('schrotti_deut'),$lang['Merchant_give_Deuterium']);
$parse['Merchant_give_Energy'] = str_replace('%energy',gettemplate('schrotti_energy'),$lang['Merchant_give_Energy']);


$page = parsetemplate(gettemplate('market_scraper'), $parse);
return $page;
}
}

$page = RinokPage ( $user, $planetrow);
display($page, $lang['Intergalactic_merchant']);


$planetrow = doquery ("SELECT * FROM {{table}} WHERE `id` = '". $planetrow['id'] ."';", 'planets', true);

?>