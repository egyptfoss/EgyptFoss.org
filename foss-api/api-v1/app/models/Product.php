<?php
class Product extends Post {

  protected $table = 'posts';

  public function __construct(array $attributes = array()) {
    $this->post_type = 'product';
    parent::__construct($attributes);
  }

  public static function boot() {
    static::addGlobalScope(new ProductScope());
  }
  
  function getProductsByFilters($args,$ef_product_filtered_taxs)
  {
     if ($args['lang'] == "en") {
      $current_lang = "en";
      $foriegn_lang = "ar";
    } else {
      $foriegn_lang = "en";
      $current_lang = "ar";
    }

    $filter_condition = "";
    $having_condition = "";
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
      
    }
    
        if (!empty($term_ids)) {
     $term_count = count($term_ids);
     $term_ids = join(',', $term_ids);
     $filter_condition = " and rel.term_taxonomy_id in ({$term_ids}) ";
     $having_condition = " having count(*) = {$term_count} ";
    }

 $order_by = " order by lang_ord asc,p.ID DESC "; 
 $featured_filter_condition = "";
 $featured_join_condition = "";
 
  if($args['industry'] == "featured")
  {
    $featured_join_condition = " join wpRuvF8_postmeta as meta on p.ID = meta.post_id ";
    $featured_filter_condition = " and (meta.meta_key = 'is_featured') and (meta.meta_value = 1) ";
    $order_by = " order by p.post_title ";
  }
 
  $sql = "SELECT p.ID as id, case when pmeta.meta_value like '%\"{$current_lang}\"%' then '1' else '2' end as lang_ord
        FROM {$foss_prefix}posts as p {$featured_join_condition}
        join {$foss_prefix}postmeta as pmeta on p.ID = pmeta.post_id
        join {$foss_prefix}term_relationships as rel on p.ID = rel.object_id
        join {$foss_prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
        where (p.post_status = '{$args['post_status']}' and p.post_type = '{$args['post_type']}')
        and (pmeta.meta_key = 'language' and 
        (pmeta.meta_value like '%\"{$current_lang}\"%' or (
        pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))
        {$filter_condition} {$featured_filter_condition}
        group by p.ID 
        {$having_condition} 
        {$order_by}";
       if($args['page_no'] != -1 && $args['no_of_posts'] != -1)
       {
          $args['page_no'] = ($args['page_no'] == 0)?1:$args['page_no'];
          $page_no = ($args['page_no'] * $args['no_of_posts']) - $args['no_of_posts'];
          $sql.="  limit {$page_no},{$args['no_of_posts']} ";
       } 
       $results = $this->getConnection()->select($sql);
       $ids = array();
       foreach($results as $result)
        {
          array_push($ids,$result->id);
        }
        $results = array();
        if(!empty($ids)){
        $ids = join(",", array_map('intval',$ids));
        $sql = "select * from wpRuvF8_posts as p
              {$featured_join_condition}
              where p.ID in ({$ids})
               {$featured_filter_condition} order by FIND_IN_SET(p.ID,'{$ids}')";  
        $results = $this->getConnection()->select($sql);
        }
        return $results;
       
  }
    
}