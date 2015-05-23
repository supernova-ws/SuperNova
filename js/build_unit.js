jQuery(document).ready( function(e) {
  $(".unit_create,.unit_destroy,#unit_create_button_auto").on('click', function(e){
    //if(!$(this).is("[disabled]") && $(this).attr("aria-disabled") != 'true') {
    if($(this).is(":disabled") || $(this).attr("aria-disabled") == 'true') {
      e.preventDefault();
      return false;
    }

    if($(this).hasClass('unit_create_autoconvert')) {
      if(DARK_MATTER < MARKET_AUTOCONVERT_COST) {
        alert(language['eco_bld_autoconvert_explain'] + language['eco_bld_autoconvert_dark_matter_none']);
        e.preventDefault();
        return false;
      }
      if(!confirm(language['eco_bld_autoconvert_explain'] + language['eco_bld_autoconvert_confirm'])) {
        e.preventDefault();
        return false;
      }
      //if(!confirm(language['eco_bld_autoconvert_confirm'])) {
      //  return false;
      //}
      //if(!confirm('{Недостающие на постройку/исследование ресурсы будут автоматически сконвертированы из наличных ресурсов (металл, кристалл, дейтерий). ' +
      //  '\r\n\r\nЭта операция будет стоить {0} ТМ.\r\n\r\nПродолжать?}'.format(MARKET_AUTOCONVERT_COST))) {
      //  return false;
      //}
    }

    $('[name=action]').val($(this).hasClass('unit_create') ? 'create' : ($(this).hasClass('unit_destroy') ? 'destroy' : 'create_autoconvert'));
    $('#form_unit').submit();
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

  $("#unit_info_extra_switch").on('click', function() {
    if($('#unit_balance').is(':visible')) {
      $(this).children().html(language['eco_bld_unit_info_extra_show']);
      // $(this).innerHTML = language['eco_bld_unit_info_extra_show'];
      $('#unit_balance').hide();
    } else {
      $(this).children().html(language['eco_bld_unit_info_extra_hide']);
      //$(this).innerHTML = language['eco_bld_unit_info_extra_hide'];
      $('#unit_balance').show();
    }
  });

  $("#auto_convert").on('change', function() {
    $('#unit_create, #unit_create *').prop('disabled', true);
    $('#unit_create_button').button('disable');

    unit_id = $('#unit_id').val();
    var unit = production[unit_id];

    $('#unit_max_number').html(sn_format_number(unit['can_build']));

    if(unit['autoconvert_amount']) {
      $('#auto_convert').prop('disabled', false).button('enable');
    }

    if(unit['build_can'] != 0 && unit['build_result'] == 0) {
      $('#unit_create, #unit_create *').prop('disabled', false);
      $('#unit_amountslide').slider({ max: unit['can_build']});
    }

    if(!$('#auto_convert').is(":disabled") && $('#auto_convert').attr("aria-disabled") != 'true' && $('#auto_convert').is(":checked")) {
      //if($(this).is(":disabled") || $(this).attr("aria-disabled") == 'true') {
      $('#unit_create, #unit_create *').prop('disabled', false);
      $('#unit_amountslide').slider({ max: unit['autoconvert_amount']});
      $('#unit_max_number').html(sn_format_number(unit['autoconvert_amount']));
    } else {
      if(unit['build_can'] == 0 || unit['build_result'] != 0) {
        $('#unit_create, #unit_create *').prop('disabled', true);
        $('#unit_amountslide').slider({ max: 0, value: 0});
        $('#unit_create_button').button('disable');
        $('#unit_amount').val(0);
      }
    }

    if(unit['autoconvert_amount']) {
      $('#auto_convert').prop('disabled', false).button('enable');
    }

    $('#unit_amount').change();
  });

  if(!planet['fleet_own']) {
    jQuery("[hide_no_fleet]").hide();
  }

  eco_bld_style_probe = sn_probe_style(element_cache['style_probe'], 'border-top-color');

  production_id_first ? eco_struc_show_unit_info(production_id_first, true) : '';
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
  $('#unit_amountslide').slider({ max: 0});

  var unit = production[unit_id];
  var result = '';
  var unit_destroy_link = '';

  document.getElementById('unit_info_image').src = dpath + 'gebaeude/' + unit['id'] +'.gif';
  document.getElementById('unit_info_description').innerHTML = unit['description'];

  document.getElementById('unit_time').innerHTML = unit['time'];
  document.getElementById('unit_time_div').style.display = unit['time_seconds'] ? "block" : "none";

  document.getElementById('unit_info_name').innerHTML = unit['name'];
  if(unit['level'] > 0 || STACKABLE) {
    document.getElementById('unit_info_level').innerHTML = (!STACKABLE ? language['level'] + ' ' : '') + unit['level'] + (parseInt(unit['level_bonus']) > 0 ? '<span class="bonus">+' + unit['level_bonus'] + '</span>' : '');
    unit_destroy_link = language['bld_destroy'] + ' ' + language['level'] + ' ' + unit['level'];
  } else {
    document.getElementById('unit_info_level').innerHTML = '&nbsp;';
  }

  if(STACKABLE) {
    $('#unit_max').show();
//alert($('#auto_convert').is(":checked"));
    $('#unit_max_number').html(sn_format_number(unit['can_build']));
    $('#unit_max_number_autoconvert').html(sn_format_number(unit['autoconvert_amount']));
  }

  $('#unit_create, #unit_create *').prop('disabled', true);
  $('#unit_create_button').button('disable');
  $('#unit_destroy').css('visibility', 'hidden');

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
      if(STACKABLE) {
        $("#auto_convert").change();
        //if(unit['build_can'] != 0 && unit['build_result'] == 0) {
        //  $('#unit_create, #unit_create *').prop('disabled', false);
        //  $('#unit_amountslide').slider({ max: unit['can_build']});
        //} else if(!$('#auto_convert').is(":disabled") && $('#auto_convert').attr("aria-disabled") != 'true' && $('#auto_convert').is(":checked")) {
        //  //if($(this).is(":disabled") || $(this).attr("aria-disabled") == 'true') {
        //  $('#unit_create, #unit_create *').prop('disabled', false);
        //  $('#unit_amountslide').slider({ max: unit['autoconvert_amount']});
        //  $('#unit_max_number').html(unit['autoconvert_amount']);
        //}
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
        }

        if(planet['fields_free'] > 0 && unit['build_can'] != 0 && unit['build_result'] == 0) {
          $('#unit_create_button').button('enable');
          $('#unit_create, #unit_create *').prop('disabled', false);
        }
      }
    }
  }
  $('#unit_create_button_auto').button(unit['can_autoconvert'] ? 'enable' : 'disable').prop('disabled', unit['can_autoconvert'] ? false : true);
  $('#unit_create_level, #unit_create_level_auto').html((parseInt(unit['level']) ? parseInt(unit['level']) : 0) + 1);

//  $('#unit_create_button_auto').button(unit['can_autoconvert'] ? 'enable' : 'disable');

  result = '';
  if(unit['resource_map']) {
    var balance_header = '';
    if(STACKABLE) {
      for(i in unit['resource_map'][0]) {
        if(unit['resource_map'][0][i]) {
          result += '<tr>';
          result += '<th class="c_l">' + language[i] + '</th>';
          result += '<td class="c_r">' + unit['resource_map'][0][i] + '</td>';
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
            result += '<td>' +
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

    result ? result = (balance_header ? '<tr>' + balance_header + '</tr>' : '') + result : false;
  }
  !result ? result = '<tr><th class="c_c">' + language['eco_bld_unit_info_extra_none'] + '</th></tr>' : false;
  $('#unit_balance').html('<table>' + result + '</table>');
  $('<style></style>').appendTo($(document.body)).remove();
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
