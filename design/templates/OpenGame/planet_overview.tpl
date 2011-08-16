<!-- DEFINE $OVERVIEW = true -->
<script language="JavaScript" src="js/lib/tw-sack.js"></script>
<script type="text/javascript"> 

var SHIP_RECYCLER = {D_SHIP_RECYCLER};

// fixing bug with sending recycle from planet overview
// replace sack() with jQuery! Everywhere!!!

function whenResponse () {
  retVals   = this.response.split("|");
  CmdCode   = retVals[0];
  strInfo   = retVals[1];
  UsedSlots = retVals[2];
  SpyProbes = retVals[3];
  Recyclers = retVals[4];
  Missiles  = retVals[5];
  document.getElementById("ov_recycle").innerHTML = retVals[1];
}

var ajax = new sack();

function doit (order, galaxy, system, planet, planettype, shipcount) {
  ajax.requestFile = "flotenajax.php?action=send";
  ajax.runResponse = whenResponse;
  ajax.execute = true;
  ajax.setVar("thisgalaxy", galaxy);
  ajax.setVar("thissystem", system);
  ajax.setVar("thisplanet", planet);
  ajax.setVar("thisplanettype", planettype);
  ajax.setVar("mission", order);
  ajax.setVar("galaxy", galaxy);
  ajax.setVar("system", system);
  ajax.setVar("planet", planet);
  ajax.setVar("planettype", 2);
  if (order == 8)
    ajax.setVar("ship" + SHIP_RECYCLER, shipcount);
  ajax.runAJAX();
}

  jQuery.noConflict(); 
// --></script>

<br>
<table><tr><th valign=top class="tr">
  <table width="519">
    <!-- IF NEW_MESSAGES -->
      <tr><th colspan=2><a href=messages.php>
        {L_ov_you_have}
        <!-- IF NEW_MESSAGES == 1 -->
          {L_ov_new_message}
        <!-- ELSE -->
          {NEW_MESSAGES} {L_ov_new_messages}
        <!-- ENDIF -->
      </a></th></tr>
    <!-- ENDIF -->

    <!-- IF NEW_LEVEL_MINER -->
      <tr><th colspan=2><a href=officer.php>{L_ov_rpg_new_level_miner}</a></th></tr>
    <!-- ENDIF -->
    
    <!-- IF NEW_LEVEL_RAID -->
      <tr><th colspan=2><a href=officer.php>{L_ov_rpg_new_level_raid}</a></th></tr>
    <!-- ENDIF -->
  </table>    

  <table width="519">
    <tr>
      <td style="white-space: nowrap;" class="c" colspan=5>
        <div class="fl">{PLANET_TYPE_TEXT} "{PLANET_NAME}" <a href="galaxy.php?mode=0&galaxy={PLANET_GALAXY}&system={PLANET_SYSTEM}">[{PLANET_GALAXY}:{PLANET_SYSTEM}:{PLANET_PLANET}]</a></div>
        <div class="fr"><a href="overview.php?mode=manage"><font color="green">[{L_ov_manage}]</font></a></td></div>
      </td>
    </tr>

    <!-- IF GATE_LEVEL -->
    <tr>
      <th colspan="2">{L_tech[43]}</th>
      <td class="c_c" colspan="2"><a href="jumpgate.php" id="gate_jump_time">
        <!-- IF GATE_JUMP_REST_TIME -->
        <script type="text/javascript"><!--
          sn_timers.unshift({id: 'gate_jump_time', type: 0, active: true, start_time: '{TIME_NOW}', options: {msg_done: '{L_gate_ready}', que: [['1', '', {GATE_JUMP_REST_TIME}, '1']]}});
        // --></script>
        <!-- ELSE -->
          {L_gate_ready}
        <!-- ENDIF --></a>
      </td>
    </tr>
    <!-- ENDIF -->

    <tr>
      <th width=100 align="center">{buildings_on_planet}</th>
      <th colspan="3" style="position: relative; border: 1px solid rgb(153, 153, 255); padding: 0px; height: 100%;">
        <div style="position: relative; width: 100%; height: 100%;">
          <!-- IF PLANET_FILL >= 100 -->
            <!-- DEFINE $BAR_COLOR = '#C00000' -->
          <!-- ELSEIF PLANET_FILL >= 80 -->
            <!-- DEFINE $BAR_COLOR = '#C0C000' -->
          <!-- ELSE -->
            <!-- DEFINE $BAR_COLOR = '#00C000' -->
          <!-- ENDIF -->
          <div style="position: absolute; top: 0px; left: 0px; width: {PLANET_FILL_BAR}%; background-color: {$BAR_COLOR}; height:100%;">&nbsp;</div>
          <div style="position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; vertical-align: middle;">{planet_field_current}/{planet_field_max} ({PLANET_FILL}%)</div>
          &nbsp;
        </div>
      </th>
    </tr>

    <tr>
      <th width=100>{orb}</th>
      <th colspan="3">{L_sys_metal}: {metal_debris} / {L_sys_crystal}: {crystal_debris}
        <!-- IF RECYCLERS_SEND -->
          <br><span id="ov_recycle" style="cursor: pointer" onclick="doit(8, {PLANET_GALAXY}, {PLANET_SYSTEM}, {PLANET_PLANET}, {PLANET_TYPE}, {RECYCLERS_SEND});">{L_type_mission[8]}</span>
        <!-- ENDIF -->
      </th>
    </tr>
    <tr>
      <th>{Diameter}</th>
      <th>{planet_diameter} {km}</th>
      <th>{Temperature}</th>
      <th>{planet_temp_min}&deg;C / {planet_temp_max}&deg;C</th>
    </tr>

    <tr><td colspan=4 class="c">{L_Planet_menu}</td></tr>
    <!-- BEGIN ques -->
    <tr>
      <th width=100>
        <!-- IF ques.LENGTH -->
        <div id="ov_{ques.ID}"></div>
        <!-- DEFINE $QUE_ID = '{ques.ID}' -->
        <!-- INCLUDE eco_queue.tpl -->
        <!-- ELSE -->
        {ques.NAME}<br>
        <!-- ENDIF -->
        <div id="ov_{ques.ID}_timer" style="color: lime"></div>
        <div id="ov_{ques.ID}_total" style="color: red"></div>
      </th>
      <th colspan=3 id="ov_{ques.ID}_que">
        {L_eco_que_empty}
      </th>
    </tr>
    <!-- END ques -->
  </table>

  <!-- INCLUDE planet_fleet_list.tpl -->
</th>

<th valign=top class="tr">
  <table border="0" cellspacing=0 cellpadding=0 width="200">
    <!-- INCLUDE planet_list.tpl -->
  </table>
</th>
</tr>

</table>

<div id="admin_message"></div>

<script type="text/javascript"><!--
jQuery(document).bind("ready", function() {
  // send requests
  jQuery.post("scheduler.php", {rating: jQuery(this).html()}, function(xml) {
    // format result
    var result = [ jQuery("message", xml).text() ];
    // output result
    jQuery("#admin_message").html(result.join(""));
  } );
});
--></script> 
