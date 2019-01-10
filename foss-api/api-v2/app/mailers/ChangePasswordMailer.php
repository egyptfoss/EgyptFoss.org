<?php

class ChangePasswordMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendChangePasswordMessage($args, $response) {
    $admin_email = $seed = Option::limit(1)->Where('option_name', '=', "admin_email")->first();  
    $this->mail->setFrom($args['sender'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $this->mail->Subject = sprintf(__("[%s] Notice of Password Change",'wordpress',$_POST['lang']),'EGYPTFOSS');
    $this->mail->isHTML(true);
    $content = __("Hi ###USERNAME###,<br/> This email confirms that your password has been changed. If you did not request this change, please contact the <a href='###ADMIN_EMAIL###'>Site Administrator</a>.",'egyptfoss',$_POST['lang']);
    $content = str_replace('###USERNAME###', $args["user_login"], $content);
    $content = str_replace('###ADMIN_EMAIL###', "mailto:".$admin_email->option_value, $content);
    $template_inputs = array(
        "title" => __('Your password has been changed','egyptfoss',$args['lang']),
        "message" => $content,
        "intro" => "",
        "footer" => "",        
        "url" => "mailto:".$admin_email->option_value,
        "admin" => "Site Administrator",
        "user_name_label" => __("Username","egyptfoss",$args["lang"]),
    );
    ob_start();
    include(getcwd().'/app/mail_templates/notice.html'); 
    $message = ob_get_contents();
    ob_end_clean();
    foreach ($template_inputs as $key=>$input) {
      $message = str_replace('%ef-mail-template-'.$key.'%', $input, $message);
    }
    $this->mail->Body = $message;
    return $this->sendMessage($this->mail,$response);
  }
}
