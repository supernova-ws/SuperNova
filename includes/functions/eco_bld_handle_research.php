<?php

/**
 * HandleTechnologieBuild.php
 *
 * @version 1.1
 * @copyright 2008 by Chlorel for XNova
 */

// -----------------------------------------------------------------------------------------------------------
// Teste s'il y a une technologie en cours de realisation
// Paramatres :
// $planetrow -> Planete sur laquelle on entre dans le laboratoire
// $user   -> Joueur
// Reponse :
// Tableau de 2 elements
//     ['OnWork'] -> Boolean .. Vrai ou Faux
//     ['WorkOn'] -> Table de l'enregistrement de la planete sur laquelle s'effectue la techno
function HandleTechnologieBuild(&$user, &$planetrow)
{
  global $sn_data, $time_now, $lang;

  if(!$user['b_tech_planet'])
  {
    return;
  }

  if($user['b_tech_planet'] != $planetrow['id'])
  {
    $planet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$user['b_tech_planet']}' LIMIT 1;", '', true);
  }
  else
  {
    $planet = $planetrow;
  }

  if($planet['b_tech'] && $planet['b_tech_id'] && $planet['b_tech'] <= $time_now)
  {
    $unit_id = $planet['b_tech_id'];
    $unit_db_name = $sn_data[$unit_id]['name'];

    $user[$unit_db_name]++;
    msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_research_from'], $lang['msg_que_research_subject'], sprintf($lang['msg_que_research_message'], $lang['tech'][$planet['b_tech_id']], $user[$unit_db_name]));

    $quest_list = qst_get_quests($user['id']);
    $quest_triggers = qst_active_triggers($quest_list);
    $quest_rewards = array();
    // TODO: Check mutiply condition quests
    $quest_trigger_list = array_keys($quest_triggers, $unit_id);
    foreach($quest_trigger_list as $quest_id)
    {
      if($quest_list[$quest_id]['quest_unit_amount'] <= $user[$unit_db_name] && $quest_list[$quest_id]['quest_status_status'] != QUEST_STATUS_COMPLETE)
      {
        $quest_rewards[$quest_id] = $quest_list[$quest_id]['quest_rewards'];
        $quest_list[$quest_id]['quest_status_status'] = QUEST_STATUS_COMPLETE;
      }
    }
    qst_reward($user, $planet, $quest_rewards, $quest_list);

    doquery("UPDATE `{{planets}}` SET `b_tech` = '0', `b_tech_id` = '0' WHERE `id` = '{$planet['id']}' LIMIT 1;");
    doquery("UPDATE `{{users}}` SET `{$unit_db_name}` = `{$unit_db_name}` + 1, `b_tech_planet` = '0' WHERE `id` = '{$user['id']}' LIMIT 1;");
    $user = doquery("SELECT * FROM {{users}} WHERE `id` = '{$user['id']}' LIMIT 1;", '', true);

    $build_data = eco_get_build_data($user, $planet, $unit_id, $user[$sn_data[$unit_id]['name']] - 1);
    $build_data = $build_data[BUILD_CREATE];
    $xp_incoming = 0;
    foreach($sn_data['groups']['resources_loot'] as $resource_id)
    {
      $xp_incoming += $build_data[$resource_id];
    }
    rpg_level_up($user, RPG_TECH, $xp_incoming / 1000);

    $planet["b_tech_id"] = 0;
  }
  elseif ($planet["b_tech_id"] == 0)
  {
    // Il n'y a rien a l'ouest ...
    // Pas de Technologie en cours devait y avoir un bug lors de la derniere connexion
    // On met l'enregistrement informant d'une techno en cours de recherche a jours
    doquery("UPDATE `{{users}}` SET `b_tech_planet` = '0'  WHERE `id` = '{$user['id']}' LIMIT 1;");
  }
}

// History revision
// 1.0 - mise en forme modularisation version initiale
// 1.1 - Correction retour de fonction (retourne un tableau a la place d'un flag)

?>
