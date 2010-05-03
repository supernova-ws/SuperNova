<br>
<table width="569"><tbody>
<tr><td class="c" colspan="5">{Prodaction_level}</td></tr>
<tr>
	<th>{Prodactions}</th>
	<th>{production_level}%</th>
	<th width="250">
      <div style="border: 1px solid rgb(153, 153, 255); width: 250px;">
        <div id="AlmDBar" style="background-color: {production_level_barcolor}; width: {production_level_bar}px; float:left;">&nbsp;</div>
      </div>
	</th>
</tr>
</tbody></table>
<br>
<form action="" method="post">
  <table width="569"><tbody>
    <tr>
      <td class="c" colspan="6">{Production_of_resources_in_the_planet}</td>
    </tr>
    <tr>
      <th height="22"></th>
      <th width="60">{Metal}</th>
      <th width="60">{Crystal}</th>
      <th width="60">{Deuterium}</th>
      <th width="60">{Energy}</th>
    </tr>
    <tr>
      <th height="22">{Basic_income}</th>
      <td class="k">{metal_basic_income}</td>
      <td class="k">{crystal_basic_income}</td>
      <td class="k">{deuterium_basic_income}</td>
      <td class="k">{energy_basic_income}</td>
    </tr>
    {resource_row}
    <tr>
      <th height="22">Всего:</th>
      <td class="k">{metal_total}</td>
      <td class="k">{crystal_total}</td>
      <td class="k">{deuterium_total}</td>
      <td class="k">{energy_total}</td>
    </tr>
    <tr>
      <th height="22">{Stores_capacity}</th>
      <td class="k">{metal_max}</td>
      <td class="k">{crystal_max}</td>
      <td class="k">{deuterium_max}</td>
      <td class="k"><font color="#00ff00">-</font></td>
    </tr>
    <tr><td class="k" colspan="6"><input name="action" value="{Calcule}" type="submit"></td></tr>
  </tbody></table>
</form>

<table width="569"><tbody>
  <tr><td class="c" colspan="4">{Widespread_production}</td></tr>
  <tr>
    <th>&nbsp;</th>
    <th>{Daily}</th>
    <th>{Weekly}</th>
    <th>{Monthly}</th>
  </tr>
  <tr>
    <th>{Metal}</th>
    <th>{daily_metal}</th>
    <th>{weekly_metal}</th>
    <th>{monthly_metal}</th>
  </tr>
  <tr>
    <th>{Crystal}</th>
    <th>{daily_crystal}</th>
    <th>{weekly_crystal}</th>
    <th>{monthly_crystal}</th>
  </tr>
  <tr>
    <th>{Deuterium}</th>
    <th>{daily_deuterium}</th>
    <th>{weekly_deuterium}</th>
    <th>{monthly_deuterium}</th>
  </tr>
</tbody></table>
<br>
<table width="569"><tbody>
  <tr><td class="c" colspan="3">{Storage_state}</td></tr>
  <tr>
    <th>{Metal}</th>
    <th>{metal_storage}</th>
    <th width="250">
      <div style="border: 1px solid rgb(153, 153, 255); width: 250px;">
        <div id="AlmMBar" style="background-color: {metal_storage_barcolor}; width: {metal_storage_bar}px; float: left;">&nbsp;</div>
      </div>
    </th>
  </tr>
  <tr>
    <th>{Crystal}</th>
    <th>{crystal_storage}</th>
    <th width="250">
      <div style="border: 1px solid rgb(153, 153, 255); width: 250px;">
        <div id="AlmCBar" style="background-color: {crystal_storage_barcolor}; width: {crystal_storage_bar}px; opacity: 0.98; float: left;">&nbsp;</div>
      </div>
    </th>
  </tr>
  <tr>
    <th>{Deuterium}</th>
    <th>{deuterium_storage}</th>
    <th width="250">
      <div style="border: 1px solid rgb(153, 153, 255); width: 250px;">
        <div id="AlmDBar" style="background-color: {deuterium_storage_barcolor}; width: {deuterium_storage_bar}px; float: left;">&nbsp;</div>
      </div>
    </th>
  </tr>
{ClickBanner}
</tbody></table>
<br>