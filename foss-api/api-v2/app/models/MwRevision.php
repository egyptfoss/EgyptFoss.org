<?php

class MwRevision extends WikiBaseModel {

    protected $connection = 'mediawiki';
    protected $table = 'revision';
    protected $attributes = array(
      'rev_page' => 0,
      'rev_comment' => "",
      'rev_text_id' => 0,
    );

    public function __construct($language="en",array $attributes = array())
    {
      parent::__construct($language, $attributes);
    }
    
    public function getPageRevisions($pageId) {
        $revisions = MwRevision::where('rev_page', '=', $pageId);
        if (count($revisions) > 0) {
            return $revisions;
        } else {
            return false;
        }        
    }

    public function getRevision($revId, $pageId) {
        $revision = MwRevision::where('rev_id', '=', $revId)->where('rev_page', '=', $pageId)->first();
        return $revision;
    }

    public function addRevision($pageId, $textId, $user) {
        $this->rev_page = $pageId;
        $this->rev_text_id = $textId;
        if($user != null)
            $this->rev_user = $user;
        return $this;
    }

}
