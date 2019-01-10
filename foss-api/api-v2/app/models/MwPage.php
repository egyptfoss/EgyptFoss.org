<?php

class MwPage extends WikiBaseModel {

    protected $connection = 'mediawiki';
    protected $table = 'page';
    protected $primaryKey = 'page_id';
    protected $attributes = array(
      'page_namespace' => 0,
      'page_title' => "",
      'page_restrictions' => "",
      'page_random' => 0.1,
      'page_latest' => 1,
      'page_len' => 1,
    );

    public function __construct($language="en",array $attributes = array())
    {
      parent::__construct($language, $attributes);
    }
    
    public function getMainPage($pageName)
    {
      $defaultPage = MwPage::select('page_title','old_text')
              ->join('revision','revision.rev_id','=','page.page_latest')
              ->join('text','text.old_id','=','revision.rev_text_id')
              ->where('page_title', '=', $pageName)
              ->first();
      
      return $defaultPage;
    }
    
    public function getPage($pageName) {
        $page = MwPage::whereRaw('upper(page_title) = "'. strtoupper($pageName).'"')->first();
        return $page;   
    }
    
    public function getPostCountByType()
    {
        $page = MwPage::get();
        return $page;
    }

    public function addPage($pageName,$pageTextId) {
//        $pageName = str_replace("_", " ", $pageName);
//        $this->page_title = str_replace(" ", "_",ucwords($pageName));
        $this->page_title = ucfirst($pageName);
        $this->page_is_new = 1;
        $this->page_is_redirect = 0;
        $this->page_content_model = "wikitext";
        $this->page_namespace = 0;
        return $this;
    }
    
    public function addPageForImage($pageName) {
        $latest = MwPage::all()->max("page_latest");
        if($latest)
        {
          $this->page_latest = $latest + 1;
        }else
        {
          $this->page_latest = 1;
        }
        $this->page_title = ucfirst($pageName);
        $this->page_is_new = 1;
        $this->page_is_redirect = 0;
        $this->page_namespace = 6;
        $this->page_random = mw_helper::wfRandom();
        $this->page_len = 0;
        $this->page_content_model = "wikitext";
        return $this;
    }
    
    public function getFossPediaContributionAdditionsBy($args)
    {    
        //get user id 
        $sqlUserEn = "select en_user.user_id from en_user where en_user.user_email = '".$args['author']."'";
        $resultsUser = $this->getConnection()->select($sqlUserEn);
        $user_en_id = -1;
        if(sizeof($resultsUser) > 0)
        {
            $user_en_id = $resultsUser[0]->user_id;
        }
        
        $sqlUserAr = "select user_id from ar_user where user_email = '".$args['author']."'";
        $resultsUser = $this->getConnection()->select($sqlUserAr);
        $user_ar_id = -1;
        if(sizeof($resultsUser) > 0)
        {
            $user_ar_id = $resultsUser[0]->user_id;
        }
        $sql = "";
        if($user_en_id != -1)
        {
            $sql .= "(select en_page.page_id,en_page.page_title as post_title,concat('/en/wiki/',en_page.page_title) as page_url,en_revision.rev_id,CONVERT(en_revision.rev_timestamp USING utf8) as post_date from en_page left join en_revision on en_page.page_id = en_revision.rev_page where en_page.page_namespace = 0"
                . " and en_revision.rev_user = {$user_en_id} and en_revision.rev_parent_id = 0"
                . " group by en_page.page_id)";
        }
        
        if($user_ar_id != -1)
        {
            if($user_en_id != -1)
            {
                $sql .= " union ";
            }
         
           $sql.= "(select ar_page.page_id,ar_page.page_title as post_title,concat('/ar/wiki/',ar_page.page_title) as page_url,ar_revision.rev_id,CONVERT(ar_revision.rev_timestamp USING utf8) as post_date from ar_page left join ar_revision on ar_page.page_id = ar_revision.rev_page where ar_page.page_namespace = 0"
            . " and ar_revision.rev_user = {$user_ar_id} and ar_revision.rev_parent_id = 0"
            . " group by ar_page.page_id) ";
        }
        
        if($user_ar_id != -1 || $user_en_id != -1)
        {
            $sql .= " order by post_date desc ";
            if(!empty($args['offset']) && !empty($args['no_of_tax']))
            {
              $sql .= "limit {$args['offset']},{$args['no_of_tax']}";
            }
        }
        
        if($sql == ""){
            return [];
        }
        
        $results = $this->getConnection()->select($sql);
        return $results;
    }
    
    public function getFossPediaContributionEditsBy($args)
    {    
        //get user id 
        $sqlUserEn = "select en_user.user_id from en_user where en_user.user_email = '".$args['author']."'";
        $resultsUser = $this->getConnection()->select($sqlUserEn);
        $user_en_id = -1;
        if(sizeof($resultsUser) > 0)
        {
            $user_en_id = $resultsUser[0]->user_id;
        }
        
        $sqlUserAr = "select user_id from ar_user where user_email = '".$args['author']."'";
        $resultsUser = $this->getConnection()->select($sqlUserAr);
        $user_ar_id = -1;
        if(sizeof($resultsUser) > 0)
        {
            $user_ar_id = $resultsUser[0]->user_id;
        }
        $sql = "";
        if($user_en_id != -1)
        {
            $sql .= "(select en_page.page_id,en_page.page_title as post_title,concat('/en/wiki/',en_page.page_title) as page_url,en_revision.rev_id,CONVERT(en_revision.rev_timestamp USING utf8) as post_date from en_page left join en_revision on en_page.page_id = en_revision.rev_page left join en_revision as en_revision_parent on en_page.page_id = en_revision_parent.rev_page and en_revision_parent.rev_parent_id = 0 where en_page.page_namespace = 0"
                . " and en_revision.rev_user = {$user_en_id} and en_revision.rev_parent_id <> 0"
                . " and (en_revision_parent.rev_user <> {$user_en_id} )"
                . " group by en_page.page_id)";
        }
        if($user_ar_id != -1)
        {
            if($user_en_id != -1)
            {
                $sql .= " union ";
            }        
             
             $sql .= "(select ar_page.page_id,ar_page.page_title as post_title,concat('/ar/wiki/',ar_page.page_title) as page_url,ar_revision.rev_id,CONVERT(ar_revision.rev_timestamp USING utf8) as post_date from ar_page left join ar_revision on ar_page.page_id = ar_revision.rev_page left join ar_revision as ar_revision_parent on ar_page.page_id = ar_revision_parent.rev_page and ar_revision_parent.rev_parent_id = 0 where ar_page.page_namespace = 0"
                . " and ar_revision.rev_user = {$user_ar_id} and ar_revision.rev_parent_id <> 0"
                . " and (ar_revision_parent.rev_user <> {$user_ar_id} ) group by ar_page.page_id) ";
        }
        
        if($user_ar_id != -1 || $user_en_id != -1)
        {
            $sql .= " order by post_date desc ";
            if(!empty($args['offset']) && !empty($args['no_of_tax']))
            {
              $sql .= "limit {$args['offset']},{$args['no_of_tax']}";
            }
        }
        
        if($sql == "")
        {
            return [];
        }
        
        $results = $this->getConnection()->select($sql);
        return $results;
    }
    
  function retrievePediaSearchResult($ids_en,$ids_ar,$query_string)
  {
    $prefixes = array('en_','ar_');
    $final_results = array();
    if(!is_array($ids_en)) {
        $ids_en = explode(",", $ids_en);
    }
    if(!is_array($ids_ar))
    {
        $ids_ar = explode(",", $ids_ar);
    }
    
    foreach($prefixes as $prefix)
    {
        $page_lang = str_replace("_", "", $prefix);
        $sqlQuery = "select {$prefix}page.page_id,{$prefix}page.page_title as post_title, {$prefix}text.old_text as meta_value "
        . ",'pedia' as post_type,concat('/$page_lang/wiki/',{$prefix}page.page_title) as page_url"
        . " from {$prefix}page left join {$prefix}revision on {$prefix}page.page_latest = {$prefix}revision.rev_id "
        . "left join {$prefix}text on {$prefix}revision.rev_text_id = {$prefix}text.old_id "
            . "where {$prefix}page.page_namespace = 0";
            
        if($page_lang == "en")
        {
            if (count($ids_en) > 0) 
            {
                $sqlQuery .= " and ( "
                        . " ({$prefix}page.page_title LIKE '%$query_string%') OR "
                        . " ({$prefix}text.old_text LIKE '%$query_string%') OR "
                        . "({$prefix}page.page_id IN (".implode(', ', $ids_en).")) ) "
                        . "  order by case when {$prefix}page.page_title like '%$query_string%'  then 1 "
                        . "when {$prefix}text.old_text like '%$query_string%' then 1 "
                        . "when {$prefix}page.page_id IN (".  implode(",", $ids_en).") then 2 end, {$prefix}page.page_id desc";

                $results = $this->getConnection()->select($sqlQuery);
                $final_results = array_merge($final_results, $results);
            }
            else 
            {
                $sqlQuery .= " and ( "
                        . "({$prefix}page.page_title LIKE '%$query_string%') OR "
                        . "({$prefix}text.old_text LIKE '%$query_string%')) order by {$prefix}page.page_id desc";

                $results = $this->getConnection()->select($sqlQuery);
                $final_results = array_merge($final_results, $results);
            }
        }else if($page_lang == "ar")
        {
            if (count($ids_ar) > 0) {
                $sqlQuery .= " and ( "
                        . " ({$prefix}page.page_title LIKE '%$query_string%') OR "
                        . "({$prefix}text.old_text LIKE '%$query_string%') OR "
                        . "({$prefix}page.page_id IN (".implode(', ', $ids_ar)."))) "
                        . "  order by case when {$prefix}page.page_title like '%$query_string%'  then 1 "
                        . "when {$prefix}text.old_text like '%$query_string%' then 1 "
                        . "when {$prefix}page.page_id IN (".  implode(",", $ids_ar).") then 2 end, {$prefix}page.page_id desc";

                $results = $this->getConnection()->select($sqlQuery);
                $final_results = array_merge($final_results, $results);
            }else {
                $sqlQuery .= " and ( "
                        . "({$prefix}page.page_title LIKE '%$query_string%') OR "
                        . "({$prefix}text.old_text LIKE '%$query_string%')) order by {$prefix}page.page_id desc";

                $results = $this->getConnection()->select($sqlQuery);
                $final_results = array_merge($final_results, $results);
            }
        }
    }
    
    return $final_results;
  }

    
}
