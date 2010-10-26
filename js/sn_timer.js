/*

SuperNova JavaScript timer system

 1.2 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [~] - changed sn_timer from array to objects

 1.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [~] - optimization: now HTML elements for timers is caching after first tick and didn't search every tick
         This should rise perfomance a bit

 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [!] - initial release

Object structure:
  'id'          - timer ID (name)
  'type'        - timer type: 0 - time countdown; 1 - counter; 2 - date&time; 3 - new counter
  'active'      - is timer active?
  'start_time'  - start time
  'options'     - timer options
  'html_main'   - reserved for internal use (link to main HTML element)
  'html_timer'  - reserved for internal use (link to 'timer' HTML element)
  'html_finish' - reserved for internal use (link to 'finish' HTML element)

Options for time countdown:
[0] - inactive message
[1] - que: array
        [0] - element ID
        [1] - element name
        [2] - build length
        [3] - level or count

Options for counter:
[0] - start value
[1] - delta value
[2] - max value

Options for date&time:
bit 1 - date
bit 2 - time
It means: 1 - just date; 2 - just time; 3 - both date & time

*/

var sn_timers = new Array();

function sn_timer() {
  var HTML, HTML_timer, HTML_finish;

  var activeTimers = 0;
  var local_time = new Date();
  var time_now = new Date();
  time_now.setTime(local_time.valueOf() + timeDiff);
  var timestamp = Math.round(time_now.valueOf() / 1000);

  for(timerID in sn_timers){
    timer = sn_timers[timerID];
    if(!timer['active'])continue;
    timer_options = timer['options'];

//    HTML        = document.getElementById(timer[0]);
//    HTML_timer  = document.getElementById(timer[0] + '_timer');
//    HTML_finish = document.getElementById(timer[0] + '_finish');
    if(!timer['html_main'])
    {
      sn_timers[timerID]['html_main'] = document.getElementById(timer['id']);
      sn_timers[timerID]['html_timer'] = document.getElementById(timer['id'] + '_timer');
      sn_timers[timerID]['html_finish'] = document.getElementById(timer['id'] + '_finish');
    }
    HTML        = sn_timers[timerID]['html_main'];
    HTML_timer  = sn_timers[timerID]['html_timer'];
    HTML_finish = sn_timers[timerID]['html_finish'];

    switch(timer['type']){
      case 0: // countdown timer
        if(timer['start_time'] + timer_options[1][0][2] - timestamp < 0){
          timer_options[1][0][3]--;
          if(timer_options[1][0][3]<=0)
            timer_options[1].shift();
          timer['start_time'] = timestamp;
        }

        if(timer_options[1].length && timer_options[1][0][0]){
          timeFinish = timer['start_time'] + timer_options[1][0][2];
          timeLeft = timer['start_time'] + timer_options[1][0][2] - timestamp;
          infoText = timer_options[1][0][1];
          timerText = sn_timestampToString(timeLeft);
        }else{
          timer['active'] = false;
          infoText = timer_options[0];
          timerText = '';
        }

        if(HTML_timer != null)
          HTML_timer.innerHTML = timerText;
        else{
          if(infoText != '' && timerText)
            infoText += '<br>';
          infoText += timerText;
        }

        if(HTML_finish != null)
          HTML_finish.innerHTML = timeFinish;

        if(HTML != null)
          HTML.innerHTML = infoText;

      break;

      case 1: // time-independent counter
        var new_value = timer_options[0] + Math.floor(timer_options[1] * (timestamp - timer['start_time']) / 36) / 100;
        printData = sn_format_number(new_value, 2, 'white', timer_options[2]);
        if(new_value >= timer_options[2])
        {
          timer['active'] = false;
        };

        if(HTML != null)
          HTML.innerHTML = printData;
        else
          timer['active'] = false;
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
          printData += local_time.toLocaleDateString();

        if(timer['options'] & 3)
         printData += '&nbsp;';

        if(timer['options'] & 2)
          printData += local_time.toTimeString().substring(0,8);

        if(HTML != null)
          HTML.innerHTML = printData;
        else
          timer['active'] = false;
      break;

    }
    activeTimers++;
  }

  if(activeTimers)
    window.setTimeout("sn_timer();", 1000);
}
