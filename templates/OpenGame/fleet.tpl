<script language="JavaScript" src="scripts/flotten.js"></script>
<script language="JavaScript" src="scripts/ocnt.js"></script>
<br>
<center>
  {vacation}
  <table width='519' border='0' cellpadding='0' cellspacing='1'>
    <tr height='20' class='c'>
      <td colspan=9 class='c'><div style="float: left">{fl_title} {MaxFlyingFleets} {fl_sur} {MaxFlottes} </div><div align=right>{ExpeditionEnCours}/{EnvoiMaxExpedition} {fl_expttl}</div></td>
    </tr>

    <tr height='20'>
      <th>{fl_id}</th>
      <th>{fl_mission}</th>
      <th>{fl_count}</th>
      <th>{fl_from}</th>
      <th>{fl_start_t}</th>
      <th>{fl_dest}</th>
      <th>{fl_dest_t}</th>
      <th>{fl_back_in}</th>
      <th>{fl_order}</th>
    </tr>

<!-- BEGIN fleets -->
<tr height=20>
  <th>{fleets.NUMBER}</th>
  <th><a>{fleets.MISSION_NAME}</a><br>
    <span title="<!-- IF fleets.MESSAGE -->{L_fl_back_to_ttl">{L_fl_back_to}<!-- ELSE -->{L_fl_get_to_ttl}">{L_fl_get_to}<!-- ENDIF --></span>
  </th>
  <th><a title="{fleets.TIP}">{fleets.AMOUNT}</a></th>
  <th>{fleets.START_PLANET}</th>
  <th>{fleets.START_TIME}</th>
  <th>{fleets.END_PLANET}</th>
  <th>{fleets.END_TIME}</th>
  <th><font color=lime><span id="fleet_timer{fleets.ID}"></span></font></th>
  <th>
  <!-- IF fleets.MESSAGE == 0 -->
  <form action="fleetback.php" method="post" style="margin: 0">
  <input name="fleetid" value="{fleets.ID}" type="hidden">
  <input value="{L_fl_back_to_ttl}" type="submit" name="send">
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
  sn_timers.unshift(['fleet_timer{fleets.ID}', 0, true, {TIME_NOW}, ['Флот прибыл',[
    ['{fleets.ID}', '', {fleets.TIME_LEFT}, '0']
  ]]]);
--></script>
<!-- END fleets -->


    <tr height="20" style="{DisplayNoSlotFree}"><th colspan="9"><font color="red">{fl_noslotfree}</font></th></tr>
  </table><br>

  {AKS}

  <form action="fleet.php?fleet_page=1" method="post">
    <table width="519" border="0" cellpadding="0" cellspacing="1">
      <tr height="20"><td colspan="4" class="c">{fl_new_miss}</td></tr>
      <tr height="20" style="{DisplayButtons}">
        <th>{fl_fleet_typ}</th>
        <th>{fl_fleet_disp}</th>
        <th>-</th>
        <th>-</th>
      </tr>

      {ErrorNoPlanetRow}
      
      {ShipList}
      
      <tr height="20" style="{DisplayNoShips}"><th colspan="4">{fl_noships}</th></tr>

      <tr height="20" style="{DisplayButtons}">
        <th colspan="2"><a href="javascript:noShips();shortInfo();noResources();" >{fl_unselectall}</a></th>
        <th colspan="2"><a href="javascript:maxShips();shortInfo();">{fl_selectall}</a></th>
      </tr>
      
      <tr height="20" style="{DisplayButtons}"><th colspan="4"><input type="submit" value="{fl_continue}" /></th></tr>
    </table>
    <input type="hidden" name="galaxy" value="{galaxy}">
    <input type="hidden" name="system" value="{system}">
    <input type="hidden" name="planet" value="{planet}">
    <input type="hidden" name="planet_type" value="{planet_type}">
    <input type="hidden" name="mission" value="{target_mission}">
    <input type="hidden" name="maxepedition" value="{EnvoiMaxExpedition}">
    <input type="hidden" name="curepedition" value="{ExpeditionEnCours}">
    <input type="hidden" name="target_mission" value="{target_mission}">
  </form>
</center>
