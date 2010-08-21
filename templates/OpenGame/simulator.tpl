<h2>{COE_combatSimulator}</h2>
<form action='simulator.php' method='post'>
  <table>
    <tr>
      <td class="c">&nbsp;</td>
      <td class="c">{sys_attacker}</td>
      <td class="c">{sys_defender}</td>
    </tr>

    <tr><td class="c" colspan="3">{Tech}</td></tr>
    {inputTech}    

    <tr><td class=c colspan=3>{COE_fleet}</td></tr>
    {inputFleet}
    
    <tr><td class=c colspan=3>{COE_defense}</td></tr>
    {inputDefense}

    <tr><td class="c" colspan="3">{sys_resources}</td></tr>
    <tr><th>{Metal}</th><th>&nbsp;</th><th><input type='text' name='resources[metal]' value='0'></th></tr>
    <tr><th>{Crystal}</th><th>&nbsp;</th><th><input type='text' name='resources[crystal]' value='0'></th></tr>
    <tr><th>{Deuterium}</th><th>&nbsp;</th><th><input type='text' name='resources[deuterium]' value='0'></th></tr>
    <tr><th colspan='3'><input type='submit' name='submit' value='{COE_simulate}'></th></tr>
  </table>
  <input type='hidden' name='BE_DEBUG' value="{BE_DEBUG}">
</form>
