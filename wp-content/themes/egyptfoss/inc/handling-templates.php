<?php
function template_redirection_conditions() {
  $current_template = get_page_template_slug();
  $redirected = "";
  $current_url = "";
  $require_login = true;
  $require_privilage = true;
  $scripts = array("js"=>array(),"php"=>array());
  $localizes = array();
  $sessionMessage = "";
  switch ($current_template):
    case "page-add-product.php":
      $redirected = "addproduct";
      $current_url = get_page_link();
      $scripts = array(
        "js"=>array(array("handle"=>"edit-product-js","src"=>get_stylesheet_directory_uri() . '/js/edit-product.js')));
    break;
    case "template-edit-product.php":
      $redirected = "editproduct";
      $current_url = get_current_lang_page_by_template("template-edit-product.php")."?pid=".$_GET['pid']; 
      $scripts = array(
        "js"=>array(array("handle"=>"edit-product-js","src"=>get_stylesheet_directory_uri() . '/js/edit-product.js')));
    break;
    case "template-manage-news.php":
      $redirected = "addnews";
      $current_url = get_page_link();
      $scripts = array(
        "js"=>array(array("handle"=>"handling-news-js","src"=>get_stylesheet_directory_uri() . '/js/handling-news.js')));
    break;
    case "template-add-success-story.php":
      $redirected = "addsuccessstory";
      $current_url = get_page_link();
      $scripts = array(
        "js"=>array(array("handle"=>"handling-success-story-js","src"=>get_stylesheet_directory_uri() . '/js/handling-success-story.js')));
    break;
    case "template-add-feedback.php":
      $redirected = "addfeedback";
      $current_url = get_current_lang_page_by_template("template-add-feedback.php");
      $scripts = array(
        "js"=>array(array("handle"=>"handling-feedback-js","src"=>get_stylesheet_directory_uri() . '/js/handling-feedback.js')));
    break;
    case "template-add-open-dataset.php":
      $redirected = "addopendataset";
      $current_url = get_page_link();
      $scripts = array(
        "js"=>array(array("handle"=>'handling-open-dataset-js',"src"=>get_stylesheet_directory_uri() . '/js/handling-open-dataset.js')));
    break;
    case "template-edit-request-center.php":
      $redirected = "editrequestcenter";
      $current_url = get_current_lang_page_by_template("template-edit-request-center.php")."?rid=".$_GET['rid'];
      $scripts = array(
        "js"=>array(array("handle"=>"add-request-center-js","src"=>get_stylesheet_directory_uri() . '/js/handling-request-center.js')));
        $localizes = array(array("handle"=>'add-request-center-js',"object" => 'ef_request', "vars" => array("is_admin" => false)));
    break;
    case "template-add-resources-open-dataset.php":
      $redirected = "addresourcesopendataset";
      $current_url = get_current_lang_page_by_template("template-add-resources-open-dataset.php")."?did=".$_GET['did'];
      $scripts = array(
        "js"=>array(array("handle"=>"handling-open-dataset-js","src"=>get_stylesheet_directory_uri() . '/js/handling-open-dataset.js')));
    break;
    case "template-request-thread.php":
      $require_privilage = false;
      $redirected = "respondtorequest";
      $current_url = get_current_lang_page_by_template("template-request-thread.php")."?pid=".$_GET['pid'];
    break;
    case "template-add-request-center.php":
      $sessionMessage = array("key"=>"login-error","status"=>"error","message"=>__('Please log in to suggest a new request',"egyptfoss"));
      $current_url = site_url($_SERVER["REQUEST_URI"]);
      $scripts = array(
        "js"=>array(array("handle"=>"add-request-center-js","src"=>get_stylesheet_directory_uri() . '/js/handling-request-center.js')));
        $localizes = array(array("handle"=>'add-request-center-js',"object" => 'ef_request', "vars" => array("is_admin" => false)));
    break;
    case "template-add-expert-thought.php":
      $require_privilage = false;
      $redirected = "expert";
      $sessionMessage = array("key"=>"login-error","status"=>"error","message"=>__('Please log in to write a new thought',"egyptfoss"));
      $current_url = get_current_lang_page_by_template("template-add-expert-thought.php");
      $scripts = array(
        "js"=>array(array("handle"=>"handling-expert-thought-js","src"=>get_stylesheet_directory_uri() . '/js/handling-expert-thought.js')));
      $localizes = array(array("handle"=>'handling-expert-thought-js',"object" => 'ef_expert', "vars" => array("crud" => "add")));
    break;
    case "template-edit-expert-thought.php":
      $require_privilage = false;
      $redirected = "editexpert";
      $sessionMessage = array("key"=>"login-error","status"=>"error","message"=>__('Please log in to edit the thought',"egyptfoss"));
      $post_id = efGetValueFromUrlByKey( 'expert-thoughts' );
      $current_url = get_current_lang_page_by_template("template-edit-expert-thought.php", false, null, array( $post_id ) );
      $scripts = array(
        "js"=>array(array("handle"=>"handling-expert-thought-js","src"=>get_stylesheet_directory_uri() . '/js/handling-expert-thought.js')));
      $localizes = array(array("handle"=>'handling-expert-thought-js',"object" => 'ef_expert', "vars" => array("crud" => "edit")));
    break;
    case "MarketPlace/template-add-service.php":
      $sessionMessage = array("key"=>"login-error","status"=>"error","message"=>__('Please log in to suggest a new service',"egyptfoss"));
      $current_url = site_url($_SERVER["REQUEST_URI"]);
      $scripts = array(
        "js"=>array(array("handle"=>"handling-service-js","src"=>get_stylesheet_directory_uri() . '/js/handling-market-place.js')));
        $localizes = array(array("handle"=>'handling-service-js',"object" => 'ef_service', "vars" => array("is_admin" => false)));
    break;
    case "MarketPlace/template-edit-service.php":
      $sessionMessage = array("key"=>"login-error","status"=>"error","message"=>__('Please log in to edit the service',"egyptfoss"));
      $current_url = site_url($_SERVER["REQUEST_URI"]);
      $scripts = array(
        "js"=>array(array("handle"=>"handling-service-js","src"=>get_stylesheet_directory_uri() . '/js/handling-market-place.js')));
        $localizes = array(array("handle"=>'handling-service-js',"object" => 'ef_service', "vars" => array("is_admin" => false)));
    break;
    case "template-service-thread.php":
      $require_privilage = false;
      $redirected = "respondtoservice";
      $current_url = get_current_lang_page_by_template("template-service-thread.php")."?pid=".$_GET['pid'];
    break;
    default:
      $require_login = false;
      $require_privilage = false;
    break;
  endswitch;
  
    if ( !is_user_logged_in() && $require_login) {
      if(!empty($sessionMessage)){
        setMessageBySession($sessionMessage["key"], $sessionMessage["status"],$sessionMessage["message"]);
        wp_redirect(get_current_lang_page_by_template('template-login.php')."?redirect_to={$current_url}");
      }else{
        wp_redirect(home_url( pll_current_language()."/login/?redirected={$redirected}&redirect_to={$current_url}" ));
      }
      exit;
    } else if (!current_user_can('add_new_ef_posts') && $require_privilage) {
      //wp_redirect( home_url( '?action=unauthorized' ) );
      wp_redirect(home_url('/?status=403'));
      exit;
    } else if ($redirected == "expert") {
      $is_expert = get_user_meta(get_current_user_id(),"is_expert",true);
      if(!$is_expert) {
         include( get_query_template( '404' ) );
          exit;
      }else if(!current_user_can('add_new_ef_posts'))
      {
        wp_redirect(home_url('/?status=403'));
        exit;
      }
    } else if ($redirected == "editexpert") {
        $post_id    = efGetValueFromUrlByKey( 'expert-thoughts' );
        $post_data = get_post( $post_id );
        
        if( !is_numeric( $post_id ) || !$post_data || $post_data->post_type != 'expert_thought' || $post_data->post_author != get_current_user_id() ) {
            include( get_query_template( '404' ) );
            exit;
        }
        
        $is_expert = get_user_meta(get_current_user_id(),"is_expert",true);
        if(!$is_expert) {
          wp_redirect(home_url('/?status=403'));
          exit;
        }else if(!current_user_can('add_new_ef_posts'))
        {
          wp_redirect(home_url('/?status=403'));
          exit;
        }
    }
    
    foreach($scripts["js"] as $script)
    {
      wp_enqueue_script( $script["handle"], $script["src"], array('jquery'), '', true);
    }
    foreach($localizes as $localize)
    {
      wp_localize_script($localize["handle"], $localize["object"], $localize["vars"]);
    }
 
    
  if(is_singular('open_dataset'))
  {
    wp_enqueue_script( 'handling-open-dataset-js', get_stylesheet_directory_uri() . '/js/handling-open-dataset.js', array('jquery'), '', true);
  }
}
add_action( 'template_redirect', 'template_redirection_conditions' );

function loadFilesAndRedirect() {
  if (is_post_type_archive(array('expert_thought'))) {
    wp_enqueue_script('handling_expert_thought', get_stylesheet_directory_uri() . '/js/handling-expert-thought.js', array('jquery'), '', true);
    wp_localize_script('handling_expert_thought', 'ef_expert', array("crud" => "list", "per_page" => constant("ef_expert_thought_per_page")));
  
  } else if (is_post_type_archive(array('product'))) {
    wp_enqueue_script('listing-product-js', get_stylesheet_directory_uri() . '/js/listing-products.js', array('jquery'), '', true);
    wp_localize_script('listing-product-js', 'ef_products', array("per_page" => constant("ef_products_per_page"), "site_url" => site_url(), "current_lang" => pll_current_language()));
    $getParams = parse_url($_GET['q']);
    parse_str($getParams['query'], $getParams);
    $getParams = array_merge($getParams, $_GET);
    unset($getParams['q']);
    if ((empty($getParams) || isset($getParams['all'])) && is_user_logged_in()) {
      $user_preferences = (get_user_meta(get_current_user_id(), 'ef_product_preferences', true));
      if ($user_preferences) {
        wp_redirect(home_url()."/".pll_current_language()."/products/" . $user_preferences);
        exit();
      }
    }
  }
}
add_action( 'pre_get_posts', 'loadFilesAndRedirect' );

function collaborationCenterRedirectionCondition() {
  
  if (get_page_template_slug() == "CollaborationCenter/template-listing-items.php") {
    if (!is_user_logged_in()) {
      $isShared = efGetValueFromUrlByKey("shared");
      $isSpace = efGetValueFromUrlByKey("spaces");
      if ($isShared || $isSpace) {
        setMessageBySession("login-error", "error", __("Please log in to access this page", "egyptfoss"));
        $current_url = $_SERVER['REQUEST_URI']; //get_current_lang_page_by_template("CollaborationCenter/template-listing-items.php",false,"spaces");
        wp_redirect(home_url(get_current_lang_page_by_template('template-login.php') . "?&redirect_to={$current_url}"));
        exit;
      }
    }
    $isShared = efGetValueFromUrlByKey("shared");
    if ($isShared && !current_user_can('add_new_ef_posts')) {
      //wp_redirect(home_url('?action=unauthorized'));
      wp_redirect(home_url('/?status=403'));
      exit;
    }
    load_orm();
    
    //check if space is deleted
    $isSpace = efGetValueFromUrlByKey("spaces");
    if($isSpace)
    {
      $space = CollaborationCenterItem::where('ID','=', $isSpace)->first();
      if(!$space)
      {
        include( get_query_template( '404' ) );
        exit;
      }
    }
    
    //include js file
    wp_enqueue_script('add-collaboration-center-js', get_stylesheet_directory_uri() . '/js/handling-collaboration-center.js', array('jquery'), '', true);
    wp_localize_script('add-collaboration-center-js', 'ef_collaboration', array("which_crud" => "list"));
  } else if (get_page_template_slug() == "CollaborationCenter/template-add-document.php") {
    if (!is_user_logged_in()) {
      setMessageBySession("login-error", "error", __("Please log in to access this page", "egyptfoss"));
      $current_url = get_current_lang_page_by_template("CollaborationCenter/template-add-document.php");
      wp_redirect(home_url(get_current_lang_page_by_template('template-login.php') . "?&redirect_to={$current_url}"));
      exit;
    }
    if (!current_user_can('add_new_ef_posts')) {
      //wp_redirect(home_url('?action=unauthorized'));
      wp_redirect(home_url('/?status=403'));
      exit;
    }

    //load orm
    load_orm();
    $user = get_current_user_id();
    $collabCenterItem = new CollaborationCenterItem();

    wp_enqueue_script('add-collaboration-center-js', get_stylesheet_directory_uri() . '/js/handling-collaboration-center.js', array('jquery'), '', true);
    wp_localize_script('add-collaboration-center-js', 'ef_collaboration', array("which_crud" => "create"));
  } 
  else if (get_page_template_slug() == "CollaborationCenter/template-edit-document.php") {
    if (!is_user_logged_in()) {
      setMessageBySession("login-error", "error", __("Please log in to access this page", "egyptfoss"));
      $current_url = $_SERVER['REQUEST_URI']; //get_current_lang_page_by_template("CollaborationCenter/template-edit-document.php");
      wp_redirect(home_url(get_current_lang_page_by_template('template-login.php') . "?&redirect_to={$current_url}"));
      exit;
    }
    
    if (!current_user_can('add_new_ef_posts')) {
      //wp_redirect(home_url('?action=unauthorized'));
      wp_redirect(home_url('/?status=403'));
      exit;
    }

    load_orm();
    $user = get_current_user_id();
    $collabCenterItem = new CollaborationCenterItem();
    $collabCenterUserPermission = new CollaborationCenterUserPermission();
    //get document id
    //$document_id = ef_get_id_from_url($_SERVER['REQUEST_URI'],"edit/");
    $document_id = efGetValueFromUrlByKey("document");
    if (!is_numeric($document_id)) {
      wp_redirect(home_url('/?status=403'));
      exit;
    }
    
    //check if document exists
    $document = CollaborationCenterItem::where('ID','=', $document_id)->first();
    if(!$document)
    {
      include( get_query_template( '404' ) );
      exit;
    }
    
    if (!isset($document_id) || !$collabCenterItem->isMyDocument($user, $document_id)) {
      //load space from docuemnt
      $document = new CollaborationCenterItem();
      $document = $document->getDocumentByID($document_id);
      if (!$document) {
        wp_redirect(home_url('/?status=403'));
        exit;
      }
      $space_id = $document->item_ID;
      //check if have permission on document itself or its space
      if (($collabCenterUserPermission->hasPermissionByItemID($user, $document_id) < 1 && $collabCenterUserPermission->hasPermissionByItemID($user, $space_id) < 1) && !$collabCenterItem->isSharedItemByUser($user, $document_id)) {
        wp_redirect(home_url('/?status=403'));
        exit;
      }
    }

    wp_enqueue_script('add-collaboration-center-js', get_stylesheet_directory_uri() . '/js/handling-collaboration-center.js', array('jquery'), '', true);
    wp_localize_script('add-collaboration-center-js', 'ef_collaboration', array("which_crud" => "edit"));
  }
}
add_action('template_redirect', 'collaborationCenterRedirectionCondition');

