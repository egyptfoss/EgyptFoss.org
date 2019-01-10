<?php

class Term extends BaseModel {
    protected $table = 'terms';
//    public $primaryKey  = 'term_id';

    public function addTerm($term_data){
      $term_data['name'] = ltrim($term_data['name']);
      $term_data['name'] = rtrim($term_data['name']);
      $term_slug = str_replace(' ', '-', $term_data['name']);
      $this->name = $term_data['name'];
      $this->slug = (isset($term_data['slug']))?$term_data['slug']:$term_slug;
      $this->term_group = 0;

      return $this;
    }
    
    public function getTerm($term_ids){
      $name = Term::where('term_id', '=', $term_ids)->first();
      return $name;
    }
    
    public static function get_terms_by($term_name, $term_taxonomy) {
    global $foss_prefix;
    $sql = "SELECT * from {$foss_prefix}terms as term
             join {$foss_prefix}term_taxonomy as termtax on term.term_id = termtax.term_id    
             where (termtax.taxonomy = '{$term_taxonomy}' and term.name = '{$term_name}')
             ";
    return self::getConnectionResolver()->connection()->selectOne($sql);
  }
  
  public static function get_terms_by_testing($term_name, $term_taxonomy) {
    $term = Term::join('term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id')
                ->where('taxonomy', '=', $term_taxonomy)
                ->where('terms.name', '=', $term_name)->first();
    return $term;
  }
  
  public function loadTaxonomyByTaxonomyType($taxonomy, $take, $skip, $hasAr)
  {
      if($hasAr)
      {
        $result = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                  ->where('term_taxonomy.taxonomy', '=', $taxonomy)
                  ->select('terms.term_id','terms.name','terms.name_ar', 'terms.slug');
        if($take != -1){
          $result->take($take);
        }
        if($skip != -1){
          $result->skip($skip);
        }       
      }else
      {
        $result = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                  ->where('term_taxonomy.taxonomy', '=', $taxonomy)
                  ->select('terms.term_id','terms.name');
        if($take != -1){
          $result->take($take);
        }
        if($skip != -1){
          $result->skip($skip);
        }   
      }
      return $result->get();
  }
  
  public function getTaxonomyTerms($taxonomy,$lang)
  {  
    if($lang == "ar")
    {
      $result = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                  ->where('term_taxonomy.taxonomy', '=', $taxonomy)
                  ->select('terms.term_id','terms.slug','terms.name','terms.name_ar')
                  ->get();   
    }else
    {
      $result = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                  ->where('term_taxonomy.taxonomy', '=', $taxonomy)
                  ->select('terms.term_id','terms.slug','terms.name')
                  ->get(); 
      
    }
      return $result;
  }
  
  public static function get_terms_by_name_language($term_name, $term_taxonomy) {
    global $foss_prefix;
    $sql = "SELECT * from {$foss_prefix}terms as term
             join {$foss_prefix}term_taxonomy as termtax on term.term_id = termtax.term_id    
             where (termtax.taxonomy = '{$term_taxonomy}' and (term.name = '{$term_name}' or term.name_ar = '{$term_name}'))
             ";
    return self::getConnectionResolver()->connection()->selectOne($sql);
  }
}
