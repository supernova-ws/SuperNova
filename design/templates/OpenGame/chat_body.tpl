<!--[if IE]><style type="text/css"><!-- #chat_box {width: 80%;} --></style><![endif]-->
<script type="text/javascript">
  var chat_type = "{chat_type}";
  var ally_id = "{ally_id}";
</script>

<script type="text/javascript" src="js/chat.js"></script>
<br />
<form name="chat_form">
<table id="chat_box"><tbody>

  <tr><td class="c"><span class="fl"><b>{chat_disc}</b></span><span class="fr"><a href=# onClick="MessageHistory()">{chat_history}</a></span></td></tr>

  <tr><th><div id="shoutbox" style="margin: 5px; vertical-align: text-top; height: 380px; overflow:auto;"></div></th></tr>

  <tr><th nowrap>{chat_message}:&nbsp;<select name="color" id="chat_color">
  	<option value="white">white</option>
  	<option value="blue">blue</option>
  	<option value="yellow">yellow</option>
  	<option value="green">green</option>
  	<option value="pink">pink</option>
  	<option value="red">red</option>
  	<option value="orange">orange</option>
  </select>&nbsp;<input name="msg" type="text" id="msg" style="width:70%" maxlength="120" onKeyPress="if(event.keyCode == 13){ addMessage(); } if (event.keyCode==60 || event.keyCode==62) event.returnValue = false; if (event.which==60 || event.which==62) return false;">&nbsp;<input type="button" name="send" value="{chat_send}" id="send" onClick="addMessage()"> 
  </th></tr>

  <tr>
    <th nowrap>
      <div nowrap>  
        <img src="design/images/smileys/aggressive.gif" align="absmiddle" title=":agr:" alt=":agr:" onClick="addSmiley(':agr:')">
        <img src="design/images/smileys/angel.gif" align="absmiddle" title=":angel:" alt=":angel:" onClick="addSmiley(':angel:')">
        <img src="design/images/smileys/bad.gif" align="absmiddle" title=":bad:" alt=":bad:" onClick="addSmiley(':bad:')">
        <img src="design/images/smileys/blink.gif" align="absmiddle" title="o0" alt="o0" onClick="addSmiley(':blink:')">
        <img src="design/images/smileys/blush.gif" align="absmiddle" title=":blush:" alt=":blush:" onClick="addSmiley(':blush:')">
        <img src="design/images/smileys/bomb.gif" align="absmiddle" title=":bomb:" alt=":blush:" onClick="addSmiley(':bomb:')">
        <img src="design/images/smileys/clapping.gif" align="absmiddle" title=":clap:" alt=":clap:" onClick="addSmiley(':clap:')">
        <img src="design/images/smileys/cool.gif" align="absmiddle" title=":cool:" alt=":cool:" onClick="addSmiley(':cool:')">
        <img src="design/images/smileys/cray.gif" align="absmiddle" title=":c:" alt=":c:" onClick="addSmiley(':c:')">
        <img src="design/images/smileys/crazy.gif" align="absmiddle" title=":crz:" alt=":crz:" onClick="addSmiley(':crz:')">
        <img src="design/images/smileys/diablo.gif" align="absmiddle" title=":diablo:" alt=":diablo:" onClick="addSmiley(':diablo:')">
        <img src="design/images/smileys/dirol.gif" align="absmiddle" title=":cool2:" alt=":cool2:" onClick="addSmiley(':cool2:')">
        <img src="design/images/smileys/fool.gif" align="absmiddle" title=":s:" alt=":s:" onClick="addSmiley(':fool:')">

        <img src="design/images/smileys/give_rose.gif" align="absmiddle" title=":rose:" alt=":rose:" onClick="addSmiley(':rose:')">
        <img src="design/images/smileys/good.gif" align="absmiddle" title=":good:" alt=":good:" onClick="addSmiley(':good:')">
        <img src="design/images/smileys/huh.gif" align="absmiddle" title=":huh:" alt=":huh:" onClick="addSmiley(':huh:')">
        <img src="design/images/smileys/lol.gif" align="absmiddle" title=":D" alt=":D" onClick="addSmiley(':D:')"><br>
        <img src="design/images/smileys/mellow.gif" align="absmiddle" title=":/" alt=":/" onClick="addSmiley(':/')"> 
        <img src="design/images/smileys/yu.gif" align="absmiddle" title=":yu" alt=":yu" onClick="addSmiley(':yu')">
        <img src="design/images/smileys/unknw.gif" align="absmiddle" title=":unknw:" alt=":unknw:" onClick="addSmiley(':unknw:')">
        <img src="design/images/smileys/sad.gif" align="absmiddle" title=":(" alt=":(" onClick="addSmiley(':sad')">
        <img src="design/images/smileys/smile.gif" align="absmiddle" title=":)" alt=":)" onClick="addSmiley(':smile')">
        <img src="design/images/smileys/shok.gif" align="absmiddle" title=":o" alt=":o" onClick="addSmiley(':shok:')"> 
        <img src="design/images/smileys/rofl.gif" align="absmiddle" title=":rofl" alt=":rofl" onClick="addSmiley(':rofl')">
        <img src="design/images/smileys/blackeye.gif" align="absmiddle" title=":eye" alt=":eye" onClick="addSmiley(':eye')">
        <img src="design/images/smileys/tongue.gif" align="absmiddle" title=":p" alt=":p" onClick="addSmiley(':p')">
        <img src="design/images/smileys/wink.gif" align="absmiddle" title=";)" alt=";)" onClick="addSmiley(';)')">                

        <img src="design/images/smileys/yahoo.gif" align="absmiddle" title=":yahoo:" alt=":yahoo:" onClick="addSmiley(':yahoo:')">
        <img src="design/images/smileys/mill.gif" align="absmiddle" title=":tratata:" alt=":tratata:" onClick="addSmiley(':tratata:')">
        <img src="design/images/smileys/friends.gif" align="absmiddle" title=":fr:" alt=":fr:" onClick="addSmiley(':fr')">
        <img src="design/images/smileys/drinks.gif" align="absmiddle" title=":dr:" alt=":dr:" onClick="addSmiley(':dr')">
        <img src="design/images/smileys/tease.gif" align="absmiddle" title=":tease:" alt=":tease:" onClick="addSmiley(':tease:')">
      </div>
    </th>
  </tr>
</table>
</form>