<?php

trait ProfileTrait
{
	/**
   * Edit Profile Contact Details with values or empty string
   *
   * @When /^I Add contact info with "([^"]*)" and "([^"]*)"$/
   */
    public function iAddContactInfoWithAnd($address, $phone) {
  		$this->fillField('address', $address);
  		$this->fillField('phone', $phone);
  		$this->pressButton('Save');
    }

    /**
     * @When /^I Add social profiles with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iAddSocialProfilesWithAndAndAnd($facebook, $google_plus, $twitter, $linkedIn)
    {
      $this->fillField('facebook_url', $facebook);
  		$this->fillField('twitter_url', $twitter);
  		$this->fillField('linkedin_url', $linkedIn);
  		$this->fillField('gplus_url', $google_plus);
  		$this->pressButton('Save');
    }

    /**
     * @When /^I edit my main info with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iEditMyMainInfoWithAndAndAnd($sub_type, $functionality, $theme, $ict_technology)
    {
      $select = $this->fixStepArgument('sub_type');
      $option = $this->fixStepArgument($sub_type);
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $this->fillField('functionality', $functionality);
      if($theme != "")
      {
        $select = $this->fixStepArgument('theme');
        $option = $this->fixStepArgument($theme);
      }else
      {
        $select = $this->fixStepArgument('theme');
        $option = $this->fixStepArgument('Select Theme'); 
      }
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      if($ict_technology != "")
      {
        $js  = "var els = document.getElementById('ict_technology');var opt = document.createElement('option');opt.value='".$ict_technology."';opt.innerHTML = '".$ict_technology."';opt.setAttribute('selected', '');els.appendChild(opt)";
        $this->getSession()->executeScript($js);
      }

      $this->pressButton('Save');
    }

    /**
     * @When /^I Add contact person info with "([^"]*)" and "([^"]*)" and "([^"]*)" and "([^"]*)"$/
     */
    public function iAddContactPersonInfoWithAndAndAnd($contact_name, $contact_email, $contact_address, $contact_phone)
    {
      //update sub-type and industry
      $select = $this->fixStepArgument('sub_type');
      $option = $this->fixStepArgument('Projects');
      $this->getSession()->getPage()->selectFieldOption($select, $option);
      
      $select = $this->fixStepArgument('industry');
      $option = $this->fixStepArgument('Software Engineer');

      $this->fillField('contact_name', $contact_name);
      $this->fillField('contact_email', $contact_email);
      $this->fillField('contact_address', $contact_address);
      $this->fillField('contact_phone', $contact_phone);
      $this->pressButton('Save');
    }

    /**
     * @When /^I add "([^"]*)" to a auto-select "([^"]*)"$/
     */
    public function iAddToAAutoSelect($value, $field)
    {
        $js  = "var els = document.getElementById('". $field ."');var opt = document.createElement('option');opt.value='".$value."';opt.innerHTML = '".$value."';opt.setAttribute('selected', '');els.appendChild(opt)";
        $this->getSession()->executeScript($js);
    }
    
    /**
     * @When I add :arg1 to a auto-multi-select :arg2
     */
    public function iAddToAAutoMultiSelect($arg1, $arg2)
    {
      $values = explode(',',$arg1);
      foreach($values as $value)
      {
      $js = <<<HEREDOC
      // Set the value
      jQuery(document).find("#{$arg2} option:contains({$value})").attr('selected','selected');
      // Then refresh
HEREDOC;
      
      $this->getSession()->executeScript($js);
      }
      $js = <<<HEREDOC
        jQuery(document).find("#{$arg2}").select2();
HEREDOC;
      $this->getSession()->executeScript($js);
      
    }

    /**
     * @Given /^I should remove "([^"]*)" from "([^"]*)"$/
     */
    public function iShouldRemoveFrom($value, $field)
    {
        $js  = "var els = document.getElementById('". $field ."');for(var i =0; i < els.length;i++){ if(els[i].value == '".$value."') { try {els[i].remove();}catch(err) {} }}";
        $this->getSession()->executeScript($js);   
    }
    
    /**
     * @When I comment on :arg1 with :arg2
     */
    public function iCommentOnWith($arg1, $arg2){
//        "var p = document.getElementsByTagName('p')[1].innerHTML";
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{";
      $js .= "found.parentNode.nextSibling.nextSibling.getElementsByTagName('a')[0].click(); }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(5000);
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{";
      $js .= "found.parentNode.parentNode.nextSibling.nextSibling.getElementsByClassName('ac-input bp-suggestions')[0].value = '".$arg2."' ; }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(5000);
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{ found.parentNode.parentNode.nextSibling.nextSibling.getElementsByClassName('btn btn-primary btn-sm')[0].click(); }catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(3000);
    }

    /**
     * @Then comments counter should add more one on :arg1
     */
    public function commentsCounterShouldAddMoreOneOn($arg1){
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{ if ( found.parentNode.nextSibling.nextSibling.getElementsByTagName('span')[0].textContent == '1' ) return true; return false; }catch(err){}";
      $this->getSession()->executeScript($js);
    }
    
    /**
     * @When I follow :arg1 in certain comment :arg2
     */
    public function iFollowInCertainComment($arg1, $arg2){
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg2."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{";
      $js .= "found.parentNode.nextSibling.nextSibling.getElementsByClassName('".$arg1."')[0].click(); }"
        . "catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(3000);
    }

    /**
     * @Then likes counter should add more one on :arg1
     */
    public function likesCounterShouldAddMoreOneOn($arg1){
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{ if ( found.parentNode.nextSibling.nextSibling.getElementsByClassName('button fav bp-secondary-action btn btn-light')[0].textContent == '1' ) return true; return false; }catch(err){}";
      $this->getSession()->executeScript($js);
    }
    
    /**
     * @Then likes counter should subtract one on :arg1
     */
    public function likesCounterShouldSubtractOneOn($arg1){
      $js = "var aTags = document.getElementsByTagName('p'); "
        . "var searchText = '".$arg1."'; "
        . "var found; "
        . "for (var i = 0; i < aTags.length; i++) { "
        . "if (aTags[i].textContent == searchText) { "
        . "found = aTags[i]; break; } } ";
      $js .= "try{ if ( found.parentNode.nextSibling.nextSibling.getElementsByClassName('button bp-secondary-action btn btn-light unfav')[0].textContent == 'Like' ) return true; return false; }catch(err){}";
      $this->getSession()->executeScript($js);
    }
    
    /**
     * @Given I choose change email tab
     */
    public function iChooseChangeEmailTab()
    {
        $js = "try{ document.getElementById('change-email').click(); }catch(err){}";
        $this->getSession()->executeScript($js);
    }


}