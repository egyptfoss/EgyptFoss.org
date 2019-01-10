<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

class NavigationContext  implements Context,SnippetAcceptingContext
{

  private $featuresObject = null;

  public function __construct() {
  }

  /**
   * Waits until the page is completely loaded. This step is auto-executed after every step.
   *
   * @Given /^I wait until the page is ready$/
   */
  public function wait_until_the_page_is_ready() {

    if (!$this->running_javascript()) {
      throw new DriverException('Waits are disabled in scenarios without Javascript support');
    }

    $this->getSession()->wait(self::TIMEOUT * 1000, self::PAGE_READY_JS);
  }

  /**
   * Follows the page redirection. Use this step after any action that shows a message and waits for a redirection
   *
   * @Given /^I wait to be redirected$/
   */
  public function i_wait_to_be_redirected() {

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
   * @Given /^I log in to gmail with "([^"]*)" and "([^"]*)"$/
   */
  public function iLogInToGmailWithAnd($email, $password) {
    $this->getSession()->visit($this->locatePath('http://gmail.com/'));
    $this->getSession()->wait(3000);
    $this->fillField('Email', $email);
    $this->pressButton('next');
    $this->getSession()->wait(3000);
    $this->fillField('Passwd', $password);
    $this->pressButton('Sign in');
  }

  /**
   * @Given /^I select "([^"]*)"$/
   */
  public function iSelect($arg1) {
    throw new PendingException();
  }

  /**
   *  @Given /^I am a logged in user with "([^"]*)" and "([^"]*)"$/
   */
  public function iAmALoggedInUserWithAnd($arg1, $arg2) {
    $this->getSession()->visit($this->locatePath('/en/login'));
    $this->getSubcontext("fourms_context")->iLoginToTheSystemWithAnd($arg1, $arg2);
    $this->i_wait_to_be_redirected();
  }

  /**
   * @When /^I press button with css selector "([^"]*)"$/
   */
  public function iPressButtonWithCssSelector($arg1) {
    $btn = $this->getSession()->getPage()->find('css', $arg1);
    $this->pressButton($btn);
  }

  /**
   * @When /^I switch to the new window$/
   */
  public function iSwitchToTheNewWindow() {
    $session = $this->getSession();
    $current = $session->getWindowName();
    $this->previousWindows[] = $current;
    $windows = $session->getWindowNames();
    $key = array_search($current, $windows);
    unset($windows[$key]);
    if (empty($windows)) {
      throw new Exception('There is only one window.');
  }
    $session->switchToWindow(reset($windows));
  }

  /**
   * @Given /^I wait for (\d+) seconds$/
   */
  public function iWaitForSeconds($seconds) {
    $this->getSession()->wait($seconds * 1000, false);
  }

  /**
   * @When /^I restart the browser$/
   */
  public function iRestartTheBrowser()
  {
      $driver = $this->getSession()->getDriver();
      $session = new Session($driver);
      $session->start();
      $session->visit('/');
  }

  /**
  * @When /^I close the browser$/
  */
  public function iCloseTheBrowser(){
      $this->getSession()->getDriver()->stop();
  }
  
   /**
     * @When /^I resize window with height (\d+) and width (\d+) in px$/
     */
    public function iResizeWindow($height,$width)
    {
       $this->getSession()->resizeWindow((int)$width, (int)$height);
    }

  /**
  * @Then /^I wait for the ajax response$/
  */
  public function iWaitForTheAjaxResponse()
  {
      $this->getSession()->wait(5000, '(0 === jQuery.active)');
  }

  /**
   * @Given /^I navigate to the new window$/
   */
  public function iNavigateToTheNewWindow()
  {
    //$this->selectWindow('_blank');
    //$this->getSession()->switchToWindow();
  }

  /**
   * @When /^I navigate to the new link "([^"]*)"$/
   */
  public function iNavigateToTheNewLink($link)
  {
    $link = $this->fixStepArgument($link);
    $this->getSession()->visit($link);

    //$this->getSession()->getPage()->clickLink($link);
  }

}
