<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CollaborationCenterTrait
{
    /**
     * @When I invite new user with id :arg1 and name :arg2
     */
    public function iInviteNewUserWithIdAndName($arg1, $arg2)
    {
      $js = "try { jQuery(\".invite-space-document\")[0].click(); }catch(err) {}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(5000);
      if($arg1 != '')
      {        
        //set user to invite
        $js = "try { jQuery(\"#user_email\").append('<option value=\"$arg1\" selected>$arg2</option>'); }catch(err) {}";
        $this->getSession()->executeScript($js);
      }
      
      //click on add button
      $js = "try { jQuery(\"#user_email_add\").click() }catch(err) {}";
      $this->getSession()->executeScript($js);      
    }
    
    /**
     * @When I open popup for invite user
     */
    public function iOpenPopupForInviteUser()
    {
      $js = "try { jQuery(\".invite-space-document\")[0].click(); }catch(err) {}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(3000);
    }
    
    /**
     * @When I invite new user with id :arg1 and name :arg2 with opened popup
     */
    public function iInviteNewUserWithIdAndNameWithOpenedPopup($arg1, $arg2)
    {
      $js = "try { jQuery(\"#user_email\").append('<option value=\"$arg1\" selected>$arg2</option>'); }catch(err) {}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(2000);
      
      //click on add button
      $js = "try { jQuery(\"#user_email_add\").click() }catch(err) {}";
      $this->getSession()->executeScript($js);     
    }
    
    /**
     * @When I press popup save button
     */
    public function iPressPopupSaveButton()
    {
      $js  = "try { jQuery('#popup_save').click(); }catch(err){}";
      $this->getSession()->executeScript($js);
      $this->getSession()->wait(12000);
    }


}
