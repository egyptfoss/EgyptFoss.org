<?php

class ChangeEmailMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendChangeEmailMessage($args,$response) {
    $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();  
    $this->mail->setFrom($args['sender'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $this->mail->Subject = "[EgyptFOSS] ".__("Verify your new email","egyptfoss",$args['lang']);
    $this->mail->isHTML(true);
   
    $style = '';
    if($args['lang'] == 'ar')
    {
      $style = 'style="direction:rtl;"';
    }
    
    $verifying_email =  $home_url->option_value."/members/".$args['to']['name'].'/settings/?verify_email_change='.$args['hash'];
    $template_inputs = array(
    "title" => __("Verify your new email","egyptfoss",$args['lang']),
   // "message" => "You recently changed the email address associated with your account on EgyptFOSS. <br/>
       // If this is correct, please click on the following link to complete the change:<br/>",
    "message" => __("Username","egyptfoss",$args['lang']).sprintf(" : %s",$args['display_name'])."<br/><br/>". 
        __("To verify your new email address, visit the following address","egyptfoss",$args['lang']),
    "user_name" => $args['to']['name'],
    "url" => $verifying_email,
    "site_url" => $home_url->option_value,
    "user_name_label" => "Username",
    "footer" => "",
    "intro" => "",
    "admin" => $verifying_email,
    "style" => $style
    ); 
    ob_start();
    include(getcwd().'/app/mail_templates/notice.html'); 
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
