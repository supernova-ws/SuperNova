<!-- INCLUDE fleet_javascript.tpl -->

<form action="fleet.php?fleet_page=2" method="post">
  <input type="hidden" name="thisgalaxy"      value="{thisgalaxy}" />
  <input type="hidden" name="thissystem"      value="{thissystem}" />
  <input type="hidden" name="thisplanet"      value="{thisplanet}" />
  <input type="hidden" name="thisplanet_type" value="{thisplanet_type}" />

  <input type="hidden" name="target_mission"  value="{target_mission}" />

  <input type="hidden" name="usedfleet"       value="{usedfleet}" />
  <br>

  <table width="519" border="0" cellpadding="0" cellspacing="1">
    <tr><td colspan="2" class="c">{fl_floten1_ttl}</td></tr>
    <tr>
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
    
    <tr>
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

    <tr>
      <th>{fl_dist}</th>
      <th><div id="distance">-</div></th>
    </tr>
    
    <tr>
      <th>{L_fl_fltime}</th>
      <th><div id="duration">-</div></th>
    </tr>
    
    <tr>
      <th>{L_fl_dest_t}</th>
      <th><div id="time_dst">-</div></th>
    </tr>
    
    <tr>
      <th>{L_fl_back_t}</th>
      <th><div id="time_src">-</div></th>
    </tr>
    
    <tr>
      <th>{L_fl_deute_need}</th>
      <th><div id="consumption">-</div></th>
    </tr>
    
    <tr>
      <th>{L_fl_speed_max}</th>
      <th><div id="fleet_speed">0</div></th>
    </tr>
    
    <tr>
      <th>{L_sys_capacity}</th>
      <th><div id="capacity">-</div></th>
    </tr>
    
    <tr><th colspan="2"><input type="submit" value="{fl_continue}" /></th></tr>

    <tr><td colspan=2 class=c><div class="fl">{fl_shortcut}</div><a href=fleetshortcut.php class="fr">{fl_shortlnk}</a></td></tr>
    <tr>
      <!-- BEGIN shortcuts -->       
        <th>
          <a href="javascript:setTarget({shortcuts.GALAXY}, {shortcuts.SYSTEM}, {shortcuts.PLANET}, {shortcuts.TYPE}); shortInfo();">
           {shortcuts.NAME} [{shortcuts.GALAXY}:{shortcuts.SYSTEM}:{shortcuts.PLANET}] {shortcuts.TYPE_PRINT}</a>
        </th>
        <!-- IF shortcuts.S_ROW_COUNT is odd -->
          </tr><tr>
        <!-- ELSEIF shortcuts.S_LAST_ROW -->
          <th>&nbsp;</th>
        <!-- ENDIF -->
      <!-- BEGINELSE shortcuts -->
        <th colspan="2">{fl_noshortc}</th>
      <!-- END shortcuts -->
    </tr>

    <tr><td colspan=2 class=c>{fl_myplanets}</td></tr>
    <tr>
      <!-- BEGIN colonies -->       
        <th>
          <a href="javascript:setTarget({colonies.GALAXY}, {colonies.SYSTEM}, {colonies.PLANET}, {colonies.TYPE}); shortInfo();">
           {colonies.NAME} [{colonies.GALAXY}:{colonies.SYSTEM}:{colonies.PLANET}] {colonies.TYPE_PRINT}</a>
        </th>
        <!-- IF colonies.S_ROW_COUNT is odd -->
          </tr><tr>
        <!-- ELSEIF colonies.S_LAST_ROW -->
          <th>&nbsp;</th>
        <!-- ENDIF -->
      <!-- BEGINELSE colonies -->
        <th colspan="2">{fl_nocolonies}</th>
      <!-- END colonies -->
    </tr>

    <tr><td colspan=2 class=c>{fl_grattack}</td></tr>
    <tr>
      <!-- BEGIN acss -->       
        <th>
          <input type="button" onClick="javascript: setTarget({acss.GALAXY},{acss.SYSTEM},{acss.PLANET},{acss.TYPE});shortInfo();setACS({acss.ID});setACS_target('g{acss.GALAXY}s{acss.SYSTEM}p{acss.PLANET}t{acss.TYPE}');" value="{acss.NAME} [{acss.GALAXY}:{acss.SYSTEM}:{acss.PLANET}] {acss.TYPE_PRINT}">
        </th>
        <!-- IF acss.S_ROW_COUNT is odd -->
          </tr><tr>
        <!-- ELSEIF acss.S_LAST_ROW -->
          <th>&nbsp;</th>
        <!-- ENDIF -->
      <!-- BEGINELSE acss -->
        <th colspan="2">{fl_noacss}</th>
      <!-- END acss -->
     </tr>
    
  </table>
  <!-- INCLUDE page_hint.tpl -->
</form>

<script type="text/javascript"><!--
var fleet_capacity = {fleet_capacity};
var fleet_speed    = {fleet_speed};
var speed_factor   = {speed_factor};

document.getElementById('fleet_speed').innerHTML = sn_format_number(fleet_speed);

var ships = Array();
<!-- BEGIN ships -->
  <!-- IF (ships.SPEED > 0) -->
  ships[{ships.ID}] = Array({ships.AMOUNT}, {ships.SPEED}, {ships.CONSUMPTION}, 0);
  <!-- ENDIF -->
<!-- END ships -->

javascript:shortInfo(); 
--></script>
