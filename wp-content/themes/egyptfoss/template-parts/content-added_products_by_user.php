<?php
$args = array(
  "post_status" => "",
  "post_type" => "product",
  "current_lang" => pll_current_language(),
  "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
  "offest" => 0//(get_query_var("ef_product_offest") ? get_query_var("ef_product_offest") : 0),
);
$xprofile_id = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayed_user_id'];
if (get_current_user_id() != $xprofile_id) {
  $args['post_status'] = "publish";
}
$products_list = display_products_by_user($args, $xprofile_id);

if (!empty($products_list)) {
  foreach ($products_list as $product) {
    $product_id = $product->ID ;
  ?>
    <div class="profile-card">
      <div class="inner clearfix">
      	     <div class="card-thumb">
        <?php
        $img_id = get_field('_thumbnail_id', $product_id, $format_value = true);
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
        <h4><a href="<?php echo home_url()."/products/".$product->post_name?>"> <?php  _e("$product->post_title", "egyptfoss"); ?> </a></h4>
        <div>
            <small><i class="fa fa-clock-o"></i> <?php  echo __("Created at", "egyptfoss"). " : ".  date('Y-m-d',strtotime($product->post_date)) ; ?></small>
      </div>
      <?php if (get_post_status ($product->ID) == 'pending' ):?>
      <span class="pending-approval">
        <i class="fa fa-history"></i>
        <?php _e('Pending Approval','egyptfoss') ?>
      </span>
      <?php endif;?>        
      </div>
      </div>
    </div>
  <?php
  }
} else{
  // echo 'There are no products added by '.bp_core_get_username($xprofile_id);
}
?>
