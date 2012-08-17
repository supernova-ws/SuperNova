<?php
// Copyright (c) 2009-2012 by Gorlum for http://supernova.ws
// Date 2009-08-08
// Open Source
// V1
//
function COE_missileAttack($defenceTech, $attackerTech, $MIPs, $structures, $targetedStructure = '0') {
  global $sn_data;

  // Here we select which part of defense should take damage: structure or shield
  // $damageTo = 'shield';
  // $damageTo = 'structure';
  $damageTo = 'defense';

  //$MIPDamage = ($MIPs * $sn_data[UNIT_DEF_MISSILE_INTERPLANET]['attack']) * (1 + 0.1 * $attackerTech[$sn_data[TECH_WEAPON]['name']]);
  $MIPDamage = floor(mrc_modify_value($attackerTech, false, TECH_WEAPON, $MIPs * $sn_data[UNIT_DEF_MISSILE_INTERPLANET]['attack'] * mt_rand(80, 120) / 100));
  foreach($structures as $key => $structure)
  {
    $amplify = isset($sn_data[UNIT_DEF_MISSILE_INTERPLANET]['amplify'][$key]) ? $sn_data[UNIT_DEF_MISSILE_INTERPLANET]['amplify'][$key] : 1;
    $structures[$key]['shield'] = floor(mrc_modify_value($defenceTech, false, TECH_SHIELD, $sn_data[$key]['shield']) / $amplify);
    $structures[$key]['structure'] = floor(mrc_modify_value($defenceTech, false, TECH_ARMOR, $sn_data[$key]['armor']) / $amplify);
    $structures[$key]['defense'] = floor((
      mrc_modify_value($defenceTech, false, TECH_ARMOR, $sn_data[$key]['armor']) + 
      mrc_modify_value($defenceTech, false, TECH_SHIELD, $sn_data[$key]['shield'])
    ) / $amplify * mt_rand(80, 120) / 100);
  };

  $startStructs = $structures;

  if ($targetedStructure)
  {
    //attacking only selected structure
    $damageDone = $structures[$targetedStructure][$damageTo];
    $structsDestroyed = min( floor($MIPDamage/$damageDone), $structures[$targetedStructure][0] );
    $structures[$targetedStructure][0] -= $structsDestroyed;
    $MIPDamage -= $structsDestroyed*$damageDone;
  }
  else
  {
    // REALLY random attack
    $can_be_damaged = $sn_data['groups']['defense_active'];
//debug($structures);
//debug($can_be_damaged);
    do
    {
      // finding is there any structure that can be damaged with leftovers of $MIPDamage
      foreach($can_be_damaged as $key => $unit_id)
      {
//debug($structures[$unit_id][0]);
//debug($structures[$unit_id][$damageTo], $MIPDamage);
        if($structures[$unit_id][0] <= 0 || $structures[$unit_id][$damageTo] > $MIPDamage)
        {
          unset($can_be_damaged[$key]);
        }
      }
      if(empty($can_be_damaged))
      {
        break;
      }
      sort($can_be_damaged);
//debug($can_be_damaged, 'can be damaged');
      $random_defense = mt_rand(0, count($can_be_damaged) - 1);
//debug($can_be_damaged[$random_defense], 'Target');
      $current_target = &$structures[$can_be_damaged[$random_defense]];
//debug($current_target[0], 'Amount was');
      $can_be_destroyed = min($current_target[0], floor($MIPDamage / $current_target[$damageTo]));
//debug($MIPDamage, 'MIPDamage');
//debug($can_be_destroyed, 'Can be destroyed');
      $destroyed = mt_rand(1, $can_be_destroyed);
      $MIPDamage -= $current_target[$damageTo] * $destroyed;
      $current_target[0] -= $destroyed;
//debug($destroyed, 'Actually destroyed');

//print('<hr>');
    }
    while($MIPDamage > 0 && !empty($can_be_damaged));
//debug($MIPDamage, 'MIPDamage left');
  };
//debug($structures);//die();
  // 1/2 of metal and 1/4 of crystal of destroyed structures returns to planet
  $metal = 0;
  $crystal = 0;
  foreach ($structures as $key => $structure)
  {
    $destroyed = $startStructs[$key][0] - $structure[0];
    $metal += $destroyed * $sn_data[$key]['metal']/2;
    $crystal += $destroyed * $sn_data[$key]['crystal']/4;
  };

  $return['structures'] = $structures;     // Structures left after attack
  $return['metal']      = floor($metal);   // Metal scraps
  $return['crystal']    = floor($crystal); // Crystal scraps

  return $return;
}

// Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
// Date 2009-08-08
// Open Source

/**
 * Copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *       OpenSource as long as you don't remove this Copyright
 * V3 2009-11-13
 * V2 2009-10-10
 */

function coe_o_missile_calculate()
{
  global $time_now, $sn_data, $lang;

  $iraks = doquery("SELECT * FROM {{iraks}} WHERE `fleet_end_time` <= '{$time_now}';");

  while ($fleetRow = mysql_fetch_assoc($iraks))
  {
    $targetUser  = doquery('SELECT * FROM {{users}} WHERE `id` = '.$fleetRow['fleet_target_owner'], '', true);

    $target_planet_row = sys_o_get_updated($targetUser, array('galaxy' => $fleetRow['fleet_end_galaxy'], 'system' => $fleetRow['fleet_end_system'], 'planet' => $fleetRow['fleet_end_planet'], 'planet_type' => PT_PLANET), $time_now);
    $target_planet_row = $target_planet_row['planet'];

    $rowAttacker = doquery("SELECT * FROM `{{users}}` WHERE `id` = '{$fleetRow['fleet_owner']}' LIMIT 1;", '', true);

    if ($target_planet_row['id'])
    {
      $planetDefense = array();
      foreach($sn_data['groups']['defense_active'] as $unit_id)
      {
        $planetDefense[$unit_id] = array($target_planet_row[$sn_data[$unit_id]['name']]);
      }
      /*
      $planetDefense = array(
        UNIT_DEF_TURRET_MISSILE => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_MISSILE]['name']],),
        UNIT_DEF_TURRET_LASER_SMALL => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_LASER_SMALL]['name']],),
        UNIT_DEF_TURRET_LASER_BIG => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_LASER_BIG]['name']],),
        UNIT_DEF_TURRET_GAUSS => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_GAUSS]['name']],),
        UNIT_DEF_TURRET_ION => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_ION]['name']],),
        UNIT_DEF_TURRET_PLASMA => array( $target_planet_row[$sn_data[UNIT_DEF_TURRET_PLASMA]['name']],),
        UNIT_DEF_SHIELD_SMALL => array( $target_planet_row[$sn_data[UNIT_DEF_SHIELD_SMALL]['name']],),
        UNIT_DEF_SHIELD_BIG => array( $target_planet_row[$sn_data[UNIT_DEF_SHIELD_BIG]['name']],),
        UNIT_DEF_SHIELD_PLANET => array( $target_planet_row[$sn_data[UNIT_DEF_SHIELD_PLANET]['name']],),
      );
      */

      $message = '';
      $interceptor_db_name = $sn_data[UNIT_DEF_MISSILE_INTERCEPTOR]['name'];
      $interceptors = $target_planet_row[$interceptor_db_name]; // Number of interceptors
      $missiles = $fleetRow['fleet_amount']; // Number of MIP
      $qUpdate = "UPDATE `{{planets}}` SET {$interceptor_db_name} = ";
      if ($interceptors >= $missiles) {
        $message = $lang['mip_all_destroyed'];
        $qUpdate .= "{$interceptor_db_name} - {$missiles} ";
      } else {
        if ($interceptors) {
          $message = sprintf($lang['mip_destroyed'], $interceptors);
        };
        $qUpdate .= "0";
        $message .= $lang['mip_defense_destroyed'];

        $attackResult = COE_missileAttack($targetUser, $rowAttacker, ($missiles - $interceptors), $planetDefense, $fleetRow['primaer']);

        foreach($attackResult['structures'] as $key => $structure)
        {
          $destroyed = $planetDefense[$key][0] - $structure[0];
//          if ($key > UNIT_DEFENCE && $destroyed)
          if ($destroyed)
          {
            $message .= "&nbsp;&nbsp;{$lang['tech'][$key]} - {$destroyed} {$lang['quantity']}<br>";
            $qUpdate .= ", `{$sn_data[$key]['name']}` = {$structure[0]}";
          };
        };

        $qUpdate .= ", `metal`=`metal`+".$attackResult['metal'].", `crystal`=`crystal`+".$attackResult['crystal'];
        $message .= "{$lang['mip_recycled']}{$lang['Metal']}: {$attackResult['metal']}, {$lang['Crystal']}: {$attackResult['crystal']}<br>";
      };

      $qUpdate .= " WHERE `id` = " . $target_planet_row['id'] . ";";
      doquery($qUpdate);

      $sourcePlanet = doquery("SELECT `name` FROM `{{planets}}` WHERE `galaxy` = '{$fleetRow['fleet_start_galaxy']}' AND `system` = '{$fleetRow['fleet_start_system']}' AND `planet` = '{$fleetRow['fleet_start_planet']}' and planet_type = " . PT_PLANET, '', true);

      $message_vorlage = sprintf($lang['mip_body_attack'], $fleetRow['fleet_amount'],
        addslashes($sourcePlanet['name']), $fleetRow['fleet_start_galaxy'], $fleetRow['fleet_start_system'], $fleetRow['fleet_start_planet'],
        addslashes($target_planet_row['name']), $fleetRow['fleet_end_galaxy'], $fleetRow['fleet_end_system'], $fleetRow['fleet_end_planet']);

      if (empty($message))
        $message = $lang['mip_no_defense'];

      msg_send_simple_message ( $fleetRow['fleet_owner'], '', $time_now, MSG_TYPE_SPY, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
      msg_send_simple_message ( $fleetRow['fleet_target_owner'], '', $time_now, MSG_TYPE_SPY, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
    };
    doquery("DELETE FROM {{iraks}} WHERE id = '{$fleetRow['id']}';");
  };
};

?>
