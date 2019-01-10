<?php

class WPAwarnessCenterController extends EgyptFOSSController {

  /**
   * @SWG\GET(
   *   path="/awareness-center/",
   *   tags={"Awareness center"},
   *   summary="List Awareness center quizes and surverys",
   *   description="List Awareness center quizes and surverys <br/> Quiz Categories can be retrieved from Setupdata",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user highest score and number of trials with each quiz <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="category", in="query", required=false, type="string", description="Filter list of quizzes by category <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Response(response="200", description="List Published Quizzes"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listQuizes($request, $response, $args){
    
    //validate token
    $params = $request->getHeaders();
    $args['user_id'] = -1;
    if(isset($params['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) 
      {
        $args['user_id'] = $loggedin_user->user_id;
      }else 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    }
    
    $parameters = ['pageNumber', 'numberOfData', 'lang', 'category'];
    $requiredParams = ['pageNumber', 'numberOfData', 'lang'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['numberOfData'] ) || $args['numberOfData'] < 1 || $args['numberOfData'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of quizes/surveys',array("range"=> "1 and 25 ")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      $total_data_to_retrieve = $args['numberOfData'];
      $quizes = new Quiz();
      $skip_number = ($args['pageNumber'] * $args['numberOfData']) - $args['numberOfData'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      $args["skip"] = $skip_number;
      
      //get category id
      if(!isset($args["category"]))
      {
        $category_id = '';
      }
      else
      {
        $term_taxonomy = $this->check_term_exists($args['category'], 'quiz_categories');
        if($term_taxonomy)
        {
          $category_id = $term_taxonomy->term_id;
        }else
        {
          $category_id = -1;
        }
      }      
      
      if($category_id == -1)
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","category"));
      }
      
      $args['category_id'] = $category_id;
      
      $list_quizes = $quizes->listQuizes($args);
      $awarness_list_result = array();
      if (count($list_quizes) == 0){
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{        
        $args['numberOfData'] = -1;
        $total_count = count($quizes->listQuizes($args));
        $awarness_list_result = $this->ef_load_data_counts($total_count, $total_data_to_retrieve);
        $index = 0;
        foreach ($list_quizes as $quiz) 
        {
          $quiz_post_id = $quiz->ID;

          $post_meta = new Postmeta();
          $meta = $post_meta->getPostMeta($quiz_post_id);
          $awarness_center_meta = array();
          foreach ($meta as $meta_key => $meta_value ) {
            $awarness_center_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
          }
          unset($meta_value);
                      
          //get category
          $category = "";
          if(array_key_exists('category', $awarness_center_meta))
          {
            $term = new Term();
            $type_id = $awarness_center_meta['category'];
            $typeObj = $term->getTerm( $type_id );
            $category = $this->ef_return_name_by_lang($typeObj, $args["lang"]);
          }

          //get interests
          $interests = array();
          if(array_key_exists('interest', $awarness_center_meta))
          {
            $interests_arr = unserialize($awarness_center_meta['interest']);
            for($i = 0; $i < sizeof($interests_arr); $i++)
            {
                if($interests_arr[$i] != '')
                {
                    $term = new Term();
                    $interest_id = $interests_arr[$i];
                    array_push($interests, html_entity_decode($term->getTerm($interest_id)->name));
                }
            }
          }
          
          $awarness_list_result['data'][$index] = array(
              "quiz_id"     => $quiz->quiz_id,
              "quiz_title"  => $quiz->quiz_name,
              "category"    => $category,
              "interest"    => $interests,
              "taken" => ($quiz->taken > 0)?true:false,
              "highest_score" => ($quiz->highest_score != null)?$quiz->highest_score:"0",
              "number_of_trials" => $quiz->taken,
              "success_rate"  => ($quiz->quiz_taken <= 0)?"":round(($quiz->success_rate/$quiz->quiz_taken) * 100).'%',
              "created_date" => $quiz->post_date
          );
          
          $index += 1;
        }
        
        unset($quiz);
        $result = $this->renderJson($response, 200, $awarness_list_result);
      }
      return $result;
    }
  }
  
    /**
   * @SWG\GET(
   *   path="/awareness-center/{quiz_id}",
   *   tags={"Awareness center"},
   *   summary="Retrieve Quiz details",
   *   description="Retrieve Quiz details with list of questions, their answers, and hints with each question",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to show the quiz <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="quiz_id", in="path", required=false, type="string", description="quiz_id used to retrieve quiz info <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Display Quiz Details and Questions"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function getQuiz($request, $response, $args){
    
    //validate token
    $params = $request->getHeaders();
    if(isset($params['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) 
      {
        $args['user_id'] = $loggedin_user->user_id;
      }else 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    }else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }

    if( !$this->user_can($loggedin_user->user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    if($args['quiz_id'] == "{quiz_id}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","quiz_id"));
    }
    else{
      
      //validate valid quiz
      $quiz = Quiz::getQuiz($args["quiz_id"]); 
      if(!$quiz)
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Quiz"));
      }
      
      $is_random = $quiz->randomness_order;
      
      //Load Quiz Questions
      $quiz_questions = QuizQuestion::listQuestions($args["quiz_id"]);
      $questions = array();
      foreach($quiz_questions as $question)
      {
        $answers = array();
        $quiz_answers = unserialize($question->answer_array);
        foreach($quiz_answers as $answer)
        {          
          array_push($answers, array(
              "id" => $answer[3],
              "answer" => html_entity_decode($answer[0])
          ));
        }
        
        if($is_random == 2 || $is_random == 3)
        {
          shuffle($answers);
        }
        
        $question_required = unserialize($question->question_settings);
        //in plugin 0=>required, 1=> not required
        if($question_required["required"] == 0)
        {
          $is_required = 1;
        }else {
          $is_required = 0;
        }
        array_push($questions, array(
            "question_id" => $question->question_id,
            "question_name" => html_entity_decode($question->question_name),
            "answers" =>$answers,
            "hint" => $question->hints,
            "is_required" => $is_required
        ));
      }
      
      //randomize questions
      if($is_random == 1 || $is_random == 2)
      {
        shuffle($questions);
      }
      
      $quiz_data = array(
          "quiz_name" => $quiz->quiz_name,
          "quiz_intro_message" => html_entity_decode(str_replace("%QUIZ_NAME%", $quiz->quiz_name, $quiz->message_before)),
          "quiz_end_message" => html_entity_decode(($quiz->message_end_template != null)?str_replace("%QUIZ_NAME%", $quiz->quiz_name, $quiz->message_end_template):""),
          "questions" => $questions
      );
      
      return $this->renderJson($response, 200, $quiz_data);
    }
  }
  
  /**
   * @SWG\POST(
   *   path="/awareness-center/{quiz_id}/take",
   *   tags={"Awareness center"},
   *   summary="Take single Quiz",
   *   description="User should be able to take and submit a single quiz",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to take the quiz <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="quiz_id", in="path", required=false, type="string", description="quiz_id need to take the quiz with this ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="answers", in="formData", required=false, type="string", description="array of json objects that contains question_id and answer_id whcih is the id of the answer that the user provided<br/> <b>Values: </b> [{""question_id"":1,""answer_id"":1},{""question_id"":1,""answer_id"":1}]<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="timer", in="formData", required=false, type="string", description="timer to complete the quiz, and timer is in seconds <br/> <b>Values: </b> 120<br/> <b>[Required]</b>"), 
   *   @SWG\Response(response="200", description="Submit Quiz Answers"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function takeQuiz($request, $response, $args){
    //validate token
    $params = $request->getHeaders();
    if(isset($params['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) 
      {
        $args['user_id'] = $loggedin_user->user_id;
      }else 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    }else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }

    
    //Load User
    $user = User::find($args["user_id"]); 
    if( !$this->user_can($user->ID, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //validate answers
    $parameters = ['answers', 'timer']; 
    $required_params = ['answers', 'timer'];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter]))
      {
        $args[$parameter] = $_POST[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params))
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    //validate timer is numeric
    if(!is_numeric($args["timer"]))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "timer"));
    }
    
    //validate answers json format
    $answers = json_decode(html_entity_decode($args['answers']), TRUE);
    if(json_last_error() !== JSON_ERROR_NONE)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongJsonFormat", "answers"));
    }
    
    if($args['quiz_id'] == "{quiz_id}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","quiz_id"));
    }
    else
    {  
      //validate valid quiz
      $quiz = Quiz::getQuiz($args["quiz_id"], false); 
      if(!$quiz->first())
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Quiz"));
      }
      
      //validate answers has all keys
      foreach($answers as $answer)
      {
        if(!isset($answer["question_id"]) || !isset($answer["answer_id"]))
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongJsonFormat", "answers"));
        }
      }
      
      //validate all required questions are answered
      $questions = $quiz->first()->Questions()->get();
      foreach($questions as $question)
      {
        $answer_index = 0;
        $question_required = unserialize($question->question_settings);
        //in plugin 0 => required, 1=> not required
        if($question_required["required"] == 0)
        {
          foreach($answers as $user_answer)
          {
            if($user_answer['question_id'] == $question->question_id)
            {
              break;
            }
                      
            $answer_index += 1;
            if($answer_index == sizeof($answers))
            {
              return $this->renderJson($response, 422, Messages::getErrorMessage("notAnsweredRequiredQuestions", "answers"));
            }
          }
        }
      }
      
      $result = new QuizResult();
      $addResult = $result->addResult($quiz->first(), $user, $answers, $args["timer"]);
      $addResult->save();
      
      //Increase number of taken in quiz
      $update_quiz = Quiz::where('quiz_id','=',$quiz->first()->quiz_id);
      if($update_quiz->first())
      {
        $update_quiz->update(array("quiz_taken"=> ($update_quiz->first()->quiz_taken + 1)));
      }      
      
      // give user foss beginner badge
      $beginner_badge = new Badge($user->ID);
      $beginner_badge->efb_manage_beginner_quiz_badge();
      
      //giver user foss specialist badge
      $specialist_quiz_badge = new Badge($user->ID);
      $specialist_quiz_badge->efb_manage_specialist_quiz_badge();
      
      $output =  Messages::getSuccessMessage("Success", "Your answers saved" );
      $output['score'] = $addResult->correct_score."%";
      
      // return new success rate
      $quiz_success_rate = new Quiz();
      $quiz_success_rate_result = $quiz_success_rate->getQuizSuccessRate($quiz->first()->quiz_id);
      $output['success_rate'] = ($quiz_success_rate_result[0]->quiz_taken <= 0)?"":round(($quiz_success_rate_result[0]->success_rate/$quiz_success_rate_result[0]->quiz_taken) * 100).'%';
      
      // set quiz url
      $post_id = $quiz_success_rate->getQuizPost($quiz->first()->quiz_id);
      $output['post_url'] = html_entity_decode(Post::where('ID', '=', $post_id)->first()->guid);
      
      // return quiz lang
      $post_meta = new Postmeta();
      $meta = $post_meta->getPostMeta($post_id);
      $news_meta = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
      }
      unset($meta_value);      
      $language = "en";
      if(array_key_exists('language', $news_meta))
      {
        $lang = $news_meta['language'];
        $lang_arr = unserialize(unserialize($lang));
        $language = $lang_arr['slug'];

        if($language == null)
        {
          $language = "en";
        }
      }
      $output['quiz_language'] = $language;
      //Load output body
      $body = "";
      $output_body_array = json_decode($quiz->first()->message_after, true);
      if(!is_array($output_body_array))
      {
        $body = $quiz->first()->message_after;
      }else {
        
        
        foreach($output_body_array as $message)
        {
          if($addResult->correct_score >= $message[0] && $addResult->correct_score <= $message[1])
          {
            $body = $message[2];
            break;
          }else if($body == "" && ($message[0] == 0 && $message[1] == 0))
          {
            $body = $message[2];
          }
        } 
      }      
      $output['body'] = html_entity_decode($body);
      return $this->renderJson($response, 200, $output);
    }
  }
  
  /**
   * @SWG\GET(
   *   path="/awareness-center/quiz/result/{result_id}",
   *   tags={"Awareness center"},
   *   summary="Retrieve Result details",
   *   description="Retrieve Result details By result id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to show user result <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="result_id", in="path", required=false, type="string", description="result_id used to retrieve quiz result <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Display Result Details"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function getResult($request, $response, $args)
  {
    //validate token
    $params = $request->getHeaders();
    if(isset($params['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) 
      {
        $args['user_id'] = $loggedin_user->user_id;
      }else 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    }else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    if($args['result_id'] == "{result_id}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","result_id"));
    }
    else{
      //Load Result
      $result = QuizResult::where('result_id','=', $args['result_id'])->where('deleted','=',0)
              ->first();
      if(!$result) 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "result_id"));
      }
      
      //check that user is owner of result
      if($loggedin_user->user_id != $result->user)
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }
      
      //Load Quiz From Result
      $quiz = Quiz::where('quiz_id','=', $result->quiz_id)->where('deleted','=',0)
              ->first();
      if(!$quiz) 
      {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "quiz"));
      }
      
      $body = "";
      $output_body_array = json_decode($quiz->message_after, true);
      if(!is_array($output_body_array))
      {
        $body = $quiz->message_after;
      }else {
        foreach($output_body_array as $message)
        {
          if($result->correct_score >= $message[0] && $result->correct_score <= $message[1])
          {
            $body = $message[2];
            break;
          }else if($body == "" && ($message[0] == 0 && $message[1] == 0))
          {
            $body = $message[2];
          }
        } 
      }
      $output =  Messages::getSuccessMessage("Success", "Your Result" );  
      $output['score'] = $result->correct_score."%";
      $output['body'] = html_entity_decode($body);
      
      return $this->renderJson($response, 200, $output);
    }
  }
}