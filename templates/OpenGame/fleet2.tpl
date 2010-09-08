<script type="text/javascript" src="scripts/flotten.js"></script>
<script type="text/javascript">
function getStorageFaktor() {
    return 1;
}
</script>

<script type="text/javascript"><!--
var fleet_capacity = {fleet_capacity};
var resource_max = Array({planet_metal}, {planet_crystal}, {planet_deuterium});
var fleet_slide_changing = false;
// var ainput_list = Array();

function int_ainput_zero()
{
  for (i in resource_max)
  {
    zero_resource(i);
  }
  calculateTransportCapacity();
}

function int_ainput_make(field_name, min_value, max_value)
{
//  var my_input = Array();
//  document.write('<input type="button" value="0" onClick="javascript:int_ainput_zero(\''+ field_name + '\');" style="font-weight:bold;color:red;width:20;">');
//  document.write('<input type="button" value="-1k" onClick="javascript:int_ainput_dec(\''+ field_name + '\');" style="width:20;">');
  document.write('<input type="text"   id="' + field_name + '" value="0" name="' + field_name + '" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'\') this.value=\'0\';"/>');
//  document.write('<input type="button" value="+1k" onClick="javascript:int_ainput_inc(\''+ field_name + '\', 1000);" style="width:20;">');
//  document.write('<input type="button" value="{L_fl_selmax}" onClick="javascript:int_ainput_max(\'' + field_name + '\');" style="font-weight:bold;color:green;width:40;">');
  document.write('<div id="' + field_name + 'slide"></div>');

  var slider_id = "#" + field_name + 'slide';
//  var field_name = field_name;

  jQuery(function() {
    jQuery(slider_id).slider({
      range: "min",
      value: min_value,
      min: min_value,
      max: max_value,
      slide: function(event, ui) {
        jQuery("#" + field_name).val(ui.value);
        jQuery("#" + field_name).change();
      }
    });
    jQuery("#" + field_name).val(jQuery(slider_id).slider("value"));
  });

  jQuery("#" + field_name).bind('keyup change',
    function(event, ui)
    {
      if(jQuery(this).val() > jQuery(slider_id).slider("option", "max"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "max"));
      }

      if(jQuery(this).val() < jQuery(slider_id).slider("option", "min"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "min"));
      }

      jQuery(slider_id).slider("value", jQuery(this).val()); 
      //document.getElementById('resource0temp').innerHTML = field_name + '!' + jQuery(this).val() + '?' + jQuery(slider_id).slider("option", "max");
      //Math.min(jQuery('#resource' + i + 'slide').slider("value") + transportCapacity, resource_max[i]) 
    }
  );

//  ainput_list[] = Array();
//  alert(ainput_list.length);

}
--></script>

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
    <tr><th><input id="fleet_page2_submit" value="{fl_continue}" type="submit"></th></tr>
  </tbody></table><br>

  <table id="resTable" border="0" cellpadding="0" cellspacing="0"><tbody>
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
          <div>
          <!--
          <div id="resource{resources.ID}temp">&nbsp;</div>
          -->

          <script type="text/javascript"><!--
            int_ainput_make('resource{resources.ID}', 0, Math.min({resources.ON_PLANET}, {fleet_capacity}));

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
                transportCapacity = storage() - check_resource(0) - check_resource(1) - check_resource(2);
                for(i = 0; i < 3; i++)
                {        
                  jQuery('#resource' + i + 'slide').slider("option", "max", Math.min(jQuery('#resource' + i + 'slide').slider("value") + transportCapacity, resource_max[i]));
                  jQuery('#resource' + i).keyup();
                  // document.getElementById('resource' + i + 'temp').innerHTML = Math.min(jQuery('#resource' + i + 'slide').slider("value") + transportCapacity, resource_max[i]);
                }
                fleet_slide_changing = false;
              }
            );

          --></script>
          </div>
        </th>
        <th align=right><div id="rest_res{resources.ID}" class="fr">{resources.ON_PLANET}</div></th>
      </tr>
    <!-- END resources -->


    <tr>
      <th colspan="2">{fl_space_left}</th>
      <th>
        <div class="fl"><input type="button" onclick="javascript:zero_resources()" value="{L_fl_none_resources}"></div>
        <div class="fr"><input type="button" onclick="javascript:max_resources()" value="{L_fl_allressources}"></div>
      </th>
      <th><div id="remainingresources" class="fr">-</div></th>
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