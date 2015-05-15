jQuery(document).ready( function() {
  $(".unit_create,.unit_destroy").on('click', function(e){
    if(!$(this).is("[disabled]")) {
      $('[name=action]').val($(this).hasClass('unit_create') ? 'create' : 'destroy');
      $('#form_unit').submit();
    }
  });
  $("#form_unit").on('keypress', function(e) { // 'form input[type="text"]',
    if(e.which == 13) {
      if(!$('#unit_create_button').is("[disabled]")) {
        $('#unit_create_button').click();
      }
      e.preventDefault();
      return false;
    }
  });

  $("#unit_amount").on('change', function(){
     $('#unit_create_button').button(parseInt($('#unit_amount').val()) ? "enable" : "disable");
  });

  jQuery('#unit_amount').on('keyup change', function(event, ui) {
    unit_id = $('#unit_id').val();
    unit = production[unit_id];

    eco_struc_make_resource_row('metal', unit['metal'], unit['destroy_metal'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('crystal', unit['crystal'], unit['destroy_crystal'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('dark_matter', unit['dark_matter'], unit['destroy_dark_matter'], unit['dark_matter'], STACKABLE);
  });

  jQuery("#unit_table")
    .on("mouseenter", "*[unit_id]", function(event, ui) {
      eco_struc_show_unit_info(jQuery(this).attr('unit_id'));
    })
    .on("mouseleave", "*[unit_id]", function(event, ui) {
      eco_struc_unborder_unit(jQuery(this).attr('unit_id'));
    })
    .on("click", "*[unit_id]", function(event, ui) {
      eco_struc_select_unit(jQuery(this).attr('unit_id'));
    });

  if(!planet['fleet_own']) {
    jQuery("[hide_no_fleet]").hide();
  }

  eco_bld_style_probe = sn_probe_style(element_cache['style_probe'], 'border-top-color');

  production_id_first ? eco_struc_show_unit_info(production_id_first, true) : '';
  // var production_id_first;
/*
  for(var production_id_first in production) {
    eco_struc_show_unit_info(production_id_first, true);
    break;
  }
*/
});


function eco_struc_make_resource_row(resource_name, value, value_destroy, value_dm, show_actual_price) {
  if(value > 0) {
    value = show_actual_price ? value * jQuery('#unit_amount').val() : value;
    document.getElementById('unit_' + resource_name).style.visibility = "visible";
    document.getElementById('unit_' + resource_name).style.display = "table-row";

    document.getElementById(resource_name + '_price').innerHTML = sn_format_number(value, 0, 'positive', planet[resource_name]);
    document.getElementById(resource_name + '_left').innerHTML = sn_format_number(parseFloat(planet[resource_name]) - parseFloat(value), 0, 'positive');
    if(planet['fleet_own']) {
      document.getElementById(resource_name + '_fleet').innerHTML = sn_format_number(parseFloat(planet[resource_name]) + parseFloat(planet[resource_name + '_incoming']) - parseFloat(value), 0, 'positive');
      // document.getElementById('fleet_res').style.display = "block";
      jQuery('#fleet_res').css('display', "block");
    } else {
      document.getElementById(resource_name + '_fleet').style.display = "none";
    }
  } else {
      (value_dm && resource_name != 'dark_matter') || (!value && resource_name == 'dark_matter')
        ? (document.getElementById('unit_' + resource_name).style.display = "none")
        : (document.getElementById('unit_' + resource_name).style.visibility = "hidden");
  }
}

var bld_unit_info_width = 0;

function eco_struc_show_unit_info(unit_id, no_color) {
  if(!no_color) {
    document.getElementById('unit' + unit_id).style.borderColor = eco_bld_style_probe;
  }

  if(unit_selected) {
    return;
  }

  $('#unit_id').val(unit_id);
  $('#unit_amountslide').slider({ value: 0});

  var unit = production[unit_id];
  var result = '';
  var unit_destroy_link = '';

  document.getElementById('unit_image').src = dpath + 'gebaeude/' + unit['id'] +'.gif';
  document.getElementById('unit_description').innerHTML = unit['description'];

  document.getElementById('unit_time').innerHTML = unit['time'];
  document.getElementById('unit_time_div').style.display = unit['time_seconds'] ? "block" : "none";

  document.getElementById('unit_name').innerHTML = unit['name'];
  if(unit['level'] > 0 || STACKABLE)
  {
    document.getElementById('unit_level').innerHTML = (!STACKABLE ? language['level'] + ' ' : '') + unit['level'] + (parseInt(unit['level_bonus']) > 0 ? '<span class="bonus">+' + unit['level_bonus'] + '</span>' : '');
    unit_destroy_link = language['bld_destroy'] + ' ' + language['level'] + ' ' + unit['level'];
  } else {
    document.getElementById('unit_level').innerHTML = '&nbsp;';
  }

  if(STACKABLE) {
    $('#unit_max').show();
    $('#unit_max_number').html(unit['can_build']);
  }

  // $('#unit_create').hide();
  // $('#unit_create, #unit_create *').addClass('a75').disable();
  $('#unit_create, #unit_create *').prop('disabled', true);
  $('#unit_destroy').css('visibility', 'hidden');

//  <li style="margin: 0; padding: 0;"><span class="<!-- IF require.REQUEREMENTS_MET -->negative<!-- ELSE -->positive<!-- ENDIF -->">{require.NAME}</span><!-- IF require.LEVEL_REQUIRE -->&nbsp;{require.LEVEL_BASIC}<!-- IF require.LEVEL_BONUS --><span class="bonus">+{require.LEVEL_BONUS}</span><!-- ENDIF -->/{require.LEVEL_REQUIRE}<!-- ENDIF --></li>


  if(require[unit_id]) {
    requirement_string = '';
    for(i in require[unit_id]) {
      req = require[unit_id][i];
      requirement_string = requirement_string
        + '<li class="' + (req['requerements_met'] ? 'positive' : 'negative  ') + '">'
          + req['name']
          + (!isNaN(req['level_basic']) ? ' ' + req['level_basic'] + (req['level_bonus'] ? '<span class="bonus">+' + req['level_bonus'] + '</span>' : '') + '/' + req['level_require'] : '')
        + '</li>';
    }
    $('#unit_require').empty().append(requirement_string);
    $('#unit_require_wrapper').show();
  } else {
    $('#unit_require_wrapper').hide();
    // requirement_string = '<li class="neutral">' + language['No_requirements'] + '</li>';
  }

  if(TEMPORARY) {
    jQuery("#unit_cost_table").hide();//css.display = "none";
  } else {
    jQuery("#unit_cost_table").css.display = "table";

    eco_struc_make_resource_row('metal', unit['metal'], unit['destroy_metal'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('crystal', unit['crystal'], unit['destroy_crystal'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('deuterium', unit['deuterium'], unit['destroy_deuterium'], unit['dark_matter'], STACKABLE);
    eco_struc_make_resource_row('dark_matter', unit['dark_matter'], unit['destroy_dark_matter'], unit['dark_matter'], STACKABLE);

    if(planet['que_has_place'] != 0 && !unit['unit_busy']) {
//    var pre_href = '<a href="buildings.php?mode=' + que_id + '&action=';
      if(STACKABLE) {
        if(unit['build_can'] != 0 && unit['build_result'] == 0)
        {
//          $('#unit_create').show();
          $('#unit_create, #unit_create *').prop('disabled', false);
//          $('#unit_create').css('visibility', 'visible');
          $('#unit_create_level').html(parseInt(unit['level']) + 1);
          $('#unit_amountslide').slider({ max: unit['can_build']});
        }
      } else {
        if(unit['level'] > 0 && unit['destroy_can'] != 0 && unit['destroy_result'] == 0) {
          $('#unit_destroy').css('visibility', 'visible');
          $('#unit_destroy_level').html(unit['level']);
          $('#unit_destroy_resources').html(
            (unit['destroy_metal'] ? language['sys_metal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_metal']), 0, 'positive') + ' ' : '')
              + (unit['destroy_crystal'] ? language['sys_crystal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_crystal']), 0, 'positive') + ' ' : '')
              + (unit['destroy_deuterium'] ? language['sys_deuterium'][0] + ':' + sn_format_number(parseFloat(unit['destroy_deuterium']), 0, 'positive') + ' ' : '')
          );
          $('#unit_destroy_time').html(unit['destroy_time']);
          /*
           document.getElementById('unit_destroy_link').innerHTML =
           (unit['destroy_metal'] ? language['sys_metal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_metal']), 0, 'positive') + ' ' : '')
           + (unit['destroy_crystal'] ? language['sys_crystal'][0] + ': ' + sn_format_number(parseFloat(unit['destroy_crystal']), 0, 'positive') + ' ' : '')
           + (unit['destroy_deuterium'] ? language['sys_deuterium'][0] + ':' + sn_format_number(parseFloat(unit['destroy_deuterium']), 0, 'positive') + ' ' : '')
           + '<br />' + unit['destroy_time']
           + '<br />' + '<span class="link negative unit_destroy">' + unit_destroy_link + '</span>';
           */
        }

        if(planet['fields_free'] > 0 && unit['build_can'] != 0 && unit['build_result'] == 0) {
//          $('#unit_create').show();
          $('#unit_create, #unit_create *').prop('disabled', false);
//          $('#unit_create').css('visibility', 'visible');
          $('#unit_create_level').html(parseInt(unit['level']) + 1);
          /*
           document.getElementById('unit_create_link').innerHTML = '<span class="link positive unit_create">' +
           language['bld_create'] + ' ' + language['level'] + ' ' + (parseInt(unit['level']) + 1) + '</span>';
           */
        }
      }
    }
  }

  result = '';
  if(unit['resource_map']) {
    var balance_header = '';
    if(STACKABLE) {
      for(i in unit['resource_map'][0]) {
        if(unit['resource_map'][0][i]) {
          result += '<tr>';
          result += '<th class="c_l">' + language[i] + '</th>';
          result += '<td class="c_r" width="75px">' + unit['resource_map'][0][i] + '</td>';
          result += '</tr>';
        }
      }
    } else {
      var has_header = false;
      for(i in unit['resource_map']) {
        result += '<tr class="c_r">';
        for(j in unit['resource_map'][i]) {
          if(unit['resource_map'][i][j]) {
            if(!has_header) {
              switch(j) {
                case 'level':
                  balance_header += '<th class="c_l">' + language['level_short'] + '</th>';
                  break;

                case 'sys_metal':
                case 'sys_crystal':
                case 'sys_deuterium':
                case 'sys_energy':
                case 'sys_expeditions':
                case 'sys_colonies':
                  balance_header += '<th class="c_c" colspan="2">' + language[j];
                  break;

                case 'metal_diff':
                case 'crystal_diff':
                case 'deuterium_diff':
                case 'energy_diff':
                case 'sys_expeditions_diff':
                case 'sys_colonies_diff':
                  balance_header += '</th>';
                  break;
              }
            }

//            t = j.indexOf('diff') == -1 ? (  unit['resource_map'][i-1] ? unit['resource_map'][i-1][j]  : 0     ) : 0;

            result += '<td>' +

//              t + ' ' +


              sn_format_number(
                parseFloat(unit['resource_map'][i][j]), 0, 'positive',
                j == 'level' ? -unit['level']-unit['level_bonus'] : (j.indexOf('diff') == -1 ? (  unit['resource_map'][i-1] ? -unit['resource_map'][i-1][j] : 0) : 0),
              unit['resource_map'][i][j] > 0 && j.indexOf('diff') >= 0) + '</td>';
          }
        }
        result += '</tr>';
        has_header = true;
      }
    }

    result = result ? '<table  style="font-size: 0.8em">' + (balance_header ? '<tr>' + balance_header + '</tr>' : '') + result + '</table>' : '';
  }
  document.getElementById('unit_balance').innerHTML = result;

  bld_unit_info_width = Math.max(bld_unit_info_width, jQuery('#unit_table').width());
  document.getElementById('unit_table').width = bld_unit_info_width;
//  bld_unit_info_cache[unit_id] = jQuery('#unit_info').html();
}

function eco_struc_select_unit(unit_id) {
  $('#unit_id').val(unit_id);
  if(unit_selected == unit_id) {
    unit_selected = null;
  } else {
    if(unit_selected) {
      document.getElementById('unit' + unit_selected).style.borderColor="";
      unit_selected = null;
      eco_struc_show_unit_info(unit_id);
    }
    unit_selected = unit_id;
    $('#unit_amountslide').slider({ value: 0});
  }
}

function eco_struc_unborder_unit(unit_id) {
  if(unit_selected != unit_id) {
    document.getElementById('unit' + unit_id).style.borderColor="";
  }
}
