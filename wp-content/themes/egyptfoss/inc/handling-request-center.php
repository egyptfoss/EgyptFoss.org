<?php
define("ef_request_center_per_page", 10);

function ef_save_request_center_frontEnd() {
  global $wpdb;
  $ef_request_center_messages = array("errors" => array());
  
  $filter_params = array("request_center_description", "request_center_constraints", "request_center_requirements");
  foreach ($filter_params as $parameter) {
      if (isset($_POST[$parameter])) {
        $_POST[$parameter] = strip_js_tags($_POST[$parameter]);
    }
  }
  
  $required_fields = array(
    "request_center_title" => "Title",
    "request_center_description" => "Description",
    "request_center_type" => "Type",
    "target_bussiness_relationship" => "Target business relationship", 
    );
  foreach ($required_fields as $field => $label) {

    if (!isset($_POST[$field]) || empty($_POST[$field])) {
      $ef_request_center_messages["errors"] = array_merge($ef_request_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("is required", "egyptfoss")));
    }
  }
  
 
  $contains_letter_fields = array(
    "request_center_title" => "Title",
    "request_center_description" => "Description",
    "request_center_requirements" => "Requirements",
    "request_center_constraints" => "Constraints",
    "technology"=>"Technology",
    "interest" => "Interest"
    );
  foreach ($contains_letter_fields as $field => $label) {
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST[$field]);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST[$field]);
    if(is_array($_POST[$field]))
    {
      $hasError = false;
      foreach ($_POST[$field] as $term)
      {
         $is_numbers_only = preg_match("/^[0-9]{1,}$/", $term);
         $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $term);
     
        if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field])) && $hasError == false) {
          $hasError = true;
          $ef_request_center_messages["errors"] = array_merge($ef_request_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
        }
      }
    }else
    {
    if (($is_numbers_only > 0 || !$contains_letters) && (isset($_POST[$field]) && !empty($_POST[$field])) ) {
      $ef_request_center_messages["errors"] = array_merge($ef_request_center_messages["errors"], array(__($label, "egyptfoss") . " " . __("must at least contain one letter", "egyptfoss")));
    }
    }
  }
  
  $uncreated_taxs = array("target_bussiness_relationship" => "Target business relationship", "request_center_type" => "Type");
  foreach ($uncreated_taxs as $uncreated_tax => $label) {
    if (isset($_POST[$uncreated_tax]) && !empty($_POST[$uncreated_tax])) {
      foreach ($_POST[$uncreated_tax] as $term) {
        if (!get_term_by('name', $term, $uncreated_tax)) {
          $ef_request_center_messages["errors"] = array_merge($ef_request_center_messages["errors"], array(sprintf(__("Please select already exist %s", "egyptfoss"), __($label, "egyptfoss"))));
        }
      }
    }
  }

  if ($ef_request_center_messages["errors"]) {
    set_query_var("ef_request_center_messages", $ef_request_center_messages);
    return false;
  }

  //check if editing or adding new
  if(isset($_POST['postid']))
  {
      $last_inserted_id = $_POST['postid'];
      $old_title = get_post($last_inserted_id)->post_title;
      //update post title
      $my_post = array(
          'ID'           => $last_inserted_id,
          'post_title'=>$_POST["request_center_title"],
          'post_status' => 'pending'
      );
      // Update the post into the database
      wp_update_post( $my_post );
  }
  else
  {
    $my_post = array(
      'post_title' => $_POST["request_center_title"],
      'post_type' => 'request_center',
      'post_status' => 'pending'
    );
    $last_inserted_id = wp_insert_post($my_post);
  }
  $taxonomies = array("target_bussiness_relationship", "request_center_type", "theme", "technology", "interest");
  foreach ($taxonomies as $tax) {
    if (isset($_POST[$tax])) {
        $term_ids = wp_set_object_terms($last_inserted_id, $_POST[$tax], $tax);  // insert tax in db
        $return_arr = getTermFromTermTaxonomy($term_ids);
        if(sizeof($return_arr) == 1 && ($tax != 'technology' && $tax != 'interest') ){
            $return_arr = $return_arr[0]; 
        }
        update_post_meta($last_inserted_id, $tax, $return_arr);
    }
  }

  $post_meta = array("description", "constraints", "requirements", "deadline");
  foreach ($post_meta as $meta) {
    if (isset($_POST['request_center_' . $meta])) {
      update_post_meta($last_inserted_id, $meta, $_POST['request_center_' . $meta]);
    }
  }

  $req_postname = $wpdb->get_row("SELECT post_name FROM ". $wpdb->prefix."posts where ID = $last_inserted_id");
  $url = home_url(pll_current_language()."/request-center/".$req_postname->post_name);
    
  //set redirect msg for edit/add
  if(isset($_POST['postid']))
  {
    setMessageBySession("edit-request-center","success",__("Request", "egyptfoss") . " " . __("edited successfully, it is now under review", "egyptfoss"));
  }else
  {
    setMessageBySession("add-request-center","success",__("Request", "egyptfoss") . " " . __("added successfully, it is now under review", "egyptfoss"));
  }
  wp_redirect($url);
}
function getRequestCenterAddLink($taxs) {
  $query_string = "";
  foreach ($taxs as $tax => $term_slugs) {
    if(!empty($term_slugs)) {
      if (is_array($term_slugs)) {
        $query_string .= $tax . "=" . join(",", $term_slugs) . "&";
      } else {
        $query_string .= $tax . "=" . $term_slugs . "&";
      }
    }
  }
  if ($query_string != "") {
    $query_string = "?" . rtrim($query_string, "&");
  }
  if (!is_user_logged_in()) {
    echo "<a href=" . home_url( pll_current_language().'/login/?redirected=addrequestcenter&redirect_to='.get_current_lang_page_by_template("template-add-request-center.php") ) . $query_string . " class='btn btn-light'><i class='fa fa-plus'></i> " .
    sprintf(__("Add %s", "egyptfoss"), _x("Request", "indefinite", "egyptfoss")) .
    "</a>";
  } else if (current_user_can('add_new_ef_posts')) {
    echo "<a href=" . home_url(pll_current_language() . "/request-center/add/") . $query_string . " class='btn btn-light'><i class='fa fa-plus'></i> " .
    sprintf(__("Add %s", "egyptfoss"), _x("Request", "indefinite", "egyptfoss")) .
    "</a>";
  }  else {
    echo "<a href=\"javascript:void(0)\" class=\"btn btn-light disabled rfloat\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"".__('You are not authorized to perform this action. Please contact us for more information.', 'egyptfoss')."\"><i class=\"fa fa-plus\"></i>".sprintf(__("Add %s", "egyptfoss"), _x("Request", "indefinite", "egyptfoss"))."</a>";
  }
}
if (is_admin()) {
  wp_enqueue_script('admin-handle-request-center', get_stylesheet_directory_uri() . '/js/handling-request-center.js', array('jquery'), '', true);
  wp_localize_script('admin-handle-request-center', 'ef_request', array("is_admin" => true));

  function ef_posts_filter_dropdown() {
    if (is_admin() && $_GET['post_type'] == 'request_center') {
    $taxs = array("request_center_type" => "types",
      "target_bussiness_relationship" => "business relationships",
      "theme" => "themes",
      "technology" => "technologies",
      "interest" => "interests");

    foreach ($taxs as $tax => $label) {
      echo "<select name='{$tax}' class='select2_admin_region'>";
      echo "<option value=''>All {$label}</option>";
      $terms = get_terms(($tax),array('hide_empty'=>0));
      foreach ($terms as $term) {
        ?>
        <option value='<?php echo $term->slug ?>' 
                    <?php if (isset($_GET[$tax]) && $_GET[$tax] == $term->slug) { ?> selected="" <?php } ?>>
        <?php echo $term->name ?>
        </option>
        <?php
      }
      echo '</select>';
    }
    }
  }

  add_action('restrict_manage_posts', 'ef_posts_filter_dropdown');

    function search_distinct() {
            return "DISTINCT";
    }
    add_filter('posts_distinct', 'search_distinct');
  
    function segnalazioni_search_join($join) {
    global $pagenow, $wpdb;
    global $ef_admin_filter_taxs;
    if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'request_center') {
      $isFilter = false;
      foreach ($ef_admin_filter_taxs as $tax) {
        if (!empty($_GET[$tax])) {
          $isFilter = true;
        }
      }
      if ($isFilter) {
        $join = " JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id ";
        $join .=" JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id";
        $join .=" JOIN {$wpdb->terms} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id";
      }
    }else if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'open_dataset') {
        $is_pending_resources = $_GET['pending_resources'];
        if($is_pending_resources == NULL)
        {
          if($_GET['s'] != '')
          {
              $join = " left JOIN {$wpdb->postmeta} as p_publisher ON {$wpdb->posts}.ID = p_publisher.post_id ";
              $join .= " left JOIN {$wpdb->postmeta} as p_theme ON {$wpdb->posts}.ID = p_theme.post_id ";
              $join .= " left JOIN {$wpdb->terms} as theme_terms ON p_theme.meta_value = theme_terms.term_id ";
              $join .= " left JOIN {$wpdb->postmeta} as p_type ON {$wpdb->posts}.ID = p_type.post_id ";
              $join .= " left JOIN {$wpdb->terms} as theme_type ON p_type.meta_value = theme_type.term_id ";
              $join .= " left JOIN {$wpdb->postmeta} as p_license ON {$wpdb->posts}.ID = p_license.post_id ";
              $join .= " left JOIN {$wpdb->terms} as theme_license ON p_license.meta_value = theme_license.term_id ";
              $join .= " left JOIN {$wpdb->postmeta} as p_formtas ON {$wpdb->posts}.ID = p_formtas.post_id ";
          }
        }else
        {
          $join = " left JOIN {$wpdb->postmeta} as p_resources ON {$wpdb->posts}.ID = p_resources.post_id ";
        }
    }else if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'feedback') {
        if( !empty( $_GET['seen'] ) ) {
          $current_user = get_current_user_id();
          $join = " LEFT JOIN {$wpdb->postmeta} as seen ON {$wpdb->posts}.ID = seen.post_id AND seen.meta_key = 'ef_feedback_{$current_user}_seen'";
        }
    }

    return $join;
  }

  add_filter('posts_join', 'segnalazioni_search_join');

  function segnalazioni_search_where($where) {
    global $pagenow, $wpdb;
    global $ef_admin_filter_taxs;

    if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'request_center') {
      $where_condition = "";
      foreach ($ef_admin_filter_taxs as $tax) {
        if (!empty($_GET[$tax])) {
          $where_condition .=" {$wpdb->terms}.slug = '{$_GET[$tax]}' or ";
        }
      }
      if ($where_condition != "") {
        $where_condition = rtrim($where_condition, "or ");
        $where_condition = " AND ( {$where_condition} )";
      }
    }else if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'open_dataset') {
        $is_pending_resources = $_GET['pending_resources'];
        if($is_pending_resources == NULL)
        {
          $search_str = $_GET['s'];
          if($search_str != ''){
            $extension_mime_types = array('pdf'=>'application/pdf','json'=>'application/json','csv'=>'text/csv','xml'=>'text/xml','html'=>'text/html', 'jpeg'=>'image/jpeg', 'jpg'=>'image/jpeg', 'png'=>'image/png', 'xls'=>'application/vnd.ms-excel', 'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'doc'=>'application/msword', 'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            $search_str_type = $extension_mime_types[$search_str];
            $where = " And wpRuvF8_posts.post_status <> 'trash' AND (p_publisher.meta_key='publisher') AND (p_formtas.meta_key='dataset_formats') AND (p_theme.meta_key='theme') AND (p_type.meta_key='dataset_type') AND (p_license.meta_key='datasets_license')";
            $where .= " and (p_publisher.meta_value like '%$search_str%' or (theme_terms.name like '%$search_str%' or theme_terms.name_ar like '%$search_str%')"
                    . "or (theme_type.name like '%$search_str%' or theme_type.name_ar like '%$search_str%')"
                    . "or (theme_license.name like '%$search_str%' or theme_license.name_ar like '%$search_str%')"
                    . "or (wpRuvF8_posts.post_title like '%$search_str%')"
                    . "or (wpRuvF8_posts.post_content like '%$search_str%')"
                    . "or (wpRuvF8_posts.post_name like '%$search_str%')";
            if($search_str_type != ''){
              $where  .= "or (p_formtas.meta_value like '%$search_str_type%'))";
            }else
            {
              $where  .= ")";
            }
          }
        }else {
          //pending resourses
          $where = " AND p_resources.meta_key like '%_resource_status%' and p_resources.meta_value = '".$is_pending_resources."'";
        }
    }else if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'feedback') {
        $seen         = $_GET['seen'];
        $current_user = get_current_user_id();
        if( $seen == 'seen' ) {
          $where .= " AND seen.meta_value = 1";
        }
        else if( $seen == 'unseen' ) {
          $where .= " AND ( seen.meta_value = 0 OR seen.post_id IS NULL ) ";
        }
    }
    return $where . $where_condition . " ";
  }

  add_filter('posts_where', 'segnalazioni_search_where');

  function segnalazioni_post_group($group) {

    global $pagenow, $wpdb;
    global $ef_admin_filter_taxs;
    if (is_admin() && $pagenow == 'edit.php' && $_GET['post_type'] == 'request_center') {
      $isFilter = 0;
      foreach ($ef_admin_filter_taxs as $tax) {
        if (!empty($_GET[$tax])) {
          $isFilter += 1;
        }
      }
      if ($isFilter > 0) {
        $group = $wpdb->posts . ".ID HAVING count({$wpdb->posts}.ID) = {$isFilter} ";
      }
    }
    return $group;
  }

  add_filter('posts_groupby', 'segnalazioni_post_group');
  
  add_filter( 'posts_request', 'segnalazioni_post_request' );
function segnalazioni_post_request($request)
{
  return $request;
}
}

function count_request_center($args = array()){
  global $wpdb;
  // Conditions
  if($args['type_id'] == -1){
    $join_condition = "";
    $join_condition_where = "";
  }else{
    $join_condition = returnRequestJoin('type');
    $join_condition_where = whereCondition('type', $args);
  }

  if($args['theme_id'] == -1){
    $join_condition_theme = "";
    $join_condition_theme_where = "";
  }else{
    $join_condition_theme = returnRequestJoin('theme');
    $join_condition_theme_where = whereCondition('theme', $args);
  }

  if($args['target_id'] == -1){
    $join_condition_target = "";
    $join_condition_target_where = "";
  }else{
    $join_condition_target = returnRequestJoin('target');
    $join_condition_target_where = whereCondition('target', $args);
  }
    
  $sql = "SELECT post.ID
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_theme
            $join_condition_target
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}')
            $join_condition_where
            $join_condition_theme_where
            $join_condition_target_where
            group by post.ID
            order by post.post_date DESC";
  $results = $wpdb->get_results($sql);
  return count($results);
}

function ef_count_request_center_ajax(){
    global $wpdb;
    $args = array(
      "post_status" => "publish",
      "post_type" => "request_center",
      "offset" => 0
    );

  //check theme
  if(isset($_POST['request_theme'])){
    if(!is_numeric($_POST['request_theme'])){
      $args['theme_id'] = -1;
    }else{
      $args['theme_id'] = $_POST['request_theme'];
    }
    set_query_var('ef_listing_request_theme_id', $args['theme_id']);
  }
    
  //check type
  if(isset($_POST['type'])){
    if(!is_numeric($_POST['type'])){
      $args['type_id'] = -1;
    }else{
      $args['type_id'] = $_POST['type'];
    }
    set_query_var('ef_listing_request_type_id', $args['type_id']);
  }
    
  //check target
  if(isset($_POST['request_target'])){
    if(!is_numeric($_POST['request_target'])){
        $args['target_id'] = -1;
    }else{
        $args['target_id'] = $_POST['request_target'];
    }
    set_query_var('ef_listing_request_target_id', $args['target_id']);
  }

  // Conditions
  if($args['type_id'] == -1){
    $join_condition = "";
    $join_condition_where = "";
  }else{
    $join_condition = returnRequestJoin('type');
    $join_condition_where = whereCondition('type', $args);
  }

  if($args['theme_id'] == -1){
    $join_condition_theme = "";
    $join_condition_theme_where = "";
  }else{
    $join_condition_theme = returnRequestJoin('theme');
    $join_condition_theme_where = whereCondition('theme', $args);
  }

  if($args['target_id'] == -1){
    $join_condition_target = "";
    $join_condition_target_where = "";
  }else{
    $join_condition_target = returnRequestJoin('target');
    $join_condition_target_where = whereCondition('target', $args);
  }
    
  $sql = "SELECT post.ID
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_theme
            $join_condition_target
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
            $join_condition_where
            $join_condition_theme_where
            $join_condition_target_where
            group by post.ID
            order by post.post_date DESC";
    $results = $wpdb->get_results($sql);
    echo count($results);
    die();
}
add_action('wp_ajax_ef_count_request_center_ajax', 'ef_count_request_center_ajax');
add_action('wp_ajax_nopriv_ef_count_request_center_ajax', 'ef_count_request_center_ajax');


function get_request_center($args = array()){
    global $wpdb;
    $no_of_posts = constant("ef_request_center_per_page");
    $args['offset'] = (get_query_var("ef_listing_requests_offset") ? get_query_var("ef_listing_requests_offset") : 0);
    
    //check type
    if(!is_numeric($args['type_id'])){
        $args['type_id'] = -1;
    }
    if($args['type_id'] == -1){
        $join_condition = "";
        $join_condition_where = "";
    }else{
        $join_condition = returnRequestJoin('type');
        $join_condition_where = whereCondition('type', $args);
    }
    
    //check theme
    if(!is_numeric($args['theme_id'])){
        $args['theme_id'] = -1;
    }
    if($args['theme_id'] == -1){
        $join_condition_theme = "";
        $join_condition_theme_where = "";
    }else{
        $join_condition_theme = returnRequestJoin('theme');
        $join_condition_theme_where = whereCondition('theme', $args);
    }

    //check target
    if(!is_numeric($args['target_id'])){
        $args['target_id'] = -1;
    }
    if($args['target_id'] == -1){
        $join_condition_target = "";
        $join_condition_target_where = "";
    }else{
        $join_condition_target = returnRequestJoin('target');
        $join_condition_target_where = whereCondition('target', $args);
    }

    $sql = "SELECT post.ID,post.post_author,post.post_date,post.post_content,post.post_title,post.guid
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_theme
            $join_condition_target
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}')
            $join_condition_where
            $join_condition_theme_where
            $join_condition_target_where
            group by post.ID
            order by post.post_date DESC
            limit {$args['offset']}, {$no_of_posts} ";
//    var_dump($sql);
    $results = $wpdb->get_results($sql);
    return $results;
}

// --- view more for listing request center --- //
function ef_load_more_listing_request_center() {
  set_query_var('ef_listing_requests_offset', $_POST['offset']);

  //check theme
  if(isset($_POST['request_theme'])){
    if(!is_numeric($_POST['request_theme'])){
        set_query_var('ef_listing_request_theme_id', -1);
    } else{
      set_query_var('ef_listing_request_theme_id', $_POST['request_theme']);
    }
  } else{
    set_query_var('ef_listing_request_theme_id', -1);
  }
  
  //check type
  if(isset($_POST['type'])){
    if(!is_numeric($_POST['type'])){
      set_query_var('ef_listing_request_type_id', -1);
    } else{
      set_query_var('ef_listing_request_type_id', $_POST['type']);
    }
  } else{
    set_query_var('ef_listing_request_type_id', -1);
  }
  
  //check target
  if(isset($_POST['request_target'])){
    if(!is_numeric($_POST['request_target'])){
      set_query_var('ef_listing_request_target_id', -1);
    }else{
      set_query_var('ef_listing_request_target_id', $_POST['request_target']);
    }
  }else{
    set_query_var('ef_listing_request_target_id', -1);
  }

  get_template_part('template-parts/content', 'listing_request_center');
  die();
}
add_action('wp_ajax_ef_load_more_listing_request_center', 'ef_load_more_listing_request_center');
add_action('wp_ajax_nopriv_ef_load_more_listing_request_center', 'ef_load_more_listing_request_center');


function ef_load_change_type_request_center() {
  set_query_var('ef_listing_requests_offset', 0);

  //check theme
  if(isset($_POST['request_theme'])){
    if(!is_numeric($_POST['request_theme'])){
        set_query_var('ef_listing_request_theme_id', -1);
    } else{
      set_query_var('ef_listing_request_theme_id', $_POST['request_theme']);
    }
  } else{
    set_query_var('ef_listing_request_theme_id', -1);
  }
  
  //check type
  if(isset($_POST['type'])){
    if(!is_numeric($_POST['type'])){
      set_query_var('ef_listing_request_type_id', -1);
    } else{
      set_query_var('ef_listing_request_type_id', $_POST['type']);
    }
  } else{
    set_query_var('ef_listing_request_type_id', -1);
  }
  
  //check target
  if(isset($_POST['request_target'])){
    if(!is_numeric($_POST['request_target'])){
      set_query_var('ef_listing_request_target_id', -1);
    }else{
      set_query_var('ef_listing_request_target_id', $_POST['request_target']);
    }
  }else{
    set_query_var('ef_listing_request_target_id', -1);
  }

  get_template_part('template-parts/content', 'listing_request_center');
  die();
}
add_action('wp_ajax_ef_load_change_type_request_center', 'ef_load_change_type_request_center');
add_action('wp_ajax_nopriv_ef_load_change_type_request_center', 'ef_load_change_type_request_center');


function ef_get_taxonomy_id_by_name($name, $taxonomy){
  global $wpdb;
  $query = $wpdb->prepare('SELECT '.$wpdb->terms.'.term_id FROM ' . $wpdb->terms . ' join '.$wpdb->term_taxonomy.' as tt on tt.term_id = '.$wpdb->terms.'.term_id WHERE (name = %s or name_ar = %s) AND tt.taxonomy = \'%s\'', $name, $name,$taxonomy);
  $wpdb->query( $query );
  if ( $wpdb->num_rows ) {
    $last_result = $wpdb->last_result;
    return $last_result[0]->term_id;
  }
  return -1;
}


function returnRequestJoin($item){
  global $wpdb;
  $join_condition = '';
  if($item == 'type'){
    $join_condition = " join {$wpdb->prefix}postmeta  as rel_type on post.ID = rel_type.post_id";
  }else if($item == 'theme'){
    $join_condition = " join {$wpdb->prefix}postmeta  as rel_theme on post.ID = rel_theme.post_id";
  }else if($item == 'target'){
    $join_condition = " join {$wpdb->prefix}postmeta  as rel_target on post.ID = rel_target.post_id";
  }
  return $join_condition;
}

function whereCondition($item,$args){
  global $wpdb;
  $where_condition = '';
  if($item == 'theme'){
    $where_condition = " and (rel_theme.meta_key = 'theme' and rel_theme.meta_value = ".$args['theme_id'].")";
  } else if($item == 'type'){
    $where_condition = " and (rel_type.meta_key = 'request_center_type' and rel_type.meta_value = ".$args['type_id'].")";
  } else if($item == 'target'){
    $where_condition = " and (rel_target.meta_key = 'target_bussiness_relationship' and rel_target.meta_value = ".$args['target_id'].")";
  }
  return $where_condition;
}
