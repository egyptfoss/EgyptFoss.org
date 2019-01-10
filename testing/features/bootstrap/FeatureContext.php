<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Driver\KernelDriver;
use PHPUnit_Framework_ExpectationFailedException as AssertException;
use tPayne\BehatMailExtension\Context\MailAwareContext;
use tPayne\BehatMailExtension\Context\MailTrait;

use GuzzleHttp\Client;
use tPayne\BehatMailExtension\Message;
use tPayne\BehatMailExtension\MessageFactory;
use tPayne\BehatMailExtension\Driver\Mail;

//var_dump(dirname($_SERVER['PWD']));
require_once(dirname($_SERVER['PWD']).'/db-config.php');
include 'config/testData.php';
require 'config/settings.php';

class FeatureContext extends MinkContext implements SnippetAcceptingContext, MailAwareContext, Mail
{
    use NavigationTrait;
    use ProfileTrait;
    use ViewTrait;
    use BackendTrait;
    use MailTrait;
    use EmailTrait;
    use TestTrait;
    use NewsTrait;
    use ShareTrait;
    use EventTrait;
    use OpenDatasetTrait;
    use SuccessStoryTrait;
    use CollaborationCenterTrait;
    use ExpertThoughtTrait;

    public $client;

    public function fixStepArgument($argument){
      return parent::fixStepArgument($argument);
    }

    public function __construct()
    {
        $url = $this->buildUrl('localhost', '1080');
        $this->client = new Client(['base_url' => $url]);
    } 
       
    /**
    * @Given /^there are following users:$/
    */
    public function thereAreFollowingUsers(TableNode $table)
    {
        $users = array();
        foreach ($table as $userHash) {
            $userClass = new User();
            $hasher = new PasswordHash(8, true);
            $userHash['password'] = $hasher->HashPassword( trim( $userHash['password'] ) );            
            $user = $userClass->addUser($userHash);
            $user->save();
            $users[] = $user;
        }
        return $users;
    }

    /**
     * @When /^I click "([^"]*)" on the popup window$/
     */
    public function iClickButtonOnPopup($textButton)
    {
      if($textButton == "Delete" || $textButton == "OK")
      {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
      }  else {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
      }  
    /* $element = $this->featuresObject->getSession()->getPage()->find('css', sprintf('div[aria-describedby="dialog-confirm"] button span:contains("%s")', $textButton));
      if (!$element) {
      throw new Exception\ElementNotFoundException($this->featuresObject->getSession(), 'element', 'css', $element);
      }
     $element->click();*/
    }

    /**
     * @AfterScenario @revoke-linkedin
     */
    public function revokeLinkedIn()
    {
      $this->getSession()->visit("https://www.linkedin.com/");

      //Fill Login Credentials
      $field = $this->fixStepArgument('session_key');
      $value = $this->fixStepArgument('buggy.tamtam1@gmail.com');
      $this->getSession()->getPage()->fillField($field, $value);

      $field = $this->fixStepArgument('session_password');
      $value = $this->fixStepArgument('buggy123');
      $this->getSession()->getPage()->fillField($field, $value);

      //Press Sign In btn
      $button = $this->fixStepArgument('Sign in');
      $this->getSession()->getPage()->pressButton($button);

      //Wait for redirection
      self::waitForRedirection();

      $this->getSession()->visit("https://www.linkedin.com/secure/settings?userAgree=");
      
      //Check remove btn
      $option = $this->fixStepArgument('api_apps');
      $this->getSession()->getPage()->checkField($option);

      //Press Save btn
      $button = $this->fixStepArgument('updUA');
      $this->getSession()->getPage()->pressButton($button);

      //Close Browser
      $this->getSession()->getDriver()->stop();
    }

    /**
     * @AfterScenario @revoke-facebook
     */
    public function revokeFacebook()
    {
      $this->getSession()->visit("https://www.facebook.com/settings?tab=applications");

      //Wait for rediretion
      self::waitForRedirection();

      $js  = "try { document.querySelector('[aria-label=\"Remove\"]').click(); }catch(err) {}";
      $this->getSession()->executeScript($js);

      $this->getSession()->wait(5000);

      $js  = "try { document.querySelector('[ajaxify=\"/ajax/settings/apps/delete_app.php?app_id=1521479591478592&legacy=1&dialog=1\"]').click(); }catch(err) {}";
      $this->getSession()->executeScript($js);

      $this->getSession()->wait(5000);

      $js  = "try { document.querySelector('[value=\"Remove\"]').click(); }catch(err) {}";
      $this->getSession()->executeScript($js);

      $this->getSession()->getDriver()->stop();
    }


    /**
     * @AfterScenario @revoke-twitter
     */
    public function revokeTwitter()
    {
      $this->getSession()->visit("https://twitter.com/settings/applications");

      //Wait for rediretion
      self::waitForRedirection();


      $button = $this->fixStepArgument('Revoke access');
      $this->getSession()->getPage()->pressButton($button);

      $this->getSession()->getDriver()->stop();
    }


    /**
     * @AfterScenario @revoke-googleplus
     */
    public function revokeGoogleplus()
    {
      $this->getSession()->visit("https://security.google.com/settings/security/permissions?pli=1");
      
      //Wait for rediretion
      self::waitForRedirection();

      //Click on Nb btn
      $js  = "try { document.querySelector('[aria-label=\"EgyptFOSS\"').click(); }catch(err){}";
      $this->getSession()->executeScript($js);

      $this->getSession()->wait(5000);

      //Click on Remove Button
      $js  = 'var event = document.createEvent("HTMLEvents");';
      $js .= "var element = document.querySelector('[data-name=\"EgyptFOSS\"]');";
      $js .= 'event.initEvent("click", true, true);';
      $js .= 'event.eventName = "click";';
      $js .= "element.dispatchEvent(event);";      
      $this->getSession()->executeScript($js);

      $this->getSession()->wait(5000);

      //Click on Remove Button
      $js  = 'var event = document.createEvent("HTMLEvents");';
      $js .= "var element = document.querySelector('[data-id=\"EBS5u\"]');";
      $js .= 'event.initEvent("click", true, true);';
      $js .= 'event.eventName = "click";';
      $js .= 'element.dispatchEvent(event);';      
      $this->getSession()->executeScript($js);     
      $this->getSession()->wait(3000); 

      $this->getSession()->getDriver()->stop();
    }

    /**
     * @BeforeSuite
     */
    public static function prepare()
    {     
        //Reset the db
        global $testingEnvironment ;
        $testingEnvironment = true;
        self::resetDb();
        self::updateEmailTestingDB();
        self::addTestTermTaxonomies(); 
        self::addUser();
        self::addTestTimeline();
        //Import Users Data
        self::addTestProducts();
        self::addTestEvents();
        self::addTestCollaborationItems();
        self::addTestEditExpertThoughts();
        self::addAttachment();
    }

    /**
     * @AfterSuite
     */
    public static function finishSuite()
    {
      //Clear Email
      $featureContext = new FeatureContext();
      $featureContext->client->delete('/messages');
    }
    
    /**
    * @BeforeScenario
    */
    public function setPageToEnglish()
    {
        //set language to english
        $this->getSession()->visit($this->locatePath('/en/'));
    }
    
    /**
    * @BeforeScenario @add-quizzes
    */
    public function addQuizzesTests()
    {
        //Import Quizzes Data
        self::addQuizzes();
    }
    
    /**
    * @BeforeScenario @add-news
    */
    public function addNewsTests()
    {
        //Import News Data
        self::addTestNews();
    }
    
    /**
    * @BeforeScenario @add-news-badges-first-user
    */
    public function addNewsBadgefirstUserTest()
    {
        //Import News badges Data for 1st user
        self::addNewsBadgeFirstUser();
    }

    /**
    * @BeforeScenario @add-news-badges-second-user
    */
    public function addNewsBadgesecondUserTest()
    {
        //Import News badges Data for 2nd user
        self::addNewsBadgeSecondUser();
    }

    /**
    * @BeforeScenario @add-top-services
    */
    public function addTopServicesInSystem()
    {
        //Import top service into the system
        self::addTopServices();
    }

    /**
    * @BeforeScenario @add-expert-thoughts
    */
    public function addExpertThoughtTests()
    {
        //Import Expert Thoughts Data
        self::addTestExpertThoughts();
    }
    
    /**
    * @BeforeScenario @add-success-stories
    */
    public function addSuccessStoriesTests()
    {
        //Import success stories Data
        self::addTestSuccessStories();
    }
     
    /**
    * @BeforeScenario @add-requests
    */
    public function addRequestsTests()
    {
        //Import success stories Data
        self::addTestRequests();
    }
    
    /**
    * @BeforeScenario @add-open-datasets
    */
    public function addOpenDatasetsTests()
    {
        //Import open datasets Data
        self::addTestOpenDatasets();
    }

    /**
    * @BeforeScenario @add-events
    */
    public function addEventsTests()
    {
        //Import Events Data
        self::addTestEvents();
    }
    
    
    /**
    * @BeforeScenario @add-wiki-pages
    */
    public function addWikiTests()
    {
        //Import Fosspedia Data
        self::addTestWiki();
    }
    
    /**
    * @BeforeScenario @add-services
    */
    public function addServicesTests()
    {
        self::addTestServices();
    }
    
    /**
    * @BeforeScenario @set-empty-list-open-dataset
    */
    public function setOpenDatasetsToEmpty()
    {
        //set open datasets to empty
        self::setOpenDatasetsToEmptyTestTrait();
    }
    
    /**
    * @BeforeScenario @set-empty-list-request-center
    */
    public function setRequestCenterToEmpty()
    {
        //set open datasets to empty
        self::setRequestCenterToEmptyTestTrait();
    }
    
    /**
    * @AfterScenario @return-list-open-dataset
    */
    public function returnOpenDatasets()
    {
        //set open datasets to empty
        self::returnOpenDatasetsTestTrait();
    }
    
    /**
    * @AfterScenario @return-list-request-center
    */
    public function returnRequestCenter()
    {
        //set open datasets to empty
        self::returnRequestCenterTestTrait();
    }

    /**
    * @BeforeScenario @set-empty-news-list
    */
    public function setNewsToEmpty()
    {
        //set news to empty
        self::setNewsTestEmptyTrait();
    }
    
    /**
    * @AfterScenario @return-list-news
    */
    public function returnNews()
    {
        //set news to empty
        self::returnNewsTestTrait();
    }

    
    /**
    * @BeforeScenario @set-empty-list-success-story
    */
    public function setSuccessStoriesToEmpty()
    {
        //set success stories to empty
        self::setSuccessStoriesToEmptyTestTrait();
    }
    
    /**
    * @AfterScenario @return-list-success-story
    */
    public function returnSuccessStories()
    {
        //set success stories to empty
        self::returnSuccessStoriesTestTrait();
    }
    
    /**
    * @BeforeScenario @remove-published-document
    */
    public function setPublishedDocumentToEmpty()
    {
        //remove published document
        self::removePublishedDocuments();
    }
    
    /**
    * @BeforeScenario @set-published-document
    */
    public function ef_setPublishedDocuments()
    {
        //set published document
        self::setPublishedDocuments();
    }
    

    public static function resetDb()
    {
      $db_user = constant('TESTING_DB_ROOT_USER');
      $db_pass = constant('TESTING_DB_ROOT_PASSWORD');

      $first_db_name = constant('TESTING_DB_NAME');
      $second_db_name = constant('TESTING_PEDIA_DB_NAME');

      $output = shell_exec('mysql -u '.$db_user.' -p'.$db_pass.' -se "DROP DATABASE '.$first_db_name.';DROP DATABASE '.$second_db_name.';"');

      //Execute the database
      $old_path = getcwd();
      chdir('../database');
      putenv("rootpwd=root");
      $output = shell_exec('bash fossdb.sh -s '.$db_pass.' '.$first_db_name);
      chdir($old_path);
    }
    
    /**
     * @BeforeScenarioScope
    */
    public function beforeScenario(BeforeScenarioScope $scope) {
      
      $width = 2048;
      $height = 800;
      $this->getSession()->resizeWindow((int)$width, (int)$height);
    }

  public function canIntercept()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof GoutteDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with '.
                '"@mink:goutte" or "@mink:symfony2". '.
                'Intercepting the redirections is not '.
                'supported by %s', $driver
            );
        }
    }

    /**
    * @Given /^I follow the redirection$/
    * @Then /^I should be redirected$/
    */
    public function iFollowTheRedirection()
    {
        $this->canIntercept();
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }
    
    /**
     * Attaches file to field with specified id|name|label|value.
     *
     * @When /^(?:|I )attach the file "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)" with relative path$/
     */
    public function attachFileToFieldRelative($field, $path)
    {
        $dir = __DIR__;
        for ($d = 1; $d <= 2; $d++)
            $dir = dirname($dir);
        
        $path = $dir."/" .$path;
        $this->attachFileToField($field, $path);
    }
    
    public static function addAttachment() {
      $args = array("post_status" => "inherit",
        "post_type" => "attachment",
        "post_author" => 1
      );
      $fileName = "test.pdf";
      $year = date('Y');
      $month = date('m');
      $home_url = $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
      $args['guid'] = $home_url->option_value . "/wp-content/uploads/{$year}/{$month}/" . $fileName;
      $args['post_title'] = "test";
      $attachment = new Post();
      $attachment->addPost($args);
      $attachment->post_mime_type = "application/pdf";
      $attachment->save();
      $Postmeta = new Postmeta();
      $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/" . $fileName);
    }

  public function waitForRedirection() {

      // Xpath and processes based on core_renderer::redirect_message(), core_renderer::$metarefreshtag and
      // moodle_page::$periodicrefreshdelay possible values.
      if (!$metarefresh = $this->getSession()->getPage()->find('xpath', "//head/descendant::meta[@http-equiv='refresh']")) {
        // We don't fail the scenario if no redirection with message is found to avoid race condition false failures.
        return true;
      }

      // Wrapped in try & catch in case the redirection has already been executed.
      try {
        $content = $metarefresh->getAttribute('content');
      } catch (NoSuchElement $e) {
        return true;
      } catch (StaleElementReference $e) {
        return true;
      }

      // Getting the refresh time and the url if present.
      if (strstr($content, 'url') != false) {

        list($waittime, $url) = explode(';', $content);

        // Cleaning the URL value.
        $url = trim(substr($url, strpos($url, 'http')));
      } else {
        // Just wait then.
        $waittime = $content;
      }


      // Wait until the URL change is executed.
      if ($this->running_javascript()) {
        $this->getSession()->wait($waittime * 1000, false);
      } else if (!empty($url)) {
        // We redirect directly as we can not wait for an automatic redirection.
        $this->getSession()->getDriver()->getClient()->request('get', $url);
      } else {
        // Reload the page if no URL was provided.
        $this->getSession()->getDriver()->reload();
      }
    }


    /******
    * Mail Catcher overide functions 
    **/

    public function buildUrl($address, $port)
    {
        return 'http://' . $address . ':' . $port;
    }


    /**
     * Get all messages
     *
     * @return Message[]
     */
    public function getMessages()
    {
        $messageData = $this->client->get('/messages')->json();

        $messages = [];

        foreach ($messageData as $message) {
            $messages[] = $this->mapToMessage($message);
        }

        return $messages;
    }

    /**
     * Get all messages
     *
     * @return Message[]
     */
    public function getAllMessages()
    {
        $messageData = $this->client->get('/messages')->json();

        $messages = [];

        foreach ($messageData as $message) {
            $messages[] = self::mapMessage($message);
        }

        return $messages;
    }

    public function mapMessage($message)
    {
        $html = $this->client->get("/messages/{$message['id']}.source")
            ->getBody()
            ->getContents();
        $text = $this->client->get("/messages/{$message['id']}.source")
            ->getBody()
            ->getContents();
        return MessageFactory::fromMailCatcher($message, $html, $text);
    }

    /**
     * Get all messages
     *
     * @return Message[]
     */
    public function getMessagesBySubject($subject)
    {
        $messageData = $this->client->get('/messages')->json();

        $messages = [];

        foreach ($messageData as $message) {
            if($message['subject'] == $subject)
                $messages[] = $this->mapToMessage($message);
        }

        return $messages;
    }

    /**
     * Get the latest message
     *
     * @return Message
     */
    public function getLatestMessage()
    {
        $messageData = $this->client->get('/messages')->json()[0];

        return $this->mapToMessage($messageData);
    }

    /**
     * Delete the messages from the inbox
     */
    public function deleteMessages()
    {
        $this->client->delete('/messages');
    }  

    /**
     * @Given I visit post name :arg1 with post type :arg2 in :arg3
     */
    public function visitWithPostName($arg1,$arg2,$arg3)
    {
        
        $post = Post::where("post_name","=",$arg1)->where("post_type","=",$arg2)->first();
        $url = "";
        if($arg3 == "marketplace")
        {
          $url = "/en/marketplace/services/edit/?sid=".$post->ID;
        }else{
          $url = "/en/{$arg3}/{$post->ID}/edit";
        }
        $this->visitPath($url);
    }
    
    /**
     * @When I click on the jquery element with css selector :arg1
     */
    public function iClickOnTheJqueryElementWithCSSSelector($cssSelector) {
      $js = <<<HEREDOC
      jQuery('$cssSelector')[0].click(); 
HEREDOC;
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(1000);
    }

}
