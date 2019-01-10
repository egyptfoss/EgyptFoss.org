<?php
define("ef_products_per_page", 10);
set_query_var("displayed_user_id", bp_displayed_user_id());

function ef_listing_get_products_by_filter($args = array(),$queryString, $get_count = FALSE) {
 global $wpdb;
 $filter_condition = "";
 $having_condition = "";
 $join_condition = "";
 $order_by = "";

 $args['current_lang'] = (in_array($args['current_lang'], array('en', 'ar'))) ? $args['current_lang'] : 'en';
 
 $term_ids = get_query_var('term_ids');
 $no_of_posts = constant("ef_products_per_page");
 $join_condition = "  ";
 $order_by = " order by lang_ord asc,p.ID DESC "; 
 $featured_filter_condition = "";
 $featured_join_condition = "";
 $terms_to_add = array();
 
 if(!empty($term_ids) && ctype_digit(implode('', $term_ids))) {
  $term_count = count($term_ids);
   foreach( $term_ids as $term_id ) {
     $subterms = get_terms( 'industry', array( 'parent' => $term_id ) );
     
     foreach( $subterms as $subterm ) {
       $terms_to_add[] = $subterm->term_id;
     }
   }
    $term_ids = array_merge($term_ids, $terms_to_add);
    $term_ids = join(',', $term_ids);
    $filter_condition = " and rel.term_taxonomy_id in ({$term_ids}) ";
    $having_condition = " having count(*) = {$term_count} ";
 }
 
  if($args['browseProductsBy'] == "featured")
  {
    $featured_join_condition = " join wpRuvF8_postmeta as meta on p.ID = meta.post_id ";
    $featured_filter_condition = " and (meta.meta_key = 'is_featured') and (meta.meta_value = 1) ";
    $order_by = " order by p.post_title ";
  }
 
  $sqlCount = "select * from wpRuvF8_posts as p
        {$featured_join_condition}
        where p.ID in
        (SELECT ID FROM {$wpdb->prefix}posts as post 
        join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
        join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
        join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language' and 
        (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
        pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))
        {$filter_condition}
        group by post.ID 
        {$having_condition}   
         ) {$featured_filter_condition} ";

$productCount = $wpdb->get_results($sqlCount);

if( $get_count ) {
  return count($productCount);
}

set_query_var('ef_product_filtered_count', count($productCount));
  $sql = "SELECT p.ID as id, case when pmeta.meta_value like '%\"{$args['current_lang']}\"%' then '1' else '2' end as lang_ord
        FROM {$wpdb->prefix}posts as p {$featured_join_condition}
        join {$wpdb->prefix}postmeta as pmeta on p.ID = pmeta.post_id
        join {$wpdb->prefix}term_relationships as rel on p.ID = rel.object_id
        join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (p.post_status = '{$args['post_status']}' and p.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language' and 
        (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
        pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))
        {$filter_condition} {$featured_filter_condition}
        group by p.ID 
        {$having_condition} 
        {$order_by}";
        
        if( isset($args['offest']) ) {
          $sql .= " limit {$args['offest']},{$no_of_posts}";
        }
        
  $results = $wpdb->get_results($sql);
  $ids = array();
  foreach($results as $result)
  {
    $ids = array_merge($ids,[$result->id]);
  }
  $ids = join(",", array_map('intval',$ids));
  $sql = "select * from wpRuvF8_posts as p
        {$featured_join_condition}
        where p.ID in ({$ids})
         {$featured_filter_condition} order by FIND_IN_SET(p.ID,'{$ids}')";      
  $results = $wpdb->get_results($sql);
 
parse_str(substr($queryString, 1),$queryString);
unset($queryString['industry']);
$queryString = http_build_query($queryString);

if($queryString != ""){
  $queryString = "?".$queryString;
}

if(is_user_logged_in())
{
  if($queryString != "?all" && $queryString != "")
  {
    update_user_meta(get_current_user_id(), 'ef_product_preferences', $queryString);
  }else
  {
    if(isset($_POST["newUrl"]))
    {
      update_user_meta(get_current_user_id(), 'ef_product_preferences', "");
    }
  }
}

return $results;
}

function ef_load_more_filtered_products() {
  get_template_part('template-parts/content', 'product_filtered');
  die();
}
add_action('wp_ajax_ef_load_more_filtered_products', 'ef_load_more_filtered_products');
add_action('wp_ajax_nopriv_ef_load_more_filtered_products', 'ef_load_more_filtered_products');

function pluralize($singular, $ignoreArabic = false) {
  if($ignoreArabic && pll_current_language() == "ar")
  {
    return $singular; 
  }
  
    if( !strlen($singular)) return $singular;

    $last_letter = strtolower($singular[strlen($singular)-1]);
    switch($last_letter) {
        case 'y':
            return substr($singular,0,-1).'ies';
        case 's':
            if($singular == "keywords")
            {
              return $singular;
            }
            return $singular.'es';
        default:
            return $singular.'s';
    }
  }
  
function ef_update_product_frontEnd($idFromLink) {
  
  foreach (array("functionality", "prerequisites", "audience", "objectives", "description") as $parameter) {
    if (array_key_exists($parameter, $_POST)) {
      $_POST[$parameter] = strip_js_tags($_POST[$parameter]);
    }
  }
  
  $edit_post_id = $_POST['postid'];
  global $ef_registered_taxonomies;
  global $wpdb;
  $ef_product_messages = array("errors" => array());
  $history_data = array();
  $sql = "select ID from $wpdb->posts where  post_type='product' and ID = %s";
  $checkExistance = $wpdb->get_col($wpdb->prepare($sql, $edit_post_id));
  
  if ($idFromLink != $edit_post_id) {
    $ef_product_messages = array("errors"=>array(__("you are trying to cheat",'egyptfoss')));
    set_query_var("ef_product_messages", $ef_product_messages);
    return false;
  }
  
  if (!$checkExistance) {
    $ef_product_messages = array("errors"=>array(__("No product found","egyptfoss")));
    set_query_var("ef_product_messages", $ef_product_messages);
    return false;
  }
  
  $sql = "select ID from $wpdb->posts where post_title = %s and post_type='product' and ID not in (%s)";
  $isTitleExists = $wpdb->get_col($wpdb->prepare($sql, $_POST['product_title'], $edit_post_id));

  if (!empty($isTitleExists)) {
     $ef_product_messages["errors"] = array_merge($ef_product_messages["errors"],array(__("the title is already exists","egyptfoss")));
  }
  
  $required_fields = array("product_title","description");
  foreach($required_fields as $field)
  {
   
    if(!isset($_POST[$field]) || empty($_POST[$field]))
    {
      $fieldText =  str_replace('post_', '', $field);
      $fieldText =  str_replace('product_', '', $fieldText);
      $ef_product_messages["errors"] = array_merge($ef_product_messages["errors"],array(__("Product ".$fieldText." is required","egyptfoss")));
    }
  }
  
      //check if one of the uploads not an image
    $errorScreenshots = false;
    if($_FILES["files"])
    {
        foreach ($files['name'] as $key => $value) {
            if ($files['name'][$key]) {
                if($files['type'][$key] != 'image/png' && $files['type'][$key] != 'image/jpg'
                        && $files['type'][$key] != 'image/jpeg')
                    $errorScreenshots = true;
            }
        }
    }
    
    if($errorScreenshots)
    {
        $ef_product_messages["errors"] = array_merge($ef_product_messages["errors"],array(__("'Please enter valid product screenshots (jpeg,jpg,png).","egyptfoss")));
    }
    
  global $ef_product_multi_uncreated_tax;
  foreach ($ef_product_multi_uncreated_tax as $uncreated_tax) {
    if (isset($_POST['post_' . $uncreated_tax]) && !empty($_POST['post_' . $uncreated_tax])) {
      foreach ($_POST['post_' . $uncreated_tax] as $term) {
        if (!get_term_by('slug', $term, $uncreated_tax)) {
          $ef_product_messages["errors"] = array_merge($ef_product_messages["errors"], array(sprintf(__("Please select already exist %s", "egyptfoss"), __($uncreated_tax, "egyptfoss"))));
        }
      }
    }
  }
  
  if($ef_product_messages["errors"])
  {
    set_query_var("ef_product_messages", $ef_product_messages);
    return false;
  }
  
  $wpdb->update($wpdb->prefix.'posts',array( 'post_title' => $_POST["product_title"] ),array( 'ID' => $edit_post_id ));
    
    $history_data = array_merge($history_data,array(
      "post_title" => $_POST["product_title"],
      "post_id" => $edit_post_id,
      "user_id" => get_current_user_id(),
      "updated_at" => date('YmdHis')
      ));
    
    $taxonomies = array('type','technology','platform','license','keywords','interest');
    foreach ($taxonomies as $tax) {
      $term_ids = wp_set_object_terms($edit_post_id, $_POST['post_' . $tax], $tax);  // insert tax in db
      update_post_meta($edit_post_id, $tax, getTermFromTermTaxonomy($term_ids));
      if($tax == "interest")
      {
        $history_data = array_merge($history_data,array("keywords_text" => $_POST['post_' . $tax],"keywords_ids" => $term_ids));
      }  else {
         $history_data = array_merge($history_data,array($tax."_text" => $_POST['post_' . $tax],$tax."_ids" => $term_ids)); 
      }
    }
    $history_data = array_merge($history_data,array("industry_text" => NULL,"industry_ids" => NULL)); 
    
    $product_meta = array("functionality", "developer", "usage_hints", "references", "link_to_source", "description");
    foreach ($product_meta as $meta) {
      update_post_meta($edit_post_id, $meta, $_POST[$meta]);
      $history_data = array_merge($history_data,array($meta => $_POST[$meta]));
    }
    
    $changed = handling_product_logo_and_screenshots($edit_post_id);
    set_query_var("ef_product_messages", array("success"=> array(__("Product updated successfully","egyptfoss"))));
    addingProductHistory($history_data,$changed);
    
    //go to view page of product
    $host_name = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."options where option_name = 'siteurl'");
    $product_postname = $wpdb->get_row("SELECT post_name FROM ". $wpdb->prefix."posts where ID = $edit_post_id");
    $url = $host_name->option_value."/".pll_current_language()."/products/".$product_postname->post_name;
    
    //set redirect msg
    setMessageBySession("edit-product","success",__("Product updated successfully","egyptfoss"));
    wp_redirect($url);
}

function handling_product_logo_and_screenshots($product_id) {
  $changed = false;
  require_once( ABSPATH . 'wp-admin/includes/image.php' );
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  require_once( ABSPATH . 'wp-admin/includes/media.php' );
  
  if (!empty($_FILES["product_logo"]["name"]) && isset($_POST['product_logo_nonce'], $product_id) && wp_verify_nonce($_POST['product_logo_nonce'], 'product_logo')) {
    $attachment_id = media_handle_upload('product_logo', $product_id);
    if( !is_wp_error( $attachment_id ) ) {
      update_post_meta($product_id, '_thumbnail_id', $attachment_id);
      $changed = true;
    }
  }
    
    //check removed files and update meta
    $original_ids = get_post_meta($product_id, 'fg_perm_metadata', true);
    $oldScreenShotsIds = array_map('intval', explode(',', $original_ids));
    if(!empty($_POST['screens_ids']) && $_POST['screens_ids'] != "")
    {
        $images_ids = explode(',',rtrim($_POST['screens_ids'], ","));
        for($i = 0; $i < sizeof($images_ids); $i++)
        {
           $original_ids = str_replace("$images_ids[$i],", "", $original_ids);
           $original_ids = str_replace(",$images_ids[$i]", "", $original_ids);
           $original_ids = str_replace("$images_ids[$i]", "", $original_ids);
        }
        if($original_ids == '')
            delete_post_meta ($product_id, 'fg_perm_metadata');
        else
            update_post_meta($product_id, 'fg_perm_metadata', $original_ids);
    }
  
    //update origianl to concatenate into
    if($original_ids != '')
        $original_ids = explode(',',$original_ids);
    else {
        $original_ids = array();
    }

  if ($_FILES["product_screenshots"] && isset($_POST['product_screenshots_nonce'], $product_id) && wp_verify_nonce($_POST['product_screenshots_nonce'], 'product_screenshots')) {
    $files = $_FILES["product_screenshots"];  // files is the input name
    $images_ids = $original_ids;
    foreach ($files['name'] as $key => $value) {
      if (($files['name'][$key] != "")) {
        $file = array(
          'name' => $files['name'][$key],
          'type' => $files['type'][$key],
          'tmp_name' => $files['tmp_name'][$key],
          'error' => $files['error'][$key],
          'size' => $files['size'][$key]
        );
        $_FILES = array("files" => $file);
        foreach ($_FILES as $file => $array) {
          $newupload = media_handle_upload($file, $product_id);
          $images_ids = array_merge($images_ids, array($newupload));
        }
      }
      // we must handle maximum size upload for screenshots .... //
    }
   
    $diff = array_intersect($oldScreenShotsIds, $images_ids);
    $newIds = $images_ids;

    if(!empty($images_ids))
    {
      $images_ids = implode(',', $images_ids);
      update_post_meta($product_id, 'fg_perm_metadata', $images_ids);
    }
    
    if(count($oldScreenShotsIds) == 1 && $oldScreenShotsIds[0] == 0)
    {
      $oldScreenShotsIds = array();
    }
    if( count($diff) != count($oldScreenShotsIds) || count($diff) != count($newIds))
    {
        $changed = true;
    }
  }
  return $changed;
}

function addingProductHistory($history_data,$changed = false)
{
  if($_POST["form_changed"] == "true" || $changed)
  {
    global $wpdb;
    foreach ($history_data as $key=>$column)
    {
      if(is_array($history_data[$key]))
      {
        $history_data[$key]=  serialize($column);
      }
    }
    ($wpdb->insert($wpdb->prefix.'posts_history',$history_data));
  }
}

// ---- Added & Contributed Products section ---- //
function count_added_products_by_user($args = array(), $xprofile_id){
  global $wpdb;

  if($args['post_status'] == "")
  {
     $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type = '{$args['post_type']}') And post.post_author = {$xprofile_id}  ";
  }else
  {
     $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$xprofile_id}  ";
  }
  
  $sql = "SELECT * FROM {$wpdb->prefix}posts as post 
        {$whereCondition}
        order by post.post_date DESC"; 
  $result = $wpdb->get_results($sql);
  return $result;
}

// --- handling user products --- //
function display_products_by_user($args = array(), $xprofile_id) {
  global $wpdb;
  $no_of_posts = 20 ;
  $args['offset'] = (get_query_var("ef_added_product_offest") ? get_query_var("ef_added_product_offest") : 0);

  if($args['post_status'] == "")
  {
     $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type = '{$args['post_type']}') And post.post_author = {$xprofile_id}  ";
  }else
  {
     $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$xprofile_id}  ";
  }
  
  /*$sql = "SELECT * FROM {$wpdb->prefix}posts as post 
        join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
        join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
        join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language')
        and post_author = '{$xprofile_id}'
        group by post.ID
        {$having_condition} 
        order by case when pmeta.meta_value like '%\"{$args['current_lang']}\"%' then 1 else 2 end,post.post_date DESC   
        limit {$args['offset']},{$no_of_posts} ";*/
  $sql = "SELECT * FROM {$wpdb->prefix}posts as post 
        {$whereCondition}
        order by post.post_date DESC   
        limit {$args['offset']},{$no_of_posts} ";  
  $results = $wpdb->get_results($sql);
  return $results;
}

// --- handling contributed products by user --- //
function display_contributed_products_by_user($args = array(), $xprofile_id) {
  global $wpdb;
  $sql = "SELECT DISTINCT ph.post_id, ph.user_id, p.post_title, p.post_modified, p.post_author
        FROM `{$wpdb->prefix}posts_history` AS ph JOIN `{$wpdb->prefix}posts` AS p ON p.ID = ph.`post_id`
        where ph.user_id = '{$xprofile_id}' AND p.post_author != '{$xprofile_id}'"
        . " and p.post_type='product' and p.post_status ='publish'";  
  $results = $wpdb->get_results($sql);
  return $results;
}

function display_contributed_products_by_user_results($args = array(), $xprofile_id) {
  global $wpdb;
  $no_of_posts = 20 ;
  $args['offset'] = (get_query_var("ef_added_product_offest") ? get_query_var("ef_added_product_offest") : 0);

  $sql = "SELECT DISTINCT ph.post_id, ph.user_id, p.post_title, p.post_modified, p.post_author
        FROM `{$wpdb->prefix}posts_history` AS ph JOIN `{$wpdb->prefix}posts` AS p ON p.ID = ph.`post_id`
        where ph.user_id = '{$xprofile_id}' AND p.post_author != '{$xprofile_id}'"
        . " and p.post_type='product' and p.post_status ='publish'
        limit {$args['offset']}, {$no_of_posts} ";
        
  $results = $wpdb->get_results($sql);
  return $results;
}

// --- view more for added products --- //
function ef_load_more_added_products_by_user() {
  set_query_var('ef_added_product_offest', $_POST['offest']);
  get_template_part('template-parts/content', 'added_products_by_user');
  die();
}
add_action('wp_ajax_ef_load_more_added_products_by_user', 'ef_load_more_added_products_by_user');
add_action('wp_ajax_nopriv_ef_load_more_added_products_by_user', 'ef_load_more_added_products_by_user');
// --- end added products --- //

// --- view more for contributed products by user --- //
function ef_load_more_contributed_products_by_user() {
  set_query_var('ef_added_product_offest', $_POST['offest']);  // ef_added_product_offest will be changed after creating the new query
  get_template_part('template-parts/content', 'contributed_products');
  die();
}
add_action('wp_ajax_ef_load_more_contributed_products_by_user', 'ef_load_more_contributed_products_by_user');
add_action('wp_ajax_nopriv_ef_load_more_contributed_products_by_user', 'ef_load_more_contributed_products_by_user');
// --- end contributed products --- //

function getTermIdsForTopTenProducts($number, $term_tax)
{
  global $wpdb;
  global $ef_registered_taxonomies;
  if(in_array($term_tax, $ef_registered_taxonomies) && is_numeric($number))
  {
    $where = "";
    $offset = 0; 
    $order_by = "RAND()";
    if(isset($_POST['random_term_ids']) && is_numeric_array(split(',', $_POST['random_term_ids'])) && is_numeric($_POST['offset']))
    { 
       $where = "and t.term_id not in ({$_POST['random_term_ids']})";
       $offset = $_POST['offset'];
       $order_by = " t.term_id DESC";
    }

    $sql = "SELECT t.* FROM {$wpdb->prefix}term_taxonomy as tax
            join {$wpdb->prefix}terms as t on t.term_id = tax.term_id 
            join {$wpdb->prefix}top_ten_products as topTen on t.term_id = topTen.term_id  
            where (tax.taxonomy = '{$term_tax}') {$where}
            group by term_id  
            order by {$order_by}
            limit {$offset},{$number}";
  } else {
    $sql = "";
  }
  $results = $wpdb->get_results($sql);
  return $results;
}

function countTopTenTerms($term_tax)
{
  global $wpdb;
  global $ef_registered_taxonomies;
  if(in_array($term_tax, $ef_registered_taxonomies))
  {
    $sql = "SELECT t.* FROM {$wpdb->prefix}term_taxonomy as tax
            join {$wpdb->prefix}terms as t on t.term_id = tax.term_id 
            join {$wpdb->prefix}top_ten_products as topTen on t.term_id = topTen.term_id  
            where (tax.taxonomy = '{$term_tax}')
            group by term_id ";
  } else {
    $sql = "";
  }
  $results = $wpdb->get_results($sql);
  return count($results);
}

function getTopTenProducts($term_id)
{
  $args = array(
    "post_status" => "publish",
    "post_type" => "product",
  );
  global $wpdb;
  if(!is_numeric($term_id))
  {
    return false;
  }
  $sql = "SELECT *,topTen.term_id as isTopTen FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}top_ten_products as topTen on post.ID = topTen.post_id
          where topTen.term_id = {$term_id}
          group by post.ID
          order by post.post_date DESC";
  $results = $wpdb->get_results($sql);        
  return $results;
}

function isTopTenProduct($post_id)
{
  global $wpdb;
  $sql = "";
  if(pll_current_language() == "ar")
  {
    $post = pll_get_post_translations($post_id);
    $post_id = (isset($post["en"]))?$post["en"]:$post_id;
  }
  if(is_numeric($post_id))
  {
    $sql = "SELECT post_id FROM {$wpdb->prefix}top_ten_products where post_id = {$post_id} limit 1";
  }
  $results = $wpdb->get_results($sql);   
  if(count($results) > 0)
  {
    return true;
  }
  return false;
}

function ef_load_more_top_ten_products() {
  get_template_part('template-parts/content', 'products_top_ten');
  die();
}
add_action('wp_ajax_ef_load_more_top_ten_products', 'ef_load_more_top_ten_products');
add_action('wp_ajax_nopriv_ef_load_more_top_ten_products', 'ef_load_more_top_ten_products');

function is_numeric_array($array)
{
  foreach ($array as $value)
  {
    if(!is_numeric($value))
    {
      return false;
    }
  }
  return true;
}
