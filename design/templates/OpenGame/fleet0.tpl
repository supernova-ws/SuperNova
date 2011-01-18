<!--
@package fleet
2.1 Overlib replaced with jQuery
2.0 Utilize PTE
    Comply with PCG
-->

<script type="text/javascript"><!--
var ships = Array();
<!-- BEGIN ships -->
  <!-- IF (ships.ID != 212) -->
  ships[{ships.ID}] = Array({ships.AMOUNT}, {ships.SPEED}, {ships.CONSUMPTION}, {ships.CAPACITY});
  <!-- ENDIF -->
<!-- END ships -->
var speed_factor   = {speed_factor};
--></script>

<!-- INCLUDE fleet_javascript.tpl -->

<br>
{vacation}
<table border='0' cellpadding='0' cellspacing='1'>
  <tr height='20' class='c'>
    <td colspan=8 class='c'><div style="float: left">{fl_title}: {FLYING_FLEETS}/{MAX_FLEETS}</div><div align=right>{fl_expttl}: {ExpeditionEnCours}/{EnvoiMaxExpedition}</div></td>
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
            <input name="fleetid" value="{fleets.ID}" type="hidden">
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

  <!-- IF FLYING_FLEETS >= MAX_FLEETS -->
    <tr><td class="c" colspan="8" align="center"><font color="red">{fl_noslotfree}</font></td></tr>
  <!-- ELSE -->
    <tr><td class="c" colspan="8" align="center"><input type="button" value="{L_flt_gather_all}" onclick="document.location = 'fleet.php?fleet_page=5';"></th></tr>
  <!-- ENDIF -->
</table><br>

{AKS}

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
    <tr><td colspan="3" class="c">
      <div class="fl">{fl_new_miss}</div>
      <!-- IF MISSION_NAME -->
        <div class="fr">{TYPE_NAME} [{galaxy}:{system}:{planet}], {MISSION_NAME}</div>
      <!-- ENDIF -->
    </td></tr>

    <tr>
      <td class="c">{fl_fleet_typ}</td>
      <td class="c">{L_fl_orbiting}</rd>
      <td class="c">{L_fl_to_fly}</td>
    </tr>

    {ErrorNoPlanetRow}
    
    <!-- IF .ships -->
      <!-- BEGIN ships -->
        <tr>
          <th><div class="fl"><a href="infos.php?gid={ships.ID}" title="{L_fl_fleetspeed}{ships.SPEED}"</a>{ships.NAME}</div></th>
          <th>{ships.AMOUNT}</th>
          <th>
            <!-- IF (ships.ID == 212) || (FLYING_FLEETS >= MAX_FLEETS) -->
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
      <tr>
          <th colspan="2">&nbsp;</th>
          <th>
              <div class="fl"><input type="button" value="{fl_unselectall}" onclick="javascript:zero_fleet();"></div>
              <div class="fr"><input type="button" value="{fl_selectall}" onclick="javascript:max_fleet();"></div>
            <div class="fr"></div>
          </th>
      </tr>
      <tr>
          <th colspan="3">
          <input type="submit" value="{L_fl_continue}" />
          </th>
      </tr>
        <!-- ELSE -->
      <tr>
          <th colspan="3"><font color="red">{fl_noslotfree}</font></th>
      </tr>
        <!-- ENDIF -->
      </tr>
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
