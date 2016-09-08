/**
 * Created by Gorlum on 22.01.2015.
 */

var NOTE_CLASS_PREVIOUSLY_SELECTED = '';

function note_select_do(status) {
  status = status == undefined ? jQuery("#note_select_all").prop("checked") : status;
  jQuery('[name^="note["][name$="]"]').prop("checked", status);
}

function note_edit(obj) {
  var elementNoteForm = jQuery('#note_form');
  jQuery('#note_id_edit').val(jQuery(obj).data('note_id'));
  jQuery('#note_text,#note_title').val("");
  elementNoteForm.prop('action', elementNoteForm.prop('action') + '#a' + jQuery(obj).data('note_id'));
  elementNoteForm.submit();
}

function note_validate() {
  // TODO: Validate
  jQuery('#note_form').submit();
}

jQuery(document).ready(function () {
  jQuery("#note_form").on("click", '[data-note_id]', function (event, ui) {
    note_edit(jQuery(this));
  });

  jQuery(document).on('focus', '#note_title', function () {
    $(this).val() == LA_note_new_title ? $(this).val('') : false;
  });
  jQuery(document).on('blur', '#note_title', function () {
    $(this).val() == '' ? $(this).val(LA_note_new_title) : false;
  });

  jQuery(document).on('focus', '#note_text', function () {
    $(this).val() == LA_note_new_text ? $(this).val('') : false;
  });
  jQuery(document).on('blur', '#note_text', function () {
    $(this).val() == '' ? $(this).val(LA_note_new_text) : false;
  });

  $('#note_priority').change();
});

jQuery(document).on('change', '.note_delete_range', function () {
  $('#note_delete_range,.note_delete_range').val(jQuery(this).val());

  var element = jQuery('.note_delete_button');
  jQuery(this).val() ? element.button('enable') : element.button('disable');
});

jQuery(document).on('change', '#note_priority', function () {
  var currentNoteClass = $(this).find('option:selected').attr('class');
  $('#note_priority,#note_title,#note_text').removeClass(NOTE_CLASS_PREVIOUSLY_SELECTED).addClass(currentNoteClass);
  NOTE_CLASS_PREVIOUSLY_SELECTED = currentNoteClass;
});
