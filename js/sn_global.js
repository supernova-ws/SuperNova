function sn_format_number(number, laenge, color)
{
  number = Math.round( number * Math.pow(10, laenge) ) / Math.pow(10, laenge);
  str_number = number+'';
  arr_int = str_number.split('.');
  if(!arr_int[0]) arr_int[0] = '0';
  if(!arr_int[1]) arr_int[1] = '';
  if(arr_int[1].length < laenge){
    nachkomma = arr_int[1];
    for(i=arr_int[1].length+1; i <= laenge; i++){  nachkomma += '0';  }
    arr_int[1] = nachkomma;
  }
  if(arr_int[0].length > 3){
    Begriff = arr_int[0];
    arr_int[0] = '';
    for(j = 3; j < Begriff.length ; j+=3){
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

  if(useDays){
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

function sn_ainput_make(field_name, min_value, max_value, div_width)
{
  var field_name_orig = field_name;

  field_name = field_name.replace('[', '');
  field_name = field_name.replace(']', '');

  var slider_id = "#" + field_name + 'slide';

  if(!min_value)
  {
    min_value = 0;
  }

  document.write('<div width="' + div_width + '">');
  document.write('<div><input type="text"   width="100%" id="' + field_name + '" value="0" style="margin: 2px;" name="' + field_name_orig + '" onfocus="javascript:if(this.value == \'0\') this.value=\'\';" onblur="javascript:if(this.value == \'\') this.value=\'0\';"/></div>');
  document.write('<div style="margin: 6px;" id="' + field_name + 'slide"></div>');
  document.write('</div>');

  jQuery(function() {
    jQuery(slider_id).slider({
      range: "min",
      value: min_value,
      min: min_value,
      max: max_value,
      slide: function(event, ui) {
        jQuery("#" + field_name).val(ui.value);
        jQuery("#" + field_name).change();
      }
    });
    jQuery("#" + field_name).val(jQuery(slider_id).slider("value"));
  });

  jQuery("#" + field_name).bind('keyup change',
    function(event, ui)
    {
      if(jQuery(this).val() > jQuery(slider_id).slider("option", "max"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "max"));
      }

      if(jQuery(this).val() < jQuery(slider_id).slider("option", "min"))
      {
        jQuery(this).val(jQuery(slider_id).slider("option", "min"));
      }

      jQuery(slider_id).slider("value", jQuery(this).val());
      //document.getElementById('resource0temp').innerHTML = field_name + '!' + jQuery(this).val() + '?' + jQuery(slider_id).slider("option", "max");
      //Math.min(jQuery('#resource' + i + 'slide').slider("value") + transportCapacity, resource_max[i])
    }
  );
}

var element_cache = new Object();

function calc_elements()
{
  var all_elements = document.getElementsByTagName('*');

  for(element in all_elements)
  {
    if(all_elements[element].id != undefined)
    {
      element_cache[all_elements[element].id] = all_elements[element];
    }
  }
}
