<!-- INCLUDE fleet_javascript.tpl -->

<script language="JavaScript" type="text/javascript" src="scripts/time.js"></script>
<script type="text/javascript"> 
  jQuery.noConflict(); 
  setTimeout("HeureCheck()", 1000);
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
    <tr><td style="white-space: nowrap;" class="c" colspan=4>
        <div class="fl">{Planet} "{planet_name}" <a href="galaxy.php?mode=0&galaxy={galaxy_galaxy}&system={galaxy_system}">[{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}]</a></div>
        <div class="fr"><a href="overview.php?mode=renameplanet"><font color="green">[{L_ov_operations}]</font></a></td></div>
    </td></tr>
    {Have_new_message}
    <!-- IF NEW_LEVEL_MINER --><tr><th colspan=4><a href=officier.php>{L_ov_rpg_new_level_miner}</a></th></tr><!-- ENDIF -->
    <!-- IF NEW_LEVEL_RAID --><tr><th colspan=4><a href=officier.php>{L_ov_rpg_new_level_raid}</a></th></tr><!-- ENDIF -->
    <tr>
      <th width="100">{Server_time}</th>
      <th colspan="3" style="white-space: nowrap;">{time}<span id="dateheure">00:00:00</span></th>
    </tr>
    <tr>
      <th>{MembersOnline}</th>
      <th colspan="3"><a href="admin/overview.php" title="{MembersOnline2}">[{NumberMembersOnline}]</a></th>
    </tr>
    {NewsFrame}
    
    <tr><td colspan="4" class="c">{L_ov_fleet_list}</td></tr>

    <!-- BEGIN fleets -->

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

      <tr class="{$OV_FLEET_ACTION}">
        {fleet_javai}
        <th>
          <div id="ov_fleer_timer_{$OV_FLEET_ACTION}{fleets.ID}" class="z">00:00:00</div>
          <font color="lime">{fleets.OV_TIME_TEXT}</font>
        </th>
        <th colspan="3">
          <span class="{$OV_FLEET_ACTION} {$OV_FLEET_PREFIX}{$OV_FLEET_STYLE}">
            <span style="cursor: pointer; font-weight: bold; text-decoration: underline;" onmouseover='fleet_dialog_show(this, {fleets.ID})' onmouseout='fleet_dialog_hide()'>
              <!-- IF USER_ID == fleets.OWNER -->
                {L_ov_fleet_yours}
              <!-- ELSE -->
                {L_ov_fleet_hostile}
              <!-- ENDIF -->
            {L_ov_fleet}</span>{L_ov_fleet_sent}
            {fleets.START_NAME} {fleets.START_URL} {fleets.START_TYPE_TEXT_SH}
            {L_ov_fleet_sent_to} {fleets.END_NAME} {fleets.END_URL} {fleets.END_TYPE_TEXT_SH} {L_ov_fleet_mission} <font color="white">{fleets.MISSION_NAME}</font>
            <!-- IF fleets.OV_LABEL == 0 -->{L_ov_fleet_arrive}<!-- ELSEIF fleets.OV_LABEL == 1 -->{L_ov_fleet_hold}<!-- ELSEIF fleets.OV_LABEL == 2 -->{L_ov_fleet_return}<!-- ENDIF -->
          </span>
        </th>
        <script type="text/javascript"><!--
          sn_timers.unshift(['ov_fleer_timer_{$OV_FLEET_ACTION}{fleets.ID}', 0, true, {TIME_NOW}, ['{L_sys_fleet_arrived}',[
            ['{fleets.ID}', '', {fleets.OV_LEFT}, '0']
          ]]]);
        --></script>
      </tr>
    <!-- BEGINELSE fleets -->
      <tr><th colspan=4>{L_ov_fleet_no_flying}</th></tr>
    <!-- END fleets -->

    
    {fleet_list}

    <tr><td colspan="4" class="c">{L_Planet_menu}</td></tr>
    <tr><th width=90>{L_ov_building}</th><th colspan=3><!-- IF BUILDING -->{BUILDING}<span id="ov_building"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>
    <tr><th>{L_ov_hangar}</th><th colspan="3"><!-- IF HANGAR -->{HANGAR}<span id="ov_hangar"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>
    <tr><th>{Teching}</th><th colspan="3"><!-- IF TECH -->{TECH}<span id="ov_tech"></span><!-- ELSE -->{L_Free}<!-- ENDIF --></th></tr>

    <tr><td class="c" colspan=4>{L_ov_planet_details}</td></tr>
    <tr>
      <th>{Diameter}</th>
      <th>{planet_diameter} {km}</th>
      <th>{Temperature}</th>
      <th>{planet_temp_min}&deg;C / {planet_temp_max}&deg;C</th>
    </tr>
    <tr>
      <th align="center">{buildings_on_planet}</th>
      <th colspan="3" align="center"  style="white-space: nowrap;">
        <font color="#CCF19F">{planet_field_current} / {planet_field_max} {Points_1}</font><br />
        <div align=left style="border: 1px solid rgb(153, 153, 255); width: 400px;">
          <div id="CaseBarre" align=center style="position: relative; left: 0px; background-color: {case_barre_barcolor}; width: {case_barre}px;">{case_pourcentage}</div>
        </div>
      </th>
    </tr>
    <tr>
      <th>{orb}</th>
      <th colspan="3">{L_sys_metal}: {metal_debris} / {L_sys_crystal}: {crystal_debris}{get_link}</th>
    </tr>
  </table>

  <table width=519>
    <tr><td colspan=4 class="c">
        <span class="fl">{L_ov_player_rpg} {user_username}</span>
        <span class="fr"><b>{L_ov_rank}: <a href="stat.php?start={u_user_rank}"><strong><span class="style2">{user_rank}</span> из <span class="style1">{max_users}</span></strong></a></b></span>
    </td></tr>
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
  </table>

  <!-- IF bannerframe || userbarframe -->
  <table width="519" align="center">
    <tr><td class="c">{L_ov_banner_and_userbar}</td></tr>
    {bannerframe}
    {userbarframe}
  </table>
  <!-- ENDIF -->
</th>

<th valign=top class="tr">
  <table border="0" cellspacing=0 cellpadding=0 width="200"><!-- BEGIN planet -->
    <tr><th class="tr"><!-- IF planet.MOON_ID == PLANET_ID -->{L_sys_moon} {planet.MOON_NAME}<!-- ELSE -->{planet.NAME}<!-- ENDIF --> [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]<br>
    <span style="position: relative;">
      <span><a href="?cp={planet.ID}&re=0" title="{planet.NAME} [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]"><img src="{dpath}planeten/{planet.IMAGE}.jpg" height="<!-- IF planet.ID == PLANET_ID -->100" width="100<!-- ELSE -->75" width="75<!-- ENDIF -->"></a></span>
      <!-- IF planet.BUILDING --><span style="position: absolute; top: 0px; left: 0px; "><img src="images/build_building.png" height="<!-- IF planet.ID == PLANET_ID -->24" width="24<!-- ELSE -->18" width="18<!-- ENDIF -->"></span><!-- ENDIF -->
      <!-- IF planet.TECH --><span title="{planet.TECH}" style="position: absolute; top: <!-- IF planet.ID == PLANET_ID -->24<!-- ELSE -->18<!-- ENDIF -->px; left: 0px; "><img src="images/build_tech.png" height="<!-- IF planet.ID == PLANET_ID -->24" width="24<!-- ELSE -->18" width="18<!-- ENDIF -->"></span><!-- ENDIF -->
      <!-- IF planet.HANGAR --><span style="position: absolute; top: <!-- IF planet.ID == PLANET_ID -->48<!-- ELSE -->36<!-- ENDIF -->px; left: 0px; "><img src="images/build_hangar.png" height="<!-- IF planet.ID == PLANET_ID -->24" width="24<!-- ELSE -->18" width="18<!-- ENDIF -->"></span><!-- ENDIF -->
    </span>
    <!-- IF planet.MOON_ID --><a href="?cp={planet.MOON_ID}&re=0" title="{planet.MOON_NAME} [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]"><img src="{dpath}planeten/small/s_{planet.MOON_IMG}.jpg" height="<!-- IF planet.MOON_ID == PLANET_ID -->100<!-- ELSE -->35<!-- ENDIF -->" width="<!-- IF planet.MOON_ID == PLANET_ID -->100<!-- ELSE -->35<!-- ENDIF -->"></a><!-- ENDIF --><br>
    <!-- IF planet.BUILDING -->{planet.BUILDING}<span id="ov_building{planet.ID}"></span><br><font color="darkgrey"><span id="ov_building{planet.ID}_timer"></span></font><!-- ELSE -->{L_Free}<!-- ENDIF --><br><br>
    </th></tr>
  <!-- END planet --></table>
</th></tr></table>
{copyright} {admin_email}.<div id="admin_message"></div>

<script type="text/javascript">
jQuery(document).ready(function() {
  // send requests
  jQuery.post("../scheduler.php", {rating: jQuery(this).html()}, function(xml) {
    // format result
    var result = [ jQuery("message", xml).text() ];
    // output result
    jQuery("#admin_message").html(result.join(""));
  } );
});
</script>
