{mlst_scpt}
<br><br>
<h2>{mlst_title}</h2>
<form action="" method="post">
<input type="hidden" name="curr" value="{mlst_data_page}">
<input type="hidden" name="pmax" value="{mlst_data_pagemax}">
<input type="hidden" name="sele" value="{mlst_data_sele}">
<table width="700" border="0" cellspacing="1" cellpadding="1">
<tr>
	<td class="c"><div align="center"><input type="submit" name="prev"value="{mlst_hdr_prev}" /></div></div></td>
	<td class="c"><div align="center">{mlst_hdr_page}</div></td>
	<td class="c"><div align="center">
		<select name="page" onchange="submit();">
		{mlst_data_pages}
		</select></div>
	</td>
	<td class="c"><div align="center"><input type="submit" name="next" value="{mlst_hdr_next}" /></div></td>
</tr><tr>
	<td class="c">&nbsp;</td>
	<td class="c"><div align="center">{mlst_hdr_type}</div></td>
	<td class="c"><div align="center">
		<select name="type" onchange="submit();">
		{mlst_data_types}
		</select></div>
	</td>
	<td class="c">&nbsp;</td>
</tr><tr>
	<td class="c"><div align="center"><input type="submit" name="delsel" value="{mlst_bt_delsel}" /></div></td>
	<td class="c"><div align="center">{mlst_hdr_delfrom}</div></td>
	<td class="c"><div align="center"><input type="text"   name="selday" size="3" /> <input type="text"   name="selmonth" size="3" /> <input type="text"   name="selyear" size="6" /></div></td>
	<td class="c"><div align="center"><input type="submit" name="deldat" value="{mlst_bt_deldate}" /></div></td>
</tr><tr>
	<th colspan="4">
		<table width="700" border="0" cellspacing="1" cellpadding="1">
		<tr align="center" valign="middle">
			<th class="c">{mlst_hdr_action}</th>
			<th class="c">{mlst_hdr_id}</th>
			<th class="c">{mlst_hdr_time}</th>
			<th class="c">{mlst_hdr_from}</th>
			<th class="c">{mlst_hdr_to}</th>
			<th class="c" width="350">{mlst_hdr_text}</th>
		</tr>
		{mlst_data_rows}
		</table>
	</th>
</tr>
</table>
</form>