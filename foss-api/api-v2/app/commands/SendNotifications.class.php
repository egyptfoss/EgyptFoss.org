<?php

namespace Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use User;
use Usermeta;
use Term;
use Post;
use Option;
use Quiz;
use Postmeta;

/**
 * Class responsible for sending email notifications to users based on their preferences
 *
 * @author maisara
 */
class SendNotifications extends Command {

    private $output;

    protected function configure() {
        $this
                ->setName('notifications:send')
                ->setDescription('Send Email Notifications')
                ->addArgument(
                        'date', InputArgument::OPTIONAL, 'Running Date, will use now() if not passed '
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->output = $output;
        
        $notificationTypes = array("notification_profile_updates", "notification_products_updates", 
          "notification_events_updates", "notification_news_updates", "notification_success_stories_updates", 
          "notification_open_datasets_updates","notification_request_center_updates", "notification_collaboration_center_updates", "notification_expert_thoughts_updates",
          "notification_awarness_center_updates", "notification_market_place_updates"  );
        
        $notificationTypesEmailNames = array("notification_profile_updates" => "Timeline", 
                "notification_products_updates" => "Products", 
                "notification_events_updates" => "Events",
                "notification_news_updates"  => "News",
                "notification_success_stories_updates" => "Success Stories",
                "notification_open_datasets_updates" => "Open Datasets",
                "notification_request_center_updates"=> "Requests",
                "notification_collaboration_center_updates"=> "Spaces/Documents",
                "notification_expert_thoughts_updates"=> "Expert Thoughts",
                "notification_awarness_center_updates" => "Quizzes",
                "notification_market_place_updates" => "Services"
          );
        
        $today = $input->getArgument('date');
        if (!$today) {
            $today = date("Y-m-d");
        } else {
            
        }
        $output->writeln("Running the job at day = ".$today);
        
        //get system interests
        // To be refactored after modifying the way of saving interests in edit profile
        $systemInterests = array();
        $interests = Term::join('term_taxonomy','terms.term_id', '=', 'term_taxonomy.term_id')
            ->select('terms.term_id', 'terms.name')
            ->where('term_taxonomy.taxonomy', '=', 'interest')
            ->get();
        foreach ($interests as $obj) {
            $systemInterests[$obj->name] = $obj->term_id;
        }

        //get users
        $users = User::all();
        foreach ($users as $user) {
            $output->writeln("*****************************************************************************"
                    . "\nProcessing user with ID = ".$user->ID. " and name = ".$user->user_nicename);
            // To be refactored after modifying the way of saving interests in edit profile
            $user_meta = Usermeta::where('user_id', '=', $user->ID)->where('meta_key', '=', 'registration_data')->first();
            if(!$user_meta) {
                $output->writeln("== User doesn't have meta record");
                continue;
            }
            $meta = $user_meta->getMeta($user->ID);
            if(!isset($meta['interest'])) {
                $output->writeln("== User doesn't have interests");
                continue;
            }
            $userInterestsValues = $meta['interest'];
            
            if(count($userInterestsValues) === 0) {
                continue;
            }
            
            $preferred_language = \Usermeta::getUserMeta($user->ID,"prefered_language");
            
            $userInterests = array();
            foreach ($userInterestsValues as $value) {
                $userInterests[] = $systemInterests[$value];
            }

            //set mail option
            $option = new Option();
            $args = array(
                "sender" => $option->getOptionValueByKey('mail_from'),
                "to" => array("email" => $user->user_email, "name" => $user->user_nicename)
            );
            
            $args['nicenmae'] = $user->user_nicename;
            $helloUserTitle = $user->display_name;
            if($helloUserTitle == '')
            {
              $helloUserTitle = $user->user_nicename;
            }
            foreach ($notificationTypes as $notificationType) {
                $output->writeln("== Notifying user with ".$notificationType);
                $notificationFrequency = Usermeta::where('user_id', '=', $user->ID)->where('meta_key', '=', $notificationType)->first();

                if ($notificationFrequency == null || $notificationFrequency->meta_value == 'Never') {
                    $output->writeln("---- User doesn't choose to get notified");
                    continue;
                }

                $fromDate = $today;//initial value

                switch ($notificationFrequency->meta_value) {
                    case 'Daily':
                        $output->writeln("---- User choose to get notified daily");
                        $fromDate = date('Y-m-d',strtotime("-1 day",  strtotime($today)));
                        $args['intro'] = sprintf(__("Hello %s,","egyptfoss",$preferred_language),$helloUserTitle)."<br/>".sprintf(__("Here are your daily %s updates based on your interests","egyptfoss", $preferred_language),  strtolower(__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language)));
                        $args['title'] = sprintf(__("Daily %s Updates","egyptfoss", $preferred_language),__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language));
                        break;
                    case 'Weekly':
                        $dayName = date('D',strtotime($today));
                        if($dayName != "Fri") {
                            $output->writeln("---- User choose to get notified weekly but today isn't Friday");
                            $fromDate = null;
                        } else {
                            $output->writeln("---- User choose to get notified weekly");
                            $fromDate = date('Y-m-d',strtotime("-1 week",  strtotime($today)));
                            $args['intro'] = sprintf(__("Hello %s,","egyptfoss",$preferred_language),$helloUserTitle)."<br/>".sprintf(__("Here are your weekly %s updates based on your interests","egyptfoss", $preferred_language),  strtolower(__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language)));
                            $args['title'] = sprintf(__("Weekly %s Updates","egyptfoss", $preferred_language),__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language));
                        }
                        break;
                    case 'Monthly':
                        $dayNumber = date('j',strtotime($today));
                        if($dayNumber != "1") {
                            $output->writeln("---- User choose to get notified monthly but today isn't 1st of month");
                            $fromDate = NULL;
                        } else {
                            $output->writeln("---- User choose to get notified monthly");
                            $fromDate = date('Y-m-d',strtotime("-1 month",  strtotime($today)));
                            $args['intro'] = sprintf(__("Hello %s,","egyptfoss",$preferred_language),$helloUserTitle)."<br/>".sprintf(__("Here are your monthly %s updates based on your interests","egyptfoss", $preferred_language),  strtolower(__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language)));
                            $args['title'] = sprintf(__("Monthly %s Updates","egyptfoss", $preferred_language),__($notificationTypesEmailNames[$notificationType],"egyptfoss",$preferred_language));
                        }    
                        break;
                    default :
                        $output->writeln("---- User choose to get notified with -- ");
                        continue; //to next loop iteration
                }
                
                if($fromDate) {
                    $output->writeln("---- Getting updates to be sent");
                    $updates = $this->getUpdatesByInterest($notificationType, $user->ID, $userInterests,$fromDate,$today, $output);
                    
                    if(count($updates) === 0) {
                        $output->writeln("---- No updates to be sent");
                        continue;
                    }

                    $output->writeln("---- Sending email");
                    $mailer = new \NotificationMailer();
                    $isSent = $mailer->sendNotificationEmail($args,  $this->prepareEmailBody($notificationType, $updates, $preferred_language), $notificationType, $preferred_language);
                }
            }
        }

        $output->writeln("Finished.");
    }
    
    private function prepareEmailBody($notificationType,$items, $preferred_language) {
        $body = "";
        $option = new Option();
        $host = $option->getOptionValueByKey('siteurl');
        switch ($notificationType) {
            case 'notification_profile_updates':
              $body = "";
              $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
              foreach ($items as $item) {
                  $txt_align = "style='text-align: left;'";
                  $user_name = User::where("ID", "=", $item->post_author)->First();
                  if($preferred_language == "ar")
                  {
                      $txt_align = "style='text-align: right;direction: rtl;'";
                  }
                  $date =  date_create($item->date_recoreded);
                  $post_date =  date_format($date,'j F Y');
                  $body .= '<tr '.$txt_align.'>
                        <td>
                        <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                          <tr '.$txt_align.'>
                          <td width="100%" style="border-bottom:solid 1px #ccc;padding:5px;"><a style="font-size:18px" href="'.$host.'/' . $preferred_language . '/members/'.$item->user_nicename.'/about">'.$item->user_login.'</a> <span style="font-size:18px">'.__("posted:","egyptfoss",$preferred_language).' '.  $post_date.' </span><br/>
                          <span style="margin-bottom:10px;display: block;width: 95%;padding: 20px;margin-top: 10px;border-radius: 10px;font-size: 18px;background-color: rgb(238, 238, 238);font-style: italic;">'.nl2br(str_replace("\'","'",$item->content)).'<a href="'.$host.'/' . $preferred_language . '/members/'.$item->user_nicename.'/activity/'.$item->id.'">'.__("Read","egyptfoss").'</a>'.'</span>
                          </td>
                        </tr></table></td></tr>';
                }
                $body .= '</table>';
                break;
            case 'notification_products_updates':
              $body = "";
              $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
              foreach ($items as $item) {
                    $txt_align = "style='text-align: left;'";
                    $user_name = User::where("ID", "=", $item->post_author)->First();
                    if($preferred_language == "ar")
                    {
                        if($item->industry_name_ar != '' && $item->industry_name_ar != null)
                        {
                          $item->industry_name = $item->industry_name_ar;
                        }
                        $txt_align = "style='text-align: right;direction: rtl;'";
                    }
                    if($item->product_logo != null && $item->product_logo != '')
                        $product_logo = $item->product_logo;
                    else {
                        $option = new Option();
                        $product_logo = $host."/wp-content/themes/egyptfoss/mail-templates/images/product.png";
                    }
                    
                  $body .= '<tr '.$txt_align.'>
                    <td>
                      <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                        <tr '.$txt_align.'>
                          <td style="width:60px;height:60px;vertical-align:top;">
                            <img src="'.$product_logo.'" alt="" style="width:50px;" />
                          </td>
                          <td style="vertical-align:top;padding:10px;line-height:1.5;">
                            <strong>
                              <span><a href="'. $host .'/'. $preferred_language .'/products/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                            </strong>
                            <br>
                            <span>
                              <small>'.__("Category","egyptfoss",$preferred_language).' <a href="'.$host.'/'.$preferred_language.'/products/?industry='.$item->slug.'" style="color:#777;">'.ucwords($item->industry_name).'</a></small>
                            </span>';
                        if($item->license != 'a:0:{}' && $item->license != '') {
                          $license_id = unserialize($item->license)[0];
                          //load term by license id
                          $license = Term::where('term_id','=',$license_id)->first();
                          $license_name = $license->name;
                          if($preferred_language == "ar")
                          {
                            if($license->name_ar != '' && $license->name_ar != null) {
                              $license_name = $license->name_ar;
                            }
                          }
                          $body .= '<br>
                            <span>
                              <small>'.__("License","egyptfoss",$preferred_language).' '.ucwords($license_name).'</small>
                            </span>';
                        }
                        if($item->developer !=  '') {
                          $body .= '<br>
                            <span>
                              <small>'.__("By","egyptfoss",$preferred_language).' '.$item->developer.'</small>
                            </span>';
                        }
                        $body .= '<p style="color:#333;">
                              <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->description)),30).'</span>
                            </p>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>';
                }
                
                //add view all products
                $body .= "<tr style=\"text-align: center;\">
                    <td style=\"background-color: #eee;\">
                      <table align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5\">
                            <strong>
                           <span><a style=\"color:#49aa32;font-size: 1.3em;\" href=\"$host/$preferred_language/products/\">".__("View All","egyptfoss",$preferred_language).' '.__("Products","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";
                $body .= "</table>";
                break;
            case 'notification_events_updates':
                //$body = "Events that match your interests <br/>";
                $body = "";
                $txt_align = "style='margin-left: auto;margin-right: auto;width: 100%;text-align: left;'";
                $txt_padding = "padding-right: 17px;";
                if($preferred_language == "ar")
                {
                    $txt_align = "style='margin-left: auto;margin-right: auto;width: 100%;text-align: right;direction: rtl;'";
                    $txt_padding = "";
                }              
                $body .= '<table class="events-list" cellspacing="0px" '.$txt_align.'>';
                foreach ($items as $item) {
                    $date_from=date_create($item->StartDate);
                    $date_to=date_create($item->EndDate);
                    if($item->Price == "0" || $item->Price == null)
                        $price = __("Free of Charge","egyptfoss", $preferred_language);
                    else
                        $price = $item->Price;
                    if($item->Symbol == null || $item->Price == "0" || $item->Price == null)
                        $symbol = "";
                    else{
                      global $ar_system_currencies;
                      if($preferred_language == "ar")
                      {
                        $symbol = $ar_system_currencies[$item->Symbol];
                      }else
                      {
                        $symbol = $item->Symbol;
                      }
                    }
                    $body .= '
                        <tr>
																							<td style="width:50px;border-bottom:solid 1px #ccc;padding-bottom:10px;">
																		<table cellspacing="0" style="width:50px;border: solid 1px #ddd;">
<tbody>
<tr>
<td style="padding: 3px;background-color: #4eaa32;color: #fff;text-align: center;font-size: 11px;font-weight: bold;">'.date("F",strtotime($item->StartDate)).'</td>
</tr>
<tr>
<td style="text-align: center;vertical-align: middle;height: 30px;font-size: 22px;font-weight: bold;">'.date("d",strtotime($item->StartDate)).'</td>
</tr>
</tbody>
  </table>
</td>
                                <td width="90%" style="border-bottom:solid 1px #ccc;padding:5px;vertical-align:top;"><a href="'. $host .'/'. $preferred_language .'/events/'. $item->post_name .'" style="color:#49aa32;font-weight:bold;    line-height: 1.6;
    padding-top: 18px;">'.$item->post_title.'</a>
																																<br>
																																<strong style="color:#777;">'.__("From","egyptfoss",$preferred_language).'</strong> '.date_format($date_from,"d/m/Y H:i").'
                                        <br>
              <strong style="color:#777;'.$txt_padding.'">'.__("To","egyptfoss",$preferred_language).'</strong>   '.date_format($date_to,"d/m/Y H:i").'
																																								<br>
																																								<strong style="color:#49aa32;">
																																								'.$price.$symbol.'
																																								</strong>
                                </td>

                        </tr>';
                }
                //add view all events
                $body .= "<tr style=\"text-align: center;\">
                    <td colspan=\"4\">
                      <table style=\"margin-bottom:30px;margin-top:20px;\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color: #eee;\">
                            <strong>
                              <span><a style=\"color:#49aa32;font-size: 1.3em;\" href=\"$host/$preferred_language/events/\">".__("View All","egyptfoss",$preferred_language).' '.__("Events","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";
                $body .= '</table>';
                break;
            case 'notification_news_updates':
              //$body = "News that match your interests <br/>";
              $body = "";
              $body .= '<table class="events-list" cellspacing="0px" style=\"margin-left: auto;margin-right: auto;width: 100%;text-align: left;line-height:24px;\">';
              foreach ($items as $item) {
                $user_name = User::where("ID", "=", $item->post_author)->First();
                $txt_align = "style='text-align: left;'";
                if($preferred_language == "ar")
                {
                    $txt_align = "style='text-align: right;direction: rtl;'";
                }                
                $body .= '
                  <tr '.$txt_align.'>
                    <td>
                      <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                        <tr '.$txt_align.'>
                          <td style="width:100px;vertical-align: top;padding-top: 10px;">
                            <img src="'.$item->image.'" style="width:100px;" />
                          </td>
                          <td style="vertical-align:top;padding:10px;line-height:1.5;">
                            <strong>
                              <span><a href="'. $host .'/'. $preferred_language .'/news/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                            </strong>
                            <br>
                            <span>
                              <small>'.__("By","egyptfoss",$preferred_language).' 
                                <a style="font-size:18px;color:#777;" href="'.$host.'/' . $preferred_language . '/members/'.$user_name->user_nicename.'/about">
                                  <span style="font-size:13px;">'.$user_name->display_name.'</span>
                                </a>
                              </small>
                            </span>
                            <p style="color:#333;">
                              <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->description)),30).'</span>
                            </p>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>';
              }
              //add view all news
                $body .= "<tr style=\"text-align: center;\">
                    <td>
                      <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                            <strong>
                              <span><a style=\"color:#49aa32;font-size: 1.3em;\" href=\"$host/$preferred_language/news/\">".__("View All","egyptfoss",$preferred_language).' '.__("News","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";              
              $body .= '</table>';
              break;
            case 'notification_success_stories_updates':
              //$body = "Success Stories that match your interests <br/>";
              $body = "";
              $body .= '<table class="events-list" cellspacing="0px" style="margin-left: auto;margin-right: auto;width: 100%;text-align: left;line-height:24px;">';
              foreach ($items as $item) {
                $user_name = User::where("ID", "=", $item->post_author)->First();
                $txt_align = "style='text-align: left;'";
                if($preferred_language == "ar")
                {
                  if($item->category_ar != '' && $item->category_ar != null)
                  {
                    $item->category = $item->category_ar;
                  }
                  $txt_align = "style='text-align: right;direction: rtl;'";
                }     
                $body .= '
                  <tr '.$txt_align.'>
                    <td>
                      <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                        <tr '.$txt_align.'>
                          <td style="width:100px;vertical-align:top;padding-top:10px;">
                            <img src="'.$item->image.'" style="width:100px;" />
                          </td>
                          <td style="vertical-align:top;padding:10px;line-height:1.5;">
                            <strong>
                              <span><a href="'.$host.'/'.$preferred_language.'/success-stories/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                            </strong>
                            <br>
                            <span>
                              <small>'.__("Category", "egyptfoss", $preferred_language).' 
                                <span style="font-size:13px;">'.$item->category.'</span>
                              </small>
                            </span>
                            <br>
                            <span>
                              <small>'.__("By", "egyptfoss", $preferred_language).' 
                                <a style="font-size:18px;color:#777;" href="'.$host.'/' . $preferred_language . '/members/'.$user_name->user_nicename.'/about">
                                  <span style="font-size:13px;">'.$user_name->display_name.'</span>
                                </a>
                              </small>
                            </span>
                            <p style="color:#333;">
                              <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->post_content)),30).'</span>
                            </p>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>';
              }
                //add view all success stories
                $body .= "<tr style=\"text-align: center;\">
                    <td>
                      <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5\">
                            <strong>
                              <span><a style=\"color:#49aa32;font-size: 1.3em;\" href=\"$host/$preferred_language/success-stories/\">".__("View All","egyptfoss",$preferred_language).' '.__("Success Stories","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";              
              $body .= '</table>';
              break;
            case 'notification_open_datasets_updates':
              //$body = "Open Datasets that match your interests <br/>";
              $body = "";
              $body .= '<table class="events-list" cellspacing="0px" style="margin-left: auto;margin-right: auto;width: 100%;text-align: left;line-height:24px;">';
              foreach ($items as $item) {
                $user_name = User::where("ID", "=", $item->post_author)->First();
                $txt_align = "style='text-align: left;'";
                if($preferred_language == "ar")
                {
                  //theme ar
                  if($item->theme_ar != '' && $item->theme_ar != null)
                  {
                    $item->theme = $item->theme_ar;
                  }
                  
                  //dataset type ar
                  if($item->dataset_type_ar != '' && $item->dataset_type_ar != null)
                  {
                    $item->dataset_type = $item->dataset_type_ar;
                  }

                  //dataset license ar
                  if($item->dataset_license_ar != '' && $item->dataset_license_ar != null)
                  {
                    $item->dataset_license = $item->dataset_license_ar;
                  }                  
                  $txt_align = "style='text-align: right;direction: rtl;'";
                }                     
                $body .= '
                  <tr '.$txt_align.'>
                    <td style="width:60px;vertical-align:top;text-align:center;">
                      <table>
                        <tr '.$txt_align.'>
                          <td style="width:50px;height:50px;background-color#eee;text-align:center;">
                            <img src="'.$host.'/wp-content/themes/egyptfoss/mail-templates/images/file.png" style="width:44px;" alt="" /> 
                           <!-- <img src="images/file.png" style="width:44px;" alt="" /> -->
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td style="vertical-align:top;padding:10px;line-height:1.5;">
                      <strong>
                        <span><a href="'. $host .'/'. $preferred_language .'/open-datasets/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                      </strong>
                      <br>
                      <span>
                        <small>'.__("Publisher", "egyptfoss",$preferred_language).' <span style="color:#777;">'.$item->publisher.'</span></small>
                      </span>
                      <br>
                      <span>
                        <small>'.__("Theme", "egyptfoss",$preferred_language).' <span style="color:#777;">'.$item->theme.'</span></small>
                      </span>
                      <br>
                      <span>
                        <small>'.__("Type", "egyptfoss",$preferred_language).' <span style="color:#777;">'.$item->dataset_type.'</span></small>
                      </span>
                      <br>
                      <span>
                        <small>'.__("License", "egyptfoss",$preferred_language).' <span style="color:#777;">'.$item->dataset_license.'</span></small>
                      </span>
                      <br>
                      <p style="color:#333;">
                        <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->description)),30).'</span>
                      </p>
                    </td>
                  </tr>';
              }
                //add view all datasets
                $body .= "<tr style=\"text-align: center;\">
                    <td colspan=\"2\">
                      <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                            <strong>
                              <span><a style=\"color:#49aa32;font-size: 1.3em;\" href=\"$host/$preferred_language/open-datasets/\">".__("View All","egyptfoss",$preferred_language).' '.__("Open Datasets","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";              
              $body .= '</table>';
              break;
            case 'notification_request_center_updates':
            $body = "";
            $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
            foreach ($items as $item) {
              $txt_align = "style='text-align: left;'";
              $user_name = User::where("ID", "=", $item->post_author)->First();
              if($preferred_language == "ar")
              {
                  $item->type = $item->type_ar;
                  if($item->theme_ar != null && $item->theme_ar != '')
                    $item->theme = $item->theme_ar;
                  $item->bussiness_relationship = $item->bussiness_relationship_ar;
                  $txt_align = "style='text-align: right;direction: rtl;'";
              }
              $body .= '<tr '.$txt_align.'>
                <td>
                <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                <tr '.$txt_align.'>
                  <td style="width:60px;vertical-align:top;padding-top:10px;text-align:center;">
                    <img src="'.$host.'/wp-content/themes/egyptfoss/mail-templates/images/'.$item->type_slug.'_icon.png" alt="'.$item->post_title.'" style="width:50px;" />
                  </td>
                  <td style="vertical-align:top;padding:10px;line-height:1.5;">
                    <strong>
                      <span><a href="'. $host .'/'. $preferred_language .'/request-center/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                    </strong>
                    <br>
                    <span>
                      <small>'.__("Target business relationship","egyptfoss",$preferred_language).'<a href="'.$host.'/'.$preferred_language.'/request-center?target='.$item->bussiness_relationship.'"> <span style="color:#777;">'.ucwords($item->bussiness_relationship).'</span></a></small>
                    </span>
                    <br>
                    <span>
                      <small>'.__("Theme","egyptfoss",$preferred_language).'<a href="'.$host.'/'.$preferred_language.'/request-center?theme='.$item->theme.'"> <span style="color:#777;">'.ucwords($item->theme).'</span></a></small>
                    </span>';

                    if($item->deadline != '') {  
                      $body .= '<br>
                      <span>
                        <small>'.__("Due Date","egyptfoss",$preferred_language).' <span style="color:#777;">'.$item->deadline.'</span></small>
                      </span>';
                    }
                    $body .= '<p style="color:#333;">
                      <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->description)),30).'</span>
                    </p>
                  </td>
                </tr></table>
                </td></tr>';
            }
              //add view all requests
              $body .= "<tr style=\"text-align: center;\">
                  <td>
                    <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                      <tbody><tr style=\"text-align: center;\">
                        <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                          <strong>
                            <span><a style=\"color:#49aa32;\" href=\"$host/$preferred_language/request-center/\">".__("View All","egyptfoss",$preferred_language).' '.__("Requests","egyptfoss",$preferred_language)."</a></span>
                          </strong>
                        </td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>";              
            $body .= '</table>';
            break;      
            case 'notification_collaboration_center_updates':
            $body = "";
            $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
            $spacesShared = array();
            foreach ($items as $item) {
              //check if space already added to the body
              if($item->is_space)
              {
                array_push($spacesShared, $item->ID);
              }else
              {
                if(in_array($item->item_ID, $spacesShared))
                {
                  continue;
                }
              }

              $user_name = User::where("ID", "=", $item->owner_id)->first();
              $txt_align = "style='text-align: left;'";
              $created_date = date_create($item->created_at);
              $date_string = date_format($created_date,"d F Y");
              if($preferred_language == "ar")
              {      
                $arabic_months = self::ef_return_arabic_months();
                $date_string = str_replace(date_format($created_date,"F"), $arabic_months[date_format($created_date,"M")], $date_string);
                $txt_align = "style='text-align: right;direction: rtl;'";
              }
              $file_name = 'file_icon.png';
              $url = '';
              $img_width = "32px";
              if($item->is_space == 1)
              {
                $url = $host.'/'.$preferred_language.'/collaboration-center/shared/spaces/'.$item->ID;
                $file_name = 'space_icon.png';
                $img_width = "36px";
              }else{
                $url = $host.'/'.$preferred_language.'/collaboration-center/spaces/'.$item->item_ID.'/document/'.$item->ID.'/edit/';
              }

              $body .= '
                <tr '.$txt_align.'>
                  <td style="width:60px;vertical-align:top;text-align:center;">
                    <table>
                      <tr '.$txt_align.'>
                        <td style="width:50px;height:50px;background-color#eee;text-align:center;">
                          <img src="'.$host.'/wp-content/themes/egyptfoss/img/'.$file_name.'" style="width:'.$img_width.';padding-top:10px;" alt="" /> 
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td style="vertical-align:top;padding:10px;line-height:1.5;">
                    <strong>
                      <span><a href="'.$url.'" style="font-size:18px;color:#49aa32;">'.$item->title.'</a></span>
                    </strong>
                    <br>
                    <span>
                      <small>'.__("Created at", "egyptfoss",$preferred_language).' <span style="color:#777;">'.$date_string.'</span></small>
                    </span>';
                    if($item->is_space != 1)
                    {
                      $body .= '<br>
                      <span>
                        <small>'.__("By", "egyptfoss", $preferred_language).' 
                          <a style="font-size:18px;color:#777;" href="'.$host.'/' . $preferred_language . '/members/'.$user_name->user_nicename.'/about">
                            <span style="font-size:13px;">'.$user_name->display_name.'</span>
                          </a>
                        </small>
                      </span>';
                    }
                    $body .= '</td>
                </tr>';
            }
            $body .= '</table>';
            break;
            case 'notification_expert_thoughts_updates':
                $body = "";
                $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
                foreach ($items as $item) {
                  $txt_align = "style='text-align: left;'";
                  if( $preferred_language == 'ar' ) {
                    $txt_align = "style='text-align: right;'";
                  }
                  $user_name = User::where("ID", "=", $item->post_author)->First();
                  $body .= '<tr '.$txt_align.'>
                    <td>
                    <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                    <tr '.$txt_align.'>
                      <td style="width:60px;vertical-align:top;padding-top:10px;text-align:center;">
                        <img src="'.$item->image.'" alt="'.$item->post_title.'" style="width:50px;height:50px" />
                      </td>
                      <td style="vertical-align:top;padding:10px;line-height:1.5;">
                        <strong>
                          <span><a href="'.$host.'/'.$preferred_language.'/expert-thoughts/'.$item->post_name.'" style="font-size:18px;color:#49aa32;">'.$item->post_title.'</a></span>
                        </strong>';
                        $body .= '
                        <div>
                          <small>'.__("By","egyptfoss",$preferred_language).' 
                            <a style="font-size:18px;color:#777;" href="'.$host.'/' . $preferred_language . '/members/'.$user_name->user_nicename.'/about">
                              <span style="font-size:13px;">'.$user_name->display_name.'</span>
                            </a>
                          </small>
                        </div>  
                        <p style="color:#333;">
                          <span>'.self::shorten_string(str_replace("\'","'", html_entity_decode($item->post_content)),30).'</span>
                        </p>
                      </td>
                    </tr></table>
                    </td></tr>';
                }
                  //add view all thoughts
                  $body .= "<tr style=\"text-align: center;\">
                      <td>
                        <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                          <tbody><tr style=\"text-align: center;\">
                            <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                              <strong>
                                <span><a style=\"color:#49aa32;\" href=\"$host/$preferred_language/expert-thoughts/\">".__("View All","egyptfoss",$preferred_language).' '.__("Thoughts", "egyptfoss", $preferred_language)."</a></span>
                              </strong>
                            </td>
                          </tr>
                        </tbody></table>
                      </td>
                    </tr>";              
                $body .= '</table>';
              break;      
            case "notification_awarness_center_updates":
              $body = "";
              $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
              foreach ($items as $item) {
                $txt_align = "style='text-align: left;'";
                $user_name = User::where("ID", "=", $item->post_author)->First();
                $created_date = date_create($item->post_date);
                $date_string = date_format($created_date,"d F Y");   
                
                $post_meta = new Postmeta();
                $meta = $post_meta->getPostMeta($item->ID);
                $awarness_center_meta = array();
                foreach ($meta as $meta_key => $meta_value ) {
                  $awarness_center_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
                }
                unset($meta_value);

                //get category
                $category = "";
                if(array_key_exists('category', $awarness_center_meta))
                {
                  $term = new Term();
                  $type_id = $awarness_center_meta['category'];
                  $typeObj = $term->getTerm( $type_id );
                  $category = \EgyptFOSSController::ef_return_name_by_lang($typeObj, $preferred_language);
                }

                //get interests
                $interests = array();
                if(array_key_exists('interest', $awarness_center_meta))
                {
                  $interests_arr = unserialize($awarness_center_meta['interest']);
                  for($i = 0; $i < sizeof($interests_arr); $i++)
                  {
                      if($interests_arr[$i] != '')
                      {
                          $term = new Term();
                          $interest_id = $interests_arr[$i];
                          array_push($interests, $term->getTerm($interest_id)->name);
                      }
                  }
                }
                
                if($preferred_language == "ar")
                {                  
                  $arabic_months = self::ef_return_arabic_months();
                  $date_string = str_replace(date_format($created_date,"F"), $arabic_months[date_format($created_date,"M")], $date_string);
                  
                  $txt_align = "style='text-align: right;direction: rtl;'";
                }
                $body .= '<tr '.$txt_align.'>
                  <td>
                  <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                  <tr '.$txt_align.'>
                    <td style="width:60px;vertical-align:top;padding-top:10px;text-align:center;">
                      <img src="'.$host.'/wp-content/themes/egyptfoss/img/quizzes.png" alt="'.$item->quiz_name.'" style="width:50px;" />
                    </td>
                    <td style="vertical-align:top;padding:10px;line-height:1.5;">
                      <strong>
                        <span><a href="'. $host .'/'. $preferred_language .'/awareness-center/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">'.$item->quiz_name.'</a> <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#link-here" style="height:36px;v-text-anchor:middle;width:100px;" arcsize="2%" strokecolor="#4caf50" fillcolor="#4caf50"><w:anchorlock/><center style="color:#ffffff;font-family:sans-serif, Arial,sans-serif;font-size:16px;">Take Quiz</center></v:roundrect><![endif]--><a style="width:100px;display: inline-block;background-color: #4caf50;padding: 5px 8px;color: #FFF;text-decoration: none;font-family: sans-serif;margin-right: 20px;margin-left: 20px;line-height: 25px;" href="'. $host .'/'. $preferred_language .'/awareness-center/'. $item->post_name .'">'.__("Take Quiz","egyptfoss",$preferred_language).'</a></span>
                      </strong>
                      <br>
                      <span>
                        <small>'.__("Category","egyptfoss",$preferred_language).'<a href="'.$host.'/'.$preferred_language.'/awareness-center?category='.$category.'"> <span style="color:#777;">'.ucwords($category).'</span></a></small>
                      </span>
                      <br>
                      <span>
                        <small>'.__("Created at","egyptfoss",$preferred_language).' <span style="color:#777;">'.$date_string.'</span></small>
                      </span>';

                      if($item->success_rate != "-1.00") {  
                        $body .= '<br>
                        <span>
                          <small>'.__("Success Rate:","egyptfoss",$preferred_language).' <span style="color:#777;">'.number_format($item->success_rate, 2, '.', '').'%</span></small>
                        </span>';
                      }
                      
                      if(sizeof($interests) > 0)
                      {
                        $body .= '<br>';
                        foreach($interests as $interest)
                        {
                          $body .= '<span style="background-color: #eee;color: #4caf50;padding: 2px 4px;display: inline-block;margin-bottom: 4px;margin-top: 4px;margin-right: 3px;margin-left: 3px;font-size: 13px;">
                            <small>'.$interest.' </small>
                          </span>';
                        }
                      }
                      $body .= '
                    </td>
                  </tr></table>
                  </td></tr>';
              }
                //add view all requests
                $body .= "<tr style=\"text-align: center;\">
                    <td>
                      <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                            <strong>
                              <span><a style=\"color:#49aa32;\" href=\"$host/$preferred_language/awareness-center/\">".__("View All","egyptfoss",$preferred_language).' '.__("Quizzes","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";              
              $body .= '</table>';              
              break;
            case "notification_market_place_updates":
              $body = "";
              $body .= '<table style="border: solid #ccc 1px;padding: 10px;"width="100%" align="center">';
              foreach ($items as $item) {
                $txt_align = "style='text-align: left;'";
                $user_name = User::where("ID", "=", $item->post_author)->First();
                $created_date = date_create($item->post_date);
                $date_string = date_format($created_date,"d F Y");   
                
                $post_meta = new Postmeta();
                $meta = $post_meta->getPostMeta($item->ID);
                $market_place_meta = array();
                foreach ($meta as $meta_key => $meta_value ) {
                  $market_place_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
                }
                unset($meta_value);

                //get category
                $category = "";
                if(array_key_exists('service_category', $market_place_meta))
                {
                  $term = new Term();
                  $type_id = $market_place_meta['service_category'];
                  $typeObj = $term->getTerm( $type_id );
                  $category = \EgyptFOSSController::ef_return_name_by_lang($typeObj, $preferred_language);
                }

                //get interests
                $interests = array();
                if(array_key_exists('interest', $market_place_meta))
                {
                  $interests_arr = unserialize($market_place_meta['interest']);
                  for($i = 0; $i < sizeof($interests_arr); $i++)
                  {
                      if($interests_arr[$i] != '')
                      {
                          $term = new Term();
                          $interest_id = $interests_arr[$i];
                          array_push($interests, $term->getTerm($interest_id)->name);
                      }
                  }
                }
                
                if($preferred_language == "ar")
                {                  
                  $arabic_months = self::ef_return_arabic_months();
                  $date_string = str_replace(date_format($created_date,"F"), $arabic_months[date_format($created_date,"M")], $date_string);
                  
                  $txt_align = "style='text-align: right;direction: rtl;'";
                }
                $body .= '<tr '.$txt_align.'>
                  <td>
                  <table align="center" width="100%" class="news-card" style="margin-bottom:30px;" >
                  <tr '.$txt_align.'>
                    <td style="width:60px;vertical-align:top;padding-top:10px;text-align:center;">
                      <img src="'.$item->image.'" alt="'.$item->post_title.'" style="width:50px;height:50px" />
                    </td>
                    <td style="vertical-align:top;padding:10px;line-height:1.5;">
                      <strong>
                        <span><a href="'. $host .'/'. $preferred_language .'/marketplace/services/'. $item->post_name .'" style="font-size:18px;color:#49aa32;">' . self::shorten_string( $item->post_title, 23 ) . '</a>
                      </strong>
                      <br>
                      <span>
                        <small>'.__("Category","egyptfoss",$preferred_language).'<a href="'.$host.'/'.$preferred_language.'/marketplace/services/?category='.$category.'"> <span style="color:#777;">'.ucwords($category).'</span></a></small>
                      </span>
                      <br>
                      <span>
                        <small>'.__("Created at","egyptfoss",$preferred_language).' <span style="color:#777;">'.$date_string.'</span></small>
                      </span>';
                      
                      if(sizeof($interests) > 0)
                      {
                        $body .= '<br>';
                        foreach($interests as $interest)
                        {
                          $body .= '<span style="background-color: #eee;color: #4caf50;padding: 2px 4px;display: inline-block;margin-bottom: 4px;margin-top: 4px;margin-right: 3px;margin-left: 3px;font-size: 13px;">
                            <small>'.$interest.' </small>
                          </span>';
                        }
                      }
                      $body .= '
                    </td>
                  </tr></table>
                  </td></tr>';
              }
                //add view all requests
                $body .= "<tr style=\"text-align: center;\">
                    <td>
                      <table style=\"margin-bottom:30px\" align=\"center\" width=\"100%\">
                        <tbody><tr style=\"text-align: center;\">
                          <td style=\"vertical-align:top;padding:10px;line-height:1.5;background-color:#eee;\">
                            <strong>
                              <span><a style=\"color:#49aa32;\" href=\"$host/$preferred_language/marketplace/services/\">".__("View All","egyptfoss",$preferred_language).' '.__("Services","egyptfoss",$preferred_language)."</a></span>
                            </strong>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>";              
              $body .= '</table>';              
              break;
        }
        
        return $body;
    }
    
    function shorten_string($string, $wordsreturned)
    {
      $retval = $string;
      $string = preg_replace('/(?<=\S,)(?=\S)/', ' ', $string);
      $string = str_replace("\n", " ", $string);
      $array = explode(" ", $string);
      if (count($array)<=$wordsreturned)
      {
        $retval = $string;
      }
      else
      {
        array_splice($array, $wordsreturned);
        $retval = implode(" ", $array)." ...";
      }
      return $retval;
    }
    
    
    function ef_return_arabic_months()
    {
        $months = array(
            "Jan" => "يناير",
            "Feb" => "فبراير",
            "Mar" => "مارس",
            "Apr" => "أبريل",
            "May" => "مايو",
            "Jun" => "يونيو",
            "Jul" => "يوليو",
            "Aug" => "أغسطس",
            "Sep" => "سبتمبر",
            "Oct" => "أكتوبر",
            "Nov" => "نوفمبر",
            "Dec" => "ديسمبر"
        );
        return $months;
    }
    
    private function getUpdatesByInterest($notificationType,$user_id,$userInterests,$fromDate,$today, $output) {
        $updates = array();
        switch ($notificationType) {
            case 'notification_profile_updates':
                $activity = new \Activity();
                $updates = $activity->getUpdatesByInterest($userInterests,$fromDate,$today);
                $output->writeln("---- Profile Updates to send: ".sizeof($updates));
                break;
            case 'notification_products_updates':
                $post = new Post();
                $updates = $post->getProductsByInterest($userInterests,$fromDate,$today);
                $output->writeln("---- Product Updates to send: ".sizeof($updates));
                break;
            case 'notification_events_updates':
                $post = new Post();
                $updates = $post->getEventsByInterest($userInterests,$fromDate,$today);
                $output->writeln("---- Event Updates to send: ".sizeof($updates));
                break;
            case 'notification_news_updates':
              $post = new Post();
              $updates = $post->getNewsByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- News Updates to send: ".sizeof($updates));
              break;
            case 'notification_success_stories_updates':
              $post = new Post();
              $updates = $post->getSuccessStoriesByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- Success Stories Updates to send: ".sizeof($updates));
              break;
            case 'notification_open_datasets_updates':
              $post = new Post();
              $updates = $post->getOpenDatasetsByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- Open Datasets Updates to send: ".sizeof($updates));
              break;
            case 'notification_request_center_updates':
              $post = new Post();
              $updates = $post->getRequestsByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- Requests Updates to send: ".sizeof($updates));
              break;   
            case 'notification_collaboration_center_updates':
              $collabItems = new \CollaborationCenterItem();
              $updates = $collabItems->getSharedItemsByUser($user_id,$fromDate,$today, true)->get();
              $output->writeln("---- Collaboration Center Updates to send: ".sizeof($updates));
              break;               
            case 'notification_expert_thoughts_updates':
              $post = new Post();
              $updates = $post->getExpertThoughtsByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- Expert Thoughts Updates to send: ".sizeof($updates));
              break;
            case 'notification_awarness_center_updates':
              $args = array();
              $args['category_id'] = '';
              $args['user_id'] = 0;
              $args['numberOfData'] = -1;
              $args['lang'] = '';
              $quizzes = new Quiz();
              $updates = $quizzes->listQuizes($args, $userInterests,$fromDate,$today);
              $output->writeln("---- Quizzes Updates to send: ".sizeof($updates));
              break;
            case 'notification_market_place_updates':
              $post = new Post();
              $updates = $post->getServicesByInterest($userInterests,$fromDate,$today);
              $output->writeln("---- Market Place Updates to send: ".sizeof($updates));
              break;
        }
        return $updates;
    }
}
