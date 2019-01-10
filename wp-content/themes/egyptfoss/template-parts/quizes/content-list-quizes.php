
<script type="text/javascript"
  src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
<style>
  td.column-title strong, td.plugin-title strong{
    display: inline-block;
  }
</style>
<?php
  wp_enqueue_script('ef_list_quizes', get_template_directory_uri() . '/js/quizes/list-quizes.js' );
?>

<div class="wrap qsm-quizes-page">
  <h1><?php _e('Quizzes/Surveys', 'quiz-master-next'); ?>
      <a id="new_quiz_button" href="javascript:();" class="add-new-h2"><?php _e('Add New', 'quiz-master-next'); ?></a>
  </h1>
  <?php $mlwQuizMasterNext->alertManager->showAlerts(); ?>
  <div class="qsm-quizzes-page-content">
    <div class="<?php if ( get_option( 'mlw_advert_shows' ) != 'false' ) { echo 'qsm-quiz-page-wrapper-with-ads'; } else { echo 'qsm-quiz-page-wrapper'; } ?>">
      <div class="tablenav top">
        <div class="tablenav-pages">
          <span class="displaying-num"><?php echo sprintf(_n('One quiz or survey', '%s quizzes or surveys', $mlw_qmn_quiz_count, 'quiz-master-next'), number_format_i18n($mlw_qmn_quiz_count)); ?></span>
          <span class="pagination-links">
            <?php
            $mlw_qmn_previous_page = 0;
            $mlw_current_page = $mlw_qmn_quiz_page+1;
            $mlw_total_pages = ceil($mlw_qmn_quiz_count/$mlw_qmn_table_limit);
            if( $mlw_qmn_quiz_page > 0 )
            {
                $mlw_qmn_previous_page = $mlw_qmn_quiz_page - 2;
                echo "<a class=\"prev-page\" title=\"Go to the previous page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_previous_page\"><</a>";
              echo "<span class=\"paging-input\">$mlw_current_page of $mlw_total_pages</span>";
                if( $mlw_qmn_quiz_left > $mlw_qmn_table_limit )
                {
                echo "<a class=\"next-page\" title=\"Go to the next page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_quiz_page\">></a>";
                }
              else
              {
                echo "<a class=\"next-page disabled\" title=\"Go to the next page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_quiz_page\">></a>";
                }
            }
            else if( $mlw_qmn_quiz_page == 0 )
            {
               if( $mlw_qmn_quiz_left > $mlw_qmn_table_limit )
               {
                echo "<a class=\"prev-page disabled\" title=\"Go to the previous page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_previous_page\"><</a>";
                echo "<span class=\"paging-input\">$mlw_current_page of $mlw_total_pages</span>";
                echo "<a class=\"next-page\" title=\"Go to the next page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_quiz_page\">></a>";
               }
            }
            else if( $mlw_qmn_quiz_left < $mlw_qmn_table_limit )
            {
               $mlw_qmn_previous_page = $mlw_qmn_quiz_page - 2;
               echo "<a class=\"prev-page\" title=\"Go to the previous page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_previous_page\"><</a>";
              echo "<span class=\"paging-input\">$mlw_current_page of $mlw_total_pages</span>";
              echo "<a class=\"next-page disabled\" title=\"Go to the next page\" href=\"?page=ef_mlw_list_quizzes&&mlw_quiz_page=$mlw_qmn_quiz_page\">></a>";
            }
            ?>
          </span>
          <br class="clear">
        </div>
      </div>
      <table class="widefat">
        <thead>
          <tr>
            <th>ID</th>
            <th><?php _e('Name', 'quiz-master-next'); ?></th>
            <th><?php _e('URL', 'quiz-master-next'); ?></th>
            <!--<th><?php _e('Shortcode', 'quiz-master-next'); ?></th>-->
            <!--<th><?php _e('Leaderboard Shortcode', 'quiz-master-next'); ?></th>-->
            <th><?php _e('Views', 'quiz-master-next'); ?></th>
            <th><?php _e('Taken', 'quiz-master-next'); ?></th>
            <th><?php _e('Last Modified', 'quiz-master-next'); ?></th>
          </tr>
        </thead>
        <tbody id="the-list">
          <?php
          $quotes_list = "";
          $display = "";
          $alternate = "";
          foreach($mlw_quiz_data as $mlw_quiz_info) {
            if($alternate) $alternate = "";
            else $alternate = " class=\"alternate\"";
            $quotes_list .= "<tr{$alternate}>";
            $quotes_list .= "<td>" . $mlw_quiz_info->quiz_id . "</td>";
            $quotes_list .= "<td class='post-title column-title'><strong>" . esc_html($mlw_quiz_info->quiz_name)." — ". ucfirst($post_to_quiz_array[$mlw_quiz_info->quiz_id]['status']) ."</strong> <a class='qsm-edit-name' onclick=\"editQuizName('".$mlw_quiz_info->quiz_id."','".esc_js($mlw_quiz_info->quiz_name)."')\" href='javascript:();'>(".__('Edit Name', 'quiz-master-next').")</a>";
            $quotes_list .= "<div class=\"row-actions\">
            <a class='qsm-action-link' href='post.php?post=".$post_to_quiz_array[$mlw_quiz_info->quiz_id]['id']."&action=edit'>Edit Quiz</a> |
            <a class='qsm-action-link' href='admin.php?page=mlw_quiz_options&&quiz_id=".$mlw_quiz_info->quiz_id."'>".__('Quiz Settings', 'quiz-master-next')."</a>
             | <a class='qsm-action-link' href='admin.php?page=ef_mlw_quiz_results&&quiz_id=".$mlw_quiz_info->quiz_id."'>".__('Results', 'quiz-master-next')."</a>
             | <a href='javascript:();' class='qsm-action-link' onclick=\"duplicateQuiz('".$mlw_quiz_info->quiz_id."','".esc_js($mlw_quiz_info->quiz_name)."')\">".__('Duplicate', 'quiz-master-next')."</a>
             | <a class='qsm-action-link qsm-action-link-delete' onclick=\"deleteQuiz('".$mlw_quiz_info->quiz_id."','".esc_js($mlw_quiz_info->quiz_name)."')\" href='javascript:();'>".__('Delete', 'quiz-master-next')."</a>
            </div></td>";
            if (isset($post_to_quiz_array[$mlw_quiz_info->quiz_id]))
            {
              $quotes_list .= "<td>
              <a href='".$post_to_quiz_array[$mlw_quiz_info->quiz_id]['link']."'>" . __( 'View Quiz/Survey', 'quiz-master-next' ) . "</a>
              <!--<div class=\"row-actions\"><a class='linkOptions' href='post.php?post=".$post_to_quiz_array[$mlw_quiz_info->quiz_id]['id']."&action=edit'>Edit Post Settings</a>--></a>
              </td>";
            }
            else
            {
              $quotes_list .= "<td></td>";
            }
            //$quotes_list .= "<td>[mlw_quizmaster quiz=".$mlw_quiz_info->quiz_id."]</td>";
            //$quotes_list .= "<td>[mlw_quizmaster_leaderboard mlw_quiz=".$mlw_quiz_info->quiz_id."]</td>";
            $quotes_list .= "<td>" . $mlw_quiz_info->quiz_views . "</td>";
            $quotes_list .= "<td>" . $mlw_quiz_info->quiz_taken ."</td>";
            $quotes_list .= "<td>" . $mlw_quiz_info->last_activity ."</td>";
            $quotes_list .= "</tr>";
          }
          echo $quotes_list; ?>
        </tbody>
        <tfoot>
          <tr>
            <th>ID</th>
            <th><?php _e('Name', 'quiz-master-next'); ?></th>
            <th><?php _e('URL', 'quiz-master-next'); ?></th>
            <!--<th><?php _e('Shortcode', 'quiz-master-next'); ?></th>-->
            <!--<th><?php _e('Leaderboard Shortcode', 'quiz-master-next'); ?></th>-->
            <th><?php _e('Views', 'quiz-master-next'); ?></th>
            <th><?php _e('Taken', 'quiz-master-next'); ?></th>
            <th><?php _e('Last Modified', 'quiz-master-next'); ?></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  <!--Dialogs-->

  <!--New Quiz Dialog-->
  <div id="new_quiz_dialog" title="Create New Quiz Or Survey" style="display:none;">
    <form action="" method="post" class="qsm-dialog-form" id="ef_create_quiz">
      <input type='hidden' name='create_quiz' value='confirmation' />
      <h3><?php _e('Create New Quiz Or Survey', 'quiz-master-next'); ?></h3>
      <label><?php _e('Name', 'quiz-master-next'); ?></label><input type="text" name="quiz_name" value="" />
      <p class='submit'><input type='submit' onclick="jQuery(this).attr('disabled', 'disabled');jQuery('#ef_create_quiz').submit();" class='button-primary' value='<?php _e('Create', 'quiz-master-next'); ?>' /></p>
    </form>
  </div>

  <!--Edit Quiz Name Dialog-->
  <div id="edit_dialog" title="Edit Name" style="display:none;">
    <form action='' method='post' class="qsm-dialog-form">
      <label><?php _e('Name', 'quiz-master-next'); ?></label>
      <input type="text" id="edit_quiz_name" name="edit_quiz_name" />
      <input type="hidden" id="edit_quiz_id" name="edit_quiz_id" />
      <input type='hidden' name='quiz_name_editted' value='confirmation' />
      <p class='submit'><input type='submit' class='button-primary' value='<?php _e('Edit', 'quiz-master-next'); ?>' /></p>
    </form>
  </div>

  <!--Duplicate Quiz Dialog-->
  <div id="duplicate_dialog" title="Duplicate Quiz Or Survey" style="display:none;">
    <form action='' method='post' class="qsm-dialog-form">
      <label for="duplicate_questions"><?php _e('Duplicate questions also?', 'quiz-master-next'); ?></label><input type="checkbox" name="duplicate_questions" id="duplicate_questions"/><br />
      <br />
      <label for="duplicate_new_quiz_name"><?php _e('Name Of New Quiz Or Survey:', 'quiz-master-next'); ?></label><input type="text" id="duplicate_new_quiz_name" name="duplicate_new_quiz_name" />
      <input type="hidden" id="duplicate_quiz_id" name="duplicate_quiz_id" />
      <input type='hidden' name='duplicate_quiz' value='confirmation' />
      <p class='submit'><input type='submit' class='button-primary' value='<?php _e('Duplicate', 'quiz-master-next'); ?>' /></p>
    </form>
  </div>

  <!--Delete Quiz Dialog-->
  <div id="delete_dialog" title="Delete Quiz Or Survey?" style="display:none;">
  <form action='' method='post' class="qsm-dialog-form">
    <h3><b><?php _e('Are you sure you want to delete this quiz or survey?', 'quiz-master-next'); ?></b></h3>
    <input type='hidden' name='delete_quiz' value='confirmation' />
    <input type='hidden' id='quiz_id' name='quiz_id' value='' />
    <input type='hidden' id='delete_quiz_name' name='delete_quiz_name' value='' />
    <p class='submit'><input type='submit' class='button-primary' value='<?php _e('Delete', 'quiz-master-next'); ?>' /></p>
  </form>
  </div>
</div>