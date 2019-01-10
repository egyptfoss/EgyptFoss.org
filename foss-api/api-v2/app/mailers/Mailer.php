<?php

//require '../../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

class Mailer extends EgyptFOSSController{

  function __construct() {
    $mail = new PHPMailer();
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $option = new Option();
    $mailer = $option->getOptionValueByKey('wp_mail_smtp');
    $mailer = unserialize($mailer);
    if( $mailer['mail']['mailer'] == 'mail' ) {
      $mail->Host = 'localhost';
    }
    else {
      $smtp_auth = $option->getOptionValueByKey('smtp_auth');
      if($smtp_auth == "false"){
          $smtp_auth = false;
      }
      else {
          $smtp_auth = true;
      }
      // Set mailer to use SMTP
      $mail->Host = $option->getOptionValueByKey('smtp_host');  // Specify main and backup SMTP servers
      $mail->SMTPAuth = $smtp_auth;                               // Enable SMTP authentication
      $mail->Username = $option->getOptionValueByKey('smtp_user');                 // SMTP username
      $mail->Password = $option->getOptionValueByKey('smtp_pass');                           // SMTP password
      $mail->Port = (int)$option->getOptionValueByKey('smtp_port');
    }

    $this->mail = $mail;
    $this->mail->CharSet = 'UTF-8';
  }

  protected function sendMessage($mail,$response) {
    //set mail from and mail name
    $option = new Option();
    $mail->From = $option->getOptionValueByKey('mail_from');
    $mail->FromName = $option->getOptionValueByKey('mail_from_name');
    if (!$mail->send()) {
      return $mail->ErrorInfo;
    } else {
      return true;
    }
  }

}
