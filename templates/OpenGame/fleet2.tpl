<script type="text/javascript" src="scripts/flotten.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
    return 1;
}
</script>
<br>
<form action="fleet.php?fleet_page=3" method="post">
  <input type="hidden" name="galaxy"          value="{galaxy}" />
  <input type="hidden" name="system"          value="{system}" />
  <input type="hidden" name="planet"          value="{planet}" />
  <input type="hidden" name="planet_type"     value="{planet_type}" />

  <input type="hidden" name="thisgalaxy"      value="{thisgalaxy}" />
  <input type="hidden" name="thissystem"      value="{thissystem}" />
  <input type="hidden" name="thisplanet"      value="{thisplanet}" />
  <input type="hidden" name="thisplanet_type" value="{thisplanet_type}" />

  <input type="hidden" name="usedfleet"       value="{usedfleet}" />

  <input type="hidden" name="consumption"     value="{consumption}" />
  <input type="hidden" name="speed"           value="{speed}" />
  <input type="hidden" name="fleet_group"     value="{fleet_group}" />
  <input type="hidden" name="acs_target_mr"   value="{acs_target_mr}" />
  
  <input type="hidden" name="speedallsmin"    value="{speedallsmin}" />
  <input type="hidden" name="speedfactor"     value="{speedfactor}" />

  <table border="0" cellpadding="0" cellspacing="1"><tbody>
    <tr align="left"><td class="c">{TableTitle}</td></tr>
    <tr align="left" valign="top">
      <th>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left"><tbody>
          <tr><td class="c" colspan="2">{fl_mission}</td></tr>

          <!-- BEGIN missions -->
            <tr><th><div align=left>
              <input id="target_mission{missions.ID}" type="radio" name="target_mission" class="frameless" value="{missions.ID}" <!-- IF missions.CHECKED -->checked <!-- ENDIF --> onChange="javascript:changeMission(this);" onClick="javascript:changeMission(this);">
              <label for="target_mission{missions.ID}">{missions.NAME}</label>
              <!-- IF missions.ID == 5 || missions.ID == 15 -->
                &nbsp;&nbsp;<select name="missiontime">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="4">4</option>
                <option value="8">8</option>
                <option value="16">16</option>
                <option value="32">32</option>
                </select>&nbsp;&nbsp;{L_fl_expe_hours}
              <!-- ENDIF -->
            </div></th></tr>
            <!-- IF missions.ID == 15 -->
              <tr><th>
                <font color="red">{fl_expe_warning}</font>
              </th></tr>
            <!-- ENDIF -->
          <!-- BEGINELSE missions -->
            <tr><th><font color="red">{L_fl_bad_mission}</font></th></tr>
          <!-- END missions -->


        </tbody></table>
      </th>
     </tr>

     <tr id="resTable">
      <th>
        <table border="0" cellpadding="0" cellspacing="0" width="100%"><tbody>
          <tr>
            <td class="c">{L_sys_resources}</td>
            <td class="c">{L_fl_on_stores}</td>
            <td class="c">{L_fl_load_cargo}</td>
            <td class="c">{L_fl_rest_on_planet}</td>
          </tr>


          <!-- BEGIN resources -->
            <tr>
              <th>{resources.NAME}</th>
              <th align=right>{resources.ON_PLANET}</th>
              <th>
                <input type="button" value="0" onClick="javascript:zero_resource({resources.ID});" style="font-weight:bold;color:red;width:20;">
                <input type="button" value="-" onClick="javascript:dec_resource({resources.ID});" style="width:20;">
                <input type="text"   value="0" name="resource{resources.ID}" onkeyup="calculateTransportCapacity();" onchange="calculateTransportCapacity();" onfocus="javascript:if(this.value == '0') this.value='';" onblur="javascript:if(this.value == '') this.value='0';" alt="{Metal} {thisresource{resources.ID}}" />
                <input type="button" value="+" onClick="javascript:inc_resource({resources.ID});" style="width:20;">
                <input type="button" value="{L_fl_selmax}" onClick="javascript:max_resource({resources.ID});" style="font-weight:bold;color:green;width:40;">
              </th>
              <th align=right><div id="rest_res{resources.ID}" class="fr">{resources.ON_PLANET}</div></th>
            </tr>
          <!-- END resources -->


          <tr>
            <th colspan="2">{fl_space_left}</th>
            <th>&nbsp;</th>
            <th><div id="remainingresources" class="fr">-</div></th>
          </tr>
          <tr>
            <th colspan="2">&nbsp;</th>
            <th>
              <input type="button" onclick="javascript:zero_resources()" value="{L_fl_none_resources}">
              <input type="button" onclick="javascript:max_resources()" value="{L_fl_allressources}">
            </th>
          </tr>
        </tbody></table>
      </th>
    </tr>

    <tr><th><input value="{fl_continue}" type="submit"></th></tr>
  </tbody></table>
</form>

<script type="text/javascript"><!--
  var fleet_capacity = {fleet_capacity};
  var resource_max = Array({planet_metal}, {planet_crystal}, {planet_deuterium});

  tme = document.getElementsByName("target_mission");
  for(i = tme.length-1; i > 0; i--)
    if(tme[i].checked) break;
  changeMission(tme[i]);
  tme[i].checked = true;

  calculateTransportCapacity();
--></script>