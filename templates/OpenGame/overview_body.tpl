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
    <!-- IF NEW_LEVEL_MINER --><tr><th colspan=4><a href=officier.$phpEx>{L_ov_rpg_new_level_miner}</a></th></tr><!-- ENDIF -->
    <!-- IF NEW_LEVEL_RAID --><tr><th colspan=4><a href=officier.$phpEx>{L_ov_rpg_new_level_raid}</a></th></tr><!-- ENDIF -->
    <tr>
      <th width="100">{Server_time}</th>
      <th colspan="3" style="white-space: nowrap;">{time}<span id="dateheure">00:00:00</span></th>
    </tr>
    <tr>
      <th>{MembersOnline}</th>
      <th colspan="3"><a href="admin/overview.php" title="{MembersOnline2}">[{NumberMembersOnline}]</a></th>
    </tr>
    {NewsFrame}
    
    <tr><td colspan="4" class="c">{Planet_menu}</td></tr>
    {fleet_list}
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
    <a href="?cp={planet.ID}&re=0" title="{planet.NAME} [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]"><img src="{dpath}planeten/{planet.IMAGE}.jpg" height="<!-- IF planet.ID == PLANET_ID -->100<!-- ELSE -->50<!-- ENDIF -->" width="<!-- IF planet.ID == PLANET_ID -->100<!-- ELSE -->50<!-- ENDIF -->"></a>
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
