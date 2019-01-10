var $j = jQuery.noConflict();
// increase the default animation speed to exaggerate the effect
$j.fx.speeds._default = 1000;
$j(function() {
  $j('#new_quiz_dialog').dialog({
    autoOpen: false,
    buttons: {
    Cancel: function() {
      $j(this).dialog('close');
      }
    }
  });

  $j('#new_quiz_button').click(function() {
    $j('#new_quiz_dialog').dialog('open');
    return false;
}	);
  $j('#new_quiz_button_two').click(function() {
    $j('#new_quiz_dialog').dialog('open');
    return false;
}	);
});

function deleteQuiz(id,quizName){
  $j("#delete_dialog").dialog({
    autoOpen: false,
    buttons: {
    Cancel: function() {
      $j(this).dialog('close');
      }
    }
  });
  $j("#delete_dialog").dialog('open');
  var idHidden = document.getElementById("quiz_id");
  var idHiddenName = document.getElementById("delete_quiz_name");
  idHidden.value = id;
  idHiddenName.value = quizName;
};

function editQuizName(id, quizName){
  $j("#edit_dialog").dialog({
    autoOpen: false,
    buttons: {
    Cancel: function() {
      $j(this).dialog('close');
      }
    }
  });
  $j("#edit_dialog").dialog('open');
  document.getElementById("edit_quiz_name").value = quizName;
  document.getElementById("edit_quiz_id"). value = id;
}

function duplicateQuiz(id, quizName){
  $j("#duplicate_dialog").dialog({
    autoOpen: false,
    buttons: {
    Cancel: function() {
      $j(this).dialog('close');
      }
    }
  });
  $j("#duplicate_dialog").dialog('open');
  document.getElementById("duplicate_quiz_id"). value = id;
}
