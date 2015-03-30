$(document).on('click', '[name="survey_confirm"]', function() {
  var survey_id = $(this).val();
  var survey_vote = $('[name="survey[' + survey_id + ']"]:checked').val();
  if(typeof survey_vote != 'undefined') {
    jQuery.post('announce.php?survey_id=' + survey_id + '&survey_vote=' + survey_vote);
    $('.survey_block[survey_id="' + survey_id + '"]').html(LA_survey_result_sent);
  }
});