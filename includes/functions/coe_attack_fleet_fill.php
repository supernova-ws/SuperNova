<?php
function BE_attackFleetFill(&$attackFleets, $fleet, $strField = 'detail'){
  $attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;

  $attackFleets[$fleet['fleet_id']]['user'] = doquery('SELECT id, username, defence_tech, rpg_amiral, shield_tech, military_tech FROM {{table}} WHERE id='.$fleet['fleet_owner'],'users', true);

  $attackFleets[$fleet['fleet_id']][$strField] = array();
  $temp = explode(';', $fleet['fleet_array']);
  foreach ($temp as $Element) {
    $Element = explode(',', $Element);

    if ($Element[0] < 100) continue;

    if (!isset($attackFleets[$fleet['fleet_id']][$strField][$Element[0]]))
      $attackFleets[$fleet['fleet_id']][$strField][$Element[0]] = 0;
    $attackFleets[$fleet['fleet_id']][$strField][$Element[0]] += $Element[1];
  }
}
?>