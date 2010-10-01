<br />

<script type="text/javascript"><!--
var production = Array();
var unit_selected;
var unit_hovered;
var unit_cache = Array();

function show_unit_info(unit_id)
{
  element_cache['unit' + unit_id].style.borderColor="#0000FF";

  if(unit_selected)
  {
    return;
  }

  if(!unit_cache[unit_id])
  {
    var unit = production[unit_id];
    
    var result = '';
    result += '<b>' + unit['name'] + ', ' + language['level'] + ' ' + unit['level'] + '</b><br>';
    result += unit['description'] + '<br>';
    result += unit['price'];
    result += unit['time'];
    result += unit['resources_left'] + '<br>';
    if(unit['energy_balance'] != 0)
    {
      result += '<font color=';
      if(unit['energy_balance'] > 0)
      {
        result += 'lime';
      }
      else
      {
        result += 'red';
      }
      result += '>' + language['sys_energy'] + ': ' + unit['energy_balance'] + '</font><br>';
    }
    result += '<br>' + unit['build_link'];

    unit_cache[unit_id] = result;
  }
  
  element_cache['unit_info'].innerHTML = unit_cache[unit_id];
}

function select_unit(unit_id)
{
  if(unit_selected)
  {
    element_cache['unit' + unit_selected].style.borderColor="";
    unit_selected = undefined;
    show_unit_info(unit_id);
  }
  unit_selected = unit_id;
  element_cache['unit' + unit_id].style.borderColor="#0000FF";
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
<table width=530 id="unit_table">
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
         <div style="cursor: pointer; position: relative; height: 100px; width: 100px; font-size: 80%; border: 3px solid;" id="unit{production.ID}" unit_id="{production.ID}">
           <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
             <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="100%" height="100%" onclick="select_unit({production.ID})"> <!-- onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})"> -->
           </span>

           <span style="position: absolute; top: 20%; left: 0px; width: 100%; height: 20%; font-size: 100%;" class="icon_alpha"> <!--  onclick="select_unit({production.ID})" onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})"> -->
             {production.NAME}
           </span>

           <span style="position: absolute; bottom: 0; right: 0; width: 80%; height: 10%; text-align: right; font-size: 100%;" class="icon_alpha">
             <!-- IF production.LEVEL -->
               {production.LEVEL}
             <!-- ENDIF -->
           </span>

           <span style="position: absolute; top: 0px; right: 0px;" class="icon_alpha" onclick="document.location='infos.php?gid={production.ID}'">
             <div class="icons icon-info"></div>
           </span>
           
           <!-- IF production.CAN_BUILD -->
             <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?cmd=insert&building={production.ID}'">
               <div class="icons icon-plus"></div>
             </span>
  
             <!-- IF production.LEVEL -->
               <span style="position: absolute; bottom: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?cmd=destroy&building={production.ID}'">
                 <div class="icons icon-minus" title="{L_bld_destroy}: {L_sys_metal} {production.DESTROY_METAL}; {L_sys_crystal} {production.DESTROY_CRYSTAL}; {L_sys_deuterium} {production.DESTROY_DEUTERIUM}; {L_sys_time} {production.DESTROY_TIME}"></div>
               </span>
             <!-- ENDIF -->
           <!-- ENDIF -->

           <!-- IF production.ID == NOW_BUILDING -->
             <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?listid=1&cmd=cancel&planet=2'">
               <div class="icons icon-cancel"></div>
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
  name: '{production.NAME}',
  level: '{production.LEVEL}',
  description: '{production.DESCRIPTION}', 
  price: '{production.PRICE}', 
  time: '{production.TIME}', 
  resources_left: '{production.RESOURCES_LEFT}',
  metal_balance: '{production.METAL_BALANCE}',
  crystal_balance: '{production.CRYSTAL_BALANCE}',
  deuterium_balance: '{production.DEUTERIUM_BALANCE}',
  energy_balance: '{production.ENERGY_BALANCE}',
  build_link: '{production.BUILD_LINK}'
};
/*
   jQuery("#unit{production.ID}").mouseenter(function(event, ui) {
     show_unit_info({production.ID});
   });

   jQuery("#unit{production.ID}").mouseleave(function(event, ui) {
     unborder_unit({production.ID});
   });
*/
--></script>
	<!-- END production -->
     </tr>
</table>

<script type="text/javascript"><!--
jQuery(document).ready(function() {
  jQuery("#unit_table").delegate("*[unit_id]", "mouseenter", function(event, ui) {
    show_unit_info(jQuery(this).attr('unit_id'));
  });

  jQuery("#unit_table").delegate("*[unit_id]", "mouseleave", function(event, ui) {
    unborder_unit(jQuery(this).attr('unit_id'));
  });

  jQuery("#unit_table").delegate("*[unit_id]", "click", function(event, ui) {
    select_unit(jQuery(this).attr('unit_id'));
  });
});

language = 
{
  level: '{L_level}',
  sys_energy: '{L_sys_energy}'
};
--></script>
