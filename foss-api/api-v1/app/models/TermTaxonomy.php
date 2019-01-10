<?php

class TermTaxonomy extends BaseModel {

  protected $table = 'term_taxonomy';
  protected $primaryKey = 'term_taxonomy_id';

  public function addTermTaxonomy($term_id, $term_tax_data) {
    $this->term_id = $term_id;
    $this->taxonomy = $term_tax_data['taxonomy'];
    $this->description = (isset($term_tax_data['description'])) ? $term_tax_data['description'] : '';
    $this->parent = 0;
    $this->count = 0;

    return $this;
  }

  public function saveTermTaxonomies($term_tax_data, $uncreatableTaxs = array(),&$tax_error = "taxonomies") {
    foreach ($term_tax_data as $key => $terms) {
      if(is_array($terms)) {
        foreach ($terms as $term) {
          $term = ltrim($term);
          $term = rtrim($term);
          if( $key == 'technology' ) {
            $term = html_entity_decode( $term );
          }
          else {
            $term = html_entity_decode( urldecode( $term ) );
          }
          /*if (empty($term)) {
            $tax_error = $key;
            return false;
          } else {*/
          if(!empty($term))
          {
            $is_term = Term::join('term_taxonomy','term_taxonomy.term_id','=','terms.term_id')
                        ->where(function ($query) use ($term) {
                          if(ctype_digit($term)) {
                            $query->where('terms.term_id', '=', $term);
                          } else {
                            $query->where('terms.name', '=', htmlentities($term));
                            $query->orWhere('terms.name_ar', '=', htmlentities($term));
                          }
                        })->where('taxonomy', '=', $key)->first();
            if (!$is_term) {
              if(in_array($key, $uncreatableTaxs)) {
                $tax_error = $key;
                return false;
              } else {
                $is_term = new Term();
                $term_slug = str_replace(' ', '-', $term);
                $is_term->addTerm(array("name" => $term, "slug" => $term_slug));
                $is_term->save();
                $newTermTax = new TermTaxonomy();
                $newTermTax->term_id = $is_term->id;
                $newTermTax->taxonomy = $key;
                $newTermTax->description = '';
                $newTermTax->parent = 0;
                $newTermTax->count = 0;
                $newTermTax->save();
              }
            }
          }
        }
      }
    }
    return true;
  }

  public static function getTermTaxonomy($term_id, $taxonomy) {
    $termTax = TermTaxonomy::Where('term_id', '=', $term_id)
        ->where('taxonomy', '=', $taxonomy)->get()->first();


    return $termTax;
  }
  
  public function getTermTaxonomyByTermSlug($term, $taxonomy) {
    return $this->join('terms as term', 'term_taxonomy.term_id', '=', 'term.term_id')
        ->where("term.slug", "=", $term)
        ->where("term_taxonomy.taxonomy", "=", $taxonomy)->first();
  }

}
