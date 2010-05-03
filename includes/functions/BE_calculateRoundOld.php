<?php

function BE_calculateRoundOld(&$fleets, &$fleet_n, &$fleet_shield, &$fleetDmgPerEnemyFleet, &$fleetArray, &$attackRoundData, &$defenseRoundData, $strFieldName, $round ){
    global $pricelist, $CombatCaps;
    // pdump(&$defenseRoundData,$round);

    foreach ($fleets as $fleetID => $fleet) {
      $fleet_n[$fleetID] = array();

      foreach($fleet[$strFieldName] as $element => $amount) {
        $fleetPctPerShipType = $fleetDmgPerEnemyFleet[$fleetID] * $amount / $attackRoundData['amount'][$fleetID];
        $attackerDmgPerShipType = floor($defenseRoundData['att']['total'] * $fleetPctPerShipType);

        $maxDestroyedShip = floor($defenseRoundData['rf']['total'][$element]);
print('<tr>'.
  '<td>'.$round.'</td>'.
  '<td>'.$element.'</td>'.
  '<td>'.$fleetArray[$fleetID][$element]['shield'].'</td>'.
  '<td>'.$attackerDmgPerShipType.'</td>'.
  '<td>'.($attackerDmgPerShipType-$fleetArray[$fleetID][$element]['shield']).'</td>'.
  '<td>'.($fleetArray[$fleetID][$element]['def'] / $amount).'</td>'.
  '<td>'.$maxDestroyedShip.'</td>'.
  '<td>'.$CombatCaps[$element]['sd'].'</td>'.
'</tr>');

        if ($amount > 0) {
          if ($fleetArray[$fleetID][$element]['shield']/$amount < $attackerDmgPerShipType) {

            $attackerDmgPerShipType -= $fleetArray[$fleetID][$element]['shield'];
            $fleet_shield += $fleetArray[$fleetID][$element]['shield'];
            $calculatedDestroyedShip = floor($attackerDmgPerShipType / ($fleetArray[$fleetID][$element]['def'] / $amount));

            if ($maxDestroyedShip < 0) $maxDestroyedShip = 0;
            if ($calculatedDestroyedShip < 0) $calculatedDestroyedShip = 0;

            if ($calculatedDestroyedShip > $maxDestroyedShip) {
              $calculatedDestroyedShip = $maxDestroyedShip;
            }

            $fleet_n[$fleetID][$element] = ceil($amount - $calculatedDestroyedShip);
            if ($fleet_n[$fleetID][$element] <= 0) {
              $fleet_n[$fleetID][$element] = 0;
            }
          } else {
            $fleet_n[$fleetID][$element] = round($amount);
            $fleet_shield += $attackerDmgPerShipType;
          }
        } else {
          $fleet_n[$fleetID][$element] = round($amount);
          $fleet_shield += $attackerDmgPerShipType;
        }
      }
    }
}

?>