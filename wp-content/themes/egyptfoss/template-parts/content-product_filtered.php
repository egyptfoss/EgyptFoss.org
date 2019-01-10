<?php

$params = parse_url($_GET['q']);
parse_str($params['query'],$params);
$params = array_merge($params,$_GET);
$url_term_ids = array();
global $ef_product_filtered_taxs;
foreach ($ef_product_filtered_taxs as $tax)
{
  $term = get_term_by('slug', $params[$tax], $tax);
  if($term)
  {
    $url_term_ids = array_merge($url_term_ids,array($term->term_taxonomy_id));
  }  
}
  if(!empty($url_term_ids))
  {
    set_query_var('term_ids', $url_term_ids);
  }else
  {
    set_query_var('term_ids', $_POST['term_ids']);
  }
  
  set_query_var('ef_product_offest', $_POST['offest']);
  $browseBy = "";
    if( (isset($_POST['browseProductsBy']) && $_POST['browseProductsBy'] == "featured") || $_GET['industry'] == "featured" )
    {
      $browseBy = "featured";
    }  else {
      if(!isset($_GET['industry']) && $_POST['browseProductsBy'] == "")
      {
        $browseBy = "featured";
      }
    }

  $args = array(
    "post_status" => "publish",
    "post_type" => "product",
    "current_lang" => pll_current_language(),
    "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
    "offest" => (get_query_var("ef_product_offest") ? get_query_var("ef_product_offest") : 0),
    "browseProductsBy" => $browseBy
  );
$productList = ef_listing_get_products_by_filter($args,$_POST['newUrl']);
?>
<div class="ef_product_set" data-filtered-product-count="<?php echo get_query_var('ef_product_filtered_count'); ?>">
 <?php
foreach($productList as $post ){
  $post = get_post($post);
  setup_postdata($post);
  ?>
    <?php $isFeatured = get_post_meta(get_the_ID(), 'is_featured', 'true');?>
    <?php $isTopTen = isTopTenProduct(get_the_ID());
    ?>
    <div class="product-card <?php echo (!empty($isFeatured))?'featured-card':''; ?>" itemscope itemtype="http://schema.org/Product" id="<?php the_ID() ?>">
     <div class="inner">
      <div class="product-card-body clearfix">
          <div class="product-img">
              <?php
                $img_id = get_field('_thumbnail_id', get_the_ID(), $format_value = true);
                if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
                  echo get_the_post_thumbnail( get_the_ID(), 'news-thumbnail-small',array('alt'=>get_the_title()) );
                 // $img_location = get_the_guid($img_id) ;
                  ?>
                         <!--<img class="attachment-post-thumbnail size-post-thumbnail wp-post-image" src="<?php echo $img_location ?>"  alt="<?php echo get_the_title();?>" />-->

                <?php }
                else { // displays default image //
                  ?><img src="<?php echo get_template_directory_uri(); ?>/img/no-product-icon.png" alt="<?php echo get_the_title();?>" />
                <?php } ?>
          </div>
          <div class="product-card-info">
              <h3 class="product-name"><a href="<?php the_permalink(); ?>" itemprop="name" title="<?php the_title(); ?>">
                <?php echo wp_trim_words(get_the_title(), 6, ' ...' ); ?>
              </a>
                     <div class="product-badges rfloat">
                  	   <?php
        if(!empty($isFeatured))
        {
      ?>
 	<img class="product-badge" src="<?php echo get_template_directory_uri(); ?>/img/featured-icon.png" alt="<?php _e("Editor's Choice", 'egyptfoss'); ?>">
      <?php } ?>


              	 <?php
        if(!empty($isTopTen))
        {
      ?>
      <img class="product-badge" src="<?php echo get_template_directory_uri(); ?>/img/top-ten.png" alt="<?php _e('Top 10 Products', 'egyptfoss'); ?>">
      <?php } ?>

              </div>
              </h3>
                    <div class="request-info-meta">
          <?php
          $taxonomies = array("license");
          foreach ($taxonomies as $tax) {
            $taxElement = wp_get_post_terms(get_the_ID(), $tax);
            $taxElementCount = count($taxElement);
            if ($taxElement) {
              ?>
              <span class="meta-item">
                  <span><?php _e(ucwords($tax), "egyptfoss"); ?>: </span> 
                  <?php
                  foreach($taxElement as $key=>$element){
                    if($key < 2)
                    {
                      echo ( pll_current_language() == 'ar' && !empty( $taxElement[$key]->name_ar ) )?$taxElement[$key]->name_ar:$taxElement[$key]->name;
                      if ($key < 1 && $taxElementCount != 1) {echo ", ";}
                    }
                  }
                  ?>
              </span>
            <?php }else{ ?>
              <span class="meta-item">
                  <span><?php echo __(ucwords($tax), "egyptfoss"). " --"; ?>: </span> 
              </span>
            <?php }
          } ?>

      </div>
              <p><?php
                $prodct_desc = get_post_meta( get_the_ID(), "description", TRUE);
                $pos= strpos($prodct_desc, ' ', 50);
                $newdesc = substr($prodct_desc,0,$pos );
                if($newdesc)
                {
                 echo html_entity_decode( $newdesc ) . " ..."; 
                }else
                {
                  echo html_entity_decode( $prodct_desc ); 
                }
                
              ?></p>
          </div>
      </div>
     </div>
  </div>

<?php } ?>
</div>
