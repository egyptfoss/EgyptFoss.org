<?php
class BadgeMailer extends Mailer {

  function __construct() {
    parent::__construct();
  }

  public function sendNewBadgeAchieved($user_id, $badge,$action = NULL, $user_email = '', $display_name = '', $usernice_name = '') {
           
    if(empty($user_email))
    {
      $user = User::find($user_id);
      $displayName = $user->display_name;
      $userEmail = $user->user_email;
      $userNicename = $user->user_nicename;
    }else
    {
      $displayName = $display_name;
      $userEmail = $user_email;
      $userNicename = $usernice_name; 
    }
    
    //Load badge in case of other Object
    $badge = EFBBadge::find($badge->id);
    
    $usermeta = new Usermeta();
    $preferred_language = $usermeta->getUserMeta($user_id, "prefered_language");
    //set mail option
    $option = new Option();
    $args = array(
      "title" => sprintf(__("You have earned the %s badge.", "efbadges",$preferred_language), $badge->getTitle($preferred_language)),
      "sender" => $option->getOptionValueByKey('mail_from'),
      "to" => array("email" => $userEmail, "name" => $userNicename));

    $home_url = Option::limit(1)->Where('option_name', '=', "home")->first();
    $this->mail->setFrom($args['sender'], 'EgyptFOSS');
    $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
    $title = $args['title'];
    $this->mail->Subject = $title;
    $this->mail->isHTML(true);
    $this->mail->CharSet = 'UTF-8';
    $msg = sprintf(__("Congratulations! You have earned the <strong>%s</strong> badge.", "efbadges", $preferred_language), $badge->getTitle($preferred_language));
    if($badge->name == "suggestions_l1"){
      $msg = sprintf(__("Congratulations! You have earned the <strong>%s</strong> badge for adding <strong>%s</strong>.", "efbadges", $preferred_language), $badge->getTitle($preferred_language),__($action["post_type"],"efbadges",$preferred_language));
    }
    $template_inputs = array(
      "title" => $args['title'],
      "message" => $msg,
      "btn_title" => __("View all badges", "efbadges", $preferred_language),
      "btn_url" => $home_url->option_value."/$preferred_language/members/".$userNicename."/badges/",
      "user_name" => $userNicename,
      "badge" => $badge,
      "home_url" => $home_url,
      "action" => $action
    );
    ob_start();
    include(getcwd() . '/app/mail_templates/badge.php');
    $message = ob_get_contents();
    ob_end_clean();
    $this->mail->Body = $message;
    return $this->sendMessage($this->mail, null);
  }

  public function sendBadgeEmail( $args ) {
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
    
    return $this->sendMessage( $this->mail, NULL );
  }
}
