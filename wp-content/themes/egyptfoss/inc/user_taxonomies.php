<?php

function check_user_relation($user_id, $term_taxonomy_id) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$result = $wpdb->get_results("SELECT user_id, term_taxonomy_id FROM ".$prefix."user_relationships WHERE user_id = $user_id AND term_taxonomy_id = $term_taxonomy_id");
	return $result;
}

function add_user_relation($user_id, $term_taxonomy_id) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$wpdb->insert($prefix."user_relationships", 
		array('user_id' => $user_id, 'term_taxonomy_id' => $term_taxonomy_id)
	);
}

function remove_user_relations($user_id, $term_taxonomy_ids) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$wpdb->query("DELETE FROM ".$prefix."user_relationships WHERE user_id = $user_id AND term_taxonomy_id NOT IN ('".join("','", $term_taxonomy_ids)."')");
}

function get_user_taxonomies($user_id, $taxonomy) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$taxonomies = array();
	$result = $wpdb->get_results("SELECT ts.name FROM ".$prefix."user_relationships ur JOIN ".$prefix."term_taxonomy tt on ur.term_taxonomy_id = tt.term_taxonomy_id JOIN ".$prefix."terms ts on tt.term_id = ts.term_id WHERE `user_id` = $user_id AND tt.taxonomy = '".$taxonomy."'");
	foreach ($result as $key => $fields) {
		array_push($taxonomies, $fields->name);
	}
	return $taxonomies;
}