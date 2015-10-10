if(typeof(window.LOADED_TIMER) === 'undefined') {
  var LOADED_TIMER = true;
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
   'html_finish' - reserved for internal use (link to 'finish' HTML element) Дата окончания постройки текущего юнита. Пока не используется
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

  var UNIT_ID = 0;
  var UNIT_NAME = 1;
  var UNIT_TIME = 2;
  var UNIT_AMOUNT = 3;
  var UNIT_LEVEL = 4;
  var UNIT_TIME_FULL = 5;

  var EVENT_TIME = 0;
  var EVENT_STRING = 1;
  var EVENT_HINT = 2;

  var sn_timers = new Array();

  function sn_timer_compile_que(timer_options) {
    var que = timer_options['que'];
    var compiled = '';
    var total = 0;
    var unit_count, que_id;

    for (que_id in que) {
      total += (que[que_id][UNIT_AMOUNT] - (que_id == 0 ? 1 : 0)) * que[que_id][UNIT_TIME_FULL];

      unit_count = que[que_id][que[que_id][UNIT_LEVEL] > 0 ? UNIT_LEVEL : UNIT_AMOUNT];

      compiled += timer_options['template']
        .replace('[UNIT_ID]', que[que_id][UNIT_ID])
        .replace('[UNIT_TIME]', sn_timestampToString(que[que_id][UNIT_TIME]))
        .replace(/\[UNIT_LEVEL\]/gi, unit_count)
        .replace(/\[UNIT_NAME\]/gi, que[que_id][UNIT_NAME])
        .replace(/\[UNIT_QUE_PLACE\]/gi, que_id);

      //unit_name = que[que_id][UNIT_NAME];

      //temp = temp.replace('[UNIT_LEVEL]', unit_count);
      //unit_name += ' (' + unit_count + ')';
      //if (que[que_id][UNIT_LEVEL] > 0) {
      //  unit_name += ' (' + que[que_id][UNIT_LEVEL] + ')';
      //  temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_LEVEL]);
      //} else {
      //  unit_name += ' (' + que[que_id][UNIT_AMOUNT] + ')';
      //  temp = temp.replace('[UNIT_LEVEL]', que[que_id][UNIT_AMOUNT]);
      //}
      //temp = temp.replace('[UNIT_NAME]', unit_name);
      //temp = temp.replace('[UNIT_NAME]', que[que_id][UNIT_NAME] + ' (' + unit_count + ')');
      //temp = temp.replace(/\[UNIT_QUE_PLACE\]/gi, que_id);
      //compiled += temp;
    }
    timer_options['total'] = total;

    return compiled;
  }


  var timer_is_started = false;

  function sn_timer() {
    if(timer_is_started) {
      window.setTimeout("sn_timer();", 1000);
      return;
    }

    timer_is_started = true;

    var timer, timerID, que_item, should_break, infoText, timer_options, local_time_plus, timerText;
    var activeTimers = 0, need_compile = false;

    var time_local_now = new Date();
    var time_passed = Math.round((time_local_now.valueOf() - localTime.valueOf()) / 1000);
    var timestamp_server = D_SN_TIME_NOW + time_passed;

    for (timerID in sn_timers) {
      timer = sn_timers[timerID];
      if (typeof timer['active'] === 'undefined') {
        continue;
      }

      if (!timer['html_main']) {
        //timer['html_main'] = document.getElementById(timer['id']);
        timer['html_main'] = $("#" + timer['id']);
        timer['html_timer'] = $("#" + timer['id'] + '_timer'); // document.getElementById(timer['id'] + '_timer');
        //timer['html_timer'] = document.getElementById(timer['id'] + '_timer');
        // timer['html_finish'] = document.getElementById(timer['id'] + '_finish');
        // timer['html_finish'] = $("#" + timer['id'] + '_finish:visible'); // Дата окончания постройки текущего юнита. Пока не используется
        timer['html_que_js'] = $("#" + timer['id'] + '_que');
        timer['html_total_js'] = $("#" + timer['id'] + '_total:visible'); // document.getElementById(timer['id'] + '_total');
        timer['html_level_current'] = $('.' + timer['id'] + '_level_0:visible');
      }

      timer_options = timer['options'];
      switch (timer['type']) {
        case 3: // new que display
          if (timer_options['que'].length == 0) {
            timer['active'] = false;
            if (timer_options['url'] != undefined) {
              document.location = timer_options['url'];
            }
            break;
          }
          que_item = timer_options['que'][0];


          need_compile = false;
          if (que_item[UNIT_TIME] <= timestamp_server - timer['start_time']) {
            que_item[UNIT_AMOUNT]--;
            if (que_item[UNIT_AMOUNT] <= 0) {
              timer_options['que'].shift();
              que_item = timer_options['que'][0];
              need_compile = true;
            } else {
              que_item[UNIT_TIME] = que_item[UNIT_TIME_FULL];
              timer['html_level_current'].text(que_item[UNIT_AMOUNT]);
            }
            timer['start_time'] = timestamp_server;
          }

          if (!timer['que_compiled'] || need_compile) { //  || timer['que_compiled'] != ''
//console.log('compile ' + timer['id'] + ' because need_compile ' + need_compile);
            timer['que_compiled'] = sn_timer_compile_que(timer_options);
            if (timer['html_que_js'] != null && timer['html_que_js'].length > 0) {
              timer['html_que_js'].html(timer['que_compiled']);
              // timer['html_que'].innerHTML = timer['que_compiled'];
            }
            timer['html_level_current'] = $('.' + timer['id'] + '_level_0:visible');
            timer['html_timer_current'] = $('.' + timer['id'] + '_timer_0:visible');
            timer['html_timer_seconds'] = $('.' + timer['id'] + '_seconds_0:visible');
          }

          should_break = typeof que_item == 'undefined' || typeof que_item[UNIT_ID] == 'undefined' || !que_item[UNIT_ID];
          if (timer_options['que'].length && !should_break) {
            //completionDateTime = new Date((timer['start_time'] + que_item[UNIT_TIME]) * 1000); // Дата окончания постройки текущего юнита. Пока не используется
            timeLeft = timer['start_time'] + que_item[UNIT_TIME] - timestamp_server;
            if(timer['html_timer_seconds'].length) {
              timer['html_timer_seconds'].width(Math.round((timeLeft % 60 + 1) / 60 * 100) + '%');
            }
            total_text = sn_timestampToString(timeLeft + timer_options['total']);
            infoText = que_item[UNIT_NAME] + ' (' + que_item[que_item[UNIT_LEVEL] ? UNIT_LEVEL : UNIT_AMOUNT] + ')';
            timerText = sn_timestampToString(timeLeft);
          } else {
            if (typeof timer_options['url'] != 'undefined') {
              document.location = timer_options['url'];
            }
            timer['active'] = false;
            infoText = timer_options['msg_done'];
            timerText = '';
            total_text = '00:00:00';
          }

          if (typeof timer['html_total_js'] != 'undefined' && timer['html_total_js'].length > 0) {
            timer['html_total_js'].html(total_text);
          } else {
            timerText += '<br>' + total_text;
          }

          //timer['html_timer_current'].text(timerText);

          if (typeof timer['html_timer_current'] != 'undefined' && timer['html_timer_current'].length > 0) {
            timer['html_timer_current'].text(timerText);
          } else {
            infoText += (infoText != '' && timerText ? '<br>' : '') + timerText;
          }

          typeof timer['html_que_js'] == 'undefined' || timer['html_que_js'].length <= 0
            ? infoText += timer['que_compiled'] : false;

          //typeof timer['html_finish'] != 'undefined' && timer['html_finish'].length ? timer['html_finish'].text(completionDateTime) : false; // Дата окончания постройки текущего юнита. Пока не используется

          //timer['html_main'] != null ? timer['html_main'].innerHTML = infoText : false;
          typeof timer['html_main'] != 'undefined' && timer['html_main'].length ? timer['html_main'].html(infoText) : false;

        break;


        case 0: // old que display
          que_item = timer_options['que'][0];
          if (que_item[UNIT_TIME] <= timestamp_server - timer['start_time']) {
            que_item[UNIT_AMOUNT]--;
            if (que_item[UNIT_AMOUNT] <= 0) {
              timer_options['que'].shift();
              que_item = timer_options['que'][0];
            }
            timer['start_time'] = timestamp_server;
          }

          if (timer_options['que'].length && que_item[UNIT_ID]) {
            //completionDateTime = new Date((timer['start_time'] + que_item[UNIT_TIME]) * 1000); // Дата окончания постройки текущего юнита. Пока не используется
            timeLeft = parseInt(timer['start_time']) + parseInt(que_item[UNIT_TIME]) - timestamp_server;
            infoText = que_item[UNIT_NAME] + (que_item[UNIT_LEVEL] ? ' (' + (que_item[UNIT_LEVEL]) + ')' : '');
            timerText = sn_timestampToString(timeLeft);
          } else {
            timer['active'] = false;
            infoText = timer_options['msg_done'];
            timerText = '';
          }

          if (typeof timer['html_timer'] != 'undefined' && timer['html_timer'].length) {
            // timer['html_timer'].innerHTML = timerText;
            timer['html_timer'].text(timerText);
          } else {
            infoText += (infoText != '' && timerText ? '<br>' : '') + timerText;
          }

          //typeof timer['html_finish'] != 'undefined' && timer['html_finish'].length ? timer['html_finish'].text(completionDateTime) : false; // Дата окончания постройки текущего юнита. Пока не используется

          //timer['html_main'] != null ? timer['html_main'].innerHTML = infoText : false;
          typeof timer['html_main'] != 'undefined' && timer['html_main'].length ? timer['html_main'].html(infoText) : false;
        break;

        case 5:
          que_item = timer_options['que'][0];
          infoText = '';

          if (que_item[EVENT_TIME] <= timestamp_server - timer['start_time']) {
            timer_options['que'].shift();
            //timer['start_time'] = timestamp;
            timer['options']['unchanged'] = false;
          }

          if (!timer['options']['unchanged']) {
            infoText = que_item[EVENT_STRING];
            hintText = que_item[EVENT_HINT];
          }

          if (!timer_options['que'].length) {
            timer['active'] = false;
            infoText = timer_options['msg_done'];
            hintText = '';
            timer['options']['unchanged'] = false;
          }

          if (!timer['options']['unchanged']) {
            timer['options']['unchanged'] = true;

            //timer['html_main'] != null ? timer['html_main'].innerHTML = infoText : false;
            typeof timer['html_main'] != 'undefined' && timer['html_main'].length ? timer['html_main'].html(infoText) : false;

            if (timer['html_total_js'] != null && timer['html_total_js'].length > 0) {
              timer['html_total_js'].prop('title', hintText);
            } else {
              //timer['html_main'].title = hintText;
              timer['html_main'].prop('title', hintText);
            }
          }
        break;

        case 1: // time-independent counter
          var new_value = parseInt(timer_options['start_value']) + (timestamp_server - parseInt(timer['start_time'])) * parseFloat(timer_options['per_second']);
          if (timer_options['round'] === undefined) {
            timer_options['round'] = 0;
          }
          if (new_value < 0) {
            new_value = 0;
            timer['active'] = false;
          }
          infoText = sn_format_number(new_value, timer_options['round'], 'positive', timer_options['max_value']);
          if ((new_value >= timer_options['max_value'] && timer_options['per_second'] > 0) || (timer_options['per_second'] == 0)) {
            timer['active'] = false;
            new_value = timer_options['max_value'];
          }

          //timer['html_main'] != null ? timer['html_main'].innerHTML = infoText : (timer['active'] = false);
          typeof timer['html_main'] != 'undefined' && timer['html_main'].length ? timer['html_main'].html(infoText) : (timer['active'] = false);
        break;

        case 4: // date&time with delta
          infoText = '';

          timer_options_delta = typeof timer_options['delta'] == 'undefined' ? 0 : timer_options['delta'];
          timer_options_format = typeof timer_options['format'] == 'undefined' ? timer_options : timer_options['format'];
          local_time_plus = new Date();
          local_time_plus.setTime(time_local_now.valueOf() + (timer_options_delta * 1000));

          timer_options_format & 1 ? infoText += local_time_plus.toLocaleDateString() : false;
          timer_options_format & 3 ? infoText += '&nbsp;' : false;
          timer_options_format & 2 ? infoText += local_time_plus.toTimeString().substring(0, 8) : false;

          //timer['html_main'] != null ? timer['html_main'].innerHTML = infoText : (timer['active'] = false);
          typeof timer['html_main'] != 'undefined' && timer['html_main'].length ? timer['html_main'].html(infoText) : (timer['active'] = false);
        break;

     }

      activeTimers++;
    }

    if (activeTimers) {
      window.setTimeout("sn_timer();", 1000);
    }
    timer_is_started = false;
  }

}
