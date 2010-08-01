<h2>{adm_dm_title}</h2>
<form action="admin_darkmatter.php" method="post">
  <table width="305"><tbody>
    <tr><td class="c" colspan="2">{adm_dm_title}</td></tr>
    <tr>
      <th>{adm_dm_user}</th>
      <th><input name="id_user" type="text" value="{id_user}"/></th>
    </tr>
    <tr><td class="c" colspan="2">{adm_dm_oruser}</td></tr>
    <tr>
      <th>{adm_dm_planet}</th>
      <th><input name="id_planet" type="text" value="{id_planet}"/></th>
    </tr>
    <tr>
      <th>{dark_matter}</th>
      <th><input name="points" type="text" value="{points}" /></th>
    </tr>
    <tr>
      <th>{L_adm_reason}</th>
      <th><input name="reason" type="text" value="{reason}" /></th>
    </tr>
    <tr><th colspan="2"><input type="Submit" value="{adm_apply}" /></th></tr>
  </tbody></table>
</form>
{message}
