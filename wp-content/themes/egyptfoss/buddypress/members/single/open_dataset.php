<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Open Datasets","egyptfoss"); ?></h2>
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
      
  <div class="change-email hidden">
  <div class="row">
      <div class="col-md-12">
          <?php bp_get_template_part( 'members/single/open_dataset_contributes' ); ?>
      </div>
    </div>
  </div>

  <div class="change-password">
    <div class="row">
        <div class="col-md-12">
        <?php bp_get_template_part( 'members/single/open_dataset_additions' ); ?>
      </div>
    </div>
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