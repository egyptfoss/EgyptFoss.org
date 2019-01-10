<?php

trait TestTrait {

  public static function addUser() {
    $users = array();
    $test_users = testData::returnUsers();
    foreach ($test_users as $userHash) {
        $userClass = new User();
        $hasher = new PasswordHash(8, true);
        $userHash['password'] = $hasher->HashPassword(trim($userHash['password']));
        $user = $userClass->addUser($userHash);
        $user->save();

        //add user meta
        $args_meta = array(
            'type' => 'Individual',
            'sub_type' => 'developer',
            'interests' => array('interest1'),
            'ict_technology' => array('php'),
            'theme' => array('theme1')
        );
        $user_meta = new Usermeta;
        $user_meta->addMeta($user->ID, $args_meta);
        $user_meta->save();
        $userRelation = new UserRelation();
        $userRelation->addUserRelationByTermAndTaxonomy($user->ID, "php", "technology");
        $userRelation->save();
        
        $userRelation = new UserRelation();
        $userRelation->addUserRelationByTermAndTaxonomy($user->ID, "interest1", "interest");
        $userRelation->save();
        
        $userRelation = new UserRelation();
        $userRelation->addUserRelationByTermAndTaxonomy($user->ID, "theme1", "theme");
        $userRelation->save();
        //add other filters
        $user_meta_nickname = new Usermeta();
        $user_meta_nickname->user_id = $user->ID;
        $user_meta_nickname->meta_key = 'nickname';
        $user_meta_nickname->meta_value = $userHash['username'];
        $user_meta_nickname->save();
        
        $user_meta_first_name = new Usermeta;
        $user_meta_first_name->user_id = $user->ID;
        $user_meta_first_name->meta_key = 'first_name';
        $user_meta_first_name->meta_value = $userHash['username'];
        $user_meta_first_name->save();
        
        $user_meta_last_name = new Usermeta;
        $user_meta_last_name->user_id = $user->ID;
        $user_meta_last_name->meta_key = 'last_name';
        $user_meta_last_name->meta_value = '';
        $user_meta_last_name->save();
        
        $user_meta_description = new Usermeta;
        $user_meta_description->user_id = $user->ID;
        $user_meta_description->meta_key = 'description';
        $user_meta_description->meta_value = '';
        $user_meta_description->save();
        $role = array('author' => 1);
        $roleLevel = 2;
        if(isset($userHash["role"]) && ($userHash["role"]))
        {
          $role = array($userHash["role"] => 1);
          $roleLevel = 1;
        }
        $user_meta_capt = new Usermeta;
        $user_meta_capt->user_id = $user->ID;
        $user_meta_capt->meta_key = 'wpRuvF8_capabilities';
        $user_meta_capt->meta_value = serialize($role);
        
        $user_meta_capt->save();

        $user_meta_level = new Usermeta;
        $user_meta_level->user_id = $user->ID;
        $user_meta_level->meta_key = 'wpRuvF8_user_level';
        $user_meta_level->meta_value = $roleLevel;
        $user_meta_level->save();

        $user_meta_lang = new Usermeta;
        $user_meta_lang->user_id = $user->ID;
        $user_meta_lang->meta_key = 'prefered_language';
        $user_meta_lang->meta_value = 'en';
        $user_meta_lang->save();    
        
        if(isset($userHash["expert"]) && ($userHash["expert"]))
        {
          $user_meta_lang = new Usermeta;
          $user_meta_lang->user_id = $user->ID;
          $user_meta_lang->meta_key = 'is_expert';
          $user_meta_lang->meta_value = $userHash["expert"];
          $user_meta_lang->save();  
        }
        
        //add buddypress signup
        $signup = new Signup();
        $signup_args = array(
            'username' =>   $userHash['username'],
            'email' =>   $userHash['email'],
            'activation_key' =>  '',
            'password' =>  $user->user_pass
        );
        $signup->addSignup($signup_args);
        $signup->active = 1;
        $signup->activated = date("Y-m-d H:i:s");
        $signup->save();
    } 
  }

//End of add quizzes function
  public static function addTestProducts() {
    for ($i = 0; $i < 10; $i++) {
      $langs = array("en", "ar");
      foreach ($langs as $lang) {
        $product_data = array("post_title" => "productTest_" . ($i+1) . "_" . $lang,
          "post_type" => "product",
          "post_status" => "publish");

        $product = new Post();
        $product->addPost($product_data);
        $product->save();
        $product->updateGUID($product->id,"product");
        $product->add_post_translation($product->id, $lang);
        
        //add contributions for foss and espace users
        $product_history_data = array();
        $terms_history_data = array(
            'post_id'=>$product->id,
            'user_id'=> 1,
            'post_title'=>'',
            'description'=>'new description #1',
            'developer'=>'',
            'functionality'=>'',
            'usage_hints'=>'',
            'references'=>'',
            'link_to_source'=>'',
            'type_ids'=>'',
            'type_text'=>'',
            'technology_ids'=>'',
            'technology_text'=>'',
            'platform_ids'=>'',
            'platform_text'=>'',
            'license_ids'=>'',
            'license_text'=>'',            
            'keywords_ids'=>'',
            'keywords_text'=>'',            
            'industry_ids'=>'',
            'industry_text'=>'',            
            'updated_at'=> gmdate('Y-m-d H:i:s', strtotime('+ 3 minutes'))                  
        );
        $product_history_data = array_merge($product_history_data,$terms_history_data);
        foreach ($product_history_data as $key=>$column)
        {
          if(is_array($product_history_data[$key]))
          {
            $product_history_data[$key]=  serialize($column);
          }
        }

        $postHistory = new PostHistory();
        $postHistory->addPostHistory($product_history_data);
        $postHistory->save();
        
        //add other user
        $user = new User();
        $user_id = $user->getUser('espace')->ID;
        $terms_history_data = array(
            'post_id'=>$product->id,
            'user_id'=> $user_id,
            'post_title'=>'',
            'description'=>'new description #2',
            'developer'=>'',
            'functionality'=>'',
            'usage_hints'=>'',
            'references'=>'',
            'link_to_source'=>'',
            'type_ids'=>'',
            'type_text'=>'',
            'technology_ids'=>'',
            'technology_text'=>'',
            'platform_ids'=>'',
            'platform_text'=>'',
            'license_ids'=>'',
            'license_text'=>'',            
            'keywords_ids'=>'',
            'keywords_text'=>'',            
            'industry_ids'=>'',
            'industry_text'=>'',            
            'updated_at'=> gmdate('Y-m-d H:i:s', strtotime('+ 3 minutes'))            
        );
        $product_history_data = array();
        $product_history_data = array_merge($product_history_data,$terms_history_data);
        foreach ($product_history_data as $key=>$column)
        {
          if(is_array($product_history_data[$key]))
          {
            $product_history_data[$key]=  serialize($column);
          }
        }

        $postHistory = new PostHistory();
        $postHistory->addPostHistory($product_history_data);
        $postHistory->save();        
      }
      
      $en_post_id = "";
      foreach ($langs as $lang) {
        $product_data = array("post_title" => "productTest_" . ($i+2) . "_" . $lang . "_translated",
          "post_type" => "product",
          "post_status" => "publish");

        $product = new Post();
        $product->addPost($product_data);
        $product->save();
        $product->add_post_translation($product->id, $lang);
        $product->updateGUID($product->id,"product");
        if ($lang == "ar") {
          $product->link_post_translation($product->id, $lang, $en_post_id);
        } else {
          $en_post_id = $product->id;
        }
      }
    }

    for ($i = 0; $i < 11; $i++) {
      $product_data = array("post_title" => "productTest_" . $i . "with_taxs_" . "en",
        "post_type" => "product",
        "post_status" => "publish");

      $product = new Post();
      $product->addPost($product_data);
      $product->save();
      $product->add_post_translation($product->id, "en");
      $product->updateGUID($product->id,"product");
      $terms_data = array("industry" => array("software-engineering"),
        "technology" => array("php", "python"), 
        "platform" => array("linux"),
        "license" => array("MIT"),
        "type" => array("application"),
        "interest" => array("interest1"));
      $product->updatePostTerms($product->id, $terms_data,true);
      
       //add other user
        $user = new User();
        $user_id = $user->getUser('espace')->ID;
        $terms_history_data = array(
            'post_id'=>$product->id,
            'user_id'=> $user_id,
            'post_title'=>'',
            'description'=>'new description #2',
            'developer'=>'',
            'functionality'=>'',
            'usage_hints'=>'',
            'references'=>'',
            'link_to_source'=>'',
            'type_ids'=>'',
            'type_text'=>'',
            'technology_ids'=>'',
            'technology_text'=>'',
            'platform_ids'=>'',
            'platform_text'=>'',
            'license_ids'=>'',
            'license_text'=>'',            
            'keywords_ids'=>'',
            'keywords_text'=>'',            
            'industry_ids'=>'',
            'industry_text'=>'',            
            'updated_at'=> gmdate('Y-m-d H:i:s', strtotime('+ 3 minutes'))                 
        );
        $product_history_data = array();
        $product_history_data = array_merge($product_history_data,$terms_history_data);
        foreach ($product_history_data as $key=>$column)
        {
          if(is_array($product_history_data[$key]))
          {
            $product_history_data[$key]=  serialize($column);
          }
        }

        $postHistory = new PostHistory();
        $postHistory->addPostHistory($product_history_data);
        $postHistory->save();        
    }
    for ($i = 0; $i < 5; $i++) {
      $product_data = array("post_title" => "productTest_" . $i+40 . "t_application_" . "en",
        "post_type" => "product",
        "post_status" => "publish");

      $product = new Post();
      $product->addPost($product_data);
      $product->save();
      $product->add_post_translation($product->id, "en");
      $product->updateGUID($product->id,"product");
      $terms_data = array("type" => array("application"));
      $product->updatePostTerms($product->id, $terms_data,true);

    }
    
    for ($i = 0; $i < 5; $i++) {
      $product_data = array("post_title" => "productTest_" . $i+40 . "no_taxs_" . "en",
        "post_type" => "product",
        "post_status" => "publish");

      $product = new Post();
      $product->addPost($product_data);
      $product->save();
      $product->add_post_translation($product->id, "en");
      $product->updateGUID($product->id,"product");
      $terms_data = array("industry" => array("software-engineering"));
      $product->updatePostTerms($product->id, $terms_data,true);

    }
  }

  public static function addTestTermTaxonomies() {
    $termTax = new TermTaxonomy();
    $terms_data = array("industry" => array("software-engineering",'Development'),
        "technology" => array("php", "java","python"), 
        "platform" => array("linux"),
        "license" => array("MIT"),
        "type" => array("application","empty-type"),
        "dataset_type" => array("GOV data","Nation","dataset type one"),
        "datasets_license" => array("Open license","GNO", "dataset license1"),
        "theme" => array("theme1","prince","hii"),
        "interest" => array("interest1","interest 1","java","php","python"),
        "success_story_category" => array("Testing Category"),
        "news_category" => array("Testing News Category","News Category"),
        "service_category" => array("mobile","web"),
        "quiz_categories" => array("FOSS", "Open Source"));
	  
    $termTax->saveTermTaxonomies($terms_data);
  }
  
  public static function addTestNews() {
    $lang = "en";
    $test_news = testData::returnNews();
    $i = 0;
    foreach ($test_news as $userHash) {
      $newsClass = new Post();
      $news = $newsClass->addPost($userHash);
      $news->comment_status = "open";
      $news->save();
      $news->updateGUID($news->id,'news');
      if($i == 0)
      {
        $lang = "ar";
        $i++;
      }
      $news->add_post_translation($news->id, $lang);
      
      //Add Subtitle & Description & Allow comments
      $postMetaSub = new Postmeta();
      $subtitle = $postMetaSub->addProductMeta($news->id,"subtitle", "Subtitle ".$userHash['post_title']);
      $subtitle->save();
 
      $postMeta = new Postmeta();
      $description = $postMeta->addProductMeta($news->id,"description", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
      $description->save();
      
      $postMetaComment = new Postmeta();
      $description = $postMetaComment->addProductMeta($news->id,"'_edit_lock'", "");
      $description->save();
      
      $lang = "en";
    }
  }
  
  public static function addTestSuccessStories() {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'success_story')));
    if($total_count > 20)
    {
        var_dump('Success Stories already added. Total Success Stories count = '. $total_count);
        return;
    }
      
    $lang = "en";
    $test_success = testData::returnSuccessStories();
    $i = 0;
    foreach ($test_success as $userHash) {
      $successClass = new Post();
      $success = $successClass->addPost($userHash);
      $success->comment_status = "open";
      $success->post_content = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
      $success->save();
      $success->updateGUID($success->id,"success_story");
      if($i == 0)
      {
        $lang = "ar";
        $i++;
      }
      $success->add_post_translation($success->id, $lang);
      
      //Add Category & Allow comments
      $category = "Testing Category";
      $term = new Term();
      $category = $term->get_terms_by_testing($category,'success_story_category');
      
      $postMetaSub = new Postmeta();
      $subtitle = $postMetaSub->addProductMeta($success->id,"success_story_category", $category->term_id);
      $subtitle->save();
      
      $postMetaComment = new Postmeta();
      $description = $postMetaComment->addProductMeta($success->id,"'_edit_lock'", "");
      $description->save();
      
      $lang = "en";
    }
  }

  public static function addTestExpertThoughts() {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'expert_thought')));
    if($total_count > 20)
    {
        var_dump('Expert Thoughts are  already added. Total Expert Thoughts count = '. $total_count);
        return;
    }
      
    $lang = "en";
    $test_success = testData::returnExpertThoughts();
    $i = 0;
    foreach ($test_success as $userHash) {
      $userHash['post_content']  = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
      $userHash['post_author']  = 1;
      $expertClass = new Post();
      $expert = $expertClass->addPost($userHash);
      $expert->comment_status = "open";
      $expert->save();
      $expert->updateGUID($expert->id,"expert_thought");

      //Add related interests
      if($userHash['post_name'] != "success-22")
      {
        $interest = "interest1";
        $terms_data = array(
        "interest" => array($interest)
        );       
        $expert->updatePostTerms($expert->id, $terms_data, true);
      }
    }
  }
  
  public static function addTestEditExpertThoughts() { 
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'expert_thought')));
      
    $lang = "en";
    $test_success = testData::returnExpertThoughtsForEdit();
    $i = 0;
    foreach ($test_success as $userHash) {
      $userHash['post_content']  = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
      $userHash['post_author']  = 10;
      $expertClass = new Post();
      $expert = $expertClass->addPost($userHash);
      $expert->comment_status = "open";
      $expert->save();
      $expert->updateGUID($expert->id,"expert_thought");

      //Add related interests
      if($userHash['post_name'] != "thought-3")
      {
        $interest = "interest1";
        $terms_data = array(
        "interest" => array($interest)
        );       
        $expert->updatePostTerms($expert->id, $terms_data, true);
      }
    }
  }
  
  public static function addTestOpenDatasets() {
        $post_count = new Post();
        $total_count = count($post_count->getPostCountByType(array('post_type'=>'open_dataset')));
        if($total_count > 20)
        {
            var_dump('Open Datasets already added. Total Open Dataset count = '. $total_count);
            return;
        }
        
        $lang = "en";
        $test_datasets = testData::returnOpenDatasets();
        $i = 0;
        foreach ($test_datasets as $userHash) {
          $datasetClass = new Post();
          $dataset = $datasetClass->addPost($userHash);
          $dataset->post_content = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
          $dataset->save();
          $dataset->updateGUID($dataset->id, "open_dataset");
          if($i == 0) {
            $lang = "ar";
          }
          $dataset->add_post_translation($dataset->id, $lang);

          $postMeta = new Postmeta();
          $postMeta->updatePostMeta($dataset->id, "publisher", "mark zuckerberg");
          $postMeta->updatePostMeta($dataset->id, "description", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
          $postMeta->updatePostMeta($dataset->id, "usage_hints", "It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.");
          $postMeta->updatePostMeta($dataset->id, "references", "Sand cats live in the deserts of North Africa and Southwest and Central Asia.");
          $postMeta->updatePostMeta($dataset->id, "source_link", "http://example.com");
          $terms_data = array(
            "dataset_type" => array("dataset type one"),
            "theme" => array("prince"),
            "datasets_license" => array("dataset license1"),
            "interest" => array("python"));
          $dataset->updatePostTermsSingle($dataset->id, $terms_data, true);
          $terms_data = array(
            "interest" => array("python")
          );
          $dataset->updatePostTerms($dataset->id, $terms_data, true);

          $uploadClass = new Post();
          $upload1 = $uploadClass->addPost(array(
            'post_author' => $dataset->post_author,
            'post_title' => 'Open Dataset Upload 1',
            'post_name' => 'open-dataset-upload-1',
            'post_type' => 'attachment',
            'post_parent' => $dataset->id,
            'post_status' => 'inherit',
            'guid' => 'http://egyptfoss.com/wp-content/uploads/open-dataset1.html'
          ));
          $upload1->save();

          $uploadClass2 = new Post();
          $upload2 = $uploadClass2->addPost(array(
            'post_author' => $dataset->post_author,
            'post_title' => 'Open Dataset Upload 2',
            'post_name' => 'open-dataset-upload-2',
            'post_type' => 'attachment',
            'post_parent' => $dataset->id,
            'post_status' => 'inherit',
            'guid' => 'http://egyptfoss.com/wp-content/uploads/open-dataset2.pdf'
          ));
          $upload2->save();

          $postMeta->updatePostMeta($dataset->id, "resources", 2);
          $postMeta->updatePostMeta($dataset->id, "resources_0_upload", $upload1->id);
          $postMeta->updatePostMeta($dataset->id, "resources_0_resource_status", "publish");
          $postMeta->updatePostMeta($dataset->id, "resources_1_upload", $upload2->id);
          $postMeta->updatePostMeta($dataset->id, "resources_1_resource_status", "publish");
          
          //update post formats and resources ids
          $rsc_ids = $upload1->id.'|||'.$upload2->id;
          $postMeta->updatePostMeta($dataset->id, "resources_ids", $rsc_ids);
          $postMeta->updatePostMeta($dataset->id, "dataset_formats", "text/html|||application/pdf");

          $lang = "en";
          
          //add contribution on some data
          $user = User::where('user_login','=','espace')->first();
          $uploadContributionClass = new Post();
          $uploadContribution1 = $uploadContributionClass->addPost(array(
            'post_author' => $user->ID,
            'post_title' => 'Open Dataset Upload 1',
            'post_name' => 'open-dataset-upload-1',
            'post_type' => 'attachment',
            'post_parent' => $dataset->id,
            'post_status' => 'inherit',
            'guid' => 'http://egyptfoss.com/wp-content/uploads/open-dataset1.html'
          ));
          $uploadContribution1->save();
          $postMeta->updatePostMeta($dataset->id, "resources_2_upload", $uploadContribution1->id);
          if($i % 2 == 0)
          {
            $postMeta->updatePostMeta($dataset->id, "resources_2_resource_status", "pending");          
          }  else {
            $postMeta->updatePostMeta($dataset->id, "resources_2_resource_status", "publish");       
          }
          $postMeta->updatePostMeta($dataset->id, "resources", 3);
          
          $i++;
        }
  }

  public static function addTestEvents() {
    $test_venues = testData::returnVenues();
    foreach ($test_venues as $userHash) {
        $newsClass = new Post();
        $venues = $newsClass->addVenues($userHash);
    }
    
    $test_organizers = testData::returnOrganizers();
    foreach ($test_organizers as $userHash) {
        $newsClass = new Post();
        $orgz = $newsClass->addOrganizers($userHash);
    }
    
    $test_events = testData::returnEvents();
    foreach ($test_events as $userHash) {
        $newsClass = new Post();
        $events = $newsClass->addEvent($userHash);
    }
  }
  
  public static function updateEmailTestingDB() {
    $option_host = new Option();
    $option_host->updateOptionValueByKey('smtp_host','localhost');
    $option_host->updateOptionValueByKey('smtp_port','1025');
    $option_host->updateOptionValueByKey('smtp_auth','false');
  }
  
  public static function addTestTimeline() {
    $data = testData::returnTimeline();
    $i = 0;
    foreach ($data as $userHash) {
        $activity = new Activity();
        $user = new User();
        $user = $user->getUser($userHash['user']);
        if(!empty($user)) {
          $activity->addActivity($user->ID,$user->user_login,$userHash['content']);
          $activity->save();
          if($i==0)
          {
              $activity_commment = new Activity();
              $activity_commment = $activity_commment->addActivityComment($user->ID,$user->user_login,$activity->id,'A New Reply');
              $activity_commment->save();
          }
        }
        $i++;
    }
  }
  
  public static function setOpenDatasetsToEmptyTestTrait() {
    $post_published = new Post();
    $data = $post_published->getPostCountByType(array('post_type'=>'open_dataset','post_status'=>'publish'));
    for($i = 0; $i < sizeof($data); $i++)
    {
        $post_published->updatePostStatus($data[$i]->ID,'publish_stopped');
    }
  }
    
  public static function setRequestCenterToEmptyTestTrait()
  {
      $post_published = new Post();
      $data = $post_published->getPostCountByType(array('post_type'=>'request_center','post_status'=>'publish'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $post_published->updatePostStatus($data[$i]->ID,'publish_stopped');
      }
  }
  
  public static function removePublishedDocuments()
  {
    $published = CollaborationCenterItemHistory::where('status','=','published')->get();
    for($i = 0; $i < sizeof($published); $i++)
    {
      CollaborationCenterItemHistory::where('ID', '=',$published[$i]->ID)->delete();
    }
  }
  
  public static function returnOpenDatasetsTestTrait()
  {
      $post_count = new Post();
      $data = $post_count->getPostCountByType(array('post_type'=>'open_dataset','post_status'=>'publish_stopped'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $post_count->updatePostStatus($data[$i]->ID,'publish');
      }
  }

  public static function returnRequestCenterTestTrait()
  {
      $post_count = new Post();
      $data = $post_count->getPostCountByType(array('post_type'=>'request_center','post_status'=>'publish_stopped'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $post_count->updatePostStatus($data[$i]->ID,'publish');
      }
  }

public static function setNewsTestEmptyTrait(){

  $published_news= new Post();
  $data= $published_news->getPostCountByType(array('post_type'=>'news','post_status'=>'publish'));
  for( $i=0; $i< sizeof($data); $i++){
    $published_news->updatePostStatus($data[$i]->ID,'publish_stopped');
  }
}

public static function returnNewsTestTrait()
  {
      $published_news = new Post();
      $data = $published_news->getPostCountByType(array('post_type'=>'news','post_status'=>'publish_stopped'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $published_news->updatePostStatus($data[$i]->ID,'publish');
      }
  }

  
  public static function setSuccessStoriesToEmptyTestTrait()
  {
      $post_published = new Post();
      $data = $post_published->getPostCountByType(array('post_type'=>'success_story','post_status'=>'publish'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $post_published->updatePostStatus($data[$i]->ID,'publish_stopped');
      }
  }
  
  public static function returnSuccessStoriesTestTrait()
  {
      $post_count = new Post();
      $data = $post_count->getPostCountByType(array('post_type'=>'success_story','post_status'=>'publish_stopped'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $post_count->updatePostStatus($data[$i]->ID,'publish');
      }
  }


  public static function setMarketPlaceToEmptyTestTrait()
  {
      $service_published = new Post();
      $data = $service_published->getPostCountByType(array('post_type'=>'service','post_status'=>'publish'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $service_published->updatePostStatus($data[$i]->ID,'publish_stopped');
      }
  }

    public static function returnarketPlaceTestTrait()
  {
      $service_count = new Post();
      $data = $service_count->getPostCountByType(array('post_type'=>'service','post_status'=>'publish_stopped'));
      for($i = 0; $i < sizeof($data); $i++)
      {
          $service_count->updatePostStatus($data[$i]->ID,'publish');
      }
  }
  
  public static function addTestWiki()
  {
      $post_count = new MwPage();
      $total_count = count($post_count->getPostCountByType());
      if($total_count > 20)
      {
          var_dump('Wiki Pages already added. Total Wiki Pages count = '. $total_count);
          return;
      }
      
      $test_data = testData::returnFOSSPedia();
      $index = 5;
      $counter = 0;
      foreach ($test_data as $userHash) {
          $page = new MwPage();
          $page = $page->addPage($userHash['title'], '');
          $page->page_latest = $index;
          $page->page_len = 14;
          $page->save();
          //add revision
          $rev = new MwRevision();
          $rev = $rev->addRevision($page->page_id, $index, null);
          $rev->rev_timestamp = date( "YmdHis");
          if($counter > (sizeof($test_data) - 3))
              $rev->rev_user = 2;
          else
              $rev->rev_user = 1;
          $rev->rev_deleted = 0;
          $rev->rev_len = 14;
          $rev->rev_minor_edit = 0;
          $rev->rev_parent_id = 0;
          $rev->save();
          
          //add text
          $txt = new MwText();
          $txt = $txt->addPageText($userHash['description']);
          $txt->old_id = $index;
          $txt->save();
          if($counter > (sizeof($test_data) - 3))
          {
              $rev_other = new MwRevision();
              $rev_other = $rev_other->addRevision($page->page_id, $index, null);
              $rev_other->rev_timestamp = date( "YmdHis");
              $rev_other->rev_user = 1;
              $rev_other->rev_deleted = 0;
              $rev_other->rev_len = 14;
              $rev_other->rev_minor_edit = 0;
              $rev_other->rev_parent_id = $index;
              $rev_other->save();
              $index = $index + 1;
              $txt_new = new MwText();
              $txt_new = $txt_new->addPageText($userHash['description']);
              $txt_new->old_id = $index;
              $txt_new->save();
          }
          $index = $index + 1;
          $counter = $counter + 1;
      }
  }
  
  public static function addTestRequests()
  {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'request_center')));
    if($total_count >= 21)
    {
        var_dump('Requests already added. Total Requests count = '. $total_count);
        return;
    }

    $test_data = testData::returnRequests();
    $i = 0;
    foreach ($test_data as $userHash) {
      $singleClass = new Post();
      $success = $singleClass->addPost($userHash);
      $success->comment_status = "open";
      $success->save();
      $success->updateGUID($success->id,"request_center");

      //Add Meta
      if($i == 2)
      {
        $metaData = ['description'];
      }else
      {
        $metaData = ['description', 'requirements', 'constraints', 'deadline'];
      }
      foreach ($metaData as $meta) {
        $itemMeta = new Postmeta();
        $itemMeta->updatePostMeta($success->id, $meta, $userHash['description']);
      }
      
      //load taxonomies
      $target = "joint venture agreement";
      //$term = new Term();
      //$target = $term->get_terms_by_testing($target,'target_bussiness_relationship');

      $theme = "prince";
      //$theme = $term->get_terms_by_testing($theme,'theme');

      $type = "dataset request";
      //$type = $term->get_terms_by_testing($type,'request_center_type');
      if($i == 2)
      {
        $terms_data = array("target_bussiness_relationship" => array($target),
          "request_center_type" => array($type)
        );              
      }else{
        $terms_data = array("target_bussiness_relationship" => array($target),
          "request_center_type" => array($type),
          "theme" => array($theme)
        );    
      }
      $success->updatePostTermsSingle($success->id, $terms_data, true);
      
      $technology = "python";
      //$technology = $term->get_terms_by_testing($technology,'technology');
      
      $interest = "interest1";
      //$interest = $term->get_terms_by_testing($interest,'interest');
      if($i != 2)
      {
        $terms_data = array(
          "technology" => array($technology),
          "interest" => array($interest)
        );       
        $success->updatePostTerms($success->id, $terms_data, true);
      }
      $i++;
      if($i > 13 && $success->post_status == 'publish')
      {
        //add thread
        $thread = new Thread();
        $args = array(
            'request_id' => $success->id,
            'user_id' => 2
        );
        $thread = $thread->addThread($args);
        $thread->responses_count = 1;
        $thread->save();
      }
    }
  }
  
  public static function addTestCollaborationItems() {
    $user = new User();
    $user_id = $user->getUser('bougy-tamtam')->ID;
    #create empty spaces  
    $collaborationCenter = new CollaborationCenterItem();
    $args = array(
      "title" => "my empty space #1",
      "owner_id" => $user_id,
      "is_space" => true,
      "content" => "test",
      "status" => "draft"
    );
    $collaborationCenter->addItem($args);
    $collaborationCenter->save();

    #create not empty spaces
    $collaborationCenter = new CollaborationCenterItem();

    $args = array(
      "title" => "my space #1",
      "owner_id" => $user_id,
      "is_space" => true,
      "content" => "test",
      "status" => "draft"
    );
    $collaborationCenter->addItem($args);
    $collaborationCenter->save();
    $space_id = $collaborationCenter->id;
    for ($j = 1; $j < 3; $j++) {

      $collaborationCenter = new CollaborationCenterItem();
      $args = array(
        "title" => "my document #{$j}",
        "owner_id" => $user_id,
        "is_space" => false,
        "content" => "test",
        "status" => "draft",
        "item_ID" => $space_id
      );
      $collaborationCenter->addItem($args);
      $collaborationCenter->save();
    }

    #create not empty spaces not owned by me but shared with me
    for ($i = 1; $i < 5; $i++) {
      $collaborationCenter = new CollaborationCenterItem();
      $args = array(
        "title" => "user space #{$i}",
        "owner_id" => 1,
        "is_space" => true,
        "content" => "test",
        "status" => "draft"
      );
      $collaborationCenter->addItem($args);
      $collaborationCenter->save();
      $space_id = $collaborationCenter->id;
      if ($i == 2) {
        $userPerm = new CollaborationCenterUserPermission();
        $args = array("permission" => "editor", "user_id" => 1, "item_ID" => $space_id);
        $userPerm->addUserPermission($args);
        $userPerm->save();
      }
      if ($i == 3) {
        $userPerm = new CollaborationCenterUserPermission();
        $args = array("permission" => "editor", "user_id" => 3, "item_ID" => $space_id);
        $userPerm->addUserPermission($args);
        $userPerm->save();
      }

      for ($j = 1; $j < 3; $j++) {

        $collaborationCenter = new CollaborationCenterItem();
        $no = $j + $i;
        $args = array(
          "title" => "user document #{$no}",
          "owner_id" => 1,
          "is_space" => false,
          "content" => "test",
          "status" => "draft",
          "item_ID" => $space_id
        );
        $collaborationCenter->addItem($args);
        $collaborationCenter->save();
        if ($j == 2) {
          $userPerm = new CollaborationCenterUserPermission();
          $args = array("permission" => "editor", "user_id" => $user_id, "item_ID" => $collaborationCenter->id);
          $userPerm->addUserPermission($args);
          $userPerm->save();
          
          //set another user as reviewer 
          $userPerm_reviewer = new CollaborationCenterUserPermission();
          $args = array("permission" => "reviewer", "user_id" => 4, "item_ID" => $collaborationCenter->id);
          $userPerm_reviewer->addUserPermission($args);
          $userPerm_reviewer->save();   
          
          //set another user as publisher 
          $userPerm_publisher = new CollaborationCenterUserPermission();
          $args = array("permission" => "publisher", "user_id" => 6, "item_ID" => $collaborationCenter->id);
          $userPerm_publisher->addUserPermission($args);
          $userPerm_publisher->save();    
          
        }
        if ($j == 3) {
          $userPerm = new CollaborationCenterUserPermission();
          $args = array("permission" => "editor", "user_id" => 3, "item_ID" => $collaborationCenter->id);
          $userPerm->addUserPermission($args);
          $userPerm->save();
          
          //set another user as reviewer 
          $userPerm_reviewer = new CollaborationCenterUserPermission();
          $args = array("permission" => "reviewer", "user_id" => 4, "item_ID" => $collaborationCenter->id);
          $userPerm_reviewer->addUserPermission($args);
          $userPerm_reviewer->save();   
          
          //set another user as publisher 
          $userPerm_publisher = new CollaborationCenterUserPermission();
          $args = array("permission" => "publisher", "user_id" => 6, "item_ID" => $collaborationCenter->id);
          $userPerm_publisher->addUserPermission($args);
          $userPerm_publisher->save();    
        }
      }
    }

    #create not empty spaces not owned by me but shared with me by tax and subtype
    for ($i = 1; $i < 5; $i++) {
      $collaborationCenter = new CollaborationCenterItem();
      $args = array(
        "title" => "user space tax and subtype #{$i}",
        "owner_id" => 1,
        "is_space" => true,
        "content" => "test",
        "status" => "draft"
      );

      $args['title'] = ($i == 2) ? "user space tech #{$i} php" : $args['title'];
      $args['title'] = ($i == 3) ? "user space interest #{$i} interest1" : $args['title'];

      $collaborationCenter->addItem($args);
      $collaborationCenter->save();
      $space_id = $collaborationCenter->id;
      if ($i == 2) {
        $term = Term::where("slug", "=", "php")->first();
        $tax = new TermTaxonomy();
        $tax = $tax->getTermTaxonomy($term->term_id, "technology");
        $args = array("permission" => "editor", "tax_id" => $tax->term_taxonomy_id, "item_ID" => $space_id,"taxonomy"=>$tax->taxonomy,
            "permission_from" => "space");
        $taxPerm = new CollaborationCenterTaxPermission();
        $taxPerm->addTaxPermission($args);
        $taxPerm->save();
      }
      if ($i == 3) {
        $term = Term::where("slug", "=", "interest1")->first();
        $tax = new TermTaxonomy();
        $tax = $tax->getTermTaxonomy($term->term_id, "interest");
        $taxPerm = new CollaborationCenterTaxPermission();
        $args = array("permission" => "editor", "tax_id" => $tax->term_taxonomy_id, "item_ID" => $space_id,"taxonomy"=>$tax->taxonomy,
            "permission_from" => "space");
        $taxPerm->addTaxPermission($args);
        $taxPerm->save();
      }

      for ($j = 1; $j < 3; $j++) {

        $collaborationCenter = new CollaborationCenterItem();
        $no = $j + $i;
        $args = array(
          "title" => "user document tax and subtype #{$no}",
          "owner_id" => 1,
          "is_space" => false,
          "content" => "test",
          "status" => "draft",
          "item_ID" => $space_id
        );
        $args['title'] = ($no == 2) ? "user document subtype #{$no} developer" : $args['title'];
        $collaborationCenter->addItem($args);
        $collaborationCenter->save();
        if ($no == 2) {
          $userPerm = new CollaborationCenterAnonPermission();
          $args = array("permission" => "editor", "name" => "developer", "type" => "sub_type", "item_ID" => $collaborationCenter->id,
              "permission_from" => "space");
          $userPerm->addAnonPermission($args);
          $userPerm->save();
        }
      }
    }
    
    $collaborationCenter = new CollaborationCenterItem();
    $args = array(
      "title" => "grp share space #1",
      "owner_id" => $user_id,
      "is_space" => true,
      "content" => "test",
      "status" => "draft"
    );
    $collaborationCenter->addItem($args);
    $collaborationCenter->save();
    $space_id = $collaborationCenter->id;
    for ($x = 1; $x < 7; $x++) {

      $collaborationCenter = new CollaborationCenterItem();
      $args = array(
        "title" => "grp document #{$x}",
        "owner_id" => $user_id,
        "is_space" => false,
        "content" => "test",
        "status" => "draft",
        "item_ID" => $space_id
      );
      $collaborationCenter->addItem($args);
      $collaborationCenter->save();
    }
    
    for ($x = 1; $x < 7; $x++) {

      $collaborationCenter = new CollaborationCenterItem();
      $args = array(
        "title" => "grp share space #{$x}",
        "owner_id" => $user_id,
        "is_space" => false,
        "content" => "test",
        "status" => "draft",
        "is_space" => true,
      );
      $collaborationCenter->addItem($args);
      $collaborationCenter->save();
    }
  }

  public static function addTestServices() {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'service')));
    if($total_count >= 21) {
      var_dump('Services already added. Total Services count = '. $total_count);
      return;
    }

    $lang = "en";
    $test_data = testData::returnServices();
    $i = 0;
    foreach ($test_data as $userHash) {
      $i++;
      $userHash['post_content']  = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
      if( $i > 6 ) {
        $author = 1;
      }
      else if( $i > 4 ) {
        $author = 2;
      }
      else if( $i > 2 ) {
        $author = 3;
      }
      else {
        $author = 4;
      }
      
      $userHash['post_author']  = $author;
      $singleClass = new Post();
      $service = $singleClass->addPost($userHash);
      $service->comment_status = "open";
      $service->save();
      $service->updateGUID($service->id,"service");

      $service_category = "mobile";
      $terms_data = array("service_category" => array($service_category) );
      $service->updatePostTermsSingle($service->id, $terms_data, true);

      if($userHash['post_name'] == "service-1") {
        $technology = "python";
        $interest = "interest 1";
        $terms_data = array(
          "technology" => array($technology),
          "interest" => array($interest)
        ); 
        $service->updatePostTerms($service->id, $terms_data, true);

        $theme = "prince";
        $terms_data = array("theme" => array($theme));
        $service->updatePostTermsSingle($service->id, $terms_data, true);
        
        $conditions = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $postMetaCond = new Postmeta();
        $subtitle = $postMetaCond->addProductMeta($service->id,"conditions", $conditions);
        $subtitle->save();
        $constraints = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $postMetaCons = new Postmeta();
        $subtitle = $postMetaCons->addProductMeta($service->id,"constraints", $constraints);
        $subtitle->save();
      }
      /**
       * - We are about to create 21 services.
       * - 8 Top services with avg rate = 4.00.
       * - These top service every 2 service is 
       *   assigned to different provider.
       */
      if($i < 10 && $service->post_status == 'publish') {
        // 2 reviews with rate 4.00
        // user 1 => ID: 5
        // user 2 => ID: 6
        for( $j = 0; $j < 2 ; $j++ ) {
          $user_id = $j + 5;
          $rate = 4.00;
          
          // to test archiving
          if( $i == 9 ) {
            $user_id = 2;
          }
          
          // 1- Create thread
          $thread = new Thread();
          $thread->addThread(array(
            'request_id' => $service->id,
            'user_id' => $user_id,
            
          ));
          $thread->save();
          
          $thread->updateThread( array(
            'responses_count' => 1,
            'seen_by_owner'   => 1,
            'seen_by_user'    => 0
          ) );
          $thread->save();
          
          // 2- Create response to the thread
          $service_response = new Response;
          $service_response->addResponse(array(
            'request_id'  => $service->id,
            'thread_id'   => $thread->id,
            'owner_id'    => $service->post_author,
            'user_id'     => $user_id,
            'message'     => 'Hello FOSS',
          ));
          $service_response->save();

          // 3- Create review rate
          $review = new Review;
          $review->saveReview(array(
            'rateable_id' => $service->id,
            'provider_id' => $service->post_author,
            'reviewer_id' => $user_id,
            'rate'        => $rate,
            'review'      => 'Well done '.$service->post_title,
          ));
          $review->save();
          $review->updateAverageRate($service->id);
        }
      }
    }
  }
  
  public static function addQuizzes()
  {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'quiz')));
    if($total_count >= 11) {
      var_dump('Quizzes already added. Total Quizzes count = '. $total_count);
      return;
    }

    $test_data = testData::returnQuizzes();
    for($i = 0; $i < sizeof($test_data); $i++)
    {
      //Add Quiz
      $args = array(
        "name" => $test_data[$i]["quiz_title"],
        "message_before" => "Welcome to your %QUIZ_NAME%",
        "message_after" => "%QUESTIONS_ANSWERS%",
        "message_end_template" => "",
        "user_email_template" => "%QUESTIONS_ANSWERS%",
        "admin_email_template" => "%QUESTIONS_ANSWERS%",
        "randomness" => 0          
      );
      
      $quiz_post = new Quiz();
      $quiz = $quiz_post->addQuiz($args);
      $quiz->save();
      
      //Add post related to this quiz
      $post_args = array(
          "post_title" => $test_data[$i]["quiz_title"],
          "post_content" => "[mlw_quizmaster quiz=$quiz->id]",
          "post_status" => $test_data[$i]["post_status"],
          "post_type" => "quiz"
      );
      $post = new Post();
      $quiz_related_post = $post->addPost($post_args);
      $quiz_related_post->save();
      
      //Add post language and meta and guid
      $quiz_related_post->updateGUID($quiz_related_post->id,"quiz");
      $quiz_related_post->save();
      
      $quiz_related_post->add_post_translation($quiz_related_post->id, "en");
      
      $category = "FOSS";
      $terms_data = array(
        "quiz_categories" => array($category)
      );    
      $post->updatePostTermsSingle($quiz_related_post->id, $terms_data, true);
      //update post meta_key of quiz_categories to be category
      $updateCategory = new Postmeta();
      $updateCategory = Postmeta::where('post_id','=',$quiz_related_post->id)
               ->where('meta_key','=','quiz_categories');
      if($updateCategory->first()) {
        $updateCategory->update(array("meta_key" => 'category'));
      }
      
      $interest = "interest1";
      $terms_data = array(
        "interest" => array($interest)
      );       
      $post->updatePostTerms($quiz_related_post->id, $terms_data, true);
      
      //add quiz_id as post meta
      $postMeta = new Postmeta();
      $postMeta->addProductMeta($quiz_related_post->id, "quiz_id", $quiz->id)->save();
      
      //answers array
      $answers = array();
      
      //Add Questions for each quiz
      foreach($test_data[$i]['questions'] as $key =>$questions)
      {
        $answers = array();
        $answer_index = 1;
        foreach($questions as $answer_key => $answer_value)
        {
          if($answer_value)
          {
            $answer_value = 1;
          }else {
            $answer_value = 0;
          }
          array_push($answers, array(
              $answer_key,0,$answer_value,$answer_index
          ));
          
          $answer_index++;
        }
        
        $question_args = array(
            "quiz_id" => $quiz->id,
            "name" => $key,
            "answer_array" => serialize($answers)
        );
        $question = new QuizQuestion();
        $question_item = $question->addQuestion($question_args);
        $question_item->save(); 
        
        $answer[] = array(
            'question_id' => $question_item->id,
            "answer_id" => 1
        );
      }
      
      //Add Result
      if($i == (sizeof($test_data) - 3))
      {
        $user = User::find(1);
        $quiz_result = new QuizResult();        
        //Load Quiz to pass to result page
        $resultQuiz = Quiz::where('quiz_id','=',$quiz->id);
        $result = $quiz_result->addResult($resultQuiz->first(),$user,$answer,120);
        $result->save();
        
        if($resultQuiz->first())
        {
          $resultQuiz->update(array("quiz_taken"=> 1)); 
        }
      }
    }
  }
  
  public static function setPublishedDocuments()
  {
    $documents = CollaborationCenterItem::where('is_space','=',0)->take(4)->get();
    foreach($documents as $document)
    {
      $args = array(
          'title' => $document->title,
          'content' => $document->content,
          'editor_id' => $document->owner_id,
          'status' => 'published',
          'item_ID' => $document->ID,
          'section' => 'news'
      );
      $itemHistory = new CollaborationCenterItemHistory();
      $itemHistory->addItem($args);
      $itemHistory->save();
    }
  }

  public static function addNewsBadgeFirstUser() {
    $lang = "en";
    $test_data = testData::returnNewsBadgeFirstUser();
    $i = 0;
    $newsActionLevel1 = EFBActions::where('name','=','publish_news_l1')->first();
    $newsActionLevel2 = EFBActions::where('name','=','publish_news_l2')->first();
    foreach ($test_data as $userHash) {
      $newsClass = new Post();
      $news = $newsClass->addPost($userHash);
      $news->comment_status = "open";
      $news->save();
      $news->updateGUID($news->id,'news');
      $newsCredit = new EFBCreditedUserPosts();
      $newsCredit->addCreditedPostUser(array("post_id"=>$news->id,"user_id"=>6,"action_id"=>$newsActionLevel1->id));
      $newsCredit->save();
      $newsCredit = new EFBCreditedUserPosts();
      $newsCredit->addCreditedPostUser(array("post_id"=>$news->id,"user_id"=>6,"action_id"=>$newsActionLevel2->id));
      $newsCredit->save();
      if($i == 0)
      {
        $lang = "ar";
        $i++;
      }
      $news->add_post_translation($news->id, $lang);
      
      //Add Subtitle & Description & Allow comments
      $postMetaSub = new Postmeta();
      $subtitle = $postMetaSub->addProductMeta($news->id,"subtitle", "Subtitle ".$userHash['post_title']);
      $subtitle->save();
 
      $postMeta = new Postmeta();
      $description = $postMeta->addProductMeta($news->id,"description", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
      $description->save();
      
      $postMetaComment = new Postmeta();
      $description = $postMetaComment->addProductMeta($news->id,"'_edit_lock'", "");
      $description->save();
      
      $lang = "en";
    }
    //add user actions level1
    Usermeta::updateActionCount(6, $newsActionLevel1->id, 8);
    //add user actions level2
    Usermeta::updateActionCount(6, $newsActionLevel2->id, 8);
    //add user points
    Usermeta::updateBadgesPoints(6,80);
  }

  public static function addNewsBadgeSecondUser() {
    $lang = "en";
    $test_data = testData::returnNewsBadgeSecondUser();
    $i = 0;
    $newsActionLevel1 = EFBActions::where('name','=','publish_news_l1')->first();
    $newsActionLevel2 = EFBActions::where('name','=','publish_news_l2')->first();
    foreach ($test_data as $userHash) {
      $newsClass = new Post();
      $news = $newsClass->addPost($userHash);
      $news->comment_status = "open";
      $news->save();
      $news->updateGUID($news->id,'news');
      $newsCredit = new EFBCreditedUserPosts ();
      $newsCredit->addCreditedPostUser(array("post_id"=>$news->id,"user_id"=>5,"action_id"=>$newsActionLevel1->id));
      $newsCredit = new EFBCreditedUserPosts();
      $newsCredit->addCreditedPostUser(array("post_id"=>$news->id,"user_id"=>5,"action_id"=>$newsActionLevel2->id));
      $newsCredit->save();
      $newsCredit->save();
      if($i == 0)
      {
        $lang = "ar";
        $i++;
      }
      $news->add_post_translation($news->id, $lang);
      
      //Add Subtitle & Description & Allow comments
      $postMetaSub = new Postmeta();
      $subtitle = $postMetaSub->addProductMeta($news->id,"subtitle", "Subtitle ".$userHash['post_title']);
      $subtitle->save();
 
      $postMeta = new Postmeta();
      $description = $postMeta->addProductMeta($news->id,"description", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");
      $description->save();
      
      $postMetaComment = new Postmeta();
      $description = $postMetaComment->addProductMeta($news->id,"'_edit_lock'", "");
      $description->save();
      
      $lang = "en";
    }
    //add user actions level1
    Usermeta::updateActionCount(5, $newsActionLevel1->id, 10);
    //add user actions level2
    Usermeta::updateActionCount(5, $newsActionLevel2->id, 10);
    //add user points
    Usermeta::updateBadgesPoints(5,100);
  }

  public static function addTopServices() {
    $post_count = new Post();
    $total_count = count($post_count->getPostCountByType(array('post_type'=>'service')));
    if($total_count >= 21) {
      var_dump('Services already added. Total Services count = '. $total_count);
      return;
    }

    $lang = "en";
    $test_data = testData::returnTopServices();
    $i = 0;
    foreach ($test_data as $userHash) {
      $userHash['post_content']  = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
      $singleClass = new Post();
      $service = $singleClass->addPost($userHash);
      $service->comment_status = "open";
      $service->save();
      $service->updateGUID($service->id,"service");

      $service_category = "mobile";
      $terms_data = array("service_category" => array($service_category)
        );
      $service->updatePostTermsSingle($service->id, $terms_data, true);

      if($userHash['post_name'] == "top-service-1")

        {
        $technology = "python";
        $interest = "interest1";
        $terms_data = array(
        "technology" => array($technology),
        "interest" => array($interest)
        ); 
        $service->updatePostTerms($service->id, $terms_data, true);

        $theme = "prince";
        $terms_data = array("theme" => array($theme)
        );
        $service->updatePostTermsSingle($service->id, $terms_data, true);
        
        $conditions = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $postMetaCond = new Postmeta();
        $subtitle = $postMetaCond->addProductMeta($service->id,"conditions", $conditions);
        $subtitle->save();
        $constraints = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.";
        $postMetaCons = new Postmeta();
        $subtitle = $postMetaCons->addProductMeta($service->id,"constraints", $constraints);
        $subtitle->save();
      }

      $i++;
      if($i > 13 && $service->post_status == 'publish') {
        $thread = new Thread();
        $args = array(
          'request_id' => $service->id,
          'user_id' => 2
        );
        $thread = $thread->addThread($args);
        $thread->responses_count = 1;
        $thread->save();
      }
    }
  }
}