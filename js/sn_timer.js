/*

SuperNova JavaScript timer system

 1.1 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [~] - optimization: now HTML elements for timers is caching after first tick and didn't search every tick
         This should rise perfomance a bit

 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws
   [!] - initial release

Array structure:
 [0] - timer ID (name)
 [1] - timer type: 0 - time countdown; 1 - counter; 2 - date&time
 [2] - is timer active?
 [3] - start time
 [4] - timer options
[90] - reserved for internal use (link to main HTML element)
[91] - reserved for internal use (link to 'timer' HTML element)
[92] - reserved for internal use (link to 'finish' HTML element)

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

  activeTimers = 0;
  local_time = new Date();
  time_now = new Date();
  time_now.setTime(local_time.valueOf() + timeDiff);
  timestamp = Math.round(time_now.valueOf() / 1000);

  for(timerID in sn_timers){
    timer = sn_timers[timerID];
    if(!timer[2])continue;
    timer_options = timer[4];

//    HTML        = document.getElementById(timer[0]);
//    HTML_timer  = document.getElementById(timer[0] + '_timer');
//    HTML_finish = document.getElementById(timer[0] + '_finish');
    if(!timer[90])
    {
      sn_timers[timerID][90] = document.getElementById(timer[0]);
      sn_timers[timerID][91] = document.getElementById(timer[0] + '_timer');
      sn_timers[timerID][92] = document.getElementById(timer[0] + '_finish');
    }
    HTML        = sn_timers[timerID][90];
    HTML_timer  = sn_timers[timerID][91];
    HTML_finish = sn_timers[timerID][92];

    switch(timer[1]){
      case 0: // countdown timer
        if(timer[3] + timer_options[1][0][2] - timestamp < 0){
          timer_options[1][0][3]--;
          if(timer_options[1][0][3]<=0)
            timer_options[1].shift();
          timer[3] = timestamp;
        }

        if(timer_options[1].length && timer_options[1][0][0]){
          timeFinish = timer[3] + timer_options[1][0][2];
          timeLeft = timer[3] + timer_options[1][0][2] - timestamp;
          infoText = timer_options[1][0][1];
          timerText = sn_timestampToString(timeLeft);
        }else{
          timer[2] = false;
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

      case 1: // counter
        if(timer_options[0] >= timer_options[2]){
          timer[2] = false;
          printData = '<font color=red>' + sn_format_number(timer_options[0], 2) + '</font>';
        }else{
          timer_options[0] += Math.floor(timer_options[1] * (timestamp - timer[3]) / 36) / 100
          printData = sn_format_number(timer_options[0], 2);
          timer[3] = timestamp;
        };

        if(HTML != null)
          HTML.innerHTML = printData;
        else
          timer[2] = false;
        break;

      case 2: // date&time
        printData = '';

        if(timer[4] & 1)
          printData += local_time.toLocaleDateString();

        if(timer[4] & 3)
         printData += '&nbsp;';

        if(timer[4] & 2)
          printData += local_time.toTimeString().substring(0,8);

        if(HTML != null)
          HTML.innerHTML = printData;
        else
          timer[2] = false;
        break;
    }
    activeTimers++;
  }

  if(activeTimers)
    window.setTimeout("sn_timer();", 1000);
}
