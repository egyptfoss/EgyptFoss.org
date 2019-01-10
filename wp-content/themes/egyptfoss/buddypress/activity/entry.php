<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_activity_entry' ); ?>

<?php if ( bp_get_activity_type() !== 'activity_comment' ) : ?>
<li class="" id="activity-<?php bp_activity_id(); ?>"> <!-- class  was  bp_activity_css_class(); -->
	<div class="activity-avatar">
		<a href="<?php bp_activity_user_link(); ?>">

			<?php bp_activity_avatar(); ?>

		</a>
	</div>

	<div class="activity-content">

		<div class="activity-header">
<div class="rfloat">
					<?php if ( bp_activity_user_can_delete() ){

$ac_del_btn = bp_activity_delete_link();
echo $ac_del_btn;

}
			?>
</div>
			<?php bp_activity_action(); ?>
		</div>

		<?php if ( bp_activity_has_content() ) : ?>
            
			<div class="activity-inner">
                            <?php bp_activity_content_body(); ?>
                            <?php
                            $activity_id = bp_get_activity_id();
                            $interests = bp_activity_get_meta($activity_id, 'interest',0);
                            if ( !empty( $interests ) ) {
                                foreach ($interests as $interest) {
                                    $term = get_term( $interest, 'interest' );
                                    if (!empty($term)){
                                      ?><span class="technology-tag"> <?php _e("$term->name", "egyptfoss");?> </span><?php
                                    }
                                }
                            }
                            ?>

			</div>

                    <?php endif; ?>

		<?php

		/**
		 * Fires after the display of an activity entry content.
		 *
		 * @since 1.2.0
		 */
		do_action( 'bp_activity_entry_content' ); ?>

		<div class="activity-meta">

			<?php if ( bp_get_activity_type() == 'activity_comment' ) : ?>

				<a href="<?php bp_activity_thread_permalink(); ?>" class="button view bp-secondary-action btn btn-light" title="<?php esc_attr_e( 'View Conversation', 'egyptfoss' ); ?>"><i class="fa fa-comments"></i> <?php _e( 'View Conversation', 'egyptfoss' ); ?></a>

			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php if ( bp_activity_can_favorite() ) : ?>

					<?php if ( !bp_get_activity_is_favorite() ) : ?>

        <a href="<?php bp_activity_favorite_link(); ?>" id="ef_activity_like_unlike_<?php bp_activity_id() ?>" data-is-like="1" data-activity-id="<?php bp_activity_id() ?>" class="ef_activity_like_unlike button fav bp-secondary-action btn btn-light re-count" title="<?php esc_attr_e( 'Mark as Like', 'egyptfoss' ); ?>"><?php _e( 'Like', 'egyptfoss' ); ?></a>

					<?php else : ?>

						<a href="<?php bp_activity_unfavorite_link(); ?>" id="ef_activity_like_unlike_<?php bp_activity_id() ?>" data-is-like="0" data-activity-id="<?php bp_activity_id() ?>" class="ef_activity_like_unlike button unfav bp-secondary-action btn btn-light re-count" title="<?php esc_attr_e( 'Remove a Like', 'egyptfoss' ); ?>"><?php  _e( 'Dislike', 'egyptfoss' ); ?></a>



					<?php endif; ?>

					<?php if ( bp_activity_can_comment() ) : ?>

					<a href="<?php bp_activity_comment_link(); ?>" class="button acomment-reply bp-primary-action btn btn-light" id="acomment-comment-<?php bp_activity_id(); ?>"> <?php printf( __( 'Comment', 'egyptfoss' ), '<span>' . bp_activity_get_comment_count() . '</span>' ); ?></a>

				<?php endif; ?>

				<?php endif; ?>



				<?php

				/**
				 * Fires at the end of the activity entry meta data area.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_activity_entry_meta' ); ?>

			<?php endif; ?>
		<div class="activity-counters rfloat">
			 	<a href="javascript:void(0)" type="button" class="bp-secondary-action btn btn-link likes-list-btn"  data-target="#likes-modal-<?php bp_activity_id(); ?>" data-placement="bottom" data-toggle="popover" data-html="true" data-container="body" data-trigger="focus">
			<?php $count = bp_activity_get_meta( bp_get_activity_id(), 'favorite_count' ); ?>
			<span class="likes-count-<?php bp_activity_id(); ?>"><?php echo ( $count == 0 )? '' : $count; ?></span>
			<i class="fa fa-thumbs-up"></i>
		</a>
        <a href="javascript:void(0)" class="bp-secondary-action btn btn-link"><span id="ef_bp_activity_comment_count_<?php bp_activity_id(); ?>"><?php echo (bp_activity_get_comment_count == 0)? '': bp_activity_get_comment_count(); ?></span> <i class="fa fa-comment"></i></a>

		<div class="likes-popover popover-content hidden" id="likes-modal-<?php bp_activity_id(); ?>">
              
					<ul id="members-list" class="item-list likes-list likes-<?php bp_activity_id(); ?>">
              <?php echo ef_load_likes_list(bp_get_activity_id())["content"]; ?>
			</ul>

		</div>
		</div>
		</div>



	</div>

	<?php

	/**
	 * Fires before the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_activity_entry_comments' ); ?>

	<?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>

				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<label for="ac-input-<?php bp_activity_id(); ?>" class="bp-screen-reader-text"><?php _e( 'Comment', 'egyptfoss' ); ?></label>
							<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions form-control" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" class="btn btn-primary btn-sm" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'egyptfoss' ); ?>" /> &nbsp; <a href="#" class="ac-reply-cancel btn btn-link btn-sm"><?php _e( 'Cancel', 'egyptfoss' ); ?></a>
						<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
					</div>
          
					<?php

					/**
					 * Fires after the activity entry comment form.
					 *
					 * @since 1.5.0
					 */
					do_action( 'bp_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>
            
				</form>

			<?php endif; ?>
       
		</div>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_activity_entry_comments' ); ?>

</li>
<?php endif; ?>

<?php

/**
 * Fires after the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_activity_entry' ); ?>   
