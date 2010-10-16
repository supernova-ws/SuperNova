<br>
<center>
<form method="post">
<table width="519">
<tr>
	<td class="c">{stat_title}: {stat_date}</td>
</tr><tr>
	<th align="center">
	<table>
	<tr>
		<th width="8%" style="background-color: transparent;">&nbsp;</th>
		<th style="background-color: transparent;">{stat_show}&nbsp;</th>
		<th style="background-color: transparent;"><select name="who" onChange="javascript:document.forms[1].submit()">{who}</select></th>
		<th style="background-color: transparent;">&nbsp;{stat_by}&nbsp;</th>
		<th style="background-color: transparent;"><select name="type" onChange="javascript:document.forms[1].submit()">{type}</select></th>
		<th style="background-color: transparent;">&nbsp;{stat_range}&nbsp;</th>
		<th style="background-color: transparent;"><select name="range" onChange="javascript:document.forms[1].submit()">{range}</select></th>
		<th width="8%" style="background-color: transparent;">&nbsp;</th>
	<tr>
	</table>
	</th>
</tr>
</table>
</form>
<table width="519">
{stat_header}
{stat_values}
</table>
</center>
{ClickBanner}
</body>
</html>