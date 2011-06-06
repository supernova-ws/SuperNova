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
function HandleTechnologieBuild(&$planetrow, &$user)
{
  global $resource, $sn_data, $time_now, $lang;

  if ($user['b_tech_planet'] != 0)
  {
    if ($user['b_tech_planet'] != $planetrow['id'])
    {
      $WorkingPlanet = doquery("SELECT * FROM `{{planets}}` WHERE `id` = '{$user['b_tech_planet']}' LIMIT 1;", '', true);
    }

    if ($WorkingPlanet) {
      $planet = $WorkingPlanet;
    } else {
      $planet = $planetrow;
    }

    if ($planet['b_tech'] <= time() && $planet['b_tech_id'] != 0)
    {
      $user[$resource[$planet['b_tech_id']]]++;
      msg_send_simple_message($user['id'], 0, $time_now, MSG_TYPE_QUE, $lang['msg_que_research_from'], $lang['msg_que_research_subject'], sprintf($lang['msg_que_research_message'], $lang['tech'][$planet['b_tech_id']], $user[$resource[$planet['b_tech_id']]]));

      $unit_id = $planet['b_tech_id'];
      $unit_db_name = $resource[$unit_id];
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
      doquery("UPDATE `{{users}}` SET `{$resource[$planet['b_tech_id']]}` = '{$user[$resource[$planet['b_tech_id']]]}', `b_tech_planet` = '0' WHERE `id` = '{$user['id']}' LIMIT 1;");
      $planet["b_tech_id"] = 0;
      if (isset($WorkingPlanet)) {
        $WorkingPlanet = $planet;
      } else {
        $planetrow = $planet;
      }
      $Result['WorkOn'] = "";
      $Result['OnWork'] = false;
    }
    elseif ($planet["b_tech_id"] == 0)
    {
      // Il n'y a rien a l'ouest ...
      // Pas de Technologie en cours devait y avoir un bug lors de la derniere connexion
      // On met l'enregistrement informant d'une techno en cours de recherche a jours
      doquery("UPDATE `{{users}}` SET `b_tech_planet` = '0'  WHERE `id` = '{$user['id']}' LIMIT 1;");
      $Result['WorkOn'] = "";
      $Result['OnWork'] = false;
    }
    else
    {
      // Bin on bosse toujours ici ... Alors ne nous derangez pas !!!
      $Result['WorkOn'] = $planet;
      $Result['OnWork'] = true;
    }
  } else {
    $Result['WorkOn'] = "";
    $Result['OnWork'] = false;
  }

  return $Result;
}

// History revision
// 1.0 - mise en forme modularisation version initiale
// 1.1 - Correction retour de fonction (retourne un tableau a la place d'un flag)
?>