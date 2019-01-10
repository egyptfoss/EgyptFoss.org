<?php
$args = array(
  "post_status" => "publish",
  "post_type" => "product",
  "current_lang" => pll_current_language(),
  "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
  "offest" => 0,// (get_query_var("ef_product_offest") ? get_query_var("ef_product_offest") : 0),
);
$xprofile_id = bp_displayed_user_id();
$products_list = display_contributed_products_by_user_results($args, $xprofile_id);

if (!empty($products_list)) {
  foreach ($products_list as $post) {
    $post = $post->post_id;
    setup_postdata($post);
  ?>
    <div class="profile-card">
     <div class="inner clearfix">
      	     <div class="card-thumb">
        <?php
        $img_id = get_field('_thumbnail_id', get_the_ID(), $format_value = true);
        if ( ! empty( $img_id ) && @ get_class($img_id) != "WP_Error" ) {
          echo get_the_post_thumbnail( $product_id, 'news-thumbnail-small',array("alt" => $product->post_title) );
          //$img_location = get_the_guid($img_id) ;
          ?><!--<img src="<?php echo $img_location ?>" alt="<?php echo $product->post_title ; ?>" />--><?php
        }
        else { // displays default image //
          ?><img src="<?php echo get_template_directory_uri(); ?>/img/no-product-icon.png" alt="<?php echo $product->post_title ; ?>"><?php
        }
        ?>
      </div>
      <div class="product-info">
          <h4><a href="<?php the_permalink() ?>"> <?php the_title();  ?> </a></h4>
         <div>
             <small><i class="fa fa-clock-o"></i> <?php  echo __("Modified at", "egyptfoss"). " : ". the_modified_date('Y-m-d','','',false); ?></small>
      </div>
      </div>
     </div>
    </div>
  <?php
  }
} else{
  // echo 'There are no contributed products by '.bp_core_get_username($xprofile_id);
}
?>
