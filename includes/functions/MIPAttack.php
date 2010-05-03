<?php
// Copyright (c) 2009 by Gorlum for http://ogame.triolan.com.ua
// Date 2009-08-08
// Open Source
// V1
//
function MIPAttack($defenceTech, $attackerTech, $MIPs, $structures, $targetedStructure = '0') {
  global $CombatCaps, $pricelist, $resource;

  // Here we select which part of defense should take damage: structure or shield
  // $damageTo = 'shield';
  $damageTo = 'structure';

  $MIPDamage = ($MIPs * $CombatCaps[503]['attack']) * (1 + 0.05 * $attackerTech[$resource[109]]);

  foreach ($structures as $key => $structure)
  {
    $structures[$key]['shield'] = $CombatCaps[$key]['shield'] * (1 + 0.05 * $defenceTech[$resource[111]]);
    $structures[$key]['structure'] = ($pricelist[$key]['metal'] + $pricelist[$key]['crystal']) * (1 + 0.05 * $defenceTech[$resource[110]]);
  };

  $minDamage = $structures[401][$damageTo];
  $startStructs = $structures;

  if ($targetedStructure){
    //attacking only selected structure
    $damageDone = $structures[$targetedStructure][$damageTo];
    $structsDestroyed = min( floor($MIPDamage/$damageDone), $structures[$targetedStructure][0] );
    $structures[$targetedStructure][0] -= $structsDestroyed;
    $MIPDamage -= $structsDestroyed*$damageDone;
  }else{
    // REALLY random attack
    do {
      // finding is there any structure that can be damaged with leftovers of $MIPDamage
      for ($i = 409; $i > 400; $i--){
        if (($structures[$i][0]>0) && ($structures[$i][$damageTo]<=$MIPDamage)){
          break;
        };
      };

      // Selecting random structure of available
      $RandomDefense = rand(401, $i);
      if ($i>400 && $structures[$RandomDefense][0]>0 && $structures[$RandomDefense][$damageTo]<=$MIPDamage){
        $MIPDamage -= $structures[$RandomDefense][$damageTo];
        $structures[$RandomDefense][0]--;
      };
    } while ($i>400); // on $i = 400 - no more structures to damage. Exiting loop
  };

  // 1/2 of metal and 1/4 of crystal of destroyed structures returns to planet
  $metal = 0;
  $crystal = 0;
  foreach ($structures as $key => $structure)
  {
    $destroyed = $startStructs[$key][0]-$structure[0];
    $metal += $destroyed*$pricelist[$key]['metal']/2;
    $crystal += $destroyed*$pricelist[$key]['crystal']/4;
  };

  $return['structures'] = $structures;     // Structures left after attack
  $return['metal']      = floor($metal);   // Metal scraps
  $return['crystal']    = floor($crystal); // Crystal scraps

  return $return;
}

// Copyright (c) 2009 by Gorlum for http://ogame.triolan.com.ua
// Date 2009-08-08
// Open Source
?>