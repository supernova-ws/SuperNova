<h1>{L_adm_pl_comp_title}</h1>
<!-- IF .error -->
  <br>
  <table width="519">
    <tr>
      <td class="c">{L_sys_error}</td>
    </tr>
    <tr><th class="c">
      <ul>
        <!-- BEGIN error -->
          <li>{error.TEXT}</li>
        <!-- END error -->
      </ul>
    </th></tr>
  </table>
  <br>
<!-- ENDIF -->

<form method="get" name="f_compensate">
  <table>
    <tr>
      <th>{L_sys_player}</th>
      <th><input type="text" name="username" value="{username}"></th>
    </tr>
    <tr>
      <th>{L_adm_pl_comp_src}</th>
      <th><input type="text" name="galaxy_src" value="{galaxy_src}" size="3" maxlength="3">:<input type="text" name="system_src" value="{system_src}" size="3" maxlength="3">:<input type="text" name="planet_src" value="{planet_src}" size="3" maxlength="3"></th>
    </tr>
    <tr>
      <th>{L_adm_pl_comp_dst}</th>
      <th><input type="text" name="galaxy_dst" value="{galaxy_dst}" size="3" maxlength="3">:<input type="text" name="system_dst" value="{system_dst}" size="3" maxlength="3">:<input type="text" name="planet_dst" value="{planet_dst}" size="3" maxlength="3"></th>
    </tr>
    <tr>
      <th>{L_adm_pl_comp_bonus}</th>
      <th><input type="text" name="bonus" value="{bonus}" value="1"></th>
    </tr>
    <tr>
      <th colspan=2>
        <input type="submit" name="btn_check" value="{L_adm_pl_comp_check}">
      </tr>
    </th>
  </table>

  <!-- IF CHECK -->
    <table>
      <tr>
        <td>
          {L_sys_planet} [{galaxy_src}:{system_src}:{planet_src}] {L_adm_pl_com_of_plr} <b>{username}</b> {L_adm_pl_comp_destr}<br>
          {L_adm_pl_comp_price} {L_sys_metal} {metal_cost}, {L_sys_crystal} {crystal_cost}, {L_sys_deuterium} {deuterium_cost}<br>
          {L_sys_metal} {metal_bonus}, {L_sys_crystal} {crystal_bonus}, {L_sys_deuterium} {deuterium_bonus} {L_adm_pl_comp_got} [{galaxy_dst}:{system_dst}:{planet_dst}]<br>
        </td>
      </tr>

      <tr>
        <th>
          <!-- IF CHECK == 1 -->
          <input type="submit" name="btn_confirm" value="{L_adm_pl_comp_confirm}">
          <!-- ELSEIF CHECK == 2 -->
          {L_adm_pl_comp_done}
          <!-- ENDIF -->
        </th>
      </tr>
    </table>
  <!-- ENDIF -->
</form>