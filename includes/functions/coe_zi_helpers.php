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

  global $CombatCaps;
  global $be_debug_array;

  $SN = array(202 => 'Ã‡“', 203 => '¡Ó“', 201 => '—Û“', 204 => 'À„»Ò', 205 => '“ˇ»Ò', 206 => ' ÂÈ', 207 => 'ÀËÌÍ', 208 => ' ÓÎÓ',
    209 => 'œÂÂ', 210 => 'ÿÔËÓ', 211 => '¡ÓÏ·', 212 => '—Ó—Ô', 213 => '”ÌËÍ', 214 => '«‚—Ï', 215 => 'ÀËÌÂ', 216 => 'ÕÓ‚‡',
    401 => '–‡ÍÂ', 402 => 'ÀÂÀ‡', 403 => '“ˇÀ‡', 404 => '√‡ÛÒ', 405 => '»ÓÌÌ', 406 => 'œÎ‡Á', 407 => 'Ã‡ÎŸ', 408 => '¡ÓÎŸ', 409 => 'œÎ‡Ì');

  $print_data = '<tr>'.
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