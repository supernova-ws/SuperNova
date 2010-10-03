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

function sn_format_number(number, precission, color)
{
  if(!precission)
  {
    precission = 0;
  }
  number = Math.round( number * Math.pow(10, precission) ) / Math.pow(10, precission);
  str_number = number+'';
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
  if(color)
  {
    if(number<0)
    {
      ret_val = '<font color="red">' + ret_val + '</font>';
    }
    else
    {
      ret_val = '<font color="' + color + '">' + ret_val + '</font>';
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

  document.write('<div>');
  document.write('<div style="width: 100%">');
  document.write('<input type="button" value="-" id="' + field_name + 'dec" style="width: 20;">');
  document.write('<input type="text"   value="0" id="' + field_name + '"    style="margin: 2px; width: ' + div_width + ';" name="' + field_name_orig + '" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'\') this.value=\'0\';"/>');
  document.write('<input type="button" value="+" id="' + field_name + 'inc" style="width: 20;">');
  document.write('</div>');
  if(div_width != 'auto')
  {
    div_width += 20 + 20 + 6 + 6 + 2 + 2;
  }
  document.write('<div style="margin: 6px; width: ' + div_width + '" id="' + field_name + 'slide"></div>');
  document.write('</div>');

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

      if(jQuery(this).val() > jQuery(slider_id).slider("option", "max"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "max"));
      }

      if(jQuery(this).val() < jQuery(slider_id).slider("option", "min"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "min"));
      }

      jQuery(slider_id).slider("value", jQuery(this).val());
    }
  );

  jQuery("#" + field_name + 'dec').bind('click',
    function(event, ui)
    {
      var element = jQuery("#" + field_name);
      if(parseInt(element.val()) > step_value)
      {
        element.val(parseInt(element.val()) - step_value);
      }
      else
      {
        element.val(0);
      };
      element.trigger('change', [event, ui]);
    }
  );

  jQuery("#" + field_name + 'inc').bind('click',
    function(event, ui)
    {
      var element = jQuery("#" + field_name);
      if(parseInt(element.val()) + step_value < jQuery(slider_id).slider("option", "max"))
      {
        element.val(parseInt(element.val()) + step_value);
      }
      else
      {
        element.val(jQuery(slider_id).slider("option", "max"));
      };
      element.trigger('change', [event, ui]);
    }
  );
}

var popup = jQuery(document.createElement("span"));
popup.dialog({ autoOpen: false }); // , width: auto, resizable: false
popup.mouseleave(function()
{
  popup.dialog("close");
});

function popup_show(html, width)
{
  popup_hide();
  if(width)
  {
    popup.dialog("option", "width", width);
  }
  popup.dialog("option", "position", [clientX, clientY]); // + 20
  popup.html(html);
  popup.dialog("open");
}

function popup_hide()
{
  popup.dialog("close");
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
