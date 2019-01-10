<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
trait ExpertThoughtTrait
{    

    /**
     * @When I Add new frontend expert thought with :arg1, :arg2
     */
    public function iAddNewFrontendExpertThoughtWith($title, $description)
    {
      $this->fillField('expert_thought_title', $title);
      $this->fillField('expert_thought_description', $description);
    }
    
    
}
