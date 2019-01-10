<?php
$args = array(
  "post_status" => "publish",
  "post_type" => "news",
  "current_lang" => pll_current_language(),
  "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
  "offset" => 0
);
$list_news = get_news($args);
if ($list_news){
foreach ($list_news as $news) {
  $news_id = $news->ID ;
  $meta = get_post_custom($news_id);
$category = get_term(get_post_meta($news->ID, 'news_category', true) );
    ?>
      <li class="news-card">
        <div class="card-inner">
                     <?php if(!is_wp_error($category)) { ?>
                    <span class="article-tag">
                        <?php 
                        $category_id = $category->term_id;
                        $categ_post_not_id = get_the_ID();
                        if( pll_current_language() == "ar")
                        {
                            if($category->name_ar != '')
                                $category->name = $category->name_ar;
                        }?>
                        <?php  
                            echo $category->name;
                        ?>
                    </span>
        <?php } ?>
          <div class="news-thumbnail">
            <a href="<?php echo get_post_permalink($news->ID); ?>">
            <?php
              $img_id = get_field('_thumbnail_id', $news_id, $format_value = true);
              if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
                $img_location = get_the_guid($img_id) ;
                ?><?php echo get_the_post_thumbnail( $news->ID, 'news-thumbnail' ); ?><?php
              }
              else { // displays default image //
                ?><img src="<?php echo get_template_directory_uri(); ?>/img/empty_article_image.svg" class="no-article-image" alt="<?php echo $news->post_title ; ?>"><?php
              }
            ?>
            </a>
          </div>
          <div class="card-summary">
            <h3>
              <a href="<?php echo get_post_permalink($news->ID); ?>" class="egy-news-title" itemprop="name"><?php echo get_the_title($news); ?></a>
            </h3>
<div class="date">
              <i class="fa fa-clock-o"></i>
              <?php echo mysql2date('d F Y', $news->post_date); ?>
            </div>
            <?php
              $description = $meta['description'];
              if ($description) {
                foreach ( $description as $key => $value ) {
                  ?><p class="egy-news-content"><?php echo wp_trim_words( $value, 18, ' ...' );?></p><?php
                }
                unset($value);
              }
              else {
                ?><p></p><?php
              }
              ?>
          </div>
        </div>
      </li>
    <?php
  }
} else{
  echo 'There are no news';
}
?>
