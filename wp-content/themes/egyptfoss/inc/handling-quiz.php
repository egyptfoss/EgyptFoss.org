<?php

define("ef_awareness_quiz_per_page", 10);

/*
* START OF QUIZ ADMIN
*/

include_once "handling-quiz-results.php";
include_once "handling-quiz-stats.php";
include_once "handling-quiz-results-page.php";

//Remove Certificate tab
if(is_admin())
{
  if(isset($_GET['page']) && $_GET['page'] == "ef_mlw_quiz_result_details" )
  {
    echo "<style>.nav-tab-wrapper .nav-tab:last-child  { display:none; }</style>";
  }

  if(isset($_GET['action']) && isset($_GET["post"])
      && $_GET['action'] == "edit" && get_post_type($_GET["post"]) == "quiz")
  {
    //validate that post is of type quiz
    echo "<style>#post-translations, .page-title-action, #title { display:none; } </style>";
    echo "<script>window.onload = changeTitle;  function changeTitle() {var title = document.getElementById('title').value;document.getElementsByClassName(\"wrap\")[0].children[0].innerHTML =\"<h1>Edit Quiz (\"+title+\")</h1>\";} </script>";
  }
}

// highlight the proper top level menu
function ef_set_taxonomy_parent($parent_file) {
	global $current_screen;
	$taxonomy = $current_screen->taxonomy;
	if ($taxonomy == 'quiz_categories')
  {
		$parent_file = __FILE__;
  }
	return $parent_file;
}
add_action('parent_file', 'ef_set_taxonomy_parent');

//Change functions in Quiz Menu
add_action('admin_menu','ef_override_quiz_menu',999);
function ef_override_quiz_menu()
{
  //listing
  remove_menu_page( 'quiz-master-next/mlw_quizmaster2.php');
  add_menu_page('Quiz And Survey Master', __('Awareness Center', 'quiz-master-next'), 'moderate_comments', 'ef_mlw_list_quizzes', 'ef_listing_quizes_surverys', 'dashicons-feedback',11);

  //single page
  remove_submenu_page( 'quiz-master-next/mlw_quizmaster2.php', 'mlw_quiz_options' );
  add_submenu_page('ef_quiz-master-next/mlw_quizmaster2.php', __('Settings', 'quiz-master-next'), __('Settings', 'quiz-master-next'), 'moderate_comments', 'mlw_quiz_options', 'ef_mlw_generate_quiz_options');

  //Other tabs
  add_submenu_page( 'ef_mlw_list_quizzes', 'Quiz Categories', 'Quiz Categories', 'edit_others_posts', 'edit-tags.php?taxonomy=quiz_categories');
  //add_submenu_page(__FILE__, __('Settings', 'quiz-master-next'), __('Settings', 'quiz-master-next'), 'moderate_comments', 'mlw_quiz_options', 'mlw_generate_quiz_options');
  //add_submenu_page(__FILE__, __('Results', 'quiz-master-next'), __('Results', 'quiz-master-next'), 'moderate_comments', 'mlw_quiz_results', 'mlw_generate_quiz_results');
  add_submenu_page('ef_mlw_list_quizzes', __('Results', 'quiz-master-next'), __('Results', 'quiz-master-next'), 'moderate_comments', 'ef_mlw_quiz_results', 'ef_mlw_generate_quiz_results');
  add_submenu_page('_doesnt_exist', __('Result Details', 'quiz-master-next'), __('Result Details', 'quiz-master-next'), 'moderate_comments', 'ef_mlw_quiz_result_details', 'ef_mlw_generate_result_details');
  //remove_submenu_page( 'ef_mlw_list_quizzes', 'ef_mlw_quiz_result_details' );
  //add_submenu_page(__FILE__, __('Settings', 'quiz-master-next'), __('Settings', 'quiz-master-next'), 'manage_options', 'qmn_global_settings', array('QMNGlobalSettingsPage', 'display_page'));
  //add_submenu_page(__FILE__, __('Tools', 'quiz-master-next'), __('Tools', 'quiz-master-next'), 'manage_options', 'mlw_quiz_tools', 'mlw_generate_quiz_tools');

  add_submenu_page('ef_mlw_list_quizzes', __('Stats', 'quiz-master-next'), __('Stats', 'quiz-master-next'), 'moderate_comments', 'ef_qmn_stats', 'ef_qmn_generate_stats_page');

}

//Add polylang to quiz
function ef_polylang_quizes($args, $post_type){
  if($post_type == "quiz")
  {
    $args["public"] = true;
    $args["rewrite"] = array('slug' => 'awareness-center');
  }

  return $args;
}
add_filter( 'register_post_type_args','ef_polylang_quizes',10, 2 );

function load_custom_wp_admin_style() {
  wp_enqueue_style( 'qmn_admin_style', plugins_url( '/quiz-master-next/css/qmn_admin.css') );
	wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-tabs' );
  wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-effects-blind' );
	wp_enqueue_script( 'jquery-effects-explode' );

  wp_enqueue_script( 'custom-single-question',get_template_directory_uri() . '/js/quizes/single-question.js', array( 'jquery', 'qmn_admin_question_js' ) );

	wp_enqueue_style( 'qmn_jquery_redmond_theme', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/redmond/jquery-ui.css' );
}

// Changes in saving of Quiz
function ef_qmn_quiz_created($quiz)
{
  global $wpdb;
  //Update post status
  $sql = "SELECT ID from {$wpdb->prefix}posts where post_content like '%quiz=$quiz]%'";
  $post_quiz = $wpdb->get_col($sql);
  if($post_quiz)
  {
    $wpdb->update($wpdb->prefix.'posts',array( 'post_status' => 'pending' ),array( 'ID' => $post_quiz[0] ));
  }

  $fromName = get_option("mail_from_name");
  $success_percentage = returnAwarenessCenterMinimumScore();
  $results = array(
      array(
          $success_percentage , 100, "<img src=\"".get_template_directory_uri()."/img/positive.png\" /><h3>Congratulations! You passed the quiz successfuly</h3>"
      ),
      array(
          0 , ($success_percentage - 1), "<img src=\"".get_template_directory_uri()."/img/negative.png\" /><h3>Sorry! You didn't pass the quiz.</h3>"
      ),
      array(
          0 , 0, "<img src=\"".get_template_directory_uri()."/img/positive.png\" /><h3>Congratulations! You passed the quiz successfuly</h3>"
      )
  );

  //update quiz parameters
  $wpdb->update($wpdb->prefix.'mlw_quizzes',array( 'loggedin_user_contact' => 1, "email_from_text" => $fromName,
      'send_admin_email' => 1, 'send_user_email' => 1, 'question_numbering' => 1
      ,'message_after' =>  json_encode($results),
      'question_answer_template' => '%QUESTION%<br /> Answer Provided: %USER_ANSWER%<br /> Correct Answer: %CORRECT_ANSWER%<br /> '/*,'require_log_in' => 1 */),array( 'quiz_id' => $quiz ));

  return $quiz;
}
add_action('qmn_quiz_created','ef_qmn_quiz_created',10);

//List Quizes/Surveys
function ef_listing_quizes_surverys()
{
	if ( !current_user_can('moderate_comments') )
	{
		return;
	}
	global $wpdb;
	global $mlwQuizMasterNext;
	$table_name = $wpdb->prefix . "mlw_quizzes";

	//Create new quiz
	if ( isset( $_POST["create_quiz"] ) && $_POST["create_quiz"] == "confirmation" )
	{
		$quiz_name = htmlspecialchars(stripslashes( $_POST["quiz_name"] ), ENT_QUOTES);
		$mlwQuizMasterNext->quizCreator->create_quiz($quiz_name);
	}

	//Delete quiz
	if (isset( $_POST["delete_quiz"] ) && $_POST["delete_quiz"] == "confirmation")
	{
		$mlw_quiz_id = intval($_POST["quiz_id"]);
		$quiz_name = sanitize_text_field( $_POST["delete_quiz_name"] );
		$mlwQuizMasterNext->quizCreator->delete_quiz($mlw_quiz_id, $quiz_name);

    //Remove post translation relation
    $sql = "select post_id from {$wpdb->prefix}postmeta as pmeta where pmeta.meta_key='quiz_id' and meta_value ='{$mlw_quiz_id}'";
    $post = $wpdb->get_col($sql);
    $post_id = $post[0];
    $sql = "select term_taxonomy_id,term_id from {$wpdb->prefix}term_taxonomy "
    . " where taxonomy='post_translations' and description like'%{$post_id}%'";
    $post = $wpdb->get_row($sql);
    if($post)
    {
      $term_tax_id = $post->term_taxonomy_id;
      $term_id = $post->term_id;

      //delete the 3 tables term_taxnomy term_relations terms
      $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id = %d",$term_tax_id));
      $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}term_taxonomy WHERE term_taxonomy_id = %d",$term_tax_id));
      $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}terms WHERE term_id = %d",$term_id));

      //update translated_id in postmeta
      $language = get_post_meta($post_id, "language", true);
      $unserialized_language = unserialize($language);
      $other_language_post_id = $unserialized_language["translated_id"];
      if($other_language_post_id > 0)
      {
        $other_language = unserialize(get_post_meta($other_language_post_id, "language", true));
        $edited_language = array(
            "slug" => $other_language['slug'],
            "translated_id" => 0
        );
        $wpdb->update($wpdb->prefix.'postmeta',array( 'meta_value' => serialize($edited_language) ),array( 'post_id' => $other_language_post_id,
            'meta_key' => 'language'));
      }
    }
	}

	//Edit Quiz Name
	if (isset($_POST["quiz_name_editted"]) && $_POST["quiz_name_editted"] == "confirmation")
	{
		$mlw_edit_quiz_id = intval($_POST["edit_quiz_id"]);
		$mlw_edit_quiz_name = htmlspecialchars( stripslashes( $_POST["edit_quiz_name"] ), ENT_QUOTES);
		$mlwQuizMasterNext->quizCreator->edit_quiz_name($mlw_edit_quiz_id, $mlw_edit_quiz_name);

    //Update post title
    $sqlPostID = "select post_id from {$wpdb->prefix}postmeta where meta_key = 'quiz_id' and meta_value = $mlw_edit_quiz_id";
    $post_id = $wpdb->get_col($sqlPostID);
    $wpdb->update($wpdb->prefix.'posts',array( 'post_title' => $mlw_edit_quiz_name ),array( 'ID' => $post_id[0] ));
	}

	//Duplicate Quiz
	if (isset($_POST["duplicate_quiz"]) && $_POST["duplicate_quiz"] == "confirmation")
	{
		$mlw_duplicate_quiz_id = intval($_POST["duplicate_quiz_id"]);
		$mlw_duplicate_quiz_name = htmlspecialchars($_POST["duplicate_new_quiz_name"], ENT_QUOTES);
		//$mlwQuizMasterNext->quizCreator->duplicate_quiz($mlw_duplicate_quiz_id, $mlw_duplicate_quiz_name, isset($_POST["duplicate_questions"]));
    ef_duplicate_quiz(-1,$mlw_duplicate_quiz_id, $mlw_duplicate_quiz_name, true);
	}

	//Retrieve list of quizzes
	global $wpdb;
	$mlw_qmn_table_limit = 25;
	$mlw_qmn_quiz_count = $wpdb->get_var( "SELECT COUNT(quiz_id) FROM " . $wpdb->prefix . "mlw_quizzes WHERE deleted='0'" );

	if( isset($_GET{'mlw_quiz_page'} ) )
	{
	   $mlw_qmn_quiz_page = $_GET{'mlw_quiz_page'} + 1;
	   $mlw_qmn_quiz_begin = $mlw_qmn_table_limit * $mlw_qmn_quiz_page ;
	}
	else
	{
	   $mlw_qmn_quiz_page = 0;
	   $mlw_qmn_quiz_begin = 0;
	}
	$mlw_qmn_quiz_left = $mlw_qmn_quiz_count - ($mlw_qmn_quiz_page * $mlw_qmn_table_limit);
	$mlw_quiz_data = $wpdb->get_results( $wpdb->prepare( "SELECT quiz_id, quiz_name, quiz_views, quiz_taken, last_activity
		FROM " . $wpdb->prefix . "mlw_quizzes WHERE deleted='0'
		ORDER BY quiz_id DESC LIMIT %d, %d", $mlw_qmn_quiz_begin, $mlw_qmn_table_limit ) );

	$post_to_quiz_array = array();
	/*$my_query = new WP_Query( array('post_type' => 'quiz') );
  //$my_query = $wpdb->get_results("select ID from {$wpdb->prefix}posts where post_type ='quiz'");
	if( $my_query->have_posts() )
	{
	  while( $my_query->have_posts() )
		{
	    $my_query->the_post();
			$post_to_quiz_array[get_post_meta( get_the_ID(), 'quiz_id', true )] = array(
				'link' => get_permalink(),
				'id' => get_the_ID()
			);
	  }
	}*/

  $post_results = $wpdb->get_results("select ID,guid,post_status from {$wpdb->prefix}posts where post_type ='quiz'");
  foreach($post_results as $result)
  {
    $post_to_quiz_array[get_post_meta( $result->ID, 'quiz_id', true )] = array(
      'link' => $result->guid,
      'id' => $result->ID,
      'status' => $result->post_status
    );
  }

	wp_reset_postdata();
  load_custom_wp_admin_style();
  include(locate_template('template-parts/quizes/content-list-quizes.php'));
}
//add_action('toplevel_page_quiz-master-next/mlw_quizmaster2' ,'ef_listing_quizes_surverys');


function ef_mlw_generate_quiz_options()
{
	if ( !current_user_can('moderate_comments') )
	{
		return;
	}
	global $wpdb;
	global $mlwQuizMasterNext;
	$tab_array = $mlwQuizMasterNext->pluginHelper->get_settings_tabs();
  // this is the logic of removing the tabs
  foreach($tab_array as $key=>$tab)
  {
    if($tab["function"] == "mlw_options_leaderboard_tab_content"
      || $tab["function"] == "mlw_options_certificate_tab_content"
      || $tab["function"] == "mlw_options_tools_tab_content"
      || $tab["function"] == "mlw_options_styling_tab_content"
      || $tab["function"] == "mlw_options_emails_tab_content")
    {
      unset($tab_array[$key]);
    }
  }
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'questions';
	$quiz_id = intval($_GET["quiz_id"]);
	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}

	?>

	<script type="text/javascript"
	  src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
	</script>
	<?php
	load_custom_wp_admin_style();
	?>
	<style>
		.mlw_tab_content
		{
			padding: 20px 20px 20px 20px;
			margin: 20px 20px 20px 20px;
		}
	</style>
	<div class="wrap">
	<div class='mlw_quiz_options'>
	<h1><?php
	/* translators: The %s corresponds to the name of the quiz */
	echo sprintf(__('Quiz Settings For %s', 'quiz-master-next'), $mlw_quiz_options->quiz_name);
	?></h1>
	<?php
	ob_start();
	if ($quiz_id != "")
	{
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach($tab_array as $tab)
			{
				$active_class = '';
				if ($active_tab == $tab['slug'])
				{
					$active_class = 'nav-tab-active';
				}
				echo "<a href=\"?page=mlw_quiz_options&quiz_id=$quiz_id&tab=".$tab['slug']."\" class=\"nav-tab $active_class\">".$tab['title']."</a>";
			}
			?>
		</h2>
		<div class="mlw_tab_content">
			<?php
				foreach($tab_array as $tab)
				{
					if ($active_tab == $tab['slug'])
					{
            switch($tab['slug']) {
              case 'questions':
                $tab['function'] = "ef_quiz_questions_page";
                break;
              case 'text':
                $tab['function'] = "ef_quiz_text_page";
                break;
              case 'options':
                $tab['function'] = "ef_quiz_options_page";
                break;
              case 'emails':
                $tab['function'] = "ef_quiz_email_page";
                break;
              case 'preview':
                $tab['function'] = "ef_quiz_preview_page";
                break;
              case 'results-pages':
                $tab['function'] = "ef_mlw_generate_results_page";
                break;
            }
						call_user_func($tab['function']);
					}
				}
			?>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
		<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
		<strong><?php _e('Error!', 'quiz-master-next'); ?></strong> <?php _e('Please go to the quizzes page and click on the Edit link from the quiz you wish to edit.', 'quiz-master-next'); ?></p>
		</div>
		<?php
	}
	$mlw_output = ob_get_contents();
	ob_end_clean();
	$mlwQuizMasterNext->alertManager->showAlerts();
	//echo mlw_qmn_show_adverts();
	echo $mlw_output;
	?>
	</div>
	</div>
<?php

}
//add_action("quizzessurveys_page_mlw_quiz_options",'ef_mlw_generate_quiz_options');

//List Results Page
function ef_mlw_generate_results_page()
{
  global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];
	//Check to add new results page
	if (isset($_POST["mlw_add_landing_page"]) && $_POST["mlw_add_landing_page"] == "confirmation")
	{
		//Function variables
		$mlw_qmn_landing_id = intval($_POST["mlw_add_landing_quiz_id"]);
		$mlw_qmn_message_after = $wpdb->get_var( $wpdb->prepare( "SELECT message_after FROM ".$wpdb->prefix."mlw_quizzes WHERE quiz_id=%d", $mlw_qmn_landing_id ) );
		//Load message_after and check if it is array already. If not, turn it into one
		if (is_json($mlw_qmn_message_after) && is_array(json_decode($mlw_qmn_message_after, true)))
		{
			$mlw_qmn_landing_array = json_decode($mlw_qmn_message_after, true);
			$mlw_new_landing_array = array(0, 100, 'Enter Your Text Here', "redirect_url" => '');
			array_unshift($mlw_qmn_landing_array , $mlw_new_landing_array);
			$mlw_qmn_landing_array = json_encode($mlw_qmn_landing_array);

		}
		else
		{
			$mlw_qmn_landing_array = array(array(0, 0, $mlw_qmn_message_after));
			$mlw_new_landing_array = array(0, 100, 'Enter Your Text Here', "redirect_url" => '');
			array_unshift($mlw_qmn_landing_array , $mlw_new_landing_array);
			$mlw_qmn_landing_array = json_encode($mlw_qmn_landing_array);
		}

		//Update message_after with new array then check to see if worked
		$mlw_new_landing_results = $wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->prefix."mlw_quizzes SET message_after=%s, last_activity='".date("Y-m-d H:i:s")."' WHERE quiz_id=%d", $mlw_qmn_landing_array, $mlw_qmn_landing_id ) );
		if ( false != $mlw_new_landing_results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The results page has been added successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "New Results Page Has Been Created For Quiz Number $mlw_qmn_landing_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0013'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0013", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	//Check to save landing pages
	if (isset($_POST["mlw_save_landing_pages"]) && $_POST["mlw_save_landing_pages"] == "confirmation")
	{
		//Function Variables
		$mlw_qmn_landing_id = intval($_POST["mlw_landing_quiz_id"]);
		$mlw_qmn_landing_total = intval($_POST["mlw_landing_page_total"]);

		//Create new array
		$i = 1;
		$mlw_qmn_new_landing_array = array();
		while ($i <= $mlw_qmn_landing_total)
		{
			if ($_POST["message_after_".$i] != "Delete")
			{
				$mlw_qmn_landing_each = array(intval($_POST["message_after_begin_".$i]), intval($_POST["message_after_end_".$i]), htmlspecialchars(stripslashes($_POST["message_after_".$i]), ENT_QUOTES), "redirect_url" => esc_url_raw($_POST["redirect_".$i]));
				$mlw_qmn_new_landing_array[] = $mlw_qmn_landing_each;
			}
			$i++;
		}
		$mlw_qmn_new_landing_array = json_encode($mlw_qmn_new_landing_array);
		$mlw_new_landing_results = $wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->prefix."mlw_quizzes SET message_after='%s', last_activity='".date("Y-m-d H:i:s")."' WHERE quiz_id=%d", $mlw_qmn_new_landing_array, $mlw_qmn_landing_id ) );
		if ( false != $mlw_new_landing_results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The results page has been saved successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Results Pages Have Been Saved For Quiz Number $mlw_qmn_landing_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0014'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0014", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}

	//Load Landing Pages
	if (is_json($mlw_quiz_options->message_after) && is_array(json_decode($mlw_quiz_options->message_after, true)))
	{
    		$mlw_message_after_array = json_decode($mlw_quiz_options->message_after, true);
	}
	else
	{
		$mlw_message_after_array = array(array(0, 0, $mlw_quiz_options->message_after, "redirect_url" => ''));
	}

  //include JS
  load_custom_wp_admin_style();

  include(locate_template('template-parts/quizes/content-results.php'));
}

function is_json($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

//List Questions Page
function ef_quiz_questions_page()
{

?>
  <script>
		var answer_text = '<?php _e('Answer', 'quiz-master-next'); ?>';
	</script>
	<?php
  load_custom_wp_admin_style();
	wp_enqueue_script('qmn_admin_question_js', plugins_url( '/quiz-master-next/js/qsm-admin-question.js' ), array( 'jquery-ui-sortable' ) );
	wp_enqueue_style('qmn_admin_question_css', plugins_url( '/quiz-master-next/css/qsm-admin-question.css') );
	wp_enqueue_script( 'math_jax', '//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML' );

	global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];

	//Re-ordering questions
	if (isset($_POST['qmn_question_order_nonce']) && wp_verify_nonce( $_POST['qmn_question_order_nonce'], 'qmn_question_order')) {
		$list_of_questions = explode( ',', $_POST["save_question_order_input"] );
		$question_order = 0;
		$success = true;
		foreach( $list_of_questions as $id ) {
			$question_order++;
			$update_question_id = explode( '_', $id );
			$results = $wpdb->update(
				$wpdb->prefix . "mlw_questions",
				array(
					'question_order' => $question_order
				),
				array( 'question_id' => $update_question_id[1] ),
				array(
					'%d'
				),
				array( '%d' )
			);
			if ( $results ) {
				$success = false;
			}
		}
		if ( ! $success ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The question order has been updated successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Question Order Has Been Updated On Quiz: $quiz_id" );
		}
	}

	//Edit question
	if ( isset( $_POST["question_submission"] ) && $_POST["question_submission"] == "edit_question" ) {

		//Variables from edit question form
		$edit_question_name = trim( preg_replace( '/\s+/',' ', nl2br( htmlspecialchars( stripslashes( $_POST["question_name"] ), ENT_QUOTES ) ) ) );
		$edit_question_answer_info = htmlspecialchars( stripslashes( $_POST["correct_answer_info"] ), ENT_QUOTES );
		$mlw_edit_question_id = intval( $_POST["question_id"] );
		$mlw_edit_question_type = sanitize_text_field( $_POST["question_type"] );
		$edit_comments = htmlspecialchars( $_POST["comments"], ENT_QUOTES );
		$edit_hint = htmlspecialchars( $_POST["hint"], ENT_QUOTES );
		$edit_question_order = intval( $_POST["new_question_order"] );
		$total_answers = intval( $_POST["new_question_answer_total"] );

    $errorExists = false;
    //validate of quiz title and number of questions
    if(!isset($_POST["question_name"]) || empty($_POST["question_name"]))
    {
      $mlwQuizMasterNext->alertManager->newAlert("Question Name is required","error");
      $errorExists = true;
    }else if($total_answers < 2)
    {
      $mlwQuizMasterNext->alertManager->newAlert("Should have at least 2 answers","error");
      $errorExists = true;
    }

    //at least and most one checkbox as correct is checked
    $correct_exists = false;
    $correct_index = 1;
    while ( $correct_index <= $total_answers ) {
      // Checks if that answer exists and it's not empty
      if ( isset( $_POST["answer_$correct_index"] ) && ! empty( trim($_POST["answer_$correct_index"]) ) ) {

        // Checks if the answer was marked as correct
        if ( isset( $_POST["answer_$correct_index"."_correct"] ) && 1 == $_POST["answer_$correct_index"."_correct"] ) {
          $correct_exists = true;
          break;
        }
      }else {
        $mlwQuizMasterNext->alertManager->newAlert("One or more of the answers is empty","error");
        $errorExists = true;
        break;
      }
      $correct_index++;
    }

    if(!$correct_exists && !$errorExists)
    {
      $mlwQuizMasterNext->alertManager->newAlert("Should have at least 1 answer correct","error");
      $errorExists = true;
    }

    if(!$errorExists) {

      // Checks if a category was selected or entered
      $qmn_edit_category = '';
      /*if ( isset( $_POST["new_category"] ) ) {

        $qmn_edit_category = sanitize_text_field( $_POST["new_category"] );

        // Checks if the new category radio was selected
        if ( 'new_category' == $qmn_edit_category ) {
          $qmn_edit_category = sanitize_text_field( stripslashes( $_POST["new_new_category"] ) );
        }
      } else {
        $qmn_edit_category = '';
      }*/

      // Retrieves question settings and sets required field
      $mlw_row_settings = $wpdb->get_row( $wpdb->prepare( "SELECT question_settings FROM " . $wpdb->prefix . "mlw_questions" . " WHERE question_id=%d", $mlw_edit_question_id ) );
      if ( is_serialized( $mlw_row_settings->question_settings ) && is_array( @unserialize( $mlw_row_settings->question_settings ) ) ) {
        $mlw_settings = @unserialize( $mlw_row_settings->question_settings );
      } else {
        $mlw_settings = array();
        $mlw_settings['required'] = intval( $_POST["required"] );
      }
      if ( ! isset( $mlw_settings['required'] ) ) {
        $mlw_settings['required'] = intval( $_POST["required"] );
      }
      $mlw_settings['required'] = intval( $_POST["required"] );
      $mlw_settings = serialize( $mlw_settings );

      // Cycles through answers
      $i = 1;
      $answer_array = array();
      while ( $i <= $total_answers ) {

        // Checks if that answer exists and it's not empty
        if ( isset( $_POST["answer_$i"] ) && ! empty( $_POST["answer_$i"] ) ) {

          // Checks if the answer was marked as correct
          $correct = 0;
          if ( isset( $_POST["answer_$i"."_correct"] ) && 1 == $_POST["answer_$i"."_correct"] ) {
            $correct = 1;
          }

          // Prepares this answer array
          $answer_array[] = array(
            htmlspecialchars( trim(stripslashes( $_POST["answer_$i"] )), ENT_QUOTES ),
            floatval( $_POST["answer_".$i."_points"] ),
            $correct,
            $i
          );
        }
        $i++;
      }

      $answer_array = serialize( $answer_array );
      $quiz_id = intval( $_POST["quiz_id"] );

      // Updates question row in table
      $results = $wpdb->update(
        $wpdb->prefix . "mlw_questions",
        array(
          'question_name' => $edit_question_name,
          'answer_array' => $answer_array,
          'question_answer_info' => $edit_question_answer_info,
          'comments' => $edit_comments,
          'hints' => $edit_hint,
          'question_order' => $edit_question_order,
          'question_type_new' => $mlw_edit_question_type,
          'question_settings' => $mlw_settings,
          'category' => $qmn_edit_category
        ),
        array( 'question_id' => $mlw_edit_question_id ),
        array(
          '%s',
          '%s',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%s',
          '%s'
        ),
        array( '%d' )
      );
      if ( false != $results ) {
        $mlwQuizMasterNext->alertManager->newAlert(__('The question has been updated successfully.', 'quiz-master-next'), 'success');
        $mlwQuizMasterNext->audit_manager->new_audit( "Question Has Been Edited: $edit_question_name" );
      } else {
        $mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0004'), 'error');
        $mlwQuizMasterNext->log_manager->add("Error 0004", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
      }
    }
	}

  //Delete question from quiz
	if ( isset( $_POST["delete_question"] ) && $_POST["delete_question"] == "confirmation")
	{
		//Variables from delete question form
		$mlw_question_id = intval( $_POST["delete_question_id"] );
		$quiz_id = intval( $_POST["quiz_id"] );

		$results = $wpdb->update(
			$wpdb->prefix . "mlw_questions",
			array(
				'deleted' => 1
			),
			array( 'question_id' => $mlw_question_id ),
			array(
				'%d'
			),
			array( '%d' )
		);
		if ( false != $results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The question has been deleted successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Question Has Been Deleted: $mlw_question_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0005'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0005", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	//Duplicate Questions
	if ( isset( $_POST["duplicate_question"] ) && $_POST["duplicate_question"] == "confirmation") {
		//Variables from delete question form
		$mlw_question_id = intval( $_POST["duplicate_question_id"] );
		$quiz_id = intval( $_POST["quiz_id"] );

		$mlw_original = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."mlw_questions WHERE question_id=%d", $mlw_question_id ), ARRAY_A );

		$results = $wpdb->insert(
						$wpdb->prefix."mlw_questions",
						array(
							'quiz_id' => $mlw_original['quiz_id'],
							'question_name' => $mlw_original['question_name'],
							'answer_array' => $mlw_original['answer_array'],
							'answer_one' => $mlw_original['answer_one'],
							'answer_one_points' => $mlw_original['answer_one_points'],
							'answer_two' => $mlw_original['answer_two'],
							'answer_two_points' => $mlw_original['answer_two_points'],
							'answer_three' => $mlw_original['answer_three'],
							'answer_three_points' => $mlw_original['answer_three_points'],
							'answer_four' => $mlw_original['answer_four'],
							'answer_four_points' => $mlw_original['answer_four_points'],
							'answer_five' => $mlw_original['answer_five'],
							'answer_five_points' => $mlw_original['answer_five_points'],
							'answer_six' => $mlw_original['answer_six'],
							'answer_six_points' => $mlw_original['answer_six_points'],
							'correct_answer' => $mlw_original['correct_answer'],
							'question_answer_info' => $mlw_original['question_answer_info'],
							'comments' => $mlw_original['comments'],
							'hints' => $mlw_original['hints'],
							'question_order' => $mlw_original['question_order'],
							'question_type_new' => $mlw_original['question_type_new'],
							'question_settings' => $mlw_original['question_settings'],
							'category' => $mlw_original['category'],
							'deleted' => $mlw_original['deleted']
						),
						array(
							'%d',
							'%s',
							'%s',
							'%s',
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%d',
							'%s',
							'%d',
							'%s',
							'%d',
							'%s',
							'%s',
							'%s',
							'%d'
						)
					);

		if ( false != $results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The question has been duplicated successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Question Has Been Duplicated: $mlw_question_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0019'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 00019", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	//Submit new question into database
	if ( isset( $_POST["question_submission"] ) && $_POST["question_submission"] == "new_question") {
      $errorExists = false;
      //Variables from new question form
      $question_name = trim( preg_replace( '/\s+/',' ', nl2br( htmlspecialchars( stripslashes( $_POST["question_name"] ), ENT_QUOTES ) ) ) );
      $question_answer_info = htmlspecialchars( stripslashes( $_POST["correct_answer_info"] ), ENT_QUOTES );
      $question_type = sanitize_text_field( $_POST["question_type"] );
      $comments = htmlspecialchars( $_POST["comments"], ENT_QUOTES );
      $hint = htmlspecialchars( $_POST["hint"], ENT_QUOTES );
      $new_question_order = intval( $_POST["new_question_order"] );
      $total_answers = intval( $_POST["new_question_answer_total"] );

      //validate of quiz title and number of questions
      if(!isset($_POST["question_name"]) || empty($_POST["question_name"]))
      {
        $mlwQuizMasterNext->alertManager->newAlert("Question Name is required","error");
        $errorExists = true;
      }else if($total_answers < 2)
      {
        $mlwQuizMasterNext->alertManager->newAlert("Should have at least 2 answers","error");
        $errorExists = true;
      }

      //at least and most one checkbox as correct is checked
      $correct_exists = false;
      $correct_index = 1;
      while ( $correct_index <= $total_answers ) {
        // Checks if that answer exists and it's not empty
        if ( isset( $_POST["answer_$correct_index"] ) && ! empty( trim($_POST["answer_$correct_index"]) ) ) {

          // Checks if the answer was marked as correct
          if ( isset( $_POST["answer_$correct_index"."_correct"] ) && 1 == $_POST["answer_$correct_index"."_correct"] ) {
            $correct_exists = true;
            break;
          }
        }else {
          $mlwQuizMasterNext->alertManager->newAlert("One or more of the answers is empty","error");
          $errorExists = true;
          break;
        }
        $correct_index++;
      }

      if(!$correct_exists && !$errorExists)
      {
        $mlwQuizMasterNext->alertManager->newAlert("Should have at least 1 answer correct","error");
        $errorExists = true;
      }

      if(!$errorExists) {
      // Checks if a category was selected or entered
      if ( isset( $_POST['new_category'] ) ) {

        $qmn_category = sanitize_text_field( $_POST["new_category"] );

        // Checks if the new category radio was selected
        if ( 'new_category' == $qmn_category ) {
          $qmn_category = sanitize_text_field( stripslashes( $_POST["new_new_category"] ) );
        }
      } else {
        $qmn_category = '';
      }

      // Creates question settings array
      $mlw_settings = array();
      $mlw_settings['required'] = intval($_POST["required"]);
      $mlw_settings = serialize($mlw_settings);

      // Cycles through answers
      $i = 1;
      $answer_array = array();
      while ( $i <= $total_answers ) {

        // Checks if that answer exists and it's not empty
        if ( isset( $_POST["answer_$i"] ) && ! empty( $_POST["answer_$i"] ) ) {

          // Checks if the answer was marked as correct
          $correct = 0;
          if ( isset( $_POST["answer_".$i."_correct"] ) && 1 == $_POST["answer_".$i."_correct"] ) {
            $correct = 1;
          }

          // Prepares answer array
          $answer_array[] = array(
            htmlspecialchars( stripslashes( $_POST["answer_".$i] ), ENT_QUOTES ),
            floatval( $_POST["answer_".$i."_points"] ),
            $correct,
            $i
          );
        }
        $i++;
      }

      $answer_array = serialize( $answer_array );
      $quiz_id = intval( $_POST["quiz_id"] );

      // Inserts new question into table
      $results = $wpdb->insert(
        $wpdb->prefix."mlw_questions",
        array(
          'quiz_id' => $quiz_id,
          'question_name' => $question_name,
          'answer_array' => $answer_array,
          'question_answer_info' => $question_answer_info,
          'comments' => $comments,
          'hints' => $hint,
          'question_order' => $new_question_order,
          'question_type_new' => $question_type,
          'question_settings' => $mlw_settings,
          'category' => $qmn_category,
          'deleted' => 0
        ),
        array(
          '%d',
          '%s',
          '%s',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%s',
          '%s',
          '%d'
        )
      );

      // Checks if insert was successful or not
      if ( false != $results ) {
        $mlwQuizMasterNext->alertManager->newAlert(__('The question has been created successfully.', 'quiz-master-next'), 'success');
        $mlwQuizMasterNext->audit_manager->new_audit( "Question Has Been Added: $question_name" );
      } else {
        $mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0006'), 'error');
        $mlwQuizMasterNext->log_manager->add("Error 0006", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
      }
    }
	}

	// Import question from another quiz
	if ( isset( $_POST["add_question_from_quiz_nonce"] ) && wp_verify_nonce( $_POST['add_question_from_quiz_nonce'], 'add_question_from_quiz') ) {

		// Load question from question bank
		$question_id = intval( $_POST["copy_question_id"] );
		$quiz_id = intval( $_POST["quiz_id"] );
		$importing_question = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}mlw_questions WHERE question_id=%d", $question_id ) );

		// Save question into question bank for this quiz
		$results = $wpdb->insert(
			$wpdb->prefix."mlw_questions",
			array(
				'quiz_id' => $quiz_id,
				'question_name' => $importing_question->question_name,
				'answer_array' => $importing_question->answer_array,
				'question_answer_info' => $importing_question->question_answer_info,
				'comments' => $importing_question->comments,
				'hints' => $importing_question->hints,
				'question_order' => $importing_question->question_order,
				'question_type_new' => $importing_question->question_type_new,
				'question_settings' => $importing_question->question_settings,
				'category' => $importing_question->category,
				'deleted' => 0
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d'
			)
		);
		if ( false !== $results ) {
			$mlwQuizMasterNext->alertManager->newAlert( __( 'The question has been created successfully.', 'quiz-master-next' ), 'success' );
			$mlwQuizMasterNext->audit_manager->new_audit( "Question Has Been Added: {$importing_question->question_name}" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert( sprintf( __( 'There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next' ), '0023' ), 'error' );
			$mlwQuizMasterNext->log_manager->add( "Error 0023", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error' );
		}
	}

	//Load questions
	$questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "mlw_questions WHERE quiz_id=%d AND deleted='0'
		ORDER BY question_order ASC", $quiz_id ) );
	$answers = array();
	foreach($questions as $mlw_question_info) {
		if (is_serialized($mlw_question_info->answer_array) && is_array(@unserialize($mlw_question_info->answer_array)))
		{
			$mlw_qmn_answer_array_each = @unserialize($mlw_question_info->answer_array);
			$answers[$mlw_question_info->question_id] = $mlw_qmn_answer_array_each;
		}
		else
		{
			$mlw_answer_array_correct = array(0, 0, 0, 0, 0, 0);
			$mlw_answer_array_correct[$mlw_question_info->correct_answer-1] = 1;
			$answers[$mlw_question_info->question_id] = array(
				array($mlw_question_info->answer_one, $mlw_question_info->answer_one_points, $mlw_answer_array_correct[0]),
				array($mlw_question_info->answer_two, $mlw_question_info->answer_two_points, $mlw_answer_array_correct[1]),
				array($mlw_question_info->answer_three, $mlw_question_info->answer_three_points, $mlw_answer_array_correct[2]),
				array($mlw_question_info->answer_four, $mlw_question_info->answer_four_points, $mlw_answer_array_correct[3]),
				array($mlw_question_info->answer_five, $mlw_question_info->answer_five_points, $mlw_answer_array_correct[4]),
				array($mlw_question_info->answer_six, $mlw_question_info->answer_six_points, $mlw_answer_array_correct[5]));
		}
	}

	//Load Question Types
	$qmn_question_types = $mlwQuizMasterNext->pluginHelper->get_question_type_options();

	//Load question type edit fields and convert to JavaScript
	$qmn_question_type_fields = $mlwQuizMasterNext->pluginHelper->get_question_type_edit_fields();
	echo "<script>
		var qmn_question_type_fields = ".json_encode($qmn_question_type_fields).";
	</script>";

	echo "<script>
	var questions_list = [";
	foreach($questions as $question) {

		//Load Required
		if (is_serialized($question->question_settings) && is_array(@unserialize($question->question_settings)))
		{
			$mlw_question_settings = @unserialize($question->question_settings);
		}
		else
		{
			$mlw_question_settings = array();
			$mlw_question_settings['required'] = 1;
		}

		//Load Answers
		$answer_string = "";
		foreach($answers[$question->question_id] as $answer_single) {
			$answer_string .= "{answer: '".esc_js( str_replace('\\', '\\\\', $answer_single[0] ) )."',points: ".$answer_single[1].",correct: ".$answer_single[2]."},";
		}

		//Load Type
		$type_slug = $question->question_type_new;
		$type_name = $question->question_type_new;
		foreach($qmn_question_types as $type)
		{
			if ($type["slug"] == $question->question_type_new)
			{
				$type_name = $type["name"];
			}
		}

		//Parse Javascript Object
		echo "{
			id: ".$question->question_id.",
		  question: '".esc_js( str_replace('\\', '\\\\', $question->question_name ) )."',
		  answers: [".$answer_string."],
		  correct_info: '".esc_js( $question->question_answer_info )."',
		  hint: '".esc_js($question->hints, ENT_QUOTES)."',
		  type: '".$question->question_type_new."',
			type_name: '".$type_name."',
			comment: ".$question->comments.",
		  order: ".$question->question_order.",
		  required: ".$mlw_question_settings['required'].",
		  category: '".esc_js($question->category)."'
		},";
	}

	echo "];
	</script>";

	//Load Categories
	$qmn_quiz_categories = $wpdb->get_results( $wpdb->prepare( "SELECT category FROM " . $wpdb->prefix . "mlw_questions WHERE quiz_id=%d AND deleted='0'
		GROUP BY category", $quiz_id ) );

	$is_new_quiz = $wpdb->num_rows;

  include(locate_template('template-parts/quizes/content-questions.php'));

}

//Set Quiz Options
function ef_quiz_options_page()
{
  global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];
	//Submit saved options into database
	if ( isset($_POST["save_options"]) && $_POST["save_options"] == "confirmation")
	{
		//Variables for save options form
		$mlw_system = intval($_POST["system"]);
		$mlw_qmn_pagination = intval($_POST["pagination"]);
		$mlw_qmn_social_media = intval($_POST["social_media"]);
		$mlw_qmn_question_numbering = intval($_POST["question_numbering"]);
		$mlw_qmn_timer = intval($_POST["timer_limit"]);
		$mlw_qmn_questions_from_total = intval($_POST["question_from_total"]);
		$mlw_randomness_order = intval($_POST["randomness_order"]);
		$mlw_total_user_tries = intval($_POST["total_user_tries"]);
		$mlw_require_log_in = intval($_POST["require_log_in"]);
		$mlw_limit_total_entries = intval($_POST["limit_total_entries"]);
		$mlw_contact_location = intval($_POST["contact_info_location"]);
		$mlw_user_name = intval($_POST["userName"]);
		$mlw_user_comp = intval($_POST["userComp"]);
		$mlw_user_email = intval($_POST["userEmail"]);
		$mlw_user_phone = intval($_POST["userPhone"]);
		$disable_answer_onselect = intval($_POST["disable_answer_onselect"]);
		$ajax_show_correct = intval($_POST["ajax_show_correct"]);
		$mlw_comment_section = intval($_POST["commentSection"]);
		$mlw_qmn_loggedin_contact = intval($_POST["loggedin_user_contact"]);
		$qmn_scheduled_timeframe = serialize( array(
			'start' => sanitize_text_field( $_POST["scheduled_time_start"] ),
			'end' => sanitize_text_field( $_POST["scheduled_time_end"] )
		));
		$quiz_id = intval( $_POST["quiz_id"] );

		$results = $wpdb->update(
			$wpdb->prefix . "mlw_quizzes",
			array(
			 	'system' => $mlw_system,
				'loggedin_user_contact' => $mlw_qmn_loggedin_contact,
				'contact_info_location' => $mlw_contact_location,
				'user_name' => $mlw_user_name,
				'user_comp' => $mlw_user_comp,
				'user_email' => $mlw_user_email,
				'user_phone' => $mlw_user_phone,
				'comment_section' => $mlw_comment_section,
				'randomness_order' => $mlw_randomness_order,
				'question_from_total' => $mlw_qmn_questions_from_total,
				'total_user_tries' => $mlw_total_user_tries,
				'social_media' => $mlw_qmn_social_media,
				'pagination' => $mlw_qmn_pagination,
				'timer_limit' => $mlw_qmn_timer,
				'question_numbering' => $mlw_qmn_question_numbering,
				'require_log_in' => $mlw_require_log_in,
				'limit_total_entries' => $mlw_limit_total_entries,
				'last_activity' => date("Y-m-d H:i:s"),
				'scheduled_timeframe' => $qmn_scheduled_timeframe,
				'disable_answer_onselect' => $disable_answer_onselect,
				'ajax_show_correct' => $ajax_show_correct
			),
			array( 'quiz_id' => $quiz_id ),
			array(
			 	'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d'
			),
			array( '%d' )
		);
		if ( false != $results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The options has been updated successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Options Have Been Edited For Quiz Number $quiz_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0008'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0008", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}

	//Load Scheduled Timeframe
    	$qmn_scheduled_timeframe = "";
	if (is_serialized($mlw_quiz_options->scheduled_timeframe) && is_array(@unserialize($mlw_quiz_options->scheduled_timeframe)))
	{
		$qmn_scheduled_timeframe = @unserialize($mlw_quiz_options->scheduled_timeframe);
	}
	else
	{
		$qmn_scheduled_timeframe = array("start" => '', "end" => '');
	}

  include(locate_template('template-parts/quizes/content-options.php'));
}

//Set Quiz Text
function ef_quiz_text_page()
{
  global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = intval($_GET["quiz_id"]);
	//Submit saved templates into database
	if ( isset($_POST["save_templates"]) && $_POST["save_templates"] == "confirmation")
	{
		//Variables for save templates form
		$mlw_before_message = htmlspecialchars( stripslashes( $_POST["mlw_quiz_before_message"] ), ENT_QUOTES);
		$mlw_qmn_message_end = htmlspecialchars( stripslashes( $_POST["message_end_template"] ), ENT_QUOTES);
		$mlw_user_tries_text = htmlspecialchars( stripslashes( $_POST["mlw_quiz_total_user_tries_text"] ), ENT_QUOTES);
		$mlw_submit_button_text = sanitize_text_field( stripslashes( $_POST["mlw_submitText"] ) );
		$mlw_name_field_text = sanitize_text_field( stripslashes( $_POST["mlw_nameText"] ) );
		$mlw_business_field_text = sanitize_text_field( stripslashes( $_POST["mlw_businessText"] ) );
		$mlw_email_field_text = sanitize_text_field( stripslashes( $_POST["mlw_emailText"] ) );
		$mlw_phone_field_text = sanitize_text_field( stripslashes( $_POST["mlw_phoneText"] ) );
		$mlw_before_comments = htmlspecialchars(stripslashes( $_POST["mlw_quiz_before_comments"] ), ENT_QUOTES);
		$mlw_comment_field_text = htmlspecialchars(stripslashes( $_POST["mlw_commentText"] ), ENT_QUOTES);
		$mlw_require_log_in_text = htmlspecialchars(stripslashes( $_POST["mlw_require_log_in_text"] ), ENT_QUOTES);
		$mlw_scheduled_timeframe_text = htmlspecialchars(stripslashes( $_POST["mlw_scheduled_timeframe_text"] ), ENT_QUOTES);
		$mlw_limit_total_entries_text = htmlspecialchars(stripslashes( $_POST["mlw_limit_total_entries_text"] ), ENT_QUOTES);
		$mlw_qmn_pagination_field = serialize( array(
			sanitize_text_field( stripslashes( $_POST["pagination_prev_text"] ) ),
			sanitize_text_field( stripslashes( $_POST["pagination_next_text"] ) )
		));
		$qmn_social_media_text = serialize( array(
			'twitter' => wp_kses_post( stripslashes( $_POST["mlw_quiz_twitter_text_template"] ) ),
			'facebook' => wp_kses_post( stripslashes( $_POST["mlw_quiz_facebook_text_template"] ) )
		));

		$mlw_question_answer_template = htmlspecialchars(stripslashes( $_POST["mlw_quiz_question_answer_template"] ), ENT_QUOTES);
		$quiz_id = intval($_POST["quiz_id"]);

		$results = $wpdb->update(
			$wpdb->prefix . "mlw_quizzes",
			array(
				'message_before' => $mlw_before_message,
				'message_comment' => $mlw_before_comments,
				'message_end_template' => $mlw_qmn_message_end,
				'comment_field_text' => $mlw_comment_field_text,
				'question_answer_template' => $mlw_question_answer_template,
				'submit_button_text' => $mlw_submit_button_text,
				'name_field_text' => $mlw_name_field_text,
				'business_field_text' => $mlw_business_field_text,
				'email_field_text' => $mlw_email_field_text,
				'phone_field_text' => $mlw_phone_field_text,
				'total_user_tries_text' => $mlw_user_tries_text,
				'social_media_text' => $qmn_social_media_text,
				'pagination_text' => $mlw_qmn_pagination_field,
				'require_log_in_text' => $mlw_require_log_in_text,
				'limit_total_entries_text' => $mlw_limit_total_entries_text,
				'last_activity' => date("Y-m-d H:i:s"),
				'scheduled_timeframe_text' => $mlw_scheduled_timeframe_text
			),
			array( 'quiz_id' => $quiz_id ),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			),
			array( '%d' )
		);
		if ( false != $results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The templates has been updated successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "Templates Have Been Edited For Quiz Number $quiz_id" );
		} else {
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0007'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0007", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $quiz_id));
	}

	//Load Pagination Text
    	$mlw_qmn_pagination_text = "";
    	if (is_serialized($mlw_quiz_options->pagination_text) && is_array(@unserialize($mlw_quiz_options->pagination_text)))
	{
		$mlw_qmn_pagination_text = @unserialize($mlw_quiz_options->pagination_text);
	}
	else
	{
		$mlw_qmn_pagination_text = array(__('Previous', 'quiz-master-next'), $mlw_quiz_options->pagination_text);
	}

	//Load Social Media Text
	$qmn_social_media_text = "";
	if (is_serialized($mlw_quiz_options->social_media_text) && is_array(@unserialize($mlw_quiz_options->social_media_text)))
	{
		$qmn_social_media_text = @unserialize($mlw_quiz_options->social_media_text);
	}
	else
	{
		$qmn_social_media_text = array(
        		'twitter' => $mlw_quiz_options->social_media_text,
        		'facebook' => $mlw_quiz_options->social_media_text
        	);
	}

  load_custom_wp_admin_style();

  include(locate_template('template-parts/quizes/content-text.php'));
}

//Set Quiz Email
function ef_quiz_email_page()
{
  global $wpdb;
	global $mlwQuizMasterNext;
	$quiz_id = $_GET["quiz_id"];
	//Check to add new user email template
	if ( isset( $_POST["mlw_add_email_page"] ) && $_POST["mlw_add_email_page"] == "confirmation" ) {
		//Function variables
		$mlw_qmn_add_email_id = intval($_POST["mlw_add_email_quiz_id"]);
		$mlw_qmn_user_email = $wpdb->get_var( $wpdb->prepare( "SELECT user_email_template FROM ".$wpdb->prefix."mlw_quizzes WHERE quiz_id=%d", $mlw_qmn_add_email_id ) );

		//Load user email and check if it is array already. If not, turn it into one
		if ( is_serialized( $mlw_qmn_user_email ) && is_array( @unserialize( $mlw_qmn_user_email ) ) ) {
			$mlw_qmn_email_array = @unserialize($mlw_qmn_user_email);
			$mlw_new_landing_array = array(0, 100, 'Enter Your Text Here', 'Quiz Results For %QUIZ_NAME%');
			array_unshift($mlw_qmn_email_array , $mlw_new_landing_array);
			$mlw_qmn_email_array = serialize($mlw_qmn_email_array);

		} else {
			$mlw_qmn_email_array = array(array(0, 0, $mlw_qmn_user_email, 'Quiz Results For %QUIZ_NAME%'));
			$mlw_new_landing_array = array(0, 100, 'Enter Your Text Here', 'Quiz Results For %QUIZ_NAME%');
			array_unshift($mlw_qmn_email_array , $mlw_new_landing_array);
			$mlw_qmn_email_array = serialize($mlw_qmn_email_array);
		}
		//Update email template with new array then check to see if worked
		$mlw_new_email_results = $wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->prefix."mlw_quizzes SET user_email_template='%s', last_activity='".date("Y-m-d H:i:s")."' WHERE quiz_id=%d", $mlw_qmn_email_array, $mlw_qmn_add_email_id ) );
		if ( false != $mlw_new_email_results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The email has been added successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "New User Email Has Been Created For Quiz Number $mlw_qmn_add_email_id" );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0016'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0016", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	//Check to add new admin email template
	if (isset($_POST["mlw_add_admin_email_page"]) && $_POST["mlw_add_admin_email_page"] == "confirmation")
	{
		//Function variables
		$mlw_qmn_add_email_id = intval($_POST["mlw_add_admin_email_quiz_id"]);
		$mlw_qmn_admin_email = $wpdb->get_var( $wpdb->prepare( "SELECT admin_email_template FROM ".$wpdb->prefix."mlw_quizzes WHERE quiz_id=%d", $mlw_qmn_add_email_id ) );

		//Load user email and check if it is array already. If not, turn it into one
		if (is_serialized($mlw_qmn_admin_email) && is_array(@unserialize($mlw_qmn_admin_email)))
		{
			$mlw_qmn_email_array = @unserialize($mlw_qmn_admin_email);
			$mlw_new_landing_array = array(
				"begin_score" => 0,
				"end_score" => 100,
				"message" => __('Enter text here', 'quiz-master-next'),
				"subject" => 'Quiz Results For %QUIZ_NAME%'
			);
			array_unshift($mlw_qmn_email_array , $mlw_new_landing_array);
			$mlw_qmn_email_array = serialize($mlw_qmn_email_array);

		}
		else
		{
			$mlw_qmn_email_array = array(array(
				"begin_score" => 0,
				"end_score" => 0,
				"message" => $mlw_qmn_admin_email,
				"subject" => 'Quiz Results For %QUIZ_NAME%'
			));
			$mlw_new_landing_array = array(
				"begin_score" => 0,
				"end_score" => 100,
				"message" => __('Enter text here', 'quiz-master-next'),
				"subject" => 'Quiz Results For %QUIZ_NAME%'
			);
			array_unshift($mlw_qmn_email_array , $mlw_new_landing_array);
			$mlw_qmn_email_array = serialize($mlw_qmn_email_array);
		}
		//Update email template with new array then check to see if worked
		$mlw_new_email_results = $wpdb->query( $wpdb->prepare( "UPDATE ".$wpdb->prefix."mlw_quizzes SET admin_email_template='%s', last_activity='".date("Y-m-d H:i:s")."' WHERE quiz_id=%d", $mlw_qmn_email_array, $mlw_qmn_add_email_id ) );
		if ( false != $mlw_new_email_results ) {
			$mlwQuizMasterNext->alertManager->newAlert(__('The email has been added successfully.', 'quiz-master-next'), 'success');
			$mlwQuizMasterNext->audit_manager->new_audit( "New Admin Email Has Been Created For Quiz Number $mlw_qmn_add_email_id" );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0016'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0016", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	//Check to save email templates
	if (isset($_POST["mlw_save_email_template"]) && $_POST["mlw_save_email_template"] == "confirmation")
	{
		//Function Variables
		$mlw_qmn_email_id = intval($_POST["mlw_email_quiz_id"]);
		$mlw_qmn_email_template_total = intval($_POST["mlw_email_template_total"]);
		$mlw_qmn_email_admin_total = intval($_POST["mlw_email_admin_total"]);
		$mlw_send_user_email = intval( $_POST["sendUserEmail"] );
		$mlw_send_admin_email = intval( $_POST["sendAdminEmail"] );
		$mlw_admin_email = sanitize_text_field( $_POST["adminEmail"] );
		$mlw_email_from_text = sanitize_text_field( $_POST["emailFromText"] );
		$from_address = sanitize_text_field( $_POST["emailFromAddress"] );
		$reply_to_user = sanitize_text_field( $_POST["replyToUser"] );

		//from email array
		$from_email_array = array(
			'from_name' => $mlw_email_from_text,
			'from_email' => $from_address,
			'reply_to' => $reply_to_user
		);

		//Create new array
		$i = 1;
		$mlw_qmn_new_email_array = array();
		while ( $i <= $mlw_qmn_email_template_total ) {
			if ( $_POST["user_email_".$i] != "Delete" ) {
				$mlw_qmn_email_each = array(intval($_POST["user_email_begin_".$i]), intval($_POST["user_email_end_".$i]), htmlspecialchars(stripslashes($_POST["user_email_".$i]), ENT_QUOTES), htmlspecialchars(stripslashes($_POST["user_email_subject_".$i]), ENT_QUOTES));
				$mlw_qmn_new_email_array[] = $mlw_qmn_email_each;
			}
			$i++;
		}

		//Create new array
		$i = 1;
		$mlw_qmn_new_admin_array = array();
		while ($i <= $mlw_qmn_email_admin_total)
		{
			if ($_POST["admin_email_".$i] != "Delete")
			{
				$mlw_qmn_email_each = array(
					"begin_score" => intval($_POST["admin_email_begin_".$i]),
					"end_score" => intval($_POST["admin_email_end_".$i]),
					"message" => htmlspecialchars(stripslashes($_POST["admin_email_".$i]), ENT_QUOTES),
					"subject" => htmlspecialchars(stripslashes($_POST["admin_email_subject_".$i]), ENT_QUOTES)
				);
				$mlw_qmn_new_admin_array[] = $mlw_qmn_email_each;
			}
			$i++;
		}

		$from_email_array = serialize( $from_email_array );
		$mlw_qmn_new_email_array = serialize($mlw_qmn_new_email_array);
		$mlw_qmn_new_admin_array = serialize($mlw_qmn_new_admin_array);

		$mlw_new_email_results = $wpdb->update(
			$wpdb->prefix . "mlw_quizzes",
			array(
				'send_user_email' => $mlw_send_user_email,
				'send_admin_email' => $mlw_send_admin_email,
				'admin_email' => $mlw_admin_email,
				'email_from_text' => $from_email_array,
				'user_email_template' => $mlw_qmn_new_email_array,
				'admin_email_template' => $mlw_qmn_new_admin_array,
				'last_activity' => date("Y-m-d H:i:s")
			),
			array( 'quiz_id' => $mlw_qmn_email_id ),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s'
			),
			array( '%d' )
		);
		if ( false != $mlw_new_email_results ) {
			$mlwQuizMasterNext->alertManager->newAlert( __( 'The email has been updated successfully.', 'quiz-master-next' ), 'success' );
			$mlwQuizMasterNext->audit_manager->new_audit( "Email Templates Have Been Saved For Quiz Number $mlw_qmn_email_id" );
		}
		else
		{
			$mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0017'), 'error');
			$mlwQuizMasterNext->log_manager->add("Error 0017", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
		}
	}

	if (isset($_GET["quiz_id"]))
	{
		$table_name = $wpdb->prefix . "mlw_quizzes";
		$mlw_quiz_options = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE quiz_id=%d LIMIT 1", $_GET["quiz_id"]));
	}

	//Load from email array
	$from_email_array = maybe_unserialize( $mlw_quiz_options->email_from_text );
	if ( ! isset( $from_email_array["from_email"] ) ) {
		$from_email_array = array(
			'from_name' => $mlw_quiz_options->email_from_text,
			'from_email' => $mlw_quiz_options->admin_email,
			'reply_to' => 1
		);
	}

	//Load User Email Templates
	if (is_serialized($mlw_quiz_options->user_email_template) && is_array(@unserialize($mlw_quiz_options->user_email_template)))
	{
		$mlw_qmn_user_email_array = @unserialize($mlw_quiz_options->user_email_template);
	}
	else
	{
		 $mlw_qmn_user_email_array = array(array(0, 0, $mlw_quiz_options->user_email_template, 'Quiz Results For %QUIZ_NAME%'));
	}

	//Load Admin Email Templates
	if (is_serialized($mlw_quiz_options->admin_email_template) && is_array(@unserialize($mlw_quiz_options->admin_email_template)))
	{
		$mlw_qmn_admin_email_array = @unserialize($mlw_quiz_options->admin_email_template);
	}
	else
	{
		 $mlw_qmn_admin_email_array = array(array(
			"begin_score" => 0,
			"end_score" => 0,
			"message" => $mlw_quiz_options->admin_email_template,
			"subject" => 'Quiz Results For %QUIZ_NAME%'
		 ));
	}
	//wp_enqueue_style( 'qmn_admin_style', plugins_url( '../css/qmn_admin.css' , __FILE__ ) );
	?>
	<script type="text/javascript">
		var $j = jQuery.noConflict();
		// increase the default animation speed to exaggerate the effect
		$j.fx.speeds._default = 1000;
	</script>
	<div id="tabs-9" class="mlw_tab_content">
	<script>
                /**
                * This deletes the user email from the list of emails.
                *
                * @return id This variable contains the ID of the email so the correct one is deleted.
                * @since 4.4.0
                */
		function delete_email(id)
		{
			document.getElementById('user_email_'+id).value = "Delete";
			document.mlw_quiz_save_email_form.submit();
		}

                /**
                * This function deletes the admin email from the list of emails.
                *
                * @return id This variable contains the ID of the email so the right one is deleted.
                * @since 4.4.0
                */
		function delete_admin_email(id)
		{
			document.getElementById('admin_email_'+id).value = "Delete";
			document.mlw_quiz_save_email_form.submit();
		}
	</script>
 <?php

 include(locate_template('template-parts/quizes/content-emails.php'));
}

//Language translation
function ef_translate_quiz(  $post_id )
{
  global $mlwQuizMasterNext;
  global $wpdb;
  $post = get_post($post_id);
  if($post->post_type == "quiz" && $post->post_content == ''
           && isset($_POST['action']))
  {
    $post_meta = get_post_meta($post_id);

    //set quiz_id
    $quiz_id = $post_meta['quiz_id'][0];

    //update text
    $submit_text = "Submit";
    if (strpos($post_meta["language"], ':"ar"') !== false) {
      $submit_text = "إرسال";
    }

    $wpdb->update($wpdb->prefix.'mlw_quizzes',
      array( 'submit_button_text' => $submit_text),array( 'quiz_id' => $quiz_id ));

    //duplicate quiz
    $mlw_duplicate_quiz_id = intval($quiz_id);
		$mlw_duplicate_quiz_name = htmlspecialchars($post->post_title, ENT_QUOTES);
		ef_duplicate_quiz($post_id,$mlw_duplicate_quiz_id, $mlw_duplicate_quiz_name, true);
  }else if($post->post_type == "quiz"
           && isset($_POST['action']))
  {
    //set text to arabic or english
    $post_meta = get_post_meta($post_id);
    $quiz_id = $post_meta['quiz_id'][0];
    $submit_text = "Submit";
    if (strpos(implode(';', $post_meta["language"]), ':"ar"') !== false) {
      $submit_text = "إرسال";
    }

    $wpdb->update($wpdb->prefix.'mlw_quizzes',
      array( 'submit_button_text' => $submit_text),array( 'quiz_id' => $quiz_id ));
  }
}
add_action( 'save_post', 'ef_translate_quiz', 10);

//over-ride of duplicate function
function ef_duplicate_quiz($post_id, $quiz_id, $quiz_name, $is_duplicating_questions)
{
  global $mlwQuizMasterNext;
  global $wpdb;

  $table_name = $wpdb->prefix . "mlw_quizzes";
  $mlw_qmn_duplicate_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE quiz_id=%d", $quiz_id ) );
  $results = $wpdb->insert(
      $table_name,
      array(
        'quiz_name' => $quiz_name,
        'message_before' => $mlw_qmn_duplicate_data->message_before,
        'message_after' => $mlw_qmn_duplicate_data->message_after,
        'message_comment' => $mlw_qmn_duplicate_data->message_comment,
        'message_end_template' => $mlw_qmn_duplicate_data->message_end_template,
        'user_email_template' => $mlw_qmn_duplicate_data->user_email_template,
        'admin_email_template' => $mlw_qmn_duplicate_data->admin_email_template,
        'submit_button_text' => $mlw_qmn_duplicate_data->submit_button_text,
        'name_field_text' => $mlw_qmn_duplicate_data->name_field_text,
        'business_field_text' => $mlw_qmn_duplicate_data->business_field_text,
        'email_field_text' => $mlw_qmn_duplicate_data->email_field_text,
        'phone_field_text' => $mlw_qmn_duplicate_data->phone_field_text,
        'comment_field_text' => $mlw_qmn_duplicate_data->comment_field_text,
        'email_from_text' => $mlw_qmn_duplicate_data->email_from_text,
        'question_answer_template' => $mlw_qmn_duplicate_data->question_answer_template,
        'leaderboard_template' => $mlw_qmn_duplicate_data->leaderboard_template,
        'system' => $mlw_qmn_duplicate_data->system,
        'randomness_order' => $mlw_qmn_duplicate_data->randomness_order,
        'loggedin_user_contact' => $mlw_qmn_duplicate_data->loggedin_user_contact,
        'show_score' => $mlw_qmn_duplicate_data->show_score,
        'send_user_email' => $mlw_qmn_duplicate_data->send_user_email,
        'send_admin_email' => $mlw_qmn_duplicate_data->send_admin_email,
        'contact_info_location' => $mlw_qmn_duplicate_data->contact_info_location,
        'user_name' => $mlw_qmn_duplicate_data->user_name,
        'user_comp' => $mlw_qmn_duplicate_data->user_comp,
        'user_email' => $mlw_qmn_duplicate_data->user_email,
        'user_phone' => $mlw_qmn_duplicate_data->user_phone,
        'admin_email' => get_option( 'admin_email', 'Enter email' ),
        'comment_section' => $mlw_qmn_duplicate_data->comment_section,
        'question_from_total' => $mlw_qmn_duplicate_data->question_from_total,
        'total_user_tries' => $mlw_qmn_duplicate_data->total_user_tries,
        'total_user_tries_text' => $mlw_qmn_duplicate_data->total_user_tries_text,
        'certificate_template' => $mlw_qmn_duplicate_data->certificate_template,
        'social_media' => $mlw_qmn_duplicate_data->social_media,
        'social_media_text' => $mlw_qmn_duplicate_data->social_media_text,
        'pagination' => $mlw_qmn_duplicate_data->pagination,
        'pagination_text' => $mlw_qmn_duplicate_data->pagination_text,
        'timer_limit' => $mlw_qmn_duplicate_data->timer_limit,
        'quiz_stye' => $mlw_qmn_duplicate_data->quiz_stye,
        'question_numbering' => $mlw_qmn_duplicate_data->question_numbering,
        'quiz_settings' => $mlw_qmn_duplicate_data->quiz_settings,
        'theme_selected' => $mlw_qmn_duplicate_data->theme_selected,
        'last_activity' => date("Y-m-d H:i:s"),
        'require_log_in' => $mlw_qmn_duplicate_data->require_log_in,
        'require_log_in_text' => $mlw_qmn_duplicate_data->require_log_in_text,
        'limit_total_entries' => $mlw_qmn_duplicate_data->limit_total_entries,
        'limit_total_entries_text' => $mlw_qmn_duplicate_data->limit_total_entries_text,
        'scheduled_timeframe' => $mlw_qmn_duplicate_data->scheduled_timeframe,
        'scheduled_timeframe_text' => $mlw_qmn_duplicate_data->scheduled_timeframe_text,
        'quiz_views' => 0,
        'quiz_taken' => 0,
        'deleted' => 0
      ),
      array(
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%d',
        '%s',
        '%d',
        '%d',
        '%d',
        '%s',
        '%s',
        '%d',
        '%s',
        '%d',
        '%s',
        '%d',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%d',
        '%d',
        '%d',
      )
    );
  $mlw_new_id = $wpdb->insert_id;

  if ( false != $results ) {
    $isValid = true;
    //Duplicate on existing post
    if($post_id == -1)
    {
      $current_user = wp_get_current_user();
      $quiz_post = array(
        'post_title'    => $quiz_name,
        'post_content'  => "[mlw_quizmaster quiz=$mlw_new_id]",
        'post_status'   => 'publish',
        'post_author'   => $current_user->ID,
        'post_type' => 'quiz'
      );
      $quiz_post_id = wp_insert_post( $quiz_post );
      $post_id = $quiz_post_id;
    }

    //Update post content
    $wpdb->update($wpdb->prefix.'posts',
            array( 'post_content' => "[mlw_quizmaster quiz=$mlw_new_id]",
                "post_status" => "pending"),array( 'ID' => $post_id ));

    //Save post meta
    $quiz_post_id = $post_id;
    update_post_meta($quiz_post_id, "quiz_id", $mlw_new_id);

    //language of the quiz post
    $sql = "select post_id from {$wpdb->prefix}postmeta where meta_key='quiz_id' and meta_value ={$quiz_id}";
    $old_postmeta = $wpdb->get_col($sql);
    if($old_postmeta[0] != NULL)
    {
      // select language from old post that will duplicate from
      $post_lang = unserialize(get_post_meta($old_postmeta[0], 'language',true));
      if($post_lang)
      {
        $current_lang = $post_lang["slug"];
        wp_set_object_terms($quiz_post_id, $current_lang, 'language');
        update_post_meta($quiz_post_id, 'language', serialize(array(
          "slug" => $current_lang,
          "translated_id" => 0))
        );

        // update category and interests
        update_post_meta($quiz_post_id, 'category', get_post_meta($old_postmeta[0], 'category',true));
        $interests = get_post_meta($old_postmeta[0], 'interest',true);
        update_post_meta($quiz_post_id, 'interest', $interests);

        if(sizeof($interests) > 0){
          wp_set_object_terms( $quiz_post_id, array_map("intval", $interests), 'interest' );
        }
      }
    }

    $mlwQuizMasterNext->alertManager->newAlert(__('Your quiz has been duplicated successfully.', 'quiz-master-next'), 'success');
    $mlwQuizMasterNext->audit_manager->new_audit( "New Quiz Has Been Created: $quiz_name" );

    do_action('qmn_quiz_duplicated', $quiz_id, $mlw_new_id);
  }
  else
  {
    $isValid = false;
    $mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0011'), 'error');
    $mlwQuizMasterNext->log_manager->add("Error 0011", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
  }
  if ($is_duplicating_questions)
  {
    $table_name = $wpdb->prefix."mlw_questions";
    $mlw_current_questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE deleted=0 AND quiz_id=%d", $quiz_id ) );
    foreach ($mlw_current_questions as $mlw_question)
    {
      $question_results = $wpdb->insert(
        $table_name,
        array(
          'quiz_id' => $mlw_new_id,
          'question_name' => $mlw_question->question_name,
          'answer_array' => $mlw_question->answer_array,
          'answer_one' => $mlw_question->answer_one,
          'answer_one_points' => $mlw_question->answer_one_points,
          'answer_two' => $mlw_question->answer_two,
          'answer_two_points' => $mlw_question->answer_two_points,
          'answer_three' => $mlw_question->answer_three,
          'answer_three_points' => $mlw_question->answer_three_points,
          'answer_four' => $mlw_question->answer_four,
          'answer_four_points' => $mlw_question->answer_four_points,
          'answer_five' => $mlw_question->answer_five,
          'answer_five_points' => $mlw_question->answer_five_points,
          'answer_six' => $mlw_question->answer_six,
          'answer_six_points' => $mlw_question->answer_six_points,
          'correct_answer' => $mlw_question->correct_answer,
          'question_answer_info' => $mlw_question->question_answer_info,
          'comments' => $mlw_question->comments,
          'hints' => $mlw_question->hints,
          'question_order' => $mlw_question->question_order,
          'question_type_new' => $mlw_question->question_type_new,
          'question_settings' => $mlw_question->question_settings,
          'category' => $mlw_question->category,
          'deleted' => 0
        ),
        array(
          '%d',
          '%s',
          '%s',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%d',
          '%d',
          '%s',
          '%d',
          '%s',
          '%d',
          '%s',
          '%s',
          '%s',
          '%d'
        )
      );
      if ($question_results == false)
      {
        $mlwQuizMasterNext->alertManager->newAlert(sprintf(__('There has been an error in this action. Please share this with the developer. Error Code: %s', 'quiz-master-next'), '0020'), 'error');
        $mlwQuizMasterNext->log_manager->add("Error 0020", $wpdb->last_error.' from '.$wpdb->last_query, 0, 'error');
      }
    }
  }
}

function ef_quiz_preview_page()
{
  ?>
  <style>
    #wpfooter {
      display:none;
    }

    .quiz_end {
      display:none !important;
    }
  </style>
	<div id="tabs-preview" class="mlw_tab_content">
		<p>If your quiz looks different on the front end compared to this preview, then there is a conflict with your theme. Check out our <a href="http://quizandsurveymaster.com/common-theme-conflict-fixes/?utm_source=qsm-preview-tab&utm_medium=plugin&utm_campaign=qsm_plugin">Common Theme Conflict Fixes</a>.</a></p>
		<?php
    $content = '[mlw_quizmaster quiz='.intval($_GET["quiz_id"]).']';
    $short_code = apply_filters('the_content', $content);
		echo do_shortcode( $short_code );
		?>
	</div>
	<?php
}

/*
* END OF QUIZ ADMIN
*/

/*
* START OF QUIZ FRONT DISPLAY (LISTING, SINGLE & RESULT PAGE)
*/

//Count of published quizzes
function count_quizes($args)
{
  //Include JS
  wp_enqueue_script( 'listing_awareness_center-js', get_stylesheet_directory_uri() . '/js/quizes/listing_awareness_center.js', array('jquery'), '', true);
  wp_localize_script('listing_awareness_center-js', 'ef_listing_awareness_center', array("per_page" => constant("ef_awareness_quiz_per_page")));

  global $wpdb;
  $sql = "select distinct `{$wpdb->prefix}mlw_quizzes`.`quiz_id`,(select count(*) from `{$wpdb->prefix}mlw_questions` where quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
      and `{$wpdb->prefix}mlw_questions`.`deleted` = 0) as question_count
    from `{$wpdb->prefix}mlw_quizzes`
    inner join `{$wpdb->prefix}postmeta` as `pmeta` on `pmeta`.`meta_value` = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
    inner join `{$wpdb->prefix}postmeta` as `pmeta_language` on `pmeta_language`.`post_id` = `pmeta`.`post_id`
    inner join `{$wpdb->prefix}posts` as `post` on `post`.`ID` = `pmeta`.`post_id`
    inner join `{$wpdb->prefix}postmeta` as `pmeta_category` on `post`.`ID` = `pmeta_category`.`post_id` ";

  //select of user taken
  $sql.= " left join `{$wpdb->prefix}mlw_results` as results on results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `results`.deleted = 0 and `results`.user = {$args['current_user']} ";

  $sql.= " where  `{$wpdb->prefix}mlw_quizzes`.`deleted` = 0
  and `pmeta_language`.`meta_value` like '%{$args['lang']}%'
  and `pmeta`.`meta_key` = 'quiz_id' and `pmeta_language`.`meta_key` = 'language'
  and `post`.`post_status` = 'publish' and `post`.`post_type` = 'quiz'
  and `pmeta_category`.meta_key = 'category' ";

  if($args['category'] != -1)
  {
    $sql .= " and `pmeta_category`.meta_value = {$args['category']}";
  }

  $sql .= " group by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` having question_count > 0";
  $results = $wpdb->get_results($sql);
  return $results;
}

//List of published quizes
function list_quizes($args)
{
  $no_of_posts = constant("ef_awareness_quiz_per_page");
  $args['offset'] = (get_query_var("ef_listing_awareness_center_offset") ? get_query_var("ef_listing_awareness_center_offset") : 0);

  //validate category
  if(!is_numeric($args['category']))
  {
    $args['category'] = -1;
  }

  $set_minimum_score = returnAwarenessCenterMinimumScore();

  global $wpdb;
  $sql = "select distinct `{$wpdb->prefix}mlw_quizzes`.`quiz_id`, `{$wpdb->prefix}mlw_quizzes`.`quiz_name`, `post`.`ID`,
    `post`.`post_date`, count(`results`.`quiz_id`) taken,
    (select count(*) from `{$wpdb->prefix}mlw_questions` where quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
      and `{$wpdb->prefix}mlw_questions`.`deleted` = 0) as question_count,
    `{$wpdb->prefix}mlw_quizzes`.`quiz_taken`, max(`results`.`correct_score`) as highest_score,
    (select ifnull(count(*),-1) from `{$wpdb->prefix}mlw_results` as success_rate_results  where success_rate_results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
    and success_rate_results.deleted = 0 and success_rate_results.correct_score >= {$set_minimum_score}) as success_rate
    from `{$wpdb->prefix}mlw_quizzes`
    inner join `{$wpdb->prefix}postmeta` as `pmeta` on `pmeta`.`meta_value` = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
    inner join `{$wpdb->prefix}postmeta` as `pmeta_language` on `pmeta_language`.`post_id` = `pmeta`.`post_id`
    inner join `{$wpdb->prefix}posts` as `post` on `post`.`ID` = `pmeta`.`post_id`
    inner join `{$wpdb->prefix}postmeta` as `pmeta_category` on `post`.`ID` = `pmeta_category`.`post_id` ";

  //select of user taken
  $sql.= " left join `{$wpdb->prefix}mlw_results` as results on results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `results`.deleted = 0 and `results`.user = {$args['current_user']} ";

  $sql.= " where  `{$wpdb->prefix}mlw_quizzes`.`deleted` = 0
  and `pmeta_language`.`meta_value` like '%{$args['lang']}%'
  and `pmeta`.`meta_key` = 'quiz_id' and `pmeta_language`.`meta_key` = 'language'
  and `post`.`post_status` = 'publish' and `post`.`post_type` = 'quiz'
  and `pmeta_category`.meta_key = 'category' ";

  if($args['category'] != -1)
  {
    $sql .= " and `pmeta_category`.meta_value = {$args['category']}";
  }

  $sql .= " group by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` having question_count > 0 order by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` desc limit {$args['offset']}, {$no_of_posts} ";
  $results = $wpdb->get_results($sql);
  return $results;
}

function ef_load_more_listing_awareness_center()
{
  set_query_var('ef_listing_awareness_center_offset', $_POST['offset']);
  set_query_var('ef_listing_awareness_center_category_id', $_POST['category']);
  get_template_part('template-parts/quizes/content', 'listing_awareness_center');
  die();
}
add_action('wp_ajax_ef_load_more_listing_awareness_center', 'ef_load_more_listing_awareness_center');
add_action('wp_ajax_nopriv_ef_load_more_listing_awareness_center', 'ef_load_more_listing_awareness_center');

function ef_load_more_listing_count_awareness_center()
{
  $category = $_POST['category'];
  $args = array(
    'lang' => pll_current_language(),
    'current_user' => get_current_user_id(),
    'category' => $category
  );
  $results = count_quizes($args);
  echo sizeof($results);
  die();
}
add_action('wp_ajax_ef_load_more_listing_count_awareness_center', 'ef_load_more_listing_count_awareness_center');
add_action('wp_ajax_nopriv_ef_load_more_listing_count_awareness_center', 'ef_load_more_listing_count_awareness_center');

//change of css of single quiz
function ef_return_display($return_display)
{
  return str_replace("qmn_quiz_form", "ef_qmn_quiz_form", $return_display);
}
add_filter('qmn_end_shortcode','ef_return_display',10,1);
//apply_filters('qmn_end_shortcode', $return_display, $qmn_quiz_options, $qmn_array_for_variables)

function ef_redirect_to_quiz_result()
{
  if(isset($_GET["quiz"]))
  {
    global $wpdb;
    // Load last result saved
    if(is_numeric($_GET["quiz"]))
    {
      //check if user should take quiz beginner badge
      load_orm();
      $first_quiz_badge = new Badge(get_current_user_id());
      $first_quiz_badge->efb_manage_beginner_quiz_badge();

      // send emails with earned badge
      foreach( $first_quiz_badge->badges_earned as $badge ) {
        global $wpdb;
        $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
        $result = $wpdb->get_results($query, ARRAY_A);

        if( class_exists( 'EFBBadges' ) && !empty( $result ) )
        {
          sendNewBadgeAchiever( get_current_user_id(), new EFBBadges( $result[0] ) );
        }
      }

      //check foss specialist badge
      $specialist_quiz_badge = new Badge(get_current_user_id());
      $specialist_quiz_badge->efb_manage_specialist_quiz_badge();
      // send emails with earned badge
      foreach( $specialist_quiz_badge->badges_earned as $badge ) {
        global $wpdb;
        $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
        $result = $wpdb->get_results($query, ARRAY_A);

        if( class_exists( 'EFBBadges' ) && !empty( $result ) )
        {
          sendNewBadgeAchiever( get_current_user_id(), new EFBBadges( $result[0] ) );
        }
      }

      $sql = "select result_id,time_taken_real from {$wpdb->prefix}mlw_results as results where quiz_id = {$_GET["quiz"]}"
      . " and user = ".get_current_user_id()." order by result_id desc";
      $lastResultID = $wpdb->get_row($sql);

      // update time taken to Cairo Time
      $wp_timezone = get_option('timezone_string');
      if($wp_timezone != ""){
        $date = new DateTime($lastResultID->time_taken_real);
        $date->setTimezone(new DateTimeZone($wp_timezone));

        $wpdb->update($wpdb->prefix.'mlw_results',array( 'time_taken' => $date->format('h:i:s A m/d/Y'),
            time_taken_real => $date->format('Y-m-d H:i:s')),array( 'result_id' => $lastResultID->result_id ));
      }

      echo home_url()."/".pll_current_language()."/awareness-center/quiz/result/". $lastResultID->result_id;
      die();
    }
  }
  echo "-1";
  die();
}
add_action('wp_ajax_ef_redirect_to_quiz_result', 'ef_redirect_to_quiz_result');
add_action('wp_ajax_nopriv_ef_redirect_to_quiz_result', 'ef_redirect_to_quiz_result');

function ef_returnResult($result_id)
{
  $returnArr = array();
  if($result_id)
  {
    if(!is_numeric($result_id))
    {
      //cheating
      return "500";
    }

    global $wpdb;
    $sql = "select quiz_id,correct_score,user from {$wpdb->prefix}mlw_results where result_id = {$result_id} and deleted = 0";
    $result = $wpdb->get_row($sql);
    if($result)
    {
      //Load quiz by quiz_id
      $sql = "select message_after,quiz_id,quiz_name from {$wpdb->prefix}mlw_quizzes where quiz_id = $result->quiz_id and deleted = 0";
      $quiz = $wpdb->get_row($sql);
      if($quiz)
      {
        $output = "";
        $result_message = json_decode($quiz->message_after, true);
        if(!is_array($result_message))
        {
          $output = $quiz->message_after;
        }else {
          foreach($result_message as $message)
          {
            if($result->correct_score >= $message[0] && $result->correct_score <= $message[1])
            {
              $output = $message[2];
              break;
            }else if($output == "" && ($message[0] == 0 && $message[1] == 0))
            {
              $output = $message[2];
            }
          }
        }
        $returnArr["score"] = $result->correct_score;
        $returnArr["message"] = $output;
        $returnArr["quiz_id"] = $quiz->quiz_id;
        $returnArr["quiz_title"] = $quiz->quiz_name;
        $returnArr["user_id"] = $result->user;

        //get post id from quiz
        $sql = "select post_id from {$wpdb->prefix}postmeta as pmeta where pmeta.meta_key='quiz_id' and meta_value ='{$quiz->quiz_id}'";
        $post = $wpdb->get_col($sql);
        $returnArr["post_id"] = $post[0];
      }
      else
      {
       //Not Found
        return "404";
      }
    }else {
      //Not found
      return "404";
    }
  } else {
    //cheating
    return "500";
  }

  return $returnArr;
}

function ef_qmn_end_quiz_form($quiz_display)
{
  $doc = new DOMDocument();

  $doc->loadHTML(mb_convert_encoding($quiz_display, 'HTML-ENTITIES', 'UTF-8'));
  $xp = new DOMXpath($doc);
  if(pll_current_language() == ar)
  {
    //submit button language
    $nodes = $xp->query('//input[@type="submit"]');
    if($nodes != null)
    {
      $node = $nodes->item(0);
      $node->setAttribute("value", __("Submit","egyptfoss"));
    }

    //remove bottom error message
    $nodes = $xp->query('//div[@name="mlw_error_message_bottom"]');
    if($nodes != null)
    {
      $node = $nodes->item(0);
      $node->parentNode->removeChild($node);
    }

    $quiz_display = $doc->saveHTML();

    //replace hint to arabic
    $quiz_display = str_replace("Hint</span>", __("Hint","egyptfoss")."</span>", $quiz_display);
  }else {
    //submit button language
    $nodes = $xp->query('//input[@type="submit"]');
    $node = $nodes->item(0);
    $node->setAttribute("value", __("Submit","egyptfoss"));

    //remove bottom error message
    $nodes = $xp->query('//div[@name="mlw_error_message_bottom"]');
    if($nodes != null)
    {
      $node = $nodes->item(0);
      $node->parentNode->removeChild($node);
    }

    $quiz_display = $doc->saveHTML();
  }

  //replace error message by our own
  $quiz_display = str_replace("qmn_error_message_section", "ef_qmn_error_message_section collapse", $quiz_display);
  return $quiz_display;
}
add_filter('qmn_end_quiz_form', "ef_qmn_end_quiz_form",10,1);

function returnAwarenessCenterMinimumScore()
{
  include( ABSPATH . 'system_data.php' );
  return $awareness_success_rate;
}
