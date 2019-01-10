<?php

class externalProductBaseModel extends BaseModel {
    
    public function __construct(array $attributes = array()) {
        $this->connection = "externalConnectionProducts";
        parent::__construct($attributes);
    }
    
}