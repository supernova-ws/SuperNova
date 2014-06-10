var language = {};

var x = "";
var e = null;

function cntchar(m) {
  if(window.document.forms[0].text.value.length > m) {
    window.document.forms[0].text.value = x;
  } else {
    x = window.document.forms[0].text.value;
  }
  if(e == null)
  e = document.getElementById('cntChars');
  else
  e.childNodes[0].data = window.document.forms[0].text.value.length;
}

function sn_format_number(number, precission, style, max)
{
  if(!precission)
  {
    precission = 0;
  }

  if(!max)
  {
    max = 0;
  }

  number = Math.round( number * Math.pow(10, precission) ) / Math.pow(10, precission);
  if(number > 0)
  {
    str_number = number+'';
  }
  else
  {
    str_number = (-number)+'';
  }
  arr_int = str_number.split('.');
  if(!arr_int[0]) arr_int[0] = '0';
  if(!arr_int[1]) arr_int[1] = '';
  if(arr_int[1].length < precission)
  {
    nachkomma = arr_int[1];
    for(i=arr_int[1].length+1; i <= precission; i++)
    {
      nachkomma += '0';
    }
    arr_int[1] = nachkomma;
  }

  if(arr_int[0].length > 3)
  {
    Begriff = arr_int[0];
    arr_int[0] = '';
    for(j = 3; j < Begriff.length ; j+=3)
    {
      Extrakt = Begriff.slice(Begriff.length - j, Begriff.length - j + 3);
      arr_int[0] = '.' + Extrakt +  arr_int[0] + '';
    }
    str_first = Begriff.substr(0, (Begriff.length % 3 == 0)?3:(Begriff.length % 3));
    arr_int[0] = str_first + arr_int[0];
  }

  ret_val = arr_int[0] + (arr_int[1] ? ','+arr_int[1] : '');
  if(number < 0)
  {
    ret_val = '-' + ret_val;
  }

  if(style)
  {
    if(number == Math.abs(max))
    {
      ret_val = '<span class="neutral">' + ret_val + '</span>';
    }
    else
      if((max > 0 && -number < -max) || (!max && number < 0) || (max < 0 && number < -max))
      {
        ret_val = '<span class="negative">' + ret_val + '</span>';
      }
      else
      {
        ret_val = '<span class="' + style + '">' + ret_val + '</span>';
      }
  }

  return ret_val;
}

function sn_timestampToString(timestamp, useDays){
  strTime = '';

  if(useDays)
  {
    tmp = Math.floor( timestamp / (60*60*24));
    timestamp -= tmp * 60*60*24;
    strTime += (tmp>0 ? tmp + 'd ' : '');
  }

  tmp = Math.floor( timestamp / (60*60));
  timestamp -= tmp * 60*60;
  strTime += (tmp<=9 ? '0' + tmp : tmp) + ':';

  tmp = Math.floor( timestamp / 60);
  timestamp -= tmp * 60;
  strTime += (tmp<=9 ? '0' + tmp : tmp) + ':';

  strTime += (timestamp<=9 ? '0' + timestamp : timestamp);

  return strTime;
}

var accelerated;

function sn_ainput_make(field_name, options)
{
  var min_value = options['min'] ? options['min'] : 0;
  var max_value = options['max'] ? options['max'] : 0;
  var step_value = options['step'] ? options['step'] : 1;
  var start_value = options['value'] ? options['value'] : min_value;
  var div_width = options['width'] ? options['width'] : 'auto';

  var field_name_orig = field_name;

  field_name = field_name.replace('[', '');
  field_name = field_name.replace(']', '');

  var slider_id = "#" + field_name + 'slide';

  // top left
  document.write('<div style="width: ' + div_width + ';">'); // main container - sets width

  document.write('<div style="width: auto">');
  if(options['button_zero'])
  {
    document.write('<input type="button" value="0" id="' + field_name + 'zero" style="width: 20px; float: left; margin-right: 3px;">');
  }
  document.write('<input type="button" value="-" id="' + field_name + 'dec" style="width: 20px; float: left; margin-right: 3px;">');// div_width
  document.write('<div style="width: auto; display: inline-block;"><input type="text" value="0" id="' + field_name + '" style="width: ' + '100%' + ';" name="' +
    field_name_orig + '" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'\') this.value=\'0\';"/></div>');
  if(options['button_max'])
  {
    document.write('<input type="button" value="M" id="' + field_name + 'max" style="width: 20px; float: right; margin-left: 3px;">');
  }
  document.write('&nbsp;<input type="button" value="+" id="' + field_name + 'inc" style="width: 20px; float: right; margin-left: 3px;">');
  document.write('</div>');
  /*
  if(div_width != 'auto')
  {
    div_width += 20 + 20 + 6 + 6 + 2 + 2 + (options['button_zero'] ? 20 + 2 + 6: 0) + (options['button_max'] ? 20 + 2 + 6: 0);
  }
  */

  document.write('<div style="margin: 6px; width: auto" id="' + field_name + 'slide"></div>'); // slider container

  document.write('</div>'); // main container

  jQuery(function()
  {
    jQuery(slider_id).slider(
    {
      range: "min",
      value: start_value,
      min: min_value,
      max: max_value,
      step: 1,
      slide: function(event, ui)
      {
        jQuery("#" + field_name).val(ui.value);
        jQuery("#" + field_name).trigger('change', [event, ui]);
      },
      change: function(event, ui)
      {
        jQuery("#" + field_name).val(ui.value);
        jQuery("#" + field_name).trigger('change', [event, ui]);
      }
    });
    jQuery("#" + field_name).val(jQuery(slider_id).slider("value"));
  });

  jQuery("#" + field_name).bind('keyup change',
    function(event, ui)
    {
      if(ui != undefined)
      {
        if(ui.type == 'slidechange')
        {
          return;
        }
      }

      value = parseInt(jQuery(this).val());
      slider = jQuery(slider_id);

// console.log(Number.MAX_VALUE);
// console.log(parseInt(jQuery(slider_id).slider("option", "max")));
// console.log(value > parseInt(jQuery(slider_id).slider("option", "max")) ? 1 : 0);
      if(value > parseInt(slider.slider("option", "max")))
      {
        jQuery(this).val(slider.slider("option", "max"));
      }

      if(value < parseInt(slider.slider("option", "min")))
      {
        jQuery(this).val(slider.slider("option", "min"));
      }

      slider.slider("value", value);
    }
  );

  jQuery("#" + field_name + 'zero').bind('click', function(event, ui) {
    jQuery("#" + field_name).val(0).trigger('change', [event, ui]);
  });

  jQuery("#" + field_name + 'max').bind('click', function(event, ui) {
    jQuery("#" + field_name).val(jQuery(slider_id).slider("option", "max")).trigger('change', [event, ui]);
  });

  jQuery("#" + field_name + 'dec, ' + "#" + field_name + 'inc').bind('mousedown', function(event, ui) {
    var element = jQuery("#" + field_name);
    accelerated = {'element': element, 'step': step_value, 'slider': slider_id, 'ticks': 0, 'step_now': step_value, 'timeout': 0, 'increase': $(this).attr('id') == field_name + 'inc'};
    // accelerated['timeout'] = window.setTimeout(sn_ainput_mouselerate, 20);
    sn_ainput_mouselerate();
  });
  jQuery("#" + field_name + 'dec, ' + "#" + field_name + 'inc').bind('mouseup', function(event, ui) {
    if(accelerated)
    {
      clearTimeout(accelerated['timeout']);
      accelerated = undefined;
    }
  });
}

function sn_ainput_mouselerate()
{
  var donext = false;
  if(accelerated['increase'] && (val = parseInt(accelerated['element'].val())) < (option_max = jQuery(accelerated['slider']).slider("option", "max")))
  {
    donext = val + accelerated['step_now'] < option_max;
    accelerated['element'].val(donext ? val + accelerated['step_now'] : option_max).trigger('change'); // , [event, ui]
  }
  if(!accelerated['increase'] && (val = parseInt(accelerated['element'].val())) > 0)
  {
    donext = val - accelerated['step_now'] > 0;
    accelerated['element'].val(donext ? val - accelerated['step_now'] : 0).trigger('change'); // , [event, ui]
  }

  if(donext)
  {
    accelerated['ticks']++;
    if(accelerated['ticks'] % 3 == 0)
    {
      accelerated['step_now'] = accelerated['step_now'] * 2;
    }
    accelerated['timeout'] = window.setTimeout(sn_ainput_mouselerate, 200);
  }
}

var popup = jQuery(document.createElement("span"));
popup.dialog({ autoOpen: false }); // , width: auto, resizable: false
popup.mouseleave(function()
{
  popup.dialog("close");
});

function popup_show(html, width, aClientX, aClientY)
{
  popup_hide();
  if(width)
  {
    popup.dialog("option", "width", width);
  }
  popup.dialog("option", "position", [aClientX ? aClientX : clientX, aClientY ? aClientY : clientY]); // + 20
  popup.html(html);
  popup.dialog("open");
}

function popup_hide()
{
  popup.dialog("close");
}

// Helper probe to use CSS-values in JS
function sn_probe_style(element, css_attribute)
{
  switch(css_attribute)
  {
    case 'border-top-color':
      if(element.currentStyle)
        return element.currentStyle.borderTopColor;
      if(document.defaultView)
        return document.defaultView.getComputedStyle(element, '').getPropertyValue('border-top-color');
    break;

  }

  return false;
}

var element_cache = new Object();

function calc_elements()
{
  if(element_cache['_IS_INIT'])
  {
    return;
  }

  var all_elements = document.getElementsByTagName('*');

  for(element in all_elements)
  {
    if(all_elements[element].id != undefined)
    {
      element_cache[all_elements[element].id] = all_elements[element];
    }
  }
  element_cache['_IS_INIT'] = true;
}

var mouseX, mouseY;
var clientX, clientY;

jQuery(document).mousemove(function(e){
   mouseX = e.pageX;
   mouseY = e.pageY;

   clientX = e.clientX;
   clientY = e.clientY;
});

jQuery(document).ready(calc_elements);

function sn_show_hide(element, element_name)
{
  var element_to_hide = jQuery("#" + element_name);
  var tag_name = element_to_hide[0].tagName;

  element_to_hide.css('display', element_to_hide.css('display') == 'none' ? (tag_name == 'TR' ? 'table-row' : (tag_name == 'UL' || tag_name == 'DIV' ? 'block' : 'inline')) : 'none');
//  jQuery(element).html("[&nbsp;" + (element_to_hide.css('display') == 'none' ? "{LA_sys_show}" : "{LA_sys_hide}") + "&nbsp;]");
  jQuery(element).html("[&nbsp;" + (element_to_hide.css('display') == 'none' ? LA_sys_show : LA_sys_hide) + "&nbsp;]");
}


jQuery(document).on('click', "[go_overview]", function(){
  document.location = 'overview.php?cp=' + jQuery(this).attr('go_overview') + (jQuery(this).attr('mode') ? '&mode=' + jQuery(this).attr('mode'): '');
});

jQuery(document).on('click', "[go_fleet]", function(){
  document.location = 'fleet.php?cp=' + jQuery(this).attr('go_fleet') + (jQuery(this).attr('mode') ? '&fleet_page=' + jQuery(this).attr('mode'): '');
});
