<?php
function ef_changer_title_header() {
  global $page, $paged;
  
  $singular_pages = array('news','product','success_story','open_dataset',
      'request_center','quiz','template-add-open-dataset.php','template-add-resources-open-dataset.php',
      'template-manage-news.php','page-add-product.php','template-edit-product.php',
      'template-add-success-story.php','page-add-event.php',
      'template-add-request-center.php', 'MarketPlace/template-add-service.php');
  
	$title = array(
		'title' => '',
	);
  
	if ( is_404() ) { // If it's a 404 page, use a "Page not found" title.
		$title['title'] = __( 'Page not found' );
	} elseif ( is_search() ) { // If it's a search, use a dynamic search results title.
		/* translators: %s: search phrase */
		$title['title'] = sprintf( __( 'Search Results for ',"egyptfoss" )."(%s)", get_search_query() );
	} elseif ( is_front_page() ) { // If on the front page, use the site title.
		$description = ucwords(get_bloginfo( 'description', 'display')); 
		$title['title'] = __($description,"egyptfoss");
	} elseif ( is_post_type_archive() ) { // If on a post type archive, use the post type archive title.
		$title['title'] = __(post_type_archive_title( '', false ),"egyptfoss");
	} elseif ( is_tax() ) { //If we're on the blog page that is not the homepage or a single post of any post type, use the post title.
		$title['title'] = single_term_title( '', false );
	} elseif ( is_home() || is_singular() ) {
		$title['title'] = ucwords(__(single_post_title( '', false ),"egyptfoss"));
	} elseif ( is_category() || is_tag() ) { // If on a category or tag archive, use the term title.
		$title['title'] = single_term_title( '', false );
	} elseif ( is_author() && $author = get_queried_object() ) { // If on an author archive, use the author's display name.    
		$title['title'] = $author->display_name;
	} 

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
		$title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
	}

	// Append the description or site title to give context.
	if ( is_front_page() ) {
		$title['tagline'] = get_bloginfo( 'name', 'display' );
	} else {
		$title['site'] = get_bloginfo( 'name', 'display' );
	}
	$sep = apply_filters( 'document_title_separator', '-' );
  
	$title = apply_filters( 'document_title_parts', $title );
    
  if ( is_post_type_archive('tribe_events') ) {
  	$title['title'] = __("Events","egyptfoss");
  } else if (is_post_type_archive('quiz'))
  {
    $title['title'] = __("Awareness Center","egyptfoss");
  } else if ( is_post_type_archive('open_dataset') ) {
  	$title['title'] = _n("Open Dataset","Open Datasets",2,"egyptfoss");
  } else if ( is_post_type_archive('request_center') ) {
  	$title['title'] = __("Request Center", "egyptfoss");
  } else if ( is_post_type_archive('service') ) {
  	$title['title'] = __("Services Market", "egyptfoss");
  } else if (is_singular('tribe_events') ) {
  	$title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Events","egyptfoss");;
  } else if(bp_is_user_change_avatar()) {
  	$title['title'] = str_replace("Change Profile Photo",__('Change Photo',"egyptfoss"),$title['title']);
  } else if (is_singular($singular_pages)) {
    switch (get_post_type()) {
      case 'news':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("News","egyptfoss");
        break;
      case 'product':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Products","egyptfoss");
        break;      
      case 'success_story':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Success Stories","egyptfoss");
        break;   
      case 'open_dataset':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Open Datasets","egyptfoss");
        break;        
      case 'request_center':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Request Center","egyptfoss");
        break;
      case 'service':
        $title['title'] = __(single_post_title( '', false ),"egyptfoss"). ' - '. __("Services Market","egyptfoss");
        break;
      case 'quiz':
        $quiz_id = get_post_meta(get_the_ID(), "quiz_id", true);
        $pageTitle = __(single_post_title( '', false ),"egyptfoss");
        if($quiz_id)
        { 
          global $wpdb;
          $sql = "select quiz_name from {$wpdb->prefix}mlw_quizzes where quiz_id = {$quiz_id}";
          $quiz_name = $wpdb->get_col($sql);
          if($quiz_name[0] != NULL)
          {
            $pageTitle = $quiz_name[0];
          }
        }
        $title['title'] = $pageTitle. ' - '. __("Awareness Center","egyptfoss");
        break;      
    }
    
    //Add/Edit templates
    switch (get_page_template_slug()) {
      case 'template-add-open-dataset.php':
        $title['title'] = __("Suggest","egyptfoss").' '._n("Open Dataset","Open Datasets",1,"egyptfoss"). ' - '. __("Open Datasets","egyptfoss");
        break;   
      case 'template-add-resources-open-dataset.php':
        $post_title = ef_get_post_title_header($_GET['did']);
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. $post_title. _n("Open Dataset","Open Datasets",2,"egyptfoss");
        break;    
      case 'template-manage-news.php':
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("News","egyptfoss");
        break;    
      case 'page-add-product.php':
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("Products","egyptfoss");
        break; 
      case 'template-edit-product.php':
        $post_title = ef_get_post_title_header($_GET['pid']);
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. $post_title. __("Products","egyptfoss");
        break;        
      case 'template-add-success-story.php':
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("Success Stories","egyptfoss");
        break;        
      case 'page-add-event.php':
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("Events","egyptfoss");
        break;          
      case 'template-add-request-center.php':
        $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("Request Center","egyptfoss");
        break;          
    }
  } else if(get_page_template_slug() == "template-quiz-result.php")
  {
    $result_id = efGetValueFromUrlByKey("result");
    $returnArray = ef_returnResult($result_id);
    if($returnArray == "404")
    {
      $title['title'] = __(get_the_title(),"egyptfoss"). ' - '. __("Awareness Center","egyptfoss");
    }else {
      $title['title'] = $returnArray["quiz_title"]. ' - '. __("Awareness Center","egyptfoss");
    }
  } else if(bp_is_register_page()) {
  	$title['title'] = __('Create an Account',"egyptfoss");
  } else if(bp_is_user_profile_edit()) {
  	$title['title'] = str_replace("Edit",__('Edit',"egyptfoss"),$title['title']);
    //replace Profile to Abount
    $title['title'] = str_replace(_x('Profile', 'Profile header menu',"buddypress"), __('About',"egyptfoss"), $title['title']);
  } else if(bp_is_user_profile()) {
  	$title['title'] = str_replace(_x('Profile', 'Profile header menu',"buddypress"), __('About',"egyptfoss"), $title['title']);
  } else if(bp_is_activation_page()) {
  	$title['title'] = __('Activate your account',"egyptfoss");
  } else if(get_page_template_slug() == "template-request-thread.php") {
    $title['title'] = get_the_title($_GET['pid']) .' - '. __('responses', "egyptfoss");
  } else if(get_page_template_slug() == "CollaborationCenter/template-single-document.php") {
    $title['title'] = __('Collaboration Center', "egyptfoss");
  } else if(bp_is_user_settings_general())
  {
    $title['title'] = __('Account', "egyptfoss"). ' - '.$title['title'];
  } else if(strtok($_SERVER["REQUEST_URI"],'?') == "/".pll_current_language()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/")
  {
    $title['title'] = __('Products', "egyptfoss"). ' - '.$title['title'];
  } else if($_SERVER["QUERY_STRING"] == "status=403")
  {
    $title['title'] = '';
  } else if(bp_is_user_activity())
  {
    $title['title'] = __('Timeline', "egyptfoss"). ' - '.$title['title'];
  } else if(strtok($_SERVER["REQUEST_URI"],'?') == "/".pll_current_language()."/request-center/edit/")
  {
    $post_title = ef_get_post_title_header($_GET['rid']);
    $title['title'] = __("Edit","egyptfoss"). ' - '. $post_title. __("Request Center","egyptfoss");
  } else if(strtok($_SERVER["REQUEST_URI"],'?') == "/".pll_current_language()."/events/edit/") {
    $post_title = ef_get_post_title_header($_GET['pid']);
    $title['title'] = __("Edit","egyptfoss"). ' - '. $post_title. __("Events","egyptfoss");
  } else if(get_page_template_slug() == "CollaborationCenter/template-listing-items.php")
  {
    $pathDetails = explode("/", strtok($_SERVER["REQUEST_URI"],'?'));
    $isSharedView = array_search("shared", $pathDetails);
    $isSpaceView = array_search("spaces", $pathDetails);
    $isPublishedView = array_search("published", $pathDetails);
    if($isPublishedView || strtok($_SERVER["REQUEST_URI"],'?') == "/".pll_current_language()."/collaboration-center/")
    {
      $title['title'] = __("Published Documents","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
    } else if($isSharedView)
    {
      $space_id = efGetValueFromUrlByKey("spaces");
      if($space_id)
      {
        load_orm();
        $collabCenter = new CollaborationCenterItem();
        $spaceTitle = $collabCenter->where("ID", "=", $space_id)->first()->title;
        $title['title'] = $spaceTitle. ' - ' .__("Shared with me","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
      } else {      
        $title['title'] = __("Shared with me","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
      }
    } else if($isSpaceView)
    {
      $space_id = efGetValueFromUrlByKey("spaces");
      if($space_id)
      {
        load_orm();
        $collabCenter = new CollaborationCenterItem();
        $spaceTitle = $collabCenter->where("ID", "=", $space_id)->first()->title;
        $title['title'] = $spaceTitle. ' - ' .__("My Spaces","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
      } else {
        $title['title'] = __("My Spaces","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
      }
    }
  } else if(get_page_template_slug() == "CollaborationCenter/template-add-document.php")
  {
    $space_id = efGetValueFromUrlByKey("spaces");
    if($space_id)
    {
      load_orm();
      $collabCenter = new CollaborationCenterItem();
      $spaceTitle = $collabCenter->where("ID", "=", $space_id)->first()->title;
      $title['title'] = __("Add", "egyptfoss"). ' - ' .$spaceTitle. ' - ' .__("My Spaces","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
    } 
  } else if(get_page_template_slug() == "CollaborationCenter/template-edit-document.php")
  {
    $space_id = efGetValueFromUrlByKey("spaces");
    if($space_id)
    {
      load_orm();
      $collabCenter = new CollaborationCenterItem();
      $spaceTitle = $collabCenter->where("ID", "=", $space_id)->first()->title;
      
      //check document title
      $document_id = efGetValueFromUrlByKey("document");
      if($document_id)
      {
        $item_spaces = __("My Spaces","egyptfoss");
        if($collabCenter->isSharedItemByUser(get_current_user_id(), $space_id))
        {
          $item_spaces = __("Shared with me","egyptfoss");
        }
        $documentTitle = $collabCenter->where("ID", "=", $document_id)->first()->title;
        $title['title'] = __("Edit", "egyptfoss"). ' - '. $documentTitle. ' - ' .$spaceTitle. ' - ' .$item_spaces. ' - '. $post_title. __("Collaboration Center","egyptfoss");
      }else {
        $title['title'] = __("Edit", "egyptfoss"). ' - ' .$spaceTitle. ' - ' .__("My Spaces","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
      }
    } 
  }else if(get_page_template_slug() == "CollaborationCenter/template-single-revision.php"){
    load_orm();
    $document_id = efGetValueFromUrlByKey("revisions");
    $item = CollaborationCenterItemHistory::where(array( 'id'=> $document_id))->first();
    $title['title'] = __("Revision of","egyptfoss") . " [".mysql2date('d/m/Y',$item->created_date)."]: "  . $item->title. ' - '. $post_title. __("Collaboration Center","egyptfoss");    
  }
  
  //check single document
  if(get_page_template_slug() == "CollaborationCenter/template-single-document.php")
  {
    $document_id = efGetValueFromUrlByKey("published");
    if($document_id)
    {
      load_orm();
      $collabCenter = new CollaborationCenterItem();
      $documentTitle = $collabCenter->where("ID", "=", $document_id)->first()->title;
      $title['title'] = $documentTitle. ' - ' .__("Published Documents","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
    }else {
      $title['title'] = __("Published Documents","egyptfoss"). ' - '. $post_title. __("Collaboration Center","egyptfoss");
    }
  }

	$title = implode( " $sep ", array_filter( $title ) );
	$title = wptexturize( $title );
	$title = convert_chars( $title );
	$title = esc_html( $title );
	$title = capital_P_dangit( $title );

	return $title;

}

function ef_wp_head() {
  add_filter( "pre_get_document_title", 'ef_changer_title_header',999 );
}
add_action( "init", 'ef_wp_head' );

function ef_bp_header_title($bp_title) {
  if(is_array($bp_title)) {
    return $bp_title[0];
  } else {
    return $bp_title ;
  }
}
//add_filter( 'bp_get_title_parts', 'ef_bp_header_title',999,1 );

function ef_get_post_title_header($post_id)
{
  $page_title = get_the_title($post_id);
  if($page_title == ''){
    return '';
  }
  return get_the_title($post_id).' - ';
}
