<?php 
$term_ids = getTermIdsForTopTenProducts(5, 'industry');
if(!$term_ids && $_POST['offset'] == 0 )
{
  if(empty($random_term_ids))
  {
    _e("Featured products aren't specified","egyptfoss");
  }
}else
{
global $random_term_ids;
$local_term_ids = array();
foreach($term_ids as $term)
{ ?>
<section class="featured-group">
	<div class="group-header">
      <?php
      $term_name = $term->name;
      if(pll_current_language() == "ar" && $term->name_ar != "")
      {
        $term_name = $term->name_ar;
      }
      ?>
      <h4 itemprop="name"><?php printf(__('Top 10 in %s','egyptfoss'), $term_name)?></h4>
		<a href="<?php echo home_url(pll_current_language()."/products/")."?industry=".$term->slug ?>" class="rfloat"><?php _e('See more','egyptfoss') ?></a>
	</div>
	<div class="group-content">
		<ul class="default-carousel" itemscope itemtype="http://schema.org/ItemList">
       <?php $topTenProducts = getTopTenProducts(intval($term->term_id), pll_current_language()); 
             if($topTenProducts)
             { 
             foreach ($topTenProducts as $post)
             { 
                $post = $post->ID;
                if (pll_current_language() == "ar") {
                  $post_ids = pll_get_post_translations($post);
                  if (isset($post_ids["ar"])) {
                    $post = $post_ids["ar"];
                  } else {
                    continue;
                  }
                }
                setup_postdata($post);
    ?> 
			<li class="carousel-item fpritem text-center clearfix" itemprop="itemListElement" itemtype="http://schema.org/Product">
				<div class="product-icon">
                                    <div class="product-img lfloat">
                                        <?php
                                          $img_id = get_field('_thumbnail_id', get_the_ID(), $format_value = true);
                                          if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
                                            //$img_location = get_the_guid($img_id) ;
                                            echo get_the_post_thumbnail( get_the_ID(), 'news-thumbnail-small',array('alt'=>get_the_title()) );
                                            ?>
                                                  <!-- <img class="attachment-post-thumbnail size-post-thumbnail wp-post-image" src="<?php echo $img_location ?>"  alt="<?php echo get_the_title();?>" /> -->

                                          <?php }
                                          else { // displays default image //
                                            ?><img src="<?php echo get_template_directory_uri(); ?>/img/no-product-icon.png" alt="<?php echo get_the_title();?>" />
                                          <?php } ?>
                                    </div>
				</div>
          <h5 itemprop="name">
              <a href="<?php the_permalink(); ?>">
              <?php the_title() ?>
            </a>
          </h5>
			</li>
             <?php }
             }else
             {
                _e("There are no results,","egyptfoss");
             }
             ?>
		</ul>
	</div>
</section>
<?php
array_push($random_term_ids, ($term->term_id));
array_push($local_term_ids, ($term->term_id));
}

?>
<input type="hidden" name="random_term_ids" value="<?php echo join(',',$local_term_ids); ?>">
<?php 
}
?>
