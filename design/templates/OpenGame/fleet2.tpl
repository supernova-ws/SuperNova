<!-- INCLUDE fleet_javascript.tpl -->

<script type="text/javascript"><!--
var fleet_capacity = {fleet_capacity};
var resource_max = Array({planet_metal}, {planet_crystal}, {planet_deuterium});
var fleet_slide_changing = false;

--></script>

<h2>{L_flt_page2_title}</h2>
<h3>{TableTitle}</h3>
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

  <table border="0" cellpadding="0" cellspacing="0">
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
    <tr><th class="c_c"><input id="fleet_page2_submit" value="{fl_continue}" type="submit"></th></tr>
  </table>
<br />

  <table id="resTable" border="0" cellpadding="0" cellspacing="0"><tbody>
    <tr class="c_c">
      <th>{L_sys_resources}</th>
      <th>{L_fl_on_stores}</th>
      <th>{L_fl_load_cargo}</th>
      <th>{L_fl_rest_on_planet}</th>
    </tr>
    <!-- BEGIN resources -->
      <tr>
        <td class="c_l">{resources.NAME}</td>
        <td class="c_r">{resources.ON_PLANET}</td>
        <td class="c_c">
          <script type="text/javascript"><!--
            sn_ainput_make('resource{resources.ID}', {max: Math.min({resources.ON_PLANET}, {fleet_capacity}), step: 1000});

            jQuery('#resource{resources.ID}').bind('keyup change',
              function(event, ui) {    
                var transportCapacity = calculateTransportCapacity();
              }
            );

            jQuery('#resource{resources.ID}slide').bind('slide slidechange',
              function(event, ui) {
                if(fleet_slide_changing)
                {
                  return;
                }
                else
                {
                  fleet_slide_changing = true;
                }
                var transportCapacity = fleet_capacity - check_resource(0) - check_resource(1) - check_resource(2);
                for(i = 0; i < 3; i++)
                {        
                  jQuery('#resource' + i + 'slide').slider("option", "max", Math.min(jQuery('#resource' + i + 'slide').slider("value") + transportCapacity, resource_max[i]));
                  jQuery('#resource' + i).keyup();
                }
                fleet_slide_changing = false;
              }
            );
          --></script>
        </td>
        <td id="rest_res{resources.ID}" class="c_r">{resources.ON_PLANET}</td>
      </tr>
    <!-- END resources -->

    <tr>
      <th colspan="2" class="c_l">{fl_space_left}</th>
      <th class="c_c">
        <div class="fl"><input type="button" onclick="javascript:zero_resources()" value="{L_fl_none_resources}"></div>
        <div class="fr"><input type="button" onclick="javascript:max_resources()" value="{L_fl_allressources}"></div>
      </th>
      <th id="remainingresources" class="c_r">-</th>
    </tr>
  </tbody></table>
</form>

<script type="text/javascript"><!--
  tme = document.getElementsByName("target_mission");
  for(i = tme.length-1; i > 0; i--)
    if(tme[i].checked) break;
  changeMission(tme[i]);
  tme[i].checked = true;

  calculateTransportCapacity();
--></script>