<br />
   <form action="alliance.php?mode=admin&edit=requests&show={id}&sort=0" method="POST">
   <tr>
     <th colspan=2>{Request_from}</th>
   </tr>
   <tr>
     <th colspan=2>{ally_request_text}</th>
   </tr>
   <tr>
     <td class="c" colspan=2>{Request_answer}</td>
   </tr>
   <tr>
     <th>{Motive_optional} <span id="cntChars">0</span> / 500 {characters}</th>
     <th><textarea name="text" cols=40 rows=10 onkeyup="javascript:cntchar(500)"></textarea></th>
   </tr>
   <tr>
     <th>&#160;</th>
     <th><input type="submit" name="action" value={Accept_cand}>
	     <input type="submit" name="action" value={Reject_cand}>
         </th>
   </tr>
   <tr>
     <td colspan=2>&#160;</td>
   </tr>
   </form>