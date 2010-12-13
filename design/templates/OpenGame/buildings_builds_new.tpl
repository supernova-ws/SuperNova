<br />

<script type="text/javascript"><!--
//var dpath = '{dpath}';

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
    element_cache[resource_name + '_left'].innerHTML = sn_format_number(parseFloat(planet[resource_name]) - parseFloat(value), 0, 'lime');
    if(planet['fleet_own'])
    {
      element_cache[resource_name + '_fleet'].innerHTML = sn_format_number(parseFloat(planet[resource_name]) + parseFloat(planet[resource_name + '_incoming']) - parseFloat(value), 0, 'lime');
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
  element_cache['unit_description'].innerHTML = unit['description'];
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
    result += '<font color=';
    if(unit['energy_balance'] > 0)
    {
      result += 'lime';
    }
    else
    {
      result += 'red';
    }
    result += '>' + language['sys_energy'] + ': ' + unit['energy_balance'] + '</font>';

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

<table width=530 id="unit_table">
  <tr>
  	<th colspan="5" class="c" align="center">
  	  <table width=100% class="noborder">
  	    <tr>
  	      <th width=120px>
           <div id="ov_{QUE_ID}" style="color: lime"></div>
           <div id="ov_{QUE_ID}_timer" style="color: red"></div>
  	      </th>
  	      <th>
           <div id="ov_{QUE_ID}_que"></div>
  	      </th>
  	    </tr>
  	  </table>
  	</th>
  </tr>
  <tr>
  	<td colspan="5" class="c" align="center">
  		{L_bld_theyare} {L_bld_cellfree} {FIELDS_FREE} (<span class="negative">{FIELDS_CURRENT}</span>/<span class="positive">{FIELDS_MAX}</span>)
  	</td>
  </tr>

  <!-- IF METAL > 99999999999 || CRYSTAL > 9999999999 || DEUTERIUM > 9999999999 -->
    <!-- DEFINE $FONT_SIZE = '80%' -->
  <!-- ELSE -->
    <!-- DEFINE $FONT_SIZE = '100%' -->
  <!-- ENDIF -->
  
  <tr>
    <td colspan=6 id="unit_info" style="vertical-align: top;">
      <table cellspacing=0 cellpadding=0 valign=top style="vertical-align: top; border: 0;" class="noborder" width=100%>
        <tr>
          <th width=120px valign=top>
            <div id="unit_name"></div>
          </th>
          <th colspan=3 valign=top width=100%>
            <div id="unit_description" style="text-align: justify; height: 8ex;"></div>
          </th>
        </tr>

        <tr>
        </tr>

        <tr>
          <th width=120px>
            <img id="unit_image" border="0" src="" align="top">
          </th>

          <th valign=top width=240px>
            <div class="fl">{L_ConstructionTime}<span id="unit_time"></span></div>   
            <table style="margin: 0px;">
              <tr>
                <td class=c style="font-size: {$FONT_SIZE}" width=45>{L_sys_resources}</td>
                <td class=c style="font-size: {$FONT_SIZE}" width=65>{L_eco_price}</td>
                <td class=c style="font-size: {$FONT_SIZE}" width=65>{L_eco_left}</td>
                <td class=c style="font-size: {$FONT_SIZE}" width=65 hide_no_fleet="yes">{L_eco_left_fleet}</td>
              </tr>
              <tr id="unit_metal">
                <td style="font-size: {$FONT_SIZE}">{L_sys_metal}</td>
                <td style="font-size: {$FONT_SIZE}" id="metal_price" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="metal_left" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="metal_fleet" hide_no_fleet="yes" align="right">0</td>
              </tr>
              <tr id="unit_crystal">
                <td style="font-size: {$FONT_SIZE}">{L_sys_crystal}</td>
                <td style="font-size: {$FONT_SIZE}" id="crystal_price" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="crystal_left" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="crystal_fleet" hide_no_fleet="yes" align="right">0</td>
              </tr>
              <tr id="unit_deuterium">
                <td style="font-size: {$FONT_SIZE}">{L_sys_deuterium}</td>
                <td style="font-size: {$FONT_SIZE}" id="deuterium_price" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="deuterium_left" align="right">0</td>
                <td style="font-size: {$FONT_SIZE}" id="deuterium_fleet" hide_no_fleet="yes" align="right">0</td>
              </tr>
            </table>
            <div id="unit_build_link"></div>
          </th>
          <th width=240px>
            <div id="unit_balance" ></div>
          </th>
        </tr>
        <tr>
          <th colspan=3 width=100%>
          </th>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <!-- BEGIN production -->
      <td class="l" align="center">
        <div class="unit_preview" style="" id="unit{production.ID}" unit_id="{production.ID}">
          <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
            <img border="0" src="{dpath}gebaeude/{production.ID}.gif" align="top" width="100%" height="100%">
          </span>

          <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 5ex; font-size: 100%;" class="icon_alpha"> 
            {production.NAME}
          </span>

          <span style="position: absolute; bottom: 2px; right: 18px; width: 84%; font-size: 100%; text-align: left;" class="icon_alpha">
            <div class="fr">{production.TIME}</div>
          </span>

          <span style="position: absolute; bottom: 0px; right: 0px;" class="icon_alpha" onclick="document.location='infos.php?gid={production.ID}'">
            <div class="icons icon-info"></div>
          </span>
          
          <!-- IF     production.METAL_REST_NUM >= 100000000000 || production.CRYSTAL_REST_NUM >= 100000000000 || production.DEUTERIUM_REST_NUM >= 100000000000 -->
            <!-- DEFINE $FONT_SIZE = '80%' -->
          <!-- ELSEIF production.METAL_REST_NUM >= 1000000000   || production.CRYSTAL_REST_NUM >= 1000000000   || production.DEUTERIUM_REST_NUM >= 1000000000 -->
            <!-- DEFINE $FONT_SIZE = '90%' -->
          <!-- ELSE -->
            <!-- DEFINE $FONT_SIZE = '100%' -->
          <!-- ENDIF -->
          <span style="position: absolute; top: 45px; left: 0px; width: 100%; font-size: {$FONT_SIZE}; text-align: left;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
            <!-- IF production.METAL --><div><div style="left: 0px; position: absolute;">{L_sys_metal}</div><div class="fr">{production.METAL_REST}</div></div><br><!-- ENDIF -->
            <!-- IF production.CRYSTAL --><div><div style="left: 0px; position: absolute;">{L_sys_crystal}</div><div class="fr">{production.CRYSTAL_REST}</div></div><br><!-- ENDIF -->
            <!-- IF production.DEUTERIUM --><div><div style="left: 0px; position: absolute;">{L_sys_deuterium}</div><div class="fr">{production.DEUTERIUM_REST}</div></div><!-- ENDIF -->
          </span>

          <!-- IF production.ID == NOW_WORKING -->
            <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?listid=1&cmd=cancel&planet=2'">
              <div class="icons icon-cancel"></div>
            </span>
            <!-- IF NOW_BUILDING -->
              <!-- DEFINE $BUILDINGPLUSONE = '+1' -->
            <!-- ELSE -->
              <!-- DEFINE $BUILDINGPLUSONE = '-1' -->
            <!-- ENDIF -->
          <!-- ELSE -->
            <!-- DEFINE $BUILDINGPLUSONE = '' -->
          <!-- ENDIF -->
          <span style="position: absolute; top: 0; left: 20%; width: 60%; height: 16px; text-align: center; font-size: 120%;" class="icon_alpha">
            <!-- IF production.LEVEL -->
              {production.LEVEL}
            <!-- ENDIF -->
            {$BUILDINGPLUSONE}
          </span>

          
          <!-- IF QUE_HAS_PLACE -->
            <!-- IF production.LEVEL -->
              <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?mode={QUE_ID}&action=destroy&unit_id={production.ID}'">
                <div class="icons icon-minus" title="{L_bld_destroy}: {L_sys_metal} {production.DESTROY_METAL}; {L_sys_crystal} {production.DESTROY_CRYSTAL}; {L_sys_deuterium} {production.DESTROY_DEUTERIUM}; {L_sys_time} {production.DESTROY_TIME}"></div>
              </span>
            <!-- ENDIF -->

            <!-- IF QUE_HAS_FIELDS -->
              <span style="position: absolute; top: 0px; right: 0px;" class="icon_alpha" onclick="document.location='?mode={QUE_ID}&action=create&unit_id={production.ID}'">
                <div class="icons icon-plus"></div>
              </span>
            <!-- ENDIF -->
          <!-- ENDIF -->
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
        
        metal: '{production.METAL}',
        crystal: '{production.CRYSTAL}',
        deuterium: '{production.DEUTERIUM}',
        time: '{production.TIME}',

        destroy_metal: '{production.DESTROY_METAL}',
        destroy_crystal: '{production.DESTROY_CRYSTAL}',
        destroy_deuterium: '{production.DESTROY_DEUTERIUM}',
        destroy_time: '{production.DESTROY_TIME}',

        metal_balance: '{production.METAL_BALANCE}',
        crystal_balance: '{production.CRYSTAL_BALANCE}',
        deuterium_balance: '{production.DEUTERIUM_BALANCE}',
        energy_balance: '{production.ENERGY_BALANCE}',
        
        //build_link: '{production.BUILD_LINK}'
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

<script type="text/javascript"><!--
<!-- IF .que -->
sn_timers.unshift(
{
  id: 'ov_{QUE_ID}', 
  type: 3, 
  active: true, 
  start_time: {TIME_NOW}, 
  options: 
  { 
    msg_done: '$lang[Free]', 
    template: '<div class="que_item fl" title="[UNIT_NAME]">\
      <span class="unit_picture"><img src="{-path_prefix-}{dpath}gebaeude/[UNIT_ID].gif" align="top" width="100%" height="100%"></span>\
      <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 2ex; font-size: 100%;" class="icon_alpha">[UNIT_LEVEL]</span>\
      <span style="position: absolute; bottom: 0px; left: 0px; width: 100%; font-size: 100%;" class="icon_alpha">[UNIT_TIME]</span>\
    </div>',
    que: [
      <!-- BEGIN que -->
        ['{que.ID}', production['{que.ID}']['name'], {que.TIME}, {que.AMOUNT}, '{que.LEVEL}'],
      <!-- END que -->
    ]
  }
});
<!-- ENDIF -->
--></script>

<!-- INCLUDE page_hint.tpl -->
