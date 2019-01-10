<?php

function ef_add_collaboration_center_document() {
  $ef_collaboration_center_messages = array("errors" => array());

  $required_fields = array(
    "document_title" => "Title",
  );
  foreach ($required_fields as $field => $label) {

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }

  $contains_letter_fields = array(
    "document_title" => "Title",
    "document_content" => "Content",
  );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);

    if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field]))) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
    }
  }
  $user = get_current_user_id();
  $collabCenterItem = new CollaborationCenterItem();

  if(!isset($_POST["space_id"]) || !$collabCenterItem->isMySpace($user, $_POST["space_id"]) )
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("you are trying to cheat",'egyptfoss')));  
  }

  if($collabCenterItem->isDocumentTitleExist($_POST["space_id"],$_POST['document_title']) )
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("%s already exists",'egyptfoss'),__("Title",'egyptfoss'))));  
  }

  if ($ef_collaboration_center_messages["errors"]) {
    set_query_var("ef_collaboration_center_messages", $ef_collaboration_center_messages);
    return false;
  }

  $content = htmlspecialchars_decode( stripslashes( $_POST['document_content'] ) );
  
  // force editor links to open in a new tab  
  $doc = new DOMDocument();
  $doc->loadHTML( mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8') );
  $links = $doc->getElementsByTagName( 'a' );
  foreach ( $links as $item ) {
      if ( !$item->hasAttribute( 'target' ) ) {
          $item->setAttribute( 'target', '_blank' );  
      }
  }
  
  $_POST['document_content'] = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $doc->saveHTML()));
  
  $args = array("title"=>$_POST["document_title"],
                "content"=>$_POST['document_content'],
                "owner_id"=>$user,
                "is_space"=>false,
                "status"=>"draft",
                "item_ID"=>$_POST["space_id"]);
  $collabCenterItem->addItem($args);
  $collabCenterItem->save();
  
  $history = new CollaborationCenterItemHistory(); 
  $historyArgs = array("title"=>$_POST["document_title"],
                "content"=>$_POST['document_content'],
                "editor_id"=>$user,
                "status"=>"draft",
                "item_ID"=>$collabCenterItem->id,
                "section"=> "");
  $history->addItem($historyArgs);
  $history->save();

  // Add group permissions
  $parentSpace = CollaborationCenterItem::Where("ID","=",$_POST["space_id"])->first();
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
  $space_id = $_POST["space_id"];
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
  
  //provide collaboration contributor badge
  provideContributorBadge($user);
  
  $ef_collaboration_center_messages = array(_x("Document","definite","egyptfoss")." ".__("added successfully",'egyptfoss'));  
  setMessageBySession("ef_collaboration_center_messages","success", $ef_collaboration_center_messages);
  wp_redirect(get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php',false,"space_content",array($_POST['space_id'])));
  exit;
}

function ef_edit_collaboration_center_document($document_id) {
  global $ef_collaboration_item_status;
  $ef_collaboration_center_messages = array("errors" => array());

  if($_POST['status'] == NULL) {
    $required_fields = array(
      "document_title" => "Title",
      "document_content" => "Content"
    );

    //set status to draft
    $_POST['status'] = "draft";
  } else {
    $required_fields = array(
      "document_title" => "Title",
      "document_content" => "Content",
      "status"  => "Status"
    );
  }
  foreach ($required_fields as $field => $label) {

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }

  $contains_letter_fields = array(
    "document_title" => "Title",
    "document_content" => "Content"  
  );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);

    if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field]))) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
    }
  }

  //load space from docuemnt
  $document = new CollaborationCenterItem();
  $document = $document->getDocumentByID($document_id);
  if(!$document)
  {
    wp_redirect( home_url( '/?status=403' ) );
    exit;        
  }

  $space_id = $document->item_ID;

  $user = get_current_user_id();
  $collabCenterItem = new CollaborationCenterItem();
  $collabCenterUserPermission = new CollaborationCenterUserPermission();
  if(!isset($document_id) || !$collabCenterItem->isMyDocument($user, $document_id))
  {
    if( ($collabCenterUserPermission->hasPermissionByItemID($user, $document_id) < 1
            && $collabCenterUserPermission->hasPermissionByItemID($user, $space_id) < 1) && !$collabCenterItem->isSharedItemByUser($user, $document_id))
    {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("you are trying to cheat",'egyptfoss')));  
    }
  }

  //validate on document status existance
  if(!array_key_exists($_POST['status'], $ef_collaboration_item_status))
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("you are trying to cheat",'egyptfoss')));  
  }

  //check user has permission to set such status
  if($collabCenterItem->isSharedItemByUser($user, $document_id, true))
  {
    $list_valid_status = array('draft' => "Draft");
  }else {
    $list_valid_status = ef_return_status_by_permission($document_id, $space_id, $document->owner_id);
  }
  if(!array_key_exists($_POST['status'], $list_valid_status))
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("you are trying to cheat",'egyptfoss')));  
  }

  if ($ef_collaboration_center_messages["errors"]) {
    set_query_var("ef_collaboration_center_messages", $ef_collaboration_center_messages);
    return false;
  }

  global $ef_collaboration_item_status;
  global $ef_sections;
  $collabCenterItemHistory = new CollaborationCenterItemHistory();
  //$section = $_POST['section'];
  $section = array_search($ef_sections[$_POST['section']], $ef_sections);

  if(!$section){
    $section = '';
  }

  $content = htmlspecialchars_decode( stripslashes( $_POST['document_content'] ) );
  
  // force editor links to open in a new tab
  $doc = new DOMDocument();
  $doc->loadHTML( mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8') );
  $links = $doc->getElementsByTagName( 'a' );
  foreach ( $links as $item ) {
      if ( !$item->hasAttribute( 'target' ) ) {
          $item->setAttribute( 'target', '_blank' );  
      }
  }
    
  $_POST['document_content'] = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $doc->saveHTML()));

  $args = array("title"=> $_POST["document_title"],
                "content"=> $_POST['document_content'],
                "editor_id"=> $user,
                "created_date"=> date('Y-m-d H:i:s'),
                "status"=> array_search($ef_collaboration_item_status[$_POST['status']], $ef_collaboration_item_status),
                "section" => $section,
                "item_ID"=> $document_id);

  $collabCenterItemHistory->addItem($args);
  $collabCenterItemHistory->save();

  //edit main document info to the new one
  $args = array(
      "title"=> $_POST["document_title"],
      "content"=> $_POST['document_content'],
      "ID"=> $document_id,
      "status"=> array_search($ef_collaboration_item_status[$_POST['status']], $ef_collaboration_item_status)
  );
  $update_document = new CollaborationCenterItem();
  $update_document->updateDocument($args);

  //save last published version in marmotta
  if($args['status'] == "published")
  {
    saveDocumentContent($document_id, $_POST['document_title'], $_POST['document_content']);
  }

  //provide badge
  provideContributorBadge($user);
  
  $ef_collaboration_center_messages["success"] = array(__("Document edited successfully",'egyptfoss'));  
  set_query_var("ef_collaboration_center_messages", $ef_collaboration_center_messages);
}

function provideContributorBadge($user)
{
  $collaboration_badge = new Badge($user);
  $collaboration_badge->efb_manage_document_contributor_badge();
  
  // send emails to collaboration contributor with earned badges;
  foreach( $collaboration_badge->badges_earned as $badge ) {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
    $result = $wpdb->get_results($query, ARRAY_A);

    if( class_exists( 'EFBBadges' ) && !empty( $result ) ) {
      sendNewBadgeAchiever( $user, new EFBBadges( $result[0] ) );
    }
  }
}

function ef_add_space_by_ajax() {
  if ( ! check_ajax_referer( 'validate_space_document', 'security', false ) ) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Token","egyptfoss"))));
    die();
  }
  load_orm();
  $ef_collaboration_center_messages = array("errors" => array());
  $required_fields = array(
    "space_title" => "Title",
  );
  foreach ($required_fields as $field => $label) {

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }

  $contains_letter_fields = array(
    "space_title" => "Title", 
  );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);

    if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field]))) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
    }
  }
  
  $newCollaborationCenter = new CollaborationCenterItem();
  if($newCollaborationCenter->isSpaceTitleExist($_POST['space_title'], get_current_user_id()) )
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("%s already exists",'egyptfoss'),__("Title",'egyptfoss'))));  
  }

  if(!current_user_can('add_new_ef_posts')) {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("You don't have permission to access this page or you have signed out.",'egyptfoss')));
  }
  
  if ($ef_collaboration_center_messages["errors"]) {
    
    echo json_encode(array("status"=>"error","data"=>implode('<br/>', $ef_collaboration_center_messages["errors"])));
    die();
  }
  
  $newCollaborationCenter = new CollaborationCenterItem();
    $args = array("title"=> $_POST["space_title"],
                "content"=> '',
                "is_space"=> 1,
                "owner_id"=> get_current_user_id(),
                "status"=> '');
  $newCollaborationCenter->addItem($args);
  $newCollaborationCenter->save();
  
  $template_space = "";
  $item = $newCollaborationCenter;
  $view = "space";
  
  ob_start();
  include(locate_template('CollaborationCenter/template-parts/item.php'));
	//get_template_part('CollaborationCenter/template-parts/content','space');
  $template_space = ob_get_contents();			
  ob_end_clean();
  echo json_encode(array("status"=>"success","data"=>$template_space));
  die();
}
add_action('wp_ajax_ef_add_new_space', 'ef_add_space_by_ajax');
add_action('wp_ajax_ef_add_new_space', 'ef_add_space_by_ajax');

function ef_rename_space_by_ajax() {
  if ( ! check_ajax_referer( 'validate_space_document', 'security', false ) ) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Token","egyptfoss"))));
    die();
  }
  load_orm();
  $ef_collaboration_center_messages = array("errors" => array());
  $required_fields = array(
    "space_title" => "Title",
  );
  foreach ($required_fields as $field => $label) {

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }

  $contains_letter_fields = array(
    "space_title" => "Title", 
  );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);

    if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field]))) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
    }
  }
  
  $newCollaborationCenter = new CollaborationCenterItem();
  if($newCollaborationCenter->isSpaceTitleExist($_POST['space_title'], get_current_user_id(),$_POST["space_id"]) )
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("%s already exists",'egyptfoss'),__("Title",'egyptfoss'))));  
  }
  
  if(!$newCollaborationCenter->isMySpace(get_current_user_id(),$_POST["space_id"]) )
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("you are trying to cheat",'egyptfoss'))));  
  }

  if(!current_user_can('add_new_ef_posts')) {
      $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(__("You don't have permission to access this page or you have signed out.",'egyptfoss')));
  }
  
  if ($ef_collaboration_center_messages["errors"]) {
    
    echo json_encode(array("status"=>"error","data"=>implode('<br/>', $ef_collaboration_center_messages["errors"])));
    die();
  }
  
   $updateModel = $newCollaborationCenter->where("ID","=",$_POST["space_id"]);
   $newCollaborationCenter->updateSpaceTitle($updateModel, $_POST["space_title"]);
   //$updateModel->update(array('title'=>$_POST["space_title"]));
  
  echo json_encode(array("status"=>"success","data"=>$_POST["space_title"]));
  die();
}
add_action('wp_ajax_ef_rename_space', 'ef_rename_space_by_ajax');
add_action('wp_ajax_ef_rename_space', 'ef_rename_space_by_ajax');

//handling invite_user_byemail
function ef_load_invite_user() {
    if(isset($_POST['user_id']))
    {
      $user_id = $_POST['user_id'];
      if (is_numeric($user_id)) 
      {
        //valid user id
        global $wpdb;
        $check_sql = "SELECT * FROM $wpdb->users WHERE ID = %s";
        $checkExistingUser = $wpdb->get_results($wpdb->prepare( $check_sql, $user_id));
        if($checkExistingUser)
        {
          //validate not email of item creator
          load_orm();

          if(!is_numeric($_POST['item_id']))
          {
            echo "invalid item_id";
            die();
          }

          $item = new CollaborationCenterItem();
          $document = $item->getDocumentByID($_POST['item_id']);
          if(!$document)
          {
            echo "not found";
            die();
          }
          
          if($document->owner_id == $checkExistingUser[0]->ID)
          {
            if($document->is_space == 1)
              echo __("You can't add the creator of the space","egyptfoss");
            else
              echo __("You can't add the creator of the document","egyptfoss");
            die();
          }
                    
          set_query_var('ef_current_user_invite_id', $checkExistingUser[0]->ID);
          if(isset($_POST['role']))
          {
            set_query_var('ef_current_user_invite_role', $_POST['role']);
          }
          
          //subscriber user
          if(!user_can(get_user_by('ID', $checkExistingUser[0]->ID), 'add_new_ef_posts'))
          {
            echo __("User is not authorized to be invited","egyptfoss");
            die();
          }
          
          //check if role is publisher to add it to users with author previlages
          if(!user_can(get_user_by('ID', $checkExistingUser[0]->ID), 'perform_direct_ef_actions') &&
                  $_POST['role'] == 'publisher')
          {
            echo __("User is not authorized to be invited as a publisher","egyptfoss");
            die();
          }
          
          echo json_encode(array('id'=> $checkExistingUser[0]->ID,'data'=> get_template_part('CollaborationCenter/template-parts/content', 'user_invite')));
          die();
        }
        echo __("Please enter a valid user","egyptfoss");
        die();
      } 
      else 
      {
        echo __("Please enter a valid user","egyptfoss");
      }
    }else
    {
      echo __("Please enter a valid user","egyptfoss");
    }
    die();
}
add_action('wp_ajax_ef_load_invite_user', 'ef_load_invite_user');
add_action('wp_ajax_nopriv_ef_load_invite_user', 'ef_load_invite_user');

//save invited users on specific item
function ef_save_invited_users() {
  if ( ! check_ajax_referer( 'validate_space_document', 'security', false ) ) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Token","egyptfoss"))));
    die();
  }
  if(!current_user_can('add_new_ef_posts')) {
    echo json_encode(array("status"=>"error", "data" => __("You don't have permission to access this page or you have signed out.",'egyptfoss')));
    die();
  }
    if(isset($_POST['item_id']))
    {
      //valid item
      load_orm();
      
      if(!is_numeric($_POST['item_id']))
      {
        echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Item","egyptfoss"))));
        die();
      }
      
      $item = new CollaborationCenterItem();
      $document = $item->getDocumentByID($_POST['item_id']);
      if(!$document)
      {
        echo json_encode(array("status"=>"error", "data"=> __("Not Found","egyptfoss")));
        die();
      }
      $owner_id = $document->owner_id;
      
      //save for specific users
      if(isset($_POST['users_ids_roles']))
      {
        global $ef_collaboration_item_roles;
        $users_roles = $_POST['users_ids_roles'];
        
        //retrieve all exisitng data first
        $existing_permissions = new CollaborationCenterUserPermission();
        $lists = $existing_permissions->listInvitedUserByItem($document->ID,-1,-1);
        
        //remove all permissions by item id
        $permission_remove = new CollaborationCenterUserPermission();
        $permission_remove->removeUserPermissionByItemID($document->ID);
                
        //remove all permissions on documents inside space
        if($document->is_space == 1)
        {
          //load all documents under a space
          $document_ids = $item->getItemIDsByParentID($document->ID);
          $doc_ids_array = array();
          for($i = 0; $i < sizeof($document_ids); $i++)
          {
            array_push($doc_ids_array, $document_ids[$i]->ID);
          }

          //delete document user permissions
          $remove_permission = new CollaborationCenterUserPermission();
          for($z = 0; $z < sizeof($lists); $z++)
          {
            $remove_permission->removeUserPermissionByItemIDs($doc_ids_array, $lists[$z]->ID, 'space');
          }
        }
        
        //send email
        global $wpdb;

        //get site url
        $site_url = get_option('home');
        for($i = 0; $i < sizeof($users_roles); $i++)
        {
          if($users_roles[$i][0] != '')
          {
            //add users permissions
            $permission = new CollaborationCenterUserPermission();
            //validate one of selected roles
            if(array_key_exists($users_roles[$i][1] , $ef_collaboration_item_roles))
            {
              $permission_from = ($document->is_space == 1)?"space":"document";
              for($z = 0; $z < sizeof($lists); $z++)
              {
                if($lists[$z]->ID == $users_roles[$i][0] && $lists[$z]->permission_from == 'space')
                {
                  $permission_from = 'space';
                  break;
                }
              }
              $args = array(
                  'user_id' => $users_roles[$i][0],
                  'permission' => $users_roles[$i][1],
                  'item_ID' => $document->ID,
                  'permission_from' => $permission_from
              );

              $permission->addUserPermission($args);
              $permission->save();

              //add permission to items on a space if it's a space
              if($document->is_space == 1)
              {
                for($z = 0; $z < sizeof($doc_ids_array); $z++)
                {
                  //check permission not added before re-add it
                  $existingPermission = new CollaborationCenterUserPermission();
                  $exists = $existingPermission->hasPermissionByItemID($users_roles[$i][0], $doc_ids_array[$z]);
                  if($exists == 0)
                  {
                    $add_document_permission = new CollaborationCenterUserPermission();
                    $args = array(
                        'user_id' => $users_roles[$i][0],
                        'permission' => $users_roles[$i][1],
                        'item_ID' => $doc_ids_array[$z],
                        'permission_from' => ($document->is_space == 1)?"space":"document"
                    );

                    $add_document_permission->addUserPermission($args);
                    $add_document_permission->save();
                  }
                }
              }

              //send email in case not sent before
              $sendemail = true;
              for($z = 0; $z < sizeof($lists); $z++)
              {
                if($lists[$z]->ID == $users_roles[$i][0] && $lists[$z]->permission == $args['permission'])
                {
                  $sendemail = false;
                  break;
                }
              }

              if($sendemail)
              {
                global $ef_email_msg_labels;
                global $ef_email_msg_labels_ar;
                $user_id = $users_roles[$i][0];
                $file = get_user_meta($user_id, 'prefered_language', true);
                if($file == "en")
                {
                    $messages = $ef_email_msg_labels;
                }
                else 
                {
                    $messages = $ef_email_msg_labels_ar;
                }

                $title = sprintf($messages["%s invited you to collaborate on a %s"], bp_core_get_user_displayname($owner_id),($document->is_space == 1?  strtolower($messages['Space']):  strtolower($messages['Document'])));

                $msg = sprintf($messages["Hi, %s"],  bp_core_get_user_displayname($user_id))."<br/><br/>";
                $msg .= sprintf($messages['%s invited you to collaborate on "%s" as %s'],bp_core_get_user_displayname($owner_id)
                        ,($document->is_space == 1?
                        "<a href=\"$site_url/$file/collaboration-center/shared/spaces/$document->ID/\">".$document->title."</a>":
                        "<a href=\"$site_url/$file/collaboration-center/spaces/$document->item_ID/document/$document->ID/edit/\">".$document->title."</a>"), $messages[$ef_collaboration_item_roles[$users_roles[$i][1]]])."<br/><br/>";
                $msg .= $messages['We are looking forward more contribution from your side to enrich EgyptFOSS.']."<br/><br/>";
                $msg .= $messages['Thank you again!'];
                $args = array(
                    "title" => $title,
                    "message" => $msg
                );

                set_query_var( 'template_inputs', serialize($args));
                ob_start();
                get_template_part( 'mail-templates/email-content' );
                $message = ob_get_contents();
                ob_end_clean();

                // Send the test mail
                $to = bp_core_get_user_email($user_id);
                // to espace converting of special characters to &#[0-9]; entities
                $title = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $title); 
                $result = wp_mail($to,$title,$message);
              }
            }
          }
        }
        
        for($z = 0; $z < sizeof($lists); $z++)
        {
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
            $total_invitees = $item_permission->hasPermissionOnAllSpace($lists[$z]->ID,$doc_ids_array);
            //$string .= $lists[$z]->ID.' '.$total_document_per_space.' '.$total_invitees."<br />";

            if($total_invitees != $total_document_per_space)
            {
              //remove user on space
              $permission_remove = new CollaborationCenterUserPermission();
              $permission_remove->removeUserPermissionByItemIDs(array($document->item_ID), $lists[$z]->ID, 'space');
            }
          }
        }

        echo json_encode(array("status"=>"success", "data"=> ""));
        die();
      }
    }else
    {
      echo json_encode(array("status"=>"error", "data"=> __("Not Found","egyptfoss")));
    }
    die();
}
add_action('wp_ajax_ef_save_invited_users', 'ef_save_invited_users');
add_action('wp_ajax_nopriv_ef_save_invited_users', 'ef_save_invited_users');

//load invited users per item
function ef_load_invited_users() {
  if(isset($_POST['item_id'])) {
    if(!is_numeric($_POST['item_id'])) {
      echo "false";
      die();
    }
    load_orm();
    global $ef_collaboration_item_roles;
    $users_with_permissions = CollaborationCenterUserPermission::where('item_ID','=', $_POST['item_id'])->get();
    $user_ids = '|||';
    for($i = 0; $i < sizeof($users_with_permissions); $i++) {
        set_query_var('ef_current_user_invite_id', $users_with_permissions[$i]->user_id);
        if(isset($users_with_permissions[$i]->permission))
        {
          set_query_var('ef_current_user_invite_role', $users_with_permissions[$i]->permission);
        }
        $user_ids .= $users_with_permissions[$i]->user_id.','.$users_with_permissions[$i]->permission.'|';
        get_template_part('CollaborationCenter/template-parts/content', 'user_invite');
    }
    echo $user_ids;
    die();
  } else {
    echo "false";
  }
  die();
}
add_action('wp_ajax_ef_load_invited_users', 'ef_load_invited_users');
add_action('wp_ajax_nopriv_ef_load_invited_users', 'ef_load_invited_users');


//set array of status depends on users permissions
function ef_return_status_by_permission($document_id, $space_id, $owner_id) {
  global $ef_collaboration_item_status;
  load_orm();
  //user id
  $user_id = get_current_user_id();
  
  if($user_id == $owner_id) {
    $roles = wp_get_current_user()->roles;
    if(!in_array("author", $roles) && !in_array("administrator", $roles)) {
      unset($ef_collaboration_item_status['published']);
    }
    return $ef_collaboration_item_status;
  }
  
  //get user permission from docuemnt id
  $permission = new CollaborationCenterUserPermission();
  $user_permission = $permission->getPermissionByItemID($user_id, $document_id);
  $current_user = wp_get_current_user();
  $roles = $current_user->roles;
  if($user_permission != NULL) {
    $result = $ef_collaboration_item_status;
    if($user_permission->permission == 'reviewer') {
      unset($result['published']);
    }else if($user_permission->permission == 'editor') {
      unset($result['published']);
      unset($result['reviewed']);
    }
  } else {
    //check space permission
    $user_permission = $permission->getPermissionByItemID($user_id, $space_id);
    if($user_permission != NULL) {
      $result = $ef_collaboration_item_status;
      if($user_permission->permission == 'reviewer')
      {
        unset($result['published']);
      } else if($user_permission->permission == 'editor') {
        unset($result['published']);
        unset($result['reviewed']);
      }
    } else {
      return [];
    }
  }
  if(!in_array("author", $roles) && !in_array("administrator", $roles)) {
    unset($result['published']);
  }
  return $result;
}

//disable wordpress tinymce
function ef_tiny_mce_before_init($args) {
  $args['readonly'] = 1;
  return $args;
}
/*
add_filter( 'tiny_mce_before_init', function( $args ) {
    // do you existing check for published here
    if ( 1 == 1 )
         

    return $args;
} );*/

function ef_save_invited_groups() {
  if ( ! check_ajax_referer( 'validate_space_document', 'security', false ) ) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Token","egyptfoss"))));
    die();
  }
  if(!current_user_can('add_new_ef_posts')) {
    echo json_encode(array("status"=>"error", "data" => __("You don't have permission to access this page or you have signed out.",'egyptfoss')));
    die();
  }
  load_orm();
  $args = array();
  $args['type'] = $_POST['type'];
  $args['subtype'] = $_POST['subtype_id'];
  $args['interest'] = $_POST['interest_ids'];
  $args['technology'] = $_POST['technology_ids'];
  $args['industry'] = $_POST['industry_id'];
  $taxPermissionArr = array();
  $anonPermissionArr = array();
  $taxPermissionIds = array();
  $anonPermissionNames = array();
 
  $emptyData = true;
  foreach ($args as $arg) {
    if(!empty($arg)) {
      $emptyData = false;
    }
  }  
  $args['item_id'] = $_POST['item_id'];
 
  /*if($emptyData)
  {
    echo json_encode(array("status"=>"error", "data"=> __("please choose at least 1 feature to share","egyptfoss")));
    die();
  }*/
  
  $collabCenterItem = new CollaborationCenterItem();
  if(!$collabCenterItem->isMyItem(get_current_user_id(),$args['item_id'])) {
    echo json_encode(array("status"=>"error", "data"=> __("you don't have permission to share this item","egyptfoss")));
    die(); 
  }
  
  global $account_types;
  if(!empty($args['type']) && !in_array($args['type'], $account_types)) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Type","egyptfoss"))));
    die();
  }
  
  if(!empty($args['type'])) {
    array_push($anonPermissionArr, array("permission"=>"editor",
      "item_ID"=>$args["item_id"],
      "name" => $args['type'],
      "type" => "type"));
    array_push($anonPermissionNames, $args['type']);
  }
  
  global $account_sub_types;
  if(!(array_key_exists($args['subtype'],$account_sub_types) && $account_sub_types[$args['subtype']] ==  $args['type'])) {
    if(!empty($args['subtype'])) {
      echo json_encode(array("status"=>"error", "data"=> __("Invalid Subtype","egyptfoss")));
      die(); 
    }
  }
  
  if(!empty($args['subtype'])) {
    array_push($anonPermissionArr, array("permission"=>"editor",
    "item_ID"=>$args["item_id"],
    "name" => $args['subtype'],
    "type" => "sub_type",
    "permission_from" => "document"));
    array_push($anonPermissionNames, $args['subtype']);
  }
  
  $multi_taxs = array("interest","technology");
  foreach ($multi_taxs as $multi_tax)
  if(!empty($args[$multi_tax])) {
    foreach($args[$multi_tax] as $id) {
      $termTax = new TermTaxonomy();
      $isTermExists = $termTax->getTermTaxonomy($id, $multi_tax);
      if(!$isTermExists)
      {
        $trans = ($multi_tax == "technology")?sprintf(_x("Invalid %s.","feminist","egyptfoss"),__(ucfirst($multi_tax),"egyptfoss")):sprintf(__("Invalid %s.","egyptfoss"),__(ucfirst($multi_tax),"egyptfoss"));
        echo json_encode(array("status"=>"error", "data"=> $trans));
        die(); 
      }
      array_push($taxPermissionArr, array("permission"=>"editor",
      "item_ID"=>$args["item_id"],
      "tax_id" => $isTermExists->term_taxonomy_id,
      "taxonomy" => $isTermExists->taxonomy,
      "permission_from" => "document"));
      array_push($taxPermissionIds, (string)$isTermExists->term_taxonomy_id);
    }
  }
  
  if(!empty($args['industry'])) {
    $termTax = new TermTaxonomy();
    $isTermExists = $termTax->getTermTaxonomy($args['industry'], 'theme');
    if(!$isTermExists) {
      echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Theme","egyptfoss"))));
      die(); 
    }
    array_push($taxPermissionArr, array("permission"=>"editor",
    "item_ID"=>$args["item_id"],
    "tax_id" => $isTermExists->term_taxonomy_id,
    "taxonomy" => $isTermExists->taxonomy,
    "permission_from" => "document"  ));
    array_push($taxPermissionIds, (string)$isTermExists->term_taxonomy_id);
  }
  //saving data
  $item = CollaborationCenterItem::where("ID","=",$_POST["item_id"])->first();
  foreach($item->documents()->get() as $document) {
    if($document->isSharedBySpace()) {
      $document->taxPermissions()->delete();
      $document->anonPermissions()->delete();
      foreach($anonPermissionArr as $arg) {
        $arg["item_ID"] = $document->ID;
        $arg["permission_from"] = "space";
        $anonPermission = new CollaborationCenterAnonPermission();
        $anonPermission->addAnonPermission($arg);
        $anonPermission->save();
      }
      foreach($taxPermissionArr as $arg) {
        $arg["item_ID"] = $document->ID;
        $arg["permission_from"] = "space";
        $taxPermission = new CollaborationCenterTaxPermission();
        $taxPermission->addTaxPermission($arg);
        $taxPermission->save();
      }
    }
  }
  if(!$item->is_space) {
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
  foreach($anonPermissionArr as $arg) {
    $anonPermission = new CollaborationCenterAnonPermission();
    $anonPermission->addAnonPermission($arg);
    $anonPermission->save();
  }
  
  foreach($taxPermissionArr as $arg) {
    $taxPermission = new CollaborationCenterTaxPermission();
    $taxPermission->addTaxPermission($arg);
    $taxPermission->save();
  }
  echo json_encode(array("status"=>"success", "data"=> __("Sharing Settings saved successfully.","egyptfoss")));
  die();
}
add_action('wp_ajax_ef_save_invited_groups', 'ef_save_invited_groups');
add_action('wp_ajax_nopriv_ef_save_invited_groups', 'ef_save_invited_groups');

function ef_list_invited_groups() {
  load_orm();
  $item = CollaborationCenterItem::where("ID","=",$_POST["item_id"])->first();
  ob_start();
  include(locate_template('CollaborationCenter/template-parts/inviteModalGroupContent.php'));
	//get_template_part('CollaborationCenter/template-parts/content','space');
  $template_space = ob_get_contents();			
  ob_end_clean();
  echo json_encode(array("status"=>"success","data"=>$template_space));
  die();
}
add_action('wp_ajax_ef_list_invited_groups', 'ef_list_invited_groups');
add_action('wp_ajax_nopriv_ef_list_invited_groups', 'ef_list_invited_groups');

function ef_load_users_by_display_name() {
  if(!is_numeric($_GET['item_id'])) {
    echo "false";
    die();
  }
  load_orm();
  //load item
  $collabCenterItem = new CollaborationCenterItem();
  $item = $collabCenterItem->getDocumentByID($_GET['item_id']);
  if(!$item) {
    echo "false";
    die();
  }    
  
  //included Ids
  $users_roles = $_GET['users_ids_roles'];
  $users_array = array();
  for($i = 0; $i < sizeof($users_roles); $i++)
  {
    if($users_roles[$i][0] != '' && is_numeric($users_roles[$i][0])){
      array_push($users_array, $users_roles[$i][0]);
    }
  }

  $name = $_GET['display_name'];
  global $wpdb;
  $check_sql = "SELECT ID,display_name FROM $wpdb->users join $wpdb->usermeta as umeta on umeta.user_id = $wpdb->users.ID 
            WHERE display_name like %s AND user_status = 0
            AND ID <> %s AND umeta.meta_key='wpRuvF8_capabilities' AND umeta.meta_value NOT LIKE %s";
  if(sizeof($users_array) > 0) {
      $check_sql .=" AND ID not in (".  implode(",", $users_array).")";
  }

  $users = $wpdb->get_results($wpdb->prepare( $check_sql, '%'.$name.'%', $item->owner_id, '%subscriber%'));
  
  for($i = 0; $i < sizeof($users); $i++) {
    $users[$i]->avatar = get_avatar( $users[$i]->ID, 32 );
    $users[$i]->display_name = bp_core_get_user_displayname($users[$i]->ID);
  }
  wp_send_json_success( $users );
  die();
}
add_action('wp_ajax_ef_load_users_by_display_name', 'ef_load_users_by_display_name');
add_action('wp_ajax_nopriv_ef_load_users_by_display_name', 'ef_load_users_by_display_name');

function ef_get_contributors_count_by_ajax() {
  load_orm();
  $item_id = $_POST['item_id'];
  $item = CollaborationCenterItem::where("ID","=",$item_id)->first();
  $itemCount = ($item)?$item->getNoOfContributers():0;
  echo json_encode(array("status"=>"success","data"=>$itemCount));
  die();
}
add_action('wp_ajax_ef_get_contributors_count_by_ajax', 'ef_get_contributors_count_by_ajax');
add_action('wp_ajax_nopriv_ef_get_contributors_count_by_ajax', 'ef_get_contributors_count_by_ajax');

function ef_get_group_contributors_count_by_ajax() {
  load_orm();
  $item_id  = $_POST['item_id'];
  $locale   = $_POST['locale'];
  $item = CollaborationCenterItem::where("ID","=",$item_id)->first();
  $groupContributors = $item->getGroupContributors( $locale );
  $gs = array();
  
  include( ABSPATH . 'system_data.php' );
                  
  foreach( $groupContributors as $g ) {
    $trans = __( $g->name, 'egyptfoss' );
    if( $locale == 'ar' && isset( $ar_sub_types[ $g->name ] ) ) {
      $trans = $ar_sub_types[ $g->name ];
    }
    else if( $locale == 'en' && isset( $en_sub_types[ $g->name ] ) ) {
      $trans = $en_sub_types[ $g->name ];
    }
    $gs[] = $trans;
  }
  
  $contibutors = (count( $groupContributors ))?$gs:0;
  
  echo json_encode(array("status"=>"success","data"=>implode( ', ', $contibutors )));
  
  die();
}
add_action('wp_ajax_ef_get_group_contributors_count_by_ajax', 'ef_get_group_contributors_count_by_ajax');
add_action('wp_ajax_nopriv_ef_get_group_contributors_count_by_ajax', 'ef_get_group_contributors_count_by_ajax');

function sendShareItemsEmails($user_id,$document,$owner_id,$headers) {
  $site_url = get_option('home');
  $file = get_user_meta($user_id, 'prefered_language', true);
  
  if ($file == "en") {
    $messages = $msg;
  } else {
    $messages = $msg_ar;
  }

  $title = sprintf($messages["%s invited you to collaborate on a %s"], bp_core_get_user_displayname($owner_id), ($document->is_space == 1 ? strtolower($messages['Space']) : strtolower($messages['Document'])));

  $msg = sprintf($messages["Hi, %s"], bp_core_get_user_displayname($user_id)) . "<br/><br/>";
  $msg .= sprintf($messages['%s invited you to collaborate on "%s" as %s'], bp_core_get_user_displayname($owner_id)
      , ($document->is_space == 1 ?
        "<a href=\"$site_url/$file/collaboration-center/spaces/$document->ID/\">" . $document->title . "</a>" :
        "<a href=\"$site_url/$file/collaboration-center/spaces/$document->item_ID/document/$document->ID/edit/\">" . $document->title . "</a>"), $messages["Editor"]) . "<br/><br/>";
  $msg .= $messages['We are looking forward more contribution from your side to enrich EgyptFOSS.'] . "<br/><br/>";
  $msg .= $messages['Thank you again!'];
  $args = array(
    "title" => $title,
    "message" => $msg
  );

  set_query_var('template_inputs', serialize($args));
  ob_start();
  get_template_part('mail-templates/email-content');
  $message = ob_get_contents();
  ob_end_clean();

  // Send the test mail
  $to = bp_core_get_user_email($user_id);
  $result = wp_mail($to, $title, $message,$headers);
  return $result;
}

// Allow access to own content only
function my_authored_content($query) {
  //get current user info to see if they are allowed to access ANY posts and pages
  $current_user = wp_get_current_user();
  // set current user to $is_user
  $is_user = $current_user->user_login;

  //if is admin or 'is_user' does not equal #username
  if (!current_user_can('manage_options')){
    //if in the admin panel
    if($query->is_admin) {

        global $user_ID;
        $query->set('author',  $user_ID);

    }
    return $query;
  }
  return $query;
}
add_filter('pre_get_posts', 'my_authored_content');

function CollaborationBreadCrumb($view,$crudType,$space = null,$doc = null) {
  
  $docFullTitle = $doc['title'];
  $docTitle = mb_substr($doc["title"], 0, 15);
  $doc["title"] = (strlen($docTitle) == strlen($doc["title"]))?$docTitle:$docTitle . "...";
  
  $spaceFullTitle = $space['title'];
  $spaceTitle = mb_substr($space["title"], 0, 15);
  $space["title"] = (strlen($spaceTitle) == strlen($space["title"]))?$spaceTitle:$spaceTitle . "...";
  $breadCrumb = "";
  $isNested = false;
  switch($view) {
    case "document":
      $link = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "spaces");
      $title = __("My Spaces","egyptfoss");
      $breadCrumb = "<li><a href='{$link}'>{$title}</a></li>";
      break;
    case "shared":
      $link = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "shared");
      $title = __("Shared with me","egyptfoss");
      $breadCrumb = "<li><a href='{$link}'>{$title}</a></li>";
      break;
    case "published":
      $link = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "published");
      $title = __("Published Documents","egyptfoss");
      $breadCrumb = "<li><a href='{$link}'>{$title}</a></li>";
      break;
  }
  switch($crudType)
  {
    case "Add":
      $spaceLink = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "space_content", array($space["ID"]));
      $title = $doc["title"];
      $breadCrumb .= "<li><a href='{$spaceLink}' title='{$spaceFullTitle}'>{$space["title"]}</a></li>";
      $isNested = true;
      break;
    case "Edit":
      $title = $doc["title"];
      $item = new CollaborationCenterItem();
      if($item->isSharedItemByUser(get_current_user_id(), $space["ID"]))
      {
        $spaceLink = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "shared_space_content", array($space["ID"]));
        $breadCrumb .= "<li><a href='{$spaceLink}' title='{$spaceFullTitle}'>{$space["title"]}</a></li>"; 
      }
      else if($view == "document")
      {
        $spaceLink = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "space_content", array($space["ID"]));
        $breadCrumb .= "<li><a href='{$spaceLink}' title='{$spaceFullTitle}'>{$space["title"]}</a></li>"; 
      }else
      {
        $breadCrumb .= "";
      }
      $isNested = true;
      break;
    case "List":
      $title = $space["title"];
      $breadCrumb .= "";
      $isNested = true;
      break;
    case "View":
      $title = $space["title"];
      $breadCrumb .= "";
      $isNested = true;
      break;
    case "Revision":
      if($view != "shared"){
        $title = $doc["title"];
        $item = new CollaborationCenterItem();
        $spaceLink = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "space_content", array($space["ID"]));
        $breadCrumb .= "<li><a href='{$spaceLink}' title='{$spaceFullTitle}'>{$space["title"]}</a></li>"; 
      }
      $link = get_current_lang_page_by_template('CollaborationCenter/template-edit-document.php', false, null, array($space["ID"],$doc["ID"]));
      $breadCrumb .= "<li><a href='{$link}' title='{$docFullTitle}'>{$doc["title"]}</a></li>";
      $isNested = true;
      break;
  }

  if($breadCrumb != "")
  {
    $breadCrumb = "<ul class='breadcrumb'>".$breadCrumb."</ul>";
    return $breadCrumb;
  }
  return false;
}

function ef_load_published_templates() {
  load_orm();
  $section = $_POST['section'];
  $collaborationCenterItem = new CollaborationCenterItem();
  $items = $collaborationCenterItem->getPublishedDocuments($section);
  $emptyMsg = __("No published documents!", "egyptfoss");
  ob_start();
  include(locate_template('CollaborationCenter/template-parts/published-item-cards.php'));
  $template_space = ob_get_contents();			
  ob_end_clean();
  echo json_encode(array("status"=>"success","data"=>$template_space));
  die();
}
add_action('wp_ajax_ef_load_published_templates', 'ef_load_published_templates');
add_action('wp_ajax_nopriv_ef_load_published_templates', 'ef_load_published_templates');

function ef_remove_space_document() {
  if ( ! check_ajax_referer( 'validate_space_document', 'security', false ) ) {
    echo json_encode(array("status"=>"error", "data"=> sprintf(__("Invalid %s.","egyptfoss"),__("Token","egyptfoss"))));
    die();
  }
  if(!current_user_can('add_new_ef_posts')) {
    echo json_encode(array("status"=>"error", "data" => __("You don't have permission to access this page or you have signed out.",'egyptfoss')));
    die();
  }
  load_orm();
  $ef_collaboration_center_messages = array("errors" => array());
  
  $item_id = -1;
  if(!isset($_POST['item_id']))
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("you are trying to cheat",'egyptfoss'))));  
  } else if(!is_numeric($_POST['item_id'])) {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("you are trying to cheat",'egyptfoss'))));  
  }
  
  $item_id = $_POST['item_id'];
  
  //Load Item
  $collabItem = CollaborationCenterItem::where('ID','=', $item_id)->first();
  if(!$collabItem)
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("you are trying to cheat",'egyptfoss'))));  
  }
  
  $newCollaborationCenter = new CollaborationCenterItem();  
  if(!$newCollaborationCenter->isMySpace(get_current_user_id(), $item_id) 
          && !$newCollaborationCenter->isMyDocument(get_current_user_id(), $item_id))
  {
    $ef_collaboration_center_messages["errors"] = array_merge($ef_collaboration_center_messages["errors"], array(sprintf(__("you are trying to cheat",'egyptfoss'))));  
  }
  
  if ($ef_collaboration_center_messages["errors"]) 
  {
    echo json_encode(array("status"=>"error","data"=>implode('<br/>', $ef_collaboration_center_messages["errors"])));
    die();
  }

  //Check Space or document to remove
  $newCollaborationCenter->removeSpaceorDocument($collabItem);

  echo json_encode(array("status"=>"success"));
  die();
}
add_action('wp_ajax_ef_remove_space_document', 'ef_remove_space_document');
add_action('wp_ajax_ef_remove_space_document', 'ef_remove_space_document');