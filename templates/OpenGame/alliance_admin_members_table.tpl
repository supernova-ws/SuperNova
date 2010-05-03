<br />
<table width="519">
	<tr>
	  <td class="c" colspan="9">{Members_list} ({Ammount}: {memberzahl})</td>
	</tr>
	<tr>
	  <th>{Number}</th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=1&sort2={s}">{Name}</a></th>
	  <th> </th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=2&sort2={s}">{Position}</a></th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=3&sort2={s}">{Points}</a></th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=0&sort2={s}">{Coordinated}</a></th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=4&sort2={s}">{Member_from}</a></th>
	  <th><a href="alliance.php?mode=admin&edit=members&sort1=5&sort2={s}">{Inactive_since}</a></th>
	  <th>{Functions}</th>
	</tr>
	{memberslist}
	<tr>
	  <td class="c" colspan="9"><a href="alliance.php?mode=admin&edit=ally">{Return_to_overview}</a></td>
	</tr>
</table>
<script src="scripts/wz_tooltip.js" type="text/javascript"></script>
