<!-- IF AUTHLEVEL >= 3 -->
<br>
<table width="519">
<form action="announce.php" method="post" name="fForm">
	<tr><td class="c" colspan=2><!-- IF MODE == 'edit' -->{L_adm_an_edit}<!-- ELSEIF MODE == 'copy' -->{L_adm_an_copy}<!-- ELSE -->{L_adm_an_add}<!-- ENDIF --></td></tr>
	<tr>
	  <td>{L_adm_an_date}</td>
	  <td>
	    <div style="float: left"><input name="dtDateTime" size="25" maxlength="19" value="{tsTimeStamp}"></div>
	    <div style="float: right"><!-- IF MODE == 'edit' -->{L_adm_an_mode_edit}<!-- ELSEIF MODE == 'copy' -->{L_adm_an_mode_copy}<!-- ELSE -->{L_adm_an_mode_new}<!-- ENDIF --></div>
       <input type="hidden" name="id" value="{ID}">
       <input type="hidden" name="mode" value="{MODE}">
	  </td>
	</tr>

	<tr>
	  <td valign="top">{L_adm_an_announce}</td>
	  <td><textarea name="text" cols=40 rows=5 width="80%">{strAnnounce}</textarea></td>
	 </tr>
	<tr><td colspan="2" align="center"><input type=submit value="<!-- IF MODE == 'edit' -->{L_adm_an_edit}<!-- ELSEIF MODE == 'copy' -->{L_adm_an_copy}<!-- ELSE -->{L_adm_an_add}<!-- ENDIF -->"></td></tr>
</form>
</table>
<!-- ENDIF -->

<br>
<table width="519">
	<tr><td class="c" colspan="<!-- IF AUTHLEVEL >= 3 -->5<!-- ELSE -->2<!-- ENDIF -->"><div class="fl">{L_adm_an_title}</div><div class="fr">{L_adm_an_total} {total}</div></td></tr>
	<tr><th>{L_adm_an_date}</th><th width="100%">{L_adm_an_announce}</th><!-- IF AUTHLEVEL >= 3 -->
     <th><img src="../images/icon_edit.png"></th><th><img src="../images/icon_copy.gif"></th><th><img src="/images/r1.png"></th><!-- ENDIF --></tr>
<!-- BEGIN announces -->
<tr><td class=b><!-- IF announces.FUTURE --><font color=red><strong>{L_adm_an_future}</strong></font><br><!-- ELSEIF announces.NEW --><font color=red><strong>{L_adm_an_new}</strong></font><br><!-- ENDIF -->{announces.TIME}</td><td class=b align=justify>{announces.ANNOUNCE}</td><!-- IF AUTHLEVEL >= 3 -->
<td class=b><a href="?mode=edit&id={announces.ID}"><img src="../images/icon_edit.png"></a></td>
<td class=b><a href="?mode=copy&id={announces.ID}"><img src="../images/icon_copy.gif"></a></td>
<td class=b><a href="?mode=del&id={announces.ID}"><img src="../images/r1.png"></a></td>
<!-- ENDIF --></tr>
<!-- END announces --></table>
