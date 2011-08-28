<br>
 <form action="search.php" method="post">

 <table width="519">
  <tr>
   <td class="c">{Search_in_all_game}</td>
  </tr>
  <tr>
   <th>
    <select name="type">
     <option value="playername"{type_playername}>{Player_name}</option>
     <option value="planetname"{type_planetname}>{Planet_name}</option>
     <option value="ally"{type_ally}>{Alliance}</option>
    </select>
    &nbsp;&nbsp;
    <input type="text" name="searchtext" value="{searchtext}"/>
    &nbsp;&nbsp;

    <input type="submit" value="{Search}" />
   </th>
  </tr>
</table>
</form>
{search_results}
