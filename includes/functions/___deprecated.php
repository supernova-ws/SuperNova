<?php

// TODO: This functions is deprecated and should be replaced!

/**
 *
 * @function CheckPlanetUsedFields
 *
 * v2.0 Rewrote to utilize foreach()
 *      Complying with PCG0
 * v1.1 some optimizations
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */
// Verification du nombre de cases utilisées sur la planete courrante
function CheckPlanetUsedFields(&$planet)
{
  if (!$planet['id'])
  {
    return 0;
  }

  global $sn_data;

  $planet_fields = 0;
  foreach ($sn_data['groups']['build_allow'][$planet['planet_type']] as $building_id)
  {
    $planet_fields += $planet[$sn_data[$building_id]['name']];
  }

  if ($planet['field_current'] != $planet_fields)
  {
    $planet['field_current'] = $planet_fields;
    doquery("UPDATE {{planets}} SET field_current={$planet_fields} WHERE id={$planet['id']} LIMIT 1;");
  }
}

/**
 * MessageForm.php
 *
 * @version 1
 * @copyright 2008 By Chlorel for XNova
 */

// Parametres en entrée:
// $Title    -> Titre du Message
// $Message  -> Texte contenu dans le message
// $Goto     -> Adresse de saut pour le formulaire
// $Button   -> Bouton de validation du formulaire
// $TwoLines -> Sur une ou sur 2 lignes
//
// Retour
//           -> Une chaine formatée affichable en html
function MessageForm ($Title, $Message, $Goto = '', $Button = ' ok ', $TwoLines = false) {
  $Form  = "<center>";
  $Form .= "<form action=\"". $Goto ."\" method=\"post\">";
  $Form .= "<table width=\"519\">";
  $Form .= "<tr>";
  $Form .= "<td class=\"c\" colspan=\"2\">". $Title ."</td>";
  $Form .= "</tr><tr>";

  if($Button){
    $Button = "<input type=\"submit\" value=\"{$Button}\">";
  }

  if ($TwoLines == true) {
    $Form .= "<th colspan=\"2\">". $Message ."</th>";
    $Form .= "</tr><tr>";
    if($Button)
      $Form .= "<th colspan=\"2\" align=\"center\">{$Button}</th>";
  } else {
    $Form .= "<th colspan=\"2\">". $Message . $Button . "</th>";
  }
  $Form .= "</tr>";
  $Form .= "</table>";
  $Form .= "</form>";
  $Form .= "</center>";

  return $Form;
}

/**
 * eco_can_build_unit.php
 *
 * 2.0 copyright 2009-2011 Gorlum for http://supernova.ws
 *  [!] Full rewrote from scratch
 *  [+] function eco_unit_busy
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function eco_can_build_unit($user, $planet, $unit_id)
{
  global $sn_data;

  $accessible = BUILD_ALLOWED;
  if(isset($sn_data[$unit_id]['require']))
  {
    foreach($sn_data[$unit_id]['require'] as $require_id => $require_level)
    {
      $db_name = $sn_data[$require_id]['name'];
      $data = in_array($require_id, $sn_data['groups']['mercenaries']) ? mrc_get_level($user, $planet, $require_id) : (isset($planet[$db_name]) ? $planet[$db_name] : (isset($user[$db_name]) ? $user[$db_name] : ($require_id == $planet['PLANET_GOVERNOR_ID'] ? $planet['PLANET_GOVERNOR_LEVEL'] : 0)));

      if($data < $require_level)
      {
        $accessible = BUILD_REQUIRE_NOT_MEET;
        break;
      }
    }
  }

  return $accessible;
}

function eco_unit_busy($user, $planet, $que, $unit_id)
{
  global $config;

  $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
  $lab_busy    = $planet['b_tech'] && $planet['b_tech_id'] && !$config->BuildLabWhileRun;

  switch($unit_id)
  {
    case STRUC_FACTORY_HANGAR:
      $return = $hangar_busy;
    break;

    case STRUC_LABORATORY:
    case STRUC_LABORATORY_NANO:
      $return = $lab_busy;
    break;

    default:
      $return = false;
    break;
  }

//  return (($unit_id == STRUC_LABORATORY || $unit_id == STRUC_LABORATORY_NANO) && $lab_busy) || ($unit_id == STRUC_FACTORY_HANGAR && $hangar_busy);
  return $return;
}

function eco_unit_buildable($user, $planet, $que, $que_id, $unit_id, $unit_amount = 1, $build_mode = BUILD_CREATE)
{
/*
  if($unit_amount < 1)
  {
    return BUILD_AMOUNT_WRONG;
  }
  $unit_amount = intval($unit_amount);

  @$que_data = $GLOBALS['sn_data']['groups']['ques'][$que_id];
  if(!isarray($que_data))
  {
    return BUILD_QUE_WRONG;
  }

  if($que_id == QUE_STRUCTURES)
  {
    $que_data['unit_list'] = $GLOBALS['sn_data']['groups']['build_allow'][$planet['planet_type']];
  }

  if(!in_array($unit_id, $que_data['unit_list']))
  {
    return BUILD_QUE_UNIT_WRONG;
  }

  $config_build_busy_lab = $GLOBALS['config']->BuildLabWhileRun;
*/

/*
  $hangar_busy = $planet['b_hangar'] && $planet['b_hangar_id'];
  $lab_busy    = $planet['b_tech'] && $planet['b_tech_id'] && !$config->BuildLabWhileRun;

  switch($unit_id)
  {
    case STRUC_FACTORY_HANGAR:
      $return = $hangar_busy;
    break;

    case STRUC_LABORATORY:
    case STRUC_LABORATORY_NANO:
      $return = $lab_busy;
    break;

    default:
      $return = false;
    break;
  }

//  return (($unit_id == STRUC_LABORATORY || $unit_id == STRUC_LABORATORY_NANO) && $lab_busy) || ($unit_id == STRUC_FACTORY_HANGAR && $hangar_busy);
  return $return;
*/

//  $unit_level = ($planet[$unit_db_name] ? $planet[$unit_db_name] : 0) + $que['in_que'][$unit_id];
//  $build_data = eco_get_build_data($user, $planet, $unit_id, $unit_level);
}

?>
