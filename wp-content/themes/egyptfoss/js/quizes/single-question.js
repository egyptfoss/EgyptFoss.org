jQuery("#ef_new_answer_button").click(function(event) {
  event.preventDefault();
  ef_add_answer();
});

jQuery("#the-list").on('click', '.edit_link', function(event) {
    event.preventDefault();
  
    jQuery('.answer_correct input').each(function () {
        jQuery(this).attr('class', "checkbox_answer_correct");
    });
  
    jQuery('.answer_points').each(function () {
        jQuery(this).css("visibility", "hidden");
        jQuery(this).css("width", "1%");
    });
    
    jQuery('#comment_area').css("display", "none");
    
});

function ef_add_answer(answer, points, correct)
{
  if (!answer) {
    answer = '';
  }
  if (!points) {
    points = 0;
  }
  if (!correct) {
    correct = 0;
  }
  var correct_text = '';
  if (correct === 1) {
    correct_text = ' checked="checked"';
  }
  var total_answers = parseInt(jQuery("#new_question_answer_total").val());
  total_answers += 1;
  jQuery("#new_question_answer_total").val(total_answers);
  var $answer_single = jQuery('<div class="answers_single">'+
    '<div class="answer_number"><button class="button delete_answer">Delete</button> '+answer_text+'</div>'+
    '<div class="answer_text"><input type="text" class="answer_input" name="answer_'+total_answers+'" id="answer_'+total_answers+'" value="'+answer+'" /></div>'+
    '<div class="answer_points" style="visibility: hidden; width: 1%;"><input type="text" class="answer_input" name="answer_'+total_answers+'_points" id="answer_'+total_answers+'_points" value="'+points+'" /></div>'+
    '<div class="answer_correct"><input type="checkbox" class="checkbox_answer_correct" id="answer_'+total_answers+'_correct" name="answer_'+total_answers+'_correct"'+correct_text+' value=1 /></div>'+
  '</div>');
  jQuery("#answers").append($answer_single);
}    

jQuery("input.checkbox_answer_correct:checkbox").live("change", function() { 
    if (this.checked) {
        jQuery(".checkbox_answer_correct").each(function () {
            jQuery(this).attr('checked', false);
        });
        jQuery(this).attr('checked', true);
    }
});

(function ($) {
  $(document).ready(function () {

  });
  
});