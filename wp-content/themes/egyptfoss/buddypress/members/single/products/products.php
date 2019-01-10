<?php 
wp_enqueue_script( 'user_contributed_products-js', get_stylesheet_directory_uri() . '/js/user_contributed_products.js', array('jquery'), '', true);
wp_localize_script('user_contributed_products-js', 'profile', array("bp_user_id" => bp_displayed_user_id()));
wp_enqueue_script( 'user_added_products-js', get_stylesheet_directory_uri() . '/js/user_added_products.js', array('jquery'), '', true);
wp_localize_script('user_added_products-js', 'profile', array("bp_user_id" => bp_displayed_user_id()));
?>
<?php 
$show_contr = ef_show_contribution_li();
if($show_contr['total_count'] > 0)
{
?>
<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Products","egyptfoss"); ?></h2>
	</div>
</header>
<div class="row">
	<div class="col-md-12">
	<div class="item-list-tabs no-ajax" id="sub-nav">
				<ul>
			<li class="current selected"><a id="change-pass" class="chng-pass"><?php _e( 'Additions', 'egyptfoss' ); ?></a></li>
			<li><a id="change-email" class="chng-email"><?php _e( 'Edits', 'egyptfoss' ); ?></a></li>
		</ul>
	</div>
	</div>
</div>

<div class="change-email hidden">
<div class="row">
    <div class="col-md-12">
      <?php bp_get_template_part( 'members/single/products/contributes' ); ?>
    </div>
	</div>
</div>


<div class="change-password">
	<div class="row">
      <div class="col-md-12">
      <?php bp_get_template_part( 'members/single/products/additions' ); ?>
		</div>
	</div>
</div>

<script>
jQuery( document ).ready(function() {
    jQuery("#change-pass").on('click',function()
    {
       jQuery("#change-pass").parent().addClass('current selected');
       jQuery("#change-email").parent().removeClass('current selected');
    }); 
    
    jQuery("#change-email").on('click',function()
    {
       jQuery("#change-email").parent().addClass('current selected');
       jQuery("#change-pass").parent().removeClass('current selected');
    });     
});
</script>

<?php }else { ?>
        <div class="row">
            <div class="col-md-12">
               <div class="empty-state-msg">
                   <i class="fa fa-warning"></i>
                   <br>
                   <h4>
    <?php _e("There are no contributions by ", "egyptfoss"); ?><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>      
                   </h4>
               </div>
           </div>  
          </div>   
<?php } ?>
