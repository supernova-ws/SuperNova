<br />
<table>
	<tr>
	  <td class="c" colspan="{colspan}"><span class="fl">{Members_list} / {ali_sys_totalMembers}: {memberCount}</span><span class="{adminBackLinkClass}"><a href="?mode=admin">{ali_adm_return}</a></span></td>
	</tr>
	<tr>
	  <th>{Number}</th>
	  <th><a href="alliance.php?mode={mode}&sort1=1&sort2={s}">{ali_sys_memberName}</a></th>
	  <th><img src="{dpath}img/m.gif" border=0 alt="{Write_message}"></th>
	  <th><a href="alliance.php?mode={mode}&sort1=0&sort2={s}">{Range}</a></th>
	  <th><a href="alliance.php?mode={mode}&sort1=2&sort2={s}">{ali_sys_points}</a></th>
	  <th><a href="alliance.php?mode={mode}&sort1=3&sort2={s}">{Coordinated}</a></th>
	  <th><a href="alliance.php?mode={mode}&sort1=4&sort2={s}">{Member_from}</a></th>
	  <th><a href="alliance.php?mode={mode}&sort1=5&sort2={s}">{onlineMessage}</a></th>
	  <th {isHideAdmin}><img src="{dpath}pic/abort.gif" border=0 alt="{ali_adm_kick}" title="{ali_adm_kick}"></th>
	</tr>
	{memberslist}
	<tr>
	  <td class="c" colspan="{colspan}"><span class="fr"><a href="alliance.php">{Return_to_overview}</a></span></td>
	</tr>
</table>
