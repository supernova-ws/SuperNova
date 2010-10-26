<style type="text/css" media="screen">
input.frameles
{
  border               : #ff0000;
  text-align           : center;
  font-size            : 11px;
}
</style>

<table align=center width="722" cellpadding="0" cellspacing="0" class="header" id="resources" style="width: 722px;" padding-right="30"><tbody>
  <tr class="c">
    <td class="c" colspan=5>
         {TIME_TEXT} <span id="top_time">00:00:00</span>
        &nbsp;
    </td>
    <td class="c" colspan=2 align="right">
        {L_top_online} [{USERS_ONLINE}/{USERS_TOTAL}]
       </td>
  </tr>

  <tr class="header">
    <td class="header" align="center" width="150"><select size="1" onChange="eval('location=\''+this.options[this.selectedIndex].value+'\'');">
    {planetlist}
    <!-- BEGIN topnav_planets -->
      <option{topnav_planets.SELECTED} value="?cp={topnav_planets.ID}&mode={TOPNAV_MODE}&re=0">{topnav_planets.NAME}&nbsp;{topnav_planets.COORDS}
    <!-- END topnav_planets -->
    </select></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/metall.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/kristall.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/deuterium.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/energie.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="140"><A HREF="resources.php"><img src="{dpath}images/dm_klein_2.jpg" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="140"><A HREF="messages.php"><img src="{dpath}images/message.gif" border="0" height="22" width="42"></A></td>
  </tr>
  <tr class="header">
    <td class="header" align="center" width="150">&nbsp;</td>
    <td class="header" align="center" width="150"><A HREF="resources.php">{L_sys_metal}</A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php">{L_sys_crystal}</A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php">{L_sys_deuterium}</A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php">{L_sys_energy}</A></td>
    <td class="header" align="center" width="140"><A HREF="resources.php">{L_sys_dark_matter}</A></td>
    <td class="header" align="center" width="140"><A HREF="messages.php">{L_Message}</A></td>
  </tr>
  <tr class="header" align=center valign=middle><form name='ressources'> <center>
    <td class="header" align="center" width="150"><b><font color="#FFFF00">{L_tech_storage}</font></b></td>
    <td class="header" align="center" width="150"><span id='top_metal'>{TOPNAV_METAL}</span></td>
    <td class="header" align="center" width="150"><span id='top_crystal'>{TOPNAV_CRYSTAL}</span></td>
    <td class="header" align="center" width="150"><span id='top_deuterium'>{TOPNAV_DEUTERIUM}</span></td>
    <td class="header" align="center" width="150">{ENERGY_BALANCE}</td>
    <td class="header" align="center" width="140" rowspan="2"><font color="green"><b>{TOPNAV_DARK_MATTER}</b></font></td>
    <td class="header" align="center" valign=middle width="140" rowspan="2" ><!-- IF TOPNAV_MESSAGES --><a href="messages.php"><blink>[ {TOPNAV_MESSAGES} ]</blink></a><!-- ELSE -->0<!-- ENDIF --></td>
  </form></tr>
  <tr class="header">
    <td class="header" align="center" width="150"><b><font color="#FFFF00">{L_tech_storage_max}</font></b></td>
    <td class="header" align="center" width="150">{TOPNAV_METAL_MAX_TEXT}</td>
    <td class="header" align="center" width="150">{TOPNAV_CRYSTAL_MAX_TEXT}</td>
    <td class="header" align="center" width="150">{TOPNAV_DEUTERIUM_MAX_TEXT}</td>
    <td class="header" align="center" width="150"><font color="#00ff00">{ENERGY_MAX}</font></td>
  </tr>
</tbody></table>
<div id="debug" style="text-align: left"></div>
<script LANGUAGE='JavaScript'><!--
  sn_timers.unshift({id: 'top_metal', type: 1, active: true, start_time: {TIME_NOW}, options: [{TOPNAV_METAL}, {TOPNAV_METAL_PERHOUR}, {TOPNAV_METAL_MAX}]});
  sn_timers.unshift({id: 'top_crystal', type: 1, active: true, start_time: {TIME_NOW}, options: [{TOPNAV_CRYSTAL}, {TOPNAV_CRYSTAL_PERHOUR}, {TOPNAV_CRYSTAL_MAX}]});
  sn_timers.unshift({id: 'top_deuterium', type: 1, active: true, start_time: {TIME_NOW}, options: [{TOPNAV_DEUTERIUM}, {TOPNAV_DEUTERIUM_PERHOUR}, {TOPNAV_DEUTERIUM_MAX}]});

  sn_timers.unshift({id: 'top_time', type: 2, active: true, start_time: {TIME_NOW}, options: 2});
// --></script>