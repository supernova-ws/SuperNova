function eco_struc_make_resource_row(resource_name, value, value_destroy)
{
  if(value>0)
  {
    document.getElementById('unit_' + resource_name).style.display = "table-row";

    document.getElementById(resource_name + '_price').innerHTML = sn_format_number(value, 0, 'positive', planet[resource_name]);
    document.getElementById(resource_name + '_left').innerHTML = sn_format_number(parseFloat(planet[resource_name]) - parseFloat(value), 0, 'positive');
    if(planet['fleet_own'])
    {
      document.getElementById(resource_name + '_fleet').innerHTML = sn_format_number(parseFloat(planet[resource_name]) + parseFloat(planet[resource_name + '_incoming']) - parseFloat(value), 0, 'positive');
      document.getElementById('fleet_res').style.display = "block";
    }
    else
    {
      document.getElementById(resource_name + '_fleet').style.display = "none";
    }
  }
  else
  {
    document.getElementById('unit_' + resource_name).style.display = "none";
  }
}

var balance_translate = {
  'level': language['level'],
  'metal': language['sys_metal'],
  'crystal': language['sys_crystal'],
  'deuterium': language['sys_deuterium'],
  'energy': language['sys_energy']
};

var bld_unit_info_width = 0;
var bld_unit_info_cache = Array();

function eco_struc_show_unit_info(unit_id, no_color)
{
  if(!no_color)
  {
    document.getElementById('unit' + unit_id).style.borderColor = eco_bld_style_probe;
  }

  if(unit_selected)
  {
    return;
  }

  if(bld_unit_info_cache[unit_id] != undefined)
  {
    jQuery('#unit_info').html(bld_unit_info_cache[unit_id]);
    return;
  }

  var unit = production[unit_id];
  var result = '';
  var unit_destroy_link = '';

  document.getElementById('unit_image').src = dpath + 'gebaeude/' + unit['id'] +'.gif';
  document.getElementById('unit_description').innerHTML = unit['description'];

  document.getElementById('unit_time').innerHTML = unit['time'];

  eco_struc_make_resource_row('metal', unit['metal'], unit['destroy_metal']);
  eco_struc_make_resource_row('crystal', unit['crystal'], unit['destroy_crystal']);
  eco_struc_make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium']);

  document.getElementById('unit_name').innerHTML = unit['name'];
  if(unit['level'] > 0)
  {
    document.getElementById('unit_name').innerHTML += '<br>' + language['level'] + ' ' + unit['level'] + (parseInt(unit['level_bonus']) > 0 ? '<span class="bonus">+' + unit['level_bonus'] + '</span>' : '');
    unit_destroy_link = language['bld_destroy'] + ' ' + language['level'] + ' ' + unit['level'];
  }

  document.getElementById('unit_create_link').innerHTML = '';
  document.getElementById('unit_destroy_link').innerHTML = '';
  if(planet['que_has_place'] != 0 && !unit['unit_busy'])
  {
    var pre_href = '<a href="buildings.php?mode=' + que_id + '&action=';
    if(unit['level'] > 0 && unit['destroy_can'] != 0 && unit['destroy_result'] == 0)
    {
      document.getElementById('unit_destroy_link').innerHTML = pre_href + 'destroy&unit_id=' + unit['id'] + '"><span class="negative">' + unit_destroy_link + '</span><br />'
      + (unit['destroy_metal'] ? language['sys_metal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_metal']), 0, 'positive') + ' ' : '')
      + (unit['destroy_crystal'] ? language['sys_crystal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_crystal']), 0, 'positive') + ' ' : '')
      + (unit['destroy_deuterium'] ? language['sys_deuterium'][0] + ':' + sn_format_number(parseFloat(unit['destroy_deuterium']), 0, 'positive') + ' ' : '')
      + unit['destroy_time']
      + '</a>';
    }
    if(planet['fields_free'] > 0 && unit['build_can'] != 0 && unit['build_result'] == 0)
    {
      document.getElementById('unit_create_link').innerHTML = pre_href + 'create&unit_id=' + unit['id'] + '"><span class="positive">' + language['bld_create'] + ' ' + language['level'] + ' ' + (parseInt(unit['level']) + 1) + '</span></a>';
    }
  }

  document.getElementById('unit_balance').innerHTML = '';
  if(unit['resource_map'])
  {
    var balance_header = '';
    var has_header = false;
    for(i in unit['resource_map'])
    {
      result += '<tr class="c_r">';
      for(j in unit['resource_map'][i])
      {
        if(unit['resource_map'][i][j])
        {
          if(!has_header)
          {
            switch(j)
            {
              case 'level':
                balance_header += '<th class="c_l">' + language['level_short'] + '</th>';
              break;

              case 'metal':
              case 'crystal':
              case 'deuterium':
              case 'energy':
                balance_header += '<th class="c_c" colspan="2">' + balance_translate[j];
              break;

              case 'metal_diff':
              case 'crystal_diff':
              case 'deuterium_diff':
              case 'energy_diff':
                balance_header += '</th>';
              break;
            }
          }
          result += '<td>' + sn_format_number(parseFloat(unit['resource_map'][i][j]), 0, 'positive', j == 'level' ? -unit['level']-unit['level_bonus'] : 0) + '</td>';
        }
      }
      result += '</tr>';
      has_header = true;
    }

    result = '<table><tr>' + balance_header + '</tr>' + result + '</table>';

    document.getElementById('unit_balance').innerHTML += result;
  }
  bld_unit_info_width = Math.max(bld_unit_info_width, jQuery('#unit_table').width());
  document.getElementById('unit_table').width = bld_unit_info_width;
  bld_unit_info_cache[unit_id] = jQuery('#unit_info').html();
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
