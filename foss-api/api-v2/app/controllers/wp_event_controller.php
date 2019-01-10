<?php
  class WPEventController extends EgyptFOSSController {

  /**
   * @SWG\Post(
   *   path="/events",
   *   tags={"Events"},
   *   summary="Creates Event",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to create a new event<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define Event Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Event Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Event Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="event_type", in="formData", required=false, type="string", description="Event Type <br/><b>Validations: </b><br/> 1. Predefined Event's type in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="start_datetime", in="formData", required=false, type="string", description="Event Start Date (Data/time format should be 2016-03-27 00:00:00)<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="end_datetime", in="formData", required=false, type="string", description="Event End Date (Data/time format should be 2016-03-27 00:00:00)<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="venue", in="formData", required=false, type="string", description="Venue Name <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>Values: </b> Any of predefined venues from setup data or ability to add new venue <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="venue_address", in="formData", required=false, type="string", description="Venue Address <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required If adding new Venue]</b>"),
   *   @SWG\Parameter(name="venue_country", in="formData", required=false, type="string", description="Venue Country <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="venue_city", in="formData", required=false, type="string", description="Venue City <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="venue_phone", in="formData", required=false, type="string", description="Venue Phone <br/><b>Validations: </b><br/> 1. Valid phone format (numbers only)"),
   *   @SWG\Parameter(name="venue_latitude", in="formData", required=false, type="string", description="Venue Latitude <br/><b>Validations: </b><br/> 1. Valid GeoLocation format <br/> <b>[Required If adding new Venue]</b>"),
   *   @SWG\Parameter(name="venue_longitude", in="formData", required=false, type="string", description="Venue Longitude <br/><b>Validations: </b><br/> 1. Valid GeoLocation format <br/> <b>[Required If adding new Venue]</b>"),
   *   @SWG\Parameter(name="organizer", in="formData", required=false, type="string", description="Organizer Name <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>Values: </b> Any of predefined venues from setup data or ability to add new venue <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="organizer_email", in="formData", required=false, type="string", description="Organizer email<br/><b>Validations</b><br/>1. Valid Email"),
   *   @SWG\Parameter(name="organizer_phone", in="formData", required=false, type="string", description="Organizer Phone <br/><b>Validations: </b><br/> 1. Valid phone format (numbers only)"),
   *   @SWG\Parameter(name="event_website", in="formData", required=false, type="string", description="Event Website <br/><b>Validations</b><br/>1. Valid URL"),
   *   @SWG\Parameter(name="currency", in="formData", required=false, type="string", description="Event Currency <br/> <b>Values:</b> <br/> 1.EGP<br/>2.USB<br/>3.EUR"),
   *   @SWG\Parameter(name="cost", in="formData", required=false, type="string", description="Event Cost <br/><b>Validations: </b><br/> 1. Numeric Value<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="audience", in="formData", required=false, type="string", description="Event Audience <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="objectives", in="formData", required=false, type="string", description="Event Objectives <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="prerequisites", in="formData", required=false, type="string", description="Event Prerequisites <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="functionality", in="formData", required=false, type="string", description="Event Functionality <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Event Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Event Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="platform", in="formData", required=false, type="string", description="Event Platforms <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Event Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Response(response="200", description="Edit user profile")
   * )
   */
  public function addEvent($request, $response, $args) {
    global $events_types;
    global $system_currencies;
    $parameters = ['token', 'lang', 'title', 'description', 'event_type', 'start_datetime', 'end_datetime', 
                   'venue', 'venue_address', 'venue_country', 'venue_city', 'venue_phone', 'venue_latitude', 'venue_longitude',
                   'organizer', 'organizer_email', 'organizer_phone', 'event_website', 'currency', 
                   'cost', 'audience', 'objectives', 'prerequisites', 'functionality', 
                   'theme', 'platform', 'technology', 'interest'];

    foreach($parameters as $parameter){
      $args[$parameter] = (array_key_exists($parameter, $_POST)) ? $_POST[$parameter] : '';
    }
    $result = "";
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
        } else {
          // validate data
          $term_taxonomy = $this->check_term_exists($args['theme'], 'theme');
          $term_taxonomy_platform = $this->check_term_exists($args['platform'], 'platform');
          if( empty($args['title']) || empty($args['description']) || empty($args['lang']) || empty($args['start_datetime']) || empty($args['end_datetime']) || empty($args['event_type']) ){
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
          } else if(mb_strlen($args["title"], 'UTF-8') < 2 || mb_strlen($args["title"], 'UTF-8') > 100){
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between", ", Event title",array("range"=>'2 to 100 characters')));
          } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['title'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", ", Event title must at least contain one letter"));
          } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['description'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", ", Event description must at least contain one letter"));
          } else if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
          } else if( $args['start_datetime'] != date('Y-m-d H:i:s',strtotime($args['start_datetime'])) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Start Datetime"));
          } else if( $args['start_datetime'] < date('Y-m-d H:i:s') ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Start Datetime"));
          } else if( $args['end_datetime'] != date('Y-m-d H:i:s',strtotime($args['end_datetime'])) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "End Datetime"));
          } else if( $args['end_datetime'] < $args['start_datetime'] ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "End Datetime"));
          } else if (!empty($args['event_website']) && filter_var($args['event_website'], FILTER_VALIDATE_URL) === false) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event Website"));
          } else if (!empty($args['cost']) && (!ctype_digit($args['cost']) || ($args['cost'] < 0))) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Cost"));
          } else if (!empty($args['audience']) && !preg_match('/[أ-يa-zA-Z]+/', $args['audience'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Audience"));
          } else if (!empty($args['objectives']) && !preg_match('/[أ-يa-zA-Z]+/', $args['objectives'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Objectives"));
          } else if (!empty($args['prerequisites']) && !preg_match('/[أ-يa-zA-Z]+/', $args['prerequisites'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Prerequisites"));
          } else if (!empty($args['functionality']) && !preg_match('/[أ-يa-zA-Z]+/', $args['functionality'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Functionality"));
          } else if (!array_key_exists($args['event_type'], $events_types)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event Type"));
          } else if (!empty($args['currency']) && !array_key_exists($args['currency'], $system_currencies)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Currency"));
          } else if (!empty($args['theme']) && empty($term_taxonomy)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Theme"));
          } else if (!empty($args['platform']) && empty($term_taxonomy_platform)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Platform"));
          }else if(!preg_match('/[أ-يa-zA-Z]+/', $args['venue'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Name"));
          } else if (!empty($args['venue_address']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_address'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Address"));
          } else if (!empty($args['venue_city']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_city'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue City"));
          } else if (!empty($args['venue_country']) && !preg_match('/[أ-يa-zA-Z]+/', $args['venue_country'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Country"));
          } else if (!empty($args['venue_phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['venue_phone'], $matches) || (!preg_match('/[0-9]+/', $args['venue_phone'], $matches)))) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Phone"));
          } else if (!empty($args['venue_latitude']) && preg_match('/[^0-9.]+/', $args['venue_latitude'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Latitude"));
          } else if (!empty($args['venue_longitude']) && preg_match('/[^0-9.]+/', $args['venue_longitude'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue Longitude"));
          } else if(!preg_match('/[أ-يa-zA-Z]+/', $args['organizer'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Organizer Name"));
          } else if (!empty($args['organizer_phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['organizer_phone'], $matches) || (!preg_match('/[0-9]+/', $args['organizer_phone'], $matches)))) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Organizer Phone"));
          } else if (!empty($args['organizer_email']) && filter_var($args['organizer_email'], FILTER_VALIDATE_EMAIL) === false) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Organizer Email"));
          } else {
            // validate taxonomies
            //$taxonomies = ['platform', 'technology', 'interest'];
            $taxonomies = ['technology', 'interest'];
            foreach ($taxonomies as $taxonomy) {
              $terms = trim($args[$taxonomy],',');
              $terms = str_getcsv($terms);
              foreach ($terms as $term) {
                if (!empty($term) && !preg_match('/[أ-يa-zA-Z]+/', $term, $matches)) {
                  return $this->renderJson($response, 422, Messages::getErrorMessage("formatError", ucwords($taxonomy)));
                }
              }
            }
            $is_first_suggestion = $this->is_first_suggestion($user_id);
            $args['venue'] = str_replace( '&#039;', '’', $args['venue'] );
            $venue_record = Post::where('post_title' , '=', $args['venue'])->where('post_type' , '=', 'tribe_venue')->first();
            if ($venue_record !== null) {
              $args['venue_name'] = $args['venue'];
              $args['venue'] = $venue_record->ID;
            } else {
              if( empty($args['venue_address']) || empty($args['venue_latitude']) || empty($args['venue_longitude']) ) {
                return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Address, Latitude, & Longitude for new venue"));
              }
              // Add new venue
              $venue = new Post;
              $venue->post_title = $args['venue'];
              $venue->post_type = 'tribe_venue';
              $venue->post_author = $user_id;
              $name = strtolower(str_replace(' ','-',trim($args['venue'])));
              if(strlen($name) !== mb_strlen($name,'UTF-8'))
              {
                $name = substr($name,0,70);
              }              
              $venue->post_name = self::generatePostName($name);
              $venue->post_status = "publish";
              $venue->save();
              $venue_id = $venue->id;
              $venue->updateGUID($venue_id, 'tribe_venue');
              $venue->add_post_translation($venue_id, $args['lang']);
              $venue->save();
              $gmap = array(
                'address' => $args['venue_address'],
                'lat' => $args['venue_latitude'],
                'lng' => $args['venue_longitude']
              );
              $venue_meta = array(
                '_VenueVenue' => $args['venue'],
                '_VenueAddress' => $args['venue_address'],
                '_VenueCity' => $args['venue_city'],
                '_VenueCountry' => $args['venue_country'],
                '_VenueProvince' => '',
                '_VenueState' => '',
                '_VenueZip'  => '21599',
                '_VenuePhone' => $args['venue_phone'],
                '_VenueURL' => '',
                '_VenueShowMap' => 'true',
                '_gmap' => 'field_56dbfbe8515df',
                'gmap' => serialize($gmap),
              );
              foreach ($venue_meta as $name => $value) {
                $meta = new Postmeta;
                $meta->post_id = $venue_id;
                $meta->meta_key = $name;
                $meta->meta_value = $value;
                $meta->save();
              }
              $args['venue_name'] = $args['venue'];
              $args['venue'] = $venue_id;
              $location = new GeoLocation;
              $location_array = array(
                'object_id' => $venue_id,
                'object_type' => 'venue',
                'lat' => $args['venue_latitude'],
                'lng' => $args['venue_longitude']
              );
              $location->addLocation($location_array);
              $location->save();
            }
            
            $args['organizer'] = str_replace( '&#039;', '’', $args['organizer'] );
            $organizer_record = Post::where('post_title' , '=', $args['organizer'])->where('post_type' , '=', 'tribe_organizer')->first();
            if ($organizer_record !== null) {
              $args['organizer'] = $organizer_record->ID;
            } else {
              // Add new organizer
              $organizer = new Post;
              $organizer->post_title = $args['organizer'];
              $organizer->post_type = 'tribe_organizer';
              $organizer->post_author = $user_id;
              $name = strtolower(str_replace(' ','-',trim($args['organizer'])));
              if(strlen($name) !== mb_strlen($name,'UTF-8'))
              {
                $name = substr($name,0,70);
              }              
              $organizer->post_name = self::generatePostName($name);
              $organizer->post_status = "publish";        
              $organizer->save();
              $organizer_id = $organizer->id;
              $organizer->updateGUID($organizer_id, 'tribe_organizer');
              $organizer->add_post_translation($organizer_id, $args['lang']);
              $organizer->save();
              $organizer_meta = array(
                '_OrganizerOrigin' => 'events-calendar',
                '_OrganizerOrganizer' => $args['organizer'],
                '_OrganizerPhone' => $args['organizer_phone'],
                '_OrganizerEmail'  => $args['organizer_email'],
                '_OrganizerWebsite'  => '',
              );
              foreach ($organizer_meta as $name => $value) {
                $meta = new Postmeta;
                $meta->post_id = $organizer_id;
                $meta->meta_key = $name;
                $meta->meta_value = $value;
                $meta->save();
              }
              $args['organizer'] = $organizer_id;
            }
          }

          // If no errors so far
          if ($result == "") {
            $post = new Post;
            $add_event_array = array(
              'post_title'  => $args['title'],
              'post_type'   => 'tribe_events',
              'post_content' => $args['description'],
              'post_status' => 'pending',
              'post_author' => $user_id
            );
            $post->addPost($add_event_array);
            $post->save();
            $post_id = $post->id;
            $post->updateGUID($post_id, 'tribe_events');
            $post->add_post_translation($post_id, $args['lang']);
            $post->save();

            $start_timestamp = strtotime( $args['start_datetime'] );
            $end_timestamp   = strtotime( $args['end_datetime'] );
            if ( $start_timestamp > $end_timestamp ) {
              $args['end_datetime'] = $args['start_timestamp'];
            }
            $duration = strtotime( $args['end_datetime'] ) - $start_timestamp;

            // save event venue record
            $event_venue = new EventVenue;
            $event_venue_array = array(
              'event_id' => $post_id,
              'venue_id' => $args['venue'],
              'venue_name' => $args['venue_name'],
              'event_start_datetime' => $args['start_datetime'],
              'event_end_datetime' => $args['end_datetime'],
            );
            $event_venue->addEventVenue($event_venue_array);
            $event_venue->save();

            $event_meta = array(
              '_EventOrigin' => 'events-calendar',
              '_heateor_sss_meta' => 'a:2:{s:7:"sharing";i:0;s:16:"vertical_sharing";i:0;}',
              '_EventShowMapLink' => '1',
              '_EventShowMap' => '1',
              '_EventOrganizerID' => $args['organizer'],
              'event_type' => $args['event_type'],
              '_EventURL' => $args['event_website'],
              '_EventVenueID' => $args['venue'],
              '_EventStartDate' => $args['start_datetime'],
              '_EventEndDate' => $args['end_datetime'],
              '_EventStartDateUTC' => $args['start_datetime'],
              '_EventEndDateUTC' => $args['end_datetime'],
              '_EventDuration' => $duration,
              '_EventTimezone' => 'UTC+0',
              '_EventTimezoneAbbr' => '',
              'audience' => $args['audience'],
              'objectives' => $args['objectives'],
              'prerequisites' => $args['prerequisites'],
              '_EventCurrencySymbol' => $args['currency'],
              '_EventCurrencyPosition' => 'prefix',
              '_EventCost' => $args['cost'],
              'functionality' => $args['functionality'],
            );
            foreach ($event_meta as $name => $value) {
              $meta = new Postmeta;
              $meta->post_id = $post_id;
              $meta->meta_key = $name;
              $meta->meta_value = $value;
              $meta->save();
            }

            $termTax = new TermTaxonomy();
            $terms_data = array(
              "theme" => array($args['theme']),
              "technology" => split(",",$args['technology']), 
              "platform" => split(",",$args['platform']),
              "interest" => split(",",$args['interest'])
            );
            $isCreated = $termTax->saveTermTaxonomies($terms_data, array("theme"));
            if(!$isCreated){
              $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
            } else {
              $post->updatePostTerms($post_id, $terms_data, true);
              $returnMsg = Messages::getSuccessMessage("Success","Event Added");
              $returnMsg['event_id'] = $post_id;
              $returnMsg['is_first_suggestion'] = $is_first_suggestion;
              $returnMsg['is_pending_review'] = !($post->post_status == 'publish');
              
              $result = $this->renderJson($response, 200, $returnMsg);
            }
          }
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
  /**
   * @SWG\Get(
   *   path="/events",
   *   tags={"Events"},
   *   summary="List events",
   *   description="Return events in the system paginated",
   *   consumes={"application/x-www-form-urlencoded"},
   *   @SWG\Parameter(name="find_by_title", in="query", required=false, type="string", description="Search by Event Title"),
   *   @SWG\Parameter(name="find_by_date", in="query", required=false, type="string", description="Search by Event Date <br/> <b>Format:</b> yyyy-mm (year-month)"),
   *   @SWG\Parameter(name="page_no", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="no_of_events", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Response(response="200", description="listing event form"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listingEvents($request, $response, $args) {
    $parameters = ['no_of_events','page_no','find_by_title','find_by_date'];
    $atLeastOneParam = ['no_of_events','find_by_date'];
    $atLeastOneParamCount = count($atLeastOneParam);
    $numeric_params = ['no_of_events','page_no'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(!empty($value) && ! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $atLeastOneParam = array_diff($atLeastOneParam,[$key]);
      }
    }
    if(count($atLeastOneParam) == $atLeastOneParamCount )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(!empty($args['no_of_events']) && ($args['no_of_events'] < 1 || $args['no_of_events'] > 25))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'products no',array("range"=> "1 and 25 ")));
    }
    
    if(!empty($args['no_of_events']) && empty($args['page_no']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue",'page_no'));
    }
    
    if(!empty($args['find_by_date']) && !validateDate($args['find_by_date'], 'Y-m') )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue",'Date'));
    }
    
    //check if isset other parameters
    if(!isset($args['find_by_date']))
    {
      $args['find_by_date'] = '';
    }
    
    if(!isset($args['find_by_title']))
    {
      $args['find_by_title'] = '';
    }
    
    $args = array("from_date" => ($args['find_by_date'] != '')?date("Y-m-1 00:00:00", strtotime($args['find_by_date'])):'',
              "to_date" => ($args['find_by_date'] != '')?date("Y-m-31 23:59:59", strtotime($args['find_by_date'])):'',
              "post_type" => "tribe_events",
              "post_status" => "publish",
              "find_by_title" => $args['find_by_title'],
              "find_by_date" => $args['find_by_date'],
              "no_of_events" => $args['no_of_events'],
              "offset" => (!empty($args['page_no']))?($args['page_no'] * $args['no_of_events'])-$args['no_of_events']:0
            );
    
    $event = new Post();
    $metaLabels = ["_EventStartDate","_EventEndDate","_EventCost","_EventCurrencySymbol"];
    $events = $event->getEventsBy($args);
    if(sizeof($events) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    //get total count
    $args_count = array("from_date" => date("Y-m-1 00:00:00", strtotime($args['find_by_date'])),
      "to_date" => date("Y-m-31 23:59:59", strtotime($args['find_by_date'])),
      "post_type" => "tribe_events",
      "post_status" => "publish",
      "find_by_title" => $args['find_by_title'],
      "find_by_date" => $args['find_by_date'],
      "no_of_events" => '',
      "offset" => ''
    );    
    $total_count = count($event->getEventsBy($args_count));
    $eventList = $this->ef_load_data_counts($total_count, $args['no_of_events']);
    $index = 0;
    foreach($events as $key => $event)
    {
      $eventList['data'][$key] = array(
                               "event_id" => $event->ID,
                               "event_title" => $event->post_title,
                               "event_author_id" => $event->post_author,
                               "event_url" => $event->guid,
                               "created_date" => $event->post_date,
                               "added_by" => $this->return_user_info_list($event->post_author)
                                );
      foreach ($event->postmeta as $eventmeta)
      {
        if(in_array($eventmeta["meta_key"], $metaLabels))
        {
          $eventList['data'][$key] = array_merge($eventList['data'][$key],array($eventmeta["meta_key"] => $eventmeta["meta_value"]));
        }else
        {
          if($eventmeta["meta_key"] == "_EventVenueID")
          {
           $venueAddress = null; 
           $is_venue = Post::find($eventmeta["meta_value"]);
           if($is_venue)
           {
              $venueAddress = $is_venue->postmeta()->where("meta_key", "=" ,"_VenueAddress")->get()->first()["meta_value"];  
           }
           $eventList['data'][$key] = array_merge($eventList['data'][$key],array("venueName" => $is_venue->post_title, "venueAddress" => $venueAddress));  
          }
        }
      }
      $index += 1;
    }
    return $this->renderJson($response,200,$eventList);
  }
  
  /**
   * @SWG\GET(
   *   path="/event/{eventId}",
   *   tags={"Events"},
   *   summary="Finds an Event",
   *   description="Get event data according to the passed event id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending news if exists"),
   *   @SWG\Parameter(name="eventId", in="path", required=false, type="integer", description="Event ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View an event info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Event not found")
   * )
   */
  public function viewEvent($request, $response, $args) {
    $event = new Post();
    if(!isset($args["eventId"]) || $args["eventId"] == "" || $args["eventId"] == "{eventId}") 
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    $params = $request->getHeaders();
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    } else if( !empty($_GET['lang']) &&  !in_array( $_GET['lang'], array( 'ar', 'en' ) ) ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "Language"));
    }
    
    //retrieve events by id
    $post_type = "tribe_events";
    if($user != null && !empty($user))
    {
      $post_status = "";
    }else {
      $post_status = "publish";
    }
           
    $event = $event->getPostByID($args['eventId'], $post_type, $post_status);
    if ($event) { 
      if($user != null && !empty($user))
      {
        if($event->post_status == 'pending' && $event->post_author != $user->ID)
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Event ID"));
        }
      }else
      {
        if($event->post_status == 'pending')
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Event ID"));
        }
      }
      
      $user = User::find($event->post_author);
      $user_name = $user->user_login;
      
      $post_meta = new Postmeta();
      $meta = $post_meta->getEventMeta($event->ID);
      $news_meta = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
      }
      unset($meta_value);

      //retrieve themes
      $term = new Term();
      $theme = "";
      if((isset($news_meta['theme'])) && $news_meta['theme'] != '') {
            $term_obj = $term->getTerm($news_meta['theme']);
            if( !empty( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] == 'ar' ) {
                $theme = $term_obj->name_ar;
            }
            else {
              $theme = $term_obj->name;
            }
      }
      
      //Organizer Info
      $organizerArr = array();
      if((isset($news_meta['_EventOrganizerID'])))
      {
          $post_type = 'tribe_organizer';
          $post_status = "publish";
          $organizer_post = new Post();
          $organizer = $organizer_post->getPostByID($news_meta['_EventOrganizerID'], $post_type, $post_status);
          
          //Get Post Meta
          $organizer_post_meta = new Postmeta();
          $meta_org = $organizer_post_meta->getOrganizerMeta($organizer->ID);
          $organizer_meta = array();
          foreach ($meta_org as $meta_key => $meta_value ) {
            $organizer_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
          }
          unset($meta_value);
          
          $organizerArr = array(
              'organizer_name'=>$organizer->post_title,  
              "organizer_phone" => (isset($organizer_meta['_OrganizerPhone'])) ? $organizer_meta['_OrganizerPhone'] : '',
              "organizer_email" => (isset($organizer_meta['_OrganizerEmail'])) ? $organizer_meta['_OrganizerEmail'] : ''
          );
      }
      
      //Venue Info
      $venueArr = array();
      if((isset($news_meta['_EventVenueID'])))
      {
          $post_type = 'tribe_venue';
          $post_status = "publish";
          $venue_post = new Post();
          $venue = $venue_post->getPostByID($news_meta['_EventVenueID'], $post_type, $post_status);
          
          //Get Post Meta
          $venue_post_meta = new Postmeta();
          $venue_meta_arr = $venue_post_meta->getVenueMeta($venue->ID);
          $venue_meta = array();
          foreach ($venue_meta_arr as $meta_key => $meta_value ) {
            $venue_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
          }
          unset($meta_value);
          
          //set address
          $address = "";
          if((isset($venue_meta['_VenueAddress'])))
              $address = $venue_meta['_VenueAddress'];
          
          //get location of venue
          $latitude = "";
          $longitude = "";
          if((isset($venue_meta['gmap'])))
          {
              
              $gmapArr = unserialize($venue_meta['gmap']);
              if(sizeof($gmapArr) == 3)
              {
                  if($address == "")
                      $address = $gmapArr["address"];
                  $latitude = $gmapArr["lat"];
                  $longitude = $gmapArr["lng"];                    
              }
          }
          
          $venueArr = array(
              'venue_name' => $venue->post_title,  
              "venue_address" => $address,
              "venue_city" => (isset($venue_meta['_VenueCity'])) ? $venue_meta['_VenueCity'] : '',
              "venue_country" => (isset($venue_meta['_VenueCountry'])) ? $venue_meta['_VenueCountry'] : '',
              "venue_phone" => (isset($venue_meta['_VenuePhone'])) ? $venue_meta['_VenuePhone'] : '',
              "venue_latitude" => $latitude,
              "venue_longitude" => $longitude
          );
      }
      if(!isset($news_meta['technology']))
      {
        $news_meta['technology'] = '';
      }
      
      if(!isset($news_meta['platform']))
      {
        $news_meta['platform'] = '';
      }
      
      if(!isset($news_meta['interest']))
      {
        $news_meta['interest'] = '';
      }
      $translated_event_types = array();
      if($_GET['lang'] == "en"){
        global $events_types;
        $translated_event_types = $events_types;
      }else{
        global $ar_events_types;
        $translated_event_types = $ar_events_types;
      }
      $event_result = array(
          "event_id" => $event->ID,
          "event_title" => $event->post_title,
          "event_status" => $event->post_status,
          "post_url" => html_entity_decode($event->guid),
          "description" => ($event->post_content) ? $event->post_content : '',
          "event_start_date_time" => (isset($news_meta['_EventStartDate'])) ? $news_meta['_EventStartDate'] : '',
          "event_end_date_time" => (isset($news_meta['_EventEndDate'])) ? $news_meta['_EventEndDate'] : '',
          "event_url" => (isset($news_meta['_EventURL'])) ? $news_meta['_EventURL'] : '',
          "event_currency" => (isset($news_meta['_EventCurrencySymbol'])) ? $news_meta['_EventCurrencySymbol'] : '',
          "event_cost" => (isset($news_meta['_EventCost'])) ? $news_meta['_EventCost'] : '',
          "event_audience" => (isset($news_meta['audience'])) ? $news_meta['audience'] : '',
          "event_objectives" => (isset($news_meta['objectives'])) ? $news_meta['objectives'] : '',
          "event_prerequisites" => (isset($news_meta['prerequisites'])) ? $news_meta['prerequisites'] : '',
          "event_functionality" => (isset($news_meta['functionality'])) ? $news_meta['functionality'] : '',
          "event_type" => (isset($news_meta['event_type'])) ? $translated_event_types[$news_meta['event_type']] : '',
          "added_by" => $this->return_user_info_list($event->post_author),
          "event_theme" => $theme,
          "event_technology" => self::retrieveTermsArr($news_meta['technology']),
          "event_platform" => self::retrieveTermsArr($news_meta['platform']),
          "event_interest" => self::retrieveTermsArr($news_meta['interest']),
          "organizer" => $organizerArr,
          "venue" => $venueArr
      );
      
      unset($event);
      return $this->renderJson($response, 200, $event_result);
    }

    return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Event ID"));
  }
    
    public function retrieveTermsArr( $post_meta ) {
        if(isset($post_meta) && $post_meta != '')
        {
            $returnArr = array();
            $dataArr = unserialize($post_meta);
            for($i =0; $i < sizeof($dataArr); $i++)
            {
              $term = new Term();
              $term_obj = $term->getTerm( $dataArr[ $i ] );

              $returnArr[] = $term_obj->name;
            }

            return $returnArr;
        }

        return [];
    }
    
  /**
   * @SWG\Post(
   *   path="/event/{eventId}/comments",
   *   tags={"Events"},
   *   summary="Creates Event Comment",
   *   description=" Add event comment to the event with the passed Id",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="eventId", in="formData", required=false, type="integer", description="Event ID to post the comment on <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Comment added successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Event not found")
   * )
   */
  public function addEventComments($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) 
      {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else 
      {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {
          $meta['comment'] = $_POST['comment'];
        }
        else
        {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment"));
          return $result;
        }
        
        if(isset($_POST['eventId']) || !empty($_POST['eventId']) )
        {
          if (is_numeric($_POST['eventId'])) {
            //we need to check if this post_name is in db or not
            $post = new Post();
            $post_type = 'tribe_events';
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['eventId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['eventId'] = $_POST['eventId'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Event ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event ID")); }
        }
        else
        {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Event ID"));
        }
        
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['comment']);
          $comment->save();
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Comment added");
            $output['comment_id'] = $comment->comment_ID;
            $result =  $this->renderJson($response, 200, $output);
          }
        }
      }
    }
    else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
    
  /**
   * @SWG\Post(
   *   path="/event/{eventId}/comments/{commentId}/replies",
   *   tags={"Events"},
   *   summary="Creates Reply On Event Comment",
   *   description="Add reply on the passed comment on the passed event",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="eventId", in="formData", required=false, type="integer", description="Event ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="commentId", in="formData", required=false, type="integer", description="Comment ID to post the reply on <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Add reply to Comment")
   * )
   */
  public function addReplyToComment($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) 
      {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else 
      {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {
          $meta['comment'] = $_POST['comment'];
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment Reply"));
          return $result;
        }

        if(isset($_POST['eventId']) || !empty($_POST['eventId']) ){
          if (is_numeric($_POST['eventId'])) {
            // we need to check if this post id is in db or not
            $post = new Post();
            $post_type = 'tribe_events';
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['eventId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['eventId'] = $_POST['eventId'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Event ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Event ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
            // we need to check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
                if (!empty($comment_exists) && ( $meta['eventId'] == $comment_exists['comment_post_ID']) ) {
                    if( empty( $comment_exists->comment_parent ) ) {
                          $meta['commentId'] = $_POST['commentId'];
                    }
                    else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
                }
                else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this event or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['eventId']);
          $comment->save();
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Reply to Comment added");
            $output['comment_id'] = $comment->comment_ID;
            $result =  $this->renderJson($response, 200, $output);
          }
        }
      }
    }
    else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/event/{eventId}/comments",
   *   tags={"Events"},
   *   summary="List Replies On Event Comment",
   *   description="list replies on the passed event",
   *   @SWG\Parameter(name="eventId", in="path", required=false, type="integer", description="Event ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to an Event"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listEventComments($request, $response, $args) {
    $eventComments = new Comment();
    if(!isset($args["eventId"]) || $args["eventId"] == "" || $args["eventId"] == "{eventId}") 
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }   
  
    $parameters = ['pageNumber', 'numberOfComments'];
    $requiredParams = ['pageNumber', 'numberOfComments'];
    $numeric_params = ['pageNumber', 'numberOfComments'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(empty($args['numberOfComments'] ) || $args['numberOfComments'] < 1 || $args['numberOfComments'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of commments',array("range"=> "1 and 25 ")));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    //List Event Comments
    $take = $args['numberOfComments'];
    $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
    $comments = $eventComments->getCommentByPostID($args["eventId"], $take, $skip);
    $hasMoreComments = new Comment();
    if(sizeof($comments) > 0)
    {
        $result = array();
        foreach ($comments as $comment) 
        { 
          $item = array(
              'parent_comment_id' => $comment->comment_parent,
              'comment_id' => $comment->comment_ID,
              "added_by" => $this->return_user_info_list($comment->user_id),
              'comment' => $comment->comment_content,
              'comment date' => $comment->comment_date,
              'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
              'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, 0)
          );  
                   
          array_push($result, $item);
        }
        
        $comments = count($eventComments->getCommentByPostID($args["eventId"], -1, -1));
        $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
        $resulsts_final['data'] = $result;
        
        return $this->renderJson($response, 200, $resulsts_final); 
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
  /**
   * @SWG\GET(
   *   path="/event/comments/{commentId}/replies",
   *   tags={"Events"},
   *   summary="List Replies On a Comment",
   *   description="list replies on the passed comment",
   *   @SWG\Parameter(name="commentId", in="path", required=false, type="integer", description="Comment ID to list the replies related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfReplies", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a comment"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listRepliesonAComment($request, $response, $args) {
    $Comments = new Comment();
    if(!isset($args["commentId"]) || $args["commentId"] == "" || $args["commentId"] == "{commentId}") 
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }   
  
    $parameters = ['pageNumber', 'numberOfReplies'];
    $requiredParams = ['pageNumber', 'numberOfReplies'];
    $numeric_params = ['pageNumber', 'numberOfReplies'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(empty($args['numberOfReplies'] ) || $args['numberOfReplies'] < 1 || $args['numberOfReplies'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of replies',array("range"=> "1 and 25 ")));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    //List Replies on Comment
    $take = $args['numberOfReplies'];
    $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfReplies'])-$args['numberOfReplies']:0;
    $comments = $Comments->getCommentByCommentID($args["commentId"], $take, $skip);
    $hasMoreComments = new Comment();
    if(sizeof($comments) > 0)
    {
        $result = array();
        foreach ($comments as $comment) 
        { 
          $item = array(
              'parent_comment_id' => $comment->comment_parent,
              'comment_id' => $comment->comment_ID,
              "added_by" => $this->return_user_info_list($comment->user_id),
              'comment' => $comment->comment_content,
              'comment date' => $comment->comment_date,
              'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
              'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, 0)
          );  
                   
          array_push($result, $item);
        }
        
        $comments_count = count($Comments->getCommentByCommentID($args["commentId"], -1, -1));
        $resulsts_final = $this->ef_load_data_counts($comments_count, $args['numberOfReplies']);
        $resulsts_final['data'] = $result;

        return $this->renderJson($response, 200, $resulsts_final);
    }
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
  //recursively list comments on comments
  public function listCommentsonComments($comment_id, $take, $skip) {
      $eventComments = new Comment();
      $comments = $eventComments->getCommentByCommentID($comment_id, $take, $skip);
      $hasMoreComments = new Comment();
      if(sizeof($comments) > 0) {
          $result = array();
          foreach ($comments as $comment) 
          { 
            $item = array(
                'parent_comment_id' => $comment_id,
                'comment_id' => $comment->comment_ID,
                "added_by" => $this->return_user_info_list($comment->user_id),
                'comment' => $comment->comment_content,
                'comment date' => $comment->comment_date,
                'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
                'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, $skip)
            );  
                     
            array_push($result, $item);
          }
          
          return $result;
      }
      
      return [];
  }

  /**
   * @SWG\Put(
   *   path="/events/{event_id}",
   *   tags={"Events"},
   *   summary="Edits Event",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to edit an event<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="event_id", in="path", required=false, type="string", description="Event ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Event Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Event Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="event_type", in="formData", required=false, type="string", description="Event Type <br/><b>Validations: </b><br/> 1. Predefined Event's type in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="start_datetime", in="formData", required=false, type="string", description="Event Start Date (Data/time format should be 2016-03-27 00:00:00)<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="end_datetime", in="formData", required=false, type="string", description="Event End Date (Data/time format should be 2016-03-27 00:00:00)<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="venue", in="formData", required=false, type="string", description="Venue ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="organizer", in="formData", required=false, type="string", description="Organizer ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="event_website", in="formData", required=false, type="string", description="Event Website <br/><b>Validations</b><br/>1. Valid URL"),
   *   @SWG\Parameter(name="currency", in="formData", required=false, type="string", description="Event Currency <br/> <b>Values:</b> <br/> 1.EGP<br/>2.USB<br/>3.EUR"),
   *   @SWG\Parameter(name="cost", in="formData", required=false, type="string", description="Event Cost <br/><b>Validations: </b><br/> 1. Numeric Value<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="audience", in="formData", required=false, type="string", description="Event Audience <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="objectives", in="formData", required=false, type="string", description="Event Objectives <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="prerequisites", in="formData", required=false, type="string", description="Event Prerequisites <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="functionality", in="formData", required=false, type="string", description="Event Functionality <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Event Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Event Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="platform", in="formData", required=false, type="string", description="Event Platforms <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Event Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Response(response="200", description="Edit user profile")
   * )
   */
  public function editEvent($request, $response, $args) {
    $put = $request->getParsedBody();
    global $events_types;
    global $system_currencies;
    $parameters = ['token', 'title', 'description', 'event_type', 'start_datetime', 'end_datetime', 
                   'venue', 'organizer', 'event_website', 'currency', 
                   'cost', 'audience', 'objectives', 'prerequisites', 'functionality', 
                   'theme', 'platform', 'technology', 'interest'];

    foreach($parameters as $parameter){
      $args[$parameter] = (array_key_exists($parameter, $put)) ? $put[$parameter] : '';
    }
    $result = "";
    $loggedin_user = AccessToken::where('access_token', '=', $args['token'])->first();
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      } else {
        $event_id = $args['event_id'];
        $event = Post::Where('post_type', '=', 'tribe_events')->where('ID', '=', $event_id)->first();
        $event_venue = EventVenue::where('event_id' , '=', $event_id)->first();
        if( $event === null ) {
          $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Event"));
        } else if( ($user_id != $event->post_author) || ($event->post_status != 'pending') || ($event_venue->event_start_datetime < date('Y-m-d H:i:s')) ) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized", ", User can only edit his/her under review pending events"));
        } else {
          // validate data
          $term_taxonomy = $this->check_term_exists($args['theme'], 'theme');
          $term_taxonomy_platform = $this->check_term_exists($args['platform'], 'platform');
          if( empty($args['title']) || empty($args['description']) || empty($args['start_datetime']) || empty($args['end_datetime']) || empty($args['event_type']) ){
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
          } else if(mb_strlen($args["title"], 'UTF-8') < 2 || mb_strlen($args["title"], 'UTF-8') > 100){
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between", ", Event title",array("range"=>'2 to 100 characters')));
          } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['title'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", ", Event title must at least contain one letter"));
          } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['description'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", ", Event description must at least contain one letter"));
          } else if( $args['start_datetime'] != date('Y-m-d H:i:s',strtotime($args['start_datetime'])) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Start Datetime"));
          } else if( $args['start_datetime'] < date('Y-m-d H:i:s') ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Start Datetime"));
          } else if( $args['end_datetime'] != date('Y-m-d H:i:s',strtotime($args['end_datetime'])) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "End Datetime"));
          } else if( $args['end_datetime'] < $args['start_datetime'] ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "End Datetime"));
          } else if (!empty($args['event_website']) && filter_var($args['event_website'], FILTER_VALIDATE_URL) === false) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event Website"));
          } else if (!empty($args['cost']) && (!ctype_digit($args['cost']) || ($args['cost'] < 0))) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Cost"));
          } else if (!empty($args['audience']) && !preg_match('/[أ-يa-zA-Z]+/', $args['audience'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Audience"));
          } else if (!empty($args['objectives']) && !preg_match('/[أ-يa-zA-Z]+/', $args['objectives'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Objectives"));
          } else if (!empty($args['prerequisites']) && !preg_match('/[أ-يa-zA-Z]+/', $args['prerequisites'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Prerequisites"));
          } else if (!empty($args['functionality']) && !preg_match('/[أ-يa-zA-Z]+/', $args['functionality'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Functionality"));
          } else if (!array_key_exists($args['event_type'], $events_types)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event Type"));
          } else if (!empty($args['currency']) && !array_key_exists($args['currency'], $system_currencies)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Currency"));
          } else if (!empty($args['theme']) && empty($term_taxonomy)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Theme"));
          } else if( (Post::where('ID' , '=', $args['venue'])->where('post_type' , '=', 'tribe_venue')->first()) == null ) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Venue"));
          } else if( (Post::where('ID' , '=', $args['organizer'])->where('post_type' , '=', 'tribe_organizer')->first()) == null ) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Organizer"));
          } else if(!empty($args['platform']) && empty($term_taxonomy_platform)){
              $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Platform"));
          } else {
            // validate taxonomies
            //$taxonomies = ['platform', 'technology', 'interest'];
            $taxonomies = ['technology', 'interest'];
            foreach ($taxonomies as $taxonomy) {
              $terms = trim($args[$taxonomy],',');
              $terms = str_getcsv($terms);
              foreach ($terms as $term) {
                if (!empty($term) && !preg_match('/[أ-يa-zA-Z]+/', $term, $matches)) {
                  return $this->renderJson($response, 422, Messages::getErrorMessage("formatError", ucwords($taxonomy)));
                }
              }
            }

            $venue = Post::where('ID' , '=', $args['venue'])->where('post_type' , '=', 'tribe_venue')->first();
            
            $event_array = array(
              'post_title'  => $args['title'],
              'post_content' => $args['description'],
            );
            Post::where('ID', '=', $event_id)->update($event_array);

            $start_timestamp = strtotime( $args['start_datetime'] );
            $end_timestamp   = strtotime( $args['end_datetime'] );
            if ( $start_timestamp > $end_timestamp ) {
              $args['end_datetime'] = $args['start_timestamp'];
            }
            $duration = strtotime( $args['end_datetime'] ) - $start_timestamp;

            // save event venue record
            $event_venue = EventVenue::where('event_id' , '=', $event_id)->first();
            $event_venue->venue_id = $args['venue'];
            $event_venue->venue_name = $venue->post_title;
            $event_venue->event_start_datetime = $args['start_datetime'];
            $event_venue->event_end_datetime = $args['end_datetime'];
            $event_venue->save();

            $event_meta = array(
              '_EventOrganizerID' => $args['organizer'],
              'event_type' => $args['event_type'],
              '_EventURL' => $args['event_website'],
              '_EventVenueID' => $args['venue'],
              '_EventStartDate' => $args['start_datetime'],
              '_EventEndDate' => $args['end_datetime'],
              '_EventStartDateUTC' => $args['start_datetime'],
              '_EventEndDateUTC' => $args['end_datetime'],
              '_EventDuration' => $duration,
              'audience' => $args['audience'],
              'objectives' => $args['objectives'],
              'prerequisites' => $args['prerequisites'],
              '_EventCurrencySymbol' => $args['currency'],
              '_EventCost' => $args['cost'],
              'functionality' => $args['functionality'],
            );
            foreach ($event_meta as $name => $value) {
              $meta = Postmeta::where('post_id' , '=', $event_id)->where('meta_key' , '=', $name)->first();
              $meta->meta_value = $value;
              $meta->save();
            }

            $termTax = new TermTaxonomy();
            $terms_data = array(
              "theme" => array($args['theme']),
              "technology" => split(",",$args['technology']), 
              "platform" => split(",",$args['platform']),
              "interest" => split(",",$args['interest'])
            );
            $isCreated = $termTax->saveTermTaxonomies($terms_data, array("theme", "platform"));
            if(!$isCreated){
              $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
            } else {
              $event->updatePostTerms($event_id, $terms_data, true);
              $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Event ".$args['title']." Edited"));
            }
          }
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  //conditions to meet wp post name criteria
  public static function generatePostName($name) {
      $name = str_replace(".", "-", $name);
      $name = str_replace("(", "", $name);
      $name = str_replace(")", "", $name);
      $name = str_replace("*", "", $name);
      $name = str_replace(":", "", $name);
      $name = str_replace("'", "", $name);
      $name = str_replace("+", "", $name);
      $name = str_replace("/", "", $name);
      $name = str_replace("~", "", $name);
      $name = preg_replace("/[\/\&%#\$]/", "", $name);
      //$name = preg_replace('/[^أ-يA-Za-z0-9\-]/', '', $name);
      $name = preg_replace('/-+/', '-', $name);
      $name = rtrim($name, '-');
      return $name;
  }
}
