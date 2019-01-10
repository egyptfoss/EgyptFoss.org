<?php

class CollaborationCenterItemHistory extends BaseModel {
    protected $table = 'ef_item_history';
    
    public function document()
    {
        return $this->belongsTo('CollaborationCenterItem','item_ID','ID');
    }
    
    public function reviewer()
    {
        return $this->belongsTo('User','reviewer_id','ID');
    }
    
    public function author()
    {
        return $this->belongsTo('User','editor_id','ID');
    }
    
    public function addItem($args)
    {
      $this->title = $args['title'];
      $this->content = $args['content'];
      $this->editor_id = $args['editor_id'];
      $this->status = $args['status'];
      $this->created_date = date('Y-m-d H:i:s');
      if(isset($args['item_ID']))
      {
        $this->item_ID = $args['item_ID'];
      } 
      $this->section = $args['section'];    
    }
    
    public static function getItemRevisions($itemId)
    {
      return CollaborationCenterItemHistory::where("item_ID","=",$itemId)->orderBy("ID"," DESC")->get();   
    }
    
    
  //Get Revision by ID
  public function getRevisionByID( $revision_id ) {
    $item = $this->where( 'ID','=', $revision_id )->first();
    
    return $item;
  }
}