<?php
$secure_routes = array(
  "POST:/collaboration/spaces/" => array("not_subscriber"),
  "POST:/collaboration/spaces/{space_id}/documents/" => array("not_subscriber"),
  "PUT:/collaboration/documents/{document_id}" => array("not_subscriber"),
  "POST:/collaboration/share-settings/users/{item_id}" => array("not_subscriber"),
  "POST:/collaboration/share-settings/groups/{item_id}" => array("not_subscriber"),
  "DELETE:/collaboration/share-settings/users/{item_id}" => array("not_subscriber"),
  "DELETE:/collaboration/spaces/delete/{item_id}" => array("not_subscriber"),
  "DELETE:/collaboration/documents/delete/{item_id}" => array("not_subscriber"),
  "PUT:/collaboration/spaces/{item_id}" => array("not_subscriber")
);

function authenticateRoutes($request,$response,$routeSecureOptions)
{
  
  foreach($routeSecureOptions as $routeSecureOption)
  {
    if($routeSecureOption == "not_subscriber" )
    {
      $put = $request->getParsedBody();
      $params = $request->getHeaders();
      if(isset($_POST["token"])){
        $params['HTTP_TOKEN'] = $_POST['token'];
      }else if(isset($put['token'])){
        $params['HTTP_TOKEN'] = $put['token'];
      }
      
      if(empty($params['HTTP_TOKEN']) )
      {
        return array("authenticated"=>false,
         "status"=>422,
         "message"=> Messages::getErrorMessage("missingValue","token"));
      }
    
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user == null) {
        return array("authenticated"=>false,
         "status"=>404,
         "message"=> Messages::getErrorMessage("notFound", "User"));
      }
    
      $user_id = $loggedin_user->user_id;
    
      if( !user_can($user_id, 'add_new_ef_posts') ) {
        return array("authenticated"=>false,
         "status"=>422,
         "message"=> Messages::getErrorMessage("unauthorized"));
      }
    }
    $_POST['logged_in_user_id'] = $user_id;
    return array("authenticated"=>true);
  }
}


  function renderJson($response, $status, $data) {
		$response->withStatus($status);
		$response->write(json_encode($data));
		$response = $response->withHeader(
			'Content-type', 'application/json; charset=utf-8'
		);
		return $response;
	}
  
  function user_can($user_id, $capability) {
		global $capabilities;
		$user_meta = new Usermeta;
		$role = $user_meta->getRole($user_id);
		return (array_key_exists($role, $capabilities) && in_array($capability, $capabilities[$role])) ? true : false;
	}

