<?php
// Copyright (c) 2009-2012 by Gorlum for http://supernova.ws
// Date 2009-08-08
// Open Source
// V1
//
use DBAL\OldDbChangeSet;
use Planet\DBStaticPlanet;

function COE_missileAttack($defenceTech, $attackerTech, $MIPs, $structures, $targetedStructure = '0')
{
  // Here we select which part of defense should take damage: structure or shield
  // $damageTo = P_SHIELD;
  // $damageTo = P_STRUCTURE;
  $damageTo = P_DEFENSE;

  $mip_data = get_unit_param(UNIT_DEF_MISSILE_INTERPLANET);
  $MIPDamage = floor(mrc_modify_value($attackerTech, false, TECH_WEAPON, $MIPs * $mip_data[P_ATTACK] * mt_rand(80, 120) / 100));
  foreach($structures as $key => $structure)
  {
    $unit_info = get_unit_param($key);
    $amplify = isset($mip_data[P_AMPLIFY][$key]) ? $mip_data[P_AMPLIFY][$key] : 1;
    $structures[$key][P_SHIELD] = floor(mrc_modify_value($defenceTech, false, TECH_SHIELD, $unit_info[P_SHIELD]) / $amplify);
    $structures[$key][P_STRUCTURE] = floor(mrc_modify_value($defenceTech, false, TECH_ARMOR, $unit_info[P_ARMOR]) / $amplify);
    $structures[$key][P_DEFENSE] = floor((
      mrc_modify_value($defenceTech, false, TECH_ARMOR, $unit_info[P_ARMOR]) +
      mrc_modify_value($defenceTech, false, TECH_SHIELD, $unit_info[P_SHIELD])
    ) / $amplify * mt_rand(80, 120) / 100);
  }

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
    $can_be_damaged = sn_get_groups('defense_active');
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
    } while($MIPDamage > 0 && !empty($can_be_damaged));
//debug($MIPDamage, 'MIPDamage left');
  }
//debug($structures);//die();
  // 1/2 of metal and 1/4 of crystal of destroyed structures returns to planet
  $metal = 0;
  $crystal = 0;
  foreach ($structures as $key => $structure)
  {
    $unit_info = get_unit_param($key);
    $destroyed = $startStructs[$key][0] - $structure[0];
    $metal += $destroyed * $unit_info[P_COST][RES_METAL] / 2;
    $crystal += $destroyed * $unit_info[P_COST][RES_CRYSTAL] / 4;
  }

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

function coe_o_missile_calculate() {
  sn_db_transaction_check(true);

  global $lang;

  $iraks = doquery("SELECT * FROM {{iraks}} WHERE `fleet_end_time` <= " . SN_TIME_NOW . " FOR UPDATE;");

  while($fleetRow = db_fetch($iraks)) {
    set_time_limit(15);
    $db_changeset = array();

    $targetUser = db_user_by_id($fleetRow['fleet_target_owner'], true);

    $target_planet_row = sys_o_get_updated($targetUser, array(
      'galaxy' => $fleetRow['fleet_end_galaxy'],
      'system' => $fleetRow['fleet_end_system'],
      'planet' => $fleetRow['fleet_end_planet'],
      'planet_type' => PT_PLANET
    ), SN_TIME_NOW);
    $target_planet_row = $target_planet_row['planet'];

    $rowAttacker = db_user_by_id($fleetRow['fleet_owner'], true);

    if($target_planet_row['id']) {
      $planetDefense = array();
      foreach(sn_get_groups('defense_active') as $unit_id) {
        $planetDefense[$unit_id] = array(mrc_get_level($targetUser, $target_planet_row, $unit_id, true, true));
      }

      $message = '';
      $interceptors = mrc_get_level($targetUser, $target_planet_row, UNIT_DEF_MISSILE_INTERCEPTOR, true, true); //$target_planet_row[$interceptor_db_name]; // Number of interceptors
      $missiles = $fleetRow['fleet_amount']; // Number of MIP
      if ($interceptors >= $missiles) {
        $message = $lang['mip_all_destroyed'];
        $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit(UNIT_DEF_MISSILE_INTERCEPTOR, -$missiles, $targetUser, $target_planet_row['id']);
      } else {
        if($interceptors) {
          $message = sprintf($lang['mip_destroyed'], $interceptors);
          $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit(UNIT_DEF_MISSILE_INTERCEPTOR, -$interceptors, $targetUser, $target_planet_row['id']);
        }

        $attackResult = COE_missileAttack($targetUser, $rowAttacker, $missiles - $interceptors, $planetDefense, $fleetRow['primaer']);

        foreach($attackResult['structures'] as $key => $structure) {
          $destroyed = $planetDefense[$key][0] - $structure[0];
          if($destroyed) {
            $db_changeset['unit'][] = OldDbChangeSet::db_changeset_prepare_unit($key, -$destroyed, $targetUser, $target_planet_row['id']);

            $message .= "&nbsp;&nbsp;{$lang['tech'][$key]} - {$destroyed} {$lang['quantity']}<br>";
          }
        }

        if(!empty($message)) {
          $message = $lang['mip_defense_destroyed'] . $message . "{$lang['mip_recycled']}{$lang['Metal']}: {$attackResult['metal']}, {$lang['Crystal']}: {$attackResult['crystal']}<br>";

          DBStaticPlanet::db_planet_set_by_id($target_planet_row['id'], "`metal` = `metal` + {$attackResult['metal']}, `crystal` = `crystal` + {$attackResult['crystal']}");
        }
      }
      OldDbChangeSet::db_changeset_apply($db_changeset);

      $fleetRow['fleet_start_type'] = PT_PLANET;
      $sourcePlanet = DBStaticPlanet::db_planet_by_vector($fleetRow, 'fleet_start_', false, 'name');

      $message_vorlage = sprintf($lang['mip_body_attack'], $fleetRow['fleet_amount'],
        addslashes($sourcePlanet['name']), $fleetRow['fleet_start_galaxy'], $fleetRow['fleet_start_system'], $fleetRow['fleet_start_planet'],
        addslashes($target_planet_row['name']), $fleetRow['fleet_end_galaxy'], $fleetRow['fleet_end_system'], $fleetRow['fleet_end_planet']);

      empty($message) ? $message = $lang['mip_no_defense'] : false;

      msg_send_simple_message ( $fleetRow['fleet_owner'], '', SN_TIME_NOW, MSG_TYPE_SPY, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
      msg_send_simple_message ( $fleetRow['fleet_target_owner'], '', SN_TIME_NOW, MSG_TYPE_SPY, $lang['mip_sender_amd'], $lang['mip_subject_amd'], $message_vorlage . $message );
    }
    doquery("DELETE FROM {{iraks}} WHERE id = '{$fleetRow['id']}';");
  }
}
