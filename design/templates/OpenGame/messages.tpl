<script language="JavaScript">
  function f(target_url, win_name) 
  {
    var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
    new_win.focus();
  }
</script>

<form action="messages.php" method="post">
  <input name="messages" value="1" type="hidden">

  <table width="519">
    <tr>
      <!-- IF MSG_CATEGORY != -1 -->
      <th colspan="4">
        <select onchange="document.getElementById('deletemessages').options[this.selectedIndex].selected='true'" id="deletemessages2" name="deletemessages2">
          <option value="deletemarked">{L_mess_deletemarked}</option>
          <option value="deleteunmarked">{L_mess_deleteunmarked}</option>
          <option value="deleteall">{L_mess_deleteall}</option>
        </select>
        <input value="{L_mess_its_ok}" type="submit">
      </th>
    </tr>

    <tr>
      <th style="color: rgb(242, 204, 74);" colspan="4">
        <input name="category" value="{MSG_CATEGORY}" type="hidden">
        <input onchange="document.getElementById('fullreports').checked=this.checked" id="fullreports2" name="fullreports2" type="checkbox">{L_mess_partialreport}
      </th>
    </tr>
  
    <tr>
      <td class="c">{L_mess_action}</td>
    <!-- ENDIF -->
      <td class="c">{L_mess_date}</td>
      <td class="c">
        <!-- IF MSG_CATEGORY == -1 -->
          {L_mess_recipient}
        <!-- ELSE -->
          {L_mess_from}
        <!-- ENDIF -->
      </td>
      <td class="c">{L_mess_subject}</td>
    </tr>
  <!-- BEGIN messages -->
    <input name="showmes{messages.ID}" type="hidden" value="1">
    <tr>
      <!-- IF MSG_CATEGORY != -1 -->
        <th>
          <input name="delmes{messages.ID}" type="checkbox">
          <!-- <span onclick='msg_toggle_spy({messages.ID});'>switch</span> -->
        </th>
      <!-- ENDIF -->    	
      <th>{messages.DATE}</th>
      <th>{messages.FROM}</th>
      <th>
        {messages.SUBJ}
        <!-- IF MSG_CATEGORY == 1 -->
          <a href="messages.php?mode=write&amp;id={messages.FROM_ID}&amp;subject={L_mess_answer_prefix}{messages.SUBJ_SANITIZED}">
            <img src="{dpath}img/m.gif" alt="{L_mess_answer}" border="0">
          </a>
        <!-- ENDIF -->
      </th>
    </tr>
    <tr id='mess{messages.ID}'>
      <!-- IF MSG_CATEGORY != -1 -->
      <td class="{messages.STYLE}" class="b">
        &nbsp;
      </td>
      <!-- ENDIF -->    	
      <td class="{messages.STYLE}" class="b" colspan="3">
        {messages.TEXT}
      </td>
    </tr>
  <!-- END messages -->

  <!-- IF MSG_CATEGORY != -1 -->
    <tr>
      <th style="color: rgb(242, 204, 74);" colspan="4">
        <input onchange="document.getElementById('fullreports2').checked=this.checked" id="fullreports" name="fullreports" type="checkbox">{L_mess_partialreport}
      </th>
    </tr>

    <tr>
      <th colspan="4">
        <select onchange="document.getElementById('deletemessages2').options[this.selectedIndex].selected='true'" id="deletemessages" name="deletemessages">
          <option value="deletemarked">{L_mess_deletemarked}</option>
          <option value="deleteunmarked">{L_mess_deleteunmarked}</option>
          <option value="deleteall">{L_mess_deleteall}</option>
        </select>
        <input value="{L_mess_its_ok}" type="submit">
      </th>
    </tr>
  <!-- ENDIF -->
  </table>
</form>
