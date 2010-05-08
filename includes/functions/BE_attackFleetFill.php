<?PHP
function BE_attackFleetFill(&$attackFleets, $fleet){
  $attackFleets[$fleet['fleet_id']]['fleet'] = $fleet;

  // !G+ We only need id, techlevels and rpg_amiral - why query whole row?
  $attackFleets[$fleet['fleet_id']]['user'] = doquery('SELECT id, username, defence_tech, rpg_amiral, shield_tech, military_tech FROM {{table}} WHERE id='.$fleet['fleet_owner'],'users', true);

  $attackFleets[$fleet['fleet_id']]['detail'] = array();
  $temp = explode(';', $fleet['fleet_array']);
  foreach ($temp as $temp2) {
    $temp2 = explode(',', $temp2);

    if ($temp2[0] < 100) continue;

    if (!isset($attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]]))
      $attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] = 0;
    $attackFleets[$fleet['fleet_id']]['detail'][$temp2[0]] += $temp2[1];
  }
}
?>