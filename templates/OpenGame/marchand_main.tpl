<form action="marchand.php" method="post">
<input type="hidden" name="action" value="2">
<br>
<table width="600">
<tr>
	<td class="c" colspan="10"><font color="#FFFFFF">{mod_ma_title}</font><td>
</tr><tr>
	<th colspan="10">{mod_ma_typer} <select name="choix">
		<option value="metal">{Metal}</option>
		<option value="cristal">{Crystal}</option>
		<option value="deut">{Deuterium}</option>
	</select>
	<br>
	{mod_ma_rates}<br /><br />
	<input type="submit" value="{mod_ma_buton}" /></th>
</tr>
</table>
{ClickBanner}
</form>