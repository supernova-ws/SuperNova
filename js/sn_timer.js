/*

SuperNova JavaScript timer system

 1.6 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
   [+] - implemented event list

 1.5 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
   [+] - implemented date&time with delta

 1.4 - copyright (c) 2010-2011 by Gorlum for http://supernova.ws
   [+] - implemented pictured que

 1.3 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [+] - implemented time-independent counter that works correctly even on browser's "back"
   [~] - simple counter now uses objects instead of arrays

 1.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [~] - changed sn_timer from array to objects

 1.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [~] - optimization: now HTML elements for timers is caching after first tick and didn't search every tick
         This should rise perfomance a bit

 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [!] - initial release

Object structure:
  'id'          - timer ID (name)
  'type'        - timer type:
                    0 - que display;
                    1 - counter;
                    2 - date&time;
                    3 - pictured que;
                    4 - date&time with delta
                    5 - event list
  'active'      - is timer active?
  'start_time'  - start time
  'options'     - timer options
  'html_main'   - reserved for internal use (link to main HTML element)
  'html_timer'  - reserved for internal use (link to 'timer' HTML element)
  'html_finish' - reserved for internal use (link to 'finish' HTML element)
  'html_que'    - reserved for internal use (link to 'que' HTML element)
  'html_total'  - reserved for internal use (link to 'total time' HTML element)

Options for que display:
  'template'  - template for que item
  'msg_done'  - inactive message
  'que'       - que: array
                [0] - element ID
                [1] - element name
                [2] - build length
                [3] - element amount
                [4] - element level
  'total'     - reserved for internal use (total que building time)

Options for counter:
  'start_value' - start value
  'per_second'  - delta value per second
  'max_value'   - max value

Options for date&time:
  bit 1 - date
  bit 2 - time
  It means: 1 - just date; 2 - just time; 3 - both date & time

Options for date&time with delta:
  'format' - bit 1 - date
             bit 2 - time
             It means: 1 - just date; 2 - just time; 3 - both date & time
  'delta'  - seconds to add or substract

Options for event list:
  'msg_done'  - inactive message
  'que'       - que: array
                [0] - time left for this event
                [1] - string for this event
                [2] - hint for this event
  'unchanged' - reserved for internal use (flag that event text was not changed)
*/

var UNIT_ID        = 0;
var UNIT_NAME      = 1;
var UNIT_TIME      = 2;
var UNIT_AMOUNT    = 3;
var UNIT_LEVEL     = 4;
var UNIT_TIME_FULL = 5;

var EVENT_TIME   = 0;
var EVENT_STRING = 1;
var EVENT_HINT   = 2;

var sn_timers = new Array();

function sn_timer_compile_que(timer_options)
{
  var compiled = '';
  var unit_name = '';
  var temp = '';
  var total = 0;
  var que = timer_options['que'];

  for(que_id in que)
  {
//    if(que_id != 0)
    {
      total += (que[que_id][UNIT_AMOUNT] - (que_id != 0 ? 0 : 1)) * que[que_id][UNIT_TIME_FULL]; // que[que_id][UNIT_TIME] +
    }

    temp = timer_options['template'].replace('[UNIT_ID]', que[que_id][UNIT_ID]);
    temp = temp.replace('[UNIT_TIME]', sn_timestampToString(que[que_id][UNIT_TIME]));

    unit_name = que[que_id][UNIT_NAME];
    if(que[que_id][UNIT_LEVEL] >= 0)
    {
      unit_name += ' (' + que[que_id][UNIT_LEVEL] + ')';
      temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_LEVEL]);
    }
    else
    {
      unit_name += ' (' + que[que_id][UNIT_AMOUNT] + ')';
      temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_AMOUNT]);
    }
    temp = temp.replace('[UNIT_NAME]', unit_name);
    compiled += temp;
  }
  timer_options['total'] = total;

  return compiled;
};

function sn_timer()
{
  var HTML, HTML_timer, HTML_finish;

  var local_time = new Date();
  var time_now = new Date(local_time.valueOf() - timeDiff * 1000);
// alert(local_time + '\r\n' + time_now);
  var timestamp = Math.round(time_now.valueOf() / 1000);

  var activeTimers = 0;

  for(timerID in sn_timers)
  {
    if(!sn_timers[timerID]['active'])
    {
      continue;
    }

    timer = sn_timers[timerID];
    if(!timer['html_main'])
    {
      sn_timers[timerID]['html_main']   = document.getElementById(timer['id']);
      sn_timers[timerID]['html_timer']  = document.getElementById(timer['id'] + '_timer');
      sn_timers[timerID]['html_finish'] = document.getElementById(timer['id'] + '_finish');
      sn_timers[timerID]['html_que']    = document.getElementById(timer['id'] + '_que');
      sn_timers[timerID]['html_total']  = document.getElementById(timer['id'] + '_total');
      timer = sn_timers[timerID];
    }
    HTML        = timer['html_main'];
    HTML_timer  = timer['html_timer'];
    HTML_finish = timer['html_finish'];
    HTML_que    = timer['html_que'];
    HTML_total  = timer['html_total'];

    timer_options = timer['options'];
    switch(timer['type'])
    {
      case 0: // old que display
        var que_item = timer_options['que'][0];

        if(que_item[UNIT_TIME] <= timestamp - timer['start_time'])
        {
          que_item[UNIT_AMOUNT]--;
          if(que_item[UNIT_AMOUNT] <= 0)
          {
            timer_options['que'].shift();
            que_item = timer_options['que'][0];
          }
          timer['start_time'] = timestamp;
        }

        if(timer_options['que'].length && que_item[UNIT_ID])
        {
          timeFinish = parseInt(timer['start_time']) + parseInt(que_item[UNIT_TIME]);
          timeLeft = parseInt(timer['start_time']) + parseInt(que_item[UNIT_TIME]) - timestamp;
          infoText = que_item[UNIT_NAME];
          if(que_item[UNIT_AMOUNT] > 1)
          {
            infoText += ' (' + que_item[UNIT_AMOUNT] + ')';
          }
          timerText = sn_timestampToString(timeLeft);
        }
        else
        {
          timer['active'] = false;
          infoText = timer_options['msg_done'];
          timerText = '';
        }

        if(HTML_timer != null)
        {
          HTML_timer.innerHTML = timerText;
        }
        else
        {
          if(infoText != '' && timerText)
          {
            infoText += '<br>';
          }
          infoText += timerText;
        }

        if(HTML_finish != null)
        {
          HTML_finish.innerHTML = timeFinish;
        }

        if(HTML != null)
        {
          HTML.innerHTML = infoText;
        }
      break;

      case 1: // time-independent counter
        var new_value = parseInt(timer_options['start_value']) + (timestamp - parseInt(timer['start_time'])) * parseFloat(timer_options['per_second']);
        if(timer_options['round'] === undefined)
        {
          timer_options['round'] = 0;
        }
        if(new_value < 0)
        {
          new_value = 0;
          timer['active'] = false;
        }
        printData = sn_format_number(new_value, timer_options['round'], 'positive', timer_options['max_value']);
        if((new_value >= timer_options['max_value'] && timer_options['per_second'] > 0) || (timer_options['per_second'] == 0))
        {
          timer['active'] = false;
        };

        if(HTML != null)
        {
          HTML.innerHTML = printData;
        }
        else
        {
          timer['active'] = false;
        }
      break;
// TODO: Merge case 2 and case 4 together
      case 2: // date&time
        printData = '';

        if(timer['options'] & 1)
        {
          printData += local_time.toLocaleDateString();
        }

        if(timer['options'] & 3)
        {
          printData += '&nbsp;';
        }

        if(timer['options'] & 2)
        {
          printData += local_time.toTimeString().substring(0,8);
        }

        if(HTML != null)
        {
          HTML.innerHTML = printData;
        }
        else
        {
          timer['active'] = false;
        }
      break;

      case 4: // date&time with delta
        printData = '';

        var local_time_plus = new Date();
        local_time_plus.setTime(local_time.valueOf() + (timer['options']['delta'] * 1000));

        if(timer['options']['format'] & 1)
        {
          printData += local_time_plus.toLocaleDateString();
        }

        if(timer['options']['format'] & 3)
        {
          printData += '&nbsp;';
        }

        if(timer['options']['format'] & 2)
        {
          printData += local_time_plus.toTimeString().substring(0,8);
        }

        if(HTML != null)
        {
          HTML.innerHTML = printData;
        }
        else
        {
          timer['active'] = false;
        }
      break;

      case 3: // new que display
        if(timer_options['que'].length == 0)
        {
          timer['active'] = false;
          if(timer_options['url'] != undefined)
          {
            document.location = timer_options['url'];
          }
          break;
        }
        var que_item = timer_options['que'][0];
        var que_compiled = '';

        if(!timer['que_compiled'] || timer['que_compiled'] != '')
        {
          sn_timers[timerID]['que_compiled'] = sn_timer_compile_que(timer_options);
//          HTML_que.innerHTML = sn_timers[timerID]['que_compiled'];
        }
        que_compiled = sn_timers[timerID]['que_compiled'];

        if(que_item[UNIT_TIME] <= timestamp - timer['start_time'])
        {
          que_item[UNIT_AMOUNT]--;
          if(que_item[UNIT_AMOUNT] <= 0)
          {
            timer_options['que'].shift();
            que_item = timer_options['que'][0];
          }
          else
          {
            que_item[UNIT_TIME] = que_item[UNIT_TIME_FULL];
          }
          timer['start_time'] = timestamp;
          sn_timers[timerID]['que_compiled'] = sn_timer_compile_que(timer_options);
//          HTML_que.innerHTML = sn_timers[timerID]['que_compiled'];
          que_compiled = sn_timers[timerID]['que_compiled'];
        }

        var should_break = true;
        if(que_item != undefined)
        {
          if(que_item[UNIT_ID])
          {
            should_break = false;
          }
        }

        if(timer_options['que'].length && !should_break)
        {
          timeFinish = timer['start_time'] + que_item[UNIT_TIME];
          timeLeft = timer['start_time'] + que_item[UNIT_TIME] - timestamp;
          total_text = sn_timestampToString(timeLeft + timer_options['total']);
          infoText = que_item[UNIT_NAME];
          if(que_item[UNIT_AMOUNT] > 1)
          {
            infoText += ' (' + que_item[UNIT_AMOUNT] + ')';
          }
          else
            if(que_item[UNIT_LEVEL] > 1)
            {
              infoText += ' (' + que_item[UNIT_LEVEL] + ')';
            }
          timerText = sn_timestampToString(timeLeft);
        }
        else
        {
          timer['active'] = false;
          if(timer_options['url'] != undefined)
          {
            document.location = timer_options['url'];
          }
          infoText = timer_options['msg_done'];
          timerText = '';
          total_text = '00:00:00';
        }

        if(HTML_total != null)
        {
          HTML_total.innerHTML = total_text;
        }
        else
        {
          timerText += '<br>' + total_text;
        }

        if(HTML_timer != null)
        {
          HTML_timer.innerHTML = timerText;
        }
        else
        {
          if(infoText != '' && timerText)
          {
            infoText += '<br>';
          }
          infoText += timerText;
        }

        if(HTML_que != null)
        {
          HTML_que.innerHTML = sn_timers[timerID]['que_compiled'];
        }
        else
        {
          infoText += sn_timers[timerID]['que_compiled'];
        }

        if(HTML_finish != null)
        {
          HTML_finish.innerHTML = timeFinish;
        }

        if(HTML != null)
        {
          HTML.innerHTML = infoText;
        }

      break;

      case 5: // old que display
        var que_item = timer_options['que'][0];
        var finalText = '';

        if(que_item[EVENT_TIME] <= timestamp - timer['start_time'])
        {
          timer_options['que'].shift();
//          timer['start_time'] = timestamp;
          timer['options']['unchanged'] = false;
        }

        if(!timer['options']['unchanged'])
        {
          infoText = que_item[EVENT_STRING];
          hintText = que_item[EVENT_HINT];
        }

        if(!timer_options['que'].length)
        {
          timer['active'] = false;
          infoText  = timer_options['msg_done'];
          hintText  = '';
          timer['options']['unchanged'] = false;
        }

        if(!timer['options']['unchanged'])
        {
          timer['options']['unchanged'] = true;

          if(HTML != null)
          {
            HTML.innerHTML = infoText;
          }

          if(HTML_total != null)
          {
            HTML_total.title = hintText;
          }
          else
          {
            HTML.title = hintText;
          }
        }

      break;

    }

    activeTimers++;
  }

  if(activeTimers)
  {
    window.setTimeout("sn_timer();", 1000);
  }
}
