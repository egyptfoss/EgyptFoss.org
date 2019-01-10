<?php

class MwText extends WikiBaseModel {

    protected $connection = 'mediawiki';
    protected $table = 'text';
    protected $primaryKey = 'old_id';
    protected $attributes = array(
      'old_flags' => "",
    );

    public function __construct($language="en",array $attributes = array())
    {
      parent::__construct($language, $attributes);
    }
    
    public function getText($id) {
        $text = MwText::where('old_id', '=', $id)->first();
        if ($text) {
            return $text->old_text;
        } else {
            return false;
        }        
    }

    public function addPageText($pageContent) {
        $this->old_text = $pageContent;
        return $this;
    }

}
