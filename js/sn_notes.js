/**
 * Created by Gorlum on 22.01.2015.
 */

var NOTE_CLASS_PREVIOUSLY_SELECTED = '';

function note_select_do(status) {
  status = status == undefined ? jQuery("#note_select_all").prop("checked") : status;
  jQuery('[name^="note["][name$="]"]').prop("checked", status);
}

function note_edit(obj) {
  jQuery('#note_id_edit').val(jQuery(obj).data('note_id'));
  jQuery('#note_text,#note_title').val("");
  jQuery('#note_form').prop('action', jQuery('#note_form').prop('action') + '#a' + jQuery(obj).data('note_id'));
  jQuery('#note_form').submit();
}

function note_validate() {
  // TODO: Validate
  jQuery('#note_form').submit();
}

jQuery(document).ready(function() {
  jQuery("#note_form").delegate('[data-note_id]', "click", function(event, ui) {
    note_edit(jQuery(this));
  });

  jQuery(document).on('focus', '#note_title', function() {
    $(this).val() == LA_note_new_title ? $(this).val('') : false;
  });
  jQuery(document).on('blur', '#note_title', function() {
    $(this).val() == '' ? $(this).val(LA_note_new_title) : false;
  });

  jQuery(document).on('focus', '#note_text', function() {
    $(this).val() == LA_note_new_text ? $(this).val('') : false;
  });
  jQuery(document).on('blur', '#note_text', function() {
    $(this).val() == '' ? $(this).val(LA_note_new_text) : false;
  });

  $('#note_priority').change();
});

jQuery(document).on('change', '.note_delete_range', function() {
  $('#note_delete_range,.note_delete_range').val(jQuery(this).val());

  jQuery(this).val() ? jQuery('.note_delete_button').button('enable') : jQuery('.note_delete_button').button('disable');
});

jQuery(document).on('change', '#note_priority', function() {
  var currentNoteClass = $(this).find('option:selected').attr('class');
  $('#note_priority,#note_title,#note_text').removeClass(NOTE_CLASS_PREVIOUSLY_SELECTED).addClass(currentNoteClass);
  NOTE_CLASS_PREVIOUSLY_SELECTED = currentNoteClass;
});
