<?php

class CollaborationCenterItem extends BaseModel {
  protected $table = 'ef_item';
  
  public function addItem($args)
  {
    $this->title = $args['title'];
    $this->content = $args['content'];
    $this->owner_id = $args['owner_id'];
    $this->is_space = $args['is_space'];
    $this->status = $args['status'];
    $this->created_date = date('Y-m-d H:i:s');
    $this->modified_date = date('Y-m-d H:i:s');
    if(isset($args['item_ID']))
    {
      $this->item_ID = $args['item_ID'];
      
      //update modified date of space
      $updateModel = CollaborationCenterItem::where("ID","=", $this->item_ID);
      $updateModel->update(array(
          'modified_date' => date('Y-m-d H:i:s')
      ));      
    } 
  }
  
  public function documents()
  {
      return $this->hasMany('CollaborationCenterItem','item_ID','ID');
  }

  public function documentHistory()
  {
      return $this->hasMany('CollaborationCenterItemHistory','item_ID','ID');
  }
  
  public function getNoOfContributers()
  {
      return count($this->userPermissions()->get());
  }

  public function space()
  {
      return $this->belongsTo('CollaborationCenterItem','item_ID','ID');
  }

  public function owner()
  {
      return $this->hasOne('User','owner_id','ID');
  }

  public function getSpacesByUser($user,$offset = 0,$limit = -1)
  {
    $items = CollaborationCenterItem::where("owner_id","=",$user)
      ->where("is_space",'=',true)
      ->orderBy("modified_date","DESC");
      if($limit > 0){
        $items->take($limit)->skip($offset);
      }
    return $items->get();
  }
  
  public function isMySpace($user,$space)
  {
    if(!$user || !$space)
    {
      return false;
    }
    $item = CollaborationCenterItem::where("owner_id","=",$user)->where("is_space",'=',true)->where("ID","=",$space)->first();
    if(!empty($item))
    {
      return true;
    }
    return false;
  }
  
  public function getDocumentsBySpaceAndUser($user,$space,$offset =0,$limit = -1)
  {
    $items = CollaborationCenterItem::where("owner_id","=",$user)
      ->where("is_space",'=',false)
      ->where("item_ID",'=',$space)
      ->orderBy("modified_date","DESC");
      if($limit > 0){
      $items->take($limit)->skip($offset);
    }
    return $items->get();
  }
  
  public function getSpaceContentById($space, $offset = -1, $limit = -1) {
    $items = CollaborationCenterItem::where("is_space", '=', false)
      ->where("item_ID", '=', $space)
      ->orderBy('modified_date', 'DESC');
    if ($limit >= 0) {
      $items = $items->take($limit)->skip($offset);
    }
    return $items->get();
  }

  public function getSpaceContentByUserAndId($space,$user)
  {
      return CollaborationCenterItem::where("is_space",'=',false)
        ->where("item_ID",'=',$space)
        ->where("owner_id","=",$user)
        ->orderBy("modified_date","DESC")
        ->get();
  }

  public function getSharedItemsByUser($user_id, $from_date = null, $today = null, $showGroupOnly = false)
  {
    //$userMeta = User::find($user_id)->userMeta()->where("meta_key", "=", "registration_data")->first();
    $userMeta = Usermeta::where('user_id', '=', $user_id)->where("meta_key", "=", "registration_data")->first();
    $registeration_data = unserialize($userMeta->meta_value);

    $registeration_data = (is_array($registeration_data)) ? $registeration_data : unserialize($registeration_data);
    $technologies = ($registeration_data['ict_technology']);
    $interests = (isset($registeration_data['interests'])) ? $registeration_data['interests'] : array();
    $subtype = ($registeration_data['sub_type']);
    $type = ($registeration_data['type']);
    $themes = (isset($registeration_data['theme'])) ? $registeration_data['theme'] : null;
    $anons = array("sub_type" => $subtype, "type" => $type);
    $taxs = array("technology" => array(), "interest" => array(), "theme" => array());

    $tax_names = array("technology" => $technologies, "interest" => $interests, 'theme' => array($themes));
    $userRelations = UserRelation::where("user_id","=",$user_id)->select("term_taxonomy_id")->get();  
    foreach($userRelations as $userRelation)
    {
      $termTax = TermTaxonomy::where("term_taxonomy_id","=",$userRelation->term_taxonomy_id)->first();
      array_push($taxs[$termTax->taxonomy], $userRelation->term_taxonomy_id);
    }
    
    $having_caluse = "";
    $having_in = array();
    if($type)
    {
      array_push($having_in,$type);
    }
    if($subtype)
    {
      array_push($having_in,"{$type},{$subtype}");
      array_push($having_in,"{$subtype}");
    }
    if(!empty($having_in))
    {
      $having_in = implode("','",$having_in);
      $having_caluse = " ( apiTypes in ('{$having_in}') or apiTypes is null ) ";
    }
    if(!empty($having_caluse) )
    {
      $having_caluse = "(" . $having_caluse . ") ";
      $having_caluse .= "and ( apiTypes is not null or apiTaxs is not null)";
      $having_caluse = " ( ".$having_caluse." ) ";
    }else
    {
      $having_caluse .= " ( apiTypes is not null or apiTaxs is not null)";
      $having_caluse = " ( ".$having_caluse." ) ";
    }
    
    $tax_caluse = "";
    global $foss_prefix;
    foreach ($taxs as $key=>$value)
    {
      if(empty($value))
      {
        $tax_caluse .= " ({$key} is null ) and";
        
      }else
      {
        $multiTaxCaluse = "";
        foreach($value as $taxID)
        {
          $multiTaxCaluse .= "( Find_In_Set({$taxID},{$key}) ) or ";
        }
        $multiTaxCaluse .= "( {$key} is null ) or";
        $multiTaxCaluse = substr($multiTaxCaluse,0,-3); 
        $multiTaxCaluse = ($multiTaxCaluse != "")?"(".$multiTaxCaluse.")   ":"";
        $tax_caluse .= $multiTaxCaluse;
      }
      $tax_caluse = substr($tax_caluse,0,-3); 
      $tax_caluse = $tax_caluse ." and";
    }
    
    $tax_caluse = substr($tax_caluse,0,-3);
    if($having_caluse != "")
    {
      $having_caluse = "(".$tax_caluse.") and " . $having_caluse;
    }else
    {
      $having_caluse = "(".$tax_caluse.") ";
    }
    if(!$showGroupOnly)
    {
      $having_caluse .= " or ( Find_In_Set({$user_id},upiUsers) )";
    }
    
    $is_taxs = false;
    $items = $this->distinct();
    if(!$showGroupOnly)
    {
      $items = $items->leftjoin('ef_user_permission_item as upi', 'ef_item.ID', '=', 'upi.item_ID');
    }
    
    $selectQuery = "{$foss_prefix}ef_item.* , GROUP_CONCAT(DISTINCT `{$foss_prefix}api`.`name` SEPARATOR ',') as apiTypes , GROUP_CONCAT(DISTINCT `{$foss_prefix}tpi`.`taxonomy` SEPARATOR ',') as apiTaxs";
    if(!$showGroupOnly)
    {
      $selectQuery .= ", GROUP_CONCAT(DISTINCT `{$foss_prefix}upi`.`user_id` SEPARATOR ',') as upiUsers";
    }
    $selectQuery .= ",GROUP_CONCAT(DISTINCT case when `{$foss_prefix}tpi`.`taxonomy` = 'interest' then `{$foss_prefix}tpi`.`tax_id` end ) as interest";
    $selectQuery .= ",GROUP_CONCAT(DISTINCT case when `{$foss_prefix}tpi`.`taxonomy` = 'technology' then `{$foss_prefix}tpi`.`tax_id` end ) as technology";
    $selectQuery .= ",GROUP_CONCAT(DISTINCT case when `{$foss_prefix}tpi`.`taxonomy` = 'theme' then `{$foss_prefix}tpi`.`tax_id` end ) as theme";
    
    $items->leftjoin('ef_anon_permission_item as api', 'ef_item.ID', '=', 'api.item_ID')
      ->leftjoin('ef_tax_permission_item as tpi', 'ef_item.ID', '=', 'tpi.item_ID')
      ->selectRaw($selectQuery)
      ->where("ef_item.owner_id", "!=", $user_id);
      $items->where(function ($query) use ($user_id, $taxs,$is_taxs, $showGroupOnly) {
        if(!$showGroupOnly)
        {
          $query->where('upi.user_id', '=', $user_id);
          $query->OrWhereRaw(" 1=1 ");
        }
        
      });
      if($from_date != NULL)
      {
        $items = $items->where('ef_item.created_date', '>', $from_date);
      }
      if($today != NULL)
      {
        $items = $items->where('ef_item.created_date', '<', $today);
      }
      
      $items->groupBy("ef_item.ID")
      ->havingRaw($having_caluse)
      ->orderBy('is_space', 'DESC')
      ->orderBy('ef_item.modified_date', 'DESC');
    return $items;
  }
  
  
  public function isSharedItemByUser($user_id,$item_id, $showGroupOnly = false)
  {
    if(!$item_id || !$user_id)
    {
      return false;
    }
    
      $items = $this->getSharedItemsByUser($user_id, null, null, $showGroupOnly)->where("ef_item.ID","=",$item_id)->first();
      
      if(!empty($items))
      {
        return true;
      }
    return false;
  }
  
  public function isDocumentTitleExist($space,$title)
  {
      $items = CollaborationCenterItem::where("is_space",'=',false)->where("item_ID",'=',$space)->where("title","=",$title)->first();
      if(!empty($items))
      {
        return true;
      }
      return false;
  }
  
   public function isSpaceTitleExist($title,$user,$excludeId = false)
  {
      $items = CollaborationCenterItem::where("is_space",'=',true)
        ->where("title","=",$title)
        ->where("owner_id","=",$user);
      if($excludeId)
      {
        $items->where("ID","!=",$excludeId);
      }
        $items->first();
       
      if(!empty($items) && $items->first() != null)
      {
        return true;
      }
      return false;
  }
  
  //Get Document by ID
  public function getDocumentByID($document_id,$isSPaceOrDoc = true)
  {
    $item = $this->where('ID','=', $document_id);
    if(!$isSPaceOrDoc)
    {
      $item = $item->where("is_space","=",false);
    }
      $item = $item->first();
    return $item;
  }
  
  public function getDocumentByUserAndID($document_id,$user)
  {
    $item = $this->where('ID','=', $document_id)->where("owner_id","=",$user)->first();
    return $item;
  }
  
  //Check if isMyDocument
  public function isMyDocument($user, $document_id)
  {
    if(!$user || !$document_id)
    {
      return false;
    }
    
    $item = $this->where("owner_id","=",$user)->where("is_space",'=',false)->where("ID","=",$document_id)->first();
    if(!empty($item))
    {
      return true;
    }
    return false;
  }
  
  public function isMyItem($user, $item_id)
  {
    if(!$user || !$item_id)
    {
      return false;
    }
    
    $item = $this->where("owner_id","=",$user)->where("ID","=",$item_id)->first();
    if(!empty($item))
    {
      return true;
    }
    return false;
  }
  
  public function updateDocument($args)
  {
    $document = $this->where('ID','=',$args['ID']);
    if($document->first())
    {
      $document->update(array(
          "title" => $args['title'],
          "content" => $args['content'],
          "status" => $args['status'],
          "modified_date" => date('Y-m-d H:i:s')
        )
      );
    }
  }
  
  public function anonPermissions()
  {
    return $this->hasMany('CollaborationCenterAnonPermission','item_ID','ID');
  }
  
  public function taxPermissions()
  {
    return $this->hasMany('CollaborationCenterTaxPermission','item_ID','ID');
  }
  
  public function userPermissions()
  {
    return $this->hasMany('CollaborationCenterUserPermission','item_ID','ID');
  }
  
  public function getTaxPermissionIdsByName($tax_name)
  {
    $permissions = $this->taxPermissions()->where("taxonomy","=",$tax_name)->get();
    $ids = array();
    foreach($permissions as $permission)
    {
      array_push($ids, $permission->tax_id);
    }
    return $ids;
  }
  
  public function getAnonPermissionByType($type)
  {
    $permission = $this->anonPermissions()->where("type","=",$type)->first();
    
    return (!empty($permission))?$permission->name:null;
  }
    
  public function getItemIDsByParentID($parent_item_id)
  {
    return $this->where('item_ID','=', $parent_item_id)->select('ID')->get();
  }
  
  public function isSharedByGroup()
  {
     $items = $this->distinct()
      ->join('ef_anon_permission_item as api', 'ef_item.ID', '=', 'api.item_ID')
      ->join('ef_tax_permission_item as tpi', 'ef_item.ID', '=', 'tpi.item_ID')
      ->select("ef_item.*")
      ->where("ef_item.ID", "=", $this->ID)->get();   
    return ($items)?true:false; 
  }
  
  public function isSharedBySpace()
  {
     $items = $this->distinct()
      ->leftjoin('ef_anon_permission_item as api', 'ef_item.ID', '=', 'api.item_ID')
      ->leftjoin('ef_tax_permission_item as tpi', 'ef_item.ID', '=', 'tpi.item_ID')
      ->where("ef_item.ID", "=", $this->ID)
      ->where(function ($query)  {     
        $query->where("api.permission_from","!=","document")
              ->orWhere("api.permission_from","=",null);
      })
      ->where(function ($query)  {     
        $query->Where("tpi.permission_from","!=","document")
              ->orWhere("tpi.permission_from","=",null);
      })
      ->groupBy("ef_item.ID")
      ->first();
    return $items; 
  }
  
  public function getPublishedDocuments($section = "", $queryLike = "", $semantic_ids = "", $take = "", $skip = "")
  {
    global $foss_prefix;
    
    $itemHistory = CollaborationCenterItemHistory::where("status",'=',"published")->groupBy("item_ID")->selectRaw("max(ID) as maxIDs")->get();
    $itemHistoryIDs = array_map('intval', $itemHistory->pluck('maxIDs')->toArray());
    $items = $this
      ->join('ef_item_history as itemHistory', 'ef_item.ID', '=', 'itemHistory.item_ID')
      ->selectRaw("{$foss_prefix}itemHistory.*,{$foss_prefix}ef_item.owner_id as owner,{$foss_prefix}ef_item.ID as document_id")
      ->where("itemHistory.status", "=", "published")
      ->whereIn("itemHistory.ID",$itemHistoryIDs);
    if ($section != "") {
      $items = $items->where("section", "=", $section);
    }
    
    if($semantic_ids != "")
    {
      $items = $items->where(function ($query, $queryLike, $semantic_ids) {
              $query->where('itemHistory.title', 'LIKE', '%'.$queryLike.'%')
                    ->orWhere('itemHistory.content', 'LIKE', '%'.$queryLike.'%')
                    ->whereRaw(' or ef_item.ID in ('.$semantic_ids.')');
          });
    }
    
    if ($queryLike != "") {
      $items = $items->where(function ($query, $queryLike) {
              $query->where('itemHistory.title', 'LIKE', '%'.$queryLike.'%')
                    ->orWhere('itemHistory.content', 'LIKE', '%'.$queryLike.'%');
          });
    }
    
    if($take != "")
    {
      $items = $items->take($take)->skip($skip);
    }
    
    $items = $items->orderBy("created_date", "DESC")->get();
    return $items;
  } 
  
  public function getPublishedDocumentByID($id)
  {
    global $foss_prefix;
    $itemHistory = CollaborationCenterItemHistory::where("status",'=',"published")->groupBy("item_ID")->selectRaw("max(ID) as maxIDs")->get();
    $itemHistoryIDs = array_map('intval', $itemHistory->pluck('maxIDs')->toArray());
    $item = $this
      ->join('ef_item_history as itemHistory', 'ef_item.ID', '=', 'itemHistory.item_ID')
      ->selectRaw("{$foss_prefix}itemHistory.*,{$foss_prefix}ef_item.owner_id as owner")
      ->where("itemHistory.status", "=", "published")
      ->where("itemHistory.item_ID","=",$id)  
      ->whereIn("itemHistory.ID",$itemHistoryIDs);
    $item = $item->orderBy("created_date", "DESC")->first();
    return $item;
  } 
  
  //Remove document with its related rows
  public function removeSpaceorDocument($item)
  {
    //Remove documents if exists
    $documents = $item->documents()->get();
    for($i =0; $i < sizeof($documents); $i++)
    {
      $documents[$i]->anonPermissions()->delete();
      $documents[$i]->userPermissions()->delete();
      $documents[$i]->taxPermissions()->delete();
      $documents[$i]->documentHistory()->delete();
      CollaborationCenterItem::where('ID','=',$documents[$i]->ID)->delete();
    }
    
    //remove item itself
    $item->anonPermissions()->delete();
    $item->userPermissions()->delete();
    $item->taxPermissions()->delete();
    $item->documentHistory()->delete();
    CollaborationCenterItem::where('ID','=',$item->ID)->delete();
  }
  
  /**
   * return group contributors
   * 
   * @return type
   */
  public function getGroupContributors( $locale ) {
    $select = ($locale == 'ar')?'name_ar':'name';
    $contributors = Term::join( 'term_taxonomy', 'terms.term_id', '=', 'term_taxonomy.term_id' )
                        ->join( 'ef_tax_permission_item', 'term_taxonomy.term_taxonomy_id', '=', 'ef_tax_permission_item.tax_id' )
                        ->where( 'ef_tax_permission_item.item_id', '=', $this->ID )
                        ->select( 'terms.'.$select.' AS name ' )
                        ->union( CollaborationCenterAnonPermission::select( 'name' )->where( 'item_id', '=', $this->ID ) )
                        ->get();
    
    return $contributors;
  }
  
  public function getUserPermissionOnItem($user_id)
  {
    
    $perm = $this->userPermissions()->where("user_id","=",(int)$user_id)->first();
    if($perm)
    {
      return $perm->permission;
    }
    return false;
  }
  
  public function updateSpaceTitle($updateModel, $title)
  {
    $updateModel->update(array(
        'title' => $title,
        'modified_date' => date('Y-m-d H:i:s')
    ));
  }

  public function userPublishedDocuments($user_id, $offset = -1, $limit = -1) {
    global $foss_prefix;
    $items = $this->join('ef_item_history as itemHistory', 'ef_item.ID', '=', 'itemHistory.item_ID')
      ->where("ef_item.status", "=", "published")
      ->where("itemHistory.editor_id", $user_id)
      ->groupBy("itemHistory.item_ID")
      ->selectRaw(" max({$foss_prefix}itemHistory.id) as ef_item_historyMaxID, {$foss_prefix}itemHistory.*,{$foss_prefix}ef_item.*")
      ->orderByRaw("ef_item_historyMaxID DESC");
    if($offset >= 0) {
      $items = $items->skip($offset);
    }
    if($limit >= 0) {
      $items = $items->take($limit);
    }
    $items = $items->get();
    return $items;
  }
}