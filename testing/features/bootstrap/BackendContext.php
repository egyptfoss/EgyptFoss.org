<?php

use Behat\Behat\Context\Context;

class BackendContext implements Context
{
    private $featuresObject = null;   
    
    public function __construct(array $parameters)
    {
     //   $this->featuresObject = $parameters["FeatureObject"];
    }


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
        $rowtextliteral = $this->featuresObject->escaper->escapeLiteral($rowtext);
        $exception = new Exception\ElementNotFoundException($this->featuresObject->getSession(), 'text', null, 'the row containing the text "' . $rowtext . '"');
        $xpath = "//div[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'listrow', ' '))" .
            " and contains(normalize-space(.), " . $rowtextliteral . ")]" .
            "|" .
            "//tr[contains(normalize-space(.), " . $rowtextliteral . ")]";
        $rownode = $this->featuresObject->find('xpath', $xpath, $exception);

        // Looking for the element DOM node inside the specified row.
        list($selector, $locator) = $this->featuresObject->transform_selector('link_or_button', $link_or_button);
        $elementnode = $this->featuresObject->find($selector, $locator, false, $rownode);
        $this->featuresObject->ensure_node_is_visible($elementnode);
        $elementnode->click();
    }

    /**
    * @Given /^I Add new interest with "([^"]*)"$/
    */
    public function iAddNewInterestWith($name)
    {
        $this->featuresObject->fillField('title', $name);
        $this->featuresObject->pressButton('publish');
    }

    /**
    * @When /^I edit the interest name with "([^"]*)"$/
    */
    public function iEditTheInterestNameWith($edited_name)
    {
        $this->featuresObject->fillField('title', $edited_name);
        $this->featuresObject->pressButton('publish');
    }
    
    /**
     * Click on the element with the provided CSS Selector
     *
     * @When /^I click on the element with css selector "([^"]*)"$/
     */
    public function iClickOnTheElementWithCSSSelector($cssSelector)
    {
        $session = $this->featuresObject->getSession();
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
      * @When /^I hover "([^"]*)"$/ 
      */
    public function iHoverOverTheElement($locator) {
      $session = $this->featuresObject->getSession();
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
      * @When /^I follow "([^"]*)" in certain place "([^"]*)"$/
      */
      public function iClickLinkInCertainPlace($link, $css)
      {
        $row = $this->featuresObject->getSession()->getPage()->find('css',$css);
        if (!$row) {
        throw new Exception\ElementNotFoundException($this->featuresObject->getSession(), 'element', 'css', $row);
        }
        $row->clickLink($link);
      }

    /**
    * @Given /^I Add new interest with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
    * @When /^I edit the interest name with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
    */
    public function iAddEditNewInterestWith($developer, $title, $desc, $func, $ind, $type, $tech, $platform, $license, $usage, $ref, $link, $keywords)
    {
        $this->featuresObject->fillField('developer', $developer);
        $this->featuresObject->pressButton('publish');
    }


	/**
	* @Given /^I Add new product with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
	*/
	public function iAddNewProductWithAnd($title, $developer, $desc, $func, $ind, $usage, $ref, $link_to_source, $type, $tech, $platform, $license, $keywords){
		$this->featuresObject->fillField('Enter title here', $title);
		$this->featuresObject->fillField('acf-field-developer', $developer);
		$this->featuresObject->fillField('acf-field-description', $desc);
		$this->featuresObject->fillField('acf-field-functionality', $func);
		$this->featuresObject->fillField('acf-field-industry', $ind);
		$this->featuresObject->fillField('acf-field-usage_hints', $usage);
		$this->featuresObject->fillField('acf-field-references', $ref);
		$this->featuresObject->fillField('acf-field-link_to_source', $link_to_source);
		$select = $this->featuresObject->fixStepArgument('acf-field-type');
		$option = $this->featuresObject->fixStepArgument($type);
		$this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
		$this->featuresObject->fillField('new-tag-technology', $tech);
		$this->featuresObject->fillField('new-tag-platform', $platform);
		$this->featuresObject->fillField('new-tag-license', $license);
		$this->featuresObject->fillField('new-tag-keywords', $keywords);
	}


	/**
	* @When /^I Edit the product name with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
	*/
	public function iEditTheProductNameWithAnd($title, $developer, $desc, $func, $ind, $usage, $ref, $type, $tech, $platform, $license, $keywords){
		$this->featuresObject->fillField('Enter title here', $title);
		$this->featuresObject->fillField('acf-field-developer', $developer);
		$this->featuresObject->fillField('acf-field-description', $desc);
		$this->featuresObject->fillField('acf-field-functionality', $func);
		$this->featuresObject->fillField('acf-field-industry', $ind);
		$this->featuresObject->fillField('acf-field-usage_hints', $usage);
		$this->featuresObject->fillField('acf-field-references', $ref);
		// $this->featuresObject->fillField('acf-field-link_to_source', $link_to_source);
		$select = $this->featuresObject->fixStepArgument('acf-field-type');
		$option = $this->featuresObject->fixStepArgument($type);
		$this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
		$this->featuresObject->fillField('new-tag-technology', $tech);
		$this->featuresObject->fillField('new-tag-platform', $platform);
		$this->featuresObject->fillField('new-tag-license', $license);
		$this->featuresObject->fillField('new-tag-keywords', $keywords);
	}

  /**
     * @Given /^I Add new frontend product with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
     */
    public function iAddNewFrontendProductWithAnd($title, $desc, $developer, $func, 
            $ind, $usage, $ref, $link_to_source, $type, $tech, $platform, $license, $keywords){
        $this->featuresObject->fillField('product_title', $title);
        $this->featuresObject->fillField('description', $desc);
        $this->featuresObject->fillField('developer', $developer);
        $this->featuresObject->fillField('functionality', $func);
        $this->featuresObject->fillField('post_industry', $ind);
        $this->featuresObject->fillField('usage_hints', $usage);
        $this->featuresObject->fillField('references', $ref);
        $this->featuresObject->fillField('link_to_source', $link_to_source);
        $select = $this->featuresObject->fixStepArgument('post_type');
        $option = $this->featuresObject->fixStepArgument($type);
        $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
        $this->featuresObject->fillField('post_technologies', $tech);
        $this->featuresObject->fillField('post_platform', $platform);
        $this->featuresObject->fillField('post_license', $license);
        $this->featuresObject->fillField('post_keywords', $keywords);
    }

    /**
     * @Given /^I Add new user with "([^"]*)", "([^"]*)", "([^"]*)", "([^"]*)"$/
     */
    public function iAdminAddNewUserWith($username, $email, $password, $role) {
      $this->featuresObject->fillField('user_login', $username);
      $this->featuresObject->fillField('email', $email);
      if($password !== '') {
        $button = $this->featuresObject->fixStepArgument('Show password');
        $this->featuresObject->getSession()->getPage()->pressButton($button);
        // $this->featuresObject->fillField('pass1-text', $password);
      }
      $select = $this->featuresObject->fixStepArgument('role');
      $option = $this->featuresObject->fixStepArgument($role);
      $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
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
      $this->featuresObject->fillField('email', $email);
      if($password !== '') {
        $button = $this->featuresObject->fixStepArgument('Generate Password');
        $this->featuresObject->getSession()->getPage()->pressButton($button);
        // $this->featuresObject->fillField('pass1-text', $password);
      }
      $select = $this->featuresObject->fixStepArgument('role');
      $option = $this->featuresObject->fixStepArgument($role);
      $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
    }
    
}
