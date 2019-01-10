<?php

class TermRelation extends BaseModel {
    protected $table = 'term_relationships';

    public function addTermRelation($post_id, $term_taxonomy_id){
        $this->object_id = $post_id;
        $this->term_taxonomy_id = $term_taxonomy_id;
        $this->term_order = 0;

        return $this;
    }  
}