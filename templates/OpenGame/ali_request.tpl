<br />
<form action="alliance.php?mode=apply&a={allyid}" method=POST>
<table width=519>
  <tr><td class=c>{ali_req_title}</td></tr>
  <tr>
    <th><span class="fl">{ali_req_text}</span><span class="fr"><span id="cntChars">{chars_count}</span> / 6000 {ali_req_characters}</span><br>
    <textarea name="text" cols=60 rows=10 onkeyup="javascript:cntchar(6000)">{text_apply}</textarea><br>
  </tr>
  <tr><th><span class="fl"><input type="reset" value="{sys_reset}"></span><span class="fr"><input type=submit value="{sys_send}"></span></th></tr>
</table>
</form>

<script language="JavaScript" src="js/wz_tooltip.js"></script>