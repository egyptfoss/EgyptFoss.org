<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
* This function generates the results details that are shown the results page.
*
* @return type void
* @since 4.4.0
*/
function ef_mlw_generate_result_details() {
	if ( ! current_user_can( 'moderate_comments' ) ) {
		return;
	}
	global $mlwQuizMasterNext;
	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'results';
	$tab_array = $mlwQuizMasterNext->pluginHelper->get_results_tabs();
	?>
	<div class="wrap">
		<h2><?php _e('Quiz Results', 'quiz-master-next'); ?></h2>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( $tab_array as $tab ) {
				$active_class = '';
				if ( $active_tab == $tab['slug'] ) {
					$active_class = 'nav-tab-active';
				}
				echo "<a href=\"?page=ef_mlw_quiz_result_details&&result_id=" . intval( $_GET["result_id"] ) . "&&tab=" . $tab['slug'] . "\" class=\"nav-tab $active_class\">" . $tab['title'] . "</a>";
			}
			?>
		</h2>
		<div>
		<?php
			foreach( $tab_array as $tab ) {
				if ( $active_tab == $tab['slug'] ) {
					call_user_func( $tab['function'] );
				}
			}
		?>
		</div>
	</div>
<style>
    .wrap {
        display: none;
    }
</style>
	<?php
  ef_qmn_generate_results_details_tab();
}


/**
* This function generates the results details tab that shows the details of the quiz
*
* @param type description
* @return void
* @since 4.4.0
*/
function ef_qmn_generate_results_details_tab() {
	echo "<br><br>";
	$mlw_result_id = intval( $_GET["result_id"] );
	global $wpdb;
	$mlw_results_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "mlw_results WHERE result_id=%d", $mlw_result_id ) );

	$previous_results = $wpdb->get_var( "SELECT result_id FROM " . $wpdb->prefix . "mlw_results WHERE result_id = (SELECT MAX(result_id) FROM " . $wpdb->prefix . "mlw_results WHERE deleted=0 AND result_id < ".$mlw_result_id.")" );
	$next_results = $wpdb->get_var( "SELECT result_id FROM " . $wpdb->prefix . "mlw_results WHERE result_id = (SELECT MIN(result_id) FROM " . $wpdb->prefix . "mlw_results WHERE deleted=0 AND result_id > ".$mlw_result_id.")" );
	if ( ! is_null( $previous_results ) && $previous_results ) {
		echo "<a class='button' href=\"?page=ef_mlw_quiz_result_details&&result_id=" . intval( $previous_results ) . "\" >View Previous Results</a> ";
	}
	if ( ! is_null( $next_results ) && $next_results ) {
		echo " <a class='button' href=\"?page=ef_mlw_quiz_result_details&&result_id=" . intval( $next_results ) . "\" >View Next Results</a>";
	}
	$settings = (array) get_option( 'qmn-settings' );
	if ( isset( $settings['results_details_template'] ) ) {
		$template = htmlspecialchars_decode( $settings['results_details_template'], ENT_QUOTES );
	} else {
		$template = "<h2>Quiz Results for %QUIZ_NAME%</h2>
		<p>Username: %USER_NAME%<br/>
		Score Received: %AMOUNT_CORRECT%/%TOTAL_QUESTIONS% or %CORRECT_SCORE%% or %POINT_SCORE% points</p>
		<h2>Answers Provided:</h2>
		<p>The user took %TIMER% seconds to complete quiz.</p>
		<p>The answers were as follows:</p>
		%QUESTIONS_ANSWERS%";
	}
	if ( is_serialized( $mlw_results_data->quiz_results ) && is_array( @unserialize( $mlw_results_data->quiz_results ) ) ) {
		$results = unserialize($mlw_results_data->quiz_results);
	} else {
		$template = str_replace( "%QUESTIONS_ANSWERS%" , $mlw_results_data->quiz_results, $template);
		$template = str_replace( "%TIMER%" , '', $template);
		$template = str_replace( "%COMMENT_SECTION%" , '', $template);
		$results = array(
			0,
			array(),
			''
		);
	}
  $user = get_userdata($mlw_results_data->user);
	$qmn_array_for_variables = array(
		'quiz_id' => $mlw_results_data->quiz_id,
		'quiz_name' => $mlw_results_data->quiz_name,
		'quiz_system' => $mlw_results_data->quiz_system,
		'user_name' => "<a href=\"/wp-admin/user-edit.php?user_id=$user->ID\">".$user->user_login."</a>",
		'user_business' => $mlw_results_data->business,
		'user_email' => $mlw_results_data->email,
		'user_phone' => $mlw_results_data->phone,
		'user_id' => $mlw_results_data->user,
		'timer' => $results[0],
		'time_taken' => $mlw_results_data->time_taken,
		'total_points' => $mlw_results_data->point_score,
		'total_score' => $mlw_results_data->correct_score,
		'total_correct' => $mlw_results_data->correct,
		'total_questions' => $mlw_results_data->total,
		'comments' => $results[2],
		'question_answers_array' => $results[1]
	);
	$template = apply_filters( 'mlw_qmn_template_variable_results_page', $template, $qmn_array_for_variables );
	$template = str_replace( "\n" , "<br>", $template );
	echo $template;
}