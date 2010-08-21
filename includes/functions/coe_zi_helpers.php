<?php

function BE_DEBUG_openTable()
{
  if (!defined("BE_DEBUG")){
    return;
  }

  $strBE_Header = '<table>'.'<tr>'.
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
  '</tr>';

  print($strBE_Header);
}

function BE_DEBUG_openRow($round, $defenseShipID, $defenseShipData, $element, $attackArray, $fleetID, $HarmPctIncoming, $HarmMade, $FinalHarm, $amount)
{
  if (!defined("BE_DEBUG")){
    return;
  }

  global $CombatCaps;

  $SN = array(202 => 'МаТр', 203 => 'БоТр', 204 => 'ЛгИс', 205 => 'ТяИс', 206 => 'Крей', 207 => 'Линк', 208 => 'Коло',
    209 => 'Пере', 210 => 'Шпио', 211 => 'Бомб', 212 => 'СоСп', 213 => 'Уник', 214 => 'ЗвСм', 215 => 'Лине', 216 => 'Нова',
    401 => 'Раке', 402 => 'ЛеЛа', 403 => 'ТяЛа', 404 => 'Гаус', 405 => 'Ионн', 406 => 'Плаз', 407 => 'МалЩ', 408 => 'БолЩ', 409 => 'План');

  print('<tr>'.
    '<td>'.$round.'</td>'.
    '<td>'.$SN[$defenseShipID].'</td>'.
    '<td>'.$defenseShipData['att'].'</td>'.
    '<td>'.$CombatCaps[$defenseShipID]['sd'][$element].'</td>'.
    '<td>'.$SN[$element].'</td>'.
    '<td>'.$attackArray[$fleetID][$element]['shield'].'</td>'.
    '<td>'.round($HarmPctIncoming,3).'</td>'.
  //  '<td>'.round($PctHarmFromThisShip,3).'</td>'.
  //  '<td>'.round($PctHarmMade,3).'</td>'.
    '<td>'.$HarmMade.'</td>'.
    '<td>'.$CombatCaps[$defenseShipID]['amplify'][$element].'</td>'.
    '<td>'.$FinalHarm.'</td>'.
  // '<td>'.$CombatCaps[$defenseShipID]['amplify'][$element].' '.$defenseShipID.' '.$element.''.'</td>'.
    '<td>'.($FinalHarm-$attackArray[$fleetID][$element]['shield']).'</td>'.
    '<td>'.round($attackArray[$fleetID][$element]['def'] / $amount).'</td>'.
  '');
}

function BE_DEBUG_closeRow($calculatedDestroyedShip, $fleet_n)
{
  if (!defined('BE_DEBUG')){
    return;
  }

  print(''.
    '<td>'.$calculatedDestroyedShip.'</td>'.
    '<td>'.$fleet_n.'</td>'.
  '');
  print('</tr>');
}

function BE_DEBUG_closeTable()
{
  if (!defined("BE_DEBUG")){
    return;
  }

  print('</table>');
}


?>