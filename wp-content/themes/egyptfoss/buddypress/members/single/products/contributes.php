<?php
/**
 * Egyptfoss- user profile products
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
?>
<?php
$args = array(
  "post_status" => "publish",
  "post_type" => "product",
  "current_lang" => pll_current_language(),
  "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
  "offest" => 0,
);
$xprofile_id = (bp_displayed_user_id())?bp_displayed_user_id():$_POST['displayed_user_id'];
set_query_var('ef_product_offest', $_POST['offest']);
$count = display_contributed_products_by_user($args, $xprofile_id);
$count = strval(count($count));
if ($count){
?>
<div class="row">
  <div class="col-md-12">
    <div class="row" id="load_user_contributed_product_by_ajax_container">
     <div class="col-md-12 section-container">
     	  <?php
      get_template_part('template-parts/content', 'contributed_products');
      ?>
     </div>
    </div>
    <div class=" pagination-row clearfix view-more-contributed hidden">
      <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_contributed_products" data-offest="20" data-count="<?php echo $count ; ?>">
        <?php _e("Load more...", "egyptfoss"); ?>
      </a>
      <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
    </div>
  </div>
</div>
<?php
} else{?>
          <div class="row">
            <div class="col-md-12">
               <div class="empty-state-msg">
                   <i class="fa fa-warning"></i>
                   <br>
                   <h4>
         <?php _e("There are no updates by ", "egyptfoss"); ?><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>       
                   </h4>
               </div>
           </div>  
          </div>  
  
  <?php
}?>
