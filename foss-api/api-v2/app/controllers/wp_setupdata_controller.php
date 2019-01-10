<?php

class WPSetupDataController extends EgyptFOSSController {
  
  /**
   * @SWG\GET(
   *   path="/setupdata/industry",
   *   tags={"SetupData"},
   *   summary="Finds System Industries",
   *   description="List system industries with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Industries"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listIndustries($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of industries',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('industry', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('industry', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        $data = array();
        
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            if( empty( $term->parent ) ) {
              $data[ $term->term_id ] =
                array( 'name' => array(
                  "en" => html_entity_decode( $term->name ),
                  "ar" => html_entity_decode( $term->name_ar )
                ) );
            }
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            if( empty( $term->parent ) ) {
              $name = html_entity_decode( $term->name );
              $name_ar = html_entity_decode( $term->name_ar );
              if( $args['lang'] == 'en' ) {
                $unsorted_terms[ $term->term_id ] = $name;
              }
              else {
                $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
              }
            }
          }
          natcasesort( $unsorted_terms );
          
          $data = $unsorted_terms;
        }
        
        foreach( $data as $key => $value ) {
          $data[ $key ] = array( 'name' => $value );
          $data[ $key ][ 'subs' ] = array();
          foreach( $list as $term ) {
            
            if( $term->parent == $key ) {
              $data[ $key ][ 'subs' ][] = array( 'id' => $term->term_id, 'name' => ($args['lang'] == 'en')?$term->name:$term->name_ar );
            }
          }
        }
        
        foreach( $data as $key => $value ) {
          $sorted_terms[] = array( $key  => $value );
        }
        $returned['data'] = $sorted_terms;
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/theme",
   *   tags={"SetupData"},
   *   summary="Finds System Themes",
   *   description="List system Themes with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Themes"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listThemes($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of themes',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('theme', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        $total_count = count($term->loadTaxonomyByTaxonomyType('theme', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
   /**
   * @SWG\GET(
   *   path="/setupdata/technology",
   *   tags={"SetupData"},
   *   summary="Finds System Technologies with ability to paginate or list all data",
   *   description="List system technologies with pagination",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Response(response="200", description="View Technologies"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  
  public function listTechnologies($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of technologies',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }        
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('technology', $args['numberOfData'], $offset, false);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('technology', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        
        $unsorted_terms = $sorted_terms = array();
        foreach( $list as $term ) {
          $unsorted_terms[ $term->term_id ] = html_entity_decode( $term->name );
        }

        natcasesort( $unsorted_terms );
        foreach( $unsorted_terms as $key => $value ) {
            $sorted_terms[] = array( $key  => $value );
        }

        $returned['data'] = $sorted_terms;
        
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
   /**
   * @SWG\GET(
   *   path="/setupdata/license",
   *   tags={"SetupData"},
   *   summary="Finds System Licenses",
   *   description="List system licenses with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Licenses"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  
  public function listLicences($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData', 'lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of licenses',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }              
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('license', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        $total_count = count($term->loadTaxonomyByTaxonomyType('license', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array(
                $key   => $value
              );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
   /**
   * @SWG\GET(
   *   path="/setupdata/platform",
   *   tags={"SetupData"},
   *   summary="Finds System Platforms",
   *   description="List system platforms with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Platforms"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  
  public function listPlatforms($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData', 'lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Platforms',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }                     
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('platform', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        $total_count = count($term->loadTaxonomyByTaxonomyType('platform', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array(
                $key   => $value
              );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
    
  /**
   * @SWG\GET(
   *   path="/setupdata/subtypes",
   *   tags={"SetupData"},
   *   summary="Sub Types",
   *   description="Get Sub-types Data by language and type",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", enum={"Individual", "Entity", "Event"}, description="data retrieved depeding on type <br/> <b>Values: </b> [Individual, Entity, Event]"),
   *   @SWG\Response(response="200", description="System Data")
   * )
   */
  public function listSubTypes($request, $response, $args) {
    global $account_types;
		global $account_sub_types;
    $result = array();
    $parameters = ['lang', 'type'];
    $requiredParams = ['lang', 'type'];
    $numeric_params = [];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!isset($args["lang"])) {
      $args["lang"] = '';
    }
    
    if($args["lang"] == 'ar') {
      global $ar_sub_types;
      global $ar_events_types;
      $sub_types = $ar_sub_types;
      if (!empty($args['type'])){
        if ($args['type'] == 'Event'){
          natcasesort( $ar_events_types );
          $result = $ar_events_types;
        }
        else{
          $res = array();
          foreach ($account_sub_types as $sub => $t) {
            if ($args['type'] == $t){
              $res[$sub] = html_entity_decode($sub_types[$sub]);
            }
          }
          natcasesort( $res );
          $result[$args['type']] = $res;
        }
      }
      else{ // empty sub-type
        $res_individual = array();
        $res_entity = array();
        foreach ($account_sub_types as $sub => $t) {
          if ('Individual' == $t){
            $res_individual[$sub] = html_entity_decode($sub_types[$sub]);
          }
          if('Entity' == $t){
            $res_entity[$sub] = html_entity_decode($sub_types[$sub]);
          }
        }
        
        natcasesort( $res_individual );
        natcasesort( $res_entity );
        natcasesort( $ar_events_types );
        
        $result['Individual'] = $res_individual;
        $result['Entity'] = $res_entity;
        $result['Event'] =  $ar_events_types;
      }
    } else if($args["lang"] == 'en') {  // lang is english
      global $en_sub_types;
      global $events_types;
      $sub_types = $en_sub_types;
      if (!empty($args['type'])){
        if ($args['type'] == 'Event'){
          natcasesort( $events_types );
          $result = $events_types;
        }
        else{
          $res = array();
          foreach ($account_sub_types as $sub => $t) {
            if ($args['type'] == $t){
              $res[$sub] = html_entity_decode($sub_types[$sub]);
            }
          }
          
          natcasesort( $res );
          
          $result[$args['type']] = $res;
        }
      }
      else{ // empty sub-type
        $res_individual = array();
        $res_entity = array();
        foreach ($account_sub_types as $sub => $t) {
          if ('Individual' == $t){
            $res_individual[$sub] = html_entity_decode($sub_types[$sub]);
          }
          if('Entity' == $t){
            $res_entity[$sub] = html_entity_decode($sub_types[$sub]);
          }
        }
        
        natcasesort( $res_individual );
        natcasesort( $res_entity );
        natcasesort( $events_types );
        
        $result['Individual'] = $res_individual;
        $result['Entity'] = $res_entity;
        $result['Event'] = $events_types;
      }
    } else {
      global $ar_sub_types;
      global $ar_events_types;
      global $en_sub_types;
      global $events_types;
      $sub_types_en = $en_sub_types;
      $sub_types = $ar_sub_types;
      if (!empty($args['type'])){
        if ($args['type'] == 'Event'){
          $res = array();
          foreach ($ar_events_types as $sub => $t) {
              $res[$sub] = array(
                  "en" => html_entity_decode($events_types[$sub]),
                  "ar" => html_entity_decode($ar_events_types[$sub])
              );
            
          }
          $result[$args['type']] = $res;
        }
        else{
          $res = array();
          foreach ($account_sub_types as $sub => $t) {
            if ($args['type'] == $t){
              $res[$sub] = array(
                  "en" => html_entity_decode($sub_types_en[$sub]),
                  "ar" => html_entity_decode($sub_types[$sub])
              );
            }
          }
          $result[$args['type']] = $res;
        }
      }
      else{ // empty sub-type
        $res_individual = array();
        $res_entity = array();
        foreach ($account_sub_types as $sub => $t) {
          if ('Individual' == $t){
            $res_individual[$sub] = array(
                "en" => html_entity_decode($sub_types_en[$sub]),
                "ar" => html_entity_decode($sub_types[$sub])
            );
          }
          if('Entity' == $t){
            $res_entity[$sub] = array(
                "en" => html_entity_decode($sub_types_en[$sub]),
                "ar" => html_entity_decode($sub_types[$sub])
            );
          }
        }
        $result['Individual'] = $res_individual ;
        $result['Entity'] = $res_entity ;
        //$result['Event'] = $ar_events_types;
        $res = array();
        foreach ($ar_events_types as $sub => $t) {
            $res[$sub] = array(
                "en" => html_entity_decode($events_types[$sub]),
                "ar" => html_entity_decode($ar_events_types[$sub])
            );

        }
        $result['Event'] = $res;
      }
    }
    return $this->renderJson($response, 200, $result);
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/open-dataset/type",
   *   tags={"SetupData"},
   *   summary="Finds System Open Dataset Types",
   *   description="List system open dataset types with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Open Dataset Types"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listOpenDatasetType($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Open Dataset Types',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('dataset_type', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        $total_count = count($term->loadTaxonomyByTaxonomyType('dataset_type', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }

  /**
   * @SWG\GET(
   *   path="/setupdata/open-dataset/license",
   *   tags={"SetupData"},
   *   summary="Finds System Open Dataset License",
   *   description="List system open dataset licenses with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Open Dataset Licenses"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listOpenDatasetLicense($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Open Dataset Licenses',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('datasets_license', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        $total_count = count($term->loadTaxonomyByTaxonomyType('datasets_license', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }  
  
  /**
   * @SWG\GET(
   *   path="/setupdata/interests",
   *   tags={"SetupData"},
   *   summary="Finds System Interests",
   *   description="List system interests with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Response(response="200", description="View Interests"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listInterests($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Interests',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }                     
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('interest', $args['numberOfData'], $offset, false);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('interest', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        $unsorted_terms = $sorted_terms = array();
        foreach( $list as $term ) {
          $unsorted_terms[ $term->term_id ] = html_entity_decode( $term->name );
        }

        natcasesort( $unsorted_terms );
        foreach( $unsorted_terms as $key => $value ) {
            $sorted_terms[] = array( $key  => $value );
        }

        $returned['data'] = $sorted_terms;
        
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/request-center/type",
   *   tags={"SetupData"},
   *   summary="Finds System request center types",
   *   description="List system request center types with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Request Center Types"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listRequestCenterTypes($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Request Center Types',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('request_center_type', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        $total_count = count($term->loadTaxonomyByTaxonomyType('request_center_type', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        $unsorted_array = $sorted_array = array();
        for($i = 0; $i < sizeof($list); $i++)
        {
            $enName = $list[$i]->name;
            $arName = ($list[$i]->name_ar)?$list[$i]->name_ar:$enName;
         
            if( !isset($args['lang']) || $args['lang'] == 'ar') {
              $unsorted_array[html_entity_decode($arName)] = $list[$i];
            }
            if( !isset($args['lang']) || $args['lang'] == 'en') {
              $unsorted_array[html_entity_decode($enName)] = $list[$i];
            }
        }
       
        ksort( $unsorted_array );
        
        foreach( $unsorted_array as $name => $term ) {
          
          $option = new Option();
          $site_url     = $option->getOptionValueByKey('siteurl');
          $active_theme = $option->getOptionValueByKey('stylesheet');
          
          $itemArray[$args['lang']] = $name;
          $itemArray['thumbnail_id'] = "{$site_url}/wp-content/themes/{$active_theme}/img/{$term->slug}_icon.svg";

          $returned['data'][] = array( $term->term_id => $itemArray );
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/request-center/target-relationships",
   *   tags={"SetupData"},
   *   summary="Finds System request center target relationships",
   *   description="List system request center target relationships with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Request Center Target relationships"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listRequestCenterBussinessRelationships($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Request Center Target relationships',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('target_bussiness_relationship', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        $total_count = count($term->loadTaxonomyByTaxonomyType('target_bussiness_relationship', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/news/category",
   *   tags={"SetupData"},
   *   summary="Finds System News Categories",
   *   description="List system news categories with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View News Categories"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listNewsCategories($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of news categories',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('news_category', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('news_category', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/success-story/category",
   *   tags={"SetupData"},
   *   summary="Finds System Success Stories Categories",
   *   description="List system success stories categories with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Success Stories Categories"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listSuccessStoriesCategories($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of success stories categories',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('success_story_category', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('success_story_category', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }

  /**
   * @SWG\GET(
   *   path="/setupdata/services/category",
   *   tags={"SetupData"},
   *   summary="Finds System Services Categories",
   *   description="List system services categories with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Services Categories"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listServicesCategories($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of services categories',array("range"=> "1 and 25 ")));
    } else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    } else if(!empty($args['pageNumber']) && empty($args['numberOfData'])) {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    } else {
      $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
      if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0) {
        $offset = -1;
      }
      if(!array_key_exists('numberOfData', $args)) {
        $args['numberOfData'] = -1;
      } else if($args['numberOfData'] == '' || $args['numberOfData'] == 0) {
        $args['numberOfData'] = -1;
      }
      $term = new Term();
      $list = $term->loadTaxonomyByTaxonomyType('service_category', $args['numberOfData'], $offset, true);
      if(sizeof($list) == 0) {
        return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }        
      $total_count = count($term->loadTaxonomyByTaxonomyType('service_category', -1, -1, true));
      $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
      if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
   /**
   * @SWG\GET(
   *   path="/setupdata/sections",
   *   tags={"SetupData"},
   *   summary="Finds System sections",
   *   description="List system sections",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View system sections"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listSystemSections($request, $response, $args) {
    if( isset( $_GET[ 'lang' ] ) ) {
      $args[ 'lang' ] = $_GET[ 'lang' ];
    }
    else {
      $args[ 'lang' ] = '';
    }
    
    if ( !empty( $args["lang"] ) && $args["lang"] != "en" && $args["lang"] != "ar" ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "lang"));
    }
    
    global $ef_sections;
    
    $en_sections = $ar_sections = $mix_sections = array();
    foreach ( $ef_sections as $key => $section ) {
      $ar_sections[ $key ] = __( $section, "egyptfoss" );
      $en_sections[ $key ] = $section;
      $mix_sections[ $key ] = array(
        'en'  => $section,
        'ar'  => __( $section, 'egyptfoss' )
      );
    }
    
    natcasesort($en_sections);
    natcasesort($ar_sections);

    if(!isset($args['lang'])) {
      $args['lang'] = '';
    }

    if($args['lang'] == "ar") {
        $returned['data'] = $ar_sections;
    }
    else if($args['lang'] == "en"){
      $returned['data'] = $en_sections;
    }
    else {
      $returned['data'] = $mix_sections;
    }

    $result = $this->renderJson($response, 200, $returned);
    
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/quiz/category",
   *   tags={"SetupData"},
   *   summary="Finds System Quiz Categories",
   *   description="List system quiz categories with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Quiz Categories"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listQuizCategories($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of categories',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('quiz_categories', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('quiz_categories', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  public function retrieveDecodedTermName($lang,$item,$returned)
  {
//    if (!isset($lang)) {
//      $lang = '';
//    }
//    if ($lang == "ar") {
//      if ($item->name_ar == null) {
//        $returned['data'][$item->term_id] = html_entity_decode($item->name);
//      } else {
//        $returned['data'][$item->term_id] = html_entity_decode($item->name_ar);
//      }
//    } else if ($lang == "en") {
//      $returned['data'][$item->term_id] = html_entity_decode($item->name);
//    } else {
//      $returned['data'][$item->term_id] = 
//    }
//    return $returned;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/type",
   *   tags={"SetupData"},
   *   summary="Finds System Types",
   *   description="List system types with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Industries"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listTypes($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','lang'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of types',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }
        $term = new Term();
        $list = $term->loadTaxonomyByTaxonomyType('type', $args['numberOfData'], $offset, true);
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }        
        
        $total_count = count($term->loadTaxonomyByTaxonomyType('type', -1, -1, true));
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        if( empty( $args['lang'] ) ) {
          foreach( $list as $term ) {
            $returned['data'][ $term->term_id ] =
              array(
                "en" => html_entity_decode( $term->name ),
                "ar" => html_entity_decode( $term->name_ar )
              );
          }
        }
        else {
          $unsorted_terms = $sorted_terms = array();
          foreach( $list as $term ) {
            $name = html_entity_decode( $term->name );
            $name_ar = html_entity_decode( $term->name_ar );
            if( $args['lang'] == 'en' ) {
              $unsorted_terms[ $term->term_id ] = $name;
            }
            else {
              $unsorted_terms[ $term->term_id ] = empty( $name_ar )?$name:$name_ar;
            }
          }
   
          natcasesort( $unsorted_terms );
          foreach( $unsorted_terms as $key => $value ) {
              $sorted_terms[] = array( $key  => $value );
          }
          
          $returned['data'] = $sorted_terms;
        }
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/setupdata/event/venues",
   *   tags={"SetupData"},
   *   summary="Finds Event Venues",
   *   description="List event venues with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Response(response="200", description="View event venues"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listEventVenues($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of venues',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }                  
        $args["post_type"] = "tribe_venue";
        $args["post_status"] = "publish";
        $args['numberOfResults'] = $args['numberOfData'];
        $args["skip"] = $offset;
        $post = new Post();
        $list = $post->loadEventData($args)->get();
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        //load total count
        $args['numberOfResults'] = -1;
        $args["skip"] = -1;
        $total_count = count($post->loadEventData($args)->get());
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        
        $unsorted_terms = $sorted_terms = array();
        foreach( $list as $term ) {
          $unsorted_terms[ $term->ID ] = html_entity_decode( $term->post_title );
        }

        natcasesort( $unsorted_terms );
        foreach( $unsorted_terms as $key => $value ) {
            $sorted_terms[] = array( $key  => $value );
        }

        $returned['data'] = $sorted_terms;
        
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
  
   /**
   * @SWG\GET(
   *   path="/setupdata/event/organizers",
   *   tags={"SetupData"},
   *   summary="Finds Event Organizers",
   *   description="List event organizers with ability to paginate or list all data",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Response(response="200", description="View event organizers"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listEventOrganizers($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of organizers',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
        $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
        if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
        {
          $offset = -1;
        }
        if(!array_key_exists('numberOfData', $args))
        {
          $args['numberOfData'] = -1;
        }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
        {
          $args['numberOfData'] = -1;
        }                  
        $args["post_type"] = "tribe_organizer";
        $args["post_status"] = "publish";
        $args['numberOfResults'] = $args['numberOfData'];
        $args["skip"] = $offset;
        $post = new Post();
        $list = $post->loadEventData($args)->get();
        if(sizeof($list) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        //load total count
        $args['numberOfResults'] = -1;
        $args["skip"] = -1;
        $total_count = count($post->loadEventData($args)->get());
        $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
        $unsorted_terms = $sorted_terms = array();
        foreach( $list as $term ) {
          $unsorted_terms[ $term->ID ] = html_entity_decode( $term->post_title );
        }

        natcasesort( $unsorted_terms );
        foreach( $unsorted_terms as $key => $value ) {
            $sorted_terms[] = array( $key  => $value );
        }

        $returned['data'] = $sorted_terms;
        
        $result = $this->renderJson($response, 200, $returned);
    }
    return $result;
  }
}