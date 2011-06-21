<script language="JavaScript">
  function f(target_url, win_name) 
  {
    var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
    new_win.focus();
  }
</script>

<h2>{L_msg_page_header}</h2>
<h3>{MESSAGE_CLASS_TEXT}</h3>
<form action="messages.php" method="post">
  <input type="hidden" name="message_class" value="{MESSAGE_CLASS}">

  <table width="519">
    <tr>
      <!-- IF MESSAGE_CLASS != -1 -->
      <th colspan="4">
        {L_sys_delete}
        <select onchange="document.getElementById('message_range').options[this.selectedIndex].selected='true'" id="message_range2" name="message_range2">
          <option>{L_msg_mark_select}</option>
          <option value="checked">{L_msg_mark_checked}</option>
          <option value="unchecked">{L_msg_mark_unchecked}</option>
          <option value="class">{L_msg_mark_class}</option>
          <option value="all">{L_msg_mark_all}</option>
        </select>
        <input name="msg_delete" value="{L_sys_delete}" type="submit">
      </th>
    </tr>
<!--
    <tr>
      <th style="color: rgb(242, 204, 74);" colspan="4">
        <input name="category" value="{MESSAGE_CLASS}" type="hidden">
        <input onchange="document.getElementById('fullreports').checked=this.checked" id="fullreports2" name="fullreports2" type="checkbox">{L_mess_partialreport}
      </th>
    </tr>
-->  

    <tr>
      <th class="c_c" width="20">
        <input type="checkbox" onchange="jQuery('.msg_checkbox').attr('checked', jQuery(this).is(':checked'));">
      </th>
    <!-- ENDIF -->
      <th class="c_c" width="120">{L_mess_date}</th>
      <th class="c_c">
        <!-- IF MESSAGE_CLASS == -1 -->
          {L_mess_recipient}
        <!-- ELSE -->
          {L_mess_from}
        <!-- ENDIF -->
      </th>
      <th class="c_c">{L_mess_subject}</th>
    </tr>
  <!-- BEGIN messages -->
    <tr>
      <!-- IF MESSAGE_CLASS != -1 -->
        <th>
          <input name="mark[{messages.ID}]" value="{messages.ID}" type="checkbox" class="msg_checkbox">
        </th>
      <!-- ENDIF -->    	
      <th>{messages.DATE}</th>
      <th>
        <!-- IF messages.FROM_ID -->
          <a href="messages.php?mode=write&id={messages.FROM_ID}&subject={L_mess_answer_prefix}{messages.SUBJ_SANITIZED}">
            <img src="{dpath}img/m.gif" alt="{L_mess_answer}" border="0">
          </a>
        <!-- ENDIF -->
        {messages.FROM}
      </th>
      <th>
        {messages.SUBJ}
      </th>
    </tr>
    <tr id='mess{messages.ID}'>
      <!-- IF MESSAGE_CLASS != -1 -->
      <td class="{messages.STYLE}" class="b">
        &nbsp;
      </td>
      <!-- ENDIF -->    	
      <td class="{messages.STYLE} c_l" class="b" colspan="3">
        {messages.TEXT}
      </td>
    </tr>
  <!-- BEGINELSE messages -->
    <tr>
      <td class="c_c notice" class="b" colspan="4">
        {L_msg_warn_no_messages}
      </td>
    </tr>
  <!-- END messages -->

  <!-- IF MESSAGE_CLASS != -1 -->
<!--
    <tr>
      <th style="color: rgb(242, 204, 74);" colspan="4">
        <input onchange="document.getElementById('fullreports2').checked=this.checked" id="fullreports" name="fullreports" type="checkbox">{L_mess_partialreport}
      </th>
    </tr>
-->
    <tr>
      <th colspan="4">
        {L_sys_delete}
        <select onchange="document.getElementById('message_range2').options[this.selectedIndex].selected='true'" id="message_range" name="message_range">
          <option>{L_msg_mark_select}</option>
          <option value="checked">{L_msg_mark_checked}</option>
          <option value="unchecked">{L_msg_mark_unchecked}</option>
          <option value="class">{L_msg_mark_class}</option>
          <option value="all">{L_msg_mark_all}</option>
        </select>
        <input name="msg_delete" value="{L_sys_delete}" type="submit">
      </th>
    </tr>
  <!-- ENDIF -->
  </table>
</form>
