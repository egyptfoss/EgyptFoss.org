<div id="tabs-6" class="mlw_tab_content">
  <script>
    var $j = jQuery.noConflict();
    // increase the default animation speed to exaggerate the effect
    $j.fx.speeds._default = 1000;
    function delete_landing(id)
    {
      var qmn_results_editor = tinyMCE.get('message_after_'+id);
      if (qmn_results_editor)
      {
        tinyMCE.get('message_after_'+id).setContent('Delete');
      }
      else
      {
        document.getElementById('message_after_'+id).value = "Delete";
      }
      document.mlw_quiz_save_landing_form.submit();
    }
  </script>
  <!--<h3 style="text-align: center;"><?php _e("Template Variables", 'quiz-master-next'); ?></h3> -->
  <div class="template_list_holder">
    <!--<div class="template_variable">
      <span class="template_name">%POINT_SCORE%</span> - <?php _e('Score for the quiz when using points', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%AVERAGE_POINT%</span> - <?php _e('The average amount of points user had per question', 'quiz-master-next'); ?>
    </div>-->
    <!--<div class="template_variable">
      <span class="template_name">%AMOUNT_CORRECT%</span> - <?php _e('The number of correct answers the user had', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%TOTAL_QUESTIONS%</span> - <?php _e('The total number of questions in the quiz', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%CORRECT_SCORE%</span> - <?php _e('Score for the quiz when using correct answers', 'quiz-master-next'); ?>
    </div>-->
    <!--<div class="template_variable">
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
    </div>-->
    <!--<div class="template_variable">
      <span class="template_name">%QUESTIONS_ANSWERS%</span> - <?php _e('Shows the question, the answer the user provided, and the correct answer', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%COMMENT_SECTION%</span> - <?php _e('The comments the user entered into comment box if enabled', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%TIMER_MINUTES%</span> - <?php _e('The amount of time user spent taking quiz in minutes', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%TIMER%</span> - <?php _e('The amount of time user spent taking quiz in seconds', 'quiz-master-next'); ?>
    </div>-->
    <!--<div class="template_variable">
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
      <span class="template_name">%FACEBOOK_SHARE%</span> - <?php _e('Displays button to share on Facebook.', 'quiz-master-next'); ?>
    </div>
    <div class="template_variable">
      <span class="template_name">%TWITTER_SHARE%</span> - <?php _e('Displays button to share on Twitter.', 'quiz-master-next'); ?>
    </div>-->
    <?php do_action('qmn_template_variable_list'); ?>
  </div>
  <div style="clear:both;"></div>
  <button id="save_landing_button" class="button-primary" onclick="javascript: document.mlw_quiz_save_landing_form.submit();"><?php _e('Save Results Pages', 'quiz-master-next'); ?></button>
  <button id="new_landing_button" class="button" onclick="javascript: document.mlw_quiz_add_landing_form.submit();"><?php _e('Add New Results Page', 'quiz-master-next'); ?></button>
  <form method="post" action="" name="mlw_quiz_save_landing_form" style=" display:inline!important;">
  <table class="widefat">
    <thead>
      <tr>
        <th>ID</th>
        <th><?php _e('Score Greater Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Score Less Than Or Equal To', 'quiz-master-next'); ?></th>
        <th><?php _e('Results Page Shown', 'quiz-master-next'); ?></th>
        <!--<th><?php _e('Redirect URL (Beta)', 'quiz-master-next'); ?></th>-->
      </tr>
    </thead>
    <tbody>
      <?php
      $mlw_each_count = 0;
      $alternate = "";
      foreach($mlw_message_after_array as $mlw_each)
      {
        if($alternate) $alternate = "";
        else $alternate = " class=\"alternate\"";
        $mlw_each_count += 1;
        if ($mlw_each[0] == 0 && $mlw_each[1] == 0)
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo "Default";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='message_after_begin_".$mlw_each_count."' name='message_after_begin_".$mlw_each_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              echo "<input type='hidden' id='message_after_end_".$mlw_each_count."' name='message_after_end_".$mlw_each_count."' value='0'/>-";
            echo "</td>";
            echo "<td>";
              wp_editor( htmlspecialchars_decode($mlw_each[2], ENT_QUOTES), "message_after_".$mlw_each_count );
              //echo "<textarea cols='80' rows='15' id='message_after_".$mlw_each_count."' name='message_after_".$mlw_each_count."'>".$mlw_each[2]."</textarea>";
            echo "</td>";
            //echo "<td>";
              //echo "<input type='text' id='redirect_".$mlw_each_count."' name='redirect_".$mlw_each_count."' value='".esc_url($mlw_each["redirect_url"])."'/>";
            //echo "</td>";
          echo "</tr>";
          break;
        }
        else
        {
          echo "<tr{$alternate}>";
            echo "<td>";
              echo $mlw_each_count."<div><span style='color:green;font-size:12px;'><a onclick=\"\$j('#trying_delete_".$mlw_each_count."').show();\">".__('Delete', 'quiz-master-next')."</a></span></div><div style=\"display: none;\" id='trying_delete_".$mlw_each_count."'>".__('Are you sure?', 'quiz-master-next')."<br /><a onclick=\"delete_landing(".$mlw_each_count.")\">".__('Yes', 'quiz-master-next')."</a>|<a onclick=\"\$j('#trying_delete_".$mlw_each_count."').hide();\">".__('No', 'quiz-master-next')."</a></div>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='message_after_begin_".$mlw_each_count."' name='message_after_begin_".$mlw_each_count."' title='What score must the user score better than to see this page' value='".$mlw_each[0]."'/>";
            echo "</td>";
            echo "<td>";
              echo "<input type='text' id='message_after_end_".$mlw_each_count."' name='message_after_end_".$mlw_each_count."' title='What score must the user score worse than to see this page' value='".$mlw_each[1]."' />";
            echo "</td>";
            echo "<td>";
              wp_editor( htmlspecialchars_decode($mlw_each[2], ENT_QUOTES), "message_after_".$mlw_each_count );
              //echo "<textarea cols='80' rows='15' id='message_after_".$mlw_each_count."' title='What text will the user see when reaching this page' name='message_after_".$mlw_each_count."'>".$mlw_each[2]."</textarea>";
            echo "</td>";
           // echo "<td>";
              //echo "<input type='text' id='redirect_".$mlw_each_count."' name='redirect_".$mlw_each_count."' value='".esc_url($mlw_each["redirect_url"])."'/>";
            //echo "</td>";
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
        <th><?php _e('Results Page Shown', 'quiz-master-next'); ?></th>
        <!--<th><?php _e('Redirect URL (Beta)', 'quiz-master-next'); ?></th>-->
      </tr>
    </tfoot>
  </table>
  <input type='hidden' name='mlw_save_landing_pages' value='confirmation' />
  <input type='hidden' name='mlw_landing_quiz_id' value='<?php echo $quiz_id; ?>' />
  <input type='hidden' name='mlw_landing_page_total' value='<?php echo $mlw_each_count; ?>' />
  <button id="save_landing_button" class="button-primary" onclick="javascript: document.mlw_quiz_save_landing_form.submit();"><?php _e('Save Results Pages', 'quiz-master-next'); ?></button>
  </form>
  <form method="post" action="" name="mlw_quiz_add_landing_form" style=" display:inline!important;">
    <input type='hidden' name='mlw_add_landing_page' value='confirmation' />
    <input type='hidden' name='mlw_add_landing_quiz_id' value='<?php echo $quiz_id; ?>' />
    <button id="new_landing_button" class="button" onclick="javascript: document.mlw_quiz_add_landing_form.submit();"><?php _e('Add New Results Page', 'quiz-master-next'); ?></button>
  </form>
</div>