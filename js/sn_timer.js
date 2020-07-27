if(window.LOADED_TIMER === undefined) {
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
   'changed' - reserved for internal use (flag that event text was changed)
   */

  var UNIT_ID = 0;
  var UNIT_NAME = 1;
  var UNIT_TIME = 2;
  var UNIT_AMOUNT = 3;
  var UNIT_LEVEL = 4;
  var UNIT_TIME_FULL = 5;
  var UNIT_IMAGE = 6;
  var UNIT_TIME_DISPLAY_OPTION = 'displayType';
  var UNIT_TIME_DISPLAY_OPTION_HUMAN = 'human'; // Human-readable display type

  var EVENT_TIME = 0;
  var EVENT_STRING = 1;
  var EVENT_HINT = 2;

  // var TIMER_UNSUPPORTED = -1;
  var TIMER_BUILD_QUE_V1 = 0;
  var TIMER_COUNTER = 1;
  var TIMER_BUILD_QUE_V2 = 3;
  var TIMER_CLOCK_REALTIME = 4;
  var TIMER_EVENT_QUE = 5; // Event que in title (NavBar)

  var sn_timers = new Array();
  var timer_is_started = false;
  var timer_is_prepared = false;

  function sn_timer_prepare() {
    var timer, timerID, timer_options;

    if(timer_is_prepared) {
      return;
    }

    for (timerID in sn_timers) {
      if (!sn_timers.hasOwnProperty(timerID)) {
        continue;
      }

      timer = sn_timers[timerID];
      timer['start_time'] = timeTimerStart; // new Date((SN_TIME_NOW - timeDiff ) * 1000  );
      if (timer['type'] === undefined) {
        timer['active'] = false;
        continue;
      }
      timer['active'] = true;

      timer.prefixClass = '.' + timer['id'];
      timer.prefixId = '#' + timer['id'];

      if(timer.hasOwnProperty('className')) {
        timer.className = '.' + timer['className'];
        timer['html_main'] = $(timer.className);
      } else {
        // Кэшируем DOM-ики
        timer['html_main'] = $(timer.prefixId);
      }


      // Если нет настроек - создаём пустой объект
      timer['options'] === undefined ? timer['options'] = {} : false;
      timer_options = timer['options'];

      timer['type'] = parseInt(timer['type']);

      switch (timer['type']) {
        case TIMER_BUILD_QUE_V1: {
          // Если нет ни основного элемента вывода, ни элемента таймера - тогда можно даже не начинать работу счётчика
          // TODO - Проверка на is(':visible')
          timer['html_timer'] = $(timer.prefixId + '_timer');
          if (!timer['html_main'].length && !timer['html_timer'].length) {
            timer['active'] = false;
            break;
          }

          // Если у нас только html_main, но нет html_timer - надо каждый шаг обновлять html_main, потому что в нём содержится и таймер
          timer['always_refresh'] = timer['html_main'].length && !timer['html_timer'].length;
          timer['changed'] = true;

          break;
        }

        case TIMER_BUILD_QUE_V2: {
          if (!timer_options['que'].length) {
            if (timer_options['url'] !== undefined) {
              document.location = timer_options['url'];
            }
            timer['active'] = false;
            break;
          }

          timer['slotAmountBarWidthLast'] = 0;
          timer['unit_amount_original'] = timer_options['que'][0][UNIT_AMOUNT];

          timer['html_total_js'] = $(timer.prefixId + '_total:visible');
          timer['html_que_js'] = $(timer.prefixId + '_que');
          timer['html_finish'] = $(timer.prefixId + '_finish:visible');
          timer['html_slots_current'] = $(timer.prefixId + '_slots');
          timer['html_queProgressBar'] = $(timer.prefixId + '_progress_bar');
          // Unit amount/level in que
          timer['html_timer_units'] = $(timer.prefixId + '_units');
          timer.queProgressBarWidth = timer['html_queProgressBar'].width() ? timer['html_queProgressBar'].width() : 0;

          timer['html_level_current'] = $(timer.prefixClass + '_level_0:visible');
          timer['html_slotUnitTimeBar'] = $(timer.prefixClass + '_unit_bar_0:visible');
          timer.slotUnitTimeBarWidth = timer['html_slotUnitTimeBar'].parent().width() ? timer['html_slotUnitTimeBar'].parent().width() : 0;

          timer['html_timer_current'] = $(timer.prefixClass + '_timer_0:visible');

          timer.slotUnitTimeBarWidthCurrent = -1;
          timer.slotAmountBarWidthCurrent = -1;
          timer.queProgressBarWidthCurrent = -1;

          timer['que_compiled'] = '';
          break;
        }

        case TIMER_EVENT_QUE: {
          // Если нет ни основного элемента вывода, ни видимого элемента с подсказкой - тогда можно даже не начинать работу счётчика
          // TODO - Проверка на is(':visible')
          timer['html_total_js'] = $(timer.prefixId + '_total:visible');
          if (!timer['html_main'].length && !timer['html_total_js'].length) {
            timer['active'] = false;
            break;
          }

          timer['changed'] = true;
          break;
        }

        case TIMER_COUNTER: {
          // TODO - Проверка на is(':visible')
          timer_options['round'] = timer_options['round'] === undefined ? 0 : parseInt(timer_options['round']);
          timer_options['start_value'] = timer_options['start_value'] === undefined ? 0 : parseInt(timer_options['start_value']);
          timer_options['per_second'] = timer_options['per_second'] === undefined ? 0 : parseFloat(timer_options['per_second']);

          timer['current'] = timer_options['start_value'];

          if (timer['html_main'].length <= 0) {
            timer['active'] = false;
            break;
          }
          break;
        }

        case TIMER_CLOCK_REALTIME: {
          // TODO - Проверка на is(':visible')
          if (timer['html_main'].length <= 0) {
            timer['active'] = false;
            break;
          }

          timer_options['format'] = parseInt(timer_options['format'] === undefined ? timer_options : timer_options['format']);
          timer_options['delta'] = timer_options['delta'] === undefined ? 0 : parseInt(timer_options['delta']);
          break;
        }


        default: {
          timer['active'] = false;
          break;
        }

      }
    }
    timer_is_prepared = true;

  }

  function sn_timer_compile_que(timer) {
    var timer_options = timer['options'];
    var que = timer_options['que'];
    var compiled = '';
    var total = 0;
    var unit_count, que_id;
    // TODO - Хак, что бы не рендерить очередь для навбара. Переделать при общем рефакторинге кода
    var timer_invisible = timer['html_que_js'].is(':not(:visible)');

    for (que_id in que) {
      if(!que.hasOwnProperty(que_id)) {
        continue;
      }

      total += (que[que_id][UNIT_AMOUNT] - (que_id == 0 ? 1 : 0)) * que[que_id][UNIT_TIME_FULL];

      if(timer_invisible) {
        continue;
      }

      unit_count = que[que_id][que[que_id][UNIT_LEVEL] > 0 ? UNIT_LEVEL : UNIT_AMOUNT];

      compiled += timer_options['template']
        .replace(/\[UNIT_ID\]/gi, que[que_id][UNIT_ID])
        .replace(/\[UNIT_TIME\]/gi, sn_timestampToString(que[que_id][UNIT_TIME]))
        .replace(/\[UNIT_LEVEL\]/gi, unit_count)
        .replace(/\[UNIT_NAME\]/gi, que[que_id][UNIT_NAME])
        .replace(/\[UNIT_IMAGE\]/gi, que[que_id][UNIT_IMAGE])
        .replace(/\[UNIT_QUE_PLACE\]/gi, que_id);
    }
    timer_options['total'] = total;
    !timer_options['total_original'] ? timer_options['total_original'] = timer_options['total'] : false;

    return compiled;
  }

  function sn_timer() {
    if(timer_is_started) {
      window.setTimeout("sn_timer();", 1000);
      return;
    }

    timer_is_started = true;

    sn_timer_prepare();

    var timer, timerID, timeLeftTotalText, infoText, timer_options, local_time_plus, timeLeftText, new_value, hintText,
      timeLeft, timeSinceLastUpdate, que;
    var activeTimers = 0;
    var textUnitsLeft = '';

    var time_local_now = new Date();

    for (timerID in sn_timers) {
      if(!sn_timers.hasOwnProperty(timerID) || !sn_timers[timerID]['active']) {
        continue;
      }

      infoText = '';
      timeLeftText = '';

      timer = sn_timers[timerID];
      timer_options = timer['options'];
      timeSinceLastUpdate = Math.round((time_local_now.valueOf() - timer['start_time'].valueOf()) / 1000);

      switch (timer['type']) {
        case TIMER_BUILD_QUE_V1: {
          que = timer_options['que'];
          if(que.length) {
            if (que[0][UNIT_TIME] <= timeSinceLastUpdate) {
              timer['start_time'] = time_local_now;
              que[0][UNIT_AMOUNT]--;
              if (que[0][UNIT_AMOUNT] <= 0) {
                que.shift();
                timer['changed'] = true;
              }
            }
          }

          if (que.length && que[0][UNIT_ID]) {
            //completionDateTime = new Date((timer['start_time'] + que_item[UNIT_TIME]) * 1000); // Дата окончания постройки текущего юнита. Пока не используется
            // TODO - Нам не обязательно каждый раз обновлять html_main, если у нас есть html_timer
            infoText = que[0][UNIT_NAME] + (que[0][UNIT_LEVEL] ? ' (' + (que[0][UNIT_LEVEL]) + ')' : '');
            timeSinceLastUpdate = Math.round((time_local_now.valueOf() - timer['start_time'].valueOf()) / 1000);
            timeLeftText = sn_timestampToString(- (timeSinceLastUpdate) + parseInt(que[0][UNIT_TIME]));
          } else {
            infoText = timer_options['msg_done'];
            timer['changed'] = true;
            timer['active'] = false;
          }

          if (timer['html_timer'].length) {
            timer['html_timer'].text(timeLeftText);
          } else {
            infoText += (infoText && timeLeftText ? '<br>' : '') + timeLeftText;
          }
          if((timer['always_refresh'] || timer['changed']) && timer['html_main'].length) {
            timer['html_main'].html(infoText);
          }

          //typeof timer['html_finish'] != 'undefined' && timer['html_finish'].length ? timer['html_finish'].text(completionDateTime) : false; // Дата окончания постройки текущего юнита. Пока не используется
          break;
        }

        case TIMER_BUILD_QUE_V2: {
          textUnitsLeft = '';

          timer['start_time'] = time_local_now;

          que = timer_options['que'];

          que[0][UNIT_TIME] -= timeSinceLastUpdate;
          if(que[0][UNIT_TIME] <= 0) {
            timeSinceLastUpdate = -que[0][UNIT_TIME];
            que[0][UNIT_AMOUNT]--;
            if (que[0][UNIT_AMOUNT] <= 0) {
              que.shift();
              timer['que_compiled'] = '';
            } else {
              que[0][UNIT_TIME] = que[0][UNIT_TIME_FULL];
              timer_options['total'] -= que[0][UNIT_TIME_FULL];
              timer['html_level_current'].text(que[0][UNIT_AMOUNT]);
              timer.slotAmountBarWidthCurrent = Math.percentPixels(que[0][UNIT_AMOUNT], timer['unit_amount_original'], timer.slotUnitTimeBarWidth);
            }
          } else {
            timeSinceLastUpdate = 0;
          }

          if (!timer['que_compiled']) {
            // TODO - проверка на timer['html_que_js'].length
            timer['que_compiled'] = sn_timer_compile_que(timer);

            timer['html_slots_current'].length ? timer['html_slots_current'].html(que.length) : false;
            timer['html_que_js'].length ? timer['html_que_js'].html(timer['que_compiled']) : false;

            timer['html_level_current'] = $(timer.prefixClass + '_level_0:visible');
            timer['html_timer_seconds'] = $(timer.prefixClass + '_seconds_0:visible');
            timer['html_timer_current'] = $(timer.prefixClass + '_timer_0:visible');

            timer['html_slotUnitTimeBar'] = $(timer.prefixClass + '_unit_bar_0:visible');
            timer.slotUnitTimeBarWidth = timer['html_slotUnitTimeBar'].parent().width() ? timer['html_slotUnitTimeBar'].parent().width() : 0;
            timer['html_slotAmountBar'] = $(timer.prefixClass + '_slot_bar_0:visible');
          }

          if (que.length && que[0][UNIT_ID]) {
            que[0][UNIT_TIME] -= timeSinceLastUpdate; // Вычитаем то, что могло остаться с прошлого юнита/стэка
            timeLeft = que[0][UNIT_TIME] <= 0 ? 1 : que[0][UNIT_TIME];

            // infoText = que[0][UNIT_NAME] + '<br />(' + que[0][que[0][UNIT_LEVEL] ? UNIT_LEVEL : UNIT_AMOUNT] + ')';
            infoText = que[0][UNIT_NAME];
            textUnitsLeft = '(' + que[0][que[0][UNIT_LEVEL] ? UNIT_LEVEL : UNIT_AMOUNT] + ')';
            if(timer.hasOwnProperty('options') && timer.options.hasOwnProperty(UNIT_TIME_DISPLAY_OPTION) && timer.options[UNIT_TIME_DISPLAY_OPTION] === UNIT_TIME_DISPLAY_OPTION_HUMAN) {
              timeLeftText = sn_timestampToStringHuman(timeLeft);
              timeLeftTotalText = sn_timestampToStringHuman(timeLeft + timer_options['total']);
            } else {
              timeLeftText = sn_timestampToString(timeLeft);
              timeLeftTotalText = sn_timestampToString(timeLeft + timer_options['total']);
            }

            if(!timer['html_finish'].already_tagged && timer['html_finish'].length) {
              // Дата окончания постройки текущего юнита
              timer['html_finish'].html(snDateToString(
                new Date(timeBrowser.valueOf() + (timeLeft + timer_options['total']) * 1000), 7
              ));
              timer['html_finish'].already_tagged = true;
            }

            timer.slotUnitTimeBarWidthCurrent = Math.percentPixels(timeLeft, que[0][UNIT_TIME_FULL], -timer.slotUnitTimeBarWidth);
            timer.queProgressBarWidthCurrent = Math.percentPixels(timeLeft + timer_options['total'], que[0][UNIT_TIME_FULL] + timer_options['total_original'], timer.queProgressBarWidth);
          } else {
            if (timer_options['url'] !== undefined) {
              document.location = timer_options['url'];
            }

            timeLeft = 0;

            infoText = timer_options['msg_done'];
            // timeLeftText = '';
            // timeLeftTotalText = '00:00:00';

            // timer['html_finish'].hide();
            $(timer.prefixClass + '_hide_on_complete').hide();
            // ov_{$QUE_ID}_finish_hide
            timer['active'] = false;

            //timer.slotUnitTimeBarWidthCurrent = 0;
            //timer.slotAmountBarWidthCurrent = 0;
            //timer.queProgressBarWidthCurrent = 0;
          }

          // Вывод строковых значений
          var barWidth = Math.round((timeLeft % 60 + 1) / 60 * 100);
          barWidth > 100 ? barWidth = 100 : false;
          timer['html_timer_seconds'].length ? timer['html_timer_seconds'].width(barWidth + '%') : false;
          if (timer['html_total_js'].length) {
            timer['html_total_js'].html(timeLeftTotalText);
          } else {
            timeLeftText += (timeLeftText ? '<br />' : '') + timeLeftTotalText;
          }

          if (timer['html_timer_units'].length) {
            timer['html_timer_units'].html("<span>" + textUnitsLeft + "</span>");
          } else {
            infoText && textUnitsLeft ? infoText += "<br />" + textUnitsLeft : false;
          }

          if (timer['html_timer_current'].length) {
            timer['html_timer_current'].html("<span>" + timeLeftText + "</span>");
          } else {
            infoText += (infoText && timeLeftText ? "<br />" : "") + timeLeftText;
          }

          // ProgressBars
          if(!PLAYER_OPTION_PROGRESS_BARS_DISABLED) {
            if (timer['html_queProgressBar'].length && timer.queProgressBarWidthCurrent != timer.queProgressBarWidthLast) {
              timer['html_queProgressBar'].css('width', timer.queProgressBarWidthLast = timer.queProgressBarWidthCurrent);
            }
            if (timer['html_slotUnitTimeBar'].length && timer.slotUnitTimeBarWidthCurrent != timer.slotUnitTimeBarWidthLast) {
              timer['html_slotUnitTimeBar'].css('width', timer.slotUnitTimeBarWidthLast = timer.slotUnitTimeBarWidthCurrent);
            }
            if (timer['html_slotAmountBar'].length && timer.slotAmountBarWidthCurrent != timer.slotAmountBarWidthLast) {
              timer['html_slotAmountBar'].css('width', timer.slotAmountBarWidthLast = timer.slotAmountBarWidthCurrent);
            }
          }

          // Анимация
          if(!jQuery.fx.off && que.length){
            if(que[0][UNIT_TIME] == 1 && que[0][UNIT_AMOUNT] == 1) {
              $(timer.prefixClass + '_container_0:visible').animate({opacity: 0}, que[0][UNIT_TIME] * 1000);
            }
            if(que[0][UNIT_TIME_FULL] == timeLeft) {
              timer['html_timer_current'].children().animate({opacity: 0}, 50, function() {
                $(this).animate({opacity: 1}, 300)});
            }
          }

          timer['html_main'].length && infoText != timer.infoText ? timer['html_main'].html(timer.infoText = infoText) : false;
          break;
        }

        case TIMER_EVENT_QUE: {
          // Event que in title (NavBar for now)
          hintText = '';

          if (timer_options['que'].length && timer_options['que'][0][EVENT_TIME] <= timeSinceLastUpdate) { // TODO - проверить. Может тут ошибка - генерятся длительности не от SN_TIME_NOW, а дельты
            //timer['start_time'] = timestamp_server; // TODO - а вот это тогда всё исправит
            timer_options['que'].shift();
            timer['changed'] = true;
          }

          if (timer_options['que'].length) {
            if(timer['changed']) {
              infoText = timer_options['que'][0][EVENT_STRING];
              hintText = timer_options['que'][0][EVENT_HINT];
            }
          } else {
            infoText = timer_options['msg_done'];
            timer['changed'] = true;
            timer['active'] = false;
          }

          if (timer['changed']) {
            timer['html_main'].length ? timer['html_main'].html(infoText) : false;
            // Если нет видимого элемента total - выводим подсказку в main. Уж один-то из них точно видимый!
            timer[timer['html_total_js'].length ? 'html_total_js' : 'html_main'].prop('title', hintText);
            timer['changed'] = false;
          }
          break;
        }

        case TIMER_COUNTER: {
          if(timer_options['per_second'] == 0) {
            new_value = timer_options['start_value'];
            timer['active'] = false;
          } else {
            new_value = timer_options['start_value'] + timeSinceLastUpdate * timer_options['per_second'];
            if (new_value < 0) {
              new_value = 0;
              timer['active'] = false;
            } else {
              if (new_value >= timer_options['max_value'] && timer_options['per_second'] > 0) {
                new_value = Math.max(timer_options['start_value'], timer_options['max_value']);
                timer['active'] = false;
              }
            }
          }
          infoText = sn_format_number(new_value, timer_options['round'], 'positive', timer_options['max_value']);

          timer['current'] = new_value;

          if(timer.hasOwnProperty('className')) {
            $(timer.className).html(infoText);
          } else {
            timer['html_main'].html(infoText);
          }

          break;
        }

        case TIMER_CLOCK_REALTIME: {
          // date&time with delta
          local_time_plus = new Date(time_local_now.valueOf() + timer_options['delta'] * 1000);

          timer['html_main'].text(snDateToString(local_time_plus, timer_options['format']));
          break;
        }
      }

      activeTimers++;
    }

    if (activeTimers) {
      window.setTimeout("sn_timer();", 1000);
    }
    timer_is_started = false;
  }

}

function timerById(id) {
  var timer = null;
  for(var idNumeric in sn_timers) {
    if(!sn_timers.hasOwnProperty(idNumeric)) {
      continue;
    }

    if(sn_timers[idNumeric].id == id) {
      timer = sn_timers[idNumeric];
      break;
    }

  }
  return timer;
}
