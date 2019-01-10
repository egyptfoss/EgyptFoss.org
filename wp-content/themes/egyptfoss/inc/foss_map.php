<?php

function validate_location_data($data) {
	$message = '';
	if ( empty($data['lat']) || empty($data['lng']) ) {
		$message = __('Please enter all the required fields','egyptfoss');
	} else if ( !is_numeric($data['lat']) || !is_numeric($data['lng']) ) {
		$message = __('Wrong location data','egyptfoss');
	}
return $message;
}

function get_object_location_data($object_id, $object_type) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$location = $wpdb->get_results("SELECT * FROM ".$prefix."geolocations WHERE object_id = ".$object_id." AND object_type = '".$object_type."'", ARRAY_A);
	if(count($location) > 0) {
		$location = $location[0];
	}	else {
		$location = array();
	}	
	return $location;
}

function add_or_update_user_location($location_data) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$location = get_object_location_data($location_data['object_id'], $location_data['object_type']);
	if(!empty($location)){
		$wpdb->update($prefix."geolocations", array('lat'=>$location_data['lat'], 'lng'=>$location_data['lng']), array('id'=>$location['id']));
	} else {
		$wpdb->insert($prefix."geolocations", $location_data);
	}
}

function get_event_venue($event_id) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$event_venue = $wpdb->get_results("SELECT * FROM ".$prefix."events_venues WHERE event_id = ".$event_id, ARRAY_A);
	if(count($event_venue) > 0) {
		$event_venue = $event_venue[0];
	}	else {
		$event_venue = array();
	}	
	return $event_venue;
}

function add_or_update_event_venue($event_venue) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$record = get_event_venue($event_venue['event_id']);
	if(!empty($record)){
		$wpdb->update($prefix."events_venues", $event_venue, array('id'=>$record['id']));
	} else {
		$wpdb->insert($prefix."events_venues", $event_venue);
	}
}

function rename_event_venue($venue_id, $venue) {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$wpdb->update($prefix."events_venues", $venue, array('venue_id'=>$venue_id));
}

function ef_load_locations() {
	global $wpdb;
	global $locations_types;
	global $events_types;
	global $account_sub_types, $ar_sub_types, $en_sub_types;
	$locations = array();
	$term_ids = array();
	$filter_condition = "";
	$having_condition = "";
	$parameters = ['type', 'sub_type', 'theme', 'technology', 'interest', 'badge'];
	$taxonomies = ['theme', 'technology', 'interest'];
	foreach($parameters as $parameter){
		$args[$parameter] = (array_key_exists($parameter, $_POST)) ? $_POST[$parameter] : '';
		if (in_array($parameter, $taxonomies) && is_numeric($args[$parameter])) {
			array_push($term_ids, $args[$parameter]);
		}
	}
	$args['type'] = (empty($args['type'])) ? 'Entity' : $args['type'];
	if (!in_array($args['type'], $locations_types)) {
		$locations = array();
	} else if($args['type'] == 'Event') {
		$sql = "SELECT e.ID, e.post_title, e.guid, e.post_content, ev.venue_id, ev.venue_name, ev.event_start_datetime, ev.event_end_datetime, g.lat, g.lng 
						FROM {$wpdb->prefix}posts e ";
		if(!empty($term_ids) && ctype_digit(implode('', $term_ids))) {
			$sql .= " LEFT JOIN {$wpdb->prefix}term_relationships as tr on e.ID = tr.object_id
							 LEFT JOIN {$wpdb->prefix}term_taxonomy as tt on tr.term_taxonomy_id = tt.term_taxonomy_id";
			$term_count = count($term_ids);
			$term_ids = join(',', $term_ids);
			$filter_condition = " AND tr.term_taxonomy_id in ({$term_ids}) ";
			$having_condition = " HAVING COUNT(*) = {$term_count} ";
		}
		if(!empty($args['sub_type']) && array_key_exists($args['sub_type'], $events_types)) {
			$sql .= " JOIN {$wpdb->prefix}postmeta m ON m.post_id = e.ID AND m.meta_key = 'event_type' AND m.meta_value = '".$args['sub_type']."' ";
		}
		$sql .= " LEFT JOIN {$wpdb->prefix}events_venues ev ON ev.event_id = e.ID
							LEFT JOIN {$wpdb->prefix}geolocations g ON g.object_id = ev.venue_id AND g.object_type = 'venue'
							WHERE e.post_type='tribe_events'
							{$filter_condition}
							AND 
              (
              ev.event_start_datetime >= DATE(NOW()) 
              OR
							ev.event_end_datetime >= DATE(NOW()) 
              )
							AND e.post_status = 'publish' 
							GROUP BY e.ID 
							{$having_condition} ";

		$results = $wpdb->get_results($sql);
		foreach ($results as $result) {
			$index = $result->venue_id;
			$event_start = date('Y-m-d H:i',strtotime($result->event_start_datetime));
			$event_end = date('Y-m-d H:i',strtotime($result->event_end_datetime));
			$events = '<div class="item">'.
					'<h4><a href="'.$result->guid.'">'.$result->post_title.'</a></h4>'.
					'<div class="event-date"><strong>'. __("Start","egyptfoss") .':</strong>'.$event_start.'<strong>'. __("End","egyptfoss") .':</strong>'.$event_end.'</div>'.
				'</div>';
			if (array_key_exists($index, $locations)) {
				$locations[$index]['events'] .= $events;
			} else {
				$locations[$index] = array(
				'title' => $result->venue_name,
				'lat' => ($result->lat),
				'lng' => ($result->lng),
				'events' => $events
				);
			}
		}
	} else {
		$account_type = '';
		if ($args['type'] == 'Individual') {
			$account_type = '%:"Individual";%';
		} else if ($args['type'] == 'Entity') {
			$account_type = '%:"Entity";%';
		}
		if(!empty($term_ids) && ctype_digit(implode('', $term_ids))) {
			$term_count = count($term_ids);
			$term_ids = join(',', $term_ids);
			$filter_condition = " AND ur.term_taxonomy_id in ({$term_ids}) ";
			$having_condition = " HAVING COUNT(*) = {$term_count} ";
		}
		$sql = "SELECT u.ID, u.user_nicename, g.lat, g.lng, m.meta_value AS regist FROM {$wpdb->prefix}users u
						LEFT JOIN {$wpdb->prefix}usermeta m ON u.ID = m.user_id AND m.meta_key = 'registration_data' 
						LEFT JOIN {$wpdb->prefix}user_relationships as ur on u.ID = ur.user_id
						LEFT JOIN {$wpdb->prefix}term_taxonomy as tt on ur.term_taxonomy_id = tt.term_taxonomy_id
						LEFT JOIN {$wpdb->prefix}efb_badges_users AS bu ON bu.user_id = u.ID
						JOIN {$wpdb->prefix}geolocations g ON g.object_id = u.ID AND g.object_type = 'user'
						WHERE m.meta_value like '".$account_type."' ";
            
            if( !empty( $args['badge'] ) ) {
              $sql .= " AND bu.badge_id = {$args['badge']} ";
            }
		if (array_key_exists($args['sub_type'], $account_sub_types)) {
			$sql .= " AND m.meta_value like '%".$args['sub_type']."%' ";
		}
		$sql .= "{$filter_condition}
						GROUP BY u.ID 
						{$having_condition} ";
		$results = $wpdb->get_results($sql);
		foreach ($results as $result) {
		  $index = $result->ID;
      $registration_data = unserialize( unserialize( $result->regist ) );
      $sub_types = ( get_locale() == 'ar') ? $ar_sub_types : $en_sub_types;
      $desc = '';
      if (!empty($registration_data['type'])) {
        if ($registration_data['type'] == "Entity") {
          $desc = '<span class="account-type-icon entity"><i class="fa fa-building" style="font-size: 10px;"></i></span> ';
        }else {
          $desc = '<span class="account-type-icon person"><i class="fa fa-user" style="font-size: 10px;"></i></span> ';
        }
      }
      if (!empty($registration_data['sub_type'])) {
        $desc .= '<span>'.((isset($registration_data['sub_type']) && !empty($registration_data['sub_type'])) ? $sub_types[$registration_data['sub_type']] : '').'</span>';
      }
			$content = '<div>'.$desc.'</div>';
			$locations[$index] = array(
				'title' => $result->user_nicename,
				'about_link' => home_url() . "/members/" . bp_core_get_username($result->ID)."/about/",
				'display_name' => bp_core_get_user_displayname($result->ID),
				'lat' => ($result->lat),
				'lng' => ($result->lng),
				'email' => $content
			);
		}
	}
	echo json_encode($locations);
	die();
}
add_action('wp_ajax_ef_load_locations', 'ef_load_locations');
add_action('wp_ajax_nopriv_ef_load_locations', 'ef_load_locations');
