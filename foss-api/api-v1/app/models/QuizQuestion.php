<?php

class QuizQuestion extends BaseModel {
	protected $table = 'mlw_questions';
  
  public function addQuestion($args)
  {
    $question = new QuizQuestion();
    $question->quiz_id = $args["quiz_id"];
    $question->question_name = $args["name"];
    $question->answer_array = $args["answer_array"];
    $question->answer_one = "";
    $question->answer_one_points = 0;
    $question->answer_two = "";
    $question->answer_two_points = 0;
    $question->answer_three = "";
    $question->answer_three_points = 0;
    $question->answer_four = "";
    $question->answer_four_points = 0;
    $question->answer_five = "";
    $question->answer_five_points = 0;
    $question->answer_six = "";
    $question->answer_six_points = 0;    
    $question->correct_answer = 0;
    $question->question_answer_info = "";
    $question->comments = 1;
    $question->question_type = 0;
    $question->question_type_new = 0;
    $question->question_settings = "a:1:{s:8:\"required\";i:1;}";
    $question->category = "";
    $question->deleted = 0;
    
    return $question;
  }
  
  public function listQuestions($quiz_id)
  {
    return QuizQuestion::where('deleted', '=' , 0)
            ->where('quiz_id', '=', $quiz_id)
            ->get();
  }
}