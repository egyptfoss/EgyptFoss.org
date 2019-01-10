<?php

trait ViewTrait
{
	/**
     * Checks, that element of specified type is disabled.
     *
     * @Then /^the "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)" should be disabled$/
     * @throws ExpectationException Thrown by BehatBase::find
     * @param string $element Element we look in
     * @param string $selectortype The type of element where we are looking in.
     */
    public function the_element_should_be_disabled($element, $selectortype) {

        // Transforming from steps definitions selector/locator format to Mink format and getting the NodeElement.
        $node = $this->get_selected_node($selectortype, $element);

        if (!$node->hasAttribute('disabled')) {
            throw new ExpectationException('The element "' . $element . '" is not disabled', $this->getSession());
        }
    }

    /**
     * @Given /^I reset password to "([^"]*)" and "([^"]*)"$/
     */
    public function iResetPasswordToAnd($password, $confirm_password)
    {
        $this->fillField('egyptfoss-pass1', $password);
        $this->fillField('egyptfoss-pass2', $confirm_password);
        $this->pressButton('Reset Password');
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

    /**
     * @Given I login to twitter with :arg1 and :arg2
     */
    public function iLoginToTwitterWithAnd($arg1, $arg2)
    {
        $this->getSession()->visit("https://www.twitter.com/");

        $js = "document.getElementById(\"signin-email\").value='".$arg1."';document.getElementById(\"signin-password\").value='".$arg2."';";
        $this->getSession()->executeScript($js);
        $this->getSession()->wait(3 * 1000, false);
        $js = "document.getElementsByClassName(\"js-submit\")[1].click();";
        $this->getSession()->executeScript($js);

        //Wait for rediretion
        self::waitForRedirection();
        $this->getSession()->wait(10 * 1000, false);
        //self::takeScreenshot();
        
        $this->getSession()->visit($this->locatePath("/en/login/"));
    }

    /**
     * @Given take screenshot
     */
    public function takeScreenshot()
    {
        if ($this->getSession()->getDriver() instanceof 
             \Behat\Mink\Driver\Selenium2Driver) {
               $stepText = 'img_'.rand(100, 9999);
               $fileTitle = preg_replace("#[^a-zA-Z0-9\._-]#", '', $stepText);
               $fileName =  $fileTitle . '.png';
               $screenshot = $this->getSession()->getDriver()->getScreenshot();
               file_put_contents($fileName, $screenshot);
               print "Screenshot for '{$stepText}' placed in {$fileName}\n";
        }
    }



    /**
     * @Given I wait for a while
     */
    public function iWaitForAWhile()
    {
        $js = "var delay=10000;setTimeout(function(){ }, delay);";
        $this->getSession()->executeScript($js);
    }



    /**
     * @Given I register using twitter with :arg1 and :arg2
     */
    public function iRegisterUsingTwitterWithAnd($arg1, $arg2)
    {
//        $this->pressButton('allow');
       // $this->getSession()->getDriver()->executeScript(
         //   "function(){ document.getElementById('username_or_email').value = '".$arg1."' }()"
        //);
        $js  = "document.getElementById('username_or_email').value = '".$arg1."'";
        $this->getSession()->executeScript($js);   

        $js  = "document.getElementById('password').value = '".$arg2."'";
        //$this->getSession()->executeScript($js);   

        $js = "document.getElementById('allow').click()";
        //$this->getSession()->executeScript($js);           
    }
    
    /**
    * Check that there are more than or = to a number of elements on a page
    *
    * @Then /^I should see more "([^"]*)" or more "([^"]*)" elements$/
    */
    public function iShouldSeeMoreOrMoreElements($num, $element){
      $container = $this->getSession()->getPage();
      $nodes = $container->findAll('css', $element);
      if (intval($num) > count($nodes)) {
        $message = sprintf( 'error ! .. %d element', count($nodes) );
//      $message = sprintf('%d elements less than %s "%s" found on the page, but should be %d.', count($nodes), $selectorType, $selector, '21');
        throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession());
      }
    }
    
    /**
      * @Then /^I should see the css selector "([^"]*)"$/
      * @Then /^I should see the CSS selector "([^"]*)"$/
    */
    public function iShouldSeeTheCssSelector($css_selector) {
      $element = $this->getSession()->getPage()->find("css", $css_selector);
      if (empty($element)) {
        throw new \Exception(sprintf("The page '%s' does not contain the css selector '%s'", $this->getSession()->getCurrentUrl(), $css_selector));
      }
    }

  /**
   * Checks that element with specified label and selector is visible on page.
   * @Then /^I must see "([^"]*)" in "([^"]*)"$/
   */
  public function iMustSeeLabel($label, $css_selector) {
    $element = $this->getSession()->getPage()->find("css", $css_selector);
    if ($element->getText() === $label) {
      if ($element->isVisible()) {
        return;
      } else {
        throw new \Exception("Item with label \"$label\" not visible.");
      }
    }
    throw new \Behat\Mink\Exception\ElementNotFoundException($this->getSession(), 'item', 'label', $label);
  }

}