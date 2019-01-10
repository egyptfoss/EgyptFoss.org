<?php
/**
 * BuddyPress - Activity Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the start of the activity loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_activity_loop' ); ?>
<?php 
if(bp_displayed_user_id() != 0)
{
    $view_id = bp_displayed_user_id();
    $_SESSION["view_id"] = bp_displayed_user_id();
}else
{
    if(isset($_SESSION["view_id"])){
        $view_id = $_SESSION["view_id"];
    }else{
        $view_id = 0;
    }
}
?>
<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ).'&user_id='.$view_id ) ) : ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>

		<ul id="activity-stream" class="activity-list item-list">

	<?php endif; ?>

	<?php while ( bp_activities() ) : bp_the_activity(); ?>

		<?php bp_get_template_part( 'activity/entry' ); ?>

	<?php endwhile; ?>

	<?php if ( bp_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="<?php bp_activity_load_more_link() ?>"><?php _e( 'Load More', 'buddypress' ); ?></a>
		</li>

	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>

		</ul>

	<?php endif; ?>

<?php else : ?>
<div class="empty-state-msg" id="message">
    <i class="fa fa-list-alt"></i>
    <br>
    <h4><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></h4>
</div>

<?php endif; ?>
<!-- Modal -->
<div id="confirm-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __( 'Cancel', 'egyptfoss' );?></button>
        <button type="button" class="btn btn-default btn-ok"><?php echo __( 'Ok', 'egyptfoss' );?></button>
      </div>
    </div>

  </div>
</div>
<script>
    function deletePost(target) {
        var li        = target.parents('div.activity ul li');
        var id        = li.attr('id').substr( 9, li.attr('id').length );
        var link_href = target.attr('data-url');
        var nonce     = link_href.split('_wpnonce=');

        nonce = nonce[1];

        target.addClass('loading');

        jq.post( ajaxurl, {
                action: 'delete_activity',
                'cookie': bp_get_cookies(),
                'id': id,
                '_wpnonce': nonce
        },
        function(response) {

                if ( response[0] + response[1] == '-1' ) {
                        li.prepend( response.substr( 2, response.length ) );
                        li.children('div#message').hide().fadeIn(300);
                } else {
                        li.slideUp(300);
                }
        });
    }
    jQuery( document ).ready(function() {
        if (jQuery("a.delete-activity.confirm").exists())
        {
            jQuery("a.delete-activity.confirm").each(function(){
                jQuery(this).replaceWith('<a class="button btn-delete-confirm bp-secondary-action" data-url="' + jQuery(this).attr('href') + '"><?php echo __( 'Delete', 'buddypress' );?></a>');
            });    
        }
        if (jQuery("a.btn-delete-confirm").exists())
        { 
            jQuery("a.btn-delete-confirm").click(function(e) {
                modal = new fossModal({
                    title: "<?php echo __( 'Delete Activity', 'egyptfoss' );?>",
                    body: "<?php echo __( 'Are you sure you want to delete this?', 'buddypress' );?>",
                    buttons: new Array(
                        {text: "<?php echo __( 'Cancel', 'egyptfoss' );?>", action:"close", class: "btn btn-light"},
                        {text: "<?php echo __( 'Ok', 'egyptfoss' );?>", action:"deletePost", clickedElement: $(this), class:"btn btn-primary"}
                    ),
                    autoSaveBtnClose: true,
                    footer: ""
                });
                modal.show();
            });
        }
    });
</script>
<?php

/**
 * Fires after the finish of the activity loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_activity_loop' ); ?>

<?php if ( empty( $_POST['page'] ) ) : ?>

	<form action="" name="activity-loop-form" id="activity-loop-form" method="post">

		<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>

	</form>

<?php endif; ?>
