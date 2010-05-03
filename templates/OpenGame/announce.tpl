<h2>{adm_an_title}</h2>
<table width="519" style="{DisplayAdmin}">
<form action="announce.php" method="post" name="fForm">
	<tr>
	  <td>{adm_an_date}</td>
	  <td><div style="float: left"><input name="dtDateTime" size="25" maxlength="19" value="{tsTimeStamp}"></div><div style="float: right">{modePrintable}</div></td>
     <input type="hidden" name="id" value="{id}"></td>
     <input type="hidden" name="mode" value="{mode}"></td>
	</tr>

	<tr>
	  <td valign="top">{adm_an_announce}</td>
	  <td><textarea name="text" cols=40 rows=5 width="80%">{strAnnounce}</textarea></td>
	 </tr>
	<tr><td colspan="2" align="center"><input type=submit value="{submitTitle}"></td></tr>
</form>
</table>

<table width="519">
	<tr><td class="c" colspan="{colspan}">{adm_an_title}</td></tr>
	<tr>
     <th>{adm_an_date}</th>
     <th width="100%">{adm_an_announce}</th>
     <th style="{DisplayAdmin}">Ed</th>
     <th style="{DisplayAdmin}">Dup</th>
     <th style="{DisplayAdmin}"><img src="/images/r1.png"></th>
	</tr>
	{announces}
  <tr><th class="b" colspan="{colspan}">{adm_an_total} {announces_total}</th></tr>
</table>
