<?php

class NotifyResponseMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendresponsenotification($args, $response, $type="request") {
    $home_url = $seed = Option::limit(1)->Where('option_name', '=', "home")->first();  
    $this->mail->setFrom($args['from']['email'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);
    $this->mail->Subject = sprintf(__("You have got a new reply on %s $type","egyptfoss", $args['lang']), $args[$type.'_title']);
    $this->mail->isHTML(true);
   
    $style = '';
    if($args['lang'] == 'ar') {
      $style = 'style="direction:rtl;"';
    }
    $from_about = '<a href="'.$home_url->option_value.'/members/'.$args['from']['name'].'/about/'.'">'.$args['from']['name'].'</a>';
    $container_link = '<a href="'.$args[$type.'_url'].'">'.$args[$type.'_title'].'</a>';
    $thread_link = '<a href="'.$home_url->option_value.'/'.$args['lang'].'/'.$type.'-thread/'.'?pid='.$args[$type.'_id']."&tid=".$args['thread_id'].'">'.__('here', 'egyptfoss', $args['lang']).'</a>';

    $template_inputs = array(
    "title" => $this->mail->Subject,
    "message" => sprintf(__("Hi, %s", "egyptfoss",$args['lang']),$args['to']['name']).'<br/>'.
      sprintf(__("You have got a new reply from %s on %s $type, check it %s.","egyptfoss", $args['lang']), $from_about, $container_link, $thread_link),
    "site_url" => $home_url->option_value,
    "user_name_label" => "Username",
    "admin" => "",
    "footer" => "",
    "intro" => "",
    "style" => $style
    ); 
    ob_start();
    include(getcwd().'/app/mail_templates/notice.html'); 
    $message = ob_get_contents();
    ob_end_clean();
    foreach ($template_inputs as $key => $input) {
      $message = str_replace('%ef-mail-template-'.$key.'%', $input, $message);
    }
    $this->mail->Body = $message;
    return $this->sendMessage($this->mail,$response);
  }
}
