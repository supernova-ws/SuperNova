<tr height=20>
<th>{FleetNum}</th>
<th>
<a>{MissionType}</a>
{U1}
</th>
<th><a title="{FleetTip}">{FleetAmount}</a></th>
<th>{FleetStart}</th>
<th>{FleetStartTime}</th>
<th>{FleetEnd}</th>
<th>{FleetEndTime}</th>
<th><font color="lime"><div id="time_0"><font>{FleetTimeLeft}</font></th>
<th>
<form action="fleetback.php" method="post" style="margin: 0">
<input name="fleetid" value="{FleetID}" type="hidden">
<input value="{fl_back_to_ttl}" type="{ShowFleetBack}" name="send">
</form>
<form action="fleet.php?fleet_page=4" method="post" style="margin: 0">
<input name="fleetid" value="{FleetID}" type="hidden">
<input value="{ACSGroup}" type="{ShowACS}">
</form></th>
</tr>
