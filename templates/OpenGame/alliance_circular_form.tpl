<br />
<form action="?a=17&sendmail=1" method=post>
  <table width=519>
	<tr>
	  <td class=c colspan=2>{Send_circular_mail}</td>
	</tr>
	<tr>
	  <th>{Destiny}</th>
	  <th>
		<select name=r>
		  {r_list}
		</select>
	  </th>
	</tr>

	<tr>
	  <th>{Text_mail} (<span id="cntChars">0</span> / 5000 {characters})</th>
	  <th>
	    <textarea name="text" cols="60" rows="10" onkeyup="javascript:cntchar(5000)"></textarea>
	  </th>
	</tr>
	<tr>
	  <td class="c"><a href="alliance.php">{Back}</a></td>
	  <td class="c">
		<input type="reset" value="{Clear}">
		<input type="submit" value="{Send}">
	  </td>
	</tr>
  </table>
</form>
