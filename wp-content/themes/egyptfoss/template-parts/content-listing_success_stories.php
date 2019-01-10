<?php
$category = '-1';
if(get_query_var('ef_listing_success_stories_category_id'))
{
    $category = get_query_var('ef_listing_success_stories_category_id');
}
$months = ef_return_arabic_months();
$lang = pll_current_language();
$args = array(
  "post_status" => "publish",
  "post_type" => "success_story",
  "current_lang" => $lang,
  "offset" => 0,
  "category_id" => $category
);
$list_success_stories = get_success_stories($args);
$homeUrl = home_url();
if (strpos($homeUrl, $lang) === false) {
  $homeUrl = $homeUrl."/$lang";
}
if ($list_success_stories){
foreach ($list_success_stories as $success_story) {
  $post = $success_story->ID;
  setup_postdata($post);
  $success_story_id = $success_story->ID ;
  $meta = get_post_custom($success_story_id);
    ?>
    <article class="story clearfix">
            <h3><a href="<?php echo $homeUrl."/success-stories/".$success_story->post_name; ?>"><?php echo get_the_title($success_story); ?></a></h3>
            <div class="story-meta">
                    <span class="story-category">
                        <?php
                        $category = get_term(get_post_meta($success_story_id, 'success_story_category', true) );
                        if($lang == "ar")
                        {
                            if($category->name_ar != '')
                                $category->name = $category->name_ar;
                        }?>
                        <a href="" onclick="return false;" data-slug="<?php echo rawurlencode($category->name); ?>" class="trigger_click" data-id="<?php echo $category->term_id; ?>"><?php
                            echo $category->name;
                        ?></a>
                    </span>
                    <span class="date"><i class="fa fa-clock-o"></i> <?php
                        if($lang == "en")
                            echo date("d M Y", strtotime($success_story->post_date)) ;
                        else
                            echo str_replace(date("M", strtotime($success_story->post_date)),$months[date("M", strtotime($success_story->post_date))],date("d M Y", strtotime($success_story->post_date))) ;
                    ?></span>
                    <span class="author"><?php _e("By","egyptfoss")?>
                        <?php if(bp_core_get_username($success_story->post_author) != '') { ?>
                            <a href="<?php echo home_url()."/members/".bp_core_get_username($success_story->post_author)."/about/" ?>">
                                <?php echo bp_core_get_user_displayname($success_story->post_author); ?>
                            </a>
                        <?php } else { echo bp_core_get_user_displayname($success_story->post_author); } ?>
                    </span>
            </div>
            <div class="story-picture lfloat">
                <?php
                    $img_id = get_field('_thumbnail_id', $success_story_id, $format_value = true);
                    if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
                      $img_location = get_the_guid($img_id) ;
                      ?><?php echo get_the_post_thumbnail( $success_story->ID, 'news-thumbnail' ); ?><?php
                    }
                    else { // displays default image //
                      ?><img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_image.svg" class="no-article-image" alt="<?php echo $success_story->post_title ; ?>"><?php
                    }
                  ?>
            </div>
            <div class="story-content lfloat">
                    <p>
                      <?php
                        ///global $post;
                        //$post = get_post($success_story->ID);
                        echo get_the_excerpt(); //echo $success_story->post_content;
                      ?>
                    </p>
            </div>
    </article>
    <?php
      wp_reset_postdata();
  }
} else{ ?>
       <div class="empty-state-msg">
             <i class="fa fa-3x fa-file-text"></i>
             <br>
             <p>
                  <?php _e("There are no Success Stories yet, ", "egyptfoss"); ?>
      <?php if ( !is_user_logged_in() ) {?>
        <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addsuccessstory&redirect_to='.get_current_lang_page_by_template("template-add-success-story.php") ); ?>"> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } else if (current_user_can('add_new_ef_posts')) { ?>
        <a href="<?php echo get_current_lang_page_by_template('template-add-success-story.php') ?> "> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } else { ?>
        <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } ?>
             </p>

     </div>
<?php
}
