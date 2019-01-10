<?php

class CollaborationCenterController extends EgyptFOSSController {

  /**
   * @SWG\Get(
   *   path="/collaboration/shared/",
   *   tags={"Collaboration Center"},
   *   summary="List shared documents with a certain user",
   *   description="List shared documents with a certain user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list shared documents<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List shared documents"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function listSharedItems($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    
    $collab_center = new CollaborationCenterItem();
    
    $return_results = $collab_center->getSharedItemsByUser($user_id)->get();
    
    if(sizeof($return_results) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $return_data = array();
    $spacesShared = array();
    $count = 0;
    for($i = 0; $i < sizeof($return_results); $i++)
    {
      if ($return_results[$i]["is_space"]) {
        array_push($spacesShared, $return_results[$i]["ID"]);
      } else {
        if (in_array($return_results[$i]["item_ID"], $spacesShared)) {
          continue;
        }
      }
      
      $return_data[$count]['added_by'] = $this->return_user_info_list($return_results[$i]['owner_id']);
      $return_data[$count]['item_id'] = $return_results[$i]["ID"];
      $return_data[$count]['parent_id'] = $return_results[$i]["item_ID"];
      $return_data[$count]['item_title'] = $return_results[$i]["title"];
      $return_data[$count]['item_content'] = $return_results[$i]["content"];  
      $return_data[$count]['item_status'] = $return_results[$i]["status"];  
      $return_data[$count]['no_of_files'] = count($return_results[$i]->documents()->get());
      $return_data[$count]['is_space'] = ($return_results[$i]["is_space"])?true:false;
      $return_data[$count]['is_shared_with_group'] = ($return_results[$i]["apiTaxs"] != null || $return_results[$i]["apiTypes"] != null)?true:false;
      $return_data[$count]['is_shared_with_individual'] = ($return_results[$i]["upiUsers"] != null)?true:false;
      $return_data[$count]['no_of_contributors'] = $return_results[$i]->getNoOfContributers();
      //$return_data[$count]['created_date'] = $return_results[$i]->created_date;
      $return_data[$count]["modified_date"] = $return_results[$i]->modified_date;
      $return_data[$count]['shared_by'] = $this->return_user_info_list($return_results[$i]['owner_id']);
      $count ++;
    }
    $results = $this->ef_load_data_counts(sizeof($return_data), -1);
    $results['data'] = $return_data;
    return $this->renderJson($response, 200, $results);
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/spaces/",
   *   tags={"Collaboration Center"},
   *   summary="List spaces with a certain user",
   *   description="List spaces with a certain user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user spaces<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List spaces"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function listSpaces($request, $response, $args) {
    $parameters = ['pageNumber', 'numberOfData']; 
    $required_params = ['pageNumber', 'numberOfData'];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_GET) && !empty($_GET[$parameter]))
      {
        $args[$parameter] = $_GET[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'numberOfData',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    $collab_center = new CollaborationCenterItem();
    $offset = ($args["numberOfData"] * $args["pageNumber"] ) - $args["numberOfData"];
    $return_results = $collab_center->getSpacesByUser($user_id,$offset,$args["numberOfData"]);
    if(sizeof($return_results) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $results = $this->ef_load_data_counts(sizeof($return_results), -1);
    $enhanced_view_results = array();
    for($i = 0; $i < sizeof($return_results); $i++)
    {
      //$return_results[$i]['added_by'] = $this->return_user_info_list($return_results[$i]['owner_id']);
      $enhanced_view_results[$i] = array(
        //"added_by"=>$return_results[$i]['added_by'],
        "space_id"=> $return_results[$i]->ID,
        "title"=> $return_results[$i]->title,
        "is_shared_with_individual" => ($return_results[$i]->getNoOfContributers() > 0)?true:false,
        "is_shared_with_group" => ($return_results[$i]->taxPermissions()->count() + $return_results[$i]->anonPermissions()->count()) > 0?true:false,
        "noOfContributors"=> $return_results[$i]->getNoOfContributers(),
        "noOfFiles"=> count($return_results[$i]->documents()->get()),
        //"created_date"=> $return_results[$i]->created_date,
        "modified_date"=> $return_results[$i]->modified_date,
        
        );
    }
    $results['data'] = $enhanced_view_results;
    return $this->renderJson($response, 200, $results);
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/spaces/{space_id}/documents",
   *   tags={"Collaboration Center"},
   *   summary="List space documents with a certain user",
   *   description="List space documents with a certain user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user documents<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="space_id", in="path", required=false, type="string", description="Space ID to list documents inside <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List space documents"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function listSpaceDocuments($request, $response, $args) {
    $parameters = ['pageNumber', 'numberOfData']; 
    $required_params = ['pageNumber', 'numberOfData'];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_GET) && !empty($_GET[$parameter]))
      {
        $args[$parameter] = $_GET[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'numberOfData',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    if(! isset($args['space_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "space_id"));
    }
    
    if(! is_numeric($args['space_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "space_id"));
    }
    
    //check if space exists
    $space = CollaborationCenterItem::where('ID', '=', $args['space_id'])
            ->where('is_space', '=', 1)
            ->first();
    if(!$space)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Space"));
    }
    
    $user_id = $loggedin_user->user_id;
    $collab_center = new CollaborationCenterItem();
    $offset = ($args["numberOfData"] * $args["pageNumber"] ) - $args["numberOfData"];
    $return_results = array();
    if ($collab_center->isSharedItemByUser($user_id, $args['space_id'])) {
      $return_results = $collab_center->getSpaceContentById(($args['space_id']),$offset,$args["numberOfData"]);
      $all_results = $collab_center->getSpaceContentById(($args['space_id']));
    }else
    {
      $return_results = $collab_center->getDocumentsBySpaceAndUser($user_id,$args['space_id'],$offset,$args["numberOfData"]);
      $all_results = $collab_center->getDocumentsBySpaceAndUser($user_id,$args['space_id']);
    }
    
    if(sizeof($return_results) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $results = $this->ef_load_data_counts(sizeof($all_results), -1);
    $enhanced_view_results = array();
    for($i = 0; $i < sizeof($return_results); $i++)
    {
      $return_results[$i]['added_by'] = $this->return_user_info_list($return_results[$i]['owner_id']);
       $enhanced_view_results[$i] = array(
        "added_by"=>$return_results[$i]['added_by'],
        "document_id"=> $return_results[$i]->ID,
        "space_id"=> $return_results[$i]->item_ID,
        "title"=> $return_results[$i]->title,
        "content"=> html_entity_decode($return_results[$i]->content), 
        "status" => $return_results[$i]->status,
        "is_shared_with_individual" => ($return_results[$i]->getNoOfContributers() > 0)?true:false,
        "is_shared_with_group" => ($return_results[$i]->taxPermissions()->count() + $return_results[$i]->anonPermissions()->count()) > 0?true:false,           
        "noOfContributors"=> $return_results[$i]->getNoOfContributers(),
        "created_date"=> $return_results[$i]->created_date,
        "modified_date"=> $return_results[$i]->modified_date
        );
    }
    $results['data'] = $enhanced_view_results;
    return $this->renderJson($response, 200, $results);
  }
  
  /**
   * @SWG\Post(
   *   path="/collaboration/spaces/{space_id}/documents/",
   *   tags={"Collaboration Center"},
   *   summary="Creates document in space",
   *   description="Create a new document with the passed data",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to create new document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Document Title <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/>2. Unique within same space <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="content", in="formData", required=false, type="string", description="Document Content <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="space_id", in="path", required=false, type="string", description="Space ID to store the document to (must be shared with you or created by you) <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="document added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addDocument($request, $response, $args) {
     $required_params = ['token', 'title'];
     $parameters = ['token', 'title','content'];
     
    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    if(!isset($args['space_id']) || empty($args['space_id']) || $args["space_id"] == "{space_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "space_id"));
    }
  
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    $parametersToCheck = array("title",'content');
    foreach ($parametersToCheck as $param) {
      if (!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && !empty($args[$param])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
      }
    }
    
    if (!is_numeric($args["space_id"]) && !empty($args["space_id"])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("notNumber", "space_id" ));
    }
    
    $collabCenterItem = new CollaborationCenterItem();
    
    if(!isset($args["space_id"]) || !$collabCenterItem->isMySpace($user_id, $args["space_id"]) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notAccessed", "space_id" ));  
    }
    
    if($collabCenterItem->isDocumentTitleExist($args["space_id"],$args['title']) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("exists", "Title" ));  
    }
    
    
    $item_data = array("title"=>$args["title"],
                "content"=>$args['content'],
                "owner_id"=>$user_id,
                "is_space"=>false,
                "status"=>"draft",
                "item_ID"=>$args["space_id"]);
  
    $collabCenterItem->addItem($item_data);
    $collabCenterItem->save();
    
    $history = new CollaborationCenterItemHistory(); 
    $historyArgs = array("title"=>$args["title"],
                  "content"=>$args['content'],
                  "editor_id"=>$user_id,
                  "status"=>"draft",
                  "item_ID"=>$collabCenterItem->id,
                  "section"=> "");
    $history->addItem($historyArgs);
    $history->save();
    
    // Add group permissions
    $parentSpace = CollaborationCenterItem::Where("ID","=",$args["space_id"])->first();
    foreach($parentSpace->taxPermissions()->get() as $taxPermission)
    {
      $newtaxPermission = new CollaborationCenterTaxPermission();
      $newtaxPermission->addTaxPermission(
        array(
          "permission"=>"editor",
          "item_ID"=>$collabCenterItem->id,
          "tax_id" => $taxPermission->tax_id,
          "taxonomy" => $taxPermission->taxonomy,
          "permission_from" => "space"  
        )
      );
      $newtaxPermission->save();
    }

    foreach($parentSpace->anonPermissions()->get() as $anonPermission)
    {
      $newAnonPermission = new CollaborationCenterAnonPermission();
      $newAnonPermission->addAnonPermission(
        array(
          "permission"=>"editor",
          "item_ID"=>$collabCenterItem->id,
          "type" => $anonPermission->type,
          "name" => $anonPermission->name,
          "permission_from" => "space"  
        )
      );
      $newAnonPermission->save();
    }
              
    //Add User permissions of Space on the new document
    $space_id = $args["space_id"];
    $collabCenterUserPermission = new CollaborationCenterUserPermission();
    $permissions = $collabCenterUserPermission->permissionOnItem($space_id);
    for($i = 0; $i < sizeof($permissions); $i++)
    {
      $documentPermission = new CollaborationCenterUserPermission();
      $args = array(
          'user_id' => $permissions[$i]->user_id,
          'permission' => $permissions[$i]->permission,
          'item_ID' => $collabCenterItem->id,
          'permission_from' => 'space'
      );

      $documentPermission->addUserPermission($args);
      $documentPermission->save();
    }
    
    //provide collaboration badge
    $collaboration_badge = new Badge($user_id);
    $collaboration_badge->efb_manage_document_contributor_badge();
    
    $output =  Messages::getSuccessMessage("Success", "Document added" );
    $output['document_id'] = $collabCenterItem->id;
    return $this->renderJson($response, 200, $output);    
  }  
  
  /**
   * @SWG\Put(
   *   path="/collaboration/documents/{document_id}",
   *   tags={"Collaboration Center"},
   *   summary="Edit a document",
   *   description="Edit a document with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to edit a document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="document_id", in="path", required=false, type="string", description="Document ID returned from list of documents <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Document Title <br/><b>Validations: </b><br/> 1. Contains at least 1 character<br/>2. Unique within same space <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="content", in="formData", required=false, type="string", description="Document Content <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="status", in="formData", required=false, type="string", description="Revision Status <br/> <b>Values</b> <br/>1. draft<br/>2.reviewed<br/>3.published<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="section", in="formData", required=false, type="string", description="Section to show in the system in case document is published only. <br/> <b>Values</b> One of predefined sections in system data"),
   *   @SWG\Response(response="200", description="document edited successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editDocument($request, $response, $args) {
    $put = $request->getParsedBody();
    //check token
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "token"));
    }
    $parameters = ['title', 'document_id', 'content','status','section'];
    $required_params = [ 'title', 'document_id', 'content','status'];
    
    //ADD DOCUMENT ID TO PUT
    $put['document_id'] = $args['document_id'];
    
    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $put)) {
        $args[$parameter] = $put[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    $formats_params = [ 'title', 'content'];
    foreach ($formats_params as $field) {
      $is_numbers_only = preg_match("/^[0-9]{1,}$/", $args[$field]);
      $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $args[$field]);
      
      if (($is_numbers_only > 0 || !$contains_letters) && (isset($args[$field]) && !empty($args[$field]))) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $field));
      }
    }
    
    $params = $request->getHeaders();
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }else if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
        
    //load item by id
    $item = new CollaborationCenterItem();
    $document = $item->getDocumentByID($args['document_id']);
    if(!$document)
    {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Document/Space"));
    }
    
    //validate user
    $collabCenterItem = new CollaborationCenterItem();
    $collabCenterUserPermission = new CollaborationCenterUserPermission();
    if(!isset($args['document_id']) || !$collabCenterItem->isMyDocument($user_id, $args['document_id']))
    {
      //check permission on document or space
      if( ($collabCenterUserPermission->hasPermissionByItemID($user_id, $args['document_id']) < 1
              &&  $collabCenterUserPermission->hasPermissionByItemID($user_id, $document->item_ID) < 1) && !$collabCenterItem->isSharedItemByUser($user_id, $args['document_id']))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("notAccessed", "document"));
      }
    }    

    //validate status
    global $ef_collaboration_item_status;
    if(!array_key_exists(strtolower(trim($args['status'])), $ef_collaboration_item_status))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "status"));
    }
  
    $isShared = $document->isSharedItemByUser($user_id, $args['document_id']);
    //validate current user has access to set such status
    $validated_status = self::ef_return_status_by_permission_api($isShared,$args['document_id'], $document->item_ID,$document->owner_id, $user_id);         
    if(!array_key_exists(strtolower(trim($args['status'])), $validated_status))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notValidUserStatus"));
    }      
       
    //check that document is editable
    if($document->status != 'draft' && $document->owner_id != $user_id)
    {
      if(!array_key_exists($document->status, $validated_status))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("documentNotEditable",'document',array('range'=>$document->status)));
      }
    }
   
    //validate section if exists
    if(isset($args['section']) && $args['section'] != '')
    {
      if(trim($args['status']) != 'published')
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("notValidSection"));
      }
      
      global $ef_sections;
      if(!array_key_exists(strtolower($args['section']), $ef_sections))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "section"));
      }      
    }
    
    //save the edit of the document
    $collabCenterItemHistory = new CollaborationCenterItemHistory();
    $args_history = array("title"=> $args["title"],
                  "content"=> $args['content'],
                  "editor_id"=> $user_id,
                  "created_date"=> date('Y-m-d H:i:s'),
                  "status"=> trim($args['status']),
                  "section" => $args['section'],
                  "item_ID"=> $args['document_id']);

    $collabCenterItemHistory->addItem($args_history);
    $collabCenterItemHistory->save();    
    
    //edit main document info to the new one
    $args = array(
        "title"=> $args["title"],
        "content"=> $args['content'],
        "ID"=> $args['document_id'],
        "status"=> trim($args['status'])
    );
    $update_document = new CollaborationCenterItem();
    $update_document->updateDocument($args);

    //provide collaboration badge
    $collaboration_badge = new Badge($user_id);
    $collaboration_badge->efb_manage_document_contributor_badge();
    
    // save edited document to marmotta
    $search = new SearchController();
    $search->save_post_to_marmotta( $args['ID'], $args['title'], $args['content'], 'collaboration-center');
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Document Edited"));
  }
  
  //set array of status depends on users permissions
public function ef_return_status_by_permission_api($isShared,$document_id, $space_id, $owner_id, $current_user_id)
{
    global $ef_collaboration_item_status;
    //user id
    $user_id = $current_user_id;

    if($user_id == $owner_id)
    {
      $user = new User();
      $userMeta = new Usermeta();
      if(!$userMeta->isUserHasRole($user_id, array("administrator","author")))
      {
        unset($ef_collaboration_item_status['published']);
      }
      return $ef_collaboration_item_status;
    }
    $result = $ef_collaboration_item_status;
    //get user permission from docuemnt id
    $permission = new CollaborationCenterUserPermission();
    $user_permission = $permission->getPermissionByItemID($user_id, $document_id);
    if($user_permission != NULL)
    {         
      $result = $ef_collaboration_item_status;
      if($user_permission->permission == 'reviewer')
      {
        unset($result['published']);
      }else if($user_permission->permission == 'editor')
      {
        unset($result['published']);
        unset($result['reviewed']);
      }
    }else
    {
      //check space condition
      $user_permission = $permission->getPermissionByItemID($user_id, $space_id);
      if($user_permission != NULL)
      {  
        if($user_permission->permission == 'reviewer')
        {
          unset($result['published']);
        }else if($user_permission->permission == 'editor')
        {
          unset($result['published']);
          unset($result['reviewed']);
        }
      }else {
        if($isShared){
          unset($result['published']);
          unset($result['reviewed']);
        }else {
          return [];
      }      
    }
    }
    $user = new User();
    $userMeta = new Usermeta();
    if(!$userMeta->isUserHasRole($user_id, array("administrator","author")))
    {      
      unset($result['published']);
    }
    return $result;
  }
  
  /**
   * @SWG\Post(
   *   path="/collaboration/spaces/",
   *   tags={"Collaboration Center"},
   *   summary="Create space",
   *   description="Create a new space with the passed data",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to create new space<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Space Title <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="space added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function createSpace($request, $response, $args) {
     $required_params = ['token', 'title'];
     $parameters = ['token', 'title'];
     
    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
  
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    $parametersToCheck = array("title");
    foreach ($parametersToCheck as $param) {
      if (!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && !empty($args[$param])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
      }
    }
    
    $collabCenterItem = new CollaborationCenterItem();
    
    if($collabCenterItem->isSpaceTitleExist($args["title"],$user_id) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("exists", "Title" ));  
    }
    
    
    $item_data = array("title"=>$args["title"],
                "content"=>"",
                "owner_id"=>$user_id,
                "is_space"=>true,
                "status"=>"");
  
    $collabCenterItem->addItem($item_data);
    $collabCenterItem->save();
    $output =  Messages::getSuccessMessage("Success", "Space added" );
    $output['space_id'] = $collabCenterItem->id;
    return $this->renderJson($response, 200, $output);
  }  
  
    /**
   * @SWG\Get(
   *   path="/collaboration/share-settings/users/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="List invited users with certain roles on specific item (space or document) by ID",
   *   description="List invited users with certain roles on specific item (space or document) by ID",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list invited users on space/document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Invited Users"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function listInvitedUser($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;

    $parameters = ['pageNumber', 'numberOfData']; 
    $required_params = [];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_GET) && !empty($_GET[$parameter]))
      {
        $args[$parameter] = $_GET[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of technologies',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    
    if(!isset($args['item_id']) || empty($args['item_id']) || $args['item_id']== "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","item_id"));
    }
    
    //load item by id
    $item = new CollaborationCenterItem();
    $document = $item->getDocumentByID($args['item_id']);
    if(!$document)
    {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Document/Space"));
    }
    
    //check that owner to list all invited users
    if($document->owner_id != $user_id)
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
    if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
    {
      $offset = -1;
    }
    if(!array_key_exists('numberOfData', $args))
    {
      $args['numberOfData'] = -1;
    }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
    {
      $args['numberOfData'] = -1;
    }        
    
    $permission = new CollaborationCenterUserPermission();
    $results = $permission->listInvitedUserByItem($args['item_id'],  $args['numberOfData'], $offset);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $total_count = count($permission->listInvitedUserByItem($args['item_id'], -1, -1));
    if(!isset($args['numberOfData']) || $args['numberOfData'] == -1)
    {
      $total_pages = 1;
    }else
    {
      $total_pages = (ceil($total_count/$args['numberOfData']));
    }
    $List = ['total_count'=> $total_count,"total_pages" => $total_pages];
    
    for($i = 0; $i < sizeof($results); $i++)
    {
      // --- Get profile image --- //
      $option = new Option();
      $host = $option->getOptionValueByKey('siteurl');
      $user_id = $results[$i]->ID ;
      $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
      $image_location = glob($directory . "*bpfull*");            
      foreach(glob($directory . "*bpfull*") as $image_name){
        $image_name = end(explode("/", $image_name));
        $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
      }
      //var_dump($image_location);exit;
      // if image is not from buddypress and from social media //
      if (empty($image_location)){
        $meta_key = "wsl_current_user_image";
        $user_meta = new Usermeta();
        $meta = $user_meta->getUserMeta($user_id, $meta_key);
        $image = $meta;
        if (empty($meta)){ // -- default gravatar image -- //
          $email = $results[$i]->user_email;
          $size = '150'; //The image size
          $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
        }
      }
      
      $List['data'][$i] = array(
          'user_id' => $results[$i]->ID,
          'display_name' => ($results[$i]->display_name == '')?$results[$i]->user_nicename:$results[$i]->display_name,
          'username' => $results[$i]->user_nicename ,
          'permission' => $results[$i]->permission,
          'profile_picture' => $image
      );
    }
    
    return $this->renderJson($response, 200, $List);
  }
  
   /**
   * @SWG\Post(
   *   path="/collaboration/share-settings/users/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="Invite User to contribute on an space or document",
   *   description="Invite User to contribute on an space or document <br/> List of error codes: <br/> 1001 -> username key not passed <br/> 1002 -> permission key not passed <br/> 1003 -> permissin not valid<br/> 1004 -> user not found<br/> 1005 -> space/document owner can't be invited<br/> 1006 -> user already invited to this space/document<br/> 1007 -> user of role subscriber can't be invited to a space/document<br/> 1008 -> user of role contributor or subscriber can't be invited to a space/document as publisher",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to invite users on space/document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="invitees", in="formData", required=false, type="string", description="json array of 2 keys (username and permission), and permission should be one of (editor,reviewer,publisher) <br/> <b>Values: </b> [{""username"":""EgyptFOSS"",""permission"":""editor""},{""username"":""EgyptFOSS-1"",""permission"":""publisher""}]<br/>"),
   *   @SWG\Response(response="200", description="document added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addUserToItem($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    //set user id of loggedIn User
    $user_id = $loggedin_user->user_id;

    //check item_id passed
    if(!isset($args['item_id']) || empty($args['item_id']) || $args['item_id']== "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","item_id"));
    }
    
    //load item by id
    $item = new CollaborationCenterItem();
    $document = $item->getDocumentByID($args['item_id']);
    if(!$document)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Document/Space"));
    }
    
    //check that owner to list all invited users
    if($document->owner_id != $user_id)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    $parameters = ['invitees']; 
    $required_params = ['invitees'];
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
    
    //Declare permissions
    $permissions = array('editor','reviewer','publisher');
    
    //Load array of users
    $invalidUsers = array();
    $users = json_decode(html_entity_decode($args['invitees']), TRUE);
    if(json_last_error() !== JSON_ERROR_NONE)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "invitees"));
    }
    
    for($i = 0; $i < sizeof($users); $i++)
    {
      $isValid = true;

      //check both username and permission passed
      if (!isset ($users[$i]['username']))
      {
        $invalidUsers["EMPTY"] = array(
            "username" => "EMPTY",
            "permission" => 'EMPTY',
            "error" => Messages::getErrorMessage("share_user_not_found_username", "username")
        );       
        $isValid = false;
      }      
      else if(!isset($users[$i]['permission']))
      {
        $invalidUsers[$users[$i]['username']] = array(
            "username" => $users[$i]['username'],
            "permission" => '',
            "error" => Messages::getErrorMessage("share_user_not_found_permission", "permission")
        );
        $isValid = false;
      } 
      
      //validate valid permission
      if($isValid)
      {
        if(!in_array($users[$i]['permission'], $permissions))
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_not_valid_permission", "permission")
          );          
          $isValid = false;
        }
      }

      //retrieve user from email
      if($isValid)
      {
        $added_user = User::where('user_nicename', '=', $users[$i]['username'])->first();
        if(!$added_user)
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_not_found_user", "username")
          );  

          $isValid = false;
        }
      }

      //validate user is not the owner of the item    
      if($isValid)
      {
        if($document->owner_id == $added_user->ID)
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_user_is_owner", "username")
          );  
          $isValid = false;
        }
      }

      //validate user not already added before 
      if($isValid)
      {
        $existingUser = CollaborationCenterUserPermission::where('user_id','=',$added_user->ID)
                  ->where('item_ID','=',$args['item_id'])->first();
        if($existingUser)
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_user_added", "username")
          );  
          $isValid = false;
        }
      }

      //subscriber user
      if($isValid)
      {      
        if( !$this->user_can($added_user->ID, 'add_new_ef_posts') ) 
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_user_is_subscriber", "username")
          ); 
          $isValid = false;
          
        }
      }

      //check if role is publisher to add it to users with author previlages
      if($isValid)
      {
        if( !$this->user_can($added_user->ID, 'perform_direct_ef_actions')   &&
                $users[$i]['permission'] == 'publisher')
        {
          $invalidUsers[$users[$i]['username']] = array(
              "username" => $users[$i]['username'],
              "permission" => $users[$i]['permission'],
              "error" => Messages::getErrorMessage("share_user_user_is_not_authorized", "username")
          ); 
          $isValid = false;
        }
      }

      //save data permission
      if($isValid)
      {
        $permission = new CollaborationCenterUserPermission();
        $metaPermission = array(
            'permission' => $users[$i]['permission'],
            'item_ID' => $args['item_id'],
            'user_id' => $added_user->ID,
            'permission_from' => ($document->is_space == 1)?"space":"document"
        );
        $permission->addUserPermission($metaPermission);
        $permission->save();

        //check if is a space
        if($document->is_space == 1)
        {
          $item = new CollaborationCenterItem();
          $document_ids = $item->getItemIDsByParentID($document->ID);
          $doc_ids_array = array();
          for($z = 0; $z < sizeof($document_ids); $z++)
          {
            array_push($doc_ids_array, $document_ids[$z]->ID);
          }
          for($z = 0; $z < sizeof($doc_ids_array); $z++)
          {
            //check permission not added before re-add it
            $existingPermission = new CollaborationCenterUserPermission();
            $exists = $existingPermission->hasPermissionByItemID($added_user->ID, $doc_ids_array[$z]);
            if($exists == 0)
            {
              $add_document_permission = new CollaborationCenterUserPermission();
              $metaPermission = array(
                  'permission' => $users[$i]['permission'],
                  'item_ID' => $doc_ids_array[$z],
                  'user_id' => $added_user->ID,
                  'permission_from' => 'space'
              );

              $add_document_permission->addUserPermission($metaPermission);
              $add_document_permission->save();
            }
          }
        }

        $user_owner = User::where('ID','=', $document->owner_id)->first();

        //send email
        global $ef_collaboration_item_roles;
        
        $permission_input = $users[ $i ][ 'permission' ];

        $option = new Option();
        $site_url = $option->getOptionValueByKey('siteurl');
        $file = \Usermeta::getUserMeta($added_user->ID,"prefered_language");
        $title = sprintf(__("%s invited you to collaborate on a %s","egyptfoss",$file),$user_owner->display_name,($document->is_space == 1?  strtolower(__('Space',"egyptfoss",$file)):  strtolower(__('Document',"egyptfoss",$file))));

        $msg = sprintf(__("Hi, %s","egyptfoss",$file),  $added_user->display_name)."<br/><br/>";
        $msg .= sprintf(__('%s invited you to collaborate on "%s" as %s',"egyptfoss",$file),$user_owner->display_name
                ,($document->is_space == 1?
                "<a href=\"$site_url/$file/collaboration-center/shared/spaces/$document->ID/\">".$document->title."</a>":
                "<a href=\"$site_url/$file/collaboration-center/spaces/$document->item_ID/document/$document->ID/edit/\">".$document->title."</a>"), ($file == "ar" && $ef_collaboration_item_roles[ $permission_input ] == "Publisher")? (substr(__($ef_collaboration_item_roles[ $permission_input ],"egyptfoss",$file), 4)):(__($ef_collaboration_item_roles[ $permission_input ],"egyptfoss",$file)))."<br/><br/>";
        $msg .= __('We are looking forward more contribution from your side to enrich EgyptFOSS.',"egyptfoss",$file)."<br/><br/>";
        $msg .= __('Thank you again!',"egyptfoss",$file);
        $template_inputs = array(
            "title" => $title,
            "message" => $msg,
            "to"=> array(
                'email' => $added_user->user_email,
                'name' => $added_user->display_name
            ),
            "lang" => $file
        );

        $mailer = new CollaborationMailer();
        $mailer->sendCollaborationEmail($template_inputs, null);

        //return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","User Permission added successfully"));   
      }
    }
    
    // return data
    $output =  Messages::getSuccessMessage("Success","User Permission added");
    $output['invalid_users'] = $invalidUsers;
    return $this->renderJson($response, 200, $output);
  }
  
   /**
   * @SWG\Delete(
   *   path="/collaboration/share-settings/users/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="Deletes User Permission on specified item (space or document)",
   *   description="Deletes User Permission on specified item (space or document)",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to delete user permission on space/document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="username", in="formData", required=false, type="string", description="username of invited user in the system <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description=""),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function deleteUserPermissionToItem($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;

    $parameters = ['username', 'permission']; 
    $required_params = [];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter]))
      {
        $args[$parameter] = $_POST[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    $params = $request->getParsedBody();
    if(!isset($args['item_id']) || empty($args['item_id']) || $args['item_id']== "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","item_id"));
    }
    
    if(empty($params['username']) || !isset($params['username'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue",'username'));
    }
  
    //load item by id
    $item = new CollaborationCenterItem();
    $document = $item->getDocumentByID($args['item_id']);
    if(!$document)
    {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Document/Space"));
    }
    
    //check that owner to list all invited users
    if($document->owner_id != $user_id)
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //retrieve user from email
    $added_user = User::where('user_nicename', '=', $params['username'])->first();
    if(!$added_user)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "username"));
    }
    
    //validate user not already added before 
    $existingUser = CollaborationCenterUserPermission::where('user_id','=',$added_user->ID)
              ->where('item_ID','=',$args['item_id'])->first();
    if(!$existingUser)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notValidUserPermission"));
    }else
    {
      //remove condition
      $item_remove = new CollaborationCenterUserPermission();
      $item_remove->removeUserPermissionByUserAndItemId($added_user->ID, $args['item_id']);
      
      // remove space permission per user if total count of document not equal to total count of permission of current 
      // user on space
      if($document->is_space != 1)
      {
        //load total document in space
        $total_document_per_space = CollaborationCenterItem::where('item_ID','=', $document->item_ID)->count();

        //load users by document invitees
        $item = new CollaborationCenterItem();
        $document_ids = $item->getItemIDsByParentID($document->item_ID);
        $doc_ids_array = array();
        for($y = 0; $y < sizeof($document_ids); $y++)
        {
          array_push($doc_ids_array, $document_ids[$y]->ID);
        }        
        $item_permission = new CollaborationCenterUserPermission();
        $total_invitees = $item_permission->hasPermissionOnAllSpace($added_user->ID,$doc_ids_array);

        if($total_invitees != $total_document_per_space)
        {
          //remove user on space
          $permission_remove = new CollaborationCenterUserPermission();
          $permission_remove->removeUserPermissionByItemIDs(array($document->item_ID), $added_user->ID, 'space');
        }
      }else
      {
        $item = new CollaborationCenterItem();
        $document_ids = $item->getItemIDsByParentID($document->ID);
        $doc_ids_array = array();
        for($i = 0; $i < sizeof($document_ids); $i++)
        {
          array_push($doc_ids_array, $document_ids[$i]->ID);
        }
        $permission_remove = new CollaborationCenterUserPermission();
        $permission_remove->removeUserPermissionByItemIDs($doc_ids_array, $added_user->ID, 'space');
      }
        
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","User Permission removed successfully"));   
    }
    
  }
  
    
  /**
   * @SWG\POST(
   *   path="/collaboration/share-settings/groups/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="share item(space or document) based on group like user subtype, technologies, theme, interests",
   *   description="share item(space or document) based on group like user subtype, technologies, theme, interests",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to invite users by group on space/document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="formData", required=false, type="string", description="Specify Type <br/><b>Values:</b>Individual or Entity"),
   *   @SWG\Parameter(name="subtype", in="formData", required=false, type="string", description="Specify Sub-type <br/><b>Values:</b>One of predefined sub-types in system data"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Theme <br/><b>Values: </b> term_id"),
   *   @SWG\Response(response="200", description="share setting saved"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function shareItemByGroup($request, $response, $args) {
    $parameters = ['token', 'type', 'subtype', 'technology', 'interest', 'theme'];
   // $at_least_parameters = ['type', 'subtype', 'technology', 'interest', 'theme'];
    $anonPermissionArr = array();
    $taxPermissionArr = array();
    $taxPermissionIds = array();
    $anonPermissionNames = array();
    
    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        $args = array_merge($args, array($parameter => ''));
      }
    }

   /* $allEmpty = true;
    foreach ($at_least_parameters as $param) {
      if ($args[$param] != "") {
        $allEmpty = false;
      }
    }
    if ($allEmpty) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }*/
    if (!isset($args['item_id']) || empty($args['item_id']) || $args["item_id"] == "{item_id}") {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "item_id"));
    }
    
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    if (!$this->user_can($user_id, 'add_new_ef_posts')) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }

    $collabCenter = new CollaborationCenterItem();
    if (!$collabCenter->isMyItem($user_id, (int) $args['item_id'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("invalidItemPermission"));
    }
    
    global $account_types;
    if (!empty($args['type'])) {
      if (!in_array($args['type'], $account_types)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Type"));
      }
    }
    
    if (!empty($args['type'])) {
    array_push($anonPermissionArr, array("permission" => "editor",
      "item_ID" => $args["item_id"],
      "name" => $args['type'],
      "type" => "type"));
     array_push($anonPermissionNames, $args['type']);
    }
    
    global $account_sub_types;
    if (!empty($args['subtype']) && !empty($args['type'])) {
      if (array_key_exists($args['subtype'], $account_sub_types) && $account_sub_types[$args['subtype']] == $args['type']) {
        array_push($anonPermissionArr, array("permission" => "editor",
          "item_ID" => $args["item_id"],
          "name" => $args['subtype'],
          "type" => "sub_type",
          "permission_from" => "document"));
         array_push($anonPermissionNames, $args['subtype']);
      } else {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Sub Type"));
      }
    }

    foreach (array("theme", "type", "subtype") as $oneValue) {
      $value = split(",", $args[$oneValue]);
      if (count($value) > 1) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $oneValue));
      }
    }

    $multi_taxs = array("interest", "technology");
    foreach ($multi_taxs as $multi_tax){
      $multiTaxValues = explode(',', $args[$multi_tax]);
    foreach ($multiTaxValues as $term_id) {
      if (!empty($term_id)) {
        $termTax = new TermTaxonomy();
        $isTermExists = $this->check_term_exists($term_id, $multi_tax);
        if (!$isTermExists) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $multi_tax));
        }
        array_push($taxPermissionArr, array("permission" => "editor",
          "item_ID" => $args["item_id"],
          "tax_id" => $isTermExists->term_taxonomy_id,
          "taxonomy" => $isTermExists->taxonomy,
          "permission_from" => "document"));
        array_push($taxPermissionIds, (string)$isTermExists->term_taxonomy_id);
      }
    }
    }

    if (!empty($args['theme'])) {

      $termTax = new TermTaxonomy();
      $isTermExists = $termTax->getTermTaxonomy($args['theme'], 'theme');
      if (!$isTermExists) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", 'theme'));
      }
      array_push($taxPermissionArr, array("permission" => "editor",
        "item_ID" => $args["item_id"],
        "tax_id" => $isTermExists->term_taxonomy_id,
        "taxonomy" => $isTermExists->taxonomy,
        "permission_from" => "document"));
      array_push($taxPermissionIds, (string)$isTermExists->term_taxonomy_id);
        
    }

    //saving data
    $item = CollaborationCenterItem::where("ID","=",$args['item_id'])->first();
    foreach($item->documents()->get() as $document)
    {
      if($document->isSharedBySpace())
      {
        $document->taxPermissions()->delete();
        $document->anonPermissions()->delete();
        foreach($anonPermissionArr as $arg)
        {
          $arg["item_ID"] = $document->ID;
          $arg["permission_from"] = "space";
          $anonPermission = new CollaborationCenterAnonPermission();
          $anonPermission->addAnonPermission($arg);
          $anonPermission->save();
        }
        foreach($taxPermissionArr as $arg)
        {
          $arg["item_ID"] = $document->ID;
          $arg["permission_from"] = "space";
          $taxPermission = new CollaborationCenterTaxPermission();
          $taxPermission->addTaxPermission($arg);
          $taxPermission->save();
        }
      }
    }
    if(!$item->is_space)
    {
      $currentTaxs = $item->taxPermissions()->get()->pluck("tax_id")->toArray();
      $currentAnon = $item->anonPermissions()->get()->pluck("name")->toArray();
      if($currentAnon != $anonPermissionNames || $currentTaxs != $taxPermissionIds)
      {
        $parentSpace = CollaborationCenterItem::where("ID","=",$item->item_ID)->first();
        $parentSpace->taxPermissions()->delete();
        $parentSpace->anonPermissions()->delete();
      }
    }
    $item->taxPermissions()->delete();
    $item->anonPermissions()->delete();
    
    foreach ($anonPermissionArr as $arg) {
      $anonPermission = new CollaborationCenterAnonPermission();
      $anonPermission->addAnonPermission($arg);
      $anonPermission->save();
    }

    foreach ($taxPermissionArr as $arg) {
      $taxPermission = new CollaborationCenterTaxPermission();
      $taxPermission->addTaxPermission($arg);
      $taxPermission->save();
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Sharing Settings saved "));
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/share-settings/groups/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="list shared group like user subtype, technologies, theme, interests by item",
   *   description="list shared group like user subtype, technologies, theme, interests by item",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list shared group conditions on space/document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List shared groups"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function listSharedGroups($request, $response, $args) {
    
    if(isset($_GET['lang']))
    {
      $args["lang"] = $_GET['lang'];
    }else {
      $args["lang"] = '';
    }
    
    if(isset($args["lang"]) && !empty($args["lang"]) && ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    if (!isset($args['item_id']) || empty($args['item_id']) || $args["item_id"] == "{item_id}") {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "item_id"));
    }
    
    $params = $request->getHeaders();
    if (!isset($params['HTTP_TOKEN']) || empty($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "token"));
    }
   
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    if (!$this->user_can($user_id, 'add_new_ef_posts')) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }

    $collabCenter = new CollaborationCenterItem();
    if (!$collabCenter->isMyItem($user_id, (int) $args['item_id'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("invalidItemPermission"));
    }
    
    $item = CollaborationCenterItem::where("ID","=",$args['item_id'])->first();
    $taxPermissions = $item->taxPermissions()->get();
    $taxArr = array("technology"=>array(),"interest"=>array(),"theme"=>"");
    $List = array("data"=> $taxArr);
    foreach($taxPermissions as $permission)
    {
      if($permission->taxonomy == "theme")
      {
        $List['data'][$permission->taxonomy] = $permission->getTaxonomyAndTerm()->term_id;
      }else 
      {
        array_push($List["data"][$permission->taxonomy], 
              self::ef_return_tax_by_lang($args['lang'], $permission->getTaxonomyAndTerm()));
      }
    }    
    $anonArray= array("type", "sub_type");
    foreach($item->anonPermissions()->get() as $permission)
    {
      $List['data'][$permission->type] = $permission->name;
    }
    
    foreach($anonArray as $anon)
    {
      if(!isset($List['data'][$anon]))
      {
        $List['data'][$anon] = "";
      } 
    }
   
    return $this->renderJson($response, 200, $List);
  }
  
  function ef_return_tax_by_lang($lang, $tax)
  {
    if($lang == "ar")
    {
      $tax_name = $tax->name_ar;
      if($tax_name == null)
      {
        $tax_name = $tax->name;
      }
    }else if($lang == "en")
    {
      $tax_name = $tax->name;
    }else {
      return array(
        "name" => $tax->name,
        "name_ar" => $tax->name_ar
      );
    }
    
    return $tax_name;
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/published/",
   *   tags={"Collaboration Center"},
   *   summary="List published documents with a certain user",
   *   description="List published documents with a certain user",
   *   @SWG\Parameter(name="section", in="query", required=false, type="string", description="you can leave it blank to retrieve all published documents"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List published documents"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function listPublishedDocuments($request, $response, $args) {
    $parameters = ['section', 'pageNumber', 'numberOfData']; 
    $required_params = ['pageNumber', 'numberOfData'];
    foreach ($parameters as $parameter) 
    {
      if(array_key_exists($parameter, $_GET) && !empty($_GET[$parameter]))
      {
        $args[$parameter] = $_GET[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'numberOfData',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    global $ef_sections;
    if(!empty($args['section']) && !array_key_exists($args['section'], $ef_sections))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "section"));
    }
    
    /*$params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
 
    $user_id = $loggedin_user->user_id;*/
    $collab_center = new CollaborationCenterItem();
    $offset = ($args["numberOfData"] * $args["pageNumber"] ) - $args["numberOfData"];
    $return_results = $collab_center->getPublishedDocuments($args["section"],"","", $args["numberOfData"],$offset);
    if(sizeof($return_results) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $total_count = $collab_center->getPublishedDocuments($args["section"],"","", "","");
    $results = $this->ef_load_data_counts(sizeof($total_count), $args["numberOfData"]);
    $enhanced_view_results = array();
    for($i = 0; $i < sizeof($return_results); $i++)
    {
      $return_results[$i]['added_by'] = $this->return_user_info_list($return_results[$i]['owner']);
       $enhanced_view_results[$i] = array(
        "added_by"=>$return_results[$i]['added_by'],
        "document_id"=> $return_results[$i]->document_id,
        "title"=> $return_results[$i]->title,
        "content"=> $return_results[$i]->content, 
        "created_date"=> $return_results[$i]->created_date,
        //"modified_date"=> $return_results[$i]->created_date, 
        );
    }
    $results['data'] = $enhanced_view_results;
    return $this->renderJson($response, 200, $results);
  }
    /**
   * @SWG\Get(
   *   path="/collaboration/valid-users/{item_id}/",
   *   tags={"Collaboration Center"},
   *   summary="List available users to invite on a space or document",
   *   description="List available users with their username and profile picture",
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents (space or document) ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="display_name", in="query", required=false, type="string", description="pass display name as filter<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List avaolable users"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function listValidUsersPerItem($request, $response, $args) {
    $parameters = ['display_name'];
    $requiredParams = ['display_name'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!isset($args['display_name']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", 'display_name'));
    }
    
    //validate item_id
    if (!isset($args['item_id']) || empty($args['item_id']) || $args["item_id"] == "{item_id}") {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "item_id"));
    }
    
    //load item by id
    $item = new CollaborationCenterItem();
    $document = $item->getDocumentByID($args['item_id']);
    if(!$document)
    {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Document/Space"));
    }
    
    //Load users array that already have permissions
    $userWithPermission = new CollaborationCenterUserPermission();
    $users = $userWithPermission->listInvitedUserByItem($document->ID, -1,-1);
    
    $usersExistings = array();
    for($i = 0; $i < sizeof($users); $i++)
    {
      array_push($usersExistings,$users[$i]->ID);
    }
    
    //Add owner of document to list of users 
    array_push($usersExistings,$document->owner_id);
    
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');;
    
    $validUsers = new User();
    $users = $validUsers->retrieveValidUsers($args['display_name'], $usersExistings);
    $result = array();
    for($i =0; $i < sizeof($users); $i++)
    {
      $user_id = $users[$i]->ID;
      // --- Get profile image --- //
      $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
      $image_location = glob($directory . "*bpfull*");            
      foreach(glob($directory . "*bpfull*") as $image_name){
        $image_name = end(explode("/", $image_name));
        $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
      }

      if (empty($image_location)){
        $meta_key = "wsl_current_user_image";
        $user_meta = new Usermeta();
        $meta = $user_meta->getUserMeta($user_id, $meta_key);
        $image = $meta;
        if (empty($meta))
        {
          $image = $host.'/wp-content/themes/egyptfoss/img/default_avatar.png';
        }
      }
      
      $result[] = array(
          'username' => $users[$i]->user_nicename,
          'display_name' => $users[$i]->display_name,
          'profile_picture' =>$image
      );      
    }
    
    return $this->renderJson($response, 200, $result);
  } 
  
   /**
   * @SWG\Delete(
   *   path="/collaboration/spaces/delete/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="Deletes a space and its documents",
   *   description="Deletes a space and its documents  by passing the owner of the space and id of space",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to delete a space and documents inside<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents space ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description=""),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function deleteSpace($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    if(!isset($args['item_id']) || $args['item_id'] == "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","item_id"));
    }else if(!is_numeric($args['item_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect","item_id"));
    }
    
    //check if item exists
    $item_id = $args['item_id'];
    $collabItem = CollaborationCenterItem::where('ID','=', $item_id)
            ->where('is_space','=',1)->first();
    if(!$collabItem)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "item_id"));
    }
    
    //check if valid collaboration and the token is of the owner
    $newCollaborationCenter = new CollaborationCenterItem();  
    if(!$newCollaborationCenter->isMySpace($user_id, $item_id))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //Delete Space or document
    $newCollaborationCenter->removeSpaceorDocument($collabItem);
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Space deleted"));
  }
  
 /**
   * @SWG\Delete(
   *   path="/collaboration/documents/delete/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="Deletes document",
   *   description="Deletes a document by passing the owner of the document and id of document",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to delete a document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="item_id represents document ID<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description=""),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function deleteDocument($request, $response, $args) {
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }

    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    if(!isset($args['item_id']) || $args['item_id'] == "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","item_id"));
    }else if(!is_numeric($args['item_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect","item_id"));
    }
    
    //check if item exists
    $item_id = $args['item_id'];
    $collabItem = CollaborationCenterItem::where('ID','=', $item_id)
            ->where('is_space','=',0)->first();
    if(!$collabItem)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "item_id"));
    }
    
    //check if valid collaboration and the token is of the owner
    $newCollaborationCenter = new CollaborationCenterItem();  
    if(!$newCollaborationCenter->isMyDocument($user_id, $item_id))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //Delete Space or document
    $newCollaborationCenter->removeSpaceorDocument($collabItem);
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "document deleted"));
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/documents/{document_id}",
   *   tags={"Collaboration Center"},
   *   summary="get document with a certain user",
   *   description="get document with a certain user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to get a document<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="document_id", in="path", required=false, type="string", description="document_id <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="get document"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function getDocument($request, $response, $args) {
    
    $params = $request->getHeaders();
    if(empty($params['HTTP_TOKEN']))
    {
       return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    if(! isset($args['document_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "document_id"));
    }
    
    if(! is_numeric($args['document_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "document_id"));
    }
    
    
    $user_id = $loggedin_user->user_id;
    $collab_center = new CollaborationCenterItem();
    $return_result = $collab_center->getDocumentByID($args['document_id'],false);
    if(sizeof($return_result) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $isShared = $collab_center->isSharedItemByUser($user_id, $args['document_id']);
    $isOwner = ($return_result->owner_id == $user_id)?true:false;
    $userPermission = $return_result->getUserPermissionOnItem($user_id);
    $itemStatus = $return_result->status;
    if(!$isShared &&!$isOwner){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $permission = "editor";
    if($userPermission){
      $permission = $userPermission;
    } else if ($isOwner){
      $permission = "owner";
    }
    
    $canEdit = true;
    switch ($itemStatus) {
      case "published" :
        if (!in_array($permission, array("publisher","owner"))) {
          $canEdit = false;
        }
      break;
      case "reviewed" :
        if (!in_array($permission, array("publisher","reviewer","owner"))) {
          $canEdit = false;
        }
      break;
    }
    
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        $canEdit = false;
    }
        
    $last_published = $return_result->documentHistory()->where("status","=","published")->orderBy("ID","desc")->first();
    $last_version_obj = $return_result->documentHistory()->orderBy("ID","desc")->first();
    $current_version = array(
        "title"=>$return_result->title,
        "content"=>$return_result->content,
        "section"=>$last_version_obj->section,
        "status"=>$return_result->status,
        "created_date"=>$return_result->created_date,
        "published_date"=>null,
        "modified_date"=>$return_result->modified_date);
    $return_result['added_by'] = $this->return_user_info_list($return_result->owner_id);
     $enhanced_view_result = array(
      "added_by"=>$return_result['added_by'], 
      "document_id"=> $return_result->ID,
      "space_id"=> $return_result->item_ID, 
      "last_published" =>  ($last_published)?array(
        "title"=>$last_published->title,
        "content"=>$last_published->content,
        "section"=>$last_published->section,
        "status"=>$last_published->status,
        "created_date"=>$last_published->created_date,
        "published_date"=>$last_published->created_date,
        "modified_date"=>$last_published->created_date,):null,
      "current_version" => $current_version,
      "is_shared_with_individual" => ($return_result->getNoOfContributers() > 0)?true:false,
      "is_shared_with_group" => ($return_result->taxPermissions()->count() + $return_result->anonPermissions()->count()) > 0?true:false,           
      "noOfContributors"=> $return_result->getNoOfContributers(),
      "token_permission" => $permission,
      "can_edit_current_version" => $canEdit,
    );
    
    $result['data'] = $enhanced_view_result;
    return $this->renderJson($response, 200, $result);
  }
  
  /**
   * @SWG\Get(
   *   path="/collaboration/published/documents/{document_id}",
   *   tags={"Collaboration Center"},
   *   summary="get published document with a certain user",
   *   description="get published document with a certain user",
   *   @SWG\Parameter(name="document_id", in="path", required=false, type="string", description="document_id <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="get document"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  
  public function getPublishedDocument($request, $response, $args) {
    /*$user_id = null;
    $params = $request->getHeaders();
    if(!empty($params['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user == null) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      $user_id = $loggedin_user->user_id;
    }*/
    
    if(! isset($args['document_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "document_id"));
    }
    
    if(! is_numeric($args['document_id']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "document_id"));
    }
    
    $collab_center = new CollaborationCenterItem();
    $return_result = $collab_center->getPublishedDocumentByID($args['document_id']);
    if(sizeof($return_result) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $item = CollaborationCenterItem::where("ID","=",$return_result->item_ID)->first();
    /*$isOwner = ($item->owner_id == $user_id)?true:false;
    $isSharedByUser = $item->getUserPermissionOnItem($user_id);
    
    $permission = "editor";
    if($isSharedByUser){
      $permission = $isSharedByUser;
    } else if ($isOwner){
      $permission = "owner";
    }else {
      $permission = false;
    }
    */
    
    $return_result['added_by'] = $this->return_user_info_list($item->owner_id);
    $enhanced_view_result = array(
     "added_by"=>$return_result['added_by'],
     "document_id"=> $item->ID,
     //"space_id"=> $item->item_ID,
     "title"=> $return_result->title,
     "content"=> $return_result->content, 
    // "status" => $return_result->status,
     //"is_shared_with_individual" => ($item->getNoOfContributers() > 0)?true:false,
     //"is_shared_with_group" => ($item->taxPermissions()->count() + $item->anonPermissions()->count()) > 0?true:false,           
     //"noOfContributors"=> $item->getNoOfContributers(),
     "published_date"=> $return_result->created_date,
     "modified_date"=> $return_result->created_date,//$item->modified_date,
     //"token_permission" => $permission
    );
    
    $result['data'] = $enhanced_view_result;
    return $this->renderJson($response, 200, $result);
  }
  
  /**
   * @SWG\Put(
   *   path="/collaboration/spaces/{item_id}",
   *   tags={"Collaboration Center"},
   *   summary="edit space",
   *   description="rename an exist space with the passed data",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to edit a space<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Space Title <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="item_id", in="path", required=false, type="string", description="Space ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="space edited successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editSpace($request, $response, $args) {
    $required_params = ['title'];
    $parameters = ['title'];
    $put = $request->getParsedBody();
    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $put)) {
        $args[$parameter] = $put[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    if(!isset($args['item_id']) || empty($args['item_id']) || $args["item_id"] == "{item_id}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "item_id"));
    }
    
    $parametersToCheck = array("title");
    foreach ($parametersToCheck as $param) {
      if (!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && !empty($args[$param])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
      }
    }
    
    $collabCenterItem = new CollaborationCenterItem();
    $query = $collabCenterItem
      ->where("ID","=",$args["item_id"])
      ->where("is_space","=",true)
      ->where("owner_id","=",$_POST['logged_in_user_id']);
    if(!$query->first())
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "space"));
    }
  
    if($collabCenterItem->isSpaceTitleExist($args["title"],$_POST['logged_in_user_id'],$args["item_id"]) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("exists", "Title" ));  
    }

    $query->update(array("title"=>$args["title"],"modified_date"=>date('Y-m-d H:i:s')));
    $output =  Messages::getSuccessMessage("Success", "Space renamed" );
    return $this->renderJson($response, 200, $output);
  }
  
}
