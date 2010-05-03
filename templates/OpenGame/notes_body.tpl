
<br>
<form action="{PHP_SELF}" method=post>
  <table width=519>
	<tr>
	  <td class=c colspan=4>{Notes}</td>
	</tr>
	<tr>
	  <th colspan=4><a href="{PHP_SELF}?a=1">{MakeNewNote}</a></th>
	</tr>
	<tr>
	  <td class=c></td>
	  <td class=c>{Date}</td>
	  <td class=c>{Subject}</td>
	  <td class=c>{Size}</td>
	</tr>

	{BODY_LIST}

<tr>
	  <td colspan=4><input value="{Delete}" type="submit"></td>
	</tr>
  </table>
</form>
</center>
</body>
</html>
