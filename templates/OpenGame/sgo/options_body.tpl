<br>
<center>
<h2>GameO</h2>

<form action="?mode=change" method="post">
 <table width="519">

     <tbody><tr><td class="c" colspan="2">GameO Configuration</td></tr>
<tr>
      <th>Server name</th>
   <th><input name="game_name" size="20" value="{game_name}" type="text"></th>
    </tr>
  <tr>
  <th>Powerd by # line<br><small></small></th>
   <th><input name="copyright" size="40" maxlength="254" value="{copyright}" type="text"></th>
  </tr>

   <tr><th colspan="2"></th></tr>
  

	<!-- Planet Settings -->
  
  <tr>
  <td class="c" colspan="2">Game options</td>
  </tr>
   <th>Max Galaxies</th>
   <th><input name="max_galaxy" maxlength="80" size="10" value="{max_galaxy}" type="text"> 
   </th>
  <tr>
   <th>Max Systems</th>
   <th><input name="max_system" maxlength="80" size="10" value="{max_system}" type="text"> 
   </th>
  <tr>
   <th>Max Posistion</th>
   <th><input name="max_position" maxlength="80" size="10" value="{max_position}" type="text"> 
   </th>
  <tr>
   <th>Fleet Speed</th>
   <th><input name="fleet_speed" maxlength="80" size="10" value="{fleet_speed}" type="text"> 
   </th>
  <tr>
   <th>Game Speed</th>
   <th><input name="game_speed" maxlength="80" size="10" value="{game_speed}" type="text"> 
   </th>
  <tr>
   <th>Research*Upgrading*Updating</th>
   <th><input name="allow_invetigate_while_lab_is_update" maxlength="80" size="10" value="{allow_invetigate_while_lab_is_update}" type="text"> 
   </th>
  <tr>
   <th>Registered Users(For fun if u wish)</th>
   <th><input name="users_amount" maxlength="80" size="10" value="{users_amount}" type="text"> 
   </th>
  <tr>
   <th>Initial fields</th>
   <th><input name="initial_fields" maxlength="80" size="10" value="{initial_fields}" type="text"> 
   </th>
  </tr>
  <tr>
   <th>Resource multiplier</th>
   <th>x<input name="resource_multiplier" maxlength="80" size="10" value="{resource_multiplier}" type="text">
   </th>
  </tr>
  <tr>
   <th>Starting Metal</th>
   <th><input name="metal_basic_income" maxlength="80" size="10" value="{metal_basic_income}" type="text"> 
  </tr>
  <tr>
   <th>Starting Crystal</th>
   <th><input name="crystal_basic_income" maxlength="80" size="10" value="{crystal_basic_income}" type="text"> 
   </th>
  </tr>
  <tr>
   <th>Starting Deuterium</th>
   <th><input name="deuterium_basic_income" maxlength="80" size="10" value="{deuterium_basic_income}" type="text"> 
   </th>
  </tr>
  <tr>
   <th>Starting Energy</th>
   <th><input name="energy_basic_income" maxlength="80" size="10" value="{energy_basic_income}" type="text"> 
   </th>
  <tr>
  </tr>

	<!-- Miscelaneos Settings -->

    <tr>
     <td class="c" colspan="2">Error</td>
	</tr>

  <tr>
     <th>Show errors</a></th>
   <th>
    <input name="debug"{debug} type="checkbox" />
   </th>
  </tr>

  <tr>
   <th colspan="2"><input value="Submit" type="submit"></th>
  </tr>


   
 </tbody></table>

 
</form>

</center>
