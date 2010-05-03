<?php

/**
 * GalaxyLegendPopup.php
 *
 * @version 1.0
 * @copyright 2008 By Chlorel for XNova
 */

function GalaxyLegendPopup () {
  global $lang;

  $Result  = "<a href=# style=\"cursor: pointer;\"";
  $Result .= " onmouseover='return overlib(\"";

  $Result .= "<table width=240>";
  $Result .= "<tr>";
  $Result .= "<td class=c colspan=2>".$lang['Legend']."</td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Strong_player']."</td><td><span class=strong>".$lang['strong_player_shortcut']."</span></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Weak_player']."</td><td><span class=noob>".$lang['weak_player_shortcut']."</span></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Way_vacation']."</td><td><span class=vacation>".$lang['vacation_shortcut']."</span></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Pendent_user']."</td><td><span class=banned>".$lang['banned_shortcut']."</span></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Inactive_7_days']."</td><td><span class=inactive>".$lang['inactif_7_shortcut']."</span></td>";
  $Result .= "</tr><tr>";
  $Result .= "<td width=220>".$lang['Inactive_28_days']."</td><td><span class=longinactive>".$lang['inactif_28_shortcut']."</span></td>";
  $Result .= "</tr>";
  $show_admin = SHOW_ADMIN;
 if ($show_admin > 0) {
    $Result .= "<tr>";
    $Result .= "<td width=220>Администратор</td><td><font color=lime><blink>A</blink></font></td>";
    $Result .= "</tr>";
    $Result .= "<tr>";
    $Result .= "<td width=220>Модератор</td><td><font color=lime><blink>M</blink></font></td>";
    $Result .= "</tr>";
    $Result .= "<tr>";
    $Result .= "<td width=220>Оператор</td><td><font color=lime><blink>O</blink></font></td>";
    $Result .= "</tr>";
  }
  $Result .= "</table>";
  $Result .= "\", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -150, OFFSETY, -150 );'";
  // $Result .= "\", STICKY, MOUSEOFF, OFFSETY, -100);'";
  $Result .= " onmouseout='return nd();'>";
  $Result .= $lang['Legend']."</a>";

  return $Result;
}
?>
