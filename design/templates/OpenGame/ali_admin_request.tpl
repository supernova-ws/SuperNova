<br />
<table width=519>
  <tr><td class=c colspan=5>
    <span class="fl">{L_ali_req_admin_title} [{ally_tag}]</span>
    <span class="fr"><a href="alliance.php?mode=admin">{L_ali_adm_return}</a></span>
  </td></tr>

  <tr>
    <td class=c><center><a href="alliance.php?mode=admin&edit=requests&show=0&sort=1">{L_ali_req_candidate}</a></center></td>
    <td class=c><center><a href="alliance.php?mode=admin&edit=requests&show=0&sort=0">{L_ali_req_date}</a></center></td>
    <td class=c><center>{L_ali_req_text}</center></td>
    <td class=c><center><img src="design/images/r4.png" alt="{L_ali_req_accept}" title="{L_ali_req_accept}" border="0"></center></td>
    <td class=c><center><img src="design/images/r1.png" alt="{L_ali_req_deny}" title="{L_ali_req_deny}" border="0"></center></td>
  </tr>

  <!-- BEGIN alliance_request -->  
    <tr>
      <th><center>{alliance_request.USER_NAME}</center></th>
      <th><center>{alliance_request.TIME}</center></th>
      <th><div align="left">
        <!-- IF alliance_request.DENIED -->
          {L_ali_req_deny_admin}
        <!-- ELSE -->
          {alliance_request.TEXT}
        <!-- ENDIF -->
      </div></th>
      <th><a href="alliance.php?mode=admin&edit=requests&id_user={alliance_request.USER_ID}"><img src="design/images/r4.png" alt="{L_ali_req_accept}" title="{L_ali_req_accept}" border="0"></a></th>
      <th><a href="alliance.php?mode=admin&edit=requests&d={alliance_request.USER_ID}"><img src="design/images/r1.png" alt="{L_ali_req_deny}" title="{L_ali_req_deny}" border="0"></a></th>
    </tr>
  <!-- BEGINELSE alliance_request -->  
    <tr><th colspan=5>{L_ali_req_emptyList}</th></tr>
  <!-- END alliance_request -->  
  
  <tr>
    <td class=c colspan=5><span class="fr"><a href="alliance.php">{L_ali_sys_main_page}</a></span></td>
  </tr>
</table>
