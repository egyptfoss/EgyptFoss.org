<?php

function change_activation_email_template($phpmailer) {
  global $wpdb;
  $subject = $phpmailer->Subject;
  if (strpos($phpmailer->Subject, 'Verify your new email address') !== false)
  {
    $user = get_user_by('ID', bp_loggedin_user_id());
    if(!$user) {
      $user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."users where ID = ".bp_loggedin_user_id());
    }
  }
  else if(strpos($phpmailer->Subject, 'Activate your account') !== false)
  {
    $user = get_user_by('user_email', $_POST['signup_email']);
    if(!$user) {
      $user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."signups where user_email = '".$_POST['signup_email']."'");
    }
  }else if(strpos($phpmailer->Subject, 'replied to one of you') !== false)
  {
    /*$user = get_user_by('ID', bp_loggedin_user_id());
    if(!$user) {
      $user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."users where ID = ".bp_loggedin_user_id());
    } */
  }

  //set from
  $smtp_user = get_option("smtp_from");
  if (strpos($phpmailer->Subject, 'Verify your new email address') !== false)
  {
    $activationUrl = $phpmailer->AltBody;
    $activationUrl_trimed = substr($activationUrl, strpos($activationUrl, 'http'));
    $activationUrl_final = substr($activationUrl_trimed, 0,strpos($activationUrl_trimed, 'Otherwise')-1);

    $msg = sprintf(__("To verify your new email address, visit the following address","egyptfoss"),$user->user_login);
    if( pll_current_language() == "ar")
      $msg = __("To verify your new email address, visit the following address","egyptfoss");

    $phpmailer->Subject = "[EgyptFOSS] ".__("Verify your new email","egyptfoss");
    $btn_address = __("Verify Email","egyptfoss");
    $title = __("Verify your new email","egyptfoss");
  }
  else if(strpos($phpmailer->Subject, 'Activate your account') !== false)
  {
    $activationUrl = $phpmailer->AltBody;
    $activationUrl_final = substr($activationUrl, strpos($activationUrl, 'http'));

    $msg = sprintf(__("Thank you for joining EgyptFOSS. To activate your account, please click the following link:","egyptfoss"),$user->user_login);
    if( pll_current_language() == "ar")
      $msg = __("Thank you for joining EgyptFOSS. To activate your account, please click the following link:","egyptfoss");

    //set subject
    $phpmailer->Subject = "[EgyptFOSS] ".__("Welcome to EgyptFOSS","egyptfoss");

    $btn_address = __("Activate account","egyptfoss");
    $title = __("Welcome to EgyptFOSS","egyptfoss");
    $ending_msg = __("activation_ending_msg","egyptfoss");
    $email_type = "activation";
  }else if(strpos($phpmailer->Subject, 'replied to one of you') !== false)
  {
        $full_content =  $phpmailer->AltBody;
        $activationUrl = $phpmailer->AltBody;
        $activationUrl_final = substr($activationUrl, strpos($activationUrl, 'http'));

        $activity_comment_id = substr($activationUrl, strpos($activationUrl, '/p/')+3);
        $msg_content = substr($full_content, strpos($full_content, "updates:") + 8);
        $msg_content = substr($msg_content, 0, strpos($msg_content, "Go to"));
        $msg_content = substr($msg_content, strpos($msg_content, "\"") + 1);
        $msg_content = trim($msg_content);
        $msg_content = rtrim($msg_content,"\"");
        $user_current = get_user_by('ID', bp_loggedin_user_id());
        if(!$user_current) {
          $user_current = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."users where ID = ".bp_loggedin_user_id());
        }

        $msg = "<a href=\"".$_SERVER['SERVER_NAME']."/".pll_current_language()."/members/".$user_current->user_nicename."\">".$user_current->user_nicename."</a>".__(" commented on one of your statuses ","egyptfoss");
        $msg .= "<br/>";
        $msg .= "<span style='display: block;width: 70%;padding: 30px;margin: 30px auto;border-radius: 10px;font-size: 18px;background-color: rgb(238, 238, 238);font-style: italic;'>".$msg_content."</span>";

        //set subject
        $phpmailer->Subject = "[EgyptFOSS] ".__("A new comment on one of your status","egyptfoss");

        $btn_address = __("View Comment","egyptfoss");
        $title = __("A new comment on one of your status","egyptfoss");
  }
    if(strpos($subject, 'replied to one of you') !== false)
    {
        $args = array(
          "title" => $title,
          "message" => $msg,
          "url" => $activationUrl_final,
          "show_url" => false,
          "button_title" => $btn_address
        );
    }else{
        $args = array(
          "title" => $title,
          "message" => $msg,
          "user_name" => $user->user_login,
          "url" => $activationUrl_final,
          "button_title" => $btn_address
        );
    }
    if($ending_msg != null){
      $args["ending_msg"] = $ending_msg;
    }
    if($email_type != null){
      $args["email_type"] = $email_type;
    }
  set_query_var( 'template_inputs', serialize($args));
  ob_start();
  get_template_part( 'mail-templates/activation' );
  $message = ob_get_contents();
  ob_end_clean();

  $phpmailer->msgHTML( $message, '', 'wp_strip_all_tags' );
  $phpmailer->From = $smtp_user;
  //$phpmailer->To = $smtp_user->option_value;
  $phpmailer->ClearReplyTos();
  if (filter_var($smtp_user, FILTER_VALIDATE_EMAIL)) {
    $phpmailer->addReplyTo($smtp_user,"EgyptFOSS");
  }
  return $phpmailer;
}
add_filter( 'bp_phpmailer_init','change_activation_email_template',10, 1);

function change_reset_password_email_template($message, $key, $user_login, $user_data) {

  $url = network_site_url(pll_current_language().'/'.get_translated_login_page() . "?action=rp&key=$key&username=" . rawurlencode($user_login), 'login') ;
  if(pll_current_language() == "ar")
      $url = $url."?action=rp&key=$key&username=". rawurlencode($user_login);

  $args = array(
    "title" => __("Reset your Password","egyptfoss"),
    "message" => __("To reset your password, visit the following address","egyptfoss"),
    "user_name" => $user_login,
    "url" => $url,
    "button_title" => __("Reset Password","egyptfoss")
  );
  set_query_var('template_inputs', serialize($args));
  return $message;
}
add_filter('retrieve_password_message','change_reset_password_email_template', 10, 4);

function change_password_email_template($pass_change_email, $user, $userdata) {
  $args = array(
    "title" => __("Your password has been changed","egyptfoss"),
    "message" => __("Hi ###USERNAME###,<br/> This email confirms that your password has been changed. If you did not request this change, please contact the <a href='###ADMIN_EMAIL###'>Site Administrator</a>.","egyptfoss"),
  );
  $args['message'] = str_replace( '###USERNAME###', $user['user_login'], $args['message'] );
  $args['message'] = str_replace( '###ADMIN_EMAIL###', "mailto:".get_option( 'admin_email' ), $args['message'] );
  set_query_var('template_inputs', serialize($args));
  return $pass_change_email;
}
add_filter( 'password_change_email', 'change_password_email_template', 10, 3);

function change_email_email_template($email_change_email, $user, $userdata) {
  $args = array(
    "title" => __("Your email address has been changed by the administrator","egyptfoss"),
    "message" => __("Hi ###USERNAME###,<br/> This email confirms that your email address has been changed on EgyptFOSS. If you did not request this change, please contact the <a href='###ADMIN_EMAIL###'>Site Administrator</a>.","egyptfoss"),
  );
  $args['message'] = str_replace( '###USERNAME###', $user['user_login'], $args['message'] );
  $args['message'] = str_replace( '###ADMIN_EMAIL###', "mailto:".get_option( 'admin_email' ), $args['message'] );
  set_query_var('template_inputs', serialize($args));
  return $email_change_email;
}
add_filter( 'email_change_email', 'change_email_email_template', 10, 3);

function change_content_type() {
  return 'text/html';
}

function get_translated_login_page() {
  $pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'meta_value' => 'template-login.php',
    'post_status' => 'publish'
  ));
  foreach ($pages as $login_page) {
    $post_in_langs = wp_get_object_terms($login_page->ID, 'post_translations');
    if ($post_in_langs) {
      $post_in_langs = unserialize($post_in_langs[0]->description);
      $current_lang = pll_current_language();
      if ($current_lang) {
        $translated_url = get_post_field('post_name', $post_in_langs[$current_lang]);
        return $translated_url;
      }
    }
  }
}

function new_user_by_admin($user_login, $key) {
  $url = network_site_url("login?action=rp&key=$key&username=" . rawurlencode($user_login), 'login');
  // $url = network_site_url(get_translated_login_page() . "?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') ;
  $args = array(
    "title" => __("[EgyptFOSS] Your account information","egyptfoss"),
    "message" => __("Hi ###USERNAME###,<br/> Welcome to EgyptFOSS.<br/> Your username is: ###USERNAME###. To set your password, please visit the following address","egyptfoss"),
    "url" => $url
  );
  $args['message'] = str_replace( '###USERNAME###', $user_login, $args['message'] );
  set_query_var('template_inputs', serialize($args));
}
add_action( 'retrieve_password_key', 'new_user_by_admin', 10, 2);

function custom_wp_mail($atts) {

  // to exclude newsletter plugin emails
  if( function_exists( 'get_current_screen' )  ) {
    $screen = get_current_screen();

    if( stripos( $screen->base, "newsletter" ) !== false ) {
      return $atts;
    }
  }

  $atts['title'] = $atts['subject'];
  $template_inputs = unserialize(get_query_var('template_inputs'));

  //check if message has direction rtl
  $message = htmlentities($atts["message"], ENT_QUOTES, "UTF-8");
  if (stripos($message, "direction:rtl") !== false) {
    $atts["language"] = "ar";
  }

  if (empty($template_inputs)) {
    set_query_var('template_inputs', serialize($atts));
  }

  add_filter('wp_mail_content_type','change_content_type');
  ob_start();
  get_template_part( 'mail-templates/activation' );
  $atts['message'] = ob_get_contents();
  ob_end_clean();
  return $atts;
}
add_filter('wp_mail', 'custom_wp_mail' );

function ef_change_my_email($email_text,$user_email)
{
    $hash = wp_hash( $user_email );
    $email_text = sprintf(
        __( 'Dear %1$s, <br/><br/>
        You recently changed the email address associated with your account on %2$s. <br/>
        If this is correct, please click on the following link to complete the change:<br/>
        <a href="%3$s">%3$s</a>
        <br/><br/>
        You can safely ignore and delete this email if you do not want to take this action or if you have received this email in error.
        <br/><br/>
        Regards,
        %5$s
        %6$s', 'buddypress' ),
        bp_core_get_user_displayname( bp_displayed_user_id() ),
        bp_get_site_name(),
        esc_url( bp_displayed_user_domain() . bp_get_settings_slug() . '/?verify_email_change=' . $hash ),
        $user_email,
        bp_get_site_name(),
        bp_get_root_domain()
    );

    return $email_text;
}
add_filter('bp_new_user_email_content',"ef_change_my_email",10,2);

function ef_change_comment_email($message, $comment_id)
{
    global $wpdb;
    global $post_type_email_text_override;
    $comment = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."comments where comment_ID = ".$comment_id);
    $post_comment = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."posts where ID = ".$comment->comment_post_ID);

    if(key_exists($post_comment->post_type,$post_type_email_text_override))
    {
      $post_comment_user = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."users where ID = ".$comment->user_id);
      $message = "<a href=\"" . $_SERVER['SERVER_NAME'] . "/" . pll_current_language() . "/members/" . $post_comment_user->user_nicename . "\">" . $comment->comment_author . "</a>" . sprintf(__(" commented on your %s ", "egyptfoss"),_x($post_type_email_text_override[$post_comment->post_type],"definite","egyptfoss")) . "<a href=\"" . get_the_permalink($post_comment->ID) . "\">" . $post_comment->post_title . "</a>";
      $message .= "<br/>";
      $message .= "<span style='display: block;width: 70%;padding: 30px;margin: 30px auto;border-radius: 10px;font-size: 18px;background-color: rgb(238, 238, 238);font-style: italic;'>" . $comment->comment_content . "</span>";

      $title = __("[EgyptFOSS] New Comment on ", "egyptfoss") . $post_comment->post_title;
      $button_title = sprintf(__("View Comment on the %s", "egyptfoss"),_x($post_type_email_text_override[$post_comment->post_type],"definite","egyptfoss"));
      $url = get_comment_link( $comment );

      $args = array(
        "title" => $title,
        "message" => $message,
        "url" => $url,
        "button_title" => $button_title
      );

      set_query_var( 'template_inputs', serialize($args));
      ob_start();
      get_template_part( 'mail-templates/email-content' );
      $message = ob_get_contents();
      ob_end_clean();
  }

  return $message;
}
add_filter('comment_notification_text',"ef_change_comment_email",10,2);

function ef_change_comment_subject($subject, $comment_id)
{
    global $wpdb;
    global $post_type_email_text_override;
    $comment = $wpdb->get_row("SELECT comment_post_ID FROM ". $wpdb->prefix."comments where comment_ID = ".$comment_id);
    $post_comment = $wpdb->get_row("SELECT ID,post_type,post_title FROM ". $wpdb->prefix."posts where ID = ".$comment->comment_post_ID);

    if(key_exists($post_comment->post_type,$post_type_email_text_override))
    {
      $subject = __("[EgyptFOSS] New Comment on ","egyptfoss").$post_comment->post_title;
    }

    return $subject;
}
add_filter('comment_notification_subject',"ef_change_comment_subject",10,2);

function ef_change_comment_From($from, $comment_id)
{
    global $wpdb;
    $blogName = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."options where option_name = 'blogname'");
    $fromEmail = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."options where option_name = 'mail_from'");
    $from = "From: \"$blogName->option_value\" <$fromEmail->option_value>";
    $from = "$from\n"
		. "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";

    return $from;
}
add_filter('comment_notification_headers',"ef_change_comment_From",10,2);

add_action( 'transition_post_status', 'ef_notify_authors', 10, 3 );
function ef_notify_authors( $new_status, $old_status, $post )
{
    $excludedArray = array('quiz');
    if($new_status == 'publish' && $old_status == 'pending' && !in_array($post->post_type, $excludedArray))
    {
        global $wpdb;
        global $ef_email_msg_labels;
        global $ef_email_msg_labels_ar;

        $user_id = $post->post_author;
        $file = get_user_meta($user_id, 'prefered_language', true);
        if($file == "en")
        {
            $messages = $ef_email_msg_labels;
        }
        else
        {
            $messages = $ef_email_msg_labels_ar;
        }
        $title = sprintf($messages['%s has been published'], $post->post_title);

        $msg = sprintf($messages['Hi, %s'],  bp_core_get_user_displayname($post->post_author))."<br/><br/>";
        $msg .= $messages['Thank you for your valuable contributions in EgyptFOSS.'];
        $msg .= sprintf($messages['We have reviewed your %s and it has been published.'],"<a href=\"".  get_the_permalink($post->ID)."\">".$post->post_title."</a>")."<br/><br/>";
        $msg .= $messages['We are looking forward more contribution from your side to enrich EgyptFOSS.']."<br/><br/>";
        $msg .= $messages['Thank you again!'];
        $args = array(
            "title" => $title,
            "message" => $msg,
            "language" => $file
        );

        set_query_var( 'template_inputs', serialize($args));
        ob_start();
        get_template_part( 'mail-templates/email-content' );
        $message = ob_get_contents();
        ob_end_clean();

        // Send the test mail
        $to = bp_core_get_user_email($post->post_author);
        $result = wp_mail($to,$title,$message);
    }
}

/** Hooks to over-ride mail from and mail name in WP&PHPMailer **/
add_action( 'phpmailer_init', 'ef_phpmailer_config' );
function ef_phpmailer_config( $phpmailer ) {
    /*$phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.example.com';
    $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
    $phpmailer->Port = 25;
    $phpmailer->Username = 'yourusername';
    $phpmailer->Password = 'yourpassword';*/

    // Additional settings…
    global $wpdb;
    $smtp_mailfrom = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."options where option_name = 'mail_from'");
    $smtp_mailfrom_name = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."options where option_name = 'mail_from_name'");
    $phpmailer->From = $smtp_mailfrom->option_value;
    $phpmailer->FromName = $smtp_mailfrom_name->option_value;
}

function ef_notify_response($from, $to, $thread) {
  $request = Post::find($thread->request_id);
  $type = ($request->post_type == 'service') ? 'service' : 'request';
  $from_user = User::find($from);
  $to_user = User::find($to);
  if($request->post_status == 'publish' && $thread->status == 1) {
    global $wpdb;
    global $ef_email_msg_labels;
    global $ef_email_msg_labels_ar;

    $user_id = $to_user->ID;
    $lang = get_user_meta($user_id, 'prefered_language', true);
    if($lang == "en") {
      $messages = $ef_email_msg_labels;
    } else {
      $messages = $ef_email_msg_labels_ar;
    }
    $from_about = '<a href="'. home_url().'/members/'.bp_core_get_username($from).'/about/'.'">'.bp_core_get_user_displayname($from).'</a>';
    $request_link = '<a href="'.get_the_permalink($request->ID).'">'.$request->post_title.'</a>';
    $thread_link = '<a href="'.get_current_lang_page_by_template('template-'.$type.'-thread.php').'?pid='.$request->ID."&tid=".$thread->id.'">'.$messages['here'].'</a>';

    $title = sprintf($messages['You have got a new reply on %s '.$type], $request->post_title);
    $msg = sprintf($messages['Hi, %s'],  bp_core_get_user_displayname($to))."<br/><br/>";
    $msg .= sprintf($messages['You have got a new reply from %s on %s '.$type.', check it %s.'], $from_about, $request_link, $thread_link)."<br/><br/>";
    $args = array(
      "title" => $title,
      "message" => $msg
    );
    set_query_var( 'template_inputs', serialize($args));
    ob_start();
    get_template_part( 'mail-templates/email-content' );
    $message = ob_get_contents();
    ob_end_clean();

    $result = wp_mail($to_user->user_email, $title, $message);
    return $result;
  }
}

function sendMarkedExpertEmail($user_id) {
  $is_email_sent = get_user_meta($user_id, 'is_expert_email_sent', true);
  if($is_email_sent)
    return true;

  $userData = get_user_by("ID", $user_id);
 // var_dump($userData->user_email);
  global $ef_email_msg_labels;
  global $ef_email_msg_labels_ar;
  $lang = get_user_meta($user_id, 'prefered_language', true);
  if ($lang == "en") {
    $lang = "en";
    $messages = $ef_email_msg_labels;
  } else {
    $lang = "ar";
    $messages = $ef_email_msg_labels_ar;
  }
  $template_inputs = array(
     "title" => $messages['expert_email_title'],
     "message" => sprintf($messages['expert_email_message'],bp_core_get_user_displayname($user_id)),
     "btn-title" => $messages['expert_email_btn'],
     "btn-url" => get_current_lang_page_by_template("template-add-expert-thought.php"),
     "lang" => $lang
    );
    ob_start();
    //get_template_part( 'mail-templates/expert_email' );
    include(locate_template('mail-templates/expert_email.php'));
    $message = ob_get_contents();
    ob_end_clean();
    foreach ($template_inputs as $key=>$input)
    {
      $message = str_replace('%ef-mail-template-'.$key.'%', $input, $message);
    }
    if(wp_mail($userData->user_email,$messages['expert_email_title'],$message))
    {
      update_user_meta($user_id, 'is_expert_email_sent', 1);
    }
}
