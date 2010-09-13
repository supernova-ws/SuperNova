<!-- INCLUDE fleet_javascript.tpl -->

<script type="text/javascript"> 
  jQuery.noConflict(); 
</script> 
<style type="text/css"><!--
.style1 {color: #FF0000}
.style2 {color: #FFFF00}
.style5 {color: #00FF00; font-weight: bold; }
.style3 {color: #FFFFFF}
.style4 {color: #FF0000; font-weight: bold; }
.style6 {color: #FFFF00; font-weight: bold; }
.style7 {color: #00FFFF}
--></style>
<br>
<table><tr><th valign=top class="tr">
  <table width="519">
    <tr>
      <th colspan=2 style="position: relative;">
        <div style="position: relative; width: 100%; height: 100%;">
          <div style="position: absolute; left: 5%;">{TIME_TEXT} <span id="ov_time">00:00:00</span></div>
          <div style="position: absolute; right: 5%; align: center;">{MembersOnline} <a href="admin/overview.php" title="{MembersOnline2}">[{USERS_ONLINE}/{USERS_TOTAL}]</a></div>
          &nbsp;
        </div>
      </th>
    </tr>

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
      <tr><th colspan=2><a href=officier.php>{L_ov_rpg_new_level_miner}</a></th></tr>
    <!-- ENDIF -->
    
    <!-- IF NEW_LEVEL_RAID -->
      <tr><th colspan=2><a href=officier.php>{L_ov_rpg_new_level_raid}</a></th></tr>
    <!-- ENDIF -->
    
    <!-- News Frame -->
    <!-- IF GAME_NEWS_OVERVIEW -->
      <tr><td colspan=2 class="c">{L_ov_news_title}</td></tr>
      <!-- BEGIN news -->
        <tr>
          <th width="90">
            <!-- IF news.IS_NEW -->
              <font color=red>{L_ov_new}</font><br>
            <!-- ENDIF -->
            <font color=cyan>{news.TIME}</font>
          </th>
          <th valign=top>
            <div align=justify>{news.ANNOUNCE}</div>
          </th>
        </tr>
      <!-- BEGINELSE news -->
        <tr><th colspan="2" class="c">{L_ov_news_none}</th></tr>
      <!-- END news -->
    <!-- ENDIF -->
  </table>    

  <table width="519">
    <!-- <tr><td colspan="5" class="c">{L_ov_fleet_list}</td></tr> -->
    <tr align="center">
      <td class="c">{L_ov_time}</td>
      <td class="c">{L_ov_fleet}</td>
      <td class="c">{L_ov_destination}</td>
      <td class="c">{L_ov_source}</td>
      <td class="c">{L_ov_mission}</td>
    </tr>

    <!-- BEGIN fleets -->
      <!-- IF fleets.S_FIRST_ROW -->
        <!-- IF fleets.OV_THIS_PLANET -->
          <tr><th colspan="5" class="c">{L_ov_flying_fleets} {PLANET_NAME} [{PLANET_GALAXY}:{PLANET_SYSTEM}:{PLANET_PLANET}]</th></tr>
        <!-- ENDIF -->
        <!-- DEFINE $THIS_PLANET = 1 -->
      <!-- ENDIF -->

      <!-- IF $THIS_PLANET == 1 && fleets.OV_THIS_PLANET != 1 -->
        <tr><th colspan="5" class="c">{L_ov_flying_fleets} {L_ov_other_planets}</th></tr>
        <!-- DEFINE $THIS_PLANET = 2 -->
      <!-- ENDIF -->
      
      <!-- IF fleets.OV_LABEL == 0 -->
        <!-- DEFINE $OV_FLEET_ACTION = 'flight' -->
      <!-- ELSEIF fleets.OV_LABEL == 1 -->
        <!-- DEFINE $OV_FLEET_ACTION = 'holding' -->
      <!-- ELSEIF fleets.OV_LABEL == 2 -->
        <!-- DEFINE $OV_FLEET_ACTION = 'return' -->
      <!-- ENDIF -->

      <!-- IF USER_ID == fleets.OWNER -->
        <!-- DEFINE $OV_FLEET_PREFIX = 'own' -->
      <!-- ELSE -->
        <!-- DEFINE $OV_FLEET_PREFIX = '' -->
      <!-- ENDIF -->

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
        <!-- DEFINE $OV_FLEET_STYLE = 'transport' -->
      <!-- ENDIF -->

      <tr class="{$OV_FLEET_ACTION} {$OV_FLEET_PREFIX}{$OV_FLEET_STYLE}">
        <th width=70>
          <div id="ov_fleer_timer_{$OV_FLEET_ACTION}{fleets.ID}" class="z">00:00:00</div>
          {fleets.OV_TIME_TEXT}
        </th>
        <th style="cursor: pointer;" onmouseover='fleet_dialog_show(this, {fleets.ID})' onmouseout='fleet_dialog_hide()'>
          {fleets.AMOUNT}
        </th>
        <!-- IF fleets.OV_LABEL == 0 || fleets.OV_LABEL == 1  || fleets.OV_LABEL == 3 -->
          <th>
            {fleets.END_NAME}<br>
            {fleets.END_URL} {fleets.END_TYPE_TEXT_SH}
          </th>
          <th>
            {fleets.START_NAME}<br>
            {fleets.START_URL} {fleets.START_TYPE_TEXT_SH}
          </th>
        <!-- ELSEIF fleets.OV_LABEL == 2 -->
          <th>
            {fleets.START_NAME}<br>
            {fleets.START_URL} {fleets.START_TYPE_TEXT_SH}
          </th>
          <th>
            {fleets.END_NAME}<br>
            {fleets.END_URL} {fleets.END_TYPE_TEXT_SH}
          </th>
        <!-- ENDIF -->
        <th>
          {fleets.MISSION_NAME}<br>
          <!-- IF fleets.OV_LABEL == 0 -->{L_ov_fleet_arrive}<!-- ELSEIF fleets.OV_LABEL == 1 -->{fleets.MISSION_NAME} - {L_ov_fleet_hold}<!-- ELSEIF fleets.OV_LABEL == 2 -->{L_ov_fleet_return}<!-- ELSEIF fleets.OV_LABEL == 3 -->{L_ov_fleet_rocket}<!-- ENDIF -->
        </th>
      </tr>

      <script type="text/javascript"><!--
        sn_timers.unshift(['ov_fleer_timer_{$OV_FLEET_ACTION}{fleets.ID}', 0, true, {TIME_NOW}, ['{L_sys_fleet_arrived}',[
          ['{fleets.ID}', '', {fleets.OV_LEFT}, '0']
        ]]]);
      --></script>
    <!-- BEGINELSE fleets -->
      <tr><th colspan=5>{L_ov_fleet_no_flying}</th></tr>
    <!-- END fleets -->
  </table>

  <table width="519">
    <tr><td style="white-space: nowrap;" class="c" colspan=4>
        <div class="fl">{PLANET_TYPE_TEXT} "{PLANET_NAME}" <a href="galaxy.php?mode=0&galaxy={PLANET_GALAXY}&system={PLANET_SYSTEM}">[{PLANET_GALAXY}:{PLANET_SYSTEM}:{PLANET_PLANET}]</a></div>
        <div class="fr"><a href="overview.php?mode=renameplanet"><font color="green">[{L_ov_operations}]</font></a></td></div>
    </td></tr>
    <tr><th width=100>{L_ov_building}</th><th colspan=3><!-- IF BUILDING -->{BUILDING}<span id="ov_building"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>
    <tr><th>{L_ov_hangar}</th><th colspan="3"><!-- IF HANGAR -->{HANGAR}<span id="ov_hangar"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>
    <tr><th>{Teching}</th><th colspan="3"><!-- IF TECH -->{TECH}<span id="ov_tech"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>


    <tr><td class="c" colspan=4>{L_ov_planet_details}</td></tr>
    
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
        <!-- IF CAN_RECYCLE -->
          <br>(<a href="quickfleet.php?mode=8&g={PLANET_GALAXY}&s={PLANET_SYSTEM}&p={PLANET_PLANET}&t=2">{L_type_mission[8]}</a>)
        <!-- ENDIF -->
      </th>
    </tr>
    <tr>
      <th>{Diameter}</th>
      <th>{planet_diameter} {km}</th>
      <th>{Temperature}</th>
      <th>{planet_temp_min}&deg;C / {planet_temp_max}&deg;C</th>
    </tr>
  </table>


  <table width=519>
    <tr>
      <td colspan=4 class="c">
        <span class="fl">{L_ov_player_rpg} {user_username}</span>
        <span class="fr"><strong>{L_ov_rank}: <a href="stat.php?start={user_rank}"><span class="style2">{user_rank} ({RANK_DIFF})</span> из <span class="style1">{USERS_TOTAL}</span></strong></a></strong></span>
      </td>
    </tr>
    <tr>
      <th rowspan=2>{L_ov_raids}</th>
      <th colspan="1" align="center">{L_ov_level}: {raid_lvl}</th>
      <th colspan="2" align="center">{L_ov_experience}: {raid_xp}/{raid_lvl_up}</th>
    </tr>
    <tr>
      <th>{NumberOfRaids}: {raids}</th>
      <th>{RaidsWin}: {raidswin}</th>
      <th>{RaidsLoose}: {raidsloose}</th>
    </tr>
    <tr>
      <th>{Economica}</th>
      <th colspan="1" align="center">{L_ov_level}: {builder_lvl}</th>
      <th colspan="2" align="center">{L_ov_experience}: {builder_xp}/{builder_lvl_up}</th>
    </tr>
    <tr align="center" class="c">
      <th rowspan=2>{L_ov_points}</th>
      <th>{user_points_1}: {user_points}</th>
      <th>{user_points_3}: {user_defs_points}</th>
      <th>{user_points_4}: {user_fleet}</th>
    </tr>
    <tr>
      <th>{user_points_2}: {player_points_tech}</th>
      <th colspan="2">{user_points_all}: {total_points}</th>
    </tr>
  </table>

  <!-- IF BANNER_URL -->
    <table width="519" align="center">
      <tr><td class="c">{L_ov_banner}</td></tr>
      <tr>
        <th>
          <img src="{BANNER_URL}"><br><br>
          {L_sys_banner_bb}<br>
          <input type="text" value="[img]{BANNER_URL}[/img]" size="55">
        </th>
      </tr>
    </table>
  <!-- ENDIF -->

  <!-- IF USERBAR_URL -->
    <table width="519" align="center">
      <tr><td class="c">{L_ov_userbar}</td></tr>
      <tr>
        <th>
          <img src="{USERBAR_URL}"><br><br>
          {L_sys_userbar_bb}<br>
          <input type="text" value="[img]{USERBAR_URL}[/img]" size="55">
        </th>
      </tr>
    </table>
  <!-- ENDIF -->
</th>

<th valign=top class="tr">
  <table border="0" cellspacing=0 cellpadding=0 width="200">
    <!-- DEFINE $OVERVIEW = true -->
    <!-- INCLUDE planet_list.tpl -->
  </table>
</th>
</tr>

</table>

{L_copyright} {ADMIN_EMAIL}<br>

<div id="admin_message"></div>

<script type="text/javascript"><!--
jQuery(document).ready(function() {
  // send requests
  jQuery.post("../scheduler.php", {rating: jQuery(this).html()}, function(xml) {
    // format result
    var result = [ jQuery("message", xml).text() ];
    // output result
    jQuery("#admin_message").html(result.join(""));
  } );
});

sn_timers.unshift(['ov_time', 2, true, {TIME_NOW}, 2]);
--></script> 
