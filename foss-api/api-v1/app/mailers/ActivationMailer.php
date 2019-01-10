<?php

class ActivationMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendActivationMessage($args,$response) {
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();  
    $this->mail->setFrom($args['sender'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $this->mail->Subject = "[EgyptFOSS] ". __("Welcome to EgyptFOSS","egyptfoss",$args["lang"]);
    $this->mail->isHTML(true);
    $activate_url =  $home_url->option_value."/activate/".$args['key'];
    $template_inputs = array(
    "title" => __("Welcome to EgyptFOSS","egyptfoss",$args["lang"]),
    "message" => __("Thank you for joining EgyptFOSS. To activate your account, please click the following link:","egyptfoss",$args["lang"]),
    "user_name" => $args["user_login"],
    "user_name_label" => __("Hello","egyptfoss",$args["lang"]),  
    "url" => $activate_url,
    "button_title" => __("Activate account","egyptfoss",$args["lang"]),
    "ending_msg" => __("activation_ending_msg","egyptfoss",$args["lang"])  
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
