<?php
/**
 * Template Name: Add Service
 * @package egyptfoss
 */

if (isset($_POST['action']) && $_POST['action'] == "add_service") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add-service' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  save_service_frontEnd();
}
get_header(); ?>

<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </div>
      <div class="col-md-5 hidden-xs">
        <?php if (function_exists('template_breadcrumbs')) template_breadcrumbs(); ?>
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
            <form id="add_service" name="add_service" method="post" action="" enctype="multipart/form-data">
              <?php
              $messages = get_query_var("ef_service_messages");
              if(isset($messages['errors'])) { ?>
                <div class="alert alert-danger"><?php
                foreach($messages['errors'] as $error ) {
                  echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
                }
                ?>
                </div>  
              <?php }
              if(isset($messages['success'])) { ?>
                <div class="alert alert-success"><?php
                  foreach($messages['success'] as $success ) {
                  echo "<i class='fa fa-check'></i> " . $success . "<br/>";
                } ?>
                </div>
              <?php }
              set_query_var("ef_service_messages", array()); ?>
              
              <?php include(locate_template('template-parts/add-form-intro.php')); ?>
              <div class="form-group row">
                <div class="col-md-12"></div>
              	<div class="col-md-12">
                  <label for="service_title" class="label">
                    <?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                  </label>
                  <input class="form-control" type="text" id="service_title" value="" name="service_title" />
              	</div>
              </div>
              <?php $service_singular_terms = array('theme' => "Theme", 'service_category' => 'Category'); 
                foreach($service_singular_terms as $term_slug => $term_label) { ?> 
                  <div class="form-group row">
                    <div class="col-md-12">
                    <label for="post_type" class="label">
                      <?php echo __($term_label, 'egyptfoss'); ?>
                      <?php ($term_slug != "theme") ? _e('(required)', 'egyptfoss') : ""; ?>
                    </label>
                      <select class="form-control" name="<?php echo $term_slug?>" id="<?php echo $term_slug?>" style="width:100%;" >
                        <optgroup>
                          <option value="" ><?php _e( 'Select', 'egyptfoss' ); ?></option>
                          <?php
                          $terms = get_terms($term_slug, array('hide_empty' => 0));
                          foreach ($terms as $term) {
                            echo("<option value='" . $term->slug . "' >");
                            _e("$term->name", "egyptfoss");
                            echo ("</option>");
                          }
                          ?>
                        </optgroup>
                      </select>
                    </div>
                  </div>
                <?php } ?> 

                <div class="form-group row">
                  <div class="col-md-12">
                    <label for="" class="label">
                      <?php echo __( 'Image', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                    </label>
                    <div class="input-group image-preview">
                      <input type="text" class="form-control image-preview-filename" disabled="disabled">
                      <span class="input-group-btn">
                        <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                          <span class="icons icon-cancel"></span> <?php _e('Clear','egyptfoss'); ?>
                        </button>
                        <div class="btn btn-default image-preview-input">
                          <span class="icons icon-folder-open"></span>
                          <span class="image-preview-input-title"><?php _e("Browse","egyptfoss"); ?></span>
                          <input type="file" accept="image/png, image/jpeg, image/gif" name="service_image" id="service_image" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                          <?php wp_nonce_field( 'service_image', 'service_image_nonce' ); ?>
                        </div>
                      </span>
                    </div>
                    <div id="service_image_validate"></div>
                  </div>
                </div>

                <?php $textareas = array('description'=>true, 'constraints'=>false, 'conditions'=>false);
                foreach($textareas as $textarea=>$isrequired) { ?>
                  <div class="form-group row">
                    <div class="col-md-12">
                      <label for="<?php echo $textarea ?>" class="label">
                        <?php echo __(ucfirst($textarea), 'egyptfoss'); ?> 
                        <?php ($isrequired) ? _e('(required)', 'egyptfoss') : ""; ?>
                      </label>
                      <textarea rows="10" class="form-control" name="service_<?php echo $textarea ?>" id="service_<?php echo $textarea ?>"></textarea>
                    </div>
                  </div>
                <?php } ?>
                
                <?php $service_multi_terms = array('technology'=>"Technologies", 'interest'=>"Related interests");
                foreach($service_multi_terms as $term_slug=>$term_label) { ?>
                  <div class="form-group row" id="filter-<?php echo $term_slug ?>">
                    <div class="col-md-12">
                      <label for="<?php echo $term_slug ?>" class="label">
                        <?php _e( $term_label, 'egyptfoss' ); ?>
                      </label>
                      <select data-tags="true" class="add-product-tax form-control" id="<?php echo $term_slug ?>" name="<?php echo $term_slug ?>[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                        <optgroup>
                          <?php $terms = get_terms( $term_slug, array( 'hide_empty' => 0 ) );
                            $chosen = split(",", $_GET[$term_slug]);
                            foreach($terms as $term) { 
                              $selected = (in_array($term->slug, $chosen)) ? "selected" : "";
                              ?><option value="<?php echo $term->slug ?>" <?php echo $selected ?>><?php _e("$term->name", "egyptfoss"); ?></option><?php
                            } ?>
                        </optgroup>
                      </select>
                      <span for="" class="error" id="<?php echo $term_slug."_error" ?>"> </span>
                    </div>
                  </div>  
                <?php } ?>
                <div class="form-group row">
                  <div class="col-md-12">
                    <input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Save","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
                  </div>
                </div>
                <input type="hidden" name="action" value="add_service" />
                <?php wp_nonce_field( 'add-service' ); ?>
            </form>
          </div><!-- .entry-content -->
        </div><!-- #post-## -->
      </main><!-- #main -->
    </div><!-- #content -->
  </div><!-- #primary -->
</div>

<?php get_footer(); ?>
