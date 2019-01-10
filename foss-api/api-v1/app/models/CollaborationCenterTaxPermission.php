<?php

class CollaborationCenterTaxPermission extends BaseModel {
  protected $table = 'ef_tax_permission_item';
  
  public function addTaxPermission($args)
  {
    $this->permission = $args['permission'];
    $this->permission_from = $args['permission_from'];
    $this->item_ID = $args['item_ID'];
    $this->tax_id = $args['tax_id'];
    $this->taxonomy = $args['taxonomy'];
    $this->created_date = date('Y-m-d H:i:s');
    $this->modified_date = date('Y-m-d H:i:s');
  }
  
    public function isPermissionExist($args)
  {
    $result = CollaborationCenterTaxPermission::where("item_ID","=",$args["item_ID"])
      ->where("tax_id","=",$args["tax_id"])
      ->first();
    if(!empty($result))
    {
      return true;
    }
    return false;
  }
  
  public function getTaxonomyAndTerm()
  {
     return $this->join('term_taxonomy as tx', 'tx.term_taxonomy_id', '=', 'ef_tax_permission_item.tax_id')
      ->join('terms as t', 't.term_id', '=', 'tx.term_id')
      ->where("tx.term_taxonomy_id","=",$this->tax_id)->first();
  }
}