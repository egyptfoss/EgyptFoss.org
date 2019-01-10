<?php

function add_event_meta($event_id, $meta){
  if( empty($meta['start_datetime']) ) {
    $meta['start_datetime'] = date('Y-m-d 08:00:00');
  }
  if( empty($meta['end_datetime']) ) {
    $meta['end_datetime'] = date('Y-m-d 17:00:00');
  }
  $start_timestamp = strtotime( $meta['start_datetime'] );
  $end_timestamp   = strtotime( $meta['end_datetime'] );
  if ( $start_timestamp > $end_timestamp ) {
    $meta['end_datetime'] = $meta['start_timestamp'];
  }
  $duration = strtotime( $meta['end_datetime'] ) - $start_timestamp;

  global $wpdb;
  $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type = %s', $meta['venue'], 'tribe_venue');
  $wpdb->query( $query );
  if ( $wpdb->num_rows ) {
    $last_result = $wpdb->last_result;
    $venue_row = $last_result[0];
    $meta['venue_name'] = $meta['venue'];
    $meta['venue'] = $venue_row->ID;
  } else {
    // Add new venue
    $add_venue_array = array(
      'post_title'  => $meta['venue_name'],
      'post_type'   => 'tribe_venue',
      'post_status' => 'publish'
    );
    $venue_id = wp_insert_post( $add_venue_array );
    update_post_meta($venue_id, 'language', serialize(array(
      "slug" => pll_current_language(),
      "translated_id" => 0))
    );
    $gmap = array(
      'address' => $meta['venue_address'],
      'lat' => $meta['lat'],
      'lng' => $meta['lng']
    );
    $venue_meta = array(
      '_VenueVenue' => $meta['venue_name'],
      '_VenueAddress' => $meta['venue_address'],
      '_VenueCity' => $meta['venue_city'],
      '_VenueCountry' => $meta['venue_country'],
      '_VenueProvince' => $meta['venue_province'],
      '_VenueState' => '',
      '_VenueZip'  => '21599',
      '_VenuePhone' => $meta['venue_phone'],
      '_VenueURL' => $meta['venue_website'],
      '_VenueShowMap' => 'true',
      '_gmap' => 'field_56dbfbe8515df',
      'gmap' => $gmap,
    );
    foreach ($venue_meta as $key => $value) {
      add_post_meta($venue_id, $key, $value, true);
    }
    $meta['venue'] = $venue_id;
    $location_data = array(
      'object_id' => $venue_id,
      'object_type' => 'venue',
      'lat' => $meta['lat'],
      'lng' => $meta['lng']
    );
    add_or_update_user_location($location_data);
  }

  $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type = %s', $meta['organizer'], 'tribe_organizer');
  $wpdb->query( $query );
  if ( $wpdb->num_rows ) {
    $last_result = $wpdb->last_result;
    $organizer_row = $last_result[0];
    $meta['organizer'] = $organizer_row->ID;
  } else {
    // Add new organizer
    $add_organizer_array = array(
      'post_title'  => $meta['organizer_name'],
      'post_type'   => 'tribe_organizer',
      'post_status' => 'publish'
    );
    $organizer_id = wp_insert_post( $add_organizer_array );
    update_post_meta($organizer_id, 'language', serialize(array(
      "slug" => pll_current_language(),
      "translated_id" => 0))
    );
    $organizer_meta = array(
      '_OrganizerOrigin' => 'events-calendar',
      '_OrganizerOrganizer' => $meta['organizer_name'],
      '_OrganizerPhone' => $meta['organizer_phone'],
      '_OrganizerWebsite'  => $meta['organizer_website'],
      '_OrganizerEmail'  => $meta['organizer_email']
    );
    foreach ($organizer_meta as $key => $value) {
      add_post_meta($organizer_id, $key, $value, true);
    }
    $meta['organizer'] = $organizer_id;
  }

  $event_meta = array(
    '_EventOrigin' => 'events-calendar',
    '_heateor_sss_meta' => 'a:2:{s:7:"sharing";i:0;s:16:"vertical_sharing";i:0;}',
    '_EventShowMapLink' => '1',
    '_EventShowMap' => '1',

    '_EventOrganizerID' => $meta['organizer'],
    'event_type' => $meta['event_type'],
    '_EventURL' => $meta['website'],

    '_EventVenueID' => $meta['venue'],

    '_EventStartDate' => $meta['start_datetime'],
    '_EventEndDate' => $meta['end_datetime'],
    '_EventStartDateUTC' => $meta['start_datetime'],
    '_EventEndDateUTC' => $meta['end_datetime'],
    '_EventDuration' => $duration,
    '_EventTimezone' => 'UTC+0',
    '_EventTimezoneAbbr' => '',

    'audience' => $meta['audience'],
    'objectives' => $meta['objectives'],
    'prerequisites' => $meta['prerequisites'],

    '_EventCurrencySymbol' => $meta['currency'],
    '_EventCurrencyPosition' => 'prefix',
    '_EventCost' => $meta['cost'],

    'functionality' => $meta['functionality']
  );
  foreach ($event_meta as $key => $value) {
    add_post_meta($event_id, $key, $value, true);
  }
  //add theme
  $theme = get_term($meta['theme'], 'theme');
  update_post_meta($event_id, 'theme', $theme->term_id);
  $taxonomies = array('technology', 'platform', 'interest');
  wp_set_object_terms($event_id, $theme->term_id, 'theme');
  foreach ($taxonomies as $taxonomy){
    $term_ids = wp_set_object_terms($event_id, $meta[$taxonomy], $taxonomy);
    update_post_meta($event_id, $taxonomy, getTermFromTermTaxonomy($term_ids));
  }
  // save event venue record
  $event_venue_array = array(
    'event_id' => $event_id,
    'venue_id' => $meta['venue'],
    'venue_name' => $meta['venue_name'],
    'event_start_datetime' => $meta['start_datetime'],
    'event_end_datetime' => $meta['end_datetime'],
  );
  add_or_update_event_venue($event_venue_array);
}

function validate_event_data($args) {
  global $wpdb;
  global $events_types;
  global $system_currencies;
  $message = '';
  if( empty($args['event_title']) || empty($args['description']) || empty($args['start_datetime']) || empty($args['end_datetime']) || empty($args['event_type']) || (empty($args['venue']) && empty($args['venue_name'])) || (empty($args['organizer']) && empty($args['organizer_name'])) ){
    $message = __('Please enter all the required fields','egyptfoss');
  } else if (( mb_strlen($args['event_title'],'UTF-8') < 2 ) || ( mb_strlen($args['event_title'],'UTF-8') > 100 )) {
    $message = __('Event title length must be between 2 and 100 characters','egyptfoss');
  } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['event_title'], $matches)) {
    $message = __('Event title must at least contain one letter','egyptfoss');
  } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['description'], $matches)) {
    $message = __('Event description must at least contain one letter','egyptfoss');
  } else if( $args['start_datetime'] != date('Y-m-d H:i:s',strtotime($args['start_datetime'])) ) {
    $message = __('Invalid Start datetime','egyptfoss');
  } else if( $args['start_datetime'] < date('Y-m-d H:i:s') ) {
    $message = __('Start date can not be in the past','egyptfoss');
  } else if( $args['end_datetime'] != date('Y-m-d H:i:s',strtotime($args['end_datetime'])) ) {
    $message = __('Invalid End datetime','egyptfoss');
  } else if( $args['end_datetime'] < $args['start_datetime'] ) {
    $message = __('End date can not be before the start date','egyptfoss');
  } else if (!empty($args['event_website']) && filter_var($args['event_website'], FILTER_VALIDATE_URL) === false) {
    $message = __('Invalid Event Website','egyptfoss');
  } else if (!empty($args['cost']) && (!ctype_digit($args['cost']) || ($args['cost'] < 0))) {
    $message = __('Invalid Cost','egyptfoss');
  } else if (!empty($args['audience']) && !preg_match('/[أ-يa-zA-Z]+/', $args['audience'], $matches)) {
    $message = __('Invalid Audience','egyptfoss');
  } else if (!empty($args['objectives']) && !preg_match('/[أ-يa-zA-Z]+/', $args['objectives'], $matches)) {
    $message = __('Invalid Objectives','egyptfoss');
  } else if (!empty($args['prerequisites']) && !preg_match('/[أ-يa-zA-Z]+/', $args['prerequisites'], $matches)) {
    $message = __('Invalid Prerequisites','egyptfoss');
  } else if (!empty($args['functionality']) && !preg_match('/[أ-يa-zA-Z]+/', $args['functionality'], $matches)) {
    $message = __('Invalid Functionality','egyptfoss');
  } else if (!array_key_exists($args['event_type'], $events_types)) {
    $message = __('Invalid Event Type','egyptfoss');
  } else if (!empty($args['currency']) && !array_key_exists($args['currency'], $system_currencies)) {
    $message = __('Invalid Currency','egyptfoss');
  } else if (empty($args['venue']) && empty($args['venue_name'])) {
    $message = __('Venue field required','egyptfoss');
  } else if(!empty($args['venue_name']) && (get_page_by_title($args['venue_name'], OBJECT, 'tribe_venue') != NULL)) {
    $message = __('Venue Name already exists','egyptfoss');
  } else if(!empty($args['venue_name']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_name'], $matches)) {
    $message = __('Venue Name must at least contain one letter','egyptfoss');
  } else if (!empty($args['venue_address']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_address'], $matches)) {
    $message = __('Venue Address must at least contain one letter','egyptfoss');
  } else if (!empty($args['venue_city']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_city'], $matches)) {
    $message = __('Venue City must at least contain one letter','egyptfoss');
  } else if (!empty($args['venue_country']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_country'], $matches)) {
    $message = __('Venue Country must at least contain one letter','egyptfoss');
  } else if (!empty($args['venue_province']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_province'], $matches)) {
    $message = __('Invalid Venue Province','egyptfoss');
  } else if (!empty($args['venue_phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['venue_phone'], $matches) || (!preg_match('/[0-9]+/', $args['venue_phone'], $matches)))) {
    $message = __('Invalid Venue Phone','egyptfoss');
  } else if (!empty($args['venue_website']) && filter_var($args['venue_website'], FILTER_VALIDATE_URL) === false) {
    $message = __('Invalid Venue Website','egyptfoss');
  } else if (empty($args['organizer']) && empty($args['organizer_name'])) {
    $message = __('Organizer field required','egyptfoss');
  } else if(!empty($args['organizer_name']) && (get_page_by_title($args['organizer_name'], OBJECT, 'tribe_organizer') != NULL)) {
    $message = __('Organizer Name already exists','egyptfoss');
  } else if(!empty($args['organizer_name']) && !preg_match('/[أ-يa-zA-Z]+/', $args['organizer_name'], $matches)) {
    $message = __('Organizer Name must at least contain one letter','egyptfoss');
  } else if (!empty($args['organizer_phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['organizer_phone'], $matches) || (!preg_match('/[0-9]+/', $args['organizer_phone'], $matches)))) {
    $message = __('Invalid Organizer Phone','egyptfoss');
  } else if (!empty($args['organizer_website']) && filter_var($args['organizer_website'], FILTER_VALIDATE_URL) === false) {
    $message = __('Invalid Organizer Website','egyptfoss');
  } else if (!empty($args['organizer_email']) && filter_var($args['organizer_email'], FILTER_VALIDATE_EMAIL) === false) {
    $message = __('Invalid Organizer Email','egyptfoss');
  } else if (!empty($args['platform'])) {
    foreach($args['platform'] as $plat)
    {
      if (!get_term_by('name', $plat, "platform")) {
          $message = sprintf(__("Please select already exist %s", "egyptfoss"), __('platform', "egyptfoss"));
      }
    }
  }
  return $message;
}

function save_event_location( $post_id, $post ) {
  $types = array('tribe_events', 'tribe_venue');
  if ( !in_array($post->post_type, $types) ) {
    return;
  }
  if ($post->post_type == 'tribe_venue') {
    if ( isset( $_REQUEST['acf'] ) ) {
      $gmap = $_REQUEST['acf']['field_56dbfbe8515df'];
      $lat = (isset($gmap['lat'])) ? $gmap['lat'] : '';
      $lng = (isset($gmap['lng'])) ? $gmap['lng'] : '';
      $location_data = array(
        'object_id' => $post_id,
        'object_type' => 'venue',
        'lat' => $lat,
        'lng' => $lng
      );
      add_or_update_user_location($location_data);
    }
    rename_event_venue($post_id, array('venue_name' => $post->post_title));
  }
  if ($post->post_type == 'tribe_events') {
    $start_datetime = get_post_meta($post_id, '_EventStartDate', true);
    $end_datetime = get_post_meta($post_id, '_EventEndDate', true);
    $venue_id = get_post_meta($post_id, '_EventVenueID', true);
    $venue_name = '';
    if ($venue_id) {
      $venue = get_post($venue_id);
      $venue_name = $venue ? $venue->post_title : '';
    }
    // save event venue record
    $event_venue_array = array(
      'event_id' => $post_id,
      'venue_id' => $venue_id,
      'venue_name' => $venue_name,
      'event_start_datetime' => $start_datetime,
      'event_end_datetime' => $end_datetime,
    );
    add_or_update_event_venue($event_venue_array);
  }
}
add_action( 'save_post', 'save_event_location', 100, 2 );

function venue_metabox_hint($post) {
  save_event_location( $post->ID, $post );
  // echo("<h3 style='color:red'>For newly added venues, please edit venue to add it's exact google map location</h3>");
}
add_action( 'tribe_events_after_venue_metabox', 'venue_metabox_hint');

function remove_add_another_organizer_link() {
  echo '<style>.tribe-add-organizer { display: none; }</style>';
}
add_action('admin_head', 'remove_add_another_organizer_link');

function get_event_data($event_id) {
  $event_id = intval($event_id);
  $event = get_post($event_id);
  $data = get_post_meta($event_id);
  $taxonomies = array('theme', 'technology', 'platform', 'interest');
  foreach ($taxonomies as $taxonomy) {
    if (isset($data[$taxonomy])) {
      if($taxonomy == 'theme') {
        $terms = $data[$taxonomy][0];
      } else {
        $terms = unserialize($data[$taxonomy][0]);
      }
      if (is_array($terms)) {
        foreach ($terms as $i => $term_id) {
          $term = get_term($term_id, $taxonomy);
          $term_name = isset($term->name) ? $term->name : '';
          $terms[$i] = $term_name;
        }
      }
      $data[$taxonomy] = $terms;
    } else {
      $data[$taxonomy] = '';
    }
  }
  $meta = ['pid' => $event_id,
           'event_title' => $event->post_title,
           'description' => $event->post_content,
           'event_type' => (isset($data['event_type']) ? $data['event_type'][0] : ''),
           'start_datetime' => (isset($data['_EventStartDate']) ? $data['_EventStartDate'][0] : ''),
           'end_datetime' => (isset($data['_EventEndDate']) ? $data['_EventEndDate'][0] : ''),
           'venue' => (isset($data['_EventVenueID']) ? $data['_EventVenueID'][0] : ''),
           'organizer' => (isset($data['_EventOrganizerID']) ? $data['_EventOrganizerID'][0] : ''),
           'website' => (isset($data['_EventURL']) ? $data['_EventURL'][0] : ''),
           'audience' => (isset($data['audience']) ? $data['audience'][0] : ''),
           'objectives' => (isset($data['objectives']) ? $data['objectives'][0] : ''),
           'prerequisites' => (isset($data['prerequisites']) ? $data['prerequisites'][0] : ''),
           'currency' => (isset($data['_EventCurrencySymbol']) ? $data['_EventCurrencySymbol'][0] : ''),
           'cost' => (isset($data['_EventCost']) ? $data['_EventCost'][0] : ''),
           'functionality' => (isset($data['functionality']) ? $data['functionality'][0] : ''),
           'theme' => $data['theme'],
           'platform' => $data['platform'],
           'interest' => $data['interest'],
           'technology' => $data['technology']
          ];
  return $meta;
}

function update_event_meta($event_id, $meta){
  $start_timestamp = strtotime( $meta['start_datetime'] );
  $end_timestamp   = strtotime( $meta['end_datetime'] );
  if ( $start_timestamp > $end_timestamp ) {
    $meta['end_datetime'] = $meta['start_timestamp'];
  }
  $duration = strtotime( $meta['end_datetime'] ) - $start_timestamp;
  $meta['venue_name'] = $meta['venue'];

  global $wpdb;
  $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type = %s', $meta['venue'], 'tribe_venue');
  $wpdb->query( $query );
  if ( $wpdb->num_rows ) {
    $last_result = $wpdb->last_result;
    $venue_row = $last_result[0];
    $meta['venue_name'] = $meta['venue'];
    $meta['venue'] = $venue_row->ID;
  }

  $query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type = %s', $meta['organizer'], 'tribe_organizer');
  $wpdb->query( $query );
  if ( $wpdb->num_rows ) {
    $last_result = $wpdb->last_result;
    $organizer_row = $last_result[0];
    $meta['organizer'] = $organizer_row->ID;
  }

  $event_meta = array(
    '_EventOrganizerID' => $meta['organizer'],
    'event_type' => $meta['event_type'],
    '_EventURL' => $meta['website'],
    '_EventVenueID' => $meta['venue'],
    '_EventStartDate' => $meta['start_datetime'],
    '_EventEndDate' => $meta['end_datetime'],
    '_EventStartDateUTC' => $meta['start_datetime'],
    '_EventEndDateUTC' => $meta['end_datetime'],
    '_EventDuration' => $duration,
    'audience' => $meta['audience'],
    'objectives' => $meta['objectives'],
    'prerequisites' => $meta['prerequisites'],
    '_EventCurrencySymbol' => $meta['currency'],
    '_EventCost' => $meta['cost'],
    'functionality' => $meta['functionality']
  );
  foreach ($event_meta as $key => $value) {
    update_post_meta($event_id, $key, $value);
  }

  $taxonomies = array('theme', 'technology', 'platform', 'interest');
  foreach ($taxonomies as $taxonomy){
    if($taxonomy == 'theme') {
        $term = get_term($meta[$taxonomy], $taxonomy);
        $term_id = isset($term->term_id) ? $term->term_id : '';
        $term_ids = $term_id;
        wp_set_object_terms($event_id, $term_ids, $taxonomy);
    } else {
      $term_ids = wp_set_object_terms($event_id, $meta[$taxonomy], $taxonomy);
      $term_ids = getTermFromTermTaxonomy($term_ids);
    }
    update_post_meta($event_id, $taxonomy, $term_ids);
  }
  // save event venue record
  $event_venue_array = array(
    'event_id' => $event_id,
    'venue_id' => $meta['venue'],
    'venue_name' => $meta['venue_name'],
    'event_start_datetime' => $meta['start_datetime'],
    'event_end_datetime' => $meta['end_datetime'],
  );
  add_or_update_event_venue($event_venue_array);
}

function admin_event_currency() { ?>
  <script type="text/javascript">
    currency_symbol = jQuery("#EventCurrencySymbol").val();
    symbols = ['EGP', 'USD', 'EUR'];
    options = '';
    for(i=0; i<symbols.length; i++){
      selected = (symbols[i] == currency_symbol) ? 'selected' : '';
      options += '<option value="'+symbols[i]+'" '+selected+'>'+symbols[i]+'</option>';
    }
    jQuery("#EventCurrencySymbol").replaceWith('<select id="EventCurrencySymbol" name="EventCurrencySymbol">' +
      options +
    '</select>');
    jQuery("#EventCurrencyPosition").remove();
  </script>
  <?php
}
add_action( 'admin_footer', 'admin_event_currency' );

function event_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['tribe_events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Event updated. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Event updated.'),
    3 => __('Event deleted.'),
    4 => __('Event updated.'),
    5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Event published. <a href="%s">View event</a> <br/>Add map location for newly added venues from venues list.'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Event saved.'),
    8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}
add_filter('post_updated_messages', 'event_updated_messages');
