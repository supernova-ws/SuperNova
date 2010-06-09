<br />
<table width=519><tr><td class=c colspan=7><span class="fl">{laws_config}</span><span class="fr"><a href="?mode=admin">{ali_adm_return}</a></span></td></tr>
  <form action="alliance.php?mode=admin&edit=rights" method=POST>
    <tr>
      <th><img src="images/abort.gif" alt="{Delete_range}" title="{Delete_range}" border="0"></th>
      <th>{Range_name}</th>
      <th><img src=images/r8.png alt="{make_circular}" title="{make_circular}"></th>
      <th><img src=images/r7.png alt="{memberlist_online}" title="{memberlist_online}"></th>
      <th><img src=images/r5.png alt="{ali_req_check}" title="{requests_admin}"></th>
      <th><img src=images/r2.png alt="{Expel_users}" title="{Expel_users}"></th>
      <th><img src=images/r9.png alt="{Left_hand_text}" title="{Left_hand_text}"></th>
    </tr>
    {list}
    <tr><th colspan=7><input type=submit value="{Save}"></th></tr>
  </form>

  <form action="alliance.php?mode=admin&edit=rights&add=name" method=POST><tr>
    <th></th>
    <th><input type=text name="newRankName" size="60" maxlength="60" value="{Range_make}"></th>
    <th colspan="5"><input type=submit value="{Make}"></th>
  </tr></form>
</table>

<br />

<form action="alliance.php?mode=admin&edit=rights" method=POST>
<table width=519>
	<tr><td class=c colspan=2>{Law_leyends}</td></tr>
	<tr><th><img src=images/r8.png></th><th>{make_circular}</th></tr>
	<tr><th><img src=images/r7.png></th><th>{memberlist_online}</th></tr>
	<tr><th><img src=images/r5.png></th><th>{ali_req_check}</th></tr>
	<tr><th><img src=images/r2.png></th><th>{Expel_users}</th></tr>
	<tr><th><img src=images/r9.png></th><th>{Left_hand_text}</th></tr>
	<tr><td class="c" colspan="2"><span class="fr"><a href="alliance.php">{Return_to_overview}</a></span></td></tr>
</table>
</form>
	