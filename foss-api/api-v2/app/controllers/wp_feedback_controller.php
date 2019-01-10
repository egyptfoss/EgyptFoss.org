<?php
class WPFeedbackController extends EgyptFOSSController {
  /**
   * @SWG\Post(
   *   path="/feedback",
   *   tags={"Feedback"},
   *   summary="Creates New Feedback",
   *   description="Create a new Feedback with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to submit a new feedback<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="feedback_title", in="formData", required=false, type="string", description="Feedback Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="feedback_description", in="formData", required=false, type="string", description="Feedback Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="feedback_sections", in="formData", required=false, type="string", description="Feedback Section<br/> <b>Values: </b> any of provided sections from setup data or leave empty to select default value"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Feedback Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Feedback added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addFeedback($request, $response, $args) {
    global $ef_sections ;
    $params = $request->getHeaders();
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    $parameters = ['feedback_title', 'feedback_description', 'feedback_sections', 'lang']; 
    $required_params = ['feedback_title', 'feedback_description', 'lang'];
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter])){
        $args[$parameter] = $_POST[$parameter];
      } else{
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
//    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
//    if ($loggedin_user == null) {
//      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
//    }
//    
//    $user_id = $loggedin_user->user_id;
//    $user = User::find($user_id);
//    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
//      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
//    }

    $parametersToCheck = array_diff($parameters, array("token", "lang"));
    foreach ($parametersToCheck as $param){
      if(!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && strlen($args[$param])> 0){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param));
      }
    }
    
    if(mb_strlen($args["feedback_title"], 'UTF-8') < 10 || mb_strlen($args["feedback_title"], 'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","title",array("range"=>"10 to 100 char")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    //check unique feedback title
//    if (!empty(Post::where('feedback_title' , '=', $args['feedback_title'])->where('post_type' , '=', 'feedback')
//                ->whereIn('post_status' , array('publish','pending'))->first())) {
//      return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "feedback_title", array("range"=>"already exists")));
//    }
    if($args['feedback_sections'] == "") {
      $feedback_sections = "general";
    }else if (array_key_exists($args['feedback_sections'], $ef_sections)){
      $feedback_sections = $args['feedback_sections'] ;
    }
    else{
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "Sections"));
    }
    $newFeedback = new Post();
    $post_data = array("post_title" => $args['feedback_title'],
                       "post_status" => "pending",
                       "post_content" => $args["feedback_description"],
                       "post_type" => "feedback",
                       "post_author" => $loggedin_user->user_id
      );
    $newFeedback->addPost($post_data);
    $newFeedback->save();
    $newFeedback->add_post_translation($newFeedback->id, $args["lang"], "feedback");
    $feedbackMeta = new Postmeta();
    $feedbackMeta->updatePostMeta($newFeedback->id, 'sections', $feedback_sections);
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Feedback Added"));
  }
}
