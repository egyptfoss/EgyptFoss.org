<?php

class Postmeta extends BaseModel {
  protected $table = 'postmeta';
  public $primaryKey  = 'meta_id';

  public function addProductMeta($product_id, $key, $value) {
    $this->post_id = $product_id;
    $this->meta_key = $key;
    $this->meta_value = $value;
    return $this;
  }
  
  public function updatePostMeta($post_id, $key, $value) {
    $postMeta = Postmeta::Where('meta_key', '=', $key)->where('post_id', '=', $post_id);
    if($postMeta->first()) {
      return $postMeta->update(array("meta_key" => $key,"meta_value"=>$value));
    } else {
      $newPostMeta = new Postmeta();
      $newPostMeta->post_id = $post_id;
      $newPostMeta->meta_key = $key;
      $newPostMeta->meta_value = $value;
      return $newPostMeta->save();
    }
  }
  
  /**
   * return unserialized array if gived str is serialized
   * and return given str if it's not serialized
   * 
   * @param type $str
   * @return type
   */
  public static function maybe_unserialize( $str ) {
    $serialzed = unserialize( $str );
    
    // given str is not serialized
    if( !$serialzed ) {
      return $str;
    }
    
    return $serialzed;
  }
  
  public function getProductMeta($product_id) {
    $meta = array();
    $array = array('_thumbnail_id', 'fg_perm_metadata', 'description', 'developer', 'functionality', 'usage_hints', 'references', 'link_to_source', 'industry', 'type', 
                  'technology', 'platform', 'license', 'interest','language','is_featured');
    $postmeta = Postmeta::where('post_id', '=', $product_id)->whereIn('meta_key', $array)->get();
    foreach ($postmeta as $metaObj){
      switch ($metaObj->meta_key) {
        case 'technology':
        case 'platform':
        case 'license':
        case 'industry':
        case 'type':
        case 'interest':
          $meta[$metaObj->meta_key] = self::maybe_unserialize( $metaObj->meta_value );
          break;
        default:
          $meta[$metaObj->meta_key] = $metaObj->meta_value;
          break;
      }
    }
    return $meta;
  }
  
  public function getPostMeta($post_id) {
    $values = array('subtitle', 'description', '_thumbnail_id','category', 'interest', 'success_story_category',
        'dataset_type','theme','datasets_license','publisher','usage_hints','references','source_link','resources',
        'news_category', 'target_bussiness_relationship', 'request_center_type', 'constraints', 'requirements',
        'deadline','technology','language','resources_ids','dataset_formats', 'service_category', 'conditions');
    $postmeta = Postmeta::where('post_id', '=', $post_id)->whereIn('meta_key', $values)->get();
    return $postmeta;
  }
  
  public function getEventMeta($post_id) {
    $values = array('_EventStartDate', '_EventEndDate', '_EventDuration',
            '_EventURL', '_EventCurrencySymbol', '_EventCost', 'theme',
            'audience', 'objectives', 'prerequisites','technology',
            'functionality', 'event_type','_EventOrganizerID',
            'interest', 'platform', '_EventVenueID');
    $postmeta = Postmeta::where('post_id', '=', $post_id)->whereIn('meta_key', $values)->get();
    return $postmeta;
  }
  
  public function getOrganizerMeta($organizer_id) {
    $values = array('_OrganizerWebsite', '_OrganizerEmail', '_OrganizerPhone');
    $postmeta = Postmeta::where('post_id', '=', $organizer_id)->whereIn('meta_key', $values)->get();
    return $postmeta;
  }
  
  public function getMetaValue( $post_id, $meta_key ) {
    
    $meta = Postmeta::where( 'post_id', '=', $post_id )
                      ->where( 'meta_key', '=', $meta_key )
                      ->first();
    
    $value = '';
    
    if( $meta ) {
      $value = $meta->meta_value;
    }
    
    return $value;
  }
  
  public function getVenueMeta($venue_id) {
    $values = array('_VenueAddress', '_VenueCity', '_VenueCountry', '_VenueProvince',
                '_VenueState', '_VenueZip', '_VenuePhone','_VenueURL',
                '_VenueShowMap','_VenueShowMapLink','gmap');
    $postmeta = Postmeta::where('post_id', '=', $venue_id)->whereIn('meta_key', $values)->get();
    return $postmeta;        
  }

  public function getOpenDatasetMeta($dataset_id) {
    $meta = array();
    $array = array('publisher', 'description', 'dataset_type', 'theme', 'datasets_license', 
                  'usage_hints', 'references', 'resources', 'source_link', 'interest','resources_ids','language',
                  'dataset_formats');
    $postmeta = Postmeta::where('post_id', '=', $dataset_id)->whereIn('meta_key', $array)->get();
    foreach ($postmeta as $metaObj){
      switch ($metaObj->meta_key) {
        case 'interest':
          $meta[$metaObj->meta_key] = unserialize($metaObj->meta_value);
          break;
        default:
          $meta[$metaObj->meta_key] = $metaObj->meta_value;
          break;
      }
    }
    return $meta;
  }

}