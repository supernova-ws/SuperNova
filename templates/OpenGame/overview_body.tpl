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
        <div class="fl">[{user_username}] - {Planet} "{planet_name}" <a href="galaxy.php?mode=0&galaxy={galaxy_galaxy}&system={galaxy_system}">[{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}]</a></div>
        <div class="fr"><a href="overview.php?mode=renameplanet"><font color="green">[{L_ov_operations}]</font></a></td></div>
    </td></tr>
    {Have_new_message}
    {Have_new_level_mineur}
    {Have_new_level_raid}
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
    <tr><th width=90>{L_ov_building}</th><th colspan=3>{building}</th></tr>
    <tr><th>{L_ov_hangar}</th><th colspan="3">{hangar}</th></tr>
    <tr><th>{Teching}</th><th colspan="3">{tech}</th></tr>

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
    <tr>
      <th>{Economica}</th>
      <th colspan="1" align="center">{Experience}: {xpminier}/{lvl_up_minier} </th>
      <th colspan="2" align="center">{Level}: {lvl_minier} </th>
    </tr>
  </table>

  <table width=519>
    <tr align=center>
      <td class="c" colspan=2>{Raids}</td>
      <td class="c" colspan=2>{Points}</td>
    </tr>
    <tr>
      <td align="right"><b>{NumberOfRaids}:</b></td>
      <td align="left" ><b>{raids}</b></td>
      <td align="right"><b>{user_points_1}:</b></td>
      <td align="left" ><b>{user_points}</b></td>
    </tr>
    <tr>
      <td align="right"><b>{RaidsWin}:</b></td>
      <td align="left" ><b>{raidswin}</b></td>
      <td align="right"><b>{user_points_2}:</b></td>
      <td align="left" ><b>{player_points_tech}</b></td>
    </tr>
    <tr>
      <td align="right"><b>{RaidsLoose}:</b></td>
      <td align="left" ><b>{raidsloose}</b></td>
      <td align="right"><b>{user_points_2}:</b></td>
      <td align="left" ><b>{player_points_tech}</b></td>
    </tr>
    <tr>
      <td align="right"><b>{L_ov_raid_xp}:</b></td>
      <td align="left" ><b>{xpraid}/{lvl_up_raid}</b></td>
      <td align="right"><b>{user_points_3}:</b></td>
      <td align="left" ><b>{user_defs_points}</b></td>
    </tr>
    <tr>
      <td colspan=2>&nbsp;</td>
      <td align="right"><b>{user_points_4}:</b></td>
      <td align="left" ><b>{user_fleet}</b></td>
    </tr>
    <tr>
      <td colspan=2>&nbsp;</td>
      <td align="right"><b>{user_points_all}:</b></td>
      <td align="left" ><b>{total_points}</b></td>
    </tr>
    <tr align=center>
      <td class="c" colspan=4><b>{L_ov_rank}: <a href="stat.php?start={u_user_rank}"><strong><span class="style2">{user_rank}</span> из <span class="style1">{max_users}</span></strong></a></b></td>
    </tr>
  </table>

  <table width="519" align="center">
    {bannerframe}
    {userbarframe}
  </table>
</th>

<th valign=top class="tr">
  <table border="0" cellspacing=0 cellpadding=0 width="200"><!-- BEGIN planet -->
    <tr><th class="tr"><!-- IF planet.MOON_ID == PLANET_ID -->{L_sys_moon} {planet.MOON_NAME}<!-- ELSE -->{L_sys_planet} {planet.NAME}<!-- ENDIF --><br>
    <a href="?cp={planet.ID}&re=0" title="{planet.NAME}"><img src="{dpath}planeten/{planet.IMAGE}.jpg" height="<!-- IF planet.ID == PLANET_ID -->100<!-- ELSE -->50<!-- ENDIF -->" width="<!-- IF planet.ID == PLANET_ID -->100<!-- ELSE -->50<!-- ENDIF -->"></a>
    <!-- IF planet.MOON_ID --><a href="?cp={planet.MOON_ID}&re=0" title="{planet.MOON_NAME}"><img src="{dpath}planeten/small/s_{planet.MOON_IMG}.jpg" height="<!-- IF planet.MOON_ID == PLANET_ID -->100<!-- ELSE -->35<!-- ENDIF -->" width="<!-- IF planet.MOON_ID == PLANET_ID -->100<!-- ELSE -->35<!-- ENDIF -->"></a><!-- ENDIF -->
    <!-- IF planet.BUILD_NAME --><br>{planet.BUILD_NAME} ({planet.BUILD_LEVEL})<br><font color="#7f7f7f">({planet.BUILD_TIME})</font><!-- ENDIF -->
    <br><br>
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
