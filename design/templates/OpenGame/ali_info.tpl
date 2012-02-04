<!-- IF ally_image -->
  <!-- DEFINE $COLSPAN = 3 -->
<!-- ELSE -->
  <!-- DEFINE $COLSPAN = 2 -->
<!-- ENDIF -->
<br />
<table width=519>
  <tr>
    <td class=c colspan="{$COLSPAN}">
      <!-- IF EXTERNAL -->
        {L_ali_info_title}
      <!-- ELSE -->
        {L_your_alliance}
      <!-- ENDIF -->
    </td>
  </tr>
  <tr><th width=150>{L_Tag}</th><th>{ally_tag}</th><!-- IF ally_image --><th rowspan="5"><img src="{D_SN_ROOT_VIRTUAL}/images/avatar/ally_{ally_id}.png"></th><!-- ENDIF --></tr>
  <tr><th>{L_Name}</th><th>{ally_name}</th></tr>
  <tr><th>{L_Main_Page}</th><th><a href="{ally_web}">{ally_web}</a></th></tr>
  <tr><th>{L_Members}</th><th>{ally_members}<!-- IF ! EXTERNAL -->&nbsp;(<a href="alliance.php?mode=memberslist">{Members_list}</a>)<!-- ENDIF --></th></tr>
  <!-- IF EXTERNAL && ! USER_ALLY_ID -->
    <tr>
      <th>{L_ali_req_make}</th>
      <th><a href="alliance.php?mode=apply&a={ally_id}">{Click_writerequest}</a></th>
    </tr>
  <!-- ENDIF -->
  <tr><th colspan=2>{ALLY_DESCRIPTION}</th></tr>
</table>

<!-- IF ! EXTERNAL -->
<table width=519>
  <tr>
    <td class=c colspan=2>
      <span class="fl">{L_ali_info_internal}</span>
      <span class="fr">
        <!-- IF ALLY_ADMIN -->
          <a href="alliance.php?mode=admin&edit=ally">{L_ally_admin}</a>
        <!-- ELSEIF ALLY_CAN_KICK -->
          <a href="alliance.php?mode=admin&edit=members">{L_members_admin}</a>
        <!-- ENDIF -->
      </span>
    </td>
  </tr>
  <tr><th width=150>{Range}</th><th>{range}</th></tr>
  <!-- IF MANAGE_REQUESTS --><tr><th>{ali_req_check}</th><th><a href="alliance.php?mode=admin&edit=requests"><!-- IF ALLY_REQUESTS -->{L_ali_req_requestCount}:&nbsp;{ALLY_REQUESTS}<!-- ELSE -->{L_ali_req_emptyList}<!-- ENDIF --></a></th></tr><!-- ENDIF -->
  <!-- IF MASS_MAIL --><tr><th>{Circular_message}</th><th><a href="alliance.php?mode=circular">{Send_circular_mail}</a></th></tr><!-- ENDIF -->
  <!-- IF ! ALLY_ADMIN -->
    <tr>
      <th>{L_ali_info_leave}</th>
      <th>
        <form action="alliance.php" method="post">
          <input type="checkbox" name="ali_info_leave_confirm" value="1"> {L_ali_info_leave}
          <input type="hidden" name="mode" value="exit">
          <input type="submit" value="{L_sys_confirm}">
        </form>
      </th>
    </tr>
  <!-- ENDIF -->
  <!-- IF ALLY_TEXT --><tr><th colspan=2>{ALLY_TEXT}</th></tr><!-- ENDIF -->
</table>
<!-- ENDIF -->

<table width=519>
  <tr>
    <td class=c colspan=3>
      <span class="fl">{L_ali_dip_title}</span>
      <!-- IF ALLY_NEGOTIATE --><a class="fr" href="alliance.php?mode=admin&edit=diplomacy">{L_ali_dip_negotiate}</a><!-- ENDIF -->
    </td>
  </tr>
  <!-- BEGIN relation -->
    <tr>
      <th width=120>{relation.TIME}</th>
      <th>{relation.NAME}</th>
      <th width=100>{relation.RELATION}</th>
    </tr>
  <!-- BEGINELSE relation -->
    <tr><th>
      {L_ali_dip_relation_none}
    </th></tr>
  <!-- END relation -->
</table>
