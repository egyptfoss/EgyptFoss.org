<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package egyptfoss
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width,minimum-scale=1">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
  <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="manifest" href="/manifest.json">
  <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#48ab48">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="msapplication-TileImage" content="/mstile-144x144.png">
  <meta name="theme-color" content="#49aa32">
  <?php wp_head(); ?>
</head>
<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) { ?>
  <script>location.href='<?php echo home_url(pll_current_language()."/login?redirected=registered");?>'</script>
<?php } ?>
<body <?php body_class(); ?>>
  <div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#content">
    <?php esc_html_e( 'Skip to content', 'egyptfoss' ); ?>
    </a>

    <div class="top-bar">
      <?php if(shouldDisplayActivationLink()) { ?>
      <div class="alert alert-warning verify">
          <p class="resend_email_text"> 
           <i class="fa fa-warning"></i>
            <?php echo sprintf(__("Please verify your email address. If you didn't receive the verification email, we can ","egyptfoss"))
            ."<a href='#' class='alert-link' id='resend_action_email'>".__("resend it","egyptfoss")."</a>"; ?>
          </p>
      </div>
      <?php } ?>
      <div class="container">
        <div class="row">
            <?php include( ABSPATH . 'system_data.php' ); ?>
            <div class="top-nav lfloat">
              <ul>
                <?php foreach($ef_top_nav as $nav) { ?>
                <li>
                  <a href="/<?php echo pll_current_language().$nav['url']; ?>"><?php _e($nav['name'],"egyptfoss"); ?></a>
                </li>
                <?php } ?>
              </ul>
          </div>
          <?php if ( $user_ID ) : ?>
          <?php	global $user_identity;?>
          <div class="btn-group login-box rfloat">
            <a type="button" href="<?php bp_loggedinuser_link() ?>activity" class="btn btn-light">
            <?php
              echo get_avatar( bp_loggedin_user_id(), 28 );
                // echo bp_core_fetch_avatar( array(
                // 	'item_id' => bp_loggedin_user_id(),
                // 	'width' => 28,
                // 	'height' => 28,
                // 	'class' => 'top-bar-avatar',
                // 	)
                // );
                $lang = get_locale();
                if($lang == 'ar') {
                    global $ar_sub_types;
                    $sub_types = $ar_sub_types;
                } else {
                    global $en_sub_types;
                    $sub_types = $en_sub_types;
                }
                ?>

                <?php echo $user_identity; ?>
            </a>
  <button type="button" class="btn btn-light dropdown-toggle" id="user-nav2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
    <i class="fa fa-angle-down"></i>
    <span class="sr-only"></span>
  </button>
  	<ul class="login-sub" aria-labelledby="user-nav2">
          <li class="my-data-box clearfix">
            <div class="my-avatar lfloat">
              <?php
                echo get_avatar( bp_loggedin_user_id(), 50 );
                // echo bp_core_fetch_avatar( array(
                // 	'item_id' => bp_loggedin_user_id(),
                // 	'width' => 28,
                // 	'height' => 28,
                // 	'class' => 'top-bar-avatar',
                // 	)
                // );
                $current_user = wp_get_current_user();
                $user_data = get_registration_data(bp_loggedin_user_id());
                $no_subtype = ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? '' : 'no-user-type');
                ?>
            </div>
            <ul class="user-menu-inner lfloat <?php echo $no_subtype; ?>">
                <li><?php echo $current_user->display_name; ?></li>
                <li class="user-subtype"><?php echo ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $sub_types[$user_data['sub_type']] : ''); ?></li>
            </ul>

          </li>
          <!--<li>
            <a href="<?php bp_loggedinuser_link() ?>activity"><?php echo _x( 'Activity', 'Profile activity screen nav', 'buddypress' ); ?></a>
          </li>
          <li>
            <a href="<?php bp_loggedinuser_link() ?>about" class="sm-ltr"><?php echo __('About','egyptfoss') ?></a>
          </li>-->
          <li>
              <a href="<?php bp_loggedinuser_link() ?>about" class="sm-ltr"><?php echo ucwords(__('profile','egyptfoss')) ?></a>
          </li>          
          <li>
            <a href="<?php bp_loggedinuser_link() ?>contributions"><?php _e('Contributions','egyptfoss') ?></a>
          </li>
          <li>
            <a href="<?php bp_loggedinuser_link() ?>settings"><?php _e('Settings','egyptfoss') ?></a>
          </li>
          <li>
                <a href="<?php echo wp_logout_url(); ?>#"><?php _e('Logout','egyptfoss') ?></a>
          </li>
        </ul>
      </div>
      <?php else :?>
      <div class="login-box rfloat">
        <a href="<?php echo get_current_lang_page_by_template('template-login.php'); ?>" class="login-register"  id="user-nav"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-user"></i> <?php _e('Login/ Register','egyptfoss') ?> <i class="fa fa-angle-down"></i></a>
        <ul class="login-sub non-user" aria-labelledby="user-nav">
          <li>     
            <a href="<?php echo get_current_lang_page_by_template('template-login.php'); ?>"><?php _e('Login','egyptfoss') ?></a>
          </li>
          <li>
            <a href="<?php echo get_register_page_current_lang(); ?>"><?php _e('Register','egyptfoss')?></a>
          </li>
        </ul>
      </div>
      <?php endif; ?>
      <div class="lang-switch rfloat">
        <?php
        //for showing language links it's flexable we can put it in wp menu where ever we want
        $translations = pll_the_languages(array('raw' => 1, 'hide_current' => 1));
        foreach ($translations as $translation) {
        if( strpos($_SERVER['REQUEST_URI'],'/wiki') === 3) {?>
            <a href="<?php echo get_option('siteurl')."/".$translation["slug"]."/wiki/"; ?>" hreflang="<?php echo $translation["slug"]; ?>" class="lan-btn">
            <?php if($translation["slug"] == "ar")
              { ?>
                <i class="fa fa-globe"></i>
                    <span>العربية</span>
              <?php }
              else
              { ?>
                <i class="fa fa-globe"></i>
                <span>English</span>
              <?php	}?>
            </a>
            <?php
        } else {?>
            <a href="<?php echo $translation["url"]; ?>" hreflang="<?php echo $translation["slug"]; ?>" class="lan-btn pll-lang-url">
            <?php if($translation["slug"] == "ar")
            { ?>
            <i class="fa fa-globe"></i>
            <span>العربية</span>
            <?php }
            else
            { ?>
            <i class="fa fa-globe"></i>
            <span>English</span>
            <?php	}?>
            </a>
        <?php
        }
      }?>
    </div>
    <div class="quick-search lfloat">
        <form class="search-box-form" action="<?php bloginfo('url'); ?>/" method="get" id="searchform">
        <button><i class="fa fa-search"></i></button>
            <input type="search" placeholder="<?php _e('Search','egyptfoss')?>" value="<?php the_search_query(); ?>" name="s" id="s">
            <div id="s_validate"></div>
        </form>
    </div>
    <div class="apps-shortcut lfloat">
    <a href="#"><i class="fa fa-mobile-phone"></i> <?php _e("Get the mobile App","egyptfoss") ?>
    <i class="fa fa-angle-down"></i>
    </a>
    <div class="apps-sub">
       <?php _e("Download our mobile apps to contribute and get the latest updates from EgyptFOSS community.","egyptfoss"); ?>
       <br>
      <div class="text-center">
        <a href="https://itunes.apple.com/us/app/egyptfoss/id1179734318" title="<?php _e("Get the mobile App","egyptfoss"); ?>" data-toggle="tooltip" data-placement="top" class="app-badges appstore-badge"></a>
        <a href="https://play.google.com/store/apps/details?id=org.egyptfoss" data-toggle="tooltip" data-placement="top" title="<?php _e("Get the mobile App","egyptfoss"); ?>" target="_blank" class="app-badges playstore-badge"></a>
       </div>
    </div>
    </div>
</div>
</div> 
</div>

<header id="masthead" class="header" role="banner">
<div class="container">
    <div class="row">
            <div class="col-md-2">
              <div class="site-branding lfloat">
                <a href="<?php echo get_site_url(); ?>" class="site-logo">
                  <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo('name'); ?>" class="foss-logo" />
                </a>
              </div>
              <!-- .site-branding -->
            </div>
            <a class="open-menu ar-open rfloat" href="#mobile-nav">
                <i class="fa fa-bars"></i>
            </a>
            <div class="col-md-10 primary-navigation">
                <?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu','container'=> 'div', 'container_class' => 'menu-main-nav-container', 'menu_class' => 'menu-main-nav-container') ); ?>

            </div>
    </div>
</header>
<!-- #masthead -->
