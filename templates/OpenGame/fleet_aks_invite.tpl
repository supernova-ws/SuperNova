<br>
<center>
  {vacation}
  <table width="519" border="0" cellpadding="0" cellspacing="1">
   <tr height="20"><td class="c">Игроки в САБ</td><td class="c">Приглашение игрока</td></tr>
   <tr>
     <td width="50%" align="center"><select size="5">{members}</select></td>

     <td>
       <form action="fleet.php?fleet_page=4" method="POST">
         <input type="hidden" name="fleetid" value="{fleetid}">
         <input name="addtogroup" type="text" /> <br /><input type="submit" value="OK" />
       </form><br />
       {add_user_message_mr}
     </td>
   </tr>
 </table>
</center>
