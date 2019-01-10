<?php

class WikiBaseModel extends BaseModel {
    
    public function __construct($language="en", array $attributes = array()) {
        if($language == "ar") {
            $this->connection = "mediawikiar";
        }
        parent::__construct($attributes);
    }
    
    
}