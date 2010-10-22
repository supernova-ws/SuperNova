<br>
<form name="stat_form" method="post">
<table width="519">
<tr>
	<td class="c">{stat_title}: {stat_date}</td>
</tr><tr>
	<th align="center">
	<table>
	<tr>
		<th width="8%" style="background-color: transparent;">&nbsp;</th>
		<th style="background-color: transparent;">{stat_show}&nbsp;</th>
		<th style="background-color: transparent;"><select name="who" onChange="javascript:document.stat_form.submit()">{who}</select></th>
		<th style="background-color: transparent;">&nbsp;{stat_by}&nbsp;</th>
		<th style="background-color: transparent;"><select name="type" onChange="javascript:document.stat_form.submit()">{type}</select></th>
		<th style="background-color: transparent;">&nbsp;{stat_range}&nbsp;</th>
		<th style="background-color: transparent;"><select name="range" onChange="javascript:document.stat_form.submit()">{range}</select></th>
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
