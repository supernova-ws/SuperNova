<!--
@package fleet
2.1 Overlib replaced with jQuery
2.0 Utilize PTE
    Comply with PCG
-->

<script type="text/javascript"><!--
var ships = Array();
<!-- BEGIN ships -->
  <!-- IF (ships.SPEED > 0) -->
  ships[{ships.ID}] = Array({ships.AMOUNT}, {ships.SPEED}, {ships.CONSUMPTION}, {ships.CAPACITY});
  <!-- ENDIF -->
<!-- END ships -->
var speed_factor   = {speed_factor};
--></script>

<!-- INCLUDE fleet_javascript.tpl -->

<h2>{fl_new_miss}</h2>
<!-- IF MISSION_NAME -->
  <h3>{TYPE_NAME} [{galaxy}:{system}:{planet}], {MISSION_NAME}</h3>
<!-- ENDIF -->
<form action="fleet.php?fleet_page=1" method="post">
  <input type="hidden" name="thisgalaxy"      value="{thisgalaxy}" />
  <input type="hidden" name="thissystem"      value="{thissystem}" />
  <input type="hidden" name="thisplanet"      value="{thisplanet}" />
  <input type="hidden" name="thisplanet_type" value="{thisplanet_type}" />

  <input type="hidden" name="galaxy" value="{galaxy}">
  <input type="hidden" name="system" value="{system}">
  <input type="hidden" name="planet" value="{planet}">
  <input type="hidden" name="planet_type" value="{planet_type}">
  <input type="hidden" name="target_mission" value="{target_mission}">

  <table border="0" cellpadding="0" cellspacing="1">
    <tr class="c_c">
      <th>{fl_fleet_typ}</th>
      <th>{L_fl_orbiting}</th>
      <th>{L_fl_to_fly}</th>
    </tr>

    {ErrorNoPlanetRow}
    
    <!-- IF .ships -->
      <!-- BEGIN ships -->
        <tr>
          <th><div class="fl"><a href="infos.php?gid={ships.ID}" title="{L_fl_fleetspeed}{ships.SPEED}"</a>{ships.NAME}</div></th>
          <th>{ships.AMOUNT}</th>
          <th>
            <!-- IF (ships.SPEED == 0) || (FLYING_FLEETS >= MAX_FLEETS) -->
              &nbsp;
            <!-- ELSE -->
              <script type="text/javascript"><!--
                sn_ainput_make('ships[{ships.ID}]', {max: ships[{ships.ID}][0]});

                jQuery('#ships{ships.ID}slide').bind('slide slidechange', fl_calc_stats);
              --></script>
            <!-- ENDIF -->
          </th>
        </tr>
      <!-- END ships -->
      <!-- IF FLYING_FLEETS < MAX_FLEETS -->
        <tr class="c_c">
          <th><input type="button" value="{L_flt_gather_all}" onclick="document.location = 'fleet.php?fleet_page=5';"></th>
          <th><input type="submit" value="{L_fl_continue}" /></th>
          <th>
              <div class="fl"><input type="button" value="{fl_unselectall}" onclick="javascript:zero_fleet();"></div>
              <div class="fr"><input type="button" value="{fl_selectall}" onclick="javascript:max_fleet();"></div>
            <div class="fr"></div>
          </th>
        </tr>
      <!-- ELSE -->
        <tr>
          <th colspan="3"><font color="red">{fl_noslotfree}</font></th>
        </tr>
      <!-- ENDIF -->
    <!-- ELSE -->
      <tr><th colspan="3">{L_fl_noships}</th></tr>
    <!-- ENDIF -->
  </table>
</form>
<!-- IF FLYING_FLEETS < MAX_FLEETS && .ships -->
  <br>
  <table>
    <tr><td class="c" colspan=2>{L_fl_fleet_data}</td></tr>
    <tr>
        <th>{L_fl_speed}</th>
        <th width="100"><span id='int_fleet_speed'>-</span></th>
    </tr>
    <tr>
        <th>{L_fl_dist}</th>
        <th><span id='distance'>0</span></th>
    </tr>
    <tr>
        <th>{L_fl_fltime}</th>
        <th><span id='duration'>-</span></th>
    </tr>
    <tr>
        <th>{L_fl_deute_need}</th>
        <th><span id='int_fleet_consumption'>0</span></th>
    </tr>
    <tr>
        <th>{L_sys_capacity}</th>
        <th><span id='int_fleet_capacity'>0</span></th>
    </tr>
    <tr>
        <th>{L_fl_planet_resources}</th>
        <th>{PLANET_RESOURCES}</th>
    </tr>
  </table>
<!-- ENDIF -->
<!-- INCLUDE page_hint.tpl -->
