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

?>
