<?php

class QuizResult extends BaseModel {
	protected $table = 'mlw_results';
  
  public function addResult($quiz, $user, $answers, $timer)
  {
    $total_questions = $quiz->Questions()->count();
    $total_correct = 0;
    $quiz_results = array(intval($timer),self::calculateScore($quiz, $answers, $total_correct),"");
    $result = new QuizResult();
    $result->quiz_id = $quiz->quiz_id;
    $result->quiz_name = $quiz->quiz_name;
    $result->quiz_system = $quiz->system;
    $result->total = $total_questions;
    $result->point_score = 0;
    $result->correct_score = round((($total_correct/$total_questions)*100), 2);
    $result->correct = $total_correct;
    $result->business = '';
    $result->phone = '';
    $result->name = $user->user_login;
    $result->email = $user->user_email;
    $result->user = $user->ID;
    $result->user_ip = "Unknown";
    $result->quiz_results = serialize($quiz_results);
    $result->time_taken = date('h:i:s A m/d/Y');
    $result->time_taken_real = date('Y-m-d H:i:s');
    $result->deleted = 0;
    
    return $result;
  }
  
  private function calculateScore($quiz, $answers, &$total_correct)
  {
    $quiz_results = array();
    $questions = $quiz->Questions()->get();
    foreach($questions as $question)
    {
      //set default values
      $mlw_user_text = "";
      $mlw_correct_text = "";
      $qmn_correct = "incorrect";
      $qmn_answer_points = 0;
      foreach($answers as $user_answer)
      {
        if($question->question_id == $user_answer['question_id'])
        {
          $question_answers = unserialize($question->answer_array);
          foreach($question_answers as $question_answer)
          {
            if($question_answer[2] == 1)
            {
              $mlw_correct_text = $question_answer[0];
            }
            
            if($question_answer[3] == $user_answer['answer_id'])
            {
              $mlw_user_text = $question_answer[0];
              if($question_answer[2] == 1)
              {
                $qmn_correct = "correct";
                $total_correct += 1;
                break;
              }
            }
          }
        }
      }
      
      array_push($quiz_results, array(
          $question->question_name, htmlspecialchars($mlw_user_text, ENT_QUOTES), 
          htmlspecialchars($mlw_correct_text, ENT_QUOTES), "", "correct" => $qmn_correct,
          "id" => $question->question_id, "points" => $qmn_answer_points,"category" => "")
      );
    }
    
    return $quiz_results;
  }
}