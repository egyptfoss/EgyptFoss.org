<?php
use Behat\Mink\Exception;
trait BackendTrait
{
  private $temp_var ;
	 /**
     * Click on the link or button inside a list/table row containing the specified text.
     *
     * @When /^I click on "(?P<link_or_button>(?:[^"]|\\")*)" in "(?P<row_text_string>(?:[^"]|\\")*)" row$/
     * @param string $link_or_button we look for
     * @param string $rowtext The list/table row text
     * @throws Exception\ElementNotFoundException
     */
    public function i_click_on_in_row($link_or_button, $rowtext) {

        // The table row container.
        $rowtextliteral = $this->escaper->escapeLiteral($rowtext);
        $exception = new Exception\ElementNotFoundException($this->getSession(), 'text', null, 'the row containing the text "' . $rowtext . '"');
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'listrow', ' '))" .
            " and contains(normalize-space(.), " . $rowtextliteral . ")]" .
            "|" .
            "//tr[contains(normalize-space(.), " . $rowtextliteral . ")]";
        $rownode = $this->find('xpath', $xpath, $exception);

        // Looking for the element DOM node inside the specified row.
        list($selector, $locator) = $this->transform_selector('link_or_button', $link_or_button);
        $elementnode = $this->find($selector, $locator, false, $rownode);
        $this->ensure_node_is_visible($elementnode);
        $elementnode->click();
    }

    /**
    * @Given /^I Add new interest with "([^"]*)"$/
    */
    public function iAddNewInterestWith($name)
    {
        $this->fillField('title', $name);
        $this->pressButton('publish');
    }

    /**
    * @When /^I edit the interest name with "([^"]*)"$/
    */
    public function iEditTheInterestNameWith($edited_name)
    {
        $this->fillField('title', $edited_name);
        $this->pressButton('publish');
    }
    
    /**
     * Click on the element with the provided CSS Selector
     *
     * @When /^I click on the element with css selector "([^"]*)"$/
     */
    public function iClickOnTheElementWithCSSSelector($cssSelector)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
        }
 
        $element->click();
 
    }
    
    /**
     * see  an element with the provided CSS Selector
     *
     * @When /^I should find element with css selector "([^"]*)"$/
     */
    public function iShouldFindElementWithCSSSelector($cssSelector)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $cssSelector));
        }
    }
    
     /**
     * shouldn't see  an element with the provided CSS Selector
     *
     * @When /^I should not find element with css selector "([^"]*)"$/
     */
    public function iShouldNotFindElementWithCSSSelector($cssSelector)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('css', $cssSelector) // just changed xpath to css
        );
        if (null !== $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS Selector: "%s"', $element));
        }
    }
    
     /** 
      * @When /^I hover "([^"]*)"$/ 
      */
    public function iHoverOverTheElement($locator) {
      $session = $this->getSession();
      // get the mink session 
      $element = $session->getPage()->find('css', $locator);
      // runs the actual query and returns the element 
  // errors must not pass silently 
      if (null === $element) {
        throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
      } // ok, let's hover it
      $element->mouseOver();
    }
    
    /**
    * Looks for a table, then looks for a row that contains the given text.
    * Once it finds the right row, it clicks a link in that row.
    *
    * Really handy when you have a generic "Edit" link on each row of
    * a table, and you want to click a specific one (e.g. the "Edit" link
    * in the row that contains "Item #2")|
    *
    * @When /^I follow "([^"]*)" on the row containing "([^"]*)"$/
    */
      public function iClickLinkOnTheRowContaining($link, $text)
      {
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $text));
        if (!$row) {
        throw new Exception\ElementNotFoundException($this->getSession(), 'element', 'css', $row);
        }
        $js  = "var els = document.getElementsByClassName('row-actions');";
        $js .= "Array.prototype.forEach.call(els, function(el) {
                // Do stuff with the element
                el.style.visibility='visible';
                });";
        $row->getSession()->executeScript($js);
        //$this->getSession()->wait(1000);
        $row->clickLink($link);
      }
      
      /**
      * @When /^I follow "([^"]*)" in certain place "([^"]*)"$/
      */
      public function iClickLinkInCertainPlace($link, $css)
      {
        $row = $this->getSession()->getPage()->find('css',$css);
        if (!$row) {
        throw new Exception\ElementNotFoundException($this->getSession(), 'element', 'css', $row);
        }
        $row->clickLink($link);
      }

    /**
    * @Given /^I Add new interest with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
    * @When /^I edit the interest name with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
    */
    public function iAddEditNewInterestWith($developer, $title, $desc, $func, $ind, $type, $tech, $platform, $license, $usage, $ref, $link, $keywords)
    {
        $this->fillField('developer', $developer);
        $this->pressButton('publish');
    }


	/**
	* @Given /^I Add new product with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
	*/
	public function iAddNewProductWithAnd($title, $developer, $desc, $func, $ind, $usage, $ref, $link_to_source, $type, $tech, $platform, $license, $keywords){
		$this->fillField('Enter title here', $title);
		$this->fillField('acf-field-developer', $developer);
		$this->fillField('acf-field-description', $desc);
		$this->fillField('acf-field-functionality', $func);
		$this->fillField('acf-field-industry', $ind);
		$this->fillField('acf-field-usage_hints', $usage);
		$this->fillField('acf-field-references', $ref);
		$this->fillField('acf-field-link_to_source', $link_to_source);
		$select = $this->fixStepArgument('acf-field-type');
		$option = $this->fixStepArgument($type);
		$this->getSession()->getPage()->selectFieldOption($select, $option);
		$this->fillField('new-tag-technology', $tech);
		$this->fillField('new-tag-platform', $platform);
		$this->fillField('new-tag-license', $license);
		$this->fillField('new-tag-keywords', $keywords);
	}


	/**
	* @When /^I Edit the product name with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
	*/
	public function iEditTheProductNameWithAnd($title, $developer, $desc, $func, $ind, $usage, $ref, $type, $tech, $platform, $license, $keywords){
		$this->fillField('Enter title here', $title);
		$this->fillField('acf-field-developer', $developer);
		$this->fillField('acf-field-description', $desc);
		$this->fillField('acf-field-functionality', $func);
		$this->fillField('acf-field-industry', $ind);
		$this->fillField('acf-field-usage_hints', $usage);
		$this->fillField('acf-field-references', $ref);
		// $this->fillField('acf-field-link_to_source', $link_to_source);
		$select = $this->fixStepArgument('acf-field-type');
		$option = $this->fixStepArgument($type);
		$this->getSession()->getPage()->selectFieldOption($select, $option);
		$this->fillField('new-tag-technology', $tech);
		$this->fillField('new-tag-platform', $platform);
		$this->fillField('new-tag-license', $license);
		$this->fillField('new-tag-keywords', $keywords);
	}

  /**
     * @Given /^I Add new frontend product with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
     */
    public function iAddNewFrontendProductWithAnd($title, $desc, $developer, $func, 
            $ind, $usage, $ref, $link_to_source, $type, $tech, $platform, $license, $keywords){
        $this->fillField('product_title', $title);
        $this->fillField('description', $desc);
        $this->fillField('developer', $developer);
        $this->fillField('functionality', $func);
        $this->fillField('post_industry', $ind);
        $this->fillField('usage_hints', $usage);
        $this->fillField('references', $ref);
        $this->fillField('link_to_source', $link_to_source);
        $select = $this->fixStepArgument('post_type');
        $option = $this->fixStepArgument($type);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
        if($tech != '')
            $this->fillField('post_technologies', $tech);
        if($platform != '')
            $this->fillField('post_platform', $platform);
        if($license != '')
            $this->fillField('post_license', $license);
        if($keywords != '')
        $this->fillField('post_keywords', $keywords);
    }

    /**
     * @Given /^I Add new user with "([^"]*)", "([^"]*)", "([^"]*)", "([^"]*)"$/
     */
    public function iAdminAddNewUserWith($username, $email, $password, $role) {
      $this->fillField('user_login', $username);
      $this->fillField('email', $email);
      if($password !== '') {
        $button = $this->fixStepArgument('Show password');
        $this->getSession()->getPage()->pressButton($button);
        $this->fillField('pass1-text', $password);
      }
      $select = $this->fixStepArgument('role');
      $option = $this->fixStepArgument($role);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @Given /^User should get an email on "([^"]*)" with:$/
     */
    public function userShouldGetAnEmailOnWith($arg1, PyStringNode $string) {
      throw new PendingException();
    }

    /**
     * @When /^I Edit the user with "([^"]*)", "([^"]*)", "([^"]*)"$/
     */
    public function iAdminEditTheUserWith($email, $password, $role) {
      $this->fillField('email', $email);
      if($password !== '') {
        $button = $this->fixStepArgument('Generate Password');
        $this->getSession()->getPage()->pressButton($button);
        // $this->fillField('pass1-text', $password);
      }
      $select = $this->fixStepArgument('role');
      $option = $this->fixStepArgument($role);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @When /^I fill required data with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
     */
    public function iFillRequiredDataWithAnd($username, $email, $password, $password_confirmation, $type)
    {
        $this->fillField('signup_username', $username);
        $this->fillField('signup_email', $email);
        $this->fillField('signup_password', $password);
        $this->fillField('signup_password_confirm', $password_confirmation);
        $select = $this->fixStepArgument('type');
        $option = $this->fixStepArgument($type);
        $this->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @Given /^I login to the system with "([^"]*)" and "([^"]*)"$/
     */
    public function iLoginToTheSystemWithAnd($username, $password)
    {
        $this->fillField('user_login', $username);
        $this->fillField('user_pass', $password);
        $this->pressButton('wp-submit');
    }

    /**
     * @When /^I Admin Add new event with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iAdminAddNewEventWith($title, $description, $start_date, $end_date, $venue, $venue_address, $venue_city, $venue_country, $venue_province, $venue_phone, $venue_website, $organizer, $organizer_phone, $organizer_website, $event_website, $currency, $cost, $audience, $objectives, $prerequisites, $functionality, $theme, $event_type, $platforms, $technologies)
    {
      $this->fillField('post_title', $title);
      // $this->fillField('wp-editor-container', $description);
      $this->fillField('EventStartDate', $start_date);
      $this->fillField('EventEndDate', $end_date);

      $this->fillField('venue[Venue]', $venue);
      $this->fillField('venue[Address]', $venue_address);
      $this->fillField('venue[City]', $venue_city);
      $select = $this->fixStepArgument('venue[Country]');
      $option = $this->fixStepArgument($venue_country);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $this->fillField('venue[Province]', $venue_province);
      $this->fillField('venue[Phone]', $venue_phone);
      $this->fillField('venue[URL]', $venue_website);

      // $this->fillField('organizer[Organizer][]', $organizer);      
      // $this->fillField('organizer[Phone][]', $organizer_phone);
      // $this->fillField('organizer[Website][]', $organizer_website);

      $this->fillField('EventURL', $event_website);
      $this->fillField('EventCurrencySymbol', $currency);
      $this->fillField('EventCost', $cost);

      $this->fillField('acf-field-audience', $audience);
      $this->fillField('acf-field-objectives', $objectives);
      $this->fillField('acf-field-prerequisites', $prerequisites);
      $this->fillField('acf-field-functionality', $functionality);
      $this->fillField('acf-field-theme', $theme);
      $select = $this->fixStepArgument('acf-field-event_type');
      $option = $this->fixStepArgument($event_type);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      // $this->fillField('acf-field-technology', $technologies);
      // $this->fillField('acf-field-platform', $platforms);
    }

    /**
     * @When /^I User Add new event with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iUserAddNewEventWith($title, $description, $start_date, $end_date, $venue, $venue_address, $venue_city, $venue_country, $venue_phone, $venue_website, $organizer, $organizer_phone, $organizer_email, $event_website, $currency, $cost, $audience, $objectives, $prerequisites, $functionality, $theme, $event_type, $technologies, $platforms)
    {
      $this->fillField('event_title', $title);
      $this->fillField('description', $description);
      $select = $this->fixStepArgument('event_type');
      $option = $this->fixStepArgument($event_type);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $this->fillField('start_datetime', $start_date);
      $this->fillField('end_datetime', $end_date);

      $new_venue = $this->getSession()->getPage()->find('css', '#new-venue-link');
      $new_venue->click();
      $this->fillField('venue_name', $venue);
      $this->fillField('venue_address', $venue_address);
      $this->fillField('venue_city', $venue_city);
      $this->fillField('venue_country', $venue_country);
      $this->fillField('venue_phone', $venue_phone);

      $new_organizer = $this->getSession()->getPage()->find('css', '#new-organizer-link');
      $new_organizer->click();
      $this->fillField('organizer_name', $organizer);      
      $this->fillField('organizer_phone', $organizer_phone);
      $this->fillField('organizer_email', $organizer_email);

      $this->fillField('website', $event_website);
      $select = $this->fixStepArgument('currency');
      $option = $this->fixStepArgument($currency);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $this->fillField('cost', $cost);
      $this->fillField('objectives', $objectives);
      $this->fillField('audience', $audience);
      $this->fillField('prerequisites', $prerequisites);
      $this->fillField('functionality', $functionality);
      $select = $this->fixStepArgument('theme');
      $option = $this->fixStepArgument($theme);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      $this->fillField('technology', $technologies);
      $this->fillField('platform', $platforms);
    }

    /**
     * @When I comment on the product with :arg1
     */
    public function iCommentOnTheProductWith($arg1){
      $this->fillField( 'comment', $arg1 );
      $session = $this->getSession(); 
      $page = $session->getPage();
      $comment_number = $page->find( 'css', '.comments-number' );
      if ( !empty( $comment_number ) ) {
        $result = $comment_number->getText();
        $this->temp_var = (int) $result;
      }
      else{
        $this->temp_var = 0 ;
      }
      $this->pressButton('SENT');
    }

    /**
     * @Then product comments counter should add more one
     */
    public function productCommentsCounterShouldAddMoreOne(){
      $session = $this->getSession(); 
      $page = $session->getPage();
      $comment_number = $page->find( 'css', '.comments-number' );
      $x = $comment_number->getText();

      PHPUnit_Framework_Assert::assertEquals( $this->temp_var+1, (int) $x );
    }
    
    /**
     * @When I reply on :arg1 with :arg2
     */
    public function iReplyOnWith($arg1, $arg2){
      $session = $this->getSession(); 
      $page = $session->getPage();
      $comment_number = $page->find('css', '.comments-number');
      $x = $comment_number->getText();
      $this->temp_var = (int) $x;

      $js = "var found = document.getElementsByClassName('comment-content')[0]; ";
      $js .= "try{ "
        . "document.getElementsByClassName('comment-content')[0].nextSibling.nextSibling.nextSibling.getElementsByClassName('comment-reply-link')[0].click(); ";
      $js .= "found.parentNode.nextSibling.getElementsByClassName('form-control')[0].value = '".$arg2."' ; }"
        . "catch(err){}";
      
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(3000);
      $js = "try{";
      $js .= "document.getElementsByClassName('comment-content')[0].parentNode.nextSibling.getElementsByClassName('submit')[0].click(); }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
    }
    
    /**
     * @When I reply on reply :arg1 with :arg2
     */
    public function iReplyOnReplyWith($arg1, $arg2){
      $session = $this->getSession(); 
      $page = $session->getPage();
      $comment_number = $page->find('css', '.comments-number');
      $x = $comment_number->getText();
      $this->temp_var = (int) $x;

      $js = "var found = document.getElementsByClassName('comment-content')[0].parentNode.nextSibling.nextSibling.nextSibling.getElementsByClassName('comment-body')[0]; ";
      $js .= "try{ "
        . "found.getElementsByClassName('comment-reply-link')[0].click(); ";
      $js .= "document.getElementsByClassName('comment-content')[0].parentNode.nextSibling.nextSibling.nextSibling.getElementsByClassName('comment-respond')[0].getElementsByClassName('form-control')[0].value = '".$arg2."' ; }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(3000);
      $js = "try{";
      $js .= "document.getElementsByClassName('comment-content')[0].parentNode.nextSibling.nextSibling.nextSibling.getElementsByClassName('comment-respond')[0].getElementsByClassName('submit')[0].click(); }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
    }

    /**
   * Attaches files/'file' to field with specified id|name|label|value. The file provided should just be the file name that is already present in 'files' folder
   *
   * @param $file
   *   string The file to be attached. The file must be present in the 'files' folder
   * @param $field
   *   string The field to which the file is to be attached
   *
   * @When /^(?:I )attach file "([^"]*)" to "([^"]*)"$/
   */
  public function attachFile($file, $field) {
    $filePath = getcwd() . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $file;
    if (!file_exists($filePath)) {
      throw new Exception("The file '" . $file . "' could not be found in the 'files' folder");
    }
    return new When('I attach the file "' . $filePath . '" to "' . $field . '"');
  }
  
    /**
   * Attaches file to field with specified id|name|label|value.
   *
   * @When /^(?:|I )attach "(?P<path>[^"]*)" to "(?P<field>(?:[^"]|\\")*)"$/
   */
  public function attachFileToField($field, $path)
  {
        $field = $this->fixStepArgument($field);
        if ($this->getMinkParameter('files_path')) {
            $fullPath = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;
            if (is_file($fullPath)) {
                $path = $fullPath;
            }
        }
        $this->getSession()->getPage()->attachFileToField($field, $path);
  }
  
  /**
    * Click on the element with the provided xpath query
    *
    * @When I click on the element with xpath "\/\/*[@id=:arg1]"

    */
    public function iClickOnTheElementWithXPath($xpath)
    {
        $row = $this->featuresObject->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $text));
        if (!$row) {
        throw new Exception\ElementNotFoundException($this->featuresObject->getSession(), 'element', 'css', $row);
        }
        $js  = "var els = document.getElementsByClassName('row-actions');";
        $js .= "Array.prototype.forEach.call(els, function(el) {
                // Do stuff with the element
                el.style.visibility='visible';
                });";
        $row->getSession()->executeScript($js);
        //$this->featuresObject->getSession()->wait(1000);
        $row->clickLink($link);
 
    }
    
    /**
     * @When /^I User Add new request with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iUserAddNewRequestWith($title, $type, $business_relation, $theme, $description, $requirements, $constraints, $technologies, $interests, $deadline)
    {
      $this->fillField('request_center_title', $title);
      
      $select = $this->fixStepArgument('request_center_type');
      $option = $this->fixStepArgument($type);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $select = $this->fixStepArgument('target_bussiness_relationship');
      $option = $this->fixStepArgument($business_relation);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $select = $this->fixStepArgument('theme');
      $option = $this->fixStepArgument($theme);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $this->fillField('request_center_description', $description);  
      $this->fillField('request_center_requirements', $requirements);
      $this->fillField('request_center_constraints', $constraints);
      
     // $this->fillField('technology', $technologies);
     // $this->fillField('interest', $interests);
      $this->fillField('request_center_deadline', $deadline);
    }
    
    
    /**
     * @When I User Add new feedback with :arg1 and :arg2 and :arg3
     */
    public function iUserAddNewFeedbackWithAndAnd($title, $description, $sections){
      $this->fillField('feedback_title', $title);
      $this->fillField('feedback_description', $description);

      $select = $this->fixStepArgument('post_sections');
      $option = $this->fixStepArgument($sections);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
    }
    
    
    /**
     * @Then I should be on url with redirecto :arg1
     */
    public function iShouldBeOnUrlWithRedirecto($arg1)
    {
        $arg1 = str_replace("http://egyptfoss.com",$this->getMinkParameter('base_url'),$arg1);
        $this->assertSession()->addressEquals($this->locatePath($arg1));
    }
    
        /**
     * @Given I click on :arg1
     */
    public function iClickOn($arg1)
    {
        $js = 'jQuery("#change-email").click()';
        $this->getSession()->executeScript($js); 
    }

        /**
     * @Then :arg1 should precede :arg2
     */
    public function shouldPrecede($arg1, $arg2)
    {
        PHPUnit_Framework_Assert::assertGreaterThan(
            $arg2,$arg1,
            "$arg1 does not proceed $arg2"
        );
    }

    /**
     * @Then :arg1 should precede :arg2 in jquery
     */
    public function shouldPrecedeInJquery($arg1, $arg2)
    {
        $return = $this->getSession()->getDriver()->evaluateScript(
        "function(){ if(jQuery('#members-list li:first-child')[0].innerHTML.indexOf('".$arg1."') >= 0 && jQuery('#members-list li:nth-child(2)')[0].innerHTML.indexOf('".$arg2."') >= 0){ return true; } else { return false; } }()"
        );
        PHPUnit_Framework_Assert::assertEquals(true, $return);
    }
    
    /**
     * @When I fill tinymce in :arg1 with :arg2
     */
    public function fillTinyMce($arg2)
    {
      $js =  sprintf("tinymce.activeEditor.setContent('%s');",$arg2);
      $this->getSession()->executeScript($js);
    }

    /**
     * @When /^I User Add new service with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iUserAddNewServiceWith($title, $category, $theme, $description, $constraints, $conditions)
    {
      $this->fillField('service_title', $title);
      
      $select = $this->fixStepArgument('service_category');
      $option = $this->fixStepArgument($category);
      $this->getSession()->getPage()->selectFieldOption($select, $option);

      $select = $this->fixStepArgument('theme');
      $option = $this->fixStepArgument($theme);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $this->fillField('service_description', $description);  
      $this->fillField('service_constraints', $constraints);
      $this->fillField('service_conditions', $conditions);
    }
    
    /**
     * @Given I wait for ajax return
     */
    public function iWaitForAjaxReturn()
    {
      $time = 8000; // time should be in milliseconds
      $this->getSession()->wait($time, '(0 === jQuery.active)');
    }



     /**
     * @When I check the :element radio button with :value value
     */

    public function iCheckTheRadioButtonWithValue($element, $value) {
      foreach ($this->getSession()->getPage()->findAll('css', 'input[type="radio"][name="'. $element .'"]') as $radio) { if ($radio->getAttribute('value') == $value) { $radio->click(); return true; } } return false; }
    }
