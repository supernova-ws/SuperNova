<?php

/**
 * ShowGalaxyMISelector.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function ShowGalaxyMISelector ( $Galaxy, $System, $Planet, $Current, $MICount ) {
  global $lang;

  $Result  = "<form action=\"raketenangriff.php?c=".$Current."&mode=2&galaxy=".$Galaxy."&system=".$System."&planet=".$Planet."\" method=\"POST\">";
  $Result .= "<tr>";
  $Result .= "<table border=\"0\">";
  $Result .= "<tr>";
  $Result .= "<td class=\"c\" colspan=\"3\">";
  $Result .= $lang['gm_launch'] ." [".$Galaxy.":".$System.":".$Planet."]";
  $Result .= "</td>";
  $Result .= "</tr>";
  $Result .= "<tr>";
  $String  = sprintf($lang['gm_restmi'], $MICount);
  $Result .= "<td class=\"c\">".$String." <input type=\"text\" name=\"SendMI\" size=\"2\" maxlength=\"7\" /></td>";
  $Result .= "<td class=\"c\">".$lang['gm_target']." <select name=\"Target\">";
  $Result .= "<option value=\"all\" selected>".$lang['gm_all']."</option>";
  $Result .= "<option value=\"401\">".$lang['tech'][401]."</option>";
  $Result .= "<option value=\"402\">".$lang['tech'][402]."</option>";
  $Result .= "<option value=\"403\">".$lang['tech'][403]."</option>";
  $Result .= "<option value=\"404\">".$lang['tech'][404]."</option>";
  $Result .= "<option value=\"405\">".$lang['tech'][405]."</option>";
  $Result .= "<option value=\"406\">".$lang['tech'][406]."</option>";
  $Result .= "<option value=\"407\">".$lang['tech'][407]."</option>";
  $Result .= "<option value=\"408\">".$lang['tech'][408]."</option>";
  $Result .= "<option value=\"409\">".$lang['tech'][409]."</option>";
  $Result .= "</select>";
  $Result .= "</td>";
  $Result .= "</tr>";
  $Result .= "<tr>";
  $Result .= "<td class=\"c\" colspan=\"2\"><input type=\"submit\" name=\"aktion\" value=\"".$lang['gm_send']."\"></td>";
  $Result .= "</tr>";
  $Result .= "</table>";
  $Result .= "</form>";

  return $Result;
}
?>