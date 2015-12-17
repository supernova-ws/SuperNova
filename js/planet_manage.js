// Planet overview - Manage

$(document)
  .on('change', "#density_type", function () {
    selected = jQuery("#density_type option:selected");
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

  //.on('submit', '#planet_abandon_form', function (e) {
  //  if($(this).prop('submitted')) {
  //    return true;
  //  }
  //  if (!$(this).attr('disabled')) {
  //    snConfirm({
  //      that: $(this),
  //    });
  //  }
  //  e.preventDefault();
  //  return false;
  //})

  .on('submit', '#planet_teleport_form, #planet_abandon_form', function (e) {
    if($(this).prop('submitted')) {
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
