<?php

use Behat\Behat\Context\Context;

class FourmsContext implements Context
{
    private $featuresObject = null;   
 
    public function __construct()
    {
        //$this->featuresObject = $parameters["FeatureObject"];
    }

    
    /**
     * @When /^I fill required data with "([^"]*)" , "([^"]*)" , "([^"]*)" , "([^"]*)" and "([^"]*)"$/
     */
    public function iFillRequiredDataWithAnd($username, $email, $password, $password_confirmation, $type)
    {
        $this->featuresObject->fillField('signup_username', $username);
        $this->featuresObject->fillField('signup_email', $email);
        $this->featuresObject->fillField('signup_password', $password);
        $this->featuresObject->fillField('signup_password_confirm', $password_confirmation);
        $select = $this->featuresObject->fixStepArgument('type');
        $option = $this->featuresObject->fixStepArgument($type);
        $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
    }

    /**
     * @Given /^I login to the system with "([^"]*)" and "([^"]*)"$/
     */
    public function iLoginToTheSystemWithAnd($username, $password)
    {
        $this->featuresObject->fillField('user_login', $username);
        $this->featuresObject->fillField('user_pass', $password);
        $this->featuresObject->pressButton('wp-submit');
    }

}