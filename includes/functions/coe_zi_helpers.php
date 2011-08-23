<?php
function BE_DEBUG_openTable()
{
  if (!defined('BE_DEBUG') || BE_DEBUG !== true){
    return;
  }

  global $be_debug_array;

  $be_debug_array = array();

  $print_data = '<table border=1>'.'<tr>'.
    '<th>R</th>'.
    '<th>Att</th>'.
    '<th>Dmg</th>'.
    '<th>Spd</th>'.
    '<th>Def</th>'.
    '<th>ShieldTotal</th>'.
    '<th>Harm%</th>'.
    '<th>Harm</th>'.
    '<th>Amplify</th>'.
    '<th>FinalHarm</th>'.
    '<th>Passed</th>'.
    '<th>Def/pcs</th>'.
    '<th>Destroyed</th>'.
    '<th>Left</th>'.
  '</tr>';

//  print($print_data);
  $be_debug_array[] = $print_data;
}

function BE_DEBUG_openRow($round, $defenseShipID, $defenseShipData, $element, $attackArray, $fleetID, $HarmPctIncoming, $HarmMade, $FinalHarm, $amount)
{
  if (!defined('BE_DEBUG') || BE_DEBUG !== true){
    return;
  }

  global $sn_data, $be_debug_array;

  $SN = array(SHIP_CARGO_SMALL => 'МаТр', SHIP_CARGO_BIG => 'БоТр', SHIP_CARGO_SUPER => 'СуТр', SHIP_FIGHTER_LIGHT => 'ЛгИс', SHIP_FIGHTER_HEAVY => 'ТяИс',
    SHIP_DESTROYER => 'Эсми', SHIP_CRUISER => 'Линк', SHIP_COLONIZER => 'Коло', SHIP_RECYCLER => 'Пере', SHIP_SPY => 'Шпио', SHIP_BOMBER => 'Бомб',
    SHIP_SATTELITE_SOLAR => 'СоСп', SHIP_DESTRUCTOR => 'Уник', SHIP_DEATH_STAR => 'ЗвСм', SHIP_BATTLESHIP => 'Лине', SHIP_SUPERNOVA => 'Нова',
    SHIP_FIGHTER_ASSAULT => 'Штур',
    401 => 'Раке', 402 => 'ЛеЛа', 403 => 'ТяЛа', 404 => 'Гаус', 405 => 'Ионн', 406 => 'Плаз', 407 => 'МалЩ', 408 => 'БолЩ', 409 => 'План');

  $print_data = '<tr>'.
    '<td>'.$round.'</td>'.
    '<td>'.$SN[$defenseShipID].'</td>'.
    '<td>'.$defenseShipData['att'].'</td>'.
    '<td>'.$sn_data[$defenseShipID]['sd'][$element].'</td>'.
    '<td>'.$SN[$element].'</td>'.
    '<td>'.$attackArray[$fleetID][$element]['shield'].'</td>'.
    '<td>'.round($HarmPctIncoming,3).'</td>'.
  //  '<td>'.round($PctHarmFromThisShip,3).'</td>'.
  //  '<td>'.round($PctHarmMade,3).'</td>'.
    '<td>'.$HarmMade.'</td>'.
    '<td>'.$sn_data[$defenseShipID]['amplify'][$element].'</td>'.
    '<td>'.$FinalHarm.'</td>'.
  // '<td>'.$sn_data[$defenseShipID]['amplify'][$element].' '.$defenseShipID.' '.$element.''.'</td>'.
    '<td>'.($FinalHarm-$attackArray[$fleetID][$element]['shield']).'</td>'.
    '<td>'.round($attackArray[$fleetID][$element]['def'] / $amount).'</td>'.
  '';

//  print($print_data);
  $be_debug_array[] = $print_data;
}

function BE_DEBUG_closeRow($calculatedDestroyedShip, $fleet_n)
{
  if (!defined('BE_DEBUG') || BE_DEBUG !== true){
    return;
  }

  global $be_debug_array;

  $print_data = ''.
    '<td>'.$calculatedDestroyedShip.'</td>'.
    '<td>'.$fleet_n.'</td>'.
  ''.'</tr>';

//  print($print_data);
  $be_debug_array[] = $print_data;
}

function BE_DEBUG_closeTable()
{
  if (!defined('BE_DEBUG') || BE_DEBUG !== true){
    return;
  }

  global $be_debug_array;

  $print_data = '</table>';

//  print($print_data);
  $be_debug_array[] = $print_data;
}

?>
