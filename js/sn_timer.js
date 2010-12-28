/*

SuperNova JavaScript timer system

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
  'type'        - timer type: 0 - que display; 1 - counter; 2 - date&time
  'active'      - is timer active?
  'start_time'  - start time
  'options'     - timer options
  'html_main'   - reserved for internal use (link to main HTML element)
  'html_timer'  - reserved for internal use (link to 'timer' HTML element)
  'html_finish' - reserved for internal use (link to 'finish' HTML element)
  'html_que'    - reserved for internal use (link to 'que' HTML element)

Options for que display:
  'template' - template for que item
  'msg_done' - inactive message
  'que'      - que: array
          [0] - element ID
          [1] - element name
          [2] - build length
          [3] - element amount
          [4] - element level

Options for counter:
'start_value' - start value
'per_second'  - delta value per second
'max_value'   - max value

Options for date&time:
bit 1 - date
bit 2 - time
It means: 1 - just date; 2 - just time; 3 - both date & time

*/

var UNIT_ID       = 0;
var UNIT_NAME     = 1;
var UNIT_TIME     = 2;
var UNIT_AMOUNT   = 3;
var UNIT_LEVEL    = 4;

var sn_timers = new Array();

function sn_timer_compile_que(timer_options)
{
  var compiled = '';
  var unit_name = '';
  var temp = '';
  var que = timer_options['que'];

  for(que_id in que)
  {
    temp = timer_options['template'].replace('[UNIT_ID]', que[que_id][UNIT_ID]);
    temp = temp.replace('[UNIT_TIME]', sn_timestampToString(que[que_id][UNIT_TIME]));

    unit_name = que[que_id][UNIT_NAME];
    if(que[que_id][UNIT_AMOUNT] > 1)
    {
      unit_name += ' (' + que[que_id][UNIT_AMOUNT] + ')';
      temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_AMOUNT]);
    }
    if(que[que_id][UNIT_LEVEL] >= 0)
    {
      unit_name += ' (' + que[que_id][UNIT_LEVEL] + ')';
      temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_LEVEL]);
    }
    temp = temp.replace('[UNIT_NAME]', unit_name);
    compiled += temp;
  }

  return compiled;
};

function sn_timer() {
  var HTML, HTML_timer, HTML_finish;

  var local_time = new Date();
  var time_now = new Date().setTime(local_time.valueOf() + timeDiff);
  var timestamp = Math.round(time_now.valueOf() / 1000);

  var activeTimers = 0;

  for(timerID in sn_timers)
  {
    timer = sn_timers[timerID];
    if(!timer['active'])
    {
      continue;
    }
    timer_options = timer['options'];

    if(!timer['html_main'])
    {
      sn_timers[timerID]['html_main'] = document.getElementById(timer['id']);
      sn_timers[timerID]['html_timer'] = document.getElementById(timer['id'] + '_timer');
      sn_timers[timerID]['html_finish'] = document.getElementById(timer['id'] + '_finish');
      sn_timers[timerID]['html_que'] = document.getElementById(timer['id'] + '_que');
      timer = sn_timers[timerID];
    }
    HTML        = timer['html_main'];
    HTML_timer  = timer['html_timer'];
    HTML_finish = timer['html_finish'];
    HTML_que    = timer['html_que'];

    switch(timer['type'])
    {
      case 0: // new que display
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
          timeFinish = timer['start_time'] + que_item[UNIT_TIME];
          timeLeft = timer['start_time'] + que_item[UNIT_TIME] - timestamp;
          infoText = que_item[UNIT_NAME];
          if(que_item[UNIT_AMOUNT] > 0)
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
        var new_value = timer_options['start_value'] + (timestamp - timer['start_time']) * timer_options['per_second'];
        printData = sn_format_number(new_value, 2, 'white', timer_options['max_value']);
        if(new_value >= timer_options['max_value'])
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
/*
      case 1: // counter
        if(timer_options[0] >= timer_options[2]){
          timer['active'] = false;
          printData = '<font color=red>' + sn_format_number(timer_options[0], 2) + '</font>';
        }else{
          timer_options[0] += Math.floor(timer_options[1] * (timestamp - timer['start_time']) / 36) / 100
          printData = sn_format_number(timer_options[0], 2);
          timer['start_time'] = timestamp;
        };

        if(HTML != null)
          HTML.innerHTML = printData;
        else
          timer['active'] = false;
      break;
*/
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

      case 3: // new que display
        if(timer_options['que'].length == 0)
        {
          timer['active'] = false;
          break;
        }
        var que_item = timer_options['que'][0];
        var que_compiled = '';

        if(!timer['que_compiled'] && timer['que_compiled'] != '')
        {
          sn_timers[timerID]['que_compiled'] = sn_timer_compile_que(timer_options);
//          HTML_que.innerHTML = sn_timers[timerID]['que_compiled'];
          que_compiled = sn_timers[timerID]['que_compiled'];
        }

        if(que_item[UNIT_TIME] <= timestamp - timer['start_time'])
        {
          que_item[UNIT_AMOUNT]--;
          if(que_item[UNIT_AMOUNT] <= 0)
          {
            timer_options['que'].shift();
            que_item = timer_options['que'][0];
          }
          timer['start_time'] = timestamp;
          sn_timers[timerID]['que_compiled'] = sn_timer_compile_que(timer_options);
//          HTML_que.innerHTML = sn_timers[timerID]['que_compiled'];
          que_compiled = sn_timers[timerID]['que_compiled'];
        }

        if(timer_options['que'].length && que_item[UNIT_ID])
        {
          timeFinish = timer['start_time'] + que_item[UNIT_TIME];
          timeLeft = timer['start_time'] + que_item[UNIT_TIME] - timestamp;
          infoText = que_item[UNIT_NAME];
          if(que_item[UNIT_AMOUNT] > 1)
          {
            infoText += ' (' + que_item[UNIT_AMOUNT] + ')';
          }
          if(que_item[UNIT_LEVEL] > 1)
          {
            infoText += ' (' + que_item[UNIT_LEVEL] + ')';
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
    }

    activeTimers++;
  }

  if(activeTimers)
  {
    window.setTimeout("sn_timer();", 1000);
  }
}
