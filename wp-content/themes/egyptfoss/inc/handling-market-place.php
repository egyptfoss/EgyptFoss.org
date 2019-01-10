<?php
define( "ef_services_per_page", 9 );

function save_service_frontEnd() {
  global $wpdb;
  $current_lang = pll_current_language();
  $ef_service_messages = array("errors" => array());

  $ef_service_messages = array("errors" => array());
  $ef_service_messages["errors"] = checkServiceFrontendValidation();
  if($ef_service_messages["errors"]) {
    set_query_var("ef_service_messages", $ef_service_messages);
    return false;
  }
  
  $filter_params = array("service_description", "service_constraints", "service_conditions");
  foreach ($filter_params as $parameter) {
    if (isset($_POST[$parameter])) {
      $_POST[$parameter] = strip_js_tags($_POST[$parameter]);
    }
  }
  $required_fields = array(
    "service_title" => "Title",
    "service_description" => "Description",
    "service_category" => "Category", 
  );
  foreach ($required_fields as $field => $label) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_service_messages["errors"] = array_merge($ef_service_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }

  $contains_letter_fields = array(
    "service_title" => "Title",
    "service_description" => "Description",
    "service_conditions" => "Conditions",
    "service_constraints" => "Constraints",
    "technology"=>"Technology",
    "interest" => "Interest"
  );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);
    if(is_array($_POST[$field])) {
      $hasError = false;
      foreach ($_POST[$field] as $term) {
        $is_numbers_only = preg_match("/^[0-9]{1,}$/", $term);
        $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $term);
        if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field])) && $hasError == false) {
          $hasError = true;
          $ef_service_messages["errors"] = array_merge($ef_service_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
        }
      }
    } else {
      if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field])) ) {
        $ef_service_messages["errors"] = array_merge($ef_service_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
      }
    }
  }
  
  $uncreated_taxs = array("theme" => "Theme", "service_category" => "Category");
  foreach ($uncreated_taxs as $uncreated_tax => $label) {
    if (isset($_POST[$uncreated_tax]) && !empty($_POST[$uncreated_tax])) {
      foreach ($_POST[$uncreated_tax] as $term) {
        if (!get_term_by('name', $term, $uncreated_tax)) {
          $ef_service_messages["errors"] = array_merge($ef_service_messages["errors"], array(sprintf(__("Please select already exist %s", "egyptfoss"), __($label, "egyptfoss"))));
        }
      }
    }
  }

  if ($ef_service_messages["errors"]) {
    set_query_var("ef_service_messages", $ef_service_messages);
    return false;
  }

  if(isset($_POST['sid'])) {
    $service_id = $_POST['sid'];
    $service_record = Post::where('ID', '=', $service_id)->where('post_author', '=', $current_user->ID)->first();
    $service_data = array(
      'ID' => $service_id,
      'post_title'=>$_POST["service_title"],
      'post_content' => $_POST["service_description"],
    );
    if( ($service_record->post_status == 'pending') && ($service_record->post_title != $_POST["service_title"]) ) {
      $service_data['post_name'] = wp_unique_post_slug(str_replace(' ', '-', strtolower($_POST["service_title"])), $service_id, 'publish', 'service', 0);
    }
    wp_update_post( $service_data );
    ef_handling_image_and_screenshots_upload($service_id,'service');
  } else {
    $service_data = array(
      'post_title' => $_POST["service_title"],
      'post_content' => $_POST["service_description"],
      'post_name' => wp_unique_post_slug(str_replace(' ', '-', strtolower($_POST["service_title"])), $service_id, 'publish', 'service', 0),
      'post_type' => 'service',
      'post_status' => 'pending'
    );
    $service_id = wp_insert_post($service_data);
    wp_set_object_terms($service_id, $current_lang, 'language');
    ef_handling_image_and_screenshots_upload($service_id,'service');
  }
    
  $taxonomies = array("theme", "service_category", "technology", "interest");
  foreach ($taxonomies as $tax) {
    $new_tax = isset($_POST[$tax]) ? $_POST[$tax] : '';
    $term_ids = wp_set_object_terms($service_id, $new_tax, $tax);
    $return_arr = getTermFromTermTaxonomy($term_ids);
    if(sizeof($return_arr) == 1 && ($tax != 'technology' && $tax != 'interest') ){
      $return_arr = $return_arr[0]; 
    }
    update_post_meta($service_id, $tax, $return_arr);
  }

  $service_meta = array("constraints", "conditions");
  foreach ($service_meta as $meta) {
    if (isset($_POST['service_' . $meta])) {
      update_post_meta($service_id, $meta, $_POST['service_' . $meta]);
    }
  }
    
  if(isset($_POST['sid'])) {
    setMessageBySession("edit-service", "success", __("Service edited successfully", "egyptfoss"));
  } else {
    setMessageBySession("add-service", "success" ,__("Service", "egyptfoss") . " " . _x("Added successfully, it is now under review", "feminist", "egyptfoss"));
  }
  $req_postname = $wpdb->get_row("SELECT post_name FROM ". $wpdb->prefix."posts where ID = $service_id");
  $url = ($req_postname->post_name == '') ? get_post_permalink($service_id) : home_url($current_lang."/marketplace/services/".$req_postname->post_name);
  wp_redirect($url);
}

function checkServiceFrontendValidation() {
  $errors = array();
  
  if(isset($_POST['sid'])) {
    load_orm();
    $current_user = wp_get_current_user();
    $service_record = Post::where('ID', '=', $_POST['sid'])->where('post_author', '=', $current_user->ID)->first();
    if($service_record == null) {
      $errors["no_service"] = __("No Service Found","egyptfoss");
      return $errors;
    }
  } else {
    if(empty($_FILES["service_image"]['tmp_name'] )) {
      $errors["service_image_required"] = __("Image","egyptfoss"). ' ' . __("is required",'egyptfoss');
    }
  }

  if (mb_strlen($_POST['service_title'],'UTF-8') == 0) {
    $errors["title"] = __("Title","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    if (mb_strlen($_POST['service_title'],'UTF-8') > 100 && mb_strlen($_POST['service_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should not be more than %d characters",'egyptfoss'),100);
    }
    if (mb_strlen($_POST['service_title'],'UTF-8') < 10 && mb_strlen($_POST['service_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should be at least %d characters",'egyptfoss'),10);
    }
  }
  
  $description = $_POST['service_description'];
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = __("Description","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Description","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
    }
  }
  
  if(!isset($_POST['service_category'])) {
    $errors["category"] = __("Category","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
  
  if(!empty($_FILES["service_image"]['tmp_name'] )) {
    if(!is_image($_FILES["service_image"]['tmp_name'])) {
      $errors["image"] = __("please enter correct image type",'egyptfoss');
    }
  }
  return $errors;
}

function ef_archive_service() {
  load_orm();
  $archived = array('status'=>'error');
  $current_user = wp_get_current_user();
  $service_id = $_POST['id'];
  $service_record = Post::where('ID', '=', $service_id)->where('post_author', '=', $current_user->ID)->first();
  if($service_record != null) {
    $service_record->updatePostStatus($service_id, 'archive');
    $archived = array('status'=>'success');
  }
  echo json_encode($archived);
  die();
}
add_action('wp_ajax_ef_archive_service', 'ef_archive_service');
add_action('wp_ajax_nopriv_ef_archive_service', 'ef_archive_service');

// return services query result/count
function ef_get_services( $args = array(), $get_count = FALSE ){
  global $wpdb, $account_types, $account_sub_types;
  
  $no_of_posts = constant("ef_services_per_page");
  
  if( !$get_count ) {
    $args['offset'] = get_query_var("ef_listing_service_offset") ? get_query_var("ef_listing_service_offset") : 0;
  }
    
  $where_condition = '';
  $join_condition = '';
  
  // Conditions
  if( $args[ 'technology_id' ] != -1 ){
    $join_condition   .= " JOIN {$wpdb->prefix}term_relationships AS tech_rel ON post.ID = tech_rel.object_id
                           JOIN {$wpdb->prefix}term_taxonomy AS tech_termtax ON tech_rel.term_taxonomy_id = tech_termtax.term_taxonomy_id";
    $where_condition  .= " AND tech_termtax.taxonomy = 'technology' AND tech_termtax.term_id = {$args[ 'technology_id' ]}";
  }
  
  if( $args[ 'theme_id' ] != -1 ) {
    $join_condition   .= " JOIN {$wpdb->prefix}postmeta AS theme_pmeta ON post.ID = theme_pmeta.post_id AND theme_pmeta.meta_key = 'theme'";
    $where_condition  .= " AND theme_pmeta.meta_value = {$args[ 'theme_id' ]}";
  }
  
  if( $args[ 'category' ] != -1 ) {
    $where_condition  .= " AND pmeta.meta_key = 'service_category' AND pmeta.meta_value = {$args[ 'category' ]}";
  }
  
  if( in_array( $args[ 'type' ], $account_types ) ) {
    $where_condition  .= " AND umeta.meta_value like '%:\"{$args[ 'type' ]}\";%' ";
  }
  
  if( array_key_exists( $args[ 'subtype' ], $account_sub_types ) ) {
    $where_condition  .= " AND umeta.meta_value like '%:\"{$args[ 'subtype' ]}\";%' ";
  }
  
  $sql = "SELECT post.ID, post.post_author, user.display_name, post.post_date, post.post_content, post.post_title, post.guid
          FROM {$wpdb->prefix}posts AS post
          JOIN {$wpdb->prefix}users AS user ON post.post_author = user.ID
          JOIN {$wpdb->prefix}usermeta AS umeta ON post.post_author = umeta.user_id AND umeta.meta_key = 'registration_data'
          JOIN {$wpdb->prefix}postmeta AS pmeta ON post.ID = pmeta.post_id
          $join_condition
          WHERE (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
          $where_condition
          GROUP BY post.ID
          ORDER BY post.post_date DESC";
          
  if( !$get_count ) {
    $sql .= " LIMIT {$args[ 'offset' ]}, {$no_of_posts} ";
  }
  
  $results = $wpdb->get_results($sql);
  
  if( $get_count ) {
    return count( $results );
  }
  
  return $results;
}

// get services count
function ef_count_service_ajax() {
  $args = array(
    "post_status"   => "publish",
    "post_type"     => "service",
    "offset"        => 0,
    "category"      => -1,
    "technology_id" => -1,
    "theme_id"   => -1,
    "type_id"       => -1,
    "subtype_id"      => -1,
  );
  
  //check category
  if( isset( $_POST[ 'category' ] ) && $_POST[ 'category' ] != -1 ) {
    $args[ "category" ] = urldecode( $_POST[ 'category' ] );
  }
  
  //check technology
  if( isset( $_POST[ 'service_technology' ] ) ) {
    $args[ "technology_id" ] = ef_get_taxonomy_id_by_name( urldecode( $_POST['service_technology'] ), 'technology' );
  }
    
  //check theme
  if(isset($_POST['service_theme'])){
    $args[ "theme_id" ] = ef_get_taxonomy_id_by_name( urldecode( $_POST[ 'service_theme' ] ), 'theme' );
  }
  
  //check type
  if( isset( $_POST[ 'service_type' ] ) ) {
    $args[ "type" ] = urldecode( $_POST[ 'service_type' ] );
  }
  
  //check subtype
  if( isset( $_POST[ 'service_subtype' ] ) ) {
    $args[ "subtype" ] = urldecode( $_POST[ 'service_subtype' ] );
  }

  echo ef_get_services( $args, TRUE );
  die();
}
add_action('wp_ajax_ef_count_service_ajax', 'ef_count_service_ajax');
add_action('wp_ajax_nopriv_ef_count_service_ajax', 'ef_count_service_ajax');

// view more for listing market place
function ef_load_more_listing_service() {
  set_query_var( 'ef_listing_service_offset', $_POST[ 'offset' ] );

  set_query_var( 'ef_listing_service_category_id', -1 );
  set_query_var( 'ef_listing_service_technology_id', -1 );
  set_query_var( 'ef_listing_service_theme_id', -1 );
  set_query_var( 'ef_listing_service_type_id', -1 );
  set_query_var( 'ef_listing_service_subtype_id', -1 );
  
  //check category
  if( isset( $_POST[ 'category' ] ) ){
    if( !in_array( $_POST[ 'category' ], array( "all", -1 ) ) ) {
      set_query_var( 'ef_listing_service_category', urldecode( $_POST[ 'category' ] ) );
    }
  }
  
  //check technology
  if( isset( $_POST[ 'service_technology' ] ) ){
    $current_technology = ef_get_taxonomy_id_by_name( urldecode( $_POST[ 'service_technology' ] ), 'technology' );
    set_query_var( 'ef_listing_service_technology_id', $current_technology );
  }
  
  //check theme
  if( isset( $_POST[ 'service_theme' ] ) ){
    $current_theme = ef_get_taxonomy_id_by_name( urldecode( $_POST[ 'service_theme' ] ), 'theme' );
    set_query_var( 'ef_listing_service_theme_id', $current_theme );
  }
  //check type
  if( isset( $_POST[ 'service_type' ] ) ){
    set_query_var( 'ef_listing_service_type_id', urldecode( $_POST[ 'service_type' ] ) );
  }
  
  //check subtype
  if( isset( $_POST[ 'service_subtype' ] ) ){
    set_query_var( 'ef_listing_service_subtype_id', urldecode( $_POST[ 'service_subtype' ] ) );
  }
  
  get_template_part( 'template-parts/content', 'listing_market_place' );
  die();
}
add_action('wp_ajax_ef_load_more_listing_service', 'ef_load_more_listing_service');
add_action('wp_ajax_nopriv_ef_load_more_listing_service', 'ef_load_more_listing_service');


// Market place ( service ) admin customization

// add custom columns
function service_cpt_columns($columns) {

    $new_columns = array( 
      'category'  => __('Category', 'egyptfoss'),
      'author'    => __('Provider', 'egyptfoss'),
      'responses' => __('# of responses', 'egyptfoss')
    );
        
    return array_merge($columns, $new_columns);
}
add_filter('manage_service_posts_columns' , 'service_cpt_columns');


// fill custom columns with values
function manage_service_cpt_columns( $column, $post_id ) {

	switch( $column ) {
		case 'responses' :
			echo getThreadsCount( $post_id );
      break;
		case 'category' :
			$terms = get_the_terms( $post_id, 'service_category' );
      if( !empty( $terms ) ) {
        $term = $terms[ 0 ];
        echo $term->name;
      }
	}
}
add_action( 'manage_service_posts_custom_column', 'manage_service_cpt_columns', 10, 2 );

// set responses column as sortable column
function service_cpt_sortable_columns( $columns ) {

	$columns['responses'] = 'responses';

	return $columns;
}
add_filter( 'manage_edit-service_sortable_columns', 'service_cpt_sortable_columns' );

// add search filters 
function service_cpt_filter_restrict_manage_posts(){
    $type = '';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('service' == $type){
      $terms = get_terms( 'service_category', array( 'hide_empty' => false ) );
      ?>
      <select name="service_cat">
        <option value=""><?php _e('All categories ', 'egyptfoss'); ?></option>
        <?php
            $current_v = isset($_GET['service_cat'])? $_GET['service_cat']:'';
            foreach ($terms as $term) {
              printf(
                '<option value="%s"%s>%s</option>',
                $term->slug,
                $term->slug == $current_v? ' selected="selected"':'',
                $term->name
              );
            }
        ?>
      </select>
      <?php
    }
}
add_action( 'restrict_manage_posts', 'service_cpt_filter_restrict_manage_posts' );

// manage submittion of custom search filters
function service_cpt_parse_query( $query ){
    global $pagenow;
    
    $type = '';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if ( 'service' == $type && is_admin() && $pagenow == 'edit.php' && !empty( $_GET[ 'service_cat' ] ) ) {
        $taxquery = array(
          array(
              'taxonomy' => 'service_category',
              'field' => 'slug',
              'terms' => $_GET[ 'service_cat' ],
          )
        );

        $query->set( 'tax_query', $taxquery );
    }
}
add_filter( 'parse_query', 'service_cpt_parse_query' );

function can_rate_service($service_id) {
  load_orm();
  $current_user = wp_get_current_user();
  $review_record = Review::where('rateable_id', '=', $service_id)->where('reviewer_id', '=', $current_user->ID)->first();
  return ($review_record == null) ? true : false ;
}

function ef_submit_review() {
  if ( ! check_ajax_referer( 'add_response', 'security', false ) ) {
    $result = json_encode( array('message'=>'Unexpected Error') );
  } else {
    load_orm();
    $current_user = wp_get_current_user();
    $rateable_id = $_POST['pid'];
    $rateable = Post::where('ID', '=', $rateable_id)->first();
    $lang = get_user_meta($rateable->post_author, 'prefered_language', true);
    $displayed_thread_id = $_POST['tid'];
    $displayed_thread = Thread::where('id', '=', $displayed_thread_id)->where('request_id', '=', $rateable->ID)->first();
    if( $rateable != null && $displayed_thread != null && $displayed_thread->owner_id != $current_user->ID && user_can($displayed_thread->user_id,'add_new_ef_posts') && !empty($_POST['review'] && ($_POST['review'] >= 0) && ($_POST['rate'] <= 5) ) ) {
      $review_record = new Review;
      $data = array(
        'rateable_id' => $rateable_id,
        'provider_id' => $rateable->post_author,
        'reviewer_id' => $current_user->ID,
        'rate' => $_POST['rate'],
        'review' => $_POST['review'],
      );
      $review_record->saveReview($data);
      if( $review_record->save() ) {
        $average_rate = $review_record->updateAverageRate($rateable_id);
        
        // market place badges management
        $mb_badge = new Badge( $rateable->post_author );
        $mb_badge->efb_manage_mb_badges( $rateable_id, $average_rate );
        
        // send emails to service author with earned badges;
        foreach( $mb_badge->badges_earned as $badge ) {
          global $wpdb;
          $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
          $result = $wpdb->get_results($query, ARRAY_A);

          if( class_exists( 'EFBBadges' ) && !empty( $result ) ) {
            sendNewBadgeAchiever( $rateable->post_author, new EFBBadges( $result[0] ) );
          }
        }
        
        $requester_rate = requester_rate($rateable_id, $displayed_thread->user_id);
        $section_header = __('Requester Rate','egyptfoss');
        $result = json_encode( array('status'=>'success', 'reviewers_count'=>$average_rate['reviewers_count'], 'rate'=>$average_rate['rate'], 'section_header'=>$section_header) );
      } else {
        $result = json_encode( array('status'=>'error') );
      }
    } else {
      $result = json_encode( array('status'=>'error') );
    }
  }
  echo $result;
  die();
}
add_action('wp_ajax_ef_submit_review', 'ef_submit_review');
add_action('wp_ajax_nopriv_ef_submit_review', 'ef_submit_review');

function getRecentReviews($service_id) {
  load_orm();
  return Review::where('rateable_id', '=', $service_id)->orderBy('created_at', 'DESC')->limit(3)->get();
}

function ef_show_more_reviews() {
  load_orm();
  $limit = 5;
  $service_id = $_POST['pid'];
  $offset = $_POST['offset'];
  $count = Review::where('rateable_id', '=', $service_id)->count();
  $reviews = Review::where('rateable_id', '=', $service_id)->orderBy('created_at', 'DESC')->offset($offset)->limit($limit)->get();
  $output = '';
  foreach ($reviews as $review) { 
    $reviewer_name = bp_core_get_user_displayname($review->reviewer_id);
    $output .= '<div class="review-panel clearfix"><div class="review-panel-header clearfix">';
    $output .= '<img src="' . bp_core_fetch_avatar( array( 'item_id' => $review->reviewer_id, 'html' => false ) ) . '" class="user-avatar lfloat" alt="' . $reviewer_name . '" />';
    $output .= '<div class="reviewer-identity lfloat"><h3>';
    $output .= '<a href="' . home_url() . "/members/" . bp_core_get_username($review->reviewer_id).'/about/'.'">';
    $output .= $reviewer_name;
    $output .= '</a></h3><br><small class="post-date"><i class="fa fa-clock-o"></i>' . date('d/m/Y - h:i A', strtotime($review->created_at));
    $output .= '</small></div><div class="rating-stars rfloat">';
    $output .= '<span class="provider-rating rating-readonly" data-rating="' . $review->rate . '" title="' . round($review->rate, 2) . '">';
    $output .= '</span></div></div><p>' . $review->review . '</p></div>';
  }
  $offset = (int)$offset + count($reviews);
  $load_more = ($count > $offset) ? true : false;
  $result = json_encode( array('status'=>'success', 'output'=>$output, 'offset'=>$offset, 'load_more'=>$load_more) );
  echo $result;
  die();
}
add_action('wp_ajax_ef_show_more_reviews', 'ef_show_more_reviews');
add_action('wp_ajax_nopriv_ef_show_more_reviews', 'ef_show_more_reviews');

function requester_rate($service_id, $requester_id) {
  load_orm();
  $review = Review::where('rateable_id', '=', $service_id)->where('reviewer_id', '=', $requester_id)->first();
  return ($review == null) ? 0 : $review->rate;
}