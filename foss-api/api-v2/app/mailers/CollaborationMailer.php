<?php

class CollaborationMailer extends Mailer{
  
  function __construct() {
    parent::__construct();
  }
  
  function sendCollaborationEmail($args,$response) {
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $this->mail->Subject = $args['title'];
    $this->mail->isHTML(true);
   
    $style = '';
    if($args['lang'] == 'ar')
    {
      $style = 'style="direction:rtl;"';
    }
    
    $template_inputs = array(
    "title" =>  $args['title'],
    "message" =>  $args['message'],
    "intro" => "",
    "footer" => "",
    "admin" => "",
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
