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

function sn_format_number(number, precission, style, max, plus_sign)
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
  } else if (number > 0 && plus_sign) {
    ret_val = '+' + ret_val;
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
  var col_span = 3;

  var field_name_orig = field_name;

  field_name = field_name.replace('[', '');
  field_name = field_name.replace(']', '');

  var slider_id = "#" + field_name + 'slide';

  // top left

  document.write('<table width="auto" class="markup">'); // main container - sets width

  document.write('<tr>');
  if(options['button_zero'])
  {
    document.write('<td width="6"><input type="button" value="0" id="' + field_name + 'zero" style="width: 6px"></td>');
    jQuery('#' + field_name + 'zero').button();
    col_span++;
  }

  document.write('<td><input type="button" value="-" id="' + field_name + 'dec" style="width: 6px"></td>');
  jQuery('#' + field_name + 'dec').button();

  document.write('<td><input type="text" value="0" id="' + field_name + '" style="width: ' + 'auto' + ';" name="' + field_name_orig + '" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'\') this.value=\'0\';"/></td>');

  $('#' + field_name).button().addClass('ui-textfield');

  document.write('<td width="6"><input type="button" value="+" id="' + field_name + 'inc" style="width: 6px"></td>');
  if(options['button_max'])
  {
    document.write('<td width="6"><input type="button" value="M" id="' + field_name + 'max" style="width: 6px"></td>');
    jQuery('#' + field_name + 'max').button();
    col_span++;
  }
  jQuery('#' + field_name + 'inc').button();
  document.write('</tr>');

  document.write('<tr><td colspan="' + col_span + '"><div style="margin: 6px; width: auto" id="' + field_name + 'slide"></div></td></tr>'); // slider container

  document.write('</table>'); // main container

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
    function(event, ui) {
      if(ui != undefined) {
        if(ui.type == 'slidechange') {
          return;
        }
      }

      value = parseInt(jQuery(this).val());
      value = value ? value : 0;
      slider = jQuery(slider_id);

      if(value > parseInt(slider.slider("option", "max"))) {
        jQuery(this).val(slider.slider("option", "max"));
      }

      if(value < parseInt(slider.slider("option", "min"))) {
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

  jQuery("#" + field_name + 'dec, ' + "#" + field_name + 'inc')
    .bind('mousedown', function(event, ui) {
      var element = jQuery("#" + field_name);
      if(element.is('[disabled]')) {
        return;
      }
      accelerated = {'element': element, 'step': step_value, 'slider': slider_id, 'ticks': 0, 'step_now': step_value, 'timeout': 0, 'increase': $(this).attr('id') == field_name + 'inc'};
      sn_ainput_mouselerate();
    })
    .bind('mouseup', function(event, ui) {
      if(accelerated)
      {
        clearTimeout(accelerated['timeout']);
        accelerated = undefined;
      }
    }
  );
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
  jQuery(element).html("[&nbsp;" + (element_to_hide.css('display') == 'none' ? LA_sys_show : LA_sys_hide) + "&nbsp;]");
}


jQuery(document).on('click', "[go]", function(){
  planet_id = (planet_id = parseInt(jQuery(this).attr('planet_id'))) ? planet_id : parseInt(jQuery(this).parent().attr('planet_id'));
  unit_id = (unit_id = parseInt(jQuery(this).attr('unit_id'))) ? unit_id : parseInt(jQuery(this).parent().attr('unit_id'));
  mode = jQuery(this).attr('mode');
  switch(jQuery(this).attr('go'))
  {
    case 'info': page = 'infos'; break;
    // case 'galaxy': page = 'galaxy'; break;
    case 'flying': page = 'flying_fleets'; break;
    case 'fleet': page = 'fleet'; break;
    case 'build': page = 'buildings'; break;
    default: page = 'overview';
  }
  document.location = page + '.php?' + (planet_id ? 'cp=' + planet_id + (mode ? '&' : '') : '')
    + (mode ? 'mode=' + mode : '')
    + (unit_id ? 'gid=' + unit_id + (typeof ALLY_ID !== 'undefined' && parseInt(ALLY_ID) ? '&ally_id=' + ALLY_ID : ''): '')
  ;
});

jQuery(document).on('click', ".gather_resources", function(){
  that = $(this);
  document.location = 'fleet.php?fleet_page=5' + (typeof PLANET_ID !== 'undefined' && parseInt(PLANET_ID) ? '&cp=' + parseInt(PLANET_ID) : '')
    + (parseFloat(that.attr('metal')) ? '&metal=' + parseFloat(that.attr('metal')) : '')
    + (parseFloat(that.attr('crystal')) ? '&crystal=' + parseFloat(that.attr('crystal')) : '')
    + (parseFloat(that.attr('deuterium')) ? '&deuterium=' + parseFloat(that.attr('deuterium')) : '')
  ;

  // onclick="document.location='fleet.php?fleet_page=5&cp={planet.ID}&re=0&metal={production.METAL_REST_NUM}&crystal={production.CRYSTAL_REST_NUM}&deuterium={production.DEUTERIUM_REST_NUM}'"
});

/*
 jQuery(document).on('click', "[go_fleet]", function(){
 document.location = 'fleet.php?cp=' + jQuery(this).attr('go_fleet') + (jQuery(this).attr('mode') ? '&fleet_page=' + jQuery(this).attr('mode'): '');
 });

 jQuery(document).on('click', "[go_overview]", function(){
  planet_id = (planet_id = parseInt(jQuery(this).attr('planet_id'))) ? planet_id : parseInt(jQuery(this).parent().attr('planet_id'));
  document.location = 'overview.php?' + (planet_id ? 'cp=' + planet_id + '&' : '') + (jQuery(this).attr('go_overview') ? 'mode=' + jQuery(this).attr('go_overview'): '');
});

jQuery(document).on('click', "[go_build]", function(){
  planet_id = (planet_id = parseInt(jQuery(this).attr('planet_id'))) ? planet_id : parseInt(jQuery(this).parent().attr('planet_id'));
  document.location = 'buildings.php?' + (planet_id ? 'cp=' + planet_id + '&' : '') + (jQuery(this).attr('go_build') ? 'mode=' + jQuery(this).attr('go_build'): '');
});
jQuery(document).on('click', ".show_unit_info", function(){
  unit_id = (unit_id = parseInt(jQuery(this).attr('unit_id'))) ? unit_id : parseInt(jQuery(this).parent().attr('unit_id'));
  document.location = 'infos.php?gid=' + unit_id + (typeof ALLY_ID !== 'undefined' && parseInt(ALLY_ID) ? '&ally_id=' + ALLY_ID : '');
//  document.location = 'infos.php?gid=' + jQuery(this).parent().attr('unit_id') + (parseInt(ALLY_ID) ? '&ally_id=' + ALLY_ID : '');
});
 */
