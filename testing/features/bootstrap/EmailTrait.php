<?php

trait EmailTrait
{
  
    /**
     * @Then I should receive an email with subject :arg1
     */
    public function iShouldReceiveAnEmailWithSubject($arg1)
    {
        #$messages = $this->getAllMessages();
        $messages = $this->client->get('/messages')->json();
        foreach ($messages as $message) {
          if($message["subject"] == $arg1){
                PHPUnit_Framework_Assert::assertEquals($arg1, $message["subject"]);
                return;
          }
        }

        //failed assertion if no email found to the passed recepient
        PHPUnit_Framework_Assert::assertEquals(true, false);
    }
  
	    /**
     * @Then I should receive a welcome email
     */
    public function iShouldReceiveAWelcomeEmail()
    {
        $message = $this->getLatestMessage();
        if($message->subject() == "[EgyptFOSS] Activate your account"
          && strpos($message->plainText(), 'Activate your account') !== false )
            return true;
        return false;
    }

    /**
     * @Given I should receive a registration email
     */
    public function iShouldReceiveARegistrationEmail()
    {
        $messages = $this->getAllMessages();
        $message = '';
        for($i = 0; $i < sizeof($messages); $i++)
        {
          if($messages[$i]->subject() == "[EgyptFOSS] Activate your account")
          {
            $message = $messages[$i];
          }
        }

        //check the message
        $dom = new DOMDocument;
        $startPos = '<!DOCTYPE html>';
        $pos = strpos($message->plainText(), $startPos);
        $dom->loadHTML(substr($message->plainText(),$pos));
        $url = '';
        foreach ($dom->getElementsByTagName('a') as $node) {
            $url = $node->getAttribute( 'href' );
            break;
        }

        //redirect to this url
        $this->getSession()->visit($url);

        if(!$this->assertSession()->addressEquals($url))
          return false;

        return true;
    }

    /**
     * @Given I should receive reset password email
     */
    public function iShouldReceiveResetPasswordEmail()
    {
        $messages = $this->getAllMessages();
        $message = '';
        for($i = 0; $i < sizeof($messages); $i++)
        {
          if($messages[$i]->subject() == "[EgyptFOSS] Password Reset")
          {
            $message = $messages[$i];
          }
        }

        //check the message
        //$dom = new DOMDocument;
        $startPos = '<!DOCTYPE html>';
        $pos = strpos($message->plainText(), $startPos);
        $html = substr($message->plainText(),$pos);
        $from = 'href="';
        $to = '"';
		    $sub = substr($html, strpos($html,$from)+strlen($from),strlen($html));
    	  $url = substr($sub,0,strpos($sub,$to));

        //redirect to this url
        $this->getSession()->visit($url);

        return true;
    }
    
        
    /**
     * arg1 should be the same as strtotime php function
     * @Then I should receive notification email :arg1 at :arg2 titled :arg3
     */
    public function iShouldReceiveNotificationEmail($arg1, $arg2,$arg3)
    {
        $runningDate = date('Y-m-d',strtotime($arg1));
        exec("cd ../foss-api/api-v2 && php cli.php notifications:send ". $runningDate,$r);
        
        $messages = $this->client->get('/messages')->json();
        foreach ($messages as $message) {
           // if($message["recipients"][0] == $arg2) {
          if($arg3 == $message["subject"]){
                PHPUnit_Framework_Assert::assertEquals($arg3, $message["subject"]);
                return;
           // }
          }
        }
        //failed assertion if no email found to the passed recepient
        PHPUnit_Framework_Assert::assertEquals(true, false);
    }

        
    /**
     * arg1 should be the same as strtotime php function
     * @Then I should not receive notification email on :arg1 at :arg2 titled :arg3
     */
    public function iShouldNotReceiveNotificationEmailOnAtTitled($arg1, $arg2,$arg3)
    {
        //clear all messages first
        $featureContext = new FeatureContext();
        $featureContext->client->delete('/messages');
        $runningDate = date('Y-m-d',strtotime($arg1));
        exec("cd ../foss-api && php cli.php notifications:send ". $runningDate,$r);
//        echo implode("\n", $r);
        
        $messages = $this->client->get('/messages')->json();
        
        foreach ($messages as $message) {
            if($message["recipients"][0] == $arg2) {
                PHPUnit_Framework_Assert::assertNotEquals($arg3, $message["subject"]);
                return;
            }
        }
        //sucess assertion if no email found to the passed recepient
        PHPUnit_Framework_Assert::assertEquals(true, true);
    }
    
    /**
     * @Given I should receive change-email email
     */
    public function iShouldReceiveChangeEmailEmail()
    {
        $messages = $this->getAllMessages();
        $message = '';
        for($i = 0; $i < sizeof($messages); $i++)
        {
          if($messages[$i]->subject() == "[EgyptFOSS] Verify your new email")
          {
            $message = $messages[$i];
            PHPUnit_Framework_Assert::assertEquals(true, true);
          }
        }
        
        if($message == '')
            PHPUnit_Framework_Assert::assertEquals(true, false);
    }
    
    
    /**
     * @Then I confirm my new email
     */
    public function iConfirmMyNewEmail()
    {
        $messages = $this->getAllMessages();
        $message = '';
        for($i = 0; $i < sizeof($messages); $i++)
        {
          if($messages[$i]->subject() == "[EgyptFOSS] Verify your new email")
          {
            $message = $messages[$i];
          }
        }
        
        //check the message
        $dom = new DOMDocument;
        $startPos = '<!DOCTYPE html>';
        $pos = strpos($message->plainText(), $startPos);
        $dom->loadHTML(substr($message->plainText(),$pos));
        
        $url = '';
        foreach ($dom->getElementsByTagName('a') as $node) {
            $url = $node->getAttribute( 'href' );
            break;
        }

        //redirect to this url
        $this->getSession()->visit($url);
    }



}
