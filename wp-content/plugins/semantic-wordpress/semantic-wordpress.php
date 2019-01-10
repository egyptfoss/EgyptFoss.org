<?php //

/**
 * Plugin Name: Semantic Wordpress
 * Description: Add Semantic Services to Wordpress
 * Version: 1.0.0
 * Author: Hesham ElBaz
 */
$config = parse_ini_file("config.ini");
define("ef_search_per_page", 10);

function post_to_stanbol($query) {
    global $config;
    $stanbol_url = $config['stanbol_server'] . 'enhancer/chain/' . $config['chain'];
    try
    {
        $stanbol_response = wp_remote_post($stanbol_url, array(
          'method' => 'POST',
          'headers' => array('Accept' => 'text/rdf+nt', 'Content-Type' => 'text/plain; charset=UTF-8'), // accepts N-Triples format (subject, property, value) to be parsed by marmota
          'body' => $query,
          'timeout' => $config['timeout']
                )
        );
    }  catch (Exception $e)
    {
        return '';
    }
    if(sizeof($stanbol_response->errors)){
        return '';
    }
    
    return $stanbol_response;
}

add_filter('the_posts', 'advanced_search_query', 999, 2);

function postToDbPedia($filters)
{
  global $config;
  try
  {
    //filter by regex
    $filters .= urlencode(" FILTER regex(?o, \"http://dbpedia.org/resource\", \"i\") ");
    $dbpedia_url = 'https://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=PREFIX+dbpedia-owl%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2F%3E%0D%0APREFIX+dbpedia%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fresource%2F%3E%0D%0ASELECT+?o%0D%0AWHERE+%7B+%0D%0A++++'.$filters.'+++++++++%0D%0A%7D%0D%0ALimit+30&format=json&CXML_redir_for_subjs=121&CXML_redir_for_hrefs=&timeout='.$config['timeout'].'&debug=on';
    $dbpedia_response = wp_remote_post($dbpedia_url, array(
        'method' => 'GET',
        'timeout' => $config['timeout']
      )
    );
  }
  catch (Exception $e)
  {
      return '';
  }
  if(sizeof($dbpedia_response->errors)) {
      return '';
  }else if ($dbpedia_response['response']['code'] == 400) {
    return '';
  }
  
  return $dbpedia_response['body'];
}

function sideBarPostToDbPedia($entity)
{
  global $config;
  try
  {
    //filter by regex
    $lang = pll_current_language();
    $filters = urlencode($entity. " ?p ?o   
     FILTER ( 
      ( 
      regex(?p, \"http://purl.org/dc/terms/subject\") ||
      regex(?p, \"http://xmlns.com/foaf/0.1/isPrimaryTopicOf\") ||
      regex(?p, \"http://dbpedia.org/ontology/thumbnail\") ||
      regex(?p, \"http://www.w3.org/2000/01/rdf-schema#seeAlso\") ||
      (regex(?p, \"http://www.w3.org/2000/01/rdf-schema#label\") && lang(?o) = \"{$lang}\") ||
      (regex(?p, \"http://www.w3.org/2000/01/rdf-schema#comment\") && lang(?o) = \"{$lang}\")
      ) 
      )         
    ");
    $dbpedia_url = 'https://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=PREFIX+dbpedia-owl%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fontology%2F%3E%0D%0APREFIX+dbpedia%3A+%3Chttp%3A%2F%2Fdbpedia.org%2Fresource%2F%3E%0D%0ASELECT+*%0D%0AWHERE+%7B+%0D%0A++++'.$filters.'+++++++++%0D%0A%7D%0D%0ALimit+30&format=json&CXML_redir_for_subjs=121&CXML_redir_for_hrefs=&timeout='.$config['timeout'].'&debug=on';
    
    $dbpedia_response = wp_remote_post($dbpedia_url, array(
        'method' => 'GET',
        'timeout' => $config['timeout']
      )
    );
  }
  catch (Exception $e)
  {
      return '';
  }
  
  if(sizeof($dbpedia_response->errors)) {
      return '';
  }else if (in_array( $dbpedia_response['response']['code'], array( 400, 502 ) )) {
    return '';
  }
  
  return $dbpedia_response['body'];
}

function advanced_search_query($posts) {
    if (is_search() && !is_admin()) {
      
        wp_enqueue_script( 'listing_search-js', get_stylesheet_directory_uri() . '/js/listing_search.js', array('jquery'), '', true);
        global $wpdb;
        global $config;
        $query_string = trim($_GET["s"]);

        if(isset($_GET["s"]))
        {
            $query_string = ucwords( trim($_GET["s"]) );
            if(empty($query_string))
            {
                return [];
            }else
            {
                $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $query_string);
                $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $query_string);

                if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
                  return [];
                }
            }
        }else
        {
            return [];
        }
        
        $_SESSION["search_string"] = $query_string;
        //$postType = isset($_GET["type"]) ? $_GET["type"] : "news,product,tribe_events";
        $postType = isset($_GET["type"]) ? $_GET["type"] : "";
        $_SESSION["search_post_type"] = $postType;
        $_SESSION["extracted_entities"] = "";
        $_SESSION["search_ids"] = "";
        $_SESSION["search_ids_en"] = "";
        $_SESSION["search_ids_ar"] = "";
        $ids = array();
        $ids_en = array();
        $ids_ar = array();
        $ids_collaboration = array();
        $resultArray = array();
        //query stanbol with the search word concatenated with "This is" or "هذه هي" to give a clear context for stanbol
        $stanbol_response = post_to_stanbol("This is " . $query_string);
        $subject = $stanbol_response['body'];
        //get from stanbol response enhancements that has reference on dppedia
        preg_match_all("/<urn:enhancement-[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/entity-reference> (?P<entity><[^>]*>)/", $subject, $output_array);

        
        $stanbol_response = post_to_stanbol("هذه هي " . $query_string);
        $subject = $stanbol_response['body'];
        preg_match_all("/<urn:enhancement-[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/entity-reference> (?P<entity><[^>]*>)/", $subject, $arabic_output_array);
        $output_array = array_merge($output_array, $arabic_output_array);

        // check dbpedia
        $dbpedia_parm = "";
        foreach ($output_array['entity'] as $entity) {
          if($dbpedia_parm != "")
          {
            $dbpedia_parm .= urlencode(" UNION ");
          }
          $dbpedia_parm .= urlencode("{".$entity." ?p ?o }");
        }
        if($dbpedia_parm != "")
        {
          $subject = postToDbPedia($dbpedia_parm);
          if($subject != '')
          {            
            $xml=json_decode($subject,true);
            for($i = 0; $i < sizeof($xml["results"]["bindings"]); $i++)
            {
              if (strpos($xml["results"]["bindings"][$i]["o"]["value"], 'http://dbpedia.org/resource/') !== false) {
                $addedEntity = "<".trim($xml["results"]["bindings"][$i]["o"]["value"]).">";
                if(!in_array($addedEntity, $output_array['entity'])
                        && strpos($addedEntity, "'") === false && strpos($addedEntity, "++") === false)
                {
                  array_push($output_array['entity'], $addedEntity);
                }
              }
            }
          }
        }
        
        $_SESSION["extracted_entities"] = $output_array['entity'];
        $main_entity = '';
        if(sizeof($output_array['entity']) > 0)
        {
          $main_entity = $output_array['entity'][0];
        }
        
        //iterate over the extracted entities from stanbol and query marmota
        //marmota will return wordpress post id
        $total_result_marmottaQuery = generateMarmottaQuery($query_string, $output_array['entity'], $postType, -1, -1);
        $total_items = retrieveFromMarmotta($total_result_marmottaQuery);
        if(sizeof($total_items) > 0 )
        {
          wp_localize_script('listing_search-js', 'ef_search', array("per_page" => constant("ef_search_per_page"),
            "entity" => $main_entity));
        }else {
          wp_localize_script('listing_search-js', 'ef_search', array("per_page" => constant("ef_search_per_page"),
            "entity" => ''));
        
        }
        // Pagination
        $marmotaQuery = generateMarmottaQuery($query_string, $output_array['entity'], $postType, constant("ef_search_per_page"), 0);
        $items = retrieveFromMarmotta($marmotaQuery);
        
        /*$marmotaQuery = 'SELECT DISTINCT ?id ?post_type ?title ?description WHERE {
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-id> ?id . 
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-type> ?post_type . 
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-title> ?title . 
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-description> ?description . 
                                 ';
        $marmotaQuery .= $filterByType. '}';
        $items = retrieveFromMarmotta($marmotaQuery);
        if(is_array($items))
        {
          foreach($items as $item)
          {
            if(!in_array($item->ID, $ids))
            {
              array_push($ids, $item->ID);
              $resultArray[] = $item;
            }
          }
        }
        
        $filterByType = '';
        if($postType != '')
        {
          $filterByType = ' FILTER (?post_type="'.$postType.'"^^xsd:string) ';
        }
        foreach ($output_array['entity'] as $entity) {
            $marmotaQuery = 'SELECT DISTINCT ?id ?post_type ?title ?description WHERE {
                                ?enhancement <http://fise.iks-project.eu/ontology/entity-reference> ' . $entity . ' .
                                ?enhancement <http://fise.iks-project.eu/ontology/extracted-from> ?document .
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-id> ?id . 
                                ?document <' . $config['marmotta_server'] . 'ontology/wp/post-type> ?post_type .';

            $marmotaQuery .= $filterByType.' }';
            $items = retrieveFromMarmotta($marmotaQuery);
            if(is_array($items))
            {
              foreach($items as $item)
              {
                if(!in_array($item->ID, $ids))
                {
                  array_push($ids, $item->ID);
                  $resultArray[] = $item;
                }
              }
            }
        }*/
        set_query_var ('ef_total_count', 0);
        if(sizeof($total_items) > 0) {
            set_query_var ('ef_total_count', sizeof($total_items));
        }
        global $semantic_posts;
        $semantic_posts = $items;
        return $items;
        /*
        if($postType !== "pedia" && $postType !== "collaboration-center"
                && $postType !== "organizations" && $postType !== "people") {
            $results = retrieveAllPostsSearchResult($ids, $ids_en, $ids_ar, $postType, $query_string, constant("ef_search_per_page"),0);
            return $results;
        } else if($postType == "collaboration-center")
        {
            $results = retrieveAllPublishedDocuments($ids_collaboration, $query_string, constant("ef_search_per_page"),0);
            return $results;
        }
        else if($postType == "organizations" || $postType == "people")
        {
            $results = retrieveAllUsers($ids, $query_string, constant("ef_search_per_page"),0, $postType);
            return $results;
        }        
        else {
            //post type is pedia
            $results = retrievePediaSearchResult($ids_en, $ids_ar, $query_string, constant("ef_search_per_page"),0);
            return $results;
        }*/
    } else {
        return $posts;
    }
}

function generateMarmottaQuery($query_string, $entities, $postType, $take, $skip)
{
  global $config;
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

function retrieveFromMarmotta($query)
{
  global $config;
  $result_arr = array();
  $encoded_query = urlencode($query);
  $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/select?query=' . $encoded_query . '&output=json';
  try
  {
      $marmotta_response = wp_remote_post($marmotta_url, array(
        'method' => 'GET',
          'timeout' => $config['timeout']
              )
      );
      if(sizeof($marmotta_response->errors) == 0 || $marmotta_response->errors == null)
      {
          $json_response = json_decode($marmotta_response['body'], true);
          foreach ($json_response['results']['bindings'] as $binding) {
              $input_id = (int) $binding['id']['value'];
              $input_post_type = $binding['post_type']['value'];
              $lang = "en";
              if($input_post_type == "pedia_ar")
              {
                $lang = "ar";
              }
              if($input_post_type == "pedia_en" || $input_post_type == "pedia_ar")
              {
                $input_post_type = "pedia";
              }
              $input_post_title = $binding['title']['value'];
              $input_post_description = $binding['description']['value'];

              if( $input_id > 0 && !empty( $input_post_type ) ) {

                $post = new WP_Post();
                $post->ID = $input_id;
                $post->guid = get_option('home').'/'.$lang.'/wiki/'.str_replace(" ","_",$input_post_title);
                $post->post_title = $input_post_title;
                $post->post_name = $input_post_title;
                $post->post_content = $input_post_description;
                $post->post_type = $input_post_type;
                $result_arr[] = $post;
              }
          }
      }
  }
  catch (Exception $e)
  {}
  
  return $result_arr;
}

function retrieveAllPostsSearchResult($ids, $ids_en, $ids_ar,$postType, $query_string, $take, $skip)
{
    global $wpdb;
    global $config;
    $sqlQuery = "select {$wpdb->prefix}posts.*, {$wpdb->prefix}postmeta.meta_value 
                from {$wpdb->prefix}posts left join {$wpdb->prefix}postmeta 
                on {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id and {$wpdb->prefix}postmeta.meta_key = 'description' 
                where ";
            
    //add where condition with the retreived postIds to normal wordpress search query 
    if (count($ids) > 0) {
        if(!is_array($ids))
            $ids = explode (",", $ids);
        
        if(!is_array($ids_en))
            $ids_en = explode (",", $ids_en);
        
        if(!is_array($ids_ar))
            $ids_ar = explode (",", $ids_ar);
        
        $ids_string = implode(",", $ids);
        $_SESSION["search_ids"] = $ids_string;
        $sqlQuery .= "
                    (
                        ({$wpdb->prefix}posts.post_title LIKE %s) 
                            OR 
                        ({$wpdb->prefix}posts.post_content LIKE %s)
                            OR 
                        ({$wpdb->prefix}postmeta.meta_value LIKE %s)
                            OR
                        ({$wpdb->prefix}posts.ID IN (".implode(', ', array_fill(0, count($ids), '%s'))."))
                    ) 
                    AND {$wpdb->prefix}posts.post_type IN (".implode(', ', array_fill(0, count(explode(",", $postType)), '%s')).") 
                    AND {$wpdb->prefix}posts.post_status = 'publish'
                        order by case when {$wpdb->prefix}posts.post_title like %s  then 1       
                          when {$wpdb->prefix}posts.post_content like %s then 1       
                          when {$wpdb->prefix}postmeta.meta_value like %s  then 1
                          when {$wpdb->prefix}posts.ID IN (".  implode(",", $ids).") then 2 end, {$wpdb->prefix}posts.ID desc
                ";
        $query_string = "%" . $query_string . "%";
        $valuesArray = array_merge(
            array($query_string, $query_string, $query_string), array_merge($ids, explode(",", $postType),
                    array($query_string, $query_string, $query_string))
                );
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    } else {
        $sqlQuery .= "
        (
            ({$wpdb->prefix}posts.post_title LIKE %s) 
                OR 
            ({$wpdb->prefix}posts.post_content LIKE %s)
                OR 
            ({$wpdb->prefix}postmeta.meta_value LIKE %s)
        ) 
        AND {$wpdb->prefix}posts.post_type IN (".implode(', ', array_fill(0, count(explode(",", $postType)), '%s')).")
        AND {$wpdb->prefix}posts.post_status = 'publish'
            order by {$wpdb->prefix}posts.ID desc
            ";
        $query_string = "%" . $query_string . "%";
        $valuesArray = array_merge(array($query_string, $query_string, $query_string), explode(",", $postType));
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    }

    //check if should concatenate pedia to all other types  
    if($postType == 'news,product,tribe_events')
    {
        //no need to take or skip on pedia data
        $pedia_result = retrievePediaSearchResult($ids_en, $ids_ar, $query_string, -1, -1);
        $results = array_merge($results, $pedia_result);
        set_query_var ('ef_total_count', 0);
        if(sizeof($results) > 0)
            set_query_var ('ef_total_count', sizeof($results));
        $results = array_slice($results, $skip, $take);
        return $results;
    }else{
        set_query_var ('ef_total_count', 0);
        if(sizeof($results) > 0)
            set_query_var ('ef_total_count', sizeof($results));
        $results = array_slice($results, $skip, $take);
        return $results;
    }
}

function retrieveAllPublishedDocuments($ids,$query_string, $take, $skip)
{
    load_orm();
    $itemHistory = CollaborationCenterItemHistory::where("status",'=',"published")->groupBy("item_ID")->selectRaw("max(ID) as maxIDs")->get();
    $itemHistoryIDs = array_map('intval', $itemHistory->pluck('maxIDs')->toArray());
    
    $page_lang = pll_current_language();
    
    global $wpdb;
    $sqlQuery = "select itemHistory.ID as ID, itemHistory.title as post_title, itemHistory.content as post_content,"
            . "'collaboration-center' as post_type, "
            . "concat('/$page_lang/collaboration-center/published/',itemHistory.item_ID) as page_url "
    . "from {$wpdb->prefix}ef_item "
    . "inner join {$wpdb->prefix}ef_item_history as itemHistory on {$wpdb->prefix}ef_item.ID = itemHistory.item_ID"
    . " where itemHistory.status = 'published' and itemHistory.ID in (".implode(', ', array_fill(0, count($itemHistoryIDs), '%s')).") ";
    
    if (count($ids) > 0) {
        if(!is_array($ids)){
            $ids = explode (",", $ids);
        }
        $ids_string = implode(",", $ids);
        $_SESSION["search_ids"] = $ids_string;
        $sqlQuery .= " AND
                    (
                        (itemHistory.title LIKE %s) 
                            OR 
                        (itemHistory.content LIKE %s)
                            OR 
                        ({$wpdb->prefix}ef_item.ID IN (".implode(', ', array_fill(0, count($ids), '%s'))."))
                    ) 
                    order by case when itemHistory.title like %s  then 1       
                          when itemHistory.content like %s then 1       
                          when {$wpdb->prefix}ef_item.ID IN (".  implode(",", $ids).") then 2 end, itemHistory.ID desc                        
                ";
        $query_string = "%" . $query_string . "%";
        $valuesArray = array_merge($itemHistoryIDs, 
            array($query_string, $query_string), $ids, array($query_string, $query_string));
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    } else {
        $sqlQuery .= " AND
        (
          (itemHistory.title LIKE %s) 
              OR 
          (itemHistory.content LIKE %s)
        ) 
            order by itemHistory.ID desc
            ";
        $query_string = "%" . $query_string . "%";
        $valuesArray = array_merge($itemHistoryIDs, 
            array($query_string, $query_string));
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    }

    //total count
    set_query_var ('ef_total_count', 0);
    if(sizeof($results) > 0){
        set_query_var ('ef_total_count', sizeof($results));
    }
    $results = array_slice($results, $skip, $take);
    
    return $results;
}

function retrieveAllUsers($ids,$query_string, $take, $skip, $postType)
{
    $type = "Individual";
    if($postType == "organizations")
    {
      $type = "Entity";
    }
    global $wpdb;
    $sqlQuery = "select users.ID as ID, users.display_name as post_title,"
            . "'organizations' as post_type "
    . "from {$wpdb->prefix}users as users "
    . "inner join {$wpdb->prefix}usermeta as usermeta_type on users.ID = usermeta_type.user_id "
    . " where users.user_status = 0 "
    . "and usermeta_type.meta_key = 'type' and usermeta_type.meta_value = '{$type}'";
    
    if (count($ids) > 0) {
        if(!is_array($ids)){
            $ids = explode (",", $ids);
        }
        $ids_string = implode(",", $ids);
        $_SESSION["search_ids"] = $ids_string;
        $sqlQuery .= " AND
                    (
                        (users.display_name LIKE %s) 
                            OR 
                        (users.ID IN (".implode(', ', array_fill(0, count($ids), '%s'))."))
                    ) 
                    order by users.display_name asc";
        $query_string = "%" . $query_string . "%";
        $valuesArray = array($query_string, $ids);
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    } else {
        $sqlQuery .= " AND
        (
          (users.display_name LIKE %s) 
        ) 
        order by users.display_name asc";
        $query_string = "%" . $query_string . "%";
        $valuesArray =  array($query_string, $query_string);
        $results =  $wpdb->get_results( $wpdb->prepare($sqlQuery, $valuesArray));
    }

    //total count
    set_query_var ('ef_total_count', 0);
    if(sizeof($results) > 0){
        set_query_var ('ef_total_count', sizeof($results));
    }
    $results = array_slice($results, $skip, $take);
    
    return $results;
}

function retrievePediaSearchResult($ids_en,$ids_ar,$query_string, $take, $skip)
{
    $wpdb_pedia = new wpdb(PEDIA_DB_USER, PEDIA_DB_PASSWORD, PEDIA_DB_NAME, PEDIA_DB_HOST);
    $prefixes = array('en_','ar_');
    $final_results = array();
    if(!is_array($ids_en))
        $ids_en = explode(",", $ids_en);
    
    if(!is_array($ids_ar))
        $ids_ar = explode(",", $ids_ar);
    
    foreach($prefixes as $prefix)
    {
        $page_lang = $prefix;
        $page_lang = str_replace("_", "", $page_lang);
        $wpdb_pedia->prefix = $prefix;
        $sqlQuery = "select {$wpdb_pedia->prefix}page.page_id,{$wpdb_pedia->prefix}page.page_title as post_title, {$wpdb_pedia->prefix}text.old_text as meta_value 
            ,'pedia' as post_type,concat('/$page_lang/wiki/',{$wpdb_pedia->prefix}page.page_title) as page_url
            from {$wpdb_pedia->prefix}page left join {$wpdb_pedia->prefix}revision  
            on {$wpdb_pedia->prefix}page.page_latest = {$wpdb_pedia->prefix}revision.rev_id
            left join {$wpdb_pedia->prefix}text 
            on {$wpdb_pedia->prefix}revision.rev_text_id = {$wpdb_pedia->prefix}text.old_id
            where {$wpdb_pedia->prefix}page.page_namespace = 0";
            
        if($page_lang == "en")
        {
            if (count($ids_en) > 0) {
                $ids_string = implode(",", $ids_en);
                $_SESSION["search_ids_en"] = $ids_string;
                        
                $sqlQuery .= " and
                            (
                                ({$wpdb_pedia->prefix}page.page_title LIKE %s) 
                                    OR 
                                ({$wpdb_pedia->prefix}text.old_text LIKE %s)
                                    OR
                                ({$wpdb_pedia->prefix}page.page_id IN (".implode(', ', array_fill(0, count($ids_en), '%s'))."))
                            ) 
                            order by case when {$wpdb_pedia->prefix}page.page_title like %s  then 1       
                          when {$wpdb_pedia->prefix}text.old_text like %s then 1       
                          when {$wpdb_pedia->prefix}page.page_id IN (".  implode(",", $ids_en).") then 2 end, {$wpdb_pedia->prefix}page.page_id desc
                        ";
                $query_string = "%" . $query_string . "%";
                $valuesArray = array_merge(
                    array($query_string, $query_string), $ids_en, array($query_string, $query_string)
                        );
                $results =  $wpdb_pedia->get_results( $wpdb_pedia->prepare($sqlQuery, $valuesArray));

                $final_results = array_merge($final_results, $results);
            }else {
                $sqlQuery .= " and 
                (
                    ({$wpdb_pedia->prefix}page.page_title LIKE %s) 
                        OR 
                    ({$wpdb_pedia->prefix}text.old_text LIKE %s)
                ) order by {$wpdb_pedia->prefix}page.page_id desc";

                $query_string = "%" . $query_string . "%";
                $valuesArray = array($query_string, $query_string);
                $results =  $wpdb_pedia->get_results( $wpdb_pedia->prepare($sqlQuery, $valuesArray));

                $final_results = array_merge($final_results, $results);
            }
        }else if($page_lang == "ar")
        {
            if (count($ids_ar) > 0) {
                $ids_string = implode(",", $ids_ar);
                $_SESSION["search_ids_ar"] = $ids_string;
                $sqlQuery .= " and
                            (
                                ({$wpdb_pedia->prefix}page.page_title LIKE %s) 
                                    OR 
                                ({$wpdb_pedia->prefix}text.old_text LIKE %s)
                                    OR
                                ({$wpdb_pedia->prefix}page.page_id IN (".implode(', ', array_fill(0, count($ids_ar), '%s'))."))
                            ) 
                          order by case when {$wpdb_pedia->prefix}page.page_title like %s  then 1       
                          when {$wpdb_pedia->prefix}text.old_text like %s then 1       
                          when {$wpdb_pedia->prefix}page.page_id IN (".  implode(",", $ids_en).") then 2 end, {$wpdb_pedia->prefix}page.page_id desc";
                $query_string = "%" . $query_string . "%";
                $valuesArray = array_merge(
                    array($query_string, $query_string), $ids_ar,array($query_string, $query_string)
                        );
                $results =  $wpdb_pedia->get_results( $wpdb_pedia->prepare($sqlQuery, $valuesArray));

                $final_results = array_merge($final_results, $results);
            }else {
                $sqlQuery .= " and
                (
                    ({$wpdb_pedia->prefix}page.page_title LIKE %s) 
                        OR 
                    ({$wpdb_pedia->prefix}text.old_text LIKE %s)
                ) order by {$wpdb_pedia->prefix}page.page_id desc";

                $query_string = "%" . $query_string . "%";
                $valuesArray = array($query_string, $query_string);
                $results =  $wpdb_pedia->get_results( $wpdb_pedia->prepare($sqlQuery, $valuesArray));

                $final_results = array_merge($final_results, $results);
            }
        }
    }
    
    set_query_var ('ef_total_count', 0);
    if(sizeof($final_results) > 0)
        set_query_var ('ef_total_count', sizeof($final_results));
    if($take != -1 && $skip != -1)
        $final_results = array_slice($final_results, $skip, $take);
    return $final_results;
}

//TODO  to be changed to on update action
//add_action('publish_product', 'post_enhancments_to_marmotta', 10, 2);
//add_action('publish_news', 'post_enhancments_to_marmotta', 10, 2);
//add_action('publish_tribe_events', 'post_enhancments_to_marmotta', 10, 2);


add_action('save_post', 'post_enhancments_to_marmotta', 9999, 2);
function post_enhancments_to_marmotta($id, $post) {
    if(
               $post->post_type == 'product' || $post->post_type == 'news' || $post->post_type == 'tribe_events'
            || $post->post_type == 'open_dataset' || $post->post_type == 'success_story'
            || $post->post_type == 'request_center'
            || $post->post_type == 'expert_thought'
            || $post->post_type == 'service')
    {
        $postMeta = get_post_custom($id);
        $title = $post->post_title;
        $content = $post->post_content;

        if ($post->post_type == "news" || $post->post_type == "product"
                || $post->post_type == "open_dataset" || $post->post_type == 'request_center' ) {
            $content = get_post_meta($id, 'description',true);
        }

        global $config;
        
        //delete previous triples saved in marmotta
        $marmotaQuery = 'DELETE WHERE { 
                                ?document ?post_id"'.$id.'" ^^xsd:integer .
                                ?document ?post_type"' . $post->post_type .'"^^xsd:string .
                           }';
        // log delete query
//            log_sparql_queries( $marmotaQuery );
        
        $encoded_query = urlencode($marmotaQuery);
        $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
        try
        {
            $marmotta_response = wp_remote_post($marmotta_url, array(
              'method' => 'GET',
              'timeout' => $config['timeout']
               )
            );
        }catch(Exception $e) {}
        
        if( $post->post_status != "publish" ) {
          return;
        }
        
        // get enhancments from stanbol
        $stanbol_response = post_to_stanbol($title.' '.$content);
        if($stanbol_response != '')
        {
            
            $subject = $stanbol_response['body'];
            //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
            preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

            $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

            $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $post->post_type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

            $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
            $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
            //append the new tuple to stanbol response
            $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;
        
            // save in marmota
            $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';
            try
            {
                $marmotta_response = wp_remote_post($marmotta_url, array(
                  'method' => 'POST',
                  'headers' => array('Content-Type' => 'text/turtle'),
                  'body' => $stanbol_response_augmented,
                  'timeout' => $config['timeout']
                        )
                );
            }  catch (Exception $e)
            {

            }
        }
    }
}

add_action('admin_enqueue_scripts', 'add_live_enhancment');

function add_live_enhancment() {
    global $config;
    wp_register_script('rdfstore', '/wp-content/plugins/semantic-wordpress/rdfstore.js'); // library that takes ld+json format and enables SPARQL queries on it
    wp_enqueue_script('rdfstore');
    wp_register_script('semantic-wordpress', '/wp-content/plugins/semantic-wordpress/semantic-wordpress.js');
    wp_enqueue_script('semantic-wordpress');
    wp_register_style('semantic-wordpress', '/wp-content/plugins/semantic-wordpress/semantic-wordpress.css');
    wp_enqueue_style('semantic-wordpress');
    wp_localize_script('semantic-wordpress', 'stanbol_server', $config['stanbol_server']); // pass variable to javascript file
}

// --- view more for listing new --- //
function ef_load_more_listing_search() {
    global $post;
    
    set_query_var('ef_listing_news_offset', $_POST['offset']); 
    /*if(isset($_SESSION["search_post_type"]) && $_SESSION["search_post_type"] == "pedia")
    {
      $result = retrievePediaSearchResult($_SESSION["search_ids_en"], 
            $_SESSION["search_ids_ar"], $_SESSION["search_string"], constant("ef_search_per_page"), $_POST['offset']);
    } else if(isset($_SESSION["search_post_type"]) && $_SESSION["search_post_type"] == "collaboration-center")
    {
      $result = retrieveAllPublishedDocuments($_SESSION["search_ids"], $_SESSION["search_string"],
              constant("ef_search_per_page"), $_POST['offset']);
    }
    else if(isset($_SESSION["search_post_type"]) 
            && ($_SESSION["search_post_type"] == "organizations" || $_SESSION["search_post_type"] == "people"))
    {
      $result = retrieveAllUsers($_SESSION["search_ids"], $_SESSION["search_string"], 
              constant("ef_search_per_page"), $_POST['offset'], $_SESSION["search_post_type"]);
    }else{
      $result = retrieveAllPostsSearchResult($_SESSION["search_ids"], $_SESSION["search_ids_en"], 
            $_SESSION["search_ids_ar"], $_SESSION["search_post_type"], $_SESSION["search_string"], constant("ef_search_per_page"), $_POST['offset']);
    }*/
    $marmotaQuery = generateMarmottaQuery($_SESSION["search_string"], $_SESSION["extracted_entities"], 
            $_SESSION["search_post_type"], constant("ef_search_per_page"), $_POST['offset']);
    $items = retrieveFromMarmotta($marmotaQuery);
    foreach ( $items as $post ) { 
        setup_postdata( $post );
        get_template_part('template-parts/content', 'search');
    }
    die();
}
add_action('wp_ajax_ef_load_more_listing_search', 'ef_load_more_listing_search');
add_action('wp_ajax_nopriv_ef_load_more_listing_search', 'ef_load_more_listing_search');

function ef_load_sidebar()
{
  $entity = $_POST['entity'];
  if(!isset($entity) || $entity == '')
  {
    echo '{"error":true, "message": "No entities found"}';
    die();
  }
  
  $body = sideBarPostToDbPedia($entity);
  if($body != '')
  {            
    $xml=json_decode($body,true);
    $title = $description = $wikiLink = $thumb = "";
    $seeAlso = array();
    if(sizeof($xml["results"]["bindings"]) > 0)
    {

      foreach( $xml["results"]["bindings"] as $record ) {
        $type = $record['p']['value'];
        $value = $record['o'];
        switch( $type ) {
          case 'http://www.w3.org/2000/01/rdf-schema#label':
            $title = $value;
            break;
          case 'http://www.w3.org/2000/01/rdf-schema#comment':
            $description = $value;
            break;
          case 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf':
            $wikiLink = $value;
            break;
          case 'http://dbpedia.org/ontology/thumbnail':
            $thumb = $value;
            break;
          case 'http://www.w3.org/2000/01/rdf-schema#seeAlso':
            $seeAlso[] = $value;
            break;
        }
      }
    }
    
      echo json_encode(array('error' => false,'title' => $title, 'description' => $description,
          'wikiLink' => $wikiLink, 'thumb' => $thumb, 'seeAlso'=> $seeAlso));
      die();
  }else {
    echo '{"error":true, "message": "No data returned from dbpedia"}';
    die();
  }
}
add_action('wp_ajax_ef_load_sidebar', 'ef_load_sidebar');
add_action('wp_ajax_nopriv_ef_load_sidebar', 'ef_load_sidebar');

function saveOldWikiInSemantic( $id, $title, $content, $type )
{
    global $config;
    $post_type = $type;
    $page_id = $id;

    // get enhancments from stanbol
    $stanbol_response = post_to_stanbol( $content );
    $subject = $stanbol_response['body'];

    //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
    preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

    $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $page_id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

    $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $post_type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

    $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
    $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

    //append the new tuple to stanbol response
    $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;

    //delete previous triples saved in marmotta
    $marmotaQuery = 'DELETE WHERE { 
                            ?document ?post_id"'.$page_id.'" ^^xsd:integer .
                            ?document ?post_type"' . $post_type .'"^^xsd:string .
                       }';
    // log delete query
//    log_sparql_queries( $marmotaQuery );
    
    $encoded_query = urlencode($marmotaQuery);
    $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
    try
    {
        $marmotta_response = wp_remote_post($marmotta_url, array(
          'method' => 'GET',
          'timeout' => $config['timeout']
           )
        );
    }catch(Exception $e) {}

    // save in marmota
    $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';

    $marmotta_response = wp_remote_post($marmotta_url, array(
      'method' => 'POST',
      'headers' => array('Content-Type' => 'text/turtle'),
      'body' => $stanbol_response_augmented
            )
    );
}

//save collaboration document in marmotta
function saveDocumentContent($document_id, $title, $content)
{
  global $config;
  // get enhancments from stanbol
  $stanbol_response = post_to_stanbol($title. ' '. $content);
  if($stanbol_response != '')
  {
    $post_type = "collaboration-center";
    $subject = $stanbol_response['body'];

    //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
    preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

    $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $document_id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

    $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $post_type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
    
    $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
    $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

    //append the new tuple to stanbol response
    $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;

    //delete previous triples saved in marmotta
    $marmotaQuery = 'DELETE WHERE { 
                            ?document ?post_id"'.$document_id.'" ^^xsd:integer .
                            ?document ?post_type"' . $post_type .'"^^xsd:string .
                       }';
    // log delete query
//    log_sparql_queries( $marmotaQuery );
    
    $encoded_query = urlencode($marmotaQuery);
    $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
    try
    {
        $marmotta_response = wp_remote_post($marmotta_url, array(
          'method' => 'GET',
          'timeout' => $config['timeout']
           )
        );
    }catch(Exception $e) {}

    // save in marmota
    $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';
    try
    {
        $marmotta_response = wp_remote_post($marmotta_url, array(
          'method' => 'POST',
          'headers' => array('Content-Type' => 'text/turtle'),
          'body' => $stanbol_response_augmented,
          'timeout' => $config['timeout']
                )
        );
    }  catch (Exception $e)
    {

    }
  }
}

//save users in marmotta
function saveUserContent( $user_id, $title, $content , $post_type )
{
  global $config;
  // get enhancments from stanbol
  $stanbol_response = post_to_stanbol( $title . ' ' . $content );

  if($stanbol_response != '')
  {
    $subject = $stanbol_response['body'];
    
    //get contextId from stanbol response, and make a new tuple to link contextId and wordpress postId
    preg_match_all("/<[^>]*> <http:\/\/fise.iks-project.eu\/ontology\/extracted-from> (?P<contextId><[^>]*>)/", $subject, $output_array);

    $post_id_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-id> "' . $user_id . '"^^<http://www.w3.org/2001/XMLSchema#integer> .';

    $post_type_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-type> "' . $post_type . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

    $post_title_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-title> "' . $title . '"^^<http://www.w3.org/2001/XMLSchema#string> .';
            
    $post_description_field = $output_array['contextId'][0] . ' <' . $config['marmotta_server'] . 'ontology/wp/post-description> "' . $content . '"^^<http://www.w3.org/2001/XMLSchema#string> .';

    //append the new tuple to stanbol response
    $stanbol_response_augmented = $subject . ' ' . $post_id_field . ' ' . $post_type_field . ' ' . $post_title_field . ' ' . $post_description_field;

    //delete previous triples saved in marmotta
    $marmotaQuery = 'DELETE WHERE { 
                            ?document ?post_id"'.$user_id.'" ^^xsd:integer .
                            ?document ?post_type"' . $post_type .'"^^xsd:string .
                       }';
    
    // log delete query
//    log_sparql_queries( $marmotaQuery );
    
    $encoded_query = urlencode($marmotaQuery);
    $marmotta_url = $config['marmotta_server'] . 'marmotta/sparql/update?query=' . $encoded_query . '&output=json';
    try
    {
        $marmotta_response = wp_remote_post($marmotta_url, array(
          'method' => 'GET',
          'timeout' => $config['timeout']
           )
        );
    }catch(Exception $e) {}

    // save in marmota
    $marmotta_url = $config['marmotta_server'] . 'marmotta/import/upload';
    try
    {
        $marmotta_response = wp_remote_post($marmotta_url, array(
            'method' => 'POST',
            'headers' => array('Content-Type' => 'text/turtle'),
            'body' => $stanbol_response_augmented,
            'timeout' => $config['timeout']
          )
        );
    }  catch (Exception $e)
    {

    }
  }
}

add_action( 'wp_ajax_ef_enhance_content', 'ef_enhance_content' );

/**
 * return content entities from Stanbol
 * 
 * @global type $config
 */
function ef_enhance_content() {
  global $config;
  $stanbol_response = '';
  $stanbol_url = $config['stanbol_server'] . 'enhancer';
  try
  {
      $stanbol_response = wp_remote_post($stanbol_url, array(
          'method'  => 'POST',
          'headers' => array('Content-Type' => 'text/plain; charset=UTF-8'),
          'body'    => $_POST['text'],
          'timeout' => $config['timeout']
        )
      );
  }  catch (Exception $e){}

  if(sizeof($stanbol_response->errors)){
      echo '';
  }
    
  echo json_encode( $stanbol_response );
  die();
}

/**
 * Log sparql query in sparql_log.txt
 * 
 * @param type $query
 */
function log_sparql_queries( $query ) {
    $query = trim(preg_replace('/\s\s+/', ' ', $query));
    $log_file = fopen(ABSPATH.'/sparql_log.txt', 'a');
    fwrite($log_file, date("F j, Y, g:i:s a").": ");
    fwrite($log_file, $query . "\n");
    fclose($log_file);
}

?>
