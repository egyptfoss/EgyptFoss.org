<?php
/**
 * Template Name: Edit Event.
 *
 * @package egyptfoss
 */
$lang = get_locale();
if($lang == 'ar') {
  global $ar_events_types;
  global $ar_system_currencies;
  $types = $ar_events_types;
  asort($types);
  $currencies = $ar_system_currencies;
  asort($currencies);
} else {
  global $events_types;
  global $system_currencies;
  $types = $events_types;
  asort($types);
  $currencies = $system_currencies;
  asort($currencies);
}
if ( !is_user_logged_in()) {
  wp_redirect( home_url( '/wp-login.php?redirected=editevent' ) );
  exit;
} else if (!current_user_can('add_new_ef_posts')) {
  // Subscriber, Contributor users should be able to view (Edit Product)
  //wp_redirect( home_url( '?action=unauthorized' ) );
  wp_redirect(home_url('/?status=403'));
  exit;
} else {
  if( 'GET' == $_SERVER['REQUEST_METHOD'] && isset($_GET['pid']) && is_numeric($_GET['pid']) ) {
    $meta = get_event_data($_GET['pid']);
    if (get_post_status($_GET['pid']) == "publish") {
      // Subscriber, Contributor users should be able to view (Edit Product)
     wp_redirect(home_url());
      exit;
    }
  } else if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "edit_event" && isset($_POST['pid'])) {
    $nonce = $_REQUEST['_wpnonce'];
    if ( ! wp_verify_nonce( $nonce, 'edit-event' ) ) {
      wp_redirect( home_url( '/?status=403' ) );
      exit;
    }
    $eventSaved = '';
    $meta = array();
    $parameters = ['pid',
                   'event_title',
                   'description',
                   'event_type',
                   'start_datetime',
                   'end_datetime',
                   'venue',
                   'organizer',
                   'website',
                   'audience',
                   'objectives',
                   'prerequisites',
                   'currency',
                   'cost',
                   'functionality',
                   'theme',
                   'platform',
                   'interest',
                   'technology'];
    foreach ($parameters as $parameter) {
      if (in_array($parameter, array("functionality", "prerequisites", "audience", "objectives", "description"))) {
        $meta[$parameter] = isset($_POST[$parameter]) ? strip_js_tags($_POST[$parameter]) : '';
      } else {
        $meta[$parameter] = isset($_POST[$parameter]) ? $_POST[$parameter] : '';
      }
    }
    // validate data
    $errorMessage = validate_event_data($meta);

    if($errorMessage == '') {
      $edit_event_array = array(
        'ID' => $meta['pid'],
        'post_title'  => $meta['event_title'],
        'post_type'   => 'tribe_events',
        'post_content' => $meta['description'],
        'post_status' => 'pending'
      );
      $event_id = wp_update_post( $edit_event_array );
      update_event_meta($event_id, $meta);
      $eventSaved = __("Event",'egyptfoss').' '. $meta['event_title'] .' '.__("Updated Successfully",'egyptfoss');
    }
  }
?>

<?php get_header(); ?>
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
            <form id="edit_event" name="edit_event" method="post" action="" enctype="multipart/form-data">
              <?php wp_nonce_field( 'edit-event' ); ?>
              <div class="required">
                <?php
                global $eventSaved, $errorMessage;
                if ( $eventSaved ) { ?>
                  <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $eventSaved; ?></div>
                  <div class="clearfix"></div>
                <?php }
                if ( $errorMessage != '') { ?>
                  <div class="alert alert-danger"><i class="fa fa-warning"></i> <?php echo $errorMessage; ?></div>
                  <div class="clearfix"></div>
                <?php } ?>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="event_title" class="label">
                    <?php _e( 'Name', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                  </label>
                  <input class="form-control" type="text" id="event_title" value="<?php echo $meta['event_title'] ?>" name="event_title" />
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="description" class="label">
                    <?php _e( 'Description', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?>
                  </label>
                  <textarea class="form-control" name="description" id="description" rows="3" placeholder="<?php _e( 'Type event description here', 'egyptfoss' ); ?> "><?php echo strip_tags($meta['description']) ?></textarea>
                </div>
              </div>
              <div class="form-group row string event_type">
                <div class="col-md-12">
                  <label for="event_type" class="label"><?php _e( 'Type', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                  <select class="form-control" name="event_type" style="width:100%;" >
                    <optgroup>
                      <?php $default = ($meta['event_type'] == '') ? 'selected' : ''; ?>
                      <option value="" <?php echo $default; ?>  disabled selected hidden><?php _e( 'Select', 'egyptfoss' ); ?></option>
                      <?php
                        foreach ($types as $key => $type) {
                          $selected = ($key == $meta['event_type']) ? 'selected' : '';
                          echo("<option value='".$key."' $selected>");
                          _e("$type", "egyptfoss");
                          echo ("</option>");
                        }
                      ?>
                    </optgroup>
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="" class="label"><?php _e( 'Start Date & Time', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                  <input type="text" id="start_datetime" name="start_datetime" class="form-control date-picker" value="<?php echo $meta['start_datetime'] ?>">
                </div>
                  <div class="col-md-6">
                  <label for="" class="label"><?php _e( 'End Date & Time', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                  <input type="text" id="end_datetime" name="end_datetime" class="form-control date-picker" value="<?php echo $meta['end_datetime'] ?>">
                </div>
              </div>
              <div class="form-group row venue-group">
                <div class="col-md-12">
                  <?php
                  $venue_type = 'tribe_venue';
                  $venues = get_custom_posts($venue_type);
                  ?>
                  <label for="venue" class="label"><?php _e( 'Venue', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                  <select class="form-control" id="venue" name="venue" style="width:100%;height:38px;" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>">
                    <option value=""></option>
                    <optgroup>
                      <?php
                      foreach ($venues as $venue) {
                        $selected = ($venue->ID == $meta['venue']) ? 'selected' : '';
                        echo("<option value='".$venue->ID."' $selected>");
                        _e("$venue->post_title", "egyptfoss");
                        echo ("</option>");
                      }
                      ?>
                    </optgroup>
                  </select>
                  <div id="venue-error" style="color: firebrick;display:none;"><?php _e('Venue field required','egyptfoss') ?></div>
                </div>
              </div>
              <div class="form-group row organizer-group">
                <div class="col-md-12">
                  <?php
                  $organizer_type = 'tribe_organizer';
                  $organizers = get_custom_posts($organizer_type);
                  ?>
                  <label for="organizer" class="label"><?php _e( 'Organizer', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                  <select class="form-control" id="organizer" name="organizer" style="width:100%;" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>">
                   <option value="" ></option>
                    <optgroup>
                      <?php
                      foreach ($organizers as $organizer) {
                        $selected = ($organizer->ID == $meta['organizer']) ? 'selected' : '';
                        echo("<option value='".$organizer->ID."' $selected>");
                        _e("$organizer->post_title", "egyptfoss");
                        echo ("</option>");
                      }
                      ?>
                    </optgroup>
                  </select>
                  <div id="organizer-error" style="color: firebrick;display:none;"><?php _e('Organizer field required','egyptfoss') ?></div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="website" class="label"><?php _e( 'Website', 'egyptfoss' ); ?></label>
                  <input type="text" name="website" id="website" class="form-control" placeholder="https://" value="<?php echo $meta['website'] ?>">
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-6">
                  <label for="" class="label"><?php _e( 'Currency', 'egyptfoss' ); ?></label>
                  <select class="form-control" id="currency" name="currency">
                    <optgroup>
                      <?php
                      foreach ($currencies as $currency => $currency_name) {
                        $selected = ($currency == $meta['currency']) ? 'selected' : '';
                        echo("<option value='".$currency."' $selected>");
                        echo $currency_name;
                        echo ("</option>");
                      }
                      ?>
                    </optgroup>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="" class="label"><?php _e( 'Cost', 'egyptfoss' ); ?></label>
                  <input type="text" name="cost" value="<?php echo $meta['cost'] ?>" class="form-control">
                </div>
                <div class="col-md-12">
                  <span class="help-block"><i class="fa fa-info-circle"></i> <?php _e('Leave the cost field empty if the event is for FREE.','egyptfoss') ?></span>
                </div>
              </div>
              <!-- theme -->
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="theme" class="label"><?php _e( 'Theme', 'egyptfoss' ); ?></label>
                  <select class="form-control" id="theme" name="theme" style="width:100%; ">
                    <optgroup>
                      <?php $default = ($meta['theme'] == '') ? 'selected' : ''; ?>
                      <option value="" <?php echo $default; ?> disabled selected hidden><?php _e( 'Select', 'egyptfoss' ); ?></option>
                      <?php
                      $themes = get_terms( 'theme', array( 'hide_empty' => 0 ) );
                      foreach ($themes as $theme) {
                        $selected = ($theme->term_id == $meta['theme']) ? 'selected' : '';
                        echo("<option value='".$theme->term_id."' $selected>");
                        _e("$theme->name", "egyptfoss");
                        echo ("</option>");
                      }
                      ?>
                    </optgroup>
                  </select>
                </div>
              </div>
              <!-- technology -->
              <div class="form-group row string post_technology">
                <div class="col-md-12">
                  <label for="technology" class="label"><?php _e( 'Technology', 'egyptfoss' ); ?></label>
                  <select data-tags="true" class="add-product-tax form-control L-validate_taxonomy" id="technology" name="technology[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                    <optgroup>
                      <?php
                        $technologies = get_terms( 'technology', array( 'hide_empty' => 0 ) );
                        $event_technologies = is_array($meta['technology']) ? $meta['technology'] : array() ;
                        foreach ($technologies as $technology) {
                          if(in_array($technology->name, $event_technologies)) {
                            $selected = 'selected';
                            $key = array_search($technology->name, $event_technologies);
                            unset($event_technologies[$key]);
                          } else {
                            $selected = '';
                          }
                          echo("<option value='".$technology->name."' $selected>");
                          echo($technology->name);
                          echo ("</option>");
                        }
                        foreach ($event_technologies as $technology) {
                          echo("<option value='".$technology."' selected>");
                          echo($technology);
                          echo ("</option>");
                        }
                      ?>
                    </optgroup>
                  </select>
                  <span id="technology-error" class="error" style="display:none;"><?php _e('Invalid ict_technology.','egyptfoss') ?></span>
                </div>
              </div>
              <!-- platform -->
              <div class="form-group row string post_platform">
                <div class="col-md-12">
                  <label for="platform" class="label"><?php _e( 'Platform', 'egyptfoss' ); ?></label>
                  <select class="add-product-tax form-control L-validate_taxonomy" id="platform" name="platform[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                    <optgroup>
                      <?php
                        $platforms = get_terms( 'platform', array( 'hide_empty' => 0 ) );
                        $event_platforms = is_array($meta['platform']) ? $meta['platform'] : array() ;
                        foreach ($platforms as $platform) {
                          if(in_array($platform->name, $event_platforms)) {
                            $selected = 'selected';
                            $key = array_search($platform->name, $event_platforms);
                            unset($event_platforms[$key]);
                          } else {
                            $selected = '';
                          }
                          echo("<option value='".$platform->name."' $selected>");
                          echo($platform->name);
                          echo("</option>");
                        }
                        foreach ($event_platforms as $platform) {
                          echo("<option value='".$platform."' selected>");
                          echo($platform);
                          echo ("</option>");
                        }
                      ?>
                    </optgroup>
                  </select>
                  <span id="platform-error" class="error" style="display:none;"><?php _e('Invalid platform.','egyptfoss') ?></span>
                </div>
              </div>
              <!-- interest -->
              <div class="form-group row string post_interest">
                <div class="col-md-12">
                  <label for="interest" class="label"><?php _e( 'Interest', 'egyptfoss' ); ?></label>
                  <select data-tags="true" class="add-product-tax form-control L-validate_taxonomy" id="interest" name="interest[]" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" style="width:100%; visibility: hidden;" multiple="multiple">
                    <optgroup>
                      <?php
                        $interests = get_terms( 'interest', array( 'hide_empty' => 0 ) );
                        $event_interests = is_array($meta['interest']) ? $meta['interest'] : array() ;
                        foreach ($interests as $interest) {
                          if(in_array($interest->name, $event_interests)) {
                            $selected = 'selected';
                            $key = array_search($interest->name, $event_interests);
                            unset($event_interests[$key]);
                          } else {
                            $selected = '';
                          }
                          echo("<option value='".$interest->name."' $selected>");
                          echo($interest->name);
                          echo("</option>");
                        }
                        foreach ($event_interests as $interest) {
                          echo("<option value='".$interest."' selected>");
                          echo($interest);
                          echo ("</option>");
                        }
                      ?>
                    </optgroup>
                  </select>
                  <span id="interest-error" class="error" style="display:none;"><?php _e('Invalid interest.','egyptfoss') ?></span>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="objectives" class="label"><?php _e( 'Objectives', 'egyptfoss' ); ?></label>
                  <textarea class="form-control" name="objectives" id="objectives" rows="3" placeholder="<?php _e( 'Type the event objectives here', 'egyptfoss' ); ?>"><?php echo strip_tags($meta['objectives']) ?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="audience" class="label"><?php _e( 'Audience', 'egyptfoss' ); ?></label>
                  <textarea class="form-control" name="audience" id="audience" rows="3" placeholder="<?php _e( 'Type the event audience here', 'egyptfoss' ); ?>"><?php echo strip_tags($meta['audience']) ?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="prerequisites" class="label"><?php _e( 'Prerequisites', 'egyptfoss' ); ?></label>
                  <textarea class="form-control" name="prerequisites" id="prerequisites" rows="3" placeholder="<?php _e( 'Type the event prerequisites here', 'egyptfoss' ); ?>"><?php echo strip_tags($meta['prerequisites']) ?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <label for="functionality" class="label"><?php _e( 'Functionality', 'egyptfoss' ); ?></label>
                  <textarea class="form-control" name="functionality" id="functionality" rows="3" placeholder="<?php _e( 'Type event functionality here', 'egyptfoss' ); ?>"><?php echo strip_tags($meta['functionality']) ?></textarea>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12">
                  <input type="submit" class="btn btn-primary rfloat" value="<?php _e("Edit","egyptfoss") ?>" tabindex="40" id="submit" name="submit" />
                </div>
              </div>
              <input type="hidden" name="pid" value="<?php echo $meta['pid']; ?>" />
              <input type="hidden" name="action" value="edit_event" />
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</div>
<?php get_footer(); ?>
<?php } ?>
