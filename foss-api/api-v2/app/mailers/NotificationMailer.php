<?php

class NotificationMailer extends Mailer {

    function __construct() {
        parent::__construct();
    }

    function sendNotificationEmail($args,$body,$notificationType, $preferred_language) {
        $home_url = $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
        $this->mail->setFrom($args['sender'], 'EgyptFOSS');
        $this->mail->addAddress($args['to']['email'], $args['to']['name']);     // Add a recipient
        $title = $args['title'];
        $this->mail->Subject = $title;
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
        $direction = '';
        $dir_style = '';
        $txt_align = "style='text-align: left;'";
        $style_head = "";
        if($preferred_language == "ar")
        {
            $direction = "style='direction:rtl;'";
            $dir_style = 'direction:rtl;';
            $txt_align = "style='text-align: right;direction: rtl;'";
            $style_head = "style='text-align: right;direction: rtl;'";
        }
        $option = new Option();
        $host = $option->getOptionValueByKey('siteurl');
        $user_nicename = $args['nicenmae'];
        $footer = "<tr $direction>
      <td class=\"mail-footer\" style=\"padding:30px;background-color:#333;\">
        <table width=\"80%\" style=\"font-size:13px;color:#ccc;max-width:800px;padding:20px;$dir_style\" align=\"center\">
          <tr>
                <td style=\"text-align:center;\">
                  <span><a href=\"https://www.facebook.com/EgyptFOSSOrg\" target=\"_blank\" style=\"text-decoration:none;border-none;\">
                    <img src=\"$host/wp-content/themes/egyptfoss/mail-templates/images/facebook.png\" alt=\"Facebook\" />
                  </a></span>
                  <span><a href=\"https://twitter.com/EgyptFOSSOrg\" target=\"_blank\" style=\"text-decoration:none;border-none;\">
                    <img src=\"$host/wp-content/themes/egyptfoss/mail-templates/images/twitter.png\" alt=\"Twitter\" />
                  </a></span>
                </td>
              </tr>
              <tr>
                <td>
                  ".__("You are receiving this email because you are subscribed to EgyptFOSS notifications. Unsubscribe from these emails by changing your","egyptfoss",$preferred_language)." "."<strong><a href=\"$host/$preferred_language/members/$user_nicename/settings/notifications-settings/\" style=\"color:#49aa32\">".__("Notifications Settings","egyptfoss",$preferred_language)."</a></strong>
                </td>
              </tr>
            </table>
          </td>
        </tr>";
        
        $template_inputs = array(
          "title" => $title,
          "intro" => $args['intro'],
          "message" => $body,
          "admin" => "",
          "url" => "",
          "home-url" => $home_url->option_value."/",
          "user_name_label" => "Username",
          "direction" => $direction,
          "dir-rtl" => $dir_style,
          "footer" => $footer,
          "style" => $style_head
        );
        ob_start();
        include(getcwd() . '/app/mail_templates/notice.html');
        $message = ob_get_contents();
        ob_end_clean();
        foreach ($template_inputs as $key => $input) {
            $message = str_replace('%ef-mail-template-' . $key . '%', $input, $message);
        }
        $this->mail->Body = $message;
        return $this->sendMessage($this->mail, null);
    }

}
