/*

SuperNova JavaScript timer system

 1.0 - copyright (c) 2010 by Gorlum for http://supernova.ws

Array structure:
[0] - timer ID (name)
[1] - timer type: 0 - time countdown; 1 - counter
[2] - is timer active?
[3] - start time
[4] - timer options

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

*/

var sn_timers = new Array();

function sn_formatNumber(number,laenge) {
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
  return arr_int[0]+','+arr_int[1];
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

function sn_timer() {
  var HTML;

  activeTimers = 0;

  for(timerID in sn_timers){
    time_now = new Date();
    timestamp = Math.round(time_now.valueOf() / 1000);

    timer = sn_timers[timerID];
    if(!timer[2])continue;
    timer_options = timer[4];

    HTML        = document.getElementById(timer[0]);
    HTML_timer  = document.getElementById(timer[0] + '_timer');
    HTML_finish = document.getElementById(timer[0] + '_finish');

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
        else
          infoText += '<br>' + timerText;

        if(HTML_finish != null)
          HTML_finish.innerHTML = timeFinish;

        if(HTML != null)
          HTML.innerHTML = infoText;

        break;

      case 1: // counter
        if(timer_options[0] >= timer_options[2]){
          timer[2] = false;
          printData = '<font color=red>' + sn_formatNumber(timer_options[0], 2) + '</font>';
        }else{
          timer_options[0] += Math.floor(timer_options[1] * (timestamp - timer[3]) / 36) / 100
          printData = sn_formatNumber(timer_options[0], 2);
          timer[3] = timestamp;
        };

        HTML.innerHTML = printData;
        break;
    }
    activeTimers++;
  }

  if(activeTimers)
    window.setTimeout("sn_timer();", 1000);
}
