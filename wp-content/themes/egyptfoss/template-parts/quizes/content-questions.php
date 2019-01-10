<?php 
$question_name = "";
$hint = "";
$question_order = count($questions)+1;
$required = 0;
$answers = "";
$ef_new_question_answer_total = 0;
$ef_new_question_text = "new_question";
$ef_question_id = 0;
if(isset($_POST) && $errorExists)
{
  $question_name = isset($_POST["question_name"])?$_POST["question_name"]:"";
  $hint = isset($_POST["hint"])?$_POST["hint"]:"";
  $question_order = isset($_POST["new_question_order"])?$_POST["new_question_order"]:"";
  $required = isset($_POST["required"])?$_POST["required"]:0;

  $total_answers = isset($_POST["new_question_answer_total"])?$_POST["new_question_answer_total"]:0;
  $ef_new_question_answer_total = $total_answers;
  if($_POST["question_submission"] == "edit_question")
  {
    $ef_question_id = $_POST['question_id'];
    $ef_new_question_text = "edit_question";
  }
}

?>
<button class="add-new-h2" id="new_question_button"><?php _e('Add Question', 'quiz-master-next'); ?></button>
<!--<button class="add-new-h2" id="from_other_quiz_button"><?php _e('Add Question From Other Survey/Quiz', 'quiz-master-next'); ?></button>-->
<button class="add-new-h2" id="save_question_order"><?php _e('Save Question Order', 'quiz-master-next'); ?></button>
<form style="display:none;" action="" method="post" name="save_question_order_form" id="save_question_order_form">
  <input type="hidden" name="save_question_order_input" id="save_question_order_input" value="" />
  <?php wp_nonce_field('qmn_question_order','qmn_question_order_nonce'); ?>
</form>
<br />
<p class="search-box">
  <label class="screen-reader-text" for="question_search">Search Questions:</label>
  <input type="search" id="question_search" name="question_search" value="">
  <a href="#" class="button">Search Questions</a>
</p>
<div class="tablenav top">
  <div class="tablenav-pages">
    <span class="displaying-num"><?php echo sprintf(_n('One question', '%s questions', count($questions), 'quiz-master-next'), number_format_i18n(count($questions))); ?></span>
  </div>
</div>
<table class="widefat">
  <thead>
    <tr>
      <th><?php _e('Question Order', 'quiz-master-next'); ?></th>
      <th><?php _e('Question Type', 'quiz-master-next'); ?></th>
      <th><?php _e('Category', 'quiz-master-next'); ?></th>
      <th><?php _e('Question', 'quiz-master-next'); ?></th>
    </tr>
  </thead>
  <!--<tfoot>
    <tr>
      <th><?php _e('Question Order', 'quiz-master-next'); ?></th>
      <th><?php _e('Question Type', 'quiz-master-next'); ?></th>
      <th><?php _e('Category', 'quiz-master-next'); ?></th>
      <th><?php _e('Question', 'quiz-master-next'); ?></th>
    </tr>
  </tfoot>-->
  <tbody id="the-list">
  </tbody>
</table>

<div class="question_area" id="question_area">
  <h2 class="question_area_header_text">Add New Question</h2>
  <form action="" method="post" class="question_form">
    <fieldset>
      <legend>Question Type</legend>
      <div class="row">
        <label class="option_label"><?php _e('Question Type', 'quiz-master-next'); ?></label>
        <select class="option_input" name="question_type" id="question_type">
          <?php
          foreach($qmn_question_types as $type)
          {
            echo "<option value='".$type['slug']."'>".$type['name']."</option>";
            break;
          }
          ?>
        </select>
      </div>
    </fieldset>
    <fieldset>
      <legend>Question And Answers</legend>
      <p id="question_type_info"></p>
      <?php wp_editor( $question_name, "question_name" ); ?>
      <div id="answer_area">
        <div class="answer_headers">
          <div class="answer_number">&nbsp;</div>
          <div class="answer_text"><?php _e('Answers', 'quiz-master-next'); ?></div>
          <!--<div class="answer_points"><?php _e('Points Worth', 'quiz-master-next'); ?></div>-->
          <div class="answer_correct"><?php _e('Correct Answer', 'quiz-master-next'); ?></div>
          <div class="answer_points" style="visibility: hidden;">Hello</div>
        </div>
        <div class="answers" id="answers">

          <!-- answers -->
          <?php 
          if(isset($_POST) && $errorExists)
          {
            $total_answers = isset($_POST["new_question_answer_total"])?$_POST["new_question_answer_total"]:0;
            $ef_new_question_answer_total = $total_answers;
            for($i = 1; $i <= $total_answers; $i++) {
              $correct_text = ' checked="checked"';
              if(!isset($_POST["answer_$i_correct"]))
              {
                $correct_text = "";
              } ?>
              
              <div class="answers_single">
                <div class="answer_number"><button class="button delete_answer">Delete</button> Answer</div>
                <div class="answer_text"><input type="text" class="answer_input" name="answer_<?php echo $i; ?>" id="answer_<?php echo $i; ?>" value="<?php echo $_POST["answer_$i"]; ?>" /></div>
                <div class="answer_correct"><input type="checkbox" class="checkbox_answer_correct" id="answer_<?php echo $i; ?>_correct" name="answer_<?php echo $i; ?>_correct" <?php echo $correct_text; ?> value=1 /></div>
              </div>
          <?php 
          } } ?>
            <!-- end of asnwers -->
            
        </div>
        <a href="#" class="button" id="ef_new_answer_button"><?php _e('Add New Answer!', 'quiz-master-next'); ?></a>
      </div>
    </fieldset>
    <fieldset>
      <legend>Question Options</legend>
      <!--<div id="correct_answer_area" class="row">
        <label class="option_label"><?php _e('Correct Answer Info', 'quiz-master-next'); ?></label>
        <input class="option_input" type="text" name="correct_answer_info" value="" id="correct_answer_info" />
      </div>-->

      <div id="hint_area" class="row">
        <label class="option_label"><?php _e('Hint', 'quiz-master-next'); ?></label>
        <input class="option_input" type="text" name="hint" value="<?php echo $hint; ?>" id="hint"/>
      </div>

      <div id="comment_area" class="row hidden">
        <label class="option_label"><?php _e('Comment Field', 'quiz-master-next'); ?></label>
        <div class="option_input">
          <input type="radio" class="comments_radio" id="commentsRadio1" name="comments" value="0" /><label for="commentsRadio1"><?php _e('Small Text Field', 'quiz-master-next'); ?></label><br>
          <input type="radio" class="comments_radio" id="commentsRadio3" name="comments" value="2" /><label for="commentsRadio3"><?php _e('Large Text Field', 'quiz-master-next'); ?></label><br>
          <input type="radio" class="comments_radio" id="commentsRadio2" name="comments" checked="checked" value="1" /><label for="commentsRadio2"><?php _e('None', 'quiz-master-next'); ?></label><br>
        </div>
      </div>

      <div class="row">
        <label class="option_label"><?php _e('Question Order', 'quiz-master-next'); ?></label>
        <input class="option_input" type="number" step="1" min="1" name="new_question_order" value="<?php echo $question_order; ?>" id="new_question_order"/>
      </div>

      <div id="required_area" class="row">
        <label class="option_label"><?php _e('Required?', 'quiz-master-next'); ?></label>
        <select class="option_input" name="required" id="required">
          <option value="0" <?php echo ($required == 0)? 'selected="selected"':''; ?>><?php _e('Yes', 'quiz-master-next'); ?></option>
          <option value="1" <?php echo ($required == 1)? 'selected="selected"':''; ?>><?php _e('No', 'quiz-master-next'); ?></option>
        </select>
      </div>

      <!--<div id="category_area" class="row">
        <label class="option_label"><?php _e('Category', 'quiz-master-next'); ?></label>
        <div class="option_input">
          <?php
          foreach($qmn_quiz_categories as $category)
          {
            if ($category->category != '')
            {
              ?>
              <input type="radio" class="category_radio" name="new_category" id="new_category<?php echo esc_attr($category->category); ?>" value="<?php echo esc_attr($category->category); ?>">
              <label for="new_category<?php echo esc_attr($category->category); ?>"><?php echo $category->category; ?></label>
              <br />
              <?php
            }
          }
          ?>
          <input type="radio" name="new_category" id="new_category_new" value="new_category"><label for="new_category_new">New: <input type='text' name='new_new_category' value='' /></label>
        </div>
      </div>-->
    </fieldset>
    <input type="hidden" name="new_question_answer_total" id="new_question_answer_total" value="<?php echo $ef_new_question_answer_total; ?>" />
    <input type="hidden" id="question_submission" name="question_submission" value="<?php echo $ef_new_question_text; ?>" />
    <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>" />
    <input type="hidden" name="question_id" id="question_id" value="<?php echo $ef_question_id; ?>" />
    <input type='submit' class='button-primary' value='<?php _e('Create Question', 'quiz-master-next'); ?>' />
  </form>
</div>
<!--Dialogs-->
<div id="delete_dialog" title="Delete Question?" style="display:none;">
  <h3><b><?php _e('Are you sure you want to delete this question?', 'quiz-master-next'); ?></b></h3>
  <form action='' method='post'>
    <input type='hidden' name='delete_question' value='confirmation' />
    <input type='hidden' id='delete_question_id' name='delete_question_id' value='' />
    <input type='hidden' name='quiz_id' value='<?php echo $quiz_id; ?>' />
    <p class='submit'><input type='submit' class='button-primary' value='<?php _e('Delete Question', 'quiz-master-next'); ?>' /></p>
  </form>
</div>

<div id="duplicate_dialog" title="Duplicate Question?" style="display:none;">
  <h3><b><?php _e('Are you sure you want to duplicate this question?', 'quiz-master-next'); ?></b></h3>
  <form action='' method='post'>
    <input type='hidden' name='duplicate_question' value='confirmation' />
    <input type='hidden' id='duplicate_question_id' name='duplicate_question_id' value='' />
    <input type='hidden' name='quiz_id' value='<?php echo $quiz_id; ?>' />
    <p class='submit'><input type='submit' class='button-primary' value='<?php _e ('Duplicate Question', 'quiz-master-next'); ?>' /></p>
  </form>
</div>

<div id="from_other_quiz_dialog" title="Add Question From Other Quiz" style="display:none;">
  <h3><?php _e('Select a question to import into this quiz', 'quiz-master-next'); ?></h3>
  <p>
    <label class="screen-reader-text" for="question_search">Search Questions:</label>
    <input type="search" id="dialog_question_search" name="dialog_question_search" value="">
    <button class="button" id="dialog_question_search_button">Search Questions</button>
  </p>
  <div class="other_quiz_questions">

  </div>
  <form action='' method='post' id="copy_question_form">
    <?php wp_nonce_field('add_question_from_quiz','add_question_from_quiz_nonce'); ?>
    <input type='hidden' id='copy_question_id' name='copy_question_id' value='' />
    <input type='hidden' name='quiz_id' value='<?php echo $quiz_id; ?>' />
  </form>
</div>