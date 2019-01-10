<?php
class TopTenProduct extends BaseModel {

  protected $table = 'top_ten_products';

  
  function getTopTenProducts($offset = -1,$limit = -1, $randomized = -1) {

    if($offset >= 0 && $limit > 0 )
      {
        $term_ids = TermTaxonomy::where("taxonomy","=","industry")->take((int)$limit)->skip($offset)->get()->pluck("term_id")->toArray();
      }else
      {
        $term_ids = TermTaxonomy::where("taxonomy","=","industry")->get()->pluck("term_id")->toArray();
      }
      $term_ids = array_map('intval', $term_ids);
      if($randomized > 0)
      {
        shuffle($term_ids);
        $term_ids_imploded = implode(',', $term_ids);
      //  var_dump($term_ids_imploded);
       // exit;
      }
      global $foss_prefix;
      $datasets = TopTenProduct::join('posts as post', 'top_ten_products.post_id', '=', 'post.ID')
        ->join('terms as term', 'top_ten_products.term_id', '=', 'term.term_id')
        ->join('postmeta as pmeta', 'pmeta.post_id', '=', 'post.ID')
        ->selectRaw("{$foss_prefix}post.ID as ID, {$foss_prefix}post.post_title as post_title, {$foss_prefix}term.term_id, {$foss_prefix}term.name, {$foss_prefix}term.name_ar, {$foss_prefix}term.slug,"
          . "GROUP_CONCAT(DISTINCT case when `{$foss_prefix}pmeta`.`meta_key` = '_thumbnail_id' then (select guid from `{$foss_prefix}posts` where ID = `{$foss_prefix}pmeta`.`meta_value`) end) as post_image_url") 
        ->whereIn('top_ten_products.term_id',$term_ids)
        ->groupBy('ID');
        
        if($randomized > 0)
        {
          $datasets = $datasets->orderByRaw("field({$foss_prefix}term.term_id, {$term_ids_imploded})");
        }
    return $datasets;
  }

}