<?php

class ResetPasswordMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendResetPasswordMessage($args,$response) {
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();  
    $this->mail->setFrom($args['sender'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $this->mail->Subject = "[EgyptFOSS] ".__("Reset Password", 'egyptfoss',$args['lang']);
    $this->mail->isHTML(true);
   // $message = "Someone requested that the password be reset for the following account:" . "\r\n\r\n";
    //$message .= $home_url->option_value . "\r\n\r\n";
    //$message .= sprintf(("Username: %s"), $args["user_login"]) . "\r\n\r\n";
    //$message .= "If this was a mistake, just ignore this email and nothing will happen." . "\r\n\r\n";
   // $message .= "To reset your password, visit the following address:" . "\r\n\r\n";
   // $message .= "<" . $home_url->option_value."/en/login?action=rp&key=".$args['key']."&login=" .$args['user_login'] . ">\r\n";
    $activate_url =  $home_url->option_value."/en/login?action=rp&key=".$args['key']."&username=" .$args['user_login'];
    $template_inputs = array(
    "title" => __("Reset your Password", 'egyptfoss',$args['lang']),
    "message" => __("To reset your password, visit the following address", 'egyptfoss',$args['lang']),
    "user_name" => $args["user_login"],
    "url" => $activate_url,
    "button_title" => __("Reset Password", 'egyptfoss',$args['lang']),
    "user_name_label" => __("Username","egyptfoss",$args["lang"]),
    "ending_msg" => ""   
    ); 
    ob_start();
    include(getcwd().'/app/mail_templates/default.html'); 
    $message = ob_get_contents();
    ob_end_clean();
    foreach ($template_inputs as $key=>$input)
    {
      $message = str_replace('%ef-mail-template-'.$key.'%', $input, $message);
    }
    $this->mail->Body = $message;
    return $this->sendMessage($this->mail,$response);
  }
}
