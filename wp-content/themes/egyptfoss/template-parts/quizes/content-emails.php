<?php
//options for sender email and name
$email = get_option('mail_from');
$name = get_option('mail_from_name');

?>
<h3 style="text-align: center;"><?php _e('Template Variables', 'quiz-master-next'); ?></h3>
<div class="template_list_holder">
  <div class="template_variable">
    <span class="template_name">%POINT_SCORE%</span> - <?php _e('Score for the quiz when using points', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%AVERAGE_POINT%</span> - <?php _e('The average amount of points user had per question', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%AMOUNT_CORRECT%</span> - <?php _e('The number of correct answers the user had', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%TOTAL_QUESTIONS%</span> - <?php _e('The total number of questions in the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CORRECT_SCORE%</span> - <?php _e('Score for the quiz when using correct answers', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_NAME%</span> - <?php _e('The name the user entered before the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_BUSINESS%</span> - <?php _e('The business the user entered before the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_PHONE%</span> - <?php _e('The phone number the user entered before the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_EMAIL%</span> - <?php _e('The email the user entered before the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%QUIZ_NAME%</span> - <?php _e('The name of the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%QUESTIONS_ANSWERS%</span> - <?php _e('Shows the question, the answer the user provided, and the correct answer', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%COMMENT_SECTION%</span> - <?php _e('The comments the user entered into comment box if enabled', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%TIMER%</span> - <?php _e('The amount of time user spent on quiz in seconds', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%TIMER_MINUTES%</span> - <?php _e('The amount of time user spent on quiz in minutes', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CERTIFICATE_LINK%</span> - <?php _e('The link to the certificate after completing the quiz', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CATEGORY_POINTS%%/CATEGORY_POINTS%</span> - <?php _e('The amount of points a specific category earned.', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <spane class="template_name">%AVERAGE_CATEGORY_POINTS%%/AVERAGE_CATEGORY_POINTS%</span> - <?php _e('The average amount of points a specific category earned.', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CATEGORY_SCORE%%/CATEGORY_SCORE%</span> - <?php _e('The score a specific category earned.', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CATEGORY_AVERAGE_POINTS%</span> - <?php _e('The average points from all categories.', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CATEGORY_AVERAGE_SCORE%</span> - <?php _e('The average score from all categories.', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%QUESTION%</span> - <?php _e('The question that the user answered', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_ANSWER%</span> - <?php _e('The answer the user gave for the question', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CORRECT_ANSWER%</span> - <?php _e('The correct answer for the question', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%USER_COMMENTS%</span> - <?php _e('The comments the user provided in the comment field for the question', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CORRECT_ANSWER_INFO%</span> - <?php _e('Reason why the correct answer is the correct answer', 'quiz-master-next'); ?>
  </div>
  <div class="template_variable">
    <span class="template_name">%CURRENT_DATE%</span> - <?php _e('The Current Date', 'quiz-master-next'); ?>
  </div>
  <?php do_action('qmn_template_variable_list'); ?>
</div>
<div style="clear:both;"></div>
<br />
<br />
<form method="post" action="" name="mlw_quiz_add_email_form">
  <input type='hidden' name='mlw_add_email_page' value='confirmation' />
  <input type='hidden' name='mlw_add_email_quiz_id' value='<?php echo $quiz_id; ?>' />
</form>
<form method="post" action="" name="mlw_quiz_add_admin_email_form">
  <input type='hidden' name='mlw_add_admin_email_page' value='confirmation' />
  <input type='hidden' name='mlw_add_admin_email_quiz_id' value='<?php echo $quiz_id; ?>' />
</form>
<button id="save_email_button" class="button-primary" onclick="javascript: document.mlw_quiz_save_email_form.submit();"><?php _e('Save Email Templates And Settings', 'quiz-master-next'); ?></button>
<form method="post" action="" name="mlw_quiz_save_email_form">
  <table class="form-table">
  <tr valign="top">
    <th scope="row"><label for="sendUserEmail"><?php _e('Send user email upon completion?', 'quiz-master-next'); ?></label></th>
    <td>
        <input type="radio" id="radio5" name="sendUserEmail" <?php if ($mlw_quiz_options->send_user_email == 0) {echo 'checked="checked"';} ?> value='0' /><label for="radio5"><?php _e('Yes', 'quiz-master-next'); ?></label><br>
        <input type="radio" id="radio6" name="sendUserEmail" <?php if ($mlw_quiz_options->send_user_email == 1) {echo 'checked="checked"';} ?> value='1' /><label for="radio6"><?php _e('No', 'quiz-master-next'); ?></label><br>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="sendAdminEmail"><?php _e('Send admin email upon completion?', 'quiz-master-next'); ?></label></th>
    <td>
        <input type="radio" id="radio19" name="sendAdminEmail" <?php if ($mlw_quiz_options->send_admin_email == 0) {echo 'checked="checked"';} ?> value='0' /><label for="radio19"><?php _e('Yes', 'quiz-master-next'); ?></label><br>
        <input type="radio" id="radio20" name="sendAdminEmail" <?php if ($mlw_quiz_options->send_admin_email == 1) {echo 'checked="checked"';} ?> value='1' /><label for="radio20"><?php _e('No', 'quiz-master-next'); ?></label><br>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="adminEmail"><?php _e('What emails should we send the admin email to? Separate emails with a comma.', 'quiz-master-next'); ?></label></th>
    <td><input name="adminEmail" type="text" id="adminEmail" value="<?php echo $mlw_quiz_options->admin_email; ?>" class="regular-text" /></td>
  </tr>
  <tr valign="top" class="hidden">
    <th scope="row"><label for="emailFromText"><?php _e("What is the From Name for the email sent to users and admin?", 'quiz-master-next'); ?></label></th>
    <td>
        <!--<input name="emailFromText" type="text" id="emailFromText" value="<?php echo $from_email_array["from_name"]; ?>" class="regular-text" />!-->
    <input name="emailFromText" type="text" id="emailFromText" value="<?php echo $name; ?>" class="regular-text" />
    </td>
  </tr>
  <tr valign="top" class="hidden">
    <th scope="row"><label for="emailFromAddress"><?php _e("What is the From Email address for the email sent to users and admin?", 'quiz-master-next'); ?></label></th>
    <td>
        <!--<input name="emailFromAddress" type="text" id="emailFromAddress" value="<?php echo $from_email_array["from_email"]; ?>" class="regular-text" /> -->
    <input name="emailFromAddress" type="text" id="emailFromAddress" value="<?php echo $email; ?>" class="regular-text" />
    </td>
  </tr>
  <tr valign="top" class="hidden">
    <th scope="row"><label for="replyToUser"><?php _e('Add user\'s email as Reply-To on admin email?', 'quiz-master-next'); ?></label></th>
    <td>
        <input type="radio" id="radio19" name="replyToUser" <?php checked( $from_email_array["reply_to"], 0 ); ?> value='0' /><label for="radio19"><?php _e('Yes', 'quiz-master-next'); ?></label><br>
        <input type="radio" id="radio20" name="replyToUser" <?php checked( $from_email_array["reply_to"], 1 ); ?> value='1' /><label for="radio20"><?php _e('No', 'quiz-master-next'); ?></label><br>
    </td>
  </tr>
  </table>
  <br />
  <br />
  <h3><?php _e('Email Sent To User', 'quiz-master-next'); ?></h3>
  <a id="new_email_button_top" class="button" href="#" onclick="javascript: document.mlw_quiz_add_email_form.submit();"><?php _e('Add New User Email', 'quiz-master-next'); ?></a>
  <table class="widefat">
    <thead>
      <tr>
        <th>ID</th>
        <th><?php _e('Score Greater Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Score Less Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Subject', 'quiz-master-next'); ?></th>
        <th><?php _e('Email To Send', 'quiz-master-next'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $mlw_each_count = 0;
      $alternate = "";
      foreach($mlw_qmn_user_email_array as $mlw_each)
      {
        if($alternate) $alternate = "";
        else $alternate = " class=\"alternate\"";
        $mlw_each_count += 1;
        if (!isset($mlw_each[3]))
        {
          $mlw_each[3] = "Quiz Results For %QUIZ_NAME%";
        }
        if ($mlw_each[0] == 0 && $mlw_each[1] == 0)
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo "Default";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='user_email_begin_".$mlw_each_count."' name='user_email_begin_".$mlw_each_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='user_email_end_".$mlw_each_count."' name='user_email_end_".$mlw_each_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='user_email_subject_".$mlw_each_count."' name='user_email_subject_".$mlw_each_count."' value='".$mlw_each[3]."' />";
            echo "</td>";
            echo "<td>";
              echo "<textarea cols='80' rows='15' id='user_email_".$mlw_each_count."' name='user_email_".$mlw_each_count."'>".$mlw_each[2]."</textarea>";
            echo "</td>";
          echo "</tr>";
          break;
        }
        else
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo $mlw_each_count."<div><span style='color:green;font-size:12px;'><a onclick=\"\$j('#trying_delete_email_".$mlw_each_count."').show();\">Delete</a></span></div><div style=\"display: none;\" id='trying_delete_email_".$mlw_each_count."'>Are you sure?<br /><a onclick=\"delete_email(".$mlw_each_count.")\">Yes</a>|<a onclick=\"\$j('#trying_delete_email_".$mlw_each_count."').hide();\">No</a></div>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='user_email_begin_".$mlw_each_count."' name='user_email_begin_".$mlw_each_count."' title='What score must the user score better than to see this page' value='".$mlw_each[0]."'/>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='user_email_end_".$mlw_each_count."' name='user_email_end_".$mlw_each_count."' title='What score must the user score worse than to see this page' value='".$mlw_each[1]."' />";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='user_email_subject_".$mlw_each_count."' name='user_email_subject_".$mlw_each_count."' value='".$mlw_each[3]."' />";
            echo "</td>";
            echo "<td>";
              echo "<textarea cols='80' rows='15' id='user_email_".$mlw_each_count."' title='What email will the user be sent' name='user_email_".$mlw_each_count."'>".$mlw_each[2]."</textarea>";
            echo "</td>";
          echo "</tr>";
        }
      }
      ?>
    </tbody>
    <tfoot>
      <tr>
        <th>ID</th>
        <th><?php _e('Score Greater Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Score Less Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Subject', 'quiz-master-next'); ?></th>
        <th><?php _e('Email To Send', 'quiz-master-next'); ?></th>
      </tr>
    </tfoot>
  </table>
  <a id="new_email_button_bottom" class="button" href="#" onclick="javascript: document.mlw_quiz_add_email_form.submit();"><?php _e('Add New User Email', 'quiz-master-next'); ?></a>
  <input type='hidden' name='mlw_save_email_template' value='confirmation' />
  <input type='hidden' name='mlw_email_quiz_id' value='<?php echo $quiz_id; ?>' />
  <input type='hidden' name='mlw_email_template_total' value='<?php echo $mlw_each_count; ?>' />
  <br />
  <br />
  <br />
  <br />
  <h3><?php _e('Email Sent To Admin', 'quiz-master-next'); ?></h3>
  <a id="new_admin_email_button_top" class="button" href="#" onclick="javascript: document.mlw_quiz_add_admin_email_form.submit();"><?php _e('Add New Admin Email', 'quiz-master-next'); ?></a>
  <table class="widefat">
    <thead>
      <tr>
        <th>ID</th>
        <th><?php _e('Score Greater Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Score Less Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Subject', 'quiz-master-next'); ?></th>
        <th><?php _e('Email To Send', 'quiz-master-next'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php
      $mlw_admin_count = 0;
      $alternate = "";
      foreach($mlw_qmn_admin_email_array as $mlw_each)
      {
        if($alternate) $alternate = "";
        else $alternate = " class=\"alternate\"";
        $mlw_admin_count += 1;
        if (!isset($mlw_each["subject"]))
        {
          $mlw_each[3] = "Quiz Results For %QUIZ_NAME%";
        }
        if ($mlw_each["begin_score"] == 0 && $mlw_each["end_score"] == 0)
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo "Default";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='admin_email_begin_".$mlw_admin_count."' name='admin_email_begin_".$mlw_admin_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='admin_email_end_".$mlw_admin_count."' name='admin_email_end_".$mlw_admin_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='admin_email_subject_".$mlw_admin_count."' name='admin_email_subject_".$mlw_admin_count."' value='".$mlw_each["subject"]."' />";
            echo "</td>";
            echo "<td>";
              echo "<textarea cols='80' rows='15' id='admin_email_".$mlw_admin_count."' name='admin_email_".$mlw_admin_count."'>".$mlw_each["message"]."</textarea>";
            echo "</td>";
          echo "</tr>";
          break;
        }
        else
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo $mlw_admin_count."<div><span style='color:green;font-size:12px;'><a onclick=\"\$j('#trying_delete_admin_email_".$mlw_admin_count."').show();\">Delete</a></span></div><div style=\"display: none;\" id='trying_delete_admin_email_".$mlw_admin_count."'>Are you sure?<br /><a onclick=\"delete_admin_email(".$mlw_admin_count.")\">Yes</a>|<a onclick=\"\$j('#trying_delete_admin_email_".$mlw_admin_count."').hide();\">No</a></div>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='admin_email_begin_".$mlw_admin_count."' name='admin_email_begin_".$mlw_admin_count."' title='What score must the user score better than to see this page' value='".$mlw_each["begin_score"]."'/>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='admin_email_end_".$mlw_admin_count."' name='admin_email_end_".$mlw_admin_count."' title='What score must the user score worse than to see this page' value='".$mlw_each["end_score"]."' />";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='admin_email_subject_".$mlw_admin_count."' name='admin_email_subject_".$mlw_admin_count."' value='".$mlw_each["subject"]."' />";
            echo "</td>";
            echo "<td>";
              echo "<textarea cols='80' rows='15' id='admin_email_".$mlw_admin_count."' title='What email will the user be sent' name='admin_email_".$mlw_admin_count."'>".$mlw_each["message"]."</textarea>";
            echo "</td>";
          echo "</tr>";
        }
      }
      ?>
    </tbody>
    <tfoot>
      <tr>
        <th>ID</th>
        <th><?php _e('Score Greater Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Score Less Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Subject', 'quiz-master-next'); ?></th>
        <th><?php _e('Email To Send', 'quiz-master-next'); ?></th>
      </tr>
    </tfoot>
  </table>
  <a id="new_admin_email_button_bottom" class="button" href="#" onclick="javascript: document.mlw_quiz_add_admin_email_form.submit();"><?php _e('Add New Admin Email', 'quiz-master-next'); ?></a>
  <input type='hidden' name='mlw_email_admin_total' value='<?php echo $mlw_admin_count; ?>' />
</form>
<br />
<br />
<button id="save_email_button" class="button-primary" onclick="javascript: document.mlw_quiz_save_email_form.submit();"><?php _e('Save Email Templates And Settings', 'quiz-master-next'); ?></button>
</div>