<script language="JavaScript" src="scripts/flotten.js"></script>
<script language="JavaScript" src="scripts/ocnt.js"></script>
<br>
<center>
  {vacation}
  <table border='0' cellpadding='0' cellspacing='1'>
    <tr height='20' class='c'>
      <td colspan=8 class='c'><div style="float: left">{fl_title} {MaxFlyingFleets} {fl_sur} {MaxFlottes} </div><div align=right>{ExpeditionEnCours}/{EnvoiMaxExpedition} {fl_expttl}</div></td>
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
    <tr height=20>
      <th>{fleets.NUMBER}</th>
      <th><a>{fleets.MISSION_NAME}</a><br>
        <span title="<!-- IF fleets.MESSAGE -->{L_fl_back_to_ttl">{L_fl_back_to}<!-- ELSE -->{L_fl_get_to_ttl}">{L_fl_get_to}<!-- ENDIF --></span>
      </th>
      <th><a href=# style="cursor: pointer;" onmouseout='return nd();' onmouseover="return overlib('<table><tr><td class=c colspan=2>{L_sys_ships}</td></tr><!-- BEGIN ships --><tr><th>{ships.NAME}</th><th>{ships.AMOUNT}</th></tr><!-- END ships --><!-- IF fleets.METAL + fleets.CRYSTAL + fleets.DEUTERIUM > 0 --><tr><td class=c colspan=2>{L_sys_resources}</td></tr><!-- IF fleets.METAL > 0 --><tr><th>{L_sys_metal}</th><th>{fleets.METAL}</th></tr><!-- ENDIF --><!-- IF fleets.CRYSTAL > 0 --><tr><th>{L_sys_crystal}</th><th>{fleets.CRYSTAL}</th></tr><!-- ENDIF --><!-- IF fleets.DEUTERIUM > 0 --><tr><th>{L_sys_deuterium}</th><th>{fleets.DEUTERIUM}</th></tr><!-- ENDIF --><!-- ENDIF --></table>', STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, 0, OFFSETY, 0);"> {fleets.AMOUNT}</a></th>
      <th>{fleets.END_PLANET} {fleets.END_TYPE}</th>
      <th><!-- IF fleets.MESSAGE == 0 --><font color=lime><span id="fleet_timer_end{fleets.ID}"></span></font><br>{fleets.END_TIME}<!-- ELSE -->-<!-- ENDIF --></th>
      <th>{fleets.START_PLANET} {fleets.START_TYPE}</th>
      <th>
        <!-- IF ((fleets.MISSION == 7 || fleets.MISSION == 4) && fleets.MESSAGE == 1) || (fleets.MISSION != 7 && fleets.MISSION != 4) -->
        <font color=lime><span id="fleet_timer_start{fleets.ID}"></span></font><br>{fleets.START_TIME}
        <!-- ELSE -->-<!-- ENDIF -->
      </th>
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
      sn_timers.unshift(['fleet_timer_start{fleets.ID}', 0, true, {TIME_NOW}, ['Флот прибыл',[
        ['{fleets.ID}', '', {fleets.START_LEFT}, '0']
      ]]]);
      sn_timers.unshift(['fleet_timer_end{fleets.ID}', 0, true, {TIME_NOW}, ['Флот прибыл',[
        ['{fleets.ID}', '', {fleets.END_LEFT}, '0']
      ]]]);
    --></script>
    <!-- END fleets -->


    <tr height="20" style="{DisplayNoSlotFree}"><th colspan="8"><font color="red">{fl_noslotfree}</font></th></tr>
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
