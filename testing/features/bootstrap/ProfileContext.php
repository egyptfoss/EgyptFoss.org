<?php

use Behat\Behat\Context\Context;

class ProfileContext implements Context {

  private $featuresObject = null;

  public function __construct() {
   // $this->featuresObject = $parameters["FeatureObject"];
  }

  /**
   * Edit Profile Contact Details with values or empty string
   *
   * @When /^I Add contact info with "([^"]*)" and "([^"]*)"$/
   */
    public function iAddContactInfoWithAnd($address, $phone) {
  		$this->featuresObject->fillField('address', $address);
  		$this->featuresObject->fillField('phone', $phone);
  		$this->featuresObject->pressButton('Save Changes');
    }

    /**
     * @When /^I Add social profiles with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iAddSocialProfilesWithAndAndAnd($facebook, $google_plus, $twitter, $linkedIn)
    {
      $this->featuresObject->fillField('facebook_url', $facebook);
  		$this->featuresObject->fillField('twitter_url', $twitter);
  		$this->featuresObject->fillField('linkedin_url', $linkedIn);
  		$this->featuresObject->fillField('gplus_url', $google_plus);
  		$this->featuresObject->pressButton('Save Changes');
    }

    /**
     * @When /^I edit my main info with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iEditMyMainInfoWithAndAndAnd($sub_type, $functionality, $industry, $ict_technology)
    {
      $select = $this->featuresObject->fixStepArgument('sub_type');
      $option = $this->featuresObject->fixStepArgument($sub_type);
      $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
      
      $this->featuresObject->fillField('functionality', $functionality);
      if($industry != "")
      {
        $select = $this->featuresObject->fixStepArgument('industry');
        $option = $this->featuresObject->fixStepArgument($industry);
      }else
      {
        $select = $this->featuresObject->fixStepArgument('industry');
        $option = $this->featuresObject->fixStepArgument('Select Industry'); 
      }
      $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
      if($ict_technology != "")
      {
        $js  = "var els = document.getElementById('ict_technology');var opt = document.createElement('option');opt.value='".$ict_technology."';opt.innerHTML = '".$ict_technology."';opt.setAttribute('selected', '');els.appendChild(opt)";
        $this->featuresObject->getSession()->executeScript($js);
      }

      $this->featuresObject->pressButton('Save Changes');
    }

    /**
     * @When /^I Add contact person info with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iAddContactPersonInfoWithAndAndAnd($contact_name, $contact_email, $contact_address, $contact_phone)
    {
      //update sub-type and industry
      $select = $this->featuresObject->fixStepArgument('sub_type');
      $option = $this->featuresObject->fixStepArgument('Projects');
      $this->featuresObject->getSession()->getPage()->selectFieldOption($select, $option);
      
      $select = $this->featuresObject->fixStepArgument('industry');
      $option = $this->featuresObject->fixStepArgument('Software Engineer');

      $this->featuresObject->fillField('contact_name', $contact_name);
      $this->featuresObject->fillField('contact_email', $contact_email);
      $this->featuresObject->fillField('contact_address', $contact_address);
      $this->featuresObject->fillField('contact_phone', $contact_phone);
      $this->featuresObject->pressButton('Save Changes');
    }

    /**
     * @When /^I add "([^"]*)" to a auto-select "([^"]*)"$/
     */
    public function iAddToAAutoSelect($value, $field)
    {
        $js  = "var els = document.getElementById('". $field ."');var opt = document.createElement('option');opt.value='".$value."';opt.innerHTML = '".$value."';opt.setAttribute('selected', '');els.appendChild(opt)";
        $this->featuresObject->getSession()->executeScript($js);
    }

    /**
     * @Given /^I should remove "([^"]*)" from "([^"]*)"$/
     */
    public function iShouldRemoveFrom($value, $field)
    {
        $js  = "var els = document.getElementById('". $field ."');for(var i =0; i < els.length;i++){ if(els[i].value == '".$value."') { els[i].remove(); } }";
        $this->featuresObject->getSession()->executeScript($js);   
    }

}