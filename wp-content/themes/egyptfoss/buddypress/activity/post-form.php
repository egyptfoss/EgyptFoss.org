<?php
/**
 * BuddyPress - Activity Post Form
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<form action="<?php bp_activity_post_form_action(); ?>" method="post" id="whats-new-form" name="whats-new-form" role="complementary">
    <?php
    /**
     * Fires before the activity post form.
     *
     * @since 1.2.0
     */
    do_action( 'bp_before_activity_post_form' ); ?>

    <div id="whats-new-avatar">
        <a href="<?php echo bp_loggedin_user_domain(); ?>">
            <?php bp_loggedin_user_avatar( 'width=' . bp_core_avatar_thumb_width() . '&height=' . bp_core_avatar_thumb_height() ); ?>
        </a>
    </div>

    <div id="whats-new-content">
      <div id="whats-new-textarea">
        <label for="whats-new" class="bp-screen-reader-text"><?php _e( "Post what\'s new", 'egyptfoss' ); ?></label>
        <textarea class="bp-suggestions" class="form-control" name="whats-new" id="whats-new" cols="50" rows="10" placeholder="<?php if ( bp_is_group() )
          printf( __( "What's new in %s, %s?", 'egyptfoss' ), bp_get_group_name(), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );
          else
          printf( __( "What's new, %s?", 'buddypress' ), bp_get_user_firstname( bp_get_loggedin_user_fullname() ) );
          ?>"
          <?php if ( bp_is_group() ) : ?>data-suggestions-group-id="<?php echo esc_attr( (int) bp_get_current_group_id() ); ?>" <?php endif; ?>
            ><?php if ( isset( $_GET['r'] ) ) : ?>@<?php echo esc_textarea( $_GET['r'] ); ?> <?php endif; ?></textarea>

        <select class="add-product-tax form-control L-validate_taxonomy" id="post_interest" name="post_interest[]" data-placeholder="<?php _e( 'Related interests', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple" data-tags="true">
          <optgroup>
          <?php
          $post_interest = get_terms( 'interest', array( 'hide_empty' => 0 ) );
          foreach ($post_interest as $interest) {
            echo("<option value='".$interest->term_id."' $selected >");
            _e("$interest->name", "egyptfoss");
            echo ("</option>");
          }
          ?>
          </optgroup>
        </select>
      </div>

        <div id="whats-new-options">
          <div id="whats-new-submit">
             <?php $user_can_post_status = true;
              if(!current_user_can('add_new_ef_posts'))
              {
                $user_can_post_status = false;
              }
             ?>
              <?php if($user_can_post_status) { ?>
                <input type="submit" name="aw-whats-new-submit" class="btn btn-primary" id="aw-whats-new-submit" value="<?php esc_attr_e( 'Post Update', 'egyptfoss' ); ?>" />
              <?php } else { ?>
                <a class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"> <?php esc_attr_e( 'Post Update', 'egyptfoss' ); ?></a>
              <?php } ?>
          </div>

          <?php if ( bp_is_active( 'groups' ) && !bp_is_my_profile() && !bp_is_group() ) : ?>
            <div id="whats-new-post-in-box">
              <?php _e( 'Post in', 'buddypress' ); ?>:
              <label for="whats-new-post-in" class="bp-screen-reader-text"><?php _e( 'Post in', 'buddypress' ); ?></label>
              <select id="whats-new-post-in" name="whats-new-post-in" class="form-control">
                <option selected="selected" value="0"><?php _e( 'My Profile', 'buddypress' ); ?></option>
                <?php if ( bp_has_groups( 'user_id=' . bp_loggedin_user_id() . '&type=alphabetical&max=100&per_page=100&populate_extras=0&update_meta_cache=0' ) ) :
                  while ( bp_groups() ) : bp_the_group(); ?>
                    <option value="<?php bp_group_id(); ?>"><?php bp_group_name(); ?></option>
                  <?php endwhile;
                endif; ?>
              </select>
            </div>
            <input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
            <?php elseif ( bp_is_group_activity() ) : ?>
                <input type="hidden" id="whats-new-post-object" name="whats-new-post-object" value="groups" />
                <input type="hidden" id="whats-new-post-in" name="whats-new-post-in" value="<?php bp_group_id(); ?>" />
            <?php endif; ?>

            <?php
            /**
             * Fires at the end of the activity post form markup.
             *
             * @since 1.2.0
             */
            do_action( 'bp_activity_post_form_options' ); ?>

        </div><!-- #whats-new-options -->
    </div><!-- #whats-new-content -->

    <?php wp_nonce_field( 'post_update', '_wpnonce_post_update' ); ?>
    <?php
    /**
     * Fires after the activity post form.
     *
     * @since 1.2.0
     */
    do_action( 'bp_after_activity_post_form' ); ?>

</form><!-- #whats-new-form -->
<script>

    
var $ =jQuery.noConflict();
$(document).ready(function(){
$('#aw-whats-new-submit').on('click', function() {
       $("#post_interest").select2( 'data' ).forEach( function( obj ) {
          if( obj.text === obj.id ) {
            $("#post_interest optgroup").append( '<option value="' + obj.text + '">' + obj.text + '</option>' );
          }
       });
       $("#post_interest").select2('val', '');
       $("#post_interest").select2({
          multiple: true,
          language: {
            noResults: function () {
              return jQuery.validator.messages.select2_no_results;
            }
          }
        });
    });
});
</script>