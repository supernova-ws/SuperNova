/**
 * Created by Gorlum on 12.02.2017.
 */

const TUTORIAL_BLOCK = 'tutorial';
const TUTORIAL_ID = 'id';
const TUTORIAL_CONTENT = 'content';
const TUTORIAL_TITLE = 'title';
const TUTORIAL_NEXT = 'next';
const TUTORIAL_PREV = 'prev';

var tutorial = [];

jQuery(document).on('click', '#tutorial_close', function (e) {
  jQuery('#tutorial_block').addClass('hide');
});

function tutorialError(errorText) {
  jQuery('#tutorial_footer').addClass('error').text(errorText).removeClass('hide');
}

function tutorialMakeAction(action, handler) {
  jQuery.get(
    SN_ROOT_VIRTUAL + 'index.php?page=ajax&mode=tutorial&action=' + action + '&id=' + jQuery('#tutorial_current').val(),
    handler,
    "json"
  );
}

function tutorialGet(action, errorEmpty) {
  jQuery.get(
    SN_ROOT_VIRTUAL + 'index.php?page=ajax&mode=tutorial&action=' + action + '&id=' + jQuery('#tutorial_current').val(),
    function (data) {
      if(data.hasOwnProperty(TUTORIAL_BLOCK) && data[TUTORIAL_BLOCK]) {
        tutorial = data[TUTORIAL_BLOCK];
        if(tutorial.hasOwnProperty(TUTORIAL_ID) && Math.intVal(tutorial[TUTORIAL_ID])) {
          tutorial_change();
          jQuery('#tutorial_footer').addClass('hide').removeClass('error');
        } else {
          tutorialError(errorEmpty);
        }
      } else {
        tutorialError('{Ошибка загрузки туториала - попробуйте еще раз! В случае повторной ошибки - сообщите Администрации игры}');
      }
    },
    "json"
  );
}

jQuery(document).on('click', '#tutorial_button_next', function (e) {
  tutorialGet('ajaxNext', '{Ошибка: Не существует следующей страницы туториала - сообщите Администрации игры}');
});

jQuery(document).on('click', '#tutorial_button_prev', function (e) {
  tutorialGet('ajaxPrev', '{Ошибка: Не существует предыдущей страницы туториала - сообщите Администрации игры}');
});

jQuery(document).on('click', '#tutorial_button_finish', function (e) {
  tutorialMakeAction('ajaxFinish', function() {
    jQuery('#tutorial_block').addClass('hide');
  });
});

function tutorial_change() {
  jQuery('#tutorial_current').val(tutorial[TUTORIAL_ID]);
  jQuery('#tutorial_text').html(tutorial[TUTORIAL_CONTENT]);
  jQuery('#tutorial_header_additional').html(' - ' + tutorial[TUTORIAL_TITLE]);
  if (Math.intVal(tutorial[TUTORIAL_NEXT])) {
    jQuery('#tutorial_button_next').removeClass('hide');
    jQuery('#tutorial_button_finish').addClass('hide');
  } else {
    jQuery('#tutorial_button_next').addClass('hide');
    jQuery('#tutorial_button_finish').removeClass('hide');
  }
  if (Math.intVal(tutorial[TUTORIAL_PREV])) {
    jQuery('#tutorial_button_prev').removeClass('hide');
  } else {
    jQuery('#tutorial_button_prev').addClass('hide');
  }
}
