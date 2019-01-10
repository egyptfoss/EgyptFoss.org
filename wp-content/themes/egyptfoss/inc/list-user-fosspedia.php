<?php
define("ef_user_fosspedia_per_page", 20);
add_action('bp_setup_nav', 'ef_add_fosspedia_tab', 305);
function ef_add_fosspedia_tab() {
    global $bp;
    bp_core_new_subnav_item( array(
            'name' => __('FOSSPedia', 'egyptfoss'),
            'slug' => 'wiki',
            'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
            'parent_slug' => $bp->bp_nav['contributions']['slug'],
            'position' => 10,
            'screen_function' => 'listing_user_fosspedia' //the function is declared below
        )
    );
}

function listing_user_fosspedia(){
    add_action( 'bp_template_content', 'listing_user_fosspedia_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_fosspedia_content() {
    bp_get_template_part( 'members/single/fosspedia' );
}

function ef_load_more_user_fosspedia() {
  set_query_var('user_fosspedia_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_fosspedia');
  die();
}
add_action('wp_ajax_ef_load_more_user_fosspedia', 'ef_load_more_user_fosspedia');
add_action('wp_ajax_nopriv_ef_load_more_user_fosspedia', 'ef_load_more_user_fosspedia');

function get_user_fosspedia($args)
{
    $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
    $prefixes = array('en_','ar_');
    $sqlQuery = "";
    foreach($prefixes as $prefix)
    {
        $page_lang = $prefix;
        $page_lang = str_replace("_", "", $page_lang);
        $wpdb_pedia->prefix = $prefix;

        //select foss pedia user_id by user_email
         $userData = get_user_by('ID', $args['author']);
        $user_login = ucfirst(str_replace("_", " ", $userData->user_login));
        $user_query = "select user_id from {$wpdb_pedia->prefix}user where user_name = '".$user_login."' ";
        $user_id =  $wpdb_pedia->get_col($user_query);
        if(sizeof($user_id) > 0)
        {
            $user_id = $user_id[0];
        }else {
            $user_id = -1;
        }
        if($user_id != -1)
        {
            if($sqlQuery != "")
            {
                $sqlQuery .= "union ";
            }
            $sqlQuery .= "(select {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title
                ,concat('/$page_lang/wiki/',{$wpdb_pedia->prefix}page.page_title) as page_url,{$wpdb_pedia->prefix}revision.rev_id
                    ,CONVERT({$wpdb_pedia->prefix}revision.rev_timestamp USING utf8) as post_date
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}revision  
                on {$wpdb_pedia->prefix}page.page_id = {$wpdb_pedia->prefix}revision.rev_page
                where {$wpdb_pedia->prefix}page.page_namespace = 0"
                . " and {$wpdb_pedia->prefix}revision.rev_user = {$user_id} and {$wpdb_pedia->prefix}revision.rev_parent_id = 0"
                . " group by {$wpdb_pedia->prefix}page.page_id) ";
        }
    }
    $sqlQuery = preg_replace('/union $/', '', $sqlQuery);
    $sqlQuery .= " order by post_date desc"
                . " limit {$args['offset']},{$args['no_of_posts']}";
                
    $results =  $wpdb_pedia->get_results($sqlQuery);
    return $results;
}

function get_user_fosspedia_count($args)
{
    $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
    $prefixes = array('en_','ar_');
    $total_count = 0;
    $sqlQuery = "";
    foreach($prefixes as $prefix)
    {
        $page_lang = $prefix;
        $page_lang = str_replace("_", "", $page_lang);
        $wpdb_pedia->prefix = $prefix;
        
        //select foss pedia user_id by user_email
        $userData = get_user_by('ID', $args['author']);
        $user_login = ucfirst(str_replace("_", " ", $userData->user_login));
        $user_query = "select user_id from {$wpdb_pedia->prefix}user where user_name = '".$user_login."' ";
        $user_id =  $wpdb_pedia->get_col($user_query);
        if(sizeof($user_id) > 0)
        {
            $user_id = $user_id[0];
        }else {
            $user_id = -1;
        }
        if($user_id != -1)
        {
            if($sqlQuery != "")
            {
                $sqlQuery .= "union ";
            }
            $sqlQuery .= "(select {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title
                ,concat('/$page_lang/wiki/',{$wpdb_pedia->prefix}page.page_title) as page_url,{$wpdb_pedia->prefix}revision.rev_id
                    ,CONVERT({$wpdb_pedia->prefix}revision.rev_timestamp USING utf8) as post_date
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}revision  
                on {$wpdb_pedia->prefix}page.page_id = {$wpdb_pedia->prefix}revision.rev_page
                where {$wpdb_pedia->prefix}page.page_namespace = 0"
                . " and {$wpdb_pedia->prefix}revision.rev_user = {$user_id} and {$wpdb_pedia->prefix}revision.rev_parent_id = 0"
                . " group by {$wpdb_pedia->prefix}page.page_id) ";
        }
    }
    $sqlQuery = preg_replace('/union $/', '', $sqlQuery);
    $results =  $wpdb_pedia->get_results($sqlQuery);
    $total_count += count($results);    
    return $total_count;    
}

function ef_load_more_user_fosspedia_edits() {
  set_query_var('user_fosspedia_edits_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_fosspedia_edits');
  die();
}
add_action('wp_ajax_ef_load_more_user_fosspedia_edits', 'ef_load_more_user_fosspedia_edits');
add_action('wp_ajax_nopriv_ef_load_more_user_fosspedia_edits', 'ef_load_more_user_fosspedia_edits');

function get_user_fosspedia_edits($args)
{
    $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
    $prefixes = array('en_','ar_');
    $sqlQuery = "";
    foreach($prefixes as $prefix)
    {
        $page_lang = $prefix;
        $page_lang = str_replace("_", "", $page_lang);
        $wpdb_pedia->prefix = $prefix;
        
        //select foss pedia user_id by user_email
        $userData = get_user_by('ID', $args['author']);
        $user_login = ucfirst(str_replace("_", " ", $userData->user_login));
        $user_query = "select user_id from {$wpdb_pedia->prefix}user where user_name = '".$user_login."' ";
        $user_id =  $wpdb_pedia->get_col($user_query);
        if(sizeof($user_id) > 0)
        {
            $user_id = $user_id[0];
        }else {
            $user_id = -1;
        }
        if($user_id != -1)
        {
            if($sqlQuery != "")
            {
                $sqlQuery .= "union ";
            }
            $sqlQuery .= "(select {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title
                ,concat('/$page_lang/wiki/',{$wpdb_pedia->prefix}page.page_title) as page_url,{$wpdb_pedia->prefix}revision.rev_id
                    ,CONVERT({$wpdb_pedia->prefix}revision.rev_timestamp USING utf8) as post_date
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}revision  
                on {$wpdb_pedia->prefix}page.page_id = {$wpdb_pedia->prefix}revision.rev_page
                left join {$wpdb_pedia->prefix}revision as en_revision_parent
                on {$wpdb_pedia->prefix}page.page_id = en_revision_parent.rev_page and en_revision_parent.rev_parent_id = 0
                where {$wpdb_pedia->prefix}page.page_namespace = 0"
                . " and {$wpdb_pedia->prefix}revision.rev_user = {$user_id} and {$wpdb_pedia->prefix}revision.rev_parent_id <> 0"
                . " and (en_revision_parent.rev_user <> {$user_id} )"
                . " group by {$wpdb_pedia->prefix}page.page_id) ";
        }
    }
    $sqlQuery = preg_replace('/union $/', '', $sqlQuery);
    $sqlQuery .= " order by post_date desc"
                . " limit {$args['offset']},{$args['no_of_posts']}";
    $results =  $wpdb_pedia->get_results($sqlQuery);
    return $results;
    
}

function get_user_fosspedia_edits_count($args)
{
    $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
    $prefixes = array('en_','ar_');
    $total_count = 0;
    $sqlQuery = "";
    foreach($prefixes as $prefix)
    {
        $page_lang = $prefix;
        $page_lang = str_replace("_", "", $page_lang);
        $wpdb_pedia->prefix = $prefix;
        
        //select foss pedia user_id by user_email
        $userData = get_user_by('ID', $args['author']);
        $user_login = ucfirst(str_replace("_", " ", $userData->user_login));
        $user_query = "select user_id from {$wpdb_pedia->prefix}user where user_name = '".$user_login."' ";
        $user_id =  $wpdb_pedia->get_col($user_query);
        if(sizeof($user_id) > 0)
        {
            $user_id = $user_id[0];
        }else {
            $user_id = -1;
        }
        if($user_id != -1)
        {
            if($sqlQuery != "")
            {
                $sqlQuery .= "union ";
            }
            $sqlQuery .= "(select {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title
                ,concat('/$page_lang/wiki/',{$wpdb_pedia->prefix}page.page_title) as page_url,{$wpdb_pedia->prefix}revision.rev_id
                    ,CONVERT({$wpdb_pedia->prefix}revision.rev_timestamp USING utf8) as post_date
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}revision  
                on {$wpdb_pedia->prefix}page.page_id = {$wpdb_pedia->prefix}revision.rev_page
                left join {$wpdb_pedia->prefix}revision as en_revision_parent
                on {$wpdb_pedia->prefix}page.page_id = en_revision_parent.rev_page and en_revision_parent.rev_parent_id = 0
                where {$wpdb_pedia->prefix}page.page_namespace = 0"
                . " and {$wpdb_pedia->prefix}revision.rev_user = {$user_id} and {$wpdb_pedia->prefix}revision.rev_parent_id <> 0"
                . " and (en_revision_parent.rev_user <> {$user_id} )"
                . " group by {$wpdb_pedia->prefix}page.page_id) ";
        }
    }
    $sqlQuery = preg_replace('/union $/', '', $sqlQuery);
    $results =  $wpdb_pedia->get_results($sqlQuery);
    $total_count += count($results); 
    return $total_count;    
}
