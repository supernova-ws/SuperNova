<h2>{L_msg_page_header}</h2>
<h3>{L_msg_compose}</h3>
<!-- INCLUDE int_message_box.tpl -->
<form action="messages.php" method="post">
  <input type="hidden" name="mode" value="write">
  <input type="hidden" name="id" value="{RECIPIENT_ID}">

  <table width="519">
    <tr>
      <td class="c_l">{L_msg_recipient}</td>
      <td class="c_l"><input type="text" name="recipient_name" size="40" value="{RECIPIENT_NAME}" /></td>
    </tr>
    
    <tr>
      <td class="c_l">{L_msg_subject}</td>
      <td class="c_l"><input type="text" name="subject" size="40" maxlength="40" value="{SUBJECT}" /></td>
    </tr>
    
    <tr>
      <td class="c_c" colspan="2"><span class="fl">{L_msg_text}</span><span class="fr">(<span id="cntChars">0</span> / 5000 {characters})</span></td>
    </tr>

    <tr>
      <td class="c_c" colspan="2"><textarea name="text" cols="40" rows="10" size="100" onkeyup="javascript:cntchar(5000)">{TEXT}</textarea></td>
    </tr>
    
    <tr>
      <th colspan="2"><input name="msg_send" type="submit" value="{L_sys_send}" /></th>
    </tr>
  </table>
</form>
