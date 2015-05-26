if(typeof(window.LOADED_GLOBAL) === 'undefined') {
  var LOADED_GLOBAL = true;

  var sn_inframe;
  function getFrameName(frame) {
    for (var i = 0; i < parent.frames.length; i++) {
      if (parent.frames[i] === frame) {
        return (parent.frames[i].name); //
      }
    }
  }

  sn_inframe = window.frameElement ? getFrameName(self) : false;
  //if(sn_inframe = window.frameElement ? getFrameName(self) : false) {
  //  if(sn_inframe != 'sn_frame_chat' && sn_inframe != 'sn_frame_main') {
  //    top.location.href = SN_ROOT_VIRTUAL;
  //  }
  //}
  //alert(sn_inframe);
  //alert(top === self);

  var sn_delay = function(func, wait) {
    var args = Array.prototype.slice.call(arguments, 2);
    return setTimeout(function(){ return func.apply(null, args); }, wait);
  };

  String.prototype.format = function () {
    var args = arguments;
    return this.replace(/\{\{|\}\}|\{(\d+)\}/g, function (m, n) {
      if (m == "{{") { return "{"; }
      if (m == "}}") { return "}"; }
      return args[n];
    });
  };

  var language = {};

  var x = "";
  var e = null;

  jQuery(document).ready(function () {
    // Натягиваем скины на элементы ввода
    inputs = jQuery("input");
    inputs.filter(':button, :submit, :reset').button().addClass('ui-textfield');
    inputs.filter(':text, :password, :file').button().addClass('ui-textfield');
    inputs.filter(':checkbox, :radio').addClass("ui-corner-all ui-state-default ui-textfield");
    jQuery("button").button().addClass('ui-textfield');
    // jQuery('textarea:not(#ally_text)').button().addClass('ui-textfield');

    calc_elements();

    // Запуск таймеров
    if (typeof sn_timer === 'function') {
      sn_timer();
    }



    $(document).on('click', '#font_minus, #font_normal, #font_plus', function(){
      temp = FONT_SIZE;
      $(this).attr('id') == 'font_plus' ? FONT_SIZE += FONT_SIZE_PERCENT_STEP :
        ($(this).attr('id') == 'font_minus' ? FONT_SIZE -= FONT_SIZE_PERCENT_STEP : FONT_SIZE = FONT_SIZE_PERCENT_DEFAULT);
      FONT_SIZE > FONT_SIZE_PERCENT_MAX ? FONT_SIZE = FONT_SIZE_PERCENT_MAX :
        (FONT_SIZE < FONT_SIZE_PERCENT_MIN ? FONT_SIZE = FONT_SIZE_PERCENT_MIN : false);

      new_size = (parseFloat($('body').css('font-size')) * FONT_SIZE / temp);
      //console.log('old ' + $('body').css('font-size'));
      //$('html').css('font-size', new_size + 'px');



      //temp != FONT_SIZE ? $('*').css('font-size', new_size + 'px') : false;
      temp != FONT_SIZE ? $('body').css('font-size', new_size + 'px') : false;

      //console.log('new ' + $('body').css('font-size'));
      //console.log('exp ' + new_size + 'px');

      //Math.round(FONT_SIZE / temp * 100) != 100 ?
      //  $('*').each(function() {
      //    console.log($(this).css('font-size'));
      //    $(this).css('font-size', (parseFloat($(this).css('font-size')) * FONT_SIZE / temp) + 'px');
      //  }) : false;
      //$('*').css('font-size', (FONT_SIZE / temp * 100) + '%');
      //$('*').css('font-size', ($('html').css('font-size') * FONT_SIZE / temp) + 'px');


      //console.log('old ' + $('html').css('font-size'));
      //var currentFontSize = $('html').css('font-size');
      //var currentFontSizeNum = parseFloat(currentFontSize, 10);
      //var newFontSize = currentFontSizeNum*1.2;
      //$('html').css('font-size', newFontSize);
      //console.log(newFontSize);
      //console.log('new ' + $('html').css('font-size'));


      jQuery.post("time_probe.php", {'font_size': FONT_SIZE + '%'}, function(data) {});
    });
  });


  $(document).on('click', '.password_show', function(){
    input = $(this).parent().find("[name=" + $(this).attr('show_element') + "]").hide();
    if(input.attr('type') == 'password') {
      type = 'text';
      button_value = L_sys_login_password_hide;
    }
    else{
      type = 'password';
      button_value = L_sys_login_password_show;
    }

    var rep = $('<input type="' + type + '" maxlength="32" />').attr('name', input.attr('name')).attr("class", input.attr("class")).val(input.val()).insertBefore(input);;
//console.log(rep);
//console.log(input);
    rep.attr("id", input.attr("id"));

    input.remove();

    $(this).attr('value', button_value);

    // alert(show_element.attr('type'));
  });

  function sn_redirect(url) {
    document.location.assign(url);
//  document.location.href = url;
  }

  function sn_reload() {
    location.reload();
  }

  jQuery(document).on('change', '#fleet_ship_sort,#fleet_ship_sort_inverse', function () {
    jQuery.post('fleet.php?fleet_ship_sort=' + $('#fleet_ship_sort').val() + '&fleet_ship_sort_inverse=' + ($('#fleet_ship_sort_inverse').is(':checked') ? '1' : '0'), function(){
      sn_reload();
    });
  });

  /* CHAT_ADVANCED specific */
  jQuery(document).on('click', '.player_nick_award', function (e) {
    document.location.assign("index.php?page=imperator&int_user_id=" + jQuery(this).attr('player_id'));
  });
  jQuery(document).on('click', '.player_nick_race', function (e) {
    document.location.assign("index.php?page=races");
  });

  jQuery(document).on('click', 'button[href]', function (e) {
    sn_redirect($(this).attr('href'));
  });

  /* Empire ------------------------------------------------------------------------------------------ */
  jQuery(document).on('change', "#empire_overview select[selector]", function () {
    if (jQuery(this).val() != '-') {
      jQuery("select[name^='percent[" + jQuery(this).attr('selector') + "][']").val(jQuery(this).val());
    }
  });

// Хэндлеры для слайдеров
  jQuery(document).on('click', "input:button[id$='_ai_zero']", function (event, ui) {
    jQuery("#" + jQuery(this).attr('parent_id')).val(0).trigger('change', [event, ui]);
  });
  jQuery(document).on('click', "input:button[id$='ai_max']", function (event, ui) {
    field_name = '#' + jQuery(this).attr('parent_id');
    jQuery(field_name).val(parseInt(jQuery(field_name + 'slide').slider("option", "max"))).trigger('change', [event, ui]);
  });
  jQuery(document)
    .on('mousedown', "input:button[id$='ai_dec'],input:button[id$='ai_inc']", function (event, ui) {
      that = jQuery(this);
      var parentIEFix = jQuery('#' + that.attr('parent_id'));
      if (parentIEFix.is('[disabled]') || parentIEFix.is('[slider_ticks]')) {
        return;
      }

      slider = jQuery("#" + that.attr('parent_id') + 'slide');

      parentIEFix.attr('slider_ticks', 0)
        .attr('step_now', slider.slider('option', 'step'))
        .attr('increase', that.attr('id') == parentIEFix.attr('id') + '_ai_inc' ? 1 : -1);
      sn_ainput_mouselerate_jquery();
    })
    .on('mouseup', "input:button[id$='ai_dec'],input:button[id$='ai_inc']", function (event, ui) {
      var parentIEFix = jQuery('#' + jQuery(this).attr('parent_id'));
      clearTimeout(parentIEFix.attr('timeout'));
      parentIEFix.removeAttr('slider_ticks').removeAttr('step_now').removeAttr('increase').removeAttr('timeout');
    })
  ;
  jQuery(document)
    .on('keyup change', "[ainput]", function (event, ui) {
      if (ui != undefined && ui.type == 'slidechange') {
        return;
      }
      slider = jQuery('#' + jQuery(this).attr('id') + 'slide');
      value = (value = parseInt(jQuery(this).val())) ? value : 0;
      value = value > (max_slide = parseInt(slider.slider("option", "max"))) ? max_slide :
        (value < (min_slide = parseInt(slider.slider("option", "min"))) ? min_slide : value);

      jQuery(this).val(value);
      slider.slider("value", value);
    })
    .on('focus', "[ainput]", function (event, ui) {
      if (this.value == '0') this.value = '';
      this.select();
    })
    .on('blur', "[ainput]", function (event, ui) {
      that = jQuery(this);
      that.val(parseInt(that.val()) ? that.val() : 0);
    })
  ;
// Спецхэндлер - если мышку отпустят за пределом элемента
  jQuery(document).on('mouseup', function (event, ui) {
    jQuery('[slider_ticks]').each(function () {
      clearTimeout(jQuery(this).attr('timeout'));
      jQuery(this).removeAttr('slider_ticks').removeAttr('step_now').removeAttr('increase').removeAttr('timeout');
    });
    // TODO - Код для старых слайдеров. Убрать, когда все старые слайдеры не будут использоваться
    if (accelerated) {
      clearTimeout(accelerated['timeout']);
      accelerated = undefined;
    }
  });

// Хэндлеры других специальных элементов
// Элементы редиректа
  jQuery(document).on('click', "[go_url]", function () {
    if (jQuery(this).attr('target') == '_blank') {
      window.open(jQuery(this).attr('go_url'), '_blank');
    } else {
      document.location = jQuery(this).attr('go_url');
    }
  });

  jQuery(document).on('click', "[go]", function () {
    planet_id = (planet_id = parseInt(jQuery(this).attr('planet_id'))) ? planet_id : parseInt(jQuery(this).parent().attr('planet_id'));
    unit_id = (unit_id = parseInt(jQuery(this).attr('unit_id'))) ? unit_id : parseInt(jQuery(this).parent().attr('unit_id'));
    mode = jQuery(this).attr('mode');
    switch (jQuery(this).attr('go')) {
      case 'info':
        page = 'infos';
        break;
      // case 'galaxy': page = 'galaxy'; break;
      case 'flying':
        page = 'flying_fleets';
        break;
      case 'fleet':
        page = 'fleet';
        break;
      case 'build':
        page = 'buildings';
        break;
      case 'res':
        page = 'resources';
        break;
      default:
        page = 'overview';
    }
    document.location = page + '.php?' + (planet_id ? 'cp=' + planet_id + (mode ? '&' : '') : '')
    + (mode ? 'mode=' + mode : '')
    + (unit_id ? 'gid=' + unit_id + (typeof ALLY_ID !== 'undefined' && parseInt(ALLY_ID) ? '&ally_id=' + ALLY_ID : '') : '')
    ;
  });
// Сбор ресурсов
  jQuery(document).on('click', ".gather_resources", function () {
    that = $(this);
    document.location = 'fleet.php?fleet_page=5' + (typeof PLANET_ID !== 'undefined' && parseInt(PLANET_ID) ? '&cp=' + parseInt(PLANET_ID) : '')
    + (parseFloat(that.attr('metal')) ? '&metal=' + parseFloat(that.attr('metal')) : '')
    + (parseFloat(that.attr('crystal')) ? '&crystal=' + parseFloat(that.attr('crystal')) : '')
    + (parseFloat(that.attr('deuterium')) ? '&deuterium=' + parseFloat(that.attr('deuterium')) : '')
    ;
  });

  function sn_ainput_mouselerate_jquery() {
    jQuery('[slider_ticks]').each(function () {
      that = jQuery(this);
      slider = jQuery("#" + that.attr('id') + 'slide');
      val = (val = parseInt(that.val())) ? val : 0;
      step_now = parseInt(that.attr('step_now'));
      val_next = val + step_now * parseInt(that.attr('increase'));
      val_next = val_next > (option_min = parseInt(slider.slider("option", "min"))) ? val_next : option_min;
      val_next = val_next < (option_max = parseInt(slider.slider("option", "max"))) ? val_next : option_max;

      if (val != val_next) {
        that.val(val_next).trigger('change');

        that
          .attr('slider_ticks', ticks = parseInt(that.attr('slider_ticks')) + 1)
          .attr('step_now', Math.round(step_now * (ticks % 4 == 0 ? 3 : 1)));
        that.attr('timeout', window.setTimeout(sn_ainput_mouselerate_jquery, ticks == 1 ? 700 : 250));
      }
    });
  }

  function sn_ainput_make_jquery(field_name, options) {
    jQuery('ainput').each(function () {
      col_span = 3;

      old = jQuery(this);
      //min_value = (min_value = old.attr('min')) ? min_value : 0;
      //max_value = (max_value = old.attr('max')) ? max_value : 0;
      step_value = (step_value = old.attr('step')) ? step_value : 1; // TODO НЕ РАБОТАЕТ ИСПРАВИТЬ
      start_value = (start_value = old.val()) ? start_value : 0; // TODO НЕ РАБОТАЕТ ИСПРАВИТЬ

      field_name_orig = old.attr('name');

      field_name = field_name_orig.replace('[', '').replace(']', '');
      field_id = '#' + field_name;

      slider_name = field_name + 'slide';
      slider_id = "#" + slider_name;

      new_element = '<table width="100%" class="markup">'; // main container - sets width
      new_element += '<tr>';
      if (!old.is('[disable_min]')) {
        new_element += '<td width="3em"><input type="button" value="0" parent_id="' + field_name + '" id="' + field_name + '_ai_zero" style="width: 3em"></td>';
        col_span++;
      }
      new_element += '<td width="3em"><input type="button" value="-" parent_id="' + field_name + '" id="' + field_name + '_ai_dec" style="width: 3em"></td>'; // style="width: 6px"
      //new_element += '<td><input type="text" ainput="true" value="0" id="' + field_name + '" style="width: ' + '100%' + '; margin: 0em 5em 0em 0em; padding: 0em 5em 0em -20em" name="' + field_name_orig + '" /></td>'; // onfocus="if(this.value == \'0\') this.value=\'\';" onblur="if(this.value == \'\') this.value=\'0\';"
      new_element += '<td style="padding-right: 2.25em;"><input type="text" ainput="true" value="0" id="' + field_name + '" style="width: ' + '100%' + ';" name="' + field_name_orig + '" /></td>'; // onfocus="if(this.value == \'0\') this.value=\'\';" onblur="if(this.value == \'\') this.value=\'0\';"
      new_element += '<td width="3em"><input type="button" value="+" parent_id="' + field_name + '" id="' + field_name + '_ai_inc"  style="width: 3em"></td>';
      if (!old.is('[disable_max]')) {
        new_element += '<td width="3em"><input type="button" value="M" parent_id="' + field_name + '" id="' + field_name + '_ai_max"  style="width: 3em"></td>';
        col_span++;
      }
      new_element += '</tr>';
      new_element += '<tr><td colspan="' + col_span + '"><div style="margin: 0.75em; width: auto; height: 1em;" parent_id="' + field_name + '" id="' + slider_name + '"></div></td></tr>'; // slider container
      new_element += '</table>'; // main container

      jQuery(new_element).insertBefore(old);
      old.remove();

      jQuery("#" + field_name).val(start_value);

      jQuery(slider_id).slider({
        'range': "min",
        'value': start_value,
        'min': (min_value = old.attr('min')) ? min_value : 0,
        'max': (max_value = old.attr('max')) ? max_value : 0,
        'step': 1, // step_value,
        slide: function (event, ui) {
          jQuery("#" + jQuery(this).attr('parent_id')).val(ui.value).trigger('change', [event, ui]);
        },
        change: function (event, ui) {
          jQuery("#" + jQuery(this).attr('parent_id')).val(ui.value).trigger('change', [event, ui]);
        }
      });
    });
  }


  var accelerated;

  function sn_ainput_make(field_name, options) {
    var min_value = options['min'] ? options['min'] : 0;
    var max_value = options['max'] ? options['max'] : 0;
    var step_value = options['step'] ? options['step'] : 1;
    var start_value = options['value'] ? options['value'] : min_value;
    var col_span = 3;

    var field_name_orig = field_name;

    field_name = field_name.replace('[', '').replace(']', '');

    var slider_id = "#" + field_name + 'slide';

    document.write('<table width="100%" class="markup">'); // main container - sets width
    document.write('<tr>');
    if (options['button_zero']) {
      document.write('<td><input type="button" value="0" id="' + field_name + 'zero" style="max-width: 31px"></td>'); //
      col_span++;
    }
    document.write('<td><input type="button" value="-" id="' + field_name + 'dec" style="max-width: 31px"></td>'); // style="width: 6px"
    document.write('<td><input type="text" value="0" id="' + field_name + '" style="width: ' + '80%' + ';" name="' + field_name_orig + '" onfocus="if(this.value == \'0\') this.value=\'\';" onblur="if(this.value == \'\') this.value=\'0\';"/></td>');
    document.write('<td><input type="button" value="+" id="' + field_name + 'inc"  style="max-width: 31px"></td>');
    if (options['button_max']) {
      document.write('<td><input type="button" value="M" id="' + field_name + 'max"  style="max-width: 31px"></td>');
      col_span++;
    }
    document.write('</tr>');
    document.write('<tr><td colspan="' + col_span + '"><div style="margin: 6px; width: auto" id="' + field_name + 'slide"></div></td></tr>'); // slider container
    document.write('</table>'); // main container


    jQuery(slider_id).slider(
      {
        range: "min",
        value: start_value,
        min: min_value,
        max: max_value,
        step: 1,
        slide: function (event, ui) {
          jQuery("#" + field_name).val(ui.value).trigger('change', [event, ui]);
        },
        change: function (event, ui) {
          jQuery("#" + field_name).val(ui.value).trigger('change', [event, ui]);
        }
      });
    jQuery("#" + field_name).val(jQuery(slider_id).slider("value"));

    jQuery("#" + field_name).bind('keyup change', function (event, ui) {
      if (ui != undefined && ui.type == 'slidechange') {
        return;
      }

      value = parseInt(jQuery(this).val());
      value = value ? value : 0;

      slider = jQuery(slider_id);

      if (value > parseInt(slider.slider("option", "max"))) {
        jQuery(this).val(slider.slider("option", "max"));
      }

      if (value < parseInt(slider.slider("option", "min"))) {
        jQuery(this).val(slider.slider("option", "min"));
      }

      slider.slider("value", value);
    }).button().addClass('ui-textfield');

    jQuery("#" + field_name + 'zero').bind('click', function (event, ui) {
      jQuery("#" + field_name).val(0).trigger('change', [event, ui]);
    }).button();

    jQuery("#" + field_name + 'max').bind('click', function (event, ui) {
      jQuery("#" + field_name).val(jQuery(slider_id).slider("option", "max")).trigger('change', [event, ui]);
    }).button();

    jQuery("#" + field_name + 'dec, ' + "#" + field_name + 'inc')
      .bind('mousedown', function (event, ui) {
        var element = jQuery("#" + field_name);
        if (element.is('[disabled]')) {
          return;
        }
        accelerated = {
          'element': element,
          'step': step_value,
          'slider': slider_id,
          'ticks': 0,
          'step_now': step_value,
          'timeout': 0,
          'increase': $(this).attr('id') == field_name + 'inc'
        };
        sn_ainput_mouselerate();
      })
      .bind('mouseup', function (event, ui) {
        if (accelerated) {
          clearTimeout(accelerated['timeout']);
          accelerated = undefined;
        }
      }
    ).button();

    //jQuery('#' + field_name).button().addClass('ui-textfield');
    //jQuery('#' + field_name + 'zero').button();
    //jQuery('#' + field_name + 'dec').button();
    //jQuery('#' + field_name + 'max').button();
    //jQuery('#' + field_name + 'inc').button();
  }

  function sn_ainput_mouselerate() {
    var donext = false;
    if (accelerated['increase'] && (val = parseInt(accelerated['element'].val())) < (option_max = jQuery(accelerated['slider']).slider("option", "max"))) {
      donext = val + accelerated['step_now'] < option_max;
      accelerated['element'].val(donext ? val + accelerated['step_now'] : option_max).trigger('change'); // , [event, ui]
    }
    if (!accelerated['increase'] && (val = parseInt(accelerated['element'].val())) > 0) {
      donext = val - accelerated['step_now'] > 0;
      accelerated['element'].val(donext ? val - accelerated['step_now'] : 0).trigger('change'); // , [event, ui]
    }

    if (donext) {
      accelerated['ticks']++;
      if (accelerated['ticks'] % 3 == 0) {
        accelerated['step_now'] = accelerated['step_now'] * 2;
      }
      accelerated['timeout'] = window.setTimeout(sn_ainput_mouselerate, 200);
    }
  }

  var popup = jQuery(document.createElement("span"));
  var popupIsOpen = false;
  popup.dialog({autoOpen: false}); // , width: auto, resizable: false
  popup.mouseleave(function () {
    popup_hide()
  });

  function popup_hide() {
    $('[popup_opened_here]').removeAttr('popup_opened_here');
    popup.dialog("close");
    popupIsOpen = false;
  }

  function popup_show(html, width, aClientX, aClientY, positioning) {
    popup_hide();
    if(width) {
      popup.dialog("option", "width", width);
    }
    if(positioning) {
      popup.dialog("option", "position", positioning);
    } else {
      aClientX = aClientX ? aClientX : clientX;
      aClientY = aClientY ? aClientY : clientY;
      popup.dialog("option", "position", [aClientX, aClientY]); // + 20
    }
    popup.html(html);
    popup.dialog("open");
    popupIsOpen = true;
  }


// Helper probe to use CSS-values in JS
  function sn_probe_style(element, css_attribute) {
    switch (css_attribute) {
      case 'border-top-color':
        if (element.currentStyle)
          return element.currentStyle.borderTopColor;
        if (document.defaultView)
          return document.defaultView.getComputedStyle(element, '').getPropertyValue('border-top-color');
        break;
    }

    return false;
  }

  var element_cache = new Object();

  function calc_elements() {
    if (element_cache['_IS_INIT']) {
      return;
    }

    var all_elements = document.getElementsByTagName('*');

    for (element in all_elements) {
      if (all_elements[element].id != undefined) {
        element_cache[all_elements[element].id] = all_elements[element];
      }
    }
    element_cache['_IS_INIT'] = true;
  }

  var mouseX, mouseY;
  var clientX, clientY;

  jQuery(document).mousemove(function (e) {
    mouseX = e.pageX;
    mouseY = e.pageY;

    clientX = e.clientX;
    clientY = e.clientY;
  });


  function sn_show_hide(element, element_name) {
    var element_to_hide = jQuery("#" + element_name);
    var tag_name = element_to_hide[0].tagName;

    element_to_hide.css('display', element_to_hide.css('display') == 'none' ? (tag_name == 'TR' ? 'table-row' : (tag_name == 'UL' || tag_name == 'DIV' ? 'block' : 'inline')) : 'none');
    jQuery(element).html("[&nbsp;" + (element_to_hide.css('display') == 'none' ? LA_sys_show : LA_sys_hide) + "&nbsp;]");
  }


  function cntchar(m) {
    if (window.document.forms[0].text.value.length > m) {
      window.document.forms[0].text.value = x;
    } else {
      x = window.document.forms[0].text.value;
    }
    if (e == null)
      e = document.getElementById('cntChars');
    else
      e.childNodes[0].data = window.document.forms[0].text.value.length;
  }

  function sn_format_number(number, precission, style, max, plus_sign) {
    if (!precission) {
      precission = 0;
    }

    if (!max) {
      max = 0;
    }

    // isNaN(number) || typeof number == 'undefined' ? number = 0 : false;

    number = Math.round(number * Math.pow(10, precission)) / Math.pow(10, precission);
    if (number > 0) {
      str_number = number + '';
    }
    else {
      str_number = (-number) + '';
    }
    arr_int = str_number.split('.');
    if (!arr_int[0]) arr_int[0] = '0';
    if (!arr_int[1]) arr_int[1] = '';
    if (arr_int[1].length < precission) {
      nachkomma = arr_int[1];
      for (i = arr_int[1].length + 1; i <= precission; i++) {
        nachkomma += '0';
      }
      arr_int[1] = nachkomma;
    }

    if (arr_int[0].length > 3) {
      Begriff = arr_int[0];
      arr_int[0] = '';
      for (j = 3; j < Begriff.length; j += 3) {
        Extrakt = Begriff.slice(Begriff.length - j, Begriff.length - j + 3);
        arr_int[0] = '.' + Extrakt + arr_int[0] + '';
      }
      str_first = Begriff.substr(0, (Begriff.length % 3 == 0) ? 3 : (Begriff.length % 3));
      arr_int[0] = str_first + arr_int[0];
    }

    ret_val = arr_int[0] + (arr_int[1] ? ',' + arr_int[1] : '');
    if (number < 0) {
      ret_val = '-' + ret_val;
    } else if (number > 0 && plus_sign) {
      ret_val = '+' + ret_val;
    }

    if (style) {
      if (number == Math.abs(max)) {
        ret_val = '<span class="neutral">' + ret_val + '</span>';
      }
      else if ((max > 0 && -number < -max) || (!max && number < 0) || (max < 0 && number < -max)) {
        ret_val = '<span class="negative">' + ret_val + '</span>';
      }
      else {
        ret_val = '<span class="' + style + '">' + ret_val + '</span>';
      }
    }

    return ret_val;
  }

  function sn_timestampToString(timestamp, useDays) {
    strTime = '';

    if (useDays) {
      tmp = Math.floor(timestamp / (60 * 60 * 24));
      timestamp -= tmp * 60 * 60 * 24;
      strTime += (tmp > 0 ? tmp + 'd ' : '');
    }

    tmp = Math.floor(timestamp / (60 * 60));
    timestamp -= tmp * 60 * 60;
    strTime += (tmp <= 9 ? '0' + tmp : tmp) + ':';

    tmp = Math.floor(timestamp / 60);
    timestamp -= tmp * 60;
    strTime += (tmp <= 9 ? '0' + tmp : tmp) + ':';

    strTime += (timestamp <= 9 ? '0' + timestamp : timestamp);

    return strTime;
  }

}
