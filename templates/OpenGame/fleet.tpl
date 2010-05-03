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

    {FlyingFleets_array}

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
