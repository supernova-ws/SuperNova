<br />
<form action="" method="POST">
  <table width=519>
    <tr><td class=c colspan=2><span class="fl">{ali_adm_options}</span><span class="fr"><a href="alliance.php">{Return_to_overview}</a></span></td></tr>
    <tr><th>{Name}</th><th><input type=text name="name" value="{ally_name}" size="70"></th></tr>
    <tr><th>{Tag}</th><th><input type=text name="tag" value="{ally_tag}" size="70"></th></tr>
    <tr><th>{Main_Page}</th><th><input type=text name="web" value="{ally_web}" size="70"></th></tr>
    <tr><th>{Alliance_logo}</th><th><input type=text name="image" value="{ally_image}" size="70"></th></tr>
    <tr>
      <th>{ali_adm_requests}</th>
      <th>
        <span class="fl"><select name="request_notallow"><option value=1{ally_request_notallow_0}>{not_allow_request}</option>
        <option value=0{ally_request_notallow_1}>{Allow_request}</option></select></span>
        <span class="fr"><a href="alliance.php?mode=admin&edit=requests">{request_count}</a></span>
      </th>
    </tr>
    <tr><th>{Founder_name}</th><th><input type="text" name="owner_range" value="{ally_owner_range}" size=30></th></tr>
    <tr><th colspan=2><input type="submit" name="isSaveOptions" value="{Save}"></th></tr>
  </table>
</form>

<form action="" method="POST">
  <table width=519>
    <tr><td class="c" colspan=3>{Texts}</td></tr>
    <tr>
      <th><a href="alliance.php?mode=admin&edit=ally&t=1">{External_text}</a></th>
      <th><a href="alliance.php?mode=admin&edit=ally&t=2">{Internal_text}</a></th>
      <th><a href="alliance.php?mode=admin&edit=ally&t=3">{Request_text}</a></th>
    </tr>
    <tr><td class=c colspan=3>{request_type} (<span id="cntChars">0</span> / 5000 {characters})</td></tr>
    <tr><th colspan=3><textarea name="text" cols=70 rows=15 onkeyup="javascript:cntchar(5000)">{text}</textarea></th></tr>
    <tr>
      <th colspan=3>
        <input type="hidden" name="t" value="{t}">
        <input type="reset"  value="{ali_sys_clear}">
        <input type="submit" name="isSaveText" value="{Save}">
      </th>
    </tr>
  </table>
</form>

<form action="" method="POST">
  <table width=519>
    <tr><td class=c colspan=2>{ally_admin}</td></tr>
    <tr><th colspan=2><a href="alliance.php?mode=admin&edit=members">{members_admin}</a></th></tr>
    <tr><th colspan=2><a href="alliance.php?mode=admin&edit=rights">{Law_settings}</a></th></tr>
    <tr{hideNotOwner}>
      <th>
        {ali_adm_transfer}&nbsp;&nbsp;&nbsp;
        <select name="idNewLeader">
          <option disabled selected>{ali_adm_newLeader}</option>
          {adminMembers}
        </select>
      </th>
      <th><input type="submit" name="isTransfer" value="{ali_confirm}"></th>
    </tr>
    <tr{hideNotOwner}>
      <th>{ali_adm_disband}&nbsp;&nbsp;&nbsp;<input type="checkbox" name="isConfirmDisband" value="1"></th>
      <th><input type="submit" name="isDisband" value="{ali_confirm}"></th>
    </tr>
  </table>
</form>