var unit_selected = null;

function eco_struc_make_resource_row(unitData, resourceName) {
  var resourceLeft, resourceLeftWithIncoming;
  // var costDestroy = intVal(unitData["destroy_" + resourceName]);
  var costBuild = Math.ceilVal(unitData[resourceName]);
  var resourceElement = $("#unit_" + resourceName);

  if (costBuild > 0) {
    if (STACKABLE) {
      costBuild *= Math.roundVal($("#unit_amount").val());
    }
    resourceLeft = Math.roundVal(planet[resourceName]) - costBuild;
    resourceLeftWithIncoming = resourceLeft + Math.floatVal(planet[resourceName + "_incoming"]);

    resourceElement.css({visibility: "visible", display: "table-row"});

    elementPrettyNumber($("#" + resourceName + "_price"), costBuild, planet[resourceName]);
    elementPrettyNumber($("#" + resourceName + "_left"), resourceLeft);

    if (planet["fleet_own"]) {
      elementPrettyNumber($("#" + resourceName + "_fleet"), resourceLeftWithIncoming);
      jQuery('#fleet_res').css("display", "block");
    } else {
      $("#" + resourceName + "_fleet").css("display", "none");
    }
  } else {
    // For more robust hide/visibility
    // Need to mark - if any of units have cost in DM. Then it should hide with visibility. Otherwise - hide with display
    //   ("dark_matter" != resourceName && unitData['dark_matter']) || ("dark_matter" == resourceName && !unitData["dark_matter"])
    //     ? {display: "none"} : {visibility: "hidden"}
    resourceElement.css("visibility", "hidden");
  }
}

function eco_struc_make_resource_rows_all(unit) {
  eco_struc_make_resource_row(unit, 'metal');
  eco_struc_make_resource_row(unit, 'crystal');
  eco_struc_make_resource_row(unit, 'deuterium');
  eco_struc_make_resource_row(unit, 'dark_matter');
}

function buildUnitProcessResult(e, result) {
  if (result) {
    $('[name=action]').val(result);
    $('#form_unit').submit();
  } else {
    e.preventDefault();
  }
  return !!result;
}

jQuery(document).ready(function (e) {
  $("#unit_create_button_auto").on('click', function (e) {
    var result = elementIsEnabled(this);
    if (result) {
      if (DARK_MATTER >= MARKET_AUTOCONVERT_COST) {
        if (confirm(language['eco_bld_autoconvert_explain'] + language['eco_bld_autoconvert_confirm'])) {
          result = "create_autoconvert";
        }
      } else {
        alert(language['eco_bld_autoconvert_explain'] + language['eco_bld_autoconvert_dark_matter_none']);
        result = false;
      }
    }

    return buildUnitProcessResult(e, result);
  });

  $(".unit_create").on('click', function (e) {
    var result = elementIsEnabled(this) || $(this).hasClass('icon_plus') || $(this).hasClass('icon_minus');
    if (result) {
      result = $(this).hasClass('unit_create') ? 'create' : 'destroy';
    }

    return buildUnitProcessResult(e, result);
  });

  $(".unit_destroy").on('click', function (e) {
    return buildUnitProcessResult(e, 'destroy');
  });

  $("#form_unit").on('keypress', function (e) {
    if (13 != e.which) {
      return true;
    }

    var ucb = $('#unit_create_button');
    if (!ucb.is("[disabled]")) {
      ucb.click();
    }
    e.preventDefault();
    return false;
  });

  $("#unit_amount").on('change', function () {
    $('#unit_create_button').button(Math.intVal($('#unit_amount').val()) ? "enable" : "disable");
  });

  jQuery('#unit_amount').on('keyup change', function (event, ui) {
    var unit_id = Math.intVal($('#unit_id').val());
    eco_struc_make_resource_rows_all(production[unit_id]);
  });

  jQuery("#unit_table")
    .on("mouseenter", "div[unit_id]", function (event, ui) {
      eco_struc_show_unit_info(jQuery(this).attr('unit_id'));
    })
    .on("mouseleave", "div[unit_id]", function (event, ui) {
      if (unit_selected != jQuery(this).attr('unit_id')) {
        jQuery(this).removeClass('unit_border_selected');
      }
    })
    .on("click", "div[unit_id]", function (event, ui) {
      eco_struc_select_unit(jQuery(this).attr('unit_id'));
    });

  $("#unit_info_extra_switch").on('click', function () {
    var ub = $('#unit_balance');
    if (ub.is(':visible')) {
      $(this).children().html(language['eco_bld_unit_info_extra_show']);
      ub.hide();
    } else {
      $(this).children().html(language['eco_bld_unit_info_extra_hide']);
      ub.show();
    }
  });

  $("#auto_convert").on('change', function () {
    var unit_create = $('#unit_create, #unit_create *');
    var unit_create_button = $('#unit_create_button');

    unit_create.prop('disabled', true);
    unit_create_button.button('disable');

    unit_id = $('#unit_id').val();
    var unit = production[unit_id];

    $('#unit_max_number').html(sn_format_number(unit['can_build']));

    if (unit['autoconvert_amount']) {
      $('#auto_convert').prop('disabled', false).not(':checkbox').button('enable');
    }

    if (unit['build_can'] != 0 && unit['build_result'] == 0) {
      unit_create.prop('disabled', false);
      $('#unit_amountslide').slider({max: unit['can_build']});
    }

    if (!$('#auto_convert').is(":disabled") && $('#auto_convert').attr("aria-disabled") != 'true' && $('#auto_convert').is(":checked")) {
      //if($(this).is(":disabled") || $(this).attr("aria-disabled") == 'true') {
      unit_create.prop('disabled', false);
      $('#unit_amountslide').slider({max: unit['autoconvert_amount']});
      $('#unit_max_number').html(sn_format_number(unit['autoconvert_amount']));
    } else {
      if (unit['build_can'] == 0 || unit['build_result'] != 0) {
        unit_create.prop('disabled', true);
        $('#unit_amountslide').slider({max: 0, value: 0});
        unit_create_button.button('disable');
        $('#unit_amount').val(0);
      }
    }

    if (unit['autoconvert_amount']) {
      $('#auto_convert').prop('disabled', false).not(':checkbox').button('enable');
    }

    $('#unit_amount').change();
  });

  if (!planet['fleet_own']) {
    jQuery("[hide_no_fleet]").hide();
  }

  // eco_bld_style_probe = $('#style_probe').css('border-top-color');

  production_id_first ? eco_struc_show_unit_info(production_id_first, true) : '';
});


var bld_unit_info_width = 0;

function extracted(j, balance_header) {
  switch (j) {
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
  return balance_header;
}
function eco_struc_show_unit_info(unit_id, no_color) {
  if (!no_color) {
    $('#unit' + unit_id).addClass('unit_border_selected');
  }

  if (unit_selected) {
    return;
  }

  $('#unit_id').val(unit_id);
  $('#unit_amountslide').slider({value: 0, max: 0});
  // $('#unit_amountslide').slider({ max: 0});

  var unit = production[unit_id];
  var result = '';
  var unit_destroy_link = '';

  var unit_info_image = $('#unit_info_image');
  unit_info_image.attr('src', unit['image']);
  $('#unit_info_wiki').attr('unit_id', unit_id);
  $('#unit_info_description').html(unit['description']);

  $('#unit_time').html(unit['time']);
  $('#unit_time_div').css('display', unit['time_seconds'] ? "block" : "none");

  $('#unit_info_name').html(unit['name']);
  if (unit['level'] > 0 || STACKABLE) {
    $('#unit_info_level').html(
      (!STACKABLE ? language['level'] + ' ' : '')
      + unit['level']
      + (Math.intVal(unit['level_bonus']) ? '<span class="bonus">+' + unit['level_bonus'] + '</span>' : '')
    );
  } else {
    $('#unit_info_level').html('&nbsp;');
  }

  if (STACKABLE) {
    $('#unit_max').show();
    $('#unit_max_number').html(sn_format_number(unit['can_build']));
    $('#unit_max_number_autoconvert').html(sn_format_number(unit['autoconvert_amount']));
  }

  $('#unit_create, #unit_create *').prop('disabled', true);
  $('#unit_create_button').button('disable');
  $('#unit_destroy').css('visibility', 'hidden');

  var req;
  var requirement_string = '';
  if (require[unit_id]) {
    for (i in require[unit_id]) {
      if(!require[unit_id].hasOwnProperty(i)) {
        continue;
      }

      req = require[unit_id][i];
      requirement_string = requirement_string
        + '<li class="' + (req['requerements_met'] ? 'positive' : 'negative  ') + '">'
        + '<a href="infos.php?gid=' + req.id + '" class="link">' + req['name'] + '</a>'
        + (!isNaN(req['level_basic']) ? ' ' + req['level_basic'] + (req['level_bonus'] ? '<span class="bonus">+' + req['level_bonus'] + '</span>' : '') + '/' + req['level_require'] : '')
        + '</li>';
    }
    $('#unit_require').empty().append(requirement_string);
    $('#unit_require_wrapper').show();
  } else {
    $('#unit_require_wrapper').hide();
  }

  if (grants[unit_id]) {
    requirement_string = '';
    for (i in grants[unit_id]) {
      req = grants[unit_id][i];
      requirement_string = requirement_string
        + '<li class="' + (req['requerements_met'] ? 'positive' : 'negative  ') + '">'
        + req['name']
        + (!isNaN(req['level_basic']) ? ' ' + req['level_basic'] + (req['level_bonus'] ? '<span class="bonus">+' + req['level_bonus'] + '</span>' : '') + '/' + req['level_require'] : '')
        + '</li>';
    }
    $('#unit_grants').empty().append(requirement_string);
    $('#unit_grants_wrapper').show();
  } else {
    $('#unit_grants_wrapper').hide();
  }

  if (TEMPORARY) {
    $("#unit_cost_table").hide();
  } else {
    $("#unit_cost_table").css('display', 'table');

    eco_struc_make_resource_rows_all(unit);

    if (planet['que_has_place'] != 0 && !unit['unit_busy']) {
      if (STACKABLE) {
        $("#auto_convert").change();
        if (unit['build_can'] != 0 && unit['build_result'] == 0) {
          $('#unit_create, #unit_create *').prop('disabled', false);
          $('#unit_amountslide').slider({max: unit['can_build']});
        }
      } else {
        if (unit['level'] > 0 && unit['destroy_can'] != 0 && unit['destroy_result'] == 0) {
          $('#unit_destroy').css('visibility', 'visible');
          $('#unit_destroy_level').html(unit['level']);
          $('#unit_destroy_resources').html(
            (unit['destroy_metal'] ? language['sys_metal'][0] + ': ' + sn_format_number(unit['destroy_metal'], 0, 'positive') + ' ' : '')
            + (unit['destroy_crystal'] ? language['sys_crystal'][0] + ': ' + sn_format_number(unit['destroy_crystal'], 0, 'positive') + ' ' : '')
            + (unit['destroy_deuterium'] ? language['sys_deuterium'][0] + ':' + sn_format_number(unit['destroy_deuterium'], 0, 'positive') + ' ' : '')
          );
          $('#unit_destroy_time').html(unit['destroy_time']);
        }

        if ((planet['fields_free'] > 0 || unit['unit_type'] == UNIT_TECHNOLOGIES) && unit['build_can'] != 0 && unit['build_result'] == 0) {
          $('#unit_create_button').button('enable');
          $('#unit_create, #unit_create *').prop('disabled', false);
        }
      }
    }
  }
  $('#unit_create_button_auto').button(unit['can_autoconvert'] ? 'enable' : 'disable').prop('disabled', unit['can_autoconvert'] ? false : true);
  $('#unit_create_level, #unit_create_level_auto').html(unit['level'] + 1);

  var i, j, limit;
  result = '';
  if (unit["resource_map"]) {
    var balance_header = '';
    if (STACKABLE) {
      for (i in unit["resource_map"][0]) {
        if (!unit["resource_map"][0].hasOwnProperty(i)) {
          continue;
        }
        if (unit["resource_map"][0][i]) {
          result += '<tr>';
          result += '<th class="c_l">' + language[i] + '</th>';
          result += '<td class="c_r">' + unit["resource_map"][0][i] + '</td>';
          result += '</tr>';
        }
      }
    } else {
      var has_header = false;
      for (i in unit["resource_map"]) {
        if (!unit["resource_map"].hasOwnProperty(i)) {
          continue;
        }

        result += '<tr class="c_r">';
        for (j in unit["resource_map"][i]) {
          if (!unit["resource_map"][i].hasOwnProperty(j) || !unit["resource_map"][i][j]) {
            continue;
          }

          if (!has_header) {
            balance_header = extracted(j, balance_header);
          }

          limit =
            j == 'level'
              ? -unit['level'] - unit['level_bonus']
              :
              (
                j.indexOf('diff') == -1
                  ? (unit["resource_map"][i - 1] ? -unit["resource_map"][i - 1][j] : 0)
                  : 0
              )
          ;
          result += '<td>' +
            sn_format_number(
              Math.floatVal(unit["resource_map"][i][j]), 0, 'positive',
              limit,
              unit["resource_map"][i][j] > 0 && j.indexOf('diff') >= 0) + '</td>';
        }
        result += '</tr>';
        has_header = true;
      }
    }

    result ? result = (balance_header ? '<tr>' + balance_header + '</tr>' : '') + result : false;
  }
  !result ? result = '<tr><th class="c_c">' + language['eco_bld_unit_info_extra_none'] + '</th></tr>' : false;
  $('#unit_balance').html('<table class="border_image_small">' + result + '</table>');
  $('<style></style>').appendTo($(document.body)).remove();
}

function eco_struc_select_unit(unit_id) {
  $('#unit_id').val(unit_id);
  if (unit_selected == unit_id) {
    unit_selected = null;
  } else {
    if (unit_selected) {
      $('#unit' + unit_selected).removeClass('unit_border_selected');

      unit_selected = null; // Need to override eco_struc_show_unit_info() behavior
      eco_struc_show_unit_info(unit_id);
    }
    unit_selected = unit_id;
    $('#unit_amountslide').slider({value: 0});
  }
}

$(document).on('click', '#eco_que_clear', function (e) {
  snConfirm({
    that: $(this),
    message: language.eco_que_clear_dialog_text,
    title: language.eco_que_clear_dialog_title
  });
  return false;
});

$(document).on('click', '#eco_que_artifact', function (e) {
  snConfirm({
    that: $(this),
    message: language.eco_que_artifact_dialog_text.format($(this).text()),
    title: language.eco_que_artifact_dialog_title.format($(this).text())
  });
  return false;
});
