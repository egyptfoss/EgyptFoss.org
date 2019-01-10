<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class WPHomepageController extends EgyptFOSSController {
   
   /**
   * @SWG\GET(
   *   path="/homepage",
   *   tags={"HomePage"},
   *   summary="List news, events,succes stories",
   *   description="List success stories random, news 1 featured and latest news, and upcoming events",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Homepage Data")
   * )
   */
  public function listHomepage($request, $response, $args){
    
    $lang = "";
    if(!isset($_GET["lang"]))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","lang"));
    }
    
    $lang = $_GET["lang"];
    if($lang != "en" && $lang != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    $post = new Post();
    $list_success_stories = $post->listHomePageSuccessStories($lang);
    foreach($list_success_stories as $story)
    {
      //get thumbnail image
      $post_meta = new Postmeta();
      $meta = $post_meta->getPostMeta($story->ID);
      $success_story_meta = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $success_story_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
      }
      unset($meta_value);      
      $url = '';
      if(array_key_exists('_thumbnail_id', $success_story_meta))
      {
        $post_type = "attachment";
        $post_status = "inherit";
        $attachment_id = $success_story_meta['_thumbnail_id'];
        $success_story_image = Post::getPostByID($attachment_id, $post_type, $post_status);
        if($success_story_image){
          $url = $success_story_image->guid;  
        }

        if($url == '')
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }else
        {
          //return thumbnail size in listing
          $url = $this->ef_image_sizes($url,'340x210');
        }
      }else
      {
        $option = new Option();
        $host = $option->getOptionValueByKey('siteurl');
        $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
      }
      $story->post_img = $url;
    }
    
    $list_news = $post->listHomePageNews($lang);
    foreach($list_news as $story)
    {
      //get thumbnail image
      $post_meta = new Postmeta();
      $meta = $post_meta->getPostMeta($story->ID);
      $success_story_meta = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $success_story_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
      }
      unset($meta_value);      
      $url = '';
      if(array_key_exists('_thumbnail_id', $success_story_meta))
      {
        $post_type = "attachment";
        $post_status = "inherit";
        $attachment_id = $success_story_meta['_thumbnail_id'];
        $success_story_image = Post::getPostByID($attachment_id, $post_type, $post_status);
        if($success_story_image){
          $url = $success_story_image->guid;  
        }

        if($url == '')
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }else
        {
          //return thumbnail size in listing
          $url = $this->ef_image_sizes($url,'340x210');
        }
      }else
      {
        $option = new Option();
        $host = $option->getOptionValueByKey('siteurl');
        $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
      }
      $story->post_img = $url;
    }    
    $list_events = $post->listHomePageEvents();
    $output["news"] = $list_news;
    $output["events"] = $list_events;
    $output["success-stories"] = $list_success_stories;
    return $this->renderJson($response, 200, $output);
  }
}

