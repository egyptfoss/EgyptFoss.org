<?php
  class GeolocationsController extends EgyptFOSSController {
  /**
   * @SWG\Post(
   *   path="/locations",
   *   tags={"FOSS Map"},
   *   summary="Add or Update user location on FOSS Map",
   *   description="Saves the given Latitude and Longitude on success",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add/update location<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="formData", required=false, type="string", description="Type of the location<br/><b>Values:</b> user <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lat", in="formData", required=false, type="string", description="User Location lat<br/><b>Validations: </b><br/> 1. Numeric<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lng", in="formData", required=false, type="string", description="User Location lng<br/><b>Validations: </b><br/> 1. Numeric<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Edit user profile")
   * )
   */
	public function addLocation($request, $response, $args) {
    $parameters = ['token', 'type', 'lat', 'lng'];
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
          if( empty($args['type']) || empty($args['lat']) || empty($args['lng']) ){
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
          } else if ( !in_array($args['type'], array('user')) ) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Type"));
          } else if ( !is_numeric($args['lat']) || !is_numeric($args['lng']) ) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Co-ordinates"));
          } else {
            $location = GeoLocation::where('object_id' , '=', $user_id)->where('object_type' , '=', $args['type'])->first();
            if($location === null) {
              $location = new GeoLocation;
            }
            $location_array = array(
              'object_id' => $user_id,
              'object_type' => $args['type'],
              'lat' => $args['lat'],
              'lng' => $args['lng']
            );
            $location->addLocation($location_array);
            $location->save();
            $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "User Location Added"));
          }
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
	}

  /**
   * @SWG\GET(
   *   path="/location/me",
   *   tags={"FOSS Map"},
   *   summary="Retrieves the location of the user on FOSS Map",
   *   description="Returns the Latitude and Longitude on success",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to find user location<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Retrieve user location")
   * )
   */
  public function getLocation($request, $response, $args) {
    $result = "";
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        $location = GeoLocation::where('object_id' , '=', $user_id)->where('object_type' , '=', 'user')->first();
        $geo['lat'] = isset($location['lat']) ? $location['lat'] : '';
        $geo['lng'] = isset($location['lng']) ? $location['lng'] : '';
        $result = $this->renderJson($response, 200, $geo);
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/locations",
   *   tags={"FOSS Map"},
   *   summary="List All Locations",
   *   description="Return all locations in the system per type",
   *   consumes={"application/x-www-form-urlencoded"},
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", description="Type of the locations to retreive<br/> <b>Values: </b><br/> 1.Event<br/>2.Individual<br/>3.Entity<br/> default: Entity<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="badge", in="query", required=false, type="integer", description="Badge id<br/> <b>Values: </b> saved badges ids"),
   *   @SWG\Parameter(name="sub_type", in="query", required=false, type="string", description="Locations sub type based on the type selected<br/> <b>Values: </b> sub-types selected from setup data"),
   *   @SWG\Parameter(name="theme", in="query", required=false, type="string", description="Theme English or Arabic name"), 
   *   @SWG\Parameter(name="technology", in="query", required=false, type="string", description="Technology name"),
   *   @SWG\Parameter(name="interest", in="query", required=false, type="string", description="Interest name"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="list of locations"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listingLocations($request, $response, $args) {
    global $locations_types;
    global $events_types;
    global $ar_events_types;
    global $account_sub_types;
    global $en_sub_types;
    global $ar_sub_types;
    $result = "";
    $locations = array();
    $term_ids = array();
    $filter_condition = "";
    $having_condition = "";
    $parameters = ['type', 'sub_type', 'theme', 'technology', 'interest', 'lang', 'badge'];
    $taxonomies = ['theme', 'technology', 'interest'];
    $required_params = ['type', 'lang'];
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_GET) && !empty($_GET[$parameter]))
      {
        $args[$parameter] = $_GET[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if($_GET["lang"] != "en" && $_GET["lang"] != "ar") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("wrongValue", "lang"));
    }
    
    //trim filters
    $args['sub_type'] = htmlspecialchars_decode(trim($args['sub_type']));
    $args['theme'] = htmlspecialchars_decode(trim($args['theme']));
    $args['technology'] = htmlspecialchars_decode(trim($args['technology']));
    $args['interest'] = htmlspecialchars_decode(trim($args['interest']));
     
    //get type id
    if(isset($args["theme"]) && $args["theme"] != '')
    {
      $theme_id = $this->ef_retrieve_taxonomy_id('theme', $args["theme"]);
      if($theme_id != -1)
      {
        array_push($term_ids, $theme_id);
      }else
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","theme"));
      }
    } 

    //get theme id
    if(isset($args["technology"])  && $args["technology"] != '')
    {
      $tecnology_id = $this->ef_retrieve_taxonomy_id('technology', $args["technology"]);
      if($tecnology_id != -1)
      {
        array_push($term_ids, $tecnology_id);
      }else
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","technology"));
      }
    }          

    //get license id
    if(isset($args["interest"])  && $args["interest"] != '')
    {
      $interest_id = $this->ef_retrieve_taxonomy_id('interest', $args["interest"]);
      if($interest_id != -1)
      {
        array_push($term_ids, $interest_id);
      }else
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","interest"));
      }
    }            
    
    $args['type'] = (empty($args['type'])) ? 'Entity' : $args['type'];
    if (!in_array($args['type'], $locations_types)) {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Location Type"));
    } else if($args['type'] == 'Event') {
      //if (!empty($args['sub_type']) && !array_search($args['sub_type'], $events_types)) {
      if (!empty($args['sub_type']) && 
              (!in_array($args['sub_type'], $events_types) && !in_array($args['sub_type'], $ar_events_types))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Event Sub Type"));
      } else {
        $geo_locations = new GeoLocation();
        
        $results = $geo_locations->get_filtered_locations($args, $term_ids);
        //return $this->renderJson($response, 200, $loop_index);
        if(sizeof($results) == 0)
        {
          $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }else
        {
          $locations_arr = array();
          $loop_index = 0;
          foreach ($results as $result) {
            $index = $result->venue_id;
            $events = array(
              'event_id' => $result->ID,
              'event_title' => $result->post_title,
              'event_url' => $result->guid,
              'event_start' => $result->event_start_datetime,
              'event_end' => $result->event_end_datetime
            );
            if (array_key_exists($index, $locations)) {
              array_push($locations[$index]['events'], $events);
            } else {
              $locations[$index] = array(
                'id' => $index,
                'title' => $result->venue_name,
                'lat' => round($result->lat, 3),
                'lng' => round($result->lng, 3),
                'events' => array($events)
              );
            }
            
            $is_found = false;
            for($i = ($loop_index + 1); $i < sizeof($results); $i++)
            {
              if($loop_index < sizeof($results))
              {
                if($index == $results[$i]->venue_id)
                {
                  $is_found = true;
                }
              }
            }
            
            if(!$is_found)
            {
              array_push($locations_arr, $locations[$index]);
            }
            
            //increment loop
            $loop_index += 1;
          }
          $result = $this->renderJson($response, 200, $locations_arr);
        }
      }
    } else {
      //if (!empty($args['sub_type']) && !array_key_exists($args['sub_type'], $account_sub_types)) {
      if (!empty($args['sub_type']) && 
        (!in_array($args['sub_type'], $ar_sub_types) && !in_array($args['sub_type'], $en_sub_types))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Account Sub Type"));
      } else {
        $geo_locations = new GeoLocation;
        $results = $geo_locations->get_filtered_locations($args, $term_ids);
        if(sizeof($results) == 0)
        {
          $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }else 
        {
          foreach ($results as $result) {
            //$index = $result->ID;
            $userMeta = new Usermeta();
            $meta = $userMeta->getMeta($result->ID);
            if( $args['lang'] == 'ar' ) {
              $sub_type = $ar_sub_types[$meta['sub_type']];
            }
            else {
              $sub_type = $en_sub_types[$meta['sub_type']];
            }
            $locations[] = array(
              'display_name' => ($result->display_name == '' || 
                    $result->display_name == null)?$result->user_nicename:$result->display_name,
              'name' => $result->user_nicename,
              'lat' => round($result->lat, 3),
              'lng' => round($result->lng, 3),
              'sub_type' => $sub_type
              //'email' => $result->user_email,
            );
          }
          $result = $this->renderJson($response, 200, $locations);
        }
      }
    }
    return $result;
  }
}
