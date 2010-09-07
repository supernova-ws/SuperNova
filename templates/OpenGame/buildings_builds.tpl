<br />
{BuildListScript}
<table width=530>
	{BuildList}
	<tr>
		<td colspan="3" class="c" align="center">
			{L_bld_theyare} {field_libre} {L_bld_cellfree} ( <font color="#00FF00">{planet_field_current}</font> / <font color="#FF0000">{planet_field_max}</font> )
		</th >
	</tr>
	<!-- BEGIN production -->
     <tr>
       <td class="l" align="center" width="120">
         <a href="infos.php?gid={production.ID}">
           <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="120" height="120">
         </a>
       </td>
       <td class="l">
         <a href="infos.php?gid={production.ID}">
           {production.NAME}
           <!-- IF production.LEVEL -->
             ({L_level} {production.LEVEL})
           <!-- ENDIF -->
         </a><br>
         {production.DESCRIPTION}<br>
         {production.PRICE}
         {production.TIME}
         {production.RESOURCES_LEFT}<br>
         
         <!-- IF production.ENERGY_BALANCE -->
           (<font color="<!-- IF production.ENERGY_BALANCE > 0 -->lime">+<!-- ELSE -->red"><!-- ENDIF -->{production.ENERGY_BALANCE} {L_sys_energy}</font>)
         <!-- ENDIF -->

         <!-- IF production.METAL_BALANCE -->
           <!--<br>{production.METAL_BALANCE}-->
         <!-- ENDIF -->
         <!-- IF production.CRYSTAL_BALANCE -->
           <!--<br>{production.CRYSTAL_BALANCE}-->
         <!-- ENDIF -->
         <!-- IF production.DEUTERIUM_BALANCE -->
           <!--<br>{production.DEUTERIUM_BALANCE}-->
         <!-- ENDIF -->
       </td>
       <td class="k">{production.BUILD_LINK}</td>
     </tr>
	<!-- END production -->
</table>
