<br />

<script type="text/javascript"><!--
var dpath = '{dpath}';

var production = Array();
var unit_selected = null;
var eco_bld_style_probe;

language = 
{
  level: '{L_level}',
  bld_destroy: '{L_bld_destroy}',
  construction_time: '{L_ConstructionTime}',
  eco_price: '{L_eco_price}',
  eco_left: '{L_eco_left}',
  eco_left_fleet: '{L_eco_left_fleet}',

  sys_resources: '{L_sys_resources}',
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

function eco_struc_make_resource_row(resource_name, value, value_destroy)
{
  if(value>0)
  {
    element_cache['unit_' + resource_name].style.display = "table-row";

    element_cache[resource_name + '_price'].innerHTML = sn_format_number(value, 0, 'lime', planet[resource_name]);
    element_cache[resource_name + '_left'].innerHTML = sn_format_number(parseInt(planet[resource_name]) - parseInt(value), 0, 'lime');
    if(planet['fleet_own'])
    {
      element_cache[resource_name + '_fleet'].innerHTML = sn_format_number(parseInt(planet[resource_name]) + parseInt(planet[resource_name + '_incoming']) - parseInt(value), 0, 'lime');
    }
  }
  else
  {
    element_cache['unit_' + resource_name].style.display = "none";
  }
}

function eco_struc_show_unit_info(unit_id, no_color)
{
  if(!no_color)
  {
    element_cache['unit' + unit_id].style.borderColor=eco_bld_style_probe;
  }

  if(unit_selected)
  {
    return;
  }

  var unit = production[unit_id];
  var result = '';

  element_cache['unit_image'].src = dpath + 'gebaeude/' + unit['id'] +'.gif';
  element_cache['unit_description'].innerHTML = unit['description'] + '"'+ eco_bld_style_probe + '"';
  element_cache['unit_build_link'].innerHTML = unit['build_link'];

  element_cache['unit_time'].innerHTML = unit['time'];

  eco_struc_make_resource_row('metal', unit['metal'], unit['destroy_metal']);
  eco_struc_make_resource_row('crystal', unit['crystal'], unit['destroy_crystal']);
  eco_struc_make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium']);

  element_cache['unit_name'].innerHTML = unit['name'];
  if(unit['level'])
  {
    element_cache['unit_name'].innerHTML += ', ' + language['level'] + ' ' + unit['level'];
  }

  element_cache['unit_balance'].innerHTML = '';
  if(unit['energy_balance'] != 0)
  {
    result += '<br>';
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

    element_cache['unit_balance'].innerHTML += result;
  }
}

function eco_struc_select_unit(unit_id)
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
      eco_struc_show_unit_info(unit_id);
    }
    unit_selected = unit_id;
  }
}

function eco_struc_unborder_unit(unit_id)
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
  	<td colspan="5" class="c" align="center">
  		{L_bld_theyare} {field_libre} {L_bld_cellfree} ( <font color="#00FF00">{planet_field_current}</font> / <font color="#FF0000">{planet_field_max}</font> )
  	</th >
  </tr>
  
  <tr>
    <td colspan=6 id="unit_info" style=" vertical-align: top;">
      <div>
        <b><div id="unit_name" class="fl"></div></b>
        <span id="unit_build_link" class="fr"></span>
      </div>
      <br>

      <div class="fl" style="margin: 4px;">
        <img id="unit_image" border="0" src="" align="top">
      </div>

      <div>
        <div class="fl" style="display: table-cell; margin-right: 4px;">
          {L_ConstructionTime}<b><span id="unit_time"></span></b><br />

          <table style="margin: 0px;">
            <tr>
              <td class=c>{L_sys_resources}</td>
              <td class=c>{L_eco_price}</td>
              <td class=c>{L_eco_left}</td>
              <td class=c hide_no_fleet="yes">{L_eco_left_fleet}</td>
            </tr>
            <tr id="unit_metal">
              <td>{L_sys_metal}</td>
              <td id="metal_price">0</td>
              <td id="metal_left">0</td>
              <td id="metal_fleet" hide_no_fleet="yes">0</td>
            </tr>
            <tr id="unit_crystal">
              <td>{L_sys_crystal}</td>
              <td id="crystal_price">0</td>
              <td id="crystal_left">0</td>
              <td id="crystal_fleet" hide_no_fleet="yes">0</td>
            </tr>
            <tr id="unit_deuterium">
              <td>{L_sys_deuterium}</td>
              <td id="deuterium_price">0</td>
              <td id="deuterium_left">0</td>
              <td id="deuterium_fleet" hide_no_fleet="yes">0</td>
            </tr>
          </table>
          <b><div id="unit_balance"></div></b>
        </div>

        <div id="unit_description" style="display: table-cell; text-align: justify;"></div>
      </div>

    </td>
  </tr>

  <tr>
    <!-- BEGIN production -->
      <td class="l" align="center">
        <div class="unit_preview" style="" id="unit{production.ID}" unit_id="{production.ID}">
          <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
            <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="100%" height="100%"> <!-- onclick="eco_struc_select_unit({production.ID})"> <!-- onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
          </span>

          <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 5ex; font-size: 100%;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
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

          <span style="position: absolute; top: 46px; left: 0px; width: 100%; font-size: 100%; text-align: left; padding: 0px 2px;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
            <!-- IF production.METAL --><div class="fl">{L_sys_metal}</div><div class="fr">{production.METAL_REST}</div><br><!-- ENDIF -->
            <!-- IF production.CRYSTAL --><div class="fl">{L_sys_crystal}</div><div class="fr">{production.CRYSTAL_REST}</div><br><!-- ENDIF -->
            <!-- IF production.DEUTERIUM --><div class="fl">{L_sys_deuterium}</div><div class="fr">{production.DEUTERIUM_REST}</div><!-- ENDIF -->
          </span>

          <span style="position: absolute; bottom: 2px; right: 18px; width: 84%; font-size: 100%; text-align: left;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
            <div class="fr">{production.TIME}</div>
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
<div id="style_probe"></div>

<script type="text/javascript"><!--
jQuery(document).ready(function() {
  jQuery("#unit_table").delegate("*[unit_id]", "mouseenter", function(event, ui) {
    eco_struc_show_unit_info(jQuery(this).attr('unit_id'));
  });

  jQuery("#unit_table").delegate("*[unit_id]", "mouseleave", function(event, ui) {
    eco_struc_unborder_unit(jQuery(this).attr('unit_id'));
  });

  jQuery("#unit_table").delegate("*[unit_id]", "click", function(event, ui) {
    eco_struc_select_unit(jQuery(this).attr('unit_id'));
  });

  if(!planet['fleet_own'])
  {
    jQuery("[hide_no_fleet]").hide();
  }

  eco_bld_style_probe = sn_probe_style(element_cache['style_probe'], 'border-top-color');

  for(production_id in production)
  {
    eco_struc_show_unit_info(production_id, true);
    break;
  }
});
--></script>
<!-- INCLUDE page_hint.tpl -->
