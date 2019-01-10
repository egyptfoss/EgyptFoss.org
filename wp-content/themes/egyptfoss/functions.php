<?php
/**
 * egyptfoss functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package egyptfoss
 */

function load_orm() {
  include_once(ABSPATH .'vendor/autoload.php');
  include_once(ABSPATH .'wp-orm-settings.php');
}
require_once('inc/ef_users_roles.php');
//require_once('inc/breadcrumb.php');
require_once('inc/user_taxonomies.php');
require_once('inc/admin_users_functions.php');
require_once('inc/foss_map.php');
require_once('inc/add-event.php');
require_once('inc/ef_custom_error_pages.php');
require_once('languages/custom-admin.php');
require_once('inc/archived-post-status.php');
include( ABSPATH . 'system_data.php' );

remove_action('wp_head', 'wp_generator');


// Set Timezone
$timeZone = get_option('timezone_string');
date_default_timezone_set($timeZone);

if ( ! function_exists( 'egyptfoss_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function egyptfoss_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on egyptfoss, use a find and replace
	 * to change 'egyptfoss' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'egyptfoss', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'egyptfoss'),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'egyptfoss_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif;
add_action( 'after_setup_theme', 'egyptfoss_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * Priority 0 to make it available to lower priority callbacks.
 * @global int $content_width
 */
function egyptfoss_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'egyptfoss_content_width', 640 );
}
add_action( 'after_setup_theme', 'egyptfoss_content_width', 0 );

/**
 * Register widget area.
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function egyptfoss_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'egyptfoss' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'egyptfoss_widgets_init' );

// Enqueue scripts and styles.
function egyptfoss_scripts() {
  wp_reset_query();
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css' );
	wp_enqueue_style( 'sidr-css', get_template_directory_uri() . '/css/sidr.css' );
	wp_enqueue_style( 'font-icons', get_template_directory_uri() . '/css/font-awesome.min.css' );
	wp_enqueue_style( 'owl-css', get_template_directory_uri() . '/css/owl.carousel.css' );
	wp_enqueue_style( 'select2-css', get_template_directory_uri() . '/css/select2.min.css' );
	wp_enqueue_style( 'custom-scroll-css', get_template_directory_uri() . '/css/nanoscroller.css' );
	wp_enqueue_style( 'bs-datepicker', get_template_directory_uri() . '/css/bootstrap-datetimepicker.min.css' );
	wp_enqueue_style( 'magnificpopup', get_template_directory_uri() . '/css/magnific-popup.css' );
	wp_enqueue_style( 'egyptfoss-style', get_stylesheet_uri() );
	wp_enqueue_script('jquery');
  wp_enqueue_script( 'foss-functions-js', get_template_directory_uri() . '/js/foss-functions.js',array('jquery'), '', true);
  wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js' );
	wp_enqueue_script( 'sticky', get_template_directory_uri() . '/js/jquery.sticky.js' );
    wp_enqueue_script( 'star-rating', get_template_directory_uri() . '/js/star-rating.js' );
	wp_enqueue_script( 'owl-carousel-js', get_template_directory_uri() . '/js/owl.carousel.min.js' );
  wp_enqueue_script( 'nanoscroller-js', get_template_directory_uri() . '/js/nanoscroller.js' );
	wp_enqueue_script( 'select2', get_template_directory_uri() . '/js/select2.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'trunk8', get_template_directory_uri() . '/js/trunk8.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'moment-js', get_template_directory_uri() . '/js/moment.js', array('jquery'), '', true);
  wp_enqueue_script( 'locale-moments-js', get_template_directory_uri() . '/js/localization/locale-moment.js', array('jquery','moment-js'), '', true);
	wp_enqueue_script( 'bs-datepicker', get_template_directory_uri() . '/js/bootstrap-datetimepicker.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'sidr', get_template_directory_uri() . '/js/sidr.min.js', array('jquery'), '', true);
  wp_enqueue_script( 'jquery_validate_min', get_stylesheet_directory_uri() . '/js/jquery.validate.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'additional_methods', get_stylesheet_directory_uri() . '/js/additional-methods.min.js', array('jquery'), '', true);
  wp_enqueue_script( 'register-jquery-validate', get_template_directory_uri() . '/js/register-jquery-validation.js', array('jquery'), '', true);
  wp_enqueue_script( 'login-jquery-validate', get_template_directory_uri() . '/js/login-jquery-validation.js', array('jquery'), '', true);
	wp_enqueue_script( 'magnific-js', get_template_directory_uri() . '/js/magnific-popup.min.js', array('jquery'), '', true);
	wp_enqueue_script( 'readmore', get_template_directory_uri() . '/js/readmore.min.js', array('jquery'), '', true);

  if ( is_page_template('page-add-user-location.php') || is_page_template('page-add-event.php') || is_singular('tribe_events') ) {
  	global $google_maps_key;
    wp_register_script( 'ef-google-maps-api', 'https://maps.googleapis.com/maps/api/js?key='. $google_maps_key, null, null, true );
  }
  if ( is_page_template('page-add-event.php') ) {
    wp_enqueue_script( 'foss-map-js', get_template_directory_uri() . '/js/foss-map.js', array( 'jquery', 'ef-google-maps-api' ) );
    wp_enqueue_script( 'events-js', get_template_directory_uri() . '/js/events.js', array( 'jquery' ) );
  } else if ( is_page_template('template-edit-event.php') ) {
    wp_enqueue_script( 'events-js', get_template_directory_uri() . '/js/events.js', array( 'jquery' ) );
  }
  if ( is_page_template('page-add-user-location.php') ) {
    wp_enqueue_script( 'user-location-js', get_template_directory_uri() . '/js/user_location.js', array( 'jquery', 'ef-google-maps-api' ) );
  }
  if ( is_singular('request_center') || is_page_template('template-request-thread.php') || is_singular('service') || is_page_template('template-service-thread.php') ) {
  	wp_enqueue_script('handle-threads', get_stylesheet_directory_uri() . '/js/handling-threads.js', array('jquery'), '', true);
  }

	if ( is_singular('service') || is_page_template('template-service-thread.php') ) {
  	wp_enqueue_script('handle-reviews', get_stylesheet_directory_uri() . '/js/handling-reviews.js', array('jquery'), '', true);
  }

  if (is_singular('tribe_events') ) {
    wp_enqueue_script( 'single_event_map', get_template_directory_uri() . '/js/single_event_map.js', array( 'jquery', 'ef-google-maps-api' ), null, true  );
  }

  if ( is_page_template('page-add-product.php') || is_page_template('template-edit-product.php')
          || is_page_template('template-add-open-dataset.php') || is_page_template('template-add-resources-open-dataset.php') || is_page_template('template-add-success-story.php') || is_page_template('template-manage-news.php') ) {
    wp_enqueue_style( 'jquery.filer-dragdropbox', get_template_directory_uri() . '/css/jquery.filer-dragdropbox-theme.css' );
    wp_enqueue_style( 'jquery.filer', get_template_directory_uri() . '/css/jquery.filer.css' );
    wp_enqueue_script( 'jquery.filer.js', get_template_directory_uri() . '/js/jquery.filer.js', array( 'jquery' ) );
  }
  wp_enqueue_script( 'profile-jquery-validate', get_template_directory_uri() . '/js/profile-jquery-validation.js', array('jquery'), '', true);
  wp_enqueue_script( 'jquery-validate-messages', get_template_directory_uri() . '/js/localization/messages_'.  preg_replace('/[_]+\w+($)/', '', get_locale()).'.js', array('jquery'), '', true);
  wp_enqueue_script( 'custom-js', get_template_directory_uri() . '/js/custom.js');
	wp_enqueue_script( 'egyptfoss-reset-password-verify', get_template_directory_uri() . '/js/reset-password-verify.js', array('jquery'), '', true);
	wp_enqueue_script( 'javascript-cookies', get_template_directory_uri() . '/js/cookies.js', array(), '', true);

  if(is_singular('quiz'))
  {
    wp_enqueue_script( 'ef_single_quiz', get_template_directory_uri() . '/js/quizes/ef_single_quiz.js', array( 'jquery' ), null, true  );
  }

	$translation_array = array(
	'is_front' => (is_front_page())?1:0,
  'current_lang' => pll_current_language()
);
wp_localize_script( 'custom-js', 'efLocalizedVars', $translation_array );

// Enqueued script with localized data.
wp_enqueue_script( 'some_handle' );

	wp_enqueue_script( 'egyptfoss-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'egyptfoss-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'egyptfoss_scripts' );

// Implement the Custom Header feature.
require get_template_directory() . '/inc/custom-header.php';

// Custom template tags for this theme.
require get_template_directory() . '/inc/template-tags.php';

// Custom functions that act independently of the theme templates.
require get_template_directory() . '/inc/extras.php';

// Customizer additions.
require get_template_directory() . '/inc/customizer.php';

// Load Jetpack compatibility file.
require get_template_directory() . '/inc/jetpack.php';

// including custom-post-types

  $cpt_files = glob(dirname(__FILE__).'/inc/custom-post-types/*', GLOB_BRACE);

  foreach($cpt_files as $cpt_file) {
    $file_name = basename($cpt_file);
    include get_template_directory() . "/inc/custom-post-types/{$file_name}";
  }

// including advanced custom fields
include get_template_directory() . '/inc/advanced-custom-fields/add_product_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_event_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_news_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/is_featured_product_field.php';
include get_template_directory() . '/inc/advanced-custom-fields/is_featured_news_field.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_success_story_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_open_dataset_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/request_center_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_feedback_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/expert_thought_fields.php';
include get_template_directory() . '/inc/advanced-custom-fields/add_partner_fields.php';

include get_template_directory() . '/inc/advanced-custom-fields/add_quiz_fields.php';

include get_template_directory() . '/inc/advanced-custom-fields/add_services_fields.php';

// including admin post validations
include get_template_directory() . '/inc/admin_validations.php';
include get_template_directory() . '/inc/admin_event_validations.php';

include get_template_directory() . '/inc/admin_api_keys.php';

include get_template_directory() . '/inc/handling-homepage.php';

include get_template_directory() . '/inc/handling-products.php';
include get_template_directory() . '/inc/handling-news.php';

include get_template_directory() . '/inc/handling-feedback.php';
include get_template_directory() . '/inc/handling-success-story.php';

include get_template_directory() . '/inc/handling-open-dataset.php';

include get_template_directory() . '/inc/optimizing-products-lang-search.php';

include get_template_directory() . '/inc/term-custom-fields.php';

include get_template_directory() . '/inc/wsl-overrides.php';

include get_template_directory() . '/inc/handling-templates.php';

include get_template_directory() . '/inc/list-user-events.php';

include get_template_directory() . '/inc/list-user-news.php';

include get_template_directory() . '/inc/list-user-fosspedia.php';

include get_template_directory() . '/inc/list-user-success-stories.php';

include get_template_directory() . '/inc/list-user-open-datasets.php';

include get_template_directory() . '/inc/list-user-request-center.php';

include get_template_directory() . '/inc/list-user-services.php';

include get_template_directory() . '/inc/list-user-expert-thoughts.php';

include get_template_directory() . '/inc/list-services.php';

include get_template_directory() . '/inc/list-quizzes.php';

include get_template_directory() . '/inc/list-user-badges.php';

include get_template_directory() . '/inc/website-links-override.php';

include get_template_directory() . '/inc/options-top-ten-products.php';

include get_template_directory() . '/inc/handling-title-header.php';

include get_template_directory() . '/inc/handling-request-center.php';

include get_template_directory() . '/inc/handling-threads.php';

include get_template_directory() . '/inc/handling-collaboration-center.php';

include get_template_directory() . '/inc/handling-quiz.php';

include get_template_directory() . '/inc/handling-expert-thought.php';

include get_template_directory() . '/inc/handling-market-place.php';

include get_template_directory() . '/inc/efb_badges.php';

include get_template_directory() . '/inc/list-user-documents.php';

include get_template_directory() . '/inc/bulk_download.php';

include get_template_directory() . '/inc/widgets/informative-widget.php';

include get_template_directory() . '/inc/widgets/services-widget.php';

// ---- Function Registeration ---- //
function save_extra_data($user_id){
	include( ABSPATH . 'system_data.php' );
	$meta = array();

	$meta['type'] = (isset($_POST['type'])) ? $_POST['type'] : 'Individual';
  $meta['sub_type'] = (array_key_exists($_POST['sub_type'], $account_sub_types)) ? $_POST['sub_type'] : 'user';

  $meta['contact_phone'] = (isset($_POST['signup_telephone_number'])) ? $_POST['signup_telephone_number'] : '';

  if( $meta['contact_phone'] == '' ) {
    $meta['contact_phone'] = (isset($_POST['telephone_number'])) ? $_POST['telephone_number'] : '';
  }

  if(!isset($_REQUEST['provider'])) {
  	$meta['registeredNormally'] = 1;
  }
	$meta_key = 'registration_data';
	$meta_value = serialize($meta);
	update_user_meta($user_id, $meta_key, $meta_value);
	update_user_meta($user_id, 'type', $meta['type']);

  // expert thoughts
  if(array_key_exists('is_expert', $_POST)) {
    $is_expert = TRUE;
    update_user_meta($user_id, 'is_expert', 1);
    sendMarkedExpertEmail($user_id);
  }else
  {
    $is_expert = FALSE;
    update_user_meta($user_id, 'is_expert', 0);
  }
  load_orm();
  // expert badge management
  $badge = new Badge( $user_id );
  $badge->efb_manage_expert_badge( $is_expert );

  // send emails to expert with earned badges;
  foreach( $badge->badges_earned as $badge ) {
    global $wpdb;
    $query = "SELECT * FROM {$wpdb->base_prefix}efb_badges WHERE name = '{$badge->name}'";
    $result = $wpdb->get_results($query, ARRAY_A);

    if( class_exists( 'EFBBadges' ) && !empty( $result ) ) {
      sendNewBadgeAchiever( $user_id, new EFBBadges( $result[0] ) );
    }
  }

  // save users content to marmotta
  saveUserContent( $user_id, $_POST['user_login'], '', $_POST['type'] );
}
add_action('user_register', 'save_extra_data');

function signup_pre_validate() {
  $errors = array();
  $admin_side = (isset($_REQUEST['wp_http_referer']) && ($_REQUEST['wp_http_referer'] == '/wp-admin/users.php')) ? true : false;
  if ($admin_side) {
    return true;
  } else if (!(isset($_POST['terms']) && ($_POST['terms'] == "checked"))) {
    bp_core_add_message( __( "You have to agree the EgyptFOSS Terms of services.", 'egyptfoss' ), 'error' );
    array_push($errors, 'terms');
  } else if(!isset($_POST['type']) || empty($_POST['type'])) {
    bp_core_add_message( __( "Please select your Account Type", 'egyptfoss' ), 'error' );
    array_push($errors, 'type');
  } else if(!isset($_POST['sub_type']) || empty($_POST['sub_type'])) {
    bp_core_add_message( __( "Please select your Account Subtype", 'egyptfoss' ), 'error' );
    array_push($errors, 'sub_type');
  }
  if ( !empty( $errors ) ) {
    return false;
  }
}
add_action ('bp_signup_pre_validate', 'signup_pre_validate', 20);

//  // -- Function commented because there is no need for it right now --- //
// function force_my_own_registration_page() {
// 	// by Eslam, 2015-12-15
// 	$turnon = 1; // 1 = yes, 0 = no
// 	$currenturl = strtolower($_SERVER["HTTP_HOST"]) . strtolower($_SERVER["REQUEST_URI"]);
// 	$findme = 'wp-login.php?action=register';
// 	$pos = strpos($currenturl, $findme);
// 	if ( $turnon == 1 && $pos !== false) {
// 		wp_safe_redirect( '/?p=8' , 301 );  // 8 is the register page id
// 	}
// }
// add_action('init', 'force_my_own_registration_page');

// added to fix media wiki logout issue (when logging out from wordpress doesn't logout from media wiki)

add_action('wp_logout', 'mw_logout');
function mw_logout() {
        $wikiDatabaseName = DB_NAME;
        // Goes through all cookies, if it finds a cookie starting with your database name it will remove it.
        foreach($_COOKIE as $cookieKey => $cookieValue) {
                if(strpos($cookieKey,$wikiDatabaseName) === 0 || $cookieKey === "PHPSESSID") {
                        // remove the cookie
                        setcookie($cookieKey, null, -1,'/');
                        unset($_COOKIE[$cookieKey]);
                }
        }
}

// temporary solution for cookie issue in integrating mediawiki and wordpress over subdomains
/*
add_action('init', 'set_cookie');
function set_cookie() {
    if($_SERVER["SERVER_NAME"] == "egyptfoss.com") {
        foreach($_COOKIE as $cookieKey => $cookieValue) {
            if(strpos($cookieKey,"wordpress_logged_in") === 0) {
                setcookie ($cookieKey, $cookieValue, 0 ,"/" , ".egyptfoss.com" );
            }
        }
    }
}
*/

// overriding wp social login behave and forcing it to link account if exists
add_filter('wsl_hook_process_login_delegate_wp_insert_user',"custom_wsl_create_user",10,3);
function custom_wsl_create_user($userdata, $provider, $hybridauth_user_profile )
{
  $is_email_exist = get_user_by("email", $hybridauth_user_profile->email);
  if($is_email_exist)
  {
    return $is_email_exist->ID;
  }  else {
     $is_user_exist = get_user_by("login", $userdata["user_login"]);
     if($is_user_exist)
     {
       return $is_user_exist->ID;
     }
  }
  return wp_insert_user($userdata);
}

add_filter('wsl_hook_process_login_after_wp_insert_user','ef_social_login_wp_insert_user',10,3);
function ef_social_login_wp_insert_user($user_id, $provider, $hybridauth_user_profile)
{
  $get_user_meta = get_user_meta($user_id, "registration_data", true);
  $user_meta = unserialize($get_user_meta);
  //Not social loggedIn user
  if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
    return;
  }

  //not verified email
  if($hybridauth_user_profile->email == null)
  {
    global $wpdb;

    $user = get_user_by('ID', $user_id);
    $activation_key = wp_hash( $user_id );

    // add user to signup db
    $args = array(
			'user_login'     => $user->user_login,
			'user_email'     => $user->user_email,
			'activation_key' => $activation_key,
			'meta'           => array(),
		);

		BP_Signup::add( $args );

    // set user as subscriber
    $role = array('subscriber' => 1);
    update_user_meta($user_id, 'wpRuvF8_capabilities', serialize($role));
    update_user_meta($user_id, 'wpRuvF8_user_level', 0);

    // set user status to 2
    $wpdb->update($wpdb->prefix.'users',array( 'user_status' => '2' ),array( 'ID' => $user_id ));

    // set verification user meta
    bp_update_user_meta( $user_id, 'activation_key', $activation_key );

    //send activation email
    ef_resend_activation($activation_key, $user);

    // save users content to marmotta
    saveUserContent( $user_id, $user->display_name, $user_meta['functionality'], 'Individual' );
  }
}

add_filter('bp_loggedin_activate_page_redirect_to','ef_bp_loggedin_activate_page_redirect_to',10);
function ef_bp_loggedin_activate_page_redirect_to($redirect_url)
{
  //log user out
  $current_user = get_current_user_id();
  $get_user_meta = get_user_meta($current_user, "registration_data", true);
  $user_meta = unserialize($get_user_meta);
  //Not social loggedIn user
  if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
    return $redirect_url;
  }

  $activation_key = get_user_meta($current_user,'activation_key',true);
  $activation_url = esc_url( trailingslashit( bp_get_activation_page() ) . "{$activation_key}/" );
  wp_destroy_current_session();
	wp_clear_auth_cookie();
  return $activation_url;
}

// asking the user to link is account or creating new user only if the provider is twitter or linked in
//add_filter( 'option_wsl_settings_bouncer_accounts_linking_enabled', 'enableLinkingAccountOption' );
add_action( 'wsl_process_login_new_users_gateway_start', "getRequestedEmail",10,3 );
function getRequestedEmail($provider, $redirect_to, $hybridauth_user_profile )
{
  if($hybridauth_user_profile->email == "")
  {
    add_filter( 'option_wsl_settings_bouncer_accounts_linking_enabled', 'get_option_accounts_linking_enabled' );
  }
}

function get_option_accounts_linking_enabled()
{
  return 1;
}

// this for loading customized translation files
add_action('after_setup_theme', 'my_theme_setup');
function my_theme_setup() {
  $moFiles = glob(dirname(__FILE__).'/languages'.'/*-'.get_locale().'.{mo}', GLOB_BRACE);
  foreach($moFiles as $moFile) {
    $textDomain = preg_replace('/[-]+\w+(.mo$)/', '', basename($moFile));
    load_textdomain( $textDomain, $moFile );
  }
}
add_filter('show_admin_bar', '__return_false');

add_action( 'login_init', function() {
	wp_deregister_style( 'login',"" );
	wp_register_style( 'login',"" );
});

function check_fields($errors, $update, $user) {
  if (preg_match('/[^a-zA-Z0-9._-]+/', $user->user_login, $matches) || (!preg_match('/[a-zA-Z]+/', $user->user_login, $matches)) || ctype_digit($user->user_login) || ( strlen($user->user_login) < 4 ) || ( strlen($user->user_login) > 20 )) {
    $errors->add( 'user_login', __( '<strong>ERROR</strong>: Username should contain at least one letter and be at least 4 characters & no more than 20 characters' ), array( 'form-field' => 'user_login' ) );
  } else if($user->user_pass) {
    $passCount = (strlen($user->user_pass));
    if($passCount < 8) {
      $errors->add('min_password_error',__('Please enter Password at least 8 characters','egyptfoss'));
    }
  }

  if( isset( $_POST['type'] ) && $_POST['type'] == 'Entity' ) {
    if(empty( $_POST['telephone_number'] )  ) {
      $errors->add( 'telephone_number', __( '<strong>ERROR</strong>: Telephone number is required' ), array( 'form-field' => 'telephone_number' ) );
    }
    else {
      if ( preg_match('/^[0-9 \/+\(\)-]*[0-9][0-9 \/+\(\)-]*$/', $_POST['telephone_number'], $matches) == 0 )  {
        $errors->add( 'telephone_number', __( '<strong>ERROR</strong>: Telephone number should contains (+) or (-) or (/) and should have one number at least' ), array( 'form-field' => 'telephone_number' ) );
      }
    }
  }

  return $errors;
}
add_action('user_profile_update_errors', 'check_fields', 0, 3);

function registeration_username_validation($valid, $username) {
  $valid = true;
  if (preg_match('/[^a-zA-Z0-9._-]+/', $username, $matches) || (!preg_match('/[a-zA-Z]+/', $username, $matches)) || ctype_digit($username) || ( strlen($username) < 4 ) || ( strlen($username) > 20 )) {
    $valid = false;
  }
  return $valid;
}
add_filter('validate_username', 'registeration_username_validation', 10, 2);

function wdm_validate_password_reset( $errors, $user)
{
  if(isset($_POST['egyptfoss-pass1']))
  {
    $passCount = (strlen($_POST['egyptfoss-pass1']));
    if($passCount < 8)
    {
      $errors->add('min_password_error',__('Please enter Password at least 8 characters','egyptfoss'));
    }
  }
  return $errors;
}
add_action('validate_password_reset','wdm_validate_password_reset',10,2);

// redirect users to front page after login
function redirect_to_front_page() {
	global $redirect_to;
	if (!isset($_GET['redirect_to'])) {
		$redirect_to = get_option('siteurl');
	}
}
add_action('login_form', 'redirect_to_front_page');


function init_taxonomies() {
  global $ef_registered_taxonomies;
  global $ef_registered_taxonomies_labels;
  foreach ($ef_registered_taxonomies as $value) {
    $label = $ef_registered_taxonomies_labels[$value];

    if( $value == 'industry' ) {
      register_taxonomy( $value, 'product', array( 'label' => $label, 'query_var' => false, 'hierarchical' => true, 'show_in_quick_edit' => false, 'meta_box_cb' => false ) );
    }
    else {
      register_taxonomy( $value, '', array( 'label' => $label, 'query_var' => false ) );
    }
  }
}
add_action( 'init', 'init_taxonomies' );

function loadTaxMenuHomePage() {
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
  echo "";
}
function my_plugin_menu() {
  global $ef_registered_taxonomies;
  global $ef_registered_taxonomies_labels;
  add_menu_page('Main Page' , 'Setup Data', 'manage_options', 'taxonomies_page','loadTaxMenuHomePage','dashicons-category','26');
  foreach ($ef_registered_taxonomies as  $value) {
    if( ! in_array( $value, array( 'industry', 'quiz_categories' ) ) ) {
      add_submenu_page('taxonomies_page', $ef_registered_taxonomies_labels[$value], $ef_registered_taxonomies_labels[$value], 'manage_options', 'edit-tags.php?taxonomy='.$value);
    }
  }
}
add_action('admin_menu', 'my_plugin_menu');

function saveProfile($user_id) {
  include( ABSPATH . 'system_data.php' );
  $errors = array();
  $db_data = get_user_meta($user_id, 'registration_data', true);
  $user_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
  }, $db_data );
  $meta = unserialize($user_data);
	$parameters = ['sub_type', 'functionality', 'theme', 'ict_technology', 'address', 'phone',
								 'facebook_url', 'twitter_url', 'gplus_url', 'linkedin_url', 'interest',
								 'contact_name', 'contact_email', 'contact_address', 'contact_phone','display_name'];
  if(!isset($_POST['display_name']) || empty($_POST['display_name']) || !preg_match('/[أ-يa-zA-Z]+/', $_POST['display_name'], $matches))
  {
    bp_core_add_message(sprintf(__( "Invalid %s .", 'egyptfoss' ),__("Display name","egyptfoss")), 'error' );
    array_push($errors, $parameter);
  }else
  {
    $userData = array("ID" => $user_id,'display_name' => $_POST['display_name']);
    wp_update_user($userData);
  }
  $current_term_taxonomy_ids = array();
  foreach($parameters as $parameter) {
    if($parameter == "functionality")
    {
      $_POST[$parameter] = strip_js_tags($_POST[$parameter]) ;
    }

    if(array_key_exists($parameter, $_POST)) {
      if (is_array($_POST[$parameter])) { // ict_technologies & interests
        $list = $_POST[$parameter];
        $taxonomy = ($parameter == 'ict_technology') ? 'technology' : 'interest';
        foreach ($list as $key => $value) {
          if (preg_match('/[أ-يa-zA-Z]+/', $value, $matches)) {
            $result = term_exists( $value, $taxonomy );
            if (!($result !== 0 && $result !== null)) {
              $result = wp_insert_term( $value, $taxonomy, $args = array('slug' => $value, 'description'=> $value) );
            }
            $term_taxonomy_id = $result['term_taxonomy_id'];
            if(count(check_user_relation($user_id, $term_taxonomy_id)) == 0) {
              add_user_relation($user_id, $term_taxonomy_id);
            }
            array_push($current_term_taxonomy_ids, $term_taxonomy_id);
          } else {
            bp_core_add_message( __( "Invalid $parameter.", 'egyptfoss' ), 'error' );
            array_push($errors, $parameter);
          }
        }
      } else if ($parameter == 'sub_type') {
        if(!array_key_exists($_POST[$parameter], $account_sub_types)) {
          bp_core_add_message( __( "Invalid $parameter.", 'egyptfoss' ), 'error' );
          array_push($errors, $parameter);
        }
        $_POST[$parameter] = (array_key_exists($_POST[$parameter], $account_sub_types)) ? $_POST[$parameter] : '';
      } else if ($parameter == 'theme') {
        $term = term_exists(intval($_POST[$parameter]), 'theme');
        if (($_POST[$parameter] != '') && (!($term !== 0 && $term !== null))) {
          bp_core_add_message( __( "Invalid $parameter.", 'egyptfoss' ), 'error' );
          array_push($errors, $parameter);
        }
        $_POST[$parameter] = ($term !== 0 && $term !== null) ? $term['term_id'] : '';
        $term_taxonomy_id = ($term !== 0 && $term !== null) ? $term['term_taxonomy_id'] : '';
        if($term_taxonomy_id != '' && count(check_user_relation($user_id, $term_taxonomy_id)) == 0) {
          add_user_relation($user_id, $term_taxonomy_id);
        }
        array_push($current_term_taxonomy_ids, $term_taxonomy_id);
      } else if ($parameter == 'phone') {
        if (!empty($_POST[$parameter]) && (preg_match('/[^0-9 \/+\(\)-]+/', $_POST[$parameter], $matches) || (!preg_match('/[0-9]+/', $_POST[$parameter], $matches)))) {
          bp_core_add_message( __( "Invalid $parameter.", 'egyptfoss' ), 'error' );
          array_push($errors, $parameter);
        }
      }
      $meta[$parameter] = $_POST[$parameter];
    }
	}
  $admin_side = (isset($_REQUEST['wp_http_referer']) && ($_REQUEST['wp_http_referer'] == '/wp-admin/users.php')) ? true : false;
  $admin_side_extended_profile = (isset($_REQUEST['_wp_http_referer']) && strpos( $_REQUEST['_wp_http_referer'], '/wp-admin/users.php?page=bp-profile-edit') !== false) ? true : false;
  // Set the feedback messages.
  if ($admin_side || $admin_side_extended_profile) {
    bp_core_add_message( __( 'Changes saved.', 'buddypress' ) );
  } else if ( !empty( $errors ) ) {
    // Redirect back to the edit screen to display the error message.
    bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() . '/edit/group/' . bp_action_variable( 1 ) ) );
  } else {
    remove_user_relations($user_id, $current_term_taxonomy_ids);
    $meta_key = 'registration_data';
    $meta_value = serialize($meta);
    update_user_meta($user_id, $meta_key, $meta_value);

		$userdata = get_userdata( $user_id );
		$name = $userdata->display_name;
		$parts = explode(" ", $name);
		update_user_meta( $user_id, 'last_name', array_pop($parts));
		update_user_meta( $user_id, 'first_name', implode(" ", $parts));
		xprofile_set_field_data( 1, $user_id, $name, false );

    // save users content to marmotta
    saveUserContent( $user_id, $_POST['display_name'], $_POST[ 'functionality' ], $meta['type'] );

    bp_core_add_message( __( 'Changes saved.', 'buddypress' ) );
    // Redirect to the view screen to display the updates and message.
    bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_profile_slug() ) );
  }
}
add_action('xprofile_updated_profile', 'saveProfile', 0, 1);

include get_template_directory() . '/inc/linking-social-media.php';

function remove_from_nav() {
  global $bp;
  bp_core_remove_subnav_item( $bp->profile->slug, 'change-cover-image' );
  bp_core_remove_subnav_item( $bp->settings->slug, 'notifications' );
  bp_core_remove_nav_item('notifications' );
  bp_core_remove_subnav_item( $bp->activity->slug, 'mentions' );
  bp_core_remove_subnav_item( $bp->settings->slug, 'profile' );
  bp_core_remove_subnav_item( $bp->settings->slug, 'delete-account' );
}
add_action( 'init', 'remove_from_nav' );

function login_with_email_address( &$username ) {
  $user = get_user_by( 'email', $username );
  if ( !empty( $user->user_login ) )
    $username = $user->user_login;
  return $username;
}
add_action( 'wp_authenticate','login_with_email_address' );

function go_home(){
  wp_redirect( home_url() );
  exit();
}
add_action('wp_logout','go_home');

include get_template_directory() . '/inc/email-template-override.php';


function blockusers_init() {
  if ( is_admin() && !current_user_can('administrator') && !current_user_can('editor') && !(defined('DOING_AJAX') && DOING_AJAX)) {
    include( get_query_template( '404' ) );
    exit;
  }
}
add_action( 'init', 'blockusers_init' );

function wscu_sanitize_user ($username, $raw_username, $strict) {
  $username = wp_strip_all_tags ($raw_username);
  $username = remove_accents ($username);
  $username = preg_replace ('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
  $username = preg_replace ('/&.+?;/', '', $username);
  if ($strict) {
    $settings = get_option ('wscu_settings');
    $username = preg_replace ('|[^a-z\p{Arabic}\p{Cyrillic}0-9 _.\-@]|iu', '', $username);
  }
  $username = trim ($username);
  $username = preg_replace ('|\s+|', ' ', $username);
  return $username;
}
add_filter ('sanitize_user', 'wscu_sanitize_user', 10, 3);

$activity_id = bp_get_activity_id();
$user_id = bp_get_activity_user_id();
function add_interest_to_activity( $content, $user_id, $activity_id ) {
  global $wpdb;
  if (!empty($_POST['post_interest'])){
      $interests = $_POST['post_interest'];
      foreach ($interests as $interest) {
        if ( !is_numeric( $interest ) ) {
          $term = $wpdb->get_row( $wpdb->prepare(
            "SELECT `term_id` FROM $wpdb->terms WHERE `name` = %s OR `name_ar` = %s", $interest, $interest
          ) );
          if( ! $term ) {
            $term_tax = wp_insert_term( $interest, 'interest' );
            if( !is_wp_error( $term_tax ) ) {
              $interest = $term_tax['term_id'];
            }
          }
          else {
            $interest = $term->term_id;
          }
        }
        bp_activity_add_meta( $activity_id, 'interest', $interest );
      }
  }
}
add_action( 'bp_activity_posted_update', 'add_interest_to_activity', 10, 3 );

function get_registration_data($user_id){
  $user_data = get_user_meta($user_id, 'registration_data', true);
  if(is_string($user_data)) {
    $user_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
      return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
    }, $user_data );
    $user_data = unserialize($user_data);
  }
  return $user_data;
}

function get_custom_posts($type) {
  $my_query = null;
  $args=array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'caller_get_posts'=> 1
  );
  $my_query = new WP_Query($args);
  return $my_query->posts;
}

function remove_meta_boxes() {
  remove_meta_box( 'tagsdiv-post_tag', 'tribe_events', 'side' );
  remove_meta_box( 'postimagediv', 'tribe_events', 'side' );
  remove_meta_box( 'postexcerpt', 'tribe_events', 'normal' );
  remove_meta_box( 'postcustom', 'tribe_events', 'normal' );
}
add_action( 'add_meta_boxes', 'remove_meta_boxes' );

function add_profile_meta_tags() {
    global $post;
    $url = "http://www.googl.com";
    $logoUrl = home_url("wp-content/themes/egyptfoss/img/social_m.jpg");
  if( bp_is_member() ) { // tell you if you’re viewing any user, including yourself
    $uploads = wp_upload_dir();
    $upload_path = $uploads['baseurl'].'/avatars/'.bp_displayed_user_id().'/';
    $upload_directory = $uploads['basedir'].'/avatars/'.bp_displayed_user_id().'/';
    @ $files = scandir($upload_directory);
    $image_name = (isset($files[2])) ? $files[2] : '';// because [0] = "." [1] = ".."
    if($image_name != '') {
      $image_path = $upload_path . $image_name;
      echo '<meta property="og:image" content="' . $image_path . '" />' . "\n";
      echo '<meta name="twitter:image" content="' . $image_path . '"/>' . "\n";
      echo '<meta itemprop="image" content="' . $image_path . '"/>' . "\n";
      echo '<link rel="image_src" href="'.$image_path.'" />'. "\n";

    }  else {
        echo '<meta property="og:image" content="' . $logoUrl . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . $logoUrl . '"/>' . "\n";
        echo '<meta itemprop="image" content="' . $logoUrl . '"/>' . "\n";
        echo '<link rel="image_src" href="'.$logoUrl.'" />'. "\n";
    }
  }else if ($post)// is_singular('news')
  {
      //check if quiz
      $result_id = efGetValueFromUrlByKey("result");
      $result = ef_returnResult($result_id);
      if(is_array($result))
      {
        echo '<meta property="og:image" content="' . $logoUrl . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . $logoUrl . '"/>' . "\n";
        echo '<meta itemprop="image" content="' . $logoUrl . '"/>' . "\n";
        echo '<link rel="image_src" href="'.$logoUrl.'" />'. "\n";
      }
      else if(!has_post_thumbnail(get_the_ID()))
      {
        echo '<meta property="og:image" content="' . $logoUrl . '" />' . "\n";
        echo '<meta name="twitter:image" content="' . $logoUrl . '"/>' . "\n";
        echo '<meta itemprop="image" content="' . $logoUrl . '"/>' . "\n";
        echo '<link rel="image_src" href="'.$logoUrl.'" />'. "\n";

        if($post->post_type == "quiz")
        {
          echo '<meta itemprop="description" content="'.__("Take this quiz to test your FOSS knowledge","egyptfoss").'" />'. "\n";
          echo '<meta property="og:description" content="'.__("Take this quiz to test your FOSS knowledge","egyptfoss").'" />'. "\n";
        }
      }else
      {
          $attachment_id = get_post_thumbnail_id(get_the_ID());
          //get image url
          $post_attachment = get_post($attachment_id);
          echo '<meta itemprop="image" content="' . $post_attachment->guid . '"/>' . "\n";
          echo '<link rel="image_src" href="'.$post_attachment->guid.'" />'. "\n";

          if(is_singular('product'))
          {
            echo '<meta property="og:image" content="'.$post_attachment->guid.'" />'. "\n";
          }
      }
  }else
  {
    echo '<meta property="og:image" content="' . $logoUrl . '" />' . "\n";
    echo '<meta name="twitter:image" content="' . $logoUrl . '"/>' . "\n";
    echo '<meta name="twitter:title" content="' . $_REQUEST['title'] . '"/>' . "\n";
    echo '<meta name="twitter:card" content="summary"/>' . "\n";
    echo '<meta name="og:title" content="' . $_REQUEST['title'] . '"/>' . "\n";
    echo '<meta itemprop="image" content="' . $logoUrl . '"/>' . "\n";
    echo '<link rel="image_src" href="'.$logoUrl.'" />'. "\n";
  }
}
add_action( 'wp_head', 'add_profile_meta_tags');

function fix_profile_title_meta_tag($title) {
  if( bp_is_member() ) { // tell you if you’re viewing any user, including yourself
    $user_id = bp_displayed_user_id();
    $ud = get_userdata( $user_id );
    $title = $ud->display_name . ' ' . $title;
  }
  return $title;
}
add_action( 'wpseo_opengraph_title', 'fix_profile_title_meta_tag');
add_action( 'wpseo_twitter_title', 'fix_profile_title_meta_tag');

//profile products tab
include get_template_directory() . '/inc/profile-products-tab.php';


remove_action( 'wp_ajax_activity_mark_fav', 'bp_dtheme_mark_activity_favorite' );
remove_action( 'wp_ajax_nopriv_activity_mark_fav', 'bp_dtheme_mark_activity_favorite' );

remove_action( 'wp_ajax_activity_mark_unfav', 'bp_dtheme_unmark_activity_favorite' );
remove_action( 'wp_ajax_nopriv_activity_mark_unfav', 'bp_dtheme_unmark_activity_favorite' );

function my_favorite_count() {
  if(bp_get_activity_id()){
    $activity_id = bp_get_activity_id();
  }
  else{
    $activity_id = $_POST['id'];
  }
  $my_fav_count = bp_activity_get_meta( $activity_id, 'favorite_count' );
  if ($my_fav_count >= 1) : {
    if (is_user_logged_in()) : {
      echo ' '.$my_fav_count.' ';
    }
    endif;
  }
  endif;
}

function custom_like_text(){
  if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
    return;
    if ( bp_activity_add_user_favorite( $_POST['id'] ) ){
//      _e( 'Dislike', 'egyptfoss' );
      printf( __( 'Like %s', 'egyptfoss' ), '' );  // it was dislike
    }else{
      printf( __( 'Like  %s', 'egyptfoss' ), '');
    }
    exit;
}

function custom_unlike_text(){
  if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
    return;
  if ( bp_activity_remove_user_favorite( $_POST['id'] ) ){
    // _e( 'Like', 'egyptfoss' );
    printf( __( 'Like  %s', 'egyptfoss' ), '' );
  } else {
    printf( __( 'Like %s', 'egyptfoss' ), '' );  // it was dislike
  }
  exit;
}

add_action( 'wp_ajax_activity_mark_fav', 'custom_like_text' );
add_action( 'wp_ajax_nopriv_activity_mark_fav', 'custom_like_text' );

add_action( 'wp_ajax_activity_mark_unfav', 'custom_unlike_text' );
add_action( 'wp_ajax_nopriv_activity_mark_unfav', 'custom_unlike_text' );

//Notifications setting tab
include get_template_directory() . '/inc/email-notification.php';

add_action('wp_ajax_ef_email_notifications', 'ef_email_notifications');
function ef_email_notifications() {
  $user_id = get_current_user_id();

  if ($user_id) {
    $notificationType = $_POST["notification"];
    $notificationFrequency = $_POST["frequency"];
    update_user_meta( $user_id, "notification_".$notificationType, $notificationFrequency);
    echo "saved";
    die();
  }
  echo "not-saved";
  die();
}

// Add images sizes (crop)
add_image_size( 'news-thumbnail', 340, 210,true, array('center', 'center' ) );
add_image_size( 'news-featured', 675, 420, true);
add_image_size( 'news-thumbnail-small', 64, 64, true);
add_image_size( 'medium-img', 410, 250, true);
add_image_size( 'xlarge-img', 825, 320, true);

//shorten title

//function to call and print shortened post title
function the_title_shorten($len,$rep='...') {
	$title = the_title('','',false);
	$shortened_title = textLimit($title, $len, $rep);
	print $shortened_title;
}

//shorten without cutting full words
function textLimit($string, $length, $replacer) {
	if(strlen($string) > $length)
	return (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;
	return $string;
}

//set event url to a label
add_filter('tribe_get_event_website_link_label', 'tribe_get_event_website_link_label_default');
function tribe_get_event_website_link_label_default ($label) {
    if( $label == tribe_get_event_website_url() ) {
        $label = __('Visit Event Website', 'egyptfoss');
    }

    return '<a href="' . tribe_get_event_website_url() . '" target="_blank">' . $label . ' </a>';
}
function js_events_types($events_types) {
  $js = '{';
  foreach ($events_types as $key => $value) {
    $js .= "'".$key."':'".__("$value", "egyptfoss")."',";
  }
  $js .= "}";
  return $js;
}

function js_individuals_types($sub_types) {
  global $account_sub_types;
  $js = '{';
  foreach ($sub_types as $key => $value) {
    if ($account_sub_types[$key] == 'Individual') {
      $js .= "'".$key."':'".$value."',";
    }
  }
  $js .= "}";
  return $js;
}

function js_entities_types($sub_types) {
  global $account_sub_types;
  $js = '{';
  foreach ($sub_types as $key => $value) {
    if ($account_sub_types[$key] == 'Entity') {
      $js .= "'".$key."':'".$value."',";
    }
  }
  $js .= "}";
  return $js;
}

function disable_extended_profile($retval, $component) {
  if($component == 'xprofile') {
    return false;
  }
}
function disable_extended_profile_for_registration() {
  add_filter('bp_is_active', 'disable_extended_profile', 10, 2);
}
add_action('bp_signup_pre_validate','disable_extended_profile_for_registration');
function enable_extended_profile($retval, $component) {
  if($component == 'xprofile') {
    return true;
  }
}
function enable_extended_profile_after_registration() {
  add_filter('bp_is_active', 'enable_extended_profile', 10, 2);
}
add_action('bp_signup_validate','enable_extended_profile_after_registration');

function switchToPreferedLang($user, $userData) {
  if ((isset($_GET['action']) && (isset($_GET['mode']) && $_GET['mode'] == "link"))) {
    session_start();
    if($_GET['action'] == "wordpress_social_authenticated")
    {
      $message = sprintf(__("linked with your %s account successfully",'egyptfoss'),__($_GET['provider'],'egyptfoss'));
      setMessageBySession('ef_wsl_login', 'success', $message);
    }else
    {
      setMessageBySession('ef_wsl_login', 'error', 'something wrong happened');
    }
  } else {
    if ((!isset($_REQUEST['redirect_to']) || get_option('siteurl') == $_REQUEST['redirect_to']
            || get_option('siteurl')."/".pll_current_language() == str_replace("/login/", "", $_REQUEST['redirect_to'])) && !is_admin()) {
      $lang = get_user_meta($userData->ID, 'prefered_language', true);
      if ($lang) {
        setcookie('saved_user_lang', $lang);
        wp_redirect(site_url() . "/" . $lang . "/members/$userData->user_nicename/");
        exit;
      }else {
        global $defaultLanguage;
        $lang = $defaultLanguage;
        setcookie('saved_user_lang', $lang);
        wp_redirect(site_url() . "/" . $lang . "/members/$userData->user_nicename/");
        exit;
      }
    }else
    {
      if(!is_admin())
      {
        $redirect_to = $_REQUEST['redirect_to'];
        if (strpos($redirect_to, 'redirect_to=') !== false)
        {
          $redirect_to = urldecode(substr($redirect_to, strpos($redirect_to, 'redirect_to=') + 12 ));
        }

        // User loggedIn with social login
        if($redirect_to == site_url()."/".pll_current_language())
        {
          $lang = pll_current_language();
          wp_redirect(site_url() . "/" . $lang . "/members/$userData->user_nicename/");
          exit;
        }

        if( !preg_match('~^/[a-z]{2}(?:/|$)~', $redirect_to) ) {
          $redirect_to = str_replace(site_url(), site_url() . '/' . pll_current_language(), $redirect_to);
        }

        wp_redirect($redirect_to);
        exit;
      }
    }
  }
}

add_action( 'wp_login', 'switchToPreferedLang', 999,2 );

function checkPreferedLanguagePerUser() {
  if (!is_admin()) {
    if (is_user_logged_in()) {
      if (isset($_COOKIE['saved_user_lang']) && $_COOKIE['saved_user_lang'] != pll_current_language()) {

        setcookie('saved_user_lang', pll_current_language());
        update_user_meta(get_current_user_id(), 'prefered_language', pll_current_language());
      } else {
        if (!isset($_COOKIE['saved_user_lang'])) {
          if (is_user_logged_in()) {
            setcookie('saved_user_lang', pll_current_language());
            update_user_meta(get_current_user_id(), 'prefered_language', pll_current_language());
          }
        }else {
            if (is_user_logged_in()) {
                setcookie('saved_user_lang', pll_current_language());
                update_user_meta(get_current_user_id(), 'prefered_language', pll_current_language());
            }
        }
      }
    }
  }
}
add_action( 'init', 'checkPreferedLanguagePerUser' );

function current_type_nav_class($classes, $item) {
  $url = preg_replace("/\//", '\/', $item->url);
  $menus  = array(
                  "maps", "events", "products", "news",
                  "wiki","success-stories","open-datasets",
                  "collaboration-center","request-center",
                  "expert-thoughts", "awareness-center",
                  "marketplace", "activist-center"
            );
  $current_regex = false;
  foreach ($menus as $menu) {
    if (preg_match("/" . "\/" . pll_current_language() . "\/" . $menu . ".*/", $_SERVER['REQUEST_URI'])) {
      $current_regex = "/" . "\/" . pll_current_language() . "\/" . $menu . ".*/";
    }
  }
  if ($current_regex) {
    if (is_home()) {
      if ("/" . pll_current_language() . "/" == $item->url) {
        array_push($classes, 'current_page_item');
      }
    } else {
      if (preg_match($current_regex, $item->url)) {
        array_push($classes, 'current_page_item');
      }
    }
  }
  return $classes;
}
add_filter('nav_menu_css_class', 'current_type_nav_class', 10, 2);
function update_homepage_news_feature($post_id, $post) {
    global $wpdb;


    if($post->post_type == "news")
    {
        //reset news is_featured of other posts
        update_post_meta($post_id, 'is_news_featured_homepage', 0);

        //reset all is_featured_home to 0
        $matches = array('meta_key' => 'is_news_featured_homepage');
        $data = array('meta_value' => '0');
        $wpdb->update("$wpdb->postmeta", $data, $matches );

        //set the latest news to is_news_featured
        $postids_en = $wpdb->get_col(
            "
            SELECT      posts.ID
            FROM        $wpdb->posts as posts
            JOIN        $wpdb->postmeta as postmeta
                        ON postmeta.post_id = posts.ID
            JOIN        $wpdb->postmeta as postmeta_lang
                        ON postmeta_lang.post_id = posts.ID
            WHERE       postmeta.meta_key = 'is_featured'
                        AND postmeta.meta_value = '1'
                        AND posts.post_type = 'news'
                        AND posts.post_status = 'publish'
                        AND postmeta_lang.meta_key = 'language'
                        AND postmeta_lang.meta_value like '%en%'
            ORDER BY    posts.post_date desc
            Limit       1
            "
        );

        if ( $postids_en )
        {
            foreach ( $postids_en as $id )
            {
                update_post_meta($id, 'is_news_featured_homepage', 1);
                break;
            }
        }

        $postids_ar = $wpdb->get_col(
            "
            SELECT      posts.ID
            FROM        $wpdb->posts as posts
            JOIN        $wpdb->postmeta as postmeta
                        ON postmeta.post_id = posts.ID
            JOIN        $wpdb->postmeta as postmeta_lang
                        ON postmeta_lang.post_id = posts.ID
            WHERE       postmeta.meta_key = 'is_featured'
                        AND postmeta.meta_value = '1'
                        AND postmeta_lang.meta_key = 'language'
                        AND posts.post_type = 'news'
                        AND posts.post_status = 'publish'
                        AND postmeta_lang.meta_value like '%ar%'
            ORDER BY    posts.post_date desc
            Limit       1
            "
        );

        if ( $postids_ar )
        {
            foreach ( $postids_ar as $id )
            {
                update_post_meta($id, 'is_news_featured_homepage', 1);
                break;
            }
        }
    }
}
add_action('save_post', 'update_homepage_news_feature', 999, 2);


add_filter('tribe_events_month_day_limit', 'tribe_remove_calendar_day_limit');
function tribe_remove_calendar_day_limit() { return -1; }

function add_my_favicon() {
  echo '<link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">';
  echo '<link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">';
  echo '<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">';
  echo '<link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">';
  echo '<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">';
  echo '<link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">';
  echo '<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">';
  echo '<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">';
  echo '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">';
  echo '<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">';
  echo '<link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">';
  echo '<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">';
  echo '<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">';
}
add_action( 'admin_head', 'add_my_favicon' ); //admin end

function ef_load_likes_list($activity_id) {
  global $wpdb;
  $avatar_size = 28;
  //$activity_id = (isset($_POST['id'])) ? $_POST['id'] : '';
  $query = "SELECT m.user_id, u.display_name FROM ".$wpdb->base_prefix."usermeta m JOIN ".$wpdb->base_prefix."users u ON u.ID = m.user_id WHERE meta_key = 'bp_favorite_activities' AND (meta_value LIKE '%:$activity_id;%' OR meta_value LIKE '%:\"$activity_id\";%') ";
  $users = $wpdb->get_results($query, ARRAY_A);
  $output = array('count'=>0, 'content'=>'');
  if(empty($users)) {
    $output['content'] .= __( "No likes for this activity yet.", 'egyptfoss' );
  } else {
    $output['count'] = count($users);
    foreach ($users as $user) {
      $link = bp_core_get_user_domain($user['user_id']);
      $avatarurl = bp_core_fetch_avatar( array( 'item_id' => $user['user_id'], 'width' => $avatar_size, 'height' => $avatar_size, 'html' => false ) );
      $output['content'] .= '<li><a href="'.$link.'"><img src="'.$avatarurl.'" class="avatar user-1-avatar avatar-28 photo" width="28" height="28" alt="Profile picture of '.$user['display_name'].'"></a> <a class="activity_fav_users" href="'.$link.'"'.">".$user['display_name']."</a></li>";
    }
  }
  return ($output);
}
//add_action('wp_ajax_ef_load_likes_list', 'ef_load_likes_list');
//add_action('wp_ajax_nopriv_ef_load_likes_list', 'ef_load_likes_list');

function social_check_password($check, $password, $hash, $user_id = '') {
  global $wpdb;
  if(is_user_logged_in() && !canDeleteLastSocialMedia($user_id, $wpdb)) {
    return true;
  }
  return $check;
}
add_filter('check_password', 'social_check_password', 9, 4 );

function after_change_password($check, $user, $userdata) {
  $user_id = $user['ID'];
  $db_data = get_user_meta($user_id, 'registration_data', true);
  $user_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
    return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
  }, $db_data );
  $meta = unserialize($user_data);
  $meta['registeredNormally'] = 1;
  update_user_meta($user_id, 'registration_data', serialize($meta));
}
add_filter('send_password_change_email', 'after_change_password', 10, 3 );


function getTermFromTermTaxonomy($tt_ids)
{
    global $wpdb;

    $arr = array();

    for($i = 0; $i < sizeof($tt_ids); $i++)
    {
        $sql = "select distinct term_id
                    from wpRuvF8_term_taxonomy
                    where wpRuvF8_term_taxonomy.term_taxonomy_id = $tt_ids[$i];";

        $item = $wpdb->get_results($sql);
        if(sizeof($item) > 0)
        {
            array_push($arr, $item[0]->term_id);
        }
    }

    return $arr;
}

function ef_bp_core_time_since($output, $older_date, $newer_date)
{
    /**
     * Filters whether or not to bypass BuddyPress' time_since calculations.
     *
     * @since 1.7.0
     *
     * @param bool   $value      Whether or not to bypass.
     * @param string $older_date Earlier time from which we're calculating time elapsed.
     * @param string $newer_date Unix timestamp of date to compare older time to.
     */
    $pre_value = apply_filters( 'bp_core_time_since_pre', false, $older_date, $newer_date );
    if ( false !== $pre_value ) {
            return $pre_value;
    }

    /**
     * Filters the value to use if the time since is unknown.
     *
     * @since 1.5.0
     *
     * @param string $value String representing the time since the older date.
     */
    $unknown_text   = apply_filters( 'bp_core_time_since_unknown_text',   __( 'sometime',  'buddypress' ) );

    /**
     * Filters the value to use if the time since is right now.
     *
     * @since 1.5.0
     *
     * @param string $value String representing the time since the older date.
     */
    $right_now_text = apply_filters( 'bp_core_time_since_right_now_text', __( 'right now', 'buddypress' ) );

    /**
     * Filters the value to use if the time since is some time ago.
     *
     * @since 1.5.0
     *
     * @param string $value String representing the time since the older date.
     */
    $ago_text       = apply_filters( 'bp_core_time_since_ago_text',       __( '%s ago',    'buddypress' ) );

    // Array of time period chunks.
    $chunks = array(
            YEAR_IN_SECONDS,
            30 * DAY_IN_SECONDS,
            WEEK_IN_SECONDS,
            DAY_IN_SECONDS,
            HOUR_IN_SECONDS,
            MINUTE_IN_SECONDS,
            1
    );

    if ( !empty( $older_date ) && !is_numeric( $older_date ) ) {
            $time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
            $date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
            $older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
    }

    /**
     * $newer_date will equal false if we want to know the time elapsed between
     * a date and the current time. $newer_date will have a value if we want to
     * work out time elapsed between two known dates.
     */
    $newer_date = ( !$newer_date ) ? bp_core_current_time( true, 'timestamp' ) : $newer_date;

    // Difference in seconds.
    $since = $newer_date - $older_date;

    // Something went wrong with date calculation and we ended up with a negative date.
    if ( 0 > $since ) {
            $output = $unknown_text;

    /**
     * We only want to output two chunks of time here, eg:
     * x years, xx months
     * x days, xx hours
     * so there's only two bits of calculation below:
     */
    } else {

            // Step one: the first chunk.
            for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
                    $seconds = $chunks[$i];

                    // Finding the biggest chunk (if the chunk fits, break).
                    $count = floor( $since / $seconds );
                    if ( 0 != $count ) {
                            break;
                    }
            }

            // If $i iterates all the way to $j, then the event happened 0 seconds ago.
            if ( !isset( $chunks[$i] ) ) {
                    $output = $right_now_text;

            } else {

                    // Set output var.
                    switch ( $seconds ) {
                            case YEAR_IN_SECONDS :
                                    $output = sprintf( _n( '%s year',   '%s years',   $count, 'buddypress' ), $count );
                                    break;
                            case 30 * DAY_IN_SECONDS :
                                    $output = sprintf( _n( '%s month',  '%s months',  $count, 'buddypress' ), $count );
                                    break;
                            case WEEK_IN_SECONDS :
                                    $output = sprintf( _n( '%s week',   '%s weeks',   $count, 'buddypress' ), $count );
                                    break;
                            case DAY_IN_SECONDS :
                                    $output = sprintf( _n( '%s day',    '%s days',    $count, 'buddypress' ), $count );
                                    break;
                            case HOUR_IN_SECONDS :
                                    $output = sprintf( _n( '%s hour',   '%s hours',   $count, 'buddypress' ), $count );
                                    break;
                            case MINUTE_IN_SECONDS :
                                    $output = sprintf( _n( '%s minute', '%s minutes', $count, 'buddypress' ), $count );
                                    break;
                            default:
                                    $output = sprintf( _n( '%s second', '%s seconds', $count, 'buddypress' ), $count );
                    }

                    // Step two: the second chunk
                    // A quirk in the implementation means that this
                    // condition fails in the case of minutes and seconds.
                    // We've left the quirk in place, since fractions of a
                    // minute are not a useful piece of information for our
                    // purposes.
                    if ( $i + 2 < $j ) {
                            $seconds2 = $chunks[$i + 1];
                            $count2   = floor( ( $since - ( $seconds * $count ) ) / $seconds2 );

                            // Add to output var.
                            if ( 0 != $count2 ) {
                                    $output .= _x( ',', 'Separator in time since', 'buddypress' ) . ' ';

                                    switch ( $seconds2 ) {
                                            case 30 * DAY_IN_SECONDS :
                                                    $output .= sprintf( _n( '%s month',  '%s months',  $count2, 'buddypress' ), $count2 );
                                                    break;
                                            case WEEK_IN_SECONDS :
                                                    $output .= sprintf( _n( '%s week',   '%s weeks',   $count2, 'buddypress' ), $count2 );
                                                    break;
                                            case DAY_IN_SECONDS :
                                                    $output .= sprintf( _n( '%s day',    '%s days',    $count2, 'buddypress' ), $count2 );
                                                    break;
                                            case HOUR_IN_SECONDS :
                                                    $output .= sprintf( _n( '%s hour',   '%s hours',   $count2, 'buddypress' ), $count2 );
                                                    break;
                                            case MINUTE_IN_SECONDS :
                                                    $output .= sprintf( _n( '%s minute', '%s minutes', $count2, 'buddypress' ), $count2 );
                                                    break;
                                            default:
                                                    $output .= sprintf( _n( '%s second', '%s seconds', $count2, 'buddypress' ), $count2 );
                                    }
                            }
                    }
            }
    }

    // Append 'ago' to the end of time-since if not 'right now'.
    if ( $output != $right_now_text ) {
            $output = sprintf( $ago_text, $output );
    }

    return $output;
}
add_filter('bp_core_time_since','ef_bp_core_time_since',10,3);

function ef_change_email_recipient($address, $user)
{
    if(isset($_POST['email']))
        $address = $_POST['email'];
    return $address;
}
add_filter('bp_email_recipient_get_address','ef_change_email_recipient',10,2);

function ef_bp_activity_get_comment_count()
{
  global $wpdb;
  $activity_id = $_POST['activity_id'];
  if(!is_numeric($activity_id))
  {
    echo 0;
  }
  $sql = "SELECT * FROM {$wpdb->prefix}bp_activity where item_id = {$activity_id};";
  echo count($wpdb->get_results($sql));
  exit;
}
add_action( 'wp_ajax_ef_bp_activity_get_comment_count', 'ef_bp_activity_get_comment_count' );
add_action('wp_ajax_nopriv_ef_bp_activity_get_comment_count', 'ef_bp_activity_get_comment_count');

function ef_tribe_get_cost($cost, $post_id, $with_currency_symbol )
{
    global $system_currencies;
    global $ar_system_currencies;
    $lang = get_locale();
    if($lang == "ar")
    {
        foreach ($system_currencies as $key => $value)
        {
            if (strpos($cost, $system_currencies[$key]) !== false) {
                $cost = str_replace($system_currencies[$key], '', $cost);
                $cost = $cost.$ar_system_currencies[$key];
            }
        }
    }

    return $cost;
}
add_filter('tribe_get_cost','ef_tribe_get_cost',10,3);

function ef_bp_core_get_user_displayname( $fullname, $user_id )
{
    global $wpdb;
    $query = "SELECT u.display_name,u.user_nicename FROM ".$wpdb->base_prefix."users u WHERE ID = $user_id";
    $user = $wpdb->get_row($query);
    if($user->display_name != null && $user->display_name != '')
        $fullname = $user->display_name;
    if($fullname == '')
        $fullname = '-';

    return $fullname;
}
add_filter('bp_core_get_user_displayname','ef_bp_core_get_user_displayname',10,2);

function ef_activity_like_unlike() {
  if($_POST['is_like'] == "0")
  {
    bp_activity_remove_user_favorite($_POST['activity_id']);
  }else
  {
    bp_activity_add_user_favorite($_POST['activity_id']);
  }

  $fav_count =  bp_activity_get_meta($_POST['activity_id'], 'favorite_count');
  $liked_user_list = ef_load_likes_list($_POST['activity_id']);
  echo json_encode(array("fav_count"=>$fav_count,"liked_user_list"=>$liked_user_list));
  exit;
}
add_action('wp_ajax_ef_activity_like_unlike', 'ef_activity_like_unlike');

function ef_tribe_events_ical_export_text($text)
{
  $tec = Tribe__Events__Main::instance();

		$view = $tec->displaying;
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $wp_query->query_vars['eventDisplay'] ) ) {
			$view = $wp_query->query_vars['eventDisplay'];
		}

		switch ( strtolower( $view ) ) {
			case 'month':
				$modifier = sprintf( esc_html__( "Month's %s", 'the-events-calendar' ), _nx("Event","Events",2,"indefinite","egyptfoss") );
				break;
			case 'week':
				$modifier = sprintf( esc_html__( "Week's %s", 'the-events-calendar' ), tribe_get_event_label_plural() );
				break;
			case 'day':
				$modifier = sprintf( esc_html__( "Day's %s", 'the-events-calendar' ), tribe_get_event_label_plural() );
				break;
			default:
				$modifier = sprintf( esc_html__( 'Listed %s', 'the-events-calendar' ), tribe_get_event_label_plural() );
				break;
		}
  return esc_html__( 'Export', 'the-events-calendar' ) . ' ' . $modifier ;
}

add_filter( 'tribe_events_ical_export_text','ef_tribe_events_ical_export_text' );

function setMessageBySession($messageKey,$messageStatus,$message)
{
  session_start();
  $_SESSION[$messageKey][$messageStatus] = $message;
}
function getMessageBySession($messageKey)
{
  $message = (isset($_SESSION[$messageKey]))?$_SESSION[$messageKey]:"";
  unset($_SESSION[$messageKey]);
  return $message;
}

function ef_tribe_events_notices($html, $notices)
{
    if (array_key_exists('event-past', $notices))
    {
        $html = '';
    }

    return $html;
}
add_filter('tribe_the_notices','ef_tribe_events_notices', 10 ,2);

function ef_tribe_is_past_event( $event_id = null ){
    if ( ! tribe_is_event( $event_id ) ){
        return false;
    }
    $end_date = get_post_meta($event_id,'_EventEndDate',true);
    $d = new DateTime($end_date);
    $formatted_date = $d->format('Y-m-d');
    return date('Y-m-d') > $formatted_date;
}
/** custom search in admin **/
/*
function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {
        $join .=" LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
//add_filter('posts_join', 'cf_search_join' );

function cf_search_where( $where ) {

  global $pagenow, $wpdb;

   if ( is_search() ) {
      $where = preg_replace(
        "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
        "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

   return $where;
   }
 //add_filter( 'posts_where', 'cf_search_where' );


 function cf_search_distinct( $where ) {
   global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
   }

   return $where;
 }
 //add_filter( 'posts_distinct', 'cf_search_distinct' );
 *
 */

function stop_rss_feeds() {
  return '';
}
add_filter('the_generator', 'stop_rss_feeds', 999);


function get_term_name_by_lang($term_id,$lang)
{
    $term = get_term($term_id);
    if($lang == "ar")
    {
        if($term->name_ar != '')
            return $term->name_ar;
        else
            return $term->name;
    }
    return $term->name;
}

function humanFileSize($size,$unit="") {
  if( (!$unit && $size >= 1<<30) || $unit == "GB")
    return number_format($size/(1<<30),2)." GB";
  if( (!$unit && $size >= 1<<20) || $unit == "MB")
    return number_format($size/(1<<20),2)." MB";
  if( (!$unit && $size >= 1<<10) || $unit == "KB")
    return number_format($size/(1<<10),2)." KB";
  return number_format($size)." bytes";
}

// Add a filter to remove srcset attribute from generated <img> tag
add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );

# Correct SSL Bug
function correct_url_ssl($url)
{
  if( function_exists('is_ssl') && is_ssl() )
  {
    return str_replace('http://', 'https://', $url);
  }
  return $url;
}
add_filter('wp_get_attachment_url', 'correct_url_ssl');
add_filter('get_the_guid','correct_url_ssl');

// Added to extend allowed files types in Media upload
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {

// Add *.json files to Media upload
$existing_mimes['json'] = 'application/json';

// Add *.xml files to Media upload
$existing_mimes['xml'] = 'text/xml';

// Add *.html files to Media upload
$existing_mimes['html'] = 'text/html';

return $existing_mimes;
}

function ef_tribe_ical_feed_month_view_query_args($args, $month)
{
  $start_date = new DateTime($month);
  $end_date = new DateTime($month);
  $args['start_date'] = $start_date->format('Y-m-01');
  $args['end_date'] = $end_date->format('Y-m-t');
  return $args;
}
add_filter( 'tribe_ical_feed_month_view_query_args', 'ef_tribe_ical_feed_month_view_query_args',10,2 );

// deprecated user ef_strip_tags instead
function strip_js_tags($input)
{
   //$input = preg_replace("/<\040*?(script)[^>]*>.*?<(\040*?)\/(\040*?)(script)(\040*?)>/", "", $input) ;
   //$input = preg_replace("/<\040*?(script)[^>]*>.*?<(\040*?)\/(\040*?)(script)(\040*?)/", "", $input) ;
   //$input = preg_replace("/<\040*?(script)[^>]*>.*?/", "", $input) ;
   $input = ef_strip_tags($input);
   return $input;
}
function ef_pre_comment_on_post($commentdata)
{
  $commentdata['comment_content'] = strip_js_tags($commentdata['comment_content']);
  return $commentdata;
}
add_filter( 'preprocess_comment', 'ef_pre_comment_on_post',10,1 );

function ef_set_post_name($data) {
    if($data['post_status'] == 'pending')
    {
        $guid = $data['guid'];
        $post_id  = substr($guid, strpos($guid, 'p=') +2);
        if($guid == '')
        {
          $post_id = 0;
        }

        if($post_id == 0)
        {
          $suggested_post_name = str_replace(' ', '-', strtolower($data['post_title']));
          if(strlen($data['post_title']) !== mb_strlen($data['post_title'],'UTF-8'))
          {
            $suggested_post_name = substr($suggested_post_name,0,70);
          }

          $data['post_name'] = urlencode(wp_unique_post_slug(generatePostName($suggested_post_name),
                          $post_id, 'publish', $data['post_type'], 0));

          //check unique postname
          global $wpdb;
          $check_sql = "SELECT count(*) as total_count FROM $wpdb->posts WHERE post_name like %s AND post_type = %s";
          $post_name_check = $wpdb->get_results( $wpdb->prepare( $check_sql, '%'.$data['post_name'].'%', $data['post_type'] ) );
          if(sizeof($post_name_check) > 0)
          {
            //edit title
            if($post_name_check[0]->total_count > 0)
            {
              $data['post_name'] = $data['post_name'].'-'.$post_name_check[0]->total_count;
            }
          }
        }
    }

    return $data;
}
add_filter('wp_insert_post_data', 'ef_set_post_name', 10, 1);

//conditions to meet wp post name criteria
function generatePostName($name)
{
    $name = str_replace(".", "-", $name);
    $name = str_replace("(", "", $name);
    $name = str_replace(")", "", $name);
    $name = str_replace("*", "", $name);
    $name = str_replace(":", "", $name);
    $name = str_replace("'", "", $name);
    $name = str_replace("+", "", $name);
    $name = str_replace("/", "", $name);
    $name = str_replace("~", "", $name);
    $name = preg_replace("/[\/\&%#\$]/", "", $name);
    //$name = preg_replace('/[^أ-يA-Za-z0-9\-]/', '', $name);
    $name = preg_replace('/-+/', '-', $name);
    $name = rtrim($name, '-');
    return $name;
}

//hide items
$arr_hide_contributions = array('products','events','news','open-datasets','success-stories','wiki');

function ef_hide_contributions_list($item,$sub_item,$selected_item)
{
    return '';
}
foreach($arr_hide_contributions as $arr_hide_contribution)
{
    add_filter('bp_get_options_nav_'.$arr_hide_contribution,'ef_hide_contributions_list',10,3);
}

//show contribution item in profile menu
function ef_profile_menu_active_li($item)
{
    $current_page = strtok($_SERVER["REQUEST_URI"],'?');
    $lang = pll_current_language();
    $checking_page = '/'.$lang.'/members/'.bp_core_get_username(bp_displayed_user_id()).$item;
    if($current_page == $checking_page)
    {
        return true;
    }
    return false;
}

function ef_show_contribution_li()
{
    if(get_current_user_id() == bp_displayed_user_id())
    {
         $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type in ('news','success_story','open_dataset','tribe_events','request_center','expert_thought')) And post.post_author = ".bp_displayed_user_id();
    }else
    {
       $whereCondition = " where (post.post_status = 'publish' and post.post_type in ('news','success_story','open_dataset','tribe_events','request_center','expert_thought')) And post.post_author = ".bp_displayed_user_id();
    }

    global $wpdb;
    $sql = "(SELECT count(*) as counter,post.post_type FROM {$wpdb->prefix}posts as post
          {$whereCondition }
          group by post.post_type
           )";
    $result = $wpdb->get_results($sql);
    $arr_return = array(
      "wiki"=>0,
      "open_dataset"=>0,
      "success_story"=>0,
      "tribe_events"=>0,
      "request_center"=>0,
      "expert_thought"=>0,
      "news"=>0);
    for($i = 0; $i < sizeof($result); $i++)
    {
        $arr_return[$result[$i]->post_type] = $result[$i]->counter;
    }

    //retrieve fosspedia add/edits
    $args = array(
        'author' => bp_displayed_user_id()
    );
    $total_count = get_user_fosspedia_edits_count($args) + get_user_fosspedia_count($args);
    $arr_return['wiki'] = $total_count;

    //retrieve products add/edits
    $xprofile_id = bp_displayed_user_id();
    if($xprofile_id == get_current_user_id())
    {
      $args = array(
          "post_status" => "",
          "post_type" => "product",
          "current_lang" => pll_current_language(),
          "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
          "offest" => 0,
      );
    }else
    {
      $args = array(
          "post_status" => "publish",
          "post_type" => "product",
          "current_lang" => pll_current_language(),
          "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
          "offest" => 0,
      );
    }
    $count = count_added_products_by_user($args, $xprofile_id);
    $total_count = count($count);

    //edits
    $args = array(
        "post_status" => "publish",
        "post_type" => "product",
        "current_lang" => pll_current_language(),
        "foriegn_lang" => (pll_current_language() == "ar")?"en":"ar",
        "offest" => 0,
    );
    $count = display_contributed_products_by_user($args, $xprofile_id);
    $count = strval(count($count));
    $total_count = $total_count + $count;
    $arr_return['product'] = $total_count;

    //list of responses by user
    if($xprofile_id == get_current_user_id())
    {
      $args = array(
          'post_type' => 'request_center',
          'author' => bp_displayed_user_id()
      );
      $responses_count = count(get_user_request_center_responses_count($args));
      $arr_return['request_center'] = $arr_return['request_center'] + $responses_count;
    }

    //edits in open dataset
    $args = array(
        "user_id" => $xprofile_id
    );
    if($xprofile_id != get_current_user_id())
    {
      $args['post_status'] = "publish";
    }  else {
      $args['post_status'] = "";
    }
    $editsCount = count(get_user_contributed_open_dataset_count($args));
    $arr_return['open_dataset'] = $arr_return['open_dataset'] + $editsCount;

    $arr_return['documents'] = count(get_user_total_published_documents(bp_displayed_user_id()));

    $arr_return['total_count'] = $arr_return['product'] + $arr_return['wiki']
            + $arr_return['tribe_events'] + $arr_return['success_story'] + $arr_return['open_dataset']
            + $arr_return['news'] + $arr_return['request_center'] + $arr_return['expert_thought'] + $arr_return['documents'];
    return $arr_return;
}

function sanitize_request_from_tags() {

  if (!is_admin()) {
    if(!preg_match("/(en|ar)\/(wiki)\//",$_SERVER["REQUEST_URI"]))
    {
      array_walk_recursive($_GET, 'ef_strip_tags');
      array_walk_recursive($_POST, 'ef_strip_tags');
    }
  }
}

add_action('init','sanitize_request_from_tags');

function ef_strip_tags(&$input)
{
   $input = htmlspecialchars($input, ENT_QUOTES);
   return $input;
}


function ef_get_id_from_url($url, $word_to_remove)
{
  $id = str_replace("/","",substr($url, strpos($url, $word_to_remove) + strlen($word_to_remove)));
  return $id;
}

function efGetValueFromUrlByKey($key,$url = "")
{
  $value = false;
  $url = ($url != "")?$url:$_SERVER['REQUEST_URI'];
  $pathDetails = explode("/", $url);
        if($position = array_search($key, $pathDetails))
        {
          if(isset($pathDetails[$position+1]) && is_numeric(($pathDetails[$position+1])))
          {
            $value = $pathDetails[$position+1];
          }
        }
  return $value;
}

//load system menu
function ef_load_menu_items_by_language()
{
  $lang = pll_current_language();
  global $wpdb;
  $query = "SELECT post_title, post_name FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}postmeta as pmeta on pmeta.post_id = post.ID
          where post_type = 'nav_menu_item'
          and pmeta.meta_key = '_menu_item_url'
          and pmeta.meta_value like '/$lang%'
          order by menu_order asc;";
  $results = $wpdb->get_results($query);
  return $results;
}

function insert_open_datasets_type() {
	load_orm();
	global $wpdb;
	$sql = "SELECT p.ID
					FROM `{$wpdb->prefix}posts` p
					JOIN `{$wpdb->prefix}postmeta` m ON p.ID = m.post_id
					WHERE `post_type` = 'open_dataset'
					AND p.ID NOT IN ( SELECT post_id
														FROM `{$wpdb->prefix}postmeta`
														WHERE `meta_key` = 'dataset_type'
													)
					GROUP BY p.ID";
	$results = $wpdb->get_results($sql);
	foreach ($results as $result) {
		$value = Postmeta::select('meta_value')->where('post_id', '=', $result->ID)->where('meta_key', '=', 'type')->first();
		$meta = new Postmeta();
		$meta->addProductMeta($result->ID, 'dataset_type', $value->meta_value);
		$meta->save();
	}
}
// insert_open_datasets_type();

function insert_open_datasets_language() {
	load_orm();
	global $wpdb;
	$sql = "SELECT p.ID
					FROM `{$wpdb->prefix}posts` p
					JOIN `{$wpdb->prefix}postmeta` m ON p.ID = m.post_id
					WHERE `post_type` = 'open_dataset'
					AND p.ID NOT IN ( SELECT post_id
														FROM `{$wpdb->prefix}postmeta`
														WHERE `meta_key` = 'language'
													)
					GROUP BY p.ID";
	$results = $wpdb->get_results($sql);
	foreach ($results as $result) {
		$dataset = Post::find($result->ID);
		$dataset->add_post_translation($result->ID, 'en', $post_type = "open_dataset");
	}
}
// insert_open_datasets_language();

// add author column to feedback admin list
function feedback_cpt_columns($columns) {

    $new_columns = array( 'author' => __('Author', 'egyptfoss') );

    return array_merge($columns, $new_columns);
}
add_filter('manage_feedback_posts_columns' , 'feedback_cpt_columns');

function removePolyLangMenuItem()
{
  global $wp_admin_bar;
  $wp_admin_bar->remove_node("languages");
}
add_action( 'admin_bar_menu', 'removePolyLangMenuItem', 999 );

function ef_change_seo_title($title)
{
  global $pagename;
  $result_id = efGetValueFromUrlByKey("result");
  if($result_id)
  {
    $result = ef_returnResult($result_id);
    if(is_array($result))
    {
      $title = sprintf(__("I got %s in the %s Quiz on #EgyptFOSS","egyptfoss"), $result['score']."%"
        ,$result['quiz_title']);
    }
  }
  return $title;
}
add_filter('wpseo_title','ef_change_seo_title',100);

function ef_format_comment( $comment, $args, $depth ) {
  $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
?>
		<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment->has_children ? 'parent' : '', $comment ); ?>>
			<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
						<?php printf( '<b class="fn">%s</b>', get_comment_author_link( $comment ) ); ?>
					</div><!-- .comment-author -->

					<div class="comment-metadata">
						<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php
									/* translators: 1: comment date, 2: comment time */
                if( pll_current_language() == 'ar' ) {
									printf( __( '%1$s %2$s' ), mysql2date( 'F j Y', $comment->comment_date ), get_comment_time() );
                }
                else {
									printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() );
                }
								?>
							</time>
						</a>
					</div><!-- .comment-metadata -->

					<?php if ( '0' == $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
					<?php endif; ?>
				</footer><!-- .comment-meta -->

				<div class="comment-content">
					<?php comment_text(); ?>
				</div><!-- .comment-content -->

				<?php
				comment_reply_link( array_merge( $args, array(
					'add_below'   => 'div-comment',
					'depth'       => $depth,
					'max_depth'   => $args['max_depth'],
					'before'      => '<div class="reply">',
					'after'       => '</div>',
          'login_text'  => ''
				) ) );
				?>
			</article><!-- .comment-body -->
<?php
}

/**
 * Shorten string with specific number of characters
 * without cutting words
 *
 * @param type $string
 * @param type $length
 * @param type $post_append
 * @return type
 */
function shorten_string_v2( $string, $length = 10, $post_append = '...' ) {
  // return string itself if not characters will be trimmed
  if( strlen( trim( $string ) ) <= $length ) {
    $shorten_st = $string;
  }
  else {
    // start cutting
    $shorten_st = substr( $string, 0, strrpos( substr( $string, 0, $length ), ' ' ) );

    // add indicator if string has been cutten
    $shorten_st .= $post_append;
  }

  return $shorten_st;
}

function get_permalink_by_lang($post_id, $path){
	$lang = pll_current_language();
	$post = get_post($post_id);
	$slug = $post->post_name;
	return ($slug == '' || $path == '') ? get_post_permalink($post_id) : home_url($lang.$path.$slug);
}

/*$EFBAction = new EFBAction();
$actions = $EFBAction->getActionsBy(array("post_type"=>"news","post_status"=>"publish"));
$actionsAffected = array();
if($actions->first())
{
  foreach($actions as $action)
  {
    $EFBCreditedUserPosts = new EFBCreditedUserPost();
    $isUpdated = $EFBCreditedUserPosts->updateCreditedUserPost(array("user_id"=>1,"post_id"=>1,"action_id"=>$action->id));
    if($isUpdated)
    {
      $actionsAffected = $actionsAffected->push(array("action_id"=>$action->name,"is_point_granted"=>$action->is_points_granted));
    }
  }
  foreach($actionsAffected as $actionAffected)
  {

  }
}*/

function wpdocs_custom_excerpt_length( $length ) {
    return $length * 2;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

function ef_resend_activation($activation_key, $user)
{
  $args = array(
    'tokens' => array(
      'activate.url' => esc_url( trailingslashit( bp_get_activation_page() ) . "{$activation_key}/" ),
      'key'          => $activation_key,
      'user.email'   => $user->user_email,
      'user.id'      => $user->ID,
    ),
  );

  if ( $user->ID ) {
    $to = $user->ID;
  } else {
    $to = array( array( $$user->user_email => $$user->user_login ) );
  }

  bp_send_email( 'core-user-registration', $to, $args );
}

function ef_resend_activation_link()
{
  load_orm();
  $current_user = get_current_user_id();
  $user = get_user_by('ID', $current_user);
  $last_send_activation = get_user_meta($user->ID, "last_resend_activation", true);
  $shouldInvite = false;
  if(!isset($last_send_activation) || $last_send_activation == '')
  {
    $shouldInvite = true;
  }else {
    $time1 = new DateTime($last_send_activation);
    $time2 = new DateTime();
    $interval = $time1->diff($time2);
    if($interval->format('%i') >= 15) // 15mins
    {
      $shouldInvite = true;
    }
  }

  if($shouldInvite)
  {

    $activation_key = get_user_meta($current_user,'activation_key',true);

    //send registration email
    $subject = "[EgyptFOSS] ".__("Activate your account","egyptfoss");
    $btn_address = __("Activate account","egyptfoss");
    $title = __("Activate your account","egyptfoss");

    $activationUrl = esc_url( trailingslashit( bp_get_activation_page() ) . "{$activation_key}/" );

    $msg = sprintf(__("To activate your account, visit the following address","egyptfoss"),$user->user_login);
    if( pll_current_language() == "ar")
      $msg = __("To activate your account, visit the following address","egyptfoss");

    $args = array(
      "title" => $title,
      "message" => $msg,
      "user_name" => $user->user_login,
      "url" => $activationUrl,
      "button_title" => $btn_address
    );

    set_query_var( 'template_inputs', serialize($args));
    ob_start();
    get_template_part( 'mail-templates/activation' );
    $message = ob_get_contents();
    ob_end_clean();

    wp_mail($user->user_email, $subject, $message);

    //save user last resend link
    update_user_meta($user->ID, "last_resend_activation", date('Y-m-d H:i:s'));
    echo 'success';
  }
  else {
    echo "waiting";
  }

  die();
}
add_action('wp_ajax_ef_resend_activation_link', 'ef_resend_activation_link');
add_action('wp_ajax_nopriv_ef_resend_activation_link', 'ef_resend_activation_link');

function shouldDisplayActivationLink()
{
  if(!is_user_logged_in())
    return false;

  $current_user = get_user_by('ID', get_current_user_id() );
  $get_user_meta = get_user_meta($current_user->ID, "registration_data", true);
  $user_meta = maybe_unserialize($get_user_meta);
  //Not social loggedIn user
  if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
    return false;
  }

  $activation_key = get_user_meta($current_user->ID,'activation_key',true);
  if(!isset($activation_key) || empty($activation_key))
    return false;

  // If user status == 0 --  active
  if($current_user->user_status == 0)
    return false;

  return true;
}
function ef_tribe_events_pre_get_posts($query){
  $query->query_vars["author"] = 0;
}
add_action( 'tribe_events_pre_get_posts', 'ef_tribe_events_pre_get_posts',10,1 );

// Change time stamp in BP
function ef_bp_core_current_time($current_time)
{
  $wp_timezone = get_option('timezone_string');
  if($wp_timezone == ""){
    return $current_time;
  }
  $cairoTimezone = new DateTimeZone($wp_timezone);
  $dateTimeGMT = new DateTime("now");
  $dateTimeCairo = new DateTime("now", $cairoTimezone);

  $timeOffset = $cairoTimezone->getOffset($dateTimeGMT);

  try{
    $is_timestamp = is_timestamp($current_time);
    if($is_timestamp) {
      return strtotime("+$timeOffset seconds");
    } else {
      return date('Y-m-d H:i:s');
    }
  }catch(Exception $e)
  {
    return $current_time;
  }

  return $current_time;
}
add_filter('bp_core_current_time','ef_bp_core_current_time',10);

function ef_bp_core_signups_add($signup_id)
{
  $date = new DateTime('now');
  // update registered date
  global $wpdb;
  $wpdb->update($wpdb->prefix.'signups',array( 'registered' => $date->format('Y-m-d H:i:s') ),array( 'signup_id' => $signup_id ));
  return $signup_id;
}
add_filter('bp_core_signups_add','ef_bp_core_signups_add',10);

function ef_bp_core_signups_validate($activated)
{
  // Update time
  if($activated)
  {
    // Grab the key (the old way).
    $key = isset( $_GET['key'] ) ? $_GET['key'] : '';

    // Grab the key (the new way).
    if ( empty( $key ) ) {
      $key = bp_current_action();
    }
    global $wpdb;
    // return $activated without any changes
    if ( empty( $key ) ) {
			return $activated;
		}

    //update activated date to timezone
    $date = new DateTime('now');
    $activated = $wpdb->update(
      // Signups table.
      buddypress()->members->table_name_signups,
      array(
        'active' => 1,
        'activated' => $date->format('Y-m-d H:i:s'),
      ),
      array(
        'activation_key' => $key,
      ),
      // Data sanitization format.
      array(
        '%d',
        '%s',
      ),
      // WHERE sanitization format.
      array(
        '%s',
      )
    );
  }

  return $activated;
}
add_filter('bp_core_signups_validate','ef_bp_core_signups_validate');

function is_timestamp($timestamp) {
  return ( 1 === preg_match( '~^[1-9][0-9]*$~', $timestamp ) );
}

add_filter( 'home_url', 'override_home_url', 10, 2 );
home_url('');
/**
 *
 * @param string $home_url
 * @return string
 */
function override_home_url( $home_url, $path ) {
  // after polylang plugin loaded
  if(function_exists( 'pll_current_language' ) ) {
    $needle = '/' . pll_current_language();
    $has_language = (($temp = strlen($home_url) - strlen($needle)) >= 0 && strpos($home_url, $needle, $temp) !== false);

    if( !$has_language && $path === "/" ) {
      $home_url = $home_url . pll_current_language() . '/';
    }
  }

  return $home_url ;
}

//add_filter( 'tribe_get_events_link', 'override_tribe_events_link' );
//
///**
// *
// * @param string $link
// * @return string
// */
//function override_tribe_events_link( $link ) {
//  $events_slug .= trailingslashit( sanitize_title( Tribe__Settings_Manager::get_option( 'eventsSlug', 'events' ) ) );
//  $link = home_url('/') . $events_slug;
//
//  return $link;
//}

add_filter( 'tribe_events_rewrite_rules', 'override_tribe_events_rewrite_rules' );

/**
 *
 * @param type $rules
 */
function override_tribe_events_rewrite_rules( $rules ) {

  $updated_rules = array();

  foreach( $rules as $k => $v ) {
    // only trace events links
    if( substr( $k, 0, strlen( '(?:event' )) === '(?:event' ) {
      $updated_rules[ 'ar/' . $k ] = $v;
      $updated_rules[ 'en/' . $k ] = $v;
    }
    else {
      $updated_rules[ $k ] = $v;
    }
  }

  return $updated_rules;
}

/**
 * retrieve count of posts that match posttype & taxonomy name & term id
 *
 * @param type $post_type
 * @param type $tax_name
 * @param type $term_id
 */
function ef_get_category_posts_count( $post_type, $tax_name = NULL, $term_id = NULL ) {

    // prepare args
    $args = array(
      'post_type'       => $post_type,
      'post_status'     => 'publish',
      'posts_per_page'  => -1
    );

    // if taxonomy search is provided
    if( $tax_name && $term_id ) {
      $args['tax_query'] = array(
        array(
          'taxonomy' => $tax_name,
          'field' => 'id',
          'terms' => array( $term_id )
        )
      );
    }

    $query = new WP_Query( $args);

    return (int) $query->post_count;
}

add_action( 'add_meta_boxes', 'ef_populate_translated_meta_boxes', 10, 2 );

/**
 * pre-populate translted post terms with original post terms
 *
 * @param type $post_type
 * @param type $post
 */
function ef_populate_translated_meta_boxes( $post_type, $post ) {
  // check if we are in add translated post page
  if ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['from_post'], $_GET['new_lang'] ) && $post_type == 'product' ) {

    // post taxonomies to be copied
    $keys = array(
      'type', 'technology', 'platform', 'license', 'interest', 'industry'
    );

    // original post ID
    $from_post_id = (int) $_GET['from_post'];

    // and now copy / synchronize
		foreach ( $keys as $key ) {
      $new_terms = array();

      // get original post terms
      $from_post_terms = get_the_terms( $from_post_id, $key );

      if( $from_post_terms ) {
        foreach( $from_post_terms as $term ) {
          $new_terms[] = $term->term_id;
        }
      }

      // replace terms in translation
      wp_set_object_terms( $post->ID, $new_terms, $key );
    }
  }
}

/**
 * Disable all feedback posttype actions
 *
 * @global type $submenu
 */
function ef_handle_feedback_post_type() {
    // Hide sidebar link
    global $submenu;

    unset( $submenu['edit.php?post_type=feedback'][10] );

    $current_screen = get_current_screen();

    if( empty( $current_screen ) ) return;

    // Hide link on listing page
    if ( in_array( $current_screen->id, array( 'feedback', 'edit-feedback' ) ) ) {
        echo '<style type="text/css">
                .row-actions .inline, .row-actions .view, .page-title-action, #edit-slug-box, #side-sortables { display:none; }
                .page-title strong a {visibility: visible;}
                .page-title strong {visibility: hidden;}
            </style>';
    }
}

add_action('admin_menu', 'ef_handle_feedback_post_type');
add_action( 'current_screen', 'ef_handle_feedback_post_type' );

function ef_remove_menus(){
  remove_menu_page( 'edit.php' ); //Posts
}
add_action( 'admin_menu', 'ef_remove_menus' );

function ef_bp_get_user_last_activity($activity, $user_id)
{
  global $wpdb;
  $sql = "select date_recorded from {$wpdb->prefix}bp_activity where user_id = {$user_id} order by date_recorded desc limit 0,1";
  $result = $wpdb->get_row($sql);
  if($result)
  {
    return $result->date_recorded;
  }

  return $activity;
}
add_filter('bp_get_user_last_activity','ef_bp_get_user_last_activity',10,2);

add_filter( 'manage_feedback_posts_columns', 'ef_add_feedback_custom_columns', 100 ) ;

/**
 * Add -section- column header to feedback admin list
 *
 * @param type $columns
 * @return type
 */
function ef_add_feedback_custom_columns( $columns ) {

  // remove not needed columns
  unset( $columns['comments'] );

  // add -section- column at the center of columns
  $new_columns = array_slice( $columns, 0, 2, true) +
    array( "section" => __( 'Section' ) ) +
    array_slice( $columns, 2, count( $columns ) - 1, true) ;

	return $new_columns;
}

add_action( 'manage_feedback_posts_custom_column', 'ef_manage_feedback_custom_column', 10, 2 );

/**
 * Populate -section- column with appropriate value
 *
 * @global type $ef_sections
 * @param type $column
 * @param type $post_id
 */
function ef_manage_feedback_custom_column( $column, $post_id ) {
  global $ef_sections;

  // -section- column
  if( $column == 'section' ) {
    $section = get_post_meta( $post_id , 'sections' , TRUE );

    if( isset( $ef_sections[ $section ] ) ) {
      echo $ef_sections[ $section ];
    }
    else {
      _e( 'General' );
    }
  }
}

add_filter( 'bulk_actions-edit-feedback', 'ef_remove_feedback_edit_bulk_action' );

/**
 * Remove -edit- option from bulk actions
 *
 * @param type $actions
 * @return type
 */
function ef_remove_feedback_edit_bulk_action( $actions ){
    unset( $actions[ 'edit' ] );

    return $actions;
}

add_filter( 'wp_die_handler', function() { return '_efb_default_wp_die_handler'; }, 20 );

/**
 * Display wordpress errors in our error template
 *
 * @param type $message
 * @param type $title
 * @param type $args
 */
function _efb_default_wp_die_handler( $message, $title = '', $args = array() ) {
  $defaults = array( 'response' => 500 );
	$r = wp_parse_args($args, $defaults);

	$have_gettext = function_exists('__');

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
		if ( empty( $title ) ) {
			$error_data = $message->get_error_data();
			if ( is_array( $error_data ) && isset( $error_data['title'] ) )
				$title = $error_data['title'];
		}
		$errors = $message->get_error_messages();
		switch ( count( $errors ) ) {
		case 0 :
			$message = '';
			break;
		case 1 :
			$message = "<p>{$errors[0]}</p>";
			break;
		default :
			$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
			break;
		}
	} elseif ( is_string( $message ) ) {
		$message = "<p>$message</p>";
	}

	if ( isset( $r['back_link'] ) && $r['back_link'] ) {
		$back_text = $have_gettext? __('&laquo; Back') : '&laquo; Back';
		$message .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
	}

	if ( ! did_action( 'admin_head' ) ) :
		if ( !headers_sent() ) {
			status_header( $r['response'] );
			nocache_headers();
			header( 'Content-Type: text/html; charset=utf-8' );
		}

		if ( empty($title) )
			$title = $have_gettext ? __('WordPress &rsaquo; Error') : 'WordPress &rsaquo; Error';

		$text_direction = 'ltr';
		if ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] )
			$text_direction = 'rtl';
		elseif ( function_exists( 'is_rtl' ) && is_rtl() )
			$text_direction = 'rtl';

  get_header(); ?>
  <?php endif; ?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<section class="error-404 not-found">
        <div class="container">
          <div class="row">
            <div class="col-md-12">
              <div class="page-content">
                <div class="empty-box">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/500.svg" alt="error" class="error-icon">
                   <h1 class="entry-title color-primary"><?php esc_html_e( 'Something went wrong.', 'egyptfoss' ); ?></h1>
                  <div class="error-page-search">
                    <h3><?php echo $message; ?></h3>
                    <?php if( !empty( $_SERVER['HTTP_REFERER'] ) ): ?>
                      <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                        <?php if( pll_current_language() != 'ar'): ?><i class="fa fa-arrow-left"></i> <?php endif; ?>
                        <?php _e( 'Go Back', 'egyptfoss' ); ?>
                        <?php if( pll_current_language() == 'ar'): ?> <i class="fa fa-arrow-left"></i><?php endif; ?>
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
                </div><!-- .page-content -->
            </div>
          </div>
        </div>
			</section><!-- .error-404 -->
		</main><!-- #main -->
	</div><!-- #primary -->
  <?php
  get_footer();
	die();
}

function ef_get_comment_author_link( $return, $author, $comment_ID = 0 ) {
	$comment = get_comment( $comment_ID );
	$url     = get_comment_author_url( $comment );
	$author  = get_comment_author( $comment );

	if ( empty( $url ) || 'http://' == $url )
		$return = $author;
	else
		$return = "<a href='$url' rel='nofollow' class='url'>$author</a>";

	return $return;
}
add_filter( 'get_comment_author_link', 'ef_get_comment_author_link', 3 );

function ef_bp_core_signup_after_activate( $signup_ids ) {
  global $wpdb;

  foreach( $signup_ids as $signup_id ) {

    $signups_table = buddypress()->members->table_name_signups;
    $signup        = $wpdb->get_row( $wpdb->prepare( "SELECT user_login FROM {$signups_table} WHERE signup_id = %d", $signup_id ) );
    $user_id = bp_core_get_userid( $signup->user_login );
    $get_user_meta = get_user_meta($user_id, "registration_data", true);
    $user_meta = unserialize($get_user_meta);

    // save users content to marmotta
    saveUserContent( $user_id, bp_core_get_user_displayname( $user_id ), '', $user_meta['type'] );
  }
}

add_action( 'bp_core_signup_after_activate', 'ef_bp_core_signup_after_activate' );

function disable_google_maps_from_events_plugin(){
  return "";
}
add_filter( 'tribe_events_google_maps_api', 'disable_google_maps_from_events_plugin',1,1 );

function ef_custom_posts_support_author() {
  add_post_type_support( 'expert_thought', 'author' );
  add_post_type_support( 'success_story', 'author' );
  add_post_type_support( 'request_center', 'author' );
  add_post_type_support( 'product', 'author' );
  add_post_type_support( 'open_dataset', 'author' );
  add_post_type_support( 'news', 'author' );
  add_post_type_support( 'service', 'author' );
}
add_action('init', 'ef_custom_posts_support_author');

function is_testing_environment() {
  return (strlen( strstr( $_SERVER['HTTP_USER_AGENT'], "PhantomJS" ) ) > 0);
}

function ef_acf_set_google_api_key( $api ){
	global $google_maps_key;

	$api['key'] = $google_maps_key;

	return $api;
}

add_filter('acf/fields/google_map/api', 'ef_acf_set_google_api_key');

/**
 * Prevent access to admin bages page
 *
 * @global type $wpdb
 * @return type
 */
function prevent_admin_showing_badges_page() {
  global $wpdb;

  $regex = '/^\/(ar|en)\/members\/(?P<nicename>(.)+)\/badges[\/]?$/';

  if ( preg_match( $regex, $_SERVER['REQUEST_URI'], $matches ) )
  {
    if ( ! $user = $wpdb->get_row( $wpdb->prepare(
        "SELECT `ID` FROM $wpdb->users WHERE `user_nicename` = %s", $matches['nicename']
    ) ) ) return;

    $user = get_userdata( $user->ID );

    if ( in_array( 'administrator', (array) $user->roles ) ) {
        include( get_query_template( '404' ) );
        exit;
    }
  }
}

add_action( 'init', 'prevent_admin_showing_badges_page' );

/**
 * Save linkedin profile url after login using linkedin
 *
 * @param type $is_new_user
 * @param type $user_id
 * @param type $provider
 * @param type $adapter
 * @param type $hybridauth_user_profile
 */
function save_linkedin_profile_url( $is_new_user, $user_id, $provider, $adapter, $hybridauth_user_profile ) {
  if( $provider == 'LinkedIn' ) {
    $profile_url = $hybridauth_user_profile->profileURL;
    $r_data = get_user_meta( $user_id, 'registration_data', true );
    $registeration_data = unserialize( $r_data );
    $registeration_data['linkedin_url'] = $profile_url;
    update_user_meta( $user_id, 'registration_data', serialize( $registeration_data ) );
  }
}

add_action( 'wsl_process_login_update_wsl_user_data_start', 'save_linkedin_profile_url', 10, 5 );

add_action( 'tribe_events_pre_get_posts', 'replace_tribe_search_dashes' );

/**
 * Fix dashes search problem in wordpress query with events
 *
 * @param type $query
 * @return type
 */
function replace_tribe_search_dashes( $query ) {
  $query->query_vars['s'] = str_replace( array( '–', '-' ), ' ', $query->query_vars['s'] );

  return $query;
}


// Product sub-categories

add_filter('acf/fields/taxonomy/wp_list_categories', 'ef_override_list_categories', 10, 2);

function ef_override_list_categories( $args, $field ) {
  if( in_array( $args['taxonomy'], array( 'industry', 'type', 'platform', 'license' ) ) ) {
    $args['walker'] = new ef_industry_field_walker( $field );
  }

    return $args;
}

class ef_industry_field_walker extends Walker
{
	// vars
	var $field = null,
		$tree_type = 'category',
		$db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );

	// construct
	function __construct( $field ) {
		$this->field = $field;
	}

	// start_el
	function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0) {
		// vars
		$selected = in_array( $term->term_id, $this->field['value'] );

		if( $this->field['field_type'] == 'checkbox' ) {
			$output .= '<li><label class="selectit"><input type="checkbox" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checked"' : '') . ' /> ' . $term->name . '</label>';
		}
		elseif( $this->field['field_type'] == 'radio' ) {
			$output .= '<li><label class="selectit"><input type="radio" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checkbox"' : '') . ' /> ' . $term->name . '</label>';
		}
		elseif( $this->field['field_type'] == 'select' ) {
			$indent = str_repeat("&mdash; ", $depth);
      $ar_name = empty($term->name_ar) ? '' : ' - ' . $term->name_ar;
			$output .= '<option value="' . $term->term_id . '" ' . ($selected ? 'selected="selected"' : '') . ( ( !$term->parent && $term->taxonomy == 'industry' ) ? 'disabled': '') . '>' . $indent . $term->name  . $ar_name . '</option>';
		}
	}

	//end_el
	function end_el( &$output, $term, $depth = 0, $args = array() ) {
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) )
		{
			$output .= '</li>';
		}

		$output .= "\n";
	}

	// start_lvl
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		// wrap element
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) )
		{
			$output .= '<ul class="children">' . "\n";
		}
	}

	// end_lvl
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		// wrap element
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) ) {
			$output .= '</ul>' . "\n";
		}
	}
}

add_filter( 'taxonomy_parent_dropdown_args', 'ef_override_parent_taxonomy_field', 10, 2 );

function ef_override_parent_taxonomy_field( $dropdown_args, $taxonomy ) {
  if( $taxonomy == 'industry' ) {
    $dropdown_args['depth'] = 1;

    if( isset( $dropdown_args['exclude_tree'] ) ) {
      $children = get_terms( $taxonomy, array('parent' => $dropdown_args['exclude_tree'],'hide_empty' => false) );

      if($children) {
          $dropdown_args['child_of'] = -1;
          $dropdown_args['show_option_none'] = 'Already parent category';
      }
    }
  }

  return $dropdown_args;
}

function ef_custom_menu_order($menu_ord) {
     if (!$menu_ord) return true;
     return array(
      'index.php', // this represents the dashboard link
      'separator1', // this represents the dashboard link
      'edit.php?post_type=news',
      'edit.php?post_type=tribe_events', // this is a custom post type menu
      'edit.php?post_type=product',
      'edit.php?post_type=open_dataset',
      'edit.php?post_type=service',
      'edit.php?post_type=request_center',
      'ef_mlw_list_quizzes',
      'edit.php?post_type=success_story',
      'edit.php?post_type=expert_thought',
      'separator2', // this represents the dashboard link
      'taxonomies_page',
      'edit.php?post_type=feedback',
      'efb_badges',
      'ef_list_api_keys',
      'users.php',
      'separator-buddypress', // this represents the dashboard link
  );
 }
 add_filter('custom_menu_order', 'ef_custom_menu_order');
 add_filter('menu_order', 'ef_custom_menu_order');

 add_filter( 'acf/load_value/name=description', 'ef_decode_product_description', 3, 10 );

 function ef_decode_product_description( $value, $post_id, $field ) {
   if( $field['name'] == 'description' ) {
     // special case for this field
    add_filter( 'esc_textarea', 'ef_decode_description_value', 3, 10 );
   }
   return $value;
 }

 function ef_decode_description_value( $safe_text, $text ) {
   // decode string entities
    $safe_text = html_entity_decode( $text );
    // it's not needed anymore
    remove_filter( 'esc_textarea', 'ef_decode_description_value', 3, 10 );
    return $safe_text;
 }

 /**
  *
  * @global type $extensions
  * @param type $attach_id
  * @return type
  */
 function  ef_get_attach_info( $attach_id ) {
    $attach_info  = NULL;
    $attach_path  = get_attached_file( $attach_id );
    $attach_url   = wp_get_attachment_url( $attach_id );
    $bytes        = filesize( $attach_path );
    $attach_size  = humanFileSize( $bytes );
    $attach_ext   = pathinfo($attach_path, PATHINFO_EXTENSION);

    //validate extension
    global $extensions;
    if( in_array( strtolower( $attach_ext ), $extensions ) ) {
      //load attachament
      $attach = get_post( $attach_id );

      $attach_title = $attach->post_title;
      $attach_name = (strlen($attach_title) > 28) ? substr($attach_title,0,25).'...' : $attach_title;

      $attach_info  = array(
        'id'      =>  $attach_id,
        'title'   =>  $attach_title,
        'name'    =>  $attach_name,
        'ext'     =>  $attach_ext,
        'url'     =>  $attach_url,
        'size'    =>  $attach_size,
        'bytes'   =>  $bytes
      );
    }

    return $attach_info;
}

 add_filter( 'wsl_render_auth_widget_alter_assets_base_url', 'ef_change_wp_social_login_icon' );

 function ef_change_wp_social_login_icon() {
   return get_template_directory_uri() . '/img/wp-social-login/';
 }

 add_filter( 'user_has_cap', 'ef_extend_user_caps' );

 function ef_extend_user_caps( $caps ) {
    $caps['unfiltered_upload'] = true;

    return $caps;
 }

  function ef_filter_plugins_update( $value ) {
    $disable_plugins = array( 'newsletter/plugin.php', 'quiz-master-next/mlw_quizmaster2.php', 'the-events-calendar/the-events-calendar.php' );

    foreach( $disable_plugins as $plugin ) {
      if( isset( $value->response[$plugin] ) ) {
        unset( $value->response[$plugin] );
      }
    }

    return $value;
  }

 add_filter( 'site_transient_update_plugins', 'ef_filter_plugins_update' );

 function ef_modify_feedback_views( $views ) {
   if( isset( $views[ 'pending' ] ) ) {
      unset( $views[ 'pending' ] );
    }

    return $views;
 }
 add_filter( 'views_edit-feedback', 'ef_modify_feedback_views' );

 function get_bp_signup_telephone_number_value() {
 	$value = '';
 	if ( isset( $_POST['signup_telephone_number'] ) )
 		$value = $_POST['signup_telephone_number'];

 	/**
 	 * Filters the email address submitted during signup.
 	 *
 	 * @since 1.1.0
 	 *
 	 * @param string $value Email address submitted during signup.
 	 */
 	return apply_filters( 'bp_get_signup_telephone_number_value', $value );
 }

 function bp_signup_telephone_number_value() {
 	echo get_bp_signup_telephone_number_value();
 }


// hook when user registers
add_action( 'user_register', 'ef_registration_save', 10, 1 );

function ef_registration_save( $user_id ) {

    // insert meta that user not logged in first time
    update_user_meta($user_id, 'ef_first_login', '1');
}

// hook when user logs in
add_action('wp_login', 'ef_wp_login', 10, 2);

function ef_wp_login($user_login, $user) {

    $user_id = $user->ID;
    // getting prev. saved meta
    $first_login = get_user_meta($user_id, 'ef_first_login', true);
    // if first time login
    if( $first_login == '1' ) {
        // update meta after first login
        update_user_meta($user_id, 'ef_first_login', '0');
        // redirect to given URL
        wp_redirect( get_current_lang_page_by_template('page-add-user-location.php') );
        exit;
    }
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );

function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
  $post = get_post( $post_id );
  if( $post->post_type == 'partner' )
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
  return $html;
}

// to fix facebook scopes issue.
function wsl_change_default_permissons( $provider_scope, $provider ) {
  if( 'facebook' == strtolower( $provider ) ) {
    $provider_scope = 'email, public_profile';
  }

  return $provider_scope;
}

add_filter( 'wsl_hook_alter_provider_scope', 'wsl_change_default_permissons', 10, 2 );

add_action('init', 'redirect_activation_url');

function redirect_activation_url() {
  $matching = preg_match('/\/(en|ar)\/activate\/(.+)[\/]*/', $_SERVER[REQUEST_URI], $matches);
  if( $matching && isset( $matches[2] ) && strpos( $matches[2], 'key=') === false && strpos( $matches[2], 'activated=') === false ) {
    $activation_key = rtrim($matches[2], '/');
    $activation_url = str_replace( '/' .$matches[2], "?key=$activation_key",  $matches[0] );
    header ("Location: $activation_url");
    exit;
  }
}

function setXprofileData() {
  if( is_user_logged_in() && !bp_has_profile() ) {
    $user_id =  get_current_user_id();
    $userdata = get_userdata( $user_id );
    $name = $userdata->display_name;
		xprofile_set_field_data( 1, $user_id, $name, false );
  }
}

add_action( 'init', 'setXprofileData' );
