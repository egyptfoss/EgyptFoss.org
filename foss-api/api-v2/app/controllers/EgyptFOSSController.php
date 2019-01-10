<?php
require 'PasswordHash.php';
use Gettext\Translations;

class EgyptFOSSController {

	protected function renderJson($response, $status, $data) {
		$response->withStatus($status);
		$response->write(json_encode($data));
		$response = $response->withHeader(
			'Content-type', 'application/json; charset=utf-8'
		);
		return $response;
	}

    protected function check_term_exists( $term, $taxonomy ) {
      $term = rtrim($term);
      $term = html_entity_decode(ltrim($term));
      $term_taxonomy = array();
      $term_taxonomy = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
              				->where(function ($query) use ($term) {
      if(ctype_digit($term)) {
      	$query->where('terms.term_id', '=', $term);
      } else {
      	$query->where('terms.name', '=', htmlentities($term));
        $query->orWhere('terms.name_ar', '=', $term);
        
      }
      })->where('taxonomy', '=', $taxonomy)->first();
      return $term_taxonomy;
    }

	protected function insert_term( $term, $taxonomy, $args = array() ) {
    $term = rtrim($term);
    $term = ltrim($term);
		$term_taxonomy_record = array();
		$saved = false;
		// TODO YF: check that the taxonomy exists
		$defaults = array( 'alias_of' => '', 'description' => '', 'parent' => 0, 'slug' => '');
		$args['name'] = $term;
		$args['taxonomy'] = $taxonomy;
		$args = array_merge($defaults, $args);
		// insert term
		if($term != ''){
			$term_record = new Term();
			$term_record['name'] = $term;
      $term_slug = str_replace(' ', '-', $term);
			$term_record['slug'] = $term_slug;
			$saved = $term_record->save();
		}
		// insert term taxonomy
		if($saved) {
			$term_taxonomy_record = new TermTaxonomy();
			$term_taxonomy_record['term_id'] = $term_record->id;
			$term_taxonomy_record['taxonomy'] = $args['taxonomy'];
			$term_taxonomy_record['description'] = $args['description'];
			$term_taxonomy_record->save();
		}
		return $term_taxonomy_record;
	}

	protected function check_user_relation($user_id, $term_taxonomy_id) {
		$result = UserRelation::where(function ($query) use ($user_id, $term_taxonomy_id) {
				$query->where('user_id', '=', $user_id)
							->where('term_taxonomy_id', '=', $term_taxonomy_id);
			})->get()->first();
		return $result;
	}

	protected function add_user_relation($user_id, $term_taxonomy_id) {
		$user_relation = new UserRelation();
		$user_relation->addUserRelation($user_id, $term_taxonomy_id);
		$saved = $user_relation->save();
		return $saved;
	}

	protected function remove_user_relations($user_id, $term_taxonomy_ids) {
		$result = UserRelation::where(function ($query) use ($user_id, $term_taxonomy_ids) {
				$query->where('user_id', '=', $user_id)
							->whereNotIn('term_taxonomy_id', $term_taxonomy_ids);
			})->delete();
		return $result;
	}

	protected function get_user_taxonomies($user_id, $taxonomy) {
		$taxonomies = array();
		$result = UserRelation::select("terms.name")
							->join("term_taxonomy", "user_relationships.term_taxonomy_id", "=", "term_taxonomy.term_taxonomy_id")
							->join("terms", "term_taxonomy.term_id", "=", "terms.term_id")
							->where("user_id", "=", $user_id)
							->where("term_taxonomy.taxonomy", "=", $taxonomy)->get();
		foreach ($result as $key => $fields) {
			array_push($taxonomies, $fields->name);
		}
		return $taxonomies;
	}

	protected function user_can($user_id, $capability) {
		global $capabilities;
		$user_meta = new Usermeta;
		$role = $user_meta->getRole($user_id);
		return (array_key_exists($role, $capabilities) && in_array($capability, $capabilities[$role])) ? true : false;
	}
  
  protected function return_user_info_list($user_id, $lang = 'en', $no_lang = false) {
    global $en_sub_types, $ar_sub_types;
    $user = User::where('ID','=', $user_id)->first();
    
    // --- Get profile image --- //
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');;
    $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
    $image_location = glob($directory . "*bpfull*");            
    foreach(glob($directory . "*bpfull*") as $image_name){
      $image_name = end(explode("/", $image_name));
      $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
    }
    
    if (empty($image_location)){
      $meta_key = "wsl_current_user_image";
      $user_meta = new Usermeta();
      $meta = $user_meta->getUserMeta($user_id, $meta_key);
      $image = $meta;
      if (empty($meta)){ // -- default gravatar image -- //
        $email = $user->user_email;
        $size = '150'; //The image size
        $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
      }
    }

    $userMeta = Usermeta::where( 'user_id', '=', $user_id )->where( "meta_key", "=", "registration_data" )->first();
    $registeration_data = unserialize( $userMeta->meta_value );
    $registeration_data = ( is_array( $registeration_data ) ) ? $registeration_data : unserialize($registeration_data);

    $retval= array(
      'display_name' => html_entity_decode($user->display_name, ENT_QUOTES),
      'username' => $user->user_nicename,
      'profile_picture' => $image,
    );
    
    if( $no_lang ) {
      $retval['type'] = array( 
        'en' => __( $registeration_data['type'], 'egyptfoss', 'en' ),
        'ar' => __( $registeration_data['type'], 'egyptfoss', 'ar' )
      );
      $retval['subtype'] = array(
        'en' => $en_sub_types[ $registeration_data['sub_type'] ],
        'ar' => $ar_sub_types[ $registeration_data['sub_type'] ]
      );
    }
    else {
      $retval ['type']  = __( $registeration_data['type'], 'egyptfoss', $lang );
      $retval ['subtype']  = ( $lang == 'en' ) ? $en_sub_types[ $registeration_data['sub_type'] ] : $ar_sub_types[ $registeration_data[ 'sub_type' ] ];
      
    }
    
    return $retval;
  }
  
  protected function ef_image_sizes($url, $preferred_size)
  {
    $original_url = $url;
    $sizes = array( 
        '64x64' => '-64x64',
        '150x150' => '-150x150',
        '340x210' => '-340x210',
        'original' => '');
    $keys  = array_keys($sizes);
    $current_size_index = array_search($preferred_size, $keys);
    $path_parts = pathinfo($url);
    $absPath = dirnameWithLevels(__FILE__,5);
    for ($i = $current_size_index; $i>=0; $i--)
    {  
      $new_url = $path_parts['dirname']."/".$path_parts['filename'].$sizes[$keys[$i]].".".$path_parts['extension'];
      preg_match("/wp-content(.*)/", $new_url,$matches);
      if(file_exists($absPath."/".$matches[0]))
      {
        return $new_url;
      }
      return $original_url;
    }
  }
  
  protected function is_url_exist($url){
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
    return $status;
  }
  
  protected function ef_load_data_counts($count , $number_of_data)
  {
    $total_pages = 1;
    if(!isset($number_of_data) || $number_of_data == -1)
    {
      $total_pages = 1;
    }else
    {
      $total_pages = (ceil($count/$number_of_data));
    }

    return ['total_count'=> $count,"total_pages" => $total_pages];
  }
  
  protected function ef_humanFileSize($size,$unit="") {
    if( (!$unit && $size >= 1<<30) || $unit == "GB")
      return number_format($size/(1<<30),2)." GB";
    if( (!$unit && $size >= 1<<20) || $unit == "MB")
      return number_format($size/(1<<20),2)." MB";
    if( (!$unit && $size >= 1<<10) || $unit == "KB")
      return number_format($size/(1<<10),2)." KB";
    
    $size = number_format($size);
    if($size < 0)
      $size = 0;
    return $size." bytes";
  }
  
  function ef_retrieve_remote_file_size($url){
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_NOBODY, TRUE);

      $data = curl_exec($ch);
      $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

      curl_close($ch);
      return $size;
 }
 
 function ef_retrieve_taxonomy_id($taxonomy_type, $taxonomy_name, $returnTermID = true)
 {
    if(isset($taxonomy_name) && !empty($taxonomy_name))
    {
      $term = new Term();
      $category = $term->get_terms_by_name_language($taxonomy_name,$taxonomy_type);
      if($category)
      {
          if($returnTermID){
            return $category->term_id;
          }
          else{
            return $category->term_taxonomy_id;
          }
      }
    }
    
    return -1;
 }
 
 function getSystemData($array_name, $option = null, $type = "key",$single = false) {
    switch ($array_name) {
      case "account_sub_types":
        global $account_sub_types;
        global $en_sub_types;
        global $ar_sub_types;
        $results;
        if ($option) {
          switch ($type) {
            case "key":
              if (array_key_exists($option, $account_sub_types)) {
                $results[$option] = array("name" => $en_sub_types[$option], "name_ar" => $ar_sub_types[$option]);
              }
            break;  
            case "name":
              $key = array_search($option, $en_sub_types);
              if ($key) {
                $results[$key] = array("name" => $en_sub_types[$key], "name_ar" => $ar_sub_types[$key]);
              }
            break;
            case "name_ar":
              $key = array_search($option, $ar_sub_types);
              if ($key) {
                $results[$key] = array("name" => $en_sub_types[$key], "name_ar" => $ar_sub_types[$key]);
              }
            break;
            default :
              if (array_key_exists($option, $account_sub_types)) {
                $results[$option] = array("name" => $en_sub_types[$option], "name_ar" => $ar_sub_types[$option]);
              }
            break;
          }
        } else {
          foreach ($account_sub_types as $key => $value) {
            $results[$key] = array("name" => $en_sub_types[$key], "name_ar" => $ar_sub_types[$key]);
          }
        }
      break;
    }
    if($single == true)
      return array_values($results);
    return $results;
  }
  
  function ef_return_name_by_lang($term, $lang)
  {
    $category = '';
    if($lang == "ar"){
      if($term->name_ar != ''){
        $category = $term->name_ar;
      }
      else {
        $category = $term->name;
      }
    }
    else {
      $category = $term->name;
    }
    
    return $category;
  }

  protected function service_average_rate($service_id) {
    $average_rate = array('rate' => 0, 'reviewers_count' => 0);
    $rate_meta = Postmeta::where('post_id', '=', $service_id)->whereIn('meta_key', array('reviewers_count', 'rate'))->get();
    foreach ($rate_meta as $meta) {
      if($meta['meta_key'] == 'rate') {
        $average_rate[$meta['meta_key']] = round($meta['meta_value'], 1);
      } else {
        $average_rate[$meta['meta_key']] = $meta['meta_value'];
      }
    }
    return $average_rate;
  }
  
  protected  function is_first_suggestion($user_id)
  {
    $suggester_badge = new Badge($user_id);
    $badge = $suggester_badge->efb_get_badges_by_name( '"suggestions_l1"' );
    $badge_id = $badge->id;
    $first_suggestion_exists = $suggester_badge->efb_is_user_took_badge($badge_id);
    
    if($first_suggestion_exists){
      return false;
    }
    
    return true;
  }
  
  /**
    * Shorten string with specific number of characters 
    * without cutting words
    * 
    * @param type $string
    * @param type $length
    * @param type $post_append
    * @return type
    */
  public static function shorten_description( $description ) {
    // convert special characters
    $short_description = html_entity_decode( $description );
    
    // revmoe tags from string
    $short_description = strip_tags( $short_description );
    
      
    // return short description
    return self::shorten_string( $short_description, 150 );
  }
  
  /**
    * Shorten string with specific number of characters 
    * without cutting words
    * 
    * @param type $string
    * @param type $length
    * @param type $post_append
    * @return type
    */
  public static function shorten_string( $string, $length = 10, $post_append = '...' ) {
    // return string itself if not characters will be trimmed
    if( strlen( trim( $string ) ) <= $length ) {
      $shorten_st = $string;
    }
    else {
      // start cutting
      $shorten_st = substr( $string, 0, strrpos( substr( $string, 0, $length ), ' ' ) );

      // add indicator if string has been cutten
      $shorten_st .= $post_append;
    }

    return $shorten_st;
  }
  
  /**
   * Log sparql query in sparql_log.txt
   * 
   * @param type $query
   */
  public static function log_sparql_queries( $query ) {
      $query = trim(preg_replace('/\s\s+/', ' ', $query));
      $log_file = fopen( '../sparql_log.txt', 'a' );
      fwrite($log_file, date("F j, Y, g:i:s a").": ");
      fwrite($log_file, $query . "\n");
      fclose($log_file);
  }
}
