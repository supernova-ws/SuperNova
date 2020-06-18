// Planet overview - Manage

$(function () {
  $("#dialog-rename-planet").dialog({
    autoOpen: false,
    resizable: false,
    modal: true,
    open: function () {
      var element = $(this).parent();
      element.find('.ui-dialog-titlebar').css('display', 'block');
    },
    buttons: [
      {
        text: language['ov_rename'],
        click: function () {
          var element = $(this).parent();
          element.find('input[name=cp]').val($('.js_navbar_planet_select').find('option:selected').val());
          element.find(':button:not(.ui-dialog-titlebar-close)').button('disable');
          jQuery('#dialog-rename-planet-form').submit();
        }
      },
      {
        text: language['sys_cancel'],
        click: function () {
          $(this).dialog("close");
        }
      }
    ]
  });
});

$(document)
  .on('click', "#planet_rename", function () {
    $('#dialog-rename-planet').dialog('open');
  })

  .on('change', "#density_type", function () {
    selected = jQuery("#density_type").find("option:selected");
    $("#transmute_button").button({disabled: parseInt(selected.attr("rest")) <= 0 || selected.attr("current") == '1'});
    $("#transmutation_cost").html(selected.attr("html")).removeClass().addClass(selected.attr("html_class"));
  })

  .on('click', '#planet_make_capital', function (e) {
    if (!$(this).attr('disabled')) {
      snConfirm({
        that: $(this),
      });
    }
    e.preventDefault();
    return false;
  })

  .on('submit', '#planet_teleport_form, #planet_abandon_form', function (e) {
    if ($(this).prop('submitted')) {
      return true;
    }
    if (!$(this).attr('disabled')) {
      snConfirm({
        that: $(this),
      });
    }
    e.preventDefault();
    return false;
  });
