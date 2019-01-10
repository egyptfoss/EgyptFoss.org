<?php
/**
 * Template Name: Add User Location.
 *
 * @package egyptfoss
 */
if ( is_user_logged_in() && current_user_can('add_new_ef_posts') ) {

  global $locations_types;
  if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "add_location" && isset($_POST["submit"])) {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'add-location' ) ) {
      wp_redirect( home_url( '/?status=403' ) );
      exit;
    }
    $data = array();
    $parameters = ['lat', 'lng'];
    foreach ($parameters as $parameter) {
      $data[$parameter] = isset($_POST[$parameter]) ? $_POST[$parameter] : '';
    }
    $errorMessage = validate_location_data($data);

    if($errorMessage == '') {
      $location_data = array(
        'object_id' => get_current_user_id(),
        'object_type' => 'user',
        'lat' => $data['lat'],
        'lng' => $data['lng']
      );
      add_or_update_user_location($location_data);
      $locationSaved = __("Your location has been added successfully",'egyptfoss');
    }
  } else {
    $data = get_object_location_data(get_current_user_id(), 'user');
  }
}

global $wpdb;

$title_col = (pll_current_language() == 'ar')?'title_ar':'title';

$sql = " SELECT * FROM {$wpdb->prefix}efb_badges ORDER BY {$title_col}";

$badges = $wpdb->get_results( $sql );
?><script><?php
  $lang = get_locale();
  if($lang == 'ar') {
    global $ar_sub_types;
    global $ar_events_types;
    $sub_types = $ar_sub_types;
    $types = $ar_events_types;
  } else {
    global $en_sub_types;
    global $events_types;
    $sub_types = $en_sub_types;
    $types = $events_types;
  }
  echo 'var events_types = ', js_events_types($types), ';';
  echo 'var individuals_types = ', js_individuals_types($sub_types), ';';
  echo 'var entities_types = ', js_entities_types($sub_types), ';';
?></script>
<?php get_header(); ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
          <h1 class="entry-title"><?php _e(get_the_title(),"egyptfoss") ?></h1>
             <?php
            // this variable is used in related documents templates
            $section_slug = 'fossmap';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
        ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->
<?php if (!isset($_COOKIE['welcome-map']) || $_COOKIE['welcome-map'] != 'dismiss') { ?>
<div class="row map-help-container">
  <div class="col-md-12">
    <div class="well alert alert-dismissable text-center add-story-intro map-help fade in">
      <button type="button" class="close dismiss-help-welcome" cname="welcome-map" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
      </button>
        <div class="row">
          <div class="col-md-12">
            <h2 class="color-primary"><?php _e('Welcome to the Foss Map', 'egyptfoss'); ?></h2>
              <p>
                <?php if ( !is_user_logged_in() ) { ?>
                  <?php printf( __('Display your services and show yourself to the world!. Please <a href="%s">Register</a> or <a href="%s">Sign in</a> to pin your location on our map', 'egyptfoss'), get_register_page_current_lang(), get_current_lang_page_by_template('template-login.php') ); ?>
                <?php } else if ( current_user_can('add_new_ef_posts') ) { ?>
                  <?php _e('Display your services and show yourself to the world!. Click on "add your location" button <i class="fa fa-plus plus-icon"></i>, then pin your location on our map by dragging the pointer, finally click on "Save"', 'egyptfoss'); ?>
                <?php } ?>
              </p>
          </div>
        </div>

      </div>
    </div>
 </div>
 <?php } ?>
<div class="map-container">
        <?php if ( !is_user_logged_in() ) { ?>
          <a title="<?php _e( 'Add Your Location' , 'egyptfoss' ); ?>" data-toggle="tooltip" data-placement="right" id="add_location_link" class="add-location fab" href="<?php echo home_url( pll_current_language().'/login/?redirected=addlocation&redirect_to='.get_current_lang_page_by_template("page-add-user-location.php") ); ?>">
              <i class="fa fa-plus"></i>
          </a>
        <?php } else if ( current_user_can('add_new_ef_posts') ) { ?>
      		<a id="add_location_link" class="add-location fab" href="#" title="<?php _e( 'Add Your Location' , 'egyptfoss' ); ?>" data-toggle="tooltip" data-placement="right">
            <i class="fa fa-plus"></i>
          </a>
        <?php } else { ?>
          <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
          <a class="btn add-location fab disabled" href="javascript:void(0)" data-toggle="tooltip" data-placement="right" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
            <i class="fa fa-plus"></i>
          </a>
        <?php } ?>


	<div class="map-options-bar">
    <div class="container filter-map">
    	<div class="col-md-2">
      	<select name="type" id="type" class="filter-location location-type form-control">
      		<optgroup>
              <?php
                foreach ($locations_types as $type) {
                  echo("<option value='".$type."'>");
                    echo _nx("$type",  pluralize($type),2 ,"definite","egyptfoss");
                  echo ("</option>");
                }
              ?>
      		</optgroup>
      	</select>
    	</div>
    	<div class="col-md-2" id="badges-container" style="display: none;">
    		<select name="badges" id="badges" class="form-control filter-location">
      		<option value="" selected><?php _e("Badges", "egyptfoss") ?></option>
          <?php foreach( $badges as $badge ): ?>
            <option value="<?php echo $badge->id; ?>"><?php echo $badge->{$title_col}; ?></option>
          <?php endforeach; ?>
      	</select>
    	</div>
    	<div class="col-md-2">
    		<select name="sub_type" id="sub_type" class="filter-location location-sub-type form-control">
      		<option value="" selected><?php _e("Sub Type", "egyptfoss") ?></option>
      	</select>
    	</div>
      <?php
      $term_taxs = array("theme", "technology", "interest");
      foreach ($term_taxs as $term_tax) {
        ?>
        <div class="col-md-2">
          <select class="custom-select2 filter-location location-<?php echo $term_tax; ?> form-control filter-class topFilters" data-taxonomy="<?php echo $term_tax ?>" style="width:100%;">
            <option value=""><?php echo __($term_tax,'egyptfoss') ?></option>
            <?php
            $terms_data = get_terms($term_tax, array('hide_empty' => 0));
            foreach ($terms_data as $term_data) {
              ?>
              <option  data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_taxonomy_id ?>"<?php if ($getParams[$term_tax] == $term_data->slug) { ?>selected="selected"<?php } ?>>
                <?php echo $term_data->name; ?>
              </option>
            <?php } ?>
          </select>
        </div>
      <?php } ?>
    </div>
    <a class="close-filter-btn"><i class="fa fa-angle-up "></i></a>
	</div>
	<div id='map_canvas'></div>
  <?php if ( is_user_logged_in() && current_user_can('add_new_ef_posts') ) { ?>
  <form id="add_location" name="add_location" method="post" action="" enctype="multipart/form-data" style="display:none;">
    <?php wp_nonce_field( 'add-location' ); ?>
    <input type="hidden" class="form-control" id="lat" value="<?php echo $data['lat'] ?>" name="lat" />
    <input type="hidden" class="form-control" id="lng" value="<?php echo $data['lng'] ?>" name="lng" />
    <div class="confirm-location-container">
  		<div class="confirm-dialog">
  			<button id="cancel_add_location" class="btn btn-light"><?php _e("Cancel", "egyptfoss") ?></button>
  			<button class="btn btn-primary" id="submit" name="submit"><?php _e("Save", "egyptfoss") ?></button>
  		</div>
  	</div>
    <input type="hidden" name="action" value="add_location" />
  </form>
  <?php } ?>
</div>
<?php get_footer(); ?>
