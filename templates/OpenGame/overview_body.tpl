<script type="text/javascript"> 
  jQuery.noConflict(); 
</script> 
<script language="JavaScript" type="text/javascript" src="scripts/mootools.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/slide.js"></script>
<style type="text/css">
<!--
.style1 {color: #FF0000}
.style2 {color: #FFFF00}
.style5 {color: #00FF00;
	font-weight: bold;
}
.style3 {color: #FFFFFF}
.style4 {color: #FF0000;
	font-weight: bold;
}
.style6 {color: #FFFF00; font-weight: bold; }
.style7 {color: #00FFFF}
-->
</style>

<center>
<script language="JavaScript" type="text/javascript" src="scripts/time.js"></script>
<table width="519">
<tr>
<td style="white-space: nowrap;" class="c" colspan=4>
<div style="float: left;">[{user_username}] - {Planet} "{planet_name}" <a href="galaxy.php?mode=0&galaxy={galaxy_galaxy}&system={galaxy_system}">[{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}]</a></div>
<div style="float: right;"><a href="overview.php?mode=renameplanet"><font color="green">[операции]</font></a></td></div>
</td>
</tr>
{Have_new_message}
{Have_new_level_mineur}
{Have_new_level_raid}
<tr>
	<th width="100">{Server_time}</th>
<!--	<th colspan="3" style="white-space: nowrap;">{time}<div id="dateheure"></div></th>-->
	<th colspan="3" style="white-space: nowrap;">{time}<span id="dateheure"></span></th>
</tr>
<tr>
    <th>{MembersOnline}</th>
	<th colspan="3"><a href="admin/overview.php" title="{MembersOnline2}">[{NumberMembersOnline}]</a></th>
</tr>
{NewsFrame}
<tr>
	<td colspan="4" class="c">{Planet_menu}</td>
</tr>
{fleet_list}
<tr><th width=90>Текущее строительство</th><th colspan=3>{building}</th></tr>
<tr><th>Верфи</th><th colspan="3">{hangaring}</th></tr>
<tr><th>{Teching}</th><th colspan="3">{teching}</th></tr>
</table>

<table width="519" border=0 cellspacing=0 cellpading=0>
  <tr>
    <td class="c" style="white-space: nowrap;" width=100%>
      Подробно о планетах:
    </td>
    <td class="c" style="white-space: nowrap;" align=right>
      <a id="v_toggle" href="#">Свернуть/Развернуть (<span id="vertical_status">Свернуто</span>)</a>
    </td>
  </tr>
</table>

<div id="vertical_slide">
  <table  width="519">
    <tr>
      <th align="left" class="c" colspan=3>Другие Планеты:</th>
      <th align="right" class="c"><a id="h_toggle" href="#">Открыть/закрыть</a></th>
    </tr>
<tr>
	<th>{moon_img}<br>{moon}</th>
	<th colspan="2"><img src="{dpath}planeten/{planet_image}.jpg" height="200" width="200"></th>
	<th class="s">
		
	<div id="horizontal_slide">
		<table  class="s" align="top" border="0">
			<tr>
				{anothers_planets}
			</tr>
		</table>
	</div>
	</th>
</tr>
</table>
</div>
<table  width="519" align="center">
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
		<div id="CaseBarre" align=center style="position: relative; left: 0px; background-color: {case_barre_barcolor}; width: {case_barre}px;">{case_pourcentage}
		</div></div>
	</th>
</tr>
<tr>
	<th>{orb}</th>
	<th colspan="3">Металл : {metal_debris} /Кристалл : {crystal_debris}{get_link}</th>
</tr>
<tr>
	<th>{Economica}</th>
	<th colspan="1" align="center">{Experience} : {xpminier}/{lvl_up_minier} </th>
	<th colspan="2" align="center">{Level} : {lvl_minier} </th>
</tr>

<tr align=center><td class="c" colspan=2>{Raids}</td><td class="c" colspan=2>{Points}</td></tr>
<tr  valign=top>
  <th colspan=2  valign=top>
  <table width="100%" valign=top><tbody>
  <tr>
    <td align="right" width="70%" style="background-color: transparent;"><b>{NumberOfRaids} :</b></td>
    <td align="left" width="30%" style="background-color: transparent;"><b>{raids}</b></td></tr>
    <tr><td align="right" width="70%" style="background-color: transparent;"><b>{RaidsWin} :</b></td>
    <td align="left" width="30%" style="background-color: transparent;"><b>{raidswin}</b></td></tr></tr>
    <tr><td align="right" width="70%" style="background-color: transparent;"><b>{RaidsLoose} :</b></td>
    <td align="left" width="30%" style="background-color: transparent;"><b>{raidsloose}</b></td></tr>
    <tr><td align="right" width="70%" style="background-color: transparent;"><b>Рейдовый опыт :</b></td>
    <td align="left" width="30%" style="background-color: transparent;"><b>{xpraid}/{lvl_up_raid}</b></td></tr>
  </tbody></table></th>
  <th colspan=2>
  <table border=0 width="100%" height=100% valign=top><tbody>
  <tr>
      <td style="background-color: transparent;" align="right" width="70%"><b>{user_points_1} :</b></td>
      <td style="background-color: transparent;" align="left" width="30%"><b>{user_points}</b></td>
    </tr>
    <tr>
      <td style="background-color: transparent;" align="right" width="70%"><b>{user_points_2} : </b></td>
      <td style="background-color: transparent;" align="left" width="30%"><b>{player_points_tech}</b></td>
    </tr>
    <tr>
      <td style="background-color: transparent;" align="right" width="70%"><b>{user_points_3} : </b></td>
      <td style="background-color: transparent;" align="left" width="30%"><b>{user_defs_points}</b></td>
    </tr>
    <tr>
      <td style="background-color: transparent;" align="right" width="70%"><b>{user_points_4} :</b></td>
      <td style="background-color: transparent;" align="left" width="30%"><b>{user_fleet}</b></td>
    </tr>
    <tr>
      <td style="background-color: transparent;" align="right" width="70%"><b>{user_points_all} :</b></td>
      <td style="background-color: transparent;" align="left" width="30%"><b>{total_points}</b></td>
    </tr>
  </tbody></table></th>  	
</tr>


<tr align=center>
  <td class="c" width="50%" colspan=2><b>Ранг:</b></td>
  <td class="c" width="50%" colspan=2><b><a href="stat.php?start={u_user_rank}"><strong><span class="style2">{user_rank}</span> из <span class="style1">{max_users}</span></strong></a></b></td>
</tr>
<!-- {ExternalTchatFrame} -->
{bannerframe}
{userbarframe}
</table>
<br />
{ClickBanner}{ClickUserbar}</center>
<!-- anda flytmeny delen -->
{copyright} {admin_email}.<div id="admin_message"></div>
</table></div>

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

</body>
</html>