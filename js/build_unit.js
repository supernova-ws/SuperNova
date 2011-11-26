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
  var unit_destroy_link = '';

  element_cache['unit_image'].src = dpath + 'gebaeude/' + unit['id'] +'.gif';
  element_cache['unit_description'].innerHTML = unit['description'];

  element_cache['unit_time'].innerHTML = unit['time'];

  eco_struc_make_resource_row('metal', unit['metal'], unit['destroy_metal']);
  eco_struc_make_resource_row('crystal', unit['crystal'], unit['destroy_crystal']);
  eco_struc_make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium']);

  element_cache['unit_name'].innerHTML = unit['name'];
  if(unit['level'] > 0)
  {
    element_cache['unit_name'].innerHTML += '<br>' + language['level'] + ' ' + unit['level'];
    unit_destroy_link = language['bld_destroy'] + ' ' + language['level'] + ' ' + unit['level'];
  }

  element_cache['unit_create_link'].innerHTML = '';
  element_cache['unit_destroy_link'].innerHTML = '';
  if(planet['que_has_place'] != 0 && !unit['unit_busy'])
  {
    var pre_href = '<a href="buildings.php?mode=' + que_id + '&action=';
    if(unit['level'] > 0 && unit['destroy_can'] != 0 && unit['destroy_result'] == 0)
    {
      element_cache['unit_destroy_link'].innerHTML = pre_href + 'destroy&unit_id=' + unit['id'] + '"><span class="negative">' + unit_destroy_link + '</span><br />'
      + language['sys_metal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_metal']), 0, 'lime') + ' ' 
      + language['sys_crystal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_crystal']), 0, 'lime') + ' ' 
      + language['sys_deuterium'][0] + ':' + sn_format_number(parseFloat(unit['destroy_deuterium']), 0, 'lime') + ' '
      + unit['destroy_time']
      + '</a>';
    }
    if(planet['fields_free'] > 0 && unit['build_can'] != 0 && unit['build_result'] == 0)
    {
      element_cache['unit_create_link'].innerHTML = pre_href + 'create&unit_id=' + unit['id'] + '"><span class="positive">' + language['bld_create'] + ' ' + language['level'] + ' ' + (parseInt(unit['level']) + 1) + '</span></a>';
    }
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
