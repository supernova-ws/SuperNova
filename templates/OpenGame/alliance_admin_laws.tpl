<br />
<table width=519><tr><td class=c colspan=11>{laws_config}</td></tr>

{list}


</table>

<br />

<form action="alliance.php?mode=admin&edit=rights&add=name" method=POST>
<table width=519>
	<tr>
	  <td class=c colspan=2>{Range_make}</td>
	</tr>
	<tr>
	  <th>{Range_name}</th>
	  <th><input type=text name="newrangname" size=20 maxlength=30></th>
	</tr>
	<tr>
	  <th colspan=2><input type=submit value="{Make}"></th>
	</tr>
</form>
</table>

<form action="alliance.php?mode=admin&edit=rights" method=POST>
<table width=519>
	<tr>
	  <td class=c colspan=2>{Law_leyends}</td>
	</tr>
	<tr>
	  <th><img src=images/r1.png></th>
	  <th>{ally_dissolve}</th>
	</tr>
	<tr>
	  <th><img src=images/r2.png></th>
	  <th>{Expel_users}</th>
	</tr>
	<tr>
	  <th><img src=images/r3.png></th>
	  <th>{requests_view}</th>
	</tr>
	<tr>
	  <th><img src=images/r4.png></th>
	  <th>{memberlist_view}</th>
	</tr>
	<tr>
	  <th><img src=images/r5.png></th>
	  <th>{requests_admin}</th>
	</tr>
	<tr>
	  <th><img src=images/r6.png></th>
	  <th>{ally_admin}</th>
	</tr>
	<tr>
	  <th><img src=images/r7.png></th>
	  <th>{memberlist_online}</th>
	</tr>
	<tr>
	  <th><img src=images/r8.png></th><th>{make_circular}</th>
	</tr>
	<tr>
	  <th><img src=images/r9.png></th><th>{Left_hand_text}</th>
	</tr>
	<tr>
	  <td class="c" colspan="2"><a href="alliance.php?mode=admin&edit=ally">{Return_to_overview}</a></td>
	</tr>
</form>
</table>
	