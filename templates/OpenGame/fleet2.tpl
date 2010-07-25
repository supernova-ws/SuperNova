<script type="text/javascript" src="scripts/flotten.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
    return 1;
}
</script>
<br><center>
<form action="fleet.php?fleet_page=3" method="post">
<input type="hidden" name="thisresource1"   value="{thisresource1}" />
<input type="hidden" name="thisresource2"   value="{thisresource2}" />
<input type="hidden" name="thisresource3"   value="{thisresource3}" />
<input type="hidden" name="consumption"     value="{consumption}" />
<input type="hidden" name="dist"            value="{dist}" />
<input type="hidden" name="thisgalaxy"      value="{thisgalaxy}" />
<input type="hidden" name="thissystem"      value="{thissystem}" />
<input type="hidden" name="thisplanet"      value="{thisplanet}" />
<input type="hidden" name="galaxy"          value="{galaxy}" />
<input type="hidden" name="system"          value="{system}" />
<input type="hidden" name="planet"          value="{planet}" />
<input type="hidden" name="thisplanet_type" value="{thisplanet_type}" />
<input type="hidden" name="planet_type"     value="{planet_type}" />
<input type="hidden" name="speedallsmin"    value="{speedallsmin}" />
<input type="hidden" name="speed"           value="{speed}" />
<input type="hidden" name="speedfactor"     value="{speedfactor}" />
<input type="hidden" name="usedfleet"       value="{usedfleet}" />
<input type="hidden" name="maxepedition"    value="{maxepedition}" />
<input type="hidden" name="curepedition"    value="{curepedition}" />
<input type="hidden" name="fleet_group"     value="{fleet_group}" />
<input type="hidden" name="acs_target_mr"   value="{acs_target_mr}" />
{FleetHidden}

<table border="0" cellpadding="0" cellspacing="1" width="519">
<tbody>
  <tr align="left" height="20"><td class="c" colspan="2">{TableTitle}</td></tr>
  <tr align="left" valign="top">
    <th width="50%">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left"><tbody>
        <tr height="20"><td class="c" colspan="2">{fl_mission}</td></tr>
        {MissionSelector}
        <tr height="20" style="{HideExpedition}"><th><!--<div align=left>
          <input type="radio" name="target_mission" value="15" checked="checked">{MsgExpedition}&nbsp;&nbsp;<select name="missiontime">
              <option value="1">1</option>
              <option value="2">2</option>
            </select> {fl_expe_hours}</div>-->
          <font color="red">{fl_expe_warning}</font>
        </th></tr>
        <tr height="20" style="{DisplayBadMission}"><th><font color="red">{fl_bad_mission}</font></th></tr>
      </tbody></table>
    </th>

    <th><div id="resTable">
      <table border="1" cellpadding="0" cellspacing="0" width="100%">
      <tbody>
        <tr height="20"><td colspan="3" class="c">{fl_ressources}</td></tr>
        <tr height="20">
          <th>{Metal}</th>
          <th><a href="javascript:maxResource('1');">{fl_selmax}</a></th>
          <th><input name="resource1" alt="{Metal} {thisresource1}" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        </tr>
        <tr height="20">
          <th>{Crystal}</th>
          <th><a href="javascript:maxResource('2');">{fl_selmax}</a></th>
          <th><input name="resource2" alt="{Crystal} {thisresource2}" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        </tr>
        <tr height="20">
          <th>{Deuterium}</th>
          <th><a href="javascript:maxResource('3');">{fl_selmax}</a></th>
          <th><input name="resource3" alt="{Deuterium} {thisresource3}" size="10" onchange="calculateTransportCapacity();" type="text"></th>
        </tr>
        <tr height="20">
          <th>{fl_space_left}</th>
          <th colspan="2"><div id="remainingresources">-</div></th>
        </tr>
        <tr height="20">
          <th colspan="3"><a href="javascript:maxResources()">{fl_allressources}</a></th>
        </tr>
<!--        <tr height="20"><th colspan="3"><div id="debug">debug</div></th></tr> -->
      </tbody></table>
    </div>&nbsp;</th>
  </tr>

  <tr height="20"><th colspan="2"><input accesskey="z" value="{fl_continue}" type="submit"></th></tr>

</tbody></table>
</form></center>
<script type="text/javascript"><!--
  tme = document.getElementsByName("target_mission");
  for(i = tme.length-1; i > 0; i--)
    if(tme[i].checked) break;
  changeMission(tme[i]);
  tme[i].checked = true;
--></script>