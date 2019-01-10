<?php

class UserRelation extends BaseModel {
	protected $table = 'user_relationships';

	public function addUserRelation($user_id, $term_taxonomy_id){
		$this->user_id = $user_id;
		$this->term_taxonomy_id = $term_taxonomy_id;
		return $this;
	}
  
  public function addUserRelationByTermAndTaxonomy($user_id, $term, $taxonomy){
    $term_taxonomy = new TermTaxonomy();
    $termTaxonomy = $term_taxonomy->getTermTaxonomyByTermSlug($term, $taxonomy);
    if($termTaxonomy->term_taxonomy_id)
    {
      $this->user_id = $user_id;
      $this->term_taxonomy_id = $termTaxonomy->term_taxonomy_id;
    }
		return $this;
	}
  
}