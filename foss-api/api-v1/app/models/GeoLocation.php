<?php

class GeoLocation extends BaseModel {
	protected $table = 'geolocations';

	public function addLocation($data){
		$this->object_id = $data['object_id'];
		$this->object_type = $data['object_type'];
		$this->lat = $data['lat'];
		$this->lng = $data['lng'];
		return $this;
	}

  public function get_filtered_locations($args=array(), $term_ids=array()) {
  	global $foss_prefix;
  	global $events_types;
    global $ar_events_types;
  	global $account_sub_types;
    global $en_sub_types;
    global $ar_sub_types;
  	$account_type = '';
    $filter_condition = '';
    $having_condition = '';
    
  	$args['type'] = (empty($args['type'])) ? 'Entity' : $args['type'];
  	if($args['type'] == 'Event') {
			$sql =    "SELECT e.ID, e.post_title, e.guid, e.post_content, ev.venue_id, ev.venue_name, ev.event_start_datetime, ev.event_end_datetime, g.lat, g.lng"
							. " FROM {$foss_prefix}posts e";
			if(!empty($term_ids)) {
				$sql .= " LEFT JOIN {$foss_prefix}term_relationships as tr on e.ID = tr.object_id"
								  ." LEFT JOIN {$foss_prefix}term_taxonomy as tt on tr.term_taxonomy_id = tt.term_taxonomy_id";
				$term_count = count($term_ids);
				$term_ids = join(',', $term_ids);
				$filter_condition = " AND tr.term_taxonomy_id in ({$term_ids}) ";
				$having_condition = " HAVING COUNT(*) >= {$term_count} ";
			}
			if(!empty($args['sub_type'])) {
        if (in_array($args['sub_type'],$events_types) || in_array($args['sub_type'], $ar_events_types)) 
        {
          $key = array_search($args['sub_type'], $events_types);
          if(!$key)
          {
            $key = array_search($args['sub_type'], $ar_events_types);
          }        
          $sql .= " JOIN {$foss_prefix}postmeta m ON m.post_id = e.ID AND m.meta_key = 'event_type' AND m.meta_value = '".$key."' ";
        }
      }
			$sql .= " LEFT JOIN {$foss_prefix}events_venues ev ON ev.event_id = e.ID"
								. " LEFT JOIN {$foss_prefix}geolocations g ON g.object_id = ev.venue_id AND g.object_type = 'venue'"
								. " WHERE e.post_type='tribe_events' and e.post_status = 'publish'"
								. " {$filter_condition}"
								. " AND ( ev.event_start_datetime >= DATE(NOW()) OR ev.event_end_datetime >= DATE(NOW()) ) "
								. " GROUP BY e.ID "
								. " {$having_condition} "
								. " ORDER BY ev.event_start_datetime ";
		} else {
      if ($args['type'] == 'Individual') {
        $account_type = '%:"Individual";%';
      } else if ($args['type'] == 'Entity') {
        $account_type = '%:"Entity";%';
      }
			if(!empty($term_ids)) {
				$term_count = count($term_ids);
				$term_ids = join(',', $term_ids);
				$filter_condition = " AND ur.term_taxonomy_id in ({$term_ids}) ";
				$having_condition = " HAVING COUNT(*) >= {$term_count} ";
			}
			$sql = "SELECT u.ID, u.user_nicename, u.display_name, u.user_email, g.lat, g.lng FROM {$foss_prefix}users u
							LEFT JOIN {$foss_prefix}usermeta m ON u.ID = m.user_id AND m.meta_key = 'registration_data' 
							LEFT JOIN {$foss_prefix}user_relationships as ur on u.ID = ur.user_id
							LEFT JOIN {$foss_prefix}term_taxonomy as tt on ur.term_taxonomy_id = tt.term_taxonomy_id
              LEFT JOIN {$foss_prefix}efb_badges_users AS bu ON bu.user_id = u.ID
							JOIN {$foss_prefix}geolocations g ON g.object_id = u.ID AND g.object_type = 'user'
							WHERE  m.meta_value like '".$account_type."' ";
              
              if( !empty( $args['badge'] ) ) {
                $sql .= " AND bu.badge_id = {$args['badge']} ";
              }
			//if (array_key_exists($args['sub_type'], $account_sub_types)) {
      if (in_array($args['sub_type'],$en_sub_types) || in_array($args['sub_type'], $ar_sub_types)) 
      {
        $key = array_search($args['sub_type'], $en_sub_types);
        if(!$key)
        {
          $key = array_search($args['sub_type'], $ar_sub_types);
        }
				$sql .= " AND m.meta_value like '%".$key."%' ";
			}
			$sql .= " {$filter_condition}
								GROUP BY u.ID 
								{$having_condition} ";
		}
    
    $results = $this->getConnection()->select($sql);
    return $results;
  }
}