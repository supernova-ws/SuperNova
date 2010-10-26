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

Options for que display:
  'msg_done' - inactive message
  'que'      - que: array
          [0] - element ID
          [1] - element name
          [2] - build length
          [3] - level or count

Options for counter:
'start_value' - start value
'per_second'  - delta value per second
'max_value'   - max value

Options for date&time:
bit 1 - date
bit 2 - time
It means: 1 - just date; 2 - just time; 3 - both date & time

*/

var sn_timers = new Array();

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
    }
    HTML        = sn_timers[timerID]['html_main'];
    HTML_timer  = sn_timers[timerID]['html_timer'];
    HTML_finish = sn_timers[timerID]['html_finish'];

    switch(timer['type'])
    {
      case 0: // que display
        var que_item = timer_options['que'][0];

        if(timer['start_time'] + que_item[2] - timestamp < 0)
        {
          que_item[3]--;
          if(que_item[3]<=0)
          {
            timer_options['que'].shift();
          }
          timer['start_time'] = timestamp;
        }

        if(timer_options['que'].length && que_item[0])
        {
          timeFinish = timer['start_time'] + que_item[2];
          timeLeft = timer['start_time'] + que_item[2] - timestamp;
          infoText = que_item[1];
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
    }

    activeTimers++;
  }

  if(activeTimers)
  {
    window.setTimeout("sn_timer();", 1000);
  }
}
