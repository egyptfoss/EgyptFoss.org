<?php

class SearchController extends EgyptFOSSController {
  
  /**
   * 
   * @param type $id
   * @param type $title
   * @param type $content
   * @param type $type
   */
  public function save_post_to_marmotta( $id, $title, $content, $type ) {
    // in case of wrong data
    if( empty( $id ) || empty( $type ) ) {
      return;
    }
    
    //Load config file
    $config = parse_ini_file(dirname(__FILE__)."/../../../../wp-content/plugins/semantic-wordpress/config.ini");
    
    // in case of empty post content( e.g: Products. )
    if( !in_array( $type, array( 'Entity', 'Individual' ) ) && empty( $content ) ) {
      if( !empty( $_POST['post_description'] ) ) {
        $content = $_POST['post_description'];
      }
      else if( !empty( $_POST['description'] ) ) {
        $content = $_POST['post_description'];
      }
    }
    
    $title = str_replace( '"', '\"', $title );
    $content = str_replace( '"', '\"', $content );
    
    // get enhancments from stanbol
    $stanbol_response = self::post_to_stanbol( $title.' '.$content, $config );
    
    if($stanbol_response != '')
    {
        $subject = $stanbol_response;
        
        //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
        preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

        $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

        $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

        $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

        $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

        //append the new tuple to stanbol response
        $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;
        
        
        //delete previous triples saved in marmotta
        $marmotaQuery = 'DELETE WHERE { 
                                ?document ?post_id"'.$id.'" ^^xsd:integer .
                                ?document ?post_type"' . $type .'"^^xsd:string .
                           }';
        // log delete query
        self::log_sparql_queries( $marmotaQuery );
        
        $encoded_query = urlencode($marmotaQuery);
        $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
        try
        {
          $this->post_to_marmotta( $marmotta_url, 'GET', $config );
        }catch(Exception $e) {}

        try
        {
          // save in marmota
          $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';   
          
          $this->post_to_marmotta( $marmotta_url, 'POST', $config, $stanbol_response_augmented );
        }  catch (Exception $e){}
    }
    
  }
    
  
  //Post Stanbol
  public function post_to_stanbol($query_string, $config)
  {
    $url  = $config['stanbol_server'] . 'enhancer/chain/' . $config['chain'];
    return shell_exec('curl -X POST -H "Accept: text/rdf+nt" -H "Content-type: text/plain; charset=UTF-8" \
--data "'.$query_string.'" '.$url.'');
  }
  
  /**
   * 
   * @param type $url
   * @param type $method
   * @param type $config
   * @param type $query_str
   * @return type
   */
  public function post_to_marmotta( $url, $method, $config, $query_str = '' )
  {
      $ch = curl_init();

      curl_setopt( $ch, CURLOPT_URL, $url );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
      
      if( $method == 'POST' ) {
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $query_str );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: text/turtle; charset=UTF-8' ) );
      }
      else {
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json; charset=UTF-8' ) );
      }

      $output = curl_exec ( $ch );
      
      curl_close ( $ch );
    
    return $output;
  }
  
  public function postToDbPedia($filters, $config)
  {
    try
    {
      $timeout = 5;
      $filters .= urlencode(" FILTER regex(?o, \"http://dbpedia.org/resource\", \"i\") ");
      $dbpedia_url = 'https://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=PREFIX+dbpedia-owl%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2F%3E%0D%0APREFIX+dbpedia%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fresource%2F%3E%0D%0ASELECT+?o%0D%0AWHERE+%7B+%0D%0A++++'.$filters.'+++++++++%0D%0A%7D%0D%0ALimit+30&format=json&CXML_redir_for_subjs=121&CXML_redir_for_hrefs=&timeout='.$timeout.'&debug=on';
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $dbpedia_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);  

      // execute the request
      $output = curl_exec($ch); 
      if (FALSE === $output) {
          return '';
      }
    }
    catch (Exception $e)
    {
        return '';
    }

    return $output;
  }
  
  public function generateMarmottaQuery($config, $query_string, $entities, $postType, $take, $skip)
  {
    $filterByType = ' FILTER (regex(?title, "'.$query_string.'", "i") || regex(?description, "'.$query_string.'", "i"))';
    if($postType != '')
    {
      if($postType != "pedia")
      {
        $filterByType = ' FILTER ((regex(?title, "'.$query_string.'", "i") || regex(?description, "'.$query_string.'", "i")) && (?post_type="'.$postType.'"^^xsd:string))';
      }else {
        $filterByType = ' FILTER ((regex(?title, "'.$query_string.'", "i") || regex(?description, "'.$query_string.'", "i")) && (?post_type="pedia_en"^^xsd:string || ?post_type="pedia_ar"^^xsd:string))';
      }
    }
    $whereCondition = ' { values (?size) { ("1") } . ?document <' . $config['marmotta_server'] . 'ontology/wp/post-id> ?id . 
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-type> ?post_type . 
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-title> ?title . 
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-description> ?description . 
                            '.$filterByType.' }';

    $filterByType = '';
    if($postType != '')
    {
      if($postType != "pedia")
      {
        $filterByType = ' FILTER ((?post_type="'.$postType.'"^^xsd:string) ';
      } else {
        $filterByType = ' FILTER ((?post_type="pedia_en"^^xsd:string || ?post_type="pedia_ar"^^xsd:string) ';
      }
    }
    if(sizeof($entities) > 0)
    {
      $whereCondition .= ' UNION { values (?size) { ("2") } . ?enhancement <http://fise.iks-project.eu/ontology/entity-reference> ?reference .
                            ?enhancement <http://fise.iks-project.eu/ontology/extracted-from> ?document .
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-id> ?id . 
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-type> ?post_type .
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-title> ?title . 
                            ?document <' . $config['marmotta_server'] . 'ontology/wp/post-description> ?description .';
      if($filterByType != '')
      {
        $filterByType .= " && (";
      } else {
        $filterByType .= " FILTER (";
      }
    }

    foreach ($entities as $entity) {
      $filterByType .= " (regex(?reference, '".substr($entity, 1, -1)."')) ||";
    }
    $filterByType = substr($filterByType,0,-2);
    if(sizeof($entities) > 0)
    {
      if($postType == '') {
        $whereCondition .= $filterByType." ) }";
      } else {
        $whereCondition .= $filterByType." )) }";
      }
    }
    $offset = '';
    if($take != -1 && $skip != -1)
    {
      $offset = 'LIMIT '.$take.' OFFSET '.$skip;
    }
    $marmotaQuery = 'SELECT DISTINCT ?id ?post_type ?title ?description WHERE { '. $whereCondition. ' } GROUP BY ?id ?post_type ?title ?description '.$offset;
    return $marmotaQuery;
  }
  
  /**
   * Get User data array
   * 
   * @param type $user_id
   */
  public function get_user_data( $user_id ) {
    global $ar_sub_types, $en_sub_types;
    
    $user = new User();
    $user = $user->getUserById( $user_id );

    // --- Get profile image --- //
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');
    $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
    $image_location = glob($directory . "*bpfull*");            
    foreach(glob($directory . "*bpfull*") as $image_name){
      $image_name = end(explode("/", $image_name));
      $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
    }

    $user_data = new Usermeta();
    $user_meta = $user_data->getMeta($user_id);

    // if image is not from buddypress and from social media //
    if (empty($image_location)){
      $meta_key = "wsl_current_user_image";
      $user_meta_image = new Usermeta();
      $meta = $user_meta_image->getUserMeta($user_id, $meta_key);
      $image = $meta;
      if (empty($meta)){ // -- default gravatar image -- //
        $email = $user->user_email;
        $size = '150'; //The image size
        $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
      }
    }
    
    return array(
      'profile_picture' => $image,
      'sub_type'        => array( 'en' => $en_sub_types[ $user_meta['sub_type'] ],
                                  'ar' => $ar_sub_types[ $user_meta['sub_type'] ]
                            ),
      'username'        => $user->user_nicename
    );
  }
  
  public function return_results($query_string , $post_type, $take, $skip)
  {
    //Load config file
    $config = parse_ini_file( dirname(__FILE__)."/../../../../wp-content/plugins/semantic-wordpress/config.ini");
    
    $stanbol_response = self::post_to_stanbol("This is " . $query_string, $config);
    $output_array = array();
    $arabic_output_array = array();
    preg_match_all("/<urn:enhancement-[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/entity-reference> (?P<entity><[^>]*>)/", $stanbol_response, $output_array);
    
    // set as arabic query string
    $stanbol_response_arabic = self::post_to_stanbol("هذه هي " . $query_string, $config);
    preg_match_all("/<urn:enhancement-[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/entity-reference> (?P<entity><[^>]*>)/", $stanbol_response_arabic, $arabic_output_array);
    $combined_output_array = array_merge($output_array, $arabic_output_array);

    // check dbpedia
    $dbpedia_parm = "";
    foreach ($combined_output_array['entity'] as $entity) { 
      if($dbpedia_parm != "")
      {
        $dbpedia_parm .= urlencode(" UNION ");
      }
      $dbpedia_parm .= urlencode("{".$entity." ?p ?o }");
    }
    
    if($dbpedia_parm != "")
    {
      $subject = self::postToDbPedia($dbpedia_parm, $config);
      if($subject != '')
      {
        $xml=json_decode($subject,true);
        for($i = 0; $i < sizeof($xml["results"]["bindings"]); $i++)
        {
          if (strpos($xml["results"]["bindings"][$i]["o"]["value"], 'http://dbpedia.org/resource/') !== false) {
            $addedEntity = "<".trim($xml["results"]["bindings"][$i]["o"]["value"]).">";
            if(!in_array($addedEntity, $combined_output_array['entity'])
                    && strpos($addedEntity, "'") === false && strpos($addedEntity, "++") === false )
            {
              array_push($combined_output_array['entity'], $addedEntity);
            }
          }
        }
      }
    }

    $results = array();
    $marmotaQuery = self::generateMarmottaQuery($config, $query_string, $combined_output_array['entity'], $post_type, $take, $skip);
    $encoded_query = urlencode($marmotaQuery);
    $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/select?query=' . $encoded_query . '&output=json';
    try
    {
      $marmotta_response = self::post_to_marmotta( $marmotta_url, 'GET', $config );
      
      if($marmotta_response != '')
      {
        $json_response = json_decode($marmotta_response, true);
        
        foreach ($json_response['results']['bindings'] as $binding) {
            $input_id = (int) $binding['id']['value'];
            $input_post_type = $binding['post_type']['value'];
            $input_post_title = $binding['title']['value'];
            $input_post_description = $binding['description']['value'];
            if( $input_post_type == 'Individual') {
              $input_post_type = 'people';
            }
            else if( $input_post_type == 'Entity') {
              $input_post_type = 'organizations';
            }
            else if( in_array( $input_post_type, array( 'pedia_ar', 'pedia_en' ) ) ) {
              $post_type = $input_post_type;
              $input_post_type = 'wiki';
            }
            $data = array(
                'id' => $input_id,
                'title' => urldecode( $input_post_title ),
                'description' => in_array( $input_post_type, array( 'people', 'organizations' ) )?$input_post_description : self::shorten_description( $input_post_description ),
                'type' => $input_post_type
            );
            
            if( in_array( $input_post_type, array( 'people', 'organizations' ) ) ) {
              $data = array_merge( $data, $this->get_user_data( $input_id ) );
            }
            
            if( $input_post_type == 'wiki' ) {
              $option = new Option();
              $host = $option->getOptionValueByKey('siteurl');
              $post_type_parts = explode( '_', $post_type );
              $page_url = $host . '/' . $post_type_parts[1] . '/wiki/' . $data['title'];
              $data = array_merge( $data, array(
                'page_url' => $page_url
              ) );
            }
            
            if( $input_id > 0 && !empty( $input_post_type ) ) {
              $results[] = $data;
            }
        }
      }
    }
    catch (Exception $e)
    {
      return [];
    }
    
    return $results;
  }
   
  /**
   * @SWG\Get(
   *   path="/search/{post_type}/{search_keyword}/",
   *   tags={"Search"},
   *   summary="List Published Posts depending on Search Keyword entered",
   *   description="List Published POsts depending on post type and Search Keyword entered also search semantically and return set of data to render",
   *   @SWG\Parameter(name="post_type", in="path", required=false, type="string", description="Leave it blank to get data from all types or Define Type to search in <b>Values: </b> any of <br/> 1.news <br/>2.product<br/>3.tribe_events<br/>4.request_center<br/>5.collaboration-center<br/>6.open_dataset<br/>7.success_story<br/>8. wiki<br/>9.people<br/>10. organizations<br/>11. expert_thought<br/>12.service<br/>"),
   *   @SWG\Parameter(name="search_keyword", in="path", required=false, type="string", description="Keyword needed to search for <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"), 
   *   @SWG\Response(response="200", description="List posts related to search keyword and type"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function listSearch($request, $response, $args) 
  {
    $system_post_type = array('news','product','success_story','open_dataset',
        'collaboration-center', 'request_center','wiki','tribe_events',
        'expert_thought','service', 'people', 'organizations');
    
    //check valid post type
    if( !empty( $args['post_type'] ) ) {
      if( in_array( $args['post_type'], array( 'all', '{post_type}' ) ) ) {
        $args['post_type'] = '';
      }
      else if( !in_array($args['post_type'], $system_post_type)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", 'post_type'));
      }
    }
    else {
      $args['post_type'] = '';
    }
    
    if( $args['post_type'] == 'people') {
      $args['post_type'] = 'Individual';
    }
    else if( $args['post_type'] == 'organizations') {
      $args['post_type'] = 'Entity';
    }
    else if( $args['post_type'] == 'wiki' ) {
      $args['post_type'] = 'pedia';
    }
    
    //check empty keyword
    if($args['search_keyword'] == '{search_keyword}')
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", 'search_keyword'));
    }
    if (!preg_match('/[أ-يa-zA-Z]+/', $args['search_keyword'], $matches)) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "search keyword must at least contain one letter"));
    }
    
    //check pageNumber and total Count
    $parameters = ['pageNumber', 'numberOfData', ];
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
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    else if(empty($args['numberOfData'] ) || $args['numberOfData'] < 1 || $args['numberOfData'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of data',array("range"=> "1 and 25 ")));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    
    $query_string = $args['search_keyword'];
    
    $results_count = count( self::return_results($query_string, $args['post_type'], -1, -1) );
    
    $take = $args['numberOfData'];
    $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
    $return_results = $this->ef_load_data_counts( $results_count,  $args['numberOfData'] );
    
    $results = self::return_results($query_string, $args['post_type'], $take, $skip);
    $return_results['data'] = $results;
    if(count($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }    
    
    return $this->renderJson($response, 200, $return_results);
  }
}
