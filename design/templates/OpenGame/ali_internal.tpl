<br />
<table width=519>
  <tr><td class=c colspan=2><span class="fl">{your_alliance}</span><span class="fr">{alliance_admin}</span></td></tr>
  {ally_image}
  <tr><th>{Name}</th><th>{ally_name}</th></tr>
  <tr><th>{Tag}</th><th>{ally_tag}</th></tr>
  <tr><th>{Members}</th><th>{ally_members} (<a href="alliance.php?mode=memberslist">{Members_list}</a>)</th></tr>
  <tr><th>{Range}</th><th>{range}</th></tr>
  <tr {isShowRequests}><th>{ali_req_check}</th><th><a href="alliance.php?mode=admin&edit=requests">{request_count} {XRequests}</a></th></tr>
  <tr {isCanMassMail}><th>{Circular_message}</th><th><a href="alliance.php?mode=circular">{Send_circular_mail}</a></th></tr>
  <tr><th>{Main_Page}</th><th><a href="{ally_web}">{ally_web}</a></th></tr>
  <tr><th colspan=2 height=100>{ally_description}</th></tr>
  <tr><td class=c colspan=2>{Inner_section}</td></tr>
  <tr><th colspan=2 height=100>{ally_text}</th></tr>
</table>
{ally_owner}
