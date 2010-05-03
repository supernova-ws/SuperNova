<center>
<h1>{rename_and_abandon_planet}</h1>
<form action="overview.php?mode=renameplanet&pl={planet_id}" method=POST>
<table width=519>
<tr>
	<td class=c colspan=3>{security_query}</td>
</tr><tr>
	<th colspan=3>{confirm_planet_delete} {galaxy_galaxy}:{galaxy_system}:{galaxy_planet} {confirmed_with_password}</th>
</tr><tr>
	<th>{password}</th>
	<th><input type=password name=pw></th>
	<th><input type=submit name=action value="{deleteplanet}" alt="{colony_abandon}"></th><input type="hidden" name="kolonieloeschen" value="1">
</tr>
</table>
<input type=hidden name=deleteid value ="{planet_id}">
</form>
</center>
</body>
</html>