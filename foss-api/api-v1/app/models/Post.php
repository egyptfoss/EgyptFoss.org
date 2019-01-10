<?php

class Post extends BaseModel {
	protected $table = 'posts';
	public $timestamps = false;
	// protected $hidden = ['user_pass'];

  protected $attributes = array(
    'post_content' => '',
    'post_excerpt' => '',
    'to_ping' => '',
    'pinged' => '',
    'post_content_filtered' => ''
  );

  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
  }
  
  public function addPost($post_data){
    $this->post_title = $post_data['post_title'];
    $name = strtolower(str_replace(' ','-',trim($post_data['post_title'])));
    if(strlen($name) !== mb_strlen($name,'UTF-8'))
    {
      $name = substr($name,0,70);
    }
    $name = self::generatePostName($name);
    $this->post_name = urlencode($name);
    $this->post_content = (isset($post_data['post_content'])) ? $post_data['post_content'] : '';
    $this->post_type = $post_data['post_type'];
    $this->post_status = $post_data['post_status'];
    $this->ping_status = "closed";
    $this->post_author = (isset($post_data['post_author']))?$post_data['post_author']:1;
    $this->post_parent = (isset($post_data['post_parent']))?$post_data['post_parent']:0;
    $this->guid = (isset($post_data['guid']))?$post_data['guid']:"";
    $this->comment_status = "closed";
    $date = gmdate('Y-m-d H:i:s');
    $this->post_date = date('Y-m-d H:i:s');
    $this->post_date_gmt = $date;
    $this->post_modified = date('Y-m-d H:i:s');
    $this->post_modified_gmt = $date;
    $this->comment_status = 'open';
    $this->post_excerpt = '';
    $this->pinged = '';
    $this->post_content_filtered = '';
    $this->to_ping = '';
    return $this;
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
  
  public function updateGUID($post_id, $post_type) {
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
    $this->guid = $home_url->option_value.'/?post_type='.$post_type.'&#038;p='.$post_id;
    return $this;
  }

  public function getPost($post_id) {
    $post = Post::where('ID', '=', $post_id)->first();
    return $post ;
  }
  
  public static function checkPostTitleExists($titleToCheck,$editPostId) {
    global $foss_prefix;
    $sql = "select ID from {$foss_prefix}posts where post_title = '{$titleToCheck}' and post_type='product' and ID <> {$editPostId}";
    return  self::getConnectionResolver()->connection()->select($sql); 
  }

  public static function getPostsBy($args) {
     $results = Post::Where('post_type', '=', $args['post_type']);
     if(isset($args['post_status'])) {
      $results->where('post_status', '=', $args['post_status']);
     }
     $results->where('ID', '=', $args["post_id"]);
     return $results;
  }

  public function getPostByID($post_id, $post_type, $post_status) {
    $post = Post::where('ID', '=', $post_id)->where('post_type', '=', $post_type);
    
    if(!empty($post_status)) {
      $post->where('posts.post_status','=', $post_status);
    } else {
      $post->where(function ($query){
         $query->where('posts.post_status','=','publish')
         ->orWhere('posts.post_status','=','pending');  
       });
    }
    return $post->first() ;
  }
  
  public function getPostLogo($logo_id){
    $post = Post::where('ID', '=', $logo_id)->first();
    return $post ;
  }
  
  public function get_products_by_filter($args = array(), $ef_product_filtered_taxs) {
    if ($args['lang'] == "en") {
      $current_lang = "en";
      $foriegn_lang = "ar";
    } else {
      $foriegn_lang = "en";
      $current_lang = "ar";
    }

    $filter_condition = "";
    $having_condition = "";
    $args['page_no'] = ($args['page_no'] == 0)?1:$args['page_no'];
    $page_no = ($args['page_no'] * $args['no_of_posts']) - $args['no_of_posts'];
    global $foss_prefix;
    $term_ids = array();
    foreach ($ef_product_filtered_taxs as $tax) {
      if(isset($args[$tax]))
      {
        
        $tax_id = EgyptFOSSController::ef_retrieve_taxonomy_id($tax, $args[$tax], false);
        if($tax_id != -1)
        {
          $term_ids = array_merge($term_ids, array($tax_id));
        }
      }  
      
    /*  if(isset($args[$tax]))
      {
      $term = Term::get_terms_by($args[$tax], $tax);
      if ($term) {
        $term_ids = array_merge($term_ids, array($term->term_taxonomy_id));
      }
      }*/
    }
    if (!empty($term_ids)) {
     $term_count = count($term_ids);
     $term_ids = join(',', $term_ids);
     $filter_condition = " and rel.term_taxonomy_id in ({$term_ids}) ";
     $having_condition = " having count(*) = {$term_count} ";
    }

    $sql = "SELECT * FROM {$foss_prefix}posts as post 
        join {$foss_prefix}postmeta as pmeta on post.ID = pmeta.post_id
        join {$foss_prefix}term_relationships as rel on post.ID = rel.object_id
        join {$foss_prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language' and 
        (pmeta.meta_value like '%\"{$current_lang}\"%' or (
        pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))
        {$filter_condition}
        group by post.ID
        {$having_condition}
        order by case when pmeta.meta_value like '%\"{$current_lang}\"%' then 1 else 2 end,post.post_date DESC ";
     if($page_no != -1 && $args['no_of_posts'] != -1)
      $sql.="  limit {$page_no},{$args['no_of_posts']} ";
    $results = $this->getConnection()->select($sql);
    return $results;
  }
  
  public function get_featured_product($args) {
    $foriegn_lang = "en";
    $current_lang = "ar";
    if ($args['lang'] == "en") {
      $current_lang = "en";
      $foriegn_lang = "ar";
    }
    
    $filter_condition = "";
    $having_condition = "";
    global $foss_prefix;
    
    $sql = Post::
    join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
    ->where('posts.post_status',$args['post_status'])
    ->where('posts.post_type',$args['post_type'])
    ->where('postmeta.meta_key','language')
    ->where(function ($query) use ($current_lang, $foriegn_lang,$args){
    $query->where('postmeta.meta_value', 'like', "%\"{$current_lang}\"%")
          ->orWhere('postmeta.meta_value', 'like', "%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%")
          ->orWhere('postmeta.meta_value','like',"%\"trashed\";i:1%");
    })->orWhere('postmeta.meta_key','=','is_featured')
      ->Where('postmeta.meta_value','=',1)
      ->groupBy('posts.ID')
      ->havingRaw('count(*) = 2')
      ->take($args['no_of_posts'])
      ->orderByRaw("RAND()")->get();
    return $sql;
  }
  
  public function add_post_translation($post_id, $lang, $post_type = "product") {
    $term = Term::where('slug', '=', $lang)->first();
    if ($term) {
      $term_tx = TermTaxonomy::getTermTaxonomy($term->term_id, "language");
      if($term_tx)
      {
      $term_rel = new TermRelation();
      $term_rel->addTermRelation($post_id, $term_tx->term_taxonomy_id);
      $term_rel->save();
      }
    }
    if( in_array($post_type, array('product', 'open_dataset','success_story')) ) {
    $product_meta = new Postmeta();
    $product_meta->addProductMeta($post_id, 'language', serialize(array(
      "slug" => "{$lang}",
      "translated_id" => 0)));
      $product_meta->save();
    }
  }
  
  public function link_post_translation($post_id, $post_lang, $translate_post_id,$isFixProductTranslation=false) {
    $foreign_lang = ($post_lang == "en") ? "ar" : "en";
    //$term_current_lang = Term::where('slug', '=', $post_lang)->first();
    //$term_tx_current_lang = TermTaxonomy::getTermTaxonomy($term->term_id, "language");
    //$term_foreign_lang = Term::where('slug', '=', $foreign_lang)->first();
    //$term_tx_foreign_lang = TermTaxonomy::getTermTaxonomy($term->term_id, "language");
    if(!$isFixProductTranslation)
    {
    $term_unique_id = new Term();
    $unique_id = uniqid("pll");
    $term_data = array("name" => $unique_id, "slug" => $unique_id);
    $term_unique_id->addTerm($term_data);
    $term_unique_id->save();

    $term_taxonomy_lnk_trans = new TermTaxonomy();
    $term_tax_lnk_data = array("taxonomy" => "post_translations",
      "description" => "a:2:{s:2:\"{$post_lang}\";i:{$post_id};s:2:\"{$foreign_lang}\";i:{$translate_post_id};}");
    $term_taxonomy_lnk_trans->addTermTaxonomy($term_unique_id->id, $term_tax_lnk_data);
    $term_taxonomy_lnk_trans->save();


    $term_rel = new TermRelation();
    $term_rel->addTermRelation($post_id, $term_taxonomy_lnk_trans->term_taxonomy_id);
    $term_rel->save();

    $term_rel = new TermRelation();
    $term_rel->addTermRelation($translate_post_id, $term_taxonomy_lnk_trans->term_taxonomy_id);
    $term_rel->save();
    }

    $product_meta = new Postmeta();
    $product_meta->updatePostMeta($post_id, 'language', serialize(array(
      "slug" => $post_lang,
      "translated_id" => $translate_post_id)));

    $product_meta = new Postmeta();
    $product_meta->updatePostMeta($translate_post_id, 'language', serialize(array(
      "slug" => $foreign_lang,
      "translated_id" => $post_id)));
    $product_meta->save();
  }
  
  public function updatePostTerms($post_id, $terms_data, $isCreate = false,$post_tax_names = array(), $no_serialize = false) {
    $term_ids = array();
    $term_taxonomy_ids = array();
    $term_tax_imploded = array();
    $post_history_data = array();
    foreach ($terms_data as $key => $terms) {
      $post_history_data = array_merge($post_history_data, array($key . "_text" => array()));
      $post_history_data = array_merge($post_history_data, array($key . "_ids" => array()));
      foreach ($terms as $term) {
        if (!empty($terms)) {
          $term = rtrim($term);
          $term = ltrim($term);
          if( $key == 'technology' ) {
            $term = html_entity_decode( $term );
          }
          else {
            $term = html_entity_decode( urldecode( $term ) );
          }
          $is_term = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                      ->where(function ($query) use ($term) {
                        if(ctype_digit($term)) {
                          $query->where('terms.term_id', '=', $term);
                        } else {
                          $query->where('terms.name', '=', htmlentities($term));
                          $query->orWhere('terms.name_ar', '=', htmlentities($term));
                        }
                      })->where('taxonomy', '=', $key)->first();
          if ($is_term) {
            $term_tx = TermTaxonomy::getTermTaxonomy($is_term->term_id, $key);
            if ($term_tx) {
              $isRelation = TermRelation::where('object_id', '=', $post_id)
                  ->where('term_taxonomy_id', '=', $term_tx->term_taxonomy_id)->get()->first();
              if ($isRelation == NULL) {
                $term_rel = new TermRelation();
                $term_rel->addTermRelation($post_id, $term_tx->term_taxonomy_id);
                $term_rel->save();
              }
              $term_taxonomy_ids = array_merge($term_taxonomy_ids, array($term_tx->term_taxonomy_id));
            }
            $term_ids = array_merge($term_ids, array($term_tx->term_id));
            $post_history_data[$key . "_ids"] = array_merge($post_history_data[$key . "_ids"], array($term_tx->term_id));
            $post_history_data[$key . "_text"] = array_merge($post_history_data[$key . "_text"], array($term));
          }
        }
      }
      if (!empty($term_ids)) {
        if ($key == 'type' || $key == 'industry'){
          $productMeta = new Postmeta();
          $productMeta->updatePostMeta($post_id, $key, $term_ids[0]);
        }
        else{
          $productMeta = new Postmeta();
          foreach ($term_ids as $k=>$term_tax_id){
            $term_ids[$k] = strval($term_tax_id);
          }
          
          // in case of "news"
          if( $no_serialize && $key != 'interest' ) {
            $productMeta->updatePostMeta($post_id, $key, $term_ids[0]);
          }
          else {
            $productMeta->updatePostMeta($post_id, $key, serialize($term_ids));
          }
        }
        
        if ($isCreate == false) {
          $term_tax_imploded = array_merge($term_tax_imploded, $term_taxonomy_ids);
        }
        
      }
      $term_ids = array();
    }
    if ($isCreate == false) {
      global $ef_registered_taxonomies;
      $ef_registered_taxonomies = array_merge($ef_registered_taxonomies,$post_tax_names);
      global $foss_prefix;
      $taxonomies_to_delete = "'" . implode("','", $ef_registered_taxonomies) . "'";
      $term_tax_imploded = implode(',', $term_tax_imploded);

      $sql = "SELECT rel.term_taxonomy_id FROM {$foss_prefix}term_relationships as rel 
      join {$foss_prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
      where rel.object_id = {$post_id} 
      and tax.taxonomy in ({$taxonomies_to_delete}) 
      and rel.term_taxonomy_id not in ({$term_tax_imploded})";
      $results = $this->getConnection()->select($sql);
      if ($results) {
        foreach ($results as $result) {
          $delete_sql = "delete from {$foss_prefix}term_relationships where object_id = {$post_id} and term_taxonomy_id = {$result->term_taxonomy_id} ";
          $this->getConnection()->delete($delete_sql);
        }
      }
      return $post_history_data;
    } else {
      return true;
    }
  }

  public function add_quotes($str) {
    return sprintf("'%s'", $str);
  }

  public function getProductsAddedByUser($xprofile_id, $no_of_posts, $skip_number, $lang ){
    global $foss_prefix;
    if ($lang == "en") {
      $current_lang = "en";
    } else {
      $current_lang = "ar";
    }
    
    $sql = "SELECT * FROM {$foss_prefix}posts as post 
        join {$foss_prefix}postmeta as pmeta on post.ID = pmeta.post_id
        join {$foss_prefix}term_relationships as rel on post.ID = rel.object_id
        join {$foss_prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (post.post_status = 'publish' and post.post_type = 'product')
        and (pmeta.meta_key = 'language')
        and post_author = '{$xprofile_id}'
        group by post.ID
        order by case when pmeta.meta_value like '%\"{$current_lang}\"%' then 1 else 2 end,post.post_date DESC   
        limit {$skip_number},{$no_of_posts} ";
    $results = $this->getConnection()->select($sql);
    return $results;
  }

  public function updateFeaturedImage($args,$post_id,$files) {
    $fileName = str_replace(" ", "-", urldecode( $files["name"] ) );
    $year = date('Y');
    $month = date('m');
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
    $args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
    preg_match("/(\.)+\w+$/", $files['name'], $extention);
    $args['post_title'] = str_replace($extention[0], '', $files['name']);
    $path = __DIR__;
    for ($d = 1; $d <= 4; $d++)
        $path = dirname($path);
    
    $uploaddir = $path . "/../wp-content/uploads/{$year}/{$month}/";
    if (!file_exists($uploaddir)) {
      mkdir($uploaddir, 0777, true); 
    }
    $uploadfile = $uploaddir . ($fileName);
    if (move_uploaded_file($files['tmp_name'], $uploadfile)) {
      $attachment = new Post();
      $attachment->addPost($args);
      $attachment->post_mime_type = $files['type'];
      $attachment->save();
      $Postmeta = new Postmeta();
      $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
      list($width, $height, $img_type, $attr) = getimagesize($uploadfile);

      //save different sizes for img
      $arrSizes = array();
      $newFileName = "";
      if($width > 64)
      {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 64, 64);
        if($newFileName != ""){
          $arrSizes["news-thumbnail-small"] = array(
              "file" => $newFileName,
              "width" => "64",
              "height" => "64",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );  
        }
      }
      if($width > 150)
      {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 150, 150);
        if($newFileName != ""){
          $arrSizes["thumbnail"] = array(
              "file" => $newFileName,
              "width" => "150",
              "height" => "150",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );  
        }
      }
      
      if($width > 300) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 300, 169);
        if($newFileName != ""){
          $arrSizes["medium"] = array(
              "file" => $newFileName,
              "width" => "300",
              "height" => "169",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          ); 
        }
      }
      
      if($width > 340) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 340, 210);
        if($newFileName != ""){
          $arrSizes["news-thumbnail"] = array(
              "file" => $newFileName,
              "width" => "340",
              "height" => "210",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );         
        }
      }
      
      if($width > 410) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 410, 250);
        if($newFileName != ""){
          $arrSizes["medium-img"] = array(
              "file" => $newFileName,
              "width" => "410",
              "height" => "250",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );         
        }
      }
      
      if($width > 675) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 675, 420);
        if($newFileName != ""){
          $arrSizes["news-featured"] = array(
              "file" => $newFileName,
              "width" => "675",
              "height" => "420",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          ); 
        }
      }
      
      if($width > 825) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 825, 320);
        if($newFileName != ""){
          $arrSizes["xlarge-img"] = array(
              "file" => $newFileName,
              "width" => "825",
              "height" => "320",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );  
        }
      }
      
      if($width > 1024) {
        $newFileName = self::resizeImg($uploadfile, $uploaddir, 1024,576);
        if($newFileName != ""){
          $arrSizes["large"] = array(
              "file" => $newFileName,
              "width" => "1024",
              "height" => "576",
              "mime-type" => image_type_to_mime_type(exif_imagetype($uploadfile))
          );         
        }
      }
      
      $image_meta_data = array("width" => $width,
      "height" => $height,
        "file" => "{$year}/{$month}/".$fileName,
        "sizes" => $arrSizes,
        "image_meta" => array(
          "aperture" => "",
          "credit" => "",
          "camera" => "",
          "caption" => "",
          "created_timestamp" => 0,
          "copyright" => "",
          "focal_length"=> 0,
          "iso"=> 0,
          "shutter_speed" => 0,
          "title" => "",
          "orientation" => 0
      ));
      $Postmeta->updatePostMeta($attachment->id, "_wp_attachment_metadata", serialize($image_meta_data));
      $Postmeta->updatePostMeta($post_id, "_thumbnail_id", $attachment->id);
    }
  }

  public function resizeImg($img, $upload_dir, $required_width, $required_height = 0){
    require_once 'ImageManipulation.php';
    $path_parts = pathinfo($img);
    $im = new ImageManipulator($img);

    list($width, $height) = getimagesize($img);
    $centreX = round($width / 2);
    $centreY = round($height / 2);

    $cropWidth  = $required_width;
    $cropHeight = $required_height;
    $cropWidthHalf  = round($cropWidth / 2); // could hard-code this but I'm keeping it flexible
    $cropHeightHalf = round($cropHeight / 2);

    $x1 = max(0, $centreX - $cropWidthHalf);
    $y1 = max(0, $centreY - $cropHeightHalf);

    $x2 = min($width, $centreX + $cropWidthHalf);
    $y2 = min($height, $centreY + $cropHeightHalf);
    
    $vdir_upload = $upload_dir;
    
    $im->crop($x1, $y1, $x2, $y2); // takes care of out of boundary conditions automatically
    $name = $path_parts['filename']."-".$required_width."x".$required_height.".".$path_parts['extension'];
    $im->save($vdir_upload.$name, exif_imagetype($img));
    
    return $name;
  }
  
  public function convertIntToArray($value)
  {
    return array($value);
  }
  
  public function uploadProductScreenshots($args,$post_id,$files, $fileName = "") 
  { 
    if($fileName == "")
      $files = $_FILES["screenshots"]; 
    else 
      $files = $_FILES[$fileName]; 
    $resources_ids = "";
    if($fileName != "")
    {
      $keys = array('name', 'type', 'tmp_name', 'error','size');
      $files = array_map("self::convertIntToArray", $files);
      $files = array_combine($keys, $files);
    }
    foreach ($files['name'] as $key => $value) 
    {
        if (($files['name'][$key] != "")) 
        {
            $fileName = str_replace(" ", "-", urldecode( $files["name"][$key] ) );
            
            $year = date('Y');
            $month = date('m');
            $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
            $args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
            preg_match("/(\.)+\w+$/", $files['name'][$key], $extention);
            $args['post_title'] = str_replace($extention[0], '', $files['name'][$key]);
            $path = __DIR__;
            for ($d = 1; $d <= 4; $d++)
                $path = dirname($path);

            $uploaddir = $path . "/../wp-content/uploads/{$year}/{$month}/";
            if (!file_exists($uploaddir)) {
              mkdir($uploaddir, 0777, true); 
            }
            $uploadfile = $uploaddir . ($fileName);
            if (move_uploaded_file($files['tmp_name'][$key], $uploadfile)) {
              $attachment = new Post();
              $attachment->addPost($args);
              $attachment->post_mime_type = $files['type'][$key];
              $attachment->save();
              $Postmeta = new Postmeta();
              $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
              //detect if file is image

                list($width, $height, $img_type, $attr) = getimagesize($uploadfile);
                $image_meta_data = array("width" => $width,
                "height" => $height,
                  "file" => "{$year}/{$month}/".$fileName,
                  "image_meta" => array(
                    "aperture" => "",
                    "credit" => "",
                    "camera" => "",
                    "caption" => "",
                    "created_timestamp" => 0,
                    "copyright" => "",
                    "focal_length"=> 0,
                    "iso"=> 0,
                    "shutter_speed" => 0,
                    "title" => "",
                    "orientation" => 0
                ));
                $Postmeta->updatePostMeta($attachment->id, "_wp_attachment_metadata", serialize($image_meta_data));
                $resources_ids = $resources_ids.$attachment->id.',';
            }
        }
    }
    
    return $resources_ids;
  }
  
  public function getProductsByInterest($interests, $fromDate, $today) {
    $products = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->join('postmeta as postmeta_description', 'posts.ID', '=', 'postmeta_description.post_id')
            ->join('postmeta as postmeta_license', 'posts.ID', '=', 'postmeta_license.post_id')
            ->join('postmeta as postmeta_developer', 'posts.ID', '=', 'postmeta_developer.post_id')
            ->leftjoin('term_relationships',  'posts.ID', '=', 'term_relationships.object_id')
            ->leftjoin('term_taxonomy',  'term_taxonomy.term_taxonomy_id', '=', 'term_relationships.term_taxonomy_id')
            ->leftjoin('terms',  'term_taxonomy.term_id', '=', 'terms.term_id')
            ->leftjoin('postmeta as attch',  'attch.post_id', '=', 'posts.ID')
            ->leftjoin('posts as attch_post',  'attch.meta_value', '=', 'attch_post.ID')
            ->select('posts.*', 'postmeta.*','terms.name as industry_name','attch_post.guid as product_logo',
                    'postmeta_description.meta_value as description','terms.name_ar as industry_name_ar'
                    ,'terms.slug as slug','postmeta_license.meta_value as license'
                    ,'postmeta_developer.meta_value as developer')
            ->where('postmeta.meta_key', '=', 'interest')
            ->where('postmeta_description.meta_key', '=', 'description')
            ->where('postmeta_license.meta_key', '=', 'license')
            ->where('postmeta_developer.meta_key', '=', 'developer')
            ->where('posts.post_type', '=', 'product')
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where('term_taxonomy.taxonomy','=','industry')
            ->where('attch.meta_key','=','_thumbnail_id')
            ->where(function ($query) use ($interests) {
                foreach ($interests as $interest) {
                    $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
                }
            })
            ->get();
    return $products;
  }

  public function getEventsByInterest($interests, $fromDate, $today) {
      $events = $this->distinct()
              ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
              ->join('postmeta as post_meta_event_start',  'posts.ID', '=', 'post_meta_event_start.post_id')
              ->join('postmeta as post_meta_event_end',  'posts.ID', '=', 'post_meta_event_end.post_id')
              ->leftjoin('postmeta as post_meta_price',  'posts.ID', '=', 'post_meta_price.post_id')
              ->leftjoin('postmeta as post_meta_symbol',  'posts.ID', '=', 'post_meta_symbol.post_id')
              ->select('posts.*', 'postmeta.*', 'post_meta_event_start.meta_value as StartDate',
                      'post_meta_event_end.meta_value as EndDate','post_meta_price.meta_value as Price',
                      'post_meta_symbol.meta_value as Symbol')
              ->where('postmeta.meta_key', '=', 'interest')
              ->where('post_meta_event_start.meta_key', '=', '_EventStartDate')
              ->where('post_meta_event_end.meta_key', '=', '_EventEndDate')
              ->where('post_meta_price.meta_key', '=', '_EventCost')
              ->where('post_meta_symbol.meta_key', '=', '_EventCurrencySymbol')
              ->where('posts.post_type', '=', 'tribe_events')
              ->where('posts.post_status', '=', 'publish')
              ->where('posts.post_date', '>', $fromDate)
              ->where('posts.post_date', '<', $today)
              ->where(function ($query) use ($interests) {
                  foreach ($interests as $interest) {
                      $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
                  }
              })
              ->get();
              
      return $events;
  }

  public function getNewsByInterest($interests, $fromDate, $today) {
    $news = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->join('postmeta as post_meta_subtitle',  'posts.ID', '=', 'post_meta_subtitle.post_id')
            ->join('postmeta as post_meta_description',  'posts.ID', '=', 'post_meta_description.post_id')
            ->join('postmeta as post_meta_news_category',  'posts.ID', '=', 'post_meta_news_category.post_id')
            ->leftjoin('terms', 'post_meta_news_category.meta_value', '=', 'terms.term_id')
        ->leftjoin('postmeta as attch',  'attch.post_id', '=', 'posts.ID')  
        ->leftjoin('posts as attch_post',  'attch.meta_value', '=', 'attch_post.ID')
            ->select('posts.*', 'post_meta_subtitle.meta_value as subtitle', 'terms.name as category', 
              'attch_post.guid as image', 'post_meta_description.meta_value as description')
            ->where('post_meta_news_category.meta_key', '=', 'news_category')
            ->where('post_meta_description.meta_key', '=', 'description')
            ->where('attch.meta_key','=','_thumbnail_id')
            ->where('post_meta_subtitle.meta_key', '=', 'subtitle')
            ->where('posts.post_type', '=', 'news')
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })
            ->get();
    return $news;
  }
    
  public function getSuccessStoriesByInterest($interests, $fromDate, $today) {
    $success_stories = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->join('postmeta as post_meta_success_story_category',  'posts.ID', '=', 'post_meta_success_story_category.post_id')
            ->leftjoin('terms', 'post_meta_success_story_category.meta_value', '=', 'terms.term_id')
        ->leftjoin('postmeta as attch',  'attch.post_id', '=', 'posts.ID')
        ->leftjoin('posts as attch_post',  'attch.meta_value', '=', 'attch_post.ID')
            ->select('posts.*', 'terms.name as category', 'terms.name_ar as category_ar', 'attch_post.guid as image')
            ->where('post_meta_success_story_category.meta_key', '=', 'success_story_category')
            ->where('attch.meta_key','=','_thumbnail_id')
            ->where('posts.post_type', '=', 'success_story')
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })
            ->get();
    return $success_stories;
  }
  
  public function getExpertThoughtsByInterest($interests, $fromDate, $today) {
    $expert_thoughts = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->leftjoin('postmeta as attch',  'attch.post_id', '=', 'posts.ID')
            ->leftjoin('posts as attch_post',  'attch.meta_value', '=', 'attch_post.ID')
            ->select('posts.*', 'attch_post.guid as image')
            ->where('attch.meta_key','=','_thumbnail_id')
            ->where('posts.post_type', '=', 'expert_thought')
            ->where('posts.post_status', '=', 'publish')
            ->where('postmeta.meta_key', '=', 'interest')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })
            ->get();
            
    return $expert_thoughts;
  }
  
  public function getServicesByInterest($interests, $fromDate, $today) {
    $expert_thoughts = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->leftjoin('postmeta as attch',  'attch.post_id', '=', 'posts.ID')
            ->leftjoin('posts as attch_post',  'attch.meta_value', '=', 'attch_post.ID')
            ->select('posts.*', 'attch_post.guid as image')
            ->where('attch.meta_key','=','_thumbnail_id')
            ->where('posts.post_type', '=', 'service')
            ->where('posts.post_status', '=', 'publish')
            ->where('postmeta.meta_key', '=', 'interest')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })->get();
            
    return $expert_thoughts;
  }
  
  public function getOpenDatasetsByInterest($interests, $fromDate, $today) {
    $datasets = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->join('postmeta as post_meta_dataset_type',  'posts.ID', '=', 'post_meta_dataset_type.post_id')
            ->join('postmeta as post_meta_description',  'posts.ID', '=', 'post_meta_description.post_id')
            ->join('postmeta as post_meta_publisher',  'posts.ID', '=', 'post_meta_publisher.post_id')
            ->leftjoin('terms', 'post_meta_dataset_type.meta_value', '=', 'terms.term_id')
            ->join('postmeta as post_meta_license',  'posts.ID', '=', 'post_meta_license.post_id')
            ->leftjoin('terms as terms_license', 'post_meta_license.meta_value', '=', 'terms_license.term_id')
            ->join('postmeta as post_meta_theme',  'posts.ID', '=', 'post_meta_theme.post_id')
            ->leftjoin('terms as terms_theme', 'post_meta_theme.meta_value', '=', 'terms_theme.term_id')
            ->select('posts.*', 'terms.name as dataset_type', 'terms.name_ar as dataset_type_ar',
              'terms_license.name as dataset_license','terms_license.name_ar as dataset_license_ar',
              'terms_theme.name as theme', 'terms_theme.name_ar as theme_ar',
              'post_meta_description.meta_value as description', 'post_meta_publisher.meta_value as publisher')
            ->where('post_meta_publisher.meta_key', '=', 'publisher')
            ->where('post_meta_description.meta_key', '=', 'description')
            ->where('post_meta_dataset_type.meta_key', '=', 'dataset_type')
            ->where('post_meta_license.meta_key', '=', 'datasets_license')
            ->where('post_meta_theme.meta_key', '=', 'theme')
            ->where('posts.post_type', '=', 'open_dataset')
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })
            ->get();
    return $datasets;
  }
  
  //get requests by user interests
  public function getRequestsByInterest($interests, $fromDate, $today) {
      $results = $this->distinct()
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id')
            ->join('postmeta as pmeta_type',  'posts.ID', '=', 'pmeta_type.post_id')
            ->join('postmeta as pmeta_deadline',  'posts.ID', '=', 'pmeta_deadline.post_id')
            ->join('postmeta as pmeta_theme',  'posts.ID', '=', 'pmeta_theme.post_id')
            ->join('postmeta as pmeta_description',  'posts.ID', '=', 'pmeta_description.post_id')
            ->leftjoin('terms as terms_type', 'pmeta_type.meta_value', '=', 'terms_type.term_id')
            ->join('postmeta as pmeta_bussiness',  'posts.ID', '=', 'pmeta_bussiness.post_id')
            ->leftjoin('terms as terms_bussiness', 'pmeta_bussiness.meta_value', '=', 'terms_bussiness.term_id')                
            ->leftjoin('terms as terms_theme', 'pmeta_theme.meta_value', '=', 'terms_theme.term_id')                
            ->select('posts.ID','posts.post_date','posts.post_name',
                    'posts.post_title', 'posts.post_author', 'posts.guid', 'terms_type.name as type', 'terms_type.name_ar as type_ar', 
                    'terms_bussiness.name as bussiness_relationship','terms_bussiness.name_ar as bussiness_relationship_ar',
                    'terms_type.slug as type_slug','pmeta_description.meta_value as description',
                    'terms_theme.name as theme', 'terms_theme.name_ar as theme_ar',
                    'terms_theme.slug as theme_slug', 'pmeta_deadline.meta_value as deadline')
            ->where('postmeta.meta_key', '=', 'interest')
            ->where('pmeta_type.meta_key', '=', 'request_center_type')
            ->where('pmeta_theme.meta_key', '=', 'theme')
            ->where('pmeta_bussiness.meta_key', '=', 'target_bussiness_relationship')
            ->where('pmeta_description.meta_key', '=', 'description')
            ->where('pmeta_deadline.meta_key', '=', 'deadline')
            ->where('posts.post_type', '=', 'request_center')
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_date', '>', $fromDate)
            ->where('posts.post_date', '<', $today)
            ->where(function ($query) use ($interests) {
              foreach ($interests as $interest) {
                $query->orWhere('postmeta.meta_value', 'like', '%' . $interest . '%');
              }
            })
            ->get();
      
      return $results;
  }
    
  public function getAllPosts($post_type, $post_status, $no_of_posts, $skip_number, $lang){  // Generic to be useful for news and events
    $results = $this->distinct()
            ->join('term_relationships', 'posts.ID', '=', 'term_relationships.object_id')
            ->join('term_taxonomy', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_taxonomy_id')
            ->join('terms', 'term_taxonomy.term_id', '=', 'terms.term_id')
            ->select('posts.ID', 'post_author', 'posts.post_title', 'posts.post_date','post_content')
            ->where('post_type', '=', $post_type)
            ->where('post_status', '=', $post_status)
            ->where('terms.slug', '=', $lang)
            ->take($no_of_posts)->skip($skip_number)
            ->orderBy('posts.post_date', 'DESC')
            ->get();
    return $results;
  }
  
  public function getEventsBy($args) {
    $events = Post::join('events_venues', 'posts.ID', '=', 'events_venues.event_id');
    if(!empty($args['post_status'])) {
      $events->where('posts.post_status','=',$args['post_status']);
    } else {
      $events->where(function ($query) use ($args) {
        $query->where('posts.post_status','=','publish')
              ->orWhere('posts.post_status','=','pending');  
      });
    }
    $events->where('posts.post_type','=',$args['post_type']);
    if(!empty($args['author'])) {
      $events->where('posts.post_author','=',$args['author']);
    }
    if(!empty($args['find_by_title'])) {
      $events->where('posts.post_title','like',"%".$args['find_by_title']."%");
    }
    if(!empty($args['find_by_date'])) {
      $events->where(function ($query) use ($args){
        $query->whereDate('events_venues.event_start_datetime', '>=', date($args['from_date']))
          ->whereDate('events_venues.event_start_datetime', '<=', date($args['to_date']));
        
        $query->orWhereDate('events_venues.event_end_datetime', '>=', date($args['from_date']))
        ->whereDate('events_venues.event_end_datetime', '<=', date($args['to_date']));
      });
    }
    $events->groupBy('posts.ID');
    if(!empty($args['no_of_events'])) {
      $events->take($args['no_of_events']);
    }
    if(!empty($args['offset'])) {
      $events->skip($args['offset']);
    }
    if(!empty($args['find_by_date']) && !isset($args['order_by']) ) {
      global $foss_prefix;
      $events->orderByRaw("date({$foss_prefix}events_venues.event_start_datetime) ASC ");
    } else {
      $events = $events->orderBy("posts.ID","DESC");
    }
    return $events->get();
  }
  
  public function postmeta() {
    return $this->hasMany('Postmeta','post_id','ID');
  }
    
  public function addVenues($args) {
    $venue = new Post;
    $venue->post_author = 1;
    $venue->post_title = $args['venue'];
    $venue->post_type = 'tribe_venue';
    $venue->save();
    $venue_id = $venue->id;
    $venue->updateGUID($venue_id, 'tribe_venue');
    $venue->add_post_translation($venue_id, $args['lang']);
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
    
  public function addOrganizers($args) {
    // Add new organizer
    $organizer = new Post;
    $organizer->post_author = 1;
    $organizer->post_title = $args['organizer'];
    $organizer->post_type = 'tribe_organizer';
    $organizer->save();
    $organizer_id = $organizer->id;
    $organizer->updateGUID($organizer_id, 'tribe_organizer');
    $organizer->add_post_translation($organizer_id, $args['lang']);
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
  }
    
  public function addEvent($args) {
      global $events_types;
      $venue_record = Post::where('post_title' , '=', $args['venue'])->where('post_type' , '=', 'tribe_venue')->first();
      if ($venue_record !== null) {
        $args['venue_name'] = $args['venue'];
        $args['venue'] = $venue_record->ID;
      }
      
      $organizer_record = Post::where('post_title' , '=', $args['organizer'])->where('post_type' , '=', 'tribe_organizer')->first();
      if ($organizer_record !== null) {
        $args['organizer'] = $organizer_record->ID;
      }
        
      $post = new Post;
      $add_event_array = array(
        'post_title'  => $args['title'],
        'post_type'   => 'tribe_events',
        'post_content' => $args['description'],
        'post_status' => 'publish'
      );
      $post->addPost($add_event_array);
      $post->post_author = 1;
      $post->comment_status = "open";
      $post->save();
      $post_id = $post->id;
      $post->updateGUID($post_id, 'tribe_events');
      $post->add_post_translation($post_id, $args['lang']);

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
  }
  
  public function addProduct($args) {
      $termTax = new TermTaxonomy();
      /*$terms_data = array(
        "industry" => array($args['industry']),
        "type" => array($args['type']),
        "license" => split(",",$args['license']),
        "technology" => split(",",$args['technology']), 
        "platform" => split(",",$args['platform']),
        "interest" => split(",",$args['interest'])
      ); */               

      $terms_data = array(
        "industry" => array($args['industry'])
      ); 
      
      //isset type
      if(isset($args['type']) && !empty($args['type']))
      {
        $terms_data["type"] = array($args['type']);
      }
      
      //split multiple items
      $multiItems = array('license', 'technology', 'platform', 'interest');
      foreach($multiItems as $item)
      {
        if(isset($args[$item]) && !empty($args[$item]))
        {
          $terms_data[$item] = split(",",$args[$item]);
        }
      }
      
      global $ef_product_multi_uncreated_tax;
      global $ef_product_single_uncreated_tax;
      $isCreated = $termTax->saveTermTaxonomies($terms_data,  array_merge($ef_product_multi_uncreated_tax,$ef_product_single_uncreated_tax)); 
      $shouldreCheck = (isset($args['shouldCheck'])) ? $args['shouldCheck'] : true;
      if(!$isCreated && $shouldreCheck){
        return -1;
      }

      $post = new Post;
      $args['post_type'] = "product";
      $args['post_status'] = $args['status'] ? $args['status'] : "pending";
      $args['post_title'] = $args['title'];
      $args['post_author'] = $args['user_id'];
      $post->addPost($args);
      $post->save();
      $post_id = $post->id;
      $post->updateGUID($post_id, 'product');
      $post->save();
      if($_POST['lang'])
          $post->add_post_translation($post_id, $_POST['lang']);
      else if ($args['lang'])
          $post->add_post_translation($post_id, $args['lang']);

      $metaProductData = [ 'description', 'developer',
        'functionality', 'usage_hints', 'references', 'link_to_source'];

      foreach ($metaProductData as $param) {
        if (isset($_POST[$param])){
          $post_meta = new Postmeta;
          $post_meta->addProductMeta($post_id, $param, $args[$param]);
          $post_meta->save();
        }else if(isset($args[$param]))
        {
          $post_meta = new Postmeta;
          $post_meta->addProductMeta($post_id, $param, $args[$param]);
          $post_meta->save();   
        }
      }

      $product = new Post();
      $product->updatePostTerms($post_id, $terms_data,true);

      return $post_id;
  }
  
  public function addExternalProduct($args) {        
      $termTax = new TermTaxonomy();
      $terms_data = array("industry" => array($args['post_industry']),
                      "type" => array($args['post_type']),
                      "license" => array(),
                      "technology" => array(), 
                      "platform" => array(),
                      "interest" => array()
                  );                
      
      $post = new Post;
      $args['post_type'] = "product";
      $args['post_status'] = $args['post_status']?$args['post_status']:"pending";
      $args['post_title'] = $args['post_title'];
      $post->addPost($args);
      $post->save();
      $post_id = $post->id;
      $post->updateGUID($post_id, 'product');
      $post->save();
      $post->add_post_translation($post_id, $args['lang']);

      $metaProductData = [ 'description', 'developer',
        'functionality', 'usage_hints', 'references', 'link_to_source'];

      foreach ($metaProductData as $param) {
        if (isset($_POST[$param])){
          $post_meta = new Postmeta;
          $post_meta->addProductMeta($post_id, $param, $args[$param]);
          $post_meta->save();
        }else if(isset($args[$param]))
        {
          $post_meta = new Postmeta;
          $post_meta->addProductMeta($post_id, $param, $args[$param]);
          $post_meta->save();   
        }
      }

      $product = new Post();
      $product->updatePostTerms($post_id, $terms_data,true);
      
      //check Icon
      $attchment_id = '';
      if(isset($args['uploadIcon']) && $args['uploadIcon'] == true)
      {
          if($args['icon'] != '')
          {
              $url = $args['icon'];
              $img_args = array("post_status" => "inherit",
                 "post_type" => "attachment",
                 "post_author" => $args['user_id'],
                 "icon_url"  => $url
              );
              
              self::addToFiles('post_image', $url);
              $attchment_id = $post->updateExternalProductImage($img_args, $post->id, $_FILES['post_image']);
          }
      }else if($args['icon_id'] != '')
      {
          $Postmeta = new Postmeta();
          $Postmeta->updatePostMeta($post->id, "_thumbnail_id", $args['icon_id']);
      }
      
      //check screenshots
      $images_ids_str = '';
      if(isset($args['uploadScreenshots']) && $args['uploadScreenshots'] == true)
      {
          $screenshots = $args['screenshots'];
          for($i = 0; $i < sizeof($screenshots); $i++)
          {
              $url = $screenshots[$i];
              if($url != '')
              {
                  $url = str_replace(' ', '', $url);
                  $img_args = array("post_status" => "inherit",
                     "post_type" => "attachment",
                     "post_author" => $args['user_id'],
                     "icon_url"  => $url
                  );
                  self::addToFiles('post_image', $url);
                  $files = $_FILES['post_image'];
                  $fileName = str_replace(" ", "-", $files["name"]);
                  $year = date('Y');
                  $month = date('m');
                  $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
                  $img_args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
                  preg_match("/(\.)+\w+$/", $files['name'], $extention);
                  $img_args['post_title'] = str_replace($extention[0], '', $files['name']);
                  $path = __DIR__;
                  for ($d = 1; $d <= 4; $d++)
                      $path = dirname($path);

                  $uploaddir = $path . "/../wp-content/uploads/{$year}/{$month}/";
                  if (!file_exists($uploaddir)) {
                      mkdir($uploaddir, 0777, true);
                  }
                  $uploadfile = $uploaddir . ($fileName);

                  if (copy($img_args['icon_url'], $uploadfile)) {
                      $attachment = new Post();
                      $attachment->addPost($img_args);
                      $attachment->post_mime_type = $files['type'];
                      $attachment->save();

                      $Postmeta = new Postmeta();
                      $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
                      list($width, $height, $img_type, $attr) = getimagesize($uploadfile);
                          $image_meta_data = array("width" => $width,
                              "height" => $height,
                              "file" => "{$year}/{$month}/".$fileName,
                              "image_meta" => array(
                                "aperture" => "",
                                "credit" => "",
                                "camera" => "",
                                "caption" => "",
                                "created_timestamp" => 0,
                                "copyright" => "",
                                "focal_length"=> 0,
                                "iso"=> 0,
                                "shutter_speed" => 0,
                                "title" => "",
                                "orientation" => 0
                          ));
                      $images_ids_str = $images_ids_str.$attachment->id.',';
                      //$images_ids = array_merge($images_ids, array($attachment->id));
                  }
              }
          }
          
          if(!empty($images_ids_str))
          {
              ///$images_ids = implode(',',$images_ids);
              $images_ids_str =  rtrim($images_ids_str, ",");
              $Postmeta_post = new Postmeta();
              $Postmeta_post->updatePostMeta($post->id, 'fg_perm_metadata', $images_ids_str);
          }else{
              $images_ids_str = '';
          }
      }else if($args['screenshots_id'] != '')
      {
          $Postmeta_post = new Postmeta();
          $Postmeta_post->updatePostMeta($post->id, 'fg_perm_metadata', $args['screenshots_id']);
      }
      
      if($args['lang'] == "en"){
          return $post_id.'|'.$images_ids_str.'|'.$attchment_id;
      }
      else{
          return $post_id;
      }
  }
  
  public function addToFiles($key, $url) {
      $tempName = tempnam('/tmp', 'php_files');
      $originalName = basename(parse_url($url, PHP_URL_PATH));

      $imgRawData = file_get_contents($url);
      file_put_contents($tempName, $imgRawData);
      $_FILES[$key] = array(
          'name' => $originalName,
          'type' => mime_content_type($tempName),
          'tmp_name' => $tempName,
          'error' => 0,
          'size' => strlen($imgRawData),
      );
  }
  
  public function updateExternalProductImage($args, $post_id, $files) {
    $fileName = str_replace(" ", "-", $files["name"]);
    $year = date('Y');
    $month = date('m');
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
    $args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
    preg_match("/(\.)+\w+$/", $files['name'], $extention);
    $args['post_title'] = str_replace($extention[0], '', $files['name']);
    $path = __DIR__;
    for ($d = 1; $d <= 4; $d++) {
      $path = dirname($path);
    }
    $uploaddir = $path . "/../wp-content/uploads/{$year}/{$month}/";
    if (!file_exists($uploaddir)) {
      mkdir($uploaddir, 0777, true);
    }
    $uploadfile = $uploaddir . ($fileName);
    
    if (copy($args['icon_url'], $uploadfile)) {
      $attachment = new Post();
      $attachment->addPost($args);
      $attachment->post_mime_type = $files['type'];
      $attachment->save();
      
      $Postmeta = new Postmeta();
      $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
      list($width, $height, $img_type, $attr) = getimagesize($uploadfile);
      $image_meta_data = array("width" => $width,
      "height" => $height,
        "file" => "{$year}/{$month}/".$fileName,
        "image_meta" => array(
          "aperture" => "",
          "credit" => "",
          "camera" => "",
          "caption" => "",
          "created_timestamp" => 0,
          "copyright" => "",
          "focal_length"=> 0,
          "iso"=> 0,
          "shutter_speed" => 0,
          "title" => "",
          "orientation" => 0
    ));
      $Postmeta->updatePostMeta($attachment->id, "_wp_attachment_metadata", serialize($image_meta_data));
      $Postmeta->updatePostMeta($post_id, "_thumbnail_id", $attachment->id);
      
      if(isset($args['icon_url']))
      {
          return $attachment->id;
      }
    }
  }
  
  //used for success stories & news
  public function getAllfilteringByCategory($post_type, $post_status, $no_of_posts, $skip_number, $lang, $category, $category_tax_name){  // Generic to be useful for news and events
        global $foss_prefix;
        if($category == '') {
          $results = $this->distinct();
          if($post_type == 'news')
          {
            $results->join('term_relationships', 'posts.ID', '=', 'term_relationships.object_id')
                  ->join('term_taxonomy', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_taxonomy_id')
                  ->join('terms', 'term_taxonomy.term_id', '=', 'terms.term_id');
          }
          $results->join('postmeta as cat', 'posts.ID', '=', 'cat.post_id')
                  ->select('posts.ID', 'post_author', 'posts.post_title', 'posts.post_date','post_content')
                  ->where('post_type', '=', $post_type)
                  ->where('post_status', '=', $post_status);
          if($post_type == 'news')
          {
            $results->where('terms.slug', '=', $lang);
          }
          else if( $post_type == 'success_story' ) {
                $foriegn_lang = ( $lang == 'ar' )? 'en' : 'ar';
                $results->whereRaw(
                        "{$foss_prefix}cat.meta_key = 'language' "
                        ."and "
                        ."( "
                            ."{$foss_prefix}cat.meta_value LIKE ? "
                            ."or "
                            ."( "
                                ."{$foss_prefix}cat.meta_value LIKE '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' "
                                ."or "
                                ."{$foss_prefix}cat.meta_value LIKE '%\"trashed\";i:1%' "
                            .") "
                        .") "
                , array( "%{$lang}%" ));            
            }
          if($no_of_posts != -1 && $skip_number != -1){
            $results->take($no_of_posts)->skip($skip_number);
          }
          $results->orderBy('posts.post_date', 'DESC');
        } else {
            $results = $this->distinct();
            if($post_type == 'news')
            {            
              $results->join('term_relationships', 'posts.ID', '=', 'term_relationships.object_id')
                ->join('term_taxonomy', 'term_relationships.term_taxonomy_id', '=', 'term_taxonomy.term_taxonomy_id')
                ->join('terms', 'term_taxonomy.term_id', '=', 'terms.term_id');
            }
            $results->join('postmeta as cat', 'posts.ID', '=', 'cat.post_id')
                    ->join('postmeta as cat1', 'posts.ID', '=', 'cat1.post_id')
                    ->select('posts.ID', 'post_author', 'posts.post_title', 'posts.post_date','post_content')
                    ->where('post_type', '=', $post_type)
                    ->where('post_status', '=', $post_status);
            if($post_type == 'news')
            {                            
              $results->where('terms.slug', '=', $lang);
            }
            else if( $post_type == 'success_story' ) {
                $foriegn_lang = ( $lang == 'ar' )? 'en' : 'ar';
                $results->whereRaw(
                        " {$foss_prefix}cat.meta_key = 'language' "
                        ."and "
                        ."( "
                            ."{$foss_prefix}cat.meta_value LIKE ? "
                            ."or "
                            ."( "
                                ."{$foss_prefix}cat.meta_value LIKE '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' "
                                ."or "
                                ."{$foss_prefix}cat.meta_value LIKE '%\"trashed\";i:1%' "
                            .") "
                        .") "
                , array( "%{$lang}%" ));            
            }
            $results->where('cat1.meta_key', '=', $category_tax_name)
                ->where('cat1.meta_value','=', $category);
                if($no_of_posts != -1 && $skip_number != -1){
                  $results->take($no_of_posts)->skip($skip_number);
                }
                $results->orderBy('posts.post_date', 'DESC');
        }
        
        return $results->get();
  }
  
  public function getAllOpenDataSets($post_type, $post_status, $no_of_posts, $skip_number, $lang, $type, $theme, $license, $format){
        $forg_lang = "ar";
        if($lang == "ar"){
            $forg_lang = "en";   
        }
        $results = $this->distinct()
                ->join('postmeta as pmeta','pmeta.post_id','=','posts.ID')
                ->leftjoin('postmeta as attch','attch.post_id','=','posts.ID')
                ->leftjoin('postmeta as type', 'posts.ID', '=', 'type.post_id')
                ->leftjoin('postmeta as theme', 'posts.ID', '=', 'theme.post_id')
                ->leftjoin('postmeta as license', 'posts.ID', '=', 'license.post_id')
                ->select('posts.ID', 'posts.post_author', 'posts.post_title', 'posts.post_date','posts.post_content')
                ->where('posts.post_type', '=', $post_type)
                ->where('posts.post_status', '=', $post_status)
                ->whereRaw(" (wpRuvF8_pmeta.meta_key = 'language' and (wpRuvF8_pmeta.meta_value like '%$lang%' or (wpRuvF8_pmeta.meta_value like '%\"slug\";s:2:\"$forg_lang\";s:13:\"translated_id\";i:0%' or wpRuvF8_pmeta.meta_value like '%\"trashed\";i:1%')))")
                ->whereRaw(($type == '')?"wpRuvF8_type.meta_key = 'dataset_type'":" wpRuvF8_type.meta_key = 'dataset_type' and wpRuvF8_type.meta_value =".$type)
                ->whereRaw(($theme == '')?"wpRuvF8_theme.meta_key = 'theme'":" wpRuvF8_theme.meta_key = 'theme' and wpRuvF8_theme.meta_value =".$theme)
                ->whereRaw(($license == '')?"wpRuvF8_license.meta_key = 'datasets_license'":" wpRuvF8_license.meta_key = 'datasets_license' and wpRuvF8_license.meta_value =".$license)
                ->whereRaw(($format == '')?"wpRuvF8_attch.meta_key = 'dataset_formats'":" wpRuvF8_attch.meta_key = 'dataset_formats' and wpRuvF8_attch.meta_value like '%".$format."%'");
        if($no_of_posts != -1)
          $results->take($no_of_posts);
        
        if($skip_number != -1)
          $results->skip($skip_number);
        
        $results->orderBy('posts.post_date', 'DESC');
        
        return $results->get();
  }
  
  public function getAllRequestCenter($post_type, $post_status, $no_of_posts, $skip_number, $lang, $type, $theme, $target){
    global $foss_prefix;
    $forg_lang = "ar";
    if($lang == "ar"){
      $forg_lang = "en";
    }
    
    $query = "SELECT DISTINCT posts.ID, posts.post_author, posts.post_title, posts.post_date, posts.post_content";
    $query .= " FROM {$foss_prefix}posts AS posts";
    $query .= " JOIN {$foss_prefix}postmeta AS pmeta ON pmeta.post_id = posts.ID";
    $query .= " LEFT JOIN {$foss_prefix}postmeta AS type ON posts.ID = type.post_id";
    $query .= " LEFT JOIN {$foss_prefix}postmeta AS theme ON posts.ID = theme.post_id";
    $query .= " LEFT JOIN {$foss_prefix}postmeta AS target ON posts.ID = target.post_id";
    //$query .= " LEFT JOIN (SELECT request_id,COUNT(id) AS responses_count FROM {$foss_prefix}request_threads) r ON posts.ID = r.request_id";
    $query .= " WHERE posts.post_type = 'request_center'";
    $query .= " AND posts.post_status = 'publish'";
    
    
    $query .= ($type == '')?" AND type.meta_key = 'request_center_type'":" AND type.meta_key = 'request_center_type' and type.meta_value =".$type;
    $query .= ($theme == '')?" AND theme.meta_key = 'theme'":" AND theme.meta_key = 'theme' and theme.meta_value =".$theme;
    $query .= ($target == '')?" AND target.meta_key = 'target_bussiness_relationship'":" AND target.meta_key = 'target_bussiness_relationship' and target.meta_value =".$target;
    
    $query .=" ORDER BY posts.post_date DESC";
    
    if($skip_number != -1 && $no_of_posts != -1) {
       // $skip_number  = ($skip_number == 0)?1:$skip_number;
       // $page_no      = ($skip_number * $no_of_posts) - $no_of_posts;
        $query       .= "  LIMIT {$skip_number},{$no_of_posts} ";
    } 
    
    return $this->getConnection()->select($query);    
  }
  
  public function getAllServices( $post_type, $post_status, $no_of_posts, $skip_number, $category, $technology, $theme, $type, $subtype ){
    global $foss_prefix;

    $where_condition = '';
    $join_condition = '';

    // Conditions
    if( !empty( $technology ) ) {
      $join_condition   .= " JOIN {$foss_prefix}term_relationships AS tech_rel ON post.ID = tech_rel.object_id".
                           " JOIN {$foss_prefix}term_taxonomy AS tech_termtax ON tech_rel.term_taxonomy_id = tech_termtax.term_taxonomy_id";
      $where_condition  .= " AND tech_termtax.taxonomy = 'technology' AND tech_termtax.term_id = {$technology}";
    }

    if( !empty( $theme ) ) {
      $join_condition   .= " JOIN {$foss_prefix}postmeta AS theme_pmeta ON post.ID = theme_pmeta.post_id AND theme_pmeta.meta_key = 'theme'";
      $where_condition  .= " AND theme_pmeta.meta_value = {$theme}";
    }

    if( !empty( $category ) ) {
      $where_condition  .= " AND pmeta.meta_key = 'service_category' AND pmeta.meta_value = {$category}";
    }

    if( !empty( $type ) ) {
      $where_condition  .= " AND umeta.meta_value like '%:\"{$type}\";%' ";
    }

    if( !empty( $subtype ) ) {
      $where_condition  .= " AND umeta.meta_value like '%:\"{$subtype}\";%' ";
    }

    $sql = "SELECT post.ID, post.post_author, user.display_name, post.post_date, post.post_content, post.post_title, post.guid".
           " FROM {$foss_prefix}posts AS post".
           " JOIN {$foss_prefix}users AS user ON post.post_author = user.ID".
           " JOIN {$foss_prefix}usermeta AS umeta ON post.post_author = umeta.user_id AND umeta.meta_key = 'registration_data'".
           " JOIN {$foss_prefix}postmeta AS pmeta ON post.ID = pmeta.post_id".
            $join_condition.
           " WHERE (post.post_status = '{$post_status}' and post.post_type = '{$post_type}')".
            $where_condition.
           " GROUP BY post.ID".
           " ORDER BY post.post_date DESC";
           
           if( $skip_number != -1 && $no_of_posts != -1 ) {
            $sql .= " LIMIT $skip_number, $no_of_posts ";
           }

    return $this->getConnection()->select($sql);    
  }

  public function getPostAttachments($attachments_list) {
    $attachments_ids = array();
    $attachments = array();
    $resources = explode("|||",$attachments_list);
    for($i = 0; $i < sizeof($resources); $i++)
    {
      array_push($attachments_ids, $resources[$i]);
    }
    
    if (!empty($attachments_ids)) {
      $attachments = Post::whereIn('ID', $attachments_ids)->get(['post_title AS title', 'guid AS link','post_mime_type AS extension']);
    }
    return $attachments;
  }
  
  public function updatePostTermsSingle($post_id, $terms_data, $isCreate = false,$post_tax_names = array()) {
    $term_ids = array();
    $term_taxonomy_ids = array();
    $term_tax_imploded = array();
    $post_history_data = array();
    foreach ($terms_data as $key => $terms) {
      $post_history_data = array_merge($post_history_data, array($key . "_text" => array()));
      $post_history_data = array_merge($post_history_data, array($key . "_ids" => array()));
      foreach ($terms as $term) {
        if (!empty($term)) {
          $term = rtrim($term);
          $term = ltrim($term);
          $term = html_entity_decode(urldecode($term));
          $is_term = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                      ->where(function ($query) use ($term) {
                        if(ctype_digit($term)) {
                          $query->where('terms.term_id', '=', $term);
                        } else {
                          $query->where('terms.name', '=', htmlentities($term));
                          $query->orWhere('terms.name_ar', '=', htmlentities($term));
                        }
                      })->where('taxonomy', '=', $key)->first();
          if ($is_term) {
            $term_tx = TermTaxonomy::getTermTaxonomy($is_term->term_id, $key);
            if ($term_tx) {
              $isRelation = TermRelation::where('object_id', '=', $post_id)
                  ->where('term_taxonomy_id', '=', $term_tx->term_taxonomy_id)->get()->first();
              if ($isRelation == NULL) {
                $term_rel = new TermRelation();
                $term_rel->addTermRelation($post_id, $term_tx->term_taxonomy_id);
                $term_rel->save();
              }
              $term_taxonomy_ids = array_merge($term_taxonomy_ids, array($term_tx->term_taxonomy_id));
            }
            $term_ids = array_merge($term_ids, array($term_tx->term_id));
            $post_history_data[$key . "_ids"] = array_merge($post_history_data[$key . "_ids"], array($term_tx->term_id));
            $post_history_data[$key . "_text"] = array_merge($post_history_data[$key . "_text"], array($term));
          }
        }
      }
      if (!empty($term_ids)) {
        if ($key == 'type' || $key == 'industry'){
          $productMeta = new Postmeta();
          $productMeta->updatePostMeta($post_id, $key, $term_ids[0]);
        }
        else{
          $productMeta = new Postmeta();
          foreach ($term_ids as $k=>$term_tax_id){
            $term_ids[$k] = strval($term_tax_id);
          }      
          $productMeta->updatePostMeta($post_id, $key, implode(',',$term_ids));
        }
        
        if ($isCreate == false) {
          $term_tax_imploded = array_merge($term_tax_imploded, $term_taxonomy_ids);
        }
        
      }
      $term_ids = array();
    }
    if ($isCreate == false) {
      global $ef_registered_taxonomies;
      $ef_registered_taxonomies = array_merge($ef_registered_taxonomies,$post_tax_names);
      global $foss_prefix;
      $taxonomies_to_delete = "'" . implode("','", $ef_registered_taxonomies) . "'";
      $term_tax_imploded = implode(',', $term_tax_imploded);

      $sql = "SELECT rel.term_taxonomy_id FROM {$foss_prefix}term_relationships as rel 
      join {$foss_prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
      where rel.object_id = {$post_id} 
      and tax.taxonomy in ({$taxonomies_to_delete}) 
      and rel.term_taxonomy_id not in ({$term_tax_imploded})";
      $results = $this->getConnection()->select($sql);
      if ($results) {
        foreach ($results as $result) {
          $delete_sql = "delete from {$foss_prefix}term_relationships where object_id = {$post_id} and term_taxonomy_id = {$result->term_taxonomy_id} ";
          $this->getConnection()->delete($delete_sql);
        }
      }
      return $post_history_data;
    } else {
      return true;
    }
  }
  
  public function getContributionBy($args) {
    if( $args['post_type'] == 'expert_thought' ) {
      $results = Post::leftjoin('postmeta', 'posts.ID', '=', 'postmeta.post_id');
    }
    else {
      $results = Post::join('postmeta', 'posts.ID', '=', 'postmeta.post_id');
    }
    if(!empty($args['lang']) && !empty($args['forg_lang']))
    {
        $results->join('postmeta as pmeta', 'posts.ID', '=', 'pmeta.post_id');
    }
    
     if( !empty($args['post_status']) && !in_array( $args['post_status'], array( 'request_center', 'service' ) ) ) 
     {
       $results->where('posts.post_status','=',$args['post_status']);
     }else if( in_array( $args['post_type'], array( 'request_center', 'service' ) ) )
     {
        $results->where(function ($query) use ($args){
          $query->where('posts.post_status','=','publish')
          ->orWhere('posts.post_status','=','pending')
          ->orWhere('posts.post_status','=','archive');  
        });       
     }else
     {
       $results->where(function ($query) use ($args){
          $query->where('posts.post_status','=','publish')
          ->orWhere('posts.post_status','=','pending');  
        });
     }
      $results->where('posts.post_type','=',$args['post_type']);
      if(!empty($args['author']))
      {
        $results->where('posts.post_author','=',$args['author']);
      }
      
      
        if(!empty($args['lang']) && !empty($args['forg_lang']))
        {
            $lang = $args['lang'];
            $foriegn_lang = $args['forg_lang'];
            global $foss_prefix;
            $results->whereRaw(" ({$foss_prefix}pmeta.meta_key = 'language' and ({$foss_prefix}pmeta.meta_value like '%{$lang}%' or ({$foss_prefix}pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or {$foss_prefix}pmeta.meta_value like '%\"trashed\";i:1%')))");
        }
      
      $results->groupBy('posts.ID');
      if(!empty($args['no_of_tax']))
      {
        $results->take($args['no_of_tax']);
      }
      if(!empty($args['offset']))
      {
        $results->skip($args['offset']);
      }
      
      $results = $results->orderBy("posts.post_date","DESC");
      return $results->get();
  }
  
  public function getPostCountByType($args) {
    $results = Post::Where('post_type', '=', $args['post_type']);
    if(!empty($args['post_status']))
    {
      $results->Where('post_status', '=', $args['post_status']);
    }
    return $results->get();
  }
    
  public function updatePostStatus($post_id, $status) {
    $original_post = Post::where('ID','=', $post_id);
    if($original_post->first())
    {
        $original_post->update(array("ID"=> $post_id,"post_status"=> $status));
    }
  }
    
  public function getPostTranslationId($post_id, &$returnPostLang = "") {
    $term_taxonomy = $this->distinct()
      ->join('postmeta as pmeta', 'pmeta.post_id', '=', 'posts.ID')
      ->join('term_relationships as rel', function($q) use ($post_id) {
        $q->on('posts.ID', '=', 'rel.object_id')
        ->where('rel.object_id', '=', $post_id);
      })
      ->join('term_taxonomy as tax', function($q) use ($post_id) {
        $q->on('rel.term_taxonomy_id', '=', 'tax.term_taxonomy_id');
      })
      ->select('tax.term_taxonomy_id','tax.description')
      ->where('tax.taxonomy', '=', "post_translations")
      ->first();
    if (!empty($term_taxonomy)) {
      $returnPostLang = $term_taxonomy->description;
      $result = TermRelation::Where("object_id", "!=", $post_id)
        ->where("term_taxonomy_id", "=", $term_taxonomy->term_taxonomy_id)
        ->select('object_id')
        ->distinct()
        ->first();
      if (!empty($result)) {
        return $result->object_id;
      }
    }
    return 0;
  }

  public function getProductListContributors($post_id) {
    global $foss_prefix;
    $sql = "select user_id,sum(contributions_count) as contributions_count,updated_at from
            (
            (select {$foss_prefix}posts.post_author as user_id, 1 as contributions_count,{$foss_prefix}posts.post_date as updated_at
            FROM {$foss_prefix}posts
            where {$foss_prefix}posts.ID = ".  $post_id.")
            union
            (
            select {$foss_prefix}posts_history.user_id,count(*) as contributions_count,max({$foss_prefix}posts_history.updated_at) as updated_at
            FROM {$foss_prefix}posts_history
            where {$foss_prefix}posts_history.post_id = ". $post_id."
            group by {$foss_prefix}posts_history.user_id
            )) as contri
            group by user_id
            order by contributions_count desc,updated_at desc";
    $results = $this->getConnection()->select($sql); 
    return $results;
  }
    
  public function deleteAllProducts() {
      global $foss_prefix;
      $AllProducts = Post::where('post_status','=','publish')
                      ->where('post_type','=','product')
                      ->get();
      for($i = 0; $i < sizeof($AllProducts); $i++)
      {
          $post_id = $AllProducts[$i]->ID;
          
          //delete term taxnomy and term of product            
          $term_taxonomy = $this->distinct()
          ->join('postmeta as pmeta', 'pmeta.post_id', '=', 'posts.ID')
          ->join('term_relationships as rel', function($q) use ($post_id) {
            $q->on('posts.ID', '=', 'rel.object_id')
            ->where('rel.object_id', '=', $post_id);
          })
          ->join('term_taxonomy as tax', function($q) use ($post_id) {
            $q->on('rel.term_taxonomy_id', '=', 'tax.term_taxonomy_id');
          })
          ->select('tax.term_taxonomy_id','tax.term_id')
          ->where('tax.taxonomy', '=', "post_translations")
          ->first();
          if($term_taxonomy != null)
          {
              $delete_sql = "delete from {$foss_prefix}terms where term_id = {$term_taxonomy->term_id}";
              $this->getConnection()->delete($delete_sql);
              
              $delete_sql = "delete from {$foss_prefix}term_taxonomy where term_taxonomy_id = {$term_taxonomy->term_taxonomy_id}";
              $this->getConnection()->delete($delete_sql);               
          }
          
          //delete term relation
          $delete_sql = "delete from {$foss_prefix}term_relationships where object_id = {$post_id}";
          $this->getConnection()->delete($delete_sql);            
          
          /*
          //delete images physically of product if exists
          $screenshot_ids = Postmeta::select('meta_value')
                    ->where('meta_key','=','fg_perm_metadata')
                    ->where('post_id','=', $post_id)
                    ->first();
          
          $screenshot_array = explode(",",$screenshot_ids);
          for($z = 0; $z < sizeof($screenshot_ids);$z++)
          {
            $attachment = Post::where('ID','=',$screenshot_array[$z])
                      ->where('post_type','=','attachment')->first();
            if($attachment)
            {
              $url_path = $attachment->guid;
              //unlink($url_path);
            }
          }*/
          
          //delete post meta
          $delete_sql = "delete from {$foss_prefix}postmeta where post_id = {$post_id}";
          $this->getConnection()->delete($delete_sql);
          
          
          //delete post history
          $delete_sql = "delete from {$foss_prefix}posts_history where post_id = {$post_id}";
          $this->getConnection()->delete($delete_sql);
                      
          //delete post
          $delete_sql = "delete from {$foss_prefix}posts where ID = {$post_id}";
          $this->getConnection()->delete($delete_sql);                        
      }
  }
  
  public function getRequestsByResponses($args) {
      global $foss_prefix;
      $sql = "(SELECT post.* FROM {$foss_prefix}posts as post join {$foss_prefix}request_threads as resp on resp.request_id = post.ID where (post.post_status = 'publish' or post.post_status = 'archive') and post.post_type = '{$args['post_type']}' and resp.user_id = {$args['author']} and resp.responses_count > 0 group by post.ID order by resp.updated_at DESC ";
      if( $args[ 'offset' ] != -1 && $args[ 'no_of_posts' ] != -1 ) {
         $sql.= "limit {$args['offset']},{$args['no_of_posts']}";
      }
      $sql.= ")";  
      $results = $this->getConnection()->select($sql); 
      return $results;
  }

  public function getContributedOpenDatasets($args){
    global $foss_prefix;
    $no_of_posts = $args['no_of_posts'];
    $offset = $args['offset'];

    $whereCondition = "and (postmeta.meta_value = 'publish' or postmeta.meta_value = 'pending')";
    if($args['post_status'] == 'publish')
    {
      $whereCondition = "and (postmeta.meta_value = 'publish')";
    }

    $sql = "select published.* from
      (
      SELECT post.*, CONCAT(SUBSTRING(postmeta.meta_key, 1, CHAR_LENGTH(postmeta.meta_key) - 7), '_resource_status') as meta_key 
              FROM  {$foss_prefix}posts as post
                  join  {$foss_prefix}postmeta as postmeta on postmeta.post_id = post.ID
                  join  {$foss_prefix}posts as attachment  on postmeta.meta_value = attachment.ID
                  where post.post_type = 'open_dataset'
                  and post.post_status = 'publish'
                  and postmeta.meta_key like '%_upload%'
                  and attachment.post_author <> post.post_author
                  and attachment.post_author = {$args['author']}
                  group by post.ID
      ) as published 
      join {$foss_prefix}postmeta as postmeta on postmeta.post_id = published.ID
      where postmeta.meta_key = published.meta_key
      $whereCondition";
    
    if($args['no_of_posts']){ 
      $sql.= "limit $offset, $no_of_posts";
    }

    $results = $this->getConnection()->select($sql); 
    return $results;
  }

  public function insertOpendatasetsTypes() {
    global $foss_prefix;
    global $extension_mime_types;
    
    // types
    $sql = "SELECT p.ID
            FROM `{$foss_prefix}posts` p 
            JOIN `{$foss_prefix}postmeta` m ON p.ID = m.post_id
            WHERE `post_type` = 'open_dataset'
            AND p.ID NOT IN ( SELECT post_id 
              FROM `{$foss_prefix}postmeta` 
              WHERE `meta_key` = 'dataset_type'
            )
            GROUP BY p.ID";
    $results = $this->getConnection()->select($sql);
    foreach ($results as $result) {
      $value = Postmeta::select('meta_value')->where('post_id', '=', $result->ID)->where('meta_key', '=', 'type')->first();
      if($value != NULL) {
        $meta = new Postmeta();
        $meta->addProductMeta($result->ID, 'dataset_type', $value->meta_value);
        $meta->save();
      }
    }
    // licenses
    $sql = "SELECT p.ID
            FROM `{$foss_prefix}posts` p 
            JOIN `{$foss_prefix}postmeta` m ON p.ID = m.post_id
            WHERE `post_type` = 'open_dataset'
            AND p.ID NOT IN ( SELECT post_id 
              FROM `{$foss_prefix}postmeta` 
              WHERE `meta_key` = 'datasets_license'
            )
            GROUP BY p.ID";
    $results = $this->getConnection()->select($sql);
    foreach ($results as $result) {
      $value = Postmeta::select('meta_value')->where('post_id', '=', $result->ID)->where('meta_key', '=', 'license')->first();
      if($value != NULL) {
        $meta = new Postmeta();
        $meta->addProductMeta($result->ID, 'datasets_license', $value->meta_value);
        $meta->save();
      }
    }

    // languages
    $sql = "SELECT p.ID
            FROM `{$foss_prefix}posts` p 
            JOIN `{$foss_prefix}postmeta` m ON p.ID = m.post_id
            WHERE `post_type` = 'open_dataset'
            AND p.ID NOT IN ( SELECT post_id 
              FROM `{$foss_prefix}postmeta` 
              WHERE `meta_key` = 'language'
            )
            GROUP BY p.ID";
    $results = $this->getConnection()->select($sql);
    foreach ($results as $result) {
      $dataset = Post::find($result->ID);
      $dataset->add_post_translation($result->ID, 'en', $post_type = "open_dataset");
    }

    // resources
    $open_datasets = Post::where('post_type', '=', 'open_dataset')->get();
    foreach ($open_datasets as $open_dataset) {
      $formats_open_dataset = "";
      $resources_ids = "";
      $resources_count = Postmeta::where('post_id', '=', $open_dataset->ID)->where('meta_key', '=', 'resources')->first();
      $resources_count = $resources_count->meta_value;
      $attachments = array();
      $attachments_status = array();
      for($i= 0; $i < $resources_count; $i++) {
        $resource_id = Postmeta::where('post_id', '=', $open_dataset->ID)->where('meta_key', '=', 'resources_'.$i.'_upload')->first();
        array_push($attachments, $resource_id->meta_value);
        $resource_status = Postmeta::where('post_id', '=', $open_dataset->ID)->where('meta_key', '=', 'resources_'.$i.'_resource_status')->first();
        $meta_value = ($resource_status == null) ? 'publish' : $resource_status->meta_value;
        array_push($attachments_status, $meta_value);
      }
      if ( $attachments ) {
        $indx = 0;
        foreach ( $attachments as $attachment ) {
          if($attachments_status[$indx] == 'publish') {
            $resources_ids = $resources_ids.$attachment.'|||';
            $attachment_path = Post::where('ID', '=', $attachment)->first();
            $attachment_path = $attachment_path->guid;
            $attachment_ext = pathinfo($attachment_path, PATHINFO_EXTENSION);
            $formats_open_dataset = $formats_open_dataset.$extension_mime_types[strtolower($attachment_ext)].'|||';
          }
          $indx++;
        }
      }
      $meta = new Postmeta;
      $meta->updatePostMeta($open_dataset->ID, 'dataset_formats', substr($formats_open_dataset, 0, -3));
      $meta->updatePostMeta($open_dataset->ID, 'resources_ids', substr($resources_ids, 0, -3));
    }
  }
  
  /*
   * Semantic Search API
   */
  function retrieveAllPostsSearchResult($ids, $ids_en, $ids_ar,$postType, $query_string)
  {
    global $foss_prefix;
    $sqlQuery = "select {$foss_prefix}posts.*, {$foss_prefix}postmeta.meta_value from {$foss_prefix}posts left join {$foss_prefix}postmeta "
    . " on {$foss_prefix}posts.ID = {$foss_prefix}postmeta.post_id and {$foss_prefix}postmeta.meta_key = 'description' "
    . " where ";
            
    //add where condition with the retreived postIds to normal wordpress search query 
    if (count($ids) > 0) {
      if(!is_array($ids)) {
          $ids = explode (",", $ids);
      }

      if(!is_array($ids_en)) {
          $ids_en = explode (",", $ids_en);
      }

      if(!is_array($ids_ar)) {
          $ids_ar = explode (",", $ids_ar);
      }

      $ids_string = implode(",", $ids);
      $_SESSION["search_ids"] = $ids_string;
      $sqlQuery .= "(
                      ({$foss_prefix}posts.post_title LIKE '%$query_string%') OR "
                      . "({$foss_prefix}posts.post_content LIKE '%$query_string%') OR "
                      . "({$foss_prefix}postmeta.meta_value LIKE '%$query_string%') OR "
                      . "({$foss_prefix}posts.ID IN (".implode(', ', $ids).")) ) "
                      . "AND {$foss_prefix}posts.post_type IN ('$postType') "
                      . "AND {$foss_prefix}posts.post_status = 'publish'"
                      . "order by case when {$foss_prefix}posts.post_title like '%$query_string%' then 1 "
                      . "when {$foss_prefix}posts.post_content like '%$query_string%' then 1 "
                      . "when {$foss_prefix}postmeta.meta_value like '%$query_string%'  then 1 "
                      . "when {$foss_prefix}posts.ID IN (".  implode(",", $ids).") then 2 end, {$foss_prefix}posts.ID desc";

      $results =   self::getConnectionResolver()->connection()->select($sqlQuery); 
  } else {
      $sqlQuery .= "
      (
          ({$foss_prefix}posts.post_title LIKE '%$query_string%') 
              OR 
          ({$foss_prefix}posts.post_content LIKE '%$query_string%')
              OR 
          ({$foss_prefix}postmeta.meta_value LIKE '%$query_string%')
      ) 
      AND {$foss_prefix}posts.post_type IN ('$postType') 
      AND {$foss_prefix}posts.post_status = 'publish'
          order by {$foss_prefix}posts.ID desc";

    $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
  }

  return $results;
}

  function retrieveAllPublishedDocuments($ids,$query_string)
  {
    global $foss_prefix;
    $itemHistory = CollaborationCenterItemHistory::where("status",'=',"published")->groupBy("item_ID")->selectRaw("max(ID) as maxIDs")->get();
    $itemHistoryIDs = array_map('intval', $itemHistory->pluck('maxIDs')->toArray());
    
    $sqlQuery = "select {$foss_prefix}ef_item.ID as ID, itemHistory.title as post_title, itemHistory.content as post_content,"
            . "'collaboration-center' as post_type, {$foss_prefix}ef_item.owner_id as author,"
            . "itemHistory.created_date as created_date"
    . " from {$foss_prefix}ef_item "
    . " inner join {$foss_prefix}ef_item_history as itemHistory on {$foss_prefix}ef_item.ID = itemHistory.item_ID"
    . " where itemHistory.status = 'published' and itemHistory.ID in (".implode(', ', $itemHistoryIDs).") ";

    if (count($ids) > 0) {
        if(!is_array($ids)){
            $ids = explode (",", $ids);
        }
        $ids_string = implode(",", $ids);
        $_SESSION["search_ids"] = $ids_string;
        $sqlQuery .= " AND ( "
                . "(itemHistory.title LIKE '%$query_string%') OR "
                . "(itemHistory.content LIKE '%$query_string%') OR "
                . "({$foss_prefix}ef_item.ID IN (".implode(', ', $ids)."))) "
                . "order by case when itemHistory.title like '%$query_string%' then 1 "
                . "when itemHistory.content like '%$query_string%' then 1 "
                . "when {$foss_prefix}ef_item.ID IN (".  implode(",", $ids).") then 2 end, itemHistory.ID desc";
        $results =   self::getConnectionResolver()->connection()->select($sqlQuery); 
    } else {
        $sqlQuery .= " AND ("
                . " (itemHistory.title LIKE '%$query_string%') OR "
                . " (itemHistory.content LIKE '%$query_string%') ) order by itemHistory.ID desc";
        
        $results =   self::getConnectionResolver()->connection()->select($sqlQuery); 
    }
    
    return $results;
  }
  
  public function loadEventData($args)
  {
    $results = Post::where('post_type','=',$args["post_type"])
             ->where('post_status','=',$args["post_status"]);
    if($args["numberOfResults"] != -1){
      $results->take($args["numberOfResults"]);
    }
    if($args["skip"] != -1){
      $results->skip($args["skip"]);
    }  
    $results->orderBy('post_title','asc');
    return $results;
  }
  
  public function save(array $options = []) {
    
    global $testingEnvironment;
    if(!$testingEnvironment){
      $args = BadgesHelper::updatePostStatusByBadgePerm($this);
      $this->post_name = $args["post_name"];
      $this->post_status = $args["post_status"];
      $isSaved = parent::save();
      BadgesHelper::updateBadgesInfoByUser($this);
      return $isSaved;
    }
    return parent::save();
  }
  
  public function listHomePageSuccessStories($lang)
  {
    global $foss_prefix;
    $sqlQuery = "select distinct posts.ID,posts.post_title,posts.guid, posts.post_type, posts.post_date 
          from {$foss_prefix}posts as posts 
          join {$foss_prefix}postmeta as news_language on news_language.post_id = posts.ID 
          where posts.post_type = 'success_story' and posts.post_status = 'publish' 
          and news_language.meta_key = 'language' 
          and news_language.meta_value like '%{$lang}%' 
          ORDER BY RAND() LIMIT 0,5";
          
    $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
    return $results;
  }
  
  public function listHomePageNews($lang)
  {
    global $foss_prefix;
    $sqlQuery = "select distinct posts.ID,posts.post_title,posts.guid, posts.post_type,
                news_description.meta_value as description,news_homepage.meta_value as home_featured,
                posts.post_date
                from {$foss_prefix}posts as posts
                join {$foss_prefix}postmeta as news_language on news_language.post_id = posts.ID
                join {$foss_prefix}postmeta as news_homepage on news_homepage.post_id = posts.ID
                join {$foss_prefix}postmeta as news_description on news_description.post_id = posts.ID
                where posts.post_type = 'news'
                and posts.post_status = 'publish'
                and news_language.meta_key = 'language'
                and news_homepage.meta_key = 'is_news_featured_homepage'
                and news_description.meta_key = 'description'
                and news_language.meta_value like '%{$lang}%'
                order by news_homepage.meta_value desc, posts.post_date desc
                Limit 5;";
          
    $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
    return $results;
  }
  
  public function listHomePageEvents()
  {
    global $foss_prefix;
    $sqlQuery = "select distinct posts.ID,posts.post_title, eventdate.meta_value as start_date,event_end_date.meta_value as end_date,eventtype.meta_value as event_type,
                venue_post.post_title as venue_name,organizer_post.post_title as organizer_name
                from {$foss_prefix}posts as posts
                join {$foss_prefix}postmeta as eventdate on eventdate.post_id = posts.ID
                join {$foss_prefix}postmeta as event_end_date on event_end_date.post_id = posts.ID
                join {$foss_prefix}postmeta as eventtype on eventtype.post_id = posts.ID
                join {$foss_prefix}postmeta as venue on venue.post_id = posts.ID 
                join {$foss_prefix}posts as venue_post on venue.meta_value = venue_post.ID
                join {$foss_prefix}postmeta as organizer on organizer.post_id = posts.ID 
                join {$foss_prefix}posts as organizer_post on organizer.meta_value = organizer_post.ID
                where posts.post_type = 'tribe_events'
                and posts.post_status = 'publish'
                and DATE(event_end_date.meta_value) >= CURDATE()
                and eventdate.meta_key = '_EventStartDate'
                and event_end_date.meta_key = '_EventEndDate'
                and eventtype.meta_key = 'event_type'
                and venue.meta_key = '_EventVenueID'
                and organizer.meta_key = '_EventOrganizerID'
                order by eventdate.meta_value asc
                Limit 5; ";      
          
    $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
    return $results;
  }
}
