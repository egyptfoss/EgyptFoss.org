
<section class='leave-comment'>
	<?php
        global $wpdb;
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
        
        $query = "SELECT u.ID FROM ".$wpdb->base_prefix."users u WHERE ID = $user_ID";
        $user = $wpdb->get_row($query); 
        
	$args = array(
		'id_form' => 'commentform',
		'id_submit' => 'submit',
		'title_reply' => __( 'Leave a Comment' ,'egyptfoss'),
		'title_reply_to' =>  __( 'Leave a Comment to %s'  ,'egyptfoss'),
		'cancel_reply_link' => __( 'Cancel Comment'  ,'egyptfoss'),
		'label_submit' => __( 'Send'  ,'egyptfoss'),
		'comment_field' => '
			<div class="input-field text-area form-group">
			<div class="col-md-12">
				<label class="label">
					' . __( 'Comment', 'egyptfoss') . '
				</label>
				<div>
					<textarea aria-required="true" rows="3" class="form-control" cols="1" name="comment" id="comment" ' . $aria_req . '></textarea>
				</div>
				</div>
			</div>',
		'must_log_in' => '<p class="must-log-in">'.sprintf(__("Please <a href=\"%s\">log in</a> to share your comments.","egyptfoss"), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ).'</p>',
		'logged_in_as' => '',
		'comment_notes_before' => '<div class="separator"><div></div></div>',

		'fields' => apply_filters( 'comment_form_default_fields',
			array(
				'author' => '
					<div class="form-group">
				  	<div class="col-md-6">

					    	<label class="label">
							' . __( 'Name', 'egyptfoss' ) . ' ' . ( $req ? '<span>'.__( '(required)').'</span>' : '' ) . '
					    	</label>


							<input id="author" class="form-control" type="text" ' . $aria_req . ' size="20" value="' . esc_attr( $commenter['comment_author'] ) . '" name="author">

					</div>',
				'email' => '
					<div class="col-md-6">
						   <label class="label">
							' . __( 'Email', 'egyptfoss' ) . ' ' . ( $req ? '<span>'.__( '(required)').'</span>' : '' ) . '
						   </label>

							<input id="email" class="form-control" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="20"' . $aria_req . ' />
					</div>'
			)
		)
	);
	if(get_post_status( get_the_ID() ) == 'publish') {
		comment_form($args); 
	} ?>
	</section>
