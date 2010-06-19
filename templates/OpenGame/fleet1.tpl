<script type="text/javascript" src="scripts/flotten.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
  return 1
}
</script>
{vacation}
<form action="fleet.php?fleet_page=2" method="post">
  {FleetHiddenBlock}
  <input type="hidden" name="speedallsmin"   value="{speedallsmin}" />
  <input type="hidden" name="usedfleet"      value="{usedfleet}" />
  <input type="hidden" name="thisgalaxy"     value="{thisgalaxy}" />
  <input type="hidden" name="thissystem"     value="{thissystem}" />
  <input type="hidden" name="thisplanet"     value="{thisplanet}" />
  <input type="hidden" name="galaxyend"      value="{galaxy}" />
  <input type="hidden" name="systemend"      value="{system}" />
  <input type="hidden" name="planetend"      value="{planet}" />
  <input type="hidden" name="speedfactor"    value="{speedfactor}" />
  <input type="hidden" name="thisplanet_type" value="{thisplanet_type}" />
  <input type="hidden" name="thisresource1"  value="{thisresource1}" />
  <input type="hidden" name="thisresource2"  value="{thisresource2}" />
  <input type="hidden" name="thisresource3"  value="{thisresource3}" />

  <br><div><center>
  <table width="519" border="0" cellpadding="0" cellspacing="1">
  <tr height="20">
  <td colspan="2" class="c">{fl_floten1_ttl}</td>
  </tr>
  <tr height="20">
  <th width="50%">{fl_dest}</th>
  <th>
  <input name="galaxy" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{galaxy}" />
  <input name="system" size="3" maxlength="3" onChange="shortInfo()" onKeyUp="shortInfo()" value="{system}" />
  <input name="planet" size="3" maxlength="2" onChange="shortInfo()" onKeyUp="shortInfo()" value="{planet}" />
  <select name="planet_type" onChange="shortInfo()" onKeyUp="shortInfo()">
    <option value="1" {t1}>{fl_planet}</option>
    <option value="2" {t2}>{fl_ruins}</option>
    <option value="3" {t3}>{fl_moon}</option>
  </select>
  <input name="fleet_group" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0" />
  <input name="acs_target_mr" type="hidden" onChange="shortInfo()" onKeyUp="shortInfo()" value="0:0:0" />
  </th>
  </tr>
  <tr height="20">
  <th>{fl_speed}</th>
  <th>
  <select name="speed" onChange="shortInfo()" onKeyUp="shortInfo()">
    <option value="10">100</option>
    <option value="9">90</option>
    <option value="8">80</option>
    <option value="7">70</option>
    <option value="6">60</option>
    <option value="5">50</option>
    <option value="4">40</option>
    <option value="3">30</option>
    <option value="2">20</option>
    <option value="1">10</option>
  </select> %
  </th>
  </tr>

  <tr height="20">
  <th>{fl_dist}</th>
  <th><div id="distance">-</div></th>
  </tr><tr height="20">
  <th>{fl_fltime}</th>
  <th><div id="duration">-</div></th>
  </tr><tr height="20">
  <th>{fl_deute_need}</th>
  <th><div id="consumption">-</div></th>
  </tr><tr height="20">
  <th>{fl_speed_max}</th>
  <th><div id="maxspeed">-</div></th>
  </tr><tr height="20">
  <th>{fl_max_load}</th>
  <th><div id="storage">-</div></th>
  </tr>
  <tr height="20"><th colspan="2"><input type="submit" value="{fl_continue}" /></th></tr>

<!--
  <tr height=20><td class=c>{fl_shortcut}</td><td class=c style="align: right;"><div style="floating: left"><a href=fleetshortcut.php>{fl_shortlnk}</a></div></td></tr>
-->
  <tr height=20><td colspan=2 class=c><div class="fl">{fl_shortcut}</div><a href=fleetshortcut.php class="fr">{fl_shortlnk}</a></td></tr>
  {shortcuts}
  <tr height="20" style="{DisplayNoShortcuts}"><th colspan="2">{fl_noshortc}</th></tr>


  <tr height=20>
  <td colspan=2 class=c>{fl_myplanets}</td>
  </tr>
  {ColoniesList}
  <th colspan="2" style="{DisplayNoColonies}">{fl_nocolonies}</th>

  <tr height=20>
  <td colspan=2 class=c>{fl_grattack}</td>
  </tr>
  {acss}
  
  </table>
  <table width="519" border="0" cellpadding="0" cellspacing="1">
    <tr><td class="c">{sys_hint}</td></tr>
    <tr><th><div class="hint">{fl_AKS_hint}</div></th></tr>
  </table>
  </div></center>
  <input type="hidden" name="maxepedition" value="{maxepedition}" />
  <input type="hidden" name="curepedition" value="{curepedition}" />
  <input type="hidden" name="target_mission" value="{target_mission}" />
  </form>
  <script>javascript:shortInfo(); </script>
