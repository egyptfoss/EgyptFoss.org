<?php

/**
 * Template Name: Semantic Import
 *
 * @package egyptfoss
 */

echo "retrieving posts <br/>";
ini_set('max_execution_time', 6000); //3000 seconds = 100 minutes
$sql = "select distinct posts.ID,posts.post_title, posts.post_content, posts.post_type, posts.post_status
                from wpRuvF8_posts as posts
                where posts.post_type in ('news','product','tribe_events','success_story','open_dataset','request_center', 'expert_thought', 'service')
                and posts.post_status = 'publish';";
 
 $data = $wpdb->get_results($sql);
 echo "retrieved ".sizeof($data)." posts <br/>";
 echo "----<br/>";
 for($i = 0; $i < sizeof($data); $i++)
 {
     echo "posting Post with ID = ". $data[$i]->ID. "<br/>";
     post_enhancments_to_marmotta($data[$i]->ID, $data[$i]);
     echo "---- <br/>";
 }
 
 echo "retrieving wiki posts (English) <br/>";
 $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
 $wpdb_pedia->prefix = 'en_';
 $sql = "select distinct {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title, {$wpdb_pedia->prefix}text.old_text as meta_value
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}text 
                on {$wpdb_pedia->prefix}page.page_latest = {$wpdb_pedia->prefix}text.old_id";
 $data = $wpdb_pedia->get_results($sql);
 echo "retrieved ".sizeof($data)." en wiki  <br/>";
 for($i = 0; $i < sizeof($data); $i++)
 {
    echo "posting Wiki Page with ID = ". $data[$i]->page_id. "<br/>";
    saveOldWikiInSemantic($data[$i]->page_id, $data[$i]->post_title, $data[$i]->meta_value,'pedia_en');
    echo "---- <br/>";
 }
 
 echo "retrieving wiki posts (Arabic) <br/>";
 $wpdb_pedia->prefix = 'ar_';
 $sql = "select distinct {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title, {$wpdb_pedia->prefix}text.old_text as meta_value
                from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}text 
                on {$wpdb_pedia->prefix}page.page_latest = {$wpdb_pedia->prefix}text.old_id";
 
 $data = $wpdb_pedia->get_results($sql);
 echo "retrieved ".sizeof($data)." ar wiki  <br/>";
 for($i = 0; $i < sizeof($data); $i++)
 {
    echo "posting Wiki Page with ID = ". $data[$i]->page_id. "<br/>";
    saveOldWikiInSemantic($data[$i]->page_id, $data[$i]->post_title, $data[$i]->meta_value,'pedia_ar');
    echo "---- <br/>";
 }
 
echo "retrieving users <br/>";
$sql = "select distinct users.ID,users.display_name
                from wpRuvF8_users as users
                where users.user_status = 0;";
 
 $data = $wpdb->get_results($sql);
 echo "retrieved ".sizeof($data)." users <br/>";
 echo "----<br/>";
 for($i = 0; $i < sizeof($data); $i++)
 {
    $get_user_meta = get_user_meta($data[$i]->ID, "registration_data", true);
    $user_meta = unserialize($get_user_meta);
    echo "posting User with ID = ". $data[$i]->ID. "<br/>";
    saveUserContent($data[$i]->ID, $data[$i]->display_name, $user_meta['functionality'], $user_meta['type']);
    echo "---- <br/>";
 }