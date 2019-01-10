<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
trait SuccessStoryTrait
{    
    /**
     * @When I Add new success story with :arg1, :arg2
     */
    public function iAddNewSuccessStoryWith($arg1, $arg2)
    {
              $this->fillField('title', $arg1);
      $js =  sprintf("tinymce.activeEditor.setContent('%s');",$arg2);
      $this->getSession()->executeScript($js);
    }

    /**
     * @When I Add new frontend success story with :arg1, :arg2
     */
    public function iAddNewFrontendSuccessStoryWith($title, $description)
    {
      $this->fillField('success_story_title', $title);
      $this->fillField('success_story_description', $description);
    }
    
    
}
