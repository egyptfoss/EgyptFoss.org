<?php
/**
 * Template Name: Add Feedback
 *
 * @package egyptfoss
 */

if (!empty($_POST['action']) && $_POST['action'] == "add_feedback") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-feedback' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  ef_add_feedback_front_end();
  $ef_feedback_messages = get_query_var("ef_feedback_messages");
}
get_header(); 
 ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php if( isset( $ef_feedback_messages[ 'success' ] ) ): ?>
            <h1 class="entry-title"><?php _e( 'Thanks for your feedback', 'egyptfoss' ); ?></h1>
        <?php else: ?>
            <?php echo ucwords(the_title( '<h1 class="entry-title">', '</h1>', false )); ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
<div class="row">
  <div id="primary" class="content-area col-md-12">
  <main id="main" class="site-main" role="main">
    <div id="content" role="main">
      <div class="new-coupon-form">
        <form id="add_feedback" name="add_feedback" method="post" action="" enctype="multipart/form-data">
          <div class="required">
            <?php
            $ef_feedback_messages = get_query_var("ef_feedback_messages");
            $feedback_title = $feedback_description = "";
            if(isset($ef_feedback_messages['errors'])) {
              $feedback_title = (isset($_POST['feedback_title']))?$_POST['feedback_title']:"";
              $feedback_description = (isset($_POST['feedback_description']))?$_POST['feedback_description']:"";
              ?>
              <div class="alert alert-danger"><?php
                foreach($ef_feedback_messages['errors'] as $error ) {
                  echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
                }
                ?>
              </div>
            <?php }
            if(isset($ef_feedback_messages['success'])): ?>
                <div class="succes-message text-center"><?php
                    foreach($ef_feedback_messages['success'] as $success ) {?>

                    <img src="<?php echo get_template_directory_uri(); ?>/img/sent_icon.svg" alt="">
                    <h3><?php echo $success ?></h3>

                 <?php } ?>
                </div>

            <?php endif;
            set_query_var("ef_feedback_messages", array()); ?>
          </div>
            <?php if( !isset( $ef_feedback_messages['success'] ) ): ?>
                <div class="form-group row">
                      <div class="col-md-12">
                    <label for="feedback_title" class="label">
                      <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                    <input class="form-control" type="text" id="feedback_title" value="<?php echo $feedback_title ?>" name="feedback_title" />
                    <div id="feedback_title_validate"></div>
                      </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-12">
                      <label for="post_sections" class="label"><?php _e( 'Related to section', 'egyptfoss' ); ?></label>
                      <?php 
                          global $ef_sections;


                          foreach( $ef_sections as $key => $section ) {
                              $ef_sections[ $key ] = __( $section, 'egyptfoss' );
                          }

                          asort($ef_sections);
                          
                          if( !empty( $_GET['section'] ) ) {
                            $s_section = $_GET['section'];
                          }
                      ?>
                      <select class="form-control" id="post_sections" name="post_sections" style="width:100%; ">
                          <optgroup>
                                <option value="general"><?php _e( "General", "egyptfoss") ?></option>
                                <?php foreach ($ef_sections as $key =>  $section) : ?>
                                    <option value="<?php echo $key ?>" <?php selected( $key, $s_section ); ?>><?php _e( $section, "egyptfoss") ?></option>
                                <?php endforeach; ?>
                          </optgroup>
                      </select>
                    <div id="post_sections_validate"></div>
                  </div>
                </div>
                <div class="form-group row">
                  <div class="col-md-12">
                    <label for="description" class="label">
                      <?php _e('Content', 'egyptfoss'); ?> <?php _e('(required)', 'egyptfoss'); ?>
                    </label>
                    <textarea rows="15" class="form-control" name="feedback_description" id="feedback_description"><?php echo $feedback_description ?></textarea>
                    <div id="feedback_description_validate"></div>
                  </div>
                </div>     
                <div class="form-group row">
                  <div class="col-md-12">
                    <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Save","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
                  </div>
                </div>
                <?php wp_nonce_field( 'add-feedback' ); ?>
                <input type="hidden" name="action" value="add_feedback" />
          <?php endif; ?>
        </form>
      </div><!-- .new-coupon-form -->
    </div><!-- #content -->
  </main><!-- #main -->
  </div><!-- #primary -->
</div>
</div>

<?php get_footer(); ?>
