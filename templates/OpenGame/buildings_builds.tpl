<center>
<br />
{BuildListScript}
<table width=530>
	{BuildList}
	<tr>
		<th >{bld_usedcells}</th>
		<th colspan="2" >
			<font color="#00FF00">{planet_field_current}</font> / <font color="#FF0000">{planet_field_max}</font> {bld_theyare} {field_libre} {bld_cellfree}
		</th >
	</tr>
	<!-- BEGIN production -->
     <tr>
       <td class="l" align="center" width="120">
         <a href="infos.php?gid={production.ID}">
           {production.NAME}<br>
           {production.LEVEL}<br>
           <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="120" height="120">
         </a>
       </td>
       <td class="l">
         <a href="infos.php?gid={production.ID}"></a><br>
         {production.DESCRIPTION}<br>
         {production.PRICE}
         {production.TIME}
         {production.RESOURCES_LEFT}<br>
         {production.ENERGY_BALANCE}
         <!-- IF production.METAL_BALANCE -->
           <br>{production.METAL_BALANCE}
         <!-- ENDIF -->
         <!-- IF production.CRYSTAL_BALANCE -->
           <br>{production.CRYSTAL_BALANCE}
         <!-- ENDIF -->
         <!-- IF production.DEUTERIUM_BALANCE -->
           <br>{production.DEUTERIUM_BALANCE}
         <!-- ENDIF -->
       </td>
       <td class="k">{production.BUILD_LINK}</td>
     </tr>
	<!-- END production -->
	{BuildingsList}
</table>
<br />
</center>