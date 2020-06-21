if (typeof(window.LOADED_GLOBAL) === 'undefined') {

  /*global sn_inframe:true LOADED_GLOBAL:true*/
  /*eslint no-undef: "error"*/
  var LOADED_GLOBAL = true;

  // Constants
  var CLASS_POSITIVE = "positive";


  // Localization class
  var LanguageObject = function() {
  };
  LanguageObject.prototype = {
    /**
     * Add list of locale strings to current locale
     *
     * @param {object} strings
     */
    addLocale: function (strings) {
      jQuery.extend(this, strings);
    }
  };
  var language = new LanguageObject();

  var x = "";
  var e = null;
  var sn_inframe;

  // Fix to jQuery-UI improper tab handling
  $.fn.__tabs = $.fn.tabs;
  $.fn.tabs = function (a, b, c, d, e, f) {
    var base = window.location.href.replace(/#.*$/, '');
    $('ul > li > a[href^="#"]', this).each(function () {
      var href = $(this).attr('href');
      $(this).attr('href', base + href);
    });
    return $(this).__tabs(a, b, c, d, e, f);
  };

  /**
   * Converts value to integer
   *
   * @param value
   * @returns {int}
   */
  Math.intVal = function (value) {
    var parsed = parseInt(value, 10);
    return parsed ? parsed : 0;
    // return typeof parsed === 'number' && !isNaN(parsed) && parsed !== Infinity ? parsed : 0;
  };

  /**
   * Converts value to float
   *
   * @param value
   * @returns {float}
   */
  Math.floatVal = function (value) {
    var parsed = parseFloat(value);
    return parsed ? parsed : 0;
  };

  Math.roundVal = function (value) {
    return Math.round(Math.floatVal(value));
  };
  Math.floorVal = function (value) {
    return Math.floor(Math.floatVal(value));
  };
  Math.ceilVal = function (value) {
    return Math.ceil(Math.floatVal(value));
  };

  /**
   * Вычисление процента с масштабированием (улучшенной точностью)
   *
   * @param {number} number1 - первое число
   * @param {number} number2 - второе число
   * @param {number} scale - масштаб/точность. Например, при scale = 10 - точность будет 0.1%
   * @returns {number}
   */
  Math.percent = function (number1, number2, scale) {
    //!(scale = Math.round(scale)) ? scale = 1 : false;
    !scale ? scale = 1 : false;
    return number2 ? Math.round((number1 / number2 * 100 * scale) / scale) : 0;
  };
  /**
   * Ширина в процентных пикселях
   *
   * @param {number} number1 - первое число
   * @param {number} number2 - второе число
   * @param {number} pixels - оригинальная ширина в пикселях. Если отрицательная - возвращается дополнение до ширины (pixels - %pixels)
   * @returns {number|string}
   */
  Math.percentPixels = function (number1, number2, pixels) {
    return number2 ? Math.round((pixels < 0 ? -pixels : 0) + (number1 / number2 * pixels)) + 'px' : 0;
  };


  /**
   * Gets name of supplied frame
   *
   * @param frame
   * @returns {*}
   */
  function getFrameName(frame) {
    for (var i = 0; i < parent.frames.length; i++) {
      if (parent.frames[i] === frame) {
        return (parent.frames[i].name); //
      }
    }
  }
  sn_inframe = window.frameElement ? getFrameName(self) : false;

  function sn_blink(that) {
    that = $(that);
    that.animate(
      {opacity: that.css('opacity') == 0 ? 1 : 0},
      that.attr('duration') ? parseInt(that.attr('duration'), 10) : 1000,
      function () {sn_blink(this)}
    );
  }

  /**
   * Delays function execution
   *
   * @param func
   * @param wait
   * @returns {number}
   */
  var sn_delay = function (func, wait) {
    var args = Array.prototype.slice.call(arguments, 2);
    return setTimeout(function () {
      return func.apply(null, args);
    }, wait);
  };

  /**
   * Formats string, replacing {0}, {1}, {2} etc with zero, first, second etc param
   *
   * @returns {string}
   */
  String.prototype.format = function () {
    var args = arguments;
    return this.replace(/\{\{|\}\}|\{(\d+)\}/g, function (m, n) {
      if (m == "{{") {
        return "{";
      }
      if (m == "}}") {
        return "}";
      }
      return args[n];
    });
  };

  /**
   * Skins input elements
   */
  function skinInputs() {
    var inputs = jQuery("input:not(.do-not-skin):not(.do-not-skin-child *),button:not(.do-not-skin *):not(.do-not-skin-child *)");
    inputs.filter(':button, :submit, :reset').button(); // .addClass('ui-textfield');
    inputs.filter(':text, :password, :file').button().addClass('ui-textfield ui-input-text').off('keydown');
    // inputs.filter(':checkbox, :radio').addClass("ui-corner-all ui-state-default ui-textfield");
    inputs.filter(':radio').addClass("ui-corner-all ui-state-default ui-textfield");
    jQuery("button:not(.do-not-skin):not(.do-not-skin-child *)").button().addClass('ui-textfield');
    jQuery('textarea:not(#ally_text):not(.do-not-skin):not(.do-not-skin-child *)').button().addClass('ui-textfield ui-input-text').off('keydown');

    //inputs.filter(':checkbox, :radio').checkator();
  }

  function makeBlink() {
    if (!jQuery.fx.off) {
      jQuery('.blink').each(function () {
        sn_blink(this);
      });
    }
  }

  //jQuery(document).ready(function () {
  // Нельзя полагаться на document.ready() из-за возможных проблем с загрузкой скриптов со сторонних серверов!
  function document_ready() {
    var theBody = $('body');

    // Натягиваем скины на элементы ввода
    skinInputs();

    // Запуск таймеров
    if (typeof(sn_timer) === 'function') {
      sn_timer();
    }

    // It's here 'cause font manipulation is available only after DOM fully loaded
    $(document).on('click', '#font_minus, #font_normal, #font_plus', function () {
      var temp = snFont.size;

      switch ($(this).attr('id')) {
        case 'font_plus':
          snFont.size += snFont.step;
          break;
        case 'font_minus':
          snFont.size -= snFont.step;
          break;
        default:
          snFont.size = snFont.sizeDefaultPercent;
          break;
      }

      snFont.size = Math.min(snFont.size, snFont.max);
      snFont.size = Math.max(snFont.size, snFont.min);

      var new_size = Math.floatVal(theBody.css('font-size')) * snFont.size / temp;

      if (temp != snFont.size) {
        theBody.css('font-size', new_size + 'px');
      }

      jQuery.post("time_probe.php", {'font_size': snFont.size + '%'}, function (data) {
      });
    });

    makeBlink();
  }

  $(document).on('click', '.password_show', function () {
    var button_value, input, type;
    input = $(this).parent().find("[name=" + $(this).attr('show_element') + "]").hide();
    if (input.attr('type') == 'password') {
      type = 'text';
      button_value = language['sys_login_password_hide'];
    }
    else {
      type = 'password';
      button_value = language['sys_login_password_show'];
    }

    var rep = $('<input type="' + type + '" maxlength="32" />').attr('name', input.attr('name')).attr("class", input.attr("class")).val(input.val()).insertBefore(input);
    rep.attr("id", input.attr("id"));

    input.remove();

    $(this).attr('value', button_value);
  });

  function sn_redirect(url) {
    document.location.assign(url);
  }

  function openInNewTab(url) {
    var win = window.open(url, '_blank');
    win.focus();
  }

  function sn_reload() {
    location.reload();
  }

  jQuery(document).on('click', '[news_toggle]', function () {
    $('#news_' + $(this).attr('news_toggle')).show();
    $(this).remove();
  });

  jQuery(document).on('click', '.survey_block [survey_id]', function () {
    var survey_id = $(this).attr('survey_id');
    $('.survey_block [survey_id=' + survey_id + ']').removeClass('button_pseudo_pressed');
    $(this).addClass('button_pseudo_pressed');
    $('input:radio[name="survey[' + survey_id + ']"]').val([$(this).attr('answer_id')]);
  });

  jQuery(document).on('click', '.button_pseudo', function () {
    $(this).addClass('button_pseudo_pressed');
  });

  jQuery(document).on('change', '#sort_elements,#sort_elements_inverse', function () {
    jQuery.post(
      $('#page_file_name').val()
      + '?mode=' + $('#mode').val()
      + '&sort_elements=' + $('#sort_elements').val()
      + '&sort_elements_inverse=' + ($('#sort_elements_inverse').is(':checked') ? '1' : '0'),
      function () {
        sn_reload();
      }
    );
  });

  /* CHAT_ADVANCED specific */
  jQuery(document).on('click', '.player_nick_award', function () {
    document.location.assign("index.php?page=imperator&int_user_id=" + jQuery(this).attr('player_id'));
  });
  jQuery(document).on('click', '.player_nick_race', function () {
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
    var field_name = '#' + jQuery(this).attr('parent_id');
    jQuery(field_name).val(parseInt(jQuery(field_name + 'slide').slider("option", "max"))).trigger('change', [event, ui]);
  });
  jQuery(document)
    .on('mousedown', "input:button[id$='ai_dec'],input:button[id$='ai_inc']", function (event, ui) {
      var that = jQuery(this);
      var parentIEFix = jQuery('#' + that.attr('parent_id'));
      if (parentIEFix.is('[disabled]') || parentIEFix.is('[slider_ticks]')) {
        return;
      }

      var slider = jQuery("#" + that.attr('parent_id') + 'slide');

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

      var slider, value, min_slide, max_slide;

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
      var that = jQuery(this);
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

  function attr_on_me_or_parent(that, attr) {
    return parseInt(jQuery(that).attr(attr) ? jQuery(that).attr(attr) : jQuery(that).parent().attr(attr));
  }

  jQuery(document).on('click', "[go]", function () {
    var location = [], planet_id, mode, unit_id;
    var planet_system, planet_planet, planet_type, mission;

    var target = jQuery(this).attr('target');
    var page = jQuery(this).attr('go');

    switch (page) {
      case 'info':
        page = 'infos';
        break;
      case 'flying':
        page = 'flying_fleets';
        break;
      case 'phalanx':
      case 'fleet':
        window.uni_galaxy ? location.push('galaxy=' + window.uni_galaxy) : false;
        window.uni_system ? location.push('system=' + window.uni_system) : false;
        (planet_planet = attr_on_me_or_parent(this, 'planet_planet')) ? location.push('planet=' + planet_planet) : false;
        (planet_type = attr_on_me_or_parent(this, 'planet_type')) ? location.push('planettype=' + planet_type) : false;
        (mission = attr_on_me_or_parent(this, 'mission')) ? location.push('target_mission=' + mission) : false;
        break;
      case 'galaxy':
        window.uni_galaxy ? location.push('galaxy=' + window.uni_galaxy) : false;
        (planet_system = attr_on_me_or_parent(this, 'planet_system')) ? location.push('system=' + planet_system) : false;
        break;
      case 'build':
        page = 'buildings';
        break;
      case 'res':
        page = 'resources';
        break;
      case 'stat':
        (mode = attr_on_me_or_parent(this, 'who')) ? location.push('who=' + mode) : false;
        location.push('range=' + attr_on_me_or_parent(this, 'rank') + '#' + attr_on_me_or_parent(this, 'rank'));
        break;
      case 'imperator':
        page = 'index';
        location.push('page=imperator');
        location.push('int_user_id=' + attr_on_me_or_parent(this, 'user_id'));
        break;
      case 'messages':
        location.push('id=' + attr_on_me_or_parent(this, 'user_id'));
        break;
      case 'buddy':
        location.push('request_user_id=' + attr_on_me_or_parent(this, 'user_id'));
        break;
      case 'alliance':
        location.push('a=' + attr_on_me_or_parent(this, 'ally_id'));
        break;
      default:
        page = 'overview';
    }
    (planet_id = attr_on_me_or_parent(this, 'planet_id')) ? location.push('cp=' + planet_id) : false;
    (mode = jQuery(this).attr('mode')) ? location.push('mode=' + mode) : false;
    (unit_id = attr_on_me_or_parent(this, 'unit_id')) ? location.push('gid=' + unit_id) : false;

    unit_id && window.ALLY_ID !== undefined && parseInt(ALLY_ID) ? location.push('ally_id=' + ALLY_ID) : false;

    location = page + '.php?' + location.join('&');

    target ? window.open(location, target) : (document.location = location);
    $(this).removeClass('button_pseudo_pressed');
  });


  // Сбор ресурсов
  jQuery(document).on('click', ".unit_preview .icon_gather", function () {
    var that = $(this);
    document.location = 'fleet.php?fleet_page=5' + (typeof PLANET_ID !== 'undefined' && parseInt(PLANET_ID) ? '&cp=' + parseInt(PLANET_ID) : '')
      + (parseFloat(that.attr('metal')) ? '&metal=' + parseFloat(that.attr('metal')) : '')
      + (parseFloat(that.attr('crystal')) ? '&crystal=' + parseFloat(that.attr('crystal')) : '')
      + (parseFloat(that.attr('deuterium')) ? '&deuterium=' + parseFloat(that.attr('deuterium')) : '')
    ;
  });

  function sn_ainput_mouselerate_jquery() {
    jQuery('[slider_ticks]').each(function () {
      var val, option_min, option_max, ticks;

      var that = jQuery(this);
      var slider = jQuery("#" + that.attr('id') + 'slide');
      val = (val = parseInt(that.val())) ? val : 0;
      var step_now = parseInt(that.attr('step_now'));
      var val_next = val + step_now * parseInt(that.attr('increase'));
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
      var step_value, start_value, min_value, max_value;

      var col_span = 3;

      var old = jQuery(this);
      //min_value = (min_value = old.attr('min')) ? min_value : 0;
      //max_value = (max_value = old.attr('max')) ? max_value : 0;
      step_value = (step_value = old.attr('step')) ? step_value : 1; // TODO НЕ РАБОТАЕТ ИСПРАВИТЬ
      start_value = (start_value = old.val()) ? start_value : 0; // TODO НЕ РАБОТАЕТ ИСПРАВИТЬ

      var field_name_orig = old.attr('name');

      field_name = field_name_orig.replace('[', '').replace(']', '');
      var field_id = '#' + field_name;

      var slider_name = field_name + 'slide';
      var slider_id = "#" + slider_name;

      var new_element = '<table width="100%" class="markup">'; // main container - sets width
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
      new_element += '<tr><td colspan="' + col_span + '"><div parent_id="' + field_name + '" id="' + slider_name + '"></div></td></tr>'; // slider container
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
    document.write('<tr><td colspan="' + col_span + '"><div id="' + field_name + 'slide"></div></td></tr>'); // slider container
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

    jQuery("#" + field_name).on('keyup change', function (event, ui) {
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

    jQuery("#" + field_name + 'zero').on('click', function (event, ui) {
      jQuery("#" + field_name).val(0).trigger('change', [event, ui]);
    }).button();

    jQuery("#" + field_name + 'max').on('click', function (event, ui) {
      jQuery("#" + field_name).val(jQuery(slider_id).slider("option", "max")).trigger('change', [event, ui]);
    }).button();

    jQuery("#" + field_name + 'dec, ' + "#" + field_name + 'inc')
      .on('mousedown', function (event, ui) {
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
      .on('mouseup', function (event, ui) {
          if (accelerated) {
            clearTimeout(accelerated['timeout']);
            accelerated = undefined;
          }
        }
      ).button();
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

  function popup_show(html, positioning, width) {
    popup_hide();
    popup.dialog("option", "width", width ? width : 'auto');
    popup.dialog("option", "position", positioning ? positioning : {my: 'center', at: 'center', of: window});
    popup.html(html);
    popup.dialog("open");
    popupIsOpen = true;
  }

  //
  /**
   * Helper to probe element's CSS attribute
   *
   * @param element
   * @param css_attribute
   * @returns {string|boolean}
   */
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

  /**
   * Switch element(s) visibility basing on his/their current state
   *
   * @param {element} control
   * @param {string} jQueryQualifier
   * @param {boolean} hideControl
   */
  function sn_show_hide2(control, jQueryQualifier, hideControl) {
    var element_to_hide = jQuery(jQueryQualifier);

    element_to_hide.each(function () {
      var that = $(this);
      var tag_name = that.tagName;

      that.css(
        'display',
        that.css('display') === 'none'
          ? (
            tag_name === 'TR'
              ? 'table-row'
              : (
                // Special HTML property 'flex' used to return flex-ability on "show"
                that.attr('flex') ?
                  'flex'
                  : (
                    tag_name === 'UL' || tag_name === 'DIV'
                      ? 'block'
                      : 'inline'
                  )
              )
          )
          : 'none');
    });

    if (hideControl) {
      $(control).hide();
    } else {
      var
        newHtml,
        newText = element_to_hide.first().css('display') === 'none' ? language.sys_show : language.sys_hide,
        textToReplace = newText === language.sys_show ? language.sys_hide : language.sys_show;

      if ($(control).html().search(textToReplace) === -1) {
        newHtml = "[&nbsp;" + (newText) + "&nbsp;]";
      } else {
        newHtml = $(control).html().replace(textToReplace, newText);
      }
      $(control).html(newHtml);
    }
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

  /**
   *
   * @param num
   * @param cssPositive
   * @param limit
   * @returns {string|*|string}
   */
  function numberGetCssClass(num, cssPositive, limit) {
    num = Math.floatVal(num);
    limit = Math.floatVal(limit);

    return Math.round(num - limit) == 0 ? "zero" : (
      (limit > 0 && limit > num) || (limit <= 0 && -limit <= num) ? cssPositive : "negative"
    );
  }

  function numberFormat(num, precision, plus_sign) {
    var str_number, arr_int, nachkomma, Begriff, i, j, Extrakt, str_first, result;

    precision = Math.intVal(precision);
    num = Math.round(Math.floatVal(num) * Math.pow(10, precision)) / Math.pow(10, precision);

    if (num > 0) {
      str_number = num + '';
    } else {
      str_number = (-num) + '';
    }
    arr_int = str_number.split('.');
    if (!arr_int[0]) arr_int[0] = '0';
    if (!arr_int[1]) arr_int[1] = '';
    if (arr_int[1].length < precision) {
      nachkomma = arr_int[1];
      for (i = arr_int[1].length + 1; i <= precision; i++) {
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

    result = arr_int[0] + (arr_int[1] ? ',' + arr_int[1] : '');
    if (num < 0) {
      result = '-' + result;
    } else if (num > 0 && plus_sign) {
      result = '+' + result;
    }

    return result;
  }

  /**
   *
   * @param num
   * @param precision
   * @param cssPositive
   * @param limit
   * @param forcePlus
   * @returns {string|*}
   */
  function sn_format_number(num, precision, cssPositive, limit, forcePlus) {
    var result = numberFormat(num, precision, forcePlus);

    if (cssPositive) {
      result = '<span class="' + numberGetCssClass(num, cssPositive === true ? CLASS_POSITIVE : cssPositive, limit) + '">' + result + '</span>';
    }

    return result;
  }

  function elementPrettyNumber(element, num, limit) {
    element.html(numberFormat(num))
      .removeClass("positive zero negative")
      .addClass(numberGetCssClass(num, "positive", limit));
  }

  function elementColorValue(element) {
    element.html();
  }

  function elementIsEnabled(element) {
    // Checking if elements is enabled
    // TODO - можно ли упростить выражение?
    return $(element).is(":enabled") && !($(this).attr("aria-disabled"));
  }


  function sn_timestampToString(timestamp, useDays) {
    var strTime = '', tmp;

    !(timestamp = parseInt(timestamp)) ? timestamp = 0 : false;

    if (useDays) {
      tmp = Math.floor(timestamp / (60 * 60 * 24));
      timestamp -= tmp * 60 * 60 * 24;
      strTime += (tmp > 0 ? tmp + 'd' : '');
    }

    tmp = Math.floor(timestamp / (60 * 60));
    timestamp -= tmp * 60 * 60;
    strTime += (tmp <= 9 ? '0' : '') + tmp + ':';

    tmp = Math.floor(timestamp / 60);
    timestamp -= tmp * 60;
    strTime += (tmp <= 9 ? '0' : '') + tmp + ':';

    strTime += (timestamp <= 9 ? '0' : '') + timestamp;

    return strTime;
  }

  function sn_timestampToStringHuman(timestamp, useDays) {
    var strTime = '', tmp;

    !(timestamp = parseInt(timestamp)) ? timestamp = 0 : false;
    tmp = Math.floor(timestamp / (60 * 60 * 24));
    if(tmp >= 3) {
      strTime = tmp + 'd';
    } else {
      tmp = Math.floor(timestamp / (60 * 60));
      if(tmp >= 3) {
        strTime = tmp + 'h';
      } else {
        tmp = Math.floor(timestamp / 60);
        if(tmp >= 3) {
          strTime = tmp + 'm';
        } else {
          strTime = timestamp + 's';
        }
      }
    }

    return strTime;
  }

  /**
   *
   *
   * @param local_time
   * @param format
   * bit 1 - have date
   * bit 2 - have time
   * bit 3 - date in YYYY-MM-DD format - otherwise toLocalDateString() format
   * @param nbsp - Replace space with &nbsp;
   * @returns {string}
   */
  function snDateToString(local_time, format, nbsp) {
    format === undefined ? format = 3 : false;

    return (format & 1 ? (format & 4 ? local_time.getFullYear() + '-' + ('0' + (local_time.getMonth() + 1)).slice(-2) + '-' + ('0' + local_time.getDate()).slice(-2) : local_time.toLocaleDateString()) : '') +
      (format & 3 ? (nbsp ? '&nbsp;' : ' ') : '') +
      (format & 2 ? local_time.toTimeString().substring(0, 8) : '');
  }

  jQuery(document).on('click', '#news_close', function (e) {
    jQuery('#fresh_news_table').css('display', 'none');
    jQuery.post('announce.php?only_hide_news=1');
  });

}

function snConfirm(params) {
  !params ? params = {} : false;
  // , message, title
  var that = $(params.that);
  var d = $.Deferred();
  $('<div><div class="sn-dialog-confirm">' + (params.message ? params.message : (that.attr('message') ? that.attr('message') : language.sys_confirm_action)) + '</div></div>').dialog({
    modal: true,
    autoOpen: true,
    resizable: false,
    width: params.width ? params.width : '40em',
    title: params.title ? params.title : (that.attr('title') ? that.attr('title') : language.sys_confirm_action_title),
    open: function () {
      var element = $(this).parent();
      element.find('.ui-dialog-titlebar').css('display', 'block');
      element.find('.ui-dialog-buttonpane button:last').focus();
    },
    buttons: {
      ok: {
        text: language.sys_confirm,
        'class': 'ok',
        click: function () {
          var element = $(this).parent();
          // Отключаем все кнопки кроме крестика закрытия в тайтл-баре
          element.find(':button:not(.ui-dialog-titlebar-close)').button('disable');
          //var form = element.find("form");
          //if(form) {
          //  form.submit();
          //} else {
          //  $(this).dialog("close");
          //
          //  d.resolve(true);
          //
          //  //return true;
          //}
          $(this).dialog("close");

          d.resolve(true);
        }
      },
      cancel: {
        text: language.sys_cancel,
        click: function () {
          $(this).dialog("close");
          d.resolve(false);

          //return false;
        }
      }
    }
  });

  d.promise().then(function (result) {
    if(result) {
      if (that.attr('href')) {
        sn_redirect(that.attr('href'));
      }

      if (that.prop('tagName') == 'FORM') {
        that.prop('submitted', true).submit();
      }

      if(params.hasOwnProperty('confirm')) {
        params.confirm();
      }
    } else {
      that.removeClass('button_pseudo_pressed');
    }

    // !result ? that.removeClass('button_pseudo_pressed') : false;
    //
    // if (result && that.attr('href')) {
    //   sn_redirect(that.attr('href'));
    // }
    //
    // if (result && that.prop('tagName') == 'FORM') {
    //   that.prop('submitted', true).submit();
    // }
    //
    // if(result && params.hasOwnProperty('confirm')) {
    //   params.confirm();
    // }
  });

  return false;
}

/**
 * Calculates CSS class based on (value / maximum) expression - i.e. class for fill rate
 *
 * @param {number} value
 * @param {number} maximum
 * @returns {string}
 */
function numberCssClass(value, maximum) {
  var result;
  maximum = Math.floatVal(maximum);
  value = Math.floatVal(value);

  switch (true) {
    case maximum == 0 && value == 0:
      result = 'zero_number';
      break;
    case value > maximum:
      result = 'error';
      break;
    case value == maximum:
      result = 'warning';
      break;
    case maximum == 0:
      result = 'zero_number';
      break;

    case (percent = value / maximum) > 0.9:
      result = 'warning';
      break;
    case percent > 0.75:
      result = 'notice';
      break;
    case percent > 0.50:
      result = 'info';
      break;
    default:
      result = 'ok';
      break;
  }

  return result;
}

/**
 * For current value returns CSS-style from numberCssClass() with maximum value
 *
 * @param {number} maximum
 * @returns {string}
 */
Number.prototype.classByMaximum = function (maximum) {
  return numberCssClass(this, maximum);
};

/**
 * For current value returns <span> styled with numberCssClass()
 *
 * @param {number} maximum
 * @param {boolean} format
 * @returns {string}
 */
Number.prototype.spanByMaximum = function (maximum, format) {
  var output = format ? sn_format_number(this) : this;
  return "<span class=\"" + this.classByMaximum(maximum) + "\">" + output + "</span>";
};

/**
 * For maximum value returns <span> styled with numberCssClass()
 *
 * @param {number} value
 * @param {boolean} format
 * @returns {string}
 */
Number.prototype.spanByValue = function (value, format) {
  var output = format ? sn_format_number(this) : this;
  return "<span class=\"" + numberCssClass(value, this) + "\">" + output + "</span>";
};

var navbarResources = {};
var PLAYER_OPTION_NAVBAR_PLANET_VERTICAL = 0;
$(document).ready(function () {
  var tooltipPosition = { my: "left top+15", at: "left bottom", collision: "flipfit" };
  if(PLAYER_OPTION_NAVBAR_PLANET_VERTICAL) {
    tooltipPosition = { my: "right-10 top", at: "left top", collision: "flipfit" }
  }

  $("[data-resource]").tooltip({
    items: "[data-resource]",
    position: tooltipPosition,

//      disabled: true,
//      close: function( event, ui ) {
//        $(this).tooltip('disable');
//        /* instead of $(this) you could also use $(event.target) */
//      },
    "content": function () {
      var
        result = '',
        that = $(this),
        resourceName = that.attr("data-resource"),
        resourceData;

      if (that.is("[data-resource]") && resourceName && (resourceData = navbarResources[resourceName])) {
        var resourceNameText, currentValue, fullness,
          storage = Math.intVal(resourceData.max_value);
        if (resourceData['resourceNameLongId']) {
          resourceNameText = language[resourceData['resourceNameLongId']];
        }
        if (!resourceNameText) {
          resourceNameText = resourceName;
        }

        if (timer = timerById("top_" + resourceName)) {
          currentValue = timer['current'];
        } else {
          currentValue = resourceData.start_value;
        }
        currentValue = Math.floatVal(currentValue);

        if(resourceName == "energy") {
          result = $("#navbar_resource_flex_tooltip_pattern_energy");
          currentValue = Math.roundVal(resourceData.used_value);
        } else {
          result = $("#navbar_resource_flex_tooltip_pattern");
        }

        fullness = storage ? Math.roundVal(currentValue / storage * 100).spanByMaximum(100, true) : '---';

        result = result.html().format(
          resourceNameText,
          currentValue.spanByMaximum(storage, true),
          storage.spanByValue(currentValue, true),
          fullness
        );
      }

      return result;
    }
  });

//    $(".navbar_resources_flex_resource > :nth-child(2)").each(function () {
//      $(this).progressbar({
//        value: 90,
//        max: 100
////      change: function() {
////        progressLabel.text( progressbar.progressbar( "value" ) + "%" );
////      },
////      complete: function() {
////        progressLabel.text( "Complete!" );
////      }
//      })
//    });


});
$(document).on("click", "[data-resource]", function () {
  var that = $(this);

  if ($(".ui-tooltip").length) {
    that.tooltip("close");
  } else {
    that.tooltip("open");
  }
});

$(document).on("click", "#universeScanStart,#universeScanStop", function () {
  $('#universe_scan_mode').val($('#universe_scan_mode').val() ? 0 : 1);
  $('#galaxy_form').submit();
});

var NAVBAR_MODE = 0;

function changePlanet(obj) {
  var pathAdd = 'cp=' + obj.options[obj.selectedIndex].value + (NAVBAR_MODE ? '&mode=' + NAVBAR_MODE : '');
  var regexp=/\?page\=(.*)/i;
  var parsed = regexp.exec(window.location.href);
  if(parsed) {
    pathAdd = parsed[0].replace(/\&cp=\d*/i, '').replace(/\&mode=\d*/i, '') + '&' + pathAdd;
  } else {
    pathAdd = '?' + pathAdd;
  }
  window.location.href = window.location.pathname + pathAdd;
}

$(document).ready(
  function() {
    $('body').append($('#benchmark').detach());
  }
);

$(document).on('change', '#filterQuestStatus', function () {
  var that = $(this);

  jQuery.get(
    SN_ROOT_VIRTUAL + "index.php?page=ajax&mode=quest&action=saveFilter&filterQuestStatus=" + that.val(),
    function(data) {
      sn_reload();
    },
    "json"
  );
});

function canIUseWebp() {
  var elem = document.createElement('canvas');
  ctx = elem.getContext('2d');
  ctx.fillStyle = "red";
  ctx.fillRect(0, 0, 8, 8);

  // console.log(elem);
  // console.log(elem.getContext);
  // console.log(elem.getContext('2d'));
  // console.log(elem.toDataURL('image/webp'));

  if (!!(elem.getContext && elem.getContext('2d'))) {
    // was able or not to get WebP representation
    return elem.toDataURL('image/webp').indexOf('data:image/webp') == 0;
  }

  // very old browser like IE 8, canvas not supported
  return false;
}

var hasWebP = (function() {
  var images = {
    basic: "data:image/webp;base64,UklGRjIAAABXRUJQVlA4ICYAAACyAgCdASoCAAEALmk0mk0iIiIiIgBoSygABc6zbAAA/v56QAAAAA==",
    lossless: "data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAQAAAAfQ//73v/+BiOh/AAA="
  };

  return function(feature) {
    var deferred = $.Deferred();

    $("<img>").on("load", function() {
      if(this.width === 2 && this.height === 1) {
        deferred.resolve();
      } else {
        deferred.reject();
      }
    }).on("error", function() {
      deferred.reject();
    }).attr("src", images[feature || "basic"]);

    return deferred.promise();
  }
})();
