<br />
<table width=519>
  <tr>
    <td class=c colspan=2>
      <span class="fl">{your_alliance}</span>
      <span class="fr">
      <!-- IF ALLY_ADMIN -->
        <a href="alliance.php?mode=admin&edit=ally">{L_ally_admin}</a>
      <!-- ELSEIF ALLY_CAN_KICK -->
        <a href="alliance.php?mode=admin&edit=members">{L_members_admin}</a>
      <!-- ENDIF -->
      </span>
    </td>
  </tr>
  <!-- IF ally_image --><tr><th colspan=2><img src="{ally_image}"></th></tr><!-- ENDIF -->
  <tr><th>{Name}</th><th>{ally_name}</th></tr>
  <tr><th>{Tag}</th><th>{ally_tag}</th></tr>
  <tr><th>{Members}</th><th>{ally_members} (<a href="alliance.php?mode=memberslist">{Members_list}</a>)</th></tr>
  <tr><th>{Range}</th><th>{range}</th></tr>
  <!-- IF MANAGE_REQUESTS --><tr><th>{ali_req_check}</th><th><a href="alliance.php?mode=admin&edit=requests"><!-- IF ALLY_REQUESTS -->{L_ali_req_requestCount}:&nbsp;{ALLY_REQUESTS}<!-- ELSE -->{L_ali_req_emptyList}<!-- ENDIF --></a></th></tr><!-- ENDIF -->
  <!-- IF MASS_MAIL --><tr><th>{Circular_message}</th><th><a href="alliance.php?mode=circular">{Send_circular_mail}</a></th></tr><!-- ENDIF -->
  <tr><th>{Main_Page}</th><th><a href="{ally_web}">{ally_web}</a></th></tr>
  <tr><th colspan=2>{ally_text}</th></tr>
  <tr><td class=c colspan=2>{Inner_section}</td></tr>
  <tr><th colspan=2>{ally_description}&nbsp;</th></tr>
</table>

<table width=519>
  <tr>
    <td class=c colspan=3>
      <span class="fl">{L_ali_dip_title}</span>
      <!-- IF ALLY_NEGOTIATE --><a class="fr" href="alliance.php?mode=admin&edit=diplomacy">{L_ali_dip_negotiate}</a><!-- ENDIF -->
    </td>
  </tr>
    <!-- BEGIN relation -->
      <tr>
        <th>{relation.TIME}</th>
        <th>{relation.NAME}</th>
        <th>{relation.RELATION}</th>
      </tr>
    <!-- BEGINELSE relation -->
      <tr><th>
        {L_ali_dip_relation_none}
      </th></tr>
    <!-- END relation -->
</table>
{ally_owner}
