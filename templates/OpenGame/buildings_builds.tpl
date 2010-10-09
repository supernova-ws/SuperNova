<br />

<script type="text/javascript"><!--
var production = Array();
var unit_selected = null;
var unit_cache = Array();

function make_resource_row(resource_name, value, value_destroy)
{
  value = parseInt(value);

  var result = '';

  if(value > 0)
  {
    if(parseInt(value) >= parseInt(planet[resource_name]))
    {
      color = 'lime';
    }
    else
    {
      color = 'red';
    }
    
    result += '<tr align="right">';
    result += '<td>' + language['sys_' + resource_name] + '</td>';
    result += '<td>' + sn_format_number(value, 0, 'lime', planet[resource_name]) + '</td>';
    result += '<td>' + sn_format_number(parseInt(planet[resource_name]) - parseInt(value), 0, 'lime') + '</td>';
    if(planet['fleet_own'])
    {
      result += '<td>' + sn_format_number(parseInt(planet[resource_name]) + parseInt(planet[resource_name + '_incoming']) - parseInt(value), 0, 'lime') + '</td>';
    }
//    result += '<td>' + sn_format_number(parseInt(value_destroy), 0, 'lime') + '</td>';
    result += '</tr>';
  }

  return result;
}

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
    
    var color = '';
    var result = '';

    result += '<div class="fl" style="margin: 4px">';
    result += '<img border="0" src="' + dpath + 'gebaeude/' + unit['id'] +'.gif" align="top">';
    result += '</div>';

    result += '<div style="margin: 4px; width: 300px;" class="fl"><b>' + unit['name'];
    if(unit['level'])
    {
     result += ', ' + language['level'] + ' ' + unit['level'];
    }
    result += '</b><br>';
    result += unit['description'] + '<br>';
    
    result += '';
//    result += unit['destroy_time'];
//    result += unit['resources_left'] + '<br>';
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
    result += '</div>';

    result += '<div class="fr" style="margin: 2px">';
    result += '<table>';
    result += '<tr>';
    result += '<td>!Ресурс' + language['a'] + '</td>';
    result += '<td>!Построить' + language['a'] + '</td>';
    result += '<td>!Остаток' + language['a'] + '</td>';
    if(planet['fleet_own'])
    {
      result += '<td>!+ флоты' + language['a'] + '</td>';
    }
//    result += '<td>' + language['bld_destroy'] + '</td>';
    result += '</tr>';
    result += make_resource_row('metal', unit['metal'], unit['destroy_metal']);
    result += make_resource_row('crystal', unit['crystal'], unit['destroy_crystal']);
    result += make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium']);
    result += '</table>';
    result += unit['time'];
    result += '</div>';

    unit_cache[unit_id] = result;
  }
  
  element_cache['unit_info'].innerHTML = unit_cache[unit_id];
}

function select_unit(unit_id)
{
  if(unit_selected == unit_id)
  {
    unit_selected = null;
  }
  else
  {
    if(unit_selected)
    {
      document.getElementById('unit' + unit_selected).style.borderColor="";
      unit_selected = null;
      show_unit_info(unit_id);
    }
    unit_selected = unit_id;
  }
}

function unborder_unit(unit_id)
{
  if(unit_selected != unit_id)
  {
    document.getElementById('unit' + unit_id).style.borderColor="";
  }
}
--></script>
{BuildListScript}<div id='deb'></div>
<table width=530 id="unit_table">
	{BuildList}
	<tr>
		<td colspan="5" class="c" align="center">
			{L_bld_theyare} {field_libre} {L_bld_cellfree} ( <font color="#00FF00">{planet_field_current}</font> / <font color="#FF0000">{planet_field_max}</font> )
		</th >
	</tr>
   <tr><td colspan=6 id="unit_info" style="height: 130px; vertical-align: top;">&nbsp;</td></tr>
   <tr>
	<!-- BEGIN production -->
       <td class="l" align="center">
         <div style="cursor: pointer; position: relative; height: 120px; width: 120px; font-size: 100%; border: 3px solid;" id="unit{production.ID}" unit_id="{production.ID}">
           <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
             <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="100%" height="100%"> <!-- onclick="select_unit({production.ID})"> <!-- onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})"> -->
           </span>

           <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 4ex; font-size: 100%;" class="icon_alpha"> <!--  onclick="select_unit({production.ID})" onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})"> -->
             {production.NAME}
           </span>

           <span style="position: absolute; bottom: 0px; right: 0px;" class="icon_alpha" onclick="document.location='infos.php?gid={production.ID}'">
             <div class="icons icon-info"></div>
           </span>
           
           <!-- IF production.CAN_BUILD -->
             <span style="position: absolute; top: 0px; right: 0px;" class="icon_alpha" onclick="document.location='?cmd=insert&building={production.ID}'">
               <div class="icons icon-plus"></div>
             </span>
  
             <!-- IF production.LEVEL -->
               <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?cmd=destroy&building={production.ID}'">
                 <div class="icons icon-minus" title="{L_bld_destroy}: {L_sys_metal} {production.DESTROY_METAL}; {L_sys_crystal} {production.DESTROY_CRYSTAL}; {L_sys_deuterium} {production.DESTROY_DEUTERIUM}; {L_sys_time} {production.DESTROY_TIME}"></div>
               </span>
             <!-- ENDIF -->
           <!-- ENDIF -->

           <!-- IF production.ID == NOW_BUILDING -->
             <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?listid=1&cmd=cancel&planet=2'">
               <div class="icons icon-cancel"></div>
             </span>
             <!-- DEFINE $BUILDINGPLUSONE = '+1' -->
           <!-- ELSE -->
             <!-- DEFINE $BUILDINGPLUSONE = '' -->
           <!-- ENDIF -->

           <span style="position: absolute; top: 0; left: 20%; width: 60%; height: 16px; text-align: center; font-size: 120%;" class="icon_alpha">
             <!-- IF production.LEVEL -->
               {production.LEVEL}
             <!-- ENDIF -->
               {$BUILDINGPLUSONE}
           </span>

           <span style="position: absolute; top: 46px; left: 2%; width: 96%; height: 6ex; font-size: 100%; text-align: left;" class="icon_alpha"> <!--  onclick="select_unit({production.ID})" onmouseout="unborder_unit({production.ID})" onmouseover="show_unit_info({production.ID})"> -->
             <div class="fl">{L_sys_metal}</div><div class="fr">{production.METAL_REST}</div><br>
             <div class="fl">{L_sys_crystal}</div><div class="fr">{production.CRYSTAL_REST}</div><br>
             <div class="fl">{L_sys_deuterium}</div><div class="fr">{production.DEUTERIUM_REST}</div>
           </span>

         </div>
       </td>
       <!-- IF (production.S_ROW_COUNT + 1) mod 5 == 0 -->
       </tr><tr>
       <!-- ENDIF -->
<script type="text/javascript"><!--
production[{production.ID}] = 
{
  id: '{production.ID}',
  name: '{production.NAME}',
  level: '{production.LEVEL}',
  description: '{production.DESCRIPTION}', 
  price: '{production.PRICE}', 
  
  metal: '{production.METAL}',
  crystal: '{production.CRYSTAL}',
  deuterium: '{production.DEUTERIUM}',
  time: '{production.TIME}',

  destroy_metal: '{production.DESTROY_METAL}',
  destroy_crystal: '{production.DESTROY_CRYSTAL}',
  destroy_deuterium: '{production.DESTROY_DEUTERIUM}',
  destroy_time: '{production.DESTROY_TIME}',

  resources_left: '{production.RESOURCES_LEFT}',

  metal_balance: '{production.METAL_BALANCE}',
  crystal_balance: '{production.CRYSTAL_BALANCE}',
  deuterium_balance: '{production.DEUTERIUM_BALANCE}',
  energy_balance: '{production.ENERGY_BALANCE}',
  
  build_link: '{production.BUILD_LINK}'
};
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
  a: '',

  level: '{L_level}',
  bld_destroy: '{L_bld_destroy}',
  sys_metal: '{L_sys_metal}',
  sys_crystal: '{L_sys_crystal}',
  sys_deuterium: '{L_sys_deuterium}',
  sys_energy: '{L_sys_energy}'
};

planet =
{
  fleet_own: '{FLEET_OWN}',
  
  metal: '{METAL}',
  crystal: '{CRYSTAL}',
  deuterium: '{DEUTERIUM}',

  metal_incoming: '{METAL_INCOMING}',
  crystal_incoming: '{CRYSTAL_INCOMING}',
  deuterium_incoming: '{DEUTERIUM_INCOMING}'
};

var dpath = '{dpath}';
--></script>
<!-- INCLUDE page_hint.tpl -->
