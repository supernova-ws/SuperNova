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

<br>
<table border='0' cellpadding='0' cellspacing='1'>
  <tr height='20' class='c'>
    <td colspan=8 class='c'><div style="float: left">{fl_title}: {FLEETS_FLYING}/{FLEETS_MAX}</div><div align=right>{fl_expttl}: {EXPEDITIONS_FLYING}/{EXPEDITIONS_MAX}</div></td>
  </tr>

  <tr height='20'>
    <th>{L_fl_id}</th>
    <th>{L_fl_mission}</th>
    <th>{L_fl_count_short}</th>
    <th>{L_fl_dest}</th>
    <th>{L_fl_dest_t}</th>
    <th>{L_fl_from}</th>
    <th>{L_fl_from_t}</th>
    <th>{L_fl_order}</th>
  </tr>

  <!-- BEGIN fleets -->
    <!-- IF fleets.MISSION == 1 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'attack' -->
    <!-- ELSEIF fleets.MISSION ==  2 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'federation' -->
    <!-- ELSEIF fleets.MISSION ==  3 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'transport' -->
    <!-- ELSEIF fleets.MISSION ==  4 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'deploy' -->
    <!-- ELSEIF fleets.MISSION ==  5 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'hold' -->
    <!-- ELSEIF fleets.MISSION ==  6 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'espionage' -->
    <!-- ELSEIF fleets.MISSION ==  7 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'colony' -->
    <!-- ELSEIF fleets.MISSION ==  8 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'harvest' -->
    <!-- ELSEIF fleets.MISSION ==  9 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'destroy' -->
    <!-- ELSEIF fleets.MISSION == 10 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'missile' -->
    <!-- ELSEIF fleets.MISSION == 15 -->
      <!-- DEFINE $OV_FLEET_STYLE = 'expedition' -->
    <!-- ENDIF -->

    <tr height=20 class="own{$OV_FLEET_STYLE}">
      <th>{fleets.NUMBER}</th>
      <th>{fleets.MISSION_NAME}<br>
        <div class="z"><!-- IF fleets.MESSAGE -->{L_ov_fleet_return}<!-- ELSE -->{L_ov_fleet_arrive}<!-- ENDIF --></div>
      </th>
      <th style="cursor: pointer;" onmouseover='fleet_dialog_show(this, {fleets.ID})' onmouseout='popup_hide()'>{fleets.AMOUNT}</th>
      <th>{fleets.END_URL} {fleets.END_TYPE_TEXT_SH}</th>
      <th><!-- IF fleets.MESSAGE == 0 --><div class="z" id="fleet_timer_end{fleets.ID}"></div>{fleets.END_TIME_TEXT}<!-- ELSE -->-<!-- ENDIF --></th>
      <th>{fleets.START_URL} {fleets.START_TYPE_TEXT_SH}</th>
      <th>
        <!-- IF ((fleets.MISSION == 7 || fleets.MISSION == 4) && fleets.MESSAGE == 1) || (fleets.MISSION != 7 && fleets.MISSION != 4) -->
        <div class="z" id="fleet_timer_start{fleets.ID}"></div>{fleets.START_TIME_TEXT}
        <!-- ELSE -->-<!-- ENDIF -->
      </th>
      <th>
        <!-- IF fleets.MESSAGE == 0 -->
          <form action="" method="post">
            <input name="fleet_id" value="{fleets.ID}" type="hidden">
            <input value="{L_fl_back_to_ttl}" type="submit" name="return_fleet">
          </form>
          <!-- IF fleets.MISSION == 1 || fleets.MISSION == 2 -->
            <form action="fleet.php?fleet_page=4" method="post" style="margin: 0">
              <input name="fleetid" value="{fleets.ID}" type="hidden">
              <input value="<!-- IF fleets.MISSION == 1 -->{L_fl_associate}<!-- ELSE -->{fleets.ACS}<!-- ENDIF -->" type="submit">
            </form>
          <!-- ENDIF -->
        <!-- ELSE -->
          {L_fl_isback}
        <!-- ENDIF -->
      </th>
    </tr>
    <script type="text/javascript"><!--
      sn_timers.unshift({id: 'fleet_timer_start{fleets.ID}', type: 0, active: true, start_time: {TIME_NOW}, options: { msg_done: '{L_sys_fleet_arrived}', que: [
        ['{fleets.ID}', '', {fleets.START_LEFT}, '0']
      ]}});
      sn_timers.unshift({id: 'fleet_timer_end{fleets.ID}', type: 0, active: true, start_time: {TIME_NOW}, options: { msg_done: '{L_sys_fleet_arrived}', que: [
        ['{fleets.ID}', '', {fleets.END_LEFT}, '0']
      ]}});
    --></script>
  <!-- BEGINELSE fleets -->
    <tr><th colspan="8">{L_fl_no_flying_fleets}</th></tr>
  <!-- END fleets -->

  <!-- IF FLEETS_FLYING >= FLEETS_MAX -->
    <tr><th class="c_c negative" colspan="8">{fl_noslotfree}</th></tr>
  <!-- ELSE -->
    <tr><th class="c_c" colspan="8"><input type="button" value="{L_flt_gather_all}" onclick="document.location = 'fleet.php?fleet_page=5';"></th></tr>
  <!-- ENDIF -->
</table><br>

<!-- INCLUDE page_hint.tpl -->
