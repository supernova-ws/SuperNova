
<form action="{PHP_SELF}" method=post>
  {inputs}
  <table width=519>
	<tr>
	  <td class=c colspan=2>{TITLE}</td>
	</tr>
	<tr>
	  <th>{Priority}</th>
	  <th>
		<select name=u>
		  {c_Options}
		</select>
	  </th>
	</tr>
	<tr>
	  <th>{Subject}</th>
	  <th>
		<input type="text" name="title" size="30" maxlength="30" value="{title}">
	  </th>
	</tr>
	<tr>
	  <th>{Note} (<span id="cntChars">{cntChars}</span> / 5000 {characters})</th>
	  <th>
	    <textarea name="text" cols="60" rows="10" onkeyup="javascript:cntchar(5000)">{text}</textarea>
	  </th>
	</tr>
	<tr>
	  <td class="c"><a href="{PHP_SELF}">{Back}</a></td>
	  <td class="c">
		<input type="reset" value="{Reset}">
		<input type="submit" value="{Save}">
	  </td>
	</tr>
  </table>
</form>
