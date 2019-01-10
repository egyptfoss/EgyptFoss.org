<?php

class Quiz extends BaseModel {
	protected $table = 'mlw_quizzes';
  
  public function Questions()
  {
      return $this->hasMany('QuizQuestion','quiz_id','quiz_id');
  }
  
  public function addQuiz($args)
  {
    $quiz = new Quiz();
    $quiz->quiz_name = $args["name"];
    $quiz->message_before = $args["message_before"];
    $quiz->message_after = $args["message_after"];
    $quiz->message_comment = '';
    $quiz->message_end_template = $args["message_end_template"];
    $quiz->user_email_template = $args["user_email_template"];
    $quiz->admin_email_template = $args["admin_email_template"];
    $quiz->submit_button_text = "Submit";
    $quiz->name_field_text = "Name";
    $quiz->business_field_text = "Business";
    $quiz->email_field_text = "Email";
    $quiz->phone_field_text = "Phone Number";
    $quiz->comment_field_text = "Comments";
    $quiz->email_from_text = "Wordpress";
    $quiz->question_answer_template = "%QUESTION%<br /> Answer Provided: %USER_ANSWER%<br /> Correct Answer: %CORRECT_ANSWER%<br /> Comments Entered: %USER_COMMENTS%<br />";
    $quiz->leaderboard_template = "<h3>Leaderboard for %QUIZ_NAME%</h3>
			1. %FIRST_PLACE_NAME%-%FIRST_PLACE_SCORE%<br />
			2. %SECOND_PLACE_NAME%-%SECOND_PLACE_SCORE%<br />
			3. %THIRD_PLACE_NAME%-%THIRD_PLACE_SCORE%<br />
			4. %FOURTH_PLACE_NAME%-%FOURTH_PLACE_SCORE%<br />
			5. %FIFTH_PLACE_NAME%-%FIFTH_PLACE_SCORE%<br />";
    $quiz->system = 0;
    $quiz->randomness_order = $args["randomness"];
    $quiz->loggedin_user_contact = 1;
    $quiz->show_score = 0;
    $quiz->send_user_email = 0;
    $quiz->send_admin_email = 0;
    $quiz->contact_info_location = 0;
    $quiz->user_name = 0;
    $quiz->user_comp = 0;
    $quiz->user_email = 0;
    $quiz->user_phone = 0;
    $quiz->admin_email = "";
    $quiz->comment_section = 1;
    $quiz->question_from_total = 0;
    $quiz->total_user_tries = 0;
    $quiz->total_user_tries_text = "You are only allowed 1 try and have already submitted your quiz.";
    $quiz->certificate_template = "Enter Your Text Here!";
    $quiz->social_media = 0;
    $quiz->social_media_text = "I just scored %CORRECT_SCORE%% on %QUIZ_NAME%!";
    $quiz->pagination = 0;
    $quiz->pagination_text = "Next";
    $quiz->timer_limit = 0;
    $quiz->quiz_stye = ".mlw_qmn_quiz label {
				display: inline;
			}
			.ui-tooltip
			{
				max-width: 500px !important;
			}
			.ui-tooltip-content
			{
				max-width: 500px !important;
			}
			.qmn_error, .qmn_page_error_message
			{
				color: red;
			}
			.mlw_qmn_hint_link
			{
			text-decoration:underline;
			color:rgb(0,0,255);
			}
			.mlw_qmn_quiz_link
			{
				display: inline;
				vertical-align:top !important;
				text-decoration: none;
			}
			div.mlw_qmn_quiz input[type=radio],
			div.mlw_qmn_quiz input[type=submit],
			div.mlw_qmn_quiz label {
				cursor: pointer;
			}
			div.mlw_qmn_quiz input:not([type=submit]):focus,
			div.mlw_qmn_quiz textarea:focus {
				background: #eaeaea;
			}
			div.mlw_qmn_quiz {
				text-align: left;
			}
			div.quiz_section {

			}
			.mlw_horizontal_choice
			{
				margin-right: 20px;
			}
			div.mlw_qmn_timer {
				position:fixed;
				top:200px;
				right:0px;
				width:130px;
				color:#00CCFF;
				border-radius: 15px;
				background:#000000;
				text-align: center;
				padding: 15px 15px 15px 15px
			}
			div.mlw_qmn_quiz input[type=submit],
			a.mlw_qmn_quiz_link
			{
				border-radius: 4px;
				position: relative;
				background-image: linear-gradient(#fff,#dedede);
				background-color: #eee;
				border: #ccc solid 1px;
				color: #333;
				text-shadow: 0 1px 0 rgba(255,255,255,.5);
				box-sizing: border-box;
				display: inline-block;
				padding: 7px 7px 7px 7px;
				margin: auto;
				font-weight: bold;
				cursor: pointer;
			}
			.mlw_qmn_question, .mlw_qmn_question_number, .mlw_qmn_comment_section_text
			{
				font-weight: bold;
			}
			.mlw_next
			{
				float: right;
			}
			.mlw_previous
			{
				float: left;
			}
			.mlw_qmn_question_comment, .mlw_answer_open_text, .qmn_comment_section {
				width: 100%;
				border-radius: 7px;
				padding: 2px 10px;
				-webkit-box-shadow: inset 0 3px 3px rgba(0,0,0,.075);
				box-shadow: inset 0 3px 3px rgba(0,0,0,.075);
				border: 1px solid #ccc;
			}
		";
    $quiz->question_numbering = 0;
    $quiz->quiz_settings = "";
    $quiz->theme_selected = "primary";
    $quiz->last_activity = "";
    $quiz->require_log_in = 0;
    $quiz->require_log_in_text = "This quiz is for logged in users only.";
    $quiz->limit_total_entries = 0;
    $quiz->limit_total_entries_text = "Unfortunately, this quiz has a limited amount of entries it can recieve and has already reached that limit.";
    $quiz->scheduled_timeframe = "";
    $quiz->scheduled_timeframe_text = "";
    $quiz->disable_answer_onselect = 0;
    $quiz->ajax_show_correct = 0;
    $quiz->quiz_views = 0;
    $quiz->quiz_taken = 0;
    $quiz->deleted = 0;
    
    return $quiz;
  }
  
  public function listQuizes($args, $interests = null, $fromDate = null, $today = null)
  {
    global $foss_prefix;
    global $awareness_success_rate;
    $set_minimum_score = $awareness_success_rate;
    $results = Quiz::selectRaw("`{$foss_prefix}mlw_quizzes`.`quiz_id`, `{$foss_prefix}mlw_quizzes`.`quiz_name`, `{$foss_prefix}post`.`ID`, `{$foss_prefix}post`.`post_name`, (select count(*) from `{$foss_prefix}mlw_questions` where quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id` and `{$foss_prefix}mlw_questions`.`deleted` = 0) question_count, `{$foss_prefix}post`.`post_date`, count( `{$foss_prefix}results`.`quiz_id`) taken,`{$foss_prefix}mlw_quizzes`.`quiz_taken`,max(`{$foss_prefix}results`.`correct_score`) as highest_score,(select ifnull(count(*),-1) from `{$foss_prefix}mlw_results` as success_rate_results  where success_rate_results.quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id`and success_rate_results.deleted = 0 and success_rate_results.correct_score >= {$set_minimum_score}) as success_rate ")
            ->join('postmeta as pmeta','pmeta.meta_value','=','mlw_quizzes.quiz_id');
    if($args['lang'] != '') {
      $results->join('postmeta as pmeta_language','pmeta_language.post_id','=','pmeta.post_id');
    }
    
    $results->join('posts as post','post.ID','=','pmeta.post_id');
    if($interests != null )
    {            
      $results->join('postmeta', 'post.ID', '=', 'postmeta.post_id'); 
    }
    $results->join('postmeta as pmeta_category', 'post.ID', '=', 'pmeta_category.post_id')
            ->leftjoin('mlw_results as results', function($join) use ($args) {
                $join->on('results.quiz_id', '=', "mlw_quizzes.quiz_id");
                $join->where('results.deleted', '=', 0);
                $join->where('results.user', '=', "{$args['user_id']}");
            })
            ->where('mlw_quizzes.deleted','=',0);
    if($args['lang'] != '') {        
      $results->where('pmeta_language.meta_key','=','language')
              ->where('pmeta_language.meta_value','like','%'.$args['lang'].'%');
    }
    
    $results->where('pmeta.meta_key','=','quiz_id')            
            ->where('post.post_status','=','publish')
            ->where('post.post_type','=','quiz')
            ->where('pmeta_category.meta_key','=','category')
            ;
    if($args['category_id'] != '')
    {
      $results->where('pmeta_category.meta_value','=',$args['category_id']);
    }
    
    if($interests != null )
    {
      $results->where('postmeta.meta_key', '=', 'interest')
              ->where('post.post_date', '>', $fromDate)
              ->where('post.post_date', '<', $today)
              ->where(function ($query) use ($interests) {
                foreach ($interests as $interest) {
                  $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
                }
              });
    }
    
    if($args['numberOfData'] != -1)
    {
      $results->take($args['numberOfData'])->skip($args['skip']);
    }
    
    $results->groupBy('mlw_quizzes.quiz_id')->having('question_count','>',0)->orderBy('mlw_quizzes.quiz_id','DESC');
            
    return $results->distinct()->get();
  }
  
  public function getQuizSuccessRate($quiz_id)
  {
    global $foss_prefix;
    global $awareness_success_rate;
    $set_minimum_score = $awareness_success_rate;
    $results = Quiz::selectRaw("`{$foss_prefix}mlw_quizzes`.`quiz_taken`,(select ifnull(count(*),-1) from `{$foss_prefix}mlw_results` as success_rate_results  where success_rate_results.quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id`and success_rate_results.deleted = 0 and success_rate_results.correct_score >= {$set_minimum_score}) as success_rate ")
            ->where('mlw_quizzes.deleted','=',0)
            ->where('mlw_quizzes.quiz_id','=', $quiz_id);
    
    return $results->distinct()->get();
  }
  
  public function getQuiz($quiz_id, $return_first = true)
  {
    $quiz = Quiz::select('mlw_quizzes.quiz_id','mlw_quizzes.quiz_name','mlw_quizzes.message_before','mlw_quizzes.message_after','mlw_quizzes.randomness_order','mlw_quizzes.system')
            ->join('mlw_questions as questions','questions.quiz_id','=','mlw_quizzes.quiz_id')
            ->join('postmeta as pmeta','pmeta.meta_value','=','mlw_quizzes.quiz_id')
            ->join('posts as post','post.ID','=','pmeta.post_id')
            ->where('questions.deleted','=',0)
            ->where('mlw_quizzes.deleted','=',0)
            ->where('pmeta.meta_key','=','quiz_id')
            ->where('post.post_status','=','publish')
            ->where('post.post_type','=','quiz')
            ->where('mlw_quizzes.quiz_id', '=', $quiz_id)
            ->groupBy('mlw_quizzes.quiz_id');
    if($return_first){
      return $quiz->first();
    }
    
    return $quiz;
  }
  
  public function listUserQuizzes($args)
  {
    global $foss_prefix;
    global $awareness_success_rate;
    $set_minimum_score = $awareness_success_rate;
    $sql = "select distinct `{$foss_prefix}mlw_quizzes`.`quiz_id`, `{$foss_prefix}mlw_quizzes`.`quiz_name`, `post`.`ID`,
      `post`.`post_date`,
      `{$foss_prefix}mlw_quizzes`.`quiz_taken`, 
      highest_result.result_id as highest_id,highest_result.correct_score as highest_score, highest_result.time_taken_real as highest_date,
      latest_result.result_id as latest_id,latest_result.correct_score as latest_score, latest_result.time_taken_real as latest_date,
      (select ifnull(count(*),-1) from `{$foss_prefix}mlw_results` as success_rate_results  where success_rate_results.quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id`and success_rate_results.deleted = 0 and success_rate_results.correct_score >= {$set_minimum_score}) as success_rate
      from `{$foss_prefix}mlw_quizzes`
      inner join `{$foss_prefix}postmeta` as `pmeta` on `pmeta`.`meta_value` = `{$foss_prefix}mlw_quizzes`.`quiz_id` 
      inner join `{$foss_prefix}posts` as `post` on `post`.`ID` = `pmeta`.`post_id` ";

    //select of user taken
    $sql.= " inner join `{$foss_prefix}mlw_results` as highest_result on highest_result.quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id` and `highest_result`.deleted = 0 and `highest_result`.user = ".  $args['user_id'];
    $sql.= " inner join `{$foss_prefix}mlw_results` as latest_result on latest_result.quiz_id = `{$foss_prefix}mlw_quizzes`.`quiz_id` and `latest_result`.deleted = 0 and `latest_result`.user = ".  $args['user_id'];

    $sql.= " where  `{$foss_prefix}mlw_quizzes`.`deleted` = 0 
    and `pmeta`.`meta_key` = 'quiz_id'
    and `post`.`post_status` = 'publish' and `post`.`post_type` = 'quiz' "
    . " and `latest_result`.result_id = 
    (SELECT result_id FROM `{$foss_prefix}mlw_results` where `{$foss_prefix}mlw_results`.quiz_id = `{$foss_prefix}mlw_quizzes`.quiz_id and `{$foss_prefix}mlw_results`.deleted = 0 and `{$foss_prefix}mlw_results`.user = ".  $args['user_id']." order by `{$foss_prefix}mlw_results`.result_id desc limit 1)"
    . " and `highest_result`.correct_score = 
    (SELECT max(correct_score) FROM `{$foss_prefix}mlw_results` where `{$foss_prefix}mlw_results`.quiz_id = `{$foss_prefix}mlw_quizzes`.quiz_id and `{$foss_prefix}mlw_results`.deleted = 0 and `{$foss_prefix}mlw_results`.user = ".$args['user_id'].")";

    $sql .= " group by `{$foss_prefix}mlw_quizzes`.`quiz_id` order by `{$foss_prefix}mlw_quizzes`.`quiz_id` desc ";
    if($args['offset'] != '')
    {
      $sql .= " limit {$args['offset']}, {$args['no_of_posts']}";
    }
    
    return  self::getConnectionResolver()->connection()->select($sql); 
  }
  
  public function getQuizPost($quiz_id)
  {
    $postmeta = Postmeta::where('meta_key','=','quiz_id')
            ->where('meta_value','=', $quiz_id)->first();
    
    return $postmeta->post_id;
  }
          
}