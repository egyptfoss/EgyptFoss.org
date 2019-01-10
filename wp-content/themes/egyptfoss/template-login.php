<?php
/*
  Template Name: Custom EGYFOSS Login
 */
?>
<?php
/** some setting for template-login to work as wp-login */
require_once( ABSPATH . 'wp-load.php' );
$query_params = isset($_GET['q']) ? preg_replace('/^.*?\?+/', '', $_GET['q']) : 'login';
$args = wp_parse_args($query_params);
$_REQUEST = array_merge($_REQUEST, $args);
if( isset($args['login']) && isset($_GET['login'])) {
  $args['login'] = $_GET['login'];
}
$_GET = array_merge($_GET, $args);
$translations = pll_the_languages(array('raw' => 1));
$translated_login_url = "";

foreach ($translations as $translation) {
  if ($translation["current_lang"]) {
      $translated_login_url = preg_replace('/^(https?:\/\/)?[^\/]*\//', '', $translation["url"]);
      $slug = (pll_current_language() == "en") ? "ar" : "en";
      $translated_login_url = preg_replace("/" . $slug . "/", pll_current_language(), $translated_login_url, 1);
  }
}
if (isset($_REQUEST["error"])) {
  $_GET = array_merge($_GET, array("error" => $_REQUEST["error"]));
}
/** some setting for template-login to work as wp-login */
// Redirect to https login if forced to use SSL
if (force_ssl_admin() && !is_ssl()) {
  if (0 === strpos($_SERVER['REQUEST_URI'], 'http')) {
    wp_redirect(set_url_scheme($_SERVER['REQUEST_URI'], 'https'));
    exit();
  } else {
    wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
  }
}
/**
 * Output the login page header.
 *
 * @param string   $title    Optional. WordPress login Page title to display in the `<title>` element.
 *                           Default 'Log In'.
 * @param string   $message  Optional. Message to display in header. Default empty.
 * @param WP_Error $wp_error Optional. The error to pass. Default empty.
 */
function login_header($title = 'Log In', $message = '', $wp_error = '') {
  global $error, $interim_login, $action;
  // Don't index any of these forms
  add_action('login_head', 'wp_no_robots');
  if (wp_is_mobile())
    add_action('login_head', 'wp_login_viewport_meta');
  if (empty($wp_error))
    $wp_error = new WP_Error();
  // Shake it!
  $shake_error_codes = array('empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password');
  $overrided_messages = array('incorrect_password','invalid_username','invalid_email');
  foreach ($overrided_messages as $overrided_message)
  {
  if(array_key_exists($overrided_message, $wp_error->errors))
  {
    if($action == "lostpassword"){
     $wp_error->errors[$overrided_message] = array(__('Wrong username or email','egyptfoss'));
    }else
    {
     $wp_error->errors[$overrided_message] = array(__('Wrong username or password','egyptfoss'));
    }
  }
  }

  /**
   * Filter the error codes array for shaking the login form.
   *
   * @since 3.0.0
   *
   * @param array $shake_error_codes Error codes that shake the login form.
   */
  $shake_error_codes = apply_filters('shake_error_codes', $shake_error_codes);
  if ($shake_error_codes && $wp_error->get_error_code() && in_array($wp_error->get_error_code(), $shake_error_codes))
    add_action('login_head', 'wp_shake_js', 12);
  get_header();
  ?><!DOCTYPE html>
  <!--[if IE 8]>
    <html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
  <![endif]-->
  <!--[if !(IE 8) ]><!-->
  <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
      <!--<![endif]-->
      <head>
          <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
          <title><?php bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
  <?php
  wp_admin_css('login', true);
  /*
   * Remove all stored post data on logging out.
   * This could be added by add_action('login_head'...) like wp_shake_js(),
   * but maybe better if it's not removable by plugins
   */
  if ('loggedout' == $wp_error->get_error_code()) {
    ?>
            <script>if ("sessionStorage" in window) {
                  try {
                      for (var key in sessionStorage) {
                          if (key.indexOf("wp-autosave-") != -1) {
                              sessionStorage.removeItem(key)
                          }
                      }
                  } catch (e) {
                  }
              }
              ;</script>
    <?php
  }
  /**
   * Enqueue scripts and styles for the login page.
   *
   * @since 3.1.0
   */
  do_action('login_enqueue_scripts');
  /**
   * Fires in the login page header after scripts are enqueued.
   *
   * @since 2.1.0
   */
  do_action('login_head');
  if (is_multisite()) {
    $login_header_url = network_home_url();
    $login_header_title = get_current_site()->site_name;
  } else {
    $login_header_url = __('https://wordpress.org/');
    $login_header_title = __('Powered by WordPress');
  }
  /**
   * Filter link URL of the header logo above login form.
   *
   * @since 2.1.0
   *
   * @param string $login_header_url Login header logo URL.
   */
  $login_header_url = apply_filters('login_headerurl', $login_header_url);
  /**
   * Filter the title attribute of the header logo above login form.
   *
   * @since 2.1.0
   *
   * @param string $login_header_title Login header logo title attribute.
   */
  $login_header_title = apply_filters('login_headertitle', $login_header_title);
  $classes = array('login-action-' . $action, 'wp-core-ui');
  if (wp_is_mobile())
    $classes[] = 'mobile';
  if (is_rtl())
    $classes[] = 'rtl';
  if ($interim_login) {
    $classes[] = 'interim-login';
    ?>
            <style type="text/css">html{background-color: transparent;}</style>
            <?php
            if ('success' === $interim_login)
              $classes[] = 'interim-login-success';
          }
          $classes[] = ' locale-' . sanitize_html_class(strtolower(str_replace('_', '-', get_locale())));
          /**
           * Filter the login page body classes.
           *
           * @since 3.5.0
           *
           * @param array  $classes An array of body classes.
           * @param string $action  The action that brought the visitor to the login page.
           */
          $classes = apply_filters('login_body_class', $classes, $action);
          ?>
      </head>
      <body class="login <?php echo esc_attr(implode(' ', $classes)); ?>">

          <header class="page-header">
              <div class="container">
                  <h1 class="entry-title"><?php echo __('Log in'); ?></h1>
              </div>
          </header><!-- .entry-header -->

          <div class="container">
              <div class="row">
                  <div id="primary" class="content-area col-md-12">
                    <div class="login-page" id="register-page">
                     <div class="row">
                      <section class="login-form-wrapper col-md-8">
                          <!-- Nav tabs -->
  <?php if (!isset($_GET["action"]) || $_GET["action"] != "rp") { ?>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" <?php
    if ($action == "login" || $action == "bp-resend-activation") {
      echo 'class="active"';
    }
    ?> ><a href="<?php echo esc_url(wp_login_url()); ?>" title="<?php esc_attr_e('Login to your Account', 'egyptfoss') ?>" aria-controls="login"><h4><?php _e('Login to your Account', 'egyptfoss'); ?></h4></a></li>
                                <li role="presentation" <?php
                                if ($action == "lostpassword" || $action == "retrievepassword") {
                                  echo 'class="active rfloat"';
                                }
                                ?> ><a href="<?php echo esc_url(wp_lostpassword_url()); ?>" title="<?php esc_attr_e('Forgot Password?', 'egyptfoss') ?>" aria-controls="forgot"><h4><?php _e('Forgot Password?', 'egyptfoss'); ?></h4></a></li>
                            </ul>
                          <?php } ?>
                          <?php
                          unset($login_header_url, $login_header_title);
                          /**
                           * Filter the message to display above the login form.
                           *
                           * @since 2.1.0
                           *
                           * @param string $message Login message text.
                           */
                          $message = apply_filters('login_message', $message);
                          if (!empty($message))
                            echo $message . "\n";
                          // In case a plugin uses $error rather than the $wp_errors object
                          if (!empty($error)) {
                            $wp_error->add('error', $error);
                            unset($error);
                          }
                          if ($wp_error->get_error_code()) {
                            $errors = '';
                            $messages = '';
                            foreach ($wp_error->get_error_codes() as $code) {
                              $severity = $wp_error->get_error_data($code);
                              foreach ($wp_error->get_error_messages($code) as $error_message) {
                                if ('message' == $severity)
                                  $messages .= '	' . $error_message . "<br />\n";
                                else
                                  $errors .= '	' . $error_message . "<br />\n";
                              }
                            }
                            if (!empty($errors)) {
                              /**
                               * Filter the error messages displayed above the login form.
                               *
                               * @since 2.1.0
                               *
                               * @param string $errors Login error message.
                               */
                              echo '<div id="login_error" class="alert alert-danger">' . apply_filters('login_errors', $errors) . "</div>\n";
                            }
                            if (!empty($messages)) {
                              /**
                               * Filter instructional messages displayed above the login form.
                               *
                               * @since 2.5.0
                               *
                               * @param string $messages Login messages.
                               */
                              echo '<div class="message alert alert-warning">' . apply_filters('login_messages', $messages) . "</div>\n";
                            }
                          }

                          $message = getMessageBySession("login-error");

                            if(isset($message['error']) && !empty($message['error']))
                            {
                            echo '<div id="login_error" class="message alert alert-danger">' . $message["error"] . "</div>\n";
                            }

                          global $successMessage;
                          if(!empty($successMessage))
                          {
                                       echo '<div class="message alert alert-success">' . $successMessage . "</div>\n";
                          }
                        }
// End of login_header()
                        /**
                         * Outputs the footer for the login page.
                         *
                         * @param string $input_id Which input to auto-focus
                         */
                        function login_footer($input_id = '') {
                          global $interim_login;
                          ?>

                      </section>
                      <div class="col-md-4">
                           <?php do_action('wordpress_social_login'); ?>
                      </div>
                  </div>
                  </div>
                  </div><!-- #primary -->
              </div>
          </div>

  <?php if (!empty($input_id)) : ?>
            <script type="text/javascript">
              try {
                  document.getElementById('<?php echo $input_id; ?>').focus();
              } catch (e) {
              }
              if (typeof wpOnload == 'function')
                  wpOnload();
            </script>
          <?php endif; ?>

          <?php
          /**
           * Fires in the login page footer.
           *
           * @since 3.1.0
           */
          do_action('login_footer');
          ?>
          <div class="clear"></div>
      </body>
  </html>
  <?php
}
/**
 * @since 3.0.0
 */
function wp_shake_js() {
  if (wp_is_mobile())
    return;
  ?>
  <script type="text/javascript">
    addLoadEvent = function (func) {
        if (typeof jQuery != "undefined")
            jQuery(document).ready(func);
        else if (typeof wpOnload != 'function') {
            wpOnload = func;
        } else {
            var oldonload = wpOnload;
            wpOnload = function () {
                oldonload();
          func();
            }
        }
    };
    function s(id, pos) {
        g(id).left = pos + 'px';
    }
    function g(id) {
        return document.getElementById(id).style;
    }
    function shake(id, a, d) {
        c = a.shift();
        s(id, c);
        if (a.length > 0) {
            setTimeout(function () {
                shake(id, a, d);
            }, d);
        } else {
            try {
                g(id).position = 'static';
                wp_attempt_focus();
            } catch (e) {
            }
        }
    }
    addLoadEvent(function () {
        var p = new Array(15, 30, 15, 0, -15, -30, -15, 0);
        p = p.concat(p.concat(p));
        var i = document.forms[0].id;
        g(i).position = 'relative';
        shake(i, p, 20);
    });
  </script>
  <?php
}
/**
 * @since 3.7.0
 */
function wp_login_viewport_meta() {
  ?>
  <meta name="viewport" content="width=device-width" />
  <?php
}
/**
 * Handles sending password retrieval email to user.
 *
 * @global wpdb         $wpdb      WordPress database abstraction object.
 * @global PasswordHash $wp_hasher Portable PHP password hashing framework.
 *
 * @return bool|WP_Error True: when finish. WP_Error on error
 */
function retrieve_password() {
  global $wpdb, $wp_hasher, $translated_login_url;
  $errors = new WP_Error();
  if (empty($_POST['user_login'])) {
    $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
  } elseif (strpos($_POST['user_login'], '@')) {
    $user_data = get_user_by('email', trim($_POST['user_login']));
    if (empty($user_data))
      $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
  } else {
    $login = trim($_POST['user_login']);
    $user_data = get_user_by('login', $login);
  }
  /**
   * Fires before errors are returned from a password reset request.
   *
   * @since 2.1.0
   */
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'lost-password-form' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  do_action('lostpassword_post');
  if ($errors->get_error_code())
    return $errors;
  if (!$user_data) {
    $errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
    return $errors;
  }
  // Redefining user_login ensures we return the right case in the email.
  $user_login = $user_data->user_login;
  $user_email = $user_data->user_email;
  /**
   * Fires before a new password is retrieved.
   *
   * @since 1.5.0
   * @deprecated 1.5.1 Misspelled. Use 'retrieve_password' hook instead.
   *
   * @param string $user_login The user login name.
   */
  do_action('retreive_password', $user_login);
  /**
   * Fires before a new password is retrieved.
   *
   * @since 1.5.1
   *
   * @param string $user_login The user login name.
   */
  do_action('retrieve_password', $user_login);
  /**
   * Filter whether to allow a password to be reset.
   *
   * @since 2.7.0
   *
   * @param bool true           Whether to allow the password to be reset. Default true.
   * @param int  $user_data->ID The ID of the user attempting to reset a password.
   */
  $allow = apply_filters('allow_password_reset', true, $user_data->ID);
  if (!$allow) {
    return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
  } elseif (is_wp_error($allow)) {
    return $allow;
  }
  // Generate something random for a password reset key.
  $key = wp_generate_password(20, false);
  /**
   * Fires when a password reset key is generated.
   *
   * @since 2.5.0
   *
   * @param string $user_login The username for the user.
   * @param string $key        The generated password reset key.
   */
  do_action('retrieve_password_key', $user_login, $key);
  // Now insert the key, hashed, into the DB.
  if (empty($wp_hasher)) {
    require_once ABSPATH . WPINC . '/class-phpass.php';
    $wp_hasher = new PasswordHash(8, true);
  }
  $hashed = time() . ':' . $wp_hasher->HashPassword($key);
  $wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user_login));
  $translated_login_url = preg_replace('/\?(action=)*[^&]+/', '', $translated_login_url);
  $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
  $message .= network_home_url('/') . "\r\n\r\n";
  $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
  $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
  $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
  $message .= '<' . network_site_url($translated_login_url . "?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
  if (is_multisite())
    $blogname = $GLOBALS['current_site']->site_name;
  else
  /*
   * The blogname option is escaped with esc_html on the way into the database
   * in sanitize_option we want to reverse this for the plain text arena of emails.
   */
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
  $title = sprintf(__('[%s] Password Reset'), $blogname);
  /**
   * Filter the subject of the password reset email.
   *
   * @since 2.8.0
   *
   * @param string $title Default email title.
   */
  $title = apply_filters('retrieve_password_title', $title);
  /**
   * Filter the message body of the password reset mail.
   *
   * @since 2.8.0
   * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
   *
   * @param string  $message    Default mail message.
   * @param string  $key        The activation key.
   * @param string  $user_login The username for the user.
   * @param WP_User $user_data  WP_User object.
   */
  $message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);
  if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message))
    wp_die(__('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.'));
  return true;
}
//
// Main
//
//$action = isset($_GET['q']) ? preg_replace('/^.*?\?+/', '', $_GET['q']) : 'login';
//$args = wp_parse_args( $action );
//$_REQUEST = array_merge($_REQUEST,$args);
//$_GET = array_merge($_GET,$args);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
//exit;
$errors = new WP_Error();
if (isset($_GET['key']))
  $action = 'resetpass';
// validate action so as to default to the login screen
if (!in_array($action, array('postpass', 'logout', 'lostpassword', 'retrievepassword', 'resetpass', 'rp', 'register', 'login'), true) && false === has_filter('login_form_' . $action))
  $action = 'login';
nocache_headers();
header('Content-Type: ' . get_bloginfo('html_type') . '; charset=' . get_bloginfo('charset'));
if (defined('RELOCATE') && RELOCATE) { // Move flag is set
  if (isset($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']))
    $_SERVER['PHP_SELF'] = str_replace($_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF']);
  $url = dirname(set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']));
  if ($url != get_option('siteurl'))
    update_option('siteurl', $url);
}
//Set a cookie now to see if they are supported by the browser.
$secure = ( 'https' === parse_url(site_url(), PHP_URL_SCHEME) && 'https' === parse_url(home_url(), PHP_URL_SCHEME) );
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure);
if (SITECOOKIEPATH != COOKIEPATH)
  setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure);
/**
 * Fires when the login form is initialized.
 *
 * @since 3.2.0
 */
do_action('login_init');
/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form. Actions include 'postpass',
 * 'logout', 'lostpassword', etc.
 *
 * @since 2.8.0
 */
do_action('login_form_' . $action);
$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
$interim_login = isset($_REQUEST['interim-login']);
switch ($action) {
  case 'postpass' :
    require_once ABSPATH . WPINC . '/class-phpass.php';
    $hasher = new PasswordHash(8, true);
    /**
     * Filter the life span of the post password cookie.
     *
     * By default, the cookie expires 10 days from creation. To turn this
     * into a session cookie, return 0.
     *
     * @since 3.7.0
     *
     * @param int $expires The expiry time, as passed to setcookie().
     */
    $expire = apply_filters('post_password_expires', time() + 10 * DAY_IN_SECONDS);
    $secure = ( 'https' === parse_url(home_url(), PHP_URL_SCHEME) );
    setcookie('wp-postpass_' . COOKIEHASH, $hasher->HashPassword(wp_unslash($_POST['post_password'])), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure);
    wp_safe_redirect(wp_get_referer());
    exit();
  case 'logout' :
    check_admin_referer('log-out');
    $user = wp_get_current_user();
    wp_logout();
    if (!empty($_REQUEST['redirect_to'])) {
      $redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
    } else {
      $redirect_to = '?loggedout=true';
      $requested_redirect_to = '';
    }
    /**
     * Filter the log out redirect URL.
     *
     * @since 4.2.0
     *
     * @param string  $redirect_to           The redirect destination URL.
     * @param string  $requested_redirect_to The requested redirect destination URL passed as a parameter.
     * @param WP_User $user                  The WP_User object for the user that's logging out.
     */
    $redirect_to = apply_filters('logout_redirect', $redirect_to, $requested_redirect_to, $user);
    wp_safe_redirect($redirect_to);
    exit();
  case 'lostpassword' :
  case 'retrievepassword' :
    if (is_user_logged_in()) {
      wp_redirect(site_url());
      exit;
    }
    if ($http_post) {
      $errors = retrieve_password();
      if (!is_wp_error($errors)) {
        $redirect_to = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '?checkemail=confirm';
        wp_safe_redirect($redirect_to);
        exit();
      }
    }
    if (isset($_GET['error'])) {
      if ('invalidkey' == $_GET['error']) {
        $errors->add('invalidkey', __('Your password reset link appears to be invalid. Please request a new link below.'));
      } elseif ('expiredkey' == $_GET['error']) {
        $errors->add('expiredkey', __('Your password reset link has expired. Please request a new link below.'));
      }
    }
    $lostpassword_redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
    /**
     * Filter the URL redirected to after submitting the lostpassword/retrievepassword form.
     *
     * @since 3.0.0
     *
     * @param string $lostpassword_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters('lostpassword_redirect', $lostpassword_redirect);
    /**
     * Fires before the lost password form.
     *
     * @since 1.5.1
     */
    do_action('lost_password');
    login_header(__('Lost Password'), '', $errors);
    $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';
    ?>

    <form name="lostpasswordform" id="lostpasswordform" action="<?php echo esc_url(network_site_url($translated_login_url, 'login_post')); ?>" method="post" class="forgot-form">
        <div class="form-group">
            <label class="label" for="username-email"><?php _e('E-mail or Username', 'egyptfoss') ?></label>
            <input type="text" name="user_login" id="user_login" class="input form-control" value="<?php echo esc_attr($user_login); ?>" placeholder="<?php _e('E-mail or Username', 'egyptfoss') ?>" /></label>
        </div>
    <?php do_action('lostpassword_form'); ?>
        <div class="form-group">
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
            <button type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-block button button-primary button-large"><?php esc_attr_e('Get New Password'); ?></button>
        </div>
        <?php wp_nonce_field( 'lost-password-form' ); ?>
    </form>

    <?php
    login_footer('user_login');
    get_footer();
    break;
  case 'resetpass' :
  case 'rp' :
    if (is_user_logged_in()) {
      wp_redirect(site_url());
      exit;
    }
    list( $rp_path ) = explode('?', wp_unslash($_SERVER['REQUEST_URI']));
    $rp_cookie = 'wp-resetpass-' . COOKIEHASH;
    if (isset($_GET['key'])) {
      $value = sprintf('%s:%s', wp_unslash($_GET['username']), wp_unslash($_GET['key']));
      setcookie($rp_cookie, $value, 0, "/", COOKIE_DOMAIN, is_ssl(), false);
      wp_safe_redirect(remove_query_arg(array('key', 'login')));
      exit;
    }
    if (isset($_COOKIE[$rp_cookie]) && 0 < strpos($_COOKIE[$rp_cookie], ':')) {
      list( $rp_login, $rp_key ) = explode(':', wp_unslash($_COOKIE[$rp_cookie]), 2);
      $user = check_password_reset_key($rp_key, $rp_login);
      if (isset($_POST['egyptfoss-pass1']) && !hash_equals($rp_key, $_POST['rp_key'])) {
        $user = false;
      }
    } else {
      $user = false;
    }
    if (!$user || is_wp_error($user)) {
      setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
      if ($user && $user->get_error_code() === 'expired_key')
        wp_redirect(site_url($translated_login_url . '?action=lostpassword&error=expiredkey123'));
      else
        wp_redirect(site_url($translated_login_url . '?action=lostpassword&error=invalidkey456'));
      exit;
    }
    $errors = new WP_Error();
    if (isset($_POST['egyptfoss-pass1']) && $_POST['egyptfoss-pass1'] != $_POST['egyptfoss-pass2'])
      $errors->add('password_reset_mismatch', __('The passwords do not match.'));
    /**
     * Fires before the password reset procedure is validated.
     *
     * @since 3.5.0
     *
     * @param object           $errors WP Error object.
     * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
     */
    do_action('validate_password_reset', $errors, $user);
    if ((!$errors->get_error_code() ) && isset($_POST['egyptfoss-pass1']) && !empty($_POST['egyptfoss-pass1'])) {
      reset_password($user, $_POST['egyptfoss-pass1']);
      setcookie($rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true);
      wp_safe_redirect(site_url(pll_current_language()."/login/")."?action=resetsuccess");
      exit;
    }
    wp_enqueue_script('utils');
    wp_enqueue_script('user-profile');
    login_header(__('Reset Password'), '<p class="message reset-pass">' . '</p>', $errors);
    ?>
    <h3><?php _e("Set your new password","egyptfoss"); ?></h3>
    <form name="resetpassform" id="resetpassform" action="<?php echo esc_url(network_site_url($translated_login_url, 'login_post')); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" value="<?php echo esc_attr($rp_login); ?>" autocomplete="off" />

        <div class="row">
            <div class="col-md-12">
                <label class="label" for="egyptfoss-pass1"><?php _e('New password') ?></label>
                <div class="wp-pwd">
                    <span class="password-input-wrapper">
                        <input type="password" data-reveal="1" name="egyptfoss-pass1" id="egyptfoss-pass1" class=" egyptfoss-pass1 form-control" size="20" value="" autocomplete="off" aria-describedby="pass-strength-result" />
                    </span>
                    <div id="pass-strength-result" class="hide-if-no-js" aria-live="polite"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label class="label" for="egyptfoss-pass2"><?php _e('Confirm new password') ?></label>
                <input type="password" name="egyptfoss-pass2" id="egyptfoss-pass2" class=" egyptfoss-pass2 form-control" size="20" value="" autocomplete="off" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <p class="description indicator-hint"><?php _e( 'Hint: The password should be at least eight characters long. To make it stronger, use upper and lower case letters, numbers, and symbols like ! " ? $ % ^ &amp; ).',"egyptfoss" ); ?></p>
                </div>
            </div>
        </div>
        <br class="clear" />

        <?php
        /**
         * Fires following the 'Strength indicator' meter in the user password reset form.
         *
         * @since 3.9.0
         *
         * @param WP_User $user User object of the user whose password is being reset.
         */
        do_action('resetpass_form', $user);
        ?>
        <input type="hidden" name="rp_key" value="<?php echo esc_attr($rp_key); ?>" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value="<?php esc_attr_e('Reset Password'); ?>" /></p>
    </form>

    <?php
    login_footer('user_pass');
    get_footer();
    break;
  case 'register' :
    if (is_user_logged_in()) {
      wp_redirect(site_url());
      exit;
    }
    if (is_multisite()) {
      /**
       * Filter the Multisite sign up URL.
       *
       * @since 3.0.0
       *
       * @param string $sign_up_url The sign up URL.
       */
      wp_redirect(apply_filters('wp_signup_location', network_site_url('wp-signup.php')));
      exit;
    }
    if (!get_option('users_can_register')) {
      wp_redirect(site_url($translated_login_url . '?registration=disabled'));
      exit();
    }
    $user_login = '';
    $user_email = '';
    if ($http_post) {
      $user_login = $_POST['user_login'];
      $user_email = $_POST['user_email'];
      $errors = register_new_user($user_login, $user_email);
      if (!is_wp_error($errors)) {
        $redirect_to = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : $translated_login_url . '?checkemail=registered';
        wp_safe_redirect($redirect_to);
        exit();
      }
    }
    $registration_redirect = !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
    /**
     * Filter the registration redirect URL.
     *
     * @since 3.0.0
     *
     * @param string $registration_redirect The redirect destination URL.
     */
    $redirect_to = apply_filters('registration_redirect', $registration_redirect);
    login_header(__('Registration Form'), '<p class="message register">' . __('Register For This Site') . '</p>', $errors);
    ?>

    <form name="registerform" id="registerform" action="<?php echo esc_url(site_url($translated_login_url . '?action=register', 'login_post')); ?>" method="post" novalidate="novalidate">
        <p>
            <label for="user_login"><?php _e('Username') ?><br />
                <input type="text" name="user_login" id="user_login" class="input" value="<?php echo esc_attr(wp_unslash($user_login)); ?>" size="20" /></label>
        </p>
        <p>
            <label for="user_email"><?php _e('E-mail') ?><br />
                <input type="email" name="user_email" id="user_email" class="input" value="<?php echo esc_attr(wp_unslash($user_email)); ?>" size="25" /></label>
        </p>
        <?php
        /**
         * Fires following the 'E-mail' field in the user registration form.
         *
         * @since 2.1.0
         */
        do_action('register_form');
        ?>
        <p id="reg_passmail"><?php _e('Registration confirmation will be e-mailed to you.'); ?></p>
        <br class="clear" />
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
        <p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e('Register'); ?>" /></p>
    </form>

    <?php
    login_footer('user_login');
    get_footer();
    break;
  case 'login' :
    if (is_user_logged_in()) {
      wp_redirect(site_url());
      exit;
    }
    if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
      $nonce = $_REQUEST['_wpnonce'];
      if ( ! wp_verify_nonce( $nonce, 'login-form' ) ) {
        wp_redirect( home_url( '/?status=403' ) );
        exit;
      }
    }
  default:
    $secure_cookie = '';
    $customize_login = isset($_REQUEST['customize-login']);
    if ($customize_login)
      wp_enqueue_script('customize-base');
    // If the user wants ssl but the session is not ssl, force a secure cookie.
    if (!empty($_POST['log']) && !force_ssl_admin()) {
      $user_name = sanitize_user($_POST['log']);
      if ($user = get_user_by('login', $user_name)) {
        if (get_user_option('use_ssl', $user->ID)) {
          $secure_cookie = true;
          force_ssl_admin(true);
        }
      }
    }
    if (isset($_REQUEST['redirect_to'])) {
      $redirect_to = $_REQUEST['redirect_to'];
      // Redirect to https if user wants ssl
      if ($secure_cookie && false !== strpos($redirect_to, 'wp-admin'))
        $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
    } else {
      $redirect_to = admin_url();
    }
    $reauth = empty($_REQUEST['reauth']) ? false : true;
    $user = wp_signon('', $secure_cookie);
    if (empty($_COOKIE[LOGGED_IN_COOKIE])) {
      if (headers_sent()) {
        $user = new WP_Error('test_cookie', sprintf(__('<strong>ERROR</strong>: Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.'), __('https://codex.wordpress.org/Cookies'), __('https://wordpress.org/support/')));
      } elseif (isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE])) {
        // If cookies are disabled we can't log in even with a valid user+pass
        $user = new WP_Error('test_cookie', sprintf(__('<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.'), __('https://codex.wordpress.org/Cookies')));
      }
    }
    $requested_redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '';
    /**
     * Filter the login redirect URL.
     *
     * @since 3.0.0
     *
     * @param string           $redirect_to           The redirect destination URL.
     * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
     * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
     */
    $redirect_to = apply_filters('login_redirect', $redirect_to, $requested_redirect_to, $user);
    if (!is_wp_error($user) && !$reauth) {
      if ($interim_login) {
        $message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
        $interim_login = 'success';
        login_header('', $message);
        ?>
        </div>
        <?php
        /** This action is documented in wp-login.php */
        do_action('login_footer');
        ?>
        <?php if ($customize_login) : ?>
          <script type="text/javascript">setTimeout(function () {
                new wp.customize.Messenger({url: '<?php echo wp_customize_url(); ?>', channel: 'login'}).send('login')
            }, 1000);</script>
        <?php endif; ?>
        </body></html>
        <?php
        exit;
      }
      if (( empty($redirect_to) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url())) {
        // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
        if (is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin($user->ID))
          $redirect_to = user_admin_url();
        elseif (is_multisite() && !$user->has_cap('read'))
          $redirect_to = get_dashboard_url($user->ID);
        elseif (!$user->has_cap('edit_posts'))
          $redirect_to = admin_url('profile.php');
      }
      wp_safe_redirect($redirect_to);
      exit();
    }
    $errors = $user;
    // Clear errors if loggedout is set.
    if (!empty($_GET['loggedout']) || $reauth)
      $errors = new WP_Error();
    if ($interim_login) {
      if (!$errors->get_error_code())
        $errors->add('expired', __('Session expired. Please log in again. You will not move away from this page.'), 'message');
    } else {
      // Some parts of this script use the main login form to display a message
      if (isset($_GET['loggedout']) && true == $_GET['loggedout'])
        $errors->add('loggedout', __('You are now logged out.'), 'message');
      elseif (isset($_GET['registration']) && 'disabled' == $_GET['registration'])
        $errors->add('registerdisabled', __('User registration is currently not allowed.'));
      elseif (isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'])
        $errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
      elseif (isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'])
        $errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
      elseif (isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'])
        $errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
      elseif (strpos($redirect_to, 'about.php?updated'))
        $errors->add('updated', __('<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.'), 'message');
      elseif (isset($_GET['action']) && 'resetsuccess' == $_GET['action'])
        $errors->add('confirm', __('Your password has been reset.','egyptfoss'), 'message');
    }
    /**
     * Filter the login page errors.
     *
     * @since 3.6.0
     *
     * @param object $errors      WP Error object.
     * @param string $redirect_to Redirect destination URL.
     */
    $message = '';
    $successMessage = '';
    $errors = apply_filters('wp_login_errors', $errors, $redirect_to);
    // Clear any stale cookies.
    if ($reauth)
      wp_clear_auth_cookie();
    // --- Add this case to show error message if user trying to add product and is not logged in --- //
    $redirected = isset($_REQUEST['redirected']) ? $_REQUEST['redirected'] : '';
    switch ($redirected) {
      case 'addproduct' :
        $errors->add('logintoaddproduct', __('Please log in to suggest a new product',"egyptfoss"));
        break;
      case 'editproduct' :
        $errors->add('logintoeditproduct', __('Please log in to edit a product',"egyptfoss"));
        break;
      case 'editrequestcenter' :
        $errors->add('logintoeditproduct', __('Please log in to edit a request',"egyptfoss"));
        break;
      case 'addnews' :
        $errors->add('news', __('Please log in to suggest a new news',"egyptfoss") );
        break;
      case 'addsuccessstory' :
        $errors->add('success-story', __('Please log in to suggest a new success story',"egyptfoss") );
        break;
      case 'addopendataset' :
        $errors->add('open-dataset', __('Please log in to suggest a new open dataset',"egyptfoss") );
        break;
      case 'addresourcesopendataset' :
        $errors->add('add-resources', __('Please log in to suggest a new resource',"egyptfoss") );
        break;
      case 'addrequestcenter' :
        $errors->add('request-center', __('Please log in to suggest a new request',"egyptfoss") );
        break;
      case 'addfeedback' :
        $errors->add('feedback', __('Please log in to suggest a new feedback',"egyptfoss") );
        break;
      case 'addevent' :
        $errors->add('logintoaddevent', __('Please log in to suggest a new event',"egyptfoss"));
        break;
      case 'addlocation' :
        $errors->add('logintoaddlocation', __('Please log in to add your location',"egyptfoss"));
        break;
      case 'respondtorequest' :
        $errors->add('logintorespondtorequest', __('Please log in to respond to a request',"egyptfoss"));
        break;
      case 'respondtoservice' :
        $errors->add('logintorespondtoservice', __('Please log in to proceed',"egyptfoss"));
        break;
      case 'registered' :
          $successMessage = __( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'egyptfoss' );
        break;
      case 'takequiz':
        $errors->add('awareness-center', __('Please log in to take the quiz',"egyptfoss") );
        break;
    }
    // --- end add product error case --- //
    login_header(__('Log In'), $message, $errors);
    if (isset($_POST['log']))
      $user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
    $rememberme = !empty($_POST['rememberme']);
    if (!empty($errors->errors)) {
      $aria_describedby_error = ' aria-describedby="login_error"';
    } else {
      $aria_describedby_error = '';
    }

    //retrieve query string if exists
    $query_new_string = "";
    if($_SERVER['QUERY_STRING'] != ''){
        $query_new_string = '?'.$_SERVER['QUERY_STRING'];
    }
    ?>

    <form name="loginform" id="loginform" action="<?php echo esc_url(site_url('wp-login.php', 'login_post').$query_new_string); ?>" method="post" class="login-form">

        <div class="form-group">
            <label class="label" for="username-email"><?php _e('E-mail or Username', 'egyptfoss') ?></label>
            <input type="text" name="log" id="user_login"<?php echo $aria_describedby_error; ?> class="input form-control" value="" placeholder="<?php _e('E-mail or Username', 'egyptfoss') ?>" />
        </div>
        <div class="form-group">
            <label class="label" for="password"><?php _e('Password') ?></label>
            <input type="password" name="pwd" id="user_pass"<?php echo $aria_describedby_error; ?> class=" form-control" value="" autocomplete="off" placeholder="<?php _e('Password') ?>" />
        </div>
    <?php do_action('login_form'); ?>
        <div class="input-group">
            <div class="checkbox">
                <label>
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked($rememberme); ?> /> <?php esc_attr_e('Remember Me'); ?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <p class="submit">
                <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large btn btn-primary btn-block" value="<?php _e('Login','egyptfoss'); ?>" />
                <?php if ($interim_login) { ?>
                  <input type="hidden" name="interim-login" value="1" />
                <?php } else { ?>
                  <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
                <?php } ?>
                <?php if ($customize_login) : ?>
                  <input type="hidden" name="customize-login" value="1" />
    <?php endif; ?>
                <input type="hidden" name="testcookie" value="1" />
            </p>
        </div>
        <?php wp_nonce_field( 'login-form' ); ?>
        <span>
          <?php _e( 'Don\'t have an account ?', 'egyptfoss' ); ?>
            <a href="<?php echo wp_registration_url(); ?>"><?php _e( 'Join Now', 'egyptfoss' ); ?></a>
        </span>
    </form>

    <script type="text/javascript">
      function wp_attempt_focus() {
          setTimeout(function () {
              try {
    <?php if ($user_login) { ?>
                    d = document.getElementById('user_pass');
                    d.value = '';
    <?php } else { ?>
                    d = document.getElementById('user_login');
      <?php if ('invalid_username' == $errors->get_error_code()) { ?>
                      if (d.value != '')
                          d.value = '';
        <?php
      }
    }
    ?>
                  d.focus();
                  d.select();
              } catch (e) {
              }
          }, 200);
      }
    <?php if (!$error) { ?>
        wp_attempt_focus();
    <?php } ?>
      if (typeof wpOnload == 'function')
          wpOnload();
    <?php if ($interim_login) { ?>
        (function () {
            try {
                var i, links = document.getElementsByTagName('a');
                for (i in links) {
                    if (links[i].href)
                        links[i].target = '_blank';
                }
            } catch (e) {
            }
        }());
    <?php } ?>
    </script>

    <?php
    login_footer();
    get_footer();
    break;
} // end action switch
