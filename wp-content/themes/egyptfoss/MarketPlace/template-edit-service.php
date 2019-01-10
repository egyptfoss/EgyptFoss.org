<?php
/**
 * Template Name: Edit Service
 * @package egyptfoss
 */

if ( !is_user_logged_in() ) {
	$current_url = home_url( pll_current_language()."/login/?redirected=editservice&redirect_to=".get_current_lang_page_by_template("MarketPlace/template-edit-service.php")."?sid=".$_GET['sid'] );
	wp_redirect( home_url( get_current_lang_page_by_template('template-login.php')."?redirected=editservice&redirect_to={$current_url}" ) );
	exit;
}
$service_id = (is_numeric($_GET["sid"])) ? $_GET["sid"] : -1;

load_orm();
if (isset($_POST['action']) && $_POST['action'] == "edit_service" && isset($_POST['sid'])) {
	$nonce = $_REQUEST['_wpnonce'];
	if ( ! wp_verify_nonce( $nonce, 'edit-service' ) ) {
		wp_redirect( home_url( '/?status=403' ) );
		exit;
	}
	$service_id = (is_numeric($_POST["sid"])) ? $_POST["sid"] : -1;
	global $wpdb;
	$service = Post::where('post_type', '=', 'service')->where('ID', '=', $service_id)->first();
	if ($service == null) {
		echo __("No Service Found","egyptfoss");
		exit;
	}
	save_service_frontEnd();
}

if($service_id != -1) {
	global $wpdb;
	$service = Post::where('post_type', '=', 'service')->where('ID', '=', $service_id)->first();
	if ($service == null) {
		echo __("No Service Found","egyptfoss");
		exit;
	}
	if($service->post_author != get_current_user_id() || $service->post_status == 'archive') {
		include( get_query_template( '404' ) );
		exit;
	}

	$service_title = $service->post_title;
	$category = get_post_meta($service_id, "service_category", true);
	$theme = get_post_meta($service_id, "theme", true);
	$description = $service->post_content;
	$constraints = get_post_meta($service_id, "constraints", true);
	$conditions = get_post_meta($service_id, "conditions", true);
	$chosen_technologies = get_field('technology',$service_id);
	$chosen_interests = get_field('interest',$service_id);
} else {
	echo __("No Service Found","egyptfoss");
	exit;
}

if (!current_user_can('add_new_ef_posts')) {
	//wp_redirect( home_url( '?action=unauthorized' ) );
  wp_redirect(home_url('/?status=403'));
	exit;
}

get_header(); ?>

<header class="page-header">
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<h1 class="entry-title"><?php echo __("Edit","egyptfoss").' '.__("Service","egyptfoss"); ?> </h1>
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
						<form id="edit_service" name="edit_service" method="post" action="" enctype="multipart/form-data">
							<?php $messages = get_query_var("ef_service_messages");
							if(isset($messages['errors'])) { ?>
								<div class="alert alert-danger">
									<?php foreach($messages['errors'] as $error ) {
										echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
									} ?>
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

							<div class="form-group row">
								<div class="col-md-12"></div>
								<div class="col-md-12">
									<label for="service_title" class="label">
										<?php _e( 'Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
									</label>
									<input class="form-control" type="text" id="service_title" value="<?php echo $service_title; ?>" name="service_title" />
								</div>
							</div>
							<?php $service_singular_terms = array('theme' => "Theme", 'service_category' => 'Category');
							foreach($service_singular_terms as $term_slug=>$term_label) { ?>
								<div class="form-group row">
									<div class="col-md-12">
										<label for="post_type" class="label"><?php echo __($term_label, 'egyptfoss'); ?>
											<?php ($term_slug != "theme") ? _e('(required)', 'egyptfoss') : ""; ?>
										</label>
										<select class="form-control" name="<?php echo $term_slug?>" id="<?php echo $term_slug?>" style="width:100%;" >
											<optgroup>
												<option value=""><?php _e( 'Select', 'egyptfoss' ); ?></option>
												<?php $terms = get_terms($term_slug, array('hide_empty' => 0));
												foreach ($terms as $term) {
													$selected = (in_array($term->term_id, [$theme, $category])) ? "selected" : "";
													echo("<option value='" . $term->slug . "' $selected >");
													_e("$term->name", "egyptfoss");
													echo ("</option>");
												} ?>
											</optgroup>
										</select>
									</div>
								</div>
							<?php } ?>

              <div class="form-group row">
                <div class="col-md-12">
                  <label for="" class="label">
                    <?php echo __('Image', 'egyptfoss'); ?>
                  </label>
                  <div class="input-group image-preview">
                  	<?php $image_url =  get_the_post_thumbnail_url($service_id);
                  		$image_data = split('/', $image_url);
                  		$image_name = end($image_data);
                  	?>
                    <input type="text" class="form-control image-preview-filename service-image" disabled="disabled" placeholder="<?php echo $image_name ?>">
                    <span class="input-group-btn">
                      <button type="button" class="btn btn-default image-preview-clear" style="display:none;">
                        <span class="icons icon-cancel"></span> <?php _e('Clear', 'egyptfoss'); ?>
                      </button>
                      <div class="btn btn-default image-preview-input">
                        <span class="icons icon-folder-open"></span>
                        <span class="image-preview-input-title"><?php _e("Browse", "egyptfoss"); ?></span>
                        <input type="file" accept="image/png, image/jpeg, image/gif" name="service_image" id="service_image" title="<?php _e( 'please enter correct image type', 'egyptfoss' ); ?>"/>
                        <?php wp_nonce_field('service_image', 'service_image_nonce'); ?>
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
										<?php $current_text_area = $$textarea; ?>
										<textarea rows="10" class="form-control" name="service_<?php echo $textarea ?>" id="service_<?php echo $textarea ?>"><?php echo $current_text_area; ?></textarea>
									</div>
								</div>
							<?php } ?>

							<?php $service_multi_terms = array('technology'=>"Technologies",'interest'=>"Related interests");
							foreach($service_multi_terms as $term_slug => $term_label) { ?>
								<div class="form-group row" id="filter-<?php echo $term_slug ?>">
									<div class="col-md-12">
										<label for="<?php echo $term_slug ?>" class="label"><?php _e( $term_label, 'egyptfoss' ); ?></label>
										<select data-tags="true" class="add-product-tax form-control" id="<?php echo $term_slug ?>" name="<?php echo $term_slug ?>[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
											<optgroup>
												<?php $terms = get_terms( $term_slug, array( 'hide_empty' => 0 ) );
												if($term_slug == 'technology'){
													$chosen = $chosen_technologies;
												} else if($term_slug == 'interest'){
													$chosen = $chosen_interests;
												}
												foreach($terms as $term) {
													$selected = (in_array($term->term_id, $chosen)) ? "selected" : ""; ?>
													<option value="<?php echo $term->slug ?>" <?php echo $selected ?>>
														<?php _e("$term->name", "egyptfoss"); ?>
													</option>
												<?php } ?>
											</optgroup>
										</select>
										<span for="" class="error" id="<?php echo $term_slug."_error" ?>"> </span>
									</div>
								</div>
							<?php } ?>
							<div class="form-group row">
								<div class="col-md-12">
									<input type="submit" class="btn btn-primary rfloat" value="<?php echo __("Edit","egyptfoss"); ?>" tabindex="40" id="submit" name="submit" />
								</div>
							</div>
							<input type="hidden" name="sid" value="<?php echo $service_id; ?>" />
							<input type="hidden" name="action" value="edit_service" />
							<?php wp_nonce_field( 'edit-service' ); ?>
						</form>
					</div><!-- .entry-content -->
				</div><!-- #post-## -->
			</main><!-- #main -->
		</div><!-- #content -->
	</div><!-- #primary -->
</div>

<?php get_footer(); ?>
