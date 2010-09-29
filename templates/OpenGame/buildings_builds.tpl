<br />

<script type="text/javascript"><!--
var production = Array();
var unit_selected;

function show_unit_info(unit_id)
{
  document.getElementById('unit' + unit_id).style.borderColor="#0000FF";
  if(unit_selected)
  {
    return;
  }

  var unit = production[unit_id];
  
  var result = unit['description'] + "<br>";
  result += unit['price'];
  result += unit['time'];
  result += unit['resources_left'] + "<br>";
  result += unit['build_link'];

  document.getElementById('unit_info').innerHTML = result;
}

function select_unit(unit_id)
{
  if(unit_selected)
  {
    document.getElementById('unit' + unit_selected).style.borderColor="";
    unit_selected = undefined;
    show_unit_info(unit_id);
  }
  unit_selected = unit_id;
  document.getElementById('unit' + unit_id).style.borderColor="#0000FF";
}

function unborder_unit(unit_id)
{
  if(unit_selected != unit_id)
  {
    document.getElementById('unit' + unit_id).style.borderColor="";
  }
}
--></script>

{BuildListScript}
<table width=530>
	{BuildList}
	<tr>
		<td colspan="6" class="c" align="center">
			{L_bld_theyare} {field_libre} {L_bld_cellfree} ( <font color="#00FF00">{planet_field_current}</font> / <font color="#FF0000">{planet_field_max}</font> )
		</th >
	</tr>
   <tr><td colspan=6 id="unit_info" style="height: 10em; vertical-align: top;">&nbsp;</td></tr>
   <tr>
	<!-- BEGIN production -->
       <td class="l" align="center">
         <div style="cursor: pointer; position: relative; height: 100px; width: 100px; font-size: 80%; border: 3px solid;" id="unit{production.ID}">
           <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
                <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="100%" height="100%" onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})" onclick="select_unit({production.ID})">
           </span>

           <span style="position: absolute; top: 15%; left: 0px; width: 100%; height: 20%; font-size: 100%;" class="icon_alpha">
             {production.NAME}
           </span>

           <span style="position: absolute; top: 88%; left: 10%; width: 90%; height: 10%; text-align: right; font-size: 100%;" class="icon_alpha">
               <!-- IF production.LEVEL -->
                 {production.LEVEL}
               <!-- ENDIF -->
           </span>

           <span style="position: absolute; top: 0px; left: 85%; width: 15%; font-size: 100%;" class="icon_alpha">
             <a href="infos.php?gid={production.ID}">?</a>
           </span>

           <!-- IF production.CAN_BUILD -->
             <span style="position: absolute; top: 0px; left: 0%; width: 15%; font-size: 100%;" class="icon_alpha">
               <a href="?cmd=insert&building={production.ID}">+1</a>
             </span>
  
             <!-- IF production.LEVEL -->
               <span style="position: absolute; top: 83%; left: 2%; width: 15%; height: 15%">
                 <a href="?cmd=destroy&building={production.ID}">
                   <img src="images/r1.png" height="100%" width="100%" title="{L_bld_destroy}: {L_sys_metal} {production.DESTROY_METAL}; {L_sys_crystal} {production.DESTROY_CRYSTAL}; {L_sys_deuterium} {production.DESTROY_DEUTERIUM}; {L_sys_time} {production.DESTROY_TIME}">
                 </a>
               </span>
             <!-- ENDIF -->
           <!-- ENDIF -->

           <!-- IF production.ID == NOW_BUILDING -->
             <span style="position: absolute; top: 0%; left: 0%; height: 15%" class="icon_alpha">
               <a href="?listid=1&cmd=cancel&planet=2">STOP</a>
             </span>
           <!-- ENDIF -->
         </div>
       </td>
       <!-- IF (production.S_ROW_COUNT + 1) mod 6 == 0 -->
       </tr><tr>
       <!-- ENDIF -->
<script type="text/javascript"><!--
production[{production.ID}] = 
{
  description: '{production.DESCRIPTION}', 
  price: '{production.PRICE}', 
  time: '{production.TIME}', 
  resources_left: '{production.RESOURCES_LEFT}',
  metal_balance: '{production.METAL_BALANCE}',
  crystal_balance: '{production.CRYSTAL_BALANCE}',
  deuterium_balance: '{production.DEUTERIUM_BALANCE}',
  build_link: '{production.BUILD_LINK}'
};
--></script>
	<!-- END production -->
     </tr>
</table>