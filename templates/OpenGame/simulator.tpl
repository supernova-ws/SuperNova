<h2>{COE_combatSimulator}</h2>
<form action='simulator.php' method='post'>
  <table>
    <tr>
      <td class="c">&nbsp;</td>
      <td class="c">{L_sys_attacker}</td>
      <td class="c">{L_sys_defender}</td>
    </tr>

    <!-- BEGIN simulator -->
    <tr>
      <!-- IF simulator.ID -->
        <th>{simulator.NAME}</th>
        <th>
          <!-- IF simulator.ID < 400 -->
            <input type='text' name='attacker[{simulator.ID}]' value='{simulator.ATTACKER}'>
          <!-- ELSE -->
            &nbsp;
          <!-- ENDIF -->
        </th>
        <th><input type='text' name='defender[{simulator.ID}]' value='{simulator.DEFENDER}'></th>
      <!-- ELSE -->
        <td class=c colspan=3>{simulator.NAME}</td>
      <!-- ENDIF -->
    </tr>
    <!-- END simulator -->

    <tr><th colspan='3'><input type='submit' name='submit' value='{L_COE_simulate}'></th></tr>
  </table>
  <input type='hidden' name='BE_DEBUG' value="{BE_DEBUG}">
</form>
