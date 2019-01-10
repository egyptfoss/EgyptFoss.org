<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Request Center","egyptfoss"); ?></h2>
	</div>
</header>
<div class="row">
	<div class="col-md-12">
  <?php  if(get_current_user_id() == bp_displayed_user_id()) { ?>
	<div class="item-list-tabs no-ajax" id="sub-nav">
				<ul>
			<li class="current selected"><a id="change-pass" class="chng-pass"><?php _e( 'Requests', 'egyptfoss' ); ?></a></li>
			<li><a id="change-email" class="chng-email"><?php _e( 'Responses', 'egyptfoss' ); ?></a></li>
		</ul>
	</div>
      
  <div class="change-email hidden">
  <div class="row">
      <div class="col-md-12">
          <?php bp_get_template_part( 'members/single/request_center_responses' ); ?>
      </div>
    </div>
  </div>


  <div class="change-password">
    <div class="row">
        <div class="col-md-12">
        <?php bp_get_template_part( 'members/single/request_center_requests' ); ?>
      </div>
    </div>
  </div>
      
  <?php } else { ?>
      <?php bp_get_template_part( 'members/single/request_center_requests' ); ?>
  <?php } ?>
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